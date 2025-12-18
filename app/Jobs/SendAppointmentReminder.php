<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use App\Notifications\AppointmentReminder as AppointmentReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $reminderType // '24h_before' or '1h_before'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if appointment is still valid
        if (! in_array($this->appointment->status, ['pending', 'confirmed'])) {
            return;
        }

        // Check if reminder already sent
        $existingReminder = AppointmentReminder::where('appointment_id', $this->appointment->id)
            ->where('reminder_type', $this->reminderType)
            ->where('sent_at', '!=', null)
            ->exists();

        if ($existingReminder) {
            return;
        }

        try {
            // Send notification to customer
            $this->appointment->customer->notify(
                new AppointmentReminderNotification($this->appointment, $this->reminderType)
            );

            // Log the reminder
            AppointmentReminder::create([
                'appointment_id' => $this->appointment->id,
                'type' => 'email', // or 'sms' based on configuration
                'reminder_type' => $this->reminderType,
                'sent_at' => now(),
                'delivered' => true,
            ]);
        } catch (\Exception $e) {
            // Log error
            AppointmentReminder::create([
                'appointment_id' => $this->appointment->id,
                'type' => 'email',
                'reminder_type' => $this->reminderType,
                'sent_at' => now(),
                'delivered' => false,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
