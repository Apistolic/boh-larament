<?php

namespace App\Filament\Resources\TouchTemplateResource\Pages;

use App\Filament\Resources\TouchTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTouchTemplate extends EditRecord
{
    protected static string $resource = TouchTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
