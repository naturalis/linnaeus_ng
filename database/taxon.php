CREATE TABLE taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	taxon varchar (32) not null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	UNIQUE ( taxon )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

create table content_taxa (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	taxon_id INT( 11 ) NOT NULL ,
	language_id INT( 11 ) NOT NULL ,
	page_name varchar (32) not null,
	content longtext ,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	unique (taxon_id, language_id, page_name)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `taxa`  TO `dev_taxa` ;
RENAME TABLE `content_taxa`  TO `dev_content_taxa` ;

