<?php
namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Services\GoogleGeocodingService;

class DeliveryCheckerService
{
    public function check(?string $postcode, ?string $housenumber)
    {
        $result = (object)[
            'allowed' => false,
            'message' => '',
            'address' => null,
            'selectedDeliveryMethod' => 'afhalen'
        ];

        if (!$postcode || !$housenumber) {
            $result->message = 'Voer een postcode en huisnummer in om bezorging te kunnen controleren.';
            return $result;
        }

        $fullAddress = "{$housenumber} {$postcode}, Nederland";
        $geocode = GoogleGeocodingService::geocode($fullAddress);

        if (
            !isset($geocode['geometry']['location']['lat']) ||
            !isset($geocode['geometry']['location']['lng'])
        ) {
            $result->message = 'Locatiegegevens konden niet worden gevonden.';
            return $result;
        }

        $lat = $geocode['geometry']['location']['lat'];
        $lng = $geocode['geometry']['location']['lng'];

        $cities = Config::get('delivery.cities', []);
        $cityCenter = null;

        if (isset($cities['amsterdam'])) {
            $cityCenter = [
                'lat' => $cities['amsterdam']['center']['lat'],
                'lng' => $cities['amsterdam']['center']['lng'],
            ];
        } else {
            $firstCity = reset($cities);
            $cityCenter = [
                'lat' => $firstCity['center']['lat'],
                'lng' => $firstCity['center']['lng'],
            ];
        }

        $distance = $this->haversineDistance($lat, $lng, $cityCenter['lat'], $cityCenter['lng']);
        $maxDistance = Config::get('delivery.max_distance_km', 10);

        if ($distance > $maxDistance) {
            $pickupUrl = route('products.index', ['delivery_method' => 'afhalen']);
            $result->message = "Bezorging is alleen mogelijk binnen een straal van {$maxDistance} km vanaf het centrum.<br>"
                . "Kies alstublieft voor <a href='{$pickupUrl}' class='underline text-blue-600'>afhalen</a> als alternatief.<br><br>";
            return $result;
        }

        $now = Carbon::now();
        $cutoff = Carbon::today()->setHour(22);

        if ($now->greaterThanOrEqualTo($cutoff)) {
            $result->message = 'Bestellen voor morgen is alleen mogelijk tot 22:00 uur.';
            return $result;
        }

        $result->allowed = true;
        $result->message = 'Bezorging is beschikbaar op dit adres.';
        $result->address = "{$geocode['street']} {$housenumber}, {$postcode}";

        return $result;
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
