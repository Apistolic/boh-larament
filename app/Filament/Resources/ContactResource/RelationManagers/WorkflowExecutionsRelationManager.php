<?php

namespace App\Filament\Resources\ContactResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class WorkflowExecutionsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowExecutions';
    protected static ?string $title = 'Workflow History';
    protected static ?string $icon = 'heroicon-m-play';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => route('filament.admin.resources.workflow-executions.view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'in_progress' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actionExecutions.status')
                    ->label('Actions')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'in_progress' => 'warning',
                        default => 'gray',
                    })
                    ->listWithLineBreaks(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Workflow Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('workflow.name')
                            ->label('Workflow'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'failed' => 'danger',
                                'in_progress' => 'warning',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Started')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ]),
                
                Infolists\Components\Section::make('Actions')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('actionExecutions')
                            ->schema([
                                Infolists\Components\TextEntry::make('action')
                                    ->label('Action Type'),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'in_progress' => 'warning',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('started_at')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('completed_at')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('result')
                                    ->label('Results')
                                    ->listWithLineBreaks(),
                                Infolists\Components\TextEntry::make('error')
                                    ->color('danger'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
