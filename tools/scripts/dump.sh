#!/bin/bash
mysqldump --opt -e --routines --default-character-set=utf8 $MYSQL_DATABASE
