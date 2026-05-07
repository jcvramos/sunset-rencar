<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SublessorPayable extends Model
{
    protected $fillable = [
        'sublessor_id', 'reservation_id', 'vehicle_id',
        'amount', 'currency', 'status',
        'due_date', 'paid_at', 'payment_reference',
        'paid_by', 'notes',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'due_date' => 'date',
        'paid_at'  => 'date',
    ];

    public function sublessor(): BelongsTo   { return $this->belongsTo(Sublessor::class); }
    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function vehicle(): BelongsTo     { return $this->belongsTo(Vehicle::class); }
    public function payer(): BelongsTo       { return $this->belongsTo(User::class, 'paid_by'); }
}
