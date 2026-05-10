#!/bin/sh
set -e

cd /var/www/html

if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
    if php artisan migrate:status --no-interaction >/dev/null 2>&1; then
        php artisan migrate --force
    else
        php artisan migrate:fresh --seed --force
    fi
fi

exec apache2-foreground
