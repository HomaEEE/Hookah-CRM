# -----------------------------------------------------------
# Stage 1: FrankenPHP Base (индивидуален для проекта)
# -----------------------------------------------------------
FROM dunglas/frankenphp:latest-php8.4-alpine AS base

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
COPY composer*.json ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# -----------------------------------------------------------
# Stage 3: Node Frontend Assets (Vite & Tailwind CSS v4)
# -----------------------------------------------------------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# -----------------------------------------------------------
# Final Stage: Production Runtime
# -----------------------------------------------------------
FROM base AS runtime
WORKDIR /app

# Копирование исходного кода и собранных артефактов
COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build

# Конфигурация и скрипты из папки deploy
COPY deploy/Caddyfile /etc/caddy/Caddyfile
COPY deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Настройка прав на директории кэша и storage
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
