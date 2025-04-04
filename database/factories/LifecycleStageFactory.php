<?php

namespace Database\Factories;

use App\Models\LifecycleCategory;
use App\Models\LifecycleStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LifecycleStageFactory extends Factory
{
    protected $model = LifecycleStage::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'lifecycle_category_id' => LifecycleCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
