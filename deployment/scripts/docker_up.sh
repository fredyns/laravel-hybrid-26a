#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

set -e

APP_CONTAINER="laravelkit26_app"
DB_CONTAINER="laravelkit26_postgres"

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

echo "\n Rebuilding containers..."
sudo docker compose build
sudo docker compose up -d

echo "\n Waiting for postgres to be ready..."
# POSIX counter loop: `for i in {1..30}` is a bashism that silently degrades to
# a single literal iteration when this script is run under sh.
i=1
while [ "$i" -le 30 ]; do
    if docker exec ${DB_CONTAINER} pg_isready -U postgres > /dev/null 2>&1; then
        echo "Postgres is ready!"
        break
    fi
    echo "Waiting... ($i/30)"
    i=$((i + 1))
    sleep 2
done

if ! docker exec ${DB_CONTAINER} pg_isready -U postgres > /dev/null 2>&1; then
    echo "ERROR: ${DB_CONTAINER} did not become ready within 60s."
    exit 1
fi

docker exec ${APP_CONTAINER} git config --global --add safe.directory /var/www/html
