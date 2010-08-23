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



CREATE TABLE rights (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	action VARCHAR( 32 ) NOT NULL unique,
	system_only enum ( 'y' , 'n' ) not null,
	full_path VARCHAR( 255 ) NOT NULL,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( action )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	name VARCHAR( 64 ) NOT NULL ,
	version VARCHAR( 16 ) NOT NULL ,
	description TEXT NOT NULL ,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE projects_rights_users (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	user_id INT( 11 ) NOT NULL ,
	right_id INT( 11 ) NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE (
	project_id ,
	user_id ,
	right_id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `projects`  TO `dev_projects` ;
RENAME TABLE `rights`  TO `dev_rights` ;
RENAME TABLE `users`  TO `dev_users` ;
RENAME TABLE `projects_rights_users`  TO `dev_projects_rights_users` ;

INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,gender ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( NULL , 'mdschermer', md5('balance'), 'Maarten', 'Schermer', 'm', 'maarten.schermer@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);

INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,gender ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( NULL , 'jlborges', md5('fictiones'), 'Jorge-Luis', 'Borges', 'm', 'slavlab@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);


INSERT INTO dev_rights VALUES (NULL , 'Create project', 'y', '/admin/views/projects/create.php',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'Create user', 'n', '/admin/views/users/create.php',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'Edit user', 'n', '/admin/views/users/edit.php',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'View user', 'n', '/admin/views/users/view.php',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'Login', 'n', '/admin/views/users/login.php',CURRENT_TIMESTAMP);


insert into dev_projects values (null,'Polar Bears of Amsterdam','first beta','Ursus maritimus in the nation\'s capital',now(),null);
insert into dev_projects values (null,'Imaginary Beings','v1.0','See Borges',now(),null);


insert into dev_projects_rights_users values (null, 1, 1, 2);
insert into dev_projects_rights_users values (null, 1, 1, 3);
insert into dev_projects_rights_users values (null, 1, 1, 4);
insert into dev_projects_rights_users values (null, 2, 1, 4);
insert into dev_projects_rights_users values (null, 1, 1, 5);
insert into dev_projects_rights_users values (null, 2, 1, 5);

insert into dev_projects_rights_users values (null, 2, 1, 4);
insert into dev_projects_rights_users values (null, 2, 2, 2);
insert into dev_projects_rights_users values (null, 2, 2, 3);
insert into dev_projects_rights_users values (null, 2, 2, 4);
insert into dev_projects_rights_users values (null, 1, 2, 5);
insert into dev_projects_rights_users values (null, 2, 2, 5);
