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
			
		//$this->saveTaxonParentage($id);

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

	public function treeGetTop()
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

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'
					
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and ifnull(_trash.is_deleted,0)=0
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
	
	
	private function storeParentage($p)
	{
		if (empty($p['id'])||empty($p['parentage']))
			return;

		$this->models->TaxonQuickParentage->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'taxon_id' => $p['id'],
			'parentage' => implode(' ',$p['parentage'])
		));
	}
	
	
	public function getProgeny($parent,$level,$family)
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

		$family[]=$parent;

		foreach((array)$result as $row)
		{
			$this->storeParentage(array('id'=>$row['id'],'parentage'=>$family));
			$this->getProgeny($row['id'],$level+1,$family);
		}
	}

	public function saveTaxonParentage($id=null)
	{
		set_time_limit(600);

		if (!$this->models->TaxonQuickParentage->getTableExists())
		{
			$this->addError('table TaxonQuickParentage does not exist');
			return;
		}

		if (empty($id))
		{
			$t = $this->treeGetTop();

			if (empty($t))
				die('no top!?');
			/*
			if (count((array)$t)>1)
				die('multiple tops!?');
			*/

			//$this->models->TaxonQuickParentage->delete(array('project_id' => $this->getCurrentProjectId())); // ??? crashes

			$this->models->TaxonQuickParentage->freeQuery("delete from %PRE%taxon_quick_parentage where  project_id = ".$this->getCurrentProjectId());
		
			$this->tmp=0;
			$this->getProgeny($t,0,array());
			$i=$this->tmp;
		}
		else
		{
			$this->tmp=array();
			$t=$this->getTaxonById($id);
			$this->getParents($t['parent_id'],0,array());
			//$this->models->TaxonQuickParentage->delete(array('project_id' => $this->getCurrentProjectId(),'taxon_id'=>$id));
			$this->models->TaxonQuickParentage->freeQuery("
				delete from %PRE%taxon_quick_parentage where  project_id = ".$this->getCurrentProjectId()." and taxon_id = ".$id
			);


			$qp=array_pop($this->tmp);
			$this->storeParentage(array('id'=>$id,'parentage'=>array_reverse($qp['parentage'])));
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
		if (!isset($p['changed']))
		{
			$before=serialize((isset($p['before']) ? $p['before'] : null));
			$after=serialize((isset($p['after']) ? $p['after'] : null));
			$p['changed']=md5($before)!=md5($after);
		}

		$this->logChange($p);
	}

	private function getNsrId($p)
	{
		$data=$this->models->NsrIds->_get(array(
			'id'=>array(
				'lng_id' => $p['id'],
				'item_type' => $p['item_type']
			),
			'columns'=>'nsr_id'
		));

		return str_replace('tn.nlsr.concept/','',$data[0]['nsr_id']);
	}
		
	public function getConcept($id)
	{
		$c=$this->getTaxonById($id);
		
		if ( !empty($c) )
		{
			$c['nsr_id']=$this->getNsrId(array('id'=>$c['id'],'item_type'=>'taxon'));
			$c['parent']=$this->getTaxonById($c['parent_id']);
	
			$d=$this->models->TrashCan->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$id,
				'item_type'=>'taxon'
			)));
	
			$c['is_deleted']=($d[0]['is_deleted']==1);
		}

		return $c;
	}		

}