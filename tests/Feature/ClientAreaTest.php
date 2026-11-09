<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAreaTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_the_hero_with_a_tracking_form(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Know exactly where')
            ->assertSee('name="q"', false)
            ->assertSee('images/hero-ship.svg', false);
    }

    public function test_guests_are_sent_to_sign_in_when_they_try_to_track(): void
    {
        $this->get('/track?q=GSABC1234567')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_client_can_track_by_tracking_id_or_container_number(): void
    {
        $client = User::factory()->create();
        $shipment = Shipment::factory()->for($client)->create([
            'tracking_id' => 'GSTEST000001',
            'container_number' => 'MSKU7654321',
            'status' => 'in_transit',
        ]);
        $shipment->events()->create(['status' => 'in_transit', 'location' => 'Mid Atlantic', 'occurred_at' => now()]);

        $this->actingAs($client)->get('/track?q=gstest000001')->assertOk()->assertSee('GSTEST000001')->assertSee('Mid Atlantic');
        $this->actingAs($client)->get('/track?q=msku 765-4321')->assertOk()->assertSee('GSTEST000001');
    }

    public function test_client_cannot_track_another_clients_shipment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Shipment::factory()->for($owner)->create(['tracking_id' => 'GSPRIVATE001', 'container_number' => 'MSKU1111111']);

        $this->actingAs($other)->get('/track?q=GSPRIVATE001')->assertOk()->assertSee('No shipment found')->assertDontSee('Mid Atlantic');
        $this->actingAs($other)->get('/track?q=MSKU1111111')->assertOk()->assertSee('No shipment found');
    }

    public function test_admin_can_track_any_shipment(): void
    {
        $admin = User::factory()->admin()->create();
        Shipment::factory()->create(['tracking_id' => 'GSANY0000001']);

        $this->actingAs($admin)->get('/track?q=GSANY0000001')->assertOk()->assertSee('GSANY0000001');
    }

    public function test_dashboard_lists_only_my_pending_shipments(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        Shipment::factory()->for($me)->create(['tracking_id' => 'GSMINEPEND01', 'status' => 'in_transit']);
        Shipment::factory()->for($me)->create(['tracking_id' => 'GSMINEDONE01', 'status' => 'delivered', 'delivered_at' => now()]);
        Shipment::factory()->for($other)->create(['tracking_id' => 'GSOTHERS0001', 'status' => 'in_transit']);

        $this->actingAs($me)->get('/dashboard')
            ->assertOk()
            ->assertSee('GSMINEPEND01')
            ->assertDontSee('GSOTHERS0001');

        $this->actingAs($me)->get('/shipments?filter=pending')->assertSee('GSMINEPEND01')->assertDontSee('GSMINEDONE01')->assertDontSee('GSOTHERS0001');
        $this->actingAs($me)->get('/shipments?filter=delivered')->assertSee('GSMINEDONE01')->assertDontSee('GSMINEPEND01');
    }

    public function test_client_can_update_their_profile(): void
    {
        $client = User::factory()->create(['username' => 'oldname']);

        $this->actingAs($client)->patch('/profile', [
            'name' => 'New Name',
            'username' => 'newname',
            'email' => $client->email,
            'phone' => '0829999999',
            'company' => 'New Co',
            'address' => '1 Harbour Road, Durban',
        ])->assertRedirect('/profile');

        $this->assertDatabaseHas('users', ['id' => $client->id, 'username' => 'newname', 'company' => 'New Co']);
    }

    public function test_profile_update_cannot_change_admin_flag_or_status(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->patch('/profile', [
            'name' => $client->name,
            'username' => $client->username,
            'email' => $client->email,
            'is_admin' => 1,
            'status' => 'pending',
        ]);

        $client->refresh();
        $this->assertFalse($client->is_admin);
        $this->assertSame('active', $client->status);
    }

    public function test_username_must_stay_unique(): void
    {
        User::factory()->create(['username' => 'taken']);
        $client = User::factory()->create(['username' => 'mine']);

        $this->actingAs($client)->patch('/profile', [
            'name' => $client->name, 'username' => 'taken', 'email' => $client->email,
        ])->assertSessionHasErrors('username');
    }

    public function test_client_can_change_password_with_the_current_one(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'brand-new-pass-1',
            'password_confirmation' => 'brand-new-pass-1',
        ])->assertSessionHasNoErrors();

        $this->post('/logout');
        $this->post('/login', ['login' => $client->username, 'password' => 'brand-new-pass-1'])->assertRedirect('/dashboard');
    }

    public function test_clients_cannot_open_the_admin_area(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)->get('/admin')->assertForbidden();
        $this->actingAs($client)->get('/admin/shipments')->assertForbidden();
        $this->actingAs($client)->get('/admin/users')->assertForbidden();
    }
}
