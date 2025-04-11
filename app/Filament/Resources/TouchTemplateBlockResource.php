<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Concerns\HasHtmlPreview;
use App\Filament\Resources\TouchTemplateBlockResource\Pages;
use App\Models\TouchTemplateBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TouchTemplateBlockResource extends BaseResource
{
    use HasHtmlPreview;

    protected static ?string $model = TouchTemplateBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 62;

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
                    ->description('Use {{ block.variable_name }} for variables. You can also include other blocks using {{ block.block_slug }}')
                    ->schema([
                        Forms\Components\Tabs::make('content_tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('HTML Code')
                                    ->schema([
                                        Forms\Components\Textarea::make('html_content')
                                            ->label('HTML Content')
                                            ->required()
                                            ->rows(20)
                                            ->default(fn ($record) => $record?->formatted_html)
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $set('preview_content', $state);
                                            })
                                            ->columnSpanFull()
                                            ->extraAttributes(['class' => 'font-mono']),

                                        Forms\Components\Hidden::make('preview_content')
                                            ->default(fn ($record) => $record?->html_content),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Display Preview')
                                    ->schema([
                                        Forms\Components\Placeholder::make('preview')
                                            ->label('Preview')
                                            ->content(fn (Forms\Get $get) => new \Illuminate\Support\HtmlString($get('html_content')))
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->persistTabInQueryString(),

                        Forms\Components\ViewField::make('image_list')
                            ->view('filament.components.html-image-list')
                            ->columnSpanFull()
                            ->dehydrated(false),
                    ]),

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
}
