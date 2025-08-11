<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class BagApiService
{
    public static function geocode(string $postcode, string $housenumber, ?string $addition = null, ?string $straatnaam = null): ?array
    {
        $postcode = trim($postcode);
        Log::info('geocode functie gestart', ['postcode' => $postcode, 'huisnummer' => $housenumber, 'addition' => $addition]);

        // Land bepalen
        $isNL = preg_match('/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/', $postcode);
        $isBE = preg_match('/^[1-9][0-9]{3}$/', $postcode);
        Log::info('Landdetectie', ['isNL' => $isNL, 'isBE' => $isBE]);

        if ($isNL) {
            // PDOK (Nederland)
            $query = $postcode . '-' . $housenumber . ($addition ? '-' . $addition : '');
            $response = Http::get("https://api.pdok.nl/bzk/locatieserver/search/v3_1/free", [
                'q' => $query,
                'rows' => 1,
                'fq' => 'type:adres'
            ]);

            if (!$response->ok()) {
                return null;
            }

            $data = $response->json();
            if (empty($data['response']['docs'][0])) {
                return null;
            }

            $doc = $data['response']['docs'][0];

            return [
                'lat' => $doc['centroide_ll'] ? floatval(explode(' ', str_replace(['POINT(', ')'], '', $doc['centroide_ll']))[1]) : null,
                'lng' => $doc['centroide_ll'] ? floatval(explode(' ', str_replace(['POINT(', ')'], '', $doc['centroide_ll']))[0]) : null,
                'formatted_address' => $doc['weergavenaam'] ?? null,
                'postcode' => $doc['postcode'] ?? null,
                'straat' => $doc['straatnaam'] ?? null,
                'woonplaats' => $doc['woonplaatsnaam'] ?? null,
            ];
        }

        if ($isBE) {
            $addressParts = array_filter([
                $straatnaam, // voeg straatnaam toe als beschikbaar
                $housenumber,
                $addition,
                $postcode,
                'Antwerpen, België'
            ]);
            $address = urlencode(implode(' ', $addressParts));
            $googleApiKey = config('services.google.maps_api_key');

            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$googleApiKey}");

            if (!$response->ok()) {
                Log::error('Google API request failed', ['status' => $response->status()]);
                return null;
            }

            $data = $response->json();

            Log::info('Google API response ontvangen', ['status' => $data['status'] ?? 'unknown', 'results_count' => count($data['results'] ?? [])]);

            if (($data['status'] ?? '') !== 'OK' || empty($data['results'][0])) {
                Log::warning('Google API returned no results or error', ['response' => $data]);
                return null;
            }

            $location = $data['results'][0]['geometry']['location'];
            $addressComponents = $data['results'][0]['address_components'];

            Log::info('Google address_components', ['components' => $addressComponents]);

            $straat = null;
            $woonplaats = null;

            // Zoek straatnaam (route) en woonplaats (locality/postal_town)
            foreach ($addressComponents as $component) {
                if (in_array('route', $component['types']) && !is_numeric($component['long_name'])) {
                    $straat = $component['long_name'];
                }
                if (in_array('locality', $component['types']) || in_array('postal_town', $component['types'])) {
                    $woonplaats = $component['long_name'];
                }
            }

            // VALIDATIE straatnaam
            if ($straatnaam !== null) {
                $straatInputClean = strtolower(str_replace(' ', '', $straatnaam));
                $straatApiClean = strtolower(str_replace(' ', '', $straat ?? ''));

                if ($straatInputClean !== $straatApiClean) {
                    Log::warning('Straatnaam komt niet overeen met API resultaat', [
                        'straat_invoer' => $straatnaam,
                        'straat_api' => $straat,
                    ]);
                    return null;
                }
            }

            return [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'formatted_address' => $data['results'][0]['formatted_address'],
                'postcode' => $postcode,
                'straat' => $straat,
                'woonplaats' => $woonplaats,
            ];
        }
        return null;
    }

}
