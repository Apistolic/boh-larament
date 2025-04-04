<?php

namespace App\Filament\Resources\WorkflowExecutionResource\RelationManagers;

use App\Models\WorkflowActionExecution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActionExecutionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actionExecutions';

    protected static ?string $recordTitleAttribute = 'action';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Action Details')
                    ->schema([
                        Forms\Components\TextInput::make('action')
                            ->required()
                            ->readonly(),
                        Forms\Components\Select::make('status')
                            ->options([
                                WorkflowActionExecution::STATUS_PENDING => 'Pending',
                                WorkflowActionExecution::STATUS_IN_PROGRESS => 'In Progress',
                                WorkflowActionExecution::STATUS_COMPLETED => 'Completed',
                                WorkflowActionExecution::STATUS_FAILED => 'Failed',
                            ])
                            ->required()
                            ->readonly(),
                    ]),
                Forms\Components\Section::make('Action Data')
                    ->schema([
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Parameters')
                            ->readonly(),
                        Forms\Components\KeyValue::make('result')
                            ->label('Result')
                            ->readonly()
                            ->visible(fn ($record) => $record?->status === WorkflowActionExecution::STATUS_COMPLETED),
                        Forms\Components\Textarea::make('error')
                            ->readonly()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->status === WorkflowActionExecution::STATUS_FAILED),
                    ]),
                Forms\Components\Section::make('Timing')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->readonly(),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->readonly(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => WorkflowActionExecution::STATUS_PENDING,
                        'primary' => WorkflowActionExecution::STATUS_IN_PROGRESS,
                        'success' => WorkflowActionExecution::STATUS_COMPLETED,
                        'danger' => WorkflowActionExecution::STATUS_FAILED,
                    ]),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        WorkflowActionExecution::STATUS_PENDING => 'Pending',
                        WorkflowActionExecution::STATUS_IN_PROGRESS => 'In Progress',
                        WorkflowActionExecution::STATUS_COMPLETED => 'Completed',
                        WorkflowActionExecution::STATUS_FAILED => 'Failed',
                    ]),
            ])
            ->headerActions([
                // No create action needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
            ]);
    }
}
