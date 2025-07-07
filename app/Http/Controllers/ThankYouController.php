<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;

class ThankYouController extends Controller
{
    public function index(Request $request)
    {
        $orderId = $request->query('orderId');
        $order = Order::find($orderId);

        if (!$order) {
            return view('thankyou', [
                'status' => 'unknown',
                'message' => 'Bestelling niet gevonden.',
            ]);
        }

        $paymentId = $order->payment_id;

        if (!$paymentId) {
            return view('thankyou', [
                'status' => 'unknown',
                'message' => 'Betaling is niet gevonden voor deze bestelling.',
            ]);
        }

        $mollie = new MollieApiClient();
        $mollie->setApiKey(env('MOLLIE_KEY'));

        try {
            $payment = $mollie->payments->get($paymentId);

            if ($payment->isPaid()) {
                $order->update(['status' => 'paid']);
                $status = 'paid';
            } elseif ($payment->isOpen()) {
                $order->update(['status' => 'pending']);
                $status = 'pending';
            } elseif ($payment->isCanceled()) {
                $order->update(['status' => 'cancelled']);
                $status = 'cancelled';
            } elseif ($payment->isExpired()) {
                $order->update(['status' => 'expired']);
                $status = 'expired';
            } else {
                $status = 'unknown';
            }

            $messages = [
                'paid' => 'Je betaling is succesvol afgerond. Bedankt voor je bestelling!',
                'failed' => 'De betaling is mislukt. Probeer het opnieuw of neem contact op.',
                'cancelled' => 'Je betaling is geannuleerd.',
                'expired' => 'De betaling is verlopen. Plaats eventueel een nieuwe bestelling.',
                'pending' => 'Je betaling is in behandeling.',
                'unknown' => 'Status van de betaling is onbekend.',
            ];

            $message = $messages[$status] ?? $messages['unknown'];

            return view('thankyou', compact('status', 'message'));

        } catch (\Exception $e) {
            return view('thankyou', [
                'status' => 'unknown',
                'message' => 'Er is een fout opgetreden bij het ophalen van de betaling: ' . $e->getMessage(),
            ]);
        }
    }
}
