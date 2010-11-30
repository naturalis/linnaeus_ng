CREATE TABLE languages (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	language VARCHAR( 32 ) NOT NULL unique,
	iso3 VARCHAR( 3 ) NOT NULL unique,
	direction enum('l2r','r2l') default 'l2r',	
	show_order integer(2) default null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) 
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE lables_languages (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id int( 11 ) NOT NULL  ,
	language_id VARCHAR( 32 ) NOT NULL,
	label_language_id VARCHAR( 32 ) NOT NULL,
	label VARCHAR(64) NOT NULL,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) 
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;



RENAME TABLE `languages`  TO `dev_languages` ;
RENAME TABLE `lables_languages`  TO `dev_lables_languages` ;

insert into dev_languages values (null,'Abkhaz','abk','l2r',null,now());
insert into dev_languages values (null,'Afrikaans','afr','l2r',null,now());
insert into dev_languages values (null,'Albanian','alb','l2r',null,now());
insert into dev_languages values (null,'Amharic','amh','l2r',null,now());
insert into dev_languages values (null,'Arabic','ara','r2l',null,now());
insert into dev_languages values (null,'Assyrian/Syriac','syr','r2l',null,now());
insert into dev_languages values (null,'Armenian','arm','l2r',null,now());
insert into dev_languages values (null,'Assamese','asm','l2r',null,now());
insert into dev_languages values (null,'Aymara','aym','l2r',null,now());
insert into dev_languages values (null,'Azeri','aze','r2l',null,now());
insert into dev_languages values (null,'Basque','baq','l2r',null,now());
insert into dev_languages values (null,'Belarusian','bel','l2r',null,now());
insert into dev_languages values (null,'Bengali','ben','l2r',null,now());
insert into dev_languages values (null,'Bislama','bis','l2r',null,now());
insert into dev_languages values (null,'Bosnian','bos','l2r',null,now());
insert into dev_languages values (null,'Bulgarian','bul','l2r',null,now());
insert into dev_languages values (null,'Burmese','bur','l2r',null,now());
insert into dev_languages values (null,'Catalan','cat','l2r',null,now());
insert into dev_languages values (null,'Chinese','chi','l2r',null,now());
insert into dev_languages values (null,'Croatian','hrv','l2r',null,now());
insert into dev_languages values (null,'Czech','cze','l2r',null,now());
insert into dev_languages values (null,'Danish','dan','l2r',null,now());
insert into dev_languages values (null,'Dhivehi','div','r2l',null,now());
insert into dev_languages values (null,'Dutch','dut','l2r',2,now());
insert into dev_languages values (null,'Dzongkha','dzo','l2r',null,now());
insert into dev_languages values (null,'English','eng','l2r',1,now());
insert into dev_languages values (null,'Estonian','est','l2r',null,now());
insert into dev_languages values (null,'Fijian','fij','l2r',null,now());
insert into dev_languages values (null,'Filipino','fil','l2r',null,now());
insert into dev_languages values (null,'Finnish','fin','l2r',null,now());
insert into dev_languages values (null,'French','fre','l2r',5,now());
insert into dev_languages values (null,'Frisian','frs','l2r',null,now());
insert into dev_languages values (null,'Gagauz','gag','l2r',null,now());
insert into dev_languages values (null,'Galician','glg','l2r',null,now());
insert into dev_languages values (null,'Georgian','geo','l2r',null,now());
insert into dev_languages values (null,'German','ger','l2r',4,now());
insert into dev_languages values (null,'Greek','gre','l2r',null,now());
insert into dev_languages values (null,'Guaraní','grn','l2r',null,now());
insert into dev_languages values (null,'Gujarati','guj','l2r',null,now());
insert into dev_languages values (null,'Haitian Creole','hat','l2r',null,now());
insert into dev_languages values (null,'Hebrew','heb','r2l',null,now());
insert into dev_languages values (null,'Hindi','hin','l2r',null,now());
insert into dev_languages values (null,'Hiri Motu','hmo','l2r',null,now());
insert into dev_languages values (null,'Hungarian','hun','l2r',null,now());
insert into dev_languages values (null,'Icelandic','ice','l2r',null,now());
insert into dev_languages values (null,'Indonesian','ind','l2r',null,now());
insert into dev_languages values (null,'Inuinnaqtun','ikt','l2r',null,now());
insert into dev_languages values (null,'Inuktitut','iku','l2r',null,now());
insert into dev_languages values (null,'Irish','gle','l2r',null,now());
insert into dev_languages values (null,'Italian','ita','l2r',null,now());
insert into dev_languages values (null,'Japanese','jpn','l2r',null,now());
insert into dev_languages values (null,'Kannada','kan','l2r',null,now());
insert into dev_languages values (null,'Kashmiri','kas','r2l',null,now());
insert into dev_languages values (null,'Kazakh','kaz','r2l',null,now());
insert into dev_languages values (null,'Khmer','khm','l2r',null,now());
insert into dev_languages values (null,'Korean','kor','l2r',null,now());
insert into dev_languages values (null,'Kurdish','kur','r2l',null,now());
insert into dev_languages values (null,'Kyrgyz','kir','l2r',null,now());
insert into dev_languages values (null,'Lao','lao','l2r',null,now());
insert into dev_languages values (null,'Latin','lat','l2r',null,now());
insert into dev_languages values (null,'Latvian','lav','l2r',null,now());
insert into dev_languages values (null,'Lithuanian','lit','l2r',null,now());
insert into dev_languages values (null,'Luxembourgish','ltz','l2r',null,now());
insert into dev_languages values (null,'Macedonian','mac','l2r',null,now());
insert into dev_languages values (null,'Malagasy','mlg','l2r',null,now());
insert into dev_languages values (null,'Malay','may','r2l',null,now());
insert into dev_languages values (null,'Malayalam','mal','r2l',null,now());
insert into dev_languages values (null,'Maltese','mlt','l2r',null,now());
insert into dev_languages values (null,'Manx Gaelic','glv','l2r',null,now());
insert into dev_languages values (null,'Ma-ori','mao','l2r',null,now());
insert into dev_languages values (null,'Marathi','mar','l2r',null,now());
insert into dev_languages values (null,'Mayan','myn','l2r',null,now());
insert into dev_languages values (null,'Moldovan','rum','l2r',null,now());
insert into dev_languages values (null,'Mongolian','mon','l2r',null,now());
insert into dev_languages values (null,'Náhuatl','nah','l2r',null,now());
insert into dev_languages values (null,'Ndebele','nde','l2r',null,now());
insert into dev_languages values (null,'Nepali','nep','l2r',null,now());
insert into dev_languages values (null,'Northern Sotho','nso','l2r',null,now());
insert into dev_languages values (null,'Norwegian','nor','l2r',null,now());
insert into dev_languages values (null,'Occitan','oci','l2r',null,now());
insert into dev_languages values (null,'Oriya','ori','l2r',null,now());
insert into dev_languages values (null,'Ossetian','oss','l2r',null,now());
insert into dev_languages values (null,'Papiamento','pap','l2r',null,now());
insert into dev_languages values (null,'Pashto','pus','r2l',null,now());
insert into dev_languages values (null,'Persian','per','r2l',null,now());
insert into dev_languages values (null,'Polish','pol','l2r',null,now());
insert into dev_languages values (null,'Portuguese','por','l2r',null,now());
insert into dev_languages values (null,'Punjabi','pan','r2l',null,now());
insert into dev_languages values (null,'Quechua','que','l2r',null,now());
insert into dev_languages values (null,'Romanian','rum','l2r',null,now());
insert into dev_languages values (null,'Rhaeto-Romansh','roh','l2r',null,now());
insert into dev_languages values (null,'Russian','rus','l2r',null,now());
insert into dev_languages values (null,'Sanskrit','san','l2r',null,now());
insert into dev_languages values (null,'Serbian','srp','l2r',null,now());
insert into dev_languages values (null,'Shona','sna','l2r',null,now());
insert into dev_languages values (null,'Sindhi','snd','r2l',null,now());
insert into dev_languages values (null,'Sinhala','sin','l2r',null,now());
insert into dev_languages values (null,'Slovak','slo','l2r',null,now());
insert into dev_languages values (null,'Slovene','slv','l2r',null,now());
insert into dev_languages values (null,'Somali','som','r2l',null,now());
insert into dev_languages values (null,'Sotho','sot','l2r',null,now());
insert into dev_languages values (null,'Spanish','spa','l2r',3,now());
insert into dev_languages values (null,'Sranan Tongo','srn','l2r',null,now());
insert into dev_languages values (null,'Swahili','swa','l2r',null,now());
insert into dev_languages values (null,'Swati','ssw','l2r',null,now());
insert into dev_languages values (null,'Swedish','swe','l2r',null,now());
insert into dev_languages values (null,'Tajik','tgk','l2r',null,now());
insert into dev_languages values (null,'Tamil','tam','l2r',null,now());
insert into dev_languages values (null,'Telugu','tel','l2r',null,now());
insert into dev_languages values (null,'Tetum','tet','l2r',null,now());
insert into dev_languages values (null,'Thai','tha','l2r',null,now());
insert into dev_languages values (null,'Tok Pisin','tpi','l2r',null,now());
insert into dev_languages values (null,'Tsonga','tog','l2r',null,now());
insert into dev_languages values (null,'Tswana','tsn','l2r',null,now());
insert into dev_languages values (null,'Turkish','tur','l2r',null,now());
insert into dev_languages values (null,'Turkmen','tuk','r2l',null,now());
insert into dev_languages values (null,'Ukrainian','ukr','l2r',null,now());
insert into dev_languages values (null,'Urdu','urd','r2l',null,now());
insert into dev_languages values (null,'Uzbek','uzb','l2r',null,now());
insert into dev_languages values (null,'Venda','ven','l2r',null,now());
insert into dev_languages values (null,'Vietnamese','vie','l2r',null,now());
insert into dev_languages values (null,'Welsh','wel','l2r',null,now());
insert into dev_languages values (null,'Xhosa','xho','l2r',null,now());
insert into dev_languages values (null,'Yiddish','yid','r2l',null,now());
insert into dev_languages values (null,'Zulu','zul','l2r',null,now());




CREATE TABLE languages_projects (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	language_id INT( 11 ) NOT NULL  ,
	project_id INT( 11 ) NOT NULL  ,
	def_language boolean not null default 0,
	active enum ('y','n') default 'y' not null,
	created DATETIME NOT NULL ,
	PRIMARY KEY ( id ) ,
	INDEX ( language_id, project_id ),
	unique ( language_id, project_id )
) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE utf8_general_ci ;


RENAME TABLE `languages_projects`  TO `dev_languages_projects` ;

insert into dev_languages_projects values (null, 23, 2, 0, 'y', now());
insert into dev_languages_projects values (null, 51, 2, 1, 'y', now());
