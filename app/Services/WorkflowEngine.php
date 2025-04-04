<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Jobs\ExecuteWorkflowAction;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    public function dispatch(string $trigger, Contact $contact, array $context = []): void
    {
        // Find matching workflows
        $workflows = Workflow::where('trigger_type', $trigger)
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            if ($this->matchesCriteria($workflow, $contact, $context)) {
                $this->executeWorkflow($workflow, $contact, $context);
            }
        }
    }

    private function matchesCriteria(Workflow $workflow, Contact $contact, array $context): bool
    {
        $criteria = $workflow->trigger_criteria;

        foreach ($criteria as $field => $value) {
            // Handle special cases for lifecycle stage changes
            if ($field === 'previous_stage' && isset($context['previous_stage'])) {
                if ($value !== $context['previous_stage']) {
                    return false;
                }
                continue;
            }

            // Handle contact field checks
            if (isset($contact->$field)) {
                if (is_array($value)) {
                    if (!in_array($contact->$field, $value)) {
                        return false;
                    }
                } else {
                    if ($contact->$field != $value) {
                        return false;
                    }
                }
            }

            // Handle context field checks
            if (isset($context[$field]) && $context[$field] != $value) {
                return false;
            }
        }

        return true;
    }

    public function executeWorkflow(Workflow $workflow, Contact $contact, array $context = []): WorkflowExecution
    {
        // Create workflow execution record
        $execution = WorkflowExecution::create([
            'workflow_id' => $workflow->id,
            'contact_id' => $contact->id,
            'status' => WorkflowExecution::STATUS_PENDING,
            'trigger_snapshot' => [
                'contact' => $contact->toArray(),
                'context' => $context,
            ],
        ]);

        try {
            $execution->markAsInProgress();

            // Create action execution records
            foreach ($workflow->actions as $action => $parameters) {
                $actionExecution = $execution->actionExecutions()->create([
                    'action' => $action,
                    'parameters' => $parameters,
                    'status' => WorkflowActionExecution::STATUS_PENDING,
                ]);

                // Dispatch job to execute action
                ExecuteWorkflowAction::dispatch($actionExecution);
            }

            return $execution;
        } catch (\Exception $e) {
            Log::error('Failed to execute workflow', [
                'workflow_id' => $workflow->id,
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);

            $execution->markAsFailed($e->getMessage());
            throw $e;
        }
    }
}
