<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasHtmlPreview;
use App\Filament\Resources\TouchTemplateBlockResource\Pages;
use App\Models\TouchTemplateBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class TouchTemplateBlockResource extends Resource
{
    use HasHtmlPreview;

    protected static ?string $model = TouchTemplateBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 83;

    public static function form(Form $form): Form
    {
        $availableBlocks = static::getAvailableBlocks();

        return $form
            ->schema([
                Forms\Components\Section::make('Block Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) return;
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->helperText('Auto-generated from name if left empty')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])->columns(2),

                static::getPreviewSection('html_content')
                    ->extraAttributes(['class' => 'mt-4'])
                    ->description('Use {{ block.variable_name }} for variables. You can also include other blocks using {{ block.block_slug }}'),

                Forms\Components\Section::make('Plain Text Version')
                    ->schema([
                        Forms\Components\Textarea::make('text_content')
                            ->helperText('Optional plain text version. If not provided, will be auto-generated from HTML')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Available Blocks')
                    ->schema([
                        Forms\Components\Placeholder::make('blocks_hint')
                            ->content('Blocks that can be included in this template:')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make()
                            ->schema($availableBlocks->map(fn ($block) => 
                                Forms\Components\Placeholder::make("block_{$block->slug}")
                                    ->label($block->name)
                                    ->content("{{ block.{$block->slug} }}")
                                    ->helperText(
                                        $block->getAvailableVariables()->map(
                                            fn ($var) => "{{ block.$var }}"
                                        )->join(', ')
                                    )
                            )->toArray())
                            ->columns(2),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn (TouchTemplateBlock $record): string => "Preview: {$record->name}")
                    ->modalContent(fn (TouchTemplateBlock $record) => view(
                        'filament.components.html-preview',
                        [
                            'state' => $record->html_content,
                            'variables' => $record->getAvailableVariables(),
                        ]
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
            'index' => Pages\ListTouchTemplateBlocks::route('/'),
            'create' => Pages\CreateTouchTemplateBlock::route('/create'),
            'edit' => Pages\EditTouchTemplateBlock::route('/{record}/edit'),
        ];
    }

    protected static function getAvailableBlocks(): Collection
    {
        return TouchTemplateBlock::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
