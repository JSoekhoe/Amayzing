<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleGeocodingService
{
    public static function geocode(string $address): ?array
    {
        $apiKey = config('services.google_maps.key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => $apiKey,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if ($data['status'] !== 'OK' || empty($data['results'])) {
            return null;
        }

        $location = $data['results'][0]['geometry']['location'];

        return [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ];
    }
}
