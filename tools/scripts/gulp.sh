#!/bin/bash

cd /var/www/html
/usr/bin/npm install --global bower
/usr/bin/bower install --allow-root
/usr/bin/npm install --global gulp 
/usr/bin/npm install
/usr/bin/gulp
