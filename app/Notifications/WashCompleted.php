<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Wash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WashCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Wash $wash,
        public int $pointsEarned
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

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $loyaltyPoints = $notifiable->loyaltyPoints;
        $tier = $loyaltyPoints?->tier ?? 'bronze';

        return (new MailMessage)
            ->subject('Service Complete - Thank You! - WashyWashy')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Thank you for choosing WashyWashy! Your car wash is complete.')
            ->line("**Package:** {$this->wash->package->name}")
            ->line("**Location:** {$this->wash->branch->name}")
            ->line("**Amount:** \${$this->wash->total_amount}")
            ->line("**Points Earned:** {$this->pointsEarned} loyalty points")
            ->line("**Current Balance:** {$loyaltyPoints?->points} points ({$tier} tier)")
            ->action('View Receipt', route('customer.history'))
            ->line('We hope to see you again soon!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'wash_id' => $this->wash->id,
            'type' => 'wash_completed',
            'title' => 'Service Complete!',
            'message' => "Your {$this->wash->package->name} wash is complete. You earned {$this->pointsEarned} points!",
            'points_earned' => $this->pointsEarned,
            'amount' => $this->wash->total_amount,
        ];
    }
}
