<?php

namespace App\Filament\Resources\TouchTemplateResource\Pages;

use App\Filament\Resources\TouchTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTouchTemplates extends ListRecords
{
    protected static string $resource = TouchTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
