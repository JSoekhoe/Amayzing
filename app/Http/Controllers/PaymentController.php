<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Mollie\Api\MollieApiClient;

class PaymentController extends Controller
{
    public function paymentCheckout($orderId)
    {
        $order = Order::findOrFail($orderId);
        $amount = number_format($order->total_price, 2, '.', ''); // Mollie verwacht string in 2 decimalen

        return view('payment.checkout', compact('order', 'amount'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $amount = number_format($request->input('amount'), 2, '.', '');

        $mollie = new MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_KEY'));

        try {
            $order = \App\Models\Order::findOrFail($request->input('order_id'));

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $amount, // string met 2 decimalen
                ],
                "description" => "Bestelling #{$order->id}",
                "redirectUrl" => route('thankyou'), // waar gebruiker terugkomt na betaling
                "metadata" => [
                    "order_id" => $order->id,
                ],
            ]);

            // Optioneel: sla Mollie payment ID op in order voor later check
            $order->update(['payment_id' => $payment->id]);

            return response()->json([
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Betaling kon niet worden aangemaakt: ' . $e->getMessage(),
            ], 500);
        }
    }
}
