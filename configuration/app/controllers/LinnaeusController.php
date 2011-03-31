<?php

include_once ('Controller.php');

class LinnaeusController extends Controller
{

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
		'glossary',
		'glossary_synonym',
		'glossary_media',
		'matrix_name',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'occurrence_taxon'
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array('basics.css','search.css');
	public $jsToLoad = array('all' => array(
		'main.js'
	));


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();

		if ($this->viewName!='set_project') {

			$this->checkForProjectId();

		}

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
     * Set the active project ID
     *
     * @access    public
     */
    public function setProjectAction ()
    {

		unset($_SESSION['project']);

		$this->resolveProjectId();

		if (!$this->getCurrentProjectId()) {

			$this->addError(_('Unknown project or invalid project ID.'));

	        $this->printPage();

		} else {

			$this->setCurrentProjectData();

			$this->redirect('index.php');

		}

    }


    /**
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function indexAction ()
    {

		if (!$this->rHasId()) {

	        $this->setPageName( _('Home'));

			$this->smarty->assign('content',$this->getContent('introduction'));

		} else {
		
			$d = $this->getContent(null,$this->requestData['id']);

	        $this->setPageName( _($d['subject']));

			$this->smarty->assign('content',$d);

		}

        $this->printPage();
  
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


	public function highlightFound($params, $content, &$smarty, &$repeat)
	{

		if (empty($content)) return;

		return $this->_highlightFound($params, $content);

	}

	private function clipContent($str,$pos,$s)
	{

		return
			($pos-25 > 0 ? '...' : '' ).
			substr($str,($pos-25<0 ? 0 : $pos-25),strlen($s)+50).
			(($pos+strlen($s)) < strlen($str) ? '...' : '');
	
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


	public function redoSearchAction()
	{

		$this->storeHistory = false;

		if ($_SESSION['user']['search']['hasSearchResults'] && $_SESSION['user']['search']['lastSearch']) {

			$_SESSION['user']['search']['redo'] = true;

		}

		$this->redirect('search.php');

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


    /**
     * Index of project: introduction
     *
     * @access    public
     */
    public function searchAction ()
    {

		if (isset($_SESSION['user']['search']['redo']) && $_SESSION['user']['search']['redo']==true) {
		
			$this->requestData['search'] = $_SESSION['user']['search']['lastSearch'];

			$_SESSION['user']['search']['redo'] = false;

		}

		if ($this->rHasVal('search')) {

			if (strlen($this->requestData['search'])>2) { 
			
				if (1==1 || !isset($_SESSION['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']])) {
		
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
		
					$_SESSION['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']]['results'] = $results;

				} else {

					$results = $_SESSION['user']['search'][$this->getCurrentLanguageId()][$this->requestData['search']]['results'];

				}
				
				$_SESSION['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
				$_SESSION['user']['search']['lastSearch'] = $this->requestData['search'];
					
				$this->smarty->assign('results',$results);
	

			} else {
			
				$this->addMessage(sprintf(_('Search term too short. Minimum is %s characters.'),3));
			
			}

		} else {

			unset($_SESSION['user']['search'][$this->requestData['search']]['results']);

		}

		$this->showBackToSearch = false;

		$this->smarty->assign('search',$this->requestData['search']);
	
		$this->smarty->register_block('h', array(&$this,'highlightFound'));

		$this->smarty->register_block('foundContent', array(&$this,'foundContent'));

