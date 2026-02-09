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

# Run migrations if database file doesn't exist or just to be safe
# Note: In a real production env, you might want to run this manually or more carefully
echo "Running migrations..."
touch database/database.sqlite
php artisan migrate --force

echo "Starting server..."
exec "$@"
