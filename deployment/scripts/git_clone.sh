#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - CI_PROJECT_PATH
# - token_pull

set -e

echo "\n ----------------- git_clone ---------------- \n"

cd /home/administrator || exit 1
git clone https://${token_pull}@gitlab.bki.co.id/${CI_PROJECT_PATH}.git
git config --global --add safe.directory /home/administrator/${CI_PROJECT_NAME}

cd ${CI_PROJECT_NAME} || exit 1
git remote set-url origin "https://${token_pull}@gitlab.bki.co.id/${CI_PROJECT_PATH}.git"

echo "\n setting permissions..."
# uid/gid 1000 is the `app` user php-fpm runs as (deployment/docker/php/www.conf).
# Group-writable is enough; 777 made the whole tree world-writable.
mkdir -p storage bootstrap/cache
chown -R 1000:1000 ./ || echo "WARNING: chown skipped — run the deploy user with sudo rights to fix ownership."
find storage bootstrap/cache -type d -exec chmod 775 {} +
