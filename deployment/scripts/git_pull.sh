#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - CI_PROJECT_PATH
# - token_pull

echo "\n ----------------- git_pull ---------------- \n"

cd /home/administrator/${CI_PROJECT_NAME} || exit 1
git reset --hard HEAD
git pull

echo "\n executing chmod..."
chmod 777 -R storage
chmod 777 -R bootstrap
chown -R 1000:33 ./
