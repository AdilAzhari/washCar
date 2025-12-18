<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'branch_id',
        'customer_id',
        'package_id',
        'plate_number',
        'vehicle_type',
        'scheduled_at',
        'actual_start_at',
        'completed_at',
        'status',
        'special_requests',
        'notes',
        'assigned_to',
        'queue_entry_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'actual_start_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'pending')
            ->orWhere('status', 'confirmed')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Methods
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isCancellable(): bool
    {
        return $this->canBeCancelled();
    }
}
