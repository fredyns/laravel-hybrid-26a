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

# The pipeline delivers .env from the GitLab CI/CD variable STAGING_ENV_FILE
# before this script runs. There is no server-side copy to fall back on.
if [ ! -f .env ]; then
    echo "ERROR: .env is missing — expected the pipeline to deliver it from the"
    echo "       STAGING_ENV_FILE CI/CD variable before running this script."
    exit 1
fi

bash deployment/scripts/docker_up.sh
bash deployment/scripts/laravel_migrate.sh
