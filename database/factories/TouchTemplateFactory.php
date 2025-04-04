<?php

namespace Database\Factories;

use App\Models\TouchTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TouchTemplateFactory extends Factory
{
    protected $model = TouchTemplate::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['email', 'sms', 'letter']),
            'subject' => $this->faker->sentence(),
            'is_active' => true,
            'metadata' => [],
        ];
    }
}
