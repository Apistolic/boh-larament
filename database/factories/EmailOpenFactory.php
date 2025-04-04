<?php

namespace Database\Factories;

use App\Models\EmailOpen;
use App\Models\EmailSend;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailOpenFactory extends Factory
{
    protected $model = EmailOpen::class;

    public function definition(): array
    {
        return [
            'email_send_id' => EmailSend::factory(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'opened_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
