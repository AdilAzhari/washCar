<?php

namespace App\Notifications;

use App\Models\QueueEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QueuePositionUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QueueEntry $queueEntry
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // SMS is ideal for queue notifications
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
        $estimatedWait = $this->queueEntry->position * 20; // 20 min per position

        return (new MailMessage)
            ->subject('Almost Your Turn! - WashyWashy')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You're almost at the front of the queue!")
            ->line("**Current Position:** #{$this->queueEntry->position}")
            ->line("**Estimated Wait:** ~{$estimatedWait} minutes")
            ->line("**Location:** {$this->queueEntry->branch->name}")
            ->line('Please be ready - we\'ll be calling you soon!')
            ->line('Thank you for your patience!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'queue_entry_id' => $this->queueEntry->id,
            'type' => 'queue_position_update',
            'title' => 'Almost Your Turn!',
            'message' => "You're #{$this->queueEntry->position} in the queue. Get ready!",
            'position' => $this->queueEntry->position,
            'branch_name' => $this->queueEntry->branch->name,
        ];
    }

    /**
     * Get SMS representation of the notification.
     *
     * Uncomment when Vonage is configured:
     *
     * public function toVonage(object $notifiable)
     * {
     *     $estimatedWait = $this->queueEntry->position * 20;
     *
     *     return (new VonageMessage)
     *         ->content("WashyWashy: You're #{$this->queueEntry->position} in queue at {$this->queueEntry->branch->name}. Est. wait: ~{$estimatedWait} min. Get ready!");
     * }
     */
}
