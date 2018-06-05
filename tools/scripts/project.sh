#!/bin/bash
echo 'SELECT `sys_name` FROM `projects`' | mysql -q --silent $MYSQL_DATABASE
