#!/bin/bash

echo "==================================================================="
echo "                             STAGING_PULL                              "
echo "==================================================================="


cd /home/administrator/${CI_PROJECT_NAME} || exit 1

rm .env
cp deployment/env/staging/.env .env

sh deployment/scripts/laravel_migrate.sh

echo "\n Restarting app and web server..."
sudo docker compose restart app web
echo "\n Deploy selesai."
