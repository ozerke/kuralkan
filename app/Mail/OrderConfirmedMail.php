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

class OrderConfirmedMail extends Mailable
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
            subject: 'Sipariş Onaylandı',
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
            'remoteSalesAgreementLink' => route('remote-sales-pdf', ['orderNo' => $this->order->order_no]),
        ];

        return new Content(
            view: 'templates.mail.orders.order-confirmed',
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
        return [
            Attachment::fromPath(PDFDocuments::getPath($this->order))
        ];
    }
}
