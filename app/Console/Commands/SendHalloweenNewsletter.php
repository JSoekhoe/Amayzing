<?php

namespace App\Console\Commands;

use App\Mail\HalloweenNewsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class SendHalloweenNewsletter extends Command
{
    protected $signature = 'newsletter:send-halloween
                            {--test= : Verstuur een testmail naar dit e-mailadres}
                            {--send : Verstuur de nieuwsbrief naar alle geldige abonnees}';

    protected $description = 'Verstuur de Halloween nieuwsbrief';

    public function handle(): int
    {
        $campaign = 'halloween-box-2026';

        // Testmail
        if ($email = $this->option('test')) {
            try {
                $unsubscribeUrl = URL::route('newsletter.unsubscribe', [
                    'token' => 'TEST',
                ]);

                Mail::to($email)->send(
                    new HalloweenNewsletter($unsubscribeUrl)
                );

                $this->info("Testmail verzonden naar {$email}.");

                return self::SUCCESS;
            } catch (Throwable $e) {
                $this->error($e->getMessage());

                return self::FAILURE;
            }
        }

        if (!$this->option('send')) {
            $this->error('Gebruik --test=emailadres of --send.');

            return self::FAILURE;
        }

        $subscribers = NewsletterSubscriber::query()
            ->where('is_subscribed', true)
            ->whereNotNull('consented_at')
            ->get();

        if ($subscribers->isEmpty()) {
            $this->warn('Er zijn geen abonnees met geregistreerde toestemming.');

            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            $alreadySent = DB::table('newsletter_campaign_sends')
                ->where('newsletter_subscriber_id', $subscriber->id)
                ->where('campaign', $campaign)
                ->exists();

            if ($alreadySent) {
                $skipped++;
                continue;
            }

            try {
                $unsubscribeUrl = URL::route('newsletter.unsubscribe', [
                    'token' => $subscriber->unsubscribe_token,
                ]);

                Mail::to($subscriber->email)->send(
                    new HalloweenNewsletter($unsubscribeUrl)
                );

                DB::table('newsletter_campaign_sends')->insert([
                    'newsletter_subscriber_id' => $subscriber->id,
                    'campaign' => $campaign,
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sent++;

                $this->info("Verzonden: {$subscriber->email}");
            } catch (Throwable $e) {
                $failed++;

                $this->error(
                    "Mislukt voor {$subscriber->email}: {$e->getMessage()}"
                );
            }
        }

        $this->newLine();
        $this->info("Verzonden: {$sent}");
        $this->info("Overgeslagen: {$skipped}");
        $this->info("Mislukt: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
