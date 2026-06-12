<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'demo-user')->first();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($days as $day) {
            Availability::create([
                'user_id' => $user->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_available' => true,
            ]);
        }

        foreach (['saturday', 'sunday'] as $day) {
            Availability::create([
                'user_id' => $user->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_available' => false,
            ]);
        }
    }
}
