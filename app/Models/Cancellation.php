<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    protected $fillable = [
        'reservation_id', 'policy_cancellation_id', 'reason',
        'hours_anticipation', 'refund_percentage',
        'total_paid', 'amount_refunded', 'amount_retained',
        'document_path', 'signature_path', 'status', 'created_by',
    ];

    protected $casts = [
        'hours_anticipation' => 'decimal:2',
        'refund_percentage'  => 'decimal:2',
        'total_paid'         => 'decimal:2',
        'amount_refunded'    => 'decimal:2',
        'amount_retained'    => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(PolicyCancellation::class, 'policy_cancellation_id');
    }
}
