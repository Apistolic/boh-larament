<?php

namespace App\Filament\Resources\TouchTemplateLayoutResource\Pages;

use App\Filament\Resources\TouchTemplateLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTouchTemplateLayouts extends ListRecords
{
    protected static string $resource = TouchTemplateLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
