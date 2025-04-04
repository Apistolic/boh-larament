<?php

namespace App\Filament\Resources\LifecycleStageResource\Pages;

use App\Filament\Resources\LifecycleStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLifecycleStages extends ListRecords
{
    protected static string $resource = LifecycleStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
