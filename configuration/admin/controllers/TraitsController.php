<?php

/*

	dude, are you COMPLETELY deleting trait groups!?
	NO! need to delete traits as well! + texts
	
*/	

include_once ('Controller.php');

class TraitsController extends Controller
{

	private $_lookupListMaxResults=99999;
	
	public $_defaultMaxLengthStringValue=4000;
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

		$this->smarty->assign('languages',$this->getProjectLanguages());
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

		$this->smarty->assign('languages',$this->getProjectLanguages());
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
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.verification_function_name as type_verification_function_name
			from
				%PRE%traits_traits _a

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

			$r['language_labels']=
				array(
					'name'=>$this->getTextTranslations(array('text_id'=>$r['name_tid'])),
					'code'=>$this->getTextTranslations(array('text_id'=>$r['code_tid'])),
					'description'=>$this->getTextTranslations(array('text_id'=>$r['description_tid']))
				);
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
				_a.string_label_tid,
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


			$r[$key]['language_labels']= $this->getTextTranslations(array('text_id'=>$val['string_label_tid']));
		
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
				on _a.name_tid=_d.text_id
				and _d.language_id=". $this->getDefaultProjectLanguage() ."
				and _d.project_id is null

			left join 
				%PRE%text_translations _e
				on _a.description_tid=_e.text_id
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
				and _a.name_tid=_b.text_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.text_id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");
		
		$r=$d[0];


		$r['names']= $this->getTextTranslations(array('text_id'=>$r['name_tid']));
		$r['descriptions']= $this->getTextTranslations(array('text_id'=>$r['description_tid']));
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
		

		$textids=$this->storeTranslations(
			array(
				'record'=>$this->getTraitgroup($id),
				'data'=>array(
					'name_tid'=>$names,
					'description_tid'=>$descriptions
				)
			)
		);
		
