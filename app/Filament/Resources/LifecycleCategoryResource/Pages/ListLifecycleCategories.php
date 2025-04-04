<?php

namespace App\Filament\Resources\LifecycleCategoryResource\Pages;

use App\Filament\Resources\LifecycleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLifecycleCategories extends ListRecords
{
    protected static string $resource = LifecycleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
