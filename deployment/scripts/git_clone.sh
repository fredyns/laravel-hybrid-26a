#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - CI_PROJECT_PATH
# - token_pull

echo "\n ----------------- git_clone ---------------- \n"

cd /home/administrator
git clone https://${token_pull}@gitlab.bki.co.id/${CI_PROJECT_PATH}.git
git config --global --add safe.directory /home/administrator/${CI_PROJECT_NAME}

cd ${CI_PROJECT_NAME}
git remote set-url origin "https://${token_pull}@gitlab.bki.co.id/${CI_PROJECT_PATH}.git"
ls -la

echo "\n executing chmod..."
chmod 777 -R storage
chmod 777 -R bootstrap
chown -R 1000:33 ./
