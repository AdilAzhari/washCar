<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public string $reminderType // '24h_before' or '1h_before'
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->email) {
            $channels[] = 'mail';
        }

        // SMS is especially useful for reminders
        // if ($notifiable->phone && config('services.vonage.key')) {
        //     $channels[] = 'vonage';
        // }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $timeUntil = $this->reminderType === '24h_before' ? '24 hours' : '1 hour';

        return (new MailMessage)
            ->subject('Appointment Reminder - WashyWashy')
            ->greeting("Hello {$notifiable->name}!")
            ->line("This is a friendly reminder about your upcoming car wash appointment in {$timeUntil}.")
            ->line("**Location:** {$this->appointment->branch->name}")
            ->line("**Address:** {$this->appointment->branch->address}")
            ->line("**Package:** {$this->appointment->package->name}")
            ->line("**Date & Time:** {$this->appointment->scheduled_at->format('F j, Y \a\t g:i A')}")
            ->line("**Vehicle:** {$this->appointment->vehicle_type} - {$this->appointment->plate_number}")
            ->action('View Appointment', route('customer.appointments.show', $this->appointment))
            ->line('See you soon!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $timeUntil = $this->reminderType === '24h_before' ? '24 hours' : '1 hour';

        return [
            'appointment_id' => $this->appointment->id,
            'type' => 'appointment_reminder',
            'reminder_type' => $this->reminderType,
            'title' => 'Appointment Reminder',
            'message' => "Your appointment at {$this->appointment->branch->name} is in {$timeUntil}.",
            'action_url' => route('customer.appointments.show', $this->appointment),
        ];
    }
}
