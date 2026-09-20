#!/bin/sh
set -e

# 1. Directories & Permissions
mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/app/public \
    /app/storage/logs \
    /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# 2. Application Encryption Key
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -f /app/storage/.app_key ]; then
        echo "==> Generating new APP_KEY..."
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > /app/storage/.app_key
    fi
    export APP_KEY="$(cat /app/storage/.app_key)"
fi

# 3. Database Readiness & Auto-Provisioning
DB_HOST="${DB_HOST:-mariadb}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-noire}"
DB_USERNAME="${DB_USERNAME:-noire_user}"
DB_PASSWORD="${DB_PASSWORD:-noire_secret_pass}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-}"

if [ "${DB_CONNECTION:-mysql}" = "mysql" ] || [ "${DB_CONNECTION:-mysql}" = "mariadb" ]; then
    echo "==> Checking database connection at ${DB_HOST}:${DB_PORT}..."
    i=1
    while [ "$i" -le 30 ]; do
        if php -r '
            $host     = getenv("DB_HOST") ?: "mariadb";
            $port     = getenv("DB_PORT") ?: "3306";
            $db       = getenv("DB_DATABASE") ?: "noire";
            $user     = getenv("DB_USERNAME") ?: "noire_user";
            $pass     = getenv("DB_PASSWORD") ?: "noire_secret_pass";
            $rootPass = getenv("DB_ROOT_PASSWORD");

            // If root password provided, ensure DB and user exist
            if (!empty($rootPass)) {
                try {
                    $rootPdo = new PDO("mysql:host={$host};port={$port}", "root", $rootPass, [
                        PDO::ATTR_TIMEOUT => 2,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]);
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}`;");
                    $rootPdo->exec("CREATE USER IF NOT EXISTS '\''{$user}'\''@'\''%'\'' IDENTIFIED BY '\''{$pass}'\'';");
                    $rootPdo->exec("ALTER USER '\''{$user}'\''@'\''%'\'' IDENTIFIED BY '\''{$pass}'\'';");
                    $rootPdo->exec("GRANT ALL PRIVILEGES ON `{$db}`.* TO '\''{$user}'\''@'\''%'\'';");
                    $rootPdo->exec("FLUSH PRIVILEGES;");
                } catch (Throwable $e) {
                    // Ignore and try user credentials directly
                }
            }

            try {
                new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                    PDO::ATTR_TIMEOUT => 2,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                exit(0);
            } catch (Throwable $e) {
                exit(1);
            }
        '; then
            echo "==> Database '${DB_DATABASE}' is ready."
            break
        fi
        echo "==> Waiting for database (${i}/30)..."
        i=$((i + 1))
        sleep 2
    done
fi

# 4. Atomic Database Migrations & Asset Upgrades
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running isolated database migrations..."
    php artisan migrate --force --isolated || true
fi

echo "==> Upgrading Filament and assets..."
php artisan filament:upgrade || true
php artisan pwa:manifest || true
php artisan storage:link --force || true

# 5. Production Cache Optimization
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "==> Caching application configuration..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
    php artisan icons:cache || true
fi

# 6. Runtime Server Dispatch
SERVER_MODE="${SERVER_MODE:-frankenphp}"

# Pass through custom shell commands (e.g. docker run ... bash / tinker)
if [ "$#" -gt 0 ] && [ "$1" != "frankenphp" ] && [ "$1" != "octane" ]; then
    exec "$@"
fi

if [ "$SERVER_MODE" = "octane" ]; then
    echo "==> Starting Laravel Octane (FrankenPHP worker)..."
    exec php artisan octane:start \
        --server=frankenphp \
        --host=0.0.0.0 \
        --port=80 \
        --workers="${OCTANE_WORKERS:-auto}" \
        --max-requests="${OCTANE_MAX_REQUESTS:-500}"
else
    echo "==> Starting native FrankenPHP..."
    exec frankenphp run --config /etc/caddy/Caddyfile
fi
