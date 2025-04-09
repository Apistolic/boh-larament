<?php

namespace Database\Factories;

use App\Models\Workflow;
use App\Models\WorkflowInitiationTrigger;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowInitiationTriggerFactory extends Factory
{
    protected $model = WorkflowInitiationTrigger::class;

    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'trigger_type' => fake()->randomElement([
                WorkflowInitiationTrigger::TRIGGER_CONTACT_CREATED,
                WorkflowInitiationTrigger::TRIGGER_CONTACT_UPDATED,
                WorkflowInitiationTrigger::TRIGGER_LIFECYCLE_STAGE_CHANGED,
                WorkflowInitiationTrigger::TRIGGER_MANUAL,
            ]),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function lifecycleStageChanged(string $stage): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowInitiationTrigger::TRIGGER_LIFECYCLE_STAGE_CHANGED,
            'criteria' => ['stage' => $stage],
        ]);
    }

    public function contactUpdated(array $fields): static
    {
        return $this->state(fn (array $attributes) => [
            'trigger_type' => WorkflowInitiationTrigger::TRIGGER_CONTACT_UPDATED,
            'criteria' => ['fields' => $fields],
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
