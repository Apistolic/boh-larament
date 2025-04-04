<?php

namespace App\Models;

use App\Events\WorkflowActionExecuted;
use App\Events\WorkflowCompleted;
use App\Events\WorkflowStarted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowExecution extends Model
{
    protected $fillable = [
        'workflow_id',
        'contact_id',
        'status',
        'started_at',
        'completed_at',
        'error',
        'trigger_snapshot',
        'results',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'trigger_snapshot' => 'array',
        'results' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

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
        $this->status = self::STATUS_IN_PROGRESS;
        $this->started_at = now();
        $this->save();

        // Fire workflow started event
        event(new WorkflowStarted(
            execution: $this,
            workflow: $this->workflow,
            contact: $this->contact,
        ));

        try {
            foreach ($this->workflow->actions as $action) {
                $this->executeAction($action);
            }

            $this->complete();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Execute a single workflow action
     */
    protected function executeAction(WorkflowAction $action): void
    {
        $status = self::STATUS_COMPLETED;
        $error = null;

        try {
            // Execute the action here
            // This is where you'd implement the actual action logic
        } catch (\Exception $e) {
            $status = self::STATUS_FAILED;
            $error = $e->getMessage();
            throw $e;
        } finally {
            // Fire workflow action executed event
            event(new WorkflowActionExecuted(
                execution: $this,
                workflow: $this->workflow,
                action: $action,
                contact: $this->contact,
                status: $status,
                error: $error,
            ));
        }
    }

    /**
     * Mark the workflow execution as completed
     */
    protected function complete(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
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
