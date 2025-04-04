<?php

namespace App\Filament\Resources\LifecycleCategoryResource\Pages;

use App\Filament\Resources\LifecycleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLifecycleCategory extends EditRecord
{
    protected static string $resource = LifecycleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
