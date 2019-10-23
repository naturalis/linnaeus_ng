#!/bin/bash
cd /var/www/html/
/usr/bin/npm install -g n                                                                                       
/usr/bin/n stable   
/usr/bin/npm install --global yarn
/usr/bin/npm install --global gulp
/usr/local/bin/yarn
/usr/local/bin/gulp
