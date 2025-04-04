<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;

interface ActionHandlerInterface
{
    /**
     * Execute the workflow action
     *
     * @param Contact $contact The contact this action is being executed for
     * @param array $parameters Parameters for the action
     * @return array Result of the action execution
     */
    public function execute(Contact $contact, array $parameters): array;
}
