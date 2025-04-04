<?php

namespace App\Filament\Resources\TouchResource\Pages;

use App\Filament\Resources\TouchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTouch extends EditRecord
{
    protected static string $resource = TouchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
