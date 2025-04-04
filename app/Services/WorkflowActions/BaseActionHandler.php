<?php

namespace App\Services\WorkflowActions;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\WorkflowExecution;
use Illuminate\Support\Facades\Log;

abstract class BaseActionHandler implements ActionHandlerInterface
{
    protected WorkflowExecution $workflowExecution;

    public function setWorkflowExecution(WorkflowExecution $workflowExecution): void
    {
        $this->workflowExecution = $workflowExecution;
    }

    public function execute(Contact $contact, array $parameters): array
    {
        try {
            Log::info("Starting workflow action", [
                'action' => static::class,
                'contact_id' => $contact->id,
                'parameters' => $parameters,
            ]);

            $result = $this->handleAction($contact, $parameters);

            // Create a touch record for this action
            $this->createTouch($contact, $parameters, $result);

            Log::info("Completed workflow action", [
                'action' => static::class,
                'contact_id' => $contact->id,
                'result' => $result,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error("Failed workflow action", [
                'action' => static::class,
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    abstract protected function handleAction(Contact $contact, array $parameters): array;

    protected function createTouch(Contact $contact, array $parameters, array $result): Touch
    {
        $touch = new Touch([
            'contact_id' => $contact->id,
            'workflow_execution_id' => $this->workflowExecution->id,
            'type' => $this->getTouchType(),
            'status' => Touch::STATUS_SENT,
            'metadata' => [
                'action_class' => static::class,
                'parameters' => $parameters,
                'result' => $result,
            ],
            'content' => $this->getTouchContent($parameters, $result),
            'executed_at' => now(),
        ]);

        $touch->save();
        return $touch;
    }

    protected function getTouchType(): string
    {
        // Default to email, override in specific handlers if needed
        return Touch::TYPE_EMAIL;
    }

    protected function getTouchContent(array $parameters, array $result): string
    {
        // Default implementation, override in specific handlers for custom content
        return sprintf(
            "Action %s executed with parameters: %s\nResult: %s",
            class_basename(static::class),
            json_encode($parameters),
            json_encode($result)
        );
    }
}
