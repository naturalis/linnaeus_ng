<?php

/*
	TAB_VERSPREIDING::$this->getPresenceData($taxon)
	TAB_BEDREIGING_EN_BESCHERMING::EZ
	CTAB_LITERATURE			
	CTAB_MEDIA
	CTAB_DNA_BARCODES
*/


include_once ('NsrController.php');

class NsrPaspoortController extends NsrController
{
    public $usedModels = array(
		'actors',
		'content_taxon',
		'page_taxon',
		'page_taxon_title',
		'tab_order'
	);
    public $usedHelpers = array();
    public $cacheFiles = array();
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $taxonId;

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
		// creating constants for the tab id's (id for page 'Schade en nut' becomes TAB_SCHADE_EN_NUT)
		foreach((array)$this->models->PageTaxon->_get(array('id' => array('project_id' => $this->getCurrentProjectId()))) as $page)
		{
			$p=trim(strtoupper(str_replace(' ','_',$page['page'])));
			if (!defined('TAB_'.$p)) {
				define('TAB_'.$p,$page['id']);
			}
		}
		if (!defined('CTAB_NAMES')) define('CTAB_NAMES','names');
		if (!defined('CTAB_CLASSIFICATION')) define('CTAB_CLASSIFICATION','classification');
		if (!defined('CTAB_TAXON_LIST')) define('CTAB_TAXON_LIST','list');
		if (!defined('CTAB_LITERATURE')) define('CTAB_LITERATURE','literature');
		if (!defined('CTAB_MEDIA')) define('CTAB_MEDIA','media');
		if (!defined('CTAB_DNA_BARCODES')) define('CTAB_DNA_BARCODES','dna barcodes');
		if (!defined('CTAB_NOMENCLATURE')) define('CTAB_NOMENCLATURE','Nomenclature');
	}

    public function paspoortAction()
    {
		$this->checkAuthorisation();
		
		if (!$this->rHasId())
		{
			$this->redirect('index.php');
		}
		
		$this->setTaxonId($this->rGetId());

        $this->setPageName($this->translate('Edit taxon passport'));

		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('tabs',$this->getPassportCategories());	
		$this->smarty->assign('concept',$this->getTaxonById($this->getTaxonId()));	

		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
		
        if (!$this->rHasVal('action'))
            return;

		if ($this->rHasVal('action', 'save_passport'))
		{
			$return=$this->savePassport($this->requestData);
        }
		
        $this->allowEditPageOverlay=false;

		$this->smarty->assign('returnText',$return);

        $this->printPage('ajax_interface');
    }


	private function setTaxonId($id)
	{
		$this->TaxonId=$id;
	}

	private function getTaxonId()
	{
		return isset($this->TaxonId) ? $this->TaxonId : false;
	}

    private function getPassportCategories()
    {
		$categories=$this->models->PageTaxon->freeQuery("
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				_a.show_order,
				_c.content,
				_c.id as content_id,
				_c.publish,
				_a.def_page

			from 
				%PRE%pages_taxa _a
				
			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $this->getDefaultProjectLanguage() ."
				
			left join %PRE%content_taxa _c
				on _a.project_id=_c.project_id
				and _a.id=_c.page_id
				and _c.taxon_id =".$this->getTaxonId()."
				and _c.language_id = ". $this->getDefaultProjectLanguage() ."

			where 
				_a.project_id=".$this->getCurrentProjectId()."

			order by 
				_a.show_order
		");

		if (!$categories) $categories=array();

		$d=$this->getPassport(array('category'=>TAB_VERSPREIDING,'taxon'=>$this->getTaxonId()));

		$order=$this->models->TabOrder->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'columns'=>'tabname,show_order,start_order',
			'fieldAsIndex'=>'tabname',
			'order'=>'start_order'
		));
		
		$start=null;
		$firstNonEmpty=null;

		foreach((array)$categories as $key=>$val)
		{
			$categories[$key]['show_order']=isset($order[$val['tabname']]) ? $order[$val['tabname']]['show_order'] : 99;
			
			if (is_null($firstNonEmpty) && empty($val['is_empty']))
				$firstNonEmpty=$val['id'];

			if (isset($requestedTab) && $val['id']==$requestedTab && empty($val['is_empty'])) {
				$start=$val['id'];
			} else
			if (is_null($start) && !empty($order[$val['tabname']]['start_order']) && empty($val['is_empty'])) {
				$start=$val['id'];
			}

			$categories[$key]['obsolete']=($val['id']==TAB_ALGEMEEN || $val['id']==TAB_BESCHERMING || $val['id']==TAB_DESCRIPTION || $val['id']==TAB_HABITAT);

			if ($val['content_id'])
			{
				$rdf=$this->Rdf->getRdfValues($val['content_id']);
				foreach((array)$rdf as $rVal)
				{
					if ($rVal['predicate']=='hasPublisher')
						$categories[$key]['rdf']['publisher']=$rVal['data'];
					if ($rVal['predicate']=='hasReference')
						$categories[$key]['rdf']['reference']=$rVal['data'];
					if ($rVal['predicate']=='hasAuthor')
						$categories[$key]['rdf']['author']=$rVal['data'];
				}
				
			}
		}
		
		$this->customSortArray($categories,array('key' => 'show_order'));

		if (is_null($start)) $start=$firstNonEmpty;

		return $categories;

    }

    private function getPassport($p=null)
    {
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$category = isset($p['category']) ? $p['category'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$isLower = isset($p['isLower']) ? $p['isLower'] : true;
		$limit=isset($p['limit']) ? $p['limit'] : null;
		$offset=isset($p['offset']) ? $p['offset'] : null;

		$content=$rdf=null;

        switch ($category)
		{
            case CTAB_CLASSIFICATION:
                //$content=$this->getTaxonClassification($taxon);
                $content=
					array(
						'classification'=>$this->getTaxonClassification($taxon),
						'taxonlist'=>$this->getTaxonNextLevel($taxon)
					);
                break;
            
            case CTAB_TAXON_LIST:
                $content=$this->getTaxonNextLevel($taxon);
                break;
            
            case CTAB_LITERATURE:
                $content=$this->getTaxonLiterature($taxon);
                break;
            
            case CTAB_DNA_BARCODES:
                $content=$this->getDNABarcodes($taxon);
                break;

            default:

                $d = array(
                    'taxon_id' => $taxon, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->getDefaultProjectLanguage(), 
                    'page_id' => $category
                );

                if (!$allowUnpublished)
                    $d['publish'] = '1';
                
                $ct = $this->models->ContentTaxon->_get(array(
                    'id' => $d, 
                ));

				$content = isset($ct) ? $ct[0] : null;

        }
/*
		if (isset($content['id']))
			$rdf=$this->Rdf->getRdfValues($content['id']);
*/
		if (isset($content['content']))
			$content=$content['content'];

		return array('content'=>$content,'rdf'=>$rdf);
    }


	private function savePassport($p)
	{
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$page = isset($p['page']) ? $p['page'] : null;
		$content = isset($p['content']) ? $p['content'] : null;
		$publish = isset($p['publish']) && $p['publish']==1 ? '1' : '0';
		
		$before=$this->getPassport(array('category'=>$page,'taxon'=>$taxon));

		if (empty($content))
		{
			$r=$this->models->ContentTaxon->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id'=>$taxon,
				'language_id'=>$this->getDefaultProjectLanguage(),
				'page_id'=>$page
			));

			$this->logNsrChange(array('before'=>$before,'note'=>'deleted passport'));
			return $r;

		}
		else
		{
			$d=$this->models->ContentTaxon->_get(array(
				'id'=>array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id'=>$taxon,
					'language_id'=>$this->getDefaultProjectLanguage(),
					'page_id'=>$page
				)
			));
			
			$id=!empty($d[0]['id']) ? $d[0]['id'] : null;

			$d=$this->models->ContentTaxon->save(array(
				'id'=>$id,
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$taxon,
				'language_id'=>$this->getDefaultProjectLanguage(),
				'page_id'=>$page,
				'content' => $content,
				'publish' => $publish
			));
			
			$after=$this->getPassport(array('category'=>$page,'taxon'=>$taxon));

			$this->logNsrChange(array('before'=>$before,'after'=>$after,'note'=>($id ? 'updated passport' : 'new passport')));
			
			return $d;
			
		}

	}

}