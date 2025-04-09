<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowInitiationTrigger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workflow_id',
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

    // Trigger types (moved from Workflow model)
    const TRIGGER_CONTACT_CREATED = 'contact_created';
    const TRIGGER_CONTACT_UPDATED = 'contact_updated';
    const TRIGGER_LIFECYCLE_STAGE_CHANGED = 'lifecycle_stage_changed';
    const TRIGGER_MANUAL = 'manual';

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Check if this trigger has been met for a given contact
     */
    public function isTriggerMet(Contact $contact, array $context = []): bool
    {
        return match($this->trigger_type) {
            self::TRIGGER_CONTACT_CREATED => $this->checkContactCreated($contact, $context),
            self::TRIGGER_CONTACT_UPDATED => $this->checkContactUpdated($contact, $context),
            self::TRIGGER_LIFECYCLE_STAGE_CHANGED => $this->checkLifecycleStageChanged($contact, $context),
            self::TRIGGER_MANUAL => true, // Manual triggers are always "met" - they just need user action
            default => false,
        };
    }

    protected function checkContactCreated(Contact $contact, array $context): bool
    {
        // If we're checking a contact_created event, it's always true
        // The workflow dispatcher will only call this for new contacts
        return true;
    }

    protected function checkContactUpdated(Contact $contact, array $context): bool
    {
        if (!isset($this->criteria['fields']) || !isset($context['changed_fields'])) {
            return false;
        }

        $requiredFields = $this->criteria['fields'];
        $changedFields = $context['changed_fields'];

        // Check if any of the required fields were changed
        return !empty(array_intersect($requiredFields, $changedFields));
    }

    protected function checkLifecycleStageChanged(Contact $contact, array $context): bool
    {
        if (!isset($this->criteria['stage'])) {
            return false;
        }

        // If we have specific from/to stage requirements
        if (isset($this->criteria['from_stage'])) {
            if (!isset($context['from_stage']) || $context['from_stage'] !== $this->criteria['from_stage']) {
                return false;
            }
        }

        // Check if the contact is in the target stage
        return $contact->activeLifecycleStages()
            ->where('lifecycle_stages.slug', $this->criteria['stage'])
            ->exists();
    }
}
