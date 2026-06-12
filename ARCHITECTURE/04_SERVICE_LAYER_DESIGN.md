# Meeting Scheduler V1 - Service Layer Design

## Overview

The Service Layer contains all business logic for the application. Services handle:
- Booking validation and creation
- Availability slot generation
- Email sending
- Google Calendar synchronization
- User authentication
- Timezone conversion
- Slot locking for double-booking prevention

---

## Service Architecture

### 1. Authentication Services

#### AuthService.php
```
Purpose: Handle user authentication logic

Methods:
- register(array $data): User
  - Validate user data
  - Hash password
  - Create user record
  - Generate verification token
  - Dispatch UserRegistered event
  
- login(string $email, string $password): array
  - Validate credentials
  - Update last_login_at
  - Return authentication token (Sanctum)
  
- logout(User $user): bool
  - Revoke all tokens
  - Clear session
  
- verifyEmail(string $token): bool
  - Validate token & expiry
  - Mark user as verified
  - Delete token
```

#### PasswordResetService.php
```
Purpose: Handle password reset flow

Methods:
- sendResetLink(string $email): bool
  - Find user by email
  - Generate reset token
  - Send reset email via job
  - Log activity
  
- resetPassword(string $token, string $password): bool
  - Validate token & expiry
  - Hash new password
  - Update user
  - Clear all tokens (force re-login)
  - Log activity
```

#### EmailVerificationService.php
```
Purpose: Handle email verification

Methods:
- sendVerificationEmail(User $user): void
  - Generate verification token
  - Dispatch email job
  
- verifyEmail(string $token): bool
  - Validate token
  - Mark user verified
```

---

### 2. Booking Services

#### BookingService.php
```
Purpose: Main booking business logic

Methods:
- createBooking(array $data): Booking
  - Validate booking data
  - Check slot availability
  - Lock slot (prevent double booking)
  - Create booking record
  - Dispatch BookingCreated event
  - Queue confirmation emails
  - Sync to Google Calendar (if enabled)
  
- updateBooking(Booking $booking, array $data): Booking
  - Validate changes
  - Check new slot availability
  - Update booking
  - Dispatch BookingRescheduled event
  - Queue emails
  - Update Google Calendar event
  
- cancelBooking(Booking $booking, string $reason = null): bool
  - Update booking status
  - Release slot
  - Dispatch BookingCancelled event
  - Queue cancellation emails
  - Delete/cancel Google Calendar event
  
- completeBooking(Booking $booking): bool
  - Mark as completed
  - Update stats
  - Trigger post-meeting actions
```

#### BookingValidationService.php
```
Purpose: Validate booking requests

Methods:
- validateBookingData(array $data): array
  - Validate guest name, email, phone
  - Validate booking date/time
  - Validate timezone
  - Return validation result
  
- checkSlotAvailability(User $user, MeetingType $type, DateTime $startTime, DateTime $endTime): bool
  - Check user availability on that date
  - Check for existing bookings
  - Check availability exceptions
  - Check meeting limits
  - Return availability status
  
- validateMinimumAdvanceBooking(User $user, DateTime $bookingDate): bool
  - Check min_advance_booking_minutes setting
  
- validateMaximumAdvanceBooking(User $user, DateTime $bookingDate): bool
  - Check max_advance_booking_days setting
```

#### SlotLockingService.php
```
Purpose: Prevent double-booking with slot locking

Methods:
- lockSlot(User $user, MeetingType $type, DateTime $startTime, int $durationSeconds = 300): bool
  - Set locked_until timestamp (5 minutes default)
  - Use UNIQUE constraint as backup
  - Return lock success/failure
  
- unlockSlot(Booking $booking): bool
  - Clear locked_until
  
- isSlotLocked(User $user, MeetingType $type, DateTime $startTime): bool
  - Check if locked_until > now()
  
- cleanExpiredLocks(): int
  - Delete expired locks
  - Run via scheduled task
```

#### TimezoneConversionService.php
```
Purpose: Handle timezone conversions

Methods:
- convertToUTC(DateTime $dateTime, string $timezone): DateTime
  - Convert local time to UTC for storage
  
- convertFromUTC(DateTime $utcDateTime, string $timezone): DateTime
  - Convert UTC to user's timezone for display
  
- getAvailableSlots(User $user, DateTime $date, string $timezone): array
  - Get user's availability in their timezone
  - Return time slots
  
- isValidTimezone(string $timezone): bool
  - Validate timezone string
```

#### BookingConfirmationService.php
```
Purpose: Handle booking confirmation

Methods:
- sendConfirmation(Booking $booking): void
  - Queue confirmation email for host
  - Queue confirmation email for guest
  - Generate booking link
  - Log activity
  
- generateBookingLink(Booking $booking): string
  - Create public URL with booking token
```

---

### 3. Availability Services

#### AvailabilityService.php
```
Purpose: Manage user availability schedule

Methods:
- updateAvailability(User $user, array $data): Collection
  - Delete old availability
  - Create new availability slots
  - Validate hours
  - Log activity
  
- getAvailability(User $user): Collection
  - Get weekly schedule
  - Order by day of week
  
- deleteAvailability(Availability $availability): bool
  - Delete availability slot
  - Log activity
  
- validateAvailabilityHours(string $startTime, string $endTime): bool
  - Validate time format (HH:MM)
  - Check start < end
```

