#!/bin/sh
set -e

echo "==> Bootstrapping application environment..."

# ------------------------------------------------------------------------------
# 0. Sync and Export Environment from openship.json
# ------------------------------------------------------------------------------
if [ -f "openship.json" ]; then
    # Export missing variables from openship.json into shell environment
    eval "$(php -r '
        $json = @json_decode(file_get_contents("openship.json"), true);
        if (!empty($json["env"]) && is_array($json["env"])) {
            foreach ($json["env"] as $k => $v) {
                if (getenv($k) === false || getenv($k) === "") {
                    $val = is_array($v) ? ($v["value"] ?? "") : (string)$v;
                    if ($val !== "") {
                        echo "export {$k}=" . escapeshellarg($val) . "\n";
                    }
                }
            }
        }
    ')"

    # Create .env if it does not exist
    if [ ! -f ".env" ]; then
        echo "==> Generating .env from openship.json..."
        php -r '
            $json = @json_decode(file_get_contents("openship.json"), true);
            if (!empty($json["env"]) && is_array($json["env"])) {
                $lines = [];
                foreach ($json["env"] as $k => $v) {
                    $val = is_array($v) ? ($v["value"] ?? "") : (string)$v;
                    if (preg_match("/\s/", $val)) {
                        $val = "\"" . addcslashes($val, "\"\\$") . "\"";
                    }
                    $lines[] = "{$k}={$val}";
                }
                file_put_contents(".env", implode("\n", $lines) . "\n");
                echo "==> Generated .env with " . count($lines) . " variables.\n";
            }
        '
    fi
fi

# ------------------------------------------------------------------------------
# 0.1 APP_KEY: use provided, else reuse/generate one persisted in storage volume
# ------------------------------------------------------------------------------
KEY_FILE="storage/app/.app_key"
if [ -z "${APP_KEY:-}" ]; then
    if [ ! -s "$KEY_FILE" ]; then
        echo "==> APP_KEY not set. Generating and persisting to $KEY_FILE..."
        mkdir -p "$(dirname "$KEY_FILE")"
        (umask 077; php artisan key:generate --show --no-interaction > "$KEY_FILE")
    fi
    export APP_KEY="$(cat "$KEY_FILE")"
fi

# ------------------------------------------------------------------------------
# 1. Resolve MariaDB Host
# ------------------------------------------------------------------------------
TARGET_DB_PORT="${DB_PORT:-3306}"
INITIAL_DB_HOST="${DB_HOST:-mariadb}"

# Default gateway IP (docker host) from /proc/net/route, hex little-endian -> dotted
GATEWAY_IP="$(awk '$2=="00000000"{h=$3; printf "%d.%d.%d.%d", strtonum("0x" substr(h,7,2)), strtonum("0x" substr(h,5,2)), strtonum("0x" substr(h,3,2)), strtonum("0x" substr(h,1,2)); exit}' /proc/net/route 2>/dev/null || true)"
if [ -z "$GATEWAY_IP" ]; then
    GATEWAY_IP="$(php -r '
        foreach (@file("/proc/net/route") ?: [] as $l) {
            $f = preg_split("/\s+/", trim($l));
            if (($f[1] ?? "") === "00000000") { echo long2ip(unpack("V", pack("H*", $f[2]))[1]); break; }
        }
    ' 2>/dev/null || true)"
fi

probe_db() {
    php -r "
        \$fp = @fsockopen('$1', (int)'$2', \$errno, \$errstr, 2);
        if (\$fp) { fclose(\$fp); exit(0); }
        exit(1);
    " 2>/dev/null
}

if probe_db "$INITIAL_DB_HOST" "$TARGET_DB_PORT"; then
    RESOLVED_DB_HOST="$INITIAL_DB_HOST"
else
    echo "==> Notice: '$INITIAL_DB_HOST:$TARGET_DB_PORT' unreachable. Probing fallback hosts..."
    for candidate in mariadb openship-deploy-mariadb-1 openship-deploy-mariadb openship-openship-deploy-mariadb openship-openship-deploy-mariadb-1 openship-deploy_mariadb_1 mariadb-redis-mariadb mariadb-redis-mariadb-1 mariadb-1 host.docker.internal 172.17.0.1 $GATEWAY_IP; do
        if probe_db "$candidate" "$TARGET_DB_PORT"; then
            RESOLVED_DB_HOST="$candidate"
            echo "==> Discovered MariaDB at '$RESOLVED_DB_HOST'"
            break
        fi
    done
fi

if [ -z "$RESOLVED_DB_HOST" ]; then
    echo "==> ERROR: MariaDB not reachable from this container. Attach it to the same docker network as 'mariadb' (set INFRA_NETWORK to its real name: docker inspect <mariadb> --format '{{json .NetworkSettings.Networks}}'), or set DB_HOST to a reachable host/IP."
    exit 1
fi
export DB_HOST="$RESOLVED_DB_HOST"

# ------------------------------------------------------------------------------
# 2. Resolve Redis Host
# ------------------------------------------------------------------------------
TARGET_REDIS_PORT="${REDIS_PORT:-6379}"
INITIAL_REDIS_HOST="${REDIS_HOST:-redis}"

probe_redis() {
    php -r "
        \$fp = @fsockopen('$1', (int)'$2', \$errno, \$errstr, 2);
        if (\$fp) { fclose(\$fp); exit(0); }
        exit(1);
    " 2>/dev/null
}

