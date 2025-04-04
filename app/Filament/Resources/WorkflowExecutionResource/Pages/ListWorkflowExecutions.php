<?php

namespace App\Filament\Resources\WorkflowExecutionResource\Pages;

use App\Filament\Resources\WorkflowExecutionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowExecutions extends ListRecords
{
    protected static string $resource = WorkflowExecutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed for executions
        ];
    }
}
