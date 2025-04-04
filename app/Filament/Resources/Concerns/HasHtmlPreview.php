<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;

trait HasHtmlPreview
{
    public static function getPreviewSection(string $field = 'html_content'): Section
    {
        return Section::make('Content')
            ->schema([
                RichEditor::make($field)
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
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->afterStateUpdated(function ($state, ViewField $component) {
                        $component->state($state);
                    })
                    ->columnSpanFull(),

                ViewField::make('preview')
                    ->view('filament.components.html-preview')
                    ->columnSpanFull()
                    ->hidden(fn ($state) => empty($state))
                    ->extraAttributes(['class' => 'border rounded-lg p-4']),
            ])
            ->collapsible()
            ->persistCollapsed(false);
    }

    protected static function getPreviewAction(): Action
    {
        return Action::make('preview')
            ->icon('heroicon-m-eye')
            ->modalHeading('Preview')
            ->modalContent(fn ($state) => view(
                'filament.components.html-preview',
                ['state' => $state['html_content'] ?? '']
            ))
            ->modalWidth('4xl')
            ->modalFooterActions([])
            ->extraModalFooterActions(fn () => [])
            ->action(function () {
                // Modal will be shown automatically
            });
    }
}
