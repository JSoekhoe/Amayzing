<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DeliveryCheckerService;

class ProductController extends Controller
{
    public function index(Request $request, DeliveryCheckerService $deliveryChecker)
    {

        $dayTranslations = [
            'monday' => 'maandag',
            'tuesday' => 'dinsdag',
            'wednesday' => 'woensdag',
            'thursday' => 'donderdag',
            'friday' => 'vrijdag',
            'saturday' => 'zaterdag',
            'sunday' => 'zondag',
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
            'pickupLocations',
            'pickupMessage'
        ));
    }
}
