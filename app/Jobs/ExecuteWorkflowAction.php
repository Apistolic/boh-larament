<?php

namespace App\Jobs;

use App\Models\WorkflowActionExecution;
use App\Services\WorkflowActions\ActionHandlerFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteWorkflowAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private WorkflowActionExecution $actionExecution
    ) {}

    public function handle(ActionHandlerFactory $factory): void
    {
        try {
            $this->actionExecution->markAsStarted();

            $handler = $factory->create($this->actionExecution->action);
            $result = $handler->execute(
                $this->actionExecution->workflowExecution->contact,
                $this->actionExecution->parameters
            );

            $this->actionExecution->markAsCompleted($result);

            // Check if all actions are completed
            $execution = $this->actionExecution->workflowExecution;
            if ($execution->actionExecutions()->where('status', '!=', WorkflowActionExecution::STATUS_COMPLETED)->doesntExist()) {
                $execution->markAsCompleted([
                    'completed_at' => now(),
                    'action_count' => $execution->actionExecutions()->count(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to execute workflow action', [
                'action_execution_id' => $this->actionExecution->id,
                'error' => $e->getMessage(),
            ]);

            $this->actionExecution->markAsFailed($e->getMessage());

            // Mark workflow execution as failed if this was a critical action
            $this->actionExecution->workflowExecution->markAsFailed(
                "Action '{$this->actionExecution->action}' failed: {$e->getMessage()}"
            );

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Workflow action job failed', [
            'action_execution_id' => $this->actionExecution->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
