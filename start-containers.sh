#!/usr/bin/env bash

echo "Starting database and web server"
docker-compose up -d

echo "Waiting until containers are ready"
sleep 5

echo "Importing database dump"

for ID in $(docker ps --filter='name=fpehomepage_db' -q)
  do
		docker exec $ID gunzip -c /sources/dump.sql.gz | mysql -uroot -pexample -h127.0.0.1
  done

echo "Done"