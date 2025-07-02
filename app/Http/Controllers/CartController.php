<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Toon de winkelwagen
    public function index(Request $request)
    {
        // Haal de cart uit de sessie, of lege array als niet aanwezig
        $cart = session('cart', []);

        // Dit kan eventueel aangepast worden afhankelijk van jouw logica
        $deliveryMethod = $request->input('type', 'delivery');

        return view('cart.index', compact('cart', 'deliveryMethod'));
    }

    // Voeg een product toe aan de cart
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|string|in:afhalen,bezorgen',
        ]);

        $quantity = $request->input('quantity');
        $type = $request->input('type');

        $cart = session('cart', []);

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [];
        }

        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Het gevraagde aantal overschrijdt de beschikbare voorraad.');
        }

        // Als product met dit type al in cart zit, tel het aantal erbij op
        if (isset($cart[$product->id][$type])) {
            $newQuantity = $cart[$product->id][$type]['quantity'] + $quantity;
            if ($newQuantity > $maxStock) {
                return redirect()->back()->with('error', 'Het totale aantal overschrijdt de beschikbare voorraad.');
            }
            $cart[$product->id][$type]['quantity'] = $newQuantity;
        } else {
            $cart[$product->id][$type] = [
                'quantity' => $quantity,
                'product' => $product,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Product toegevoegd aan winkelwagen.');
    }

    // Update het aantal van een product in de cart
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|string|in:afhalen,bezorgen', // afhalen of bezorgen
        ]);

        $quantity = $request->input('quantity');
        $type = $request->input('type');

        $cart = session('cart', []);

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [];
        }

        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Het gevraagde aantal overschrijdt de beschikbare voorraad.');
        }

        $cart[$product->id][$type] = [
            'quantity' => $quantity,
            'product' => $product,
        ];

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Aantal bijgewerkt.');
    }

    // Verwijder product uit cart
    public function remove(Request $request, Product $product)
    {
        $request->validate([
            'type' => 'required|string|in:afhalen,bezorgen',
        ]);

        $type = $request->input('type');

        $cart = session('cart', []);

        if (isset($cart[$product->id][$type])) {
            unset($cart[$product->id][$type]);

            if (empty($cart[$product->id])) {
                unset($cart[$product->id]);
            }
        }

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Product verwijderd uit winkelwagen.');
    }
}
