<?php /** @noinspection PhpMissingParentCallMagicInspection */

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
		$id = $this->treeGetTop();

		if (empty($id))
		{
			$this->addError("Didn't find a taxon tree top.");
			return;
		}

		$this->tmp=0;
		// circumventing unexplained errors (mysqli spontaneously getting lost)
		//$this->models->TaxonQuickParentage->delete(['project_id' => $this->getCurrentProjectId()]);
		mysqli_query(
			$this->models->TaxonQuickParentage->databaseConnection,
			"delete from ".$this->models->TaxonQuickParentage->tableName." where project_id = " . $this->getCurrentProjectId()
		);

		$this->getProgeny($id,0,array());
		return $this->tmp;
	}

	public function generateParentage( $id )
	{
		$this->tmp=0;
		
		// circumventing unexplained errors (mysqli spontaneously getting lost)
		//$this->models->TaxonQuickParentage->delete( [ 'project_id' => $this->getCurrentProjectId(), 'taxon_id'=>$id ] );

		mysqli_query(
			$this->models->TaxonQuickParentage->databaseConnection,
			"delete from " . $this->models->TaxonQuickParentage->tableName. "
				where project_id = " . $this->getCurrentProjectId() . 
 		      " and MATCH(parentage) AGAINST ('" . self::generateTaxonParentageId($id) . "' in boolean mode)"
		);
		
		$this->getProgeny($id,0,array());
		return $this->tmp;
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
		$family[]=Controller::generateTaxonParentageId($parent);
		
		$result = $this->models->Taxa->_get( [
			'id' => [
				'project_id' => $this->getCurrentProjectId(),
				'parent_id' => $parent
			],
			'columns' => 'id,parent_id,taxon,'.$level.' as level'
		] );
		
		foreach((array)$result as $row)
		{
			$row['parentage']=$family;

			$this->models->TaxonQuickParentage->save( [
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $row['id'],
				'parentage' => implode(' ',$row['parentage'])
			] );
			
			$this->tmp++;

			$this->getProgeny($row['id'],$level+1,$family);
		}
	}

}
