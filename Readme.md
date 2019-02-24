# Readme
This project is used to develop a layout for the site https://fighting-pirates.net 

It is intended to be for local use only. How to setup the live environment and how it is synchronized with this repo will need to be figured out. 

## Installation
  * Install [docker](https://www.docker.com) with [docker-compose](https://docs.docker.com/compose/)
  * Make sure the ports `3306` and `8080` are available
  * copy the file `docker-drupal/sitecopyrc.template` to `docker-drupal/sitecopyrc` and change username and password in that file
  * create drupal docker image ```docker build -t fpe/drupal docker-drupal``` 
  * run `install.sh` from the project root directory. This will build a docker image with drupal, drush and sitecopy, start all containers including a MySQL database and also import the dump from `./database/dump.sql.gz`
  * A blank drupal should now be available at http://localhost:8080

## Running & Stopping
Once the containers are created and the dump is installed you can simply start the containers with `docker-compose up`

To shut everything down (This will remove the containers entirely and you will need to re-import the dump.), run `docker-compose down` from the project root directory.

## HOW TOs

### Make changes to a template
Even though the template cache should be disabled, it sometimes seems to be neccessary to reload the themplates. To do so, go to http://localhost:8080/core/reload.php

### Create new dump
```
mysqldump -h 127.0.0.1 -uroot -pexample --databases fpe | gzip -c > database/dump.sql.gz 
```

or simply call ```./database/create-dump.sh```

### Update the live system
Run 
```
./update-production.sh
```
This will synchronize the state of the themes directory with the remote version.