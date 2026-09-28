# Развёртывание проекта в OpenShip (MariaDB + Redis + FrankenPHP)

## Архитектура
- **CI/CD**: GitHub Actions автоматически собирает образ при пуше в `v4` или `main` и публикует в GitHub Container Registry (`ghcr.io/homaeee/hookah-crm:latest`).
- **Среда выполнения**: PHP 8.4 + FrankenPHP (Caddyfile `php_server` на порту 80).
- **База данных**: MariaDB (авто-провизионинг базы и прав пользователя через `DB_ROOT_PASSWORD` в `entrypoint.sh`).
- **Кэш/Очереди/Сессии**: Redis (`REDIS_HOST=redis`, порт `6379`).
- **Внешний прокси**: OpenResty / Caddy в OpenShip (SSL через Let's Encrypt, перенаправление на порт контейнера 80).

---

## 1. Настройка проекта в панели OpenShip

1. Создайте проект и выберите репозиторий `HomaEEE/Hookah-CRM` (ветка `v4`).
2. В качестве типа выберите **Docker Compose** (подхватится `openship.json` и `deploy/docker-compose.yml`).
3. В разделе **Environment Variables** укажите:

```env
APP_NAME="Noire"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://noire.od.ua

# MariaDB (контейнер в сети openship-mariadb)
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=noire
DB_USERNAME=noire_user
DB_PASSWORD=ВАШ_НАДЁЖНЫЙ_ПАРОЛЬ_ДЛЯ_БД
# Пароль root от MariaDB для автоматического создания БД 'noire' и прав:
DB_ROOT_PASSWORD=ВАШ_ROOT_ПАРОЛЬ_MARIADB

# Redis (контейнер в сети openship-mariadb)
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=ВАШ_ПАРОЛЬ_REDIS

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 2. Автоматическое создание БД и запуск

При каждом старте контейнера `deploy/entrypoint.sh`:
1. Проверяет доступность MariaDB по сокету.
2. При наличии `DB_ROOT_PASSWORD` автоматически выполняет:
   ```sql
   CREATE DATABASE IF NOT EXISTS `noire`;
   CREATE USER IF NOT EXISTS 'noire_user'@'%' IDENTIFIED BY '...';
   GRANT ALL PRIVILEGES ON `noire`.* TO 'noire_user'@'%';
   FLUSH PRIVILEGES;
   ```
3. Проверяет соединение с Redis и выполняет `PING`.
4. Накатывает миграции: `php artisan migrate --force`.
5. Оптимизирует Filament и Laravel кэши:
   - `php artisan storage:link --force`
   - `php artisan filament:upgrade --ansi`
   - `php artisan filament:optimize`
   - `php artisan config:cache`, `route:cache`, `view:cache`
6. Запускает FrankenPHP с конфигурацией `/etc/caddy/Caddyfile`.
