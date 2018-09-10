#!/usr/bin/env bash

for ID in $(docker ps --filter='name=fpe-homepage_db_1' -q)
  do
		docker exec $ID bash -c 'gunzip -c /sources/dump.sql.gz | mysql -uroot -pexample -h127.0.0.1'
  done
