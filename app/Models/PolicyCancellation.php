<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyCancellation extends Model
{
    protected $fillable = [
        'hours_before', 'hours_before_max', 'refund_percentage', 'label', 'is_active',
    ];

    protected $casts = [
        'refund_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function getRefundForHours(int $hoursAhead): float
    {
        $policy = static::where('is_active', true)
            ->where('hours_before', '<=', $hoursAhead)
            ->where(function ($q) use ($hoursAhead) {
                $q->whereNull('hours_before_max')
                  ->orWhere('hours_before_max', '>=', $hoursAhead);
            })
            ->orderByDesc('hours_before')
            ->first();

        return $policy ? (float) $policy->refund_percentage : 0.0;
    }
}