        $this->printPage();
  
    }

	private function resolveProjectId()
	{
	
		if (!$this->rHasVal('p')) $this->setCurrentProjectId(null);

		if (is_numeric($this->requestData['p'])) {

			$p = $this->models->Project->_get(
				array(
					'id' => $this->requestData['p']
				)			
			);
			
			if (!$p)
				$this->setCurrentProjectId(null);
			else
				$this->setCurrentProjectId(intval($this->requestData['p']));

		} else {

			$pName = str_replace('_',' ',strtolower($this->requestData['p']));

			$p = $this->models->Project->_get(
				array(
					'id' => array('sys_name' => $pName)
				)			
			);

			if (!$p[0]) {

				$this->setCurrentProjectId(null);

			} else {

				$this->setCurrentProjectId(intval($p[0]['id']));

			}

		}
	
	}
	
	private function getContent($sub=null,$id=null)
	{

		$d = array(
			'project_id' => $this->getCurrentProjectId(),
			'language_id' => $this->getCurrentLanguageId()
		);
		
		if ($id!=null) $d['id'] = $id;
		elseif ($sub!=null) $d['subject'] = $sub;
		else return;
		
		$c = $this->models->Content->_get(array('id' => $d));

		return isset($c[0]['content']) ? $c[0]['content'] : '';
	
	}

	private function maxeTaxonList($records)
	{

		$taxonList = null;

		foreach((array)$records as $key => $val) {

			if (!isset($taxonList[$val['taxon_id']])) {

				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'id' => $val['taxon_id']
						),
						'columns' => 'id,taxon'
					)
				);
				
				$taxonList[$val['taxon_id']] = $t[0];

			}

		}

		return $taxonList;
	
	}

	private function makeCategoryList()
	{

		if (!isset($_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()])) {

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
		
				if ($val['def_page'] == 1) $_SESSION['user']['species']['defaultCategory'] = $val['id'];
			
			}
			
			$_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()] = $tp;

		}

		return $_SESSION['user']['species']['categories'][$this->getCurrentLanguageId()];
	
	}

	private function makeRegExpCompatSearchString($s)
	{
	
		$s = trim($s);

		// if string enclosed by " take it literally		
		if (preg_match('/^"(.+)"$/',$s)) return '('.mysql_real_escape_string(substr($s,1,strlen($s)-2)).')';

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0) return mysql_real_escape_string($s);

		$s = str_replace(' ','|',$s);

		return '('.mysql_real_escape_string($s).')';
	
	}

	private function searchSpecies($search)
	{

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id as taxon_id,taxon as label'
			)
		);

		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon_id,synonym as label,\'names\' as cat'
			)
		);

		$commonnames1 = $this->models->Commonname->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'commonname regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon_id,commonname as label,\'names\' as cat'
			)
		);

		$commonnames2 = $this->models->Commonname->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'transliteration regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon_id,transliteration as label,\'names\' as cat'
			)
		);

		$commonnames = array_merge((array)$commonnames1,(array)$commonnames2);

		$content = $this->models->ContentTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'publish' => 1,
					'content regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon_id,content as content,page_id as cat'
			)
		);

		$media1 = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'file_name regexp' => $this->makeRegExpCompatSearchString($search)
				),
			'columns' => 'id,taxon_id,id as media_id,file_name as label,\'media\' as cat'
			)
		);

		$media2 = $this->models->MediaDescriptionsTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'description regexp' => $this->makeRegExpCompatSearchString($search)
				),
			'columns' => 'id,taxon_id,media_id,description as content,\'media\' as cat'
			)
		);

		$media = array_merge((array)$media1,(array)$media2);

		$d = array_merge((array)$synonyms,(array)$commonnames,(array)$content,(array)$media);


		return array(
			'results' => array(
				_('Species names') => $taxa, // when changing the label 'Species names', do the same in searchMap()
				_('Species descriptions') => $content,
				_('Species synonyms') => $synonyms,
				_('Species common names') => $commonnames,
				_('Species media') => $media,
			),
			'taxonList' => $this->maxeTaxonList($d),
			'categoryList' => $this->makeCategoryList(),
			'numOfResults' => count((array)$d)+count((array)$taxa)
		);

	}

	private function searchModules($search)
	{

		$content1 = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'topic regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'page_id,module_id,topic as label',
				'order' => 'module_id'
			)
		);

		$content2 = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'content regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'page_id,module_id,topic,content as content',
				'order' => 'module_id'
			)
		);

		$content = array_merge((array)$content1,(array)$content2);

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

		return array(
			'results' => isset($results) ? $results : null,
			'numOfResults' => count((array)$content)
		);

	}

	private function searchMatrixKey($search)
	{

		$matrices = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'name regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'matrix_id,name as label'
			)
		);

		$characteristics = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'label regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'characteristic_id,label'
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

		$states2 	 = $this->models->CharacteristicLabelState->_get(
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
				_('Matrix key matrices') => $matrices,
				_('Matrix key characteristics') => $characteristics,
				_('Matrix key states') => $states
			),
			'numOfResults' => count((array)$matrices)+count((array)$characteristics)+count((array)$states),
			'matrices' => $matrixNames
		);

	}

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

		foreach((array)$steps as $key => $val) {

			$step = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'id' => $val['keystep_id']
					),
					'columns' => 'number'
				)
			);

			$steps[$key]['number'] = $step[0]['number'];

		}

		return array(
			'results' => array(
				_('Dichotomous key steps') => $choices,
				_('Dichotomous key choices') => $steps
			),
			'numOfResults' => count((array)$choices)+count((array)$steps)
		);

	}

	private function searchLiterature($search)
	{

		$books1 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'author_first regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,year(`year`) as year,author_first as label,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full',
			)
		);

		$books2 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'author_second regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,year(`year`) as year,author_second as label,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full',
			)
		);

		$books3 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'year regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,year(`year`) as year,year as label,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full',
			)
		);

		$books4 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'text regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,year(`year`) as year ,text as content,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full',
			)
		);

		$books = array_merge((array)$books1,(array)$books2,(array)$books3,(array)$books4);

		return array(
			'results' => array(
				_('Literary references') => $books,
			),
			'numOfResults' => count((array)$books)
		);

	}

	private function searchGlossary($search)
	{

		$gloss1 = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'term regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,term as label'
			)
		);

		$gloss2 = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'definition regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,term as label,definition as content'
			)
		);

		$gloss = array_merge((array)$gloss1,(array)$gloss2);

		$synonyms = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'glossary_id as id,synonym as label'
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

		$media = $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'file_name regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'glossary_id as id,file_name as label'
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

		$media = isset($d) ? $d : null;

		return array(
			'results' => array(
				_('Glossary terms') => $gloss,
				_('Glossary synonyms') => $synonyms,
				_('Glossary media') => $media,
			),
			'numOfResults' => count((array)$gloss)+count((array)$synonyms)+count((array)$media)
		);

	}

	private function searchContent($search)
	{

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

		return array(
			'results' => array(
				_('Other pages') => $content,
			),
			'numOfResults' => count((array)$content)
		);

	}

	private function searchMap($species)
	{

		foreach((array)$species['results']['Species names'] as $key => $val) {
		
			$ot = $this->models->OccurrenceTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['taxon_id']
					),
					'columns' => 'count(*) as total'
				)
			);
			
			if ($ot[0]['total']>0) $geo[] =
				array(
					'id' => $val['taxon_id'],
					'content' => $val['label'],
					'number' => $ot[0]['total']
				);

		}

		return array(
			'results' => array(
				_('geographical data') => $geo,
			),
			'numOfResults' => count((array)$geo)
		);

	}


}
