<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleGeocodingService
{
    public static function geocode(string $address)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = Http::get($url, [
            'address' => $address,
            'key' => $apiKey,
            'region' => 'nl',
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (isset($data['status']) && $data['status'] === 'OK' && count($data['results']) > 0) {
            return $data['results'][0];
        }

        return null;
    }
}
