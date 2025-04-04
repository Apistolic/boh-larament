<?php

namespace Database\Factories;

use App\Models\TouchTemplateBlock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TouchTemplateBlockFactory extends Factory
{
    protected $model = TouchTemplateBlock::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'type' => $this->faker->randomElement(['header', 'body', 'footer']),
            'is_active' => true,
            'metadata' => [],
        ];
    }
}
