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

echo "###### Changing ownership in drupal to www-data ######"
DRUPAL_ID=$(docker ps --filter='name=fpehomepage_drupal' -q)
docker exec $DRUPAL_ID bash -c 'chown -R www-data:www-data /var/www/html/sites'
docker exec $DRUPAL_ID bash -c 'curl -fSL "http://files.drush.org/drush.phar" -o /usr/local/bin/drush && chmod +x /usr/local/bin/drush'
echo "###### Changing ownership in grunt to node ######"
GRUNT_ID=$(docker ps --filter='name=fpehomepage_grunt' -q)
docker exec $GRUNT_ID bash -c 'chown -R node:node /data'

echo "###### Fixing file permissions. Please provide your root password to do so. ######"
sudo chmod -R a+w sites themes modules

echo "###### Installation complete! Now go to http://localhost:8080 ######"
