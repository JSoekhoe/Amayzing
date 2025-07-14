<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;

class ThankYouController extends Controller
{
    public function thankyou(Request $request)
    {
        $orderId = $request->query('orderId');
        $order = Order::findOrFail($orderId);

        $mollie = new MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_KEY'));

        try {
            $payment = $mollie->payments->get($order->payment_id);

            if ($payment->isPaid()) {
                return view('checkout.thankyou', compact('order'));
            } else {
                // Eventueel status opslaan:
                $order->update(['status' => $payment->status]);

                return view('checkout.failed', compact('order'));
            }
        } catch (\Exception $e) {
            \Log::error("Fout bij ophalen betaling na redirect: " . $e->getMessage());
            return view('checkout.failed', compact('order'));
        }
    }
}

