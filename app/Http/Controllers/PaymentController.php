<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Mollie\Api\MollieApiClient;
use Illuminate\Support\Facades\Mail;


class PaymentController extends Controller
{
    public function paymentCheckout($orderId)
    {
        $order = Order::findOrFail($orderId);
        $amount = number_format($order->total_price, 2, '.', '');

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
            $order = Order::findOrFail($request->input('order_id'));

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $amount,
                ],
                "description" => "Bestelling #{$order->id}",
                "redirectUrl" => route('thankyou', ['orderId' => $order->id], true),
                "webhookUrl" => route('mollie.webhook', [], true),

                "metadata" => [
                    "order_id" => $order->id,
                ],
            ]);

            \Log::info('Mollie Payment aangemaakt', [
                'payment_id' => $payment->id,
                'order_id' => $order->id]);

            $order->update(['payment_id' => $payment->id]);

            // Mail direct versturen
//            Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
//            Mail::to('amayzingpastry@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

            return response()->json([
                'checkoutUrl' => $payment->getCheckoutUrl(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Betaling kon niet worden aangemaakt: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $mollie = new MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_KEY'));

        $paymentId = $request->input('id');

        try {
            $payment = $mollie->payments->get($paymentId);
            $orderId = $payment->metadata->order_id;

            $order = Order::with('items.product')->find($orderId);

            if (!$order) {
                \Log::warning("Webhook: Geen order gevonden voor betaling ID: {$paymentId}");
                return response('Order not found', 404);
            }

            if ($payment->isPaid() && !$order->paid_at) {
                $order->update([
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                \Log::info("Order #{$order->id} status bijgewerkt naar 'paid' en betaaldatum ingesteld.");

                // Mail versturen
                Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
                Mail::to('amayzingpastry@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

                \Log::info("Order confirmation mails verzonden voor order #{$order->id}");
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            \Log::error('Mollie webhook fout: ' . $e->getMessage());
            return response('Webhook error', 500);
        }
    }

}
