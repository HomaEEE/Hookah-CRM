#!/bin/sh
set -e

# ==============================================================================
# OpenShip Shared Infrastructure Bootstrap Script
# Idempotently initializes the shared network on the server
# ==============================================================================

NETWORK_NAME="${SHARED_NETWORK:-openship}"

echo "==> [1/2] Checking Docker network: ${NETWORK_NAME}..."
if ! docker network inspect "${NETWORK_NAME}" >/dev/null 2>&1; then
    echo "==> Creating external Docker network: ${NETWORK_NAME}..."
    docker network create "${NETWORK_NAME}"
else
    echo "==> Network ${NETWORK_NAME} already exists."
fi

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
COMPOSE_SHARED="${SCRIPT_DIR}/docker-compose.shared.yml"

START_CONTAINERS="${START_SHARED_CONTAINERS:-false}"
if [ "$1" = "--with-services" ] || [ "$START_CONTAINERS" = "true" ]; then
    echo "==> [2/2] Starting fallback containerized MariaDB & Redis services..."
    if [ -f "${COMPOSE_SHARED}" ]; then
        docker compose -f "${COMPOSE_SHARED}" up -d
        docker ps --filter "name=mariadb" --filter "name=redis" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
    else
        echo "==> Error: ${COMPOSE_SHARED} not found."
        exit 1
    fi
else
    echo "==> [2/2] External MariaDB & Redis mode active. Shared network is ready."
fi
