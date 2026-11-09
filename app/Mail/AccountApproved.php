<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the client, when the admin approves their account. */
class AccountApproved extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Glo Shipa account is approved');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.account-approved');
    }
}
