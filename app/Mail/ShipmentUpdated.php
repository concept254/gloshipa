<?php

namespace App\Mail;

use App\Models\Shipment;
use App\Models\ShipmentEvent;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the client, when the admin logs a status or location change. */
class ShipmentUpdated extends Mailable
{
    public function __construct(
        public Shipment $shipment,
        public ShipmentEvent $event,
        public ?string $previousLocation = null,
        public ?string $previousStatus = null,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->shipment->status === 'delivered'
            ? "Delivered: {$this->shipment->tracking_id}"
            : "Update on {$this->shipment->tracking_id}: " . $this->shipment->statusLabel();

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.shipment-updated');
    }
}
