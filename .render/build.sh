#!/bin/bash
set -e

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install --production
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"
