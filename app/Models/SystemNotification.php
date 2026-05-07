<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SystemNotification extends Model
{
    protected $fillable = [
        'type', 'recipient_type', 'recipient_id',
        'source_type', 'source_id', 'channel',
        'title', 'message', 'data', 'status',
        'sent_at', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function recipient(): MorphTo { return $this->morphTo(); }
    public function source(): MorphTo    { return $this->morphTo(); }
}
