#!/usr/bin/env bash

for ID in $(docker ps --filter='name=fpehomepage_db' -q)
  do
		docker exec $ID bash -c 'mysqldump -h 127.0.0.1 -uroot -pexample --databases fpe | gzip -c > /sources/dump.sql.gz'
  done
