<?php

namespace Database\Seeders;

use App\Models\TouchTemplate;
use Illuminate\Database\Seeder;

class TouchTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome New Donor',
                'subject' => 'Welcome to Our Community, {{contact.first_name}}!',
                'target_lifecycle_stages' => ['donor_active', 'donor_influencer', 'donor_aggregator'],
                'html_content' => '
                    <h2>Welcome, {{contact.first_name}}!</h2>
                    <p>Thank you for becoming a donor in our community. Your generosity will help make a real difference in the lives of local families.</p>
                    <p>As a donor, you\'ll receive regular updates about how your support is impacting our community and invitations to special events.</p>
                    <p>If you have any questions, feel free to reach out to us!</p>
                    <p>Best regards,<br>The Team</p>
                ',
            ],
            [
                'name' => 'Gala Invitation',
                'subject' => '{{contact.first_name}}, You\'re Invited to Our Annual Gala!',
                'target_lifecycle_stages' => ['gala_candidate', 'donor_active', 'donor_influencer', 'donor_aggregator'],
                'html_content' => '
                    <h2>You\'re Invited!</h2>
                    <p>Dear {{contact.full_name}},</p>
                    <p>We\'re excited to invite you to our annual fundraising gala. Join us for an evening of celebration and community as we share stories of impact and vision for the future.</p>
                    <p>Your presence will help make this evening truly special.</p>
                    <h3>Event Details:</h3>
                    <ul>
                        <li>Date: [Event Date]</li>
                        <li>Time: [Event Time]</li>
                        <li>Location: [Venue]</li>
                    </ul>
                    <p>Please RSVP by [date].</p>
                ',
            ],
            [
                'name' => 'Welcome New Neighbor',
                'subject' => 'Welcome to the Neighborhood, {{contact.first_name}}!',
                'target_lifecycle_stages' => ['neighbor_active', 'neighbor_leader', 'neighbor_influencer'],
                'html_content' => '
                    <h2>Welcome to Our Neighborhood!</h2>
                    <p>Dear {{contact.first_name}},</p>
                    <p>We\'re thrilled to welcome you as a new neighbor volunteer! Your commitment to serving our community is truly appreciated.</p>
                    <p>Here\'s what you can expect:</p>
                    <ul>
                        <li>Regular updates about neighborhood activities</li>
                        <li>Opportunities to connect with other neighbors</li>
                        <li>Resources to help you make an impact</li>
                    </ul>
                    <p>If you have any questions, don\'t hesitate to reach out!</p>
                ',
            ],
            [
                'name' => 'Mom Program Welcome',
                'subject' => 'Welcome to Our Program, {{contact.first_name}}!',
                'target_lifecycle_stages' => ['mom_active'],
                'html_content' => '
                    <h2>Welcome to Our Community!</h2>
                    <p>Dear {{contact.first_name}},</p>
                    <p>We\'re so glad you\'ve joined our program! We\'re here to support you on your journey and provide resources to help you succeed.</p>
                    <p>What\'s next:</p>
                    <ul>
                        <li>You\'ll be paired with a dedicated mentor</li>
                        <li>Access to our resource library</li>
                        <li>Regular check-ins and support sessions</li>
                    </ul>
                    <p>We\'re looking forward to walking alongside you!</p>
                ',
            ],
            [
                'name' => 'Mom Graduate Congratulations',
                'subject' => 'Congratulations on Your Graduation, {{contact.first_name}}!',
                'target_lifecycle_stages' => ['mom_graduate'],
                'html_content' => '
                    <h2>Congratulations, Graduate!</h2>
                    <p>Dear {{contact.first_name}},</p>
                    <p>We\'re incredibly proud to celebrate your graduation from our program! Your dedication and hard work have been inspiring to witness.</p>
                    <p>While this marks the completion of the program, remember that you\'ll always be part of our community. We\'d love to stay connected and hear about your continued success.</p>
                    <p>Congratulations again on this significant achievement!</p>
                    <p>With pride and best wishes,<br>The Team</p>
                ',
            ],
        ];

        foreach ($templates as $template) {
            TouchTemplate::create([
                'name' => $template['name'],
                'subject' => $template['subject'],
                'html_content' => $template['html_content'],
                'target_lifecycle_stages' => $template['target_lifecycle_stages'],
                'is_active' => true,
            ]);
        }
    }
}
