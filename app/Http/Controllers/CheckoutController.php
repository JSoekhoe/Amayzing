<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Helpers\GeoHelper;


class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        return view('checkout.index', compact('cart'));
    }



    public function store(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'type' => 'required|in:afhalen,bezorgen',
            'pickup_time' => 'nullable|date_format:H:i',
            'address' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
        ];

        $validated = $request->validate($rules);

        if ($request->type === 'bezorgen') {

            if (!$this->validateDeliveryDayAndTime()) {
                return redirect()->back()->withInput()->withErrors([
                    'type' => 'Je kunt nu niet meer voor morgen bezorgen bestellen. Bestel uiterlijk voor 22:00 uur de dag ervoor.'
                ]);
            }
            

            $cityCenters = [
                'woensdag' => ['city' => 'Arnhem',    'lat' => 51.9851, 'lng' => 5.8987],
                'donderdag' => ['city' => 'Groningen', 'lat' => 53.2194, 'lng' => 6.5665],
                'vrijdag' => ['city' => 'Utrecht',    'lat' => 52.0907, 'lng' => 5.1214],
                'zaterdag' => ['city' => 'Breda',     'lat' => 51.5719, 'lng' => 4.7683],
                'zondag' => ['city' => 'Rotterdam',   'lat' => 51.9244, 'lng' => 4.4777],
            ];

            $deliveryDay = strtolower(now()->locale('nl')->dayName); // bijv. 'woensdag'

            if (array_key_exists($deliveryDay, $cityCenters)) {
                $center = $cityCenters[$deliveryDay];

                // 🧭 Klantlocatie ophalen via Google Geocoding API of eigen databron (tijdelijk hardcoded testadres)
                $fullAddress = $request->address . ', ' . $request->postcode . ', Nederland';

                $geoData = self::geocode($fullAddress);

                if ($geoData) {
                    $distance = GeoHelper::haversine(
                        $center['lat'],
                        $center['lng'],
                        $geoData['lat'],
                        $geoData['lng']
                    );

                    if ($distance > 10) {
                        return redirect()->back()->withInput()->withErrors([
                            'address' => "Je woont buiten het bezorggebied (10 km rond {$center['city']}). Kies aub voor afhalen."
                        ]);
                    }
                }
            }

            $request->validate([
                'address' => 'required|string|max:255',
                'postcode' => 'required|string|max:10',
            ]);
        } elseif ($request->type === 'afhalen') {
            $request->validate([
                'pickup_time' => 'required|date_format:H:i',
            ]);
        }

        // Bereken totaalprijs
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
        }

        if ($request->type === 'bezorgen' && $total < 99) {
            $total += 5.50;
        }

        // Bestelling opslaan
        $order = Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'type' => $request->type,
            'pickup_time' => $request->pickup_time,
            'total_price' => $total,
        ]);

        // OrderItems opslaan
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'price' => $item['product']->price,
            ]);
        }

        // Winkelwagen legen
        Session::forget('cart');

        // Gerelateerde producten laden voor mail
        $order->load('items.product');

        // Mail sturen naar klant en admin
        Mail::to($order->email)->send(new OrderConfirmation($order));
        Mail::to('jamaytuller@gmail.com')->send(new OrderConfirmation($order));



        return redirect()->route('thankyou');

    }
    private static function geocode($address)
    {
        $apiKey = config('services.google_maps.key');
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $apiKey;

        $response = @file_get_contents($url);
        $json = json_decode($response, true);

        if ($json['status'] === 'OK') {
            $location = $json['results'][0]['geometry']['location'];
            return [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ];
        }

        return null;
    }
    private function validateDeliveryDayAndTime(): bool
    {
        $now = now();
        $deliveryDays = ['woensdag', 'donderdag', 'vrijdag', 'zaterdag', 'zondag'];

        $tomorrow = $now->copy()->addDay();
        $tomorrowName = strtolower($tomorrow->locale('nl')->dayName);

        // Morgen moet een bezorgdag zijn
        if (!in_array($tomorrowName, $deliveryDays)) {
            return false;
        }

        // Bestellen kan alleen tot 22:00 uur de dag ervoor
        if ($now->hour >= 22) {
            return false;
        }

        return true;
    }

}
