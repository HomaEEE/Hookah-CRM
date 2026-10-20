# Руководство по инфраструктуре OpenShip: MariaDB, Redis, PMA и деплой проектов (Noire / Hookah-CRM)

Документ фиксирует рабочую конфигурацию серверов, контейнеров и сетей на сервере **158.51.125.88**, учётные данные, сетевую модель OpenShip и пошаговую инструкцию для построения автоматизированного CI/CD пайплайна подключения новых проектов к MariaDB и Redis.

---

## 1. Сведения о сервере и панели управления

- **Хост сервера**: `158.51.125.88`
- **ID сервера в OpenShip**: `24c92a66-6ee5-4f01-b203-d4cf482226e5` (`Hostodo Pool 1`)
- **Панель управления**: OpenShip (`https://os.blackcore.dev`)
- **Организация / Workspace**: `org_2ae9a65d-6fa8-4d18-8cdf-5db18b767d27`
- **Edge Proxy**: `openship-edge` (OpenResty 1.27.1, порты 80/443 на host-интерфейсе, SSL через Cloudflare / Let's Encrypt).
- **Конфигурация vhosts**: `/var/lib/openship/edge/sites-enabled/`

---

## 2. Учётные данные и сервисы инфраструктуры

Проект инфраструктуры в OpenShip: **`OpenShip-deploy`** (`proj_kO0hXVuYLXpNYE_k`)  
Исходный код: `HomaEEE/OpenShip-deploy` (ветка `main`, путь `services/mariadb-redis/docker-compose.yml`)

### 2.1. MariaDB 11.4 LTS
- **Имя контейнера**: `openship-openship-deploy-mariadb`
- **Основная сеть**: `openship-openship-deploy` (подсеть `172.18.0.0/16`, IP: `172.18.0.2`)
- **Внутренний порт**: `3306`
- **Сетевой алиас (DNS)**: `mariadb`
- **Том (Volume)**: `mariadb_data:/var/lib/mysql`
- **Root пользователь**: `root`
- **Root пароль**: `DbRoot_Secr3t_9x7vK2m`
- **Параметры запуска**:
  ```text
  --default-storage-engine=InnoDB
  --innodb-buffer-pool-size=192M
  --innodb-buffer-pool-instances=1
  --innodb-flush-log-at-trx-commit=2
  --innodb-flush-method=O_DIRECT
  --innodb-io-capacity=2000
  --innodb-io-capacity-max=4000
  --performance-schema=OFF
  --skip-name-resolve=1
  --max-connections=30
  --max-connect-errors=10000
  --thread-cache-size=16
  --wait-timeout=600
  --interactive-timeout=600
  ```

#### База данных проекта Noire:
- **Имя БД**: `noire`
- **Пользователь**: `noire`
- **Пароль**: `Noire_Db_Pass_4m8qW1z`
- **Привилегии**: `ALL PRIVILEGES ON \`noire\`.* TO 'noire'@'%'`

### 2.2. Redis 7.4-alpine
- **Имя контейнера**: `openship-openship-deploy-redis`
- **Основная сеть**: `openship-openship-deploy` (подсеть `172.18.0.0/16`, IP: `172.18.0.3`)
- **Внутренний порт**: `6379`
- **Сетевой алиас (DNS)**: `redis`
- **Том (Volume)**: `redis_data:/data`
- **Пароль аутентификации**: `Redis_Secr3t_7n2pL9y`
- **Параметры запуска**:
  ```text
  redis-server --requirepass Redis_Secr3t_7n2pL9y --maxmemory 48mb --maxmemory-policy allkeys-lru
  ```

### 2.3. phpMyAdmin (PMA)
- **Имя контейнера**: `openship-openship-deploy-phpmyadmin`
- **Локальный порт проксирования**: `127.0.0.1:20003` -> контейнер `:80`
- **Публичный домен**: **`https://pma.blackcore.dev`**
- **Статус**: `HTTP 200 OK`, форма входа активна.
- **Подключение к MariaDB**: автоматически направлено на хост `mariadb:3306`.
- **Вход**: пользователь `root` (пароль `DbRoot_Secr3t_9x7vK2m`) или `noire` (пароль `Noire_Db_Pass_4m8qW1z`).

---

## 3. Развёрнутый проект: Noire (Hookah-CRM)

Проект в OpenShip: **`Noire`** (`proj_Wcj5j7dVS_G53VQ2`)  
Исходный код: `HomaEEE/Hookah-CRM` (ветка `v4`, путь `deploy/docker-compose.yml`)

- **Имя контейнера**: `openship-noire-web`
- **Локальный порт**: `127.0.0.1:20000` -> контейнер `:80`
- **Публичный домен**: **`https://noire.od.ua`**
- **Стек runtime**: **FrankenPHP 1.x (PHP 8.4.26) + Caddy + Laravel 12 + Livewire + Filament 5**
- **Статус**: `HTTP 200 OK`, главная страница отдается со сжатием и кэшем.
- **Сессии и кэш**: активны в Redis (ключи с префиксами `noire_database_noire_cache_*`).
- **СУБД**: подключена к `mariadb:3306` (76 таблиц, все миграции применены).

### Переменные окружения Noire (`.env` в контейнере):
```dotenv
APP_NAME=Noire
APP_ENV=production
APP_DEBUG=false
APP_URL=https://noire.od.ua
APP_KEY=base64:BbmYidK6eBYLQf3AidkR0MKlwf6ztS3I1swz0ZOKwj0=

# Database
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=noire
DB_USERNAME=noire
DB_PASSWORD=Noire_Db_Pass_4m8qW1z
DB_ROOT_PASSWORD=DbRoot_Secr3t_9x7vK2m

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=Redis_Secr3t_7n2pL9y

# Drivers & Prefixes
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_PREFIX=noire_cache_
REDIS_PREFIX=noire_database_
SESSION_COOKIE=noire_session

# Web Server
SERVER_NAME=:80
FRANKENPHP_CONFIG="worker ./public/frankenphp-worker.php"
```

---

## 4. Сетевая модель OpenShip и межпроектное взаимодействие

### 4.1. Особенность OpenShip
OpenShip для каждого создаваемого проекта автоматически генерирует изолированную Docker-сеть bridge:
- Проект инфраструктуры: сеть `openship-openship-deploy` (172.18.0.0/16).
- Проект Noire: сеть `openship-noire` (172.19.0.0/16).
- Любой будущий проект: сеть `openship-<project_slug>` (172.20.0.0/16 и т.д.).

### 4.2. Как контейнеры получают доступ к `mariadb` и `redis`
В Docker контейнер может быть одновременно подключен к двум и более сетям:
1. Контейнер проекта создаётся в своей сети проекта (для связи со своим edge-роутером).
2. Контейнер подключается к сети СУБД командой:
   ```bash
   docker network connect openship-openship-deploy <container_name>
   ```
3. После подключения к сети `openship-openship-deploy` внутренний Docker DNS начинает мгновенно разрешать имена `mariadb` и `redis` для этого контейнера!

---

## 5. Точные команды, выполненные при деплое

### Шаг 1. Создание и деплой инфраструктуры MariaDB + Redis + PMA
```bash
# Через OpenShip MCP / API:
# 1. post_projects
#    name: "OpenShip-deploy", slug: "openship-deploy", serverId: "24c92a66-6ee5-4f01-b203-d4cf482226e5"
#    gitOwner: "HomaEEE", gitRepo: "OpenShip-deploy", gitBranch: "main"
#    composePath: "services/mariadb-redis/docker-compose.yml"
#
# 2. post_deployments_build_access (передача паролей)
#    MARIADB_ROOT_PASSWORD: "DbRoot_Secr3t_9x7vK2m"
#    REDIS_PASSWORD: "Redis_Secr3t_7n2pL9y"
#    ENABLE_PHPMYADMIN: "true"

# 3. Маршрутизация PMA на домен pma.blackcore.dev
#    post_domains: hostname: "pma.blackcore.dev", isPrimary: true, externalIngress: true
#    patch_projects_by_id_services_by_serviceId (svc_dKAb83icx1YcXcPx): customDomain: "pma.blackcore.dev", exposed: true, exposedPort: "80"
#    patch_projects_by_id: port: 20003, routeStrategy: "loopback-port"
#    post_projects_by_id_routing_retry
```

### Шаг 2. Создание базы данных и пользователя под Noire
```bash
docker exec openship-openship-deploy-mariadb mariadb -u root -pDbRoot_Secr3t_9x7vK2m -e "
CREATE DATABASE IF NOT EXISTS \`noire\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'noire'@'%' IDENTIFIED BY 'Noire_Db_Pass_4m8qW1z';
ALTER USER 'noire'@'%' IDENTIFIED BY 'Noire_Db_Pass_4m8qW1z';
GRANT ALL PRIVILEGES ON \`noire\`.* TO 'noire'@'%';
FLUSH PRIVILEGES;
"
```

### Шаг 3. Деплой проекта Noire в OpenShip
```bash
# 1. post_projects
#    name: "Noire", slug: "noire", serverId: "24c92a66-6ee5-4f01-b203-d4cf482226e5"
#    gitOwner: "HomaEEE", gitRepo: "Hookah-CRM", gitBranch: "v4"
#    composePath: "deploy/docker-compose.yml", port: 80

# 2. post_deployments_build_access
#    Передача переменных окружения (APP_KEY, DB_HOST=mariadb, DB_DATABASE=noire, DB_USERNAME=noire, DB_PASSWORD, REDIS_HOST=redis, REDIS_PASSWORD...)
#    Маршрутизация noire.od.ua -> порт 80
```

### Шаг 4. Подключение контейнера Noire к сети MariaDB/Redis
```bash
docker network connect openship-openship-deploy openship-noire-web
docker restart openship-noire-web
```

---

## 6. Универсальный пайплайн для автоматизации деплоя других проектов

Для подключения любого нового проекта к MariaDB и Redis достаточно автоматизировать 2 действия после сборки контейнера в OpenShip:
1. Создать БД и пользователя в MariaDB.
2. Подключить контейнер проекта к сети `openship-openship-deploy`.

### 6.1. Скрипт автоматического подключения (`openship-link-infra.sh`)
Создайте на сервере или положите в репозиторий скрипт автоматического линкования:

```bash
#!/usr/bin/env bash
set -euo pipefail

PROJECT_SLUG="$1"              # например: newapp
DB_NAME="${2:-$PROJECT_SLUG}"  # имя БД
DB_USER="${3:-$PROJECT_SLUG}"  # имя пользователя
DB_PASS="$4"                   # пароль пользователя

INFRA_NET="openship-openship-deploy"
MARIADB_CONTAINER="openship-openship-deploy-mariadb"
ROOT_PASS="DbRoot_Secr3t_9x7vK2m"

# 1. Создание БД и пользователя в MariaDB
echo "==> Provisioning database '${DB_NAME}' in MariaDB..."
docker exec "$MARIADB_CONTAINER" mariadb -u root -p"$ROOT_PASS" -e "
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'%';
FLUSH PRIVILEGES;
"
echo "==> Database '${DB_NAME}' provisioned."

# 2. Поиск контейнера проекта и подключение к общей сети
TARGET_CONTAINER="$(docker ps --filter "name=openship-${PROJECT_SLUG}" --format '{{.Names}}' | head -n 1)"

if [[ -z "$TARGET_CONTAINER" ]]; then
    echo "==> ERROR: Container for project '${PROJECT_SLUG}' not found."
    exit 1
fi

echo "==> Connecting '${TARGET_CONTAINER}' to '${INFRA_NET}'..."
if ! docker inspect "$TARGET_CONTAINER" --format '{{json .NetworkSettings.Networks}}' | grep -q "$INFRA_NET"; then
    docker network connect "$INFRA_NET" "$TARGET_CONTAINER"
    docker restart "$TARGET_CONTAINER"
    echo "==> Successfully connected '${TARGET_CONTAINER}' to '${INFRA_NET}'."
else
    echo "==> '${TARGET_CONTAINER}' is already connected to '${INFRA_NET}'."
fi
```

### 6.2. Шаблон GitHub Actions для нового проекта
В `.github/workflows/deploy.yml` нового проекта:

```yaml
name: Deploy to OpenShip

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Trigger OpenShip Deploy Webhook
        run: |
          curl -f -X POST "${{ secrets.OPENSHIP_WEBHOOK_URL }}"

      - name: Link container to MariaDB & Redis Network via SSH
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: 158.51.125.88
          username: root
          key: ${{ secrets.SERVER_SSH_KEY }}
          script: |
            PROJECT_CONTAINER="openship-${{ secrets.PROJECT_SLUG }}-web"
            INFRA_NET="openship-openship-deploy"
            
            # Подключаем контейнер к сети СУБД (идемпотентно)
            if ! docker inspect "$PROJECT_CONTAINER" --format '{{json .NetworkSettings.Networks}}' | grep -q "$INFRA_NET"; then
              docker network connect "$INFRA_NET" "$PROJECT_CONTAINER" || true
              docker restart "$PROJECT_CONTAINER" || true
            fi
```

### 6.3. Переменные окружения для подключения любого нового проекта:
В настройках переменных нового проекта в OpenShip указываются стандартные алиасы:
- `DB_CONNECTION`: `mysql`
- `DB_HOST`: `mariadb`
- `DB_PORT`: `3306`
- `DB_DATABASE`: `<project_name>`
- `DB_USERNAME`: `<project_user>`
- `DB_PASSWORD`: `<project_password>`
- `REDIS_HOST`: `redis`
- `REDIS_PORT`: `6379`
- `REDIS_PASSWORD`: `Redis_Secr3t_7n2pL9y`
- `CACHE_STORE`: `redis`
- `SESSION_DRIVER`: `redis`
- `QUEUE_CONNECTION`: `redis`
