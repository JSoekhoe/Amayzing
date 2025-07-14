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
        $deliveryMethod = $request->input('type', session('delivery_method', 'afhalen'));
        session(['delivery_method' => $deliveryMethod]);

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $productId => &$types) {
            foreach ($types as $type => &$item) {
                if (isset($products[$productId])) {
                    $item['product'] = $products[$productId];
                } else {
                    unset($cart[$productId][$type]);
                }
            }
            if (empty($cart[$productId])) {
                unset($cart[$productId]);
            }
        }
        unset($types, $item);

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

            session([
                'postcode' => $postcode,
                'housenumber' => $housenumber,
                'straat' => $checkResult->street ?? '',
                'addition' => $addition,
                'delivery_check_passed' => true,
            ]);
        }

        $cart = session('cart', []);

        // Verbied mixen van afhalen en bezorgen
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
            ];
        }

//        if ($type === 'bezorgen') {
//            $total = $this->calculateCartTotal($cart);
//            $minOrderAmount = 40;
//            if ($total < $minOrderAmount) {
//                return redirect()->back()->with('error', "Voor bezorging is het minimale bestelbedrag €{$minOrderAmount}. Je huidige bestelling is €" . number_format($total, 2));
//            }
//        }

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

        // Controleer mix afhalen/bezorgen
        foreach ($cart as $productId => $types) {
            foreach ($types as $existingType => $item) {
                if ($existingType !== $type) {
                    session(['cart' => []]);
                    return redirect()->back()->with('error', 'Je kunt niet afhalen en bezorgen combineren. De winkelwagen is geleegd.');
                }
            }
        }

        $maxStock = ($type === 'afhalen') ? $product->pickup_stock : $product->delivery_stock;

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Het gevraagde aantal overschrijdt de beschikbare voorraad.');
        }

        $cart[$product->id][$type]['quantity'] = $quantity;

//        // Controle minimale bestelbedrag bij bezorgen
//        if ($type === 'bezorgen') {
//            $total = $this->calculateCartTotal($cart);
//            $minOrderAmount = 40;
//            if ($total < $minOrderAmount) {
//                return redirect()->back()->with('error', "Voor bezorging is het minimale bestelbedrag €{$minOrderAmount}. Je huidige bestelling is €" . number_format($total, 2));
//            }
//        }

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
    protected function calculateCartTotal(array $cart): float
    {
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;

        foreach ($cart as $productId => $types) {
            foreach ($types as $type => $item) {
                if (isset($products[$productId])) {
                    $price = $products[$productId]->price;
                    $quantity = $item['quantity'];
                    $total += $price * $quantity;
                }
            }
        }

        return $total;
    }
}
