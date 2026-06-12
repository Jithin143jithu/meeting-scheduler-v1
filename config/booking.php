<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    */

    'buffer_time' => env('BOOKING_BUFFER_TIME', 15),
    
    'max_advance_booking_days' => env('BOOKING_MAX_ADVANCE_DAYS', 90),
    
    'min_advance_booking_minutes' => env('BOOKING_MIN_ADVANCE_MINUTES', 1440),
    
    'duration_presets' => [
        15,
        30,
        45,
        60,
        90,
        120,
    ],
    
    'location_types' => [
        'google_meet' => 'Google Meet',
        'phone_call' => 'Phone Call',
        'custom_url' => 'Custom URL',
        'in_person' => 'In Person',
    ],
    
    'slot_lock_duration_minutes' => 5,
    
    'meeting_limit_per_day' => null,
    
    'timezone_conversion_enabled' => true,
    
    'double_booking_prevention' => true,
];
