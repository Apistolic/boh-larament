<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Touch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact_id',
        'workflow_execution_id',
        'type',
        'status',
        'metadata',
        'content',
        'subject',
        'scheduled_for',
        'executed_at',
        'error',
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_for' => 'datetime',
        'executed_at' => 'datetime',
    ];

    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';
    const TYPE_CALL = 'call';
    const TYPE_LETTER = 'letter';

    const STATUS_PENDING = 'pending';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function workflowExecution(): BelongsTo
    {
        return $this->belongsTo(WorkflowExecution::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_for', '<=', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_SCHEDULED]);
    }

    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'executed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error' => $error,
            'executed_at' => now(),
        ]);
    }
}
