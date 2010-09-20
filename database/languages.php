CREATE TABLE languages ( id INT( 11 ) NOT NULL AUTO_INCREMENT , language
VARCHAR( 32 ) NOT NULL unique, iso3 VARCHAR( 3 ) NOT NULL unique, iso2
VARCHAR( 2 ) NULL unique, direction enum('l2r','r2l') default 'l2r',
show_order integer(2) default null, created DATETIME NOT NULL , PRIMARY
KEY ( id ) ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci
; RENAME TABLE `languages` TO `dev_languages` ; insert into
dev_languages values (null,'Abkhaz','abk','l2r',null,now()); insert into
dev_languages values (null,'Afrikaans','afr','l2r',null,now()); insert
into dev_languages values (null,'Albanian','alb','l2r',null,now());
insert into dev_languages values
(null,'Amharic','amh','l2r',null,now()); insert into dev_languages
values (null,'Arabic','ara','r2l',null,now()); insert into dev_languages
values (null,'Assyrian/Syriac','syr','r2l',null,now()); insert into
dev_languages values (null,'Armenian','arm','l2r',null,now()); insert
into dev_languages values (null,'Assamese','asm','l2r',null,now());
insert into dev_languages values (null,'Aymara','aym','l2r',null,now());
insert into dev_languages values (null,'Azeri','aze','r2l',null,now());
insert into dev_languages values (null,'Basque','baq','l2r',null,now());
insert into dev_languages values
(null,'Belarusian','bel','l2r',null,now()); insert into dev_languages
values (null,'Bengali','ben','l2r',null,now()); insert into
dev_languages values (null,'Bislama','bis','l2r',null,now()); insert
into dev_languages values (null,'Bosnian','bos','l2r',null,now());
insert into dev_languages values
(null,'Bulgarian','bul','l2r',null,now()); insert into dev_languages
values (null,'Burmese','bur','l2r',null,now()); insert into
dev_languages values (null,'Catalan','cat','l2r',null,now()); insert
into dev_languages values (null,'Chinese','chi','l2r',null,now());
insert into dev_languages values
(null,'Croatian','hrv','l2r',null,now()); insert into dev_languages
values (null,'Czech','cze','l2r',null,now()); insert into dev_languages
values (null,'Danish','dan','l2r',null,now()); insert into dev_languages
values (null,'Dhivehi','div','r2l',null,now()); insert into
dev_languages values (null,'Dutch','dut','l2r',2,now()); insert into
dev_languages values (null,'Dzongkha','dzo','l2r',null,now()); insert
into dev_languages values (null,'English','eng','l2r',1,now()); insert
into dev_languages values (null,'Estonian','est','l2r',null,now());
insert into dev_languages values (null,'Fijian','fij','l2r',null,now());
insert into dev_languages values
(null,'Filipino','fil','l2r',null,now()); insert into dev_languages
values (null,'Finnish','fin','l2r',null,now()); insert into
dev_languages values (null,'French','fre','l2r',5,now()); insert into
dev_languages values (null,'Frisian','frs','l2r',null,now()); insert
into dev_languages values (null,'Gagauz','gag','l2r',null,now()); insert
into dev_languages values (null,'Galician','glg','l2r',null,now());
insert into dev_languages values
(null,'Georgian','geo','l2r',null,now()); insert into dev_languages
values (null,'German','ger','l2r',4,now()); insert into dev_languages
values (null,'Greek','gre','l2r',null,now()); insert into dev_languages
values (null,'Guaran�','grn','l2r',null,now()); insert into
dev_languages values (null,'Gujarati','guj','l2r',null,now()); insert
into dev_languages values (null,'Haitian
Creole','hat','l2r',null,now()); insert into dev_languages values
(null,'Hebrew','heb','r2l',null,now()); insert into dev_languages values
(null,'Hindi','hin','l2r',null,now()); insert into dev_languages values
(null,'Hiri Motu','hmo','l2r',null,now()); insert into dev_languages
values (null,'Hungarian','hun','l2r',null,now()); insert into
dev_languages values (null,'Icelandic','ice','l2r',null,now()); insert
into dev_languages values (null,'Indonesian','ind','l2r',null,now());
insert into dev_languages values
(null,'Inuinnaqtun','ikt','l2r',null,now()); insert into dev_languages
values (null,'Inuktitut','iku','l2r',null,now()); insert into
dev_languages values (null,'Irish','gle','l2r',null,now()); insert into
dev_languages values (null,'Italian','ita','l2r',null,now()); insert
into dev_languages values (null,'Japanese','jpn','l2r',null,now());
insert into dev_languages values
(null,'Kannada','kan','l2r',null,now()); insert into dev_languages
values (null,'Kashmiri','kas','r2l',null,now()); insert into
dev_languages values (null,'Kazakh','kaz','r2l',null,now()); insert into
dev_languages values (null,'Khmer','khm','l2r',null,now()); insert into
dev_languages values (null,'Korean','kor','l2r',null,now()); insert into
dev_languages values (null,'Kurdish','kur','r2l',null,now()); insert
into dev_languages values (null,'Kyrgyz','kir','l2r',null,now()); insert
into dev_languages values (null,'Lao','lao','l2r',null,now()); insert
into dev_languages values (null,'Latin','lat','l2r',null,now()); insert
into dev_languages values (null,'Latvian','lav','l2r',null,now());
insert into dev_languages values
(null,'Lithuanian','lit','l2r',null,now()); insert into dev_languages
values (null,'Luxembourgish','ltz','l2r',null,now()); insert into
dev_languages values (null,'Macedonian','mac','l2r',null,now()); insert
into dev_languages values (null,'Malagasy','mlg','l2r',null,now());
insert into dev_languages values (null,'Malay','may','r2l',null,now());
insert into dev_languages values
(null,'Malayalam','mal','r2l',null,now()); insert into dev_languages
values (null,'Maltese','mlt','l2r',null,now()); insert into
dev_languages values (null,'Manx Gaelic','glv','l2r',null,now()); insert
into dev_languages values (null,'Ma-ori','mao','l2r',null,now()); insert
into dev_languages values (null,'Marathi','mar','l2r',null,now());
insert into dev_languages values (null,'Mayan','myn','l2r',null,now());
insert into dev_languages values
(null,'Moldovan','rum','l2r',null,now()); insert into dev_languages
values (null,'Mongolian','mon','l2r',null,now()); insert into
dev_languages values (null,'N�huatl','nah','l2r',null,now()); insert
into dev_languages values (null,'Ndebele','nde','l2r',null,now());
insert into dev_languages values (null,'Nepali','nep','l2r',null,now());
insert into dev_languages values (null,'Northern
Sotho','nso','l2r',null,now()); insert into dev_languages values
(null,'Norwegian','nor','l2r',null,now()); insert into dev_languages
values (null,'Occitan','oci','l2r',null,now()); insert into
dev_languages values (null,'Oriya','ori','l2r',null,now()); insert into
dev_languages values (null,'Ossetian','oss','l2r',null,now()); insert
into dev_languages values (null,'Papiamento','pap','l2r',null,now());
insert into dev_languages values (null,'Pashto','pus','r2l',null,now());
insert into dev_languages values
(null,'Persian','per','r2l',null,now()); insert into dev_languages
values (null,'Polish','pol','l2r',null,now()); insert into dev_languages
values (null,'Portuguese','por','l2r',null,now()); insert into
dev_languages values (null,'Punjabi','pan','r2l',null,now()); insert
into dev_languages values (null,'Quechua','que','l2r',null,now());
insert into dev_languages values
(null,'Romanian','rum','l2r',null,now()); insert into dev_languages
values (null,'Rhaeto-Romansh','roh','l2r',null,now()); insert into
dev_languages values (null,'Russian','rus','l2r',null,now()); insert
into dev_languages values (null,'Sanskrit','san','l2r',null,now());
insert into dev_languages values
(null,'Serbian','srp','l2r',null,now()); insert into dev_languages
values (null,'Shona','sna','l2r',null,now()); insert into dev_languages
values (null,'Sindhi','snd','r2l',null,now()); insert into dev_languages
values (null,'Sinhala','sin','l2r',null,now()); insert into
dev_languages values (null,'Slovak','slo','l2r',null,now()); insert into
dev_languages values (null,'Slovene','slv','l2r',null,now()); insert
into dev_languages values (null,'Somali','som','r2l',null,now()); insert
into dev_languages values (null,'Sotho','sot','l2r',null,now()); insert
into dev_languages values (null,'Spanish','spa','l2r',3,now()); insert
into dev_languages values (null,'Sranan Tongo','srn','l2r',null,now());
insert into dev_languages values
(null,'Swahili','swa','l2r',null,now()); insert into dev_languages
values (null,'Swati','ssw','l2r',null,now()); insert into dev_languages
values (null,'Swedish','swe','l2r',null,now()); insert into
dev_languages values (null,'Tajik','tgk','l2r',null,now()); insert into
dev_languages values (null,'Tamil','tam','l2r',null,now()); insert into
dev_languages values (null,'Telugu','tel','l2r',null,now()); insert into
dev_languages values (null,'Tetum','tet','l2r',null,now()); insert into
dev_languages values (null,'Thai','tha','l2r',null,now()); insert into
dev_languages values (null,'Tok Pisin','tpi','l2r',null,now()); insert
into dev_languages values (null,'Tsonga','tog','l2r',null,now()); insert
into dev_languages values (null,'Tswana','tsn','l2r',null,now()); insert
into dev_languages values (null,'Turkish','tur','l2r',null,now());
insert into dev_languages values
(null,'Turkmen','tuk','r2l',null,now()); insert into dev_languages
values (null,'Ukrainian','ukr','l2r',null,now()); insert into
dev_languages values (null,'Urdu','urd','r2l',null,now()); insert into
dev_languages values (null,'Uzbek','uzb','l2r',null,now()); insert into
dev_languages values (null,'Venda','ven','l2r',null,now()); insert into
dev_languages values (null,'Vietnamese','vie','l2r',null,now()); insert
into dev_languages values (null,'Welsh','wel','l2r',null,now()); insert
into dev_languages values (null,'Xhosa','xho','l2r',null,now()); insert
into dev_languages values (null,'Yiddish','yid','r2l',null,now());
insert into dev_languages values (null,'Zulu','zul','l2r',null,now());

update dev_languages set iso2 = 'aa' where iso3 ='aar'; update
dev_languages set iso2 = 'ab' where iso3 ='abk'; update dev_languages
set iso2 = 'af' where iso3 ='afr'; update dev_languages set iso2 = 'ak'
where iso3 ='aka'; update dev_languages set iso2 = 'sq' where iso3
='sqi'; update dev_languages set iso2 = 'am' where iso3 ='amh'; update
dev_languages set iso2 = 'ar' where iso3 ='ara'; update dev_languages
set iso2 = 'an' where iso3 ='arg'; update dev_languages set iso2 = 'hy'
where iso3 ='hye'; update dev_languages set iso2 = 'as' where iso3
='asm'; update dev_languages set iso2 = 'av' where iso3 ='ava'; update
dev_languages set iso2 = 'ae' where iso3 ='ave'; update dev_languages
set iso2 = 'ay' where iso3 ='aym'; update dev_languages set iso2 = 'az'
where iso3 ='aze'; update dev_languages set iso2 = 'ba' where iso3
='bak'; update dev_languages set iso2 = 'bm' where iso3 ='bam'; update
dev_languages set iso2 = 'eu' where iso3 ='eus'; update dev_languages
set iso2 = 'be' where iso3 ='bel'; update dev_languages set iso2 = 'bn'
where iso3 ='ben'; update dev_languages set iso2 = 'bh' where iso3
='bih'; update dev_languages set iso2 = 'bi' where iso3 ='bis'; update
dev_languages set iso2 = 'bs' where iso3 ='bos'; update dev_languages
set iso2 = 'br' where iso3 ='bre'; update dev_languages set iso2 = 'bg'
where iso3 ='bul'; update dev_languages set iso2 = 'my' where iso3
='mya'; update dev_languages set iso2 = 'ca' where iso3 ='cat'; update
dev_languages set iso2 = 'ch' where iso3 ='cha'; update dev_languages
set iso2 = 'ce' where iso3 ='che'; update dev_languages set iso2 = 'cu'
where iso3 ='chu'; update dev_languages set iso2 = 'cv' where iso3
='chv'; update dev_languages set iso2 = 'kw' where iso3 ='cor'; update
dev_languages set iso2 = 'co' where iso3 ='cos'; update dev_languages
set iso2 = 'cr' where iso3 ='cre'; update dev_languages set iso2 = 'cs'
where iso3 ='ces'; update dev_languages set iso2 = 'da' where iso3
='dan'; update dev_languages set iso2 = 'dv' where iso3 ='div'; update
dev_languages set iso2 = 'nl' where iso3 ='dut'; update dev_languages
set iso2 = 'dz' where iso3 ='dzo'; update dev_languages set iso2 = 'en'
where iso3 ='eng'; update dev_languages set iso2 = 'eo' where iso3
='epo'; update dev_languages set iso2 = 'et' where iso3 ='est'; update
dev_languages set iso2 = 'ee' where iso3 ='ewe'; update dev_languages
set iso2 = 'fo' where iso3 ='fao'; update dev_languages set iso2 = 'fj'
where iso3 ='fij'; update dev_languages set iso2 = 'fi' where iso3
='fin'; update dev_languages set iso2 = 'fr' where iso3 ='fre'; update
dev_languages set iso2 = 'fy' where iso3 ='fry'; update dev_languages
set iso2 = 'ff' where iso3 ='ful'; update dev_languages set iso2 = 'ka'
where iso3 ='geo'; update dev_languages set iso2 = 'de' where iso3
='ger'; update dev_languages set iso2 = 'gd' where iso3 ='gla'; update
dev_languages set iso2 = 'ga' where iso3 ='gle'; update dev_languages
set iso2 = 'gl' where iso3 ='glg'; update dev_languages set iso2 = 'gv'
where iso3 ='glv'; update dev_languages set iso2 = 'el' where iso3
='gre'; update dev_languages set iso2 = 'gn' where iso3 ='grn'; update
dev_languages set iso2 = 'gu' where iso3 ='guj'; update dev_languages
set iso2 = 'ht' where iso3 ='hat'; update dev_languages set iso2 = 'ha'
where iso3 ='hau'; update dev_languages set iso2 = 'he' where iso3
='heb'; update dev_languages set iso2 = 'hz' where iso3 ='her'; update
dev_languages set iso2 = 'hi' where iso3 ='hin'; update dev_languages
set iso2 = 'ho' where iso3 ='hmo'; update dev_languages set iso2 = 'hr'
where iso3 ='hrv'; update dev_languages set iso2 = 'hu' where iso3
='hun'; update dev_languages set iso2 = 'ig' where iso3 ='ibo'; update
dev_languages set iso2 = 'is' where iso3 ='ice'; update dev_languages
set iso2 = 'io' where iso3 ='ido'; update dev_languages set iso2 = 'ii'
where iso3 ='iii'; update dev_languages set iso2 = 'iu' where iso3
='iku'; update dev_languages set iso2 = 'ie' where iso3 ='ile'; update
dev_languages set iso2 = 'ia' where iso3 ='ina'; update dev_languages
set iso2 = 'id' where iso3 ='ind'; update dev_languages set iso2 = 'ik'
where iso3 ='ipk'; update dev_languages set iso2 = 'it' where iso3
='ita'; update dev_languages set iso2 = 'jv' where iso3 ='jav'; update
dev_languages set iso2 = 'ja' where iso3 ='jpn'; update dev_languages
set iso2 = 'kl' where iso3 ='kal'; update dev_languages set iso2 = 'kn'
where iso3 ='kan'; update dev_languages set iso2 = 'ks' where iso3
='kas'; update dev_languages set iso2 = 'kr' where iso3 ='kau'; update
dev_languages set iso2 = 'kk' where iso3 ='kaz'; update dev_languages
set iso2 = 'km' where iso3 ='khm'; update dev_languages set iso2 = 'ki'
where iso3 ='kik'; update dev_languages set iso2 = 'rw' where iso3
='kin'; update dev_languages set iso2 = 'ky' where iso3 ='kir'; update
dev_languages set iso2 = 'kv' where iso3 ='kom'; update dev_languages
set iso2 = 'kg' where iso3 ='kon'; update dev_languages set iso2 = 'ko'
where iso3 ='kor'; update dev_languages set iso2 = 'kj' where iso3
='kua'; update dev_languages set iso2 = 'ku' where iso3 ='kur'; update
dev_languages set iso2 = 'lo' where iso3 ='lao'; update dev_languages
set iso2 = 'la' where iso3 ='lat'; update dev_languages set iso2 = 'lv'
where iso3 ='lav'; update dev_languages set iso2 = 'li' where iso3
='lim'; update dev_languages set iso2 = 'ln' where iso3 ='lin'; update
dev_languages set iso2 = 'lt' where iso3 ='lit'; update dev_languages
set iso2 = 'lb' where iso3 ='ltz'; update dev_languages set iso2 = 'lu'
where iso3 ='lub'; update dev_languages set iso2 = 'lg' where iso3
='lug'; update dev_languages set iso2 = 'mk' where iso3 ='mac'; update
dev_languages set iso2 = 'mh' where iso3 ='mah'; update dev_languages
set iso2 = 'ml' where iso3 ='mal'; update dev_languages set iso2 = 'mi'
where iso3 ='mao'; update dev_languages set iso2 = 'mr' where iso3
='mar'; update dev_languages set iso2 = 'ms' where iso3 ='may'; update
dev_languages set iso2 = 'mg' where iso3 ='mlg'; update dev_languages
set iso2 = 'mt' where iso3 ='mlt'; update dev_languages set iso2 = 'mn'
where iso3 ='mon'; update dev_languages set iso2 = 'na' where iso3
='nau'; update dev_languages set iso2 = 'nv' where iso3 ='nav'; update
dev_languages set iso2 = 'nr' where iso3 ='nbl'; update dev_languages
set iso2 = 'nd' where iso3 ='nde'; update dev_languages set iso2 = 'ng'
where iso3 ='ndo'; update dev_languages set iso2 = 'ne' where iso3
='nep'; update dev_languages set iso2 = 'nn' where iso3 ='nno'; update
dev_languages set iso2 = 'nb' where iso3 ='nob'; update dev_languages
set iso2 = 'no' where iso3 ='nor'; update dev_languages set iso2 = 'ny'
where iso3 ='nya'; update dev_languages set iso2 = 'oc' where iso3
='oci'; update dev_languages set iso2 = 'oj' where iso3 ='oji'; update
dev_languages set iso2 = 'or' where iso3 ='ori'; update dev_languages
set iso2 = 'om' where iso3 ='orm'; update dev_languages set iso2 = 'os'
where iso3 ='oss'; update dev_languages set iso2 = 'pa' where iso3
='pan'; update dev_languages set iso2 = 'fa' where iso3 ='per'; update
dev_languages set iso2 = 'pi' where iso3 ='pli'; update dev_languages
set iso2 = 'pl' where iso3 ='pol'; update dev_languages set iso2 = 'pt'
where iso3 ='por'; update dev_languages set iso2 = 'ps' where iso3
='pus'; update dev_languages set iso2 = 'qu' where iso3 ='que'; update
dev_languages set iso2 = 'rm' where iso3 ='roh'; update dev_languages
set iso2 = 'ro' where iso3 ='rum'; update dev_languages set iso2 = 'rn'
where iso3 ='run'; update dev_languages set iso2 = 'ru' where iso3
='rus'; update dev_languages set iso2 = 'sg' where iso3 ='sag'; update
dev_languages set iso2 = 'sa' where iso3 ='san'; update dev_languages
set iso2 = 'si' where iso3 ='sin'; update dev_languages set iso2 = 'sk'
where iso3 ='slo'; update dev_languages set iso2 = 'sl' where iso3
='slv'; update dev_languages set iso2 = 'se' where iso3 ='sme'; update
dev_languages set iso2 = 'sm' where iso3 ='smo'; update dev_languages
set iso2 = 'sn' where iso3 ='sna'; update dev_languages set iso2 = 'sd'
where iso3 ='snd'; update dev_languages set iso2 = 'so' where iso3
='som'; update dev_languages set iso2 = 'st' where iso3 ='sot'; update
dev_languages set iso2 = 'es' where iso3 ='spa'; update dev_languages
set iso2 = 'sc' where iso3 ='srd'; update dev_languages set iso2 = 'sr'
where iso3 ='srp'; update dev_languages set iso2 = 'ss' where iso3
='ssw'; update dev_languages set iso2 = 'su' where iso3 ='sun'; update
dev_languages set iso2 = 'sw' where iso3 ='swa'; update dev_languages
set iso2 = 'sv' where iso3 ='swe'; update dev_languages set iso2 = 'ty'
where iso3 ='tah'; update dev_languages set iso2 = 'ta' where iso3
='tam'; update dev_languages set iso2 = 'tt' where iso3 ='tat'; update
dev_languages set iso2 = 'te' where iso3 ='tel'; update dev_languages
set iso2 = 'tg' where iso3 ='tgk'; update dev_languages set iso2 = 'tl'
where iso3 ='tgl'; update dev_languages set iso2 = 'th' where iso3
='tha'; update dev_languages set iso2 = 'ti' where iso3 ='tir'; update
dev_languages set iso2 = 'to' where iso3 ='ton'; update dev_languages
set iso2 = 'tn' where iso3 ='tsn'; update dev_languages set iso2 = 'ts'
where iso3 ='tso'; update dev_languages set iso2 = 'tk' where iso3
='tuk'; update dev_languages set iso2 = 'tr' where iso3 ='tur'; update
dev_languages set iso2 = 'tw' where iso3 ='twi'; update dev_languages
set iso2 = 'ug' where iso3 ='uig'; update dev_languages set iso2 = 'uk'
where iso3 ='ukr'; update dev_languages set iso2 = 'ur' where iso3
='urd'; update dev_languages set iso2 = 'uz' where iso3 ='uzb'; update
dev_languages set iso2 = 've' where iso3 ='ven'; update dev_languages
set iso2 = 'vi' where iso3 ='vie'; update dev_languages set iso2 = 'vo'
where iso3 ='vol'; update dev_languages set iso2 = 'cy' where iso3
='ven'; update dev_languages set iso2 = 'cy' where iso3 ='wel'; update
dev_languages set iso2 = 'wa' where iso3 ='wln'; update dev_languages
set iso2 = 'wo' where iso3 ='wol'; update dev_languages set iso2 = 'xh'
where iso3 ='xho'; update dev_languages set iso2 = 'yi' where iso3
='yid'; update dev_languages set iso2 = 'yo' where iso3 ='yor'; update
dev_languages set iso2 = 'za' where iso3 ='zha'; update dev_languages
set iso2 = 'zh' where iso3 ='zho'; update dev_languages set iso2 = 'zu'
where iso3 ='zul'; CREATE TABLE languages_projects ( id INT( 11 ) NOT
NULL AUTO_INCREMENT , language_id INT( 11 ) NOT NULL , project_id INT(
11 ) NOT NULL , def_language boolean not null default 0, active enum
('y','n') default 'y' not null, created DATETIME NOT NULL , PRIMARY KEY
( id ) , INDEX ( language_id, project_id ), unique ( language_id,
project_id ) ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE
utf8_general_ci ; RENAME TABLE `languages_projects` TO
`dev_languages_projects` ; insert into dev_languages_projects values
(null, 23, 2, 0, 'y', now()); insert into dev_languages_projects values
(null, 51, 2, 1, 'y', now());
