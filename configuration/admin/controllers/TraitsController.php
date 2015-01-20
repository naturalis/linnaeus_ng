<?php

/*

	dude, are you COMPLETELY deleting trait groups!?

*/



include_once ('Controller.php');

class TraitsController extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations'
    );
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array(
    );
    
    public $cssToLoad = array(
		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('taxon_groups.js','jquery.mjs.nestedSortable.js')
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

    public function traitgroupAction ()
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

		if ($this->rHasVal('action','saveorder'))
		{
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$this->getTraitgroup($this->rGetVal('group')));
		$this->printPage();
    }

    public function traitgroupTraitAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait group traits'));

		if ($this->rHasVal('action','save'))
		{
			$this->saveTraitgroupTrait($this->requestData);
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$this->getTraitgroup($this->rGetVal('group')));
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
	
	private function getTraitgroups($p=null)
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
		$groups=isset($p['groups']) ? $p['groups'] : null;

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

		/*
		$this->models->TaxongroupsTaxa->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));
		*/
					
		$this->models->TraitsGroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>mysql_real_escape_string($id),
		));

		$this->models->TraitsGroups->update(
			array('parent_id'=>'null'),
			array('project_id'=>$this->getCurrentProjectId(),'parent_id'=>mysql_real_escape_string($id))
		);

		return true;
	}

	private function getTraitgroupTraits($group)
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
				_g.sysname as type_sysname
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
				and _a.trait_group_id=".$group."
			order by _a.show_order
		");

		return $r;
	}
	
	private function saveTraitgroupTrait($p)
	{
		$trait_group_id=isset($p['trait_group_id']) ? $p['trait_group_id'] : null;
		$project_type_id=isset($p['project_type_id']) ? $p['project_type_id'] : null;
		$date_format_id=isset($p['date_format_id']) ? $p['date_format_id'] : null;
		$sysname=isset($p['sysname']) ? $p['sysname'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$code=isset($p['code']) ? $p['code'] : null;
		$description=isset($p['description']) ? $p['description'] : null;
		$unit=isset($p['unit']) ? $p['unit'] : null;
		$can_select_multiple=isset($p['can_select_multiple']) ? $p['can_select_multiple'] : null;
		$can_include_comment=isset($p['can_include_comment']) ? $p['can_include_comment'] : null;
		$show_index_numbers=isset($p['show_index_numbers']) ? $p['show_index_numbers'] : null;
		
		if (empty($trait_group_id)||empty($project_type_id)||empty($sysname)||empty($name))
		{
			if (empty($trait_group_id)) $this->addError($this->translate('Missing group ID.'));
			if (empty($project_type_id)) $this->addError($this->translate('Missing type ID.'));
			if (empty($sysname)) $this->addError($this->translate('Missing system name.'));
			if (empty($name)) $this->addError($this->translate('Missing name.'));
			$this->addError($this->translate('Trait not saved'));
			return;
		}

		$d=$this->models->TraitsTraits->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'trait_group_id' => $trait_group_id,
				'project_type_id' => $project_type_id,
				'date_format_id' => $date_format_id,
				'sysname' => trim($sysname),
				'unit' => $unit,
				'can_select_multiple' => ($can_select_multiple=='y'),
				'can_include_comment' => ($can_include_comment=='y'),
				'can_be_null' => ($can_include_comment=='y'),
				'show_index_numbers' => ($show_index_numbers=='y')
			));

		if ($d)
		{
			$id=$this->models->TraitsTraits->getNewId();
			
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

			$this->addMessage($this->translate('Trait saved.'));
		}
		else 
		{
			$this->addError($this->translate('Saving trait failed'));
		}
			
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





}

