CREATE TABLE taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	taxon varchar (32) not null,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) 
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

create table content_taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	taxon_id INT( 11 ) NOT NULL ,
	language_id INT( 11 ) NOT NULL ,
	page_id INT( 11 ) NOT NULL ,
	content longtext ,
	title varchar (64),
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique (project_id , taxon_id, language_id, page_id)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

create table taxa_pages (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	page varchar (32) not null,
	def_page boolean not null default 0,	
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique ( project_id, page)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

create table taxa_pages_titles (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	page_id INT( 11 ) NOT NULL ,
	language_id INT( 11 ) NOT NULL ,
	title varchar (32) not null,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique ( project_id, page_id, language_id)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `taxa`  TO `dev_taxa` ;
RENAME TABLE `content_taxa`  TO `dev_content_taxa` ;
RENAME TABLE `taxa_pages`  TO `dev_taxa_pages` ;
RENAME TABLE `taxa_pages_titles`  TO `dev_taxa_pages_titles` ;


