#!/bin/sh
set -e

# 1. Ensure storage and cache directories exist with correct permissions
mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/storage/backups \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# 2. Dynamic APP_URL resolution
if [ -z "${APP_URL:-}" ] || [ "${APP_URL}" = "http://localhost" ]; then
    if [ -n "${OPENSHIP_URL:-}" ]; then
        export APP_URL="${OPENSHIP_URL}"
    elif [ -n "${OPENSHIP_ENDPOINT:-}" ]; then
        export APP_URL="${OPENSHIP_ENDPOINT}"
    elif [ -n "${APP_ENDPOINT:-}" ]; then
        export APP_URL="${APP_ENDPOINT}"
    elif [ -n "${DOMAIN:-}" ]; then
        export APP_URL="https://${DOMAIN}"
    fi
fi

case "${APP_URL:-}" in
    http://*|https://*) ;;
    "") export APP_URL="http://localhost" ;;
    *) export APP_URL="https://${APP_URL}" ;;
esac

# 3. Persistent APP_KEY fallback
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -s /app/storage/.app_key ]; then
        echo "==> First deploy: Generating persistent APP_KEY..."
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > /app/storage/.app_key
    fi
    export APP_KEY="$(cat /app/storage/.app_key)"
fi

# 4. Database configuration and persistence
DB_HOST="${DB_HOST:-mariadb}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-noire}"
DB_USERNAME="${DB_USERNAME:-noire_user}"
DB_ROOT_USER="${DB_ROOT_USER:-root}"

if [ -z "${DB_PASSWORD:-}" ]; then
    if [ ! -s /app/storage/.db_password ]; then
        echo "==> First deploy: Generating persistent DB_PASSWORD..."
        php -r 'echo bin2hex(random_bytes(16));' > /app/storage/.db_password
    fi
    export DB_PASSWORD="$(cat /app/storage/.db_password)"
fi

# 5. Web service startup automation
if [ "${CONTAINER_ROLE:-web}" = "web" ]; then
    rm -f /app/bootstrap/cache/config.php /app/bootstrap/cache/packages.php /app/bootstrap/cache/services.php
    php artisan package:discover --ansi || true
    php artisan storage:link --force || true

    # Database connectivity & auto-provisioning
    if [ -n "$DB_HOST" ]; then
        echo "==> Checking database connection at ${DB_HOST}:${DB_PORT}..."
        i=1
        while [ "$i" -le 30 ]; do
            # 1. Quick TCP socket probe (2s timeout) to avoid blocking on startup
            TCP_OK=$(php -r "
                \$s = @fsockopen(getenv('DB_HOST'), getenv('DB_PORT') ?: 3306, \$err, \$errstr, 2);
                if (\$s) { fclose(\$s); exit(0); }
                exit(1);
            " 2>&1)
            if [ $? -ne 0 ]; then
                echo "==> Waiting for database host ${DB_HOST}:${DB_PORT} to accept connections (${i}/30)..."
                i=$((i + 1))
                sleep 2
                continue
            fi

            # 2. Auto-provision database & grant permissions if root password is provided
            if [ -n "${DB_ROOT_PASSWORD:-}" ]; then
                echo "==> Auto-provisioning database '${DB_DATABASE}' and user '${DB_USERNAME}'..."
                PROVISION_OUTPUT=$(mariadb --connect-timeout=5 -h "$DB_HOST" -P "$DB_PORT" -u "$DB_ROOT_USER" -p"$DB_ROOT_PASSWORD" 2>&1 <<EOSQL
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'%';
FLUSH PRIVILEGES;
EOSQL
)
                if [ $? -eq 0 ]; then
                    echo "==> Database '${DB_DATABASE}' and user '${DB_USERNAME}' provisioned successfully."
                else
                    echo "==> Warning: Database auto-provisioning encountered an error: ${PROVISION_OUTPUT}"
                fi
            fi

            # 3. Verify PDO connection
            DB_ERROR=$(php -r "
                \$host = getenv('DB_HOST');
                \$port = getenv('DB_PORT') ?: 3306;
                \$db   = getenv('DB_DATABASE');
                \$user = getenv('DB_USERNAME');
                \$pass = getenv('DB_PASSWORD');
                try {
                    new PDO(\"mysql:host={\$host};port={\$port};dbname={\$db}\", \$user, \$pass, [
                        PDO::ATTR_TIMEOUT => 2,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]);
                    exit(0);
                } catch (\Throwable \$e) {
                    echo \$e->getMessage();
                    exit(1);
                }
            " 2>&1)
            if [ $? -eq 0 ]; then
                echo "==> Database is ready."
                break
            fi

            echo "==> Waiting for database (${i}/30): ${DB_ERROR}"
            i=$((i + 1))
            sleep 2
        done
    fi

    # Redis connectivity check
    if [ -n "${REDIS_HOST:-}" ]; then
        echo "==> Checking Redis connection at ${REDIS_HOST}:${REDIS_PORT:-6379}..."
        REDIS_OK=$(php -r "
            \$s = @fsockopen(getenv('REDIS_HOST'), getenv('REDIS_PORT') ?: 6379, \$err, \$errstr, 2);
            if (\$s) { fclose(\$s); exit(0); }
            exit(1);
        " 2>&1)
        if [ $? -eq 0 ]; then
            echo "==> Redis host is reachable."
        else
            echo "==> Notice: Redis host ${REDIS_HOST}:${REDIS_PORT:-6379} is not reachable (${REDIS_OK})."
        fi
    fi

    # Pre-deploy backup & migrations
    if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
        if [ -n "$DB_HOST" ] && command -v mariadb-dump >/dev/null 2>&1; then
            echo "==> Creating pre-deploy database backup..."
            mariadb-dump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" \
                --single-transaction --quick "$DB_DATABASE" 2>/dev/null \
                | gzip > "/app/storage/backups/backup_$(date +%Y%m%d_%H%M%S).sql.gz" || true
        fi

        echo "==> Running database migrations..."
        php artisan migrate --force
        echo "==> Upgrading Filament assets..."
        php artisan filament:upgrade || true
        echo "==> Generating PWA manifest..."
        php artisan pwa:manifest || true
    fi

    # Production cache optimization
    if [ "${APP_ENV:-production}" = "production" ]; then
        php artisan config:cache
        php artisan route:cache || true
        php artisan view:cache || true
        php artisan icons:cache || true
    fi
fi

# 6. Pass through custom commands (e.g. docker run ... bash / tinker)
if [ "$#" -gt 0 ] && [ "$1" != "frankenphp" ] && [ "$1" != "octane" ]; then
    exec "$@"
fi

# 7. Runtime Server Dispatch: Octane vs native FrankenPHP
SERVER_MODE="${SERVER_MODE:-frankenphp}"
if [ "$SERVER_MODE" = "octane" ]; then
    if php artisan list 2>/dev/null | grep -q 'octane:start'; then
        echo "==> Starting Laravel Octane (FrankenPHP worker)..."
        exec php artisan octane:start \
            --server=frankenphp \
            --host=0.0.0.0 \
            --port=80 \
            --workers="${OCTANE_WORKERS:-auto}" \
            --max-requests="${OCTANE_MAX_REQUESTS:-500}"
    else
        echo "==> Notice: laravel/octane is not installed or enabled. Falling back to native FrankenPHP..."
        exec frankenphp run --config /etc/caddy/Caddyfile
    fi
else
    echo "==> Starting native FrankenPHP..."
    exec frankenphp run --config /etc/caddy/Caddyfile
fi
