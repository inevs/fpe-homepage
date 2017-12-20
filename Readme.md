# Readme
This project is used to develop a layout for the site https://fighting-pirates.net 

It is intended to be for local use only. How to setup the live environment and how it is synchronized with this repo will need to be figured out. 

## Installation
  * Install [docker](https://www.docker.com) with [docker-compose](https://docs.docker.com/compose/)
  * Make sure the ports `3306` and `8080` are available
  * run `start-containers.sh` from the project root directory. This will start an Apache Webserver as well as a MySQL database and also import the dump from `./database/dump.sql.gz`
  * A blank drupal should now be available at http://localhost:8080

To shut everything down, run `docker-compose down` from the project root directory.

## HOW TOs

