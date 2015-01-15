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

    public function traitgroupsAction()
    {
		$this->checkAuthorisation();
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
			$this->models->TextTranslations->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'language_id'=>mysql_real_escape_string($language_id),
				'translation'=>mysql_real_escape_string($name),
			));
			$tid=$this->models->TextTranslations->getNewId();
			if ($tid)
			{
				$this->models->TraitsGroups->update(array('name_tid'=>$tid),array('id'=>$id));			
			}

			if (isset($descriptions[$language_id]))
			{
				$this->models->TextTranslations->save(array(
					'project_id'=>$this->getCurrentProjectId(),
					'language_id'=>mysql_real_escape_string($language_id),
					'translation'=>mysql_real_escape_string($descriptions[$language_id])
				));
				$tid=$this->models->TextTranslations->getNewId();
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

















}

