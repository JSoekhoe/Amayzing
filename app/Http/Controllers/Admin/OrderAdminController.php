<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TimeslotNotification;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        // Alleen betaalde orders tonen, maar week = delivery/pickup date
        $selectedWeekKey = $request->input('week'); // bv "2026-W08" of null

        // ------------------------------------------------------------
        // 1) Weeks dropdown: alleen betaalde orders + order_date bestaat
        // ------------------------------------------------------------
        $weeks = Cache::remember('admin_orders_weeks_paid_only', 600, function () {
            $weekStarts = Order::query()
                ->whereNotNull('paid_at')
                ->whereRaw('COALESCE(delivery_date, pickup_date) IS NOT NULL')
                ->selectRaw("
                    DATE_SUB(
                        COALESCE(delivery_date, pickup_date),
                        INTERVAL WEEKDAY(COALESCE(delivery_date, pickup_date)) DAY
                    ) as week_start
                ")
                ->distinct()
                ->orderBy('week_start')
                ->pluck('week_start');

            return $weekStarts->map(function ($weekStart) {
                $start = Carbon::parse($weekStart)->startOfWeek(); // maandag
                $end   = $start->copy()->endOfWeek();              // zondag

                return [
                    'key'    => $start->format('o-\WW'), // "2026-W08"
                    'number' => $start->weekOfYear,
                    'label'  => 'Week ' . $start->weekOfYear . ' (' .
                        $start->format('d M') . ' - ' . $end->format('d M') . ')',
                ];
            })->values();
        });

        // Zet selected week om naar start/end datum range (of null)
        [$weekStart, $weekEnd] = $this->weekKeyToRange($selectedWeekKey);

        // ------------------------------------------------------------
        // 2) Orders: paginate + eager loading, betaald + filter op order_date
        // ------------------------------------------------------------
        $pickupQuery = Order::query()
            ->where('type', 'afhalen')
            ->whereNotNull('paid_at')
            ->whereRaw('COALESCE(delivery_date, pickup_date) IS NOT NULL')
            ->with(['items.product'])
            ->when($weekStart && $weekEnd, function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween(DB::raw("COALESCE(delivery_date, pickup_date)"), [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ]);
            })
            ->orderByRaw("COALESCE(delivery_date, pickup_date) ASC");

        $deliveryQuery = Order::query()
            ->where('type', 'bezorgen')
            ->whereNotNull('paid_at')
            ->whereRaw('COALESCE(delivery_date, pickup_date) IS NOT NULL')
            ->with(['items.product'])
            ->when($weekStart && $weekEnd, function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween(DB::raw("COALESCE(delivery_date, pickup_date)"), [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ]);
            })
            ->orderByRaw("COALESCE(delivery_date, pickup_date) ASC");

        // verschillende page-names zodat paginate niet botst
        $pickupOrders   = $pickupQuery->paginate(15, ['*'], 'pickup_page')->withQueryString();
        $deliveryOrders = $deliveryQuery->paginate(15, ['*'], 'delivery_page')->withQueryString();

        // ------------------------------------------------------------
        // 3) Sales by day: alleen betaalde orders + filter op order_date
        // ------------------------------------------------------------
        $salesByDay = Cache::remember(
            'admin_sales_by_day_paid_only_' . ($selectedWeekKey ?: 'all'),
            300,
            function () use ($weekStart, $weekEnd) {
                $rows = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'products.id', '=', 'order_items.product_id')
                    ->whereNotNull('orders.paid_at')
                    ->whereRaw('COALESCE(orders.delivery_date, orders.pickup_date) IS NOT NULL')
                    ->when($weekStart && $weekEnd, function ($q) use ($weekStart, $weekEnd) {
                        $q->whereBetween(
                            DB::raw("COALESCE(orders.delivery_date, orders.pickup_date)"),
                            [$weekStart->toDateString(), $weekEnd->toDateString()]
                        );
                    })
                    ->selectRaw("
                        WEEKDAY(COALESCE(orders.delivery_date, orders.pickup_date)) as weekday,
                        products.name as product_name,
                        SUM(order_items.quantity) as qty
                    ")
                    ->groupBy('weekday', 'products.name')
                    ->orderBy('weekday')
                    ->orderBy('products.name')
                    ->get();

                $weekdayMap = [
                    0 => 'maandag',
                    1 => 'dinsdag',
                    2 => 'woensdag',
                    3 => 'donderdag',
                    4 => 'vrijdag',
                    5 => 'zaterdag',
                    6 => 'zondag',
                ];

                $out = collect();

                foreach ($rows as $r) {
                    $dayName = $weekdayMap[(int)$r->weekday] ?? 'onbekend';
                    if (!$out->has($dayName)) {
                        $out->put($dayName, collect());
                    }
                    $out[$dayName]->put($r->product_name, (int) $r->qty);
                }

                $dayOrder = ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag'];
                return $out->sortBy(fn($_, $day) => array_search($day, $dayOrder));
            }
        );

        // ------------------------------------------------------------
        // 4) Sales by type: alleen betaalde orders + filter op order_date
        // ------------------------------------------------------------
        $salesByType = Cache::remember(
            'admin_sales_by_type_paid_only_' . ($selectedWeekKey ?: 'all'),
            300,
            function () use ($weekStart, $weekEnd) {
                $rows = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'products.id', '=', 'order_items.product_id')
                    ->whereNotNull('orders.paid_at')
                    ->whereRaw('COALESCE(orders.delivery_date, orders.pickup_date) IS NOT NULL')
                    ->when($weekStart && $weekEnd, function ($q) use ($weekStart, $weekEnd) {
                        $q->whereBetween(
                            DB::raw("COALESCE(orders.delivery_date, orders.pickup_date)"),
                            [$weekStart->toDateString(), $weekEnd->toDateString()]
                        );
                    })
                    ->selectRaw("
                        orders.type as order_type,
                        products.name as product_name,
                        SUM(order_items.quantity) as qty
                    ")
                    ->groupBy('orders.type', 'products.name')
                    ->orderBy('products.name')
                    ->get();

                $out = [
                    'bezorgen' => collect(),
                    'afhalen'  => collect(),
                ];

                foreach ($rows as $r) {
                    $type = $r->order_type;
                    if (!isset($out[$type])) {
                        $out[$type] = collect();
                    }
                    $out[$type]->put($r->product_name, (int) $r->qty);
                }

                return $out;
            }
        );

        $selectedWeek = $selectedWeekKey;

        return view('admin.orders.index', compact(
            'pickupOrders',
            'deliveryOrders',
            'salesByDay',
            'salesByType',
            'weeks',
            'selectedWeek'
        ));
    }

    /**
     * Converteer "2026-W08" naar [start,end] (Carbon) of [null,null]
     */
    private function weekKeyToRange(?string $weekKey): array
    {
        if (!$weekKey) return [null, null];

        if (!preg_match('/^(\d{4})-W(\d{2})$/', $weekKey, $m)) {
            return [null, null];
        }

        $year = (int) $m[1];
        $week = (int) $m[2];

        $start = Carbon::now()->setISODate($year, $week)->startOfWeek(); // maandag
        $end   = $start->copy()->endOfWeek();                            // zondag

        return [$start, $end];
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
        $today = Carbon::today();

        $orders = Order::query()
            ->whereNotNull('paid_at')
            ->whereDate(DB::raw("COALESCE(delivery_date, pickup_date)"), $today)
            ->with('items.product')
            ->orderByRaw('timeslot IS NULL DESC')
            ->orderBy('timeslot')
            ->get();

        $slots = [];
        $start = Carbon::createFromTime(11, 0);
        $end   = Carbon::createFromTime(20, 30);

        while ($start->lessThan($end)) {
            $slotStart = $start->copy();
            $slotEnd   = $start->copy()->addHours(2);

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

        if ($order->email) {
            Mail::to($order->email)->send(new TimeslotNotification($order, $order->timeslot));
        }

        return redirect()->back()->with('success', 'Tijdslot succesvol toegewezen en e-mail verstuurd!');
    }
}
