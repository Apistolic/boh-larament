<?php

namespace Database\Seeders;

use App\Models\LifecycleStage;
use App\Models\Workflow;
use Illuminate\Database\Seeder;

class WorkflowTriggerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For each workflow, create appropriate triggers
        Workflow::where('legacy_trigger', true)->each(function ($workflow) {
            // Convert legacy trigger to new initiation trigger
            $workflow->initiationTriggers()->create([
                'trigger_type' => $workflow->trigger_type,
                'criteria' => $workflow->trigger_criteria,
                'name' => 'Legacy Trigger',
                'description' => 'Automatically converted from legacy trigger',
                'is_active' => true,
                'sort_order' => 0,
            ]);

            // Create default completion triggers based on workflow type
            $this->createCompletionTriggers($workflow);

            // Mark workflow as migrated
            $workflow->update(['legacy_trigger' => false]);
        });
    }

    protected function createCompletionTriggers(Workflow $workflow): void
    {
        // Get the donor stage for success triggers
        $donorStage = LifecycleStage::where('name', 'like', '%Donor%')
            ->where('name', 'not like', '%Candidate%')
            ->first();

        // Get the candidate stage for failure triggers
        $candidateStage = LifecycleStage::where('name', 'like', '%Candidate%')
            ->first();

        // Common failure triggers
        $workflow->completionTriggers()->create([
            'type' => 'failure',
            'trigger_type' => 'no_response',
            'criteria' => ['days' => 90],
            'name' => 'No Response',
            'description' => 'No response received in 90 days',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        // Type-specific triggers
        if (str_contains(strtolower($workflow->legacy_type), 'donor')) {
            if ($donorStage) {
                // Success: Became a donor
                $workflow->completionTriggers()->create([
                    'type' => 'success',
                    'trigger_type' => 'lifecycle_stage_changed',
                    'criteria' => ['stage' => $donorStage->slug],
                    'name' => 'Became Donor',
                    'description' => 'Contact became an active donor',
                    'is_active' => true,
                    'sort_order' => 0,
                ]);
            }

            // Success: Made a donation
            $workflow->completionTriggers()->create([
                'type' => 'success',
                'trigger_type' => 'donation_received',
                'criteria' => ['minimum_amount' => 100.00],
                'name' => 'Donation Received',
                'description' => 'Contact made a donation of at least $100',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        // Add stage demotion trigger if we have both stages
        if ($donorStage && $candidateStage) {
            $workflow->completionTriggers()->create([
                'type' => 'failure',
                'trigger_type' => 'stage_demoted',
                'criteria' => [
                    'from_stage' => $donorStage->slug,
                    'to_stage' => $candidateStage->slug,
                ],
                'name' => 'Stage Demoted',
                'description' => 'Contact was demoted from donor to candidate',
                'is_active' => true,
                'sort_order' => 20,
            ]);
        }
    }
}
