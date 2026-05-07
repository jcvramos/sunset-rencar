<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    public const STAGES = [
        'cotizacion'   => '1. Cotización',
        'deposito'     => '2. Depósito',
        'validacion'   => '3. Validación',
        'confirmacion' => '4. Confirmación',
        'entrega'      => '5. Entrega',
        'activa'       => '6. Renta activa',
        'extension'    => '7. Extensión',
        'recepcion'    => '8. Recepción',
        'factura'      => '9. Factura',
        'cerrada'      => 'Cerrada',
        'cancelada'    => 'Cancelada',
    ];

    protected $fillable = [
        'code', 'client_id', 'vehicle_id', 'vehicle_type_id', 'sublessor_id',
        'start_date', 'end_date', 'days', 'daily_rate',
        'subtotal', 'discount', 'total',
        'deposit_amount', 'deposit_status', 'deposit_paid_at',
        'destination', 'destination_municipality', 'notes',
        'current_stage', 'requires_approval',
        'approved_by', 'approved_at',
        'created_by', 'source',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'deposit_paid_at'   => 'date',
        'approved_at'       => 'datetime',
        'requires_approval' => 'boolean',
        'daily_rate'        => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'discount'          => 'decimal:2',
        'total'             => 'decimal:2',
        'deposit_amount'    => 'decimal:2',
    ];

    public function client(): BelongsTo            { return $this->belongsTo(Client::class); }
    public function vehicle(): BelongsTo           { return $this->belongsTo(Vehicle::class); }
    public function vehicleType(): BelongsTo       { return $this->belongsTo(VehicleType::class); }
    public function sublessor(): BelongsTo         { return $this->belongsTo(Sublessor::class); }
    public function approver(): BelongsTo          { return $this->belongsTo(User::class, 'approved_by'); }
    public function creator(): BelongsTo           { return $this->belongsTo(User::class, 'created_by'); }

    public function stages(): HasMany              { return $this->hasMany(ReservationStage::class)->orderBy('id'); }
    public function photos(): HasMany              { return $this->hasMany(ReservationPhoto::class); }
    public function extensions(): HasMany          { return $this->hasMany(ReservationExtension::class); }
    public function damages(): HasMany             { return $this->hasMany(Damage::class); }
    public function payables(): HasMany            { return $this->hasMany(SublessorPayable::class); }
    public function cancellation(): HasOne         { return $this->hasOne(Cancellation::class); }

    public function deliveryPhotos()
    {
        return $this->photos()->where('type', 'entrega');
    }

    public function receptionPhotos()
    {
        return $this->photos()->where('type', 'recepcion');
    }

    public static function generateCode(): string
    {
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('RES-%d-%05d', $year, $count);
    }
}
