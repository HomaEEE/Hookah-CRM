#!/bin/sh
set -eu

mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

: "${APP_KEY:?APP_KEY is required}"
: "${DB_HOST:?DB_HOST is required}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"

if [ "${CONTAINER_ROLE:-web}" = "web" ]; then
    php artisan storage:link --force || true
    php artisan config:cache
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan icons:cache || true
fi

exec "$@"
