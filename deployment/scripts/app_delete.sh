#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

echo "\n ----------------------- PURGE APPLICATION ----------------------- \n"

cd /home/administrator || exit 1

if [ ! -d "$CI_PROJECT_NAME" ]; then
    exit 0
fi

echo "\n Removing previous containers..."
cd ${CI_PROJECT_NAME} || exit 1
sudo docker compose down

echo "\n Unset Git safe.directory..."
git config --global --unset-all safe.directory /home/administrator/${CI_PROJECT_NAME} || true

cd ..
echo "\n Removing previous clone..."
# No chmod needed first — sudo rm can remove the tree regardless of its mode.
sudo rm -rf ./${CI_PROJECT_NAME}
