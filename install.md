# Полное руководство по деплою Laravel + Filament v4/v5 на продакшен через OpenShip

В данном руководстве описана эталонная архитектура сборки и развертывания проекта **Laravel 12** с **Filament PHP** и веб-сервером **FrankenPHP** через **OpenShip** с подключением к существующему на сервере контейнеру **MariaDB + Redis**.

---

## Архитектура развертывания

```text
  [GitHub Push (v4/main)]
            │
            ▼
 ┌──────────────────────┐
 │ GitHub Actions CI/CD │ 
 │ 1. composer install  │  (формирует vendor/)
 │ 2. npm run build     │  (успешно компилирует Filament theme.css)
 │ 3. docker build+push │  (собирает готовый образ и пушит в GHCR)
 └──────────┬───────────┘
            │ Webhook / Deploy trigger
            ▼
 ┌────────────────────────────────────────────────────────┐
 │                      OpenShip                          │
 │  1. Pull: ghcr.io/homaeee/hookah-crm:latest (секунды)  │
 │  2. Connects to external network: openship-mariadb     │
 │  3. Runs entrypoint.sh:                                │
 │     - Проверка MariaDB и Redis                         │
 │     - php artisan migrate --force                      │
 │     - php artisan filament:optimize                    │
 │     - php artisan config:cache / route:cache           │
 │     - Запуск FrankenPHP на порту 80/8000               │
 │  4. Caddy / OpenResty Edge: SSL (Let's Encrypt)        │
 └────────────────────────────────────────────────────────┘
```

---

## 1. Главная проблема сборки Filament на сервере

В Filament с кастомной темой файл `resources/css/filament/admin/theme.css` содержит импорт:
```css
@import "../../../../vendor/filament/filament/resources/css/theme.css";
@source "../../../../vendor/filament/**/*.blade.php";
```

Если собирать ассеты через стандартный buildpack на сервере, сборщик пытается выполнить `npm run build` до того, как установлены PHP-зависимости (`vendor/` еще не существует в Git). Это вызывает ошибку Vite / Tailwind v4:
```text
enhanced-resolve finishWithoutResolve ... theme.css
```

### Эталонное решение:
Сборка в Docker multi-stage (`deploy/Dockerfile`), где этап Composer выполняется **до** этапа Node:
1. `vendor` stage: `composer install` создает `/app/vendor`.
2. `frontend` stage: копирует `/app/vendor` и запускает `npm run build`.
3. `runtime` stage: упаковывает готовые файлы в легковесный production-образ.
4. Сервер **ничего не компилирует**, а только скачивает готовый образ за пару секунд.

---

## 2. Предварительная настройка: GitHub Container Registry (GHCR)

По умолчанию новые образы в GitHub Packages создаются как **Private**. Чтобы OpenShip мог скачивать образ без ввода токенов на сервере:

1. Откройте страницу пакета в GitHub:
   `https://github.com/users/HomaEEE/packages/container/package/hookah-crm/settings`
2. Прокрутите страницу вниз до раздела **Danger Zone**.
3. В пункте **Change package visibility** выберите **Public** и подтвердите ввод имени пакета.

*(Если образ должен оставаться приватным: на сервере один раз выполните `echo $GITHUB_PAT | docker login ghcr.io -u HomaEEE --password-stdin` с токеном с правами `read:packages`).*

---

## 3. Файлы конфигурации деплоя в репозитории

### 3.1. `openship.json` (в корне проекта)
```json
{
  "$schema": "https://openship.io/openship.schema.json",
  "framework": "docker-compose",
  "composePath": "deploy/docker-compose.yml",
  "port": 80,
  "domains": [
    "noire.od.ua"
  ]
}
```

