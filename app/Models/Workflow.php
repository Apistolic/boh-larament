<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legacy_type', // Will be removed after migration
        'workflow_type_id',
        'description',
        'trigger_type', // Legacy field
        'trigger_criteria', // Legacy field
        'actions',
        'is_active',
        'last_executed_at',
        'execution_count',
        'legacy_trigger',
    ];

    protected $casts = [
        'trigger_criteria' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'execution_count' => 'integer',
        'legacy_trigger' => 'boolean',
    ];

    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class)
            ->withTimestamps()
            ->withPivot(['status', 'executed_at']);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
    }

    public function initiationTriggers(): HasMany
    {
        return $this->hasMany(WorkflowInitiationTrigger::class);
    }

    public function completionTriggers(): HasMany
    {
        return $this->hasMany(WorkflowCompletionTrigger::class);
    }

    /**
     * Check if any initiation triggers have been met for a contact
     */
    public function checkInitiationTriggers(Contact $contact, array $context = []): bool
    {
        // If this is a legacy workflow, use the old trigger system
        if ($this->legacy_trigger) {
            return $this->checkLegacyTrigger($contact, $context);
        }

        // Check all active triggers
        foreach ($this->initiationTriggers()->where('is_active', true)->get() as $trigger) {
            if ($trigger->isTriggerMet($contact, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Legacy method to check old-style triggers
     */
    protected function checkLegacyTrigger(Contact $contact, array $context = []): bool
    {
        return match($this->trigger_type) {
            'contact_created' => true,
            'contact_updated' => $this->checkLegacyContactUpdated($context),
            'lifecycle_stage_changed' => $this->checkLegacyLifecycleStageChanged($contact),
            'manual' => true,
            default => false,
        };
    }

    protected function checkLegacyContactUpdated(array $context): bool
    {
        if (!isset($this->trigger_criteria['fields']) || !isset($context['changed_fields'])) {
            return false;
        }

        $requiredFields = $this->trigger_criteria['fields'];
        $changedFields = $context['changed_fields'];

        return !empty(array_intersect($requiredFields, $changedFields));
    }

    protected function checkLegacyLifecycleStageChanged(Contact $contact): bool
    {
        if (!isset($this->trigger_criteria['stage'])) {
            return false;
        }

        return $contact->activeLifecycleStages()
            ->where('lifecycle_stages.slug', $this->trigger_criteria['stage'])
            ->exists();
    }

    /**
     * Check if any completion triggers have been met for a workflow execution
     */
    public function checkCompletionTriggers(WorkflowExecution $execution): ?array
    {
        foreach ($this->completionTriggers()->where('is_active', true)->get() as $trigger) {
            if ($trigger->isTriggerMet($execution)) {
                return [
                    'triggered' => true,
                    'type' => $trigger->type,
                    'name' => $trigger->name,
                    'description' => $trigger->description,
                ];
            }
        }

        return null;
    }

    /**
     * Get active success triggers for this workflow
     */
    public function getSuccessTriggers()
    {
        return $this->completionTriggers()
            ->where('is_active', true)
            ->where('type', WorkflowCompletionTrigger::TYPE_SUCCESS)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get active failure triggers for this workflow
     */
    public function getFailureTriggers()
    {
        return $this->completionTriggers()
            ->where('is_active', true)
            ->where('type', WorkflowCompletionTrigger::TYPE_FAILURE)
            ->orderBy('sort_order')
            ->get();
    }

    public function getLastExecution(): ?WorkflowExecution
    {
        return $this->executions()->latest()->first();
    }

    public function getExecutionCount(): int
    {
        return $this->executions()->count();
    }

    public function getSuccessfulExecutionCount(): int
    {
        return $this->executions()
            ->where('status', WorkflowExecution::STATUS_COMPLETED)
            ->count();
    }

    public function getFailedExecutionCount(): int
    {
        return $this->executions()
            ->where('status', WorkflowExecution::STATUS_FAILED)
            ->count();
    }
}
