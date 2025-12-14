<?php

namespace Database\Seeders;

use App\Models\Wash;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Bay;
use Illuminate\Database\Seeder;

class WashSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $customers = Customer::all();
        $packages = Package::all();
        $bays = Bay::all();

        if ($branches->isEmpty() || $customers->isEmpty() || $packages->isEmpty() || $bays->isEmpty()) {
            return;
        }

        $statuses = ['active', 'completed'];

        // Create 20 wash transactions
        for ($i = 0; $i < 20; $i++) {
            $branch = $branches->random();
            $branchBays = $bays->where('branch_id', $branch->id);

            if ($branchBays->isEmpty()) {
                continue;
            }

            $status = $statuses[array_rand($statuses)];
            $startedAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            $completedAt = $status === 'completed' ? $startedAt->copy()->addMinutes(rand(15, 45)) : null;

            Wash::create([
                'branch_id' => $branch->id,
                'customer_id' => rand(0, 1) ? $customers->random()->id : null,
                'package_id' => $packages->random()->id,
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
