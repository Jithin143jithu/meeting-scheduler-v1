# Meeting Scheduler V1 - ER Diagram

## Entity Relationship Diagram (Text Format)

```
┌────────────────────────────────────────────────┐
│              USERS                             │
├────────────────────────────────────────────────┤
│ PK  id (BIGINT)                                │
│ UQ  email (VARCHAR)                            │
│ UQ  username (VARCHAR) - for public profile    │
│     first_name, last_name (VARCHAR)            │
│     password (VARCHAR)                         │
│     avatar (VARCHAR)                           │
│     timezone (VARCHAR) DEFAULT='UTC'           │
│     role ENUM('admin','user')                  │
│     is_active, is_verified (BOOLEAN)           │
│     created_at, updated_at, deleted_at         │
└────────────────────────────────────────────────┘
          │
          │ 1:M (one user has many...)
          │
          ├─────────────────────┬─────────────────────┬──────────────────┐
          │                     │                     │                  │
          ▼                     ▼                     ▼                  ▼

┌────────────────────────────────┐  ┌──────────────────────┐  ┌──────────────┐
│     MEETING_TYPES              │  │    AVAILABILITIES    │  │GOOGLE_ACCOUNTS
├────────────────────────────────┤  ├──────────────────────┤  ├──────────────┤
│ PK  id (BIGINT)                │  │ PK  id (BIGINT)      │  │ PK  id       │
│ FK  user_id → users.id         │  │ FK  user_id          │  │ FK  user_id  │
│ UQ  (user_id, slug)            │  │     day_of_week      │  │     google_id│
│     name, slug (VARCHAR)       │  │     start_time (TIME)│  │ google_email │
│     description (TEXT)         │  │     end_time (TIME)  │  │ access_token │
│     duration_minutes (INT)     │  │     is_active        │  │ refresh_token│
│     location_type ENUM         │  │     created_at       │  │ token_expires│
│     location_value (VARCHAR)   │  │     updated_at       │  │ calendar_id  │
│     buffer_time_minutes (INT)  │  └──────────────────────┘  │ is_active    │
│     color (VARCHAR)            │                             │ last_synced  │
│     is_active (BOOLEAN)        │                             │ sync_status  │
│     meeting_limit_per_day      │                             └──────────────┘
│     max_advance_booking_days   │
│     min_advance_booking_minutes│
│     created_at, updated_at     │
└────────────────────────────────┘
          │
          │ 1:M
          │
          ▼

┌──────────────────────────────────────────────────────────┐
│            AVAILABILITY_EXCEPTIONS                       │
├──────────────────────────────────────────────────────────┤
│ PK  id (BIGINT)                                          │
│ FK  user_id → users.id                                   │
│     type ENUM('holiday','vacation','blocked',           │
│               'extended_hours')                          │
│     exception_date (DATE) - single day block             │
│     start_date, end_date (DATE) - date range             │
│     start_time, end_time (TIME) - specific time slots    │
│     reason (VARCHAR)                                     │
│     is_all_day (BOOLEAN)                                 │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│                          BOOKINGS                                        │
├──────────────────────────────────────────────────────────────────────────┤
│ PK  id (BIGINT)                                                          │
│ UQ  booking_token (VARCHAR) - for public guest access                    │
│ FK  user_id → users.id                                                   │
│ FK  meeting_type_id → meeting_types.id                                   │
│ UQ  (user_id, booking_date, start_time, meeting_type_id) - NO DOUBLEOK │
│     guest_name, guest_email (VARCHAR)                                    │
│     guest_phone, guest_notes                                             │
│     booking_date (DATE)                                                  │
│     start_time, end_time (TIME)                                          │
│     timezone (VARCHAR) - for historical accuracy                         │
│     status ENUM('pending','confirmed','completed','cancelled')          │
│     cancellation_reason, cancelled_by (VARCHAR/ENUM)                    │
│     cancelled_at (TIMESTAMP)                                             │
│     meeting_link (VARCHAR) - Google Meet URL or phone                    │
│     meeting_recording_url (VARCHAR)                                      │
│     calendar_event_id (VARCHAR) - for Google Calendar sync               │
│     is_reminder_sent (BOOLEAN)                                           │
│     reminder_sent_at (TIMESTAMP)                                         │
│     locked_until (TIMESTAMP) ◄─ For slot locking                        │
│     ip_address, user_agent (VARCHAR/TEXT)                                │
│     created_at, updated_at, deleted_at                                   │
└──────────────────────────────────────────────────────────────────────────┘
          │
          │ 1:M
          │
          ▼

┌────────────────────────────────────┐
│       BOOKING_NOTES                │
├────────────────────────────────────┤
│ PK  id (BIGINT)                    │
│ FK  booking_id → bookings.id       │
│ FK  created_by → users.id (null)   │
│     note_type ENUM('guest_note',   │
│                   'host_note',      │
│                   'system_note')    │
│     content (TEXT)                 │
│     created_at, updated_at         │
└────────────────────────────────────┘


Supporting Tables (Not shown in main diagram for clarity):

┌────────────────────────────────┐
│      EMAIL_TEMPLATES           │
├────────────────────────────────┤
│ PK  id (BIGINT)                │
│ UQ  template_key (VARCHAR)     │
│     name, subject (VARCHAR)    │
│     body (TEXT)                │
│     variables (JSON)           │
│     is_active (BOOLEAN)        │
│     created_at, updated_at     │
└────────────────────────────────┘

┌────────────────────────────────┐
│          SETTINGS              │
├────────────────────────────────┤
│ PK  id (BIGINT)                │
│ UQ  setting_key (VARCHAR)      │
│     setting_value (LONGTEXT)   │
│     value_type ENUM(...)       │
│     is_editable (BOOLEAN)      │
│     created_at, updated_at     │
└────────────────────────────────┘

┌────────────────────────────────┐
│      ACTIVITY_LOGS             │
├────────────────────────────────┤
│ PK  id (BIGINT)                │
│ FK  user_id → users.id (null)  │
│     action (VARCHAR)           │
│     action_type ENUM(...)      │
│     resource_type, resource_id │
│     old_values, new_values JSON│
│     ip_address, user_agent     │
│     created_at                 │
└────────────────────────────────┘

┌────────────────────────────────┐
│      NOTIFICATIONS             │
├────────────────────────────────┤
│ PK  id (BIGINT)                │
│ FK  user_id → users.id         │
│     type (VARCHAR)             │
│     notification_title (VARCHAR│
│     notification_message (TEXT)│
│     data (JSON)                │
│     is_read (BOOLEAN)          │
│     read_at (TIMESTAMP)        │
│     created_at                 │
└────────────────────────────────┘

┌────────────────────────────────┐
│PERSONAL_ACCESS_TOKENS (Sanctum)│
├────────────────────────────────┤
│ PK  id (BIGINT)                │
│     tokenable_type (VARCHAR)   │
│     tokenable_id (BIGINT)      │
│     name (VARCHAR)             │
│ UQ  token (VARCHAR)            │
│     abilities (JSON)           │
│     last_used_at (TIMESTAMP)   │
│     expires_at (TIMESTAMP)     │
│     created_at, updated_at     │
└────────────────────────────────┘
```

