<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowResource\Pages;
use App\Filament\Resources\WorkflowResource\RelationManagers;
use App\Models\Workflow;
use App\Models\WorkflowType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\View;

class WorkflowResource extends BaseResource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Engagement';
    protected static ?int $navigationSort = 81;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('workflow_type_id')
                            ->label('Type')
                            ->required()
                            ->options(WorkflowType::pluck('name', 'id')),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Trigger Configuration')
                    ->schema([
                        Forms\Components\Select::make('trigger_type')
                            ->options([
                                'manual' => 'Manual',
                                'contact_created' => 'Contact Created',
                                'contact_updated' => 'Contact Updated',
                                'lifecycle_stage_changed' => 'Lifecycle Stage Changed',
                                'donation_received' => 'Donation Received',
                            ])
                            ->required()
                            ->native(false)
                            ->reactive(),
                        Forms\Components\KeyValue::make('trigger_criteria')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->addButtonLabel('Add Condition')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Actions')
                    ->schema([
                        Forms\Components\KeyValue::make('actions')
                            ->keyLabel('Action')
                            ->valueLabel('Parameters')
                            ->addButtonLabel('Add Action')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Sequence Diagram')
                    ->schema([
                        Forms\Components\Textarea::make('sequence_diagram')
                            ->label('Mermaid Sequence Diagram')
                            ->helperText('Enter Mermaid sequence diagram code here')
                            ->columnSpanFull(),
                        View::make('components.mermaid-diagram')
                            ->viewData(['content' => fn ($get) => $get('sequence_diagram')])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workflowType.name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (explode('_', $state)[0]) {
                        'new' => 'warning',
                        'gala' => 'info',
                        default => 'success',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trigger_type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('execution_count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_executed_at')
                    ->dateTime()
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
                Tables\Filters\SelectFilter::make('workflow_type_id')
                    ->label('Type')
                    ->options(WorkflowType::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->options([
                        'manual' => 'Manual',
                        'contact_created' => 'Contact Created',
                        'contact_updated' => 'Contact Updated',
                        'lifecycle_stage_changed' => 'Lifecycle Stage Changed',
                        'donation_received' => 'Donation Received',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('execute')
                    ->icon('heroicon-m-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Workflow $record) => $record->is_active && $record->trigger_type === 'manual'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InitiationTriggersRelationManager::class,
            RelationManagers\CompletionTriggersRelationManager::class,
            RelationManagers\ExecutionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflows::route('/'),
            'create' => Pages\CreateWorkflow::route('/create'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
            'view' => Pages\ViewWorkflow::route('/{record}'),
        ];
    }
}
