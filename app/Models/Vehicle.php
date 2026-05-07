<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    public const CATALOG_ZONES = [
        'frontal_3_4'       => 'Frontal 3/4',
        'frontal'           => 'Frontal',
        'lateral_izq'       => 'Lateral Izq.',
        'lateral_der'       => 'Lateral Der.',
        'trasera_3_4'       => 'Trasera 3/4',
        'trasera'           => 'Trasera',
        'interior_general'  => 'Interior',
        'tablero'           => 'Tablero',
        'asientos_traseros' => 'Asientos traseros',
        'maletero'          => 'Maletero',
        'rines'             => 'Rines / llantas',
        'detalle_extra'     => 'Detalle / extra',
    ];

    protected $fillable = [
        'plate', 'name', 'vehicle_type_id', 'brand', 'model', 'year',
        'color', 'seats', 'status', 'is_own', 'sublessor_id',
        'daily_rate', 'deposit_amount', 'description',
        'main_photo', 'photos', 'is_active',
    ];

    protected $casts = [
        'photos' => 'array',
        'is_own' => 'boolean',
        'is_active' => 'boolean',
        'daily_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function sublessor(): BelongsTo
    {
        return $this->belongsTo(Sublessor::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(VehicleAvailability::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class);
    }

    public function isAvailableForDates(string $startDate, string $endDate): bool
    {
        return !$this->availability()
            ->whereBetween('date', [$startDate, $endDate])
            ->exists();
    }
}
