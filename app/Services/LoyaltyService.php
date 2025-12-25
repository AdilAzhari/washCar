<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoyaltyPoint;
use App\Models\User;
use App\Models\Wash;

final class LoyaltyService
{
    /**
     * Award loyalty points to a customer for a completed wash.
     */
    public function awardPoints(User $customer, Wash $wash): void
    {
        // Ensure customer has loyalty points record
        $loyaltyPoints = $customer->loyaltyPoints()->firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'points' => 0,
                'lifetime_points' => 0,
                'tier' => LoyaltyPoint::TIER_BRONZE,
            ]
        );

        // Get the amount from the package price
        $amount = $wash->package?->price ?? 0.0;

        // Calculate points based on wash amount and tier multiplier
        $points = $this->calculatePointsForWash(
            $amount,
            $loyaltyPoints->tier
        );

        // Award the points
        $loyaltyPoints->addPoints(
            $points,
            "Earned from wash at {$wash->branch->name}",
            $wash->id,
            null
        );
    }

    /**
     * Calculate loyalty points for a wash amount and tier.
     *
     * Business rule: 1 point per $1 spent, multiplied by tier multiplier
     */
    public function calculatePointsForWash(float $amount, string $tier): int
    {
        $basePoints = (int) floor($amount); // 1 point per dollar
        $multiplier = LoyaltyPoint::TIER_MULTIPLIERS[$tier] ?? 1.0;

        return (int) floor($basePoints * $multiplier);
    }

    /**
     * Update customer's tier based on lifetime points.
     */
    public function updateTier(LoyaltyPoint $loyaltyPoint): void
    {
        $loyaltyPoint->updateTier();
    }

    /**
     * Redeem points for a discount code.
     *
     * @return string Discount code
     */
    public function redeemPoints(User $customer, int $points): ?string
    {
        $loyaltyPoints = $customer->loyaltyPoints;

        if (! $loyaltyPoints) {
            return null;
        }

        $success = $loyaltyPoints->redeemPoints(
            $points,
            'Redeemed for discount code'
        );

        if (! $success) {
            return null;
        }

        // Generate a simple discount code
        // In a real app, you'd create a DiscountCode model and track usage
        $discountCode = 'LOYALTY'.mb_strtoupper(mb_substr(md5($customer->id.time()), 0, 8));

        return $discountCode;
    }

    /**
     * Get tier progress information for a customer.
     */
    public function getTierProgress(User $customer): array
    {
        $loyaltyPoints = $customer->loyaltyPoints;

        if (! $loyaltyPoints) {
            return [
                'currentTier' => 'bronze',
                'lifetimePoints' => 0,
                'nextTier' => 'silver',
                'pointsToNextTier' => 500,
                'progressPercentage' => 0,
            ];
        }

        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        $currentTierIndex = array_search($loyaltyPoints->tier, $tiers);

        if ($currentTierIndex === false || $currentTierIndex >= count($tiers) - 1) {
            // Already at max tier
            return [
                'currentTier' => $loyaltyPoints->tier,
                'lifetimePoints' => $loyaltyPoints->lifetime_points,
                'nextTier' => null,
                'pointsToNextTier' => 0,
                'progressPercentage' => 100,
            ];
        }

        $nextTier = $tiers[$currentTierIndex + 1];
        $currentThreshold = LoyaltyPoint::TIER_THRESHOLDS[$loyaltyPoints->tier];
        $nextThreshold = LoyaltyPoint::TIER_THRESHOLDS[$nextTier];

        $pointsToNextTier = $nextThreshold - $loyaltyPoints->lifetime_points;
        $progressPercentage = (($loyaltyPoints->lifetime_points - $currentThreshold) / ($nextThreshold - $currentThreshold)) * 100;

        return [
            'currentTier' => $loyaltyPoints->tier,
            'lifetimePoints' => $loyaltyPoints->lifetime_points,
            'nextTier' => $nextTier,
            'pointsToNextTier' => $pointsToNextTier,
            'progressPercentage' => min(100, max(0, $progressPercentage)),
        ];
    }
}
