<?php

namespace App\Services\WorkflowActions;

use InvalidArgumentException;

class ActionHandlerFactory
{
    private array $handlers = [];

    public function __construct()
    {
        $this->handlers = [
            // Email Actions
            'send_welcome_email' => WelcomeEmailHandler::class,
            'send_thank_you' => SendThankYouHandler::class,
            'send_info_packet' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_welcome_packet' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_schedule' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_invitation' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_confirmation' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_congratulations' => SendThankYouHandler::class, // Reuse SendThankYouHandler
            'send_application' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_reminder' => WelcomeEmailHandler::class, // Reuse WelcomeEmailHandler
            'send_welcome_kit' => SendWelcomeKitHandler::class,

            // Task Actions - These will be no-ops for now
            'create_task' => NoOpActionHandler::class,
            'schedule_event' => NoOpActionHandler::class,
            'schedule_followup' => NoOpActionHandler::class,
            'schedule_orientation' => NoOpActionHandler::class,
            'schedule_training' => NoOpActionHandler::class,
            'schedule_interview' => NoOpActionHandler::class,
            'prepare_graduation_certificate' => NoOpActionHandler::class,

            // Program Actions - These will be no-ops for now
            'add_to_alumni' => NoOpActionHandler::class,
            'add_to_newsletter' => NoOpActionHandler::class,
            'assign_mentor' => NoOpActionHandler::class,
            'update_metrics' => NoOpActionHandler::class,
            'assign_owner' => NoOpActionHandler::class,
            'monitor_donor_status' => NoOpActionHandler::class,
            'create_followup_sequence' => NoOpActionHandler::class,

            // Event Actions - These will be no-ops for now
            'add_to_seating' => NoOpActionHandler::class,
            'create_name_tag' => NoOpActionHandler::class,
            'process_payment' => NoOpActionHandler::class,
            'coordinate_delivery' => NoOpActionHandler::class,
            'add_to_roster' => NoOpActionHandler::class,
        ];
    }

    public function create(string $action): ActionHandlerInterface
    {
        if (!isset($this->handlers[$action])) {
            throw new InvalidArgumentException("No handler registered for action: {$action}");
        }

        $handlerClass = $this->handlers[$action];
        return new $handlerClass();
    }
}
