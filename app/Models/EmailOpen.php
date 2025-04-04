<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOpen extends Model
{
    use HasUuids;

    protected $fillable = [
        'email_send_id',
        'user_agent',
        'ip_address',
        'email_client',
        'device_type',
        'country',
        'city',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function emailSend(): BelongsTo
    {
        return $this->belongsTo(EmailSend::class);
    }
}
