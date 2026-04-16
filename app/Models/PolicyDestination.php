<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyDestination extends Model
{
    protected $fillable = [
        'municipality', 'department', 'status', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isAllowed(): bool
    {
        return $this->status === 'autorizado' && $this->is_active;
    }
}
