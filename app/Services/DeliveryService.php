<?php

namespace App\Services;

class DeliveryService
{
    public function checkDelivery(string $postcode, array $productIds): array
    {
        // Je logica om te checken of bezorgen kan per product
        // Voorbeeld:
        $results = [];
        foreach ($productIds as $id) {
            // Simpel voorbeeld, hier vervang je door echte logica
            $results[$id] = $postcode === '1234AB'; // alleen bezorgen mogelijk op 1234AB
        }
        return $results;
    }
}
