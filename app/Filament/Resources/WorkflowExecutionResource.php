<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowExecutionResource\Pages;
use App\Filament\Resources\WorkflowExecutionResource\RelationManagers;
use App\Models\WorkflowExecution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkflowExecutionResource extends BaseResource
{
    protected static ?string $model = WorkflowExecution::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';
    protected static ?string $navigationGroup = 'Engagement';
    protected static ?string $navigationLabel = 'Workflow Executions';
    protected static ?int $navigationSort = 92;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Workflow Execution Details')
                    ->schema([
                        Forms\Components\Select::make('workflow_id')
                            ->relationship('workflow', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'full_name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                WorkflowExecution::STATUS_PENDING => 'Pending',
                                WorkflowExecution::STATUS_IN_PROGRESS => 'In Progress',
                                WorkflowExecution::STATUS_COMPLETED => 'Completed',
                                WorkflowExecution::STATUS_FAILED => 'Failed',
                            ])
                            ->required(),
                    ]),
                Forms\Components\Section::make('Execution Data')
                    ->schema([
                        Forms\Components\KeyValue::make('trigger_snapshot')
                            ->label('Trigger Data')
                            ->disabled(),
                        Forms\Components\KeyValue::make('results')
                            ->label('Results')
                            ->disabled(),
                        Forms\Components\Textarea::make('error')
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->status === WorkflowExecution::STATUS_FAILED),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.full_name')
                    ->label('Contact')
                    ->searchable(['contacts.first_name', 'contacts.last_name'])
                    ->sortable(['contacts.first_name', 'contacts.last_name']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => WorkflowExecution::STATUS_PENDING,
                        'primary' => WorkflowExecution::STATUS_IN_PROGRESS,
                        'success' => WorkflowExecution::STATUS_COMPLETED,
                        'danger' => WorkflowExecution::STATUS_FAILED,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        WorkflowExecution::STATUS_PENDING => 'Pending',
                        WorkflowExecution::STATUS_IN_PROGRESS => 'In Progress',
                        WorkflowExecution::STATUS_COMPLETED => 'Completed',
                        WorkflowExecution::STATUS_FAILED => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('workflow')
                    ->relationship('workflow', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed for executions
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActionExecutionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflowExecutions::route('/'),
            'view' => Pages\ViewWorkflowExecution::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['workflow', 'contact'])
            ->latest();
    }

}