## Key Relationships Summary

### 1:M Relationships
- **Users (1) ← → (M) Meeting Types** - User defines multiple meeting types
- **Users (1) ← → (M) Availabilities** - User has weekly schedule slots
- **Users (1) ← → (M) Availability Exceptions** - User can block dates/times
- **Users (1) ← → (M) Bookings** - Host has many bookings
- **Users (1) ← → (M) Google Accounts** - User can connect multiple Google accounts
- **Users (1) ← → (M) Notifications** - User receives notifications
- **Users (1) ← → (M) Activity Logs** - Audit trail per user
- **Meeting Types (1) ← → (M) Bookings** - Type can have many bookings
- **Bookings (1) ← → (M) Booking Notes** - Booking has many notes

## Unique Constraints (Data Integrity)

1. `users.email` - Email must be unique
2. `users.username` - Username unique (public profile URL)
3. `meeting_types.(user_id, slug)` - Slug unique per user
4. `bookings.booking_token` - Public booking token unique
5. `bookings.(user_id, booking_date, start_time, meeting_type_id)` - **Prevents double-booking**
6. `email_templates.template_key` - Template key unique
7. `settings.setting_key` - Setting key unique
8. `google_accounts.(user_id, google_id)` - Prevents duplicate connections
9. `personal_access_tokens.token` - API token unique

## Indexing Strategy

### Fast Lookups
- `users.email` - Login queries
- `users.username` - Public profile
- `bookings.booking_date` - Availability queries
- `bookings.status` - Status filtering
- `bookings.guest_email` - Guest lookup

### Performance Optimization
- All foreign keys indexed
- Date-range queries optimized
- User-specific queries cached
