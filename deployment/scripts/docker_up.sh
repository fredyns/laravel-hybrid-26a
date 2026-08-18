#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

APP_CONTAINER="laravelkit26_app"

cd /home/administrator/${CI_PROJECT_NAME}

echo "\n Rebuilding containers..."
sudo docker compose build
sudo docker compose up -d

echo "\n Waiting for postgres to be ready..."
for i in {1..30}; do
  if docker exec postgres pg_isready -U postgres > /dev/null 2>&1; then
    echo "Postgres is ready!"
    break
  fi
  echo "Waiting... ($i/30)"
  sleep 2
done

docker exec ${APP_CONTAINER} git config --global --add safe.directory /var/www/html
