#!/usr/bin/env bash

directory="$PWD/.circleci/build/"

mkdir -p "$directory"app/code/Mandytech/Postmark
rsync -av --progress "$PWD"/* "$directory"app/code/Mandytech/Postmark/ --exclude ".circleci" --exclude ".git"

cd "$directory"
php bin/magento module:enable Mandytech_Postmark
php bin/magento setup:upgrade