#### AvailabilitySlotService.php
```
Purpose: Generate available time slots

Methods:
- getAvailableSlots(User $user, DateTime $date, MeetingType $type): Collection
  - Get user's availability for that day
  - Generate slots based on duration
  - Remove booked slots
  - Remove exception blocks
  - Apply buffer time
  - Return collection of available slots
  
- generateSlotsForDay(DateTime $day, Availability $availability, int $slotDuration): array
  - Split availability into slots
  - Consider buffer time
  - Return array of [start_time, end_time]
  
- getNextAvailableSlot(User $user, MeetingType $type): ?DateTime
  - Find next available slot from now
```

#### AvailabilityExceptionService.php
```
Purpose: Manage availability exceptions (holidays, blocks)

Methods:
- addException(User $user, array $data): AvailabilityException
  - Create holiday/vacation/block
  - Validate date range
  - Log activity
  
- updateException(AvailabilityException $exception, array $data): AvailabilityException
  - Update exception
  - Log activity
  
- deleteException(AvailabilityException $exception): bool
  - Delete exception
  - Log activity
  
- isDateBlocked(User $user, DateTime $date): bool
  - Check if date is in any exception
  
- getExceptionsForDateRange(User $user, DateTime $startDate, DateTime $endDate): Collection
  - Get exceptions in date range
```

#### WorkingHoursService.php
```
Purpose: Calculate working hours

Methods:
- getTotalWorkingHours(User $user, DateTime $startDate, DateTime $endDate): int
  - Calculate total hours excluding weekends/exceptions
  
- getWorkingHoursForDay(User $user, DateTime $date): int
  - Get hours for specific day
  - Return 0 if no availability or exception
```

---

### 4. Meeting Type Services

#### MeetingTypeService.php
```
Purpose: Manage meeting types

Methods:
- createMeetingType(User $user, array $data): MeetingType
  - Validate meeting type data
  - Create record
  - Log activity
  
- updateMeetingType(MeetingType $type, array $data): MeetingType
  - Update record
  - Log activity
  
- deleteMeetingType(MeetingType $type): bool
  - Delete record
  - Log activity
  
- reorderMeetingTypes(User $user, array $typeIds): bool
  - Update position field
  
- validateMeetingTypeData(array $data): array
  - Validate duration, location type
  - Check buffer time >= 0
```

---

### 5. Email Services

#### EmailService.php
```
Purpose: Send emails with templates

Methods:
- sendBookingConfirmation(Booking $booking): void
  - Load template: booking.created.host
  - Replace variables
  - Send to host
  - Queue for guest
  
- sendBookingCancellation(Booking $booking): void
  - Load templates: booking.cancelled.host/guest
  - Send to both
  
- sendBookingReminder(Booking $booking): void
  - Load template: booking.reminder.guest
  - Send reminder before meeting
  
- sendPasswordReset(User $user, string $token): void
  - Load template: user.reset_password
  - Send reset link
  
- sendVerificationEmail(User $user, string $token): void
  - Load template: user.verify_email
  - Send verification link
```

#### EmailTemplateService.php
```
Purpose: Manage email templates

Methods:
- renderTemplate(string $templateKey, array $variables): string
  - Load template from database
  - Replace {{variable}} with values
  - Return rendered HTML
  
- getAllTemplates(): Collection
  - Get all templates
  
- updateTemplate(string $templateKey, array $data): EmailTemplate
  - Update template subject/body
  
- getDefaultTemplates(): array
  - Return default templates array
  
- seedDefaults(): void
  - Seed database with default templates
```

#### NotificationService.php
```
Purpose: Create in-app notifications

Methods:
- notifyBookingCreated(Booking $booking): void
  - Create notification for host
  
- notifyBookingReminder(Booking $booking): void
  - Create reminder notification
  
- createNotification(User $user, string $type, string $title, string $message, ?array $data): Notification
  - Create notification record
  - Return notification
  
- markAsRead(Notification $notification): bool
  - Mark notification read
```

---

### 6. Google Calendar Services

#### GoogleCalendarService.php
```
Purpose: Main Google Calendar integration

Methods:
- connectGoogleAccount(User $user, string $authCode): GoogleAccount
  - Exchange auth code for tokens
  - Store tokens (encrypted)
  - Create GoogleAccount record
  - Log activity
  
- disconnectGoogleAccount(GoogleAccount $account): bool
  - Revoke tokens
  - Delete account record
  
- isConnected(User $user): bool
  - Check if user has active Google account
  
- getConnectedAccount(User $user): ?GoogleAccount
  - Get user's connected Google account
```

#### CalendarSyncService.php
```
Purpose: Sync with Google Calendar

Methods:
- syncCalendarEvents(GoogleAccount $account): int
  - Fetch events from Google Calendar
  - Find conflicts with bookings
  - Create availability exceptions for busy times
  - Return count of synced events
  
- getCalendarEvents(GoogleAccount $account, DateTime $startDate, DateTime $endDate): array
  - Fetch events for date range
  - Parse event times
  - Return array of events
```

