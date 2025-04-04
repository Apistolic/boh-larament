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
            'send_congratulations' => CongratulationsEmailHandler::class,
            'send_info_packet' => InfoPacketEmailHandler::class,
            'send_welcome_packet' => WelcomePacketEmailHandler::class,
            'send_schedule' => ScheduleEmailHandler::class,

            // Task Actions
            'create_task' => CreateTaskHandler::class,
            'schedule_event' => ScheduleEventHandler::class,
            'schedule_followup' => ScheduleFollowupHandler::class,
            'prepare_graduation_certificate' => PrepareGraduationCertificateHandler::class,

            // Program Actions
            'add_to_alumni' => AddToAlumniHandler::class,
            'add_to_newsletter' => AddToNewsletterHandler::class,
            'assign_mentor' => AssignMentorHandler::class,
            'update_metrics' => UpdateMetricsHandler::class,

            // Event Actions
            'add_to_seating' => AddToSeatingHandler::class,
            'create_name_tag' => CreateNameTagHandler::class,
            'process_payment' => ProcessPaymentHandler::class,
            'coordinate_delivery' => CoordinateDeliveryHandler::class,
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
