<?php

namespace Database\Factories;

use App\Models\EmailClick;
use App\Models\EmailSend;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailClickFactory extends Factory
{
    protected $model = EmailClick::class;

    public function definition(): array
    {
        return [
            'email_send_id' => EmailSend::factory(),
            'url' => $this->faker->url(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'clicked_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
