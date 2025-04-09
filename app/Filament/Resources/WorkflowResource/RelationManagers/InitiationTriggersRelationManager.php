<?php

namespace App\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InitiationTriggersRelationManager extends RelationManager
{
    protected static string $relationship = 'initiationTriggers';
    protected static ?string $title = 'Initiation Triggers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('trigger_type')
                    ->options([
                        'manual' => 'Manual',
                        'contact_created' => 'Contact Created',
                        'contact_updated' => 'Contact Updated',
                        'lifecycle_stage_changed' => 'Lifecycle Stage Changed',
                        'donation_received' => 'Donation Received',
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ])
            ->filters([
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ->defaultSort('sort_order');
    }
}
