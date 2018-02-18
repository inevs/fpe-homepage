#!/usr/bin/env bash

DRUPAL_ID=$(docker ps --filter='name=fpehomepage_drupal' -q)
docker exec $DRUPAL_ID bash -c 'sitecopy --fetch theme_production && sitecopy --update -k theme_production'
