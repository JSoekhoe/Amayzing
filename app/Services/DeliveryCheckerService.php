<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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
     * @return object
     */
    public function check(?string $postcode, ?string $housenumber, ?string $addition = null, string $deliveryMethod = 'afhalen', ?string $pickupUrl = null)
    {
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

        // Als het om afhalen gaat, dan geen check, direct toegestaan
        if ($deliveryMethod === 'afhalen') {
            $result->allowed = true;
            $result->message = 'Afhalen is beschikbaar.';
            return $result;
        }

        // 1. Validatie (alleen bij bezorgen)
        $validator = Validator::make([
            'postcode' => $postcode,
            'housenumber' => $housenumber,
            'addition' => $addition,
        ], [
            'postcode' => ['required', 'regex:/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/'],
            'housenumber' => ['required', 'numeric', 'min:1'],
            'addition' => ['nullable', 'string', 'regex:/^[a-zA-Z0-9\s\-]*$/'],
        ], [
            'postcode.required' => 'Postcode is verplicht.',
            'postcode.regex' => 'Voer een geldige Nederlandse postcode in (bijv. 1234 AB).',
            'housenumber.required' => 'Huisnummer is verplicht.',
            'housenumber.numeric' => 'Huisnummer moet een getal zijn.',
            'housenumber.min' => 'Huisnummer moet minimaal 1 zijn.',
            'addition.regex' => 'Toevoeging mag alleen letters, cijfers, spaties en streepjes bevatten.',
        ]);

        if ($validator->fails()) {
            $result->errors = $validator->errors()->all();
            $result->message = implode(' ', $result->errors);
            return $result;
        }

        // 2. Normaliseer postcode en bouw adres
        $postcode = strtoupper(str_replace(' ', '', $postcode));
        $formattedPostcode = substr($postcode, 0, 4) . ' ' . substr($postcode, 4);
        $fullAddress = "{$formattedPostcode} {$housenumber}" . ($addition ? " {$addition}" : "") . ", Nederland";

        // 3. Haal coördinaten op via Google Geocode
        $geo = BagApiService::geocode($formattedPostcode, $housenumber, $addition);

        if (!$geo || !isset($geo['lat'], $geo['lng'])) {
            $result->message = 'Locatiegegevens konden niet worden gevonden. Controleer je adres.';
            return $result;
        }

        $lat = $geo['lat'];
        $lng = $geo['lng'];

        // 4. Config ophalen
        $cities = config('delivery.cities');
        $maxDistance = config('delivery.max_distance_km', 10);

        // 5. Zoek dichtstbijzijnde stad op basis van afstand (zonder dagfilter)
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

        // Check of binnen max afstand
        if ($nearestDistance > $maxDistance) {
            $result->message = 'Helaas, bezorging is niet beschikbaar op dit adres voor levering. ' .
                'De dichtstbijzijnde stad is ' . ucfirst($nearestCityName) . ', maar deze ligt ' .
                number_format($nearestDistance, 2) . ' km van je adres af. ' .
                'We bezorgen alleen binnen ' . $maxDistance . ' km van onze stadscentra.';

            return $result;
        }

        // 6. Bepaal leverdagen (altijd als array)
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

        // 7. Genereer beschikbare leverdata in komende 2 maanden voor deze dagen
        $deliverySchedule = config('delivery.delivery_schedule');
        $maxWeeksAhead = 2; // Aantal weken vooruitkijken
        $availableDates = collect();

        foreach (range(0, $maxWeeksAhead * 7) as $i) {
            $date = $now->copy()->addDays($i);
            $dayName = strtolower($date->format('l')); // e.g. "wednesday"
            $weekNumber = $date->week;

            // Alleen verder als deze dag in de standaard delivery_days zit
            if (!in_array($dayName, $deliveryDays)) {
                continue;
            }

            // Is deze stad op deze dag ingepland in de delivery_schedule?
            $cityForDay = $deliverySchedule[$weekNumber][$dayName] ?? $fixedSchedule[$dayName] ?? null;

            if (
                $cityForDay &&
                strtolower($cityForDay) === strtolower($nearestCityName)
            )
            {
                $availableDates->push([
                    'iso' => $date->format('Y-m-d'),
                    'label' => $date->translatedFormat('l j F Y'),
                ]);
            }
        }

        $result->availableDates = $availableDates;



        // 8. Check of levering voor morgen mogelijk is
        $tomorrow = $now->copy()->addDay();
        $canDeliverTomorrow = $availableDates->first() && $availableDates->first()['iso'] === $tomorrow->format('Y-m-d');

        if ($canDeliverTomorrow) {
            $deadline = $tomorrow->copy()->subDay()->setTimeFromTimeString(config('delivery.last_order_time'));
            if ($now->gt($deadline)) {
                $result->message = "Bezorging voor <strong>morgen</strong> is niet meer mogelijk in " . ucfirst($nearestCityName) .
                    ". Kies een andere datum hieronder.<br>Bestel vóór <strong>" . config('delivery.last_order_time') . "</strong> uur de avond ervoor.";
            }
        } else {
            $daysFormatted = implode(', ', array_map(function($d) use ($dayTranslations) {
                return $dayTranslations[$d] ?? ucfirst($d);
            }, $deliveryDays));
            $result->message = "Bezorging is mogelijk in " . ucfirst($nearestCityName) . " op de volgende dag(en): {$daysFormatted}.";
        }
// 9. Check of de stad voorkomt in leveringsschema van deze of volgende week
        $weekNow = now()->week;
        $weekNext = now()->addWeek()->week;
        $cityFoundInSchedule = false;

        foreach ([$weekNow, $weekNext] as $week) {
            if (!isset($deliverySchedule[$week])) {
                continue;
            }

            foreach ($deliverySchedule[$week] as $day => $cityName) {
                if (strtolower($cityName) === strtolower($nearestCityName)) {
                    $cityFoundInSchedule = true;
                    break 2;
                }
            }
        }

// Check ook vaste dagen
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

        // 10. Bezorging toegestaan: adres info
        $straat = $geo['straat'] ?? '';
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

    /**
     * Bereken afstand in km tussen 2 coördinaten (Haversine-formule)
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
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
