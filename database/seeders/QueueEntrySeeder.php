<?php

namespace Database\Seeders;

use App\Models\QueueEntry;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Database\Seeder;

class QueueEntrySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $customers = Customer::all();
        $packages = Package::all();

        if ($branches->isEmpty() || $customers->isEmpty() || $packages->isEmpty()) {
            return;
        }

        $statuses = ['waiting', 'in_progress', 'completed'];
        $plateNumbers = [
            'ABC-123', 'XYZ-789', 'DEF-456', 'GHI-321', 'JKL-654',
            'MNO-987', 'PQR-147', 'STU-258', 'VWX-369', 'YZA-741'
        ];

        $position = 1;
        foreach ($branches->take(2) as $branch) {
            for ($i = 0; $i < 5; $i++) {
                $status = $statuses[array_rand($statuses)];

                QueueEntry::create([
                    'branch_id' => $branch->id,
                    'customer_id' => $customers->random()->id,
                    'package_id' => $packages->random()->id,
                    'plate_number' => $plateNumbers[$i % count($plateNumbers)],
                    'position' => $status === 'waiting' ? $position++ : $i + 1,
                    'status' => $status,
                    'joined_at' => now()->subMinutes(rand(5, 120)),
                    'started_at' => in_array($status, ['in_progress', 'completed']) ? now()->subMinutes(rand(5, 60)) : null,
                    'completed_at' => $status === 'completed' ? now()->subMinutes(rand(1, 30)) : null,
                ]);
            }
        }
    }
}
