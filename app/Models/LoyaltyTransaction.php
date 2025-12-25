<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'points',
        'wash_id',
        'appointment_id',
        'description',
        'wash_amount',
        'metadata',
    ];

    protected $casts = [
        'points' => 'integer',
        'wash_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function wash(): BelongsTo
    {
        return $this->belongsTo(Wash::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
