<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // letters, digits and underscores only, so it always passes the app's alpha_dash rule
            'username' => fake()->unique()->bothify('user_????###'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            // Plain 'password', hashed once and reused so tests stay fast.
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'status' => User::STATUS_ACTIVE,
            'is_admin' => false,
            'approved_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['is_admin' => true]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_PENDING, 'approved_at' => null]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => User::STATUS_SUSPENDED]);
    }
}
