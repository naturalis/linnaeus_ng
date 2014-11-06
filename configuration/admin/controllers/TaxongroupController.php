<?php

include_once ('Controller.php');
class TaxongroupController extends Controller
{

    public $usedModels = array(
		'taxongroups',
		'taxongroups_labels',
		'taxongroups_taxa'
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
	}
	

    public function taxongroupsAction ()
    {
		$this->checkAuthorisation();
		$this->smarty->assign('groups',$this->getGroups());
		$this->printPage();
    }
	
    public function taxongroupAction ()
    {
		$this->checkAuthorisation();
		
		if (!$this->rHasId() && $this->rHasVal('action','save'))  //!$this->isFormResubmit()
		{
			if ($this->saveGroup($this->requestData))
			{
				$this->redirect('taxongroups.php');
			}
			else
			{
				$this->smarty->assign('group',$this->requestData);
				$this->smarty->assign('newgroup',true);
			}
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
		
		if ($this->rHasVal('action','save')) // && !$this->isFormResubmit())
		{
			$this->saveTaxongroupTaxa($this->requestData);
		}
		
		$this->smarty->assign('group',$this->getGroup($this->rGetId()));
		$this->smarty->assign('taxongroupTaxa',$this->getTaxongroupTaxa($this->rGetId()));
		$this->smarty->assign('taxa',$this->getTaxa());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }
	



	private function getGroups($parent=null)
	{
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
		");
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['children']=$this->getGroups($val['id']);
		}
		
		return $g;
	}

	private function getGroup($id)
	{
		$d=$this->models->Taxongroups->freeQuery("
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
				and _a.id=".$id."
		");
		return $d[0];
	}
	
	private function saveGroup($p)
	{
		$parent_id=isset($p['parent_id']) ? $p['parent_id'] : null;
		$sys_label=isset($p['sys_label']) ? $p['sys_label'] : null;
		$names=isset($p['names']) ? $p['names'] : null;
		$descriptions=isset($p['descriptions']) ? $p['descriptions'] : null;

		if (empty($sys_label))
			return false;

		$this->models->Taxongroups->save(array(
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=>mysql_real_escape_string($parent_id),
			'sys_label'=>mysql_real_escape_string($sys_label)
		));
		
		$id=$this->models->Taxongroups->getNewId();
		
		if (!$id) return;
		
		foreach((array)$names as $language_id=>$name)
		{
			$this->models->TaxongroupsLabels->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxongroup_id'=>$id,
				'language_id'=>mysql_real_escape_string($language_id),
				'name'=>mysql_real_escape_string($name),
				'description'=>(isset($descriptions[$language_id]) ? mysql_real_escape_string($name) : null),
			));
		}
		
		return true;
	}

	private function getTaxa($parent=null)
	{
		$g=$this->models->Taxon->freeQuery("
			select
				_t.*,
				_q.rank
			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id

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
			$g[$key]['children']=$this->getTaxa($val['id']);
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

		return $this->models->TaxongroupsTaxa->_get(array(
			'id'=>
				array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxongroup_id'=>mysql_real_escape_string($id),
				),
			'order'=>'show_order'
		));

	}
	













}