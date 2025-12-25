<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CustomerLoyaltyController extends Controller
{
    /**
     * Display loyalty points and transaction history.
     */
    public function index(Request $request): Response
    {
        $customer = $request->user();

        // Get or create loyalty points record
        $loyaltyPoints = $customer->loyaltyPoints()->firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'points' => 0,
                'lifetime_points' => 0,
                'tier' => 'bronze',
            ]
        );

        // Get transaction history with pagination
        $transactions = $customer->loyaltyTransactions()
            ->with(['wash', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate points needed for next tier
        $tierThresholds = LoyaltyPoint::TIER_THRESHOLDS;
        $currentTier = $loyaltyPoints->tier;
        $nextTier = null;
        $pointsToNextTier = null;

        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        $currentTierIndex = array_search($currentTier, $tiers);

        if ($currentTierIndex !== false && $currentTierIndex < count($tiers) - 1) {
            $nextTier = $tiers[$currentTierIndex + 1];
            $pointsToNextTier = $tierThresholds[$nextTier] - $loyaltyPoints->lifetime_points;
        }

        return Inertia::render('Customer/Loyalty', [
            'loyaltyPoints' => $loyaltyPoints,
            'transactions' => $transactions,
            'tierThresholds' => $tierThresholds,
            'tierMultipliers' => LoyaltyPoint::TIER_MULTIPLIERS,
            'nextTier' => $nextTier,
            'pointsToNextTier' => $pointsToNextTier,
        ]);
    }

    /**
     * Redeem loyalty points.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $customer = $request->user();
        $loyaltyPoints = $customer->loyaltyPoints;

        if (! $loyaltyPoints) {
            return back()->withErrors(['points' => 'You do not have a loyalty points account.']);
        }

        $success = $loyaltyPoints->redeemPoints(
            $request->input('points'),
            $request->input('description')
        );

        if (! $success) {
            return back()->withErrors(['points' => 'Insufficient points for redemption.']);
        }

        return back()->with('success', 'Points redeemed successfully!');
    }
}
