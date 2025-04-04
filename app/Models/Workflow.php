<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'trigger_type',
        'trigger_criteria',
        'actions',
        'is_active',
        'last_executed_at',
        'execution_count',
    ];

    protected $casts = [
        'trigger_criteria' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'execution_count' => 'integer',
    ];

    // Workflow Types
    const TYPE_NEW_DONOR_CANDIDATE = 'new_donor_candidate';
    const TYPE_NEW_ACTIVE_DONOR = 'new_active_donor';
    const TYPE_NEW_NEIGHBORING_VOLUNTEER_CANDIDATE = 'new_neighboring_volunteer_candidate';
    const TYPE_NEW_NV = 'new_nv';
    const TYPE_NEW_MOM_CANDIDATE = 'new_mom_candidate';
    const TYPE_NEW_MOM = 'new_mom';
    const TYPE_GALA_CANDIDATE = 'new_gala_candidate';
    const TYPE_GALA_ATTENDEE = 'gala_attendee';
    const TYPE_GALA_AUCTION_WINNER = 'gala_auction_winner';
    const TYPE_GALA_NEIGHBOR_SIGNUP = 'gala_neighbor_signup';

    // Trigger Types
    const TRIGGER_CONTACT_CREATED = 'contact_created';
    const TRIGGER_CONTACT_UPDATED = 'contact_updated';
    const TRIGGER_LIFECYCLE_STAGE_CHANGED = 'lifecycle_stage_changed';
    const TRIGGER_MANUAL = 'manual';
    const TRIGGER_SCHEDULED = 'scheduled';

    public static function getTypes(): array
    {
        return [
            self::TYPE_NEW_DONOR_CANDIDATE => 'New Donor Candidate',
            self::TYPE_NEW_ACTIVE_DONOR => 'New Active Donor',
            self::TYPE_NEW_NEIGHBORING_VOLUNTEER_CANDIDATE => 'New Neighboring Volunteer Candidate',
            self::TYPE_NEW_NV => 'New NV',
            self::TYPE_NEW_MOM_CANDIDATE => 'New Mom Candidate',
            self::TYPE_NEW_MOM => 'New Mom',
            self::TYPE_GALA_CANDIDATE => 'Gala Candidate',
            self::TYPE_GALA_ATTENDEE => 'Gala Attendee',
            self::TYPE_GALA_AUCTION_WINNER => 'Gala Auction Winner',
            self::TYPE_GALA_NEIGHBOR_SIGNUP => 'Gala Neighbor Signup',
        ];
    }

    public static function getTriggerTypes(): array
    {
        return [
            self::TRIGGER_CONTACT_CREATED => 'Contact Created',
            self::TRIGGER_CONTACT_UPDATED => 'Contact Updated',
            self::TRIGGER_LIFECYCLE_STAGE_CHANGED => 'Lifecycle Stage Changed',
            self::TRIGGER_MANUAL => 'Manual Trigger',
            self::TRIGGER_SCHEDULED => 'Scheduled',
        ];
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_workflow')
            ->withTimestamps()
            ->withPivot(['status', 'executed_at']);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(WorkflowExecution::class);
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
