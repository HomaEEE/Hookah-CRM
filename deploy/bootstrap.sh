#!/bin/sh
set -e

# ==============================================================================
# OpenShip Shared Infrastructure Bootstrap Script
# Idempotently initializes the shared network and databases on a fresh server
# ==============================================================================

NETWORK_NAME="${SHARED_NETWORK:-openship_shared}"

echo "==> [1/3] Checking Docker network: ${NETWORK_NAME}..."
if ! docker network inspect "${NETWORK_NAME}" >/dev/null 2>&1; then
    echo "==> Creating external Docker network: ${NETWORK_NAME}..."
    docker network create "${NETWORK_NAME}"
else
    echo "==> Network ${NETWORK_NAME} already exists."
fi

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
COMPOSE_SHARED="${SCRIPT_DIR}/docker-compose.shared.yml"

echo "==> [2/3] Starting shared MariaDB & Redis services..."
if [ -f "${COMPOSE_SHARED}" ]; then
    docker compose -f "${COMPOSE_SHARED}" up -d
else
    echo "==> Error: ${COMPOSE_SHARED} not found."
    exit 1
fi

echo "==> [3/3] Shared infrastructure status:"
docker ps --filter "name=mariadb" --filter "name=redis" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
