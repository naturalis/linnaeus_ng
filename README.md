Linnaeus Next Generation
========================

Linnaeus is program developed by Naturalis to setup and manager 
taxonomic databases. It is used for many public project such as 
Nederlandse Soorten Register and Dutch Caribbean Species. But also for Dierenzoeker.


Contents
--------

The project contains all the files needed to setup a basic Linnaeus installation. 
But is also dependent on server configuration projects:

 - [docker composer configuration](https://github.com/naturalis/docker-linnaeusng)
 - [ansible control script](https://github.com/naturalis/linnaeus_ng_control/blob/master/linnaeus.ansible/roles/naturalis-linnaeus_docker-control/tasks/main.yml)
 - [puppet](https://github.com/naturalis/puppet-linnaeusng)

Linnaeus docker configuration
-----------------------------

Usually Linnaeus is already running on a remote virtual host (_the host_) and setup using
[puppet](https://github.com/naturalis/puppet-linnaeusng), normally any changes to the scripts or setup should be done through [ansible](https://github.com/naturalis/linnaeus_ng_control/). 
A typical remote configuration looks like this.

**Docker**

`/opt/docker-linnaeusng`

This directory contains a clone of [docker composer configuration](https://github.com/naturalis/docker-linnaeusng)
and is running a docker composer. This can be verified by logging into _the host_ machine. 

```
cd /opt/docker-linnaeusng
sudo docker-compose ps                                                                                                                          :(
```

This should show:

```
           Name                          Command               State           Ports         
---------------------------------------------------------------------------------------------
dockerlinnaeusng_db_1         docker-entrypoint.sh mysqld      Up      0.0.0.0:3306->3306/tcp
dockerlinnaeusng_linnaeus_1   /usr/local/bin/docker-entr ...   Up      0.0.0.0:80->80/tcp    
```

The setup contains a mysql database and a apache/php stack. To connect to the database
from _the host_ you can use the configuration parameters from `/opt/docker-linnaeusng/.env`. 
A typical .env contains fields like these:

* `MYSQL_HOST=db` name of the docker database host
* `MYSQL_USER=linnaeus_user` name of the linnaeus database user
* `MYSQL_PASSWORD=linnaeus_password` linnaeus database password
* `MYSQL_ROOT_PASSWORD=the_root_password` the mysql root password
* `MYSQL_EXTERNAL_PORT=3306` the exposed external mysql port
* `GIT_BRANCH=development` the git branch of the linnaeus codebase used by this branch
* `COMPOSER_ALLOW_SUPERUSER=1` switch to allow composer.phar to be run as root
* `TABLE_PREFIX=` optional table prefix
* `BASE_PATH=/data` base path where all data and scripts reside on the host machine
* `DEV=0` switch to put the linnaeus installation in dev mode (for extra error reporting for instance)

**Connecting to the database** 

To connect to the database you can either use the mysql client on _the host_:

`mysql --host=127.0.0.1 --user=root --password`

Or through docker:

`sudo docker-compose exec db mysql --user=root`

**Connecting to the linnaeus app** 

To see the linnaeus installation from the docker machine. You can login to the linnaeus machine:

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


Starting the linnaeus installation step by step
-----------------------------------------------

- Install a virtual machine using puppet, vagrant, docker
- Install a recent version of npm using `brew install npm` or `apt-get npm`
- For instance you can use [our own docker composer file](https://github.com/naturalis/docker-linnaeusng) to setup a basic stack
- Clone this repository to your webserver root

To get the third party javascript libaries working:

```
cd /path/to/linnaeus_ng
npm install --global gulp
npm install --global bower
npm install
bower install
gulp
```

Gulp can fail in the first run, because some gulp packages need to be installed globally first. Gulp will generate the
following files that are not in the git repository:

- ./www/app/vendor/bundle.js
- ./www/app/vendor/*
- ./www/admin/vendor/bundle.js
- ./www/admin/vendor/*


To get the composer and php requirements working:

- download and install composer
- cd /path/to/linnaeus_ng
- composer install

