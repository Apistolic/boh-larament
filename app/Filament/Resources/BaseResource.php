<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class BaseResource extends Resource
{
    /**
     * Apply common query scopes to the resource query
     */
    // protected static function applyCommonScopes(Builder $query): Builder
    // {
    //     return $query->latest();
    // }

    /**
     * Get the display name for the resource
     */
    // public static function getModelLabel(): string
    // {
    //     return Str::title(Str::snake(class_basename(static::getModel()), ' '));
    // }

    /**
     * Get the plural display name for the resource
     */
    // public static function getPluralModelLabel(): string
    // {
    //     return Str::plural(static::getModelLabel());
    // }

    /**
     * Check if the current user has permission to perform an action
     */
    // public static function can(string $action, $model = null): bool
    // {
    //     $user = auth()->user();
        
    //     if (!$user) {
    //         return false;
    //     }

    //     $modelName = Str::kebab(class_basename(static::getModel()));
    //     $permission = match($action) {
    //         'viewAny' => "view-any-{$modelName}",
    //         'view' => "view-{$modelName}",
    //         'create' => "create-{$modelName}",
    //         'update' => "update-{$modelName}",
    //         'delete' => "delete-{$modelName}",
    //         default => $action,
    //     };

    //     return $user->can($permission, $model ?? static::getModel());
    // }

    /**
     * Get the base query for the resource
     */
    // public static function getEloquentQuery(): Builder
    // {
    //     return static::applyCommonScopes(parent::getEloquentQuery());
    // }

    /**
     * Get the globally searchable attributes
     */
    // public static function getGloballySearchableAttributes(): array
    // {
    //     return ['name', 'title', 'slug'];
    // }

    /**
     * Format a record for global search results
     */
    // public static function getGlobalSearchResultTitle($record): string
    // {
    //     return $record->name ?? $record->title ?? parent::getGlobalSearchResultTitle($record);
    // }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}