if probe_redis "$INITIAL_REDIS_HOST" "$TARGET_REDIS_PORT"; then
    RESOLVED_REDIS_HOST="$INITIAL_REDIS_HOST"
else
    echo "==> Notice: '$INITIAL_REDIS_HOST:$TARGET_REDIS_PORT' unreachable. Probing fallback hosts..."
    for candidate in redis openship-deploy-redis-1 openship-deploy-redis openship-openship-deploy-redis openship-openship-deploy-redis-1 openship-deploy_redis_1 mariadb-redis-redis mariadb-redis-redis-1 redis-1 host.docker.internal 172.17.0.1 $GATEWAY_IP; do
        if probe_redis "$candidate" "$TARGET_REDIS_PORT"; then
            RESOLVED_REDIS_HOST="$candidate"
            echo "==> Discovered Redis at '$RESOLVED_REDIS_HOST'"
            break
        fi
    done
fi

export REDIS_HOST="${RESOLVED_REDIS_HOST:-$INITIAL_REDIS_HOST}"

# ------------------------------------------------------------------------------
# 3. Auto-Provision Database and User via PDO
# ------------------------------------------------------------------------------
php -r '
$host = getenv("DB_HOST") ?: "mariadb";
$port = (int)(getenv("DB_PORT") ?: 3306);
$dbName = preg_replace("/[^a-zA-Z0-9_]/", "", getenv("DB_DATABASE"));
$dbUser = preg_replace("/[^a-zA-Z0-9_]/", "", getenv("DB_USERNAME"));
$dbPass = getenv("DB_PASSWORD") ?: "";
$rootPass = getenv("DB_ROOT_PASSWORD") ?: "";

if (empty($dbName) || empty($dbUser)) {
    echo "==> ERROR: DB_DATABASE or DB_USERNAME is not configured.\n";
    exit(1);
}

// 1. Проверяем: возможно база и пользователь уже созданы и доступны
try {
    $testPdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName}", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2,
    ]);
    echo "==> MariaDB: Database [{$dbName}] already accessible with user [{$dbUser}]. Skipping root provisioning.\n";
    exit(0);
} catch (\Throwable $e) {
    // База или пользователь пока недоступны — требуется создание через root
}

// 2. Если требуется создание, но root-пароль не задан — фатальная ошибка
if ($rootPass === "") {
    echo "==> ERROR: Database [{$dbName}] not accessible with [{$dbUser}] and DB_ROOT_PASSWORD is not provided.\n";
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port}", "root", $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    
    $quotedPass = $pdo->quote($dbPass);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("CREATE USER IF NOT EXISTS \"{$dbUser}\"@\"%\" IDENTIFIED BY {$quotedPass}");
    $pdo->exec("ALTER USER \"{$dbUser}\"@\"%\" IDENTIFIED BY {$quotedPass}");
    $pdo->exec("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO \"{$dbUser}\"@\"%\"");
    $pdo->exec("FLUSH PRIVILEGES");

    echo "==> MariaDB: Database [{$dbName}] and user [{$dbUser}] provisioned successfully.\n";
} catch (\Throwable $e) {
    echo "==> ERROR provisioning MariaDB: " . $e->getMessage() . "\n";
    exit(1);
}
'

# Security: clear root password from memory
unset DB_ROOT_PASSWORD
export DB_ROOT_PASSWORD=""

# ------------------------------------------------------------------------------
# 4. Wait for Database Readiness with Project Credentials
# ------------------------------------------------------------------------------
echo "==> Checking database connection at ${DB_HOST}:${DB_PORT}..."
MAX_TRIES=5
COUNT=0

until php -r '
$host = getenv("DB_HOST");
$port = (int)(getenv("DB_PORT") ?: 3306);
$db = getenv("DB_DATABASE");
$user = getenv("DB_USERNAME");
$pass = getenv("DB_PASSWORD");

try {
    $dsn = "mysql:host={$host};port={$port}" . ($db ? ";dbname={$db}" : "");
    new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2,
    ]);
    exit(0);
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit(1);
}
' 2>/dev/null; do
    COUNT=$((COUNT + 1))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "==> ERROR: Database connection timed out after ${MAX_TRIES} attempts."
        exit 1
    fi
    echo "==> Waiting for database ($COUNT/$MAX_TRIES)..."
    sleep 2
done

echo "==> Database connected successfully."

# ------------------------------------------------------------------------------
# 5. Application Setup & Migrations
# ------------------------------------------------------------------------------
php artisan storage:link --quiet || true
php artisan optimize:clear
php artisan filament:assets --quiet || true
chown -R www-data:www-data public/js public/css public/fonts 2>/dev/null || true

echo "==> Running database migrations..."
php artisan migrate --force

# Run module migrations if present
if [ -d "Modules" ]; then
    for module_mig in Modules/*/database/migrations Modules/*/Database/Migrations; do
        if [ -d "$module_mig" ]; then
            php artisan migrate --path="$module_mig" --force || true
        fi
    done
fi

# ------------------------------------------------------------------------------
# 6. Production Caching
# ------------------------------------------------------------------------------
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "==> Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan icons:cache 2>/dev/null || true
    php artisan filament:optimize 2>/dev/null || true
fi

# ------------------------------------------------------------------------------
# 7. Start FrankenPHP
# ------------------------------------------------------------------------------
if [ "$#" -gt 0 ] && [ "$1" != "frankenphp" ]; then
    exec "$@"
fi

echo "==> Starting FrankenPHP on port ${PORT:-80}..."
exec frankenphp run --config /etc/caddy/Caddyfile
