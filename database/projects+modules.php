CREATE TABLE projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	name VARCHAR( 64 ) NOT NULL ,
	version VARCHAR( 16 ) NOT NULL ,
	description TEXT NOT NULL ,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE modules (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	module VARCHAR( 64 ) NOT NULL ,
	description TEXT NOT NULL ,
	controller VARCHAR( 32 ) NOT NULL,
	show_order int( 2 ) not null,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE modules_projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	module_id INT( 11 ) NOT NULL  ,
	project_id INT( 11 ) NOT NULL  ,
	active enum ('y','n') default 'y' not null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( module_id, project_id )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE free_modules_projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL  ,
	module VARCHAR( 32 ) NOT NULL ,
	active enum ('y','n') default 'y' not null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( project_id, module )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;



RENAME TABLE `projects`  TO `dev_projects` ;
RENAME TABLE `modules`  TO `dev_modules` ;
RENAME TABLE `modules_projects`  TO `dev_modules_projects` ;
RENAME TABLE `free_modules_projects`  TO `dev_free_modules_projects` ;


insert into dev_projects values (null,'Polar Bears of Amsterdam','first beta','Ursus maritimus in the nation\'s capital',now(),null);
insert into dev_projects values (null,'Imaginary Beings','v1.0','See Borges',now(),null);


insert into dev_modules values (null,'Introduction','Comprehensive project introduction','Species',0,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Glossary','Project glossary','Species',1,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Literature','Literary references','Species',2,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Species module','Detailed pages for taxa','Species',3,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Higher taxa','Detailed pages for higher taxa','Species',4,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Text key','Dichotomic key based on text only','TextKey',5,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Picture key','Dichotomic key based on pictures and text', 'PictureKey', 6, current_timestamp, current_timestamp);
insert into dev_modules values (null,'Matrix key','Key based on attributes','MatrixKey',7,current_timestamp,current_timestamp);
insert into dev_modules values (null,'Map key','Key based on species distribution','MapKey',8,current_timestamp,current_timestamp);

