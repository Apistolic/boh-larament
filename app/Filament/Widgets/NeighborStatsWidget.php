<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ContactResource;

class NeighborStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Active Neighbors', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Neighbor%')
                        ->where('name', 'not like', '%Candidate%')
                        ->whereNull('contact_lifecycle.ended_at');
                })
                ->count())
                ->description('Active neighbors in the system')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['neighbor_active'],
                            ],
                        ],
                    ])
                ),

            Stat::make('Neighbor Candidates', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Neighbor%Candidate%')
                        ->whereNull('contact_lifecycle.ended_at');
                })
                ->count())
                ->description('Potential neighbors')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['neighbor_candidate'],
                            ],
                        ],
                    ])
                ),
        ];
    }
}
