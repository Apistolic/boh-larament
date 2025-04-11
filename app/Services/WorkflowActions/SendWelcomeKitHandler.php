<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\TouchTemplate;
use App\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Notification;

class SendWelcomeKitHandler extends BaseActionHandler
{
    protected function handleAction(Contact $contact, array $parameters): array
    {
        // Get the template from parameters
        $templateName = $parameters['template'] ?? 'Volunteer Welcome Kit';
        $delay = $parameters['delay'] ?? 0;

        \Log::info("SendWelcomeKitHandler: Handling action", [
            'contact' => $contact->id,
            'template' => $templateName,
            'delay' => $delay,
            'parameters' => $parameters
        ]);

        // Find the template
        $template = TouchTemplate::where('name', $templateName)->first();
        if (!$template) {
            \Log::error("SendWelcomeKitHandler: Template not found", ['template_name' => $templateName]);
            throw new \RuntimeException("Template not found: {$templateName}");
        }

        \Log::info("SendWelcomeKitHandler: Found template", ['template_id' => $template->id]);

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

        \Log::info("SendWelcomeKitHandler: Created touch", ['touch_id' => $touch->id]);

        // Send the welcome kit email
        try {
            Notification::send($contact, new WelcomeEmail($template));
            
            // Update touch status
            $touch->update([
                'status' => Touch::STATUS_SENT,
                'executed_at' => now(),
            ]);

            \Log::info("SendWelcomeKitHandler: Email sent and touch updated", ['touch_id' => $touch->id]);

            return [
                'email_sent' => true,
                'template_used' => $templateName,
                'sent_at' => now()->toIso8601String(),
                'touch_id' => $touch->id,
            ];
        } catch (\Exception $e) {
            \Log::error("SendWelcomeKitHandler: Failed to send email", [
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
}
