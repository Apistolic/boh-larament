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

        $this->command->info('Found ' . $workflows->count() . ' workflows and ' . $contacts->count() . ' contacts');

        foreach ($workflows as $workflow) {
            // Create 2-4 executions for each workflow
            $numExecutions = rand(2, 4);
            
            $this->command->info("Creating {$numExecutions} executions for workflow: {$workflow->name}");
            
            for ($i = 0; $i < $numExecutions; $i++) {
                $contact = $contacts->random();
                $startDate = Carbon::now()->subDays(rand(1, 30));

                $execution = WorkflowExecution::create([
                    'workflow_id' => $workflow->id,
                    'contact_id' => $contact->id,
                    'status' => WorkflowExecution::STATUS_PENDING,
                    'trigger_snapshot' => [
                        'contact' => [
                            'id' => $contact->id,
                            'first_name' => $contact->first_name,
                            'last_name' => $contact->last_name,
                            'email' => $contact->email,
                            'lifecycle_stages' => $contact->activeLifecycleStages()->pluck('name')->toArray(),
                        ],
                        'trigger_type' => $workflow->trigger_type,
                        'trigger_time' => $startDate->toIso8601String(),
                    ],
                    'created_at' => $startDate,
                    'updated_at' => $startDate->addMinutes(rand(5, 60)),
                ]);

                $this->command->info("  - Created execution {$execution->id} for contact: {$contact->first_name} {$contact->last_name}");

                // Start the workflow execution to create action executions
                try {
                    $execution->start();
                    $this->command->info("  - Started execution {$execution->id}");
                } catch (\Exception $e) {
                    $this->command->error("  - Failed to start execution {$execution->id}: " . $e->getMessage());
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
