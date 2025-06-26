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
        return view('cart.index', compact('cart'));
    }

    public function add(Product $product)
    {
        $cart = Session::get('cart', []);
        $cart[$product->id] = [
            'product' => $product,
            'quantity' => ($cart[$product->id]['quantity'] ?? 0) + 1
        ];
        Session::put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Product toegevoegd!');
    }

    public function remove(Product $product)
    {
        $cart = Session::get('cart', []);
        unset($cart[$product->id]);
        Session::put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Product verwijderd.');
    }
}
