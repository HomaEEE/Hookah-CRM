#!/bin/sh
set -e

mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# 1. APP_KEY: используем переданный или генерируем/сохраняем в storage
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -f /app/storage/.app_key ]; then
        echo "==> Generating new APP_KEY..."
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > /app/storage/.app_key
    fi
    export APP_KEY="$(cat /app/storage/.app_key)"
fi

if [ "${CONTAINER_ROLE:-web}" = "web" ]; then
    echo "==> Initializing web container..."
    php artisan storage:link --force || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan icons:cache || true
fi

echo "==> Starting FrankenPHP on :80..."
exec "$@"
