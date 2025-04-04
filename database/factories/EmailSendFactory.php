<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\EmailSend;
use App\Models\Touch;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailSendFactory extends Factory
{
    protected $model = EmailSend::class;

    public function definition(): array
    {
        return [
            'touch_id' => Touch::factory(),
            'contact_id' => Contact::factory(),
            'to_email' => $this->faker->email(),
            'subject' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['queued', 'sent', 'failed', 'bounced']),
            'sent_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'metadata' => [],
        ];
    }
}
