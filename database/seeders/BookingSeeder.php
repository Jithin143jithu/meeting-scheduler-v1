<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\MeetingType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'demo-user')->first();
        $meetingTypes = MeetingType::where('user_id', $user->id)->get();

        $statuses = ['pending', 'confirmed', 'cancelled'];
        $startDate = Carbon::now()->addDays(1);

        for ($i = 0; $i < 20; $i++) {
            $meetingType = $meetingTypes->random();
            $slotStart = $startDate->copy()->setHour(9 + ($i % 8))->setMinute(0);
            $slotEnd = $slotStart->copy()->addMinutes($meetingType->duration);

            Booking::create([
                'user_id' => $user->id,
                'meeting_type_id' => $meetingType->id,
                'guest_name' => "Guest $i",
                'guest_email' => "guest$i@example.com",
                'guest_phone' => '555-000-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'timezone' => 'UTC',
                'status' => $statuses[array_rand($statuses)],
                'payment_status' => 'unpaid',
                'confirmed_at' => now(),
            ]);
        }
    }
}
