<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QueueEntry extends Model
{
    protected $fillable = [
        'branch_id',
        'customer_id',
        'package_id',
        'plate_number',
        'position',
        'status',
        'joined_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'position' => 'integer',
        'joined_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function wash(): HasOne
    {
        return $this->hasOne(Wash::class);
    }
}
