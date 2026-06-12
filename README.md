# Meeting Scheduler V1 - Production Ready

> A CodeCanyon-ready meeting scheduler application built with Laravel 13, PHP 8.4, MySQL 8, and Bootstrap 5.

## 🎯 Overview

Meeting Scheduler V1 is a complete scheduling solution similar to Calendly. Users can create availability, define meeting types, and share a public booking link where visitors can book meetings instantly.

**Live Demo:** Coming Soon  
**Documentation:** See `ARCHITECTURE/` folder  
**CodeCanyon Ready:** Yes ✓

---

## ✨ Key Features

### Authentication & User Management
- ✅ User Registration with Email Verification
- ✅ Login/Logout with Remember Me
- ✅ Forgot Password & Reset
- ✅ Profile Management
- ✅ Change Password
- ✅ Admin Dashboard

### Availability Management
- ✅ Weekly Schedule Configuration
- ✅ Multiple Time Slots per Day
- ✅ Break Hours Management
- ✅ Holidays & Vacation Blocking
- ✅ Specific Date Blocking
- ✅ Timezone Support

### Meeting Types
- ✅ Create Custom Meeting Types
- ✅ Duration Options: 15, 30, 45, 60, Custom minutes
- ✅ Location Types: Google Meet, Phone, Custom URL, In-Person
- ✅ Buffer Time Configuration
- ✅ Daily Meeting Limits
- ✅ Advance Booking Restrictions

### Public Booking Page
- ✅ Public Profile URL (e.g., /jithin)
- ✅ Meeting Types Display
- ✅ Interactive Calendar Picker
- ✅ Available Time Slots
- ✅ Booking Form
- ✅ Responsive Mobile Design

### Smart Booking Engine
- ✅ Anti-Double Booking with Slot Locking
- ✅ Database Transactions
- ✅ Timezone Conversion
- ✅ Real-Time Slot Availability
- ✅ Automatic Confirmation
- ✅ Activity Logging

### Email Notifications
- ✅ Booking Confirmation (Host & Guest)
- ✅ Booking Cancellation (Host & Guest)
- ✅ Booking Rescheduling (Host & Guest)
- ✅ Meeting Reminders
- ✅ Customizable Email Templates
- ✅ Queue-based Processing

### Google Calendar Integration
- ✅ Connect Google Account
- ✅ Sync Calendar Events
- ✅ Prevent Double Booking
- ✅ Auto Event Creation
- ✅ Bidirectional Sync

### Admin Dashboard
- ✅ System Overview (Users, Meetings, Revenue)
- ✅ Manage Users (Create, Edit, Delete)
- ✅ Manage Bookings
- ✅ Email Template Editor
- ✅ System Settings
- ✅ Activity Logs

### Security & Performance
- ✅ CSRF Protection
- ✅ XSS Protection
- ✅ SQL Injection Prevention
- ✅ Rate Limiting
- ✅ Activity Logging
- ✅ Role-Based Access Control (RBAC)
- ✅ Request Validation
- ✅ Proper Error Handling

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.4, Laravel 13 |
| **Database** | MySQL 8.0 |
| **Frontend** | Bootstrap 5, jQuery, Blade Templates |
| **Authentication** | Laravel Sanctum |
| **Task Queue** | Laravel Queues |
| **Email** | Laravel Mail |
| **API Integration** | Google Calendar API |

---

## 📋 System Requirements

```
PHP >= 8.4
Composer
MySQL >= 8.0
Node.js >= 18.0 (for frontend)
Git
```

