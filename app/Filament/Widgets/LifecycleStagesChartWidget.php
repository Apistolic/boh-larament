<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\LifecycleStage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LifecycleStagesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Contacts by Lifecycle Stage';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = LifecycleStage::select('lifecycle_stages.name')
            ->selectRaw('COUNT(DISTINCT contact_lifecycle.contact_id) as count')
            ->leftJoin('contact_lifecycle', 'lifecycle_stages.id', '=', 'contact_lifecycle.lifecycle_stage_id')
            ->whereNull('contact_lifecycle.ended_at')
            ->groupBy('lifecycle_stages.id', 'lifecycle_stages.name')
            ->orderByDesc('count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Contacts',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $data->map(function ($stage) {
                        return match (true) {
                            str_contains($stage->name, 'Donor') => '#22c55e', // success
                            str_contains($stage->name, 'Gala') => '#3b82f6', // info
                            str_contains($stage->name, 'Neighbor') => '#f59e0b', // warning
                            str_contains($stage->name, 'Mom') => '#ef4444', // danger
                            default => '#6b7280', // gray
                        };
                    })->toArray(),
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
