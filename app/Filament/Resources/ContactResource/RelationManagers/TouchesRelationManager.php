<?php

namespace App\Filament\Resources\ContactResource\RelationManagers;

use App\Models\Touch;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TouchesRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowTouches';
    protected static ?string $title = 'Touch History';
    protected static ?string $icon = 'heroicon-m-hand-raised';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->wrap()
                    ->limit(100),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('executed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Touch::TYPE_EMAIL => 'Email',
                        Touch::TYPE_SMS => 'SMS',
                        Touch::TYPE_CALL => 'Call',
                        Touch::TYPE_LETTER => 'Letter',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Touch::STATUS_PENDING => 'Pending',
                        Touch::STATUS_SCHEDULED => 'Scheduled',
                        Touch::STATUS_SENT => 'Sent',
                        Touch::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form(fn (Touch $record): array => [
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('type')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('subject')
                                    ->visible(fn ($record) => !empty($record->subject)),
                                Infolists\Components\TextEntry::make('content')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('metadata')
                                    ->label('Details')
                                    ->listWithLineBreaks()
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('error')
                                    ->color('danger')
                                    ->visible(fn ($record) => !empty($record->error))
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('scheduled_for')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('executed_at')
                                    ->dateTime(),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->bulkActions([
                // No bulk actions needed
            ]);
    }
}
