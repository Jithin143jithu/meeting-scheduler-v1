# Meeting Scheduler V1 - Folder Structure

## Complete Project Directory Organization

```
meeting-scheduler-v1/
│
├── 📁 app/
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── Auth/                          # Authentication controllers
│   │   │   ├── Dashboard/                     # User dashboard controllers
│   │   │   ├── Public/                        # Public page controllers
│   │   │   ├── Admin/                         # Admin panel controllers
│   │   │   ├── Api/                           # REST API controllers
│   │   │   └── GoogleCalendar/                # Google integration
│   │   │
│   │   ├── 📁 Requests/
│   │   │   ├── Auth/                          # Auth validation
│   │   │   ├── Dashboard/                     # Dashboard validation
│   │   │   └── Booking/                       # Booking validation
│   │   │
│   │   ├── 📁 Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   ├── VerifyEmailMiddleware.php
│   │   │   ├── RateLimitMiddleware.php
│   │   │   ├── LogActivityMiddleware.php
│   │   │   └── SetUserTimezone.php
│   │   │
│   │   └── 📁 Resources/
│   │       ├── UserResource.php
│   │       ├── BookingResource.php
│   │       ├── MeetingTypeResource.php
│   │       └── AvailabilityResource.php
│   │
│   ├── 📁 Models/
│   │   ├── User.php
│   │   ├── MeetingType.php
│   │   ├── Availability.php
│   │   ├── AvailabilityException.php
│   │   ├── Booking.php
│   │   ├── BookingNote.php
│   │   ├── EmailTemplate.php
│   │   ├── Setting.php
│   │   ├── GoogleAccount.php
│   │   ├── ActivityLog.php
│   │   ├── Notification.php
│   │   └── 📁 Traits/                         # Reusable model traits
│   │       ├── HasAvailability.php
│   │       ├── HasBookings.php
│   │       ├── HasTimezone.php
│   │       └── HasGoogleCalendar.php
│   │
│   ├── 📁 Services/
│   │   ├── 📁 Auth/
│   │   │   ├── AuthService.php
│   │   │   ├── PasswordResetService.php
│   │   │   └── EmailVerificationService.php
│   │   │
│   │   ├── 📁 Booking/
│   │   │   ├── BookingService.php
│   │   │   ├── BookingValidationService.php
│   │   │   ├── SlotLockingService.php
│   │   │   ├── TimezoneConversionService.php
│   │   │   └── BookingConfirmationService.php
│   │   │
│   │   ├── 📁 Availability/
│   │   │   ├── AvailabilityService.php
│   │   │   ├── AvailabilitySlotService.php
│   │   │   ├── AvailabilityExceptionService.php
│   │   │   └── WorkingHoursService.php
│   │   │
│   │   ├── 📁 MeetingType/
│   │   │   └── MeetingTypeService.php
│   │   │
│   │   ├── 📁 Email/
│   │   │   ├── EmailService.php
│   │   │   ├── EmailTemplateService.php
│   │   │   └── NotificationService.php
│   │   │
│   │   ├── 📁 GoogleCalendar/
│   │   │   ├── GoogleCalendarService.php
│   │   │   ├── CalendarSyncService.php
│   │   │   ├── GoogleOAuthService.php
│   │   │   └── EventConflictService.php
│   │   │
│   │   ├── 📁 User/
│   │   │   ├── UserService.php
│   │   │   ├── ProfileService.php
│   │   │   └── UserManagementService.php
│   │   │
│   │   └── 📁 Admin/
│   │       ├── AdminService.php
│   │       ├── SettingsService.php
│   │       ├── DashboardStatsService.php
│   │       └── ActivityLogService.php
│   │
│   ├── 📁 Repositories/
│   │   ├── 📁 Contracts/                      # Interfaces
│   │   │   ├── UserRepositoryContract.php
│   │   │   ├── BookingRepositoryContract.php
│   │   │   ├── AvailabilityRepositoryContract.php
│   │   │   ├── MeetingTypeRepositoryContract.php
│   │   │   └── SettingRepositoryContract.php
│   │   │
│   │   ├── BaseRepository.php                 # Abstract base class
│   │   ├── UserRepository.php
│   │   ├── BookingRepository.php
│   │   ├── AvailabilityRepository.php
│   │   ├── AvailabilityExceptionRepository.php
│   │   ├── MeetingTypeRepository.php
│   │   ├── EmailTemplateRepository.php
│   │   ├── SettingRepository.php
│   │   ├── GoogleAccountRepository.php
│   │   ├── ActivityLogRepository.php
│   │   ├── NotificationRepository.php
│   │   └── BookingNoteRepository.php
│   │
│   ├── 📁 Events/
│   │   ├── BookingCreated.php
│   │   ├── BookingCancelled.php
│   │   ├── BookingRescheduled.php
│   │   ├── BookingCompleted.php
│   │   ├── UserRegistered.php
│   │   ├── GoogleAccountConnected.php
│   │   └── GoogleCalendarSynced.php
│   │
│   ├── 📁 Jobs/
│   │   ├── SendBookingConfirmationEmailJob.php
│   │   ├── SendBookingCancellationEmailJob.php
│   │   ├── SendBookingReminderEmailJob.php
│   │   ├── SyncGoogleCalendarJob.php
│   │   ├── CreateGoogleCalendarEventJob.php
│   │   ├── ProcessActivityLogJob.php
│   │   └── GenerateSlotsJob.php
│   │
│   ├── 📁 Policies/
│   │   ├── BookingPolicy.php
│   │   ├── MeetingTypePolicy.php
│   │   ├── AvailabilityPolicy.php
│   │   └── AdminPolicy.php
│   │
│   ├── 📁 Exceptions/
│   │   ├── SlotNotAvailableException.php
│   │   ├── DoubleBookingException.php
│   │   ├── GoogleCalendarException.php
│   │   ├── InvalidTimezoneException.php
│   │   ├── BookingValidationException.php
│   │   └── EmailSendException.php
│   │
│   ├── 📁 Helpers/
│   │   ├── DateTimeHelper.php
│   │   ├── TimezoneHelper.php
│   │   ├── SlotGenerationHelper.php
│   │   ├── ValidationHelper.php
│   │   └── ResponseHelper.php
│   │
│   ├── 📁 Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   │
│   └── Application.php
│
├── 📁 database/
│   ├── 📁 migrations/
│   │   ├── 2026_01_01_000001_create_users_table.php
│   │   ├── 2026_01_01_000002_create_meeting_types_table.php
│   │   ├── 2026_01_01_000003_create_availabilities_table.php
│   │   ├── 2026_01_01_000004_create_availability_exceptions_table.php
│   │   ├── 2026_01_01_000005_create_bookings_table.php
│   │   ├── 2026_01_01_000006_create_booking_notes_table.php
│   │   ├── 2026_01_01_000007_create_email_templates_table.php
│   │   ├── 2026_01_01_000008_create_settings_table.php
│   │   ├── 2026_01_01_000009_create_google_accounts_table.php
│   │   ├── 2026_01_01_000010_create_activity_logs_table.php
│   │   ├── 2026_01_01_000011_create_notifications_table.php
│   │   └── 2026_01_01_000012_create_personal_access_tokens_table.php
│   │
│   ├── 📁 seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── SettingSeeder.php
│   │   ├── EmailTemplateSeeder.php
│   │   ├── MeetingTypeSeeder.php
│   │   └── AvailabilitySeeder.php
│   │
│   └── 📁 factories/
│       ├── UserFactory.php
│       ├── BookingFactory.php
│       ├── MeetingTypeFactory.php
│       └── AvailabilityFactory.php
│
├── 📁 resources/
│   ├── 📁 views/
│   │   ├── 📁 layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── auth.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── public.blade.php
│   │   │   └── 📁 components/
│   │   │       ├── navbar.blade.php
│   │   │       ├── sidebar.blade.php
│   │   │       ├── footer.blade.php
│   │   │       └── alerts.blade.php
│   │   │
│   │   ├── 📁 auth/
│   │   │   ├── register.blade.php
│   │   │   ├── login.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   ├── reset-password.blade.php
│   │   │   └── verify-email.blade.php
│   │   │
│   │   ├── 📁 dashboard/
│   │   │   ├── index.blade.php
│   │   │   ├── 📁 bookings/
│   │   │   ├── 📁 meeting-types/
│   │   │   ├── 📁 availability/
│   │   │   ├── 📁 profile/
│   │   │   ├── 📁 google-calendar/
│   │   │   └── 📁 settings/
│   │   │
│   │   ├── 📁 public/
│   │   │   ├── profile.blade.php
│   │   │   ├── booking-form.blade.php
│   │   │   ├── calendar-picker.blade.php
│   │   │   ├── time-slots.blade.php
│   │   │   ├── booking-success.blade.php
│   │   │   └── booking-error.blade.php
│   │   │
│   │   ├── 📁 admin/
│   │   │   ├── 📁 dashboard/
│   │   │   ├── 📁 users/
│   │   │   ├── 📁 bookings/
│   │   │   ├── 📁 settings/
│   │   │   ├── 📁 email-templates/
│   │   │   └── 📁 activity-logs/
│   │   │
│   │   └── 📁 errors/
│   │       ├── 404.blade.php
│   │       ├── 500.blade.php
│   │       └── 403.blade.php
│   │
│   ├── 📁 css/
│   │   ├── app.css
│   │   ├── dashboard.css
│   │   ├── public-page.css
│   │   └── admin.css
│   │
│   └── 📁 js/
│       ├── app.js
│       ├── 📁 dashboard/
│       ├── 📁 public/
│       ├── 📁 admin/
│       └── 📁 utils/
│
├── 📁 routes/
│   ├── api.php                               # /api/v1 routes
│   ├── web.php                               # Web routes
│   ├── admin.php                             # /admin routes
│   └── public.php                            # Public routes
│
├── 📁 config/
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   ├── auth.php
│   ├── queue.php
│   ├── cache.php
│   ├── session.php
│   ├── timezone.php                          # Timezone config
│   ├── booking.php                           # Booking settings
│   ├── google-calendar.php                   # Google API config
│   └── email.php                             # Email templates config
│
├── 📁 storage/
│   ├── 📁 app/
│   │   ├── 📁 uploads/                       # User uploads
│   │   └── 📁 logs/
│   ├── 📁 framework/
│   └── 📁 logs/
│
├── 📁 tests/
│   ├── 📁 Feature/
│   │   ├── 📁 Auth/
│   │   ├── 📁 Booking/
│   │   ├── 📁 Availability/
│   │   ├── 📁 GoogleCalendar/
│   │   └── 📁 Email/
│   └── 📁 Unit/
│       ├── 📁 Services/
│       └── 📁 Repositories/
│
├── 📁 ARCHITECTURE/
│   ├── 01_DATABASE_SCHEMA.md
│   ├── 02_FOLDER_STRUCTURE.md
│   ├── 03_ER_DIAGRAM.md
│   ├── 04_SERVICE_LAYER_DESIGN.md
│   ├── 05_REPOSITORY_DESIGN.md
│   └── 06_FEATURE_ROADMAP.md
│
├── 📁 SETUP/
│   ├── INSTALLATION.md
│   ├── CONFIGURATION.md
│   ├── DEPLOYMENT.md
│   ├── API_DOCUMENTATION.md
│   └── TROUBLESHOOTING.md
│
├── 📁 docs/
│   ├── USER_GUIDE.md
│   ├── ADMIN_GUIDE.md
│   ├── DEVELOPER_GUIDE.md
│   └── API_REFERENCE.md
│
├── .env.example
├── .gitignore
├── .editorconfig
├── composer.json
├── package.json
├── vite.config.js
├── phpunit.xml
├── artisan
├── README.md
├── LICENSE
└── CHANGELOG.md
```

