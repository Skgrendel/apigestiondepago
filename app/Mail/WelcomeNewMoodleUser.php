<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeNewMoodleUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $username,
        public string $password,
        public string $firstname,
        public string $lastname,
        public string $email,
        public string $membershipName = 'Membresía ACOFICUM',
        public string $campusUrl = 'https://campus.asociados.acoficum.org/'
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido al Campus ACOFICUM! 🎓',
            from: env('MAIL_FROM_ADDRESS', 'noreply@acoficum.org'),
            replyTo: env('MAIL_REPLY_TO', 'sig@acoficum.org'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-moodle-user',
            with: [
                'username' => $this->username,
                'password' => $this->password,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'email' => $this->email,
                'membershipName' => $this->membershipName,
                'campusUrl' => $this->campusUrl,
            ],
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
