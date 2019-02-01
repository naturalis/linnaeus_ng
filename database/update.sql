ALTER DATABASE `linnaeus_ng` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE linnaeus_ng;

insert ignore into name_types (
    select null, id as project_id, 'isMisidentificationOf', now(), now() from projects
);
insert ignore into name_types (
    select null, id as project_id, 'isNomenNudemOf', now(), now() from projects
);

INSERT IGNORE INTO `traits_date_formats` VALUES
(1,'year','Y','JJJJ','%Y',0,now(),now()),
(2,'fulldate','d-m-Y','DD-MM-JJJJ','%Y-%m-%d',1,now(),now());

INSERT IGNORE INTO `traits_types` VALUES 
(1,'boolean',1,2,'check_boolean',0,0,0,0,0,0,0,now(),now()),
(2,'stringlist',3,4,'check_stringlist',1,1,1,0,1,0,0,now(),now()),
(3,'stringlistfree',5,6,'check_stringlistfree',1,1,1,0,1,0,0,now(),now()),
(4,'stringfree',7,8,'check_stringfree',0,1,1,0,0,0,0,now(),now()),
(5,'intlist',9,NULL,'check_intlist',1,1,1,0,1,1,1,now(),now()),
(6,'intlistfree',10,11,'check_intlistfree',1,1,1,0,1,1,1,now(),now()),
(7,'intfree',12,13,'check_intfree',0,1,1,0,0,1,1,now(),now()),
(8,'intfreelimit',14,15,'check_intfreelimit',1,1,1,0,0,1,1,now(),now()),
(9,'floatlist',16,17,'check_floatlist',1,1,0,1,1,1,1,now(),now()),
(10,'floatlistfree',18,19,'check_floatlistfree',1,1,0,1,1,1,1,now(),now()),
(11,'floatfree',20,21,'check_floatfree',0,1,0,1,0,1,1,now(),now()),
(12,'floatfreelimit',22,23,'check_floatfreelimit',1,1,0,1,0,1,1,now(),now()),
(13,'datelist',24,NULL,'check_datelist',1,0,0,0,1,1,1,now(),now()),
(14,'datelistfree',25,26,'check_datelistfree',1,0,0,0,1,1,1,now(),now()),
(15,'datefree',27,NULL,'check_datefree',0,0,0,0,0,1,1,now(),now()),
(16,'datefreelimit',28,29,'check_datefreelimit',1,0,0,0,0,1,1,now(),now());


select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Artikel';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Article', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Boek';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Book', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Boek (deel)';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Book (part)', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Database';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Database', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Hoofdstuk';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Chapter', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Literatuur';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Literature', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Manuscript';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Manuscript', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Persbericht';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Press release', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Persoonlijke mededeling';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Personal communication', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Rapport';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Report', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Serie';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Series', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Tijdschrift';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Periodical', now(), now());

select (@id := id), (@project_id := project_id) from literature2_publication_types where sys_label = 'Website';
insert ignore into literature2_publication_types_labels values (null, @project_id, @id, 26, 'Web site', now(), now());


CREATE TABLE if not exists `actors_taxa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_id` (`project_id`,`taxon_id`,`actor_id`,`organisation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


UPDATE actors SET last_change = '1971-01-01 00:00:00' WHERE last_change = '0000-00-00 00:00:00';
