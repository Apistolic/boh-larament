<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LifecycleStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lifecycle_category_id',
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stage) {
            if (empty($stage->slug)) {
                $stage->slug = Str::slug($stage->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LifecycleCategory::class, 'lifecycle_category_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_lifecycle')
            ->withPivot(['started_at', 'ended_at', 'notes'])
            ->withTimestamps();
    }

    public function activeContacts(): BelongsToMany
    {
        return $this->contacts()->wherePivotNull('ended_at');
    }

    public function getEffectiveColorAttribute(): string
    {
        return $this->color ?? $this->category->color;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->category->name} - {$this->name}";
    }
}
