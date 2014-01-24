<?php

include_once ('Controller.php');
class SpeciesController extends Controller
{
	private $_lookupListMaxResults=100;
	private $_includeOverviewImageInMedia=true;
	private $_defaultSpeciesTab;
	private $_automaticTabTranslation=array();

    public $usedModels = array(
        'content_taxon', 
        'section', 
        'label_section', 
        'page_taxon', 
        'page_taxon_title', 
        'media_taxon', 
        'media_descriptions_taxon', 
        'hybrid', 
        'synonym', 
        'commonname', 
        'label_language', 
        'literature', 
        'literature_taxon',
		'nbc_extras',
		'content_free_module',
        'taxa_relations', 
        'variation_relations',
		'names',
		'name_types',
		'actors',
		'dna_barcodes',
		'presence_taxa',
		'media_meta'
    );
    public $controllerPublicName = 'Species module';
    public $controllerBaseName = 'species';
    public $cssToLoad = array(
        'species.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
            'lookup.js', 
            'dialog/jquery.modaldialog.js'
        ), 
        'IE' => array()
    );
	
	public $useCache=false;


	/* init */
    public function __construct ()
    {
        parent::__construct();
		$this->initialise();

    }


    public function __destruct ()
    {
        parent::__destruct();
    }


    private function initialise ()
    {
		
// MOVE THIS TO CONFIG ON ALL EXTERNAL MACHINES!
if (!defined('LANGUAGE_ID_SCIENTIFIC')) define('LANGUAGE_ID_SCIENTIFIC',123);		
		
		// creating constants for the tab id's (id for page 'Schade en nut' becomes TAB_SCHADE_EN_NUT)
		foreach((array)$this->models->PageTaxon->_get(array('id' => array('project_id' => $this->getCurrentProjectId()))) as $page) {
			
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


        $this->_lookupListMaxResults=$this->getSetting('lookup_list_species_max_results',$this->_lookupListMaxResults);
        $this->_includeOverviewImageInMedia=$this->getSetting('include_overview_in_media',true);
		$this->_defaultSpeciesTab=$this->getSetting('species_default_tab',CTAB_CLASSIFICATION);

		$d=$this->getSetting('species_tab_translate');
		$d=explode(',',$d);
		foreach($d as $val) {
			$val=explode(':',trim($val,'{}'));
			if (!$val[0]||!$val[1]) continue;
			$this->_automaticTabTranslation[$val[0]]=$val[1];
		}

		include_once ('RdfController.php');
		$this->Rdf = new RdfController;
		
    }


	/* public set/get */

    public function setTaxonType ($type)
    {
		//public as it needs to be callable from the view
        $_SESSION['app'][$this->spid()]['species']['type'] = ($type == 'higher') ? 'higher' : 'lower';
    }


	/* public actions */
	
    public function indexAction ()
    {
        $this->setPageName($this->translate('Species module index'));
        
        $this->setTaxonType('lower');
        
        $this->setControllerBaseName();
        
        $this->_indexAction();
    }


    public function higherSpeciesIndexAction ()
    {
        $this->setPageName($this->translate('Higher taxa index'));
        
        $this->setTaxonType('higher');
        
        $this->setControllerBaseName();
        
        $this->_indexAction();
    }


    public function taxonAction ()
    {
        if ($this->rHasId()) {

            // get taxon
            $taxon = $this->getTaxonById($this->requestData['id']);
		}
        if (!empty($taxon)) {

            $this->setTaxonType($taxon['lower_taxon'] == 1 ? 'lower' : 'higher');

            $this->setControllerBaseName();
            
            if (isset($this->requestData['lan']))
                $this->setCurrentLanguageId($this->requestData['lan']);

            // get categories
            $categories = $this->getCategories(
				array(
					'taxon' => $taxon['id'], 
					'allowUnpublished' => $this->isLoggedInAdmin()
				)
            );
			
            // determine the page_id the page will open in
			$requestedCat=$this->rHasVal('cat') ? $this->requestData['cat'] : null;

			if (isset($requestedCat) && isset($this->_automaticTabTranslation[$this->requestData['cat']])) {
				$requestedCat=$this->_automaticTabTranslation[$this->requestData['cat']];
			}

            $activeCategory = 
            	!empty($requestedCat) ? 
	            	(isset($categories['emptinessList'][$requestedCat]) && $categories['emptinessList'][$requestedCat]==0 ? 
	            		$requestedCat : 
	            		$categories['defaultCategory']
	            	) : 
           		$categories['defaultCategory'];


			$activeCategory = !empty($requestedCat) ? $requestedCat : $categories['defaultCategory'];


            // setting the css classnames
            foreach ((array) $categories['categories'] as $key => $val) {
                $c = array(
                    'category'
                );
                if ($val['id'] == $activeCategory) {
                    $c[] = 'category-active';

					$activeCategory_label=isset($val['page']) ? $val['page'] : $val['title'];

                }
                if ($key == 0) {
                    $c[] = 'category-first';
                }
                else if ($key == count($categories['categories']) - 1) {
                    $c[] = 'category-last';
                }
                if ($val['is_empty'] == 1 && $val['id'] != $activeCategory) {
                    $c[] = 'category-no-content';
                }
                $categories['categories'][$key]['className'] = implode(' ', $c);
								
            }

            if ($taxon['lower_taxon'] == 1) {
                
                $this->setPageName(sprintf($this->translate('Species module: "%s" (%s)'), $taxon['label'], $this->getCategoryName($activeCategory)));
                
                $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Higher taxa: "%s" (%s)'), $taxon['label'], $this->getCategoryName($activeCategory)));
            }

            if (isset($taxon)) {
                
                $this->smarty->assign('overviewImage', $this->getTaxonOverviewImage($taxon['id']));
                
                $this->smarty->assign('taxon',$taxon);

				/*
					NSR add on
				*/
				if (isset($this->models->Names) && $this->getSetting('taxon_page_ext_classification',0)) {

					/*
						must redesign this, as the original method refuses empty categories as active ones,
						whereas the NSR has several automatically generated pages that are empty in the
						database.
					*/
	
					$names=$this->getNames($taxon['id']);
					$classification=$this->getTaxonClassification($taxon['id']);
					
					$this->smarty->assign('names',$names);

					foreach((array)$classification as $key=>$val) {

						$d=$this->getName(array(
								'taxonId' => $val['id'],
								'languageId' => $this->getCurrentLanguageId(),
								'predicateType' => PREDICATE_PREFERRED_NAME
						));

						$classification[$key]['preferredName']=$d[0]['name'];

						$d=$this->getName(array(
								'taxonId' => $val['id'],
								'languageId' => $this->getCurrentLanguageId(),
								'predicateType' => PREDICATE_VALID_NAME
						));

						$classification[$key]['uninomial']=$d[0]['uninomial'];
						$classification[$key]['specificEpithet']=$d[0]['specific_epithet'];

					}

					$this->smarty->assign('classification',$classification);
					$this->smarty->assign('ranks',$this->getProjectRanks());
					
					// verspreiding
					if ($activeCategory==TAB_DISTRIBUTION) {

						$presenceData=$this->getPresence($taxon['id']);
						$this->smarty->assign('presenceData', $presenceData);

					}
	
				}

				$d=$this->getTaxonContent(
					array(
						'taxon' => $taxon['id'], 
						'category' => $activeCategory, 
						'allowUnpublished' => $this->isLoggedInAdmin(),
						'isLower' =>  $taxon['lower_taxon']
					)
				);

				$content=$d['content'];
				$rdf=$d['rdf'];

				if ($activeCategory!=CTAB_MEDIA && $activeCategory!=CTAB_DNA_BARCODES) {
					$content = $this->matchGlossaryTerms($content);
					$content = $this->matchHotwords($content);
				}
				

            
                $this->smarty->assign('content', $content);

                $this->smarty->assign('rdf', $rdf);

                $this->smarty->assign('contentCount', $this->getContentCount($taxon['id']));
                
                $this->smarty->assign('adjacentItems', $this->getAdjacentTaxa($taxon['id']));
           
            }
            
            $this->smarty->assign('categories', $categories['categories']);

            $this->smarty->assign('activeCategory', $activeCategory);

            $this->smarty->assign('categorySysList', $categories['categorySysList']);
            
            $this->smarty->assign('headerTitles', 
            array(
                'title' => $taxon['label']
            ));
            
            //$this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);
        }
        else {
            
            $this->addError($this->translate('No or unknown taxon ID specified.'));
            
            $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey(null);
        }
        
        $this->printPage('taxon');
    }


    public function nameAction ()
    {
        //if (!$this->rHasId())
		$name=$this->getName(array('nameId'=>$this->requestData['id']));
		$name=$name[0];
		$name['nametype']=sprintf($this->Rdf->translatePredicate($name['nametype']),$name['language_label']);
		
		$taxon=$this->getTaxonById($name['taxon_id']);
		$this->smarty->assign('name',$name);
		$this->smarty->assign('taxon',$taxon);
        $this->printPage();
    }


    public function ajaxInterfaceAction ()
    {
		$return='error';
        
        if ($this->rHasVal('action', 'get_lookup_list') && !empty($this->requestData['search'])) {
            
            $return=$this->getLookupList($this->requestData);
			
        }
        else 
		if ($this->rHasVal('action', 'get_media_info') && !empty($this->requestData['id'])) {
            
			$return=json_encode($this->getTaxonMedia(null, $this->requestData['id']));

        }
        
        $this->allowEditPageOverlay = false;
		
		$this->smarty->assign('returnText',$return);
        
        $this->printPage();
    }

	
    public function taxonOverviewAction ()
    {
		
		//sec overview of taxon without header, menu's etc (created for dierenzoeker, fetched through ajax)

        if ($this->rHasId()) {
			
			if ($this->rHasVal('type','v')) {
			
				$tv = $this->models->TaxonVariation->_get(array(
					'id' => array('project_id'=>$this->getCurrentProjectId(),'id'=> $this->requestData['id']),
					'columns' => 'taxon_id,label'
				));
				
				$taxonVariation = $tv[0];
				
				$tId = $taxonVariation['taxon_id'];

				$related = $this->getRelatedEntities(array(
					'vId' => $tId
				));

				$freePageId = $this->getNbcExtras(array('id'=>$this->requestData['id'],'name' => 'free_page_id','type'=>'variation'));
							
			} else {

				$tId = $this->requestData['id'];

				$related = $this->getRelatedEntities(array(
					'tId' => $tId 
				));
				

				$freePageId = $this->getNbcExtras(array('id'=>$this->requestData['id'],'name' => 'free_page_id'));

			}

			foreach((array)$related as $key => $val) {

				if($val['ref_type']=='variation'){
					$d = $this->getVariation($val['relation_id']);
					$related[$key]['label'] = $d['label'];
					$related[$key]['url_image'] = $this->getNbcExtras(array('id'=>$val['relation_id'],'name' => 'url_image','type'=>'variation'));
				} else {
					$d = $this->getCommonname($val['relation_id']);
					$related[$key]['label'] = $d;
					$related[$key]['url_image'] = $this->getNbcExtras(array('id'=>$val['relation_id'],'name' => 'url_image'));
				}
					
			}
			
			if (!empty($freePageId)) {

				$cfm = $this->models->ContentFreeModule->_get(array(
					'id' => array(
						'page_id' => $freePageId, 
						'project_id' => $this->getCurrentProjectId()
					)
				));
				
				$freePageContent = $cfm[0];
				
			}

            // get taxon
            $taxon = $this->getTaxonById($tId);

            $this->setTaxonType($taxon['lower_taxon'] == 1 ? 'lower' : 'higher');
            
            $this->setControllerBaseName();
            
            if (isset($this->requestData['lan']))
                $this->setCurrentLanguageId($this->requestData['lan']);
                
            // get categories
            $categories = $this->getCategories(
				array(
					'taxon' => $this->requestData['id'], 
					'allowUnpublished' => $this->isLoggedInAdmin()
				)
            );

			foreach((array)$categories['categories'] as $val) {

	            $d=$this->getTaxonContent(
					array(
						'taxon' => $taxon['id'], 
						'category' => $val['id'], 
						'allowUnpublished' => $this->isLoggedInAdmin(),
						'incOverviewImage' => true
					)
				);
				
				$content[$val['id']]=$d['content'];

				if (!$this->rHasVal('hotwords',false)) {
					$content = $this->matchGlossaryTerms($content);
					$content = $this->matchHotwords($content);
				}
			
			}

            if ($taxon['lower_taxon'] == 1) {
                
                $this->setPageName(sprintf($this->translate('Species module: "%s"'), $taxon['label']));
                
                $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Higher taxa: "%s"'), $taxon['label']));
            }
            
			$this->smarty->assign('taxon', $taxon);
		
			$this->smarty->assign('content', $content);

			$this->smarty->assign('overviewImage', $this->getTaxonOverviewImage($taxon['id']));
                
			if (!$this->rHasVal('navigation',false))
				$this->smarty->assign('adjacentItems', $this->getAdjacentTaxa($taxon['id']));
           
            $this->smarty->assign('categoryList', $categories['categoryList']);

			if (isset($freePageContent))
				$this->smarty->assign('freePageContent', $freePageContent);

			if (isset($related))
				$this->smarty->assign('related', $related);

			$this->smarty->assign('type', ($this->rHasVal('type') ? $this->requestData['type'] : 't' ));

			if ($this->rHasVal('type','v')) {
				
				$this->smarty->assign('headerTitles', 
					array(
						'title' => $taxonVariation['label'],
						'subtitle' => $taxon['label']
					));

			} else { 

				$this->smarty->assign('headerTitles', 
					array(
						'title' => $content['names']['common'][0]['commonname'],
						'subtitle' => $taxon['label']
					));

			}
            
        }
        else {
            
            $this->addError($this->translate('No taxon ID specified.'));
            
            $this->setLastViewedTaxonIdForTheBenefitOfTheMapkey(null);
        }
        
        $this->printPage();
    }







	/* private set/get */

    private function getTaxonType()
    {
        return isset($_SESSION['app'][$this->spid()]['species']['type']) ? $_SESSION['app'][$this->spid()]['species']['type'] : 'lower';
    }


    private function setControllerBaseName()
    {
        if ($this->getTaxonType() == 'higher')
            $this->controllerBaseName = 'highertaxa';
        else
            $this->controllerBaseName = 'species';
    }

    private function setLastViewedTaxonIdForTheBenefitOfTheMapkey($id)
    {
        if (!is_null($id)) {
            
            $_SESSION['app'][$this->spid()]['species']['lastTaxon'] = $id;
            
            unset($_SESSION['app']['user']['mapkey']['state']);
        }
        else {
            
            unset($_SESSION['app'][$this->spid()]['species']['lastTaxon']);
        }
    }


    private function getlastVisitedCategory($taxon,$category)
    {
		
		if (!$this->useCache)
			return false;
		
        if (isset($_SESSION['app'][$this->spid()]['species']['last_visited'][$taxon][$category])) {
            return $_SESSION['app'][$this->spid()]['species']['last_visited'][$taxon][$category];
        }
        
        if (isset($_SESSION['app'][$this->spid()]['species']['last_visited'])) {
            
            $storedTaxon = key($_SESSION['app'][$this->spid()]['species']['last_visited']);
            if ($storedTaxon != $taxon) {
                unset($_SESSION['app'][$this->spid()]['species']['last_visited'][$storedTaxon]);
            }
        }
        
        return false;
    }


    private function setlastVisitedCategory($taxon,$category,$d)
    {
        $_SESSION['app'][$this->spid()]['species']['last_visited'][$taxon][$category] = $d;
    }


    private function getFirstTaxonId()
    {

        $t = $this->models->Taxon->freeQuery(
        array(
			'query' => '
				select _a.id
				from %PRE%taxa _a 
				left join %PRE%projects_ranks _b on _a.rank_id=_b.id
				left join %PRE%ranks _c on _b.rank_id=_c.id
				where _a.project_id = '.$this->getCurrentProjectId().'
				and _b.lower_taxon = '.($this->getTaxonType() == 'higher' ? 0 : 1).'
				order by _a.taxon_order, _a.taxon
				limit 1'
        ));
		
		return isset($t) ? $t[0]['id'] : null;
    }
	
	
	private function getAdjacentTaxa($id)
    {

		if (!isset($_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()])) {

			$_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()]=
				$this->models->Taxon->freeQuery(
					array(
						'query' => '
							select _a.id,_a.taxon
							from %PRE%taxa _a 
							left join %PRE%projects_ranks _b on _a.rank_id=_b.id 
							where _a.project_id = '.$this->getCurrentProjectId().'
							and _b.lower_taxon = '.($this->getTaxonType() == 'higher' ? 0 : 1).'
							order by _a.taxon_order, _a.taxon
							'
					));

		}

		$prev=$next=false;
		while (list ($key, $val) = each($_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()])) {

			if ($val['id']==$id) {

				// current = next because the pointer has already shifted forward
				$next = current($_SESSION['app'][$this->spid()]['species']['browse_order'][$this->getTaxonType()]);

				return array(
					'prev' => $prev!==false ? array(
						'id' => $prev['id'], 
						'label' => $prev['taxon']
					) : null, 
					'next' => $next!==false ? array(
						'id' => $next['id'], 
						'label' => $next['taxon']
					) : null
				);
			}
			
			$prev=$val;
            
		}

        return null;
    }


    private function getTaxonNextLevel($id)
    {
        $t = $this->models->Taxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'parent_id' => $id
            ), 
            'columns' => 'id,taxon,parent_id,rank_id,taxon_order,is_hybrid,list_level,is_empty,author', 
            'fieldAsIndex' => 'id', 
            'order' => 'taxon_order,id'
        ));
        
        foreach ((array) $t as $key => $val) {
            
            $t[$key]['label'] = $this->formatTaxon($val);
            
            $names = $this->getTaxonCommonNames($val['id'], $this->getCurrentLanguageId());
            
            if (isset($names[0]['commonname']))
                $t[$key]['commonname'] = $names[0]['commonname'];
        }
        
        return $t;
    }


    private function getCategories($p=null)
    {
		
		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$showAlways = isset($p['showAlways']) ? $p['showAlways'] : null;

		// get the defined categories (just the page definitions, no content yet)
		$tp = $this->models->PageTaxon->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			), 
			'order' => 'show_order', 
			'fieldAsIndex' => 'id'
		));

		foreach ((array) $tp as $key => $val)
		{
			
			$tp[$key]['title'] = $this->getCategoryName($val['id']);
			$tp[$key]['tabname'] = 'TAB_'.str_replace(' ','_',strtoupper($val['page']));
			
			if ($val['def_page'] == 1)
				$_SESSION['app'][$this->spid()]['species']['defaultCategory'] = $val['id'];
		}
		

        if ($taxon)
		{
            
            $defCat = $this->_defaultSpeciesTab;
            
            $n = $this->getTaxonNames($taxon);
            
            $stdCats[] = array(
                'id' => CTAB_NAMES, 
                'title' => $this->translate('Names'), 
                'is_empty' => (count((array) $n['synonyms']) == 0 && count((array) $n['common']) == 0 ? 1 : 0),
				'tabname' => 'CTAB_NAMES'
            );

            $stdCats[] = array(
                'id' => CTAB_NOMENCLATURE, 
                'title' => $this->translate('Nomenclature'), 
                'is_empty' => false,
				'tabname' => 'CTAB_NOMENCLATURE'
            );
            
            $d = array();
            
            foreach ((array) $tp as $key => $val) {
                
                $val['is_empty'] = 1;
                
                $ct = $this->models->ContentTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->getCurrentLanguageId(), 
                        'taxon_id' => $taxon, 
                        'page_id' => $val['id']
                    ), 
                    'columns' => 'page_id,publish,content'
                ));
                
                if (($ct[0]['publish'] == '1' || $allowUnpublished) && strlen($ct[0]['content']) > 0)
                    $val['is_empty'] = 0;
                
                $d[$key] = $val;
                
                if ($ct[0]['page_id'] == $_SESSION['app'][$this->spid()]['species']['defaultCategory']) // && ($ct[0]['publish']=='1' || $allowUnpublished)) 
                    $defCat = $_SESSION['app'][$this->spid()]['species']['defaultCategory'];
            }

            $stdCats[] = array(
                'id' => CTAB_CLASSIFICATION, 
                'title' => $this->translate('Classification'), 
                'is_empty' => 0,
				'tabname' => 'CTAB_CLASSIFICATION'
            );
            
            if ($this->getTaxonType() == 'higher')
			{
            
	            $tnl = $this->getTaxonNextLevel($taxon);
	            
	            $stdCats[] = array(
	                'id' => CTAB_TAXON_LIST, 
	                'title' => $this->translate('Taxon list'), 
	                'is_empty' => (count((array) $tnl) > 0 ? 0 : 1),
					'tabname' => 'CTAB_TAXON_LIST'
	            );
	            
            }
            
             if ($this->doesProjectHaveModule(MODCODE_LITERATURE))
			 {
                
                $l = $this->getTaxonLiterature($taxon);
                
                $stdCats[] = array(
                    'id' => CTAB_LITERATURE, 
                    'title' => $this->translate('Literature'), 
                    'is_empty' => (count((array) $l) > 0 ? 0 : 1),
					'tabname' => 'CTAB_LITERATURE'
                );
            }
            
            $m = $this->getTaxonMedia($taxon, null);
			
            $stdCats[] = array(
                'id' => CTAB_MEDIA, 
                'title' => $this->translate('Media'), 
                'is_empty' => (count((array) $m) > 0 ? 0 : 1),
				'tabname' => 'CTAB_MEDIA'
            );

            foreach ((array) $d as $key => $val) {
                $categoryList[$key] = $val['title'];
                $categorySysList[$key] = $val['page'];
			}


			if (isset($this->models->DnaBarcodes))
			{			
		
				$dna = $this->models->DnaBarcodes->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'taxon_id' => $taxon
					),
					'columns' => 'count(*) as tot'
				));
				
				$stdCats[] = array(
					'id' => CTAB_DNA_BARCODES, 
					'title' => $this->translate('DNA barcodes'), 
					'is_empty' => $dna[0]['tot']==0,
					'tabname' => 'CTAB_DNA_BARCODES'
				);
			}
			
            $d = array_merge($d, $stdCats);

            foreach ((array) $d as $val)
                $emptinessList[$val['id']] = $val['is_empty'];

            
            return array(
                'categories' => $d, 
                'defaultCategory' => $defCat, 
                'categoryList' => isset($categoryList) ? $categoryList : null,
                'categorySysList' => isset($categorySysList) ? $categorySysList : null,
                'emptinessList' => $emptinessList,
            );
        }
        

        return array(
            'categories' => $tp, 
            'defaultCategory' => $_SESSION['app'][$this->spid()]['species']['defaultCategory']
        );
    }


    private function getCategoryName($id)
    {
		if (is_numeric($id)) {
			
			$tpt = $this->models->PageTaxonTitle->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->getCurrentLanguageId(), 
					'page_id' => $id
				), 
				'columns' => 'title'
			));
			
			if (empty($tpt[0]['title'])) {
				
				$tpt = $this->models->PageTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $id
					), 
					'columns' => 'page as title'
				));
			}
			
			return $tpt[0]['title'];
		}
		else {
			
			return ucwords($id);
		}

    }


	private function _collectChildIds($id)
	{
		foreach((array)$this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id'=>$this->getCurrentProjectId(),
					'parent_id'=>$id
				),
				'columns'=>'id'
			)
		) as $val) {
			
			$this->tmp[]=$val['id'];
			$this->_collectChildIds($val['id']);

		}
	}

    private function getCollectedHigherTaxonMedia($id)
    {
	
		$this->tmp=array();

		$this->_collectChildIds($id);

        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.id,
				_a.file_name,
				_a.thumb_name,
				_a.original_name,
				_a.taxon_id,
				_b.description,
				_k.name,
				trim(
					ifnull(
						concat(_e.uninomial,' ',_e.specific_epithet,' ',_e.infra_specific_epithet),
						replace(_e.name,_e.authorship,'')
					) 
				) as taxon
			from
				%PRE%media_taxon _a
			
			left join %PRE%media_descriptions_taxon _b
				on _a.project_id = _b.project_id
				and _a.id=_b.media_id
				and _b.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%names _k
				on _a.taxon_id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _e
				on _a.taxon_id=_e.taxon_id
				and _a.project_id=_e.project_id
				and _e.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _e.language_id=".LANGUAGE_ID_SCIENTIFIC."
				
			where
				_a.project_id=".$this->getCurrentProjectId()." and 
				_a.taxon_id in (".implode(',',$this->tmp).")
			limit 100" 
		);
		
		//q($this->models->MediaTaxon->q(),1);
		//q($media,1);

		/*
		foreach((array)$media as $key => $val) {

			$d=$this->models->MediaMeta->_get(
				array(
					'id'=>array(
						'project_id' => $this->getCurrentProjectId(),
						'media_id'=> $val['id']
					),
					'columns'=>'sys_label,meta_data'
				)
			);

			foreach((array)$d as $metaVal) {
				$media[$key]['meta'][$metaVal['sys_label']]=$metaVal['meta_data'];
			}
					
		}
		*/

		return $media;

    }

    private function getTaxonContent($p=null)
    {

		$taxon = isset($p['taxon']) ? $p['taxon'] : null;
		$category = isset($p['category']) ? $p['category'] : null;
		$allowUnpublished = isset($p['allowUnpublished']) ? $p['allowUnpublished'] : false;
		$incOverviewImage = isset($p['incOverviewImage']) ? $p['incOverviewImage'] : $this->_includeOverviewImageInMedia;
		$isLower = isset($p['isLower']) ? $p['isLower'] : true;

		$content=$rdf=null;

        switch ($category) {

            case CTAB_MEDIA:
                $content=$this->getTaxonMedia($taxon,null,$incOverviewImage);

				if (empty($content) && !$isLower) {
					$content=$this->getCollectedHigherTaxonMedia($taxon);
				}
				
                break;
            
            case CTAB_CLASSIFICATION:
                $content=$this->getTaxonClassification($taxon);
                break;
            
            case CTAB_TAXON_LIST:
                $content=$this->getTaxonNextLevel($taxon);
                break;
            
            case CTAB_LITERATURE:
                $content=$this->getTaxonLiterature($taxon);
                break;
            
            case CTAB_NAMES:
                $content=$this->getTaxonNames($taxon);
                break;
            
            case CTAB_DNA_BARCODES:
                $content=$this->getDNABarcodes($taxon);
                break;
            
            default:
                $d = array(
                    'taxon_id' => $taxon, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->getCurrentLanguageId(), 
                    'page_id' => $category
                );
                
                if (!$allowUnpublished)
                    $d['publish'] = '1';
                
                $ct = $this->models->ContentTaxon->_get(array(
                    'id' => $d, 
                ));
				
				$content = isset($ct) ? $ct[0] : null;
				
				$rdf=$this->Rdf->getRdfValues($content['id']);

                $content=$content['content'];
        }
		
		return array('content'=>$content,'rdf'=>$rdf);
    }


    private function getTaxonMedia($taxon=null,$id=null,$inclOverviewImage=false)
    {

        if ($mt = $this->getlastVisitedCategory($taxon, CTAB_MEDIA))
            return $mt;
        
        $d = array(
            'project_id' => $this->getCurrentProjectId()
        );
        
        if (isset($taxon))
            $d['taxon_id']=$taxon;

        if (isset($id))
            $d['id']=$id;
        
		if (!$inclOverviewImage)
			$d['overview_image']='0';
        
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => $d, 
            'columns' => 'id,file_name,thumb_name,original_name,mime_type,sort_order,overview_image,substring(mime_type,1,locate(\'/\',mime_type)-1) as mime', 
            'order' => 'mime, sort_order'
        ));

        $this->loadControllerConfig('species');
        
        foreach ((array) $mt as $key => $val) {
            
            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->getCurrentLanguageId(), 
                    'media_id' => $val['id']
                ), 
                'columns' => 'description'
            ));
  
            
