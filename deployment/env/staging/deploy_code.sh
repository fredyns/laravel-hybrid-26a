#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

set -e

echo "==================================================================="
echo "                          STAGING_PULL                             "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

# The pipeline delivers .env from the GitLab CI/CD variable STAGING_ENV_FILE
# before this script runs. There is no server-side copy to fall back on.
if [ ! -f .env ]; then
    echo "ERROR: .env is missing — expected the pipeline to deliver it from the"
    echo "       STAGING_ENV_FILE CI/CD variable before running this script."
    exit 1
fi

bash deployment/scripts/laravel_migrate.sh

echo "\n Restarting app and web server..."
sudo docker compose restart app web
echo "\n Deploy selesai."
