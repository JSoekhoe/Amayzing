<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Services\DeliveryCheckerService;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartWithProducts = [];

        foreach ($cart as $productId => $types) {
            if (!isset($products[$productId])) {
                continue;
            }

            foreach ($types as $type => $data) {
                $cartWithProducts[$productId][$type] = [
                    'quantity' => $data['quantity'],
                    'product' => $products[$productId],
                ];
            }
        }

        $typesInCart = collect($cart)->flatMap(fn($types) => array_keys($types))->unique();

        $deliveryMethod = $typesInCart->count() === 1 ? $typesInCart->first() : 'afhalen';

        return view('cart.index', [
            'cart' => $cartWithProducts,
            'deliveryMethod' => $deliveryMethod,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:afhalen,bezorgen',
            'quantity' => 'required|integer|min:1',
        ]);

        $type = $request->input('type');
        $quantity = (int) $request->input('quantity');
        $postcode = $request->input('postcode');
        $housenumber = $request->input('housenumber');

        $cart = $request->session()->get('cart', []);
        $existingTypes = collect($cart)->flatMap(fn($types) => array_keys($types))->unique();

        if ($existingTypes->isNotEmpty() && !$existingTypes->contains($type)) {
            return redirect()->route('cart.index')->with('error', 'Je kunt niet afhalen en bezorgen combineren in één bestelling.');
        }

        $availableStock = $type === 'afhalen' ? $product->pickup_stock : $product->delivery_stock;
        $currentQty = $cart[$product->id][$type]['quantity'] ?? 0;

        if ($currentQty + $quantity > $availableStock) {
            return redirect()->route('cart.index')->with('error', 'Je kunt niet meer toevoegen dan de beschikbare voorraad.');
        }

        if ($type === 'bezorgen') {
            if (!$postcode || !$housenumber) {
                return redirect()->route('cart.index')->with('error', 'Postcode en huisnummer zijn verplicht voor bezorgen.');
            }

            $deliveryChecker = app(DeliveryCheckerService::class);
            $checkResult = $deliveryChecker->check($postcode, $housenumber);

            if (!$checkResult->allowed) {
                return redirect()->route('cart.index')->with('error', 'Bezorging is niet mogelijk op dit adres: ' . strip_tags($checkResult->message));
            }
        }

        $cart[$product->id][$type] = [
            'quantity' => $currentQty + $quantity,
        ];

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product toegevoegd!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:afhalen,bezorgen',
            'quantity' => 'required|integer|min:0',
        ]);

        $type = $request->input('type');
        $quantity = (int) $request->input('quantity');
        $cart = $request->session()->get('cart', []);

        $availableStock = $type === 'afhalen' ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity <= 0) {
            unset($cart[$product->id][$type]);
            if (empty($cart[$product->id])) {
                unset($cart[$product->id]);
            }
        } else {
            if ($quantity > $availableStock) {
                return redirect()->back()->with('error', "Maximale voorraad voor {$product->name} ({$type}) is {$availableStock}");
            }
            $cart[$product->id][$type] = [
                'quantity' => $quantity,
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "Aantal van {$product->name} ({$type}) is bijgewerkt.");
    }

    public function remove(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:afhalen,bezorgen',
        ]);

        $type = $request->input('type');
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id][$type])) {
            unset($cart[$product->id][$type]);
            if (empty($cart[$product->id])) {
                unset($cart[$product->id]);
            }
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product verwijderd.');
    }

    // Nieuwe methode voor de checkout pagina
    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartWithProducts = [];

        foreach ($cart as $productId => $types) {
            if (!isset($products[$productId])) {
                continue;
            }

            foreach ($types as $type => $data) {
                $cartWithProducts[$productId][$type] = [
                    'quantity' => $data['quantity'],
                    'product' => $products[$productId],
                ];
            }
        }

        $typesInCart = collect($cart)->flatMap(fn($types) => array_keys($types))->unique();

        $deliveryMethod = $typesInCart->count() === 1 ? $typesInCart->first() : 'afhalen';

        return view('checkout.index', [
            'cart' => $cartWithProducts,
            'deliveryMethod' => $deliveryMethod,
        ]);
    }
}
