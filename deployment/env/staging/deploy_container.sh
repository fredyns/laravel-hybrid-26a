#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

set -e

echo "==================================================================="
echo "                        STAGING_CONTAINER                          "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

# -f, not -d: these are files. The old -d tests never matched, so the cleanup
# never ran.
if [ -f docker-compose.yaml ]; then
    rm -f docker-compose.yaml
fi

cp deployment/env/staging/docker-compose.yml docker-compose.yaml

APP_ENV_NAME=staging bash deployment/scripts/env_install.sh

bash deployment/scripts/docker_up.sh
bash deployment/scripts/laravel_migrate.sh
