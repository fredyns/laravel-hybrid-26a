#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - CI_PROJECT_PATH
# - token_pull

set -e

echo "\n ----------------- git_pull ---------------- \n"

cd /home/administrator/${CI_PROJECT_NAME} || exit 1
git reset --hard HEAD
git pull

echo "\n setting permissions..."
mkdir -p storage bootstrap/cache
chown -R 1000:1000 ./ || echo "WARNING: chown skipped — run the deploy user with sudo rights to fix ownership."
find storage bootstrap/cache -type d -exec chmod 775 {} +
