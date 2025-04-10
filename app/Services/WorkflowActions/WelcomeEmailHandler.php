<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\TouchTemplate;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class WelcomeEmailHandler extends BaseActionHandler
{
    protected function handleAction(Contact $contact, array $parameters): array
    {
        // Get the template from parameters
        $templateName = $parameters['template'] ?? 'Welcome New Donor';
        $delay = $parameters['delay'] ?? 0;

        Log::info("WelcomeEmailHandler: Handling action", [
            'contact' => $contact->id,
            'template' => $templateName,
            'delay' => $delay,
            'parameters' => $parameters
        ]);

        // Find the template
        $template = TouchTemplate::where('name', $templateName)->first();
        if (!$template) {
            Log::error("WelcomeEmailHandler: Template not found", ['template_name' => $templateName]);
            throw new \RuntimeException("Template not found: {$templateName}");
        }

        Log::info("WelcomeEmailHandler: Found template", ['template_id' => $template->id]);

        // Create the touch
        $touch = Touch::create([
            'contact_id' => $contact->id,
            'workflow_execution_id' => $this->workflowExecution->id,
            'type' => Touch::TYPE_EMAIL,
            'status' => Touch::STATUS_PENDING,
            'template_id' => $template->id,
            'subject' => $template->subject,
            'content' => $template->html_content,
            'scheduled_for' => now()->addDays($delay),
        ]);

        Log::info("WelcomeEmailHandler: Created touch", ['touch_id' => $touch->id]);

        // Send the welcome email
        try {
            Notification::send($contact, new WelcomeEmail($template));
            
            // Update touch status
            $touch->update([
                'status' => Touch::STATUS_SENT,
                'executed_at' => now(),
            ]);

            Log::info("WelcomeEmailHandler: Email sent and touch updated", ['touch_id' => $touch->id]);

            return [
                'email_sent' => true,
                'template_used' => $templateName,
                'sent_at' => now()->toIso8601String(),
                'touch_id' => $touch->id,
            ];
        } catch (\Exception $e) {
            Log::error("WelcomeEmailHandler: Failed to send email", [
                'touch_id' => $touch->id,
                'error' => $e->getMessage()
            ]);

            // Update touch status with error
            $touch->update([
                'status' => Touch::STATUS_FAILED,
                'error' => $e->getMessage(),
                'executed_at' => now(),
            ]);

            throw $e;
        }
    }

    protected function getTouchContent(array $parameters, array $result): string
    {
        return sprintf(
            "Sent welcome email using template: %s (Touch ID: %d)",
            $parameters['template'] ?? 'default',
            $result['touch_id'] ?? 0
        );
    }
}
