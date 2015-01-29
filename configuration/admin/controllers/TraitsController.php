<?php
/*
INSERT INTO modules
	(`id`, `module`, `description`, `controller`, `icon`, `show_order`, `show_in_menu`, `show_in_public_menu`, `created`, `last_change`)
VALUES
	(null, 'Kenmerken', 'Kenmerkenmodule', 'traits', 'index.png', 0, 1, 0, now(), now());

INSERT INTO rights
	(`id`, `controller`, `view`, `view_description`, `created`)
VALUES
	(null, 'traits', '*', 'full access', now());

//drop TABLE if exists traits_date_formats;
//drop TABLE if exists traits_groups;
//drop TABLE if exists traits_project_types;
//drop TABLE if exists traits_taxon_values;
//drop TABLE if exists traits_taxon_values_references;
//drop TABLE if exists traits_traits;
//drop TABLE if exists traits_types;
//drop TABLE if exists traits_values;

drop TABLE if exists `text_translations`;
CREATE TABLE IF NOT EXISTS `text_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NULL,
  `language_id` int(11) NOT NULL,
  `translation` varchar(4000) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

drop TABLE if exists `traits_types`;
CREATE TABLE IF NOT EXISTS `traits_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `verification_class_name` varchar(64),
  `allow_select_multiple` tinyint(1) default 0 not null,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

insert into text_translations values (1,null,24,'Boolean',now(),now());
insert into text_translations values (2,null,24,'Ja of Nee',now(),now());
insert into traits_types values (null,'boolean',1,2,'boolean',0,now(),now());

insert into text_translations values (3,null,24,'Lijst van termen',now(),now());
insert into text_translations values (4,null,24,'Lijst van termen, tekst, max. 255 karakters per term',now(),now());
insert into traits_types values (null,'stringlist',3,4,'stringlist',1,now(),now());

insert into text_translations values (5,null,24,'Lijst van termen plus optie eigen antwoord',now(),now());
insert into text_translations values (6,null,24,'Lijst van termen, tekst, max. 255 karakters per term, plus optie eigen antwoord ("Anders, namelijk:")',now(),now());
insert into traits_types values (null,'stringlistfree',5,6,'stringlistfree',1,now(),now());

insert into text_translations values (7,null,24,'Vrije invoer tekst',now(),now());
insert into text_translations values (8,null,24,'Vrije invoer, tekst, max. 1000 karakters',now(),now());
insert into traits_types values (null,'stringfree',7,8,'stringfree',0,now(),now());


insert into text_translations values (9,null,24,'Numerieke lijst, gehele getallen',now(),now());
insert into traits_types values (null,'intlist',9,null,'intlist',1,now(),now());

insert into text_translations values (10,null,24,'Numerieke lijst, gehele getallen, plus optie eigen antwoord',now(),now());
insert into text_translations values (11,null,24,'Numerieke lijst, gehele getallen (maximale precisie: 12,5), plus optie eigen antwoord ("Anders, namelijk:")',now(),now());
insert into traits_types values (null,'intlistfree',10,11,'intlistfree',1,now(),now());

insert into text_translations values (12,null,24,'Vrije invoer geheel getal',now(),now());
insert into text_translations values (13,null,24,'Vrije invoer, geheel getal, zonder begrenzing',now(),now());
insert into traits_types values (null,'intfree',12,13,'intfree',0,now(),now());

insert into text_translations values (14,null,24,'Vrije invoer geheel getal, begrensd',now(),now());
insert into text_translations values (15,null,24,'Vrije invoer, geheel getal, met boven- en/of ondergrens',now(),now());
insert into traits_types values (null,'intfreelimit',14,15,'intfreelimit',0,now(),now());


insert into text_translations values (16,null,24,'Numerieke lijst, decimale getallen',now(),now());
insert into text_translations values (17,null,24,'Numerieke lijst, decimale getallen (maximale precisie: 12,5)',now(),now());
insert into traits_types values (null,'floatlist',16,17,'floatlist',1,now(),now());

insert into text_translations values (18,null,24,'Numerieke lijst, decimale getallen, plus optie eigen antwoord',now(),now());
insert into text_translations values (19,null,24,'Numerieke lijst, decimale getallen, plus optie eigen antwoord ("Anders, namelijk:") (maximale precisie: 12,5)',now(),now());
insert into traits_types values (null,'floatlistfree',18,19,'floatlistfree',1,now(),now());

insert into text_translations values (20,null,24,'Vrije invoer decimaal getal',now(),now());
insert into text_translations values (21,null,24,'Vrije invoer, decimaal getal, zonder begrenzing (maximale precisie: 12,5)',now(),now());
insert into traits_types values (null,'floatfree',20,21,'floatfree',0,now(),now());

insert into text_translations values (22,null,24,'Vrije invoer decimaal getal, begrensd',now(),now());
insert into text_translations values (23,null,24,'Vrije invoer, decimaal getal, met boven- en/of ondergrens (maximale precisie: 12,5)',now(),now());
insert into traits_types values (null,'floatfreelimit',22,23,'floatfreelimit',0,now(),now());


insert into text_translations values (24,null,24,'Datumlijst (verschillende datumformaten)',now(),now());
insert into traits_types values (null,'datelist',24,null,'datelist',1,now(),now());

insert into text_translations values (25,null,24,'Datumlijst, plus optie eigen antwoord',now(),now());
insert into text_translations values (26,null,24,'Datumlijst, plus optie eigen antwoord ("Anders, namelijk:"), verschillende datumformaten',now(),now());
insert into traits_types values (null,'datelistfree',25,26,'datelistfree',1,now(),now());

insert into text_translations values (27,null,24,'Vrije invoer datum (verschillende datumformaten)',now(),now());
insert into traits_types values (null,'datefree',27,null,'datefree',0,now(),now());

insert into text_translations values (28,null,24,'Vrije invoer datum, begrensd',now(),now());
insert into text_translations values (29,null,24,'Vrije invoer, datum, met boven- en/of ondergrens (verschillende datumformaten)',now(),now());
insert into traits_types values (null,'datefreelimit',28,29,'datefreelimit',0,now(),now());

alter table traits_types add column allow_values tinyint(1) default 1 after verification_class_name;
update traits_types set allow_values=0 where id in (1,4,7,11,15);

alter table traits_types add column allow_max_length tinyint(1) default 0 after allow_values;
update traits_types set allow_max_length=1 where id in (2,3,4,5,6,7,8,9,10,11,12);

alter table traits_types add column allow_unit tinyint(1) default 0 after allow_max_length;
update traits_types set allow_unit=1 where id in (2,3,4,5,6,7,8); // KLOPT DIT!?

alter table traits_types add column allow_fractures tinyint(1) default 0 after allow_unit;
update traits_types set allow_fractures=1 where id in (9,10,11,12);



drop TABLE if exists `traits_date_formats`;
CREATE TABLE IF NOT EXISTS `traits_date_formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sysname` varchar(64) NOT NULL,
  `format` varchar(64) NOT NULL,
  `show_order` int(3) null DEFAULT 0,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`sysname`),
  UNIQUE KEY `format` (`format`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

insert into traits_date_formats values (null,'year','YYYY',0,now(),now());
insert into traits_date_formats values (null,'fulldate','DD-MM-YYYY',1,now(),now());


drop TABLE if exists `traits_groups`;
CREATE TABLE IF NOT EXISTS `traits_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `parent_id` int(11), # when null: topmost are projects ("invasieve exoten")
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `show_order` int(3) null DEFAULT 0,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`sysname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

drop TABLE if exists `traits_project_types`;
CREATE TABLE IF NOT EXISTS `traits_project_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_type` (`project_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

drop TABLE if exists `traits_traits`;
CREATE TABLE IF NOT EXISTS `traits_traits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_group_id` int(11) NOT NULL, # trait type
  `project_type_id` int(11) NOT NULL,
  `date_format_id` int(11),
  `sysname` varchar(64) NOT NULL,
  `name_tid` int(11) DEFAULT NULL,
  `code_tid` int(11) DEFAULT NULL,
  `description_tid` int(11) DEFAULT NULL,
  `unit` varchar(32) NULL, # m, gr, years, etc
  `can_select_multiple` tinyint(1) not null DEFAULT 1,
  `can_include_comment` tinyint(1) not null DEFAULT 0, # can include comment (other than extra value)
  `can_be_null` tinyint(1) not null DEFAULT 0, # trait accepts possible null values
  `show_index_numbers` tinyint(1) not null DEFAULT 0, # index with 1), 2) etc. when displaying
  `show_order` int(3) null DEFAULT 0,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sysname` (`project_id`,`trait_group_id`,`sysname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

alter table traits_traits add max_length smallint(3) null after description_tid;



drop TABLE if exists `traits_values`;
CREATE TABLE IF NOT EXISTS `traits_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_id` int(11) NOT NULL,
  `string_value` varchar(255),  
  `numerical_value` FLOAT(12,5),
  `numerical_value_end` FLOAT(12,5),
  `date_start` date,
  `date_end` date,
  `is_lower_limit` tinyint(1) default 0,  # for limit like "< 0" or "prior to 1758"
  `is_upper_limit` tinyint(1) default 0, # for limit  like "> 100" 
  `lower_limit_label` varchar(16), # must be able to have a label as well, i.e. "< 0"
  `upper_limit_label` varchar(16), # idem, "> 100"
  `show_order` int(3) null DEFAULT 0, # "natural" sort if null
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


----------------------------------------------

drop TABLE if exists `traits_taxon_values`;
CREATE TABLE IF NOT EXISTS `traits_taxon_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `taxon_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `comment` varchar(255), # ref `can_include_comment`
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


vvvvvvvvvvv IS EEN BRON PER WAARDE VAN BELANG??? vvvvvvvvvvvvvvvvv
TAXON-VALUE
- some sort of link!
- some sort of source!

drop TABLE if exists `traits_taxon_values_references`;
CREATE TABLE IF NOT EXISTS `traits_taxon_values_references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `trait_taxon_value_id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_update` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
*/

