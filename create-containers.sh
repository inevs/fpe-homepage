#!/usr/bin/env bash

echo "Starting database and web server"
docker-compose up -d

echo "Waiting until containers are ready"
sleep 1
ID=$(docker ps --filter='name=fpehomepage_db' -q)
while : ; do
	docker exec $ID mysql -uroot -pexample -h127.0.0.1 -e 'show databases;' &> /dev/null
	[[ $? -gt 0 ]] || break
	echo "still waiting for mysql to finish initialization"
	sleep 2
done

echo "Importing database dump"
./database/import-dump.sh
echo "Done"