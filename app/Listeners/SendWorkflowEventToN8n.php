<?php

namespace App\Listeners;

use App\Events\WorkflowActionEvent;
use App\Services\N8nService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWorkflowEventToN8n implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly N8nService $n8nService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(WorkflowActionEvent $event): void
    {
        $this->n8nService->pushEvent($event->workflowEvent);
    }

    /**
     * Handle a job failure.
     */
    public function failed(WorkflowActionEvent $event, \Throwable $exception): void
    {
        $event->workflowEvent->markAsFailed($exception->getMessage());
    }
}
