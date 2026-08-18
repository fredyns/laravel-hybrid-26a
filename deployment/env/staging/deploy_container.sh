#!/bin/bash

echo "==================================================================="
echo "                             STAGING_CONTAINER                         "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME}

if [ -d .env ]; then
    rm -f .env
fi

if [ -d docker-compose.yaml ]; then
    rm -f docker-compose.yaml
fi

cp deployment/env/staging/.env .env
cp deployment/env/staging/docker-compose.yml docker-compose.yaml

sh deployment/scripts/docker_up.sh
sh deployment/scripts/laravel_migrate.sh
