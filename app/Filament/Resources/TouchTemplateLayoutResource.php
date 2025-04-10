<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasHtmlPreview;
use App\Filament\Resources\TouchTemplateLayoutResource\Pages;
use App\Models\TouchTemplateLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class TouchTemplateLayoutResource extends BaseResource
{
    use HasHtmlPreview;

    protected static ?string $model = TouchTemplateLayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 63;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Layout Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->helperText('Leave empty to auto-generate from name')
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options(TouchTemplateLayout::getTypes())
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),

                static::getPreviewSection('html_content')
                    ->extraAttributes(['class' => 'mt-4'])
                    ->description('Use {{ block.name }} to include blocks'),

                Forms\Components\Section::make('Plain Text Version')
                    ->schema([
                        Forms\Components\Textarea::make('text_content')
                            ->helperText('Optional plain text version. If not provided, will be auto-generated from HTML')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Block Usage Guide')
                    ->schema([
                        Forms\Components\Placeholder::make('blocks_hint')
                            ->content('Available blocks that can be included:')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make()
                            ->schema(array_map(
                                fn ($block) => Forms\Components\Placeholder::make("block_{$block->slug}")
                                    ->label($block->name)
                                    ->content("{{ block.{$block->slug} }}")
                                    ->helperText($block->description ?? 'No description available'),
                                \App\Models\TouchTemplateBlock::where('is_active', true)->get()->all()
                            ))
                            ->columns(2),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(TouchTemplateLayout::getTypes()),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Layout Preview')
                    ->modalContent(fn (TouchTemplateLayout $record) => view(
                        'filament.components.html-preview',
                        ['state' => $record->html_content]
                    ))
                    ->modalWidth('4xl'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTouchTemplateLayouts::route('/'),
            'create' => Pages\CreateTouchTemplateLayout::route('/create'),
            'edit' => Pages\EditTouchTemplateLayout::route('/{record}/edit'),
        ];
    }
}
