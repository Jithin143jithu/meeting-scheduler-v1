# Meeting Scheduler V1 - Configuration Guide

## Environment Variables

### Application Settings

```env
APP_NAME="Meeting Scheduler"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://yourdomain.com
APP_TIMEZONE=UTC
```

### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=meeting_scheduler
DB_USERNAME=scheduler_user
DB_PASSWORD=secure_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### Cache Configuration

```env
CACHE_DRIVER=redis
CACHE_PREFIX=scheduler_
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Configuration

```env
QUEUE_CONNECTION=database
# For production use Redis
QUEUE_CONNECTION=redis
```

### Session Configuration

```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIES=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Meeting Scheduler"
```

### Google Calendar API

```env
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

---

## Config Files

### config/app.php

```php
return [
    'name' => env('APP_NAME', 'Meeting Scheduler'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    // ...
];
```

### config/database.php

```php
return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
    ],
];
```

### config/mail.php

```php
return [
    'default' => env('MAIL_MAILER', 'log'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
        ],
    ],
];
```

### config/queue.php

```php
return [
    'default' => env('QUEUE_CONNECTION', 'sync'),
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
        ],
    ],
];
```

---

## System Settings

Access via admin panel or database:

```php
app.name                          // Application name
app.timezone                      // Default timezone

booking.buffer_time               // Default buffer between meetings (minutes)
booking.max_advance_days          // How far ahead users can book (days)
booking.min_advance_minutes       // Minimum advance booking (minutes)
booking.duration_presets          // Available durations: [15, 30, 45, 60]

email.from_address                // From email address
email.from_name                   // From name

google_calendar.enabled           // Enable Google Calendar integration
google_calendar.auto_sync         // Auto sync events

rate_limit.bookings_per_hour      // Bookings per hour per IP
rate_limit.login_attempts         // Failed login attempts before lockout

session.timeout_minutes           // Session timeout
```

---

## Feature Flags

Enable/disable features:

```php
// config/features.php
return [
    'email_notifications' => env('ENABLE_EMAIL_NOTIFICATIONS', true),
    'google_calendar' => env('ENABLE_GOOGLE_CALENDAR', true),
    'activity_logging' => env('ENABLE_ACTIVITY_LOGGING', true),
    'rate_limiting' => env('ENABLE_RATE_LIMITING', true),
];
```

Use in code:

```php
if (config('features.email_notifications')) {
    // Send email
}
```

---

## Timezone Support

### Supported Timezones

All PHP supported timezones:
- America/New_York
- Europe/London
- Europe/Paris
- Asia/Tokyo
- Australia/Sydney
- Pacific/Auckland
- etc.

### Configure Default

```env
APP_TIMEZONE=UTC
```

### Convert Times

```php
$service = app(TimezoneConversionService::class);

// Convert to UTC
$utc = $service->convertToUTC(
    new DateTime('2025-06-15 14:00:00'),
    'America/New_York'
);

// Convert from UTC
$local = $service->convertFromUTC(
    $utc,
    'Europe/London'
);
```

---

## Email Templates

### Available Templates

1. **booking.created.host** - Host receives new booking
2. **booking.created.guest** - Guest receives confirmation
3. **booking.cancelled.host** - Host receives cancellation
4. **booking.cancelled.guest** - Guest receives cancellation
5. **booking.reminder.guest** - Guest receives reminder
6. **user.registered** - New user registration
7. **user.verify_email** - Email verification
8. **user.reset_password** - Password reset

### Template Variables

Available in all email templates:

```
{{ app_name }}
{{ app_url }}
{{ user_name }}
{{ user_email }}
{{ booking_date }}
{{ booking_time }}
{{ booking_duration }}
{{ meeting_type }}
{{ meeting_link }}
{{ guest_name }}
{{ guest_email }}
{{ host_name }}
{{ host_email }}
{{ booking_link }}
{{ reset_link }}
{{ verification_link }}
```

### Customize Templates

Edit via admin panel or database:

```php
$template = EmailTemplate::where('template_key', 'booking.created.host')->first();
$template->update([
    'subject' => 'New Booking: {{ booking_date }} at {{ booking_time }}',
    'body' => '<h1>You have a new booking!</h1>'
]);
```

---

## Rate Limiting

### Configure Limits

```php
// config/rate-limit.php
return [
    'enabled' => env('RATE_LIMIT_ENABLED', true),
    
    'bookings' => [
        'per_hour' => env('RATE_LIMIT_BOOKINGS_PER_HOUR', 60),
        'per_day' => env('RATE_LIMIT_BOOKINGS_PER_DAY', 1000),
    ],
    
    'login' => [
        'attempts' => env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
        'minutes' => 15,
    ],
    
    'api' => [
        'requests_per_minute' => 60,
        'requests_per_hour' => 1000,
    ],
];
```

### Apply Rate Limiting

```php
// In middleware or controller
RateLimiter::for('booking', function (Request $request) {
    return Limit::perHour(config('rate-limit.bookings.per_hour'));
});
```

---

## Security Configuration

### CORS Settings

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### CSRF Configuration

```php
// config/session.php
return [
    'http_only' => true,
    'same_site' => 'lax', // or 'strict', 'none'
];
```

### Security Headers

```php
// In middleware
return response()
    ->header('X-Content-Type-Options', 'nosniff')
    ->header('X-Frame-Options', 'DENY')
    ->header('X-XSS-Protection', '1; mode=block')
    ->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
```

---

## Performance Tuning

### Database Optimization

```php
// Use query caching
Cache::remember('user_availability_' . $userId, 3600, function () {
    return Availability::where('user_id', $userId)->get();
});

// Eager load relationships
Booking::with(['user', 'meetingType', 'bookingNotes'])->get();

// Use select specific columns
User::select('id', 'name', 'email')->get();
```

### Redis Caching

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Queue Jobs

Use for long-running tasks:

```php
SendEmailJob::dispatch($booking);
SyncGoogleCalendarJob::dispatch($user);
```

---

## Monitoring & Logging

### Log Configuration

```php
// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack'],
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
    ],
];
```

### Log Levels

```
emergency | alert | critical | error | warning | notice | info | debug
```

### Usage

```php
Log::info('Booking created', ['booking_id' => $booking->id]);
Log::error('Email failed', ['exception' => $e]);
```

---

## Backup Configuration

### Database Backup

```bash
# Daily backup at 2 AM
0 2 * * * mysqldump -u user -p database > /backups/meeting_scheduler_$(date +\%Y\%m\%d).sql
```

### File Backup

```bash
# Weekly backup of uploads
0 3 0 * * tar -czf /backups/uploads_$(date +\%Y\%m\%d).tar.gz /var/www/meeting-scheduler/storage/app/uploads
```
