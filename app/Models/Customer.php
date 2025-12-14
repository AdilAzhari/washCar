<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'plate_number',
        'vehicle_type',
        'make',
        'model',
        'color',
        'membership',
        'status',
    ];

    public function washes(): HasMany
    {
        return $this->hasMany(Wash::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }
}
