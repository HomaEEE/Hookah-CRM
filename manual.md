# Руководство по инфраструктуре и деплою OpenShip (Noire / Hookah-CRM)

Документ фиксирует текущую конфигурацию серверов, контейнеров и сетей в OpenShip, учётные данные и шаги восстановления базы данных.

---

## 1. Архитектура и сетевая структура на сервере

- **Панель управления**: OpenShip (`https://os.blackcore.dev`)
- **Воркспейс**: `Local User's workspace` (`org_2530a76f-d95a-4a78-ba53-4deb0cbb4b69`)
- **Сервер**: `1f974c1c-7d92-496e-99ae-d0495fda6fa8`
- **Единая сеть Docker**: `openship-openship-deploy` (подсеть `172.18.0.0/16`).
  - *Важно*: Новых Docker-сетей не создавалось. Все сервисы инфраструктуры запущены в общей сети проекта и доступны друг другу напрямую по именам хостов (`mariadb`, `redis`, `phpmyadmin`).

---

## 2. Развёрнутые контейнеры инфраструктуры (`OpenShip-deploy`)

Проект в OpenShip: `OpenShip-deploy` (`proj_7vDG4KJtN-wVAotZ`)

### 2.1. MariaDB (СУБД)
- **Имя сервиса / хост**: `mariadb`
- **Образ**: `mariadb:11.4`
- **Порт**: `3306` (внутренний)
- **IP в сети**: `172.18.0.2`
- **Том (Volume)**: `mariadb_data:/var/lib/mysql`
- **Параметры запуска**:
  ```text
  --character-set-server=utf8mb4
  --collation-server=utf8mb4_unicode_ci
  --bind-address=0.0.0.0
  --innodb-buffer-pool-size=512M
  --innodb-log-file-size=128M
  --max-connections=150
  ```
- **Учётные данные**:
  - `Root пользователь`: `root`
  - `Root пароль`: `DbRoot_Secr3t_9x7vK2m`
  - `База данных проекта`: `noire`
  - `Пользователь приложения`: `noire`
  - `Пароль пользователя`: `Noire_Db_Pass_4m8qW1z`

### 2.2. Redis (Кэш, Очереди, Сессии)
- **Имя сервиса / хост**: `redis`
- **Образ**: `redis:7.4-alpine`
- **Порт**: `6379` (внутренний)
- **IP в сети**: `172.18.0.3`
- **Том (Volume)**: `redis_data:/data`
- **Параметры запуска**:
  ```text
  redis-server --bind 0.0.0.0 --protected-mode no --appendonly yes --maxmemory 256mb --maxmemory-policy allkeys-lru --requirepass "Redis_Secr3t_7n2pL9y"
  ```
- **Пароль**: `Redis_Secr3t_7n2pL9y`

### 2.3. phpMyAdmin (Управление и восстановление БД)
- **Имя сервиса / хост**: `phpmyadmin`
- **Образ**: `phpmyadmin/phpmyadmin:latest`
- **Внутренний порт**: `80` (IP: `172.18.0.4`)
- **Внешний порт / роутинг OpenShip**: `20003` -> `pma.blackcore.dev`
- **Домен**: `https://pma.blackcore.dev`
- **Переменные окружения**:
  - `PMA_HOST=mariadb`
  - `PMA_PORT=3306`
  - `UPLOAD_LIMIT=512M`

---

## 3. Инструкция по восстановлению базы данных через phpMyAdmin

1. Откройте в браузере: **`https://pma.blackcore.dev`**
2. Авторизуйтесь:
   - **Пользователь**: `root`
   - **Пароль**: `DbRoot_Secr3t_9x7vK2m`
   *(Сервер `mariadb:3306` подключён автоматически).*
3. В левой колонке выберите базу данных **`noire`** (если база пустая, она уже создана).
4. Перейдите на вкладку **Импорт (Import)** в верхнем меню.
5. Нажмите **Выберите файл** и укажите дамп базы данных (`.sql` или `.sql.gz`).
6. Нажмите кнопку **Импорт (Import)** внизу страницы.
7. После успешного импорта проверьте, что таблицы загрузились (включая `inventory_items`, `users`, `warehouses` и др.).

