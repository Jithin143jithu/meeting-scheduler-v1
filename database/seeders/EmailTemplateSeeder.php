<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Booking Confirmation',
                'slug' => 'booking_confirmation',
                'template_type' => 'booking_confirmation',
                'subject' => 'Your meeting is confirmed',
                'body' => 'Your meeting with {{host_name}} has been confirmed for {{start_time}}',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Booking Cancellation',
                'slug' => 'booking_cancellation',
                'template_type' => 'booking_cancellation',
                'subject' => 'Your meeting has been cancelled',
                'body' => 'Your meeting scheduled for {{start_time}} has been cancelled',
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Meeting Reminder',
                'slug' => 'meeting_reminder',
                'template_type' => 'reminder',
                'subject' => 'Reminder: Your meeting is in 24 hours',
                'body' => 'This is a reminder about your meeting with {{host_name}} at {{start_time}}',
                'is_default' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
