<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportOrderEmails extends Command
{
    protected $signature = 'newsletter:import-orders';

    protected $description = 'Importeer unieke e-mailadressen uit bestellingen';

    public function handle(): int
    {
        $added = 0;

        Order::whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'email')
            ->chunkById(500, function ($orders) use (&$added) {
                foreach ($orders as $order) {
                    $email = strtolower(trim($order->email));

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }

                    $subscriber = NewsletterSubscriber::firstOrCreate(
                        ['email' => $email],
                        [
                            'is_subscribed' => false,
                            'unsubscribe_token' => Str::random(64),
                            'consented_at' => null,
                        ]
                    );

                    if ($subscriber->wasRecentlyCreated) {
                        $added++;
                    }
                }
            });

        $this->info("Nieuwe e-mailadressen toegevoegd: {$added}");

        $this->info(
            'Totaal adressen: ' . NewsletterSubscriber::count()
        );

        return self::SUCCESS;
    }
}
