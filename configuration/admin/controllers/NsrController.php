<?php
/*
 *  NSR Controller
 *
 *  TAB_VERSPREIDING::$this->getPresenceData($taxon)
 *  TAB_BEDREIGING_EN_BESCHERMING::EZ
 *  CTAB_LITERATURE
 *  CTAB_MEDIA
 *  CTAB_DNA_BARCODES
 */

include_once ('Controller.php');
include_once ('RdfController.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('TaxonParentageController.php');

class NsrController extends Controller
{

    public $usedModels = array(
		'nsr_ids',
		'trash_can',
    );

	public $conceptId=null;

//    public $modelNameOverride='NsrTreeModel';


	private $cTabs=[
		'CTAB_NAMES'=>['id'=>-1,'title'=>'Naamgeving'],
		'CTAB_MEDIA'=>['id'=>-2,'title'=>'Media'],
		'CTAB_CLASSIFICATION'=>['id'=>-3,'title'=>'Classification'],
		'CTAB_TAXON_LIST'=>['id'=>-4,'title'=>'Child taxa list'],	// $this->getTaxonNextLevel($taxon); (children?)
		'CTAB_LITERATURE'=>['id'=>-5,'title'=>'Literature'],
		'CTAB_DNA_BARCODES'=>['id'=>-6,'title'=>'DNA barcodes','remarks'=>'suppressing will also remove DNA-barcodes from the extended search options'],
		'CTAB_DICH_KEY_LINKS'=>['id'=>-7,'title'=>'Key links'],
		//		'CTAB_NOMENCLATURE'=>['id'=>-8,'title'=>'Nomenclature'],
	    'CTAB_EXPERTS'=>['id'=>-10,'title'=>'Experts'],
	    'CTAB_PRESENCE_STATUS'=>['id'=>-9,'title'=>'Presence status','remarks'=>'suppressing will also remove presence status from the extended search options'],
	];

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
		$this->TaxonParentageController = new TaxonParentageController;
		
		$this->smarty->assign( 'noautoexpand', $this->rHasVal('noautoexpand','1') );

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->show_nsr_specific_stuff=$this->moduleSettings->getGeneralSetting( 'show_nsr_specific_stuff' , 0)==1;
		$this->smarty->assign( 'show_nsr_specific_stuff',$this->show_nsr_specific_stuff );

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

	public function setConceptId($id)
	{
		$this->conceptId=$id;
	}

	public function getConceptId()
	{
		return isset($this->conceptId) ? $this->conceptId : false;
	}

	public function getCTabs()
	{
		return $this->cTabs;
	}

    public function getCategories()
    {
		$categories=$this->models->NsrTaxonModel->getCategories(array(
            'project_id' => $this->getCurrentProjectId(),
    		'language_id' => $this->getDefaultProjectLanguage()
		));

		$standard_categories=$this->getCTabs();

		array_walk($standard_categories,function(&$a,$b) {
			$a=['tabname'=>$b,'id'=>$a['id'],'page'=>$a['title'],'remarks'=>isset($a['remarks']) ? $a['remarks'] : null,'type'=>'auto'];
		});

		$all_categories=array_merge((array)$categories,$standard_categories);

        $lp=$this->getProjectLanguages();

		$order=$this->models->TabOrder->_get([
			'id'=>['project_id' => $this->getCurrentProjectId()],
			'order'=>'show_order',
			'fieldAsIndex'=>'page_id'
		]);

        foreach((array)$all_categories as $key=>$page)
		{
			if ( !empty($page['external_reference']) )
			{
				$all_categories[$key]['external_reference_decoded'] = @json_decode($page['external_reference']);
			}

            foreach((array)$lp as $k=>$language)
			{
                $tpt = $this->models->PagesTaxaTitles->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'page_id' => $page['id'],
                        'language_id' => $language['language_id']
                    )
                ));

                $all_categories[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];
                $all_categories[$key]['show_order']=isset($order[$page['id']]) ? $order[$page['id']]['show_order'] : 99;
                $all_categories[$key]['suppress']=isset($order[$page['id']]) ? $order[$page['id']]['suppress']==1 : false;
                $all_categories[$key]['start_order']=isset($order[$page['id']]) ? $order[$page['id']]['start_order'] : null;
                $all_categories[$key]['show_when_empty']=isset($order[$page['id']]) ? $order[$page['id']]['show_when_empty']==1 : false;
            }
        }

		usort($all_categories,function($a,$b)
		{
			if ($a['show_order']>$b['show_order'] )
				return 1;
			if ($a['show_order']<$b['show_order'] )
				return -1;
			if ($a['page']>$b['page'] )
				return 1;
			if ($a['page']<$b['page'] )
				return -1;
			return 0;
		});

		return $all_categories;

    }

	// removes interfering noise from search term
	protected function removeSearchNoise( $search )
	{
		$noise = [
			$this->_hybridMarker,
			$this->_hybridMarkerHtml,
			$this->_formaMarker,
			$this->_hybridMarker_graftChimaera,
			$this->_varietyMarker,
			$this->_subspeciesMarker,
			$this->_nothoInfixPrefix . $this->_varietyMarker,
			$this->_nothoInfixPrefix . $this->_subspeciesMarker,
		];
		 
		return preg_replace('/(\s+)/',' ',str_replace($noise,' ', $search));
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

	public function saveTaxonParentage( $id=null )
	{
	    if ( is_null($id) )
		{
			return $this->TaxonParentageController->generateParentageAll();
		}
		else
		{
			return $this->TaxonParentageController->generateParentage( $id );
		}
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
		unset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']);
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

		return ltrim(str_replace('tn.nlsr.concept/','',$data[0]['nsr_id']),'0');
	}

	public function getConcept($id)
	{
		$c=$this->getTaxonById($id,true);

		if ( !empty($c) )
		{
			$c['nsr_id']=$this->getNsrId(array('id'=>$c['id'],'item_type'=>'taxon'));
			$c['parent']=$this->getTaxonById($c['parent_id'],true);

			$d=$this->models->TrashCan->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$id,
				'item_type'=>'taxon'
			)));

			$c['is_deleted']=($d[0]['is_deleted']==1);

			if ( isset($c['label']) )
			{
				$c['label_no_infix']=$c['label'];
				$c['label']=$this->addHybridMarkerAndInfixes( [ 'name'=>$c['label'],'base_rank_id'=>$c['base_rank'],'taxon_id'=>$c['id'],'parent_id'=>$c['parent_id'] ] );
			}
			if ( isset($c['parent']['label']) )
			{
				$c['parent']['label_no_infix']=$c['parent']['label'];
				$c['parent']['label']=
					$this->addHybridMarkerAndInfixes( [ 'name'=>$c['parent']['label'],'base_rank_id'=>$c['parent']['base_rank'],'taxon_id'=>$c['parent']['id'],'parent_id'=>$c['parent']['parent_id'] ] );
			}
		}

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

			$this->logChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));

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

			$this->logChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));
		}

		return array(
			'rdf_id' => !empty($rdf) ? $rdf : null,
			'nsr_id' => !empty($nsr) ? $nsr : null,
		);
	}

	private function generateNsrCode()
	{
		$c='ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		$r='';
		while(strlen($r)<11)
		{
			$r.=substr($c,rand(0,35),1);
		}
		return str_pad($r,12,'0',STR_PAD_LEFT);
	}

    /**
     * NSR ID prefixes (inherited from Trezorix)
     * -----------------------------------------
     * taxon concept	tn.nlsr.concept/
     * taxon name		tn.nlsr.name/
     * actor (indiv.)	nlsr.person/
     * actor (comp.)	nlsr.organization/
     * reference		tn.nlsr.reference/
     *
     * to be determined (item_type exists in table, but ID's never issued)
     * -------------------------------------------------------------------
     * taxon_presence
     * habitat
     * presence
     * rank
     * @param $prefix
     * @return null|string|void
     */
    private function createNsrCode($prefix )
	{

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
				$this->addError('Could not create a unique NSR ID.');
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
				$this->addError('Could not create a unique Rdf ID.');
				return;
			}
			$i++;
		}

		return $code;
	}

}