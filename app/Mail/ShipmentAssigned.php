<?php

namespace App\Mail;

use App\Models\Shipment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the client, when a shipment is assigned (or reassigned) to them. */
class ShipmentAssigned extends Mailable
{
    public function __construct(public Shipment $shipment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'A shipment has been assigned to you: ' . $this->shipment->tracking_id);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.shipment-assigned');
    }
}
