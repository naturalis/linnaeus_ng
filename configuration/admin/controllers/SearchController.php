<?php

include_once ('Controller.php');

class SearchController extends Controller
{

	public $noResultCaching = true;
	private $_replaceId = 1;
	private $_replaceCounter = 0;
	private $_replaceData = null;
	private $_replaceStatusIndex = array();
	private $_replacementResultCounters = array('mismatched' => 0, 'skipped' => 0, 'replaced' => 0);

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
		'glossary_media',
		'glossary_synonym',
		'matrix',
		'matrix_name',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'geodata_type_title',
		'content_introduction'
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'search.css'
	);

	public $jsToLoad = array('all' => array(
		'search.js'
	));
	

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
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function searchIndexAction ()
    {

		$this->checkAuthorisation();

		$this->setPageName(_('Search and replace'));

		$this->setControllerMask('utilities','Search');
		
		unset($_SESSION['user']['search']['results']);

		if ($this->rHasVal('search')) {

			$_SESSION['user']['search']['search'] = array(
				'search' => $this->requestData['search'],
				'replacement' => $this->rHasVal('replacement') ? $this->requestData['replacement'] : null,
				'doReplace' => $this->rHasVal('doReplace') ? $this->requestData['doReplace'] : null,
				'modules' => $this->rHasVal('modules') ? $this->requestData['modules'] : null,
				'freeModules' => $this->rHasVal('freeModules') ? $this->requestData['freeModules'] : null,
				'options' =>$this->rHasVal('options') ? $this->requestData['options'] : null,
				);

			$_SESSION['user']['search']['results'] =
				$this->_searchAction(
					$this->requestData['search'],
					$this->rHasVal('modules') ? $this->requestData['modules'] : false,
					$this->rHasVal('freeModules') ? $this->requestData['freeModules'] : false
				);

			$_SESSION['user']['search']['replace']['index'] = $this->_replaceStatusIndex;

			if ($this->rHasVal('doReplace','on') && $this->rHasVal('replacement') && $this->rHasVal('options','all')) {
			
				$_SESSION['user']['search']['results']['replace_all'] = true;

				$this->redirect('search_replace_all.php');

			} else
			if ($this->rHasVal('doReplace','on') && $this->rHasVal('replacement') && $this->rHasVal('options','perOccurrence')) {

				$this->redirect('search_replace.php');

			} else {

				$this->redirect('search_results.php');

			}		

		}

		if (isset($_SESSION['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['user']['search']['search']);

		$this->smarty->assign('modules',$this->getProjectModules());

        $this->printPage();
  
    }

    /**
     * 
     *
     * @access    public
     */
    public function searchReplaceIndexAction ()
    {

		$this->checkAuthorisation();
		
		$this->setPageName(_('Search results'));

		$this->setControllerMask('utilities','Search');

		if ($_SESSION['user']['search']['search']) {

			if (isset($_SESSION['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['user']['search']['search']);
			if (isset($_SESSION['user']['search']['results'])) $this->smarty->assign('resultData',$_SESSION['user']['search']['results']);
			if (isset($_SESSION['user']['search']['replace']['index'] )) $this->smarty->assign('replaceIndex',$_SESSION['user']['search']['replace']['index']);
			
		} else {
		
			$this->redirect('search_index.php');
		
		}

		$this->smarty->assign('includeReplace',true);

        $this->printPage();
  
    }

    /**
     * 
     *
     * @access    public
     */
    public function searchResultsAction ()
    {

		$this->checkAuthorisation();
		
		$this->setPageName(_('Search results'));

		$this->setControllerMask('utilities','Search');

		if ($_SESSION['user']['search']['search']) {

			if (isset($_SESSION['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['user']['search']['search']);
			if (isset($_SESSION['user']['search']['results'])) $this->smarty->assign('resultData',$_SESSION['user']['search']['results']);
			if (isset($_SESSION['user']['search']['replace']['index'] )) $this->smarty->assign('replaceIndex',$_SESSION['user']['search']['replace']['index']);
			
		} else {
		
			$this->redirect('search_index.php');
		
		}

		$this->smarty->assign('includeReplace',false);

        $this->printPage();
  
    }



    /**
     * 
     *
     * @access    public
     */
    public function searchReplaceAllAction ()
    {

		$this->checkAuthorisation();

		$this->setPageName(_('Replace results'));
		
		$this->setControllerMask('utilities','Search');

		if ($_SESSION['user']['search']['results']['replace_all']!==true) $this->redirect('search_index.php');

		unset($_SESSION['user']['search']['results']['replace_all']);

		$this->processReplacements('*','replace');
		
		$this->smarty->assign('replacementResultCounters',$this->_replacementResultCounters);

        $this->printPage();
  
  
    }
	
    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'replace') {
            
            $this->ajaxDoReplace($this->requestData['id']);

        } else
        if ($this->requestData['action'] == 'skip') {
            
            $this->ajaxDoSkip($this->requestData['id']);

        } else
        if ($this->requestData['action'] == 'reset') {
            
            $this->ajaxDoReset($this->requestData['id']);

        }

        $this->printPage('../utilities/ajax_interface');

    }

	private function ajaxDoReplace($id)
	{

		$this->smarty->assign('returnText',json_encode($this->processReplacements($id,'replace')));

	}

	private function ajaxDoSkip($id)
	{

		$this->smarty->assign('returnText',json_encode($this->processReplacements($id,'skip')));

	}

	private function ajaxDoReset($id)
	{

		$this->smarty->assign('returnText',json_encode($this->processReplacements($id,'reset')));

	}


	/*
	*	id can be
	*		- an integer, representing one match in the search result
	*		- a string with the form 'module:module name', representing all matches within one module
	*		- an asterisk (*), representing all matches within the result
	*
	*	type can be
	*		- 'skip': do not replace, set status to 'skipped'
	*		- 'replace': replace match, set status to 'replaced'
	*		- 'reset': reset status to null (for development purposes)
	*/
	private function processReplacements($id,$type)
	{

		if (!isset($_SESSION['user']['search']['results']) || !isset($_SESSION['user']['search']['search']['replacement'])) return null;

		if (is_string($id) && substr($id,0,7)=='module:') {

			$thisModule = substr($id,7);

		} else {

			$thisModule = false;

		}

		if (!is_numeric($id) && $id!='*' && $thisModule===false) return null;
		
		if (is_numeric($id)) $id = intval($id);

		foreach((array)$_SESSION['user']['search']['results'] as $key1 => $val) {

			foreach((array)$val['results'] as $key2 => $module) {

				foreach((array)$module['data'] as $key3 => $data) {

					if (isset($data['replace']['matches'])) {
					
						if ($thisModule!==false && $module['label']!=$thisModule) continue;
	
						$this->doReplace(
							$_SESSION['user']['search']['search']['search'],
							$_SESSION['user']['search']['search']['replacement'],
							$data['replace'],
							(($thisModule!==false && $module['label']==$thisModule) ? '*' : $id),
							$type
						);

					}
	
				}

			}
		
		}

		return array($id,($type=='replace' ? 'replaced' : ($type=='reset' ? 'reset' : 'skipped' )));

	}

	private function doReplaceMatchCallback($matches)
	{
	
		// the stored results have the same keys as the new search results
		$storedId = $this->_replaceData['matches'][$this->_replaceCounter]['id'];
		$requestedId = $this->_replaceData['idToReplace'];
		$replaceStatus =
			isset($_SESSION['user']['search']['replace']['index'][$storedId]) ? $_SESSION['user']['search']['replace']['index'][$storedId] : null;

		$this->_replaceCounter++;

		// match the id from the request with the one stored in the session search results
		if (($storedId===$requestedId) || ($requestedId=='*')) {
		
			if ($replaceStatus===false) {

				if ($this->_replaceData['matches'][($this->_replaceCounter-1)][0]==$this->_replaceData['search']) {
				
					if ($this->_replaceData['type']=='replace') {
	
						$_SESSION['user']['search']['replace']['index'][$storedId] = 'replaced';
						
						$this->_replacementResultCounters['replaced']++;
		
						return $this->_replaceData['replacement'];
					
					} else {
	
						$_SESSION['user']['search']['replace']['index'][$storedId] = $this->_replaceData['type']=='skip' ? 'skipped' : false;
		
						$this->_replacementResultCounters['skipped']++;

						return $this->_replaceData['search'];
	
					}
	
	
				} else {
	
					$_SESSION['user']['search']['replace']['index'][$storedId] = 'mismatch';
		
					$this->_replacementResultCounters['mismatched']++;

					return $this->_replaceData['search'];
	
				}

			}

		}

		return $this->_replaceData['search'];
	
	}

	private function doReplace($search,$replace,$data,$id,$type)
	{

		// store relevant data for the callback function globally
		$this->_replaceData = array(
			'search' => $search,
			'replacement' => $replace,
			'idToReplace' => $id,
			'type' => $type,
		);

		// get the current data for this id
		$currentData = $this->models->{$data["model"]}->_get(array('id' => $data['id']));

		// walk through the search results, one column at the time
		foreach ((array)$data['matches'] as $column => $matches) {

			// check data was unchanged between searching and replacing
			if ($matches['original_md5'] = md5($currentData[0][$column])) {
			
				$this->_replaceData['matches'] = $matches;
				$this->_replaceCounter = 0;
	
				if (!empty($currentData[0][$column])) {
			
					// redo search on new data, replace that match id of requested replace
					$d = preg_replace_callback(
						$this->makeRegExpCompatSearchString($search,true),
						array($this,'doReplaceMatchCallback'),
						$currentData[0][$column]
					);

					// update to the new value					
					$result = $this->models->{$data["model"]}->update(
						array(
							$column => $d
						),
						$data['id']
					);

				}
				
			}

		}

	}

    /**
     * Index of project: introduction
     *
     * @access    public
     */
    private function _searchAction ($search,$modules,$freeModules)
    {

		if (strlen($search)>2) { 
		
			if (
				!isset($_SESSION['user']['search'][$search]) || 
				$this->noResultCaching
				) {
				
				$this->_replaceId = 0;

				$results = $this->doSearch($search,$modules,$freeModules);

				$results['numOfResults'] =
					(isset($results['introduction']['numOfResults']) ? $results['introduction']['numOfResults'] : 0) +
					(isset($results['species']['numOfResults']) ? $results['species']['numOfResults'] : 0) +
					(isset($results['modules']['numOfResults']) ? $results['modules']['numOfResults'] : 0)  +
					(isset($results['dichkey']['numOfResults']) ? $results['dichkey']['numOfResults'] : 0)  +
					(isset($results['literature']['numOfResults']) ? $results['literature']['numOfResults'] : 0)  +
					(isset($results['glossary']['numOfResults']) ? $results['glossary']['numOfResults'] : 0)  +
					(isset($results['matrixkey']['numOfResults']) ? $results['matrixkey']['numOfResults'] : 0)  +
					(isset($results['content']['numOfResults']) ? $results['content']['numOfResults'] : 0)  +
					(isset($results['map']['numOfResults']) ? $results['map']['numOfResults'] : 0) 
					;

				$results['numOfReplacements'] =
					(isset($results['introduction']['numOfReplacements']) ? $results['introduction']['numOfReplacements'] : 0) +
					(isset($results['species']['numOfReplacements']) ? $results['species']['numOfReplacements'] : 0) +
					(isset($results['modules']['numOfReplacements']) ? $results['modules']['numOfReplacements'] : 0)  +
					(isset($results['dichkey']['numOfReplacements']) ? $results['dichkey']['numOfReplacements'] : 0)  +
					(isset($results['literature']['numOfReplacements']) ? $results['literature']['numOfReplacements'] : 0)  +
					(isset($results['glossary']['numOfReplacements']) ? $results['glossary']['numOfReplacements'] : 0)  +
					(isset($results['matrixkey']['numOfReplacements']) ? $results['matrixkey']['numOfReplacements'] : 0)  +
					(isset($results['content']['numOfReplacements']) ? $results['content']['numOfReplacements'] : 0)  +
					(isset($results['map']['numOfReplacements']) ? $results['map']['numOfReplacements'] : 0) 
					;
						
				$_SESSION['user']['search'][$search]['results'] = $results;

				$this->_replaceId = null;

			} else {

				$results = $_SESSION['user']['search'][$search]['results'];

			}
			
			$_SESSION['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
			$_SESSION['user']['search']['lastSearch'] = $search;
				
			return $results;


		} else {
		
			$this->addMessage(sprintf(_('Search term too short. Minimum is %s characters.'),3));
			
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

	private function doSearch($search,$modules,$freeModules)
	{

		$species = $this->searchSpecies($search);

		return array(
			'introduction' =>
				(is_array($modules) && in_array('introduction',$modules) ? $this->searchIntroduction($search) : null),
			'glossary' =>
				(is_array($modules) && in_array('glossary',$modules) ? $this->searchGlossary($search) : null),
			'literature' => 
				(is_array($modules) && in_array('literature',$modules) ? $this->searchLiterature($search) : null),
			'species' => 
				(is_array($modules) && in_array('species',$modules) ? $species : null),
			'dichkey' => 
				(is_array($modules) && in_array('key',$modules) ? $this->searchDichotomousKey($search) : null),
			'matrixkey' => 
				(is_array($modules) && in_array('matrixkey',$modules) ? $this->searchMatrixKey($search) : null),
			'map' => 
				(is_array($modules) && in_array('mapkey',$modules) ? $this->searchMap($search,$species) : null),
			'content' => 
				(is_array($modules) && in_array('content',$modules) ? $this->searchContent($search) : null),
			'modules' => 
				$this->searchModules($search,$freeModules)	
		);

	}
	
	private function makeTaxonList($records)
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

		if (!isset($_SESSION['user']['species']['categories'])) {

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
						//'language_id' => $this->getCurrentLanguageId(),
						'page_id' => $val['id']
					),
					'columns'=>'title'));
		
				$tp[$key]['title'] = $tpt[0]['title'];
		
				if ($val['def_page'] == 1) $_SESSION['user']['species']['defaultCategory'] = $val['id'];
			
			}
			
			$_SESSION['user']['species']['categories'] = $tp;

		}

		return $_SESSION['user']['species']['categories'];
	
	}

	private function escapeRegExSpecialChars($matches)
	{
	
		return '\\'.$matches[0];
	
	}

	private function makeRegExpCompatSearchString($s,$phpCompat=false)
	{
	
		$s = trim($s);
		
		$s = preg_replace_callback('/(\^|\$|\(|\)|\<|\>|\{|\}|\[|\]|\\|\||\.|\*|\+|\?)/',array(&$this,'escapeRegExSpecialChars'),$s);

		// if string enclosed by " take it literally		
		if (preg_match('/^"(.+)"$/',$s))
			return
				($phpCompat ? '/' : '').
				'('.(substr($s,1,strlen($s)-2)).')'.
				($phpCompat ? '/i' : '');

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0)
			return
				($phpCompat ? '/' : '').
				($s).
				($phpCompat ? '/i' : '');

		$s = str_replace(' ','|',$s);

		return
			($phpCompat ? '/' : '').
			'('.($s).')'.
			($phpCompat ? '/i' : '');
	
	}

	private function getColumnMatches($search,$values,$fields,&$counter=null)
	{
	
		$results = array();
	
		if (!is_array($fields))
			$columns[] = $fields;
		else
			$columns = $fields;
			
		foreach($columns as $key => $column) {
		
			$content = $values[$column];

			preg_match_all($this->makeRegExpCompatSearchString($search,true),$content,$matches,PREG_OFFSET_CAPTURE);

			foreach((array)$matches[0] as $mK => $mV) {

				$matches[0][$mK]['id'] = $this->_replaceId++;
				$this->_replaceStatusIndex[$matches[0][$mK]['id']] = false;
				//$matches[0][$mK]['occurrence'] = $mK;
				//$matches[0][$mK]['column'] = $column;
				//$matches[0][$mK]['original_md5'] = md5($values[$column]);
				
				$matches[0][$mK]['highlighted'] =
					($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? '...' : '').
					htmlentities(
						substr(
							$content,
							($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $mV[1]-$this->controllerSettings['excerptLengthLeft'] : 0),
							($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $this->controllerSettings['excerptLengthLeft'] : $mV[1])
						)
					).
					'<span class="stringToReplace">'.
					$mV[0].
					'</span>'.
					htmlentities(
						substr(
							$content,
							$mV[1]+strlen($mV[0]),
							$this->controllerSettings['excerptLengthRight']
						)
					).					
					($mV[1]+strlen($mV[0])+$this->controllerSettings['excerptLengthRight'] >= strlen($content) ? '' : '...');
	
			}

			//$results = array_merge($results,$matches[0]);
			if (!empty($matches[0])) {

				$results[$column] = $matches[0];
				$results[$column]['original_md5'] = md5($values[$column]);

			}
		}
		
		$counter = (is_null($counter) ? 0 : $counter) + count((array)$results);

		return empty($results) ? null : $results;
	
	}


	private function searchSpecies($search,$extensive=true)
	{

		$taxa = $synonyms = $commonnames = $content = $media = array();
		
		$replaceCountTaxa = $replaceCountContent = $replaceCountSynonym = $replaceCountCommon = $replaceCountMedia = 0;

		$ranks = $this->getProjectRanks(array('idsAsIndex'=>true));

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon,id as taxon_id,taxon as label,rank_id'
			)
		);

		foreach((array)$taxa as $key => $val)  {
		
			$taxa[$key]['rank'] = $ranks[$val['rank_id']]['rank'];

			$taxa[$key]['replace'] = array(
				'model' => $this->models->Taxon->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('taxon'),$replaceCountTaxa),
			);
			
			$taxa[$key]['url'] = '../species/edit.php?id='.$val['id'];
				
		}

		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,synonym,taxon_id,synonym as label,\'names\' as cat'
			)
		);

		foreach((array)$synonyms as $key => $val)  {
		
			$synonyms[$key]['replace'] = array(
				'model' => $this->models->Synonym->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('synonym'),$replaceCountSynonym)
			);
			
			$synonyms[$key]['url'] = '../species/synonyms.php?id='.$val['id'];
				
		}

		$commonnames = $this->models->Commonname->_get(
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
					commonname,
					transliteration,
					if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) as label,
					\'names\' as cat'
			)
		);	
	
		foreach((array)$commonnames as $key => $val) {

			$l = $this->models->Language->_get(array('id'=>$val['language_id']));
			$commonnames[$key]['language'] = $l['language'];

			$commonnames[$key]['replace'] = array(
				'model' => $this->models->Commonname->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('commonname','transliteration'),$replaceCountCommon)
			);
			
			$commonnames[$key]['url'] = '../species/common.php?id='.$val['id'];

		}

		if ($extensive) {

			$content = $this->models->ContentTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'publish' => 1,
						'content regexp' => $this->makeRegExpCompatSearchString($search)
					),
					'columns' => 'id,taxon_id,content,page_id'
				)
			);

			foreach((array)$content as $key => $val) {

				$content[$key]['replace'] = array(
					'model' => $this->models->ContentTaxon->getClassName(),
					'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
					'matches' => $this->getColumnMatches($search,$val,array('content'),$replaceCountContent)
				);
				
				$content[$key]['url'] = '../species/taxon.php?id='.$val['taxon_id'].'&page='.$val['page_id'];

			}

			/*
			$media1 = $this->models->MediaTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'file_name regexp' => $this->makeRegExpCompatSearchString($search)
					),
				'columns' => 'id,taxon_id,id as media_id,file_name as label,\'media\' as cat'
				)
			);
			*/
	
			$media = $this->models->MediaDescriptionsTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'description regexp' => $this->makeRegExpCompatSearchString($search)
					),
				'columns' => 'id,media_id,description,description as content,\'media\' as cat'
				)
			);

			//$media = array_merge((array)$media1,(array)$media2);

			foreach((array)$media as $key => $val) {

				$media[$key]['replace'] = array(
					'model' => $this->models->MediaDescriptionsTaxon->getClassName(),
					'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
					'matches' => $this->getColumnMatches($search,$val,array('description'),$replaceCountMedia)
				);
				
				$media[$key]['url'] = '../species/media.php?id='.$val['id'];

			}
			
		}		
		
		$d = array_merge((array)$synonyms,(array)$commonnames,(array)$content,(array)$media);

		return array(
			'results' => array(
				array(
					'label' => _('Species names'),
					'data' => $taxa, // when changing the label 'Species names', do the same in searchMap()
					'numOfResults' => count((array)$taxa),
					'numOfReplacements' => $replaceCountTaxa
				),
				array(
					'label' => _('Species descriptions'),
					'data' => $content,
					'numOfResults' => count((array)$content),
					'numOfReplacements' => $replaceCountContent
				),
				array(
					'label' => _('Species synonyms'),
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms),
					'numOfReplacements' => $replaceCountSynonym
				),
				array(
					'label' => _('Species common names'),
					'data' => $commonnames,
					'numOfResults' => count((array)$commonnames),
					'numOfReplacements' => $replaceCountCommon
				),
				array(
					'label' => _('Species media'),
					'data' => $media,
					'numOfResults' => count((array)$media),
					'numOfReplacements' => $replaceCountMedia
				),
			),
			//'taxonList' => $this->makeTaxonList($d),
			//'categoryList' => $this->makeCategoryList(),
			'numOfResults' => count((array)$d)+count((array)$taxa),
			'numOfReplacements' => $replaceCountTaxa + $replaceCountContent + $replaceCountSynonym + $replaceCountCommon + $replaceCountMedia
		);

	}

	private function getSpeciesLookupList($search)
	{

		$s = $this->searchSpecies($search,false);

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

	private function searchModules($search,$freeModules=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();	
	
		if ($freeModules!==false) $d['module_id in']  = implode(',',$freeModules).')';					

		// get appropriate free modules
		$modules = $this->models->FreeModuleProject->_get(
			array(
				'id' => $d,
				'columns' => 'id,module',
				'fieldAsIndex' => 'id'
			)
		);

		// get all matching content in all appropriate free modules
		$content = $this->models->ContentFreeModule->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().' and
					(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\' or
					content regexp \''.$this->makeRegExpCompatSearchString($search).'\')'.
					($freeModules!==false ? ' and module_id in ('.implode(',',$freeModules).')' : ''),
				'columns' => 'id,page_id,module_id,topic,content',
				'order' => 'module_id'
			)
		);
		
		$replaceCount = array();
		$replaceCountTot = $resultCountTot = 0;

		foreach((array)$content as $key => $val) {

			$matches = $this->getColumnMatches($search,$val,array('topic','content'));

			if (!is_null($matches)) {

				$val['replace'] = array(
					'model' => $this->models->ContentFreeModule->getClassName(),
					'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
					'matches' => $matches
				);
				
				$val['url'] = '../module/edit.php?id='.$val['page_id'];
				
				$replaceCount[$modules[$val['module_id']]['module']] = 
					(isset($replaceCount[$modules[$val['module_id']]['module']]) ? $replaceCount[$modules[$val['module_id']]['module']] : 0) + 
					count((array)$matches);
					
				$replaceCountTot = $replaceCountTot + count((array)$matches);

			}
		
			$resultCountTot++;

			$results[$modules[$val['module_id']]['module']][] = $val;

		}

		$r = null;

		if (isset($results)) {

			foreach ($results as $key => $val)

				 $r[] = array(
					'label' => $key, 
					'data' => $val, 
					'numOfResults' => count((array)$val), 
					'numOfReplacements' => $replaceCount[$key]
				);

		}

		return array(
			'results' => isset($r) ? $r : null,
			'numOfResults' => $resultCountTot,
			'numOfReplacements' => $replaceCountTot
		);

	}
	
	private function getModuleLookupList($search)
	{
	
		$l = $this->searchModules($search);
	
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

	private function searchMatrixKey($search)
	{

		$replaceCountMatrices = $replaceCountChars = $replaceCountStates = 0;

		$matrices = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'name regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,matrix_id,name'
			)
		);

		foreach((array)$matrices as $key => $val) {

			$matrices[$key]['replace'] = array(
				'model' => $this->models->MatrixName->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('name'),$replaceCountMatrices)
			);
			
			$matrices[$key]['url'] = '../matrixkey/matrix.php?id='.$val['matrix_id'];

		}

		$characteristics = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'label regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,characteristic_id,label'
			)
		);

		foreach((array)$characteristics as $key => $val) {

			$characteristics[$key]['replace'] = array(
				'model' => $this->models->CharacteristicLabel->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('label'),$replaceCountChars)
			);
			
			$characteristics[$key]['url'] = '../matrixkey/char.php?id='.$val['characteristic_id'];

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


		$states = $this->models->CharacteristicLabelState->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().' and 
					(label regexp \''.$this->makeRegExpCompatSearchString($search).'\' or
					text regexp \''.$this->makeRegExpCompatSearchString($search).'\')',
				'columns' => 'id,state_id,label,text'
			)
		);

		foreach((array)$states as $key => $val) {

			$states[$key]['replace'] = array(
				'model' => $this->models->CharacteristicLabelState->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('label','text'),$replaceCountStates)
			);
			
			$cs = $this->models->CharacteristicState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['state_id'],
					),
					'columns' => 'characteristic_id'
				)
			);

			$states[$key]['url'] = '../matrixkey/state.php?char='.$cs[0]['characteristic_id'].'&id='.$val['state_id'];

			$cl = $this->models->CharacteristicLabel->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
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
				),
				'columns' => 'matrix_id,name',
				'fieldAsIndex' => 'matrix_id'
			)
		);

		return array(
			'results' => array(
				array(
					'label' => _('Matrix key matrices'),
					'data' => $matrices,
					'numOfResults' => count((array)$matrices),
					'numOfReplacements' => $replaceCountMatrices
				),
				array(
					'label' => _('Matrix key characteristics'),
					'data' => $characteristics,
					'numOfResults' => count((array)$characteristics),
					'numOfReplacements' => $replaceCountChars
				),
				array(
					'label' => _('Matrix key states'),
					'data' => $states,
					'numOfResults' => count((array)$states),
					'numOfReplacements' => $replaceCountStates
				)
			),
			'numOfResults' => count((array)$matrices)+count((array)$characteristics)+count((array)$states),
			'matrices' => $matrixNames,
			'numOfReplacements' => $replaceCountMatrices+$replaceCountChars+$replaceCountStates
		);

	}

	private function searchDichotomousKey($search)
	{

		$replaceCountSteps = $replaceCountChoices = 0;
		
		$choices = $this->models->ChoiceContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'choice_txt regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,choice_id,choice_txt as content,choice_txt'
			)
		);

		foreach((array)$choices as $key => $val) {

			$choices[$key]['replace'] = array(
				'model' => $this->models->ChoiceContentKeystep->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('choice_txt'),$replaceCountChoices)
			);

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

				$choices[$key]['url'] = '../key/step_show.php?id='.$step[0]['keystep_id'];

				$ck = $this->models->ContentKeystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'keystep_id' => $step[0]['keystep_id']
						),
						'columns' => 'keystep_id,title'
					)
				);

				$choices[$key]['title'] = $ck[0]['title'];
				//$choices[$key]['marker'] = $this->showOrderToMarker($step[0]['show_order']);

				$step = $this->models->Keystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'id' => $step[0]['keystep_id']
						),
						'columns' => 'number'
					)
				);

				$choices[$key]['number'] = $step[0]['number'];

			}
		
		}


		$steps = $this->models->ContentKeystep->_get(
			array(
				'where' => 
					'project_id = '. $this->getCurrentProjectId().' and
					(title regexp \''. $this->makeRegExpCompatSearchString($search).'\' or
					content regexp \''. $this->makeRegExpCompatSearchString($search).'\')',
				'columns' => 'id,keystep_id,title,content'
			)
		);

		foreach((array)$steps as $key => $val) {

			$steps[$key]['replace'] = array(
				'model' => $this->models->ContentKeystep->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('title','content'),$replaceCountSteps)
			);
			
			$steps[$key]['url'] = '../key/step_show.php?id='.$val['keystep_id'];

			$step = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['keystep_id']
					),
					'columns' => 'number'
				)
			);

			$steps[$key]['number'] = $step[0]['number'];

		}

		return array(
			'results' => array(
				array(
					'label' => _('Dichotomous key steps'),
					'data' => $choices,
					'numOfResults' => count((array)$choices),
					'numOfReplacements' => $replaceCountSteps
				),
				array(
					'label' => _('Dichotomous key choices'),
					'data' => $steps,
					'numOfResults' => count((array)$steps),
					'numOfReplacements' => $replaceCountChoices
				)
			),
			'numOfResults' => count((array)$choices)+count((array)$steps),
			'numOfReplacements' => $replaceCountSteps+$replaceCountChoices
		);

	}

	private function searchLiterature($search,$extensive=true)
	{

		/*
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
					'id,author_first,
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
					) as author_full',
			)
		);
		*/
		
		$books = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'text regexp ' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 
					'id,author_first,
					concat(year(`year`),ifnull(suffix,\'\')) as year,
					text,
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
		
		$replaceCount = 0;
		
		foreach((array)$books as $key => $val) {

			$books[$key]['replace'] = array(
				'model' => $this->models->Literature->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('text'),$replaceCount)
			);
			
			$books[$key]['url'] = '../literature/edit.php?id='.$val['id'];

		}

		return array(
			'results' => array(
				array(
					'label' => _('Literary references'),
					'data' => $books,
					'numOfResults' => count((array)$books),
					'numOfReplacements' => $replaceCount
				)
			),
			'numOfResults' => count((array)$books),
			'numOfReplacements' => $replaceCount
		);

	}
	
	private function getLiteratureLookupList($search)
	{
	
		$l = $this->searchLiterature($search,false);
	
		$d = array();
		
		foreach((array)$l['results'] as $val) {
		
			if (is_array($val['data'])) {

				foreach($val['data'] as $val2) {

					$d[] = array(
						'id' => $val2['id'],
						'label' => $val2['author_full'].($val2['year'] ? ', '.$val2['year'] : ''),
						'source' => $val['label'],
						'url'  => '../literature/term.php?id='.$val2['id']
					);

				}

			}

		}
		
		return $d;
	
	}

	private function searchGlossary($search,$extensive=true)
	{

		$replaceCountGloss = $replaceCountSynonym = 0;

		$gloss = $this->models->Glossary->_get(
			array(
				'where' =>
					'project_id = '.$this->getCurrentProjectId().' and
					(
						term regexp \''.$this->makeRegExpCompatSearchString($search).'\''.
						($extensive ? 'or definition regexp \''.$this->makeRegExpCompatSearchString($search).'\'' : '').	
					')'
				,
				'columns' => 'id,term,definition'
			)
		);

		foreach((array)$gloss as $key => $val) {
		
			$gloss[$key]['replace'] = array(
				'model' => $this->models->Glossary->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('term','definition'),$replaceCountGloss),
			);

			$gloss[$key]['url'] = '../glossary/edit.php?id='.$val['id'];
	
		}

		$synonyms = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(), 
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,glossary_id,synonym,language_id'
			)
		);
	
		foreach((array)$synonyms as $key => $val) {
		
			$g = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['glossary_id']
					),
					'columns' => 'id,term'
				)
			);
	
			$synonyms[$key]['synonym'] = $g[0]['term'];

			$synonyms[$key]['replace'] = array(
				'model' => $this->models->GlossarySynonym->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('synonym'),$replaceCountSynonym)
			);

			$synonyms[$key]['url'] = '../glossary/edit.php?id='.$g[0]['id'];

		}

		/*
		if ($extensive) {

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
							//'language_id' => $this->getCurrentLanguageId(), 
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
			
		}

		$media = isset($d) ? $d : null;
		
		*/

		return array(
			'results' => array(
				array(
					'label' => _('Glossary terms'),
					'data' => $gloss,
					'numOfResults' => count((array)$gloss),
					'numOfReplacements' => $replaceCountGloss
				),
				array(
					'label' => _('Glossary synonyms'),
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms),
					'numOfReplacements' => $replaceCountSynonym
				)
				/*					,
				array(
					'label' => _('Glossary media'),
					'data' => $media,
					'numOfResults' => count((array)$media)
				)
				*/
			),
			'numOfResults' => count((array)$gloss)+count((array)$synonyms), //+count((array)$media),
			'numOfReplacements' => $replaceCountGloss  + $replaceCountSynonym
		);

	}
	
	private function getGlossaryLookupList($search)
	{

		$g = $this->searchGlossary($search,false);

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

	private function searchContent($search)
	{

		$content = $this->models->Content->_get(
			array(
				'where' =>
					'project_id = '. $this->getCurrentProjectId().' and
					(
						subject regexp \''.$this->models->Content->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
						content regexp \''.$this->models->Content->escapeString($this->makeRegExpCompatSearchString($search)).'\'
					)',
				'columns' => 'id,subject,content,language_id'
			)

		);
		
		$replaceCount = 0;

		foreach((array)$content as $key => $val) {

			$content[$key]['replace'] = array(
				'model' => $this->models->Content->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('subject','content'),$replaceCount)
			);
			
			$content[$key]['url'] = '../content/content.php?id='.$val['id'];

		}

		return array(
			'results' => array(
				array(
					'label' => _('Navigator'),
					'data' => $content,
					'numOfResults' => count((array)$content),
					'numOfReplacements' => $replaceCount
				)
			),
			'numOfResults' => count((array)$content),
			'numOfReplacements' => $replaceCount
		);

	}

	private function searchMap($search,$species)
	{

		$titles = $this->models->GeodataTypeTitle->_get(
			array(
				'where' =>
					'project_id = '. $this->getCurrentProjectId().' and
					title regexp \''.$this->models->Content->escapeString($this->makeRegExpCompatSearchString($search)).'\'',
				'columns' => 'id,title'
			)
		);

		$replaceCount = 0;
		
		foreach((array)$titles as $key => $val) {

			$titles[$key]['replace'] = array(
				'model' => $this->models->GeodataTypeTitle->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('title'),$replaceCount)
			);

			$titles[$key]['url'] =  '../mapkey/data_types.php';

		}

		return array(
			'results' => array(
				array(
					'label' => _('Geographic datatype'),
					'data' => $titles,
					'numOfResults' => (isset($titles) ? count((array)$titles) : 0),
					'numOfReplacements' => $replaceCount
				),
			),
			'numOfResults' => isset($geo) ? count((array)$geo) : 0,
			'numOfReplacements' => $replaceCount
		);

	}

	private function searchCommonNames($search=null)
	{

		return $this->models->Commonname->_get(
			array(
				'where' =>
					'project_id  = '.$this->getCurrentProjectId().
						($search ? 
							' and
							(
								commonname regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
								transliteration regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\'
							)' : 
							''
						),
				'columns' => 
					'taxon_id as id,'.
					($search ? '
						if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) ' :
						'commonname' ) .' as label,
						transliteration,
					\'common name\' as source, 
					concat(\'views/species/common.php?id=\',taxon_id) as url,
					language_id'
			)
		);

	}

	private function searchSynonyms($search=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();
		
		if ($search) $d['synonym regexp'] = $this->makeRegExpCompatSearchString($search);

		return $this->models->Synonym->_get(
			array(
				'id' => $d,
				'columns' => 'taxon_id as id,synonym as label,\'synonym\' as source, concat(\'views/species/synonyms.php?id=\',taxon_id) as url'
			)
		);

	}
	
	private function searchIntroduction($search=null)
	{

		$replaceCount = 0;

		$content = $this->models->ContentIntroduction->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().' and
					(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\' or
					content regexp \''.$this->makeRegExpCompatSearchString($search).'\')',
				'columns' => 'id,topic,content'
			)
		);

		foreach((array)$content as $key => $val)  {
		
			$content[$key]['replace'] = array(
				'model' => $this->models->ContentIntroduction->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('topic','content'),$replaceCount),
			);
			
			$content[$key]['url'] = '../introduction/edit.php?id='.$val['id'];
				
		}

		return array(
			'results' => array(
				array(
					'label' => _('Introduction'),
					'data' => $content,
					'numOfResults' => count((array)$content),
					'numOfReplacements' => $replaceCount
				)
			),
			'numOfResults' => count((array)$content),
			'numOfReplacements' => $replaceCount
		);

	}
	
	
	private function getLookupList($search)
	{
	
		/*
		excluded:
		- Introduction / other content
		- Dichotomous key
		- Matrix key 
		- Map key
		*/

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				array_merge(
					$this->getGlossaryLookupList($search),
					$this->getLiteratureLookupList($search),
					$this->getSpeciesLookupList($search),
					$this->getModuleLookupList($search)
				),
				$this->controllerBaseName,
				null,
				true
			)
		);

	}

	private function getMatrices()
	{

		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_names' => 1
				)
			)
		);
		
		foreach((array)$m as $key => $val) {

			$mn = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $val['id'],
						'language_id' => $this->getCurrentLanguageId()
					),
					'columns' => 'name'
				)
			);

			$m[$key]['name'] = $mn[0]['name'];

		}
			
		return $m;
	
	}

	private function getCharacteristic($id)
	{

		$c = $this->models->Characteristic->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		if (!isset($c)) return;
		
		$char = $c[0];

		$cl = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					//'language_id' => $this->getCurrentLanguageId(),
					'characteristic_id' => $id,
					)
			)
		);

		$char['label'] = $cl[0]['label'];

		return $char;

	}

	private function getCharacteristicStateLabelOrText($id,$type='label')
	{

		if (!isset($id)) return;
        
		$cls = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'state_id' => $id,
					'language_id' => $this->getCurrentLanguageId()
				),
				'columns' => 'text,label'
			)
		);

		return $type=='text' ? $cls[0]['text'] : $cls[0]['label'];

	}

	private function getCharacteristicState($id)
	{

		if (!isset($id)) return;

		$cs = $this->models->CharacteristicState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				),
				'columns' => 'id,file_name,lower,upper,mean,sd'
			)
		);

		$cs[0]['label'] = $this->getCharacteristicStateLabelOrText($cs[0]['id']);

		return $cs[0];

	}

	private function ORIGprocessReplacements($id,$type)
	{

		if (!isset($_SESSION['user']['search']['results'])) return null;

		if (is_string($id) && substr($id,0,7)=='module:') {

			$thisModule = substr($id,7);

		} else {

			$thisModule = false;

		}

		if (!is_numeric($id) && $id!='*' && $thisModule===false) return null;

		foreach((array)$_SESSION['user']['search']['results'] as $key1 => $val) {

			foreach((array)$val['results'] as $key2 => $module) {

				foreach((array)$module['data'] as $key3 => $data) {

					if (isset($data['replace']['matches'])) {

						foreach((array)$data['replace']['matches'] as $key4 => $match) {

							if ($match['id']==$id || ($module!==false && $module['label']==$thisModule) || $id=='*') {

								$modelData = $_SESSION['user']['search']['results'][$key1]['results'][$key2]['data'][$key3]['replace'];

								$replaceData = &$_SESSION['user']['search']['results'][$key1]['results'][$key2]['data'][$key3]['replace']['matches'][$key4];
							
								if ($type=='skip') {

									if($replaceData['status']===false) {

										$replaceData['status'] = 'skipped';
										
										if ($thisModule===false && $id!='*') return array($id,'skipped');

									} else {

										if ($thisModule===false && $id!='*') return array($id,'no action');

									}

								} else
								if ($type=='replace') {

									if($replaceData['status']===false) {

										if ($this->doReplace($modelData,$replaceData)) {

											$replaceData['status'] = 'replaced';

											if ($thisModule===false && $id!='*') return array($id,'replaced');

										} else {

											if ($thisModule===false && $id!='*') return array($id,'replace failed');

										}

									} else {
									
										if ($thisModule===false && $id!='*') return array($id,'no action');
									
									}

								} else
								if ($type=='reset') {

									$replaceData['status'] = false;

									if ($thisModule===false && $id!='*') return array($id,'reset');

								} else {

									if ($thisModule===false && $id!='*') return array($id,'no action');

								}
								
							}

						}

					}
	
				}

			}
		
		}

		return array('*',($type=='replace' ? 'replaced' : ($type=='reset' ? 'reset' : 'skipped' )));
	
	}

	private function ORIGdoReplace($modelData,$matchData)
	{

		// viva readability
		$searchedFor = $_SESSION['user']['search']['search']['search'];
		$replaceWith = $_SESSION['user']['search']['search']['replacement'];
		$locationOfSearchTerm = $matchData[1];

		// removing enbclosing double quotes
		$searchedFor = preg_match('/^"(.+)"$/',$searchedFor) ? trim($searchedFor, '"') : $searchedFor;

		// check whether the string is still what we want to replace (just to be on the safe side)
		$willReplace = substr($matchData['original'],$locationOfSearchTerm,strlen($searchedFor));

		if (strtolower($searchedFor)!==strtolower($willReplace)) return false;

		// match initial capital
		$init = substr($matchData['original'],$locationOfSearchTerm,1);
		$replaceWith = ($init == strtoupper($init)) ? ucfirst($replaceWith) : $replaceWith;

		// create new data by replacing the old string (using substring to make sure we *only* replace the term starting at $locationOfSearchTerm)
		$newData =
			substr($matchData['original'],0,$locationOfSearchTerm).
			$replaceWith.
			substr($matchData['original'],$locationOfSearchTerm + strlen($searchedFor));

		// replacing the old content with the new ($modelData["model"] holds the name of the appropriate model, $modelData['id'] is an array of relevant id's)
		$result = $this->models->{$modelData["model"]}->update(
			array(
				$matchData['column'] => $newData,
			),
			$modelData['id']
		);

		return $result;

	}

}
