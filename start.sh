#!/bin/bash

echo "Starting deployment scripts..."

# Cache configuration, routes, and views for production performance
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations safely in production
echo "Running migrations..."
php artisan migrate --force

echo "Starting Apache..."
# Start the main apache server (must be the last command)
exec apache2-foreground
