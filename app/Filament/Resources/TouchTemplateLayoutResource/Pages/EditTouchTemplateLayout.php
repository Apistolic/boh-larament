<?php

namespace App\Filament\Resources\TouchTemplateLayoutResource\Pages;

use App\Filament\Resources\TouchTemplateLayoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTouchTemplateLayout extends EditRecord
{
    protected static string $resource = TouchTemplateLayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
