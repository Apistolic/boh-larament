<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DonorStatsWidget;
use App\Filament\Widgets\LifecycleStagesChartWidget;
use App\Filament\Widgets\MomStatsWidget;
use App\Filament\Widgets\NeighborStatsWidget;
use App\Filament\Widgets\TouchesTimelineWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeaderWidgets(): array
    {
        return [
            DonorStatsWidget::class,
            NeighborStatsWidget::class,
            MomStatsWidget::class,
            TouchesTimelineWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            'lifecycle_stages_chart' => LifecycleStagesChartWidget::class,
        ];
    }
}
