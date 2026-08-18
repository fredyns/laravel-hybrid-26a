#!/bin/bash

echo "==================================================================="
echo "                            MIGRATE                                "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME}

APP_CONTAINER="laravelkit26_app"

# Check if the Docker container sikom26_app is running
if [ -z "$(docker ps --format '{{.Names}}' | grep "^${APP_CONTAINER}")" ]; then
    echo "||   Docker container ${APP_CONTAINER} is not running   ||"
    echo "==================================================================="
    exit 0
fi

echo "||                        SYSTEM CHECK                           ||"
echo "==================================================================="

docker exec ${APP_CONTAINER} pwd
docker exec ${APP_CONTAINER} ls -la

echo "==================================================================="

# check versions
docker exec ${APP_CONTAINER} which php
docker exec ${APP_CONTAINER} php -v
docker exec ${APP_CONTAINER} which node
docker exec ${APP_CONTAINER} node -v
docker exec ${APP_CONTAINER} which npm
docker exec ${APP_CONTAINER} npm -v

echo "==================================================================="
set -x

if [ -z "$(docker exec ${APP_CONTAINER} ls /var/www/html/artisan)" ]; then
    echo "|| Artisan file missing, maybe there is no code in /var/www/html ||"
    echo "==================================================================="
    exit 0
fi

echo "install vendor (strict lock file)"
docker exec ${APP_CONTAINER} composer install --no-interaction --prefer-dist --optimize-autoloader

echo "install node dependencies"
docker exec ${APP_CONTAINER} npm install --no-audit --no-fund

echo "set permission"
docker exec ${APP_CONTAINER} id www-data
docker exec ${APP_CONTAINER} mkdir -p /var/www/html/storage/logs
docker exec ${APP_CONTAINER} sh -lc 'touch /var/www/html/storage/logs/laravel.log || true'
docker exec ${APP_CONTAINER} chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap
docker exec ${APP_CONTAINER} sh -lc 'chown www-data:www-data /var/www/html/storage/logs/laravel.log 2>/dev/null || true'
docker exec ${APP_CONTAINER} chmod -R 777 /var/www/html/bootstrap
docker exec ${APP_CONTAINER} chmod -R 777 /var/www/html/storage

echo "prepare laravel"
docker exec ${APP_CONTAINER} php artisan key:generate
docker exec ${APP_CONTAINER} php artisan storage:link

echo "optimize laravel"
docker exec ${APP_CONTAINER} php artisan optimize:clear
docker exec ${APP_CONTAINER} php artisan route:clear
docker exec ${APP_CONTAINER} composer dump-autoload -o

echo "build frontend assets"
docker exec ${APP_CONTAINER} mkdir -p /var/www/html/public/build

# Run build and capture exit code
if docker exec ${APP_CONTAINER} npm run build; then
    echo "npm run build succeeded"
else
    echo "npm run build FAILED - checking for partial output"
fi

# Verify manifest was created
if docker exec ${APP_CONTAINER} test -f /var/www/html/public/build/manifest.json; then
    echo "Vite manifest.json found OK"
else
    echo "ERROR: Vite manifest.json NOT FOUND after build!"
    echo "Trying alternative: using php artisan vite:build..."
    docker exec ${APP_CONTAINER} php artisan vite:build 2>/dev/null || true
    docker exec ${APP_CONTAINER} php artisan vendor:publish --tag=laravel-assets --force 2>/dev/null || true
fi

echo "migrate database"
docker exec ${APP_CONTAINER} php artisan migrate:status
docker exec ${APP_CONTAINER} php artisan migrate --force

# docker exec ${APP_CONTAINER} php artisan db:seed --class=SeederClass --force

echo "reload php-fpm"
docker exec ${APP_CONTAINER} kill -USR2 1
echo "PHP-FPM reload signaled"
