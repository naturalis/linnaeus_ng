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
	page varchar (16) not null,
	content longtext ,
	content_name varchar (32),
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	unique (taxon_id, language_id, page)	
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `taxa`  TO `dev_taxa` ;
RENAME TABLE `content_taxa`  TO `dev_content_taxa` ;

