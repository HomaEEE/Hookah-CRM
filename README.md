# Hookah CRM — Laravel / Filament / OpenShip

Production deployment configuration for the **v4** branch.

The project is designed for the user's OpenShip deployment-node architecture:

- Laravel 12 + Filament 5
- PHP 8.4 + FrankenPHP
- Docker/BuildKit builds on the deployment VPS
- one shared MariaDB per deployment node
- one shared Redis per deployment node
- persistent application storage per project
- OpenShip Edge owns public routing
- application containers do not provision infrastructure credentials
- migrations run as a dedicated one-shot deployment service

## Deployment architecture

```
Internet
   │
   ▼
OpenShip Edge
   │
   ▼
Hookah CRM :80
   │
   ├── shared MariaDB  → DB_HOST=mariadb
   └── shared Redis    → REDIS_HOST=redis

Project volume:
   app_storage → /app/storage
```

The project does **not** run MariaDB or Redis itself. Both services are expected to exist on the deployment node and be attached to the shared Docker network.

Default shared database network:

```text
openship-network
```

Override it with:

```text
SHARED_NETWORK=openship-network
```

## Build pipeline

The base FrankenPHP runtime with pre-compiled extensions is built in **GitHub Actions** via `.github/workflows/docker-base.yml` and hosted on GHCR (`ghcr.io/homaeee/frankenphp-base:8.4`). The OpenShip server uses this base image, avoiding any on-server PHP extension compilation.

```text
GitHub Actions (on Dockerfile.base change)
        │
        └── Builds and pushes ghcr.io/homaeee/frankenphp-base:8.4
                │
                ▼
OpenShip Deployment Node (on push to v4)
        │
        └── Docker BuildKit
              ├── FROM ghcr.io/homaeee/frankenphp-base:8.4 (instant pull, no compilation)
              ├── Composer install (cached via /tmp/composer-cache)
              ├── npm run build (cached via /root/.npm)
              └── Produces local production image
```

## Runtime services

### web

FrankenPHP serves Laravel on container port `80`.

Resource limit:

```text
256 MB RAM
```

The host port is controlled by `APP_PORT`. On a multi-project server, every project must have a unique host port when OpenShip uses host-port routing.

### migrate

A separate one-shot container runs:

```bash
php artisan migrate --force
```

It waits for MariaDB before running migrations.

The web container depends on successful completion of this service.

This deliberately removes migrations from the normal web-container startup path.

## Persistent storage

Only Laravel runtime storage is owned by this project:

```text
app_storage:/app/storage
```

Database and Redis data are **not** project volumes.

They belong to the shared deployment-node infrastructure.

## Required production environment

At minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://your-domain.example

APP_PORT=8080

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=hookah_crm
DB_USERNAME=hookah_crm
DB_PASSWORD=...
DB_ROOT_PASSWORD=... # Optional: if set, auto-creates DB and user on startup

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=...

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

Secrets should be configured in OpenShip rather than committed to Git.

If `DB_ROOT_PASSWORD` is provided, the web container will automatically create the database and dedicated user on first boot if they do not already exist.

## Laravel startup

The web container performs lightweight, idempotent runtime tasks:

- creates required Laravel storage directories;
- fixes storage/cache permissions;
- auto-provisions database and dedicated user if `DB_ROOT_PASSWORD` is set;
- creates the storage symlink;
- caches configuration, routes, Blade views, and Filament icons.

## MariaDB / Redis

MariaDB and Redis are hosted externally or pre-installed on the host machine. Do not add these services back to this project's Compose file.

The target deployment-node model is:

```text
Target VPS
│
├── OpenShip Edge
│
├── MariaDB
│   ├── hookah_crm
│   ├── project_a
│   └── project_b
│
├── Redis
│
├── OpenShip project: Hookah CRM
│   ├── web
│   └── migrate
│
└── other projects
```

This prevents every Laravel project from starting another MariaDB and Redis instance.

## Horizon / Reverb

The Compose environment is already prepared for the shared Redis / Reverb topology:

```env
BROADCAST_CONNECTION=reverb
REVERB_HOST=reverb
REVERB_PORT=8080
```

Horizon and Reverb services are **not enabled in this commit**, because the current `composer.lock` does not contain `laravel/horizon` or `laravel/reverb`.

Do not fake these services at the Docker level. First add the Laravel packages and regenerate `composer.lock`; then add dedicated:

- `horizon` service;
- `reverb` service;
- scheduler service.

This keeps the production build deterministic.

## OpenShip

`openship.json` intentionally remains minimal:

```json
{
  "$schema": "https://openship.io/openship.schema.json",
  "framework": "docker-compose",
  "composePath": "docker-compose.yml",
  "port": 80
}
```

OpenShip reads the Compose stack and manages the services. Its current Compose implementation supports multi-service stacks, BuildKit builds, healthchecks, volumes, environment variables, dependencies and service-level deployment.

For production, configure the public domain on the `web` service through OpenShip Edge rather than publishing MariaDB/Redis or exposing internal services.

## Files

```text
.
├── Dockerfile
├── docker-compose.yml
├── openship.json
├── .dockerignore
└── docker/
    ├── Caddyfile
    ├── entrypoint.sh
    └── migrate.sh
```

## Validate before deployment

On a deployment node:

```bash
docker compose config
docker compose build web
```

For OpenShip:

```bash
openship config validate
```

OpenShip documents `openship.json` as a repository-level deployment declaration and supports validation before deployment.

## Important

This branch assumes the deployment node already provides:

1. Docker + Docker Compose v2 / BuildKit.
2. OpenShip Edge.
3. Shared Docker network `openship-network`.
4. Shared MariaDB reachable as `mariadb`.
5. Shared Redis reachable as `redis`.
6. A unique `APP_PORT` for this project if host-port routing is used.
7. OpenShip-managed HTTPS/domain routing.

The project itself is responsible only for the application runtime and its persistent Laravel storage.
