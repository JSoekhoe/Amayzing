<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Haal winkelwagen uit sessie
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        // Delivery method ophalen, standaard 'bezorgen'
        $deliveryMethod = $request->input('type', 'bezorgen');

        // Bereken totaal en bezorgkosten
        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = Product::find($productId);
            if (!$product) continue;

            foreach ($types as $type => $data) {
                $subtotal = $product->price * $data['quantity'];
                $total += $subtotal;
            }
        }

        $deliveryFee = ($deliveryMethod === 'bezorgen' && $total < 99) ? 5.50 : 0;
        $grandTotal = $total + $deliveryFee;

        // Minimum tijd ophalen voor afhalen (bijv. 11:00 in weekend, 14:00 doordeweeks)
        $dayName = strtolower(now()->locale('nl')->dayName);
        $minPickupTime = in_array($dayName, ['zaterdag', 'zondag']) ? '11:00' : '14:00';

        return view('checkout.index', compact(
            'cart',
            'deliveryMethod',
            'total',
            'deliveryFee',
            'grandTotal',
            'minPickupTime'
        ));
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
            'phone' => 'required|string',
            'type' => 'required|in:afhalen,bezorgen',
            'pickup_time' => 'required_if:type,afhalen|date_format:H:i',
            'address' => 'required_if:type,bezorgen|string|max:255',
            'postcode' => 'required_if:type,bezorgen|string|max:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        // Custom voorraad check
        $validator->after(function ($validator) use ($cart, $request) {
            foreach ($cart as $productId => $types) {
                $product = Product::find($productId);
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

        // Bereken totaalprijs opnieuw (voor zekerheid)
        $total = 0;
        foreach ($cart as $productId => $types) {
            $product = Product::find($productId);
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
                'postcode' => $request->type === 'bezorgen' ? $request->postcode : null,
                'type' => $request->type,
                'pickup_time' => $request->type === 'afhalen' ? $request->pickup_time : null,
                'total_price' => $grandTotal,
            ]);

            foreach ($cart as $productId => $types) {
                $product = Product::find($productId);
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

            // Laad de bestelling met items
            $order->load('items.product');

            // Verstuur bevestigingsmails
            Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
            Mail::to('soekhoe.j@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

            // Maak winkelwagen leeg
            $request->session()->forget('cart');

            return redirect()->route('payment.checkout', ['order' => $order->id])
                ->with('success', 'Bestelling succesvol geplaatst. Ga verder met betalen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors([
                'error' => 'Er is iets misgegaan bij het plaatsen van de bestelling. Probeer het later opnieuw.',
            ]);
        }
    }
}
