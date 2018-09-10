#!/usr/bin/env bash

echo "Starting database and web server"
docker-compose up -d

echo "Waiting until containers are ready"
sleep 1
ID=$(docker ps --filter='name=fpe-homepage_db_1' -q)
while : ; do
	docker exec $ID mysql -uroot -pexample -h127.0.0.1 -e 'show databases;'
	[[ if $? -gt 0 ]] || break
	echo "still waiting"
	sleep 1
done

echo "Importing database dump"

echo "Done"