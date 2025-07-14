<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\DeliveryCheckerService;


class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $deliveryInfo;
    public function __construct(Order $order)
    {
        $this->order = $order->load('items.product');
        if ($order->type === 'bezorgen') {
            $deliveryChecker = app(DeliveryCheckerService::class);
            $this->deliveryInfo = $deliveryChecker->check(
                $order->postcode,
                $order->housenumber,
                $order->addition,
                $order->type
            );
        } else {
            $this->deliveryInfo = null;
        }
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.order_confirmation_pdf', [
            'order' => $this->order,
            'deliveryInfo' => $this->deliveryInfo,
        ]);

        return $this->subject('Bevestiging van je bestelling bij Amayzing')
            ->view('emails.order_confirmation')
            ->attachData($pdf->output(), "factuur_order_{$this->order->id}.pdf");
    }

}
