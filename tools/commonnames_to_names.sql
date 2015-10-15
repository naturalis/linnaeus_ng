/* manually add the beheer-module to the project and alter the rights to the module */
/* preliminaries */
set @project_id=401; /* change this to the appropriate ID */


CREATE TABLE `trash_can` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `lng_id` int(11) NOT NULL,
  `item_type` varchar(32) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trash_can_1` (`project_id`,`lng_id`,`item_type`),
  KEY `trash_can_2` (`project_id`),
  KEY `trash_can_3` (`project_id`,`lng_id`,`item_type`),
  KEY `trash_can_4` (`project_id`,`lng_id`,`item_type`,`is_deleted`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
;

insert into modules (id,module,description,controller,icon,show_order,show_in_menu,show_in_public_menu,created,last_change)
values (15,'Beheer Soortenregister','Beheer Soortenregister','nsr','index.png',0,1,0,now(),now());
insert into rights (controller,view,view_description,created) values ('nsr','*','full access',now());


insert into name_types values (null,@project_id,'isValidNameOf',now(),now());
insert into name_types values (null,@project_id,'isSynonymOf',now(),now());
insert into name_types values (null,@project_id,'isSynonymSLOf',now(),now());
insert into name_types values (null,@project_id,'isBasionymOf',now(),now());
insert into name_types values (null,@project_id,'isHomonymOf',now(),now());
insert into name_types values (null,@project_id,'isAlternativeNameOf',now(),now());
insert into name_types values (null,@project_id,'isPreferredNameOf',now(),now());
insert into name_types values (null,@project_id,'isMisspelledNameOf',now(),now());
insert into name_types values (null,@project_id,'isInvalidNameOf',now(),now());

insert languages (language,iso3,direction,created) values ('Scientific','sci','ltr',now());


/* delete previous valid names, preferred names, synonyms from names table */
delete from names where
	taxon_id in (select id from taxa)
	and  type_id =
		(select id from name_types where project_id=@project_id and nametype ='isValidNameOf');

delete from names where
	taxon_id in (select taxon_id from commonnames)
	and type_id =
		(select id from name_types where project_id=@project_id and nametype ='isPreferredNameOf');

delete from names where 
	taxon_id in (select taxon_id from synonyms)
	and type_id =
		(select id from name_types where project_id=@project_id and nametype ='isSynonymOf');


/* create valid names */
insert into names (project_id,taxon_id,language_id,type_id,name,authorship,created)
	select 
		@project_id,
		_a.id,
		_c.id,
		_b.id,
		_a.taxon, 
		_a.author , 
		now()
	from 
		taxa _a

	left join name_types _b
		on _a.project_id=_b.project_id
		and _b.nametype = 'isValidNameOf'

	left join languages _c
		on _c.language='scientific'
		
	where
		_a.project_id=@project_id
		and _a.taxon is not null
;

/* create synonyms */
insert into names (project_id,taxon_id,language_id,type_id,name,authorship,created)
	select 
		@project_id,
		_a.taxon_id,
		_c.id,
		_b.id,
		_a.synonym, 
		_a.author , 
		now()
	from 
		synonyms _a

	left join name_types _b
		on _a.project_id=_b.project_id
		and _b.nametype = 'isSynonymOf'

	left join languages _c
		on _c.language='scientific'
		
	where
		_a.project_id=@project_id
		and _a.synonym is not null
;




/* 
	create preferred names
	known bug: using show_order=0 as switch for preferred/alternative will turn
	all but the very first name into alternatives, even if they are the only
	name in a specific language (the show_order transcends language)
 */
insert into names (project_id,taxon_id,language_id,type_id,name,created)
select
	@project_id,
	taxon_id,
	language_id,
	if (show_order=0,_b.id,_c.id) as type_id,
	name,
	now() as created

from
(
	select 
		_a.taxon_id,
		_a.commonname as name, 
		_a.language_id,
		_a.show_order
	from 
		commonnames _a
	where
		_a.project_id=@project_id
		and _a.commonname is not null

	union

	select 
		_a.taxon_id,
		_a.transliteration as name, 
		_a.language_id,
		_a.show_order
	from 
		commonnames _a
	where
		_a.project_id=@project_id
		and _a.transliteration is not null
) as unionized

left join name_types _b
	on _b.project_id = @project_id
	and _b.nametype = 'isPreferredNameOf'

left join name_types _c
	on _b.project_id = @project_id
	and _c.nametype = 'isAlternativeNameOf'

order by taxon_id,language_id,show_order
;
