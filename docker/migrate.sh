#!/bin/sh
set -eu

: "${DB_HOST:?DB_HOST is required}"
: "${DB_DATABASE:?DB_DATABASE is required}"
: "${DB_USERNAME:?DB_USERNAME is required}"
: "${DB_PASSWORD:?DB_PASSWORD is required}"

DB_PORT="${DB_PORT:-3306}"

echo "==> Waiting for MariaDB at ${DB_HOST}:${DB_PORT}..."

i=1
while [ "$i" -le 60 ]; do
    if php -r '
        $host=getenv("DB_HOST");
        $port=getenv("DB_PORT") ?: "3306";
        $db=getenv("DB_DATABASE");
        $user=getenv("DB_USERNAME");
        $pass=getenv("DB_PASSWORD");
        try {
            new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    '; then
        echo "==> MariaDB is ready."
        exec php artisan migrate --force
    fi

    echo "==> MariaDB is not ready yet (${i}/60)."
    i=$((i + 1))
    sleep 2
done

echo "==> MariaDB did not become ready in time."
exit 1
