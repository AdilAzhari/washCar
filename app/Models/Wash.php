<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wash extends Model
{
    use HasFactory;
    protected $fillable = [
        'queue_entry_id',
        'bay_id',
        'branch_id',
        'customer_id',
        'package_id',
        'total_amount',
        'progress',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'progress' => 'integer',
        'total_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Wash $wash) {
            if ($wash->package_id && ! $wash->total_amount) {
                $package = Package::find($wash->package_id);
                if ($package) {
                    $wash->total_amount = $package->price;
                }
            }
        });
    }

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    public function bay(): BelongsTo
    {
        return $this->belongsTo(Bay::class);
    }

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
}
