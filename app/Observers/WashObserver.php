<?php

namespace App\Observers;

use App\Models\Wash;
use App\Services\LoyaltyService;

class WashObserver
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Handle the Wash "updated" event.
     *
     * Award loyalty points and send notification when wash is completed.
     */
    public function updated(Wash $wash): void
    {
        // Check if status changed to completed
        if ($wash->isDirty('status') && $wash->status === 'completed') {
            // Only award points if customer exists and has a customer ID
            if ($wash->customer_id && $wash->customer) {
                // Calculate points earned
                $pointsEarned = $this->loyaltyService->calculatePointsForWash(
                    $wash->total_amount,
                    $wash->customer->loyaltyPoints?->tier ?? 'bronze'
                );

                // Award points
                $this->loyaltyService->awardPoints($wash->customer, $wash);

                // Send wash completed notification
                $wash->customer->notify(
                    new \App\Notifications\WashCompleted($wash, $pointsEarned)
                );
            }
        }
    }
}
