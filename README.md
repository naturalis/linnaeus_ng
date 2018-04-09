Linnaeus Next Generation
========================

Linnaeus is program developed by Naturalis to setup and manager taxonomic databases. It is used for many
public project such as Nederlandse Soorten Register and Dutch Caribbean Species. But also for Dierenzoeker.


Contents
--------

The project contains all the files needed to setup a basic Linnaeus installation.


Instruction steps needed to get the installation working
--------------------------------------------------------

- Install a virtual machine using vagrant or Docker
- Install a recent version of npm using `brew install npm` or `apt-get npm`
- For instance you can use [our own docker file](https://github.com/naturalis/docker-linnaeusng) to setup a basic stack
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

