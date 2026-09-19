#!/bin/sh
set -e

# Определение директории со скриптом
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
NETWORK_NAME="${SHARED_NETWORK_NAME:-shared_network}"

# 1. Проверка и создание общей Docker-сети
if ! docker network inspect "$NETWORK_NAME" >/dev/null 2>&1; then
    echo "==> Docker network '$NETWORK_NAME' not found. Creating..."
    docker network create "$NETWORK_NAME"
else
    echo "==> Docker network '$NETWORK_NAME' exists."
fi

# 2. Проверка наличия MariaDB
if ! docker ps -a --format '{{.Names}}' | grep -Eq "^mariadb$"; then
    echo "==> MariaDB container not found on server. Starting shared MariaDB..."
    docker compose -f "$SCRIPT_DIR/docker-compose.shared.yml" up -d mariadb
else
    echo "==> MariaDB container found."
    if ! docker ps --format '{{.Names}}' | grep -Eq "^mariadb$"; then
        echo "==> Starting existing MariaDB container..."
        docker start mariadb
    fi
fi

# 3. Проверка наличия Redis
if ! docker ps -a --format '{{.Names}}' | grep -Eq "^redis$"; then
    echo "==> Redis container not found on server. Starting shared Redis..."
    docker compose -f "$SCRIPT_DIR/docker-compose.shared.yml" up -d redis
else
    echo "==> Redis container found."
    if ! docker ps --format '{{.Names}}' | grep -Eq "^redis$"; then
        echo "==> Starting existing Redis container..."
        docker start redis
    fi
fi
