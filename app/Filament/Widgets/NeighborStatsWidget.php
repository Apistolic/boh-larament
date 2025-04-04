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
            Stat::make('Active Neighbors', Contact::where(function ($query) {
                    $query->where('lifecycle_stage', 'like', '%neighbor_active%')
                        ->orWhere('lifecycle_stage', 'like', '%neighbor_leader%')
                        ->orWhere('lifecycle_stage', 'like', '%neighbor_influencer%');
                })
                ->count())
                ->description('Active neighboring volunteers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['neighbor_active', 'neighbor_leader', 'neighbor_influencer'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Gala Signups', Contact::where('lifecycle_stage', 'like', '%gala_neighbor_signup%')
                ->count())
                ->description('Gala neighbor signups')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['gala_neighbor_signup'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Neighbor Pipeline', Contact::where('lifecycle_stage', 'like', '%neighbor_candidate%')
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
            
            Stat::make('Retired Neighbors', Contact::where('lifecycle_stage', 'like', '%neighbor_retired%')
                ->count())
                ->description('Past neighbors')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('gray')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['neighbor_retired'],
                            ],
                        ],
                    ])
                ),
        ];
    }
}
