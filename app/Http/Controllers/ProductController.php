<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DeliveryCheckerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request, DeliveryCheckerService $deliveryChecker)
    {
        $dayTranslations = [
            'monday'    => 'Maandag',
            'tuesday'   => 'Dinsdag',
            'wednesday' => 'Woensdag',
            'thursday'  => 'Donderdag',
            'friday'    => 'Vrijdag',
            'saturday'  => 'Zaterdag',
            'sunday'    => 'Zondag',
        ];

        $deliveryMethod = $request->input('delivery_method', 'afhalen');

        // postcode data (NL/BE)
        $postcode    = $request->input('postcode', session('postcode'));
        $housenumber = $request->input('housenumber', session('housenumber'));
        $addition    = $request->input('addition', session('addition'));
        $woonplaats  = $request->input('woonplaats', session('woonplaats'));

        // Belgische postcode => straatnaam input
        if ($postcode && preg_match('/^[1-9][0-9]{3}$/', $postcode)) {
            $straatnaam = $request->input('straatnaam', session('straatnaam'));
        } else {
            $straatnaam = null;
        }

        Log::info('Straatnaam ontvangen:', ['straatnaam' => $straatnaam]);

        // producten
        $lastIds = [32, 33];
        $query = Product::query();

        if ($deliveryMethod === 'afhalen') {
            $query->where('pickup_stock', '>', 0);
        } elseif ($deliveryMethod === 'bezorgen') {
            $query->where('delivery_stock', '>', 0);
        }

        $products = $query
            ->orderByRaw('CASE WHEN id IN (' . implode(',', $lastIds) . ') THEN 1 ELSE 0 END ASC')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        // bezorg check
        $deliveryAllowed = null;
        $deliveryMessage = '';

        if ($deliveryMethod === 'bezorgen') {
            $result = $deliveryChecker->check(
                $postcode,
                $housenumber,
                $addition,
                $deliveryMethod,
                null,
                $straatnaam,
                $woonplaats
            );

            $deliveryAllowed = $result->allowed;
            $deliveryMessage = $result->message;
        }

        // config
        $cities      = config('delivery.cities', []);
        $dateSchedule = config('delivery.date_schedule', []);

        $radiusKm    = (float) config('delivery.max_distance_km', 10);
        $orderCutoff = (string) config('delivery.last_order_time', '22:00');
        $deliveryEnd = (string) config('delivery.delivery_end_time', '20:30');

        $weekdayStart = (string) config('delivery.weekday_start_time', '13:00');
        $weekendStart = 'vanaf 11:00 uur'; // eventueel ook als config toevoegen

        $pickupLocations = config('pickup.locations', []);
        $pickupMessage   = (string) config('pickup.message', '');

        // vakantieperiode
        $holidayPeriods = [
            [
                'start' => Carbon::create(2026, 1, 1)->startOfDay(),
                'end'   => Carbon::create(2026, 1, 26)->endOfDay(),
            ],
        ];

        // helper: is vakantie?
        $isHoliday = function (Carbon $date) use ($holidayPeriods): bool {
            foreach ($holidayPeriods as $period) {
                if ($date->between($period['start'], $period['end'])) {
                    return true;
                }
            }
            return false;
        };

        // ✅ schedule builder: vaste offsets vanaf maandag (betrouwbaar)
        $weekDays = ['wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayOffsets = [
            'wednesday' => 2,
            'thursday'  => 3,
            'friday'    => 4,
            'saturday'  => 5,
            'sunday'    => 6,
        ];

        $buildScheduleForWeek = function (Carbon $startOfWeek) use (
            $weekDays,
            $dayOffsets,
            $dateSchedule,
            $cities,
            $dayTranslations,
            $isHoliday,
            $weekdayStart,
            $weekendStart
        ): array {
            $items = [];

            foreach ($weekDays as $day) {
                $date = $startOfWeek->copy()->addDays($dayOffsets[$day])->startOfDay();
                if ($isHoliday($date)) {
                    continue;
                }

                $dateKey = $date->format('Y-m-d');

                if (!isset($dateSchedule[$dateKey])) {
                    continue;
                }

                $cityKey = (string) $dateSchedule[$dateKey];

                // cityKey moet bestaan in cities
                if (!isset($cities[$cityKey])) {
                    continue;
                }

                // tijd: weekend vs weekday
                $time = in_array($day, ['saturday', 'sunday'], true)
                    ? ($cities[$cityKey]['delivery_time'] ?? $weekendStart)
                    : ($cities[$cityKey]['delivery_time'] ?? "van {$weekdayStart} tot " . (string) config('delivery.delivery_end_time', '20:30') . " uur");

                $items[] = [
                    'day'  => $dayTranslations[$day] ?? ucfirst($day),
                    'date' => $date->format('d-m-Y'),
                    'city' => ucfirst($cityKey),
                    'time' => $time,
                ];
            }

            return $items;
        };

        $startOfThisWeek = now()->startOfWeek(Carbon::MONDAY);
        $startOfNextWeek = now()->copy()->addWeek()->startOfWeek(Carbon::MONDAY);

        $scheduleThisWeek = $buildScheduleForWeek($startOfThisWeek);
        $scheduleNextWeek = $buildScheduleForWeek($startOfNextWeek);

        $weekNow  = $startOfThisWeek->week;
        $weekNext = $startOfNextWeek->week;

        // ✅ Zonder delivery_day: simpele teksten voor blade
        $deliveryStartWeekday = "van {$weekdayStart} tot {$deliveryEnd} uur";
        $deliveryStartWeekend = $weekendStart;

        return view('products.index', compact(
            'products',
            'deliveryMethod',
            'postcode',
            'housenumber',
            'addition',
            'deliveryAllowed',
            'deliveryMessage',
            'radiusKm',
            'orderCutoff',
            'deliveryEnd',
            'deliveryStartWeekday',
            'deliveryStartWeekend',
            'pickupLocations',
            'pickupMessage',
            'scheduleThisWeek',
            'scheduleNextWeek',
            'weekNow',
            'weekNext',
            'straatnaam',
            'woonplaats',
        ));
    }
}
