# Meeting Scheduler V1 - Repository Design

## Overview

Repositories provide a clean abstraction layer for database queries. They encapsulate all database access logic and return model instances or collections.

---

## Repository Pattern

### Base Repository

```php
// app/Repositories/BaseRepository.php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->get();
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): \Illuminate\Pagination\Paginator
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    protected function query()
    {
        return $this->model->query();
    }
}
```

---

## Concrete Repositories

### 1. UserRepository

```php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->query()
            ->where('email', $email)
            ->first();
    }

    /**
     * Find user by username (public profile)
     */
    public function findByUsername(string $username): ?User
    {
        return $this->query()
            ->where('username', $username)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if username is available
     */
    public function isUsernameAvailable(string $username, ?int $excludeId = null): bool
    {
        $query = $this->query()
            ->where('username', $username);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Get active users
     */
    public function getActive(array $columns = ['*']): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->select($columns)
            ->get();
    }

    /**
     * Get verified users
     */
    public function getVerified(): Collection
    {
        return $this->query()
            ->where('is_verified', true)
            ->get();
    }

    /**
     * Get recent registrations
     */
    public function getRecentRegistrations(int $days = 30, int $limit = 10): Collection
    {
        return $this->query()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get admins
     */
    public function getAdmins(): Collection
    {
        return $this->query()
            ->where('role', 'admin')
            ->get();
    }

    /**
     * Get users by timezone
     */
    public function findByTimezone(string $timezone): Collection
    {
        return $this->query()
            ->where('timezone', $timezone)
            ->get();
    }
}
```

### 2. BookingRepository

```php
// app/Repositories/BookingRepository.php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $booking)
    {
        parent::__construct($booking);
    }

    /**
     * Find booking by token (for guests)
     */
    public function findByToken(string $token): ?Booking
    {
        return $this->query()
            ->where('booking_token', $token)
            ->with(['user', 'meetingType'])
            ->first();
    }

    /**
     * Get bookings for user (host)
     */
    public function getForUser(int $userId, array $columns = ['*']): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->select($columns)
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
    }

    /**
     * Get upcoming bookings for user
     */
    public function getUpcoming(int $userId, int $limit = 10): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('booking_date', '>=', today())
            ->where('status', 'confirmed')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit($limit)
            ->with('meetingType')
            ->get();
    }

    /**
     * Get completed bookings for user
     */
    public function getCompleted(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('booking_date', 'desc')
            ->get();
    }

    /**
     * Get bookings by status
     */
    public function getByStatus(int $userId, string $status): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check slot availability
     */
    public function isSlotAvailable(int $userId, int $meetingTypeId, string $bookingDate, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $query = $this->query()
            ->where('user_id', $userId)
            ->where('meeting_type_id', $meetingTypeId)
            ->where('booking_date', $bookingDate)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    /**
     * Get bookings for date range
     */
    public function getForDateRange(int $userId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('booking_date', '>=', $startDate->toDateString())
            ->where('booking_date', '<=', $endDate->toDateString())
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get today's bookings
     */
    public function getToday(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('booking_date', today()->toDateString())
            ->where('status', 'confirmed')
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Get guest bookings by email
     */
    public function findByGuestEmail(string $email): Collection
    {
        return $this->query()
            ->where('guest_email', $email)
            ->orderBy('booking_date', 'desc')
            ->get();
    }

    /**
     * Count bookings by status
     */
    public function countByStatus(int $userId, string $status): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->count();
    }
}
```

### 3. AvailabilityRepository

```php
// app/Repositories/AvailabilityRepository.php

namespace App\Repositories;

use App\Models\Availability;

class AvailabilityRepository extends BaseRepository
{
    public function __construct(Availability $availability)
    {
        parent::__construct($availability);
    }

    /**
     * Get availability for user
     */
    public function getForUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('day_of_week', 'asc')
            ->get();
    }

    /**
     * Get availability for specific day
     */
    public function getForDay(int $userId, int $dayOfWeek): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time', 'asc')
            ->get();
    }

    /**
     * Delete all availability for user
     */
    public function deleteForUser(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Check if user has availability configured
     */
    public function hasAvailability(int $userId): bool
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->count() > 0;
    }
}
```

### 4. AvailabilityExceptionRepository

```php
// app/Repositories/AvailabilityExceptionRepository.php

namespace App\Repositories;

use App\Models\AvailabilityException;
use Carbon\Carbon;

class AvailabilityExceptionRepository extends BaseRepository
{
    public function __construct(AvailabilityException $exception)
    {
        parent::__construct($exception);
    }

    /**
     * Get exceptions for user
     */
    public function getForUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('exception_date', 'asc')
            ->get();
    }

    /**
     * Check if date is blocked
     */
    public function isDateBlocked(int $userId, Carbon $date): bool
    {
        $dateString = $date->toDateString();

        return $this->query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($dateString) {
                $q->where('exception_date', $dateString)
                  ->orWhere(function ($q) use ($dateString) {
                      $q->where('start_date', '<=', $dateString)
                        ->where('end_date', '>=', $dateString);
                  });
            })
            ->count() > 0;
    }

    /**
     * Get exceptions for date range
     */
    public function getForDateRange(int $userId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($q) use ($startDate, $endDate) {
                    $q->where('exception_date', '>=', $startDate->toDateString())
                      ->where('exception_date', '<=', $endDate->toDateString());
                })
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate->toDateString())
                      ->where('end_date', '>=', $startDate->toDateString());
                });
            })
            ->orderBy('start_date', 'asc')
            ->get();
    }
}
```

