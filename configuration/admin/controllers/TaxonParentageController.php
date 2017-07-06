<?php

class TaxonParentageController extends Controller
{

    public $usedModels = array(
		'taxon_quick_parentage'
    );

    public $controllerPublicName = 'Quick Taxon Parentage';
    public $modelNameOverride = 'TaxonParentageModel';

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		if (!$this->models->TaxonQuickParentage->getTableExists())
		{
			$this->addError('table TaxonQuickParentage does not exist');
			return;
		}
		
		set_time_limit(600);
	}

    public function getParentageTableRowCount()
    {
		$d=$this->models->TaxonQuickParentage->_get( [
			'id'=> [ 'project_id' => $this->getCurrentProjectId() ],
			'columns' => 'count(*) as total'
		] );

		return $d[0]['total'];
    }

	public function generateParentageAll()
	{
		$t = $this->treeGetTop();

		if (empty($t))
		{
			$this->addError('Didn\'t find a taxon tree top.');
			return;
		}
			
		$this->tmp=array();

		$this->getProgeny($t,0,array());

		$d=array('project_id' => $this->getCurrentProjectId());

		$this->models->TaxonQuickParentage->delete($d);

		$i=0;
		foreach((array)$this->tmp as $key=>$val)
		{
			$this->models->TaxonQuickParentage->save( [
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $val['id'],
				'parentage' => implode(' ',$val['parentage'])
			] );

			$i++;
		}

		return $i;

	}

	public function generateParentage( $id )
	{
		$this->tmp=[];

		$this->getProgeny($id,0,[]);

		$d=[ 'project_id' => $this->getCurrentProjectId(), 'taxon_id'=>$id ];

		$this->models->TaxonQuickParentage->delete( $d );

		$i=0;
		foreach((array)$this->tmp as $key=>$val)
		{
			$this->models->TaxonQuickParentage->save( [
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $val['id'],
				'parentage' => implode(' ',$val['parentage'])
			] );

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

	    $p = $this->models->TaxonParentageModel->getTreeTop( [ 'project_id'=>$this->getCurrentProjectId() ] );

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
		$result = $this->models->Taxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'parent_id' => $parent
				),
				'columns' => 'id,parent_id,taxon,'.$level.' as level'
			)
		);

		$family[]=$this->generateTaxonParentageId($parent);

		foreach((array)$result as $row)
		{
			$row['parentage']=$family;
			$this->tmp[]=$row;
			$this->getProgeny($row['id'],$level+1,$family);
		}
	}

}
