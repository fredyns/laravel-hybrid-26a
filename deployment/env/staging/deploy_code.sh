#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

set -e

echo "==================================================================="
echo "                          STAGING_PULL                             "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

APP_ENV_NAME=staging bash deployment/scripts/env_install.sh

bash deployment/scripts/laravel_migrate.sh

echo "\n Restarting app and web server..."
sudo docker compose restart app web
echo "\n Deploy selesai."
