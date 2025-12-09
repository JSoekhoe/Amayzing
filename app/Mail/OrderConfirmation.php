<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.order_confirmation_pdf', [
            'order' => $this->order,
        ])->setPaper('a4');

        return $this->subject('Bevestiging van je bestelling bij aMayzing')
            ->view('emails.order_confirmation') // optionele HTML mail
            ->attachData($pdf->output(), "factuur_order_{$this->order->id}.pdf");
    }
}
