<?php

namespace Database\Factories;

use App\Models\WorkflowType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowTypeFactory extends Factory
{
    protected $model = WorkflowType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
