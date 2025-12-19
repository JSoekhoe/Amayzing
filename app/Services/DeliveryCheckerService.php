<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DeliveryCheckerService
{
    /**
     * Controleer bezorgmogelijkheid.
     *
     * @param string|null $postcode
     * @param string|null $housenumber
     * @param string|null $addition
     * @param string $deliveryMethod ('afhalen' of 'bezorgen')
     * @param string|null $pickupUrl
     * @param string|null $straatnaam
     * @return object
     */
    public function check(
        ?string $postcode,
        ?string $housenumber,
        ?string $addition = null,
        string $deliveryMethod = 'afhalen',
        ?string $pickupUrl = null,
        ?string $straatnaam = null
    ) {
        $postcode = trim($postcode);
        $straatnaam = $straatnaam ? trim($straatnaam) : null;

        // Fallback: straatnaam uit sessie halen als hij niet meegegeven is
        if (!$straatnaam) {
            $straatnaam = session('straatnaam');
        }

        Log::info('DeliveryCheckerService check data', [
            'postcode'    => $postcode,
            'housenumber' => $housenumber,
            'addition'    => $addition,
            'straatnaam'  => $straatnaam,
        ]);

        // Sessie vullen met laatste bekende waarden
        session([
            'postcode'    => $postcode,
            'housenumber' => $housenumber,
            'addition'    => $addition,
            'straatnaam'  => $straatnaam
        ]);

        if (empty(trim($straatnaam))) {
            Log::warning('Straatnaam is leeg of alleen spaties');
        }

        $result = (object)[
            'allowed' => false,
            'message' => '',
            'errors' => [],
            'address' => null,
            'selectedDeliveryMethod' => $deliveryMethod,
            'pickupUrl' => $pickupUrl,
        ];

        $dayTranslations = [
            'monday' => 'maandag',
            'tuesday' => 'dinsdag',
            'wednesday' => 'woensdag',
            'thursday' => 'donderdag',
            'friday' => 'vrijdag',
            'saturday' => 'zaterdag',
            'sunday' => 'zondag',
        ];

        // Land bepalen op basis van postcode
        $isDutchPostcode = preg_match('/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/', $postcode);
        $isBelgianPostcode = preg_match('/^[1-9][0-9]{3}$/', $postcode);

        if ($isDutchPostcode) {
            $rules['postcode'][] = 'regex:/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/';
            $messages['postcode.regex'] = 'Voer een geldige Nederlandse postcode in (bijv. 1234 AB).';
        } elseif ($isBelgianPostcode) {
            $rules['postcode'][] = 'regex:/^[1-9][0-9]{3}$/';
            $messages['postcode.regex'] = 'Voer een geldige Belgische postcode in (bijv. 1000).';

            // Straatnaam verplicht bij BE
            if (empty(trim($straatnaam))) {
                $result->errors[] = 'Straatnaam is verplicht voor Belgische adressen.';
                $result->message = implode(' ', $result->errors);
                return $result;
            }
        } else {
            $result->message = 'Voer een geldige Nederlandse of Belgische postcode in.';
            return $result;
        }

        // Afhalen direct toestaan
        if ($deliveryMethod === 'afhalen') {
            $result->allowed = true;
            $result->message = 'Afhalen is beschikbaar.';
            return $result;
        }

        // Validatie regels
        $rules = [
            'postcode' => ['required'],
            'housenumber' => ['required', 'numeric', 'min:1'],
            'addition' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9\s\-]*$/'],
            'straatnaam' => ['nullable', 'string'],
        ];

        $messages = [
            'postcode.required' => 'Postcode is verplicht.',
            'housenumber.required' => 'Huisnummer is verplicht.',
            'housenumber.numeric' => 'Huisnummer moet een getal zijn.',
            'housenumber.min' => 'Huisnummer moet minimaal 1 zijn.',
            'addition.regex' => 'Toevoeging mag alleen letters, cijfers, spaties en streepjes bevatten.',
        ];

        $data = [
            'postcode' => $postcode,
            'housenumber' => $housenumber,
            'addition' => $addition,
            'straatnaam' => $straatnaam,
        ];

        Log::info('Data voor validatie', $data);

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $result->errors = $validator->errors()->all();
            $result->message = implode(' ', $result->errors);
            return $result;
        }

        // Postcode normaliseren
        $postcode = strtoupper(str_replace(' ', '', $postcode));
        $formattedPostcode = substr($postcode, 0, 4) . ' ' . substr($postcode, 4);
        $fullAddress = "{$formattedPostcode} {$housenumber}" . ($addition ? " {$addition}" : "") . ", Nederland";

        // Geo lookup
        $geo = BagApiService::geocode($formattedPostcode, $housenumber, $addition, $straatnaam);
        if (!$geo || !isset($geo['lat'], $geo['lng'])) {
            $result->message = 'Locatiegegevens konden niet worden gevonden. Controleer je adres.';
            return $result;
        }

        // Alleen voor Belgische postcodes straatnaam matchen
        if ($isBelgianPostcode && $straatnaam !== null) {
            $officialStreet = $geo['straat'] ?? null;
            if (
                $officialStreet === null ||
                strtolower(trim($straatnaam)) !== strtolower(trim($officialStreet))
            ) {
                $result->message = 'De opgegeven straatnaam komt niet overeen met het adres bij de postcode en huisnummer.';
                return $result;
            }
        }


        $lat = $geo['lat'];
        $lng = $geo['lng'];

        // Config ophalen
        $cities = config('delivery.cities');
        $maxDistance = config('delivery.max_distance_km', 10);

        // Dichtstbijzijnde stad vinden
        $nearestCityName = null;
        $nearestCityCenter = null;
        $nearestDistance = INF;

        foreach ($cities as $city => $info) {
            $distance = $this->haversineGreatCircleDistance($lat, $lng, $info['center']['lat'], $info['center']['lng']);
            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestCityName = $city;
                $nearestCityCenter = $info;
            }
        }

        // Afstand check
        if ($nearestDistance > $maxDistance) {
            $result->message = 'Helaas, bezorging is niet beschikbaar op dit adres voor levering. ' .
                'De dichtstbijzijnde stad is ' . ucfirst($nearestCityName) . ', maar deze ligt ' .
                number_format($nearestDistance, 2) . ' km van je adres af. ' .
                'We bezorgen alleen binnen ' . $maxDistance . ' km van onze stadscentra.';
            return $result;
        }

        // Leveringsschema
        $deliverySchedule = config('delivery.delivery_schedule');
        $fixedSchedule = config('delivery.fixed_schedule');
        $deliveryDays = [];

        foreach ($deliverySchedule as $week => $days) {
            foreach ($days as $day => $city) {
                if (strtolower($city) === strtolower($nearestCityName)) {
                    $deliveryDays[] = strtolower($day);
                }
            }
        }

        foreach ($fixedSchedule as $day => $city) {
            if (strtolower($city) === strtolower($nearestCityName)) {
                $deliveryDays[] = strtolower($day);
            }
        }

        $deliveryDays = array_unique($deliveryDays);
        Carbon::setLocale('nl');
        $now = Carbon::now();


        // Beschikbare datums
        $maxWeeksAhead = 2;
        $availableDates = collect();

        foreach (range(0, $maxWeeksAhead * 7) as $i) {
            $date = $now->copy()->addDays($i);
            $dayName = strtolower($date->format('l'));
            $weekNumber = $date->week;

            if (!in_array($dayName, $deliveryDays)) {
                continue;
            }

            $cityForDay = $deliverySchedule[$weekNumber][$dayName] ?? $fixedSchedule[$dayName] ?? null;
            if ($cityForDay && strtolower($cityForDay) === strtolower($nearestCityName)) {
                $availableDates->push([
                    'iso' => $date->format('Y-m-d'),
                    'label' => $date->translatedFormat('l j F Y'),
                ]);
            }
        }

