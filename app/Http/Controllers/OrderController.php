<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\GeoHelper;
use App\Services\GoogleGeocodingService;

class OrderController extends Controller
{
    public function checkout()
    {
        // toont de checkout pagina
        return view('checkout');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'type' => 'required|in:afhalen,bezorgen',
            'address' => 'nullable|required_if:type,bezorgen',
            'postcode' => 'nullable|required_if:type,bezorgen',
            'pickup_time' => 'nullable|required_if:type,afhalen',
            'cart' => 'required|array',
        ]);

        if ($data['type'] === 'bezorgen') {
            $address = trim($data['address'] . ' ' . $data['postcode']);
            $coords = GoogleGeocodingService::geocode($address);

            if (!$coords) {
                return back()->withErrors(['adres' => 'Kon het adres niet vinden via Google Maps']);
            }

            $dayName = strtolower(now()->locale('nl')->dayName);
            $cityData = config("delivery.cities.$dayName");

            if (!$cityData) {
                return back()->withErrors(['dag' => 'Vandaag wordt er niet bezorgd.']);
            }

            $afstand = GeoHelper::haversine(
                $coords['lat'], $coords['lng'],
                $cityData['lat'], $cityData['lng']
            );

            if ($afstand > 10) {
                return back()->withErrors([
                    'adres' => 'Je woont buiten het bezorggebied van ' . $cityData['name'] . ' (10km max).'
                ]);
            }

            // Bestellen mag alleen vóór 22:00
            if (now()->greaterThan(now()->setHour(22)->setMinute(0))) {
                return back()->withErrors(['tijd' => 'Bestellen voor bezorging moet vóór 22:00 uur.']);
            }
        }

        // Bereken totaalbedrag
        $total = 0;
        foreach ($data['cart'] as $productId => $quantity) {
            $product = Product::findOrFail($productId);

            if ($product->stock < $quantity) {
                return back()->withErrors(['voorraad' => $product->name . ' is niet meer beschikbaar.']);
            }

            $total += $product->price * $quantity;
        }

        // Bezorgkosten
        $deliveryFee = ($data['type'] === 'bezorgen' && $total < config('delivery.free_shipping_threshold'))
            ? config('delivery.delivery_fee')
            : 0;

        $total += $deliveryFee;

        // Order opslaan
        $order = Order::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'type' => $data['type'],
            'address' => $data['address'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'pickup_time' => $data['pickup_time'] ?? null,
            'total_price' => $total,
        ]);
        \Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));


        // OrderItems opslaan
        foreach ($data['cart'] as $productId => $quantity) {
            $product = Product::findOrFail($productId);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);

            // Voorraad verlagen
            $product->decrement('stock', $quantity);
        }

        return redirect()->route('thankyou');
    }

    public function thankyou()
    {
        return view('thankyou');
    }
}
