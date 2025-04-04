<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DonorStatsWidget;
use App\Filament\Widgets\MomStatsWidget;
use App\Filament\Widgets\NeighborStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            DonorStatsWidget::class,
            NeighborStatsWidget::class,
            MomStatsWidget::class,
        ];
    }
}