//            $mt[$key]['description'] = $mdt ? $this->matchHotwords($this->matchGlossaryTerms($mdt[0]['description'])) : null;
			$mt[$key]['description'] = $mdt ? $mdt[0]['description'] : null;
            
            $t = isset($this->controllerSettings['mime_types'][$val['mime_type']]) ? $this->controllerSettings['mime_types'][$val['mime_type']] : null;

            $mt[$key]['category'] = isset($t['type']) ? $t['type'] : 'other';
            $mt[$key]['category_label'] = isset($t['label']) ? $t['label'] : 'Other';
            $mt[$key]['mime_show_order'] = isset($t['type']) ? $this->controllerSettings['mime_show_order'][$t['type']] : 99;
            $mt[$key]['full_path'] = $this->getProjectUrl('uploadedMedia').$mt[$key]['file_name'];
        }
        
        $this->loadControllerConfig();
        
        $sortBy = array(
            'key' => array(
                'mime_show_order', 
                'sort_order'
            ), 
            'dir' => 'asc', 
            'case' => 'i'
        );
        
        $this->customSortArray($mt, $sortBy);
        
        $this->setlastVisitedCategory($taxon, CTAB_MEDIA, $mt);

        return $mt;
    }


    private function getTaxonLiterature($tId)
    {
        if ($refs = $this->getlastVisitedCategory($tId, CTAB_LITERATURE))
            return $refs;
        
        $lt = $this->models->LiteratureTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $tId
            )
        ));
        
        $refs = array();

        foreach ((array) $lt as $key => $val) {
            
            $l = $this->models->Literature->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['literature_id']
                ), 
                'columns' => '*, year(`year`) as `year`,
									concat(
										author_first,
										(
											if(multiple_authors=1,
												\' et al.\',
												if(author_second!=\'\',concat(\' & \',author_second),\'\')
											)
										)
									) as author_full,
									concat(
										if(isnull(`year`)!=1,`year`,\'\'),
										if(isnull(suffix)!=1,suffix,\'\'),
										if(isnull(year_2)!=1,
											concat(
												if(year_separator!=\'-\',
													concat(
														\' \',
														year_separator,
														\' \'
													),
													year_separator
												),
												year_2,
												if(isnull(suffix_2)!=1,
													suffix_2,
													\'\')
												)
												,\'\'
											)
									) as year_full'
            ));
            
            $refs[] = $l[0];
        }
        
        $sortBy = array(
            'key' => 'author_first', 
            'dir' => 'asc', 
            'case' => 'i'
        );
        
        $this->customSortArray($refs, $sortBy);
        
        $this->setlastVisitedCategory($tId, CTAB_LITERATURE, $refs);
        
        return $refs;
    }


    private function getTaxonNames($tId)
    {
        if ($names = $this->getlastVisitedCategory($tId, CTAB_NAMES))
            return $names;
        
        $names = array(
            'synonyms' => $this->getTaxonSynonyms($tId), 
            'common' => $this->getTaxonCommonNames($tId)
        );
        
        $this->setlastVisitedCategory($tId, CTAB_NAMES, $names);
        
        return $names;
    }


    private function getTaxonSynonyms($tId)
    {
        $s = $this->models->Synonym->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $tId
            )
        ));

        if (isset($s)) {
            
            foreach ((array) $s as $key => $val) {
                
                if ($val['lit_ref_id'])
                    $s[$key]['reference'] = $this->getReference($val['lit_ref_id']);
            }
        }
        
        return $s;
    }


    private function getTaxonCommonNames($tId, $languageId = null)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $tId
        );
        
        if (isset($languageId))
            $d['language_id'] = $languageId;
        
        $c = $this->models->Commonname->_get(array(
            'id' => $d, 
            'columns' => 'language_id,commonname,transliteration', 
            'order' => 'show_order,commonname'
        ));
        
        if (isset($c)) {
            
            foreach ((array) $c as $key => $val) {
                
                if (isset($_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()])) {
                    
                    $c[$key]['language_name'] = $_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()];
                }
                else {
                    
                    $ll = $this->models->LabelLanguage->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'label_language_id' => $val['language_id'], 
                            'language_id' => $this->getCurrentLanguageId()
                        ), 
                        'columns' => 'label'
                    ));
                    
                    if ($ll) {
                        
                        $c[$key]['language_name'] = $_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = $ll[0]['label'];
                    }
                    else {
                        
                        $l = $this->models->Language->_get(
                        array(
                            'id' => array(
                                'id' => $val['language_id']
                            ), 
                            'columns' => 'language'
                        ));
                        
                        $c[$key]['language_name'] = $_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = $l[0]['language'];
                    }
                }
            }
        }
        
        return $c;
    }


    private function getReference($id)
    {
        $l = $this->models->Literature->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $id
            ), 
            'columns' => '*, year(`year`) as `year`,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full'
        ));
        
        return $l[0];
    }


    private function getContentCount($id)
    {
        return array(
            CTAB_NAMES => $this->getTaxonSynonymCount($id) + $this->getTaxonCommonnameCount($id), 
            CTAB_MEDIA => $this->getTaxonMediaCount($id), 
            CTAB_LITERATURE => $this->getTaxonLiteratureCount($id)
        );
    }


    private function getTaxonSynonymCount($id)
    {
        $s = $this->models->Synonym->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'count(*) as total'
        ));
        
        return isset($s) ? $s[0]['total'] : 0;
    }


    private function getTaxonCommonnameCount($id)
    {
        $c = $this->models->Commonname->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'count(*) as total'
        ));
        
        return isset($c) ? $c[0]['total'] : 0;
    }


    private function getTaxonMediaCount($id)
    {
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'count(*) as total'
        ));
        
        return isset($mt) ? $mt[0]['total'] : 0;
    }


    private function getTaxonLiteratureCount($id)
    {
        $lt = $this->models->LiteratureTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'count(*) as total'
        ));
        
        return isset($lt) ? $lt[0]['total'] : 0;
    }


    private function getTaxonOverviewImage($id)
    {
        $mt = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'taxon_id' => $id, 
                'overview_image' => 1, 
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        if ($mt) {
			
            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->getCurrentLanguageId(), 
                    'media_id' => $mt[0]['id']
                ), 
                'columns' => 'description'
            ));			
			
            return array('image' => $mt[0]['file_name'],'label' => $mdt[0]['description']);
		} else
            return null;
    }


    private function getLookupList($p)
    {

        $search = isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start'] == '1' : false;
        $getAll = isset($p['get_all']) ? $p['get_all'] == '1' : false;

        if (empty($search) && !$getAll)
            return;

		$regexp = ($matchStartOnly?'^':'').preg_quote($search);
        
        $taxa = $this->models->Taxon->freeQuery(
			array(
				'query' => "
					select _a.id, _a.taxon, _a.rank_id, _a.is_hybrid
					from %PRE%taxa _a 
					left join %PRE%projects_ranks _b on _a.rank_id=_b.id 
					where _a.project_id = ".$this->getCurrentProjectId()."
					and _b.lower_taxon = ".($this->getTaxonType() == 'higher' ? 0 : 1)."
					".($getAll ? "" : "and _a.taxon REGEXP '".$regexp."'")."
					limit ".$this->_lookupListMaxResults
			));

        foreach ((array) $taxa as $key => $val) {
			$taxa[$key]['label'] = $this->formatTaxon(array('taxon'=>$val,'rankpos'=>'post'));
			unset($taxa[$key]['taxon']);
		}

        $this->customSortArray($taxa, array(
            'key' => 'label', 
            'dir' => 'asc', 
            'case' => 'i'
		));

		return $this->makeLookupList(
				$taxa, 
				($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species'),
				'../' . ($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species') . '/taxon.php?id=%s'
			);
    
	}
	

    private function getRelatedEntities($p)
    {
        $tId = isset($p['tId']) ? $p['tId'] : null;
        $vId = isset($p['vId']) ? $p['vId'] : null;
        $includeSelf = isset($p['includeSelf']) ? $p['includeSelf'] : false;
        
        $rel = null;
        
        if ($tId) {
            
            $rel = $this->models->TaxaRelations->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $tId
                )
            ));
            
            if ($includeSelf && isset($tId))
                array_unshift($rel, array(
                    'id' => $tId, 
                    'relation_id' => $tId, 
                    'ref_type' => 'taxon'
                ));
            
            foreach ((array) $rel as $key => $val) {
                
                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                    $rel[$key]['taxon_id'] = $d['taxon_id'];
                }
            }
        }
        else if ($vId) {
            
            $rel = $this->models->VariationRelations->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $vId
                )
            ));
            
            if ($includeSelf && isset($vId))
                array_unshift($rel, array(
                    'id' => $vId, 
                    'relation_id' => $vId, 
                    'ref_type' => 'variation'
                ));
            

            foreach ((array) $rel as $key => $val) {
                
                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                }
            }
        }
        
        return $rel;
    }
	

	private function getActor($id)
	{
		$data=$this->models->Actors->_get(array(
			'id' => array(
				'project_id'=>$this->getCurrentProjectId(),
				'id'=>$id
			)
		));	
		return $data[0];
	}


	private function getNames($id)
	{

        $names=$this->models->Names->freeQuery(
			array(
				'query' => '
					select _a.id, _a.name,_a.uninomial,_a.specific_epithet,_a.name_author,_a.authorship_year,_a.reference,
					_a.reference_id,_a.reference_id,_a.expert,_a.expert_id,_a.organisation,_a.organisation_id, _b.nametype,
					_a.language_id,_c.language,_d.label as language_label
					from %PRE%names _a 
					left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id = _b.project_id
					left join %PRE%languages _c on _a.language_id=_c.id
					left join %PRE%labels_languages _d on _a.language_id=_d.language_id
						and _d.label_language_id='.$this->getDefaultLanguageId().'
					where _a.project_id = '.$this->getCurrentProjectId().'
					and _a.taxon_id='.$id,
				'fieldAsIndex' => 'id'
			)
		);

		$sci=$pref=null;

		foreach((array)$names as $key=>$val) {
			if ($val['nametype']==PREDICATE_VALID_NAME)
				$sci=$key;
			if ($val['nametype']==PREDICATE_PREFERRED_NAME && $val['language_id']==$this->getDefaultLanguageId())
				$pref=$key;
			if (!empty($val['expert_id']))
				$names[$key]['expert']=$this->getActor($val['expert_id']);
			if (!empty($val['organisation_id']))
				$names[$key]['organisation']=$this->getActor($val['organisation_id']);

			$names[$key]['nametype']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
						
		}

		return array(
			'sciId'=>$sci,
			'prefId'=>$pref,
			'list'=>$names
		);
		
	}


	private function getName($p)
	{

		$nameId=isset($p['nameId']) ? $p['nameId'] : null;
		$taxonId=isset($p['taxonId']) ? $p['taxonId'] : null;
		$languageId=isset($p['languageId']) ? $p['languageId'] : $this->getCurrentLanguageId();
		$predicateType=isset($p['predicateType']) ? $p['predicateType'] : null;
	
		if (empty($nameId) && (empty($taxonId) || empty($languageId) || empty($predicateType))) return;

		return $this->models->Names->freeQuery(
			array(
				'query' => "
					select
						_a.taxon_id,
						_a.name,
						_a.uninomial,
						_a.specific_epithet,
						_a.name_author,
						_a.authorship_year,
						_a.reference,
						_a.reference_id,
						_a.expert,
						_a.expert_id,
						_a.organisation,
						_a.organisation_id,
						_b.nametype,
						_a.language_id,
						_c.language,
						_d.label as language_label,
						_e.name as expert_name,
						_f.name as organisation_name,
						_g.label as reference_label,
						_g.author as reference_author,
						_g.date as reference_date
					from %PRE%names _a

					left join %PRE%name_types _b 
						on _a.type_id=_b.id 
						and _a.project_id = _b.project_id
						
					left join %PRE%languages _c 
						on _a.language_id=_c.id
						
					left join %PRE%labels_languages _d 
						on _a.language_id=_d.language_id
						and _a.project_id=_d.project_id 
						and _d.label_language_id=".$languageId."

					left join %PRE%actors _e
						on _a.expert_id = _e.id 
						and _a.project_id=_e.project_id

					left join %PRE%actors _f
						on _a.organisation_id = _f.id 
						and _a.project_id=_f.project_id

					left join %PRE%literature2 _g
						on _a.reference_id = _g.id 
						and _a.project_id=_g.project_id
			
					where _a.project_id = ".$this->getCurrentProjectId().
					(!empty($nameId) ? " and _a.id=".$nameId : "").				
					(!empty($taxonId) ? " and _a.taxon_id=".$taxonId : "").				
					(!empty($predicateType) ? " and _b.nametype=".$predicateType : "")
			)
		);
		
	}
	

	private function getDNABarcodes($id)
	{
		return $this->models->DnaBarcodes->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'taxon_id' => $id
				)
			));		
	}


	private function getPresence($id)
	{
		$data=$this->models->PresenceTaxa->freeQuery(
			"select
				ifnull(_a.is_indigeneous,0) as is_indigeneous,
				_a.presence_id,
				_a.presence82_id,
				_a.reference_id,
				_b.label as presence_label,
				_b.information as presence_information,
				_b.information_title as presence_information_title,
				_b.index_label as presence_index_label,
				_c.label as presence82_label,
				_d.label as habitat_label,
				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date
			from %PRE%presence_taxa _a
			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getCurrentLanguageId()."
			left join %PRE%presence_labels _c
				on _a.presence82_id = _c.presence_id 
				and _a.project_id=_c.project_id 
				and _c.language_id=".$this->getCurrentLanguageId()."
			left join %PRE%habitat_labels _d
				on _a.habitat_id = _d.habitat_id 
				and _a.project_id=_d.project_id 
				and _d.language_id=".$this->getCurrentLanguageId()."
			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id
			left join %PRE%actors _f
				on _a.actor_org_id = _f.id 
				and _a.project_id=_f.project_id
			left join %PRE%literature2 _g
				on _a.reference_id = _g.id 
				and _a.project_id=_g.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			and _a.taxon_id =".$id
		);	
		
		return $data[0];
	}

	/* private other */
	
    private function _indexAction ()
    {
        if (!$this->rHasVal('id')) {

            $id = $this->getFirstTaxonId();

        }
        else {
            
            $id = $this->requestData['id'];
        }
        
        $this->setStoreHistory(false);
		
        $this->redirect('taxon.php?id=' . $id);
    }


	public function findByNameAction()
	{
	
		//RewriteEngine on
		//RewriteRule ^p/([\d]+)/([^/\.]+)/?$ /linnaeus_ng/app/views/species/find_by_name.php?epi=$1&name=$2
		
		$base = $this->baseUrl.$this->appName.'/views/'.$this->controllerBaseName.'/';
		
		$name = trim($this->requestData['name']);
		
		if (empty($name))
			$this->redirect($base.'index.php');
		
		// try literal
		$t = $this->models->Taxon->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon' => $name
			), 
			'columns' => 'id'
		));

		if ($t)
			$this->redirect($base.'taxon.php?epi='.$this->requestData['epi'].'&id='.$t[0]['id']);
	
	}
	

}
