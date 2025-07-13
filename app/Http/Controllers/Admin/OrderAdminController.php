<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
class OrderAdminController extends Controller
{

    public function index()
    {
        $today = Carbon::today()->toDateString();

        $pickupOrders = Order::with('items.product')
            ->where('type', 'afhalen')
            ->orderByRaw("CASE WHEN pickup_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('pickup_date', 'asc')
            ->get();

        $deliveryOrders = Order::with('items.product')
            ->where('type', 'bezorgen')
            ->orderByRaw("CASE WHEN delivery_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('delivery_date', 'asc')
            ->get();

        return view('admin.orders.index', compact('pickupOrders', 'deliveryOrders'));
    }



    public function show(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $order->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->back()->with('success', 'Status succesvol bijgewerkt.');
    }


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Bestelling verwijderd.');
    }


}
