<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationPhoto extends Model
{
    public const ZONES = [
        'frontal'           => 'Frontal',
        'trasera'           => 'Trasera',
        'lateral_izq'       => 'Lateral Izquierdo',
        'lateral_der'       => 'Lateral Derecho',
        'interior_frontal'  => 'Interior Frontal',
        'interior_trasero'  => 'Interior Trasero',
        'tablero'           => 'Tablero / Combustible',
        'odometro'          => 'Odómetro',
    ];

    protected $fillable = [
        'reservation_id', 'type', 'zone', 'path', 'original_name',
        'mime_type', 'size', 'notes', 'captured_by', 'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'size'        => 'integer',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
