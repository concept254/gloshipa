<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the admin, when someone registers and needs approving. */
class NewRegistrationAlert extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Global Shipping Alliance registration awaiting approval: ' . $this->user->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-registration-alert');
    }
}
