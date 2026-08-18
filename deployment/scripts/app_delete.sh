#!/bin/bash

# PARAMETERS:
# - CI_PROJECT_NAME

echo "\n ----------------------- PURGE APPLICATION ----------------------- \n"

cd /home/administrator

if [ ! -d $CI_PROJECT_NAME ]; then
    exit 0
fi

echo "\n Removing previous containers..."
cd ${CI_PROJECT_NAME}
sudo docker compose down

echo "\n Unset Git safe.directory..."
git config --global --unset-all safe.directory /home/administrator/${CI_PROJECT_NAME}

cd ..
echo "\n Removing previous clone..."
sudo chmod 777 -R ./$CI_PROJECT_NAME
sudo rm -rf ./$CI_PROJECT_NAME
