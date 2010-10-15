CREATE TABLE dev_ranks (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	rank varchar (128) not null,
	additional varchar(64),
	default_label varchar(32),
	parent_id INT( 11 ),
	in_col boolean default false,
	can_hybrid boolean default false,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	 unique (rank,additional),
	index (parent_id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE dev_hybrids (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	hybrid varchar (128) not null unique,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) 
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;


CREATE TABLE dev_projects_ranks (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	rank_id INT( 11 ) NOT NULL ,
	parent_id INT( 11 ),
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id )  ,
	index (project_id ),
	unique (project_id,rank_id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;

CREATE TABLE dev_labels_projects_ranks (
	id INT( 11 ) NOT NULL AUTO_INCREMENT ,
	project_id INT( 11 ) NOT NULL ,
	project_rank_id INT( 11 ) NOT NULL ,
	language_id INT( 11 ) NOT NULL ,
	label varchar (64) not null,
	created DATETIME NOT NULL ,
	last_change TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY ( id ) ,
	index (project_id,language_id)
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;




insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Domain or Empire','Empire',null,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Kingdom','Kingdom','1',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subkingdom','Subkingdom','2',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Branch','Branch','3',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infrakingdom','Infrakingdom','4',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('or Superdivision in botany','Superphylum','Superphylum ','5',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('or Division in botany','Phylum','Phylum','6',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('or Subdivision in botany','Subphylum','Subphylum','7',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('or Infradivision in botany','Infraphylum','Infraphylum','8',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Microphylum','Microphylum','9',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Supercohort','Supercohort','10',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Cohort','Cohort','11',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Subcohort','Subcohort','12',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Infracohort','Infracohort','13',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Superclass','Superclass','14',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Class','Class','15',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subclass','Subclass','16',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infraclass','Infraclass','17',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Parvclass','Parvclass','18',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Superdivision','Superdivision','19',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Division','Division','20',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Subdivision','Subdivision','21',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Infradivision','Infradivision','22',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Superlegion','Superlegion','23',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Legion','Legion','24',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Sublegion','Sublegion','25',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Infralegion','Infralegion','26',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Supercohort','Supercohort','27',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Cohort','Cohort','28',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Subcohort','Subcohort','29',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Infracohort','Infracohort','30',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Gigaorder','Gigaorder','31',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Magnorder or Megaorder','Megaorder','32',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Grandorder or Capaxorder','Grandorder','33',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Mirorder or Hyperorder','Hyperorder','34',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Superorder','Superorder','35',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('for fishes','Series','Series','36',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Order','Order','37',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('position in some  classifications','Parvorder','Parvorder','38',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoological','Nanorder','Nanorder','39',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoological','Hypoorder','Hypoorder','40',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoological','Minorder','Minorder','41',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Suborder','Suborder','42',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infraorder','Infraorder','43',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('(usual position) or Microorder (zoology)','Parvorder','Parvorder','44',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Section','Section ','45',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Subsection','Subsection','46',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Gigafamily','Gigafamily','47',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Megafamily','Megafamily','48',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Grandfamily','Grandfamily','49',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Hyperfamily','Hyperfamily ','50',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Superfamily','Superfamily','51',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Epifamily','Epifamily ','52',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('for Lepidoptera','Series','Series ','53',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('for Lepidoptera','Group','Group','54',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Family','Family','55',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subfamily','Subfamily','56',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infrafamily','Infrafamily','57',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Supertribe','Supertribe','58',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Tribe','Tribe','59',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subtribe','Subtribe','60',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infratribe','Infratribe','61',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Genus','Genus','62',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subgenus','Subgenus','63',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infragenus','Infragenus','64',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Section','Section','65',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Subsection','Subsection','66',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Series','Series','67',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Subseries','Subseries','68',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Superspecies or Species-group','Species Group','69',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Species Subgroup','Species Subgroup','70',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Species Complex','Species Complex','71',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Species Aggregate','Species Aggregate','72',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Species','Species','73',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Infraspecies','Infraspecies','74',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subspecific Aggregate','Subspecific Aggregate','75',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('or Forma Specialis for fungi, or Variety for bacteria','Subspecies','Subspecies','76',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('zoology','Varietas/Variety or Form/Morph','Variety','77',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Subvariety','Subvariety','78',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subsubvariety','Subsubvariety','79',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Forma/Form','Form','80',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values ('botany','Subform','Subform','81',now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Subsubform','Subsubform','82',now());


insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Candidate','Candidate',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Cultivar','Cultivar',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Cultivar-group','Cultivar-group',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Denomination Class','Denomination Class',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Graft-chimaera','Graft-chimaera',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Grex','Grex',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Patho-variety','Patho-variety',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Special form','Special form',-1,now());
insert into dev_ranks (additional,rank,default_label,parent_id,created) values (null,'Bio-variety','Bio-variety',-1,now());

update dev_ranks set can_hybrid = true where id >= 63;


update dev_ranks set in_col = true where rank = 'Kingdom';
update dev_ranks set in_col = true where rank = 'Phylum (or Division in botany)';
update dev_ranks set in_col = true where rank = 'Class';
update dev_ranks set in_col = true where rank = 'Order';
update dev_ranks set in_col = true where rank = 'Superfamily';
update dev_ranks set in_col = true where rank = 'Family';
update dev_ranks set in_col = true where rank = 'Genus';
update dev_ranks set in_col = true where rank = 'Species';
update dev_ranks set in_col = true where rank = 'Subspecies (or Forma Specialis for fungi, or Variety for bacteria)';



insert into dev_hybrids values (null,'x Genus',now(),null);
insert into dev_hybrids values (null,'x Genus species',now(),null);
insert into dev_hybrids values (null,'Genus x species',now(),null);
insert into dev_hybrids values (null,'Genus species x Genus species',now(),null);
 