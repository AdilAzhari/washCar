<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
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

        // SMS channel would be added here if configured
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
        return (new MailMessage)
            ->subject('Appointment Confirmed - WashyWashy')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your car wash appointment has been confirmed.')
            ->line("**Location:** {$this->appointment->branch->name}")
            ->line("**Package:** {$this->appointment->package->name}")
            ->line("**Date & Time:** {$this->appointment->scheduled_at->format('F j, Y \a\t g:i A')}")
            ->line("**Vehicle:** {$this->appointment->vehicle_type} - {$this->appointment->plate_number}")
            ->line("**Bonus Points:** You'll earn {$this->appointment->package->loyalty_points} bonus points for booking!")
            ->action('View Appointment', route('customer.appointments.show', $this->appointment))
            ->line('Thank you for choosing WashyWashy!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'type' => 'appointment_confirmed',
            'title' => 'Appointment Confirmed',
            'message' => "Your appointment at {$this->appointment->branch->name} on {$this->appointment->scheduled_at->format('M j, Y \a\t g:i A')} has been confirmed.",
            'action_url' => route('customer.appointments.show', $this->appointment),
        ];
    }
}
