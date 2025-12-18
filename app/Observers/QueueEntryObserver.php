<?php

namespace App\Observers;

use App\Models\QueueEntry;
use App\Notifications\QueuePositionUpdate;
use Illuminate\Support\Facades\Cache;

class QueueEntryObserver
{
    /**
     * Handle the QueueEntry "updated" event.
     *
     * Notify customer when they're approaching the front of the queue.
     */
    public function updated(QueueEntry $queueEntry): void
    {
        // Only notify for waiting customers
        if ($queueEntry->status !== 'waiting') {
            return;
        }

        // Only notify when position is 3 or less
        if ($queueEntry->position > 3) {
            return;
        }

        // Check if we've already notified for this queue entry
        $cacheKey = "queue_notified_{$queueEntry->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        // Only notify if customer exists
        if (! $queueEntry->customer) {
            return;
        }

        // Send notification
        $queueEntry->customer->notify(new QueuePositionUpdate($queueEntry));

        // Cache for 30 minutes to avoid duplicate notifications
        Cache::put($cacheKey, true, 1800);
    }

    /**
     * Handle position changes.
     *
     * When queue positions are updated, check if any customers need notifications.
     */
    public function saved(QueueEntry $queueEntry): void
    {
        // If position was just updated, trigger the update check
        if ($queueEntry->wasChanged('position')) {
            $this->updated($queueEntry);
        }
    }
}
