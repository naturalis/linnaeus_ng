CREATE TABLE taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	taxon varchar (32) not null,
	parent_id INT( 11 ),
	rank varchar (16),
	col_id INT( 11 ),
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
	publish boolean not null default 0,	
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique (project_id , taxon_id, language_id, page_id)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


create table content_taxa_undo (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	content_taxa_id INT( 11 ) NOT NULL ,
	project_id INT( 11 ) NOT NULL ,
	taxon_id INT( 11 ) NOT NULL ,
	language_id INT( 11 ) NOT NULL ,
	page_id INT( 11 ) NOT NULL ,
	content longtext ,
	title varchar (64),
	publish boolean not null default 0,	
	content_taxa_created datetime not null,
	content_last_change datetime not null,
	save_type enum ('auto','manual'),
	save_label varchar(64) null,
	created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	index (content_taxa_id, project_id )	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


create table pages_taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	page varchar (32) not null,
	def_page boolean not null default 0,	
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique ( project_id, page)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

create table pages_taxa_titles (
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

create table media_taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	taxon_id INT( 11 ) NOT NULL ,
	full_path varchar (512) not null,
	original_name varchar (128) not null,
	mime_type varchar (32) not null,
	file_size int(11) not null,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `taxa`  TO `dev_taxa` ;
RENAME TABLE `content_taxa`  TO `dev_content_taxa` ;
RENAME TABLE `content_taxa_undo`  TO `dev_content_taxa_undo` ;
RENAME TABLE `pages_taxa`  TO `dev_pages_taxa` ;
RENAME TABLE `pages_taxa_titles`  TO `dev_pages_taxa_titles` ;
RENAME TABLE `media_taxa`  TO `dev_media_taxa` ;





