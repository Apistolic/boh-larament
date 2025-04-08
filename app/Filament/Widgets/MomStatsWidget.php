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
            Stat::make('Active Moms', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Mom%')
                        ->where('name', 'not like', '%Candidate%')
                        ->whereNull('contact_lifecycle.ended_at');
                })
                ->count())
                ->description('Moms in the system')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('danger')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['mom_active'],
                            ],
                        ],
                    ])
                ),

            Stat::make('Mom Candidates', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Mom%Candidate%')
                        ->whereNull('contact_lifecycle.ended_at');
                })
                ->count())
                ->description('Potential moms')
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
        ];
    }
}
