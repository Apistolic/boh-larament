<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowExecutionFactory extends Factory
{
    protected $model = WorkflowExecution::class;

    public function definition(): array
    {
        return [
            'workflow_id' => Workflow::factory(),
            'contact_id' => Contact::factory(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'failed']),
            'started_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'failed_at' => null,
            'error' => null,
            'metadata' => [],
        ];
    }

    public function failed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'failed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
                'error' => $this->faker->sentence(),
            ];
        });
    }
}
