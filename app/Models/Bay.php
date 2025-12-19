<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bay extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'status',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(BayActivityLog::class);
    }

    public function washes(): HasMany
    {
        return $this->hasMany(Wash::class);
    }

    public function currentWash(): HasOne
    {
        return $this->hasOne(Wash::class)->where('status', 'active');
    }
}
