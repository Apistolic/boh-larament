<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;
use App\Models\Touch;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;

class WelcomeEmailHandler extends BaseActionHandler
{
    protected function handleAction(Contact $contact, array $parameters): array
    {
        // Get the template type from parameters
        $template = $parameters['template'] ?? 'default';

        // Send the welcome email
        Notification::send($contact, new WelcomeEmail($template));

        return [
            'email_sent' => true,
            'template_used' => $template,
            'sent_at' => now()->toIso8601String(),
        ];
    }

    protected function getTouchContent(array $parameters, array $result): string
    {
        return sprintf(
            "Sent welcome email using template: %s",
            $parameters['template'] ?? 'default'
        );
    }
}
