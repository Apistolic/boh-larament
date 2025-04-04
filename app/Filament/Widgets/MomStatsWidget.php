<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ContactResource;

class MomStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Active Moms', Contact::where('lifecycle_stage', 'like', '%mom_active%')
                ->count())
                ->description('Currently active moms')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['mom_active'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Mom Pipeline', Contact::where('lifecycle_stage', 'like', '%mom_candidate%')
                ->count())
                ->description('Potential program participants')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['mom_candidate'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Program Graduates', Contact::where('lifecycle_stage', 'like', '%mom_graduate%')
                ->count())
                ->description('Program graduates')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')  // Changed to success since graduation is a positive outcome
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['mom_graduate'],
                            ],
                        ],
                    ])
                ),
        ];
    }
}
