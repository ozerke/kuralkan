<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisteredByShopMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $password;
    private $salesPoint;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password, User $salesPoint)
    {
        $this->user = $user;
        $this->password = $password;
        $this->salesPoint = $salesPoint;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ekuralkan\'a bayimiz aracılığı ile kayıt oldunuz',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = [
            'salesPoint' => $this->salesPoint->full_name,
            'email' => $this->user->email,
            'randomPassword' => $this->password,
            'fullname' => $this->user->full_name
        ];

        return new Content(
            view: 'templates.mail.users.sales-point-user-registration',
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
