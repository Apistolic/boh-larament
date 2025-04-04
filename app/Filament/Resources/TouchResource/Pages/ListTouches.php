<?php

namespace App\Filament\Resources\TouchResource\Pages;

use App\Filament\Resources\TouchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTouches extends ListRecords
{
    protected static string $resource = TouchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
