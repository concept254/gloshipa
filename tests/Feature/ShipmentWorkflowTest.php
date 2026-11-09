<?php

namespace Tests\Feature;

use App\Mail\ShipmentAssigned;
use App\Mail\ShipmentUpdated;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ShipmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function payload(User $client, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $client->id,
            'container_number' => 'msku 123-4567',
            'origin' => 'Shanghai, CN',
            'destination' => 'Durban, ZA',
            'cargo_description' => 'Electronics',
            'weight_kg' => 12000,
            'eta' => now()->addDays(20)->toDateString(),
            'status' => 'pending',
        ], $overrides);
    }

    public function test_admin_creates_a_shipment_assigned_to_a_client_and_the_client_is_emailed(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/shipments', $this->payload($client));

        $shipment = Shipment::firstOrFail();
        $response->assertRedirect("/admin/shipments/{$shipment->id}");

        $this->assertSame($client->id, $shipment->user_id);
        $this->assertMatchesRegularExpression('/^GS[A-Z0-9]{10}$/', $shipment->tracking_id);
        $this->assertSame('MSKU1234567', $shipment->container_number); // normalised
        $this->assertSame('Shanghai, CN', $shipment->current_location);
        $this->assertCount(1, $shipment->events);

        Mail::assertSent(ShipmentAssigned::class, fn ($m) => $m->hasTo($client->email) && $m->shipment->is($shipment));
    }

    public function test_a_shipment_cannot_be_assigned_to_an_unapproved_client_or_an_admin(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)->post('/admin/shipments', $this->payload($pending))->assertSessionHasErrors('user_id');
        $this->actingAs($admin)->post('/admin/shipments', $this->payload($admin))->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('shipments', 0);
        Mail::assertNothingSent();
    }

    public function test_a_client_cannot_create_shipments(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->post('/admin/shipments', $this->payload($client))->assertForbidden();
        $this->assertDatabaseCount('shipments', 0);
    }

    public function test_moving_a_shipment_to_a_new_location_emails_the_client(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create();
        $shipment = Shipment::factory()->for($client)->create(['current_location' => 'Shanghai, CN']);

        $this->actingAs($admin)->post("/admin/shipments/{$shipment->id}/events", [
            'status' => 'at_port',
            'location' => 'Port of Durban',
            'note' => 'Berthed at Pier 2',
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'notify' => '1',
        ])->assertSessionHasNoErrors();

        $shipment->refresh();
        $this->assertSame('at_port', $shipment->status);
        $this->assertSame('Port of Durban', $shipment->current_location);
        $this->assertCount(1, $shipment->events);

        Mail::assertSent(ShipmentUpdated::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email)
                && $mail->previousLocation === 'Shanghai, CN'
                && $mail->event->location === 'Port of Durban';
        });
    }

    public function test_an_update_without_the_notify_box_sends_no_email(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create();

        $this->actingAs($admin)->post("/admin/shipments/{$shipment->id}/events", [
            'status' => 'in_transit',
            'location' => 'At sea',
            'occurred_at' => now()->format('Y-m-d\TH:i'),
        ]);

        $this->assertSame('in_transit', $shipment->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_marking_a_shipment_delivered_sets_the_delivery_date(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create();

        $this->actingAs($admin)->post("/admin/shipments/{$shipment->id}/events", [
            'status' => 'delivered',
            'location' => 'Client warehouse',
            'occurred_at' => now()->format('Y-m-d\TH:i'),
            'notify' => '1',
        ]);

        $this->assertNotNull($shipment->fresh()->delivered_at);
        Mail::assertSent(ShipmentUpdated::class);
    }

    public function test_reassigning_a_shipment_emails_the_new_owner_only(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $old = User::factory()->create();
        $new = User::factory()->create();
        $shipment = Shipment::factory()->for($old)->create();

        $this->actingAs($admin)->put("/admin/shipments/{$shipment->id}", [
            'user_id' => $new->id,
            'origin' => $shipment->origin,
            'destination' => $shipment->destination,
        ])->assertSessionHasNoErrors();

        $this->assertSame($new->id, $shipment->fresh()->user_id);
        Mail::assertSent(ShipmentAssigned::class, 1);
        Mail::assertSent(ShipmentAssigned::class, fn ($m) => $m->hasTo($new->email));
    }

    public function test_editing_details_without_changing_the_client_sends_no_email(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create();

        $this->actingAs($admin)->put("/admin/shipments/{$shipment->id}", [
            'user_id' => $shipment->user_id,
            'origin' => 'Cape Town, ZA',
            'destination' => $shipment->destination,
        ])->assertSessionHasNoErrors();

        $this->assertSame('Cape Town, ZA', $shipment->fresh()->origin);
        Mail::assertNothingSent();
    }

    public function test_removing_the_latest_timeline_entry_rolls_the_shipment_back(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = Shipment::factory()->create(['status' => 'at_port', 'current_location' => 'Port of Durban']);
        $shipment->events()->create(['status' => 'pending', 'location' => 'Shanghai, CN', 'occurred_at' => now()->subDays(3)]);
        $latest = $shipment->events()->create(['status' => 'at_port', 'location' => 'Port of Durban', 'occurred_at' => now()]);

        $this->actingAs($admin)->delete("/admin/shipments/{$shipment->id}/events/{$latest->id}");

        $shipment->refresh();
        $this->assertSame('pending', $shipment->status);
        $this->assertSame('Shanghai, CN', $shipment->current_location);
    }
}
