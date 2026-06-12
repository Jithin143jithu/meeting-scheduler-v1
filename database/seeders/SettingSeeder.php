<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Meeting Scheduler V1', 'type' => 'system'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'system'],
            ['key' => 'email_from', 'value' => 'noreply@meetingscheduler.com', 'type' => 'system'],
            ['key' => 'booking_reminder_hours', 'value' => '24', 'type' => 'system'],
            ['key' => 'auto_confirm_bookings', 'value' => 'false', 'type' => 'system'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
