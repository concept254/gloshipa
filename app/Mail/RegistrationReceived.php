<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the client, right after they register. */
class RegistrationReceived extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We received your Glo Shipa registration');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.registration-received');
    }
}
