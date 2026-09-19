#!/bin/sh
set -e

# Проверка и создание структуры каталогов в постоянном томе
mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Действия, выполняемые только основным web-контейнером
if [ "$CONTAINER_ROLE" = "app" ]; then
    echo "==> Running deployment automation..."

    # Симлинк для публичных файлов
    php artisan storage:link --force || true

    # Запуск миграций базы данных
    echo "==> Running database migrations..."
    php artisan migrate --force

    # Обновление ассетов Filament
    echo "==> Upgrading Filament..."
    php artisan filament:upgrade || true

    # Кэширование конфигураций
    echo "==> Caching application configurations..."
    php artisan optimize || true
    php artisan icons:cache || true
fi

exec "$@"
