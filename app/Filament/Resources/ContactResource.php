<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use App\Models\LifecycleStage;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class ContactResource extends BaseResource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Engagement';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mobile_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->helperText('Primary contact number'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->helperText('Secondary/alternate number'),
                    ])->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street_2')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('state_code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(255)
                            ->default('USA'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('lifecycle_stages')
                            ->relationship('lifecycleStages', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\Select::make('lifecycle_category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\ColorPicker::make('color')
                                    ->required(),
                            ])
                            ->optionsLimit(100)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('source')
                            ->options([
                                'website' => 'Website',
                                'referral' => 'Referral',
                                'event' => 'Event',
                                'social_media' => 'Social Media',
                                'direct' => 'Direct Contact',
                            ]),
                        Forms\Components\DateTimePicker::make('last_touched_at'),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('formatted_mobile_phone')
                    ->label('Mobile')
                    ->searchable(query: function ($query, $search) {
                        return $query->where('mobile_phone', 'like', '%' . preg_replace('/[^0-9]/', '', $search) . '%');
                    }),
                Tables\Columns\TextColumn::make('formatted_phone')
                    ->label('Other Phone')
                    ->searchable(query: function ($query, $search) {
                        return $query->where('phone', 'like', '%' . preg_replace('/[^0-9]/', '', $search) . '%');
                    }),
                Tables\Columns\TagsColumn::make('lifecycleStages')
                    ->getStateUsing(fn ($record) => $record->lifecycleStages->pluck('name'))
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        str_contains($state, 'Donor') => 'success',
                        str_contains($state, 'Gala') => 'info',
                        str_contains($state, 'Neighbor') => 'warning',
                        str_contains($state, 'Mom') => 'danger',
                        default => 'gray'
                    })
                    ->searchable(['lifecycle_stages.name', 'lifecycle_categories.name'])
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_touched_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lifecycle_stage')
                    ->relationship('lifecycleStages', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('donor')
                    ->query(fn (Builder $query): Builder => $query->whereHas('lifecycleStages', fn ($q) => $q->where('name', 'like', 'Donor%')->where('name', 'not like', '%Candidate%'))),
                Tables\Filters\Filter::make('donor_candidate')
                    ->query(fn (Builder $query): Builder => $query->whereHas('lifecycleStages', fn ($q) => $q->where('name', 'like', 'Donor Candidate%'))),
                Tables\Filters\Filter::make('neighbor')
                    ->query(fn (Builder $query): Builder => $query->whereHas('lifecycleStages', fn ($q) => $q->where('name', 'like', 'Neighbor%')->where('name', 'not like', '%Candidate%'))),
                Tables\Filters\Filter::make('mom')
                    ->query(fn (Builder $query): Builder => $query->whereHas('lifecycleStages', fn ($q) => $q->where('name', 'like', 'Mom%')->where('name', 'not like', '%Candidate%'))),
                Tables\Filters\Filter::make('candidate')
                    ->query(fn (Builder $query): Builder => $query->whereHas('lifecycleStages', fn ($q) => $q->where('name', 'like', '%Candidate%'))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('addLifecycleStages')
                        ->label('Add Lifecycle Stage')
                        ->icon('heroicon-m-plus')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('lifecycle_stages')
                                ->label('Lifecycle Stages')
                                ->relationship('lifecycleStages', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->required()
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $stages = LifecycleStage::whereIn('id', $data['lifecycle_stages'])->get();
                            $records->each(function (Contact $contact) use ($stages) {
                                $contact->lifecycleStages()->syncWithoutDetaching($stages);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('removeLifecycleStages')
                        ->label('Remove Lifecycle Stage')
                        ->icon('heroicon-m-minus')
                        ->color('danger')
                        ->form([
                            Forms\Components\Select::make('lifecycle_stages')
                                ->label('Lifecycle Stages')
                                ->relationship('lifecycleStages', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->required()
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $stages = LifecycleStage::whereIn('id', $data['lifecycle_stages'])->get();
                            $records->each(function (Contact $contact) use ($stages) {
                                $contact->lifecycleStages()->detach($stages);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Name'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('formatted_mobile_phone')
                            ->label('Mobile'),
                        Infolists\Components\TextEntry::make('formatted_phone')
                            ->label('Phone'),
                    ])->columns(2),

                Infolists\Components\Section::make('Address')
                    ->schema([
                        Infolists\Components\TextEntry::make('street'),
                        Infolists\Components\TextEntry::make('street_2'),
                        Infolists\Components\TextEntry::make('city'),
                        Infolists\Components\TextEntry::make('state_code'),
                        Infolists\Components\TextEntry::make('postal_code'),
                        Infolists\Components\TextEntry::make('country'),
                    ])->columns(2),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\TagsEntry::make('lifecycleStages')
                            ->label('Lifecycle Stages')
                            ->getStateUsing(fn ($record) => $record->lifecycleStages->pluck('name'))
                            ->badge()
                            ->color(fn ($state) => match (true) {
                                str_contains($state, 'Donor') => 'success',
                                str_contains($state, 'Gala') => 'info',
                                str_contains($state, 'Neighbor') => 'warning',
                                str_contains($state, 'Mom') => 'danger',
                                default => 'gray'
                            }),
                        Infolists\Components\TextEntry::make('source')
                            ->badge(),
                        Infolists\Components\TextEntry::make('last_touched_at')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TouchesRelationManager::class,
            RelationManagers\WorkflowExecutionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
            'view' => Pages\ViewContact::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['lifecycleStages', 'lifecycleStages.category'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

}
