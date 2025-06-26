<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Stripe\Stripe;
    use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function checkout()
    {
        return view('payment.checkout');
    }

    public function process(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amount = $request->input('amount'); // in euro’s, moet * 100 (cents)

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Stripe verwacht centen
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'description' => 'Betaling bestelling Amayzing',
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
