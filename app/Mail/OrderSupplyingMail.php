<?php

namespace App\Mail;

use App\Models\Order;
use App\Utils\PDFDocuments;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderSupplyingMail extends Mailable
{
    use Queueable, SerializesModels;

    private $order;
    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişiniz Tedarik Ediliyor',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'fullname' => $this->order->invoiceUser->full_name,
            'orderNo' => $this->order->order_no,
            'deliveryDate' => $this->order->getDeliveryDate()
        ];

        return new Content(
            view: 'templates.mail.orders.order-supplying',
            with: $data
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
