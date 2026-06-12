# Meeting Scheduler V1 - Installation Guide

## System Requirements

### Minimum
- PHP 8.4+
- MySQL 8.0+
- Node.js 18.0+
- Composer 2.0+
- 512 MB RAM
- 500 MB Disk Space

### Recommended
- PHP 8.4 or higher
- MySQL 8.0.30+
- Node.js 20.0+
- 2+ GB RAM
- 2+ GB Disk Space
- Redis (for caching/queues)

---

## Installation Steps

### 1. Clone Repository

```bash
git clone https://github.com/Jithin143jithu/meeting-scheduler-v1.git
cd meeting-scheduler-v1
```

### 2. Install Backend Dependencies

```bash
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` file:

```env
APP_NAME="Meeting Scheduler"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meeting_scheduler
DB_USERNAME=root
DB_PASSWORD=

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="Meeting Scheduler"

# Queue
QUEUE_CONNECTION=sync

# Google Calendar
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Create Database

```bash
# Create empty database
mysql -u root -p -e "CREATE DATABASE meeting_scheduler;"
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Database

```bash
php artisan db:seed
```

This will create:
- Admin user (admin@example.com / password123)
- Sample email templates
- System settings
- Demo data

### 9. Create Storage Symlink

```bash
php artisan storage:link
```

### 10. Build Frontend Assets

```bash
npm run build
```

### 11. Start Development Server

```bash
php artisan serve
```

In another terminal:

```bash
npm run dev
```

---

## Access Application

### User Dashboard
**URL:** http://localhost:8000  
**Username:** user@example.com  
**Password:** password123

### Admin Dashboard
**URL:** http://localhost:8000/admin  
**Email:** admin@example.com  
**Password:** password123

### Demo Public Page
**URL:** http://localhost:8000/demo-user

---

## Docker Setup (Optional)

### Using Docker Compose

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/app
    environment:
      - DB_HOST=db
      - DB_DATABASE=meeting_scheduler
      - DB_USERNAME=scheduler
      - DB_PASSWORD=password
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: meeting_scheduler
      MYSQL_USER: scheduler
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

Start with:

```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

---

## Nginx Configuration (Production)

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/meeting-scheduler/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).*$ {
        deny all;
    }
}
```

---

## Apache Configuration (Production)

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/meeting-scheduler/public

    <Directory /var/www/meeting-scheduler/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.4-fpm.sock|fcgi://localhost/"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Enable mod_rewrite:

```bash
sudo a2enmod rewrite
sudo a2enmod proxy
sudo a2enmod proxy_fcgi
```

---

## Troubleshooting

### Issue: "SQLSTATE[HY000]: General error: 1030 Got error..."

**Solution:**
```bash
# Increase max_allowed_packet
mysql -u root -p
SET GLOBAL max_allowed_packet = 67108864;
```

### Issue: "No such file or directory" on storage:link

**Solution:**
```bash
mkdir -p storage/app/public
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache
```

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php artisan cache:clear
php artisan config:clear
```

### Issue: "Cannot find Node modules"

**Solution:**
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Issue: Database migrations fail

**Solution:**
```bash
# Check MySQL version
mysql --version

# Ensure user has privileges
mysql -u root -p
GRANT ALL PRIVILEGES ON meeting_scheduler.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

---

## Post-Installation

### 1. Configure Mail

Update `.env` with your mail provider:

```env
# Using Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# Or using Mailtrap
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### 2. Setup Google Calendar

1. Go to Google Cloud Console
2. Create OAuth 2.0 credentials
3. Set authorized redirect URIs: `http://localhost:8000/auth/google/callback`
4. Add credentials to `.env`

### 3. Configure Queue (Optional)

For production, use database or Redis queue:

```env
QUEUE_CONNECTION=database
# OR
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Start queue worker:

```bash
php artisan queue:work
```

### 4. Setup Cron Job (For Scheduled Tasks)

```bash
* * * * * cd /var/www/meeting-scheduler && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Create Admin User

```bash
php artisan tinker

$user = App\Models\User::create([
    'username' => 'admin',
    'email' => 'admin@yourdomain.com',
    'password' => bcrypt('secure-password'),
    'first_name' => 'Admin',
    'last_name' => 'User',
    'role' => 'admin',
    'is_active' => true,
    'is_verified' => true,
    'email_verified_at' => now(),
]);
```

---

## Security Checklist

- [ ] Change default admin password
- [ ] Update `.env` with production settings
- [ ] Set `APP_DEBUG=false` in production
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall
- [ ] Set up rate limiting
- [ ] Enable 2FA (when available)
- [ ] Regular database backups
- [ ] Monitor error logs
- [ ] Update dependencies regularly

---

## Next Steps

1. Read the documentation
2. Configure email templates
3. Set up Google Calendar integration
4. Create meeting types
5. Configure availability
6. Share public booking link

---

## Support

For issues or questions:
- Check GitHub issues
- Email: support@meetingscheduler.com
- Documentation: See ARCHITECTURE/ folder
