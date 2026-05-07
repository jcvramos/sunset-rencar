<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationStage extends Model
{
    protected $fillable = [
        'reservation_id', 'stage', 'status', 'data', 'notes',
        'completed_by', 'completed_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'completed_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
