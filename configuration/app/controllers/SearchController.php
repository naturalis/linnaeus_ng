<?php

	/*

	sort order:

	1. starts with Str
	2. has Str after a word boundary other than start
	3. alphabet

	*/

include_once ('Controller.php');

class SearchController extends Controller
{

	public $noResultCaching = true;

    public $usedModels = array(
		'content',
        'content_taxon', 
        'page_taxon', 
        'page_taxon_title', 
        'media_taxon',
        'media_descriptions_taxon',
		'synonym',
		'commonname',
		'literature',
		'content_free_module',
		'choice_content_keystep',
		'content_keystep',
		'choice_keystep',
		'keystep',
		'literature',
		'glossary_media',
		'matrix',
		'matrix_name',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'occurrence_taxon'
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'search.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'lookup.js',
		'dialog/jquery.modaldialog.js'
	));
	
		
    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {

       parent::__construct($p);

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

    public function searchAction()
	{
	
		if (isset($_SESSION['app']['user']['search']['redo']) && $_SESSION['app']['user']['search']['redo']==true) {
		
			$this->requestData['search'] = $_SESSION['app']['user']['search']['lastSearch'];

			$_SESSION['app']['user']['search']['redo'] = false;

		}

		$results = $this->_searchAction();

		$this->smarty->assign('results',$results);

		$this->showBackToSearch = false;

		$this->smarty->assign('search',$this->requestData['search']);

		$this->smarty->assign('visibleSearchResultsPerCategory',$this->controllerSettings['visibleSearchResultsPerCategory']);
	
		$this->smarty->register_block('h', array(&$this,'highlightFound'));

		$this->smarty->register_block('foundContent', array(&$this,'foundContent'));

        $this->printPage();
	
	}

	public function redoSearchAction()
	{

		$this->storeHistory = false;

		if ($_SESSION['app']['user']['search']['hasSearchResults'] && $_SESSION['app']['user']['search']['lastSearch']) {

			$_SESSION['app']['user']['search']['redo'] = true;

		}

		$this->redirect('search.php');

	}

	public function highlightFound($params, $content, &$smarty, &$repeat)
	{

		if (empty($content)) return;

		return $this->_highlightFound($params, $content);

	}

	public function foundContent($params, $content, &$smarty, &$repeat)
	{

		if (empty($content)) return;
		
		$s = $params['search'];
		
		$content = strip_tags($content);

		if (preg_match('/^"(.+)"$/',$s)) {

			$s = substr($s,1,strlen($s)-2);

			$d = $this->clipContent($content,stripos($content,$s),$s);

		} else {

			$s = preg_replace('/(\s+)/',' ',trim($params['search']));
	
			if (stripos($s,' ')!==0) {
	
				$s = explode(' ',$s);
				
				foreach((array)$s as $key => $val) {
	
					$pos = stripos($content,$val);
	
					if ($pos!==false) {
	
						$d = $this->clipContent($content,$pos,$val);
	
					}
	
				}
	
			}
			
		}

		if ($d=='') $d = substr($content,0,50).(strlen($content) > 50 ? '...' : '');

		return $this->_highlightFound($params, $d);

	}

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);

        }

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
    
    }

    private function _searchAction ()
    {

		if ($this->rHasVal('search')) {

			if (strlen($this->requestData['search'])>=$this->controllerSettings['minimumSearchStringLength']) { 

				if (
					!isset($_SESSION['app']['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']]) || 
					$this->noResultCaching
					) {

					$results = $this->doSearch($this->requestData['search']);
		
					$results['numOfResults'] =
						$results['species']['numOfResults'] +
						$results['modules']['numOfResults'] +
						$results['dichkey']['numOfResults'] +
						$results['literature']['numOfResults'] +
						$results['glossary']['numOfResults'] +
						$results['matrixkey']['numOfResults'] +
						$results['content']['numOfResults'] +
						$results['map']['numOfResults']
						;
		
					$_SESSION['app']['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']]['results'] = $results;

				} else {

					$results = $_SESSION['app']['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']]['results'];

				}
				
				$_SESSION['app']['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
				$_SESSION['app']['user']['search']['lastSearch'] = $this->requestData['search'];
					
				return $results;
	

			} else {
			
				$this->addMessage(sprintf($this->translate('Search term too short. Minimum is %s characters.'),$this->controllerSettings['minimumSearchStringLength']));
				
				return null;
			
			}

		} else {

			unset($_SESSION['app']['user']['search'][$this->requestData['search']]['results']);

			return null;

		}

    }

	private function _highlightFoundCallback($matches)
	{

		return '<span class="highlight">'.$matches[0].'</span>';

	}

	private function _highlightFound($params, $content)
	{

		if (preg_match('/^"(.+)"$/',$params['search'])) {
		
			$s = '('.substr($params['search'],1,strlen($params['search'])-2).')';

		} else {

			$s = preg_replace('/(\s+)/',' ',trim($params['search']));
	
			if (strpos($s,' ')!==0) $s = '('.str_replace(' ','|',$s).')i';

		}

		return preg_replace_callback($s,array( &$this, '_highlightFoundCallback'),$content);

	}

	private function clipContent($str,$pos,$s)
	{

		return
			($pos-25 > 0 ? '...' : '' ).
			substr($str,($pos-25<0 ? 0 : $pos-25),strlen($s)+50).
			(($pos+strlen($s)) < strlen($str) ? '...' : '');
	
	}

	private function doSearch($search)
	{

		$species = $this->searchSpecies($search);

		return array(
			'species' => $species,
			'modules' => $this->searchModules($search),
			'dichkey' => $this->searchDichotomousKey($search),
			'literature' => $this->searchLiterature($search),
			'glossary' => $this->searchGlossary($search),
			'matrixkey' => $this->searchMatrixKey($search),
			'content' => $this->searchContent($search),
			'map' => $this->searchMap($species)
			
		);

	}

	private function makeRegExpCompatSearchString($s,$containsOrStarts='contains')
	{
	
		$s = trim($s);

		// if string enclosed by " take it literally		
		if (preg_match('/^"(.+)"$/',$s)) {
			
			$s = mysql_real_escape_string(substr($s,1,strlen($s)-2));

			if ($containsOrStarts=='begins')
				$s = '^'.$s;
			elseif ($containsOrStarts=='boundary')
				$s = '[[:<:]]'.$s;
			else
				return '('.$s.')';

		}

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0) return mysql_real_escape_string($s);

		if ($containsOrStarts=='begins')
			$s = '^'.str_replace(' ','^',$s);
		else
		if ($containsOrStarts=='boundary')
			$s = '[[:<:]]'.str_replace(' ','|[[:<:]]',$s);
		else
		if ($containsOrStarts=='full')
			$s = $s;
		else
			$s = str_replace(' ','|',$s);

		return '('.mysql_real_escape_string($s).')';
	
	}



	// species ++
	private function makeCategoryList()
	{

		if (!isset($_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()])) {

			// get the defined categories (just the page definitions, no content yet)
			$tp = $this->models->PageTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'order' => 'show_order',
					'fieldAsIndex' => 'id'
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

		return $_SESSION['app']['user']['species']['categories'][$this->getCurrentLanguageId()];
	
	}

	private function addTaxonToFoundData($taxa,&$data)
	{

		foreach((array)$data as $key => $val) {
		
			if (isset($taxa[$val['taxon_id']]['label'])) {

				$data[$key]['taxon'] = $taxa[$val['taxon_id']]['label'];
				
			}
		
		}
	
	}

	private function _searchSpeciesGetTaxa($search)
	{
	
		$d = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'taxon regexp' => $this->makeRegExpCompatSearchString($search,'contains') // we need them all!
				),
				'fieldAsIndex' => 'taxon_id',
				'columns' =>
					'id as taxon_id,
					taxon,
			        parent_id,
					taxon as label,
			        rank_id, 
			        is_hybrid,
					taxon regexp \''.$this->makeRegExpCompatSearchString($search,'full').'\' as fullMatch,
					taxon regexp \''.$this->makeRegExpCompatSearchString($search,'contains').'\' as isMatch,
					taxon regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					taxon regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'fullMatch desc, sortA desc, sortB desc, taxon'
			)
		);

		foreach((array)$d as $key => $val)		
			$d[$key]['label'] = $this->formatTaxon($val);
			
		return $d;
	
	}

	private function _searchSpeciesGetSpAndHT($taxa,$ranks)
	{
	
		$sp = $ht = array();
	
		foreach((array)$taxa as $key => $val) {
		
			if ($val['isMatch']=='1') {
		
				$taxa[$key]['rank'] = $ranks[$val['rank_id']]['rank'];

				if ($ranks[$val['rank_id']]['lower_taxon']==1)
					$sp[] = $taxa[$key];
				else
					$ht[] = $taxa[$key];
					
			}
				
		}
		
		return
			array(
				'sp' => $sp,
				'ht' => $ht
			);

	}

	private function _searchSpeciesGetSynonyms($search,$taxa)
	{
	
		$d = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
//					'language_id' => $this->getCurrentLanguageId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' =>
					'id,
					taxon_id,
					synonym as label,
					\'names\' as cat, 
					label regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					label regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, label'
			)
		);

		$this->addTaxonToFoundData($taxa,$d);
		
		return $d;
	
	}

	private function _searchSpeciesGetCommonnames($search,$taxa)
	{
	
		$d = $this->models->Commonname->_get(
			array(
				'where' =>
					'project_id  = '.$this->getCurrentProjectId(). ' and
					(
						commonname regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
						transliteration regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\'
					)',
				'columns' => 
					'id,
					language_id,
					taxon_id,
					if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) as label,
					\'names\' as cat, 
					if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) regexp \''.
						$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) regexp \''.
						$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, label'
			)
		);	

		foreach((array)$d as $key => $val) {

            $l = $this->models->Language->_get(array('id'=>$val['language_id']));
			$d[$key]['language'] = $l['language'];
			if (isset($taxa[$val['taxon_id']]['label']))
				$d[$key]['post_script'] = '(' . sprintf($this->translate('common name of %s'),$taxa[$val['taxon_id']]['label']) . ')';

		}

		//$this->addTaxonToFoundData($taxa,$d);
		
		return $d;
	
	}

	private function _searchSpeciesGetTaxonContent($search,$taxa)
	{
	
		$d = $this->models->ContentTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'publish' => 1,
						'content regexp' => $this->makeRegExpCompatSearchString($search)
					),
					'columns' => 
						'id,
						taxon_id,
						content as content,
						page_id as cat'
				)
			);

		$this->addTaxonToFoundData($taxa,$d);
		$this->customSortArray($d,array('key'=>'taxon'));
		
		return $d;
	
	}

	private function _searchSpeciesGetTaxonMedia($search,$taxa)
	{
	
		$m1 = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
//					'language_id' => $this->getCurrentLanguageId(),
					'file_name regexp' => $this->makeRegExpCompatSearchString($search)
				),
			'columns' => 'id,taxon_id,id as media_id,file_name as label,\'media\' as cat, LEFT(mime_type,5) as mime'
			)
		);

		$m2 = $this->models->MediaDescriptionsTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'description regexp' => $this->makeRegExpCompatSearchString($search)
				),
			'columns' => 'id,media_id,description as content,\'media\' as cat'
			)
		);
		
		foreach((array)$m2 as $key => $val) {

			$d = $this->models->MediaTaxon->_get(
				array(
					'id' => array(
						'id' => $val['media_id']
					),
				'columns' => 'taxon_id'
				)
			);
			
			$m2[$key]['taxon_id'] = $d[0]['taxon_id'];
					
		}

		$d = array_merge((array)$m1,(array)$m2);

		$this->addTaxonToFoundData($taxa,$d);
		$this->customSortArray($d,array('key'=>'taxon'));

		return $d;
	
	}

	private function searchSpecies($search,$extensive=true)
	{
	
		$taxa = $synonyms = $commonnames = $content = $media = array();

		$ranks = $this->getProjectRanks();

		$taxa = $this->_searchSpeciesGetTaxa($search);

		$d = $this->_searchSpeciesGetSpAndHT($taxa,$ranks);
		$species = $d['sp'];
		$higherTaxa = $d['ht'];
		
		$synonyms = $this->_searchSpeciesGetSynonyms($search,$taxa);
		$commonnames = $this->_searchSpeciesGetCommonnames($search,$taxa);

		if ($extensive) {

			$content = $this->_searchSpeciesGetTaxonContent($search,$taxa);
			$media = $this->_searchSpeciesGetTaxonMedia($search,$taxa);

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Higher taxa'),
					'data' => $higherTaxa,
					'numOfResults' => count((array)$higherTaxa)
				),
				array(
					'label' => $this->translate('Species names'), // when changing the label 'Species names', do the same in searchMap()
					'data' => $species, //$taxa,
					'numOfResults' => count((array)$species)//count((array)$taxa)
				),
				array(
					'label' => $this->translate('Species descriptions'),
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
				array(
					'label' => $this->translate('Species synonyms'),
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms)
				),
				array(
					'label' => $this->translate('Species common names'),
					'data' => $commonnames,
					'numOfResults' => count((array)$commonnames)
				),
				array(
					'label' => $this->translate('Species media'),
					'data' => $media,
					'numOfResults' => count((array)$media)
				),
			),
			'categoryList' => $this->makeCategoryList(),
			'numOfResults' =>
				count((array)$higherTaxa)+
				count((array)$species)+
				count((array)$content)+
				count((array)$synonyms)+
				count((array)$commonnames)+
				count((array)$media),
			'subsetsWithResults' =>
				(count((array)$taxa) > 0 ? 1 : 0)+
				(count((array)$content) > 0 ? 1 : 0)+
				(count((array)$synonyms) > 0 ? 1 : 0)+
				(count((array)$commonnames) > 0 ? 1 : 0)+
				(count((array)$media) > 0 ? 1 : 0)
			
		);

	}

	public function getSpeciesLookupList($search=null)
	{

		if (empty($search)) {

			$s = $this->getCache('search-contentsSpecies');

			if (!$s) {

				$s = $this->searchSpecies($search,false);

				$this->saveCache('search-contentsSpecies',$s);

			}
			
		} else {

			$s = $this->searchSpecies($search,false);
	
		}

		$d = array();
		
		foreach((array)$s['results'] as $val) {
		
			if (
				is_array($val['data']) &&
					(
						$val['label']=='Species names' ||
						$val['label']=='Species synonyms' ||
						$val['label']=='Species common names'	
					)
				) {

				foreach($val['data'] as $val2) {

					$d[] = array(
						'id' => $val2['taxon_id'],
						'label' => $val2['label'],
						'source' => $val['label'],
						'url'  => '../species/taxon.php?id='.$val2['taxon_id'].($val['label']!='Species names' ? '&cat=names' : '')
					);

				}

			}

		}
		
		return $d;

	}


	// glossary
	private function searchGlossary($search,$extensive=true)
	{

		$gloss = $this->models->Glossary->_get(
			array(
				'where' =>
					'project_id = '.$this->getCurrentProjectId().' and
					language_id = '. $this->getCurrentLanguageId().' and
					(
						term regexp \''.$this->makeRegExpCompatSearchString($search).'\''.
						($extensive ? 'or definition regexp \''.$this->makeRegExpCompatSearchString($search).'\'' : '').	
					')'
				,
				'columns' => 
					'id,
					term as label,
					definition as content,
					term regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					term regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc,sortB desc,label'
			)
		);


		$synonyms = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 
					'glossary_id as id,
					synonym as label,
					synonym regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					synonym regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc,sortB desc,synonym'
			)
		);

		foreach((array)$synonyms as $key => $val) {
		
			$g = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['id']
					),
					'columns' => 'term'
				)
			);
	
			$synonyms[$key]['synonym'] = $g[0]['term'];

		}

		if ($extensive) {

			$media = $this->models->GlossaryMedia->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'file_name regexp' => $this->makeRegExpCompatSearchString($search)
					),
					'columns' => 
						'glossary_id as id,
						file_name as label',
					'order' => 'label'
				)
			);
	
			foreach((array)$media as $key => $val) {
			
				$g = $this->models->Glossary->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->getCurrentLanguageId(),
							'id' => $val['id']
						),
						'columns' => 'term'
					)
				);
		
				if (isset($g)) {
	
					$d[$key] = $val;
					$d[$key]['term'] = $g[0]['term'];
	
				}
	
			}
			
			$this->customSortArray($d,array('key' => 'term'));

		}

		$media = isset($d) ? $d : null;

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Glossary terms'),
					'data' => $gloss,
					'numOfResults' => count((array)$gloss)
				),
				array(
					'label' => $this->translate('Glossary synonyms'),
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms)
				),
				array(
					'label' => $this->translate('Glossary media'),
					'data' => $media,
					'numOfResults' => count((array)$media)
				)
			),
			'numOfResults' => count((array)$gloss)+count((array)$synonyms)+count((array)$media),
			'subsetsWithResults' => 
				(count((array)$gloss) > 0 ? 1 : 0) +
				(count((array)$synonyms) > 0 ? 1 : 0) +
				(count((array)$media) > 0 ? 1 : 0)
		);

	}

	public function getGlossaryLookupList($search=null)
	{

		if (empty($search)) {

			$g = $this->getCache('search-contentsGlossary');

			if (!$g) {

				$g = $this->searchGlossary($search,false);

				$this->saveCache('search-contentsGlossary',$g);

			}
			
		} else {

			$g = $this->searchGlossary($search,false);
	
		}

		$d = array();
		
		foreach((array)$g['results'] as $val) {
		
			if (is_array($val['data']) && ($val['label']=='Glossary terms' || $val['label']=='Glossary synonyms')) {

				foreach($val['data'] as $val2) {

					$d[] = array(
						'id' => $val2['id'],
						'label' => $val2['label'],
						'source' => $val['label'],
						'url'  => '../glossary/term.php?id='.$val2['id']
					);

				}

			}

		}
		
		return $d;

	}


	// literature
	private function searchLiterature($search,$extensive=true)
	{

		$books = $this->models->Literature->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().'
					and (
						author_first regexp \''.$this->models->Literature->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
						author_second regexp \''.$this->models->Literature->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
						year regexp \''.$this->models->Literature->escapeString($this->makeRegExpCompatSearchString($search)).'\' '.
						($extensive ? 'or text regexp \''.$this->models->Literature->escapeString($this->makeRegExpCompatSearchString($search)).'\'' : '').
						')',
				'columns' => 
					'id,
					concat(year(`year`),ifnull(suffix,\'\')) as year,
					text as content,
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						)
					) as author_full,
					author_first regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					author_first regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, author_full'
			)
		);

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Literary references'),
					'data' => $books,
					'numOfResults' => count((array)$books)
				)
			),
			'numOfResults' => count((array)$books),
			'subsetsWithResults' => count((array)$books) > 0 ? 1 : 0
		);

	}

	public function getLiteratureLookupList($search=null)
	{

		if (empty($search)) {

			$l = $this->getCache('search-contentsLiterature');

			if (!$l) {

				$l = $this->searchLiterature($search,false);

				$this->saveCache('search-contentsLiterature',$l);

			}
			
		} else {

			$l = $this->searchLiterature($search,false);
	
		}
	
		$d = array();
		
		foreach((array)$l['results'] as $val) {
		
			if (is_array($val['data'])) {

				foreach($val['data'] as $val2) {

					$d[] = array(
						'id' => $val2['id'],
						'label' => $val2['author_full'].($val2['year'] ? ', '.$val2['year'] : ''),
						'source' => $val['label'],
						'url'  => '../literature/reference.php?id='.$val2['id']
					);

				}

			}

		}
		
		return $d;
	
	}


	// dich. key
	private function searchDichotomousKey($search)
	{

		$choices = $this->models->ChoiceContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'choice_txt regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'choice_id,choice_txt as content'
			)
		);

		foreach((array)$choices as $key => $val) {

			$step = $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['choice_id'],
					),
					'columns' => 'keystep_id,show_order'
				)
			);

			if ($step) {

				$ck = $this->models->ContentKeystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->getCurrentLanguageId(),
							'keystep_id' => $step[0]['keystep_id']
						),
						'columns' => 'keystep_id,title'
					)
				);

				$choices[$key]['title'] = $ck[0]['title'];
				$choices[$key]['marker'] = $this->showOrderToMarker($step[0]['show_order']);

				$step = $this->models->Keystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->getCurrentLanguageId(),
							'id' => $step[0]['keystep_id']
						),
						'columns' => 'number'
					)
				);

				$choices[$key]['number'] = $step[0]['number'];

			}

		}

		$steps1 = $this->models->ContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'title regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'keystep_id,title,title as label'
			)
		);

		$steps2 = $this->models->ContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'content regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'keystep_id,title,content as content'
			)
		);

		$steps = array_merge((array)$steps1,(array)$steps2);
		
		$this->customSortArray($steps,array('key' => 'keystep_id'));

		foreach((array)$steps as $key => $val) {

			$step = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'id' => $val['keystep_id']
					),
					'columns' => 'number',
					'order' => 'number'
				)
			);

			$steps[$key]['number'] = $step[0]['number'];

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Dichotomous key steps'),
					'data' => $steps,
					'numOfResults' => count((array)$steps)
				),
				array(
					'label' => $this->translate('Dichotomous key choices'),
					'data' => $choices,
					'numOfResults' => count((array)$choices)
				)
			),
			'numOfResults' => count((array)$choices)+count((array)$steps),
			'subsetsWithResults' =>
				(count((array)$choices) > 0 ? 1 : 0) +
				(count((array)$steps) > 0 ? 1 : 0)
		);

	}


	// matrix key
	private function searchMatrixKey($search)
	{

		$matrices = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'name regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 
					'matrix_id,
					name as label,
					name regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					name regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, label'
			)
		);

		$characteristics = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'label regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 
					'characteristic_id,
					label,
					label regexp \''.$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					label regexp \''.$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, label'
			)
		);

		foreach((array)$characteristics as $key => $val) {

			$cm = $this->models->CharacteristicMatrix->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic_id' => $val['characteristic_id'],
					),
					'columns' => 'matrix_id'
				)
			);

			$characteristics[$key]['matrices'] = $cm;

		}


		$states1 = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'label regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'state_id,label'
			)
		);

		$states2= $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'text regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'state_id,label,text as content'
			)
		);

		$states = array_merge((array)$states1,(array)$states2);

		foreach((array)$states as $key => $val) {

			$cs = $this->models->CharacteristicState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['state_id'],
					),
					'columns' => 'characteristic_id'
				)
			);

			$cl = $this->models->CharacteristicLabel->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'characteristic_id' => $cs[0]['characteristic_id']
					),
					'columns' => 'label'
				)
			);
			
			$states[$key]['characteristic'] = $cl[0]['label'];

			$cm = $this->models->CharacteristicMatrix->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic_id' => $cs[0]['characteristic_id']
					),
					'columns' => 'matrix_id'
				)
			);
			
			$states[$key]['matrices'] = $cm;

		}
		
		$this->customSortArray($states,array('key'=>'label'));

		$matrixNames = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
				),
				'columns' => 'matrix_id,name',
				'fieldAsIndex' => 'matrix_id'
			)
		);

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Matrix key matrices'),
					'data' => $matrices,
					'numOfResults' => count((array)$matrices)
				),
				array(
					'label' => $this->translate('Matrix key characters'),
					'data' => $characteristics,
					'numOfResults' => count((array)$characteristics)
				),
				array(
					'label' => $this->translate('Matrix key states'),
					'data' => $states,
					'numOfResults' => count((array)$states)
				)
			),
			'numOfResults' => count((array)$matrices)+count((array)$characteristics)+count((array)$states),
			'subsetsWithResults' =>
				(count((array)$matrices) > 0 ? 1 : 0)+
				(count((array)$characteristics) > 0 ? 1 : 0)+
				(count((array)$states) > 0 ? 1 : 0),
			'matrices' => $matrixNames
		);

	}


	// distribution
	private function searchMap($species)
	{

		foreach((array)$species['results'] as $key => $val) {
		
			if ($val['label']=='Species names') {

				foreach((array)$val['data'] as $dKey => $dVal) {

					$ot = $this->models->OccurrenceTaxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'taxon_id' => $dVal['taxon_id']
							),
							'columns' => 'count(*) as total'
						)
					);
					
					if ($ot[0]['total']>0) $geo[] =
						array(
							'id' => $dVal['taxon_id'],
							'content' => $dVal['label'],
							'number' => $ot[0]['total']
						);
				}

			}

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('geographical data'),
					'data' => (isset($geo) ? $geo : null),
					'numOfResults' => (isset($geo) ? count((array)$geo) : 0)
				),
			),
			'numOfResults' => isset($geo) ? count((array)$geo) : 0,
			'subsetsWithResults' => isset($geo) && count((array)$geo) > 0 ? 1 : 0
		);

	}


	// content
	private function searchContent($search)
	{
		/*
		$content1 = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'subject regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,subject as label'
			)

		);

		$content2 = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'content regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,subject as label,content'
			)

		);

		$content = array_merge((array)$content1,(array)$content2);
		*/

		$content = $this->models->Content->_get(
			array(
				'where' =>
					'project_id  = '.$this->getCurrentProjectId(). ' and
					language_id = '.$this->getCurrentLanguageId(). ' and
					(
						subject regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
						content regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\'
					)',
				'columns' => 
					'id,
					subject as label,
					content,
					if(subject regexp \''.$this->makeRegExpCompatSearchString($search).'\',subject,content) regexp \''.
						$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					if(subject regexp \''.$this->makeRegExpCompatSearchString($search).'\',subject,content) regexp \''.
						$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'sortA desc, sortB desc, label'
			)
		);	
		
		//$this->customSortArray($content,array('key'=>'label'));

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Navigator'),
					'data' => $content,
					'numOfResults' => count((array)$content)
				)
			),
			'numOfResults' => count((array)$content),
			'subsetsWithResults' => count((array)$content) > 0 ? 1 : 0
		);

	}


	// modules
	private function searchModules($search)
	{

		$content = $this->models->ContentFreeModule->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().' and
					language_id ='.$this->getCurrentLanguageId().' and
					(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\' or
					content regexp \''.$this->makeRegExpCompatSearchString($search).'\')',
				'columns' => 
					'page_id,
					module_id,
					content,
					topic as label,
					if(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\',topic,content) regexp \''.
						$this->makeRegExpCompatSearchString($search,'begins').'\' as sortA,
					if(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\',topic,content) regexp \''.
						$this->makeRegExpCompatSearchString($search,'boundary').'\' as sortB',
				'order' => 'module_id,sortA desc, sortB desc, topic'
			)
		);
		
		
		foreach((array)$content as $key => $val) {

			$module = $this->models->FreeModuleProject->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['module_id']
					),
					'columns' => 'module'
				)
			);

			$results[$module[0]['module']][] = $val;

		}

		$r = null;
		if (isset($results))
			foreach ($results as $key => $val) $r[] = array('label' => $key, 'data' =>$val, 'numOfResults' => count((array)$val) );

		return array(
			'results' => isset($r) ? $r : null,
			'numOfResults' => count((array)$content),
			'subsetsWithResults' => count((array)$r)
		);

	}
	
	public function getModuleLookupList($search=null)
	{

		if (empty($search)) {

			$l = $this->getCache('search-contentsModules');

			if (!$l) {

				$l = $this->searchModules($search);

				$this->saveCache('search-contentsModules',$l);

			}
			
		} else {

			$l = $this->searchModules($search);
	
		}
	
		$d = array();
		
		foreach((array)$l['results'] as $val) {
		
			if (is_array($val['data'])) {

				foreach($val['data'] as $val2) {

					$d[] = array(
						'id' => $val2['page_id'],
						'label' => $val2['label'],
						'source' => $val['label'],
						'url'  => '../module/topic.php?id='.$val2['page_id'].'&modId='.$val2['module_id']
					);

				}

			}

		}
		
		return $d;
	
	}	

	// general
	public function getLookupList($search)
	{

		$search = isset($p['search']) ? $p['search'] : null;
	
		/*
		excluded:
		- Introduction / other content
		- Dichotomous key
		- Matrix key 
		- Distribution
		*/

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				array_merge(
					(array)$this->getGlossaryLookupList($search),
					(array)$this->getLiteratureLookupList($search),
					(array)$this->getSpeciesLookupList($search),
					(array)$this->getModuleLookupList($search)
				),
				$this->controllerBaseName,
				null,
				true
			)
		);

	}

 
}