/*

	dude, are you COMPLETELY deleting trait groups!?
	NO! need to delete traits as well! + texts

*/	

include_once ('Controller.php');

class TraitsController extends Controller
{

	private $_lookupListMaxResults=99999;
	
	public $_defaultMaxLengthStringValue=1000;
	public $_defaultMaxLengthIntegerValue=12;
	public $_defaultMaxLengthFloatValue=17;

    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values'
    );
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array();
    
    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('traits.js','jquery.mjs.nestedSortable.js')
	);

    public $usedHelpers = array(
        'session_messages'
    );

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->smarty->assign('defaultMaxLengthStringValue',$this->_defaultMaxLengthStringValue);
		$this->smarty->assign('defaultMaxLengthIntegerValue',$this->_defaultMaxLengthIntegerValue);
		$this->smarty->assign('defaultMaxLengthFloatValue',$this->_defaultMaxLengthFloatValue);
    }

    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Index'));
		$this->printPage();
	}
	
    public function projectTypesAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Project data types'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','add'))
		{
			$this->addDatatypeToProject($this->requestData);
			$this->addMessage('Data type added to project.');
		}
		else
		if ($this->rHasVal('action','remove'))
		{
			$this->removeDatatypeFromProject($this->requestData);
			$this->addMessage('Data type removed project.');
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->printPage();
    }

    public function traitgroupsAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait groups'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveTraitgroupOrder($this->requestData);
			if ($i>0) $this->addMessage('New order saved.');
		}

		$this->smarty->assign('groups',$this->getTraitgroups());
		$this->printPage();
    }

    public function traitgroupAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait group'));

		if ($this->rHasVal('action','save'))
		{
			$this->saveTraitgroup($this->requestData);
			$this->helpers->SessionMessages->setMessage('Saved.');
			$this->redirect('traitgroups.php');
		}
		else 
		if ($this->rHasVal('action','delete'))
		{
			$this->deleteTraitgroup($this->requestData);
			$this->helpers->SessionMessages->setMessage('Group deleted.');
			$this->redirect('traitgroups.php');
		}
		else 
		if (!$this->rHasId())
		{
			$this->smarty->assign('newgroup',true);
		}
		else
		if ($this->rHasId())
		{
			$this->smarty->assign('group',$this->getTraitgroup($this->rGetId()));
			$this->smarty->assign('newgroup',false);
		}
		else
		{
			$this->smarty->assign('newgroup',true);
		}

		$this->smarty->assign('groups',$this->getTraitgroups());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }

    public function traitgroupTraitsAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait group traits'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveTraitgroupTraitsOrder($this->requestData);
			if ($i>0) $this->addMessage('New order saved.');
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$this->getTraitgroup($this->rGetVal('group')));
		$this->printPage();
    }

    public function traitgroupTraitAction()
    {
		$this->checkAuthorisation();
		
		if (!$this->rHasId() && !$this->rHasVar('group'))
			$this->redirect('index.php');
		
        $this->setPageName($this->translate('Trait group traits'));

		if ($this->rHasId())
		{
			$trait=$this->getTraitgroupTrait($this->rGetId());
			$group=$this->getTraitgroup($trait['trait_group_id']);
		}
		else
		if ($this->rHasVar('group'))
		{
			$trait=null;
			$group=$this->getTraitgroup($this->rGetVal('group'));
		}
		
		if ($this->rHasVal('action','save'))
		{

			$r=$this->saveTraitgroupTrait($this->requestData);
			
			if ($r)
			{
				if ($this->rHasId())
				{
					$this->addMessage('Data saved.');
					$trait=$this->getTraitgroupTrait($this->rGetId());
				}
				else
				{
					$this->helpers->SessionMessages->setMessage('Trait saved.');
					$this->redirect('traitgroup_traits.php?group='.$group['id']);
				}
			}
			else
			{
				$this->helpers->SessionMessages->setMessage('Saving trait failed.');
				$trait=$this->getTraitgroupTrait($this->rGetId());
			}
			
		}
		else
		if ($this->rHasVal('action','delete') && $this->rHasId())
		{
			$this->deleteTraitgroupTrait($this->requestData);
			$this->helpers->SessionMessages->setMessage('Trait deleted.');
			$this->redirect('traitgroup_traits.php?group='.$group['id']);
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$group);
		$this->smarty->assign('trait',$trait);
		$this->printPage();
    }

    public function traitgroupTraitValuesAction()
    {

		$this->checkAuthorisation();
		
		if (!$this->rHasId() && !$this->rHasVar('trait'))
			$this->redirect('index.php');
		
        $this->setPageName($this->translate('Trait values'));


		if ($this->rHasVal('action','save'))
		{
			$this->saveTraitgroupTraitValues($this->requestData);
		}

		if ($this->rHasVar('trait'))
		{
			$trait=$this->getTraitgroupTrait($this->rGetVal('trait'));
			$group=$this->getTraitgroup($trait['trait_group_id']);
		}

		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$group);
		$this->smarty->assign('trait',$trait);
		$this->printPage();
    }
	
	public function getTraitgroups($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;
		$level=isset($p['level']) ? $p['level'] : 0;
		$stopLevel=isset($p['stop_level']) ? $p['stop_level'] : null;
		
		$g=$this->models->TraitsGroups->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as description
			from
				%PRE%traits_groups _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."
				order by _a.show_order, _a.sysname
		");
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['level']=$level;	
			//$g[$key]['taxa']=$this->getTaxongroupTaxa($val['id']);
			if (!is_null($stopLevel) && $stopLevel<=$level)
			{
				continue;
			}
			$g[$key]['children']=$this->getTraitgroups(array('parent'=>$val['id'],'level'=>$level+1,'stop_level'=>$stopLevel));
		}
		
		return $g;
	}

	public function getTraitgroupTraits($group)
	{
		if (empty($group)) return;
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as code,
				_d.translation as description,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.allow_values as type_allow_values,
				_g.allow_select_multiple as type_allow_select_multiple,
				_g.allow_max_length as type_allow_max_length,
				_g.allow_unit as type_allow_unit,
				count(_v.id) as value_count

			from
				%PRE%traits_traits _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id
				
			left join
				%PRE%traits_values _v
				on _a.project_id=_v.project_id
				and _a.id=_v.trait_id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.trait_group_id=".$group."
			group by _a.id
			order by _a.show_order
		");

		return $r;
	}

	public function getTraitgroupTrait($id)
	{
		if (empty($id)) return;
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as code,
				_d.translation as description,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.verification_function_name as type_verification_function_name
			from
				%PRE%traits_traits _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");

		$r = isset($r[0]) ? $r[0] : null;

		if (!empty($r))
		{
			if (strpos($r['type_sysname'],'float')===false)
			{
				$r['max_length']=round($r['max_length'],0,PHP_ROUND_HALF_DOWN);
			}

			$r['values']=$this->getTraitgroupTraitValues(array('trait'=>$id));
		}

		return $r;
	}

	public function getTraitgroupTraitValues($p)
	{
		$trait=isset($p['trait']) ? $p['trait'] : null;

		if (empty($trait)) return;

		$r=$this->models->TraitsValues->freeQuery("
			select
				_a.id,
				_a.trait_id,
				_a.string_value,
				_a.numerical_value,
				_a.numerical_value_end,
				_a.date,
				_a.date_end,
				_a.is_lower_limit,
				_a.is_upper_limit,
				_a.lower_limit_label,
				_a.upper_limit_label,						
				_g.allow_fractures,
				_e.format as date_format_format

			from 
				%PRE%traits_values _a
				
			left join 
				%PRE%traits_traits _b
				on _a.project_id=_b.project_id
				and _a.trait_id=_b.id

			left join 
				%PRE%traits_date_formats _e
				on _b.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _b.project_id=_f.project_id
				and _b.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.trait_id = ".$trait." 
			order by 
				_a.show_order
		
		");
		
		foreach((array)$r as $key=>$val)
		{
			if ($val['allow_fractures']!='1' && (!empty($val['numerical_value']) || !empty($val['numerical_value_end'])))
			{
				if (!empty($val['numerical_value']))
					$r[$key]['numerical_value']=round($val['numerical_value'],0,PHP_ROUND_HALF_DOWN);
				if (!empty($val['numerical_value_end']))
					$r[$key]['numerical_value_end']=round($val['numerical_value_end'],0,PHP_ROUND_HALF_DOWN);
			} else
			if (!empty($val['date']) || !empty($val['date_end'])  && !empty($val['date_format_format']))
			{
				if (!empty($val['date']))
					$r[$key]['date']=$this->formatDbDate($val['date'],$val['date_format_format']);
				if (!empty($val['date_end']))
					$r[$key]['date_end']=$this->formatDbDate($val['date_end'],$val['date_format_format']);
			}
		}

		return $r;

	}

    public function settingsAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Traits settings'));
        $this->printPage();
    }
	
    public function ajaxInterfaceAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','verifydate'))
		{
			if (!$this->rGetVal('date') || !$this->rGetVal('format'))
			{
				$r=!$this->rGetVal('date') ? 'missing date' : 'missing format';
			}
			else
			{
				$r=$this->verifyDate($this->rGetVal('date'),$this->rGetVal('format'));
			}

		}
		
		$this->smarty->assign('returnText',json_encode($r));
		$this->printPage();
    }
	



	private function saveTextTranslation($text,$language_id)
	{
		$this->models->TextTranslations->save(array(
			'project_id'=>$this->getCurrentProjectId(),
			'language_id'=>$language_id,
			'translation'=>$text,
		));
		return $this->models->TextTranslations->getNewId();
	}
	
	private function getDatatypes()
	{
		$g=$this->models->TraitsTypes->freeQuery("
			select
				_a.*,
				_d.translation as name,
				_e.translation as description,
				_b.id as project_type_id
			from
				%PRE%traits_types _a

			left join 
				%PRE%text_translations _d
				on _a.name_tid=_d.id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.id
				and _e.language_id=". $this->getDefaultProjectLanguage() ."
				and _e.project_id is null
				
			left join 
				%PRE%traits_project_types _b
				on _a.id=_b.type_id
				and _b.project_id=". $this->getCurrentProjectId()."
			order by _a.id
		");

		return $g;
	}

	private function getProjectDatatype($id)
	{
		if (empty($id)) return;
		
		$t=$this->models->TraitsTypes->freeQuery("
			select
				_a.*,
				_d.translation as name,
				_e.translation as description,
				_b.id as project_type_id
			from
				%PRE%traits_types _a

			left join 
				%PRE%text_translations _d
				on _a.name_tid=_d.id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.id
				and _e.language_id=". $this->getDefaultProjectLanguage() ."
				and _e.project_id is null
				
			left join 
				%PRE%traits_project_types _b
				on _a.id=_b.type_id
				and _b.project_id=". $this->getCurrentProjectId()."
			where _b.id =".$id."
		");
		
		$t=isset($t[0]) ? $t[0] : null;

		return $t;
	}

	private function getDateFormats()
	{
		return $this->models->TraitsTypes->freeQuery("select * from %PRE%traits_date_formats order by show_order");
	}

	private function addDatatypeToProject($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;

		$this->models->TraitsProjectTypes->save(array(
			'project_id'=>$this->getCurrentProjectId(),
			'type_id'=>mysql_real_escape_string($id)
		));

		return true;
	}

	private function removeDatatypeFromProject($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;

		$this->models->TraitsProjectTypes->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>mysql_real_escape_string($id)
		));
		
		return true;
	}
	
	private function getTraitgroup($id)
	{
		if (empty($id)) return;

		$d=$this->models->TraitsGroups->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as description
			from
				%PRE%traits_groups _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");
		
		$r=$d[0];

		$d=$this->models->TextTranslations->_get(array("id"=>array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$r['name_tid']
		)));

		foreach((array)$d as $val)
		{
			$r['names'][$val['language_id']]=$val['translation'];
		}

		$d=$this->models->TextTranslations->_get(array("id"=>array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$r['description_tid']
		)));

		foreach((array)$d as $val)
		{
			$r['descriptions'][$val['language_id']]=$val['translation'];
		}
		
		$r['groups']=$this->getTraitgroups(array('parent'=>$r['id'],'level'=>0,'stop_level'=>0));

		$r['traits']=$this->getTraitgroupTraits($r['id']);

		return $r;
	}
	
	private function saveTraitgroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$parent_id=!empty($p['parent_id']) ? $p['parent_id'] : 'null';
		$sysname=isset($p['sysname']) ? $p['sysname'] : null;
		$names=isset($p['names']) ? $p['names'] : null;
		$descriptions=isset($p['descriptions']) ? $p['descriptions'] : null;

		if (empty($sysname))
			return false;

		$this->models->TraitsGroups->save(array(
			'id'=>$id,
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=>mysql_real_escape_string($parent_id),
			'sysname'=>mysql_real_escape_string($sysname)
		));
		
		if (empty($id)) $id=$this->models->TraitsGroups->getNewId();
		
		if (!$id) return;
					
		foreach((array)$names as $language_id=>$name)
		{
			$tid=$this->saveTextTranslation($name,$language_id);
			if ($tid)
			{
				$this->models->TraitsGroups->update(array('name_tid'=>$tid),array('id'=>$id));			
			}

			if (isset($descriptions[$language_id]))
			{
				$tid=$this->saveTextTranslation($descriptions[$language_id],$language_id);
				
				if ($tid)
				{
					$this->models->TraitsGroups->update(array('description_tid'=>$tid),array('id'=>$id));			
				}
			}
		}
		
		return true;
	}

	private function saveTraitgroupOrder($p)
	{
		$groups=isset($p['sortable']) ? $p['sortable'] : null;

		if (empty($groups))
			return false;
			
		$i=0;
		foreach((array)$groups as $key=>$group)
		{
			$this->models->TraitsGroups->save(array(
				'id'=>$group,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->TraitsGroups->getAffectedRows();
		}
		return $i;
	}

	private function deleteTraitgroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;
		
		$g=$this->getTraitgroup($id);
		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['name_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['description_tid']
		));		
					
		$this->models->TraitsGroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$id
		));

		$this->models->TraitsGroups->update(
			array('parent_id'=>'null'),
			array('project_id'=>$this->getCurrentProjectId(),'parent_id'=>mysql_real_escape_string($id))
		);

		return true;
	}

	private function saveTraitgroupTrait($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$trait_group_id=isset($p['trait_group_id']) ? $p['trait_group_id'] : null;
		$project_type_id=isset($p['project_type_id']) ? $p['project_type_id'] : null;
		$date_format_id=isset($p['date_format_id']) ? $p['date_format_id'] : null;
		$sysname=isset($p['sysname']) ? $p['sysname'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$code=isset($p['code']) ? $p['code'] : null;
		$description=isset($p['description']) ? $p['description'] : null;
		$unit=isset($p['unit']) ? $p['unit'] : null;
		$can_be_null=isset($p['can_be_null']) ? $p['can_be_null'] : null;
		$can_select_multiple=isset($p['can_select_multiple']) ? $p['can_select_multiple'] : null;
		$can_include_comment=isset($p['can_include_comment']) ? $p['can_include_comment'] : null;
		$can_have_range=isset($p['can_have_range']) ? $p['can_have_range'] : null;
		$show_index_numbers=isset($p['show_index_numbers']) ? $p['show_index_numbers'] : null;
		$show_order=isset($p['show_order']) && is_numeric($p['show_order'])  ? $p['show_order'] : (empty($id) ? 999 : null);
		$max_length=isset($p['max_length']) && is_numeric($p['max_length']) ? $p['max_length'] : null;
		
		if (empty($trait_group_id)||empty($project_type_id)||empty($sysname)||empty($name))
		{
			if (empty($trait_group_id)) $this->addError($this->translate('Missing group ID.'));
			if (empty($project_type_id)) $this->addError($this->translate('Missing type ID.'));
			if (empty($sysname)) $this->addError($this->translate('Missing system name.'));
			if (empty($name)) $this->addError($this->translate('Missing name.'));
			$this->addError($this->translate('Trait not saved'));
			return;
		}

		$type=$this->getProjectDatatype($project_type_id);

		if (!empty($max_length) && $max_length < 1)
		{
			$max_length=null;
			$this->addWarning($this->translate('Max. length cannot be smaller than 1'));
		}
		else
		if (!empty($max_length))
		{
			
			if (
				 	(
						$type['sysname']=='stringlist' || 
						$type['sysname']=='stringlistfree' || 
						$type['sysname']=='stringfree'
					) &&
			 		$max_length > $this->_defaultMaxLengthStringValue
				)
			{
				$max_length=$this->_defaultMaxLengthStringValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthStringValue));
			}
			else
			if (
				 	(
						$type['sysname']=='intlist' || 
						$type['sysname']=='intlistfree' || 
						$type['sysname']=='intfree' ||
						$type['sysname']=='intfreelimit'
					) &&
			 		$max_length > $this->_defaultMaxLengthIntegerValue
				)
			{
				$max_length=$this->_defaultMaxLengthIntegerValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthIntegerValue));
			}
			else
			if (
				 	(
						$type['sysname']=='floatlist' || 
						$type['sysname']=='floatlistfree' || 
						$type['sysname']=='floatfree' ||
						$type['sysname']=='floatfreelimit'
					) &&
			 		$max_length > $this->_defaultMaxLengthFloatValue
				)
			{
				$max_length=$this->_defaultMaxLengthFloatValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthFloatValue));
			}
		}


		$d=array(
				'project_id' => $this->getCurrentProjectId(),
				'trait_group_id' => $trait_group_id,
				'project_type_id' => $project_type_id,
				'date_format_id' => $date_format_id,
				'sysname' => trim($sysname),
				'unit' => $unit,
				'can_select_multiple' => ($can_select_multiple=='y' ? 1 : 0),
				'can_include_comment' => ($can_include_comment=='y' ? 1 : 0),
				'can_be_null' => ($can_be_null=='y' ? 1 : 0),
				'can_have_range' => ($can_have_range=='y' ? 1 : 0),
				'show_index_numbers' => ($show_index_numbers=='y' ? 1 : 0),
				'show_order' => $show_order,
				'max_length' => $max_length
			);

		if (!empty($id)) $d['id']=$id;

		$d=$this->models->TraitsTraits->save($d);

		if ($d)
		{
			if (empty($id))
			{
				$id=$this->models->TraitsTraits->getNewId();
			}
			
			if (!empty($name))
			{
				$nId=$this->saveTextTranslation($name,$this->getDefaultProjectLanguage());
				$this->models->TraitsTraits->update(array('name_tid'=>$nId),array('id'=>$id));
			}

			if (!empty($code))
			{
				$cId=$this->saveTextTranslation($code,$this->getDefaultProjectLanguage());
				$this->models->TraitsTraits->update(array('code_tid'=>$cId),array('id'=>$id));
			}

			if (!empty($description))
			{
				$dId=$this->saveTextTranslation($description,$this->getDefaultProjectLanguage());
				$this->models->TraitsTraits->update(array('description_tid'=>$dId),array('id'=>$id));
			}

			return true;
		}
		else 
		{
			return false;
		}
			
	}

	private function saveTraitgroupTraitsOrder($p)
	{
		$traits=isset($p['sortable']) ? $p['sortable'] : null;

		if (empty($traits))
			return false;
			
		$i=0;
		foreach((array)$traits as $key=>$trait)
		{
			$this->models->TraitsTraits->save(array(
				'id'=>$trait,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->TraitsTraits->getAffectedRows();
		}
		return $i;
	}

	private function deleteTraitgroupTrait($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;
		
		$g=$this->getTraitgroupTrait($id);
		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['name_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['code_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['description_tid']
		));		
		
		$this->models->TraitsTraits->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$id
		));

		return true;
	}
	
	private function saveTraitgroupTraitValues($p)
	{
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$values=isset($p['values']) ? $p['values'] : null;

		if (empty($trait))
		{
			if (empty($trait)) $this->addError($this->translate('Missing trait ID.'));
			if (empty($values)) $this->addError($this->translate('No values to save.'));
			$this->addError($this->translate('Values not saved'));
			return;
		}
		
		$trait=$this->getTraitgroupTrait($trait);

		$base=array(
			'project_id'=>$this->getCurrentProjectId(),
			'trait_id'=>$trait['id']
		);
		
		if ($trait['type_sysname']=='stringlist' || $trait['type_sysname']=='stringlistfree')
		{
			
			$this->models->TraitsValues->delete($base);

			$saved=0;

			foreach((array)$values as $key=>$val)
			{
				$d=$base+
					array(
						'string_value'=>trim($val),
						'show_order'=>$key
					);
				
				$r=$this->models->TraitsValues->save($d);
				
				if (!$r)
				{
					$this->addError(sprintf($this->translate('Value %s not saved'),$val));
				}
				else
				{
					$saved++;
				}

			}
			
			$this->addMessage(sprintf($this->translate('%s values saved'),$saved));

		}
		else
		if (
			$trait['type_sysname']=='intlist' || $trait['type_sysname']=='intlistfree' ||
			$trait['type_sysname']=='floatlist' || $trait['type_sysname']=='floatlistfree'
			)
		{
			$this->models->TraitsValues->delete($base);

			$saved=0;

			foreach((array)$values as $key=>$val)
			{
				$d=$base+
					array(
						'numerical_value'=>trim($val),
						'show_order'=>$key
					);
				
				$r=$this->models->TraitsValues->save($d);
				
				if (!$r)
				{
					$this->addError(sprintf($this->translate('Value %s not saved'),$val));
				}
				else
				{
					$saved++;
				}

			}
			
			$this->addMessage(sprintf($this->translate('%s values saved'),$saved));

		}
		else
		if ($trait['type_sysname']=='datelist' || $trait['type_sysname']=='datelistfree')
		{
			$this->models->TraitsValues->delete($base);

			$saved=0;

			foreach((array)$values as $key=>$val)
			{
				$r=date_parse_from_format($trait['date_format_format'],$val);
				
				if ($r['error_count']==0)
				{
					/*
						we want to be able to do:
						  date_format(date_create($row['date']),'Y');
						and since
						  date_format(date_create('1996-00-00'),'Y')
						outputs "1995" (1995-11-30, even!), we default empty months and
						days to 01 rather than 00 to avoid unpleasentness. the distinction
						between '1996-01-01' equalling 'january 1st 1996' or '1996' (or
						'january 1996') is made based upon te chosen date format of the
						trait (Y, Y-m-d or Y-m).

						column is `date` so the time parts are somewhat pointless
					*/
					$d=$base+
						array(
							'date'=>
								(!empty($r['year']) ? $r['year'] : '0000')."-".
								(!empty($r['month']) ? sprintf('%02s',$r['month']) : '01')."-".
								(!empty($r['day']) ? sprintf('%02s',$r['day']) : '01')." ".
								(!empty($r['hour']) ? sprintf('%02s',$r['hour']) : '00').":".
								(!empty($r['minute']) ? sprintf('%02s',$r['minute']) : '00').":".
								(!empty($r['second']) ? sprintf('%02s',$r['second']) : '00'),
							'show_order'=>$key
						);

					$r=$this->models->TraitsValues->save($d);
				}
				else
				{
					$r=false;
				}
				
				
				if (!$r)
				{
					$this->addError(sprintf($this->translate('Value %s not saved'),$val));
				}
				else
				{
					$saved++;
				}

			}
			
			$this->addMessage(sprintf($this->translate('%s values saved'),$saved));

		}


	}

	private function verifyDate($date,$format)
	{
		$r=date_parse_from_format($format,$date);

		if ($r['error_count']==0)
		{
			return true;
		}
		else
		{
			return implode("\n",$r['errors']);
		}
	}

	private function formatDbDate($date,$format)
	{
		return date_format(date_create($date),$format);
	}



}



