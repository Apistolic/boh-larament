<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\LifecycleStage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\ContactResource;

class DonorStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Active Donors', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Donor%')
                        ->whereNull('contact_lifecycle.ended_at');
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
            
            Stat::make('Gala Engagement', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Gala%')
                        ->whereNull('contact_lifecycle.ended_at');
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

            Stat::make('Donor Candidates', Contact::whereHas('lifecycleStages', function ($query) {
                    $query->where('name', 'like', '%Donor%Candidate%')
                        ->whereNull('contact_lifecycle.ended_at');
                })
                ->count())
                ->description('Potential donors in pipeline')
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
        ];
    }
}
