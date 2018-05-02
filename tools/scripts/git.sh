#!/bin/bash
echo -n "Updating GIT BRANCH: "
echo $GIT_BRANCH
pwd
git checkout $GIT_BRANCH
git pull origin $GIT_BRANCH