// Hardcoded vakantieperiode (2025 voorbeeld)
        $holidayPeriods = [
            [
                'start' => Carbon::create(2025, 12, 20)->startOfDay(),
                'end'   => Carbon::create(2025, 12, 20)->endOfDay(),
            ],
            [
                'start' => Carbon::create(2025, 12, 27)->startOfDay(),
                'end'   => Carbon::create(2025, 12, 31)->endOfDay(),
            ],
        ];
// Filter vakantiedagen eruit
        $availableDates = $availableDates->reject(function ($d) use ($holidayPeriods) {
            $date = Carbon::parse($d['iso'])->startOfDay();

            foreach ($holidayPeriods as $period) {
                if ($date->between($period['start'], $period['end'])) {
                    return true; // valt binnen vakantie
                }
            }

            return false;
        })->values();


// Als alles wegvalt door vakantie -> melding
        if ($availableDates->isEmpty()) {
            $result->allowed = false;
            $result->availableDates = collect();
            $result->message = '🚫 Geen bezorgmomenten beschikbaar';
            return $result;
        }


        // Vandaag eruit filteren
        $availableDates = $availableDates->reject(fn($d) => $d['iso'] === $now->format('Y-m-d'))->values();

        // Morgen checken
        $tomorrow = $now->copy()->addDay();
        $canDeliverTomorrow = $availableDates->first() && $availableDates->first()['iso'] === $tomorrow->format('Y-m-d');

        if ($canDeliverTomorrow) {
            $deadline = $tomorrow->copy()->subDay()->setTimeFromTimeString(config('delivery.last_order_time'));
            if ($now->gt($deadline)) {
                $availableDates = $availableDates->reject(fn($d) => $d['iso'] === $tomorrow->format('Y-m-d'))->values();
                $result->message = "Bezorging voor <strong>morgen</strong> is niet meer mogelijk in " . ucfirst($nearestCityName) .
                    ". Kies een andere datum hieronder.<br>Bestel vóór <strong>" . config('delivery.last_order_time') . "</strong> uur de avond ervoor.";
            }
        }

        // Algemene message
        if (empty($result->message)) {
            $daysFormatted = implode(', ', array_map(fn($d) => $dayTranslations[$d] ?? ucfirst($d), $deliveryDays));
            $result->message = "Bezorging is mogelijk in " . ucfirst($nearestCityName) . " op de volgende dag(en): {$daysFormatted}.";
        }

        $result->availableDates = $availableDates;

        // Stad in schema check
        $weekNow = now()->week;
        $weekNext = now()->addWeek()->week;
        $cityFoundInSchedule = false;

        foreach ([$weekNow, $weekNext] as $week) {
            if (!isset($deliverySchedule[$week])) continue;
            foreach ($deliverySchedule[$week] as $day => $cityName) {
                if (strtolower($cityName) === strtolower($nearestCityName)) {
                    $cityFoundInSchedule = true;
                    break 2;
                }
            }
        }

        foreach ($fixedSchedule as $day => $cityName) {
            if (strtolower($cityName) === strtolower($nearestCityName)) {
                $cityFoundInSchedule = true;
                break;
            }
        }

        if (!$cityFoundInSchedule) {
            $result->allowed = false;
            $result->message = 'Helaas, we bezorgen momenteel niet in ' . ucfirst($nearestCityName) .
                ' in de huidige of volgende week. Kijk later nog eens, of kies voor afhalen.';
            return $result;
        }

        // Adres instellen
        $straat = $geo['straat'] ?? $straatnaam ?? '';
        $woonplaats = $geo['woonplaats'] ?? ucfirst($nearestCityName);
        $postcode = $geo['postcode'] ?? $formattedPostcode;

        $adresRegel = trim("{$straat} {$housenumber}" . ($addition ? " {$addition}" : ""));
        $adresVolledig = "{$adresRegel}, <br> {$postcode}, {$woonplaats}";

        $result->allowed = true;
        $result->selectedDeliveryMethod = 'bezorgen';
        $result->message .= "<br>Op het volgende adres:<br><strong>{$adresVolledig}</strong><br>";
        $result->address = $geo['formatted_address'] ?? $adresVolledig;
        $result->street = $straat;
        $result->adresVolledig = $adresVolledig;
        $result->woonplaats = $woonplaats;
        $result->nearestCityCenter = $nearestCityCenter;

        return $result;
    }

    private function haversineGreatCircleDistance(
        float $latitudeFrom,
        float $longitudeFrom,
        float $latitudeTo,
        float $longitudeTo,
        int $earthRadius = 6371
    ): float {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
