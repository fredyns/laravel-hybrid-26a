#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

set -e

APP_CONTAINER="laravelkit26_app"

echo "==================================================================="
echo "                            MIGRATE                                "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

if [ -z "$(docker ps --format '{{.Names}}' | grep "^${APP_CONTAINER}")" ]; then
    echo "||   Docker container ${APP_CONTAINER} is not running   ||"
    echo "==================================================================="
    exit 1
fi

if ! docker exec ${APP_CONTAINER} test -f /var/www/html/artisan; then
    echo "|| Artisan file missing, maybe there is no code in /var/www/html ||"
    echo "==================================================================="
    exit 1
fi

echo "==================================================================="
echo "||                        SYSTEM CHECK                           ||"
echo "==================================================================="

docker exec ${APP_CONTAINER} php -v
docker exec ${APP_CONTAINER} node -v
docker exec ${APP_CONTAINER} npm -v

echo "\n install vendor (production, strict lock file)"
docker exec ${APP_CONTAINER} composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

# NOTE: no --omit=dev here. This project keeps its build toolchain (vite,
# tailwind, laravel-vite-plugin) plus laravel-echo/pusher-js under
# `dependencies`, and devDependencies is empty, so there is nothing to omit.
echo "\n install node dependencies"
docker exec ${APP_CONTAINER} npm ci --no-audit --no-fund

echo "\n set permissions"
# php-fpm workers run as `app` (uid 1000) per deployment/docker/php/www.conf.
docker exec ${APP_CONTAINER} mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache
docker exec ${APP_CONTAINER} sh -lc 'touch /var/www/html/storage/logs/laravel.log || true'
docker exec ${APP_CONTAINER} chown -R app:app /var/www/html/storage /var/www/html/bootstrap/cache
docker exec ${APP_CONTAINER} sh -lc 'find /var/www/html/storage /var/www/html/bootstrap/cache -type d -exec chmod 775 {} +'

echo "\n prepare laravel"
# APP_KEY is provisioned with the environment file. It is generated here only on
# a first-time deploy — regenerating it would invalidate every session, signed
# URL, and encrypted column.
if docker exec ${APP_CONTAINER} sh -lc 'grep -qE "^APP_KEY=base64:.+" /var/www/html/.env'; then
    echo "APP_KEY already set — skipping key:generate"
else
    echo "APP_KEY missing — generating one (first provisioning only)"
    docker exec ${APP_CONTAINER} php artisan key:generate --force
fi

docker exec ${APP_CONTAINER} php artisan storage:link || true

echo "\n build frontend assets"
docker exec ${APP_CONTAINER} npm run build

if docker exec ${APP_CONTAINER} test -f /var/www/html/public/build/manifest.json; then
    echo "Vite manifest.json found OK"
else
    echo "ERROR: Vite manifest.json NOT FOUND after build — aborting deploy."
    exit 1
fi

echo "\n migrate database"
docker exec ${APP_CONTAINER} php artisan migrate:status
docker exec ${APP_CONTAINER} php artisan migrate --force

# docker exec ${APP_CONTAINER} php artisan db:seed --class=SeederClass --force

echo "\n rebuild caches"
docker exec ${APP_CONTAINER} php artisan optimize:clear
docker exec ${APP_CONTAINER} php artisan config:cache
docker exec ${APP_CONTAINER} php artisan event:cache
docker exec ${APP_CONTAINER} php artisan view:cache
# route:cache is deliberately NOT run: routes/web.php and routes/settings.php
# register closure routes, which cannot be serialised.

echo "\n reload php-fpm"
docker exec ${APP_CONTAINER} kill -USR2 1
echo "PHP-FPM reload signaled"
