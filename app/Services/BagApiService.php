<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BagApiService
{
    public static function geocode(string $postcode, string $housenumber, ?string $addition = null): ?array
    {
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
}
