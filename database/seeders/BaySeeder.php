<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class BaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = \App\Models\Branch::all();

        $bayStatuses = ['idle', 'active', 'maintenance'];

        foreach ($branches as $branch) {
            // Create 3-5 bays per branch
            $bayCount = random_int(3, 5);

            for ($i = 1; $i <= $bayCount; $i++) {
                \App\Models\Bay::create([
                    'name' => "Bay {$i}",
                    'branch_id' => $branch->id,
                    'status' => $bayStatuses[array_rand($bayStatuses)],
                ]);
            }
        }
    }
}
