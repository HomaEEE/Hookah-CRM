# syntax=docker/dockerfile:1.7
ARG PHP_VERSION=8.4
ARG NODE_VERSION=22

# ==============================================================================
# 1. Base Runtime Image (Pre-compiled FrankenPHP from GHCR)
# ==============================================================================
ARG BASE_IMAGE=ghcr.io/homaeee/frankenphp-base:8.4
FROM ${BASE_IMAGE} AS base

# ==============================================================================
# 2. Composer Dependencies
# ==============================================================================
FROM base AS vendor
WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts

# ==============================================================================
# 3. Frontend Assets (Vite & Tailwind CSS)
# ==============================================================================
FROM node:${NODE_VERSION}-bookworm-slim AS frontend
WORKDIR /app

COPY package*.json ./
RUN --mount=type=cache,target=/root/.npm \
    if [ -f package.json ]; then npm ci --no-audit --no-fund; fi

COPY . /app
COPY --from=vendor /app/vendor /app/vendor

RUN if [ -f package.json ] && grep -q '"build"' package.json; then \
        npm run build; \
    else \
        mkdir -p /app/public/build; \
    fi

# ==============================================================================
# 4. Production Runtime
# ==============================================================================
FROM base AS runtime
WORKDIR /app

COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build
COPY deploy/Caddyfile /etc/caddy/Caddyfile
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh

# Optimize composer classmap for production runtime (skip scripts during build)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN rm -f /app/bootstrap/cache/*.php && \
    composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative \
    --no-scripts && \
    rm -f /usr/bin/composer

# PHP Production Configuration
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "upload_max_filesize = 32M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "post_max_size = 32M" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.enable = 1" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.enable_cli = 1" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.memory_consumption = 64" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.max_accelerated_files = 10000" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo "opcache.validate_timestamps = 0" >> /usr/local/etc/php/conf.d/99-custom.ini && \
    mkdir -p /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/storage/app/public /app/storage/logs /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache && \
    chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD php -r '$s=@fsockopen("127.0.0.1",80,$e,$m,2); exit($s ? 0 : 1);'

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
