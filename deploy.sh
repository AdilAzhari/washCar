#!/bin/bash

# WashyWashy Pro - Deployment Script for cPanel
# This script automates the deployment process
# Run this script after pulling from GitHub

echo "🚀 Starting WashyWashy Pro Deployment..."

# Exit on any error
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}!${NC} $1"
}

# Check if .env file exists
if [ ! -f .env ]; then
    print_error ".env file not found!"
    print_warning "Please copy .env.example to .env and configure it first"
    exit 1
fi

print_status "Environment file found"

# Enable maintenance mode
print_status "Enabling maintenance mode..."
php artisan down || true

# Pull latest changes from Git
print_status "Pulling latest changes from GitHub..."
git pull origin main

# Install/Update Composer dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install/Update NPM dependencies and build assets
print_status "Installing Node.js dependencies..."
npm install --production

print_status "Building frontend assets..."
npm run build

# Run database migrations
print_status "Running database migrations..."
php artisan migrate --force

# Clear all caches
print_status "Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize the application
print_status "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set proper permissions
print_status "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;

# Create storage link if it doesn't exist
if [ ! -L public/storage ]; then
    print_status "Creating storage symlink..."
    php artisan storage:link
fi

# Disable maintenance mode
print_status "Disabling maintenance mode..."
php artisan up

print_status "Deployment completed successfully! 🎉"
print_warning "Don't forget to test your application!"

# Optional: Show current application status
echo ""
echo "📊 Application Status:"
php artisan --version
echo ""
php artisan route:list --columns=Method,URI,Name | head -n 20
