<?php

namespace Tests\Feature;

use App\Mail\AccountApproved;
use App\Mail\NewRegistrationAlert;
use App\Mail\RegistrationReceived;
use App\Mail\ShipmentAssigned;
use App\Mail\ShipmentUpdated;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Notify::send() deliberately swallows mail errors so a bad SMTP password cannot break the admin's page.
 * The cost is that a broken email template would fail silently, so every template is rendered here.
 */
class MailRenderingTest extends TestCase
{
    use RefreshDatabase;

    private function shipment(array $overrides = []): Shipment
    {
        $client = User::factory()->create(['name' => 'Thandi Mokoena']);

        return Shipment::factory()->for($client)->create($overrides + [
            'tracking_id' => 'GSMAILTEST01',
            'container_number' => 'MSKU1234567',
            'origin' => 'Shanghai, CN',
            'destination' => 'Durban, ZA',
            'current_location' => 'Shanghai, CN',
        ])->load('user');
    }

    public function test_shipment_assigned_email_renders(): void
    {
        $html = (new ShipmentAssigned($this->shipment()))->render();

        $this->assertStringContainsString('A shipment has been assigned to you', $html);
        $this->assertStringContainsString('GSMAILTEST01', $html);
        $this->assertStringContainsString('MSKU1234567', $html);
        $this->assertStringContainsString('Thandi Mokoena', $html);
        $this->assertStringContainsString('/track?q=GSMAILTEST01', $html);
    }

    public function test_shipment_updated_email_shows_the_location_change(): void
    {
        $shipment = $this->shipment(['status' => 'at_port', 'current_location' => 'Port of Durban']);
        $event = $shipment->events()->create(['status' => 'at_port', 'location' => 'Port of Durban', 'note' => 'Berthed at Pier 2', 'occurred_at' => now()]);

        $html = (new ShipmentUpdated($shipment, $event, 'Shanghai, CN', 'in_transit'))->render();

        $this->assertStringContainsString('Your shipment has moved', $html);
        $this->assertStringContainsString('Shanghai, CN', $html);
        $this->assertStringContainsString('Port of Durban', $html);
        $this->assertStringContainsString('Berthed at Pier 2', $html);
        $this->assertStringContainsString('In transit', $html);
    }

    public function test_shipment_updated_email_without_a_move_or_note_renders(): void
    {
        $shipment = $this->shipment(['status' => 'customs']);
        $event = $shipment->events()->create(['status' => 'customs', 'location' => null, 'note' => null, 'occurred_at' => now()]);

        $html = (new ShipmentUpdated($shipment, $event))->render();

        $this->assertStringContainsString('There is an update on your shipment', $html);
        $this->assertStringContainsString('In customs', $html);
    }

    public function test_delivered_email_renders(): void
    {
        $shipment = $this->shipment(['status' => 'delivered', 'delivered_at' => now()]);
        $event = $shipment->events()->create(['status' => 'delivered', 'location' => 'Client warehouse', 'note' => null, 'occurred_at' => now()]);

        $mail = new ShipmentUpdated($shipment, $event, 'Port of Durban', 'out_for_delivery');
        $html = $mail->render();

        $this->assertStringContainsString('Your shipment has been delivered', $html);
        $this->assertStringContainsString('Delivered to', $html);
        $this->assertSame('Delivered: GSMAILTEST01', $mail->envelope()->subject);
    }

    public function test_account_emails_render(): void
    {
        $user = User::factory()->pending()->create(['name' => 'Sipho Dlamini', 'username' => 'sipho', 'company' => 'Dlamini Freight']);

        $approved = (new AccountApproved($user))->render();
        $this->assertStringContainsString('Your account is approved', $approved);
        $this->assertStringContainsString('sipho', $approved);

        $received = (new RegistrationReceived($user))->render();
        $this->assertStringContainsString('Sipho Dlamini', $received);

        $alert = (new NewRegistrationAlert($user))->render();
        $this->assertStringContainsString('Dlamini Freight', $alert);
        $this->assertStringContainsString('/admin/users?status=pending', $alert);
    }
}
