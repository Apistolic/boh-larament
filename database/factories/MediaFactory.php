<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $fileName = $this->faker->uuid() . '.jpg';
        return [
            'name' => $this->faker->words(3, true),
            'file_name' => $fileName,
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(1000, 5000000),
            'collection' => 'default',
            'disk' => 'public',
        ];
    }
}
