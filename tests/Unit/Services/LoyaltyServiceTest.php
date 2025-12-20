<?php

namespace Tests\Unit\Services;

use App\Models\LoyaltyPoint;
use App\Models\Package;
use App\Models\User;
use App\Models\Wash;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoyaltyService $loyaltyService;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loyaltyService = app(LoyaltyService::class);
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        // Create loyalty points record
        LoyaltyPoint::factory()->create([
            'customer_id' => $this->customer->id,
            'points' => 0,
            'lifetime_points' => 0,
            'tier' => 'bronze',
        ]);
    }

    public function test_calculates_points_for_bronze_tier(): void
    {
        $points = $this->loyaltyService->calculatePointsForWash(50.00, 'bronze');

        // Bronze tier: 1x multiplier, so $50 = 50 points
        $this->assertEquals(50, $points);
    }

    public function test_calculates_points_for_silver_tier(): void
    {
        $points = $this->loyaltyService->calculatePointsForWash(50.00, 'silver');

        // Silver tier: 1.25x multiplier, so $50 * 1.25 = 62.5 → 62 points
        $this->assertEquals(62, $points);
    }

    public function test_calculates_points_for_gold_tier(): void
    {
        $points = $this->loyaltyService->calculatePointsForWash(50.00, 'gold');

        // Gold tier: 1.5x multiplier, so $50 * 1.5 = 75 points
        $this->assertEquals(75, $points);
    }

    public function test_calculates_points_for_platinum_tier(): void
    {
        $points = $this->loyaltyService->calculatePointsForWash(50.00, 'platinum');

        // Platinum tier: 2x multiplier, so $50 * 2 = 100 points
        $this->assertEquals(100, $points);
    }

    public function test_awards_points_for_wash(): void
    {
        $package = Package::factory()->create([
            'price' => 50.00,
        ]);

        $wash = Wash::factory()->create([
            'customer_id' => $this->customer->id,
            'package_id' => $package->id,
            'total_amount' => 50.00,
        ]);

        $this->loyaltyService->awardPoints($this->customer, $wash);

        $this->customer->loyaltyPoints->refresh();

        $this->assertEquals(50, $this->customer->loyaltyPoints->points);
        $this->assertEquals(50, $this->customer->loyaltyPoints->lifetime_points);

        // Assert transaction logged
        $this->assertDatabaseHas('loyalty_transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'earned',
            'points' => 50,
            'wash_id' => $wash->id,
        ]);
    }

    public function test_tier_updates_from_bronze_to_silver(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->lifetime_points = 500;
        $loyaltyPoints->save();

        $loyaltyPoints->updateTier();

        $this->assertEquals('silver', $loyaltyPoints->tier);
    }

    public function test_tier_updates_from_silver_to_gold(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->tier = 'silver';
        $loyaltyPoints->lifetime_points = 1500;
        $loyaltyPoints->save();

        $loyaltyPoints->updateTier();

        $this->assertEquals('gold', $loyaltyPoints->tier);
    }

    public function test_tier_updates_from_gold_to_platinum(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->tier = 'gold';
        $loyaltyPoints->lifetime_points = 3000;
        $loyaltyPoints->save();

        $loyaltyPoints->updateTier();

        $this->assertEquals('platinum', $loyaltyPoints->tier);
    }

    public function test_tier_does_not_downgrade(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->tier = 'gold';
        $loyaltyPoints->lifetime_points = 1000; // Below gold threshold
        $loyaltyPoints->save();

        $loyaltyPoints->updateTier();

        // Should stay gold (no downgrades)
        $this->assertEquals('gold', $loyaltyPoints->tier);
    }

    public function test_adding_points_updates_tier_automatically(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;

        // Add enough points to reach silver tier
        $loyaltyPoints->addPoints(500, 'Test award');

        $loyaltyPoints->refresh();

        $this->assertEquals('silver', $loyaltyPoints->tier);
        $this->assertEquals(500, $loyaltyPoints->points);
        $this->assertEquals(500, $loyaltyPoints->lifetime_points);
    }

    public function test_redeeming_points_decreases_balance(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->points = 500;
        $loyaltyPoints->save();

        $result = $loyaltyPoints->redeemPoints(100, 'Discount applied');

        $this->assertTrue($result);

        $loyaltyPoints->refresh();
        $this->assertEquals(400, $loyaltyPoints->points);

        // Assert transaction logged
        $this->assertDatabaseHas('loyalty_transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'redeemed',
            'points' => -100,
        ]);
    }

    public function test_cannot_redeem_more_points_than_available(): void
    {
        $loyaltyPoints = $this->customer->loyaltyPoints;
        $loyaltyPoints->points = 50;
        $loyaltyPoints->save();

        $result = $loyaltyPoints->redeemPoints(100, 'Discount applied');

        $this->assertFalse($result);

        $loyaltyPoints->refresh();
        $this->assertEquals(50, $loyaltyPoints->points);
    }
}
