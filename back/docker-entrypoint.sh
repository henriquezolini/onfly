#!/bin/sh
set -e

# Install dependencies if vendor is missing
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found, running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

# Copy .env if missing
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Check if database exists
if [ ! -f "database/database.sqlite" ]; then
    echo "Database not found. Creating and seeding..."
    touch database/database.sqlite
    php artisan migrate --force
    php artisan db:seed --force
else
    echo "Database exists. Running migrations to ensure schema is up to date..."
    php artisan migrate --force
fi

echo "Starting server..."
exec "$@"
