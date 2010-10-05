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

RENAME TABLE `roles`  TO `dev_roles` ;
RENAME TABLE `rights`  TO `dev_rights` ;
RENAME TABLE `rights_roles`  TO `dev_rights_roles` ;



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

INSERT INTO dev_rights VALUES (NULL , 'projects', '*',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'projects', 'index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'projects', 'data',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'projects', 'modules',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'projects', 'collaborators',CURRENT_TIMESTAMP);


INSERT INTO dev_rights VALUES (NULL , 'species', '*',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'edit',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'list',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'page',CURRENT_TIMESTAMP);




/*

1 | System administrator | ETI admin; creates new projects and lead experts
2 | Lead expert          | General manager of a project
3 | Expert               | Content manager of a project
4 | Editor               | Edits specific parts of a project
5 | Contributor          | Contributes to a project but cannot edit


 1 | users      | *
 2 | users      | index
 3 | users      | choose_project
 4 | users      | create
 5 | users      | edit
 6 | users      | user_overview
 7 | users      | view
 8 | projects   | *
 9 | projects   | index
10 | projects   | data
11 | projects   | modules
12 | projects   | collaborators
13 | species    | *
14 | species    | index

*/


# system administrator
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 1, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 8, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 13, CURRENT_TIMESTAMP );

# lead expert
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 1, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 8, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 13, CURRENT_TIMESTAMP );

# expert
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 6, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 7, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 13, CURRENT_TIMESTAMP );

# editor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 15, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 16, CURRENT_TIMESTAMP );


# contributor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 3, CURRENT_TIMESTAMP );
