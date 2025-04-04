<?php

namespace App\Filament\Resources\WorkflowResource\RelationManagers;

use App\Models\WorkflowExecution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ExecutionsRelationManager extends RelationManager
{
    protected static string $relationship = 'executions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Execution Details')
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->relationship('contact', 'name')
                            ->required()
                            ->searchable()
                            ->readonly(),
                        Forms\Components\Select::make('status')
                            ->options([
                                WorkflowExecution::STATUS_PENDING => 'Pending',
                                WorkflowExecution::STATUS_IN_PROGRESS => 'In Progress',
                                WorkflowExecution::STATUS_COMPLETED => 'Completed',
                                WorkflowExecution::STATUS_FAILED => 'Failed',
                            ])
                            ->required()
                            ->readonly(),
                    ]),
                Forms\Components\Section::make('Execution Data')
                    ->schema([
                        Forms\Components\KeyValue::make('trigger_snapshot')
                            ->label('Trigger Data')
                            ->readonly(),
                        Forms\Components\KeyValue::make('results')
                            ->label('Results')
                            ->readonly(),
                        Forms\Components\Textarea::make('error')
                            ->readonly()
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record?->status === WorkflowExecution::STATUS_FAILED),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Contact')
                    ->searchable()
                    ->sortable(),
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        WorkflowExecution::STATUS_PENDING => 'Pending',
                        WorkflowExecution::STATUS_IN_PROGRESS => 'In Progress',
                        WorkflowExecution::STATUS_COMPLETED => 'Completed',
                        WorkflowExecution::STATUS_FAILED => 'Failed',
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
