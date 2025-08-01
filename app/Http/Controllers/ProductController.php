<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DeliveryCheckerService;
use Carbon\Carbon;



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

        // Haal postcode, huisnummer en toevoeging uit request, of fallback naar sessie
        $postcode = $request->input('postcode', session('postcode'));
        $housenumber = $request->input('housenumber', session('housenumber'));
        $addition = $request->input('addition', session('addition'));

        // Haal producten op
        $query = Product::query();
        if ($deliveryMethod === 'afhalen') {
            $query->where('pickup_stock', '>', 0);
        } elseif ($deliveryMethod === 'bezorgen') {
            $query->where('delivery_stock', '>', 0);
        }
        $products = $query->paginate(9)->withQueryString();

        // Initieer bezorgstatus
        $deliveryAllowed = null;
        $deliveryMessage = '';

        if ($deliveryMethod === 'bezorgen') {
            $result = $deliveryChecker->check($postcode, $housenumber, $addition, $deliveryMethod);

            $deliveryAllowed = $result->allowed;
            $deliveryMessage = $result->message;
        }

        // Configs ophalen

        $cities = config('delivery.cities');
        $cities = array_map(function ($city) use ($dayTranslations) {
            $city['delivery_day'] = $dayTranslations[strtolower($city['delivery_day'])] ?? $city['delivery_day'];
            return $city;
        }, $cities);

        $radiusKm = config('delivery.max_distance_km');
        $orderCutoff = config('delivery.last_order_time');
        $deliveryEnd = config('delivery.delivery_end_time');
        $pickupLocations = config('pickup.locations');
        $pickupMessage = config('pickup.message');

        $weekdayCities = array_filter($cities, fn($city) =>
        in_array(strtolower($city['delivery_day']), ['maandag','dinsdag','woensdag','donderdag','vrijdag'])
        );
        $weekendCities = array_filter($cities, fn($city) =>
        in_array(strtolower($city['delivery_day']), ['zaterdag','zondag'])
        );

        $deliveryStartWeekday = count($weekdayCities) ? reset($weekdayCities)['delivery_time'] : '-';
        $deliveryStartWeekend = count($weekendCities) ? reset($weekendCities)['delivery_time'] : '-';
        $deliverySchedule = config('delivery.delivery_schedule');
        $fixedSchedule = config('delivery.fixed_schedule');
        $cities = config('delivery.cities');

// Weekdagen waarop bezorging mogelijk is
        $weekDays = ['wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

// Schedules voor huidige en volgende week
        $scheduleThisWeek = [];
        $scheduleNextWeek = [];

        $cities = config('delivery.cities'); // opnieuw nodig voor mapping
        $deliverySchedule = config('delivery.delivery_schedule');
        $fixedSchedule = config('delivery.fixed_schedule');

// Huidige week
        $weekNow = now()->week;
        $startOfThisWeek = now()->startOfWeek(Carbon::MONDAY);

        foreach ($weekDays as $day) {
            $date = $startOfThisWeek->copy()->next(ucfirst($day));

            if (in_array($day, ['wednesday', 'thursday']) && isset($deliverySchedule[$weekNow][$day])) {
                $cityKey = $deliverySchedule[$weekNow][$day];
            } elseif (isset($fixedSchedule[$day])) {
                $cityKey = $fixedSchedule[$day];
            } else {
                continue;
            }

            $cityData = $cities[$cityKey];
            $scheduleThisWeek[] = [
                'day' => $dayTranslations[$day] ?? ucfirst($day),
                'date' => $date->format('d-m-Y'),
                'city' => ucfirst($cityKey),
                'time' => $cityData['delivery_time'],
            ];
        }

// Volgende week
        $weekNext = now()->addWeek()->week;
        $startOfNextWeek = now()->addWeek()->startOfWeek(Carbon::MONDAY);

        foreach ($weekDays as $day) {
            $date = $startOfNextWeek->copy()->next(ucfirst($day));

            if (in_array($day, ['wednesday', 'thursday']) && isset($deliverySchedule[$weekNext][$day])) {
                $cityKey = $deliverySchedule[$weekNext][$day];
            } elseif (isset($fixedSchedule[$day])) {
                $cityKey = $fixedSchedule[$day];
            } else {
                continue;
            }

            $cityData = $cities[$cityKey];
            $scheduleNextWeek[] = [
                'day' => $dayTranslations[$day] ?? ucfirst($day),
                'date' => $date->format('d-m-Y'),
                'city' => ucfirst($cityKey),
                'time' => $cityData['delivery_time'],
            ];
        }


        return view('products.index', compact(
            'products',
            'deliveryMethod',
            'postcode',
            'housenumber',
            'addition',
            'deliveryAllowed',
            'deliveryMessage',
            'cities',
            'radiusKm',
            'orderCutoff',
            'deliveryEnd',
            'deliveryStartWeekday',
            'deliveryStartWeekend',
            'deliverySchedule',
            'pickupLocations',
            'pickupMessage',
            'scheduleThisWeek',
            'scheduleNextWeek',
            'weekNow',
            'weekNext'
        ));
    }
}
