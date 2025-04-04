<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\TouchTemplateBlock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TouchTemplateBlocksSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing blocks
        DB::table('touch_template_blocks')->delete();

        // Get the logo from media library
        $logo = Media::where('name', 'Logo')->first();
        $logoUrl = $logo ? $logo->url : 'https://harrisburg.bridgeofhopeinc.org/wp-content/uploads/sites/12/2023/01/Bridge-of-Hope-Harrisburg-Area-Logo.png';

        $blocks = [
            [
                'name' => 'Logo Header',
                'slug' => 'logo-header',
                'html_content' => '
                    <div style="text-align: center; padding: 30px; background-color: white;">
                        <img src="' . $logoUrl . '" alt="Bridge of Hope Harrisburg Area" style="max-width: 300px; height: auto;">
                        <div style="margin-top: 20px; color: #2A4B9B; font-size: 1.5rem; font-weight: bold;">
                            Helping End Homelessness
                        </div>
                    </div>
                ',
                'default_values' => json_encode([
                    'logo_url' => $logoUrl,
                    'organization_name' => 'Bridge of Hope Harrisburg Area',
                    'tagline' => 'Helping End Homelessness'
                ]),
            ],
            [
                'name' => 'Hero Section',
                'slug' => 'hero',
                'html_content' => '
                    <div style="position: relative; margin-bottom: 30px;">
                        <img src="{{ block.image_url }}" alt="{{ block.heading }}" style="width: 100%; height: auto;">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(42, 75, 155, 0.8); color: white; padding: 20px;">
                            <h1 style="margin: 0; font-size: 2rem;">{{ block.heading }}</h1>
                            <p style="margin: 10px 0 0 0;">{{ block.subheading }}</p>
                        </div>
                    </div>
                ',
                'default_values' => json_encode([
                    'image_url' => Media::where('name', 'Hero')->first()?->url,
                    'heading' => 'Transform Lives Today',
                    'subheading' => 'Join us in making a difference in our community'
                ]),
            ],
            [
                'name' => 'Call to Action',
                'slug' => 'cta',
                'html_content' => '
                    <div style="text-align: center;">
                        <h2 style="color: white; font-size: 1.5rem; margin-bottom: 20px;">{{ block.heading }}</h2>
                        <p style="color: white; margin-bottom: 25px;">{{ block.description }}</p>
                        <a href="{{ block.button_url }}" 
                           style="display: inline-block; background-color: #F15A29; color: white; padding: 12px 30px; 
                                  text-decoration: none; border-radius: 4px; font-weight: bold;">
                            {{ block.button_text }}
                        </a>
                    </div>
                ',
                'default_values' => json_encode([
                    'heading' => 'Make a Difference Today',
                    'description' => 'Your support can help transform lives and end homelessness in our community.',
                    'button_url' => 'https://harrisburg.bridgeofhopeinc.org/donate/',
                    'button_text' => 'Donate Now'
                ]),
            ],
            [
                'name' => 'Email Footer',
                'slug' => 'footer',
                'html_content' => '
                    <div style="color: #58595B; font-size: 0.875rem;">
                        <p style="margin-bottom: 15px;">{{ block.organization_name }}<br>
                        {{ block.address }}<br>
                        {{ block.phone }}</p>
                        <p style="margin-bottom: 15px;">{{ block.disclaimer }}</p>
                    </div>
                ',
                'default_values' => json_encode([
                    'organization_name' => 'Bridge of Hope Harrisburg Area',
                    'address' => '1935 Linglestown Road, Harrisburg, PA 17110',
                    'phone' => '(717) 635-5957',
                    'disclaimer' => 'This email was sent by Bridge of Hope Harrisburg Area. To unsubscribe, please click here.'
                ]),
            ],
            [
                'name' => 'Social Links',
                'slug' => 'social_links',
                'html_content' => '
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ block.facebook_url }}" style="margin: 0 10px; color: #2A4B9B; text-decoration: none;">Facebook</a>
                        <a href="{{ block.instagram_url }}" style="margin: 0 10px; color: #2A4B9B; text-decoration: none;">Instagram</a>
                        <a href="{{ block.linkedin_url }}" style="margin: 0 10px; color: #2A4B9B; text-decoration: none;">LinkedIn</a>
                    </div>
                ',
                'default_values' => json_encode([
                    'facebook_url' => 'https://www.facebook.com/BridgeofHopeHbg/',
                    'instagram_url' => 'https://www.instagram.com/bridgeofhopehbg/',
                    'linkedin_url' => 'https://www.linkedin.com/company/bridge-of-hope-harrisburg-area/'
                ]),
            ],
        ];

        foreach ($blocks as $block) {
            TouchTemplateBlock::create($block);
        }
    }
}
