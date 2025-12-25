<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bay;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Wash;
use Illuminate\Database\Seeder;

final class WashSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::where('is_active', true)->get();
        $customers = Customer::where('status', 'active')->get();
        $packages = Package::where('is_active', true)->get();
        $bays = Bay::all();

        if ($branches->isEmpty() || $customers->isEmpty() || $packages->isEmpty() || $bays->isEmpty()) {
            return;
        }

        $statuses = ['active', 'completed'];

        // Create historical washes (last 60 days)
        for ($day = 60; $day >= 1; $day--) {
            $date = now()->subDays($day);

            // Skip if it's the inactive branch
            foreach ($branches as $branch) {
                // Create 5-15 washes per day per branch
                $washCount = random_int(5, 15);

                for ($i = 0; $i < $washCount; $i++) {
                    $branchBays = $bays->where('branch_id', $branch->id);

                    if ($branchBays->isEmpty()) {
                        continue;
                    }

                    $package = $packages->random();
                    $startedAt = $date->copy()->addHours(random_int(7, 20))->addMinutes(random_int(0, 59));

                    // Historical washes are all completed
                    if ($day > 0) {
                        $status = 'completed';
                        $completedAt = $startedAt->copy()->addMinutes($package->duration_minutes + random_int(-5, 10));
                    } else {
                        // Today's washes can be active or completed
                        $status = $statuses[array_rand($statuses)];
                        $completedAt = $status === 'completed' ?
                            $startedAt->copy()->addMinutes($package->duration_minutes + random_int(-5, 10)) : null;
                    }

                    Wash::create([
                        'branch_id' => $branch->id,
                        'customer_id' => random_int(0, 4) === 0 ? null : $customers->random()->id, // 20% walk-in without customer record
                        'package_id' => $package->id,
                        'bay_id' => $branchBays->random()->id,
                        'status' => $status,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'created_at' => $startedAt,
                        'updated_at' => $completedAt ?? $startedAt,
                    ]);
                }
            }
        }

        // Create some active washes for today
        foreach ($branches->take(3) as $branch) {
            $branchBays = $bays->where('branch_id', $branch->id)->where('status', '!=', 'maintenance');

            if ($branchBays->isEmpty()) {
                continue;
            }

            $activeCount = random_int(1, min(3, $branchBays->count()));

            for ($i = 0; $i < $activeCount; $i++) {
                $package = $packages->random();
                $startedAt = now()->subMinutes(random_int(5, 30));

                Wash::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customers->random()->id,
                    'package_id' => $package->id,
                    'bay_id' => $branchBays->skip($i)->first()->id,
                    'status' => 'active',
                    'started_at' => $startedAt,
                    'completed_at' => null,
                    'created_at' => $startedAt,
                    'updated_at' => $startedAt,
                ]);
            }
        }
    }
}
