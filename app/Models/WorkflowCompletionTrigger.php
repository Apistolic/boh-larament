<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowCompletionTrigger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
        'type',
        'trigger_type',
        'criteria',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Success types indicate the workflow achieved its goal
    const TYPE_SUCCESS = 'success';
    // Failure types indicate the workflow should stop because it's no longer relevant/possible
    const TYPE_FAILURE = 'failure';

    // Trigger types
    const TRIGGER_LIFECYCLE_STAGE_CHANGED = 'lifecycle_stage_changed';
    const TRIGGER_NO_RESPONSE = 'no_response';
    const TRIGGER_STAGE_DEMOTED = 'stage_demoted';
    const TRIGGER_DONATION_RECEIVED = 'donation_received';
    const TRIGGER_DONATION_RENEWED = 'donation_renewed';
    const TRIGGER_MANUAL = 'manual';

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Check if this trigger has been met for a given workflow execution
     */
    public function isTriggerMet(WorkflowExecution $execution): bool
    {
        $contact = $execution->contact;

        return match($this->trigger_type) {
            self::TRIGGER_LIFECYCLE_STAGE_CHANGED => $this->checkLifecycleStageChange($contact),
            self::TRIGGER_NO_RESPONSE => $this->checkNoResponse($execution),
            self::TRIGGER_STAGE_DEMOTED => $this->checkStageDemotion($contact),
            self::TRIGGER_DONATION_RECEIVED => $this->checkDonationReceived($contact),
            self::TRIGGER_DONATION_RENEWED => $this->checkDonationRenewed($contact),
            default => false,
        };
    }

    protected function checkLifecycleStageChange(Contact $contact): bool
    {
        if (!isset($this->criteria['stage'])) {
            return false;
        }

        return $contact->activeLifecycleStages()
            ->where('lifecycle_stages.slug', $this->criteria['stage'])
            ->exists();
    }

    protected function checkNoResponse(WorkflowExecution $execution): bool
    {
        if (!isset($this->criteria['days'])) {
            return false;
        }

        $lastResponse = $execution->touches()
            ->whereNotNull('responded_at')
            ->latest('responded_at')
            ->first();

        if (!$lastResponse) {
            $daysSinceStart = now()->diffInDays($execution->created_at);
            return $daysSinceStart >= $this->criteria['days'];
        }

        $daysSinceResponse = now()->diffInDays($lastResponse->responded_at);
        return $daysSinceResponse >= $this->criteria['days'];
    }

    protected function checkStageDemotion(Contact $contact): bool
    {
        if (!isset($this->criteria['from_stage']) || !isset($this->criteria['to_stage'])) {
            return false;
        }

        // Check if there's a lifecycle change from the higher stage to the lower stage
        return $contact->lifecycleChanges()
            ->where('from_stage', $this->criteria['from_stage'])
            ->where('to_stage', $this->criteria['to_stage'])
            ->exists();
    }

    protected function checkDonationReceived(Contact $contact): bool
    {
        if (!isset($this->criteria['minimum_amount'])) {
            return false;
        }

        // This is a placeholder - implement actual donation check based on your donation model
        return false;
    }

    protected function checkDonationRenewed(Contact $contact): bool
    {
        if (!isset($this->criteria['renewal_count'])) {
            return false;
        }

        // This is a placeholder - implement actual renewal check based on your donation model
        return false;
    }
}
