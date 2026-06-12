<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Calendar Configuration
    |--------------------------------------------------------------------------
    */

    'enabled' => env('ENABLE_GOOGLE_CALENDAR', true),
    
    'client_id' => env('GOOGLE_CLIENT_ID'),
    
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    
    'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    
    'scopes' => [
        'https://www.googleapis.com/auth/calendar.readonly',
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events',
    ],
    
    'auto_sync' => env('GOOGLE_CALENDAR_AUTO_SYNC', true),
    
    'sync_interval_minutes' => 60,
    
    'calendar_id' => 'primary',
    
    'event_summary' => 'Meeting with {{ guest_name }}',
    
    'event_description' => 'Meeting Type: {{ meeting_type }}\nGuest: {{ guest_name }} ({{ guest_email }})',
];
