<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TimeslotNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $timeslot;

    public function __construct($order, $timeslot)
    {
        $this->order = $order;
        $this->timeslot = $timeslot;
    }

    public function build()
    {
        return $this->subject('Bezorgtijd aMayzing')
            ->view('emails.timeslot');
    }
}
