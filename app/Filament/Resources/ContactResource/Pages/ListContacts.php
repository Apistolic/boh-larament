<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use App\Services\ContactImportService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Contacts')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('csv_file')
                        ->label('CSV File')
                        ->acceptedFileTypes(['text/csv'])
                        ->required()
                        ->helperText('Please upload a CSV file with the required columns. [Download Template]'),
                ])
                ->action(function (array $data, ContactImportService $importService): void {
                    $result = $importService->import(storage_path('app/public/' . $data['csv_file']));
                    
                    if ($result['failed'] > 0) {
                        $errorMessage = "Import completed with errors.\n";
                        $errorMessage .= "Imported: {$result['imported']} contacts\n";
                        $errorMessage .= "Failed: {$result['failed']} contacts\n\n";
                        $errorMessage .= "Errors:\n";
                        
                        foreach ($result['errors'] as $error) {
                            $errorMessage .= "Row {$error['row']}: " . implode(', ', $error['errors']) . "\n";
                        }
                        
                        Notifications\Notification::make()
                            ->warning()
                            ->title('Import Completed with Errors')
                            ->body($errorMessage)
                            ->persistent()
                            ->send();
                    } else {
                        Notifications\Notification::make()
                            ->success()
                            ->title('Import Successful')
                            ->body("Successfully imported {$result['imported']} contacts.")
                            ->send();
                    }
                }),

            Action::make('template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (ContactImportService $importService) {
                    $headers = $importService->getTemplate();
                    
                    $csv = fopen('php://temp', 'r+');
                    fputcsv($csv, $headers->toArray());
                    
                    rewind($csv);
                    $content = stream_get_contents($csv);
                    fclose($csv);
                    
                    return response()->streamDownload(function () use ($content) {
                        echo $content;
                    }, 'contacts_template.csv');
                }),

            Actions\CreateAction::make(),
            ...parent::getHeaderActions(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        $title = 'Contacts';
        
        // Get the current filter values
        $filter = $this->getTableFilters()['lifecycle_stage']['values'] ?? [];
        
        if (!empty($filter)) {
            $filterLabels = [
                'donor' => 'Active Donors',
                'donor_influencer' => 'Donor Influencers',
                'donor_candidate' => 'Donor Candidates',
                'donor_retired' => 'Retired Donors',
            ];
            
            $activeFilters = array_intersect_key($filterLabels, array_flip($filter));
            
            if (!empty($activeFilters)) {
                $title .= ' - ' . implode(', ', $activeFilters);
            }
        }
        
        return $title;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $filter = $this->getTableFilters()['lifecycle_stage']['values'] ?? [];
        
        if (empty($filter)) {
            return null;
        }

        return new HtmlString(
            '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full ' . 
            $this->getFilterBadgeColor($filter) . '">' .
            count($filter) . ' donor ' . (count($filter) === 1 ? 'filter' : 'filters') . ' active' .
            '</span>'
        );
    }

    protected function getFilterBadgeColor(array $filter): string
    {
        if (in_array('donor_candidate', $filter)) {
            return 'bg-warning-500/10 text-warning-700';
        }
        
        if (array_intersect(['donor_retired'], $filter)) {
            return 'bg-gray-500/10 text-gray-700';
        }
        
        return 'bg-success-500/10 text-success-700';
    }
}
