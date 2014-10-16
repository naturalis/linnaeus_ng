<?php

/*
	TAB_VERSPREIDING::$this->getPresenceData($taxon)
	TAB_BEDREIGING_EN_BESCHERMING::EZ
	CTAB_LITERATURE			
	CTAB_MEDIA
	CTAB_DNA_BARCODES
*/

include_once ('Controller.php');
include_once ('RdfController.php');

class NsrController extends Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->Rdf = new RdfController;
	}

	public function getActors()
	{
		return $this->models->Actors->freeQuery(
			"select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id 
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$this->getCurrentProjectId()."

			order by
				_e.is_company, _e.name
		");	
	}

	public function getTaxonParentage($id)
	{
		if (is_null($id))
			return;
			
		$this->saveTaxonParentage($id);

		$d=$this->models->TaxonQuickParentage->freeQuery("
			select
				parentage
			from
				%PRE%taxon_quick_parentage
			where 
				project_id = ".$this->getCurrentProjectId()." 
				and taxon_id = ".$id
		);

		return explode(' ',$d[0]['parentage']);

	}

	public function saveTaxonParentage($id=null)
	{

		if (!$this->models->TaxonQuickParentage->getTableExists())
			return;
			
		if (empty($id))
		{

			$t = $this->treeGetTop();
	
			if (empty($t))
				die('no top!?');
			/*
			if (count((array)$t)>1)
				die('multiple tops!?');
			*/
	
			$this->tmp=array();
	
			$this->getProgeny($t,0,array());

			$d=array('project_id' => $this->getCurrentProjectId());
	
			$this->models->TaxonQuickParentage->delete($d);
	
			$i=0;
			foreach((array)$this->tmp as $key=>$val)
			{
	
				if (!is_null($id) && $val['id']!=$id)
					continue;
	
				$this->models->TaxonQuickParentage->save(
				array(
					'id' => null,
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id,
					'parentage' => implode(' ',$val['parentage'])
	
				));
	
				$i++;
			}
					
		}
		else
		{
			$this->tmp=array();
			$t=$this->getTaxonById($id);
			$this->getParents($t['parent_id'],0,array());
			$this->models->TaxonQuickParentage->delete(array('project_id' => $this->getCurrentProjectId(),'taxon_id'=>$id));
			$qp=array_pop($this->tmp);
			$this->models->TaxonQuickParentage->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id,
				'parentage' => implode(' ',array_reverse($qp['parentage']))

			));
			$i=1;
		}

		return $i;

	}

	private function getParents($parent,$level,$family)
	{
		$result = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $parent
				),
				'columns' => 'id,parent_id,taxon'
			)
		);

		$family[]=$parent;

		foreach((array)$result as $row)
		{
			$row['parentage']=$family;
			$this->tmp[]=$row;
			$this->getParents($row['parent_id'],$level+1,$family);
		}
	}

	public function resetTree()
	{
		unset($_SESSION['admin']['user']['species']['tree']);
	}

	public function logNsrChange($p)
	{
		$p['changed']=isset($p['changed']) ? $p['changed']: true;
		$this->logChange($p);
	}

		

}