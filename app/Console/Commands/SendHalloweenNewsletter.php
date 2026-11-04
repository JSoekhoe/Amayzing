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
                            {--test= : Verstuur een testmail}
                            {--send : Verstuur de nieuwsbrief}';

    protected $description = 'Verstuur de Halloween nieuwsbrief';

    public function handle(): int
    {
        $campaign = 'halloween-box-2026';
        $dailyLimit = 400;

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

        $sentToday = DB::table('newsletter_campaign_sends')
            ->whereDate('sent_at', today())
            ->count();

        $remaining = max(0, $dailyLimit - $sentToday);

        if ($remaining === 0) {
            $this->warn('De dagelijkse limiet van 400 mails is bereikt.');

            return self::SUCCESS;
        }

        $subscribers = NewsletterSubscriber::query()
            ->where('is_subscribed', true)
            ->whereNotNull('consented_at')
            ->whereNotExists(function ($query) use ($campaign) {
                $query->selectRaw('1')
                    ->from('newsletter_campaign_sends')
                    ->whereColumn(
                        'newsletter_campaign_sends.newsletter_subscriber_id',
                        'newsletter_subscribers.id'
                    )
                    ->where('campaign', $campaign);
            })
            ->orderBy('id')
            ->limit($remaining)
            ->get();

        if ($subscribers->isEmpty()) {
            $this->warn('Er zijn geen nieuwe abonnees om te mailen.');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
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
        $this->info("Mislukt: {$failed}");
        $this->info("Dagelijkse limiet: {$dailyLimit}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
