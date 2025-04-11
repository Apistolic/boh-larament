<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Touch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailTrackingSeeder extends Seeder
{
    public function run(): void
    {
        // Get sent email touches
        $emailTouches = Touch::where('type', Touch::TYPE_EMAIL)
            ->where('status', Touch::STATUS_SENT)
            ->with('template')
            ->get();

        if ($emailTouches->isEmpty()) {
            $this->command->warn('No sent email touches found. Run TouchSeeder first.');
            return;
        }

        // Clear existing tracking data
        DB::table('email_sends')->delete();
        DB::table('email_opens')->delete();
        DB::table('email_clicks')->delete();

        foreach ($emailTouches as $touch) {
            // Create email send
            $emailSendId = Str::uuid();
            DB::table('email_sends')->insert([
                'id' => $emailSendId,
                'contact_id' => $touch->contact_id,
                'subject' => $touch->template?->subject ?? $touch->subject ?? 'Welcome to Bridge of Hope',
                'content' => $touch->template?->html_content ?? $touch->content ?? 'Welcome to our community!',
                'tracking_pixel_id' => Str::random(50),
                'sent_at' => $touch->executed_at,
                'created_at' => $touch->executed_at,
                'updated_at' => $touch->executed_at,
            ]);

            // 80% chance of email being opened
            if (rand(1, 100) <= 80) {
                $openedAt = $touch->executed_at->addMinutes(rand(1, 120));
                
                DB::table('email_opens')->insert([
                    'id' => Str::uuid(),
                    'email_send_id' => $emailSendId,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'email_client' => ['Gmail', 'Outlook', 'Apple Mail'][rand(0, 2)],
                    'device_type' => ['desktop', 'mobile', 'tablet'][rand(0, 2)],
                    'country' => 'United States',
                    'city' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'][rand(0, 4)],
                    'opened_at' => $openedAt,
                    'created_at' => $openedAt,
                    'updated_at' => $openedAt,
                ]);

                // 60% chance of clicking a link if opened
                if (rand(1, 100) <= 60) {
                    $clickedAt = $openedAt->addMinutes(rand(1, 60));
                    
                    DB::table('email_clicks')->insert([
                        'id' => Str::uuid(),
                        'email_send_id' => $emailSendId,
                        'link_url' => 'https://example.com/' . ['donate', 'about', 'contact', 'events'][rand(0, 3)],
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'ip_address' => '192.168.1.' . rand(1, 255),
                        'device_type' => ['desktop', 'mobile', 'tablet'][rand(0, 2)],
                        'country' => 'United States',
                        'city' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'][rand(0, 4)],
                        'clicked_at' => $clickedAt,
                        'created_at' => $clickedAt,
                        'updated_at' => $clickedAt,
                    ]);
                }
            }
        }

        $this->command->info('Email tracking data seeded successfully.');
    }
}
