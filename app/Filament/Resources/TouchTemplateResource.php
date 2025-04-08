<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TouchTemplateResource\Pages;
use App\Models\Contact;
use App\Models\TouchTemplate;
use App\Models\TouchTemplateBlock;
use App\Models\TouchTemplateLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TouchTemplateResource extends Resource
{
    protected static ?string $model = TouchTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Templates';
    protected static ?int $navigationSort = 71;

    public static function form(Form $form): Form
    {
        $mergeFieldsHelp = collect(TouchTemplate::availableMergeFields())
            ->map(fn ($label, $field) => "{{$field}} - $label")
            ->join("\n");

        $blocks = TouchTemplateBlock::where('is_active', true)->get();
        $blockFields = [];
        
        foreach ($blocks as $block) {
            // Extract variables from block content using regex
            preg_match_all('/\{\{\s*block\.([\w]+)\s*\}\}/', $block->html_content, $matches);
            $variables = $matches[1] ?? [];
            
            if (!empty($variables)) {
                $variableFields = [];
                foreach ($variables as $variable) {
                    $variableFields[] = Forms\Components\TextInput::make("block_content.{$block->slug}.{$variable}")
                        ->label(ucwords(str_replace('_', ' ', $variable)));
                }
                
                $blockFields[] = Forms\Components\Section::make(ucwords($block->name) . ' Variables')
                    ->schema($variableFields)
                    ->columns(2)
                    ->collapsible()
                    ->collapsed();
            }
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Supports merge fields'),
                        Forms\Components\Select::make('target_lifecycle_stages')
                            ->multiple()
                            ->required()
                            ->options(Contact::LIFECYCLE_STAGES)
                            ->searchable(),
                        Forms\Components\Select::make('layout_id')
                            ->label('Layout')
                            ->relationship('layout', 'name')
                            ->options(TouchTemplateLayout::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Email Content')
                    ->schema([
                        Forms\Components\RichEditor::make('html_content')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->helperText("Available merge fields:\n" . $mergeFieldsHelp),
                        Forms\Components\Textarea::make('text_content')
                            ->helperText('Optional plain text version. If not provided, will be auto-generated from HTML')
                            ->columnSpanFull(),
                    ]),

                ...$blockFields,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('layout.name')
                    ->label('Layout'),
                Tables\Columns\TextColumn::make('target_lifecycle_stages')
                    ->listWithLineBreaks()
                    ->formatStateUsing(fn ($state) => collect($state)->map(fn ($stage) => Contact::LIFECYCLE_STAGES[$stage] ?? $stage)),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('target_lifecycle_stages')
                    ->multiple()
                    ->options(Contact::LIFECYCLE_STAGES),
                Tables\Filters\SelectFilter::make('layout')
                    ->relationship('layout', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('test')
                    ->label('Test Template')
                    ->icon('heroicon-o-beaker')
                    ->url(fn (TouchTemplate $record): string => static::getUrl('test', ['record' => $record])),
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
            'index' => Pages\ListTouchTemplates::route('/'),
            'create' => Pages\CreateTouchTemplate::route('/create'),
            'edit' => Pages\EditTouchTemplate::route('/{record}/edit'),
            'test' => Pages\ViewTestTouchTemplate::route('/{record}/test'),
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
