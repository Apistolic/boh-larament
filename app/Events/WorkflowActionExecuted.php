<?php

namespace App\Events;

use App\Models\Contact;
use App\Models\Workflow;
use App\Models\WorkflowAction;
use App\Models\WorkflowExecution;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowActionExecuted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public WorkflowExecution $execution,
        public Workflow $workflow,
        public WorkflowAction $action,
        public Contact $contact,
        public string $status,
        public ?string $error = null,
    ) {}
}
