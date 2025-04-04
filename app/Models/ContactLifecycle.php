<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactLifecycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact_lifecycle';

    protected $fillable = [
        'contact_id',
        'lifecycle_stage_id',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(LifecycleStage::class, 'lifecycle_stage_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }
}
