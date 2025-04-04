<?php

namespace App\Filament\Resources\LifecycleStageResource\Pages;

use App\Filament\Resources\LifecycleStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLifecycleStage extends EditRecord
{
    protected static string $resource = LifecycleStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
