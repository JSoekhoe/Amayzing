<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HalloweenNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $unsubscribeUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laatste kans: de Halloween Box van Jamay Tuller!'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter.halloween'
        );
    }
}
