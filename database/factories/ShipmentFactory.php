<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        $ports = ['Durban, ZA', 'Cape Town, ZA', 'Mombasa, KE', 'Dar es Salaam, TZ', 'Shanghai, CN', 'Rotterdam, NL', 'Dubai, AE', 'Singapore, SG'];
        $origin = fake()->randomElement($ports);

        return [
            'tracking_id' => Shipment::newTrackingId(),
            'container_number' => 'MSKU' . fake()->unique()->numerify('#######'),
            'user_id' => User::factory(),
            'origin' => $origin,
            'destination' => fake()->randomElement(array_diff($ports, [$origin])),
            'current_location' => $origin,
            'cargo_description' => fake()->randomElement(['Auto parts', 'Textiles', 'Electronics', 'Machinery', 'Furniture', 'Foodstuffs']),
            'weight_kg' => fake()->numberBetween(800, 24000),
            'status' => 'pending',
            'eta' => now()->addDays(fake()->numberBetween(5, 40))->toDateString(),
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'delivered_at' => $status === 'delivered' ? now() : null,
        ]);
    }
}
