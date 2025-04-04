<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ContactResource;

class DonorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Active Donors', Contact::where(function ($query) {
                    $query->where('lifecycle_stage', 'like', '%donor_active%')
                        ->orWhere('lifecycle_stage', 'like', '%donor_influencer%')
                        ->orWhere('lifecycle_stage', 'like', '%donor_aggregator%');
                })
                ->count())
                ->description('Active donors in the system')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['donor_active', 'donor_influencer', 'donor_aggregator'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Gala Engagement', Contact::where(function ($query) {
                    $query->where('lifecycle_stage', 'like', '%gala_attendee%')
                        ->orWhere('lifecycle_stage', 'like', '%gala_donor%')
                        ->orWhere('lifecycle_stage', 'like', '%gala_candidate%');
                })
                ->count())
                ->description('Gala participants and prospects')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['gala_attendee', 'gala_donor', 'gala_candidate'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Donor Pipeline', Contact::where('lifecycle_stage', 'like', '%donor_candidate%')
                ->count())
                ->description('Potential donors')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['donor_candidate'],
                            ],
                        ],
                    ])
                ),
            
            Stat::make('Retired Donors', Contact::where('lifecycle_stage', 'like', '%donor_retired%')
                ->count())
                ->description('Past donors')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('gray')
                ->url(
                    ContactResource::getUrl('index', [
                        'tableFilters' => [
                            'lifecycle_stage' => [
                                'values' => ['donor_retired'],
                            ],
                        ],
                    ])
                ),
        ];
    }
}
