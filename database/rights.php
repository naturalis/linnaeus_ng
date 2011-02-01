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
	unique ( controller, view )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE rights_roles (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	right_id INT( 11 ) NOT NULL  ,
	role_id INT( 11 ) NOT NULL  ,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( right_id, role_id ),
	unique(right_id,role_id)
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
INSERT INTO dev_rights VALUES (NULL , 'species', 'col',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'collaborators',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'file',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'media',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'media_upload',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'ranklabels',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'ranks',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'sections',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'species', 'taxon',CURRENT_TIMESTAMP);


INSERT INTO dev_rights VALUES (NULL , 'key', '*','all',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'index','index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'step_edit','editing keysteps',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'step_show','reviewing keysteps and choices',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'choice_edit','editing choices',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'dead_ends','list of dead ends',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'section','list of key sections',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'map','key map',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'orphans','list of orphans',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'process','create list of remaining taxa',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'key', 'rank','define rank of taxa available in key',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'literature', '*','full access',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'literature', 'browse','browse literary references',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'literature', 'edit','edit literary references',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'literature', 'search','search literary references',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'glossary', '*','full access',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'highertaxa', '*','full access',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'matrixkey', '*','full access',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'mapkey', '*','full access',CURRENT_TIMESTAMP);


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
15 | species    | edit
16 | species    | list
17 | species    | page
18 | species    | col
19 | species    | collaborators
20 | species    | file
21 | species    | media
22 | species    | media_upload
23 | species    | ranklabels
24 | species    | ranks
25 | species    | sections
26 | species    | taxon
27 | key        | *
28 | key        | index
29 | key        | step_edit
30 | key        | step_show
31 | key        | choice_edit
32 | key        | dead_ends
33 | key        | section
34 | key        | map
35 | key        | orphans
36 | key        | process
37 | key        | rank
38 | literature | *
39 | literature | browse
40 | literature | edit
41 | literature | search
42 | glossary   | *
43 | highertaxa | *
44 | matrixkey  | *
45 | mapkey     | *

*/







# system administrator
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 1, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 8, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 13, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 27, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 38, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 42, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 43, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 44, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 45, CURRENT_TIMESTAMP );

# lead expert
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 1, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 8, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 13, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 27, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 38, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 42, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 43, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 44, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 45, CURRENT_TIMESTAMP );

# expert
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 6, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 7, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 13, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 27, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 38, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 42, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 43, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 44, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 45, CURRENT_TIMESTAMP );

# editor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 15, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 16, CURRENT_TIMESTAMP );

# contributor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (5, 3, CURRENT_TIMESTAMP );
