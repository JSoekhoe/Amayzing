<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\TimeslotNotification;
use Illuminate\Support\Facades\Mail;

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

    public function today()
    {
        $today = \Carbon\Carbon::today();

        $orders = Order::whereDate('delivery_date', $today)
            ->whereNotNull('paid_at')
            ->with('items.product')
            ->orderByRaw('timeslot IS NULL DESC')  // NULL eerst
            ->orderBy('timeslot')                 // daarna op tijdslot oplopend
            ->get();


        // Maak tijdslots (2 uur blokken vanaf 11:00 tot 20:30)
        $slots = [];
        $start = Carbon::createFromTime(11, 0);
        $end = Carbon::createFromTime(20, 30);

        while ($start->lessThan($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addHours(2);

            if ($slotEnd->greaterThan($end)) {
                $slotEnd = $end;
            }

            $slots[] = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');
            $start->addHours(2);
        }

        return view('admin.orders.today', compact('orders', 'slots'));
    }

    public function assignTimeslot(Request $request, Order $order)
    {
        $request->validate([
            'timeslot' => 'required|string',
        ]);

        $order->update([
            'timeslot' => $request->input('timeslot'),
        ]);

        // Verstuur e-mail naar de klant
        if($order->email) {
            Mail::to($order->email)->send(new TimeslotNotification($order, $order->timeslot));
        }

        return redirect()->back()->with('success', 'Tijdslot succesvol toegewezen en e-mail verstuurd!');
    }
}

