<?php

namespace Database\Factories;

use App\Models\TouchTemplate;
use App\Models\TouchTemplateBlock;
use App\Models\TouchTemplateLayout;
use Illuminate\Database\Eloquent\Factories\Factory;

class TouchTemplateLayoutFactory extends Factory
{
    protected $model = TouchTemplateLayout::class;

    public function definition(): array
    {
        return [
            'touch_template_id' => TouchTemplate::factory(),
            'touch_template_block_id' => TouchTemplateBlock::factory(),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'metadata' => [],
        ];
    }
}
