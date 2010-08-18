create database linnaeus_ng;

grant all on linnaeus_ng.* to linnaeus_user@localhost identified by 'car0lu5';


CREATE TABLE users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	username VARCHAR( 32 ) NOT NULL ,
	password VARCHAR( 32 ) NOT NULL ,
	first_name VARCHAR( 32 ) NOT NULL ,
	last_name VARCHAR( 32 ) NOT NULL ,
	gender ENUM( 'm' , 'f' ) NOT NULL ,
	email_address VARCHAR( 54 ) NOT NULL ,
	active BOOLEAN DEFAULT '1' NOT NULL ,
	last_login DATETIME NULL ,
	logins INT NOT NULL DEFAULT '0',
	password_changed DATETIME NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( password ) ,
	UNIQUE (
	username ,
	email_address
	)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


INSERT INTO users (
id ,username ,password ,first_name ,last_name ,gender ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( NULL , 'mdschermer', md5('balance'), 'Maarten', 'Schermer', 'm', 'maarten.schermer@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);



CREATE TABLE rights (
id INT( 11 ) NOT NULL AUTO_INCREMENT ,
action VARCHAR( 32 ) NOT NULL ,
created DATETIME NOT NULL ,
PRIMARY KEY ( id ) ,
INDEX ( action )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;


INSERT INTO rights (id ,action ,created) VALUES (NULL , 'Create project',CURRENT_TIMESTAMP);


CREATE TABLE projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	name VARCHAR( 64 ) NOT NULL ,
	version VARCHAR( 16 ) NOT NULL ,
	description TEXT NOT NULL ,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `projects`  TO `dev_projects` ;
RENAME TABLE `rights`  TO `dev_rights` ;
RENAME TABLE `users`  TO `dev_users` ;


