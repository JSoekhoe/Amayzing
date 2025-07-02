<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DeliveryCheckerService;

class CartController extends Controller
{
    protected $deliveryChecker;

    public function __construct(DeliveryCheckerService $deliveryChecker)
    {
        $this->deliveryChecker = $deliveryChecker;
    }

    // Toon de winkelwagen
    public function index(Request $request)
    {
        $cart = session('cart', []);

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Verrijk de cart data met actuele product modellen
        foreach ($cart as $productId => &$types) {
            foreach ($types as $type => &$item) {
                if (isset($products[$productId])) {
                    $item['product'] = $products[$productId];  // Eloquent model toevoegen
                } else {
                    // Product bestaat niet meer, verwijderen uit cart
                    unset($cart[$productId][$type]);
                }
            }
            if (empty($cart[$productId])) {
                unset($cart[$productId]);
            }
        }
        unset($types, $item);

        $deliveryMethod = $request->input('type', 'bezorgen');

        return view('cart.index', compact('cart', 'deliveryMethod'));
    }

    // Voeg een product toe aan de cart
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|string|in:afhalen,bezorgen',
            'postcode' => 'nullable|string',
            'housenumber' => 'nullable|string',
            'addition' => 'nullable|string',
        ]);

        $quantity = $request->input('quantity');
        $type = $request->input('type');

        if ($type === 'bezorgen') {
            $postcode = $request->input('postcode');
            $housenumber = $request->input('housenumber');
            $addition = $request->input('addition');

            $checkResult = $this->deliveryChecker->check($postcode, $housenumber, $addition, 'bezorgen');

            if (!$checkResult->allowed) {
                return redirect()->back()->with('error', 'Bezorging is niet mogelijk op dit adres: ' . $checkResult->message);
            }

            // Opslaan in sessie zodat gegevens niet verloren gaan
            session([
                'postcode' => $postcode,
                'housenumber' => $housenumber,
                'addition' => $addition,
                'delivery_check_passed' => true,
            ]);
        }

        $cart = session('cart', []);

        // Verbied mixen van afhalen en bezorgen in 1 cart
        foreach ($cart as $productId => $types) {
            foreach ($types as $existingType => $item) {
                if ($existingType !== $type) {
                    session(['cart' => []]);
                    return redirect()->back()->with('error', 'Je kunt niet afhalen en bezorgen combineren. De winkelwagen is geleegd.');
                }
            }
        }

        if (!isset($cart[$product->id])) {
            $cart[$product->id] = [];
        }

        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Het gevraagde aantal overschrijdt de beschikbare voorraad.');
        }

        if (isset($cart[$product->id][$type])) {
            $newQuantity = $cart[$product->id][$type]['quantity'] + $quantity;
            if ($newQuantity > $maxStock) {
                return redirect()->back()->with('error', 'Het totale aantal overschrijdt de beschikbare voorraad.');
            }
            $cart[$product->id][$type]['quantity'] = $newQuantity;
        } else {
            $cart[$product->id][$type] = [
                'quantity' => $quantity,
                // Product details worden pas in index() toegevoegd als model
            ];
        }

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Product succesvol toegevoegd aan je winkelwagen.');
    }

    // Update aantal van een product in de cart
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|string|in:afhalen,bezorgen',
        ]);

        $quantity = $request->input('quantity');
        $type = $request->input('type');

        $cart = session('cart', []);

        if (!isset($cart[$product->id][$type])) {
            return redirect()->back()->with('error', 'Product niet gevonden in de winkelwagen.');
        }

        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Het gevraagde aantal overschrijdt de beschikbare voorraad.');
        }

        $cart[$product->id][$type]['quantity'] = $quantity;

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Aantal succesvol bijgewerkt.');
    }

    // Verwijder een product uit de cart
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

        return redirect()->back()->with('success', 'Product succesvol verwijderd.');
    }
}
