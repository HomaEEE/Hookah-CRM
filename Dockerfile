# syntax=docker/dockerfile:1.7

# ── Stage 1: PHP dependencies ────────────────────────────────────────────────
# Must run BEFORE the asset build: resources/css imports vendor assets (Filament),
# so npm run build fails without vendor/ present.
FROM php:8.4-cli AS vendor

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip intl bcmath pcntl pdo_mysql gd exif redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ── Stage 2: Front-end assets (Vite + Filament theme) ────────────────────────
FROM node:22-slim AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci --no-audit --no-fund
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ── Stage 3: Runtime (FrankenPHP + Caddy) ────────────────────────────────────
FROM dunglas/frankenphp:1-php8.4-bookworm AS runtime

RUN apt-get update && apt-get install -y --no-install-recommends \
      mariadb-client \
 && install-php-extensions zip intl bcmath pcntl pdo_mysql gd exif redis opcache \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build
COPY deploy/Caddyfile /etc/caddy/Caddyfile
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini

RUN php artisan filament:assets && \
    mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
