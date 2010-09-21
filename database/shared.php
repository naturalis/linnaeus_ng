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


CREATE TABLE projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	name VARCHAR( 64 ) NOT NULL ,
	version VARCHAR( 16 ) NOT NULL ,
	description TEXT NOT NULL ,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE roles (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	role VARCHAR( 32 ) NOT NULL unique,
	description VARCHAR( 255 ) ,
	assignable enum( 'y', 'n' ) not null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( role )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE rights (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	controller VARCHAR( 32 ) NOT NULL,
	view VARCHAR( 32 ) NOT NULL,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( controller, view )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE rights_roles (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	right_id INT( 11 ) NOT NULL  ,
	role_id INT( 11 ) NOT NULL  ,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( right_id, role_id )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

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

CREATE TABLE helptexts (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	controller VARCHAR( 32 ) NOT NULL,
	view VARCHAR( 32 ) NOT NULL,
	subject VARCHAR( 64 ) NOT NULL ,
	helptext TEXT NOT NULL ,
	show_order int (3),
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;





RENAME TABLE `projects`  TO `dev_projects` ;
RENAME TABLE `roles`  TO `dev_roles` ;
RENAME TABLE `rights`  TO `dev_rights` ;
RENAME TABLE `rights_roles`  TO `dev_rights_roles` ;
RENAME TABLE `users`  TO `dev_users` ;
RENAME TABLE `projects_roles_users`  TO `dev_projects_roles_users` ;
RENAME TABLE `helptexts`  TO `dev_helptexts` ;



insert into dev_projects values (null,'Polar Bears of Amsterdam','first beta','Ursus maritimus in the nation\'s capital',now(),null);
insert into dev_projects values (null,'Imaginary Beings','v1.0','See Borges',now(),null);

insert into dev_roles values (null,'System administrator','ETI admin; creates new projects and lead experts','n',now());
insert into dev_roles values (null,'Lead expert','General manager of a project','n',now());
insert into dev_roles values (null,'Expert','Content manager of a project','y',now());
insert into dev_roles values (null,'Editor','Edits specific parts of a project','y',now());
insert into dev_roles values (null,'Contributor','Contributes to a project but cannot edit','y',now());

INSERT INTO dev_rights VALUES (NULL , 'users', '*',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'choose_project',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'create',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'edit',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'user_overview',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'users', 'view',CURRENT_TIMESTAMP);

/*

1 | System administrator | ETI admin; creates new projects and lead experts
2 | Lead expert          | General manager of a project
3 | Expert               | Content manager of a project
4 | Editor               | Edits specific parts of a project
5 | Contributor          | Contributes to a project but cannot edit


 1 | users  | ALL                   | *
 2 | users  | Login                 | index.php
 3 | users  | Choose active project | choose_project.php
 4 | users  | Create                | create.php
 5 | users  | Edit                  | edit.php
 6 | users  | View users            | user_overview.php
 7 | users  | View user             | view.php

*/

# users:all
INSERT INTO dev_rights_roles VALUES (NULL, 1, 1, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles VALUES (NULL, 1, 2, CURRENT_TIMESTAMP );

# users:login
INSERT INTO dev_rights_roles VALUES (NULL, 2, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles VALUES (NULL, 2, 4, CURRENT_TIMESTAMP );

# users:choose project
INSERT INTO dev_rights_roles VALUES (NULL, 3, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles VALUES (NULL, 3, 4, CURRENT_TIMESTAMP );

# users:view users
INSERT INTO dev_rights_roles VALUES (NULL, 6, 3, CURRENT_TIMESTAMP );

# users:view user
INSERT INTO dev_rights_roles VALUES (NULL, 7, 3, CURRENT_TIMESTAMP );


INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( NULL , 'mdschermer', md5('balance'), 'Maarten', 'Schermer', 'maarten.schermer@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);
INSERT INTO dev_users (
id ,username ,password ,first_name ,last_name ,email_address ,active ,last_login ,logins ,password_changed ,last_change ,created
) VALUES ( NULL , 'jlborges', md5('ficiones'), 'Jorge Luis', 'Borges', 'slavlab@xs4all.nl', '1', NULL , '0', NULL ,
CURRENT_TIMESTAMP , CURRENT_TIMESTAMP);


insert into dev_projects_roles_users values (null, 1, 2, 1);
insert into dev_projects_roles_users values (null, 2, 3, 1);
insert into dev_projects_roles_users values (null, 2, 2, 2);
insert into dev_projects_roles_users values (null, 1, 4, 2);
