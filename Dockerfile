# -----------------------------------------------------------
# Stage 1: FrankenPHP Base (индивидуален для проекта)
# -----------------------------------------------------------
FROM dunglas/frankenphp:1-php8.4-alpine AS base

# Установка mariadb-client для автоматической инициализации базы данных при первом запуске
RUN apk add --no-cache mariadb-client

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    redis \
    bcmath \
    intl \
    gd \
    zip \
    pcntl \
    opcache \
    excimer \
    sodium

# -----------------------------------------------------------
# Stage 2: Composer Dependencies
# -----------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# -----------------------------------------------------------
# Stage 3: Node Frontend Assets (Vite & Tailwind CSS v4)
# -----------------------------------------------------------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN --mount=type=bind,from=vendor,source=/app/vendor,target=/app/vendor npm run build

# -----------------------------------------------------------
# Final Stage: Production Runtime
# -----------------------------------------------------------
FROM base AS runtime
WORKDIR /app

# Копирование исходного кода и собранных зависимостей/ассетов
COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build

# Настройки PHP 8.4
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "max_execution_time = 120" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.enable_cli = 1" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.memory_consumption = 256" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.max_accelerated_files = 30000" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.validate_timestamps = 0" >> /usr/local/etc/php/conf.d/99-custom.ini

# Конфигурация Caddy для FrankenPHP
RUN cat <<'EOF' > /etc/caddy/Caddyfile
{
    {$CADDY_GLOBAL_OPTIONS}
    frankenphp
    order php_server before file_server
}

:80 {
    root * /app/public
    encode zstd gzip

    php_server

    file_server

    header {
        X-Content-Type-Options "nosniff"
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
    }
}
EOF

# Entrypoint скрипт с автоматическим сохранением и неповторяющейся генерацией ключей/паролей
RUN cat <<'EOF' > /usr/local/bin/entrypoint.sh
#!/bin/sh
set -e

# Создание базовой структуры каталогов в persistent volume
mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

# 1. APP_KEY: используем переданный, либо сохраняем/считываем из тома storage
if [ -z "$APP_KEY" ]; then
    if [ ! -f /app/storage/.app_key ]; then
        echo "==> First deploy: Generating new APP_KEY..."
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > /app/storage/.app_key
    fi
    export APP_KEY="$(cat /app/storage/.app_key)"
fi

# 2. DB_PASSWORD: используем переданный, либо сохраняем/считываем из тома storage
if [ -z "$DB_PASSWORD" ]; then
    if [ ! -f /app/storage/.db_password ]; then
        echo "==> First deploy: Generating new DB_PASSWORD..."
        php -r 'echo bin2hex(random_bytes(16));' > /app/storage/.db_password

        # Автоматическое создание БД и пользователя в MariaDB при первом запуске (если передан DB_ROOT_PASSWORD)
        if [ "$CONTAINER_ROLE" = "app" ] && [ -n "$DB_ROOT_PASSWORD" ]; then
            echo "==> Provisioning MariaDB database and user..."
            NEW_PASS="$(cat /app/storage/.db_password)"
            mariadb -h "$DB_HOST" -P "${DB_PORT:-3306}" -u root -p"$DB_ROOT_PASSWORD" <<EOSQL || true
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE:-noire}\`;
CREATE USER IF NOT EXISTS '${DB_USERNAME:-noire_user}'@'%' IDENTIFIED BY '${NEW_PASS}';
ALTER USER '${DB_USERNAME:-noire_user}'@'%' IDENTIFIED BY '${NEW_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE:-noire}\`.* TO '${DB_USERNAME:-noire_user}'@'%';
FLUSH PRIVILEGES;
EOSQL
        fi
    fi
    export DB_PASSWORD="$(cat /app/storage/.db_password)"
fi

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Действия, выполняемые только основным web-контейнером
if [ "$CONTAINER_ROLE" = "app" ]; then
    echo "==> Running deployment automation..."
    php artisan storage:link --force || true

    if [ -n "$DB_HOST" ]; then
        echo "==> Checking database connection ($DB_HOST)..."
        for i in $(seq 1 30); do
            if php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (\Exception \$e) { exit(1); }"; then
                echo "==> Database is ready!"
                break
            fi
            echo "==> Waiting for database... ($i/30)"
            sleep 2
        done
    fi

    echo "==> Running database migrations..."
    php artisan migrate --force || echo "==> Warning: Migration failed, continuing..."
    echo "==> Upgrading Filament..."
    php artisan filament:upgrade || true
    echo "==> Generating PWA manifest..."
    php artisan pwa:manifest || true
    echo "==> Caching application configurations..."
    php artisan optimize || true
    php artisan icons:cache || true
fi

exec "$@"
EOF
RUN chmod +x /usr/local/bin/entrypoint.sh

# Права доступа к storage и bootstrap/cache
RUN mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
