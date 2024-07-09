<?php

namespace App\Mail;

use App\Models\Ebond;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EbondsCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $ebond;

    /**
     * Create a new message instance.
     */
    public function __construct(Ebond $ebond)
    {
        $this->ebond = $ebond;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-senetleriniz Oluşturuldu',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'fullname' => $this->ebond->user->full_name,
        ];

        return new Content(
            view: 'templates.mail.ebonds.created',
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
