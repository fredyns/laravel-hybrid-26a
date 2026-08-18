#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

APP_DIR="/home/administrator/${CI_PROJECT_NAME}"
APP_CONTAINER="laravelkit26_app"
DB_CONTAINER="laravelkit26_postgres"

echo "==================================================================="
echo "                        CONTAINER CHECK                            "
echo "==================================================================="

echo "\n --- Project containers --- \n"
docker ps -a --filter "name=laravelkit26_"

echo "\n --- Postgres logs (last 50) --- \n"
docker logs ${DB_CONTAINER} 2>&1 | tail -50 || echo "${DB_CONTAINER} not found"

echo "\n --- App logs (last 20) --- \n"
docker logs ${APP_CONTAINER} 2>&1 | tail -20 || echo "${APP_CONTAINER} not found"

echo "\n --- Networks --- \n"
docker network ls | grep laravelkit26 || echo "No laravelkit26 network found"

echo "\n --- Compose config --- \n"
cd ${APP_DIR} || exit 1
docker compose config 2>&1 | head -50 || echo "Docker compose config failed"

echo "\n --- Database connectivity --- \n"
docker exec ${DB_CONTAINER} pg_isready -U postgres || echo "Postgres not accepting connections"
docker exec ${APP_CONTAINER} php artisan db:show || echo "Laravel cannot reach the database"
