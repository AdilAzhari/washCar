<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\QueueEntry;
use Illuminate\Database\Seeder;

final class QueueEntrySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::where('is_active', true)->get();
        $customers = Customer::where('status', 'active')->get();
        $packages = Package::where('is_active', true)->get();

        if ($branches->isEmpty() || $customers->isEmpty() || $packages->isEmpty()) {
            return;
        }

        // Create historical queue entries for the past 7 days
        for ($day = 7; $day >= 1; $day--) {
            $date = now()->subDays($day);

            foreach ($branches as $branch) {
                // Create 3-8 queue entries per day per branch
                $queueCount = random_int(3, 8);

                for ($i = 0; $i < $queueCount; $i++) {
                    $customer = $customers->random();
                    $package = $packages->random();

                    $joinedAt = $date->copy()->addHours(random_int(7, 19))->addMinutes(random_int(0, 59));
                    $startedAt = $joinedAt->copy()->addMinutes(random_int(5, 30));
                    $completedAt = $startedAt->copy()->addMinutes($package->duration_minutes + random_int(-5, 10));

                    QueueEntry::create([
                        'branch_id' => $branch->id,
                        'customer_id' => $customer->id,
                        'package_id' => $package->id,
                        'plate_number' => $customer->plate_number,
                        'position' => $i + 1,
                        'status' => 'completed',
                        'joined_at' => $joinedAt,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'created_at' => $joinedAt,
                        'updated_at' => $completedAt,
                    ]);
                }
            }
        }

        // Add waiting queue for first 4 branches
        foreach ($branches->take(4) as $branch) {
            $waitingCount = random_int(2, 6);
            $inProgressCount = random_int(0, 2);

            // Create waiting entries
            for ($i = 0; $i < $waitingCount; $i++) {
                $customer = $customers->random();
                $package = $packages->random();
                $joinedAt = now()->subMinutes(random_int(5, 45));

                QueueEntry::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customer->id,
                    'package_id' => $package->id,
                    'plate_number' => $customer->plate_number,
                    'position' => $i + 1,
                    'status' => 'waiting',
                    'joined_at' => $joinedAt,
                    'started_at' => null,
                    'completed_at' => null,
                    'created_at' => $joinedAt,
                    'updated_at' => $joinedAt,
                ]);
            }

            // Create in-progress entries
            for ($i = 0; $i < $inProgressCount; $i++) {
                $customer = $customers->random();
                $package = $packages->random();
                $joinedAt = now()->subMinutes(random_int(20, 60));
                $startedAt = now()->subMinutes(random_int(5, 20));

                QueueEntry::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customer->id,
                    'package_id' => $package->id,
                    'plate_number' => $customer->plate_number,
                    'position' => $waitingCount + $i + 1,
                    'status' => 'in_progress',
                    'joined_at' => $joinedAt,
                    'started_at' => $startedAt,
                    'completed_at' => null,
                    'created_at' => $joinedAt,
                    'updated_at' => $startedAt,
                ]);
            }
        }

        // Add some completed entries from earlier today
        foreach ($branches->take(3) as $branch) {
            $completedCount = random_int(3, 7);

            for ($i = 0; $i < $completedCount; $i++) {
                $customer = $customers->random();
                $package = $packages->random();

                $joinedAt = now()->subHours(random_int(3, 8))->subMinutes(random_int(0, 59));
                $startedAt = $joinedAt->copy()->addMinutes(random_int(5, 20));
                $completedAt = $startedAt->copy()->addMinutes($package->duration_minutes + random_int(-5, 10));

                QueueEntry::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customer->id,
                    'package_id' => $package->id,
                    'plate_number' => $customer->plate_number,
                    'position' => $i + 100, // High position to indicate they were served earlier
                    'status' => 'completed',
                    'joined_at' => $joinedAt,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'created_at' => $joinedAt,
                    'updated_at' => $completedAt,
                ]);
            }
        }
    }
}
