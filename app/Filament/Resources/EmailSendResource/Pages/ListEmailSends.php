<?php

namespace App\Filament\Resources\EmailSendResource\Pages;

use App\Filament\Resources\EmailSendResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailSends extends ListRecords
{
    protected static string $resource = EmailSendResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
