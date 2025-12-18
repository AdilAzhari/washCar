# WashyWashy Deployment Guide

## System Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 14+
- Redis (optional, for queue and cache)
- Supervisor (for queue workers)

## Pre-Deployment Checklist

### 1. Environment Configuration

Copy `.env.example` to `.env` and configure:

```bash
cp .env.example .env
```

**Required Environment Variables:**

```env
APP_NAME="WashyWashy"
APP_ENV=production
APP_KEY=  # Run: php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=washywashy
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Mail Configuration (required for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@washywashy.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration
QUEUE_CONNECTION=database  # or redis for better performance

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=database  # or redis
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# JavaScript dependencies
npm install && npm run build
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (optional)
php artisan db:seed
```

### 4. Application Optimization

```bash
# Cache configuration, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### 5. Storage & Permissions

```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Queue Worker Setup (REQUIRED)

Create `/etc/supervisor/conf.d/washywashy-worker.conf`:

```ini
[program:washywashy-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/washywashy/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/washywashy/storage/logs/worker.log
```

## Scheduler Setup (REQUIRED)

Add to crontab:

```cron
* * * * * cd /path/to/washywashy && php artisan schedule:run >> /dev/null 2>&1
```

## Testing the Deployment

```bash
# Run test suite
php artisan test

# Test notifications
php artisan appointments:send-reminders 24h
php artisan queue:work --once
```

---

**For complete deployment instructions, see inline documentation.**

