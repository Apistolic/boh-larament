<?php

namespace App\Filament\Resources\TouchTemplateBlockResource\Pages;

use App\Filament\Resources\TouchTemplateBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTouchTemplateBlocks extends ListRecords
{
    protected static string $resource = TouchTemplateBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
