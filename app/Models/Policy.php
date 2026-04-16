<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'key', 'group', 'label', 'value', 'description', 'is_active',
    ];

    protected $casts = [
        'value' => 'array',
        'is_active' => 'boolean',
    ];
}
