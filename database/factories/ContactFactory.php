<?php

namespace Database\Factories;

use App\Models\Contact;
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
            'lifecycle_stage' => $this->faker->randomElement(array_keys(Contact::LIFECYCLE_STAGES)),
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'source' => $this->faker->randomElement(['website', 'referral', 'event', 'social_media', 'direct']),
            'last_touched_at' => $this->faker->optional(0.8)->dateTimeThisYear(),
        ];
    }
}
