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


        // Haal de bestellingen op, gesorteerd op afhaaldatum of bezorgdatum

        $pickupOrders = Order::with('items.product')
            ->where('type', 'afhalen')
            ->wherenotNull('paid_at')
            ->orderByRaw("CASE WHEN pickup_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('pickup_date', 'asc')
            ->get();


        $deliveryOrders = Order::with('items.product')
            ->where('type', 'bezorgen')
            ->wherenotNull('paid_at')
            ->orderByRaw("CASE WHEN delivery_date = ? THEN 0 ELSE 1 END", [$today])
            ->orderBy('delivery_date', 'asc')
            ->get();

        $deliveryChecker = app(\App\Services\DeliveryCheckerService::class);

        foreach ($deliveryOrders as $order) {
            $cacheKey = "city_lookup_{$order->postcode}_{$order->housenumber}_{$order->addition}";
            $order->city = Cache::remember($cacheKey, now()->addDay(), function() use ($deliveryChecker, $order) {
                $cityResponse = $deliveryChecker->check(
                    $order->postcode,
                    $order->housenumber,
                    $order->addition,
                    $order->type
                );
                return $cityResponse->woonplaats ?? null;
            });
        }

        return view('admin.orders.index', compact('pickupOrders', 'deliveryOrders',));
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
