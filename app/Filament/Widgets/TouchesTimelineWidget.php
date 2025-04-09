<?php

namespace App\Filament\Widgets;

use App\Models\Touch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TouchesTimelineWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getCacheLifetime(): ?int
    {
        return 60 * 60 * 24; // 24 hours in seconds
    }

    protected function getStats(): array
    {
        $pastWeekCount = Touch::where('executed_at', '>=', now()->subDays(7))
            ->whereNotNull('executed_at')
            ->count();

        $upcomingWeekCount = Touch::where('scheduled_for', '>=', now())
            ->where('scheduled_for', '<=', now()->addDays(7))
            ->whereNull('executed_at')
            ->count();

        $failedCount = Touch::where('status', Touch::STATUS_FAILED)
            ->where('executed_at', '>=', now()->subDays(7))
            ->count();

        return [
            Stat::make('Past Week Touches', $pastWeekCount)
                ->description('Touches executed in the last 7 days')
                ->color('success')
                ->url(
                    url: '/admin/touches?tableFilters[executed_at][from]=' . now()->subDays(7)->format('Y-m-d') . 
                        '&tableFilters[executed_at][until]=' . now()->format('Y-m-d') .
                        '&tableFilters[status]=sent'
                ),

            Stat::make('Upcoming Week', $upcomingWeekCount)
                ->description('Touches scheduled for next 7 days')
                ->color('info')
                ->url(
                    url: '/admin/touches?tableFilters[scheduled_for][from]=' . now()->format('Y-m-d') . 
                        '&tableFilters[scheduled_for][until]=' . now()->addDays(7)->format('Y-m-d') .
                        '&tableFilters[status][]=pending&tableFilters[status][]=scheduled'
                ),

            Stat::make('Failed Touches', $failedCount)
                ->description('Failed touches in the last 7 days')
                ->color('danger')
                ->url(
                    url: '/admin/touches?tableFilters[executed_at][from]=' . now()->subDays(7)->format('Y-m-d') . 
                        '&tableFilters[executed_at][until]=' . now()->format('Y-m-d') .
                        '&tableFilters[status]=failed'
                ),
        ];
    }
}
