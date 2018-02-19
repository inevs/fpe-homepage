#!/usr/bin/env bash

DRUPAL_ID=$(docker ps --filter='name=fpehomepage_drupal' -q)
docker exec $DRUPAL_ID bash -c 'rm -rf /var/www/html/sites/default/files/config_*/sync \
    && drush config-export \
    && sitecopy --fetch --allsites \
    && sitecopy --update -k --allsites'

echo "Go to https://fighting-pirates.net/drupal/admin/config/development/configuration to import the changes"