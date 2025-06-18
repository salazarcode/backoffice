#!/bin/bash

# docker-entrypoint.sh
# Entrypoint script for Laravel app container
#
# This script performs the following actions:
#   1. Copies .env.example to .env if .env does not exist
#   2. Sets database environment variables in .env
#   3. Prepares storage and cache directories
#   4. Installs PHP and Node dependencies
#   5. Builds frontend assets
#   6. Generates Laravel app key
#   7. Runs database migrations
#   8. Starts the Laravel development server

# Step 1: Copy .env if it doesn't exist and set DB variables
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i 's/DB_HOST=.*/DB_HOST=db/' .env
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel/' .env
    sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel/' .env
    sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=secret/' .env
fi

# Step 2: Prepare storage and cache directories
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage
chmod -R 775 public/storage
chmod -R 777 storage bootstrap/cache

# Step 3: Install PHP and Node dependencies
composer install --no-interaction --no-progress
npm install
npm run build

# Step 4: Generate Laravel app key
php artisan key:generate --force

# Step 5: Run database migrations
php artisan migrate --force

# Step 5.1: Run database seeders
php artisan db:seed --force

# Step 5.2: Create storage symlink for public access to uploaded files
if [ ! -L public/storage ]; then
    rm -rf public/storage
    ln -s ../storage/app/public public/storage
fi

# Step 6: Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
