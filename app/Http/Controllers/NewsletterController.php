<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * Toon de nieuwsbrief inschrijfpagina.
     */
    public function index()
    {
        return view('newsletter.subscribe');
    }

    /**
     * Verwerk een nieuwe nieuwsbriefinschrijving.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'consent' => 'accepted',
        ]);

        $email = strtolower(trim($validated['email']));

        $subscriber = NewsletterSubscriber::firstOrNew([
            'email' => $email,
        ]);

        // Controleer of de abonnee al actief was.
        $alreadySubscribed = $subscriber->exists && $subscriber->is_subscribed;

        if (!$subscriber->unsubscribe_token) {
            $subscriber->unsubscribe_token = Str::random(64);
        }

        $subscriber->name = $validated['name'];
        $subscriber->is_subscribed = true;
        $subscriber->consented_at = now();
        $subscriber->unsubscribed_at = null;

        $subscriber->save();

        // Verstuur alleen bij een nieuwe of hernieuwde inschrijving.
        if (!$alreadySubscribed) {
            try {
                Mail::to($subscriber->email)
                    ->send(new NewsletterWelcomeMail($subscriber));

            } catch (\Throwable $e) {
                Log::error('Welkomstmail nieuwsbrief mislukt', [
                    'email' => $subscriber->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('newsletter.subscribe')
            ->with('success', 'Bedankt! Je bent aangemeld voor onze nieuwsbrief.');
    }

    /**
     * Verwerk een uitschrijving.
     */
    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where(
            'unsubscribe_token',
            $token
        )->firstOrFail();

        if ($subscriber->is_subscribed) {
            $subscriber->update([
                'is_subscribed' => false,
                'unsubscribed_at' => now(),
            ]);
        }

        return view('newsletter.unsubscribed');
    }
}
