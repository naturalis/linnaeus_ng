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


CREATE TABLE projects_roles_users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	role_id INT( 11 ) NOT NULL ,
	user_id INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE (
	project_id ,
	role_id ,
	user_id  )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;



RENAME TABLE `users`  TO `dev_users` ;
RENAME TABLE `projects_roles_users`  TO `dev_projects_roles_users` ;



INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,gender ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( 1, 'mdschermer', md5('balance'), 'Maarten', 'Schermer', 'm', 'maarten.schermer@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);
INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,gender ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( 2 , 'jlborges', md5('ficiones'), 'Jorge Luis', 'Borges', 'm', 'slavlab@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);


insert into dev_projects_roles_users values (null, 1, 2, 1);
insert into dev_projects_roles_users values (null, 2, 3, 1);
insert into dev_projects_roles_users values (null, 2, 2, 2);
insert into dev_projects_roles_users values (null, 1, 4, 2);