---

## Directory Purposes

| Directory | Purpose |
|-----------|----------|
| `app/Http/Controllers` | Request handlers for all endpoints |
| `app/Models` | Eloquent models representing database tables |
| `app/Services` | Business logic layer (all core functionality) |
| `app/Repositories` | Data access layer (database queries only) |
| `app/Events` | Events triggered during operations |
| `app/Jobs` | Async queue jobs (emails, sync, etc.) |
| `app/Policies` | Authorization logic (who can do what) |
| `database/migrations` | Database schema definitions |
| `database/seeders` | Test data generators |
| `resources/views` | Blade templates (HTML UI) |
| `resources/css` | Stylesheets |
| `resources/js` | JavaScript files |
| `routes` | URL routing definitions |
| `config` | Application configuration |
| `storage` | File uploads, logs, temporary files |
| `tests` | Automated tests (Unit & Feature) |
| `ARCHITECTURE` | System design documentation |

---

## Naming Conventions

### Controllers
- Singular nouns: `BookingController`, `UserController`
- Placement: `app/Http/Controllers/{Feature}/`

### Models
- Singular: `User`, `Booking`, `MeetingType`
- Placement: `app/Models/`

### Services
- Suffix with `Service`: `BookingService`, `EmailService`
- Placement: `app/Services/{Feature}/`

### Repositories
- Suffix with `Repository`: `UserRepository`, `BookingRepository`
- Placement: `app/Repositories/`

### Events
- Past tense: `BookingCreated`, `UserRegistered`
- Placement: `app/Events/`

### Jobs
- Suffix with `Job`: `SendEmailJob`, `SyncCalendarJob`
- Placement: `app/Jobs/`

### Views
- Lowercase with hyphens: `booking-form.blade.php`
- Placement: `resources/views/{Feature}/`

### Tests
- Mirror production structure
- Suffix with `Test`: `BookingTest.php`
