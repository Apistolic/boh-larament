<?php

namespace App\Filament\Resources\EmailSendResource\Pages;

use App\Filament\Resources\EmailSendResource;
use App\Models\EmailOpen;
use App\Models\EmailClick;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Collection;

class ViewEmailSend extends ViewRecord
{
    protected static string $resource = EmailSendResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Email Details')
                    ->schema([
                        TextEntry::make('contact.name')
                            ->label('Recipient'),
                        TextEntry::make('subject'),
                        TextEntry::make('sent_at')
                            ->dateTime(),
                        TextEntry::make('content')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Section::make('Email Opens')
                    ->schema([
                        RepeatableEntry::make('opens')
                            ->schema([
                                TextEntry::make('opened_at')
                                    ->dateTime(),
                                TextEntry::make('email_client'),
                                TextEntry::make('device_type'),
                                TextEntry::make('country'),
                                TextEntry::make('city'),
                            ])
                            ->grid(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Link Clicks')
                    ->schema([
                        RepeatableEntry::make('clicks')
                            ->schema([
                                TextEntry::make('link_url')
                                    ->url(),
                                TextEntry::make('clicked_at')
                                    ->dateTime(),
                                TextEntry::make('device_type'),
                                TextEntry::make('country'),
                                TextEntry::make('city'),
                            ])
                            ->grid(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Analytics')
                    ->schema([
                        TextEntry::make('opens_count')
                            ->state(fn ($record) => $record->opens()->count())
                            ->label('Total Opens'),
                        TextEntry::make('unique_opens')
                            ->state(fn ($record) => $record->opens()->distinct('ip_address')->count())
                            ->label('Unique Opens'),
                        TextEntry::make('clicks_count')
                            ->state(fn ($record) => $record->clicks()->count())
                            ->label('Total Clicks'),
                        TextEntry::make('unique_clicks')
                            ->state(fn ($record) => $record->clicks()->distinct('ip_address')->count())
                            ->label('Unique Clicks'),
                        TextEntry::make('popular_clients')
                            ->state(function ($record) {
                                return $record->opens()
                                    ->select('email_client')
                                    ->selectRaw('COUNT(*) as count')
                                    ->whereNotNull('email_client')
                                    ->groupBy('email_client')
                                    ->orderByDesc('count')
                                    ->limit(3)
                                    ->get()
                                    ->map(fn ($item) => "{$item->email_client} ({$item->count})")
                                    ->implode(', ');
                            })
                            ->label('Popular Email Clients'),
                        TextEntry::make('popular_locations')
                            ->state(function ($record) {
                                return $record->opens()
                                    ->select('country')
                                    ->selectRaw('COUNT(*) as count')
                                    ->whereNotNull('country')
                                    ->groupBy('country')
                                    ->orderByDesc('count')
                                    ->limit(3)
                                    ->get()
                                    ->map(fn ($item) => "{$item->country} ({$item->count})")
                                    ->implode(', ');
                            })
                            ->label('Popular Locations'),
                    ])
                    ->columns(3),
            ]);
    }
}
