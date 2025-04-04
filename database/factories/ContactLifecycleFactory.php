<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactLifecycle;
use App\Models\LifecycleStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactLifecycleFactory extends Factory
{
    protected $model = ContactLifecycle::class;

    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'lifecycle_stage_id' => LifecycleStage::factory(),
            'status' => 'active',
            'started_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'ended_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }

    public function ended(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'ended',
                'ended_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }
}
