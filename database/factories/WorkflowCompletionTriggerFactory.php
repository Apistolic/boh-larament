<?php

namespace Database\Factories;

use App\Models\Workflow;
use App\Models\WorkflowCompletionTrigger;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowCompletionTriggerFactory extends Factory
{
    protected $model = WorkflowCompletionTrigger::class;

    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'type' => fake()->randomElement([
                WorkflowCompletionTrigger::TYPE_SUCCESS,
                WorkflowCompletionTrigger::TYPE_FAILURE,
            ]),
            'trigger_type' => fake()->randomElement([
                WorkflowCompletionTrigger::TRIGGER_LIFECYCLE_STAGE_CHANGED,
                WorkflowCompletionTrigger::TRIGGER_NO_RESPONSE,
                WorkflowCompletionTrigger::TRIGGER_STAGE_DEMOTED,
                WorkflowCompletionTrigger::TRIGGER_DONATION_RECEIVED,
                WorkflowCompletionTrigger::TRIGGER_DONATION_RENEWED,
                WorkflowCompletionTrigger::TRIGGER_MANUAL,
            ]),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkflowCompletionTrigger::TYPE_SUCCESS,
        ]);
    }

    public function failure(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkflowCompletionTrigger::TYPE_FAILURE,
        ]);
    }

    public function noResponse(int $days = 90): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowCompletionTrigger::TRIGGER_NO_RESPONSE,
            'criteria' => ['days' => $days],
            'type' => WorkflowCompletionTrigger::TYPE_FAILURE,
        ]);
    }

    public function lifecycleStageChanged(string $stage): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowCompletionTrigger::TRIGGER_LIFECYCLE_STAGE_CHANGED,
            'criteria' => ['stage' => $stage],
            'type' => WorkflowCompletionTrigger::TYPE_SUCCESS,
        ]);
    }

    public function stageDemoted(string $fromStage, string $toStage): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowCompletionTrigger::TRIGGER_STAGE_DEMOTED,
            'criteria' => [
                'from_stage' => $fromStage,
                'to_stage' => $toStage,
            ],
            'type' => WorkflowCompletionTrigger::TYPE_FAILURE,
        ]);
    }

    public function donationReceived(float $minimumAmount = 100.00): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowCompletionTrigger::TRIGGER_DONATION_RECEIVED,
            'criteria' => ['minimum_amount' => $minimumAmount],
            'type' => WorkflowCompletionTrigger::TYPE_SUCCESS,
        ]);
    }

    public function donationRenewed(int $renewalCount = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowCompletionTrigger::TRIGGER_DONATION_RENEWED,
            'criteria' => ['renewal_count' => $renewalCount],
            'type' => WorkflowCompletionTrigger::TYPE_SUCCESS,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
