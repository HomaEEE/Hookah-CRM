#!/bin/sh
set -e

# ==============================================================================
# Helper functions
# ==============================================================================
set_env_var() {
    key="$1"
    value="$2"
    file="/app/.env"

    if [ -z "$value" ]; then
        return 0
    fi

    if [ ! -f "$file" ]; then
        touch "$file"
    fi

    if grep -qE "^${key}=" "$file"; then
        escaped_val=$(printf '%s\n' "$value" | sed -e 's/[\/&]/\\&/g')
        sed -i "s/^${key}=.*/${key}=\"${escaped_val}\"/" "$file"
    else
        printf '%s="%s"\n' "$key" "$value" >> "$file"
    fi
}

echo "==> Bootstrapping application environment..."

# Ensure storage directories exist with correct permissions
mkdir -p /app/storage/framework/cache/data \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/app/public \
         /app/storage/logs \
         /app/bootstrap/cache

# 1. Ensure .env exists
if [ ! -f /app/.env ]; then
    if [ -f /app/.env.example ]; then
        cp /app/.env.example /app/.env
    else
        touch /app/.env
    fi
fi

# 2. Sync Core Environment Variables to .env
set_env_var "APP_NAME" "${APP_NAME:-Noire}"
set_env_var "APP_ENV" "${APP_ENV:-production}"
set_env_var "APP_DEBUG" "${APP_DEBUG:-false}"
set_env_var "APP_URL" "${APP_URL:-http://localhost}"

# Ensure APP_KEY exists
if [ -z "${APP_KEY:-}" ]; then
    APP_KEY=$(grep -E "^APP_KEY=" /app/.env 2>/dev/null | cut -d '=' -f2- | tr -d '"')
fi
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application key (APP_KEY)..."
    php artisan key:generate --force
fi

# 3. Database configuration
export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-mariadb}"
export DB_PORT="${DB_PORT:-3306}"
export DB_DATABASE="${DB_DATABASE:-noire}"
export DB_USERNAME="${DB_USERNAME:-noire}"
export DB_ROOT_USER="${DB_ROOT_USER:-root}"

if [ -z "${DB_PASSWORD:-}" ]; then
    DB_PASSWORD=$(grep -E "^DB_PASSWORD=" /app/.env 2>/dev/null | cut -d '=' -f2- | tr -d '"')
    if [ -z "${DB_PASSWORD:-}" ]; then
        echo "==> Generating persistent DB_PASSWORD..."
        DB_PASSWORD=$(php -r 'echo bin2hex(random_bytes(16));')
    fi
fi
export DB_PASSWORD

set_env_var "DB_CONNECTION" "${DB_CONNECTION}"
set_env_var "DB_HOST" "${DB_HOST}"
set_env_var "DB_PORT" "${DB_PORT}"
set_env_var "DB_DATABASE" "${DB_DATABASE}"
set_env_var "DB_USERNAME" "${DB_USERNAME}"
set_env_var "DB_PASSWORD" "${DB_PASSWORD}"

# 4. Redis configuration
export REDIS_HOST="${REDIS_HOST:-redis}"
export REDIS_PORT="${REDIS_PORT:-6379}"
if [ -z "${REDIS_PASSWORD:-}" ]; then
    REDIS_PASSWORD=$(grep -E "^REDIS_PASSWORD=" /app/.env 2>/dev/null | cut -d '=' -f2- | tr -d '"')
fi
export REDIS_PASSWORD="${REDIS_PASSWORD:-}"
export CACHE_PREFIX="${CACHE_PREFIX:-noire_}"
export REDIS_PREFIX="${REDIS_PREFIX:-noire_database_}"

set_env_var "REDIS_HOST" "${REDIS_HOST}"
set_env_var "REDIS_PORT" "${REDIS_PORT}"
set_env_var "REDIS_PASSWORD" "${REDIS_PASSWORD}"

set_env_var "CACHE_STORE" "${CACHE_STORE:-redis}"
set_env_var "SESSION_DRIVER" "${SESSION_DRIVER:-redis}"
set_env_var "QUEUE_CONNECTION" "${QUEUE_CONNECTION:-redis}"
set_env_var "CACHE_PREFIX" "${CACHE_PREFIX}"
set_env_var "REDIS_PREFIX" "${REDIS_PREFIX}"

