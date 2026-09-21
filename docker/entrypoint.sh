#!/bin/sh
set -e

# 1. Ensure storage directories exist
mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true


# 3. APP_URL endpoint resolution
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

# 3. APP_KEY generation & persistence
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -s /app/storage/.app_key ]; then
        echo "==> First deploy: Generating persistent APP_KEY..."
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > /app/storage/.app_key
    fi
    export APP_KEY="$(cat /app/storage/.app_key)"
fi

# 3. Database credentials generation & persistence
DB_HOST="${DB_HOST:-mariadb}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-noire}"
DB_USERNAME="${DB_USERNAME:-noire_user}"

if [ -z "${DB_PASSWORD:-}" ]; then
    if [ ! -s /app/storage/.db_password ]; then
        echo "==> First deploy: Generating persistent DB_PASSWORD..."
        php -r 'echo bin2hex(random_bytes(16));' > /app/storage/.db_password
    fi
    export DB_PASSWORD="$(cat /app/storage/.db_password)"
fi

# 4. Web service startup automation
if [ "${CONTAINER_ROLE:-web}" = "web" ]; then
    php artisan storage:link --force || true

    # Wait for database readiness & optional auto-provisioning
    if [ -n "$DB_HOST" ]; then
        echo "==> Checking database connection at ${DB_HOST}:${DB_PORT}..."
        i=1
        while [ "$i" -le 30 ]; do
            if [ -n "${DB_ROOT_PASSWORD:-}" ]; then
                mariadb -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_ROOT_PASSWORD" 2>/dev/null <<EOSQL || true
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'%';
FLUSH PRIVILEGES;
EOSQL
            fi

            if php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306).';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (\Throwable \$e) { exit(1); }"; then
                echo "==> Database is ready."
                break
            fi

            echo "==> Waiting for database (${i}/30)..."
            i=$((i + 1))
            sleep 2
        done
    fi

    # Run migrations if enabled
    if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
        if [ -n "$DB_HOST" ] && command -v mariadb-dump >/dev/null 2>&1; then
            echo "==> Creating pre-deploy database backup..."
            mkdir -p /app/storage/backups
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

    # Cache application configurations for production
    php artisan config:cache
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan icons:cache || true
fi

exec "$@"
