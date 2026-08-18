#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - APP_ENV_NAME   (e.g. staging)
#
# Installs the environment file for this deployment. Real credentials live on
# the server, outside the clone, so they survive a rebuild and never enter git.

set -e

APP_DIR="/home/administrator/${CI_PROJECT_NAME}"
SECRET_ENV="/home/administrator/secrets/${CI_PROJECT_NAME}.${APP_ENV_NAME}.env"
TEMPLATE="deployment/env/${APP_ENV_NAME}/.env.example"

echo "\n ----------------- env_install (${APP_ENV_NAME}) ---------------- \n"

cd "${APP_DIR}" || exit 1

if [ -f "${SECRET_ENV}" ]; then
    echo "Installing environment from ${SECRET_ENV}"
    cp "${SECRET_ENV}" .env
elif [ -f .env ]; then
    echo "No server secrets file found — keeping the .env already on the server."
else
    echo "ERROR: no environment file available."
    echo ""
    echo "  Expected: ${SECRET_ENV}"
    echo ""
    echo "  Create it once on the server, using the committed template as a"
    echo "  starting point, then fill in the real credentials:"
    echo ""
    echo "    mkdir -p /home/administrator/secrets"
    echo "    cp ${APP_DIR}/${TEMPLATE} ${SECRET_ENV}"
    echo "    chmod 600 ${SECRET_ENV}"
    echo "    \$EDITOR ${SECRET_ENV}"
    echo ""
    exit 1
fi

chown 1000:1000 .env || echo "WARNING: could not chown .env"
chmod 640 .env
