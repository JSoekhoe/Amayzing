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


        // 4. Bepaal leverdatum (morgen)
        Carbon::setLocale('nl');
        $now = Carbon::now();
        $orderForDay = $now->copy()->addDay();

        $orderWeekdayEn = strtolower($orderForDay->format('l'));
        $orderWeekdayNl = ucfirst($orderForDay->translatedFormat('l'));

        // 5. Check afstand tot bezorggebieden
        $cities = config('delivery.cities');
        $maxDistance = config('delivery.max_distance_km', 10);

        $withinCity = false;
        $nearestCityName = null;
        $nearestCityCenter = null;
        $nearestDistance = INF;

        foreach ($cities as $city => $info) {
            if (strtolower($info['delivery_day']) !== $orderWeekdayEn) {
                continue;
            }

            $distance = $this->haversineGreatCircleDistance($lat, $lng, $info['center']['lat'], $info['center']['lng']);
            if ($distance < $nearestDistance) {
                $nearestDistance = $distance;
                $nearestCityName = $city;
                $nearestCityCenter = $info;
            }

            if ($distance <= $maxDistance) {
                $withinCity = true;
            }
        }

        if (!$withinCity || !$nearestCityCenter) {
            $result->message = 'Helaas, bezorging is niet beschikbaar op dit adres voor levering op ' . $orderWeekdayNl ;
            return $result;
        }

//        // 6. Controleer besteltijd (deadline)
//        $lastOrderDeadline = $orderForDay->copy()->subDay()->setTimeFromTimeString(config('delivery.last_order_time'));
//        if ($now->gt($lastOrderDeadline)) {
//        $result->message = "Bezorging voor <strong>morgen</strong> is niet meer mogelijk in " . ucfirst($nearestCityName) . "<br>Bestel vóór <strong>" . config('delivery.last_order_time') . "</strong> uur de avond.";
//            return $result;
//        }

        // 7. Bezorging toegestaan
        $straat = $geo['straat'] ?? '';
        $woonplaats = $geo['woonplaats'] ?? ucfirst($nearestCityName);
        $postcode = $geo['postcode'] ?? $formattedPostcode;

        $adresRegel = trim("{$straat} {$housenumber}" . ($addition ? " {$addition}" : ""));
        $adresVolledig = "{$adresRegel}, <br> {$postcode}, {$woonplaats}";

        $result->allowed = true;
        $result->selectedDeliveryMethod = 'bezorgen';
        $result->message = "Bezorging is mogelijk op het volgende adres:<br><strong>{$adresVolledig}</strong><br>" .
            "Tussen <strong>" . $nearestCityCenter['delivery_time'] . "</strong> en <strong>" . config('delivery.delivery_end_time') . "</strong> uur.";
        $result->address = $geo['formatted_address'] ?? $adresVolledig;
        $result->street = $straat;



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
