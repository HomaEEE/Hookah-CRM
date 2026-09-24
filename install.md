# Полное руководство по деплою Laravel (Filament) проекта через UI панель OpenShip

Данный мануал описывает процесс развертывания проекта **Laravel** (с Filament Admin и FrankenPHP) через веб-интерфейс **OpenShip** с подключением к существующему контейнеру **MariaDB + Redis** на сервере.

---

## Содержание
1. [Предварительные требования](#1-предварительные-требования)
2. [Подготовка репозитория (Ассеты)](#2-подготовка-репозитория-ассеты)
3. [Связь контейнера Laravel с MariaDB / Redis](#3-связь-контейнера-laravel-с-mariadb--redis)
4. [Настройка проекта в UI панели OpenShip](#4-настройка-проекта-в-ui-панели-openship)
5. [Настройка переменных окружения (.env)](#5-настройка-переменных-окружения-env)
6. [Привязка домена и выпуск SSL](#6-привязка-домена-и-выпуск-ssl)
7. [Запуск и первый деплой](#7-запуск-и-первый-деплой)
8. [Решение частых проблем (Troubleshooting)](#8-решение-частых-проблем-troubleshooting)

---

## 1. Предварительные требования

Перед началом настройки убедитесь, что:
- Сервер с установленным **OpenShip** работает и доступен.
- На сервере уже запущен контейнер с **MariaDB** и **Redis** (например, в Docker-сети `openship-mariadb` или с опубликованными портами).
- Создана база данных (например, `noire`) и пользователь базы данных с полными правами к ней.
- Репозиторий проекта на GitHub подключен к OpenShip (выбрана нужная ветка, например `v4`).
- DNS A-запись вашего домена (например, `noire.od.ua`) направлена на публичный IP-адрес сервера.

---

## 2. Подготовка репозитория (Ассеты)

> **ВАЖНО!** В проектах с кастомной темой **Filament** стили ссылаются на файлы внутри директории `vendor/`:
> ```css
> @import "../../../../vendor/filament/filament/resources/css/theme.css";
> ```
> В изолированном этапе сборки фронтенда OpenShip директория `vendor/` отсутствует, что приводит к ошибке сборщика `enhanced-resolve`.
> 
> **Решение:** Фронтенд-ассеты собираются локально и пушатся в Git в папку `public/build/`.

### Локальные команды перед деплоем:
```bash
# 1. Собрать ассеты в продакшн
npm run build

# 2. Убедиться, что public/build отслеживается гитом
git add public/build -f
git commit -m "build: compile production assets"
git push origin v4
```

---

## 3. Связь контейнера Laravel с MariaDB / Redis

Для подключения приложения к существующему контейнеру базы данных есть два надежных способа:

### Вариант А: Единая Docker-сеть (Рекомендуется)
Если MariaDB работает в Docker-сети `openship-mariadb`, подключите контейнер OpenShip к этой сети после его создания:
```bash
# Найти имя контейнера приложения
docker ps --filter "name=hookah" --format "{{.Names}}"

# Подключить контейнер к сети с MariaDB
docker network connect openship-mariadb <CONTAINER_NAME>
```
*В переменных окружения указывается:*
- `DB_HOST=mariadb` (или имя контейнера базы данных)
- `REDIS_HOST=redis`

### Вариант Б: Через Docker Gateway (Шлюз хоста)
Если порт MariaDB (3306) и Redis (6379) проброшены на хост (`127.0.0.1` или `0.0.0.0`):
- `DB_HOST=172.17.0.1` (IP стандартного шлюза docker0) или `host.docker.internal`
- `REDIS_HOST=172.17.0.1`

---

## 4. Настройка проекта в UI панели OpenShip

Откройте панель создания/редактирования проекта в OpenShip:

![OpenShip Deploy Configuration](https://raw.githubusercontent.com/HomaEEE/Hookah-CRM/v4/resources/images/openship-guide.png)

### 4.1. Framework
- В верхнем блоке **Framework** выберите: **Laravel** *(Detected)*.

### 4.2. Deploy Configuration
Раскройте блок **Deploy Configuration**:

| Поле UI | Значение | Описание |
| :--- | :--- | :--- |
| **Build** (переключатель) | **ВЫКЛ** *(или ВКЛ с командой `true`)* | Отключает компиляцию ассетов на сервере |
| **Install Command** | `composer install --no-dev --optimize-autoloader` | Устанавливает PHP-зависимости |
| **Build Command** | `true` | Заглушка, так как ассеты уже собраны в `public/build` |
| **Output Directory** | `public` | Точка входа веб-сервера Laravel |

### 4.3. Start
В блоке запуска выберите режим **Server**:

- **Production Port (Optional):** `8000`
- **Start Command:**
  ```bash
  php artisan storage:link --force && php artisan migrate --force && SERVER_NAME=":$PORT" exec frankenphp run --config /etc/caddy/Caddyfile
  ```
  *(Данная команда автоматически создает симлинк хранилища, применяет свежие миграции базы данных и запускает веб-сервер FrankenPHP).*

---

## 5. Настройка переменных окружения (.env)

В нижней левой части интерфейса найдите блок **Environment Variables** и нажмите кнопку **Paste .env**.

Вставьте следующий шаблон, заменив плейсхолдеры на реальные данные:

```env
APP_NAME="Hookah CRM"
APP_ENV=production
APP_KEY=base64:ВАШ_СГЕНЕРИРОВАННЫЙ_КЛЮЧ_ПРИЛОЖЕНИЯ
APP_DEBUG=false
APP_URL=https://noire.od.ua

LOG_CHANNEL=stack
LOG_LEVEL=error

# Подключение к MariaDB
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=noire
DB_USERNAME=noire_user
DB_PASSWORD=ВАШ_ПАРОЛЬ_ОТ_БД

# Подключение к Redis
REDIS_HOST=redis
REDIS_PASSWORD=ВАШ_ПАРОЛЬ_ОТ_REDIS
REDIS_PORT=6379

# Драйверы сессий, кэша и очередей
BROADCAST_DRIVER=log
CACHE_STORE=redis
FILESYSTEM_DISK=public
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

> **Как сгенерировать APP_KEY:**
> На локальной машине в терминале проекта выполните:
> ```bash
> php artisan key:generate --show
> ```
> Скопируйте полученную строку вида `base64:...` в поле `APP_KEY`.

---

## 6. Привязка домена и выпуск SSL

В правой колонке интерфейса OpenShip:

1. В блоке **Domain** выберите вкладку **Custom**.
2. В поле ввода (где подсказка `app.example.com`) введите ваш домен:
   ```text
   noire.od.ua
   ```
3. Нажмите кнопку **`+`** (Добавить).
4. Убедитесь, что порт указан: `8000` (или `80`).
5. OpenShip Edge (на базе Caddy / OpenResty) автоматически:
   - Проверит привязку домена.
   - Выпустит валидный SSL-сертификат через **Let's Encrypt**.
   - Настроит автоматическое перенаправление с HTTP на HTTPS.

---

## 7. Запуск и первый деплой

1. Проверьте правильность заполненных полей в блоке **DEPLOY SUMMARY** справа:
   - **Framework:** Laravel
   - **Domain:** noire.od.ua
   - **Install:** `composer install --no-dev --optimize-autoloader`
   - **Output:** `public`
2. Нажмите черную кнопку **Deploy**.
3. Наблюдайте за логами сборки в реальном времени:
   - Клонирование репозитория.
   - Установка composer-пакетов.
   - Выполнение миграций `php artisan migrate --force`.
   - Запуск FrankenPHP на порту 8000.
4. По завершении статус сменится на **Live** / **Running**. Перейдите по адресу `https://noire.od.ua` для проверки работы приложения.

---

## 8. Решение частых проблем (Troubleshooting)

### Ошибка 1: `Error response from daemon: error from registry: unauthorized`
- **Причина:** В репозитории остался файл `openship.json` с директивой `composePath: deploy/docker-compose.yml`, указывающий на приватный образ GHCR.
- **Решение:** Удалите `openship.json` или убедитесь, что в нем указан `"framework": "laravel"` без `composePath`.

### Ошибка 2: `enhanced-resolve finishWithoutResolve ... theme.css`
- **Причина:** Попытка сборки ассетов через `npm run build` на сервере без наличия директории `vendor/`.
- **Решение:** Соберите ассеты локально (`npm run build`), закомитьте папку `public/build` в Git, а в UI OpenShip отключите сборку или укажите в **Build Command** значение `true`.

### Ошибка 3: `SQLSTATE[HY000] [2002] Connection refused`
- **Причина:** Контейнер Laravel не видит MariaDB по указанному `DB_HOST`.
- **Решение:** 
  1. Проверьте имя сети: `docker network ls`.
  2. Подключите контейнер к сети базы данных: `docker network connect openship-mariadb <CONTAINER_NAME>`.
  3. Либо укажите `DB_HOST=172.17.0.1` (шлюз Docker-моста) при проброшенном порте 3306.

### Ошибка 4: `419 Page Expired` или проблемы со входом в Filament
- **Причина:** Неверно настроен `APP_URL` или драйвер сессий.
- **Решение:** Убедитесь, что в переменных окружения задано точное значение: `APP_URL=https://noire.od.ua` (обязательно с `https://`).
