<?php

namespace App\Filament\Resources\WorkflowInitiationTriggerResource\Pages;

use App\Filament\Resources\WorkflowInitiationTriggerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowInitiationTrigger extends EditRecord
{
    protected static string $resource = WorkflowInitiationTriggerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
