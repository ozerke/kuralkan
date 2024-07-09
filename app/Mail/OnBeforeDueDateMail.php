<?php

namespace App\Mail;

use App\Models\Ebond;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnBeforeDueDateMail extends Mailable
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
            subject: 'E-senetinizin Ödemesi Yarın',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'fullname' => $this->ebond->user->full_name,
            'eBondNo' => $this->ebond->e_bond_no,
            'eBondDueDate' => $this->ebond->due_date->format('d-m-Y'),
            'eBondAmount' => $this->ebond->bond_amount,
        ];

        return new Content(
            view: 'templates.mail.ebonds.beforeDueDate',
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
