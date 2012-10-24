<?php

include_once ('Controller.php');

class SpeciesController extends Controller
{
    
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
		'literature_taxon'
    );
    
	public $controllerPublicName = 'Species module';

	public $cssToLoad = array(
		'basics.css',
		'species.css',
		'prettyPhoto/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	); 
				
	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'prettyPhoto/jquery.prettyPhoto.js',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
			),
			'IE' => array(
			)
		);


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();
		
    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }

    /**
     * Index of the species module
     *
     * @access    public
     */
    public function indexAction ()
    {

		$this->setPageName(_('Species module index'));

		$this->setTaxonType('lower');

		$this->setControllerBaseName();

		$this->_indexAction();

	}

    /**
     * Index of the higher taxa
     *
     * @access    public
     */
	public function higherSpeciesIndexAction()
	{

		$this->setPageName(_('Higher taxa index'));

		$this->setTaxonType('higher');

		$this->setControllerBaseName();

		$this->_indexAction();

	}


    /**
     * Show a taxon
     *
     * @access    public
     */
    public function taxonAction ()
    {

        if ($this->rHasId()) {
		
	        // get taxon
            $taxon = $this->getTaxonById($this->requestData['id']);

			$this->setTaxonType($taxon['lower_taxon']==1 ? 'lower' : 'higher');

			$this->setControllerBaseName();
			
			if (isset($this->requestData['lan'])) $this->setCurrentLanguageId($this->requestData['lan']);

			// get categories
			$categories = $this->getCategories(
				$this->requestData['id'],
				$this->isLoggedInAdmin(),
				$this->isLoggedInAdmin()
			);

			// determine the page_id the page will open in
			$activeCategory = $this->rHasVal('cat') ? $this->requestData['cat'] : $categories['defaultCategory'];

			// setting the css classnames
			foreach((array)$categories['categories'] as $key => $val) {
				$c = array('category');
				if ($val['id']==$activeCategory) {
				    $c[] = 'category-active';
				}
				if ($key == 0) {
				    $c[] = 'category-first';
				} else if ($key == count($categories['categories']) - 1) {
				    $c[] ='category-last';
				}
				if ($val['is_empty']==1 && $val['id']!=$activeCategory) {
					$c[] ='category-no-content';
				}
				$categories['categories'][$key]['className'] = implode(' ', $c);
			}

			$content = $this->getTaxonContent(
				$taxon['id'],
				$activeCategory,
				$this->isLoggedInAdmin()
			);

			$content = $this->matchGlossaryTerms($content);
			$content = $this->matchHotwords($content);

			if ($taxon['lower_taxon']==1) {

				$this->setPageName(sprintf(_('Species module: "%s" (%s)'),$taxon['label'],$this->getCategoryName($activeCategory)));

				$this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);


			} else {

				$this->setPageName(sprintf(_('Higher taxa: "%s" (%s)'),$taxon['label'],$this->getCategoryName($activeCategory)));

			}

			if (isset($taxon)) {

				$this->smarty->assign('overviewImage', $this->getTaxonOverviewImage($taxon['id']));

				$this->smarty->assign('taxon', $taxon);

				$this->smarty->assign('content', $content);

				$this->smarty->assign('contentCount', $this->getContentCount($taxon['id']));

				$this->smarty->assign('adjacentItems', $this->getAdjacentItems($taxon['id']));

			}

			$this->smarty->assign('categories', $categories['categories']);
			
			$this->smarty->assign('activeCategory', $activeCategory);

			$this->smarty->assign('headerTitles',
				array(
					'title' =>
						$taxon['label'].
						($taxon['is_hybrid']=='1' ?
							'<span class="hybrid-marker" title="'._('hybrid').'">'.
							(isset($_SESSION['app']['project']['hybrid_marker']) ? $_SESSION['app']['project']['hybrid_marker'] : 'X').
							'</span>'
							: ''
						)
					)
			);
			
			//$this->setLastViewedTaxonIdForTheBenefitOfTheMapkey($taxon['id']);

		} else {

			$this->addError(_('No taxon ID specified.'));

			$this->setLastViewedTaxonIdForTheBenefitOfTheMapkey(null);

		}

        $this->printPage();
  
    }
	
	public function ajaxInterfaceAction()
	{

		if (!$this->rHasVal('action')) $this->smarty->assign('returnText','error');
		
		if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);

        } else
		if (!$this->rHasVal('action') || !$this->rHasId()) {

			$this->smarty->assign('returnText','error');
		
		} else
		if ($this->rHasVal('action','get_media_info')) {

			$this->smarty->assign('returnText',json_encode($this->getTaxonMedia(null,$this->requestData['id'])));
		
		}

		$this->allowEditPageOverlay = false;

        $this->printPage();
	
	}

	private function getFirstTaxonId()
	{
	
		$taxa = $this->buildTaxonTree();
		
		if (empty($taxa)) return null;

		$d = current($taxa);
		
		if ($this->getTaxonType() == 'higher') {
				
			return $d['id'];
			
		} else {
		
			while($d['lower_taxon']==0) {
			
				$d = next($taxa);
			
			}

			return $d['id'];
		
		}
	
	}


    private function _indexAction ()
    {

		if (!$this->rHasVal('id')) {

			$id = $this->getFirstTaxonId();

		} else {

			$id = $this->requestData['id'];
				
		}

		$this->setStoreHistory(false);

		$this->redirect('taxon.php?id='.$id);
  
    }

	public function setTaxonType($type)
	{

		$_SESSION['app']['user']['species']['type'] = ($type=='higher') ? 'higher' : 'lower';
	
	}

	private function getTaxonType()
	{

		return isset($_SESSION['app']['user']['species']['type']) ? $_SESSION['app']['user']['species']['type'] : 'lower';
	
	}

	private function setControllerBaseName ()
	{

		if ($this->getTaxonType() == 'higher')
			$this->controllerBaseName = 'highertaxa';
		else
			$this->controllerBaseName = 'species';
	
	}
	
	private function getCategories($taxon=null,$allowUnpublished=false,$forcelookup=false)
	{
	
		$forcelookup = true;
	
		if (!isset($_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()]) || $forcelookup) {

			// get the defined categories (just the page definitions, no content yet)
			$tp = $this->models->PageTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'order' => 'show_order',
					'fieldAsIndex' => 'page_id'
				)
			);

			foreach ((array) $tp as $key => $val) {

				$tp[$key]['title'] = $this->getCategoryName($val['id']);
		
				if ($val['def_page'] == 1) $_SESSION['app']['user']['species']['defaultCategory'] = $val['id'];
			
			}

			$_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()] = $tp;

		}

		if ($taxon) {

			$defCat = 'classification';
		
			$d = array();

			foreach((array)$_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()] as $key => $val) {

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
					)
				);
				
				if (($ct[0]['publish']=='1' || $allowUnpublished) && strlen($ct[0]['content'])>0) $val['is_empty'] = 0;
					
				$d[] = $val;

				if ($ct[0]['page_id']==$_SESSION['app']['user']['species']['defaultCategory']) // && ($ct[0]['publish']=='1' || $allowUnpublished)) 
					$defCat = $_SESSION['app']['user']['species']['defaultCategory'];

			}


			$m = $this->getTaxonMedia($taxon,null);

			$stdCats[] = array(
					'id' => 'media',
					'title' => _('Media'),
					'is_empty' => (count((array)$m)>0 ? 0 : 1)
			);

			$stdCats[] = array(
				'id' => 'classification',
				'title' => _('Classification'),
				'is_empty' => 0
			);

			$n = $this->getTaxonNames($taxon);

			$stdCats[] = array(
				'id' => 'names',
				'title' => _('Names'),
				'is_empty' => (count((array)$n['synonyms'])==0 && count((array)$n['common'])==0 ? 1 : 0)
			);
			
			if ($this->doesProjectHaveModule(MODCODE_LITERATURE)) {
		
				$l = $this->getTaxonLiterature($taxon);
//print_r($l); die();				
				$stdCats[] = array(
					'id' => 'literature',
					'title' => _('Literature'),
					'is_empty' => (count((array)$l)>0 ? 0 : 1)
				);

			}

			$d = array_merge($d,$stdCats);
