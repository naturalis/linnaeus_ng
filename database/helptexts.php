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




RENAME TABLE `helptexts`  TO `dev_helptexts` ;

truncate dev_helptexts;

insert into dev_helptexts values (null, 'users', 'login', 'Logging in',
'To log in, fill in your Linnaeus NG-username and password, and press the button labeled "Login".',0,current_timestamp,null);
insert into dev_helptexts values (null, 'users', 'login', 'Problems logging in?',
'If you cannot login, please <a href="mailto:helpdesk@linnaeus.eti.uva.nl">contact the helpdesk</a>.',1,current_timestamp,null);


insert into dev_helptexts values (null, 'users', 'edit', 'Role',
'The \'role\' indicates the role this user will have in the current project. Hover your mouse over the role\'s names to see a short description.',0,current_timestamp,null);
insert into dev_helptexts values (null, 'users', 'edit', 'Active',
'\'Active\' indicates whether a user is actively working on the current project. When set to \'n\', the user can no longer log in or work on the project. It allows you to temporarily disable users without deleting them outright.<br />Users that have the role of \'Lead expert\' cannot change role, or be made in-active, as they are the lead manager of a project.',1,current_timestamp,null);
