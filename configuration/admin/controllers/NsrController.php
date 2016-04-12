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

//    public $modelNameOverride='NsrTreeModel';

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
		return $this->models->ControllerModel->getActors($this->getCurrentProjectId());
	}

	public function getTaxonParentage($id)
	{
		if (is_null($id))
			return;

		return $this->models->ControllerModel->getTaxonParentage(array(
            'projectId' => $this->getCurrentProjectId(),
    		'taxonId' => $id
		));

	}

	public function treeGetTop()
	{
		/*
			get the top taxon = no parent
			"_r.id < 10" added as there might be orphans, which are ususally low-level ranks
		*/
		$p = $this->models->ControllerModel->treeGetTop($this->getCurrentProjectId());

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
		$result = $this->models->Taxa->_get(
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

			$this->models->ControllerModel->deleteTaxonParentage(array(
                'projectId' => $this->getCurrentProjectId()
            ));

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
			$this->models->ControllerModel->deleteTaxonParentage(array(
                'projectId' => $this->getCurrentProjectId(),
			    'taxonId' => $id
            ));

			$qp=array_pop($this->tmp);
			$this->storeParentage(array('id'=>$id,'parentage'=>array_reverse($qp['parentage'])));
			$i=1;
		}

		return $i;

	}

	private function getParents($parent,$level,$family)
	{
		$result = $this->models->Taxa->_get(
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
		
		$c['taxon']=$this->addHybridMarker( array( 'name'=>$c['taxon'],'base_rank_id'=>$c['base_rank'] ) );
		$c['label']=$this->addHybridMarker( array( 'name'=>$c['label'],'base_rank_id'=>$c['base_rank'] ) );
		$c['parent']['taxon']=$this->addHybridMarker( array( 'name'=>$c['parent']['taxon'],'base_rank_id'=>$c['parent']['base_rank'] ) );
		$c['parent']['label']=$this->addHybridMarker( array( 'name'=>$c['parent']['label'],'base_rank_id'=>$c['parent']['base_rank'] ) );

		return $c;
	}



	public function createNsrIds($p)
	{

		$id=isset($p['id']) ? $p['id'] : null;
		$type=isset($p['type']) ? $p['type'] : null;
		$subtype=isset($p['subtype']) ? $p['subtype'] : null;

		if (empty($id) || empty($type))	return;

		$d=$this->models->NsrIds->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$id,
				'item_type'=>$type
			)));

		$rdf=$nsr=null;

		if (empty($d[0]['rdf_id'])) $rdf=$this->createRdfId();
		if (empty($d[0]['nsr_id']) && $type=='taxon') $nsr=$this->createNsrCode('tn.nlsr.concept');
		if (empty($d[0]['nsr_id']) && $type=='name') $nsr=$this->createNsrCode('tn.nlsr.name');
		if (empty($d[0]['nsr_id']) && $type=='actor')
		{
			if ($subtype=='person' || empty($subtype) )
			{
				$nsr=$this->createNsrCode('nlsr.person');
			}
			else
			if ($subtype=='organization')
			{
				$nsr=$this->createNsrCode('nlsr.organization');
			}
		}

		if (empty($rdf) && empty($nsr)) return;

		if (!empty($rdf) && !empty($nsr))
		{
			$this->models->NsrIds->insert(
				array(
					'project_id'=>$this->getCurrentProjectId(),
					'rdf_id'=>$rdf,
					'nsr_id'=>$nsr,
					'lng_id'=>$id,
					'item_type'=>$type
				));

			$this->logNsrChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));

		}
		else
		if (!empty($rdf))
		{
			$this->models->NsrIds->update(
				array('rdf_id'=>$rdf),
				array('lng_id'=>$id,'project_id'=>$this->getCurrentProjectId(),'item_type'=>$type)
			);
		}
		else
		if (!empty($nsr))
		{
			$this->models->NsrIds->update(
				array('nsr_id'=>$nsr),
				array('lng_id'=>$id,'project_id'=>$this->getCurrentProjectId(),'item_type'=>$type)
			);

			$this->logNsrChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));
		}

		return array(
			'rdf_id' => !empty($rdf) ? $rdf : null,
			'nsr_id' => !empty($nsr) ? $nsr : null,
		);
	}

	private function generateNsrCode()
	{
		$c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$r='';
		while(strlen($r)<11)
		{
			$r.=substr($c,rand(0,35),1);
		}
		return str_pad($r,12,'0',STR_PAD_LEFT);
	}

	private function createNsrCode( $prefix )
	{
		/*
		NSR ID prefixes (inherited from Trezorix)
		-----------------------------------------
		taxon concept	tn.nlsr.concept/
		taxon name		tn.nlsr.name/
		actor (indiv.)	nlsr.person/
		actor (comp.)	nlsr.organization/
		reference		tn.nlsr.reference/

		to be determined (item_type exists in table, but ID's never issued)
		-------------------------------------------------------------------
		taxon_presence
		habitat
		presence
		rank
		*/

		$exists=true;
		$i=0;
		$code=null;

		while( $exists )
		{
			$code=$prefix."/".$this->generateNsrCode();
			$d = $this->models->ControllerModel->checkNsrCode(array(
                'projectId' => $this->getCurrentProjectId(),
    			'nsrCode' => $code
			));

			if ( $d[0]['total']==0 )
			{
				$exists=false;
			}
			if ( $i>=100 )
			{
				$this->addError('Kon geen nieuw uniek NSR ID creëren.');
				return;
			}
			$i++;
		}

		return $code;
	}

	private function generateRdfId()
	{
		$c='abcdefghijklmnopqrstuvwxyz0123456789';
		$r='';
		while(strlen($r)<32)
		{
			$r.=substr($c,rand(0,35),1);
		}

		return substr($r,0,8).'-'.substr($r,8,4).'-'.substr($r,12,4).'-'.substr($r,16,4).'-'.substr($r,20);
	}

	private function createRdfId()
	{
		$exists=true;
		$i=0;
		$code=null;
		while($exists)
		{
			$code=$this->generateRdfId();
			$d = $this->models->ControllerModel->checkRdfId(array(
                'projectId' => $this->getCurrentProjectId(),
			    'rdfId' => 'http://data.nederlandsesoorten.nl/' . $code
			));

			if ($d[0]['total']==0)
			{
				$exists=false;
			}
			if ($i>=100)
			{
				$this->addError('Kon geen nieuw uniek Rdf ID creëren.');
				return;
			}
			$i++;
		}

		return $code;
	}
	
}