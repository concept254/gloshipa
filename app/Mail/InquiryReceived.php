<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the Global Shipping Alliance team when someone uses the contact or quote form. */
class InquiryReceived extends Mailable
{
    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->inquiry->type === 'quote' ? 'New quote request: ' : 'New website message: ') . $this->inquiry->name,
            replyTo: [$this->inquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inquiry-received');
    }
}
