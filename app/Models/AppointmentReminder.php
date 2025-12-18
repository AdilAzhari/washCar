<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentReminder extends Model
{
    protected $fillable = [
        'appointment_id',
        'type',
        'reminder_type',
        'sent_at',
        'delivered',
        'error',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered' => 'boolean',
    ];

    // Relationships
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
