<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke tests: every screen renders without a Blade or query error,
 * for guests, clients and the admin, with empty and with filled data.
 */
class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        foreach (['/', '/login', '/register', '/forgot-password', '/reset-password/sometoken?email=a@example.test'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_client_screens_render_when_there_is_no_data(): void
    {
        $client = User::factory()->create();

        foreach (['/dashboard', '/shipments', '/shipments?filter=pending', '/shipments?filter=delivered', '/track', '/track?q=NOTHING', '/profile'] as $url) {
            $this->actingAs($client)->get($url)->assertOk();
        }
    }

    public function test_client_screens_render_a_shipment_in_every_status(): void
    {
        $client = User::factory()->create();

        foreach (array_keys(Shipment::STATUSES) as $status) {
            $shipment = Shipment::factory()->for($client)->status($status)->create();
            $shipment->events()->create(['status' => 'pending', 'location' => $shipment->origin, 'note' => 'Created', 'occurred_at' => now()->subDay()]);
            $shipment->events()->create(['status' => $status, 'location' => 'Somewhere', 'note' => null, 'occurred_at' => now()]);

            $this->actingAs($client)->get('/track?q=' . $shipment->tracking_id)
                ->assertOk()
                ->assertSee($shipment->tracking_id)
                ->assertSee('Somewhere');
        }

        foreach (['/dashboard', '/shipments', '/shipments?filter=pending', '/shipments?filter=delivered'] as $url) {
            $this->actingAs($client)->get($url)->assertOk();
        }
    }

    public function test_admin_screens_render(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->pending()->create(['name' => 'Waiting Person']);
        User::factory()->suspended()->create();
        $client = User::factory()->create();

        $shipment = Shipment::factory()->for($client)->create(['status' => 'at_port']);
        $shipment->events()->create(['status' => 'at_port', 'location' => 'Port of Durban', 'note' => 'Berthed', 'occurred_at' => now()]);

        $urls = [
            '/admin',
            '/admin/users',
            '/admin/users?status=active',
            '/admin/users?status=suspended',
            '/admin/users?q=Waiting',
            '/admin/shipments',
            '/admin/shipments?q=GS&status=at_port',
            '/admin/shipments/create',
            "/admin/shipments/{$shipment->id}",
            "/admin/shipments/{$shipment->id}/edit",
            '/track?q=' . $shipment->tracking_id,
            '/profile',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }

        $this->actingAs($admin)->get('/admin')->assertSee('Waiting Person');
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
    }

    public function test_a_failed_email_shows_a_warning_instead_of_claiming_success(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $admin = User::factory()->admin()->create();
        $client = User::factory()->create();

        $this->actingAs($admin)->post('/admin/shipments', [
            'user_id' => $client->id,
            'origin' => 'Shanghai, CN',
            'destination' => 'Durban, ZA',
            'status' => 'pending',
        ])
            ->assertSessionHas('warning')
            ->assertSessionHas('status', fn ($status) => ! str_contains($status, 'has been emailed'));

        $this->assertDatabaseCount('shipments', 1);
    }
}
