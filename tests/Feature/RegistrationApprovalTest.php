<?php

namespace Tests\Feature;

use App\Mail\AccountApproved;
use App\Mail\NewRegistrationAlert;
use App\Mail\RegistrationReceived;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Thandi Mokoena',
            'username' => 'thandi',
            'email' => 'thandi@example.test',
            'phone' => '0821234567',
            'company' => 'Mokoena Imports',
            'password' => 'secret-pass-1',
            'password_confirmation' => 'secret-pass-1',
        ], $overrides);
    }

    public function test_registration_creates_a_pending_user_and_sends_emails(): void
    {
        Mail::fake();

        $this->post('/register', $this->registrationData())->assertRedirect('/login');

        $this->assertGuest();
        $this->assertDatabaseHas('users', ['username' => 'thandi', 'status' => 'pending', 'is_admin' => false]);

        Mail::assertSent(RegistrationReceived::class, fn ($m) => $m->hasTo('thandi@example.test'));
        Mail::assertSent(NewRegistrationAlert::class, fn ($m) => $m->hasTo(config('gloshipa.admin_email')));
    }

    public function test_registration_cannot_grant_admin_rights_or_skip_approval(): void
    {
        Mail::fake();

        $this->post('/register', $this->registrationData(['is_admin' => 1, 'status' => 'active']));

        $this->assertDatabaseHas('users', ['username' => 'thandi', 'status' => 'pending', 'is_admin' => false]);
    }

    public function test_pending_user_cannot_sign_in(): void
    {
        $user = User::factory()->pending()->create(['username' => 'waiting']);

        $this->post('/login', ['login' => 'waiting', 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_suspended_user_cannot_sign_in(): void
    {
        User::factory()->suspended()->create(['username' => 'blocked']);

        $this->post('/login', ['login' => 'blocked', 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_active_user_can_sign_in_with_username_or_email(): void
    {
        $user = User::factory()->create(['username' => 'active_one', 'email' => 'active@example.test']);

        $this->post('/login', ['login' => 'active_one', 'password' => 'password'])->assertRedirect('/dashboard');
        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', ['login' => 'active@example.test', 'password' => 'password'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['username' => 'active_one']);

        $this->post('/login', ['login' => 'active_one', 'password' => 'nope'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_is_sent_to_the_admin_area(): void
    {
        User::factory()->admin()->create(['username' => 'boss']);

        $this->post('/login', ['login' => 'boss', 'password' => 'password'])->assertRedirect('/admin');
    }

    public function test_a_user_suspended_mid_session_is_signed_out(): void
    {
        $user = User::factory()->suspended()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_admin_can_approve_a_pending_user_and_the_user_is_emailed(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$pending->id}/status", ['status' => 'active'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $pending->id, 'status' => 'active']);
        $this->assertNotNull($pending->fresh()->approved_at);
        Mail::assertSent(AccountApproved::class, fn ($m) => $m->hasTo($pending->email));
    }

    public function test_admin_can_switch_a_client_off_without_an_email(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create();

        $this->actingAs($admin)->patch("/admin/users/{$client->id}/status", ['status' => 'suspended']);

        $this->assertDatabaseHas('users', ['id' => $client->id, 'status' => 'suspended']);
        Mail::assertNothingSent();
    }

    public function test_a_client_cannot_approve_users(): void
    {
        $client = User::factory()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($client)->patch("/admin/users/{$pending->id}/status", ['status' => 'active'])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $pending->id, 'status' => 'pending']);
    }

    public function test_admin_accounts_cannot_be_changed_from_the_clients_screen(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$other->id}/status", ['status' => 'suspended'])->assertForbidden();
    }
}