//print_r($d); die();
			
			return array(
				'categories' => $d,
				'defaultCategory' => $defCat
			);

		}

		
		return array(
			'categories' => $_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()],
			'defaultCategory' => $_SESSION['app']['user']['species']['defaultCategory']
		);

	}

	private function getCategoryName($id)
	{

		if (!isset($_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id])) {

			if (is_numeric($id)) {

				$tpt = $this->models->PageTaxonTitle->_get(
					array('id'=>array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(), 
						'page_id' => $id
					),
					'columns'=>'title'));
					
				if (empty($tpt[0]['title'])) {

					$tpt = $this->models->PageTaxon->_get(
						array('id'=>array(
							'project_id' => $this->getCurrentProjectId(), 
							'id' => $id
						),
						'columns'=>'page as title'));

				}
		
				$_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = $tpt[0]['title'];

			} else {

				$_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = ucwords($id);

			}

		}

		return $_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id];

	}
	
	private function getTaxonContent($taxon,$category,$allowUnpublished=false)
	{

		switch($category) {

			case 'media':
				return $this->getTaxonMedia($taxon);
				break;

			case 'classification':
				return $this->getTaxonClassification($taxon);
				break;

			case 'literature':
				return $this->getTaxonLiterature($taxon);
				break;

			case 'names':
				return $this->getTaxonNames($taxon);
				break;

			default:
				$d = array(
						'taxon_id' => $taxon,
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(),
						'page_id' => $category
					);

				if (!$allowUnpublished) $d['publish'] = '1';
			
				$ct = $this->models->ContentTaxon->_get(
					array(
						'id' => $d,
						'columns' => 'content'
					)
				);

				return isset($ct) ? $ct[0]['content'] : null;

		}
	
	}
	
	private function getTaxonMedia($taxon=null,$id=null)
	{

		if ($mt = $this->getTaxonCategoryLastVisited($taxon, 'media')) return $mt;
		
		$d = array('project_id' => $this->getCurrentProjectId());
		
		if (isset($taxon)) $d['taxon_id'] = $taxon;
		if (isset($id)) $d['id'] = $id;
		$d['overview_image'] = '0';

		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => $d,
				'columns' => 'id,file_name,thumb_name,original_name,mime_type,sort_order,overview_image,substring(mime_type,1,locate(\'/\',mime_type)-1) as mime',
				'order' => 'mime, sort_order'
			)
		);

		$this->loadControllerConfig('species');

		foreach((array)$mt as $key => $val) {

			$mdt = $this->models->MediaDescriptionsTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(),
						'media_id' => $val['id']
					),
					'columns' => 'description'
				)
			);

			$mt[$key]['description'] = $mdt ? $this->matchHotwords($this->matchGlossaryTerms($mdt[0]['description'])) : null;

			$t = isset($this->controllerSettings['mime_types'][$val['mime_type']]) ?
					$this->controllerSettings['mime_types'][$val['mime_type']] :
					null;

					
			$mt[$key]['category'] = isset($t['type']) ? $t['type'] : 'other';
			$mt[$key]['category_label'] = isset($t['label']) ? $t['label'] : 'Other';
			$mt[$key]['mime_show_order'] = isset($t['type']) ? $this->controllerSettings['mime_show_order'][$t['type']] : 99;
			$mt[$key]['full_path'] = $_SESSION['app']['project']['urls']['uploadedMedia'].$mt[$key]['file_name'];

		}

		$this->loadControllerConfig();

		$sortBy = array(
			'key' => array('mime_show_order','sort_order'),
			'dir' => 'asc',
			'case' => 'i'
		);

		$this->customSortArray($mt, $sortBy);
		
		$this->setlastVisited($taxon, 'media', $mt);

		return $mt;

	}

	private function getTaxonLiterature($taxon)
	{

		if ($refs = $this->getTaxonCategoryLastVisited($taxon, 'literature')) return $refs;
		
		$lt =  $this->models->LiteratureTaxon->_get(
			array(
				'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $taxon
					)
				)
			);

		$refs = array();
		
		foreach((array)$lt as $key => $val) {

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
									) as author_full'
				)
			);
			
			$refs[] = $l[0];

		}

		$sortBy = array(
			'key' => 'author_first', 
			'dir' => 'asc', 
			'case' => 'i'
		);

		$this->customSortArray($refs, $sortBy);

		$this->setlastVisited($taxon, 'literature', $refs);
		
		return $refs;

	}

	private function getTaxonNames($taxon)
	{

		if ($names = $this->getTaxonCategoryLastVisited($taxon, 'names')) return $names;
		
		$names = array(
			'synonyms' => $this->getTaxonSynonyms($taxon),
			'common' => $this->getTaxonCommonNames($taxon)
		);
		
		$this->setlastVisited($taxon, 'names', $names);
		
		return $names;

	}

	private function getTaxonSynonyms($taxon)
	{

		$s = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon
				)
			)
		);

		if (isset($s)) {

			foreach((array)$s as $key => $val) {

				if ($val['lit_ref_id']) $s[$key]['reference'] = $this->getReference($val['lit_ref_id']);

			}

		}

		return $s;
		
	}
	
	private function getTaxonCommonNames($taxon)
	{

		$c = $this->models->Commonname->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon
				),
				'columns' => 'language_id,commonname,transliteration',
				'order' => 'show_order,commonname'
			)
		);

		if (isset($c)) {

			foreach((array)$c as $key => $val) {
			
				if (isset($_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()])) {

					$c[$key]['language_name'] = $_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()];

				} else {

					$ll = $this->models->LabelLanguage->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'label_language_id' => $val['language_id'],
								'language_id' => $this->getCurrentLanguageId(),
							),
							'columns' => 'label'
						)
					);
	
					if ($ll) {

						$c[$key]['language_name'] = 
							$_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = 
							$ll[0]['label'];

					} else {

						$l = $this->models->Language->_get(
							array(
								'id' => array(
									'id' =>  $val['language_id']
								),
								'columns' => 'language'
							)
						);

						$c[$key]['language_name'] = 
							$_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = 
							$l[0]['language'];
					
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
			)
		);
		
		return $l[0];

	}

	private function getContentCount($id)
	{
	
		return array(
			'names' => $this->getTaxonSynonymCount($id) + $this->getTaxonCommonnameCount($id),
			'media' => $this->getTaxonMediaCount($id),
			'literature' => $this->getTaxonLiteratureCount($id)
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
			)
		);

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
			)
		);

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
			)
		);

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
			)
		);
	
		return isset($lt) ? $lt[0]['total'] : 0;

	}

	private function getAdjacentItems($id)
	{

		$taxa = $this->buildTaxonTree();

		$d = array();
		
		while (list($key, $val) = each($taxa)) {

			if (
				($this->getTaxonType() == 'higher' && $val['lower_taxon']==0) ||
				($this->getTaxonType() == 'lower' && $val['lower_taxon']==1)
				) {
				$d[$key] = $val;
			}
			
		}

		$taxa = $d;
		
		if ($taxa && !empty($taxa)) {
			
			reset($taxa);
			
			$prev = $next = null;
	
			while (list($key, $val) = each($taxa)) {
			
				if ($key==$id) {
	
					$next = current($taxa); // current = next because the pointer has already shifted forward
	
					return array(
						'prev' => isset($prev) ? array('id' => $prev['id'],'label' => $prev['taxon']) : null,
						'next' => isset($next) ? array('id' => $next['id'],'label' => $next['taxon']) : null
					);
	
				}
	
				$prev = $val;
	
			}
		}
		
		return null;

	}
	
	private function getTaxonOverviewImage($id)
	{

		$mt = $this->models->MediaTaxon->_get(
			array('id' =>
				array(
					'taxon_id' => $id,
					'overview_image' => 1,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		if ($mt)
			return $mt[0]['file_name'];
		else
			return null;
	
	}


	private function getLookupList($p)
	{

		$search = isset($p['search']) ? $p['search'] : null;
		$matchStartOnly = isset($p['match_start']) ? $p['match_start']=='1' : false;
		$getAll = isset($p['get_all']) ? $p['get_all']=='1' : false;

		$search = str_replace(array('/','\\'),'',$search);

		if (empty($search) && !$getAll) return;
		
		if ($matchStartOnly)
			$regexp = '/^'.preg_quote($search).'/i';
		else
			$regexp = '/'.preg_quote($search).'/i';

		$l = array();

		$taxa = $this->buildTaxonTree();
				
		foreach((array)$taxa as $key => $val) {
		
			if (
				($getAll || preg_match($regexp,$val['taxon']) == 1) &&
				($this->getTaxonType() == 'higher' ? $val['lower_taxon']==0 : $val['lower_taxon']==1)
				)
				$l[] = array(
					'id' => $val['id'],
					'label' => 	$t[$key]['label'] = $this->formatSpeciesEtcNames($val['taxon'],$val['rank_id'])
				);

		}


		
		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species'),
				'../'.($this->getTaxonType() == 'higher' ? 'highertaxa' : 'species').'/taxon.php?id=%s'
			)
		);
		
	}

	private function setLastViewedTaxonIdForTheBenefitOfTheMapkey($id)
	{
	
		if (!is_null($id)) {
		
			$_SESSION['app']['user']['species']['lastTaxon'] = $id;
			
			unset($_SESSION['app']['user']['mapkey']['state']);
		
		} else {

			unset($_SESSION['app']['user']['species']['lastTaxon']);
			
		}
	
	}


	private function getTaxonCategoryLastVisited ($taxon, $category) {
		if (isset($_SESSION['app']['user']['species']['last_visited'][$taxon][$category])) {
			return $_SESSION['app']['user']['species']['last_visited'][$taxon][$category];
		}
	
		if (isset($_SESSION['app']['user']['species']['last_visited'])) {
	
			$storedTaxon = key($_SESSION['app']['user']['species']['last_visited']);
			if ($storedTaxon != $taxon) {
				unset($_SESSION['app']['user']['species']['last_visited'][$storedTaxon]);
			}
		}
	
		return false;
	}
	
	private function setlastVisited ($taxon, $category, $d) {
		$_SESSION['app']['user']['species']['last_visited'][$taxon][$category] = $d;
	}
	
	
}

