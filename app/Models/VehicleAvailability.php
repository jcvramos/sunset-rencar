<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAvailability extends Model
{
    protected $table = 'vehicle_availability';

    protected $fillable = [
        'vehicle_id', 'date', 'status', 'reservation_id', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
