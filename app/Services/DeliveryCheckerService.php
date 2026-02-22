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
     * @param string|null $woonplaats
     * @return object
     */
    public function check(
        ?string $postcode,
        ?string $housenumber,
        ?string $addition = null,
        string $deliveryMethod = 'afhalen',
        ?string $pickupUrl = null,
        ?string $straatnaam = null,
        ?string $woonplaats = null
    ) {
        $postcode = trim((string) $postcode);
        $straatnaam = $straatnaam ? trim($straatnaam) : null;
        $woonplaats = $woonplaats ? trim($woonplaats) : null;

        // Fallback: straat uit sessie (gebruik 'straat' als hoofdkey)
        if (!$straatnaam) {
            $straatnaam = session('straat') ?: session('straatnaam');
        }

        Log::info('DeliveryCheckerService check data', [
            'postcode'    => $postcode,
            'housenumber' => $housenumber,
            'addition'    => $addition,
            'deliveryMethod' => $deliveryMethod,
            'straatnaam'  => $straatnaam,
            'woonplaats'  => $woonplaats,
        ]);

        // Sessie vullen met laatst bekende waarden (CONSISTENTE keys)
        session([
            'postcode'    => $postcode,
            'housenumber' => $housenumber,
            'addition'    => $addition,
            'straat'      => $straatnaam, // ✅ checkout verwacht 'straat'
            'straatnaam'  => $straatnaam, // (optioneel) backwards compat
            'woonplaats'  => $woonplaats,
        ]);

        $result = (object)[
            'allowed' => false,
            'message' => '',
            'errors' => [],
            'address' => null,
            'selectedDeliveryMethod' => $deliveryMethod,
            'pickupUrl' => $pickupUrl,
            'availableDates' => collect(),
        ];

        $dayTranslations = [
            'monday'    => 'maandag',
            'tuesday'   => 'dinsdag',
            'wednesday' => 'woensdag',
            'thursday'  => 'donderdag',
            'friday'    => 'vrijdag',
            'saturday'  => 'zaterdag',
            'sunday'    => 'zondag',
        ];

        // Land bepalen op basis van postcode
        $isDutchPostcode = preg_match('/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/', $postcode);
        $isBelgianPostcode = preg_match('/^[1-9][0-9]{3}$/', $postcode);

        if (!$isDutchPostcode && !$isBelgianPostcode) {
            $result->message = 'Voer een geldige Nederlandse of Belgische postcode in.';
            return $result;
        }

        // Belgische postcode: straatnaam verplicht
        if ($isBelgianPostcode && empty(trim((string) $straatnaam))) {
            $result->errors[] = 'Straatnaam is verplicht voor Belgische adressen.';
            $result->message = implode(' ', $result->errors);
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

        // Postcode regex toevoegen afhankelijk van land
        if ($isDutchPostcode) {
            $rules['postcode'][] = 'regex:/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/';
        } else {
            $rules['postcode'][] = 'regex:/^[1-9][0-9]{3}$/';
        }

        $messages = [
            'postcode.required' => 'Postcode is verplicht.',
            'postcode.regex' => $isDutchPostcode
                ? 'Voer een geldige Nederlandse postcode in (bijv. 1234 AB).'
                : 'Voer een geldige Belgische postcode in (bijv. 1000).',
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

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            $result->errors = $validator->errors()->all();
            $result->message = implode(' ', $result->errors);
            return $result;
        }

        // Postcode normaliseren
        $postcodeNormalized = strtoupper(str_replace(' ', '', $postcode));
        $formattedPostcode = $isDutchPostcode
            ? (substr($postcodeNormalized, 0, 4) . ' ' . substr($postcodeNormalized, 4))
            : $postcodeNormalized;

        // Geo lookup
        $geo = BagApiService::geocode($formattedPostcode, $housenumber, $addition, $straatnaam);

        if (!$geo || !isset($geo['lat'], $geo['lng'])) {
            $result->message = 'Locatiegegevens konden niet worden gevonden. Controleer je adres.';
            return $result;
        }

        // Alleen voor Belgische postcodes straatnaam matchen
        if ($isBelgianPostcode && $straatnaam !== null) {
            $officialStreet = $geo['straat'] ?? null;
            if ($officialStreet === null || strtolower(trim($straatnaam)) !== strtolower(trim($officialStreet))) {
                $result->message = 'De opgegeven straatnaam komt niet overeen met het adres bij de postcode en huisnummer.';
                return $result;
            }
        }

        $lat = (float) $geo['lat'];
        $lng = (float) $geo['lng'];

        // Config ophalen
        $cities = config('delivery.cities', []);
        $maxDistance = (float) config('delivery.max_distance_km', 10);
        $dateSchedule = config('delivery.date_schedule', []);

        if (empty($cities) || empty($dateSchedule)) {
            $result->message = '🚫 Geen bezorgplanning beschikbaar.';
            return $result;
        }

        // Dichtstbijzijnde stad vinden
        $nearestCityName = null;
        $nearestCityCenter = null;
        $nearestDistance = INF;

        foreach ($cities as $city => $info) {
            if (!isset($info['center']['lat'], $info['center']['lng'])) {
                continue;
            }
            $distance = $this->haversineGreatCircleDistance(
                $lat,
                $lng,
                (float) $info['center']['lat'],
                (float) $info['center']['lng']
            );

            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestCityName = $city;
                $nearestCityCenter = $info;
            }
        }

        if (!$nearestCityName) {
            $result->message = '🚫 Bezorglocaties zijn niet correct geconfigureerd.';
            return $result;
        }

        // Afstand check
        if ($nearestDistance > $maxDistance) {
            $result->message = 'Helaas, bezorging is niet beschikbaar op dit adres. ' .
                'De dichtstbijzijnde stad is ' . ucfirst($nearestCityName) . ', maar deze ligt ' .
                number_format($nearestDistance, 2) . ' km van je adres af. ' .
                'We bezorgen alleen binnen ' . $maxDistance . ' km van onze stadscentra.';
            return $result;
        }

        Carbon::setLocale('nl');
        $now = Carbon::now();

        // Vakantieperiode
        $holidayPeriods = [
            [
                'start' => Carbon::create(2026, 1, 1)->startOfDay(),
                'end'   => Carbon::create(2026, 1, 26)->endOfDay(),
            ],
        ];

        $availableDates = collect();

        foreach ($dateSchedule as $iso => $cityKey) {
            if (strtolower((string) $cityKey) !== strtolower((string) $nearestCityName)) {
                continue;
            }

            try {
                $date = Carbon::createFromFormat('Y-m-d', $iso)->startOfDay();
            } catch (\Exception $e) {
                continue;
            }

            if ($date->lt($now->copy()->startOfDay())) {
                continue;
            }

            $isHoliday = false;
            foreach ($holidayPeriods as $period) {
                if ($date->between($period['start'], $period['end'])) {
                    $isHoliday = true;
                    break;
                }
            }
            if ($isHoliday) continue;

            $availableDates->push([
                'iso' => $date->format('Y-m-d'),
                'label' => $date->translatedFormat('l j F Y'),
            ]);
        }

        $availableDates = $availableDates->sortBy('iso')->values();
        $availableDates = $availableDates->reject(fn($d) => $d['iso'] === $now->format('Y-m-d'))->values();

        if ($availableDates->isEmpty()) {
            $result->allowed = false;
            $result->availableDates = collect();
            $result->message = '🚫 Geen bezorgmomenten beschikbaar';
            return $result;
        }

        // Deadline check voor morgen
        $tomorrow = $now->copy()->addDay()->startOfDay();
        $tomorrowIso = $tomorrow->format('Y-m-d');

        $firstIso = $availableDates->first()['iso'] ?? null;
        $canDeliverTomorrow = ($firstIso === $tomorrowIso);

        if ($canDeliverTomorrow) {
            $deadline = $now->copy()->startOfDay()->setTimeFromTimeString(config('delivery.last_order_time', '22:00'));
            if ($now->gt($deadline)) {
                $availableDates = $availableDates->reject(fn($d) => $d['iso'] === $tomorrowIso)->values();
                $result->message = "Bezorging voor <strong>morgen</strong> is niet meer mogelijk in " . ucfirst($nearestCityName) .
                    ". Kies een andere datum hieronder.<br>Bestel vóór <strong>" . config('delivery.last_order_time') . "</strong> uur de avond ervoor.";
            }
        }

        if ($availableDates->isEmpty()) {
            $result->allowed = false;
            $result->availableDates = collect();
            $result->message = '🚫 Geen bezorgmomenten beschikbaar';
            return $result;
        }

        if (empty($result->message)) {
            $days = $availableDates
                ->map(fn($d) => strtolower(Carbon::createFromFormat('Y-m-d', $d['iso'])->format('l')))
                ->unique()
                ->values()
                ->all();

            $daysFormatted = implode(', ', array_map(fn($d) => $dayTranslations[$d] ?? ucfirst($d), $days));

            $result->message = "Bezorging is mogelijk in " . ucfirst($nearestCityName) . " op de volgende dag(en): {$daysFormatted}.";
        }

        // Adres output + resolve city/street
        $straat = $geo['straat'] ?? $straatnaam ?? '';
        $woonplaatsResolved = $geo['woonplaats'] ?? ($woonplaats ?: ucfirst($nearestCityName));
        $postcodeResolved = $geo['postcode'] ?? $formattedPostcode;

        $adresRegel = trim("{$straat} {$housenumber}" . ($addition ? " {$addition}" : ""));
        $adresVolledig = "{$adresRegel}, <br> {$postcodeResolved}, {$woonplaatsResolved}";

        $result->allowed = true;
        $result->selectedDeliveryMethod = 'bezorgen';
        $result->availableDates = $availableDates;

        $result->message .= "<br>Op het volgende adres:<br><strong>{$adresVolledig}</strong><br>";

        $result->address = $geo['formatted_address'] ?? $adresVolledig;
        $result->street = $straat;
        $result->adresVolledig = $adresVolledig;
        $result->woonplaats = $woonplaatsResolved;
        $result->nearestCityCenter = $nearestCityCenter;

        // ✅ Zet definitief de resolved waarden in de sessie voor de checkout
        session([
            'postcode'    => $postcodeResolved,
            'housenumber' => $housenumber,
            'addition'    => $addition,
            'straat'      => $straat,
            'straatnaam'  => $straat,
            'woonplaats'  => $woonplaatsResolved,
        ]);

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

        $angle = 2 * asin(sqrt(
                pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
            ));

        return $angle * $earthRadius;
    }
}
