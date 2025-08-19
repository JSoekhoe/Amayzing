<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OrderAdminController extends Controller
{

    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Haal de afhaalbestellingen op, gesorteerd op afhaaldatum
        $pickupOrders = Order::with('items.product')
            ->where('type', 'afhalen')
            ->wherenotNull('paid_at')
            ->orderByRaw("CASE WHEN pickup_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('pickup_date', 'asc')
            ->get();

        // Haal de bezorgbestellingen op, gesorteerd op bezorgdatum
        $deliveryOrders = Order::with('items.product')
            ->where('type', 'bezorgen')
            ->wherenotNull('paid_at')
            ->orderByRaw("CASE WHEN delivery_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('delivery_date', 'asc')
            ->get();

        // Geen DeliveryCheckerService meer nodig
        return view('admin.orders.index', compact('pickupOrders', 'deliveryOrders'));
    }


    public function show(Order $order)
    {
        $order->load('items.product');

        $cityResponse = app(\App\Services\DeliveryCheckerService::class)->check(
            $order->postcode,
            $order->housenumber,
            $order->addition,
            $order->type, // 'afhalen' of 'bezorgen'
        );
        $city = $cityResponse->woonplaats ?? null;

        return view('admin.orders.show', compact('order', 'city'));
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
