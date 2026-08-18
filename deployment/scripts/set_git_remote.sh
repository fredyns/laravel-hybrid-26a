#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME
# - CI_PROJECT_PATH
# - token_pull

set +x

echo "==================================================================="
echo "                        SET GIT REMOTE                             "
echo "==================================================================="

cd /home/administrator/${CI_PROJECT_NAME} || exit 1

git remote set-url origin "https://${token_pull}@gitlab.bki.co.id/${CI_PROJECT_PATH}.git"

echo "\n Origin updated for ${CI_PROJECT_PATH}"
