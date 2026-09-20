# syntax=docker/dockerfile:1.7

FROM dunglas/frankenphp:1-php8.4-alpine AS base

RUN apk add --no-cache \
    mariadb-client \
    icu-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip

# Keep the production image aligned with composer.lock platform requirements.\n# xml provides DOM/SimpleXML/XMLReader/XMLWriter; curl and mbstring are required by\n# Laravel/Filament dependencies. fileinfo/ctype/filter/hash/iconv/json/libxml/openssl/\n# pcre/session/tokenizer/zlib are provided by PHP core or the base image.\nENV REQUIRED_EXTENSIONS="pdo_mysql mysqli redis bcmath curl dom exif intl mbstring xml gd zip pcntl opcache excimer sodium"
RUN install-php-extensions ${REQUIRED_EXTENSIONS}

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

FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN --mount=type=cache,target=/root/.npm \
    npm ci --no-audit --no-fund

COPY . .
COPY --from=vendor /app/vendor /app/vendor
RUN npm run build

FROM base AS runtime
WORKDIR /app

COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/migrate.sh /usr/local/bin/migrate.sh

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
    chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/migrate.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD php -r '$s=@fsockopen("127.0.0.1",80,$e,$m,2); exit($s ? 0 : 1);'

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
