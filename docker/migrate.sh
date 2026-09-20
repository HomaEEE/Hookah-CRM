#!/bin/sh
set -e

DB_HOST="${DB_HOST:-mariadb}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-noire}"
DB_USERNAME="${DB_USERNAME:-noire_user}"
DB_PASSWORD="${DB_PASSWORD:-noire_secret_pass}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-noire_root_pass}"

echo "==> Waiting for MariaDB at ${DB_HOST}:${DB_PORT}..."

i=1
while [ "$i" -le 60 ]; do
    if php -r '
        $host     = getenv("DB_HOST") ?: "mariadb";
        $port     = getenv("DB_PORT") ?: "3306";
        $db       = getenv("DB_DATABASE") ?: "noire";
        $user     = getenv("DB_USERNAME") ?: "noire_user";
        $pass     = getenv("DB_PASSWORD") ?: "noire_secret_pass";
        $rootPass = getenv("DB_ROOT_PASSWORD");

        // 1. If root password provided, ensure DB and user exist
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
                // Ignore and try user credentials
            }
        }

        // 2. Connect with application credentials and verify
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
        echo "==> MariaDB is ready and database '\''${DB_DATABASE}'\'' is verified."
        echo "==> Running database migrations..."
        php artisan migrate --force || true
        echo "==> Upgrading Filament assets..."
        php artisan filament:upgrade || true
        echo "==> Generating PWA manifest..."
        php artisan pwa:manifest || true
        echo "==> Migration stage completed successfully."
        exit 0
    fi

    echo "==> MariaDB is not ready yet (${i}/60)..."
    i=$((i + 1))
    sleep 2
done

echo "==> Warning: MariaDB did not become ready in time, continuing to allow web container to boot..."
exit 0
