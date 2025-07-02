<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\DeliveryCheckerService;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    public function index(Request $request, DeliveryCheckerService $deliveryChecker)
    {
        $deliveryMethod = $request->input('delivery_method', 'afhalen');
        $postcode = $request->input('postcode');
        $housenumber = $request->input('housenumber');
        $addition = $request->input('addition');  // Toegevoeging

        $query = Product::query();
        if ($deliveryMethod === 'afhalen') {
            $query->where('pickup_stock', '>', 0);
        } elseif ($deliveryMethod === 'bezorgen') {
            $query->where('delivery_stock', '>', 0);
        }
        $products = $query->paginate(9)->withQueryString();

        $deliveryAllowed = null;
        $deliveryMessage = '';

        if ($deliveryMethod === 'bezorgen') {
            if ($postcode && $housenumber) {
                // Nu met addition doorgegeven
                $deliveryCheckResult = $deliveryChecker->check($postcode, $housenumber, $addition);

                $deliveryAllowed = $deliveryCheckResult->allowed;
                $deliveryMessage = $deliveryCheckResult->message;
            } else {
                $deliveryAllowed = false;
                $deliveryMessage = 'Voer een postcode en huisnummer in om bezorging te kunnen controleren.';
            }
        }

        // Bezorg- en Afhaalinformatie vanuit config laden
        $cities = config('delivery.cities');
        $radiusKm = config('delivery.max_distance_km');
        $orderCutoff = config('delivery.last_order_time');
        $deliveryEnd = config('delivery.delivery_end_time');
        $pickupLocations = config('pickup.locations');
        $pickupMessage = config('pickup.message');


        // Bereken starttijd voor weekdagen & weekend (voorbeeld)
        $weekdayCities = array_filter($cities, function ($city) {
            return in_array(strtolower($city['delivery_day']), ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        });
        $weekendCities = array_filter($cities, function ($city) {
            return in_array(strtolower($city['delivery_day']), ['saturday', 'sunday']);
        });

        // Pak de eerste delivery_time voor weekdagen en weekend (kan je naar wens aanpassen)
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
