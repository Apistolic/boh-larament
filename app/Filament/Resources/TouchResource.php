<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TouchResource\Pages;
use App\Models\Touch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class TouchResource extends BaseResource
{
    protected static ?string $model = Touch::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $navigationGroup = 'Engagement';
    protected static ?int $navigationSort = 93;

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::count();
        $pending = static::getModel()::whereIn('status', [Touch::STATUS_PENDING, Touch::STATUS_SCHEDULED])->count();
        return $total . ' (' . $pending . ' pending)';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::whereIn('status', [Touch::STATUS_PENDING, Touch::STATUS_SCHEDULED])->exists()
            ? 'warning'
            : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'first_name', fn ($query) => $query->orderBy('first_name'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['first_name', 'last_name', 'email'])
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Select::make('workflow_execution_id')
                            ->relationship('workflowExecution', 'id')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                Touch::TYPE_EMAIL => 'Email',
                                Touch::TYPE_SMS => 'SMS',
                                Touch::TYPE_CALL => 'Call',
                                Touch::TYPE_LETTER => 'Letter',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                Touch::STATUS_PENDING => 'Pending',
                                Touch::STATUS_SCHEDULED => 'Scheduled',
                                Touch::STATUS_SENT => 'Sent',
                                Touch::STATUS_FAILED => 'Failed',
                                Touch::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Select::make('template_id')
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($record) => $record?->type === Touch::TYPE_EMAIL),
                        Forms\Components\Tabs::make('Content')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Raw Content')
                                    ->schema([
                                        Forms\Components\Textarea::make('content')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Preview')
                                    ->schema([
                                        Forms\Components\View::make('filament.forms.components.html-preview')
                                            ->view('filament.forms.components.html-preview')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('scheduled_for'),
                        Forms\Components\DateTimePicker::make('executed_at')
                            ->disabled(),
                        Forms\Components\Textarea::make('error')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('contact.full_name')
                    ->label('Contact')
                    ->description(fn ($record) => $record->contact?->email)
                    ->searchable(['contacts.first_name', 'contacts.last_name', 'contacts.email'])
                    ->sortable(['contacts.first_name', 'contacts.last_name'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('content')
                    ->html()
                    ->wrap()
                    ->toggleable()
                    ->label('Content Preview'),
                Tables\Columns\TextColumn::make('scheduled_for')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('executed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        Touch::STATUS_CANCELLED => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('scheduled')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('scheduled_for')),
                Tables\Filters\Filter::make('executed')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('executed_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTouches::route('/'),
            'create' => Pages\CreateTouch::route('/create'),
            'edit' => Pages\EditTouch::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['contact.name', 'contact.email', 'subject', 'content'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['contact', 'workflowExecution', 'template.layout']);
    }
}
