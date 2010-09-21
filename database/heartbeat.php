CREATE TABLE heartbeats (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL,
	user_id INT( 11 ) NOT NULL,
	app varchar(32) not null,
	ctrllr varchar(32) not null,
	view varchar(32) not null,
	params varchar(255) ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	unique ( project_id,user_id,app,ctrllr,view )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

rename table heartbeats to dev_heartbeats;
