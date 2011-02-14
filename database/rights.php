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
INSERT INTO dev_rights VALUES (NULL , 'literature', 'index','index',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'glossary', '*','full access',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'glossary', 'index','index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'glossary', 'browse','browse glossary',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'glossary', 'edit','edit and add glossary items',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'glossary', 'media_upload','ulpload media for glossary item',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'glossary', 'search','search glossary',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'highertaxa', '*','full access',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'highertaxa', 'index','index',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'matrixkey', '*','full access',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'index','index',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'char','add or edit characteristic',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'edit','edit a matrix',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'links','add or edit a link',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'matrices','view matrices',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'matrix','create a new matrix',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'state','add or edit states',CURRENT_TIMESTAMP);
INSERT INTO dev_rights VALUES (NULL , 'matrixkey', 'taxa','add or remove taxa',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'mapkey', '*','full access',CURRENT_TIMESTAMP);

INSERT INTO dev_rights VALUES (NULL , 'content', '*','full access',CURRENT_TIMESTAMP);


/*

1 | System administrator | ETI admin; creates new projects and lead experts
2 | Lead expert          | General manager of a project
3 | Expert               | Content manager of a project
4 | Editor               | Edits specific parts of a project
5 | Contributor          | Contributes to a project but cannot edit


 id | controller | view           | view_name
----+------------+----------------+-------------------------------------
  1 | users      | *              | full access
  2 | users      | index          | index
  3 | users      | choose_project | NULL
  4 | users      | create         | NULL
  5 | users      | edit           | NULL
  6 | users      | user_overview  | NULL
  7 | users      | view           | NULL
  8 | projects   | *              | full access
  9 | projects   | index          | index
 10 | projects   | data           | NULL
 11 | projects   | modules        | NULL
 12 | projects   | collaborators  | NULL
 13 | species    | *              | full access
 14 | species    | index          | index
 15 | species    | edit           | NULL
 16 | species    | list           | NULL
 17 | species    | page           | NULL
 18 | species    | col            | NULL
 19 | species    | collaborators  | NULL
 20 | species    | file           | NULL
 21 | species    | media          | NULL
 22 | species    | media_upload   | NULL
 23 | species    | ranklabels     | NULL
 24 | species    | ranks          | NULL
 25 | species    | sections       | NULL
 26 | species    | taxon          | NULL
 27 | key        | *              | full access
 28 | key        | index          | index
 29 | key        | step_edit      | editing keysteps
 30 | key        | step_show      | reviewing keysteps and choices
 31 | key        | choice_edit    | editing choices
 32 | key        | dead_ends      | list of dead ends
 33 | key        | section        | list of key sections
 34 | key        | map            | key map
 35 | key        | orphans        | list of orphans
 36 | key        | process        | create list of remaining taxa
 37 | key        | rank           | define rank of taxa available in key
 38 | literature | *              | full access
 47 | literature | index          | index
 39 | literature | browse         | browse literary references
 40 | literature | edit           | edit literary references
 41 | literature | search         | search literary references
 42 | glossary   | *              | full access
 48 | glossary   | index          | index
 49 | glossary   | browse         | browse glossary
 50 | glossary   | edit           | edit and add glossary items
 51 | glossary   | media_upload   | ulpload media for glossary item
 52 | glossary   | search         | search glossary
 43 | highertaxa | *              | full access
 53 | highertaxa | index          | index
 44 | matrixkey  | *              | full access
 54 | matrixkey  | index          | index
 55 | matrixkey  | char           | add or edit characteristic
 56 | matrixkey  | edit           | edit a matrix
 57 | matrixkey  | links          | add or edit a link
 58 | matrixkey  | matrices       | view matrices
 59 | matrixkey  | matrix         | create a new matrix
 60 | matrixkey  | state          | add or edit states
 61 | matrixkey  | taxa           | add or remove taxa
 45 | mapkey     | *              | full access
 46 | content    | *              | full access

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
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (1, 46, CURRENT_TIMESTAMP );

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
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (2, 46, CURRENT_TIMESTAMP );

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
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (3, 46, CURRENT_TIMESTAMP );

# editor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 3, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 14, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 15, CURRENT_TIMESTAMP );
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (4, 16, CURRENT_TIMESTAMP );

# contributor
INSERT INTO dev_rights_roles (role_id, right_id, created) VALUES (5, 3, CURRENT_TIMESTAMP );