#### GoogleOAuthService.php
```
Purpose: Handle Google OAuth flow

Methods:
- getAuthorizationUrl(User $user): string
  - Generate Google OAuth authorization URL
  - Include state parameter
  
- handleCallback(User $user, string $code, string $state): GoogleAccount
  - Verify state parameter
  - Exchange code for tokens
  - Create/update GoogleAccount
  - Queue sync job
```

#### EventConflictService.php
```
Purpose: Detect calendar conflicts

Methods:
- detectConflicts(GoogleAccount $account, DateTime $startTime, DateTime $endTime): bool
  - Check for conflicting events
  - Return true if conflict exists
  
- findConflictingEvents(GoogleAccount $account, DateTime $startTime, DateTime $endTime): array
  - Get list of conflicting events
```

---

### 7. User Services

#### UserService.php
```
Purpose: General user operations

Methods:
- getUserByUsername(string $username): ?User
  - Find user by public username
  
- isUsernameAvailable(string $username): bool
  - Check if username not taken
  
- getPublicProfile(User $user): array
  - Return profile data for public display
```

#### ProfileService.php
```
Purpose: User profile management

Methods:
- updateProfile(User $user, array $data): User
  - Update first_name, last_name, bio, avatar, phone, country, timezone
  - Validate timezone
  - Log activity
  
- changePassword(User $user, string $oldPassword, string $newPassword): bool
  - Verify old password
  - Hash new password
  - Update user
  - Revoke all tokens (force re-login)
  - Log activity
  
- updateTimezone(User $user, string $timezone): User
  - Validate timezone
  - Update user
  - Log activity
```

#### UserManagementService.php
```
Purpose: Admin user management

Methods:
- createUser(array $data): User
  - Admin creates user
  - Log activity
  
- updateUser(User $user, array $data): User
  - Admin updates user
  - Log activity
  
- deleteUser(User $user): bool
  - Admin deletes user
  - Soft delete
  - Log activity
  
- activateUser(User $user): bool
  - Activate user account
  
- deactivateUser(User $user): bool
  - Deactivate user account
```

---

### 8. Admin Services

#### AdminService.php
```
Purpose: General admin operations

Methods:
- getDashboardStats(): array
  - Total users
  - Total bookings
  - Upcoming meetings
  - Recent registrations
  - Return as array
```

#### SettingsService.php
```
Purpose: System settings management

Methods:
- getSetting(string $key, $default = null): mixed
  - Get setting value
  - Return as correct type (string/int/bool/json)
  
- setSetting(string $key, $value): Setting
  - Update/create setting
  - Log activity
  
- getAllSettings(): Collection
  - Get all settings
  
- resetToDefaults(): bool
  - Reset all settings to defaults
```

#### DashboardStatsService.php
```
Purpose: Calculate dashboard statistics

Methods:
- getTotalUsers(): int
- getTotalBookings(): int
- getUpcomingBookings(): int
- getCompletedBookings(): int
- getCancelledBookings(): int
- getRecentRegistrations(int $days = 30): Collection
- getBookingsThisMonth(): int
- getAverageBookingDuration(): float
```

#### ActivityLogService.php
```
Purpose: Track and retrieve activity logs

Methods:
- logAction(User $user, string $action, string $actionType, string $resourceType, int $resourceId, ?array $oldValues, ?array $newValues): ActivityLog
  - Create activity log entry
  - Include IP, user agent
  
- getActivityLogs(array $filters = []): Collection
  - Get logs filtered by user, action, resource
  - Order by created_at desc
  
- exportActivityLogs(array $filters = []): string
  - Export logs as CSV
```

---

## Service Binding

Register services in `RepositoryServiceProvider`:

```php
public function register()
{
    // Auth Services
    $this->app->bind(AuthService::class, AuthService::class);
    $this->app->bind(PasswordResetService::class, PasswordResetService::class);
    
    // Booking Services
    $this->app->bind(BookingService::class, BookingService::class);
    $this->app->bind(BookingValidationService::class, BookingValidationService::class);
    $this->app->bind(SlotLockingService::class, SlotLockingService::class);
    
    // ... register all services
}
```

---

## Usage Example

```php
// In Controller
public function __construct(
    private BookingService $bookingService,
    private AvailabilitySlotService $slotService
) {}

public function store(StoreBookingRequest $request)
{
    // Get available slots
    $slots = $this->slotService->getAvailableSlots(
        $request->user(),
        $request->get('date'),
        $request->get('meeting_type')
    );
    
    // Create booking
    $booking = $this->bookingService->createBooking($request->validated());
    
    return response()->json($booking);
}
```

---

## Key Principles

1. **Single Responsibility**: Each service handles one domain
2. **Dependency Injection**: Services receive dependencies in constructor
3. **Repositories**: Services use repositories for data access
4. **Events**: Services dispatch events for async processing
5. **Transactions**: Database changes wrapped in transactions
6. **Logging**: All critical operations logged
7. **Validation**: Input validation in services or requests
8. **Exception Handling**: Custom exceptions for specific errors
