<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Mollie\Api\MollieApiClient;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    // Show the payment checkout page
    public function paymentCheckout($orderId)
    {
        $order = Order::findOrFail($orderId);
        $amount = number_format($order->total_price, 2, '.', '');

        // Pass to the Blade view
        return view('payment.checkout', compact('order', 'amount'));
    }

    // Process the payment creation via Mollie API
    public function process(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->input('order_id'));

        $amount = number_format($order->total_price, 2, '.', '');
        $mollie = new \Mollie\Api\MollieApiClient();

        $mollie->setApiKey(config('services.mollie.key'));

        try {
            $order = Order::findOrFail($request->input('order_id'));

            $payment = $mollie->payments->create([
                "amount" => [
                    "currency" => "EUR",
                    "value" => $amount,
                ],
                "description" => "Bestelling #{$order->id}",
                "redirectUrl" => route('thankyou', ['orderId' => $order->id], true),
                'webhookUrl' => 'https://jamaytuller.com/webhook/mollie',
//                'webhookUrl' => 'https://bc0838a81c7f.ngrok-free.app/webhook/mollie',
                "metadata" => [
                    "order_id" => $order->id,
                ],
            ]);

            $order->update(['payment_id' => $payment->id]);

            return redirect()->away($payment->getCheckoutUrl());
        } catch (\Exception $e) {
            \Log::error('Payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Betaling kon niet worden gestart. Probeer opnieuw.');
        }
    }

    // Mollie webhook handler to update order status after payment status change
    public function webhook(Request $request)
    {
        $mollie = new MollieApiClient();
        $mollie->setApiKey(config('services.mollie.key'));

        $paymentId = $request->input('id');

        try {
            if (!$paymentId) {
                return response('Payment ID ontbreekt', 400);
            }

            $payment = $mollie->payments->get($paymentId);
            $orderId = $payment->metadata->order_id ?? null;

            if (!$orderId) {
                return response('Metadata order_id ontbreekt', 400);
            }

            $order = Order::with('items.product')->find($orderId);

            if (!$order) {
                return response('Order not found', 404);
            }

            if ($payment->status === 'paid' && !$order->paid_at) {

                $order->update([
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                \Log::info("Order #{$order->id} succesvol betaald.");

                // Klantbevestiging
                try {
                    Mail::to($order->email)
                        ->send(new \App\Mail\OrderConfirmation($order));

                    \Log::info("Klantmail verzonden voor order #{$order->id}.");
                } catch (\Exception $e) {
                    \Log::error("Klantmail mislukt voor order #{$order->id}: " . $e->getMessage());
                }

                // Bedrijfsbevestiging
                try {
                    Mail::to('amayzingpastry@gmail.com')
                        ->send(new \App\Mail\OrderConfirmation($order));

                    \Log::info("Bedrijfsmail verzonden voor order #{$order->id}.");
                } catch (\Exception $e) {
                    \Log::error("Bedrijfsmail mislukt voor order #{$order->id}: " . $e->getMessage());
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            \Log::error('Mollie webhook fout: ' . $e->getMessage());

            return response('Webhook error', 500);
        }
    }

    // Show thank you page after successful payment
    public function thankyou(Request $request)
    {
        $orderId = $request->query('orderId'); // /thankyou?orderId=123
        $order = Order::findOrFail($orderId);

        return view('checkout.thankyou', compact('orderId', 'order'));
    }
}
