<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        $productIds = array_keys($cart);

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Combineer cart met producten voor de view
        $cartWithProducts = [];

        foreach ($cart as $productId => $types) {
            if (!isset($products[$productId])) {
                continue; // Product niet gevonden, overslaan
            }

            foreach ($types as $type => $data) {
                $cartWithProducts[$productId][$type] = [
                    'quantity' => $data['quantity'],
                    'product' => $products[$productId],
                ];
            }
        }

        return view('cart.index', ['cart' => $cartWithProducts]);
    }


    public function add(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:afhalen,bezorgen',
        ]);

        $type = $request->input('type');
        $cart = Session::get('cart', []);

        $currentQty = $cart[$product->id][$type]['quantity'] ?? 0;

        $availableStock = $type === 'afhalen' ? $product->pickup_stock : $product->delivery_stock;

        if ($availableStock <= $currentQty) {
            return redirect()->route('cart.index')->with('error', 'Je kunt niet meer toevoegen dan de beschikbare voorraad.');
        }

        $cart[$product->id][$type] = [
            'quantity' => $currentQty + 1,
        ];

        Session::put('cart', $cart);

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
        $cart = Session::get('cart', []);

        $availableStock = $type === 'afhalen' ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity <= 0) {
            unset($cart[$product->id][$type]);
            // Indien geen types meer voor dit product, unset product key
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

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "Aantal van {$product->name} ({$type}) is bijgewerkt.");
    }

    public function remove(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|in:afhalen,bezorgen',
        ]);

        $type = $request->input('type');
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id][$type])) {
            unset($cart[$product->id][$type]);
            if (empty($cart[$product->id])) {
                unset($cart[$product->id]);
            }
        }

        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Product verwijderd.');
    }
}
