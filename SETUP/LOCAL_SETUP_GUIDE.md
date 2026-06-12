# Meeting Scheduler V1 - Local Setup Guide

## 🚀 Step-by-Step Local Installation & Testing

### Prerequisites
Make sure you have installed:
- **PHP 8.4+** - [Download](https://www.php.net/downloads)
- **Composer** - [Download](https://getcomposer.org/download/)
- **MySQL 8.0+** - [Download](https://dev.mysql.com/downloads/mysql/)
- **Node.js 18+** - [Download](https://nodejs.org/)
- **Git** - [Download](https://git-scm.com/)

Verify installations:
```bash
php --version
composer --version
mysql --version
node --version
npm --version
```

---

## 📥 Step 1: Clone Repository

```bash
git clone https://github.com/Jithin143jithu/meeting-scheduler-v1.git
cd meeting-scheduler-v1
```

---

## 📦 Step 2: Install Dependencies

### Install PHP Dependencies
```bash
composer install
```

### Install NPM Dependencies
```bash
npm install
```

---

## 🔧 Step 3: Environment Configuration

### Create .env File
```bash
cp .env.example .env
```

### Generate Application Key
```bash
php artisan key:generate
```

### Edit .env File
Open `.env` in your editor and configure:

```env
APP_NAME="Meeting Scheduler"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meeting_scheduler
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (for testing)
MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@meetingscheduler.com"
MAIL_FROM_NAME="Meeting Scheduler"

# Queue Configuration
QUEUE_CONNECTION=database

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

---

## 🗄️ Step 4: Create Database

### Using MySQL CLI
```bash
mysql -u root -p
```

In MySQL prompt:
```sql
CREATE DATABASE meeting_scheduler;
USE meeting_scheduler;
EXIT;
```

### Or using MySQL Workbench/phpMyAdmin
- Create new database named `meeting_scheduler`
- Keep default charset (utf8mb4)

---

## 🔄 Step 5: Run Migrations & Seeders

### Run All Migrations
```bash
php artisan migrate
```

### Seed Database with Test Data
```bash
php artisan db:seed
```

### Or Run Both Together
```bash
php artisan migrate:fresh --seed
```

**Output should show:**
```
Migration table created successfully.
Migrated: 2024_01_01_000001_create_users_table
Migrated: 2024_01_01_000002_create_meeting_types_table
[... all other migrations ...]

Seeding: Database\Seeders\DatabaseSeeder
Seeding: Database\Seeders\UserSeeder
Seeding: Database\Seeders\MeetingTypeSeeder
[... all other seeders ...]
```

---

## 📁 Step 6: Create Storage Symlink

```bash
php artisan storage:link
```

**This creates a public symlink to storage/app/public for file uploads**

---

## 🚀 Step 7: Start Development Server

### Terminal 1: Start Laravel Server
```bash
php artisan serve
```

**Output:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

### Terminal 2: Build Frontend Assets
```bash
npm run dev
```

**This watches for CSS/JS changes**

### Terminal 3: Start Queue Worker (Optional - for emails)
```bash
php artisan queue:work
```

---

## 🧪 Step 8: Access Application

Open your browser:

### User Login
- **URL:** http://localhost:8000/login
- **Email:** demo@example.com
- **Password:** password123
- **Role:** User

### Admin Login
- **URL:** http://localhost:8000/admin/login
- **Email:** admin@example.com
- **Password:** password123
- **Role:** Admin

### Public Profile
- **URL:** http://localhost:8000/demo-user
- **View:** Public booking page for demo user

---

## 🧪 Step 9: Test Features

### Test 1: User Registration
```
1. Go to: http://localhost:8000/register
2. Fill in form:
   - Name: Your Name
   - Email: yourname@example.com
   - Username: yourname
   - Password: password123 (min 8 chars)
   - Timezone: Your timezone
3. Click Register
4. You should be redirected to login
```

### Test 2: User Login
```
1. Go to: http://localhost:8000/login
2. Enter credentials:
   - Email: demo@example.com
   - Password: password123
3. Click Login
4. You should see the Dashboard
```

### Test 3: Create Meeting Type
```
1. After login, go to: Meeting Types section
2. Click "Create New Meeting Type"
3. Fill in:
   - Name: Coffee Chat
   - Duration: 15 minutes
   - Location Type: Google Meet
   - Buffer Before: 5 min
   - Buffer After: 5 min
   - Daily Limit: 10
4. Click Save
5. Meeting type should appear in list
```

### Test 4: Set Availability
```
1. Go to: Availability / Schedule section
2. Configure weekly schedule:
   - Monday: 09:00 - 17:00
   - Tuesday: 09:00 - 17:00
   - ... etc
3. Click Save
4. Schedule should be updated
```

### Test 5: View Public Profile
```
1. Go to: http://localhost:8000/demo-user
2. You should see:
   - Demo user profile
   - Meeting types available
   - Calendar picker
3. (Booking creation requires frontend views)
```

### Test 6: Admin Panel
```
1. Go to: http://localhost:8000/admin
2. Login with admin credentials
3. Dashboard should show:
   - Total users
   - Total bookings
   - System statistics
4. Manage Users / Bookings / Settings
```

---

## 🧪 Step 10: Test API Endpoints (Optional)

### Using Postman or cURL

#### Register API
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "API User",
    "email": "apiuser@example.com",
    "username": "apiuser",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Login API
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "demo@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "user": {...},
  "token": "your-sanctum-token-here"
}
```

#### Use Token for Authenticated Requests
```bash
curl http://localhost:8000/api/v1/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🐛 Step 11: Debugging & Testing Commands

### Check Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> User::count();
>>> exit
```

### Run Tests
```bash
php artisan test
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Run Specific Migration
```bash
php artisan migrate --path=database/migrations/2024_01_01_000001_create_users_table.php
```

### Rollback Migrations
```bash
php artisan migrate:rollback
```

### Reset Database
```bash
php artisan migrate:reset
```

### Fresh Install (Delete & Recreate)
```bash
php artisan migrate:fresh --seed
```

---

## 📧 Step 12: Test Emails (Optional)

### Using MailHog (Catch emails locally)

#### Install MailHog
```bash
# macOS
brew install mailhog
mailhog

# Linux
# Download from: https://github.com/mailhog/MailHog/releases
./MailHog
```

#### Configure .env
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

#### View Emails
- Open: http://localhost:1025
- All emails sent will appear here

---

## 🔒 Step 13: Security Best Practices

### Before Going to Production

```bash
# 1. Disable debug mode
APP_DEBUG=false

# 2. Enable HTTPS
APP_URL=https://yourdomain.com

# 3. Set secure session cookie
SESSION_SECURE_COOKIES=true

# 4. Set strong APP_KEY
php artisan key:generate

# 5. Update database credentials
DB_USERNAME=secure_user
DB_PASSWORD=strong_password

# 6. Configure proper mail service
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
```

---

## 📝 Step 14: Common Issues & Solutions

### Issue 1: "SQLSTATE[HY000]: General error: 1030 Got error"
**Solution:**
```bash
php artisan migrate:reset
php artisan migrate:fresh --seed
```

### Issue 2: "Class not found" error
**Solution:**
```bash
composer dump-autoload
```

### Issue 3: "Storage symlink already exists"
**Solution:**
```bash
rm public/storage
php artisan storage:link
```

### Issue 4: "Port 8000 already in use"
**Solution:**
```bash
php artisan serve --port=8001
```

### Issue 5: "npm ERR! code ERESOLVE"
**Solution:**
```bash
npm install --legacy-peer-deps
```

### Issue 6: Database connection refused
**Solution:**
- Check MySQL is running
- Verify .env DB credentials
- Verify DB_HOST is 127.0.0.1 (not localhost on Windows)

---

## 📊 Step 15: Database Verification

### Check All Tables Created
```bash
mysql -u root meeting_scheduler

SHOW TABLES;

# Should display:
# users
# meeting_types
# availabilities
# availability_exceptions
# bookings
# booking_notes
# email_templates
# settings
# google_accounts
# activity_logs
# notifications
```

### Check Sample Data
```bash
SELECT * FROM users;
SELECT * FROM meeting_types;
SELECT * FROM availabilities;
```

---

## ✅ Final Checklist

- [ ] PHP 8.4+ installed
- [ ] Composer installed
- [ ] MySQL 8.0+ running
- [ ] Node.js 18+ installed
- [ ] Repository cloned
- [ ] Dependencies installed (composer + npm)
- [ ] .env file created & configured
- [ ] APP_KEY generated
- [ ] Database created
- [ ] Migrations ran successfully
- [ ] Seeders created test data
- [ ] Storage symlink created
- [ ] Laravel dev server running
- [ ] Frontend assets compiled
- [ ] Can login with demo@example.com
- [ ] Can access admin panel with admin@example.com
- [ ] Can view public profile at /demo-user

---

## 🎉 Success!

Your Meeting Scheduler V1 is now running locally!

### Quick Access Links:
- **Dashboard:** http://localhost:8000/dashboard
- **Login:** http://localhost:8000/login
- **Register:** http://localhost:8000/register
- **Public Profile:** http://localhost:8000/demo-user
- **Admin Panel:** http://localhost:8000/admin
- **API Docs:** http://localhost:8000/api/documentation (if generated)

---

## 📞 Need Help?

1. Check Laravel Logs: `storage/logs/laravel.log`
2. Run: `php artisan tinker` for debugging
3. Check .env configuration
4. Verify database connection

**Happy testing!** 🚀
