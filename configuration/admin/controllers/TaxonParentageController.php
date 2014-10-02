<?php

class TaxonParentageController extends Controller
{

    public $usedModels = array(
		'taxon_quick_parentage'
    );

    public $controllerPublicName = 'Quick Taxon Parentage';

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }
	
	public function padId($id)
	{
		return sprintf('%05s',$id);
	}
	
	private function initialize()
	{
		if (!$this->models->TaxonQuickParentage->getTableExists())
		{
			// raise error
			return;
		}
	}

    public function getParentageTableRowCount()
    {
		$d=$this->models->TaxonQuickParentage->_get(array(
			'id'=>
				array(
					'project_id' => $this->getCurrentProjectId()
			
				),
			'columns' => 'count(*) as total'
			));
			
		return $d[0]['total'];
				
    }

	public function generateParentageAll()
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
			$this->models->TaxonQuickParentage->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $val['id'],
				'parentage' => implode(' ',$val['parentage'])

			));

			$i++;
		}

		return $i;

	}

	private function treeGetTop()
	{
		/*
			get the top taxon = no parent
			"_r.id < 10" added as there might be orphans, which are ususally low-level ranks 
		*/
		$p=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a
					
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.parent_id is null
				and _r.id < 10

		");

		if ($p && count((array)$p)==1)
		{
			$p=$p[0]['id'];
		} 
		else
		{
			$p=null;
		}

		if (count((array)$p)>1)
		{
			$this->addError('Detected multiple high-order taxa without a parent. Unable to determine which is the top of the tree.');
		}

		return $p;
	}
	
	private function getProgeny($parent,$level,$family)
	{
		$result = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'parent_id' => $parent
				),
				'columns' => 'id,parent_id,taxon,'.$level.' as level'
			)
		);

		$family[]=$this->padId($parent);

		foreach((array)$result as $row)
		{
			$row['parentage']=$family;
			$this->tmp[]=$row;
			$this->getProgeny($row['id'],$level+1,$family);
		}
	}



}
