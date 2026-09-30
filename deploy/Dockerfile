# syntax=docker/dockerfile:1.7

ARG BASE_IMAGE=ghcr.io/homaeee/hookah-crm:base

# ── Stage 1: PHP dependencies ────────────────────────────────────────────────
FROM ${BASE_IMAGE} AS vendor
WORKDIR /app

# Cache Composer dependencies layer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Copy application source and dump optimized autoload
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ── Stage 2: Front-end assets (Vite + Filament theme) ────────────────────────
FROM node:22-slim AS assets
WORKDIR /app

# Cache npm dependencies layer
COPY package*.json ./
RUN npm ci --no-audit --no-fund

# Copy source + vendor for Tailwind scanning and theme @import
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ── Stage 3: Runtime (FrankenPHP) ────────────────────────────────────────────
FROM ${BASE_IMAGE} AS runtime
WORKDIR /app

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build
COPY deploy/Caddyfile /etc/caddy/Caddyfile
COPY deploy/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN php -d memory_limit=-1 artisan filament:assets || true \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache \
 && chmod +x /usr/local/bin/entrypoint.sh

# Enable OPcache preloading strictly for the runtime environment.
# Preload is kept runtime-only so that Composer install, dump-autoload,
# and build-time artisan commands never fail attempting to load missing files.
COPY deploy/php-runtime.ini /usr/local/etc/php/conf.d/zz-runtime-preload.ini

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