---

## 4. Почему перезапускался контейнер приложения Laravel (`Hookah-CRM`)

В логах развертывания `web` было зафиксировано:
```text
==> Running database migrations...
2026_08_19_000741_add_sold_fields_to_inventory_items_table ..... FAIL
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'noire.inventory_items' doesn't exist
alter table `inventory_items` add `sold_quantity` int null after `quantity`
```

**Причина**: В пустой базе данных миграция `2026_08_19_000741_add_sold_fields_to_inventory_items_table` запускалась раньше создания базовой таблицы `inventory_items` (которая создаётся модулем `Modules/Manage`). 
После восстановления оригинального дампа базы через phpMyAdmin все базовые таблицы будут на месте, и миграции завершатся успешно.

---

## 5. Запуск приложения Noire (`Hookah-CRM`)

После восстановления дампа базы:
1. Контейнер `Hookah-CRM` в OpenShip перезапустится (или запустите вручную в панели).
2. [deploy/entrypoint.sh](file:///Users/arthurboyko/Sites/Noire/deploy/entrypoint.sh) выполнит:
   - Проверку сокета MariaDB (`mariadb:3306`) и Redis (`redis:6379`).
   - Связку `storage:link`.
   - `php artisan migrate --force`.
   - Кэширование Filament и роутов (`filament:optimize`, `config:cache`, `route:cache`).
   - Запуск **FrankenPHP** на порту 80 с поддержкой Cloudflare SSL на домене **`https://noire.od.ua`**.

---

## 6. Dockerfile приложения (Reference)

Файл [Dockerfile](file:///Users/arthurboyko/Sites/Noire/Dockerfile) оптимизирован под multi-stage сборку на FrankenPHP + PHP 8.4:

- **Этап 1 (vendor)**: Установка PHP-зависимостей через Composer 2 (`php:8.4-cli` с `pdo_mysql`, `redis`, `gd`, `exif`, `bcmath`, `intl`, `zip`, `pcntl`).
- **Этап 2 (assets)**: Сборка фронтенда (`node:22-slim`) скомпилированными стилями темы Filament 5 и Tailwind v4.
- **Этап 3 (runtime)**: `dunglas/frankenphp:1-php8.4-bookworm` с Caddyfile, клиентом MariaDB и OPcache.

---

## 7. Текущий статус сервисов (Проверено)

- **phpMyAdmin**: `https://pma.blackcore.dev`
  - Пользователь: `root`
  - Пароль: `DbRoot_Secr3t_9x7vK2m`
  - Доступ проверен: логин успешен, дамп базы импортирован (73 таблицы в наличии).
  - Альтернативный пользователь: `noire` / `Noire_Db_Pass_4m8qW1z` (база `noire`).
- **Сайт проекта**: `https://noire.od.ua`
  - Статус контейнера `web`: `running` (HTTP 200 OK).
  - Стек: FrankenPHP + Caddy + Redis (сессии и кэш) + MariaDB.

---

## 8. Оптимизация FrankenPHP и Caddy (Применено)

- **Многопоточность**: `num_threads: 4` (параллельная обработка до 4 одновременных PHP-запросов внутри процесса Caddy).
- **Лимиты ресурсов**: `memory_limit: 256M`, `max_execution_time: 60s`, `upload_max_filesize: 64M`.
- **PHP OPcache & JIT**:
  - `opcache.memory_consumption = 128`
  - `opcache.interned_strings_buffer = 16`
  - `opcache.jit = tracing` (32MB)
  - `opcache.preload = /app/deploy/preload.php` (предзагрузка ядра Laravel в shared memory)
- **Caddyfile**:
  - Защита от slowloris: `read_body 60s`, `read_header 10s`, `write 60s`, `idle 120s`.
  - Селективное сжатие: `encode zstd br gzip` только для текстовых/json/js/css/svg ответов.
  - Раздельный кэш: `/build/*` и Filament ассеты — `max-age=31536000, immutable`; `/storage/*` — `max-age=2592000, stale-while-revalidate`.
  - Безопасность: блокировка `/.git/*`, `/.env*`, `/composer.*`, удаление `Server` и `X-Powered-By`.


