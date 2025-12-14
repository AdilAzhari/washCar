<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed branches first (needed for foreign keys)
        $this->call([
            BranchSeeder::class,
        ]);

        // Create test user with admin role
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
            'branch_id' => 1, // Assign to first branch
        ]);

        // Create additional staff members
        User::factory()->create([
            'name' => 'John Manager',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'branch_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Jane Staff',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'branch_id' => 2,
        ]);

        // Seed remaining tables
        $this->call([
            PackageSeeder::class,
            BaySeeder::class,
            CustomerSeeder::class,
            WashSeeder::class,
            QueueEntrySeeder::class,
            InventoryItemSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
