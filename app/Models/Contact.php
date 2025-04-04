<?php

namespace App\Models;

use App\Events\ContactLifecycleChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile_phone',
        'street',
        'street_2',
        'city',
        'state_code',
        'postal_code',
        'country',
        'lifecycle_stages',
        'notes',
        'source',
        'last_touched_at',
    ];

    protected $casts = [
        'last_touched_at' => 'datetime',
        'lifecycle_stages' => 'array',
    ];

    protected $appends = [
        'formatted_phone',
        'formatted_mobile_phone',
        'full_name',
    ];

    const LIFECYCLE_STAGES = [
        'donor_candidate' => 'Donor Candidate',
        'donor_active' => 'Active Donor',
        'donor_influencer' => 'Donor Influencer',
        'donor_aggregator' => 'Donor Aggregator',
        'donor_retired' => 'Retired Donor',
        'gala_candidate' => 'Gala Candidate',
        'gala_attendee' => 'Gala Attendee',
        'gala_donor' => 'Gala Donor',
        'gala_neighbor_signup' => 'Gala Neighbor Signup',
        'neighbor_candidate' => 'Neighbor Candidate',
        'neighbor_active' => 'Active Neighbor',
        'neighbor_retired' => 'Retired Neighbor',
        'neighbor_influencer' => 'Neighbor Influencer',
        'neighbor_leader' => 'Neighbor Leader',
        'mom_candidate' => 'Mom Candidate',
        'mom_active' => 'Active Mom',
        'mom_graduate' => 'Graduated Mom',
    ];

    protected static function booted()
    {
        static::updating(function (Contact $contact) {
            // If lifecycle_stage is changing, fire an event
            if ($contact->isDirty('lifecycle_stage')) {
                event(new ContactLifecycleChanged(
                    contact: $contact,
                    oldLifecycleStage: $contact->getOriginal('lifecycle_stage'),
                    newLifecycleStage: $contact->lifecycle_stage,
                ));
            }
        });
    }

    public function workflows(): BelongsToMany
    {
        return $this->belongsToMany(Workflow::class)
            ->withTimestamps()
            ->withPivot(['status', 'executed_at']);
    }

    public function workflowExecutions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
    }

    public function workflowTouches(): HasMany
    {
        return $this->hasMany(Touch::class);
    }

    /**
     * Check if the contact is retired
     */
    public function isRetired(): bool
    {
        return str_contains($this->lifecycle_stage, '_retired');
    }

    /**
     * Check if the contact is in process
     */
    public function isInProcess(): bool
    {
        return str_contains($this->lifecycle_stage, '_candidate');
    }

    /**
     * Check if the contact is a donor.
     */
    public function isDonor(): bool
    {
        return str_contains($this->lifecycle_stage, 'donor_active') ||
               str_contains($this->lifecycle_stage, 'donor_influencer') ||
               str_contains($this->lifecycle_stage, 'donor_aggregator');
    }

    public function isDonorCandidate(): bool
    {
        return str_contains($this->lifecycle_stage, 'donor_candidate');
    }

    public function isDonorRetired(): bool
    {
        return str_contains($this->lifecycle_stage, 'donor_retired');
    }

    public function isNeighbor(): bool
    {
        return str_contains($this->lifecycle_stage, 'neighbor_active') ||
               str_contains($this->lifecycle_stage, 'neighbor_leader') ||
               str_contains($this->lifecycle_stage, 'neighbor_influencer');
    }

    public function isMom(): bool
    {
        return str_contains($this->lifecycle_stage, 'mom');
    }

    public function isCandidate(): bool
    {
        return str_contains($this->lifecycle_stage, 'candidate');
    }

    // Format phone number for display
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        // Strip everything except digits
        $cleaned = preg_replace('/[^0-9]/', '', $this->phone);
        
        // Format as (XXX) XXX-XXXX
        if (strlen($cleaned) === 10) {
            return sprintf("(%s) %s-%s",
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6, 4)
            );
        }
        
        return $this->phone;
    }

    // Format mobile phone number for display
    public function getFormattedMobilePhoneAttribute(): ?string
    {
        if (!$this->mobile_phone) {
            return null;
        }

        // Strip everything except digits
        $cleaned = preg_replace('/[^0-9]/', '', $this->mobile_phone);
        
        // Format as (XXX) XXX-XXXX
        if (strlen($cleaned) === 10) {
            return sprintf("(%s) %s-%s",
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6, 4)
            );
        }
        
        return $this->mobile_phone;
    }

    // Get full name
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // Clean phone number before saving
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    // Clean mobile phone number before saving
    public function setMobilePhoneAttribute($value)
    {
        $this->attributes['mobile_phone'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    // Lifecycle relationships
    public function lifecycleStages(): BelongsToMany
    {
        return $this->belongsToMany(LifecycleStage::class, 'contact_lifecycle')
            ->withPivot(['status', 'started_at', 'ended_at'])
            ->withTimestamps();
    }

    public function activeLifecycleStages(): BelongsToMany
    {
        return $this->lifecycleStages()
            ->wherePivotNull('ended_at')
            ->orderBy('lifecycle_stages.sort_order');
    }

    public function contactLifecycles(): HasMany
    {
        return $this->hasMany(ContactLifecycle::class);
    }

    public function activeContactLifecycles(): HasMany
    {
        return $this->contactLifecycles()->whereNull('ended_at');
    }
}
