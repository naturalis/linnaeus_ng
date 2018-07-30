#!/bin/bash
echo "DROP DATABASE linnaeus_ng" | mysql
cat /data/linnaeus/initdb/* | mysql
