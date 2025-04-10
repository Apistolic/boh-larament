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
        'notes',
        'source',
        'last_touched_at',
    ];

    protected $casts = [
        'last_touched_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_phone',
        'formatted_mobile_phone',
        'full_name',
    ];

    protected static function booted(): void
    {
        static::updating(function (Contact $contact): void {
            // If lifecycle stages change, fire an event
            if ($contact->isDirty('lifecycle_stages')) {
                $oldStages = $contact->getOriginal('lifecycle_stages');
                $newStages = $contact->lifecycle_stages;
                
                $oldStage = $oldStages ? $oldStages->first()?->name : '';
                $newStage = $newStages ? $newStages->first()?->name : '';

                event(new ContactLifecycleChanged(
                    contact: $contact,
                    oldLifecycleStage: $oldStage ?? '',
                    newLifecycleStage: $newStage ?? '',
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
        return $this->activeLifecycleStages()
            ->where('name', 'like', '%Retired%')
            ->exists();
    }

    /**
     * Check if the contact is in process
     */
    public function isInProcess(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'not like', '%Retired%')
            ->exists();
    }

    /**
     * Check if the contact is a donor.
     */
    public function isDonor(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', 'Donor%')
            ->where('name', 'not like', '%Candidate%')
            ->exists();
    }

    public function isDonorCandidate(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', 'Donor Candidate%')
            ->exists();
    }

    public function isDonorRetired(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', 'Donor Retired%')
            ->exists();
    }

    public function isNeighbor(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', 'Neighbor%')
            ->where('name', 'not like', '%Candidate%')
            ->exists();
    }

    public function isMom(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', 'Mom%')
            ->where('name', 'not like', '%Candidate%')
            ->exists();
    }

    public function isCandidate(): bool
    {
        return $this->activeLifecycleStages()
            ->where('name', 'like', '%Candidate%')
            ->exists();
    }

    // Format phone number for display
    public function getFormattedPhoneAttribute(): ?string
    {
        return $this->formatPhoneNumber($this->phone);
    }

    // Format mobile phone number for display
    public function getFormattedMobilePhoneAttribute(): ?string
    {
        return $this->formatPhoneNumber($this->mobile_phone);
    }

    protected function formatPhoneNumber(?string $number): ?string
    {
        if (!$number) {
            return null;
        }

        // Remove everything except digits
        $number = preg_replace('/[^0-9]/', '', $number);

        // Format as (XXX) XXX-XXXX
        if (strlen($number) === 10) {
            return sprintf(
                '(%s) %s-%s',
                substr($number, 0, 3),
                substr($number, 3, 3),
                substr($number, 6)
            );
        }

        return $number;
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

    public function contactTouches(): HasMany
    {
        return $this->hasMany(Touch::class);
    }
}
