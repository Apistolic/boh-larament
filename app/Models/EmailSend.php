<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSend extends Model
{
    use HasUuids;

    protected $fillable = [
        'contact_id',
        'subject',
        'content',
        'tracking_pixel_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function opens(): HasMany
    {
        return $this->hasMany(EmailOpen::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(EmailClick::class);
    }

    public function getOpenRateAttribute(): float
    {
        return $this->opens()->distinct('ip_address')->count() > 0 ? 1 : 0;
    }

    public function getClickRateAttribute(): float
    {
        return $this->clicks()->distinct('ip_address')->count() > 0 ? 1 : 0;
    }

    public function getFirstOpenAttribute()
    {
        return $this->opens()->orderBy('opened_at')->first();
    }

    public function getLastOpenAttribute()
    {
        return $this->opens()->orderBy('opened_at', 'desc')->first();
    }
}
