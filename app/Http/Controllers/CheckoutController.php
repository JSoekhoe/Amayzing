<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckoutController extends Controller
{


    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $deliveryMethod = $request->input('type', 'bezorgen');

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = $products->get($productId);
            if (!$product) continue;

            foreach ($types as $type => $data) {
                $subtotal = $product->price * $data['quantity'];
                $total += $subtotal;
            }
        }

        $deliveryFee = ($deliveryMethod === 'bezorgen' && $total < 99) ? 5.50 : 0;
        $grandTotal = $total + $deliveryFee;

        $pickupLocationsConfig = config('pickup.locations');
        $pickupLocations = collect($pickupLocationsConfig)->mapWithKeys(fn($location, $key) => [$key => $location['name']]);

        $selectedPickupLocation = old('pickup_location') ?? array_key_first($pickupLocations->toArray());
        $dayName = strtolower(Carbon::now()->locale('nl')->dayName);

        $openingHours = $pickupLocationsConfig[$selectedPickupLocation]['hours'] ?? [];
        $open = $openingHours[$dayName]['open'] ?? null;
        $close = $openingHours[$dayName]['close'] ?? null;

        $timeSlots = [];
        if ($open && $close && $open !== $close) {
            $timeSlots = $this->generateTimeSlots($open, $close);
        }

        $minPickupTime = in_array($dayName, ['zaterdag', 'zondag']) ? '11:00' : '14:00';

        return view('checkout.index', [
            'cart' => $cart,
            'products' => $products,
            'deliveryMethod' => $deliveryMethod,
            'deliveryFee' => $deliveryFee,
            'grandTotal' => $grandTotal,
            'pickupLocations' => $pickupLocations,
            'timeSlots' => $timeSlots, // <-- controller genereert deze
            'minPickupTime' => $minPickupTime,
            'postcode' => session('postcode'),
            'housenumber' => session('housenumber'),
            'addition' => session('addition'),
        ]);

    }

    private function generateTimeSlots($start, $end, $interval = 30)
    {
        $slots = [];
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        while ($startTime->lte($endTime)) {
            $slots[] = $startTime->format('H:i');
            $startTime->addMinutes($interval);
        }

        return $slots;
    }


    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^(\+31|0)[1-9][0-9]{8}$/'],
            'type' => 'required|in:afhalen,bezorgen',
            'pickup_time' => 'required_if:type,afhalen|date_format:H:i',
            'pickup_location' => 'required_if:type,afhalen|in:' . implode(',', array_keys(config('pickup.locations'))),
            'address' => 'required_if:type,bezorgen|string|max:255',
            'postcode' => 'required_if:type,bezorgen|string|max:10',
            'housenumber' => 'required_if:type,bezorgen|string|max:10',
            'addition' => 'nullable|string|max:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $validator->after(function ($validator) use ($cart, $products, $request) {
            if ($request->type === 'afhalen') {
                $dayName = strtolower(now()->locale('nl')->dayName);
                $minPickupTime = in_array($dayName, ['zaterdag', 'zondag']) ? '11:00' : '14:00';

                try {
                    $pickupTime = \Carbon\Carbon::createFromFormat('H:i', $request->pickup_time);
                    $minPickupTimeCarbon = \Carbon\Carbon::createFromFormat('H:i', $minPickupTime);
                } catch (\Exception $e) {
                    $validator->errors()->add('pickup_time', 'Ongeldig tijdsformaat.');
                    return;
                }

                // Check 1: Afhaaltijd minimaal vanaf minPickupTime
                if ($pickupTime->lt($minPickupTimeCarbon)) {
                    $validator->errors()->add('pickup_time', "Afhaaltijd moet na $minPickupTime zijn.");
                }

                $pickupLocations = config('pickup.locations');
                $location = $request->pickup_location;

                if (isset($pickupLocations[$location])) {
                    $openingHours = $pickupLocations[$location]['hours'];

                    if (isset($openingHours[$dayName])) {
                        $openTime = \Carbon\Carbon::createFromFormat('H:i', $openingHours[$dayName]['open']);
                        $closeTime = \Carbon\Carbon::createFromFormat('H:i', $openingHours[$dayName]['close']);

                        // Locatie gesloten als open == close (zoals '00:00' - '00:00')
                        if ($openTime->eq($closeTime)) {
                            $validator->errors()->add('pickup_time', "De locatie is op deze dag gesloten.");
                        } else {
                            if ($pickupTime->lt($openTime) || $pickupTime->gt($closeTime)) {
                                $validator->errors()->add('pickup_time', "Afhaaltijd moet binnen de openingstijden van de locatie zijn ({$openingHours[$dayName]['open']} - {$openingHours[$dayName]['close']}).");
                            }
                        }
                    } else {
                        $validator->errors()->add('pickup_location', "Openingstijden voor deze dag zijn niet beschikbaar.");
                    }
                } else {
                    $validator->errors()->add('pickup_location', "Gekozen afhaallocatie bestaat niet.");
                }
            }


        foreach ($cart as $productId => $types) {
                $product = $products->get($productId);
                if (!$product) {
                    $validator->errors()->add('stock', "Product met ID {$productId} bestaat niet.");
                    continue;
                }

                foreach ($types as $type => $data) {
                    $quantity = $data['quantity'];
                    if ($type === 'afhalen' && $product->pickup_stock < $quantity) {
                        $validator->errors()->add('stock', "Niet genoeg voorraad om af te halen voor {$product->name}.");
                    }
                    if ($type === 'bezorgen' && $product->delivery_stock < $quantity) {
                        $validator->errors()->add('stock', "Niet genoeg voorraad om te bezorgen voor {$product->name}.");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = $products->get($productId);
            if (!$product) continue;

            foreach ($types as $type => $data) {
                $total += $product->price * $data['quantity'];
            }
        }

        $deliveryFee = ($request->type === 'bezorgen' && $total < 99) ? 5.50 : 0;
        $grandTotal = $total + $deliveryFee;

        DB::beginTransaction();

        try {
            $order = \App\Models\Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->type === 'bezorgen' ? $request->address : null,
                'housenumber' => $request->type === 'bezorgen' ? $request->housenumber : null,
                'addition' => $request->type === 'bezorgen' ? $request->addition : null,
                'postcode' => $request->type === 'bezorgen' ? $request->postcode : null,
                'type' => $request->type,
                'pickup_time' => $request->type === 'afhalen' ? $request->pickup_time : null,
                'pickup_location' => $request->type === 'afhalen' ? $request->pickup_location : null,
                'total_price' => $grandTotal,
            ]);

            foreach ($cart as $productId => $types) {
                $product = $products->get($productId);
                if (!$product) continue;

                foreach ($types as $type => $data) {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $data['quantity'],
                        'price' => $product->price,
                        'type' => $type,
                    ]);

                    if ($type === 'afhalen') {
                        $product->decrement('pickup_stock', $data['quantity']);
                    } else {
                        $product->decrement('delivery_stock', $data['quantity']);
                    }
                }
            }

            DB::commit();

            $order->load('items.product');

            Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
            Mail::to('soekhoe.j@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

            return redirect()->route('payment.checkout', ['orderId' => $order->id])
                ->with('success', 'Bestelling succesvol geplaatst. Ga verder met betalen.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Er is iets misgegaan bij het plaatsen van de bestelling. Probeer het later opnieuw.',
            ]);
        }
    }
}
