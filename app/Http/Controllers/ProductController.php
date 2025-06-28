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
        $products = Product::paginate(9);

        $selectedDeliveryMethod = $request->input('delivery_method', 'afhalen');
        $postcode = $request->input('postcode');
        $housenumber = $request->input('housenumber');

        $deliveryAllowed = null;
        $deliveryMessage = '';
        $addressResolved = null;

        $pickupAddress = Config::get('delivery.pickup.address');
        $pickupHours = Config::get('delivery.pickup.opening_hours');
        $pickupMessage = Config::get('delivery.pickup.message');

        if ($selectedDeliveryMethod === 'bezorgen') {
            if ($postcode && $housenumber) {
                $deliveryCheckResult = $deliveryChecker->check($postcode, $housenumber);

                $deliveryAllowed = $deliveryCheckResult->allowed;
                $deliveryMessage = $deliveryCheckResult->message;
                $addressResolved = $deliveryCheckResult->address;
            } else {
                $deliveryAllowed = false;
                $deliveryMessage = 'Voer een postcode en huisnummer in om bezorging te kunnen controleren.';
            }
        }

        $cities = Config::get('delivery.cities', []);

        return view('products.index', compact(
            'products',
            'selectedDeliveryMethod',
            'postcode',
            'housenumber',
            'deliveryAllowed',
            'deliveryMessage',
            'addressResolved',
            'cities',
            'pickupAddress',
            'pickupHours',
            'pickupMessage',
        ));
    }
}
