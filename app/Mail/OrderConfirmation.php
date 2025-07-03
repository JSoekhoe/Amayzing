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
        $pdf = Pdf::loadView('emails.order_confirmation_pdf', ['order' => $this->order]);

        return $this->subject('Bevestiging van je bestelling bij Amayzing')
            ->view('emails.order_confirmation')
            ->attachData($pdf->output(), "factuur_order_{$this->order->id}.pdf");
    }

}
