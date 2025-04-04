<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Models\WorkflowActionExecution;
use App\Services\WorkflowActions\ActionHandlerFactory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WorkflowExecutionSeeder extends Seeder
{
    protected ActionHandlerFactory $actionHandlerFactory;

    public function __construct(ActionHandlerFactory $actionHandlerFactory)
    {
        $this->actionHandlerFactory = $actionHandlerFactory;
    }

    public function run(): void
    {
        // Get some sample data
        $workflows = Workflow::all();
        $contacts = Contact::all();

        if ($workflows->isEmpty() || $contacts->isEmpty()) {
            $this->command->warn('Please seed workflows and contacts first!');
            return;
        }

        foreach ($workflows as $workflow) {
            // Create 2-4 executions for each workflow
            $numExecutions = rand(2, 4);
            
            for ($i = 0; $i < $numExecutions; $i++) {
                $contact = $contacts->random();
                $startDate = Carbon::now()->subDays(rand(1, 30));

                $execution = WorkflowExecution::create([
                    'workflow_id' => $workflow->id,
                    'contact_id' => $contact->id,
                    'status' => $this->getRandomStatus(),
                    'trigger_snapshot' => [
                        'contact' => [
                            'id' => $contact->id,
                            'first_name' => $contact->first_name,
                            'last_name' => $contact->last_name,
                            'email' => $contact->email,
                            'lifecycle_stage' => $contact->lifecycle_stage,
                        ],
                        'trigger_type' => $workflow->trigger_type,
                        'trigger_time' => $startDate->toIso8601String(),
                    ],
                    'created_at' => $startDate,
                    'updated_at' => $startDate->addMinutes(rand(5, 60)),
                ]);

                // Create action executions
                foreach ($workflow->actions as $action => $parameters) {
                    $actionStatus = $execution->status === WorkflowExecution::STATUS_FAILED ? 
                        WorkflowActionExecution::STATUS_FAILED :
                        $this->getRandomActionStatus();

                    $startedAt = $execution->created_at->addMinutes(rand(1, 5));
                    $completedAt = $actionStatus !== WorkflowActionExecution::STATUS_PENDING ? 
                        $startedAt->addMinutes(rand(1, 15)) : 
                        null;

                    $actionExecution = $execution->actionExecutions()->create([
                        'action' => $action,
                        'parameters' => $parameters,
                        'status' => $actionStatus,
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                        'created_at' => $startedAt,
                        'updated_at' => $completedAt ?? $startedAt,
                    ]);

                    // Add results or errors based on status
                    if ($actionStatus === WorkflowActionExecution::STATUS_COMPLETED) {
                        // Create handler and execute action to generate touch
                        try {
                            $handler = $this->actionHandlerFactory->create($action);
                            $handler->setWorkflowExecution($execution);
                            $result = $handler->execute($contact, $parameters);
                            
                            $actionExecution->update([
                                'result' => $result,
                            ]);
                        } catch (\Exception $e) {
                            // If handler execution fails, mark as failed
                            $actionStatus = WorkflowActionExecution::STATUS_FAILED;
                            $actionExecution->update([
                                'status' => $actionStatus,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    } elseif ($actionStatus === WorkflowActionExecution::STATUS_FAILED) {
                        $actionExecution->update([
                            'error' => $this->getRandomError(),
                        ]);
                    }
                }

                // Update execution results if completed
                if ($execution->status === WorkflowExecution::STATUS_COMPLETED) {
                    $execution->update([
                        'results' => [
                            'completed_at' => $execution->updated_at->toIso8601String(),
                            'action_count' => $execution->actionExecutions()->count(),
                            'successful_actions' => $execution->actionExecutions()
                                ->where('status', WorkflowActionExecution::STATUS_COMPLETED)
                                ->count(),
                        ],
                    ]);
                } elseif ($execution->status === WorkflowExecution::STATUS_FAILED) {
                    $failedAction = $execution->actionExecutions()
                        ->where('status', WorkflowActionExecution::STATUS_FAILED)
                        ->first();
                    
                    $execution->update([
                        'error' => $failedAction ? $failedAction->error : 'Unknown error',
                    ]);
                }
            }
        }
    }

    protected function getRandomStatus(): string
    {
        return collect([
            WorkflowExecution::STATUS_COMPLETED => 70,
            WorkflowExecution::STATUS_FAILED => 20,
            WorkflowExecution::STATUS_PENDING => 10,
        ])->random();
    }

    protected function getRandomActionStatus(): string
    {
        return collect([
            WorkflowActionExecution::STATUS_COMPLETED => 80,
            WorkflowActionExecution::STATUS_FAILED => 15,
            WorkflowActionExecution::STATUS_PENDING => 5,
        ])->random();
    }

    protected function getRandomError(): string
    {
        return collect([
            'Connection timeout',
            'Invalid response from external service',
            'Rate limit exceeded',
            'Service unavailable',
            'Invalid parameters',
        ])->random();
    }
}
