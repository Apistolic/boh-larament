<?php

namespace Database\Seeders;

use App\Models\TouchTemplateLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TouchTemplateLayoutsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing layouts
        DB::table('touch_template_layouts')->delete();

        $layouts = [
            [
                'name' => 'Bridge of Hope Standard',
                'slug' => 'boh-standard',
                'type' => TouchTemplateLayout::TYPE_EMAIL,
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                        <style>
                            /* Bridge of Hope Colors */
                            :root {
                                --boh-blue: #2A4B9B;
                                --boh-light-blue: #7C9AC7;
                                --boh-orange: #F15A29;
                                --boh-gray: #58595B;
                                --boh-light-gray: #F7F7F7;
                            }
                        </style>
                    </head>
                    <body style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: var(--boh-light-gray);">
                        <div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
                            <!-- Header with Logo -->
                            {{ block:logo-header }}
                            
                            <!-- Hero Section -->
                            {{ block:hero }}
                            
                            <!-- Main Content Area -->
                            <div style="padding: 30px; color: var(--boh-gray);">
                                {{ block:content }}
                            </div>

                            <!-- Call to Action -->
                            {{ block:cta }}

                            <!-- Footer Area -->
                            <div style="background-color: var(--boh-light-gray); padding: 30px;">
                                {{ block:footer }}
                                {{ block:social_links }}
                            </div>
                        </div>
                    </body>
                    </html>
                ',
            ],
            [
                'name' => 'Simple Text',
                'slug' => 'simple-text',
                'type' => TouchTemplateLayout::TYPE_SMS,
                'html_content' => '{{ block:content }}',
                'text_content' => '{{ block:content }}',
            ],
        ];

        foreach ($layouts as $layout) {
            TouchTemplateLayout::create($layout);
        }
    }
}
