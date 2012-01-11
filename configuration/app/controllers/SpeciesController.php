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
		'colorbox/colorbox.css',
		'lookup.css'
	); //'key-tree.css'

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'colorbox/jquery.colorbox.js',
				'lookup.js'
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
		
		$this->checkForProjectId();

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

		$this->showLowerTaxon = true;
		
		unset($_SESSION['app']['user']['search']['hasSearchResults']);

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

		$this->showLowerTaxon = false;

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

			// get categories
			$categories = $this->getCategories($this->requestData['id']);

			// determine the page_id the page will open in
			$activeCategory = $this->rHasVal('cat') ? $this->requestData['cat'] : $categories['defaultCategory'];

			$content = $this->getTaxonContent($taxon['id'],$activeCategory);


			if ($taxon['lower_taxon']==1) {
			
				$this->setPageName(sprintf(_('Species module: "%s" (%s)'),$taxon['taxon'],$this->getCategoryName($activeCategory)));

			} else {

				$this->setPageName(sprintf(_('Higher taxa: "%s" (%s)'),$taxon['taxon'],$this->getCategoryName($activeCategory)));

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
						$taxon['taxon'].
						($taxon['is_hybrid']=='1' ?
							'<span class="hybrid-marker" title="'._('hybrid').'">'.$_SESSION['app']['project']['hybrid_marker'].'</span>'
							: ''
						)
					)
			);

		} else {

			$this->addError(_('No taxon ID specified.'));
		
		}

        $this->printPage();
  
    }

	public function ajaxInterfaceAction()
	{

		if (!$this->rHasVal('action')) $this->smarty->assign('returnText','error');
		
		if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        } else
		if (!$this->rHasVal('action') || !$this->rHasId()) {

			$this->smarty->assign('returnText','error');
		
		} else
		if ($this->rHasVal('action','get_media_info')) {

			$this->smarty->assign('returnText',json_encode($this->getTaxonMedia(null,$this->requestData['id'])));
		
		}

        $this->printPage();
	
	}

    private function _indexAction ()
    {

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));

		// get taxa
		$taxa = $this->getTreeList();

		$d = current($taxa);

		$this->redirect('taxon.php?id='.(isset($d['id']) ? $d['id'] : null));

		/*
		// max taxa to show per page
		$taxaPerPage = $this->controllerSettings['speciesPerPage'];

		$pagination = $this->getPagination($taxa,$taxaPerPage);

		if (isset($pagination['items'])) $this->smarty->assign('taxa', $pagination['items']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);

		$this->smarty->assign('nextStart', $pagination['nextStart']);

        $this->printPage();
		*/
  
    }

	public function setTaxonType ($type)
	{

		$_SESSION['app']['user']['species']['type'] = ($type=='higher') ? 'higher' : 'lower';
	
	}

	private function getTaxonType ()
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

	private function getCategories($taxon=null,$forcelookup=false)
	{

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
		
				// for each category, get the category title
				$tpt = $this->models->PageTaxonTitle->_get(
					array('id'=>array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(), 
						'page_id' => $val['id']
					),
					'columns'=>'title'));
		
				$tp[$key]['title'] = $tpt[0]['title'];
		
				if ($val['def_page'] == 1) $_SESSION['app']['user']['species']['defaultCategory'] = $val['id'];
			
			}
			
			$_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()] = $tp;

		}

		if ($taxon) {
		
			$defCat = 'classification';
		
			$d = null;

			foreach((array)$_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()] as $key => $val) {

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

				if ($ct[0]['publish']=='1') $d[] = $val;

				if ($ct[0]['page_id']==$_SESSION['app']['user']['species']['defaultCategory'] && $ct[0]['publish']=='1') 
					$defCat = $_SESSION['app']['user']['species']['defaultCategory'];

			}

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
		
				$_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = $tpt[0]['title'];

			} else {

				$_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = ucwords($id);

			}

		}

		return $_SESSION['app']['user']['species']['catnames'][$this->getCurrentLanguageId()][$id];

	}
	
	private function getTaxonContent($taxon,$category)
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
				$ct = $this->models->ContentTaxon->_get(
					array(
						'id' => array(
							'taxon_id' => $taxon,
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->getCurrentLanguageId(),
							'page_id' => $category,
							'publish' => '1'
						),
						'columns' => 'content'
					)
				);
				
				return isset($ct) ? $this->matchGlossaryTerms($ct[0]['content']) : null;

		}
	
	}

	private function getTaxonMedia($taxon=null,$id=null)
	{

		$d = array('project_id' => $this->getCurrentProjectId());
		
		if (isset($taxon)) $d['taxon_id'] = $taxon;
		if (isset($id)) $d['id'] = $id;

		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => $d,
				'order' => 'mime_type, file_name'
			)
		);

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

			$mt[$key]['description'] = $mdt ? $this->matchGlossaryTerms($mdt[0]['description']) : null;

			$t = isset($this->controllerSettings['mime_types'][$val['mime_type']]) ?
					$this->controllerSettings['mime_types'][$val['mime_type']] :
					null;

			$mt[$key]['category'] = isset($t['type']) ? $t['type'] : 'other';
			$mt[$key]['category_label'] = isset($t['label']) ? $t['label'] : 'Other';
			$mt[$key]['mime_show_order'] = isset($t['type']) ? $this->controllerSettings['mime_show_order'][$t['type']] : 99;
			$mt[$key]['full_path'] = $_SESSION['app']['project']['urls']['project_media'].$mt[$key]['file_name'];


		}

		$sortBy = array(
			'key' => 'mime_show_order', 
			'dir' => 'asc', 
			'case' => 'i'
		);

		$this->customSortArray($mt, $sortBy);

		return $mt;

	}

	private function getTaxonLiterature($taxon)
	{

		$lt =  $this->models->LiteratureTaxon->_get(
			array(
				'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $taxon
					)
				)
			);

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

		return $refs;

	}

	private function getTaxonNames($taxon)
	{

		return array(
			'synonyms' => $this->getTaxonSynonyms($taxon),
			'common' => $this->getTaxonCommonNames($taxon)
		);

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
				'order' => 'show_order'
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
	
					if ($ll) $c[$key]['language_name'] = 
						$_SESSION['app']['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = 
						$ll[0]['label'];

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

		$this->showLowerTaxon = ($this->getTaxonType() == 'lower');

		$this->getTaxonTree(array('forceLookup'=>true));

		$taxa = $this->getTreeList();

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


	private function getLookupList($search)
	{

		$search = str_replace(array('/','\\'),'',$search);

		if (empty($search)) return;
		
		$regexp = '/'.preg_quote($search).'/i';

		$l = array();

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->treeList)));
		$taxa = $this->getTreeList();
				
		foreach((array)$taxa as $key => $val) {
		
			if (preg_match($regexp,$val['taxon']) == 1)
				$l[] = array(
					'id' => $val['id'],
					'label' => $val['taxon']
				);

		}
		
		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				'species',
				'../species/taxon.php?id=%s',
				true
			)
		);
		
	}

}

