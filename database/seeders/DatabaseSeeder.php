<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MeetingTypeSeeder::class,
            AvailabilitySeeder::class,
            BookingSeeder::class,
            EmailTemplateSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
