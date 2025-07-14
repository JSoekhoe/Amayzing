<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $deliveryMethod = 'afhalen';
        foreach ($cart as $productId => $types) {
            if (isset($types['bezorgen'])) {
                $deliveryMethod = 'bezorgen';
                break;
            }
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = $products->get($productId);
            if (!$product) continue;
            foreach ($types as $type => $data) {
                $total += $product->price * $data['quantity'];
            }
        }

        $deliveryFee = ($deliveryMethod === 'bezorgen' && $total < 99) ? 5.50 : 0;
        $grandTotal = $total + $deliveryFee;

        // Haal config pickup locaties op
        $pickupLocationsConfig = config('pickup.locations');
        $pickupLocations = collect($pickupLocationsConfig)->mapWithKeys(fn($location, $key) => [$key => $location['name']]);
        $selectedPickupLocation = old('pickup_location') ?? array_key_first($pickupLocations->toArray());

        $openingHours = $pickupLocationsConfig[$selectedPickupLocation]['hours'] ?? [];

        $today = Carbon::now();

        // Beschikbare afhaaldagen (max 14 dagen vooruit) op basis van openingsuren config
        $availablePickupDates = [];
        for ($i = 0; $i < 14; $i++) {
            $date = $today->copy()->addDays($i);
            $dayNameLoop = strtolower($date->locale('nl')->dayName);

            if (isset($openingHours[$dayNameLoop])) {
                $hours = $openingHours[$dayNameLoop];
                if (!empty($hours['open']) && $hours['open'] !== $hours['close']) {
                    $availablePickupDates[] = $date->format('Y-m-d');
                }
            }
        }

        // Kies de eerste beschikbare pickup date als default
        $pickupDate = $availablePickupDates[0] ?? $today->format('Y-m-d');
        $pickupDateCarbon = Carbon::createFromFormat('Y-m-d', $pickupDate);
        $availablePickupDatesFormatted = collect($availablePickupDates)->mapWithKeys(function ($date) {
            $formatted = \Carbon\Carbon::parse($date)->locale('nl')->isoFormat('dddd D MMMM YYYY');
            return [$date => $formatted];
        })->toArray();
        $dayNamePickup = strtolower($pickupDateCarbon->locale('nl')->dayName);

        // Openingstijden voor de geselecteerde pickup date
        $openingHoursPickupDay = $openingHours[$dayNamePickup] ?? null;

        $open = $openingHoursPickupDay['open'] ?? null;
        $close = $openingHoursPickupDay['close'] ?? null;

        $timeSlots = [];
        if ($open && $close && $open !== $close) {
            $timeSlots = $this->generateTimeSlots($open, $close);
        }

        // Standaard pickup_time als eerste tijdslot, of null als gesloten
        $pickupTime = $timeSlots[0] ?? null;

        // Min pickup tijd op basis van huidige dag (optioneel, je kunt hier ook logica toepassen)
        $currentDayName = strtolower($today->locale('nl')->dayName);
        $minPickupTime = in_array($currentDayName, ['zaterdag', 'zondag']) ? '11:00' : '14:00';

        $availableDeliveryDates = [];
        if ($deliveryMethod === 'bezorgen') {
            $checker = new \App\Services\DeliveryCheckerService();
            $deliveryCheck = $checker->check(
                session('postcode'),
                session('housenumber'),
                session('addition'),
                'bezorgen'
            );

            if ($deliveryCheck->allowed) {
                $availableDeliveryDates = $deliveryCheck->availableDates ?? [];
            }
        }

        return view('checkout.index', [
            'cart' => $cart,
            'products' => $products,
            'deliveryMethod' => $deliveryMethod,
            'deliveryFee' => $deliveryFee,
            'grandTotal' => $grandTotal,
            'pickupLocations' => $pickupLocations,
            'timeSlots' => $timeSlots,
            'minPickupTime' => $minPickupTime,
            'straat' => session('straat', ''),
            'postcode' => session('postcode'),
            'housenumber' => session('housenumber'),
            'addition' => session('addition'),
            'availableDeliveryDates' => $availableDeliveryDates,
            'availablePickupDates' => $availablePickupDates,
            'selectedPickupLocation' => $selectedPickupLocation,
            'availablePickupDatesFormatted' => $availablePickupDatesFormatted,
            'selectedPickupTime' => $pickupTime,
        ]);
    }




    private function generateTimeSlots($start, $end, $interval = 30)
    {
        $slots = [];
        $startTime = Carbon::createFromFormat('H:i', $start);
        $endTime = Carbon::createFromFormat('H:i', $end);

        while ($startTime->lt($endTime)) {
            $slots[] = $startTime->format('H:i');
            $startTime->addMinutes($interval);
        }

        return $slots;
    }

    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if ($request->type === 'bezorgen') {
            $request->merge([
                'straat' => session('straat'),
                'housenumber' => session('housenumber'),
                'addition' => session('addition'),
                'postcode' => session('postcode'),
            ]);
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $pickupLocations = config('pickup.locations');
        $pickupLocationKeys = array_keys($pickupLocations);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^(\+31|0)[1-9][0-9]{8}$/'],
            'type' => 'required|in:afhalen,bezorgen',
            'pickup_location' => 'required_if:type,afhalen|in:' . implode(',', $pickupLocationKeys),
            'pickup_date' => 'required_if:type,afhalen|date_format:Y-m-d|after_or_equal:today',
            'pickup_time' => 'required_if:type,afhalen|date_format:H:i',
            'straat' => 'required_if:type,bezorgen|string|max:255',
            'postcode' => 'required_if:type,bezorgen|string|max:10',
            'housenumber' => 'required_if:type,bezorgen|string|max:10',
            'addition' => 'nullable|string|max:10',
            'delivery_date' => 'required_if:type,bezorgen|date_format:Y-m-d',
        ];

        $validator = Validator::make($request->all(), $rules);

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Bereken totaalbedrag vóór validatie
        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = $products->get($productId);
            if (!$product) continue;

            foreach ($types as $type => $data) {
                $total += $product->price * $data['quantity'];
            }
        }

        $validator->after(function ($validator) use ($cart, $products, $request, $pickupLocations, $total) {
            // Check minimaal bestelbedrag
            $minimumOrderAmount = 40;

            if ($request->type === 'bezorgen' && $total < $minimumOrderAmount) {
                $validator->errors()->add('minimum_order', "Het minimale bestelbedrag is €{$minimumOrderAmount}.");
            }


            if ($request->type === 'afhalen') {
                $location = $request->pickup_location;
                $pickupDate = $request->pickup_date;
                $pickupTime = $request->pickup_time;

                // Check dat locatie bestaat
                if (!isset($pickupLocations[$location])) {
                    $validator->errors()->add('pickup_location', 'Ongeldige afhaallocatie.');
                    return;
                }

                $openingHours = $pickupLocations[$location]['hours'] ?? [];

                try {
                    $pickupDateCarbon = Carbon::createFromFormat('Y-m-d', $pickupDate);
                } catch (\Exception $e) {
                    $validator->errors()->add('pickup_date', 'Ongeldige datum.');
                    return;
                }

                $dayName = strtolower($pickupDateCarbon->locale('nl')->dayName);

                if (!isset($openingHours[$dayName])) {
                    $validator->errors()->add('pickup_date', 'Afhalen is niet mogelijk op deze dag.');
                    return;
                }

                $hours = $openingHours[$dayName];
                if ($hours['open'] === $hours['close']) {
                    $validator->errors()->add('pickup_date', 'De locatie is op deze dag gesloten.');
                    return;
                }

                try {
                    $pickupTimeCarbon = Carbon::createFromFormat('H:i', $pickupTime);
                    $openTime = Carbon::createFromFormat('H:i', $hours['open']);
                    $closeTime = Carbon::createFromFormat('H:i', $hours['close']);
                } catch (\Exception $e) {
                    $validator->errors()->add('pickup_time', 'Ongeldig tijdsformaat.');
                    return;
                }

                if ($pickupTimeCarbon->lt($openTime) || $pickupTimeCarbon->gt($closeTime)) {
                    $validator->errors()->add('pickup_time', "Afhaaltijd moet binnen de openingstijden zijn ({$hours['open']} - {$hours['close']}).");
                }

                // Min pickup tijd vandaag (optioneel)
                $today = Carbon::now()->startOfDay();
                if ($pickupDateCarbon->isSameDay($today)) {
                    $minPickupTime = in_array($dayName, ['dinsdag', 'woensdag','donderdag','vrijdag','zaterdag', 'zondag']) ? '11:00' : '14:00';
                    $minTime = Carbon::createFromFormat('H:i', $minPickupTime);
                    if ($pickupTimeCarbon->lt($minTime)) {
                        $validator->errors()->add('pickup_time', "Afhaaltijd moet na $minPickupTime zijn.");
                    }
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

        // Haal aantal producten per dag op, inclusief whole menu box weging
        $productCountMultipliers = [
            11 => 7, // whole menu box telt als 7
        ];

// Datum waarop afhalen of bezorgen plaatsvindt
        $orderDate = null;
        if ($request->type === 'afhalen') {
            $orderDate = $request->pickup_date;
        } elseif ($request->type === 'bezorgen') {
            $orderDate = $request->delivery_date;
        }

        if ($orderDate) {
            // Haal alle order items op van orders op die dag en van dat type (afhalen/bezorgen)
            $ordersOfTheDay = Order::where('type', $request->type)
                ->whereDate($request->type === 'afhalen' ? 'pickup_date' : 'delivery_date', $orderDate)
                ->pluck('id');

            $orderItems = OrderItem::whereIn('order_id', $ordersOfTheDay)->get();

            $totalProductsSold = 0;
            foreach ($orderItems as $item) {
                $multiplier = $productCountMultipliers[$item->product_id] ?? 1;
                $totalProductsSold += $item->quantity * $multiplier;
            }

            // Bereken ook de producten in deze nieuwe bestelling
            $newOrderProductCount = 0;
            foreach ($cart as $productId => $types) {
                $multiplier = $productCountMultipliers[$productId] ?? 1;
                foreach ($types as $type => $data) {
                    if ($type === $request->type) { // Alleen producten voor dit type (afhalen of bezorgen)
                        $newOrderProductCount += $data['quantity'] * $multiplier;
                    }
                }
            }

            $maxPerDay = 300;
            if (($totalProductsSold + $newOrderProductCount) > $maxPerDay) {
                return redirect()->back()->withInput()->withErrors([
                    'max_products_per_day' => "Er kunnen maximaal {$maxPerDay} producten per dag verkocht worden. Er zijn er al {$totalProductsSold} besteld voor deze dag."
                ]);
            }
        }

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
            $order = Order::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'street' => $request->type === 'bezorgen' ? $request->straat : null,
                'housenumber' => $request->type === 'bezorgen' ? $request->housenumber : null,
                'addition' => $request->type === 'bezorgen' ? $request->addition : null,
                'postcode' => $request->type === 'bezorgen' ? $request->postcode : null,
                'type' => $request->type,
                'pickup_location' => $request->type === 'afhalen' ? $request->pickup_location : null,
                'pickup_date' => $request->type === 'afhalen' ? $request->pickup_date : null,
                'pickup_time' => $request->type === 'afhalen' ? $request->pickup_time : null,
                'delivery_date' => $request->type === 'bezorgen' ? $request->delivery_date : null,
                'total_price' => $grandTotal,
            ]);


            foreach ($cart as $productId => $types) {
                $product = $products->get($productId);
                if (!$product) continue;

                foreach ($types as $type => $data) {
                    OrderItem::create([
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
