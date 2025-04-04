<?php

namespace App\Providers;

use App\Filament\Resources\MediaResource;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                MediaResource::class,
            ])
            ->maxContentWidth('full')
            ->databaseNotifications(false) // Disable database notifications
            ->globalSearch(false) // Disable global search
            ->sidebarCollapsibleOnDesktop() // Make sidebar collapsible
            ->renderHook(
                'panels::head.end',
                fn () => '<meta name="turbo-cache-control" content="no-cache">' // Disable Turbo caching
            );
    }
}
