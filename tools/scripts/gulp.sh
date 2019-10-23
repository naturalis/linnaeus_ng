#!/bin/bash
cd /var/www/html/
/usr/bin/npm install -g n                                                                                       
/usr/bin/n stable   
/usr/bin/npm install --global yarn
/usr/bin/yarn
/usr/bin/gulp
