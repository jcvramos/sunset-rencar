<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sublessor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'identity_number', 'phone', 'whatsapp', 'email',
        'address', 'city', 'bank_name', 'bank_account', 'payment_method',
        'default_share', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'default_share' => 'decimal:2',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function payables(): HasMany
    {
        return $this->hasMany(SublessorPayable::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function pendingPayablesTotal(): float
    {
        return (float) $this->payables()->where('status', 'pendiente')->sum('amount');
    }
}
