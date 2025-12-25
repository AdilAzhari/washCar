<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class LoyaltyPoint extends Model
{
    use HasFactory;

    // Tier thresholds
    public const TIER_BRONZE = 'bronze';

    public const TIER_SILVER = 'silver';

    public const TIER_GOLD = 'gold';

    public const TIER_PLATINUM = 'platinum';

    public const TIER_THRESHOLDS = [
        self::TIER_BRONZE => 0,
        self::TIER_SILVER => 500,
        self::TIER_GOLD => 1500,
        self::TIER_PLATINUM => 3000,
    ];

    public const TIER_MULTIPLIERS = [
        self::TIER_BRONZE => 1.0,
        self::TIER_SILVER => 1.25,
        self::TIER_GOLD => 1.5,
        self::TIER_PLATINUM => 2.0,
    ];

    protected $fillable = [
        'customer_id',
        'points',
        'lifetime_points',
        'tier',
    ];

    protected $casts = [
        'points' => 'integer',
        'lifetime_points' => 'integer',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'customer_id', 'customer_id');
    }

    // Methods
    public function addPoints(int $points, string $description, ?int $washId = null, ?int $appointmentId = null): void
    {
        $this->points += $points;
        $this->lifetime_points += $points;
        $this->save();

        $this->transactions()->create([
            'customer_id' => $this->customer_id,
            'type' => 'earned',
            'points' => $points,
            'description' => $description,
            'wash_id' => $washId,
            'appointment_id' => $appointmentId,
        ]);

        $this->updateTier();
    }

    public function redeemPoints(int $points, string $description): bool
    {
        if ($this->points < $points) {
            return false;
        }

        $this->points -= $points;
        $this->save();

        $this->transactions()->create([
            'customer_id' => $this->customer_id,
            'type' => 'redeemed',
            'points' => -$points,
            'description' => $description,
        ]);

        return true;
    }

    public function updateTier(): void
    {
        $newTier = $this->calculateTier($this->lifetime_points);

        // Only upgrade tiers, never downgrade
        if ($newTier !== $this->tier && $this->isTierUpgrade($this->tier, $newTier)) {
            $this->tier = $newTier;
            $this->save();
        }
    }

    public function getTierMultiplier(): float
    {
        return self::TIER_MULTIPLIERS[$this->tier] ?? 1.0;
    }

    protected function isTierUpgrade(string $currentTier, string $newTier): bool
    {
        $tierOrder = [
            self::TIER_BRONZE => 0,
            self::TIER_SILVER => 1,
            self::TIER_GOLD => 2,
            self::TIER_PLATINUM => 3,
        ];

        return $tierOrder[$newTier] > $tierOrder[$currentTier];
    }

    protected function calculateTier(int $lifetimePoints): string
    {
        if ($lifetimePoints >= self::TIER_THRESHOLDS[self::TIER_PLATINUM]) {
            return self::TIER_PLATINUM;
        }
        if ($lifetimePoints >= self::TIER_THRESHOLDS[self::TIER_GOLD]) {
            return self::TIER_GOLD;
        }
        if ($lifetimePoints >= self::TIER_THRESHOLDS[self::TIER_SILVER]) {
            return self::TIER_SILVER;
        }

        return self::TIER_BRONZE;
    }
}