---

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/Jithin143jithu/meeting-scheduler-v1.git
cd meeting-scheduler-v1
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
```bash
# Update .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meeting_scheduler
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

### 6. Create Symlink (for file uploads)
```bash
php artisan storage:link
```

### 7. Start Development Server
```bash
php artisan serve
npm run dev
```

### 8. Access Application
- **Dashboard:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Demo Public Page:** http://localhost:8000/demo-user

---

## 📚 Architecture Documentation

All architectural documents are in the `ARCHITECTURE/` folder:

1. **DATABASE_SCHEMA.md** - Complete database design with 12 tables
2. **FOLDER_STRUCTURE.md** - Project directory organization
3. **ER_DIAGRAM.md** - Entity relationship diagram
4. **SERVICE_LAYER_DESIGN.md** - Business logic architecture
5. **REPOSITORY_DESIGN.md** - Data access layer patterns
6. **FEATURE_ROADMAP.md** - Implementation phases and priorities

---

## 📁 Project Structure

```
meeting-scheduler-v1/
├── app/                          # Application code
│   ├── Http/Controllers/         # Request handlers
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic
│   ├── Repositories/             # Data access
│   ├── Events/                   # Events
│   ├── Jobs/                     # Queue jobs
│   └── Policies/                 # Authorization
├── database/
│   ├── migrations/               # Schema migrations
│   ├── seeders/                  # Test data
│   └── factories/                # Model factories
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript
├── routes/                       # URL routing
├── config/                       # Configuration files
├── storage/                      # Logs, uploads
├── tests/                        # Test files
├── ARCHITECTURE/                 # Design documents
├── SETUP/                        # Installation guides
└── docs/                         # User documentation
```

---

## 🔐 Default Admin Credentials

After migration and seeding:

```
Email: admin@example.com
Password: password123
Role: Admin
```

⚠️ **IMPORTANT:** Change these immediately in production!

---

## 📊 Database Tables

1. **users** - User accounts & authentication
2. **meeting_types** - Custom meeting definitions
3. **availabilities** - Weekly schedule
4. **availability_exceptions** - Holidays, vacations, blocks
5. **bookings** - Meeting bookings
6. **booking_notes** - Guest/host notes
7. **email_templates** - Customizable emails
8. **settings** - System configuration
9. **google_accounts** - Google Calendar integration
10. **activity_logs** - Audit trail
11. **notifications** - In-app notifications
12. **personal_access_tokens** - Sanctum API tokens

---

## 🔌 API Endpoints

Full API documentation available in `SETUP/API_DOCUMENTATION.md`

### Public API
- `GET /api/v1/users/{username}` - Get user profile
- `GET /api/v1/users/{username}/availability` - Get availability
- `GET /api/v1/users/{username}/meeting-types` - Get meeting types
- `GET /api/v1/users/{username}/available-slots` - Get available slots
- `POST /api/v1/bookings` - Create booking

### Authenticated API (Sanctum)
- `GET /api/v1/dashboard` - User dashboard stats
- `GET /api/v1/meetings` - List meetings
- `POST /api/v1/meeting-types` - Create meeting type
- `PUT /api/v1/availabilities` - Update availability

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test tests/Feature/BookingTest.php
```

---

## 🚀 Deployment

See `SETUP/DEPLOYMENT.md` for:
- Server setup (Apache/Nginx)
- SSL certificate installation
- Database backup strategy
- Performance optimization
- Monitoring setup

---

## 📝 Environment Variables

```bash
APP_NAME="Meeting Scheduler"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=meeting_scheduler
DB_USERNAME=root
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Meeting Scheduler"

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback

QUEUE_CONNECTION=database
```

---

## 🤝 Contributing

1. Create feature branch: `git checkout -b feature/amazing-feature`
2. Commit changes: `git commit -m 'Add amazing feature'`
3. Push to branch: `git push origin feature/amazing-feature`
4. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.

---

## 💬 Support

- **Email:** support@meetingscheduler.com
- **Documentation:** See `ARCHITECTURE/` folder
- **Issues:** GitHub Issues
- **Discussions:** GitHub Discussions

---

## 🎉 Credits

Built with ❤️ for CodeCanyon

**Version:** 1.0.0  
**Last Updated:** June 2026  
**Status:** Production Ready ✓