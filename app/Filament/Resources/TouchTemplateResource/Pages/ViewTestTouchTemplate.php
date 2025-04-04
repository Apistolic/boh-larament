<?php

namespace App\Filament\Resources\TouchTemplateResource\Pages;

use App\Filament\Resources\TouchTemplateResource;
use App\Models\Contact;
use App\Models\TouchTemplate;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;

class ViewTestTemplate extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TouchTemplateResource::class;

    protected static string $view = 'filament.resources.email-template-resource.pages.view-test-template';

    public ?array $data = [];
    public ?string $record = null;
    
    public $selectedContactId = null;
    public $previewHtml = null;
    public $previewText = null;
    public $previewSubject = null;

    public function mount($record): void
    {
        $this->record = $record;
        $template = $this->getRecord();
        $this->selectedContactId = $template->test_contact_id;
        
        $this->form->fill([
            'selectedContactId' => $this->selectedContactId,
        ]);

        if ($this->selectedContactId) {
            $this->generatePreview();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedContactId')
                    ->label('Select Contact to Test With')
                    ->options(Contact::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $this->selectedContactId = $state;
                            $this->generatePreview();
                            $this->saveTestContact();
                        }
                    }),
            ]);
    }

    public function generatePreview(): void
    {
        $contact = Contact::find($this->selectedContactId);
        if (!$contact) return;

        $template = $this->getRecord();
        $parsed = $template->parseForContact($contact);

        $this->previewSubject = $parsed['subject'];
        $this->previewHtml = $parsed['html_content'];
        $this->previewText = $parsed['text_content'];
    }

    protected function saveTestContact(): void
    {
        $template = $this->getRecord();
        $template->update(['test_contact_id' => $this->selectedContactId]);

        Notification::make()
            ->title('Test contact saved')
            ->success()
            ->send();
    }

    public function getRecord(): TouchTemplate
    {
        return TouchTemplate::find($this->record);
    }
}
