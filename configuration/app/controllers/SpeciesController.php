<?php

/*

lijst met species
bladen
classificatie (boom)

glossary!

*/

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
    
    private $_treeList;
    private $_showLowerTaxon = true;

	public $controllerPublicName = 'Species module';

	public $cssToLoad = array(
		'basics.css',
		'species.css',
		'colorbox/colorbox.css'
	); //'key-tree.css'

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'colorbox/jquery.colorbox.js'
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

		$this->_showLowerTaxon = true;
		
		unset($_SESSION['user']['search']['hasSearchResults']);

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

		$this->_showLowerTaxon = false;

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

				$this->smarty->assign('taxon', $taxon);

				$this->smarty->assign('content', $content);

				$this->smarty->assign('contentCount', $this->getContentCount($taxon['id']));

			}

			$this->smarty->assign('categories', $categories['categories']);
			
			$this->smarty->assign('activeCategory', $activeCategory);

			$this->smarty->assign('headerTitles',array('title' => $taxon['taxon']));

		} else {

			$this->addError(_('No taxon ID specified.'));
		
		}

        $this->printPage();
  
    }

    private function _indexAction ()
    {

		$this->getTaxonTree(array('includeOrphans' => false,'forceLookup' => !isset($this->_treeList)));

		// get taxa
		$taxa = $this->getTreeList();

		// max taxa to show per page
		$taxaPerPage = $this->controllerSettings['speciesPerPage'];

		// determine index of the first taxon to show
		$start = $this->rHasVal('start') ? $this->requestData['start'] : 0;

		//determine index of the first taxon to show on the previous page (if any)
		$prevStart = $start==0 ? -1 : (($start-$taxaPerPage<1) ? 0 : ($start-$taxaPerPage));

		//determine index of the first taxon to show on the next page (if any)
		$nextStart = ($start+$taxaPerPage>=count((array)$taxa)) ? -1 : ($start+$taxaPerPage);

		// slice out only the taxa we need (faster than looping the entire thing in smarty)
		$taxa = array_slice($taxa,$start,$taxaPerPage);

		if (isset($taxa)) $this->smarty->assign('taxa', $taxa);

		$this->smarty->assign('prevStart', $prevStart);

		$this->smarty->assign('nextStart', $nextStart);

        $this->printPage();
  
    }

	private function setTaxonType ($type)
	{

		$_SESSION['user']['species']['type'] = ($type=='higher') ? 'higher' : 'lower';
	
	}

	private function getTaxonType ()
	{

		return isset($_SESSION['user']['species']['type']) ? $_SESSION['user']['species']['type'] : 'lower';
	
	}

	private function setControllerBaseName ()
	{

		if ($this->getTaxonType() == 'higher')
			$this->controllerBaseName = 'highertaxa';
		else
			$this->controllerBaseName = 'species';
	
	}

	private function getTreeList ()
	{

		foreach((array)$this->_treeList as $key => $val) {

			if ($this->_showLowerTaxon) {
			
				if ($val['lower_taxon']=='1')  $d[$key] = $val;
			
			} else {

				if ($val['lower_taxon']=='0')  $d[$key] = $val;

				if ($val['lower_taxon']=='1')  break;

			}

		}	

		return $d;
	
	}

    private function getTaxonTree($params=null) 
    {

		if (
			(isset($params['forceLookup']) && $params['forceLookup']==true) ||
			!isset($_SESSION['user']['species']['tree']) ||
			!isset($_SESSION['user']['species']['treeList']) ||
			$this->didActiveLanguageChange()
		) {

			$_SESSION['user']['species']['tree'] = $this->_getTaxonTree($params);

			$_SESSION['user']['species']['treeList'] = $this->_treeList;

		} else {

			$this->_treeList = $_SESSION['user']['species']['treeList'];

		}

		return $_SESSION['user']['species']['tree'];
	
	}

    private function _getTaxonTree($params) 
    {

		// the parent_id to start with
		$pId = isset($params['pId']) ? $params['pId'] : null;
		// the current level of depth in the tree
		$level = isset($params['level']) ? $params['level'] : 0;
		// a specific rank_id to stop the recursion; taxa below this rank are omitted from the tree
		$stopAtRankId = isset($params['stopAtRankId']) ? $params['stopAtRankId'] : null;
		// taxa without a parent_id that are not of the uppermost rank are orphans; these can be excluded from the tree
		$includeOrphans = isset($params['includeOrphans']) ? $params['includeOrphans'] : false;
		// force lookup
		$forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : null;

		// get all ranks defined within the project	
		$d = $this->getProjectRanks(array('idsAsIndex' => true,'forceLookup' => $forceLookup));
		$pr = $d['ranks'];

		// $this->_treeList an additional non-recursive list of taxa
		if ($level==0) unset($this->_treeList);

		// setting the parameters for the taxon search
		$id['project_id'] = $this->getCurrentProjectId();

		if ($pId === null) {

			$id['parent_id is'] = $pId;

		} else {

			$id['parent_id'] = $pId;

		}

		// decide whether or not to include orphans, taxa with no parent_id that are not the topmost taxon (which is usually 'kingdom')
		if ($pId === null && $includeOrphans === false) {

			$id['rank_id'] = $d['topRankId'];

		}

		// get the child taxa of the current parent
        $t = $this->models->Taxon->_get(
				array(
					'id' =>  $id,
					'order' => 'taxon_order'
				)
			);

        foreach((array)$t as $key => $val) {

			// for each taxon, look whether they belong to the lower taxa...
			$val['lower_taxon'] = $pr[$val['rank_id']]['lower_taxon'];
	
			// ...and can be the endpoint of the key
			$val['keypath_endpoint'] = $pr[$val['rank_id']]['keypath_endpoint'];

			// level is effectively the recursive depth of the taxon within the tree
			$val['level'] = $level;

			// count taxa on the same level
			$val['sibling_count'] = count((array)$t);

			// sibling_pos reflects the position amongst taxa on the same level
			$val['sibling_pos'] = ($key==0 ? 'first' : ($key==count((array)$t)-1 ? 'last' : '-' ));

			// get rank label
			$val['rank'] = $pr[$val['rank_id']]['labels'][$this->getCurrentLanguageId()];

			// fill the treelist (which is a global var)
            $this->_treeList[$val['id']] = $val;

			$t[$key]['level'] = $level;
			
			// and call the next recursion for each of the children
			if (!isset($stopAtRankId) || (isset($stopAtRankId) && $stopAtRankId!=$val['rank_id'])) {

				$children = $this->_getTaxonTree(
					array(
						'pId' => $val['id'],
						'level' => $level+1,
						'stopAtRankId' => $stopAtRankId
					)
				);

	            $t[$key]['children_count'] = 
					$this->_treeList[$val['id']]['children_count'] = 
					isset($children) ? count((array)$children) : 0;
				
				$t[$key]['children'] = $children;

			}

        }

        return $t;

    }

	private function getProjectRanks($params=false)
	{

		$includeLanguageLabels = isset($params['includeLanguageLabels']) ? $params['includeLanguageLabels'] : true;
		$lowerTaxonOnly = isset($params['lowerTaxonOnly']) ? $params['lowerTaxonOnly'] : false;
		$forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : $this->didActiveLanguageChange();
		$keypathEndpoint = isset($params['keypathEndpoint']) ? $params['keypathEndpoint'] : false;
		$idsAsIndex = isset($params['idsAsIndex']) ? $params['idsAsIndex'] : false;

		if (!$forceLookup) $forceLookup = !isset($_SESSION['user']['species']['ranks']['projectRanks']);

		if (!$forceLookup) {

			if (
				!isset($_SESSION['user']['species']['ranks']['includeLanguageLabels']) || 
				$_SESSION['user']['species']['ranks']['includeLanguageLabels']!=$includeLanguageLabels ||
				!isset($_SESSION['user']['species']['ranks']['lowerTaxonOnly']) || 
				$_SESSION['user']['species']['ranks']['lowerTaxonOnly']!=$lowerTaxonOnly ||
				!isset($_SESSION['user']['species']['ranks']['keypathEndpoint']) || 
				$_SESSION['user']['species']['ranks']['keypathEndpoint']!=$keypathEndpoint ||
				!isset($_SESSION['user']['species']['ranks']['idsAsIndex']) || 
				$_SESSION['user']['species']['ranks']['idsAsIndex']!=$idsAsIndex
				)

				$forceLookup = true;

		}

		$_SESSION['user']['species']['ranks']['includeLanguageLabels'] = $includeLanguageLabels;
		$_SESSION['user']['species']['ranks']['lowerTaxonOnly'] = $lowerTaxonOnly;
		$_SESSION['user']['species']['ranks']['keypathEndpoint'] = $keypathEndpoint;
		$_SESSION['user']['species']['ranks']['idsAsIndex'] = $idsAsIndex;

		if ($forceLookup) {

			if ($keypathEndpoint)
				$d = array('project_id' => $this->getCurrentProjectId(),'keypath_endpoint' => 1);
			elseif ($lowerTaxonOnly)
				$d = array('project_id' => $this->getCurrentProjectId(),'lower_taxon' => 1);
			else
				$d = array('project_id' => $this->getCurrentProjectId());

			$p = array('id' => $d,'order' => 'rank_id');
			
			if ($idsAsIndex) {

				$p['fieldAsIndex'] = 'id';

			}

			$pr = $this->models->ProjectRank->_get($p);
			
			$topRankId = null;

			foreach((array)$pr as $rankkey => $rank) {
			
				if ($topRankId==null) $topRankId = $rankkey;
	
				$r = $this->models->Rank->_get(array('id' => $rank['rank_id']));
	
				$pr[$rankkey]['rank'] = $r['rank'];
	
				$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];

				if ($includeLanguageLabels) {
	
					$lpr = $this->models->LabelProjectRank->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'project_rank_id' => $rank['id'],
								'language_id' => $this->getCurrentLanguageId()
							),
							'columns' => 'label'
						)
					);
					
					$pr[$rankkey]['labels'][$this->getCurrentLanguageId()] = $lpr[0]['label'];

				}
	
			}
			
			$_SESSION['user']['species']['ranks']['projectRanks'] = $pr;
			$_SESSION['user']['species']['ranks']['topRankId'] = $topRankId;
			
		}

		return
			array(
				'ranks' => $_SESSION['user']['species']['ranks']['projectRanks'],
				'topRankId' => $_SESSION['user']['species']['ranks']['topRankId']
			);

	}
	
	private function getTaxonById($id)
	{

		if (!isset($_SESSION['user']['species']['taxon']) ||
			$_SESSION['user']['species']['taxon']['id']!=$id) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $id
					)
				)
			);

			$_SESSION['user']['species']['taxon'] = $t[0];
			
			$pr = $this->models->ProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $_SESSION['user']['species']['taxon']['rank_id']
					)
				)
			);
			
			$_SESSION['user']['species']['taxon']['lower_taxon'] = $pr[0]['lower_taxon'];


		}

		return $_SESSION['user']['species']['taxon'];
	
	}

	private function getCategories($taxon=null,$forcelookup=false)
	{

		if (!isset($_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()]) || $forcelookup) {

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
		
				if ($val['def_page'] == 1) $_SESSION['user']['species']['defaultCategory'] = $val['id'];
			
			}
			
			$_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()] = $tp;

		}

		if ($taxon) {
		
			$defCat = 'classification';
		
			$d = null;

			foreach((array)$_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()] as $key => $val) {

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

				if ($ct[0]['page_id']==$_SESSION['user']['species']['defaultCategory'] && $ct[0]['publish']=='1') 
					$defCat = $_SESSION['user']['species']['defaultCategory'];

			}

			return array(
				'categories' => $d,
				'defaultCategory' => $defCat
			);

		}

		return array(
			'categories' => $_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()],
			'defaultCategory' => $_SESSION['user']['species']['defaultCategory']
		);

	}

	private function getCategoryName($id)
	{

		if (!isset($_SESSION['user']['species']['catnames'][$this->getCurrentLanguageId()][$id])) {

			if (is_numeric($id)) {

				$tpt = $this->models->PageTaxonTitle->_get(
					array('id'=>array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(), 
						'page_id' => $id
					),
					'columns'=>'title'));
		
				$_SESSION['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = $tpt[0]['title'];

			} else {

				$_SESSION['user']['species']['catnames'][$this->getCurrentLanguageId()][$id] = ucwords($id);

			}

		}

		return $_SESSION['user']['species']['catnames'][$this->getCurrentLanguageId()][$id];

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
				
				return isset($ct) ? $ct[0]['content'] : null;

		}
	
	}

	private function getTaxonMedia($taxon)
	{

		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon
				),
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

			$mt[$key]['description'] = $mdt ? $mdt[0]['description'] : null;
		}

		return $mt;

	}

	private function getTaxonClassification($taxon)
	{

		$d = null;

		$this->getTaxonTree();

		foreach((array)$this->_treeList as $key => $val) {

			$d[$val['rank_id']] = $val;
			
			if ($val['id'] == $taxon) {

				$d = array_slice($d,0,$val['level']+1);

				break;

			}

		}

		return $d;

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
			
				if (isset($_SESSION['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()])) {

					$c[$key]['language_name'] = $_SESSION['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()];

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
						$_SESSION['user']['languages'][$val['language_id']][$this->getCurrentLanguageId()] = 
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

}

