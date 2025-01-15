#!/bin/bash

cd /home/forge/imagine

# Pull from GitHub
git pull origin main

# Install composer dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Install npm dependencies and build assets
npm ci
npm run build

# Restart queue workers
php artisan queue:restart

# Reload PHP
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service php8.2-fpm reload ) 9>/tmp/fpmlock