# Root password for MariaDB auto-creation
ROOT_PW="${DB_ROOT_PASSWORD:-${MARIADB_ROOT_PASSWORD:-}}"

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
            # 1. Quick TCP socket probe
            SOCKET_ERR=$(php -r '
                $host = getenv("DB_HOST");
                $port = (int)(getenv("DB_PORT") ?: 3306);
                $s = @fsockopen($host, $port, $errno, $errstr, 2);
                if ($s) { fclose($s); exit(0); }
                echo "$errstr ($errno)";
                exit(1);
            ' 2>&1)
            if [ $? -ne 0 ]; then
                echo "==> Waiting for database host ${DB_HOST}:${DB_PORT} (${i}/30): ${SOCKET_ERR}"
                i=$((i + 1))
                sleep 2
                continue
            fi

            # 2. Auto-provision database & user if root password is provided
            if [ -n "${ROOT_PW:-}" ]; then
                echo "==> Auto-provisioning database '${DB_DATABASE}' and user '${DB_USERNAME}'..."
                php -r '
                    $host = getenv("DB_HOST");
                    $port = (int)(getenv("DB_PORT") ?: 3306);
                    $root = getenv("DB_ROOT_USER") ?: "root";
                    $rootPw = getenv("ROOT_PW");
                    $db = getenv("DB_DATABASE");
                    $user = getenv("DB_USERNAME");
                    $pass = getenv("DB_PASSWORD");

                    try {
                        $pdo = new PDO("mysql:host={$host};port={$port}", $root, $rootPw, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 5
                        ]);
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS \`{$db}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        if ($user && $user !== "root") {
                            $pdo->exec("CREATE USER IF NOT EXISTS \x27{$user}\x27@\x27%\x27 IDENTIFIED BY \x27{$pass}\x27");
                            $pdo->exec("ALTER USER \x27{$user}\x27@\x27%\x27 IDENTIFIED BY \x27{$pass}\x27");
                            $pdo->exec("GRANT ALL PRIVILEGES ON \`{$db}\`.* TO \x27{$user}\x27@\x27%\x27");
                            $pdo->exec("FLUSH PRIVILEGES");
                        }
                        echo "==> Database and user provisioned successfully.\n";
                    } catch (\Throwable $e) {
                        fwrite(STDERR, "==> DB Provisioning notice: " . $e->getMessage() . "\n");
                    }
                '

                # Immediately wipe root password from environment memory
                unset DB_ROOT_PASSWORD MARIADB_ROOT_PASSWORD ROOT_PW
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
            " 2>&1) || true

            if [ -z "$DB_ERROR" ]; then
                echo "==> Database is ready and verified."
                break
            fi

            echo "==> Waiting for database (${i}/30): ${DB_ERROR}"
            i=$((i + 1))
            sleep 2
        done
    fi

    # Redis connectivity check
    if [ -n "${REDIS_HOST:-}" ]; then
        echo "==> Checking Redis connection at ${REDIS_HOST}:${REDIS_PORT}..."
        if php -r "
            \$redis = new Redis();
            try {
                \$redis->connect(getenv('REDIS_HOST'), (int)(getenv('REDIS_PORT') ?: 6379), 2);
                if (getenv('REDIS_PASSWORD')) {
                    \$redis->auth(getenv('REDIS_PASSWORD'));
                }
                \$redis->ping();
                exit(0);
            } catch (\Throwable \$e) {
                exit(1);
            }
        " >/dev/null 2>&1; then
            echo "==> Redis host is reachable and authenticated."
        else
            echo "==> Notice: Redis host ${REDIS_HOST}:${REDIS_PORT} is not yet reachable or password unauthenticated."
        fi
    fi

    # Run database migrations
    if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
        echo "==> Running database migrations..."
        php artisan migrate --force
        echo "==> Upgrading Filament assets..."
        php artisan filament:upgrade --ansi || true
        echo "==> Optimizing Filament..."
        php artisan filament:optimize || true
    fi

    # Production cache optimization
    if [ "${APP_ENV:-production}" = "production" ]; then
        echo "==> Caching Laravel configuration for production..."
        php artisan config:cache
        php artisan route:cache || true
        php artisan view:cache || true
        php artisan icons:cache || true
    fi
fi

# Ensure storage permissions for www-data
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true

# Pass through custom command if provided
if [ "$#" -gt 0 ] && [ "$1" != "frankenphp" ]; then
    exec "$@"
fi

# Start native FrankenPHP server
echo "==> Starting FrankenPHP via Caddyfile on port 80..."
exec frankenphp run --config /etc/caddy/Caddyfile
