<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::paginate(9);
        $hasProducts = $products->count() > 0;

        $selectedDeliveryMethod = $request->input('delivery_method', 'afhalen');
        $postcode = $request->input('postcode');

        $deliveryAllowed = false;
        $deliveryMessage = '';

        if ($selectedDeliveryMethod === 'bezorgen') {
            if ($postcode) {
                // Check bezorgbaarheid voor alle producten
                $deliveryAllowed = DeliveryService::checkDelivery($postcode, $products->pluck('id')->toArray());

                if (!$deliveryAllowed) {
                    $deliveryMessage = 'Bezorgen is niet mogelijk op deze postcode. Teruggezet naar ophalen.';
                    $selectedDeliveryMethod = 'afhalen';
                }
            } else {
                $deliveryMessage = 'Voer een postcode in om bezorging te kunnen controleren.';
                $deliveryAllowed = false;
            }
        }

        return view('products.index', compact(
            'products', 'hasProducts', 'selectedDeliveryMethod', 'postcode', 'deliveryAllowed', 'deliveryMessage'
        ));
    }




}
