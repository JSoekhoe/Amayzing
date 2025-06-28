<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GoogleGeocodingService
{
    public static function geocode(string $address)
    {
        try {
            $apiKey = config('services.google_maps.key');
            $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);

            if ($response->failed()) {
                Log::error('Google Geocoding API request failed: ' . $response->body());
                return null; // of throw nieuwe exceptie
            }

            $data = $response->json();

            if (empty($data['results'])) {
                return null;
            }

            return $data['results'][0];
        } catch (\Exception $e) {
            Log::error('Google Geocoding API error: ' . $e->getMessage());
            return null;
        }
    }
}
