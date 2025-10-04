<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $selectedWeek = $request->input('week', null); // weeknummer uit dropdown

        // Alle bestellingen ophalen met betaaldatum
        $allOrders = Order::whereNotNull('paid_at')->get();

// Unieke weeknummers uit de pickup_date / delivery_date halen
        $weeks = $allOrders->map(function ($order) {
            $date = $order->delivery_date ?? $order->pickup_date;
            return $date ? Carbon::parse($date)->startOfWeek() : null;
        })
            ->filter() // verwijder nulls
            ->unique() // alleen unieke weken
            ->sort()   // oplopend sorteren
            ->map(function ($weekStart) {
                return [
                    'number' => $weekStart->weekOfYear,
                    'label'  => 'Week ' . $weekStart->weekOfYear . ' (' . $weekStart->format('d M') . ' - ' . $weekStart->endOfWeek()->format('d M') . ')',
                ];
            })
            ->values();

        // Basisqueries
        $queryPickup = Order::with('items.product')
            ->where('type', 'afhalen')
            ->whereNotNull('paid_at');

        $queryDelivery = Order::with('items.product')
            ->where('type', 'bezorgen')
            ->whereNotNull('paid_at');

        // Filteren op weeknummer als gekozen
        if ($selectedWeek) {
            $queryPickup->whereRaw('WEEK(pickup_date, 1) = ?', [$selectedWeek]);
            $queryDelivery->whereRaw('WEEK(delivery_date, 1) = ?', [$selectedWeek]);
        }

        $pickupOrders = $queryPickup->orderBy('pickup_date')->get();
        $deliveryOrders = $queryDelivery->orderBy('delivery_date')->get();

        // weekdagen in juiste volgorde (NL)
        $dayOrder = [
            'maandag', 'dinsdag', 'woensdag',
            'donderdag', 'vrijdag', 'zaterdag', 'zondag'
        ];

        $salesByDay = Order::with(['items.product'])
            ->whereNotNull('paid_at')
            ->when($selectedWeek, function ($q) use ($selectedWeek) {
                $q->whereRaw('WEEK(COALESCE(delivery_date, pickup_date), 1) = ?', [$selectedWeek]);
            })
            ->get()
            ->flatMap->items
            ->groupBy(fn($item) => Carbon::parse(
                $item->order->delivery_date ?? $item->order->pickup_date
            )->locale('nl_NL')->dayName)
            ->map(fn($items) =>
            $items->groupBy('product.name')->map->sum('quantity')
            )
            // hier sorteren volgens $dayOrder
            ->sortBy(fn($_, $day) => array_search(strtolower($day), $dayOrder));

        return view('admin.orders.index', compact('pickupOrders', 'deliveryOrders', 'salesByDay', 'weeks', 'selectedWeek'));
    }


    public function show(Order $order)
    {
        $order->load('items.product');

        $cityResponse = app(\App\Services\DeliveryCheckerService::class)->check(
            $order->postcode,
            $order->housenumber,
            $order->addition,
            $order->type,
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
