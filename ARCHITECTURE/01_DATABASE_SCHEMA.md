# Meeting Scheduler V1 - Complete Database Schema

## Overview
Production-ready database schema for Meeting Scheduler application with proper relationships, constraints, and indexing.

---

## Database Tables

### 1. USERS Table
**Purpose:** Store user account information with authentication details

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    bio TEXT NULL,
    phone VARCHAR(20) NULL,
    timezone VARCHAR(255) DEFAULT 'UTC',
    country VARCHAR(100) NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255) NULL,
    verification_token_expires_at TIMESTAMP NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_token_expires_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
);
```

### 2. MEETING_TYPES Table
**Purpose:** Store different meeting configurations created by users

```sql
CREATE TABLE meeting_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    duration_minutes INT NOT NULL,
    location_type ENUM('google_meet', 'phone_call', 'custom_url', 'in_person') NOT NULL,
    location_value VARCHAR(255) NULL,
    buffer_time_minutes INT DEFAULT 0,
    color VARCHAR(7) DEFAULT '#3498db',
    is_active BOOLEAN DEFAULT TRUE,
    meeting_limit_per_day INT NULL,
    max_advance_booking_days INT DEFAULT 90,
    min_advance_booking_minutes INT DEFAULT 1440,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_slug (user_id, slug),
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active)
);
```

### 3. AVAILABILITIES Table
**Purpose:** Store recurring weekly availability schedule

```sql
CREATE TABLE availabilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    day_of_week INT NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_day_of_week (day_of_week)
);
```

### 4. AVAILABILITY_EXCEPTIONS Table
**Purpose:** Store one-off availability changes (holidays, vacations, etc.)

```sql
CREATE TABLE availability_exceptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('holiday', 'vacation', 'blocked', 'extended_hours') DEFAULT 'blocked',
    exception_date DATE NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    reason VARCHAR(255) NULL,
    is_all_day BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_exception_date (exception_date),
    INDEX idx_date_range (start_date, end_date)
);
```

### 5. BOOKINGS Table
**Purpose:** Store all meeting bookings with status tracking

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_token VARCHAR(255) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    meeting_type_id BIGINT UNSIGNED NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    guest_email VARCHAR(255) NOT NULL,
    guest_phone VARCHAR(20) NULL,
    guest_notes TEXT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    timezone VARCHAR(255) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'rescheduled') DEFAULT 'confirmed',
    cancellation_reason TEXT NULL,
    cancelled_by ENUM('host', 'guest', 'system') NULL,
    cancelled_at TIMESTAMP NULL,
    meeting_link VARCHAR(255) NULL,
    meeting_recording_url VARCHAR(255) NULL,
    calendar_event_id VARCHAR(255) NULL,
    is_reminder_sent BOOLEAN DEFAULT FALSE,
    reminder_sent_at TIMESTAMP NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    locked_until TIMESTAMP NULL COMMENT 'For slot locking during booking',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (meeting_type_id) REFERENCES meeting_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot_lock (user_id, booking_date, start_time, meeting_type_id),
    INDEX idx_user_id (user_id),
    INDEX idx_guest_email (guest_email),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_booking_token (booking_token)
);
```

### 6. BOOKING_NOTES Table
**Purpose:** Store guest notes separately for flexibility

```sql
CREATE TABLE booking_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    note_type ENUM('guest_note', 'host_note', 'system_note') DEFAULT 'guest_note',
    content TEXT NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_booking_id (booking_id),
    INDEX idx_note_type (note_type)
);
```

### 7. EMAIL_TEMPLATES Table

```sql
CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_key VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_template_key (template_key),
    INDEX idx_is_active (is_active)
);
```

### 8. SETTINGS Table

```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE NOT NULL,
    setting_value LONGTEXT NOT NULL,
    value_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    is_editable BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
);
```

### 9. GOOGLE_ACCOUNTS Table

```sql
CREATE TABLE google_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    google_id VARCHAR(255) NOT NULL,
    google_email VARCHAR(255) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NOT NULL,
    token_expires_at TIMESTAMP NOT NULL,
    calendar_id VARCHAR(255) DEFAULT 'primary',
    is_active BOOLEAN DEFAULT TRUE,
    last_synced_at TIMESTAMP NULL,
    sync_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_google (user_id, google_id),
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active)
);
```

### 10. ACTIVITY_LOGS Table

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    action_type ENUM('create', 'read', 'update', 'delete', 'login', 'logout', 'booking') DEFAULT 'create',
    resource_type VARCHAR(255) NULL,
    resource_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    changes_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_resource (resource_type, resource_id),
    INDEX idx_created_at (created_at)
);
```

### 11. NOTIFICATIONS Table

```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(255) NOT NULL,
    notification_title VARCHAR(255) NOT NULL,
    notification_message TEXT NOT NULL,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);
```

### 12. PERSONAL_ACCESS_TOKENS Table (Sanctum)

```sql
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(80) UNIQUE NOT NULL,
    abilities JSON NOT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tokenable (tokenable_type, tokenable_id),
    INDEX idx_token (token)
);
```

---

## Key Relationships

- **Users (1) ← → (M) Meeting Types**
- **Users (1) ← → (M) Availabilities**
- **Users (1) ← → (M) Availability Exceptions**
- **Users (1) ← → (M) Bookings**
- **Meeting Types (1) ← → (M) Bookings**
- **Bookings (1) ← → (M) Booking Notes**
- **Users (1) ← → (M) Google Accounts**
- **Users (1) ← → (M) Notifications**
- **Users (1) ← → (M) Activity Logs**

---

## Performance Indexes

Strategic indexing for:
- Fast user lookups: email, username, role
- Fast booking queries: user_id, booking_date, status
- Fast searches: guest_email, template_key
- Date-based queries: created_at, booking_date
