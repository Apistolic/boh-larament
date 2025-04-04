<?php

namespace App\Events;

use App\Models\Contact;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public WorkflowExecution $execution,
        public Workflow $workflow,
        public Contact $contact,
    ) {}
}
