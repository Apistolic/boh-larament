<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Touch;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class TouchFactory extends Factory
{
    protected $model = Touch::class;

    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'workflow_id' => Workflow::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['email', 'sms', 'call', 'letter']),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(3, true),
            'status' => 'sent',
            'sent_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
