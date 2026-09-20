#!/bin/sh
set -eu

: "${DB_HOST:?DB_HOST is required}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"

DB_PORT="${DB_PORT:-3306}"

echo "==> Waiting for MariaDB at ${DB_HOST}:${DB_PORT} and ensuring database exists..."

i=1
while [ "$i" -le 60 ]; do
    if php -r '
        $host     = getenv("DB_HOST");
        $port     = getenv("DB_PORT") ?: "3306";
        $db       = getenv("DB_DATABASE");
        $user     = getenv("DB_USERNAME");
        $pass     = getenv("DB_PASSWORD");
        $rootPass = getenv("DB_ROOT_PASSWORD");

        // 1. If DB_ROOT_PASSWORD is provided, ensure database and user exist on the MariaDB instance
        if (!empty($rootPass)) {
            try {
                $rootPdo = new PDO("mysql:host={$host};port={$port}", "root", $rootPass, [
                    PDO::ATTR_TIMEOUT => 3,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}`;");
                $rootPdo->exec("CREATE USER IF NOT EXISTS '\''{$user}'\''@'\''%'\'' IDENTIFIED BY '\''{$pass}'\'';");
                $rootPdo->exec("ALTER USER '\''{$user}'\''@'\''%'\'' IDENTIFIED BY '\''{$pass}'\'';");
                $rootPdo->exec("GRANT ALL PRIVILEGES ON `{$db}`.* TO '\''{$user}'\''@'\''%'\'';");
                $rootPdo->exec("FLUSH PRIVILEGES;");
            } catch (Throwable $e) {
                // Root provisioning failed or not needed; proceed to test application credentials
            }
        }

        // 2. Connect with application credentials and verify database
        try {
            $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}`;");
            new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    '; then
        echo "==> MariaDB is ready and database '${DB_DATABASE}' is verified."
        echo "==> Running database migrations..."
        php artisan migrate --force
        echo "==> Upgrading Filament assets..."
        php artisan filament:upgrade
        echo "==> Generating PWA manifest..."
        php artisan pwa:manifest || true
        echo "==> Migration stage completed successfully."
        exit 0
    fi

    echo "==> MariaDB is not ready yet (${i}/60)..."
    i=$((i + 1))
    sleep 2
done

echo "==> MariaDB did not become ready in time."
exit 1
