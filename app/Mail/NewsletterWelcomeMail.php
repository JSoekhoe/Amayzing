<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscriber $subscriber
    ) {}

    public function build()
    {
        return $this
            ->subject('Welkom bij Jamay Tuller Patisserie!')
            ->view('emails.newsletter.welcome');
    }
}
