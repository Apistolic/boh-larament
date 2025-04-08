<?php

namespace App\Events;

use App\Models\WorkflowEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowActionEvent
{
    use Dispatchable, SerializesModels;

    public WorkflowEvent $workflowEvent;

    public function __construct(
        public string $eventType,
        public string $workflowType,
        public array $payload
    ) {
        // Create and store the workflow event
        $this->workflowEvent = WorkflowEvent::create([
            'event_type' => $eventType,
            'workflow_type' => $workflowType,
            'payload' => $payload,
            'status' => 'pending'
        ]);
    }
}
