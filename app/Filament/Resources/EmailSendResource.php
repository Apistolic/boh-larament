<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSendResource\Pages;
use App\Models\EmailSend;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmailSendResource extends Resource
{
    protected static ?string $model = EmailSend::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Touches';
    protected static ?int $navigationSort = 95;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opens_count')
                    ->counts('opens')
                    ->label('Opens')
                    ->sortable(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->counts('clicks')
                    ->label('Clicks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_open.opened_at')
                    ->label('First Opened')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_open.opened_at')
                    ->label('Last Opened')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('sent_today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('sent_at', now())),
                Tables\Filters\Filter::make('opened')
                    ->query(fn (Builder $query): Builder => $query->has('opens')),
                Tables\Filters\Filter::make('clicked')
                    ->query(fn (Builder $query): Builder => $query->has('clicks')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailSends::route('/'),
            'view' => Pages\ViewEmailSend::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
