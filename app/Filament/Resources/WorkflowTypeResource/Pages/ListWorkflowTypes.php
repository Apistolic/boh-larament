<?php

namespace App\Filament\Resources\WorkflowTypeResource\Pages;

use App\Filament\Resources\WorkflowTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowTypes extends ListRecords
{
    protected static string $resource = WorkflowTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
