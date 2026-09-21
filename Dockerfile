# syntax=docker/dockerfile:1.7

# ==============================================================================
# 1. Base Runtime Image (Debian Bookworm)
# ==============================================================================
FROM dunglas/frankenphp:1-php8.4-bookworm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Precompiled binary extensions on Debian Bookworm (fast install, no source compilation)
ENV REQUIRED_EXTENSIONS="pdo_mysql mysqli redis bcmath curl dom exif intl mbstring xml gd zip pcntl opcache excimer sodium"
RUN install-php-extensions ${REQUIRED_EXTENSIONS}

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
# 3. Frontend Assets (Vite & Tailwind CSS v4)
# ==============================================================================
FROM node:22-bookworm-slim AS frontend
WORKDIR /app

COPY package*.json ./
RUN --mount=type=cache,target=/root/.npm \
    npm ci --no-audit --no-fund

# Copy only files required for Vite and Tailwind bundle scanning
COPY vite.config.js ./
COPY resources ./resources
COPY Modules ./Modules
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

# ==============================================================================
# 4. Production Runtime
# ==============================================================================
FROM base AS runtime
WORKDIR /app

COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Optimize composer classmap for production runtime
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload \
    --no-dev \
    --optimize \
    --classmap-authoritative && \
    rm -f /usr/bin/composer

# PHP 8.4 Production Configuration
RUN echo "memory_limit = 128M" > /usr/local/etc/php/conf.d/99-custom.ini && \
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
