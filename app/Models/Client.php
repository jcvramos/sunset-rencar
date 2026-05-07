<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'identity_number', 'phone', 'whatsapp',
        'email', 'nationality', 'client_type', 'status', 'block_reason',
        'license_photo', 'identity_photo', 'notes', 'source', 'city', 'country',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(Damage::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isBlocked(): bool
    {
        return $this->status === 'bloqueado';
    }
}
