<?php

namespace App\Observers;

use App\Models\Contact;
use App\Services\WorkflowEngine;

class ContactObserver
{
    public function __construct(
        private WorkflowEngine $workflowEngine
    ) {}

    public function created(Contact $contact): void
    {
        $this->workflowEngine->dispatch('contact_created', $contact);
    }

    public function updated(Contact $contact): void
    {
        // Check for lifecycle stage changes
        if ($contact->isDirty('lifecycle_stage')) {
            $this->workflowEngine->dispatch('lifecycle_stage_changed', $contact, [
                'previous_stage' => $contact->getOriginal('lifecycle_stage'),
                'new_stage' => $contact->lifecycle_stage,
            ]);
        }

        // General contact update trigger
        $this->workflowEngine->dispatch('contact_updated', $contact, [
            'changed_attributes' => $contact->getDirty(),
        ]);
    }
}
