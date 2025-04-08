<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',      // Type of event (e.g., 'contact.created', 'deal.updated')
        'workflow_type',   // Associated workflow type
        'payload',         // JSON payload of the event data
        'status',         // Status of n8n processing (pending, processed, failed)
        'processed_at',   // When the event was processed by n8n
        'error_message'   // Store any error messages from n8n
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime'
    ];

    // Scope to get unprocessed events
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }

    // Scope to get failed events
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Mark event as processed
    public function markAsProcessed()
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now()
        ]);
    }

    // Mark event as failed
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }
}
