<?php

include_once ('Controller.php');
class TaxongroupController extends Controller
{

    public $usedModels = array(
		'taxongroups',
		'taxongroups_labels',
		'taxongroups_taxa'
    );
    public $jsToLoad = array(
        'all' => array('taxon_groups.js','jquery.mjs.nestedSortable.js')
    );	

    public $cssToLoad = array(
		'taxon_groups.css'
	);
    public $usedHelpers = array(
        'session_messages'
    );
   
    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
		$this->cleanUp();
	}
	


    public function taxongroupsAction ()
    {
		$this->checkAuthorisation();
		$this->addMessage($this->helpers->SessionMessages->getMessages());
		
		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveGroupOrder($this->requestData);
			if ($i>0) $this->addMessage('New order saved.');
		}
		
		$this->smarty->assign('groups',$this->getGroups());
		$this->printPage();
    }
	
    public function taxongroupAction ()
    {
		$this->checkAuthorisation();
		
		if ($this->rHasVal('action','save'))
		{
			$this->saveGroup($this->requestData);
			$this->helpers->SessionMessages->setMessage('Saved.');
			$this->redirect('taxongroups.php');
		}
		else 
		if ($this->rHasVal('action','delete'))
		{
			$this->deleteGroup($this->requestData);
			$this->helpers->SessionMessages->setMessage('Group deleted.');
			$this->redirect('taxongroups.php');
		}
		else 
		if (!$this->rHasId())
		{
			$this->smarty->assign('newgroup',true);
		} else
		if ($this->rHasId())
		{
			$this->smarty->assign('group',$this->getGroup($this->rGetId()));
			$this->smarty->assign('newgroup',false);
		}
		else
		{
			$this->smarty->assign('newgroup',true);
		}

		$this->smarty->assign('groups',$this->getGroups());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }
	
    public function taxongroupTaxaAction ()
    {
		$this->checkAuthorisation();
		if (!$this->rHasId()) $this->redirect('taxongroups.php');
		
		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->saveTaxongroupTaxa($this->requestData);
			$this->addMessage('Saved');
		}

		$this->smarty->assign('group',$this->getGroup($this->rGetId()));
		$this->smarty->assign('taxongroupTaxa',$this->getTaxongroupTaxa($this->rGetId()));
		$this->smarty->assign('taxa',$this->getTaxa());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }



	private function getGroups($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;
		
		$g=$this->models->Taxongroups->freeQuery("
			select
				_a.*,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a
				
			left join 
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."
				order by _a.show_order
		");
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['taxa']=$this->getTaxongroupTaxa($val['id']);	
			$g[$key]['children']=$this->getGroups(array('parent'=>$val['id']));
		}
		
		return $g;
	}

	private function getGroup($id)
	{
		$d=$this->models->Taxongroups->freeQuery("
			select
				_a.id,
				_a.parent_id,
				_a.sys_label,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a
				
			left join 
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");

		$r=$d[0];

		$d=$this->models->TaxongroupsLabels->_get(array("id"=>array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>$id
		)));

		foreach((array)$d as $val)
		{
			$r['names'][$val['language_id']]=$val['name'];
			$r['descriptions'][$val['language_id']]=$val['description'];
		}

		return $r;
	}
	
	private function saveGroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$parent_id=!empty($p['parent_id']) ? $p['parent_id'] : 'null';
		$sys_label=isset($p['sys_label']) ? $p['sys_label'] : null;
		$names=isset($p['names']) ? $p['names'] : null;
		$descriptions=isset($p['descriptions']) ? $p['descriptions'] : null;

		if (empty($sys_label))
			return false;

		$this->models->Taxongroups->save(array(
			'id'=>$id,
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=>mysql_real_escape_string($parent_id),
			'sys_label'=>mysql_real_escape_string($sys_label)
		));
		
		if (empty($id)) $id=$this->models->Taxongroups->getNewId();
		
		if (!$id) return;

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>$id,
		));
					
		foreach((array)$names as $language_id=>$name)
		{
			$this->models->TaxongroupsLabels->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxongroup_id'=>$id,
				'language_id'=>mysql_real_escape_string($language_id),
				'name'=>mysql_real_escape_string($name),
				'description'=>(isset($descriptions[$language_id]) ? mysql_real_escape_string($descriptions[$language_id]) : null),
			));
		}
		
		return true;
	}

	private function saveGroupOrder($p)
	{
		$groups=isset($p['groups']) ? $p['groups'] : null;

		if (empty($groups))
			return false;
			
		$i=0;
		foreach((array)$groups as $key=>$group)
		{
			$this->models->Taxongroups->save(array(
				'id'=>$group,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->Taxongroups->getAffectedRows();
		}
		return $i;
	}

	private function deleteGroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;

		$this->models->TaxongroupsTaxa->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));

		$this->models->TaxongroupsLabels->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($id),
		));
					
		$this->models->Taxongroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>mysql_real_escape_string($id),
		));

		return true;
	}



	private function getTaxa($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;

		$g=$this->models->Taxon->freeQuery("
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank
			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.project_id = _f.project_id
				and _t.rank_id=_f.id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			where
				_t.project_id=". $this->getCurrentProjectId()."
				and _t.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."

			order by
					_t.taxon
		");

		foreach((array)$g as $key=>$val)
		{
			$g[$key]['group_memberships']=$this->getTaxongroupMemberships($val['id']);
			$g[$key]['children']=$this->getTaxa(array('parent'=>$val['id']));
		}
				
		return $g;
	}

	private function saveTaxongroupTaxa($p)
	{
		$group_id=isset($p['group_id']) ? $p['group_id'] : null;
		$taxa=isset($p['taxa']) ? $p['taxa'] : null;

		if (empty($group_id))
			return false;

		$this->models->TaxongroupsTaxa->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxongroup_id'=>mysql_real_escape_string($group_id),
		));

		foreach((array)$taxa as $key=>$taxon)
		{
			$this->models->TaxongroupsTaxa->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxongroup_id'=>mysql_real_escape_string($group_id),
				'taxon_id'=>mysql_real_escape_string($taxon),
				'show_order'=>$key
			));
		}

	}

	private function getTaxongroupTaxa($id)
	{
		if (empty($id))
			return;

		$d=$this->models->TaxongroupsTaxa->freeQuery("
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank,			
				_a.show_order
			from
				%table% _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id
				
			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.taxongroup_id = ".mysql_real_escape_string($id)."
			order by 
				_a.show_order
		");

		return $d;

	}
	
	private function getTaxongroupMemberships($id)
	{
		if (empty($id))
			return;

		$d=$this->models->TaxongroupsTaxa->freeQuery(array(
			"query"=>"
				select
					_a.id,_a.taxongroup_id
				from
					%table% _a
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id = ".$id,
			"fieldAsIndex"=> "taxongroup_id"
		));

		return $d;
	}


	private function cleanUp()
	{
		$d=$this->models->TaxongroupsTaxa->freeQuery("
			select
				_a.id
			from
				%table% _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _t.id is null
		");

		foreach((array)$d as $val)
		{
			$this->models->TaxongroupsTaxa->delete(
				array(
					"project_id"=>$this->getCurrentProjectId(),
					"id"=>$val['id']
				)
			);
		}
			
	}


}