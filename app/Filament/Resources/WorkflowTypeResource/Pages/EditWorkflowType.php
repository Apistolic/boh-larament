<?php

namespace App\Filament\Resources\WorkflowTypeResource\Pages;

use App\Filament\Resources\WorkflowTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkflowType extends EditRecord
{
    protected static string $resource = WorkflowTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
