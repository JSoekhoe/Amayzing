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
            'amount' => 'required|numeric|min:0.5',
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $amount = number_format($request->input('amount'), 2, '.', '');

        $mollie = new \Mollie\Api\MollieApiClient();
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
                'webhookUrl' => 'https://jamaytuller.com/webhook/mollie',
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
        $mollie->setApiKey(env('MOLLIE_KEY'));

        $paymentId = $request->input('id');

        try {
            $payment = $mollie->payments->get($paymentId);
            $orderId = $payment->metadata->order_id ?? null;

            \Log::info("Webhook ontvangen voor payment ID: {$paymentId} met status: {$payment->status}");

            if (!$orderId) {
                \Log::warning("Webhook: Geen order_id metadata gevonden voor betaling ID: {$paymentId}");
                return response('Metadata order_id ontbreekt', 400);
            }

            $order = Order::with('items.product')->find($orderId);

            if (!$order) {
                \Log::warning("Webhook: Geen order gevonden voor betaling ID: {$paymentId}");
                return response('Order not found', 404);
            }

            if ($payment->status === 'paid' && !$order->paid_at) {
                $updated = $order->update([
                    'paid_at' => now(),
                    'status' => 'paid',
                ]);

                if ($updated) {
                    \Log::info("Order #{$order->id} status bijgewerkt naar 'paid' en betaaldatum ingesteld.");

                    // Send confirmation mails
                    Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
                    Mail::to('amayzingpastry@gmail.com')->send(new \App\Mail\OrderConfirmation($order));

                    \Log::info("Order confirmation mails verzonden voor order #{$order->id}");
                } else {
                    // Update status ongeacht wat het is (mislukt, geannuleerd, verlopen etc.)
                    $order->update(['status' => $payment->status]);
                    \Log::info("Order status geüpdatet naar {$payment->status}.");
                }
            } else {
                \Log::info("Order #{$order->id} status niet bijgewerkt. Payment status: {$payment->status}, paid_at: {$order->paid_at}");
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
