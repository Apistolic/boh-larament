<?php

namespace App\Models;

use App\Events\WorkflowActionExecuted;
use App\Events\WorkflowCompleted;
use App\Events\WorkflowStarted;
use App\Services\WorkflowActions\ActionHandlerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class WorkflowExecution extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'workflow_id',
        'contact_id',
        'status',
        'trigger_snapshot',
        'results',
        'error',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'trigger_snapshot' => 'array',
        'results' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    private ?ActionHandlerFactory $actionHandlerFactory = null;

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function actionExecutions(): HasMany
    {
        return $this->hasMany(WorkflowActionExecution::class);
    }

    /**
     * Start executing the workflow
     */
    public function start(): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \RuntimeException('Cannot start a workflow execution that is not pending');
        }

        // Mark as in progress
        $this->status = self::STATUS_IN_PROGRESS;
        $this->started_at = now();
        $this->save();

        try {
            // Execute each action in sequence
            foreach ($this->workflow->actions as $action => $parameters) {
                Log::info("Starting action execution", [
                    'workflow_execution_id' => $this->id,
                    'action' => $action,
                    'parameters' => $parameters
                ]);

                // Create action execution record
                $actionExecution = $this->actionExecutions()->create([
                    'action' => $action,
                    'parameters' => is_array($parameters) ? $parameters : ['value' => $parameters],
                    'status' => WorkflowActionExecution::STATUS_PENDING,
                ]);

                // Execute the action
                try {
                    $actionExecution->status = WorkflowActionExecution::STATUS_IN_PROGRESS;
                    $actionExecution->started_at = now();
                    $actionExecution->save();

                    $result = $this->executeAction($action, is_array($parameters) ? $parameters : ['value' => $parameters]);

                    $actionExecution->status = WorkflowActionExecution::STATUS_COMPLETED;
                    $actionExecution->completed_at = now();
                    $actionExecution->result = $result;
                    $actionExecution->save();

                    Log::info("Action execution completed", [
                        'workflow_execution_id' => $this->id,
                        'action_execution_id' => $actionExecution->id,
                        'result' => $result
                    ]);
                } catch (\Exception $e) {
                    Log::error("Action execution failed", [
                        'workflow_execution_id' => $this->id,
                        'action_execution_id' => $actionExecution->id,
                        'error' => $e->getMessage()
                    ]);

                    $actionExecution->status = WorkflowActionExecution::STATUS_FAILED;
                    $actionExecution->completed_at = now();
                    $actionExecution->error = $e->getMessage();
                    $actionExecution->save();

                    throw $e;
                }
            }

            // Mark as completed
            $this->complete();

            Log::info("Workflow execution completed", [
                'workflow_execution_id' => $this->id
            ]);
        } catch (\Exception $e) {
            Log::error("Workflow execution failed", [
                'workflow_execution_id' => $this->id,
                'error' => $e->getMessage()
            ]);

            // Mark as failed
            $this->fail($e->getMessage());

            throw $e;
        }
    }

    /**
     * Execute a single action
     */
    protected function executeAction(string $action, array $parameters): array
    {
        if (!$this->actionHandlerFactory) {
            $this->actionHandlerFactory = new ActionHandlerFactory();
        }

        $handler = $this->actionHandlerFactory->create($action);
        $handler->setWorkflowExecution($this);
        return $handler->execute($this->contact, $parameters);
    }

    /**
     * Mark the workflow execution as completed
     */
    protected function complete(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->results = [
            'completed_at' => $this->completed_at->toIso8601String(),
            'action_count' => $this->actionExecutions()->count(),
            'successful_actions' => $this->actionExecutions()
                ->where('status', WorkflowActionExecution::STATUS_COMPLETED)
                ->count(),
        ];
        $this->save();

        // Fire workflow completed event
        event(new WorkflowCompleted(
            execution: $this,
            workflow: $this->workflow,
            contact: $this->contact,
            status: self::STATUS_COMPLETED,
        ));
    }

    /**
     * Mark the workflow execution as failed
     */
    protected function fail(string $error): void
    {
        $this->status = self::STATUS_FAILED;
        $this->error = $error;
        $this->completed_at = now();
        $this->save();

        // Fire workflow completed event with failed status
        event(new WorkflowCompleted(
            execution: $this,
            workflow: $this->workflow,
            contact: $this->contact,
            status: self::STATUS_FAILED,
        ));
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function markAsCompleted(array $results = []): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'results' => $results,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error' => $error,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
