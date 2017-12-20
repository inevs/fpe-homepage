#!/usr/bin/env bash

for ID in $(docker ps --filter='name=fpehomepage_db' -q)
  do
		docker exec $ID gunzip -c /sources/dump.sql.gz | mysql -uroot -pexample -h127.0.0.1
  done