		foreach((array)$textids as $col=>$text_id)
		{
			$this->models->TraitsGroups->update(array($col=>$text_id),array('id'=>$id));
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

			if (empty($id)) $id=$this->models->TraitsTraits->getNewId();

			$trait=$this->models->TraitsTraits->_get(array('id'=>array(
				'id'=>$id,
				'project_id'=>$this->getCurrentProjectId(),
			)));
			
			$textids=$this->storeTranslations(
				array(
					'record'=>$trait[0],
					'data'=>array(
						'name_tid'=>$name,
						'code_tid'=>$code,
						'description_tid'=>$description
					)
				)
			);

			foreach((array)$textids as $col=>$text_id)
			{
				$this->models->TraitsTraits->update(array($col=>$text_id),array('id'=>$id));
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
		$valuelabels=isset($p['valuelabels']) ? $p['valuelabels'] : null;

		if (empty($trait))
		{
			if (empty($trait)) $this->addError($this->translate('Missing trait ID.'));
			if (empty($values)) $this->addError($this->translate('No values to save.'));
			$this->addError($this->translate('Values not saved'));
			return;
		}
		
		$trait=$this->getTraitgroupTrait($trait);

		$base=
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_id'=>$trait['id']
			);
		

		// get the current values
		$current=$this->models->TraitsValues->_get(array('id'=>$base));
		
		if ($trait['type_sysname']=='stringlist' || $trait['type_sysname']=='stringlistfree')
		{

			$index=array();
			$showorder=0;
			foreach((array)$values as $key=>$val)
			{
				$base['show_order']=$showorder++;
				$base['string_value']=trim($val);
				
				if ($key>=0)
				// update
				{
					$this->models->TraitsValues->update($base,array('id'=>$key,'project_id'=>$this->getCurrentProjectId()));
					$value=$this->models->TraitsValues->_get(array('id'=>$key));
					
					if (isset($valuelabels[$key]))
					{
						$textids=$this->storeTranslations(
							array(
								'record'=>$value,
								'data'=>array('string_label_tid'=>$valuelabels[$key])
							)
						);
				
						foreach((array)$textids as $col=>$text_id)
						{
							$this->models->TraitsValues->update(array($col=>$text_id),array('id'=>$key));
						}
					}

				}
				else
				// insert
				{
					$this->models->TraitsValues->save($base);
					$id=$this->models->TraitsValues->getNewId();
					
					if (isset($valuelabels[$id]))
					{
						$textids=$this->storeTranslations(
							array(
								'record'=>array('string_label_tid'=>null), // all new values, none have a text_id yet
								'data'=>array('string_label_tid'=>$valuelabels[$id],)
							)
						);
				
						foreach((array)$textids as $col=>$text_id)
						{
							$this->models->TraitsValues->update(array($col=>$text_id),array('id'=>$id));
						}
					}
				}
			}

			// delete previous values that are no longer part of the new set
			foreach((array)$current as $key=>$val)
			{
				if (!array_key_exists($val['id'],(array)$values))
				{
					$this->deleteTranslations(array('text_id'=>$val['string_label_tid']));
					$this->models->TraitsValues->delete(array('id'=>$val['id'],'project_id'=>$this->getCurrentProjectId()));
				}
			}

			$this->addMessage(sprintf($this->translate('%s values saved'),$showorder));
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
							'date'=>$this->makeInsertableDate($val,$trait['date_format_format']),
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
		return is_null($date) ? null : date_format(date_create($date),$format);
	}
	
	public function makeInsertableDate($date,$format)
	{
		$r=date_parse_from_format($format,$date);
		
		if ($r['error_count']==0)
		{
			return
				(!empty($r['year']) ? $r['year'] : '0000')."-".
				(!empty($r['month']) ? sprintf('%02s',$r['month']) : '01')."-".
				(!empty($r['day']) ? sprintf('%02s',$r['day']) : '01')." ".
				(!empty($r['hour']) ? sprintf('%02s',$r['hour']) : '00').":".
				(!empty($r['minute']) ? sprintf('%02s',$r['minute']) : '00').":".
				(!empty($r['second']) ? sprintf('%02s',$r['second']) : '00')
			;
		}
	}



	private function getNextTextId()
	{
		$d=$this->models->TextTranslations->freeQuery("
			select ifnull(max(text_id)+1,1) as next from %TABLE%  where project_id = ".$this->getCurrentProjectId()
		);
		return $d[0]['next'];
	}

	private function storeTranslations($p)
	{
		$record=isset($p['record']) ? $p['record'] : null;
		$data=isset($p['data']) ? $p['data'] : null;

		/*		
		array(
			'record'=>$this->getTraitgroup(), // any record with column x_tid ref. text_id
			'data'=>array(
				'x_tid'=>$xs, // array(language_id=>translation,language_id=>translation)
				...=>...
			)
		*/

		if (empty($record)) return;

		$index=array();

		foreach((array)$data as $column=>$translations)
		{
			foreach((array)$translations as $language=>$translation)
			{

				if (!array_key_exists($column,$record)) continue;

				$newTextId=false;
				
				// make sure this record has a valid text_id (regardless of language)
				$text_id=$record[$column];

				if (empty($text_id) && isset($index[$column]))
				{
					$text_id=$index[$column];
				}
				
				if (empty($text_id))
				{
					$text_id=$this->getNextTextId();
					$newTextId=true;
				}
				else
				{
					$d=$this->models->TextTranslations->_get(array('id'=>array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id)));
					if (empty($d))
					{
						$newTextId=true;
					}
				}

				if ($newTextId)
				{
					$this->models->TextTranslations->save(array(
						'project_id'=>$this->getCurrentProjectId(),
						'language_id'=>$language,
						'text_id'=>$text_id,
						'translation'=>$translation
					));
				}
				
				$index[$column]=$text_id;
				
				if ($newTextId)
				{
					// we already inserted this first translation, to get a new text_id
					continue;
				}

				$base=array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id,'language_id'=>$language);

				// see if a translation exist for this combination of text_id and language
				$d=$this->models->TextTranslations->_get(array('id'=>$base));

				$base+=array('translation'=>(empty($translation) ? 'null' : $translation));

				// update translation if the combination text_id+language already exists
				if (!empty($d))
				{
					$this->models->TextTranslations->update($base,array('id'=>$d[0]['id']));
				}
				else
				// insert if it doesn't
				{
					$this->models->TextTranslations->save($base);
				}
				
				//echo $text_id,$column,$language,'<br />';
			}
		}

		return $index;
	}

	private function deleteTranslations($p)
	{
		$text_id=isset($p['text_id']) ? $p['text_id'] : null;
		$language=isset($p['language']) ? $p['language'] : null;

		if (empty($text_id)) return;
		
		$base=
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'text_id'=>$text_id
			);

		if (!empty($language)) $base+=array('language_id'=>$language);

		$d=$this->models->TextTranslations->delete($base);
	}

	private function getTextTranslations($p)
	{
		$text_id=isset($p['text_id']) ? $p['text_id'] : null;
		$language_id=isset($p['language']) ? $p['language'] : null;
		
		if (empty($text_id)) return;
		
		$base=array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id);
		if (!empty($language_id)) $base+=array('language_id'=>$language_id);

		$d=$this->models->TextTranslations->_get(array('id'=>$base));

		$r=array();
		foreach((array)$d as $key=>$val)
		{
			$r[$val['language_id']]=$val['translation'];
		}
		return $r;
	}




















}



