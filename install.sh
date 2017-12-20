#!/usr/bin/env bash

echo "###### Destroying all old containers ######"
docker-compose down

echo "###### Creating containers for database and web server ######"
docker-compose up -d

echo "###### Waiting until containers are ready ######"
sleep 1
ID=$(docker ps --filter='name=fpehomepage_db' -q)
while : ; do
	docker exec $ID mysql -uroot -pexample -h127.0.0.1 -e 'show databases;' &> /dev/null
	[[ $? -gt 0 ]] || break
	echo "###### still waiting for mysql to finish initialization ######"
	sleep 2
done

echo "###### Importing database dump ######"
./database/import-dump.sh

echo "###### Fixing permissions of sites directory to ######"
sudo chmod a+w -R sites

echo "###### Installation complete! Now go to http://localhost:8080 ######"