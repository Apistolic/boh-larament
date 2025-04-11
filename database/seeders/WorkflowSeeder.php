<?php

namespace Database\Seeders;

use App\Models\Workflow;
use App\Models\WorkflowType;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $workflows = [
            // Lead to DonorActive Workflow
            [
                'name' => 'Lead to DonorActive Process',
                'workflow_type_id' => 1, // Donor workflow type
                'description' => 'Complete process flow from initial lead to active donor status',
                'trigger_type' => 'contact_created',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'lead',
                ],
                'actions' => [
                    'send_welcome_email' => [
                        'template' => 'Welcome New Donor',
                        'delay' => 0
                    ],
                    'create_followup_sequence' => [
                        'email_followup_1' => '+3 days',
                        'email_followup_2' => '+7 days',
                        'call_followup_1' => '+10 days'
                    ],
                    'monitor_donor_status' => true
                ],
                'sequence_diagram' => 'sequenceDiagram
    title Lead to DonorActive
    autonumber
    participant BoH_BD
    participant Leads
    participant DonorCandidate
    
    Leads -->> BoH_BD: New Donor Candidate Lead
    BoH_BD -->> DonorCandidate: Welcome/Initial Outreach
    loop Donor Candidate Close
        BoH_BD -->> DonorCandidate: Email/Text Follow-up 1
        BoH_BD -->> DonorCandidate: Email/Text Follow-up 2
        BoH_BD -->> DonorCandidate: Call Follow-up 1
    end
    DonorCandidate -->> BoH_BD: Donor Close & Vitals
    note right of DonorCandidate: Donor Status Changed
    participant DonorActive
    DonorActive -->> BoH_BD: Donation',
                'is_active' => true,
            ],
            
            // Donor Workflows
            [
                'name' => 'New Donor Candidate Process',
                'workflow_type_id' => 1,
                'description' => 'Workflow triggered when a new donor candidate is identified',
                'trigger_type' => 'lifecycle_stage_changed',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'donor_candidate',
                    'previous_stage' => null,
                ],
                'actions' => [
                    'send_welcome_email' => [
                        'template' => 'Welcome New Donor',
                        'delay' => 0
                    ],
                    'create_task' => [
                        'type' => 'schedule_initial_meeting',
                        'due_in' => '+7 days'
                    ],
                    'assign_owner' => 'random_development_team',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Donor Activation',
                'workflow_type_id' => 1,
                'description' => 'Process when a donor candidate becomes an active donor',
                'trigger_type' => 'lifecycle_stage_changed',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'donor',
                    'previous_stage' => 'donor_candidate',
                ],
                'actions' => [
                    'send_thank_you' => [
                        'template' => 'Thank You for Your Donation',
                        'delay' => 0
                    ],
                    'add_to_newsletter' => 'donor_newsletter',
                    'schedule_followup' => [
                        'type' => 'check_in',
                        'timing' => '30_days',
                        'assignee' => 'program_coordinator'
                    ],
                ],
                'is_active' => true,
            ],

            // Neighbor Workflows
            [
                'name' => 'New Neighboring Volunteer Interest',
                'workflow_type_id' => 2, // Neighbor workflow type
                'description' => 'Process for new volunteer inquiries',
                'trigger_type' => 'contact_created',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'neighbor_candidate',
                ],
                'actions' => [
                    'send_info_packet' => [
                        'template' => 'Volunteer Information Packet',
                        'delay' => 0
                    ],
                    'schedule_orientation' => [
                        'type' => 'next_available',
                        'due_in' => '+14 days'
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Volunteer Onboarding',
                'workflow_type_id' => 2,
                'description' => 'Onboarding process for new volunteers',
                'trigger_type' => 'lifecycle_stage_changed',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'neighbor',
                    'previous_stage' => 'neighbor_candidate',
                ],
                'actions' => [
                    'send_welcome_kit' => [
                        'template' => 'Welcome New Volunteer',
                        'delay' => 0
                    ],
                    'schedule_training' => [
                        'type' => 'initial_training',
                        'due_in' => '+21 days'
                    ],
                    'assign_mentor' => 'experienced_volunteer',
                ],
                'is_active' => true,
            ],

            // Mom Workflows
            [
                'name' => 'New Mom Application',
                'workflow_type_id' => 3, // Mom workflow type
                'description' => 'Process for new mom program applications',
                'trigger_type' => 'contact_created',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'mom_candidate',
                ],
                'actions' => [
                    'send_application' => [
                        'template' => 'Mom Program Application',
                        'delay' => 0
                    ],
                    'create_task' => [
                        'type' => 'review_application',
                        'due_in' => '+7 days'
                    ],
                    'schedule_interview' => [
                        'type' => 'initial_interview',
                        'due_in' => '+14 days'
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Mom Program Acceptance',
                'workflow_type_id' => 3,
                'description' => 'Process when a mom is accepted into the program',
                'trigger_type' => 'lifecycle_stage_changed',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'mom_participant',
                    'previous_stage' => 'mom_candidate',
                ],
                'actions' => [
                    'send_welcome_packet' => [
                        'template' => 'Welcome to Mom Program',
                        'delay' => 0
                    ],
                    'assign_mentor' => 'mom_program_mentor',
                    'schedule_orientation' => [
                        'type' => 'next_mom_orientation',
                        'due_in' => '+21 days'
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Mom Program Graduation',
                'workflow_type_id' => 3,
                'description' => 'Process when a mom completes the program and graduates',
                'trigger_type' => 'lifecycle_stage_changed',
                'trigger_criteria' => [
                    'lifecycle_stage' => 'mom_graduate',
                    'previous_stage' => 'mom_participant',
                ],
                'actions' => [
                    'send_congratulations' => [
                        'template' => 'Congratulations on Graduation',
                        'delay' => 0
                    ],
                    'schedule_event' => [
                        'type' => 'graduation_ceremony',
                        'due_in' => '+30 days'
                    ],
                    'create_task' => [
                        'type' => 'prepare_graduation_certificate',
                        'due_in' => '+14 days'
                    ],
                    'add_to_alumni' => 'mom_program_alumni',
                    'schedule_followup' => [
                        'type' => 'check_in',
                        'timing' => '3_months',
                        'assignee' => 'program_coordinator'
                    ],
                    'update_metrics' => 'program_completion_stats',
                ],
                'is_active' => true,
            ],

            // Gala Workflows
            [
                'name' => 'Gala Invitation Process',
                'workflow_type_id' => 4, // Gala workflow type
                'description' => 'Workflow for potential gala attendees',
                'trigger_type' => 'manual',
                'trigger_criteria' => [
                    'donor_level' => ['major', 'regular'],
                    'previous_attendance' => true,
                ],
                'actions' => [
                    'send_invitation' => [
                        'template' => 'Gala Invitation',
                        'delay' => 0
                    ],
                    'create_task' => [
                        'type' => 'followup_call',
                        'due_in' => '+7 days'
                    ],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gala Attendee Registration',
                'workflow_type_id' => 4,
                'description' => 'Process when someone registers for the gala',
                'trigger_type' => 'contact_updated',
                'trigger_criteria' => [
                    'event_registration' => 'gala_confirmed',
                ],
                'actions' => [
                    'send_confirmation' => [
                        'template' => 'Gala Registration Confirmation',
                        'delay' => 0
                    ],
                    'add_to_seating' => 'gala_seating_chart',
                    'create_name_tag' => 'gala_name_tags',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gala Auction Winner Follow-up',
                'workflow_type_id' => 4,
                'description' => 'Process for auction item winners',
                'trigger_type' => 'manual',
                'trigger_criteria' => [
                    'auction_status' => 'won',
                ],
                'actions' => [
                    'send_congratulations' => [
                        'template' => 'Auction Winner Congratulations',
                        'delay' => 0
                    ],
                    'process_payment' => 'auction_payment',
                    'coordinate_delivery' => 'auction_item_delivery',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gala Volunteer Sign-up',
                'workflow_type_id' => 4,
                'description' => 'Process for volunteers signing up for gala duties',
                'trigger_type' => 'contact_updated',
                'trigger_criteria' => [
                    'volunteer_event' => 'gala',
                    'status' => 'confirmed',
                ],
                'actions' => [
                    'send_schedule' => [
                        'template' => 'Gala Volunteer Schedule',
                        'delay' => 0
                    ],
                    'add_to_roster' => 'gala_volunteer_roster',
                    'send_reminder' => [
                        'type' => 'reminder',
                        'timing' => 'day_before'
                    ],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($workflows as $workflow) {
            Workflow::create($workflow);
        }
    }
}
