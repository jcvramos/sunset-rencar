<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationExtension extends Model
{
    protected $fillable = [
        'reservation_id', 'previous_end_date', 'new_end_date',
        'additional_days', 'additional_amount', 'reason',
        'document_path', 'signature_path',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'previous_end_date' => 'date',
        'new_end_date'      => 'date',
        'approved_at'       => 'datetime',
        'additional_amount' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