### 3.2. `deploy/docker-compose.yml`
```yaml
services:
  web:
    image: ${APP_IMAGE:-ghcr.io/homaeee/hookah-crm}:${APP_VERSION:-latest}
    restart: unless-stopped
    ports:
      - "${APP_PORT:-8080}:80"
    env_file:
      - path: ../.env
        required: false
      - path: .env
        required: false
    environment:
      CONTAINER_ROLE: web
      SERVER_MODE: ${SERVER_MODE:-frankenphp}
      DB_HOST: mariadb
      REDIS_HOST: redis
    deploy:
      resources:
        limits:
          memory: 256M
    volumes:
      - app_storage:/app/storage
    networks:
      - openship-mariadb

volumes:
  app_storage:
    driver: local

networks:
  openship-mariadb:
    external: true
```

---

## 4. Настройка проекта в UI панели OpenShip

1. **Создание / выбор проекта:**
   - Репозиторий: `HomaEEE/Hookah-CRM`
   - Ветка: `v4` (или `main`)
   - Framework определится как **Docker Compose** (согласно `openship.json`).

2. **Переменные окружения (Environment Variables):**
   Внизу слева нажмите **Paste .env** и вставьте значения:
   ```env
   APP_NAME="Hookah CRM"
   APP_ENV=production
   APP_KEY=base64:ВАШ_КЛЮЧ_ПРИЛОЖЕНИЯ
   APP_DEBUG=false
   APP_URL=https://noire.od.ua

   LOG_CHANNEL=stack
   LOG_LEVEL=error

   # База данных MariaDB (контейнер в сети openship-mariadb)
   DB_CONNECTION=mysql
   DB_HOST=mariadb
   DB_PORT=3306
   DB_DATABASE=noire
   DB_USERNAME=noire_user
   DB_PASSWORD=ВАШ_ПАРОЛЬ_ОТ_БД

   # Redis (контейнер в сети openship-mariadb)
   REDIS_HOST=redis
   REDIS_PORT=6379
   REDIS_PASSWORD=ВАШ_ПАРОЛЬ_ОТ_REDIS

   # Драйверы
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

   > Ключ `APP_KEY` генерируется локально командой:
   > ```bash
   > php artisan key:generate --show
   > ```

3. **Привязка домена (Domain):**
   - В правой колонке выберите вкладку **Custom**.
   - Введите домен: `noire.od.ua`
   - Нажмите **`+`** (Добавить).
   - Убедитесь, что DNS A-запись домена указывает на IP-адрес сервера. OpenShip автоматически выпустит SSL-сертификат через Let's Encrypt.

---

## 5. Автоматические оптимизации Filament и Laravel на продакшене

Скрипт `deploy/entrypoint.sh` при каждом запуске контейнера автоматически выполняет полный цикл оптимизаций, рекомендованных командой Filament:

1. **Миграции базы данных:**
   ```bash
   php artisan migrate --force
   ```
2. **Оптимизация Filament (кэширование компонентов и иконок):**
   ```bash
   php artisan filament:optimize
   ```
   *(Ускоряет загрузку админ-панели в несколько раз за счет предкомпиляции Blade-иконок и схем форм).*
3. **Кэширование Laravel:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. **Симлинк хранилища:**
   ```bash
   php artisan storage:link --force
   ```
5. **Запуск FrankenPHP веб-сервера** на порту 80 с поддержкой HTTP/2, HTTP/3 и сжатия Brotli/Zstandard.

---

## 6. Деплой и проверка

1. Выполните коммит и пуш в ветку `v4`:
   ```bash
   git push origin v4
   ```
2. GitHub Actions соберет образ, протестирует сборку ассетов и загрузит его в GHCR.
3. В панели OpenShip нажмите **Deploy** (или сработает автоматический Webhook).
4. Проверьте логи в OpenShip:
   - Образ скачивается без компиляции.
   - Проверяется доступность MariaDB и Redis.
   - Проходят миграции.
   - Кешируются компоненты Filament.
   - Статус переходит в **Live**.
5. Откройте `https://noire.od.ua/admin` для входа в панель управления.
