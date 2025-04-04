<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\WorkflowExecution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TouchSeeder extends Seeder
{
    public function run(): void
    {
        // Get some contacts and workflow executions to work with
        $contacts = Contact::take(3)->get();
        $workflowExecutions = WorkflowExecution::take(3)->get();

        if ($contacts->isEmpty() || $workflowExecutions->isEmpty()) {
            $this->command->warn('No contacts or workflow executions found. Skipping touch seeding.');
            return;
        }

        $touches = [
            // Past touches
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_SENT,
                'subject' => 'Welcome to Bridge of Hope',
                'content' => 'Dear {contact.first_name},

We are so glad you\'ve joined the Bridge of Hope community. We look forward to walking alongside you on this journey.

Best regards,
The Bridge of Hope Team',
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
                'subject' => 'Your Bridge of Hope Journey Update',
                'content' => 'Dear {contact.first_name},

We wanted to check in and see how you\'re doing. Your journey with Bridge of Hope is important to us.

Would you like to schedule a time to talk?

Best regards,
The Bridge of Hope Team',
                'scheduled_for' => now(),
            ],
            // Failed touch
            [
                'type' => Touch::TYPE_SMS,
                'status' => Touch::STATUS_FAILED,
                'content' => 'Hi {contact.first_name}! Your mentor would like to connect with you. Please call us back when you can.',
                'scheduled_for' => now()->subDay(),
                'executed_at' => now()->subDay(),
                'error' => 'Failed to send SMS: Invalid phone number',
            ],
            // Future scheduled touches
            [
                'type' => Touch::TYPE_EMAIL,
                'status' => Touch::STATUS_SCHEDULED,
                'subject' => 'Monthly Check-in',
                'content' => 'Dear {contact.first_name},

It\'s time for our monthly check-in! How are things going with your goals?

Let us know if you need any support.

Best regards,
The Bridge of Hope Team',
                'scheduled_for' => now()->addDays(3),
            ],
            [
                'type' => Touch::TYPE_CALL,
                'status' => Touch::STATUS_SCHEDULED,
                'content' => 'Monthly follow-up call to check on housing situation and current needs.',
                'scheduled_for' => now()->addDays(5),
            ],
        ];

        foreach ($contacts as $index => $contact) {
            // Get a workflow execution for this contact
            $workflowExecution = $workflowExecutions[$index % $workflowExecutions->count()];

            // Create two touches for each contact
            $contactTouches = array_slice($touches, $index * 2, 2);
            foreach ($contactTouches as $touch) {
                Touch::create(array_merge($touch, [
                    'contact_id' => $contact->id,
                    'workflow_execution_id' => $workflowExecution->id,
                ]));
            }
        }

        $this->command->info('Created ' . (count($contacts) * 2) . ' touches.');
    }
}
