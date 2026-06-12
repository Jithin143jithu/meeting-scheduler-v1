<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    */

    'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
    
    'from_name' => env('MAIL_FROM_NAME', 'Meeting Scheduler'),
    
    'templates' => [
        'booking.created.host',
        'booking.created.guest',
        'booking.cancelled.host',
        'booking.cancelled.guest',
        'booking.rescheduled.host',
        'booking.rescheduled.guest',
        'booking.reminder.guest',
        'user.registered',
        'user.verify_email',
        'user.reset_password',
    ],
    
    'queue_emails' => true,
    
    'queue_name' => 'emails',
    
    'retry_after' => 60,
    
    'max_attempts' => 3,
];
