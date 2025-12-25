<?php

namespace Database\Seeders;

use App\Models\Bay;
use App\Models\BayActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class BayActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $bays = Bay::all();
        $users = User::all();

        if ($bays->isEmpty() || $users->isEmpty()) {
            return;
        }

        $statuses = ['active', 'maintenance'];

        // Create activity logs for the past 30 days
        for ($day = 30; $day >= 1; $day--) {
            $date = now()->subDays($day);

            foreach ($bays as $bay) {
                // Create 2-5 status changes per day per bay
                $changeCount = rand(2, 5);

                for ($i = 0; $i < $changeCount; $i++) {
                    $previousStatus = $i === 0 ? 'idle' : $statuses[array_rand($statuses)];
                    $newStatus = $statuses[array_rand($statuses)];

                    // Ensure status actually changes
                    while ($newStatus === $previousStatus) {
                        $newStatus = $statuses[array_rand($statuses)];
                    }

                    $changedAt = $date->copy()->addHours(rand(7, 20))->addMinutes(rand(0, 59));

                    $branchUsers = $users->where('branch_id', $bay->branch_id);
                    $changedBy = $branchUsers->isNotEmpty() ? $branchUsers->random()->id : $users->random()->id;

                    BayActivityLog::create([
                        'bay_id' => $bay->id,
                        'previous_status' => $previousStatus,
                        'new_status' => $newStatus,
                        'changed_by' => $changedBy,
                        'notes' => $this->getReasonForChange($previousStatus, $newStatus),
                        'changed_at' => $changedAt,
                        'created_at' => $changedAt,
                        'updated_at' => $changedAt,
                    ]);
                }
            }
        }

        // Add some recent activity logs for today
        foreach ($bays->take(10) as $bay) {
            $changeCount = rand(1, 3);

            for ($i = 0; $i < $changeCount; $i++) {
                $previousStatus = $bay->status;
                $newStatus = $statuses[array_rand($statuses)];

                while ($newStatus === $previousStatus) {
                    $newStatus = $statuses[array_rand($statuses)];
                }

                $changedAt = now()->subHours(rand(1, 8))->subMinutes(rand(0, 59));

                $branchUsers = $users->where('branch_id', $bay->branch_id);
                $changedBy = $branchUsers->isNotEmpty() ? $branchUsers->random()->id : $users->random()->id;

                BayActivityLog::create([
                    'bay_id' => $bay->id,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'changed_by' => $changedBy,
                    'notes' => $this->getReasonForChange($previousStatus, $newStatus),
                    'changed_at' => $changedAt,
                    'created_at' => $changedAt,
                    'updated_at' => $changedAt,
                ]);
            }
        }
    }

    private function getReasonForChange(string $from, string $to): ?string
    {
        $reasons = [
            'active_to_idle' => [
                'Service completed',
                'Wash finished',
                'Customer departed',
            ],
            'active_to_maintenance' => [
                'Emergency maintenance required',
                'Equipment failure during service',
                'Water pressure issue detected',
            ],
            'maintenance_to_idle' => [
                'Maintenance completed',
                'Inspection passed',
                'Equipment repaired and tested',
            ],
            'maintenance_to_active' => [
                'Emergency service after quick fix',
                'Maintenance paused for urgent customer',
            ],
        ];

        $key = "{$from}_to_{$to}";

        if (isset($reasons[$key])) {
            return $reasons[$key][array_rand($reasons[$key])];
        }

        return 'Status updated';
    }
}
