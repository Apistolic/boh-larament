<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\TouchTemplate;
use App\Models\WorkflowExecution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TouchSeeder extends Seeder
{
    public function run(): void
    {
        // Get some contacts and workflow executions to work with
        $contacts = Contact::take(20)->get();
        $workflowExecutions = WorkflowExecution::take(10)->get();
        $templates = TouchTemplate::all();

        if ($contacts->isEmpty() || $workflowExecutions->isEmpty()) {
            $this->command->warn('No contacts or workflow executions found. Skipping touch seeding.');
            return;
        }

        if ($templates->isEmpty()) {
            $this->command->warn('No templates found. Run TouchTemplateSeeder first.');
            return;
        }

        // Clear existing touches
        DB::table('touches')->delete();

        $welcomeTemplate = $templates->firstWhere('name', 'Welcome New Donor');
        $galaTemplate = $templates->firstWhere('name', 'Gala Invitation');
        $neighborTemplate = $templates->firstWhere('name', 'Welcome New Neighbor');

        $touches = [
            // Past touches
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_SENT,
                'template_id' => $welcomeTemplate->id,
                'subject' => $welcomeTemplate->subject,
                'content' => $welcomeTemplate->html_content,
                'scheduled_for' => now()->subDays(5),
                'executed_at' => now()->subDays(5),
            ],
            [
                'type' => Touch::TYPE_SMS,
                'status' => Touch::STATUS_SENT,
                'content' => 'Hi {contact.first_name}! Just a reminder about your upcoming meeting tomorrow at 2 PM. Looking forward to seeing you!',
                'scheduled_for' => now()->subDays(2),
                'executed_at' => now()->subDays(2),
            ],
            // Current touches
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_PENDING,
                'template_id' => $galaTemplate->id,
                'subject' => $galaTemplate->subject,
                'content' => $galaTemplate->html_content,
                'scheduled_for' => now()->addDays(2),
            ],
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_SCHEDULED,
                'template_id' => $neighborTemplate->id,
                'subject' => $neighborTemplate->subject,
                'content' => $neighborTemplate->html_content,
                'scheduled_for' => now()->addDays(5),
            ],
            // Failed touch
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_FAILED,
                'template_id' => $welcomeTemplate->id,
                'subject' => $welcomeTemplate->subject,
                'content' => $welcomeTemplate->html_content,
                'scheduled_for' => now()->subDays(1),
                'executed_at' => now()->subDays(1),
                'error' => 'Failed to deliver: Invalid email address',
            ],
        ];

        foreach ($touches as $touch) {
            // Get a random contact and workflow execution
            $contact = $contacts->random();
            $workflowExecution = $workflowExecutions->random();

            // Create the touch
            Touch::create(array_merge($touch, [
                'contact_id' => $contact->id,
                'workflow_execution_id' => $workflowExecution->id,
            ]));
        }

        $this->command->info('Created ' . count($touches) . ' touches.');
    }
}
