<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdmin();
        $this->call(ContentSeeder::class);

        // Demo data is only created on a local machine (Herd), never on the live server.
        if (app()->environment('local')) {
            $this->createDemoData();
        }
    }

    private function createAdmin(): void
    {
        $admin = User::firstOrNew(['username' => 'admin']);

        // is_admin / status are not mass assignable, so they are set explicitly here.
        $admin->forceFill([
            'name' => 'Global Shipping Alliance Admin',
            'email' => config('gloshipa.admin_email'),
            'password' => config('gloshipa.seed_admin_password'),
            'is_admin' => true,
            'status' => User::STATUS_ACTIVE,
            'approved_at' => $admin->approved_at ?? now(),
        ])->save();
    }

    private function createDemoData(): void
    {
        $client = User::firstOrNew(['username' => 'demo']);
        $client->forceFill([
            'name' => 'Demo Client',
            'email' => 'demo@example.test',
            'password' => 'password',
            'company' => 'Demo Imports (Pty) Ltd',
            'phone' => '+27 82 000 0000',
            'status' => User::STATUS_ACTIVE,
            'approved_at' => now(),
        ])->save();

        $waiting = User::firstOrNew(['username' => 'newclient']);
        $waiting->forceFill([
            'name' => 'Waiting For Approval',
            'email' => 'newclient@example.test',
            'password' => 'password',
            'company' => 'Fresh Start Trading',
            'status' => User::STATUS_PENDING,
        ])->save();

        if ($client->shipments()->exists()) {
            return;
        }

        $demo = [
            ['status' => 'in_transit', 'origin' => 'Shanghai, CN', 'destination' => 'Durban, ZA', 'loc' => 'Indian Ocean', 'cargo' => 'Electronics', 'ref' => 'GSDEMO0001', 'box' => 'MSKU1234567'],
            ['status' => 'customs', 'origin' => 'Rotterdam, NL', 'destination' => 'Cape Town, ZA', 'loc' => 'Cape Town port', 'cargo' => 'Auto parts', 'ref' => 'GSDEMO0002', 'box' => 'TCLU7654321'],
            ['status' => 'delivered', 'origin' => 'Dubai, AE', 'destination' => 'Johannesburg, ZA', 'loc' => 'Johannesburg', 'cargo' => 'Textiles', 'ref' => 'GSDEMO0003', 'box' => 'HLCU1122334'],
        ];

        foreach ($demo as $i => $row) {
            $shipment = Shipment::create([
                'tracking_id' => $row['ref'],
                'container_number' => $row['box'],
                'user_id' => $client->id,
                'origin' => $row['origin'],
                'destination' => $row['destination'],
                'current_location' => $row['loc'],
                'cargo_description' => $row['cargo'],
                'weight_kg' => 12500 + $i * 1800,
                'status' => $row['status'],
                'eta' => now()->addDays(12 - $i * 6)->toDateString(),
                'delivered_at' => $row['status'] === 'delivered' ? now()->subDays(2) : null,
            ]);

            $shipment->events()->createMany([
                ['status' => 'pending', 'location' => $row['origin'], 'note' => 'Shipment record created.', 'occurred_at' => now()->subDays(20 - $i)],
                ['status' => 'in_transit', 'location' => 'At sea', 'note' => 'Departed port of origin.', 'occurred_at' => now()->subDays(14 - $i)],
                ['status' => $row['status'], 'location' => $row['loc'], 'note' => null, 'occurred_at' => now()->subDays(3)],
            ]);
        }
    }
}