### 5. MeetingTypeRepository

```php
// app/Repositories/MeetingTypeRepository.php

namespace App\Repositories;

use App\Models\MeetingType;

class MeetingTypeRepository extends BaseRepository
{
    public function __construct(MeetingType $meetingType)
    {
        parent::__construct($meetingType);
    }

    /**
     * Get meeting types for user
     */
    public function getForUser(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('position', 'asc')
            ->get();
    }

    /**
     * Find by slug for user
     */
    public function findBySlug(int $userId, string $slug): ?MeetingType
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if slug is available for user
     */
    public function isSlugAvailable(int $userId, string $slug, ?int $excludeId = null): bool
    {
        $query = $this->query()
            ->where('user_id', $userId)
            ->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Get inactive meeting types
     */
    public function getInactive(int $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_active', false)
            ->get();
    }
}
```

### 6. EmailTemplateRepository

```php
// app/Repositories/EmailTemplateRepository.php

namespace App\Repositories;

use App\Models\EmailTemplate;

class EmailTemplateRepository extends BaseRepository
{
    public function __construct(EmailTemplate $template)
    {
        parent::__construct($template);
    }

    /**
     * Find by template key
     */
    public function findByKey(string $key): ?EmailTemplate
    {
        return $this->query()
            ->where('template_key', $key)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get active templates
     */
    public function getActive(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->orderBy('template_key', 'asc')
            ->get();
    }
}
```

### 7. SettingRepository

```php
// app/Repositories/SettingRepository.php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    public function __construct(Setting $setting)
    {
        parent::__construct($setting);
    }

    /**
     * Get setting by key
     */
    public function getByKey(string $key)
    {
        $setting = $this->query()
            ->where('setting_key', $key)
            ->first();

        if (!$setting) {
            return null;
        }

        return $this->parseValue($setting->setting_value, $setting->value_type);
    }

    /**
     * Set/update setting
     */
    public function set(string $key, $value, string $type = 'string'): Setting
    {
        return $this->query()
            ->updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $this->serializeValue($value), 'value_type' => $type]
            );
    }

    /**
     * Get all settings
     */
    public function getAllSettings(): array
    {
        return $this->query()
            ->get()
            ->mapWithKeys(fn($setting) => [
                $setting->setting_key => $this->parseValue($setting->setting_value, $setting->value_type)
            ])
            ->toArray();
    }

    /**
     * Parse value based on type
     */
    private function parseValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Serialize value for storage
     */
    private function serializeValue($value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return (string) $value;
    }
}
```

### 8. GoogleAccountRepository

```php
// app/Repositories/GoogleAccountRepository.php

namespace App\Repositories;

use App\Models\GoogleAccount;

class GoogleAccountRepository extends BaseRepository
{
    public function __construct(GoogleAccount $account)
    {
        parent::__construct($account);
    }

    /**
     * Get account for user
     */
    public function getForUser(int $userId): ?GoogleAccount
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get accounts needing sync
     */
    public function getNeedingSync(): Collection
    {
        return $this->query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('last_synced_at', null)
                  ->orWhere('last_synced_at', '<', now()->subHours(1));
            })
            ->get();
    }
}
```

### 9. ActivityLogRepository

```php
// app/Repositories/ActivityLogRepository.php

namespace App\Repositories;

use App\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    public function __construct(ActivityLog $log)
    {
        parent::__construct($log);
    }

    /**
     * Get logs for user
     */
    public function getForUser(int $userId, int $limit = 100): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get logs by action
     */
    public function getByAction(string $action, int $limit = 100): Collection
    {
        return $this->query()
            ->where('action', $action)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get logs by resource
     */
    public function getByResource(string $resourceType, int $resourceId): Collection
    {
        return $this->query()
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
```

### 10. NotificationRepository

```php
// app/Repositories/NotificationRepository.php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $notification)
    {
        parent::__construct($notification);
    }

    /**
     * Get unread notifications for user
     */
    public function getUnread(int $userId, int $limit = 10): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all notifications for user
     */
    public function getForUser(int $userId, int $limit = 50): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Count unread
     */
    public function countUnread(int $userId): int
    {
        return $this->query()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
```

---

## Repository Binding

Register repositories in `RepositoryServiceProvider`:

```php
public function register()
{
    $this->app->bind(
        UserRepositoryContract::class,
        UserRepository::class
    );
    $this->app->bind(
        BookingRepositoryContract::class,
        BookingRepository::class
    );
    // ... register all repositories
}
```

---

## Usage Pattern

```php
class BookingService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private AvailabilityRepository $availabilityRepository
    ) {}

    public function createBooking(array $data): Booking
    {
        // Check availability using repository
        $isAvailable = $this->bookingRepository->isSlotAvailable(
            $data['user_id'],
            $data['meeting_type_id'],
            $data['booking_date'],
            $data['start_time'],
            $data['end_time']
        );

        if (!$isAvailable) {
            throw new SlotNotAvailableException();
        }

        // Create booking
        return $this->bookingRepository->create($data);
    }
}
```

---

## Key Principles

1. **Separation of Concerns**: Repositories handle data, services handle logic
2. **Reusability**: Repositories used by multiple services
3. **Testability**: Easy to mock repositories in tests
4. **Query Optimization**: Repositories handle eager loading and indexing
5. **Consistency**: All database access through repositories
