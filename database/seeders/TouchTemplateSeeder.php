<?php

namespace Database\Seeders;

use App\Models\TouchTemplate;
use Illuminate\Database\Seeder;

class TouchTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Donor Templates
            [
                'name' => 'Welcome New Donor',
                'subject' => 'Welcome to Bridge of Hope!',
                'html_content' => '<h1>Welcome to Bridge of Hope!</h1><p>We are so excited to have you join our community of donors making a difference in the lives of others.</p>',
                'text_content' => 'Welcome to Bridge of Hope! We are so excited to have you join our community of donors making a difference in the lives of others.',
                'target_lifecycle_stages' => ['donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],
            [
                'name' => 'Thank You for Your Donation',
                'subject' => 'Thank You for Your Generous Donation',
                'html_content' => '<h1>Thank You!</h1><p>Your generous donation will help us continue our mission of serving those in need.</p>',
                'text_content' => 'Thank You! Your generous donation will help us continue our mission of serving those in need.',
                'target_lifecycle_stages' => ['donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],

            // Volunteer Templates
            [
                'name' => 'Volunteer Information Packet',
                'subject' => 'Volunteer Information for Bridge of Hope',
                'html_content' => '<h1>Volunteer Information</h1><p>Thank you for your interest in volunteering with Bridge of Hope. Here is all the information you need to get started.</p>',
                'text_content' => 'Thank you for your interest in volunteering with Bridge of Hope. Here is all the information you need to get started.',
                'target_lifecycle_stages' => ['volunteer_active', 'volunteer_leader', 'volunteer_influencer'],
                'is_active' => true,
            ],
            [
                'name' => 'Welcome New Volunteer',
                'subject' => 'Welcome to the Bridge of Hope Volunteer Team',
                'html_content' => '<h1>Welcome to Our Volunteer Team!</h1><p>We are thrilled to have you join us as a volunteer. Your commitment to making a difference is truly appreciated.</p>',
                'text_content' => 'Welcome to Our Volunteer Team! We are thrilled to have you join us as a volunteer. Your commitment to making a difference is truly appreciated.',
                'target_lifecycle_stages' => ['volunteer_active', 'volunteer_leader', 'volunteer_influencer'],
                'is_active' => true,
            ],
            [
                'name' => 'Volunteer Welcome Kit',
                'subject' => 'Welcome to the Bridge of Hope Volunteer Team',
                'html_content' => '<h1>Welcome to the Team!</h1><p>We are thrilled to have you join our volunteer team. Here is your welcome kit with everything you need to know.</p>',
                'text_content' => 'Welcome to the Team! We are thrilled to have you join our volunteer team. Here is your welcome kit with everything you need to know.',
                'target_lifecycle_stages' => ['volunteer_active', 'volunteer_leader', 'volunteer_influencer'],
                'is_active' => true,
            ],

            // Mom Program Templates
            [
                'name' => 'Mom Program Application',
                'subject' => 'Bridge of Hope Mom Program Application',
                'html_content' => '<h1>Mom Program Application</h1><p>Thank you for your interest in our Mom Program. Please find the application details attached.</p>',
                'text_content' => 'Thank you for your interest in our Mom Program. Please find the application details attached.',
                'target_lifecycle_stages' => ['mom_active'],
                'is_active' => true,
            ],
            [
                'name' => 'Welcome to Mom Program',
                'subject' => 'Welcome to the Bridge of Hope Mom Program',
                'html_content' => '<h1>Welcome to the Mom Program!</h1><p>Congratulations on being accepted into our Mom Program. We look forward to supporting you on your journey.</p>',
                'text_content' => 'Congratulations on being accepted into our Mom Program. We look forward to supporting you on your journey.',
                'target_lifecycle_stages' => ['mom_active'],
                'is_active' => true,
            ],
            [
                'name' => 'Congratulations on Graduation',
                'subject' => 'Congratulations on Your Graduation!',
                'html_content' => '<h1>Congratulations!</h1><p>We are so proud of your accomplishments in completing the Mom Program. This is just the beginning of your success story!</p>',
                'text_content' => 'Congratulations! We are so proud of your accomplishments in completing the Mom Program. This is just the beginning of your success story!',
                'target_lifecycle_stages' => ['mom_graduate'],
                'is_active' => true,
            ],

            // Gala Templates
            [
                'name' => 'Gala Invitation',
                'subject' => 'You\'re Invited to the Bridge of Hope Annual Gala',
                'html_content' => '<h1>You\'re Invited!</h1><p>Join us for an evening of celebration and support at our annual Bridge of Hope Gala.</p>',
                'text_content' => 'Join us for an evening of celebration and support at our annual Bridge of Hope Gala.',
                'target_lifecycle_stages' => ['gala_candidate', 'donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],
            [
                'name' => 'Gala Registration Confirmation',
                'subject' => 'Your Gala Registration is Confirmed',
                'html_content' => '<h1>Registration Confirmed</h1><p>Thank you for registering for our annual Gala. We look forward to seeing you there!</p>',
                'text_content' => 'Thank you for registering for our annual Gala. We look forward to seeing you there!',
                'target_lifecycle_stages' => ['gala_candidate', 'donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],
            [
                'name' => 'Auction Winner Congratulations',
                'subject' => 'Congratulations on Your Auction Win!',
                'html_content' => '<h1>Congratulations!</h1><p>Thank you for your generous bid at our Gala auction. Here are the details about your winning item.</p>',
                'text_content' => 'Thank you for your generous bid at our Gala auction. Here are the details about your winning item.',
                'target_lifecycle_stages' => ['gala_candidate', 'donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],
            [
                'name' => 'Gala Volunteer Schedule',
                'subject' => 'Your Gala Volunteer Schedule',
                'html_content' => '<h1>Volunteer Schedule</h1><p>Thank you for volunteering at our annual Gala. Here is your schedule and assignment details.</p>',
                'text_content' => 'Thank you for volunteering at our annual Gala. Here is your schedule and assignment details.',
                'target_lifecycle_stages' => ['gala_candidate', 'donor_active', 'donor_influencer', 'donor_aggregator'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            TouchTemplate::create($template);
        }
    }
}
