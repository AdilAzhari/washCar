# WashyWashy Pro - cPanel Deployment Guide

This guide will help you deploy the WashyWashy Pro Laravel application to cPanel using GitHub.

## Prerequisites

- cPanel hosting account with:
  - PHP 8.2 or higher
  - Composer installed
  - Node.js and npm (for building assets)
  - MySQL database
  - SSH access (recommended) or Terminal in cPanel
  - Git version control

## Deployment Steps

### 1. Prepare Your cPanel Environment

#### Create MySQL Database
1. Log in to cPanel
2. Go to **MySQL Databases**
3. Create a new database (e.g., `username_washywashy`)
4. Create a new database user with a strong password
5. Add the user to the database with **ALL PRIVILEGES**
6. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### 2. Setup GitHub Repository

#### On Your Local Machine
```bash
# Navigate to your project directory
cd C:/laragon/www/washywashy-laravel

# Add all files to git
git add .

# Create initial commit
git commit -m "Initial commit - WashyWashy Pro application"

# Create a new repository on GitHub (via browser)
# Then link it to your local repository
git remote add origin https://github.com/YOUR_USERNAME/washywashy-pro.git
git branch -M main
git push -u origin main
```

### 3. Clone Repository to cPanel

#### Via SSH (Recommended)
```bash
# SSH into your cPanel server
ssh username@yourdomain.com

# Navigate to your home directory (usually ~)
cd ~

# Clone your repository
git clone https://github.com/YOUR_USERNAME/washywashy-pro.git

# Or clone into a specific folder
git clone https://github.com/YOUR_USERNAME/washywashy-pro.git washywashy
```

#### Via cPanel Terminal
1. Open **Terminal** in cPanel
2. Run the same git clone commands as above

### 4. Configure Your Application

#### Create .env File
```bash
cd washywashy  # or your cloned directory name

# Copy the example environment file
cp .env.example .env

# Edit the .env file
nano .env  # or use cPanel File Manager
```

#### Update .env with Your Details
```env
APP_NAME="WashyWashy Pro"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_washywashy
DB_USERNAME=username_dbuser
DB_PASSWORD=your_strong_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies (if not already done)
npm install --production

# Build frontend assets
npm run build
```

### 6. Setup Application

```bash
# Generate application key
php artisan key:generate

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### 7. Configure Web Root

You need to point your domain to the `public` directory of your Laravel application.

#### Option A: Using .htaccess in Root (if you can't change document root)

If your domain points to `public_html`, create this `.htaccess` file in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ washywashy/public/$1 [L]
</IfModule>
```

#### Option B: Change Document Root (Recommended)

1. In cPanel, go to **Domains** or **Addon Domains**
2. Find your domain
3. Click **Manage**
4. Change the **Document Root** to: `/home/username/washywashy/public`
5. Save changes

### 8. Verify Installation

Visit your domain in a browser. You should see the WashyWashy Pro login page.

### 9. Create Admin User

```bash
# Via SSH or Terminal
php artisan tinker

# Then in tinker:
User::create([
    'name' => 'Admin User',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('your_secure_password'),
    'email_verified_at' => now()
]);
```

Or run the seeder if you have one:
```bash
php artisan db:seed --class=UserSeeder
```

## Post-Deployment

### Security Checklist
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL certificate (via cPanel or Let's Encrypt)
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Never commit `.env` file to Git
- [ ] Set up regular database backups
- [ ] Configure email for password resets

### Performance Optimization
```bash
# Optimize composer autoloader
composer install --optimize-autoloader --no-dev

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# For production, also consider:
php artisan optimize
```

## Updating Your Application

When you make changes and want to deploy them:

```bash
# SSH into your server
ssh username@yourdomain.com
cd washywashy

# Pull latest changes from GitHub
git pull origin main

# Update dependencies if composer.json changed
composer install --no-dev --optimize-autoloader

# Rebuild frontend assets if changed
npm install --production
npm run build

# Run new migrations if any
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Error: 500 Internal Server Error
1. Check storage and bootstrap/cache permissions (755)
2. Verify `.env` file exists and has correct database credentials
3. Check error logs in `storage/logs/laravel.log`
4. Check cPanel error logs

### Error: Base table or view not found
```bash
# Run migrations
php artisan migrate --force
```

### Error: No application encryption key has been specified
```bash
php artisan key:generate
```

### Assets not loading (CSS/JS)
1. Make sure `npm run build` was executed
2. Check `APP_URL` in `.env` matches your domain
3. Clear cache: `php artisan config:clear`

### Database Connection Error
1. Verify database credentials in `.env`
2. Ensure database user has proper privileges
3. Check if database host is correct (usually `localhost`)

## Scheduled Tasks (Cron Jobs)

Laravel's task scheduler requires a cron job:

1. In cPanel, go to **Cron Jobs**
2. Add a new cron job:
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `cd /home/username/washywashy && php artisan schedule:run >> /dev/null 2>&1`

## Queue Workers (Optional)

If you're using queues:

1. Use cPanel's **Node.js App** or **Python App** interface to create a background process
2. Or set up a cron job that runs every minute:
```bash
* * * * * cd /home/username/washywashy && php artisan queue:work --stop-when-empty
```

## Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs
- Check Inertia.js documentation: https://inertiajs.com
- Check cPanel documentation for your hosting provider

## File Structure on Server

```
/home/username/
├── washywashy/              # Your Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/              # Document root should point here
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── .env                 # Your environment config
│   └── ...
└── public_html/             # cPanel default (if using .htaccess redirect)
```

## Important Notes

1. **Never** commit the `.env` file to Git
2. **Always** use `--force` flag with artisan commands in production (they won't prompt)
3. **Always** back up your database before running migrations
4. Keep your Git repository **private** if it contains any sensitive configuration
5. Use **environment variables** for all sensitive data
6. Enable **2FA** on your GitHub account for security
7. Set up **daily backups** via cPanel

---

Generated for WashyWashy Pro - Car Wash Management System
