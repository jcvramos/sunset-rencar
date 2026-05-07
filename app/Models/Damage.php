<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Damage extends Model
{
    public const STATUSES = ['pendiente', 'cobrado', 'disputado', 'absorbido'];

    protected $fillable = [
        'reservation_id', 'vehicle_id', 'client_id',
        'zone', 'description', 'evidence_photo', 'extra_photos',
        'amount_charged', 'linked_to_deposit', 'status',
        'occurred_at', 'reported_by', 'reported_at',
        'resolved_by', 'resolved_at', 'notes',
    ];

    protected $casts = [
        'extra_photos'      => 'array',
        'amount_charged'    => 'decimal:2',
        'linked_to_deposit' => 'boolean',
        'occurred_at'       => 'date',
        'reported_at'       => 'datetime',
        'resolved_at'       => 'datetime',
    ];

    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function vehicle(): BelongsTo     { return $this->belongsTo(Vehicle::class); }
    public function client(): BelongsTo      { return $this->belongsTo(Client::class); }
    public function reporter(): BelongsTo    { return $this->belongsTo(User::class, 'reported_by'); }
    public function resolver(): BelongsTo    { return $this->belongsTo(User::class, 'resolved_by'); }
}
