<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {type : 24h or 1h}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders for upcoming appointments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');

        if (!in_array($type, ['24h', '1h'])) {
            $this->error('Invalid type. Use "24h" or "1h".');
            return Command::FAILURE;
        }

        // Calculate the time window for appointments
        $now = now();

        if ($type === '24h') {
            // Find appointments scheduled 24 hours from now (+/- 30 minutes)
            $startTime = $now->copy()->addHours(24)->subMinutes(30);
            $endTime = $now->copy()->addHours(24)->addMinutes(30);
            $reminderType = '24h_before';
        } else {
            // Find appointments scheduled 1 hour from now (+/- 15 minutes)
            $startTime = $now->copy()->addHour()->subMinutes(15);
            $endTime = $now->copy()->addHour()->addMinutes(15);
            $reminderType = '1h_before';
        }

        // Get appointments in the time window
        $appointments = Appointment::whereBetween('scheduled_at', [$startTime, $endTime])
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('customer')
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            // Check if reminder already sent
            $alreadySent = $appointment->reminders()
                ->where('reminder_type', $reminderType)
                ->whereNotNull('sent_at')
                ->exists();

            if ($alreadySent) {
                continue;
            }

            // Dispatch job to send reminder
            SendAppointmentReminder::dispatch($appointment, $reminderType);
            $count++;
        }

        $this->info("Sent {$count} {$type} appointment reminders.");

        return Command::SUCCESS;
    }
}
