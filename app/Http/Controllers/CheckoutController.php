<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Helpers\GeoHelper;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        return view('checkout.index', compact('cart'));
    }

    // ...

// ...

    public function store(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        // Basisvalidatie zoals jij had (name, email, phone, type, etc.)

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'type' => 'required|in:afhalen,bezorgen',
            'pickup_time' => 'nullable|date_format:H:i',
            'address' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $cart) {

            // Valideer afhaal / bezorg opties voor de hele bestelling
            // (dit kan anders als je per product type hebt, maar ik neem aan dat klant kiest 1 type per bestelling?)
            // Maar jouw winkelwagen heeft meerdere types per product, dan moet je dat hier ook checken, anders foutmelding.

            // Voorraad check per product en type
            foreach ($cart as $productId => $types) {
                $product = Product::find($productId);
                if (!$product) {
                    $validator->errors()->add('stock', "Product met ID {$productId} bestaat niet.");
                    continue;
                }
                foreach ($types as $type => $data) {
                    $quantity = $data['quantity'];
                    $availableStock = $type === 'afhalen' ? $product->pickup_stock : $product->delivery_stock;
                    if ($quantity > $availableStock) {
                        $validator->errors()->add('stock', "Niet genoeg voorraad voor {$product->name} ({$type}). Beschikbaar: {$availableStock}.");
                    }
                }
            }

            // Extra validatie bezorgen/afhalen zoals jij had (adres, tijd, bezorggebied, etc.)

            if ($request->type === 'bezorgen') {
                if (!$this->validateDeliveryDayAndTime()) {
                    $validator->errors()->add('type', 'Je kunt nu niet meer voor morgen bezorgen bestellen. Bestel uiterlijk voor 22:00 uur de dag ervoor.');
                }

                if (empty($request->address) || empty($request->postcode)) {
                    $validator->errors()->add('address', 'Adres en postcode zijn verplicht bij bezorgen.');
                }

                $cityCenters = [
                    'woensdag' => ['city' => 'Arnhem', 'lat' => 51.9851, 'lng' => 5.8987],
                    'donderdag' => ['city' => 'Groningen', 'lat' => 53.2194, 'lng' => 6.5665],
                    'vrijdag' => ['city' => 'Utrecht', 'lat' => 52.0907, 'lng' => 5.1214],
                    'zaterdag' => ['city' => 'Breda', 'lat' => 51.5719, 'lng' => 4.7683],
                    'zondag' => ['city' => 'Rotterdam', 'lat' => 51.9244, 'lng' => 4.4777],
                ];

                $deliveryDay = strtolower(now()->locale('nl')->dayName);

                if (array_key_exists($deliveryDay, $cityCenters)) {
                    $center = $cityCenters[$deliveryDay];
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
                            $validator->errors()->add('type', "Je woont buiten het bezorggebied (10 km rond {$center['city']}). Kies 'Afhalen' in plaats van bezorgen.");
                        }
                    } else {
                        $validator->errors()->add('address', 'Adres kon niet worden geverifieerd.');
                    }
                }
            }

            if ($request->type === 'afhalen') {
                if (empty($request->pickup_time)) {
                    $validator->errors()->add('pickup_time', 'Kies een afhaaltijd.');
                } else {
                    try {
                        $pickupTime = \Carbon\Carbon::createFromFormat('H:i', $request->pickup_time);
                        $day = strtolower(now()->locale('nl')->dayName);
                        $openingTime = in_array($day, ['zaterdag', 'zondag']) ? '11:00' : '14:00';
                        $closingTime = '21:30';

                        $opening = \Carbon\Carbon::createFromFormat('H:i', $openingTime);
                        $closing = \Carbon\Carbon::createFromFormat('H:i', $closingTime);

                        if ($pickupTime->lt($opening) || $pickupTime->gt($closing)) {
                            $validator->errors()->add('pickup_time', "Kies een afhaaltijd tussen {$openingTime} en {$closingTime}.");
                        }
                    } catch (\Exception $e) {
                        $validator->errors()->add('pickup_time', 'Ongeldig tijdformaat voor afhaaltijd.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // Bereken totaalprijs
        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = Product::find($productId);
            foreach ($types as $type => $data) {
                $total += $product->price * $data['quantity'];
            }
        }

        if ($request->type === 'bezorgen' && $total < 99) {
            $total += 5.50;
        }

        \DB::beginTransaction();

        try {
            $order = \App\Models\Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'postcode' => $request->postcode,
                'type' => $request->type,
                'pickup_time' => $request->pickup_time,
                'total_price' => $total,
            ]);

            foreach ($cart as $productId => $types) {
                $product = Product::find($productId);
                foreach ($types as $type => $data) {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $data['quantity'],
                        'price' => $product->price,
                        'type' => $type, // als je deze kolom hebt (anders negeren)
                    ]);

                    if ($type === 'afhalen') {
                        $product->decrement('pickup_stock', $data['quantity']);
                    } else {
                        $product->decrement('delivery_stock', $data['quantity']);
                    }
                }
            }

            \DB::commit();

            Session::forget('cart');

            $order->load('items.product');

            \Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
            \Mail::to('jamaytuller@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

            return redirect()->route('thankyou');

        } catch (\Exception $e) {
            \DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Er is iets misgegaan bij het plaatsen van de bestelling. Probeer het later opnieuw.'
            ]);
        }
    }
}
