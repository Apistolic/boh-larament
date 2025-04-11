<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;

class NoOpActionHandler extends BaseActionHandler
{
    protected function handleAction(Contact $contact, array $parameters): array
    {
        \Log::info("NoOpActionHandler: Handling action", [
            'contact' => $contact->id,
            'parameters' => $parameters
        ]);

        return [
            'status' => 'completed',
            'message' => 'No operation performed',
            'parameters' => $parameters,
        ];
    }

    protected function getTouchType(): string
    {
        return 'system';
    }

    protected function getTouchContent(array $parameters, array $result): string
    {
        return sprintf(
            "No-op action executed with parameters: %s",
            json_encode($parameters)
        );
    }
}
