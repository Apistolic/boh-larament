<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowCompletionTriggerResource\Pages;
use App\Models\WorkflowCompletionTrigger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkflowCompletionTriggerResource extends Resource
{
    protected static ?string $model = WorkflowCompletionTrigger::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Workflow Management';
    protected static ?int $navigationSort = 30;
    protected static ?string $modelLabel = 'Completion Trigger';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Trigger Details')
                    ->schema([
                        Forms\Components\Select::make('workflow_id')
                            ->relationship('workflow', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('trigger_type')
                            ->options([
                                'manual' => 'Manual',
                                'contact_updated' => 'Contact Updated',
                                'lifecycle_stage_changed' => 'Lifecycle Stage Changed',
                                'donation_received' => 'Donation Received',
                                'time_elapsed' => 'Time Elapsed',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\KeyValue::make('criteria')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->addButtonLabel('Add Criteria')
                            ->reorderable(false),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trigger_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('workflow')
                    ->relationship('workflow', 'name'),
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->options([
                        'manual' => 'Manual',
                        'contact_updated' => 'Contact Updated',
                        'lifecycle_stage_changed' => 'Lifecycle Stage Changed',
                        'donation_received' => 'Donation Received',
                        'time_elapsed' => 'Time Elapsed',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListWorkflowCompletionTriggers::route('/'),
            'create' => Pages\CreateWorkflowCompletionTrigger::route('/create'),
            'edit' => Pages\EditWorkflowCompletionTrigger::route('/{record}/edit'),
        ];
    }
}
