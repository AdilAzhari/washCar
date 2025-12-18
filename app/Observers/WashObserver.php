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
            // Only process if customer exists
            if ($wash->customer_id && $wash->customer) {
                // Check if customer has an associated user account for loyalty points
                if ($wash->customer->user_id && $wash->customer->user) {
                    // Get the amount from the package price
                    $amount = $wash->package?->price ?? 0.0;

                    // Calculate points earned
                    $pointsEarned = $this->loyaltyService->calculatePointsForWash(
                        $amount,
                        $wash->customer->user->loyaltyPoints?->tier ?? 'bronze'
                    );

                    // Award points to the user account
                    $this->loyaltyService->awardPoints($wash->customer->user, $wash);

                    // Send wash completed notification to the user
                    $wash->customer->user->notify(
                        new \App\Notifications\WashCompleted($wash, $pointsEarned)
                    );
                }
            }
        }
    }
}
