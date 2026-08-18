#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

APP_CONTAINER="laravelkit26_app"

echo "==================================================================="
echo "                          ROUTE CHECK                              "
echo "==================================================================="

if [ -z "$(docker ps --format '{{.Names}}' | grep "^${APP_CONTAINER}")" ]; then
    echo "||   Docker container ${APP_CONTAINER} is not running   ||"
    exit 1
fi

echo "\n --- Application --- \n"
docker exec ${APP_CONTAINER} php artisan about --only=environment

echo "\n --- Routes --- \n"
docker exec ${APP_CONTAINER} php artisan route:list --except-vendor
