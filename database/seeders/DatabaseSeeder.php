<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
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

        // Get branch IDs for assignment
        $branches = \App\Models\Branch::all();

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@washywashy.com',
            'role' => 'admin',
            'branch_id' => $branches[0]->id, // Downtown
        ]);

        // Create managers for each active branch
        User::factory()->create([
            'name' => 'Robert Martinez',
            'email' => 'robert.martinez@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[0]->id, // Downtown
        ]);

        User::factory()->create([
            'name' => 'Sarah Thompson',
            'email' => 'sarah.thompson@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[1]->id, // Northside
        ]);

        User::factory()->create([
            'name' => 'Michael Chen',
            'email' => 'michael.chen@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[2]->id, // Eastside
        ]);

        User::factory()->create([
            'name' => 'Emily Rodriguez',
            'email' => 'emily.rodriguez@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[3]->id, // Westside
        ]);

        User::factory()->create([
            'name' => 'James Anderson',
            'email' => 'james.anderson@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[4]->id, // Beverly Hills
        ]);

        User::factory()->create([
            'name' => 'Jennifer Williams',
            'email' => 'jennifer.williams@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[5]->id, // Santa Monica
        ]);

        User::factory()->create([
            'name' => 'David Kim',
            'email' => 'david.kim@washywashy.com',
            'role' => 'manager',
            'branch_id' => $branches[6]->id, // Pasadena
        ]);

        // Create staff members for each branch (2-3 per branch)
        $staffMembers = [
            // Downtown
            ['name' => 'Carlos Mendez', 'email' => 'carlos.mendez@washywashy.com', 'branch_id' => $branches[0]->id],
            ['name' => 'Lisa Chang', 'email' => 'lisa.chang@washywashy.com', 'branch_id' => $branches[0]->id],
            ['name' => 'Marcus Johnson', 'email' => 'marcus.johnson@washywashy.com', 'branch_id' => $branches[0]->id],

            // Northside
            ['name' => 'Sophia Rivera', 'email' => 'sophia.rivera@washywashy.com', 'branch_id' => $branches[1]->id],
            ['name' => 'Tyler Brown', 'email' => 'tyler.brown@washywashy.com', 'branch_id' => $branches[1]->id],
            ['name' => 'Amanda Lee', 'email' => 'amanda.lee@washywashy.com', 'branch_id' => $branches[1]->id],

            // Eastside
            ['name' => 'Daniel Reyes', 'email' => 'daniel.reyes@washywashy.com', 'branch_id' => $branches[2]->id],
            ['name' => 'Jessica Wu', 'email' => 'jessica.wu@washywashy.com', 'branch_id' => $branches[2]->id],

            // Westside
            ['name' => 'Kevin Park', 'email' => 'kevin.park@washywashy.com', 'branch_id' => $branches[3]->id],
            ['name' => 'Michelle Santos', 'email' => 'michelle.santos@washywashy.com', 'branch_id' => $branches[3]->id],
            ['name' => 'Ryan Carter', 'email' => 'ryan.carter@washywashy.com', 'branch_id' => $branches[3]->id],

            // Beverly Hills
            ['name' => 'Victoria Stone', 'email' => 'victoria.stone@washywashy.com', 'branch_id' => $branches[4]->id],
            ['name' => 'Brandon Mitchell', 'email' => 'brandon.mitchell@washywashy.com', 'branch_id' => $branches[4]->id],

            // Santa Monica
            ['name' => 'Olivia Turner', 'email' => 'olivia.turner@washywashy.com', 'branch_id' => $branches[5]->id],
            ['name' => 'Jacob Foster', 'email' => 'jacob.foster@washywashy.com', 'branch_id' => $branches[5]->id],
            ['name' => 'Emma Collins', 'email' => 'emma.collins@washywashy.com', 'branch_id' => $branches[5]->id],

            // Pasadena
            ['name' => 'Nathan Gray', 'email' => 'nathan.gray@washywashy.com', 'branch_id' => $branches[6]->id],
            ['name' => 'Ashley Cooper', 'email' => 'ashley.cooper@washywashy.com', 'branch_id' => $branches[6]->id],
        ];

        foreach ($staffMembers as $staff) {
            User::factory()->create([
                'name' => $staff['name'],
                'email' => $staff['email'],
                'role' => 'staff',
                'branch_id' => $staff['branch_id'],
            ]);
        }

        // Seed remaining tables
        $this->call([
            PackageSeeder::class,
            BaySeeder::class,
            BayActivityLogSeeder::class,
            CustomerSeeder::class,
            WashSeeder::class,
            QueueEntrySeeder::class,
            InventoryItemSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
