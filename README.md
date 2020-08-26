**THIS REPO HAS MOVED AND IS NO LONGER MAINTAINED**

We are in the process of moving to gitlab for our day-to-day development.
This repository has now been moved and is currently not publicly accessable.
Please contact us at [support@naturalis.nl](support@naturalis.nl) if you 
need any assistance or information.

Linnaeus Next Generation
========================

Linnaeus is a program/app developed by Naturalis to setup and manage 
taxonomic databases. It is used for many public project such as 
[Nederlandse Soorten Register](http://www.nederlandsesoorten.nl/) 
and [Dutch Caribbean Species](http://www.dutchcaribbeanspecies.org/), 
but also for [Dierenzoeker](http://www.dierenzoeker.nl/) and
many others listed at [linnaeus.naturalis.nl](http://linnaeus.naturalis.nl/).


Contents
--------

This project contains all the files needed to setup a basic Linnaeus installation. 
But it is also dependent on server configuration projects:

 - [docker composer configuration](https://github.com/naturalis/docker-linnaeusng) - to setup a dockerized version of linnaeus
 - [ansible control script](https://github.com/naturalis/linnaeus_ng_control/blob/master/linnaeus.ansible/roles/naturalis-linnaeus_docker-control/tasks/main.yml) - to control remote dockerized installs
 - [puppet](https://github.com/naturalis/puppet-linnaeusng) - to setup and monitor the linnaeus host system with a docker installation

Linnaeus docker configuration
-----------------------------

Usually Linnaeus is already running on a remote virtual host (_the host_) and already setup using
[puppet](https://github.com/naturalis/puppet-linnaeusng), normally any changes to the scripts or setup should be done 
through [ansible](https://github.com/naturalis/linnaeus_ng_control/). 
A typical remote configuration looks like this.

**Docker**

`/opt/docker-linnaeusng`

This directory contains a clone of [docker composer configuration](https://github.com/naturalis/docker-linnaeusng)
and is running a docker composer. This can be verified by logging into _the host_ machine. 

```
cd /opt/docker-linnaeusng
sudo docker-compose ps                                                                                                                          :(
```

This should show a running instance consisting of two components:

```
           Name                          Command               State           Ports         
---------------------------------------------------------------------------------------------
dockerlinnaeusng_db_1         docker-entrypoint.sh mysqld      Up      0.0.0.0:3306->3306/tcp
dockerlinnaeusng_linnaeus_1   /usr/local/bin/docker-entr ...   Up      0.0.0.0:80->80/tcp    
```

The setup contains a mysql database and a apache/php stack. To connect to the database
from _the host_ you can use the configuration parameters from `/opt/docker-linnaeusng/.env`. 
The .env file contains these fields:

* `MYSQL_HOST=db` name of the docker database host
* `MYSQL_USER=linnaeus_user` name of the linnaeus database user
* `MYSQL_DATABASE=linnaeus_ng`
* `MYSQL_PASSWORD=linnaeus_password` linnaeus database password
* `MYSQL_ROOT_PASSWORD=the_root_password` the mysql root password
* `MYSQL_EXTERNAL_PORT=3306` the exposed external mysql port
* `GIT_BRANCH=development` the git branch of the linnaeus codebase used by this branch
* `COMPOSER_ALLOW_SUPERUSER=1` switch to allow composer.phar to be run as root
* `TABLE_PREFIX=` optional table prefix
* `BASE_PATH=/data` base path where all data and scripts reside on the host machine
* `DEV=0` switch to put the linnaeus installation in dev mode (for extra error reporting for instance)

Foreman and puppet setup this file once and use randomized passwords for each installation.

**Connecting to the database** 

To connect to the database you can either use the mysql client directly on _the host_ machine:

`mysql --host=127.0.0.1 --user=root --password`

You can also setup a ssh tunnel to connect to port 3306 using your favorite local mysql client, since it is exposed 
on the host machine.

Or you can access the database through docker:

`sudo docker-compose exec db mysql --user=root`

**Connecting to the linnaeus app** 

To see and influence the linnaeus installation from the docker machine. You can login to the linnaeus docker machine:

`sudo docker-compose exec linnaeus bash`

This moves you to `/var/www/html` in the running apache/php stack machine. 
From which you can run commands and script directly changing the installation. 
This is not recommended on production machines which should normally be 
configured and maintained using the ansible scripts.

**Data and scripts**

On _the host_ machine the data can usually be found in `/data/linnaeus`. Which contains:

* `apachelog` the directory containing the webserver logs of the docker linnaeus machine
* `backup` directory containing database dumps (made whenever the database migrates)
* `db` the raw mysql files used by the db server in the docker setup
* `initdb` the base database installation the first time ansible is run and no database is setup files in this directory get imported
* `mysqlconf` contains specific config file for the linnaeus database
* `mysqllog` contains the database log files
* `www` the linnaeus application

These directories are accessible from _the host_ machine, because we need to be able to
backup these files. And the logging directories can easily be accessed and viewed or 
grepped. But you can also force a reinstall of a certain linnaeus installation by 
putting a different database dump in initdb.


Managing linnaeus installations without ansible
-----------------------------------------------

- Install a virtual machine using puppet, vagrant, docker.
- Install a recent version of npm using `brew install npm` (osX) or `apt-get npm` (linux).
- For instance you can use [our own docker composer file](https://github.com/naturalis/docker-linnaeusng) to setup a basic stack
- Clone this repository to your webserver root

**Third party javascripts**

If you do not use the docker installation try to get the third party javascript libaries working:

```
cd /path/to/linnaeus_ng
npm install --global bower
bower install --allow-root
npm install --global gulp 
npm install
gulp
```

This similar to the [gulp.sh script](https://github.com/naturalis/linnaeus_ng/blob/development/tools/scripts/gulp.sh).

Gulp can fail in the first run, because some gulp packages need to be installed globally 
first, follow the hints given in the errors. Gulp will generate the following files that are 
not (and should not be) in the git repository:

- ./www/app/vendor/bundle.js
- ./www/app/vendor/*
- ./www/admin/vendor/bundle.js
- ./www/admin/vendor/*

**Third party php**

The linnaeus installation is also dependent on external php projects. Which are
installed by composer. To get the composer and php requirements working:

- [download and install composer](https://getcomposer.org/)
- cd _/path/to/linnaeus_ng_
- composer update

This is also done by the [composer.sh](https://github.com/naturalis/linnaeus_ng/blob/development/tools/scripts/composer.sh) script.

Managing Linnaeus through Docker
--------------------------------

If you do have docker running on your local setup or somewhere remote you can use these commands to do certain tasks. First
move to your local docker-compose configuration.

`cd /opt/docker-linnaeusng`

This is the path where puppet puts the docker installation by default, on your own installation this could be anywhere.
You should be at this location on your host machine, or else it will not work.

**Checking out and pulling an updated version of linnaeus**

`docker-compose exec -T linnaeus /var/www/html/tools/scripts/git.sh`

git.sh uses the local environment variable GIT_BRANCH to checkout and pull the latest commit of this branch. If you want to install some experimental branch.

```bash
docker-compose exec linnaeus bash
git fetch origin yourbranch:yourbranch
git checkout yourbranch
git pull origin yourbranch
```

**Installing or updating third party php libraries**

`docker-compose exec -T linnaeus /var/www/html/tools/scripts/composer.sh`

This runs [composer update](https://getcomposer.org/doc/03-cli.md#update).

**Installing or updating third party javascript libraries and css**

`docker-compose exec -T linnaeus /var/www/html/tools/scripts/gulp.sh`

The script is doing exactly this:

```
cd /var/www/html
/usr/bin/npm install --global bower
/usr/bin/bower install --allow-root
/usr/bin/npm install --global gulp 
/usr/bin/npm install
/usr/bin/gulp
```

