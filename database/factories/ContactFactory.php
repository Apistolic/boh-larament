<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\LifecycleStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_phone' => $this->faker->numerify('##########'),
            'phone' => $this->faker->optional(0.3)->numerify('##########'),
            'street' => $this->faker->streetAddress(),
            'street_2' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $this->faker->city(),
            'state_code' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'USA',
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'source' => $this->faker->randomElement(['website', 'referral', 'event', 'social_media', 'direct']),
            'last_touched_at' => $this->faker->optional(0.8)->dateTimeThisYear(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Contact $contact) {
            // Attach a random lifecycle stage
            $stage = LifecycleStage::inRandomOrder()->first();
            if ($stage) {
                $contact->lifecycleStages()->attach($stage->id, [
                    'status' => 'active',
                    'started_at' => now(),
                ]);
            }
        });
    }
}
