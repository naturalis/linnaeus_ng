create database linnaeus_ng;

grant all on linnaeus_ng.* to linnaeus_user@localhost identified by 'car0lu5';


CREATE TABLE users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	username VARCHAR( 32 ) NOT NULL ,
	password VARCHAR( 32 ) NOT NULL ,
	first_name VARCHAR( 32 ) NOT NULL ,
	last_name VARCHAR( 32 ) NOT NULL ,
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

CREATE TABLE modules_projects_users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	module_id INT( 11 ) NOT NULL ,
	project_id INT( 11 ) NOT NULL ,
	user_id INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE (
	module_id ,
	project_id ,
	user_id  )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE free_modules_projects_users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	free_module_id INT( 11 ) NOT NULL ,
	project_id INT( 11 ) NOT NULL ,
	user_id INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE (
	free_module_id ,
	project_id ,
	user_id  )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;



RENAME TABLE `users`  TO `dev_users` ;
RENAME TABLE `projects_roles_users`  TO `dev_projects_roles_users` ;
RENAME TABLE `modules_projects_users`  TO `dev_modules_projects_users` ;
RENAME TABLE `free_modules_projects_users`  TO `free_modules_projects_users` ;

INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( 1, 'mdschermer', md5('balance'), 'Maarten', 'Schermer', 'maarten.schermer@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);
INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( 2 , 'jlborges', md5('ficiones'), 'Jorge Luis', 'Borges',  'slavlab@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);


insert into dev_projects_roles_users values (null, 1, 2, 1);
insert into dev_projects_roles_users values (null, 2, 3, 1);
insert into dev_projects_roles_users values (null, 2, 2, 2);
insert into dev_projects_roles_users values (null, 1, 4, 2);


INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( null , 'admin', md5('admin'), 'Lead', 'Expert',  'expert@eti.uva.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);

insert into dev_projects_roles_users values (null, 1, 2, 14);
insert into dev_projects_roles_users values (null, 2, 2, 14);

INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( null , 'sysadmin', md5('sysadmin'), 'System', 'Administrator',  'sysadmin@eti.uva.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);


insert into dev_projects_roles_users values (null, 1, 1, 15);
insert into dev_projects_roles_users values (null, 2, 1, 15);
insert into dev_projects_roles_users values (null, 3, 1, 15);



CREATE TABLE dev_users_taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	user_id INT( 11 ) NOT NULL ,
	taxon_id INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE (
	project_id ,
	taxon_id ,
	user_id  )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;