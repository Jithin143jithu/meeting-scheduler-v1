<?php

namespace Database\Seeders;

use App\Models\MeetingType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MeetingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'demo-user')->first();

        $types = [
            ['name' => '15 Minute Coffee Chat', 'duration' => 15, 'color' => '#FF6B6B'],
            ['name' => '30 Minute Meeting', 'duration' => 30, 'color' => '#4ECDC4'],
            ['name' => '1 Hour Consultation', 'duration' => 60, 'color' => '#45B7D1'],
            ['name' => '45 Minute Planning Session', 'duration' => 45, 'color' => '#FFA07A'],
        ];

        foreach ($types as $type) {
            MeetingType::create([
                'user_id' => $user->id,
                'name' => $type['name'],
                'description' => "A {$type['duration']} minute meeting",
                'duration' => $type['duration'],
                'location_type' => 'google_meet',
                'buffer_before' => 5,
                'buffer_after' => 5,
                'daily_limit' => 10,
                'advance_booking_days' => 30,
                'min_booking_notice' => 0,
                'is_active' => true,
                'color' => $type['color'],
                'slug' => Str::slug($type['name'] . '-' . uniqid()),
            ]);
        }
    }
}
