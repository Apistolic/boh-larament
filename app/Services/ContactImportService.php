<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ContactImportService
{
    public function import(string $filePath): array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($records as $offset => $record) {
            $validator = Validator::make($record, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'mobile_phone' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'job_title' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'twitter_url' => 'nullable|url|max:255',
                'facebook_url' => 'nullable|url|max:255',
                'lead_source' => 'nullable|string|max:255',
                'lead_status' => 'nullable|string|max:255',
                'lifecycle_stage' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $failed++;
                $errors[] = [
                    'row' => $offset + 2, // +2 because of 0-based index and header row
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            try {
                Contact::create($validator->validated());
                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'row' => $offset + 2,
                    'errors' => [$e->getMessage()]
                ];
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    public function getTemplate(): Collection
    {
        return collect([
            'first_name',
            'last_name',
            'email',
            'phone',
            'mobile_phone',
            'company',
            'job_title',
            'department',
            'street_address',
            'city',
            'state',
            'postal_code',
            'country',
            'linkedin_url',
            'twitter_url',
            'facebook_url',
            'lead_source',
            'lead_status',
            'lifecycle_stage',
            'notes',
        ]);
    }
}
