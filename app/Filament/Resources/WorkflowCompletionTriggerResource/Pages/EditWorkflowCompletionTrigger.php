<?php

namespace App\Filament\Resources\WorkflowCompletionTriggerResource\Pages;

use App\Filament\Resources\WorkflowCompletionTriggerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowCompletionTrigger extends EditRecord
{
    protected static string $resource = WorkflowCompletionTriggerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
