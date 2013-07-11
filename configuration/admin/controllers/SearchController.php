<?php

/*

STRIP TAGS AND SHIT FROM SEARCH STRING!!!


	search is case-insensitive! 
	the php post-filtering is designed to allow for case-sensitivity, but it is not actually implemented. 
	as the full text search is insensitive by default (unless we start altering the collation of the indexed 
	columns), searches that have no literal bits ("...") will be harder to turn into case sensitive ones.



 Some words are ignored in full-text searches:

    Any word that is too short is ignored. The default minimum length of words that are found by full-text searches is four characters.

    Words in the stopword list are ignored. A stopword is a word such as “the” or “some” that is so common that it is considered to have zero semantic value. There is a built-in stopword list, but it can be overwritten by a user-defined list. 

The default stopword list is given in Section 12.9.4, “Full-Text Stopwords”. 
	http://dev.mysql.com/doc/refman/5.0/en/fulltext-stopwords.html
The default minimum word length and stopword list can be changed as described in Section 12.9.6, “Fine-Tuning MySQL Full-Text Search”. 

//WHERE MATCH(title, body) AGAINST ('vnurk vnork' in boolean mode) // returns AND vnurk AND vnork

*/

include_once ('Controller.php');

class SearchController extends Controller
{

	private $_minSearchLength = 3;
	private $_maxSearchLength = 25;
	private $_searchStringGroupDelimiter = '"';
	private $_excerptPreMatchLength = 25;
	private $_excerptPostMatchLength = 25;
	private $_excerptPrePostMatchString = '...';

	/*
	public $noResultCaching = true;
	private $_replaceId = 1;
	private $_replaceCounter = 0;
	private $_replaceData = null;
	private $_replaceStatusIndex = array();
	private $_replacementResultCounters = array('mismatched' => 0, 'skipped' => 0, 'replaced' => 0);
	*/

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
		'occurrence_taxon',
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
	

    public function __construct ()
    {

        parent::__construct();
		$this->initialize();

    }


    public function __destruct ()
    {
        
        parent::__destruct();
    
    }
	
	
	private function validateSearchString($s)
	{
		return
			(strlen($s)>=$this->_minSearchLength) &&  // is it long enough?
			(strlen($s)<=$this->_maxSearchLength);    // is it short enough?
	}
	

	private function tokenizeSearchString($s)
	{

		/*
			splits search string in groups delimited by ". if there's an 
			uneven number the last one is ignored.
		*/	

		$parts = preg_split('/('.$this->_searchStringGroupDelimiter.')/i',$s,-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$b = null;    // buffer
		$t = array(); // resulting array of parts
		$r = false;   // "rec"-toggle

		foreach($parts as $val) {
			if ($val=='"') {
				// if "rec" is on, add the concatenated string to the results and reset the buffer
				if ($r) {
					if (!empty($b))
						array_push($t,$b);
					$b = null;
				}
				// and toggle "rec"
				$r = !$r;
			} else {
				// concatenate consecutive parts when "rec" is on (i.e., we are inside a "...")
				if($r)
					$b .= $val;
				// else split the part on spaces and add them as separate results
				else
					$t = array_merge($t,explode(' ',$val));
			}
		}
		// take out the empty ones and return
		return array_filter($t);

	}
	
	
	private function prefabFullTextMatchString($s)
	{
		// make tokens into a single string for mysql MATCH statement; add *'s to enable partial matches
		$r = '';
		foreach((array)$s as $val) {
			foreach(explode(' ',$val) as $b)
				$r .= '*'.$b.'* ';
		}
		return trim($r);
	}


	private function doesSearchStringContainLiterals($s)
	{
		foreach((array)$s as $val) {
			if (strpos($val,' ')!==false)
				return true;
		}
		return false;
	}

	private function stripTagsForSearchExcerpt($s)
	{
		// replace <br> and ends of block elements with spaces to avoid words being concatenated
		return strip_tags(str_replace('  ',' ',str_ireplace(array('<br>','<br />','</p>','</div>','</td>','</li>','</blockquote>','</h1>','</h2>','</h3>','</h4>','</h5>','</h6>'),' ',$s)));
	}


	private function filterResultsWithTokenizedSearch($p)
	{

		/*
			$p[0] : array of search parameters:
				$s[S_TOKENIZED_SEARCH]	: array of tokens
				$s[S_FULLTEXT_STRING]	: string for fulltext search (not used in this function)
				$s[S_CONTAINS_LITERALS]	: boolean, indicates the presence of literal token(s) ("aa bb")
			$p[1] : array of results
			$p[2] : array of fields to check (optional; defaults to array('label','content'))			
		*/
	
		$s = isset($p[0]) ? $p[0] : null;
		$r = isset($p[1]) ? $p[1] : null;

		if (!isset($s) || !isset($s[S_CONTAINS_LITERALS]) || !isset($s[S_TOKENIZED_SEARCH]) ||$s[S_CONTAINS_LITERALS]==false) return $r;

		if ($s[S_CONTAINS_LITERALS]) {
			
			$filtered = array();

			// loop all results
			foreach((array)$r as $result) {

				if (!isset($result[__CONCAT_RESULT__])) continue;

				$d = $this->stripTagsForSearchExcerpt($result[__CONCAT_RESULT__]);
				
				$match = false;

				// loop through all tokens
				foreach((array)$s[S_TOKENIZED_SEARCH] as $token) {

					if ($match==true) break;

					// match if token exists in value of specific field of each result
					$match = $s[S_IS_CASE_SENSITIVE] ? strpos($d,$token)!==false : stripos($d,$token)!==false;

				}
				
				if ($match) {
					unset($result[__CONCAT_RESULT__]);
					array_push($filtered,$result);
				}

			}
			
			return $filtered;

		}
		
		// just in case
		return $r;

	}


	private function getExcerptsSurroundingMatches($p)
	{

		$s = isset($p[0]) ? $p[0] : null;
		$r = isset($p[1]) ? $p[1] : null;
		$f = isset($p[2]) ? $p[2] : array('label','content');
		
		if (!isset($s) || !isset($s[S_TOKENIZED_SEARCH]) ) return $r;

		foreach((array)$r as $rKey => $result) {

			foreach((array)$f as $fKey => $field) {

				$fullmatches = array();

				if (isset($result[$field])) {

					$stripped = $this->stripTagsForSearchExcerpt($result[$field]);

					foreach((array)$s[S_TOKENIZED_SEARCH] as $token) {

						$matches=array();
						preg_match_all('/'.$token.'/'.($s[S_IS_CASE_SENSITIVE] ? '' : 'i'),$stripped,$matches,PREG_OFFSET_CAPTURE);

						if (isset($matches[0])) {
							foreach((array)$matches[0] as $match) {
								if (isset($match[0])) $fullmatches[]=$match;
							}	
						}
						
						unset($matches);
					
					}

					foreach((array)$fullmatches as $match) {
						$start = ($match[1] < $this->_excerptPreMatchLength ? 0 : ($match[1] - $this->_excerptPreMatchLength));
						$length = strlen($match[0]) + $this->_excerptPostMatchLength;
						$excerpt = 
							($match[1]>0 ? $this->_excerptPrePostMatchString : '').
							substr($stripped,$start,$this->_excerptPreMatchLength).
							'<span class="searchResultMatch">'.$match[0].'</span>'.
							substr($stripped,$match[1]+strlen($match[0]),$this->_excerptPostMatchLength).
							($match[1]+strlen($match[0])+$this->_excerptPostMatchLength<strlen($stripped) ? $this->_excerptPrePostMatchString : '');
						$r[$rKey]['excerpts'][]=$excerpt;
					}
				
				}
			
			}

		}

		return $r;


	}




	private function highlightMatches($p)
	{

		$s = isset($p[0]) ? $p[0] : null;
		$r = isset($p[1]) ? $p[1] : null;
		$f = isset($p[2]) ? $p[2] : array('label','content');

		return $r;

	}




	private function doSearch($search,$modules,$freeModules)
	{
		
		$tokenized = $this->tokenizeSearchString($search);
		$fulltext = $this->prefabFullTextMatchString($tokenized);
		$containsLiterals = $this->doesSearchStringContainLiterals($tokenized);

		$p = array(
			S_TOKENIZED_SEARCH => $tokenized,
			S_FULLTEXT_STRING => $fulltext,
			S_CONTAINS_LITERALS => $containsLiterals,
			S_IS_CASE_SENSITIVE => false,
			S_RESULT_LIMIT_PER_CAT => 125 // performance tweak. this is per category - might be made dependable on the (number of) modules selected
		);

		// species first, as the results are needed for knowing what to look for in the map
		if (is_array($modules) && (in_array('species',$modules) || in_array('mapkey',$modules)))
			$species = $this->searchSpecies($p); 
		else
			$species = null;

		return array(
			'introduction' =>
null,//				(is_array($modules) && in_array('introduction',$modules) ? $this->searchIntroduction($tokenized) : null),
			'glossary' =>
null,//				(is_array($modules) && in_array('glossary',$modules) ? $this->searchGlossary($tokenized) : null),
			'literature' => 
null,//				(is_array($modules) && in_array('literature',$modules) ? $this->searchLiterature($tokenized) : null),
			'species' => 
				$species,
			'dichkey' => 
null,//				(is_array($modules) && in_array('key',$modules) ? $this->searchDichotomousKey($tokenized) : null),
			'matrixkey' => 
null,//				(is_array($modules) && in_array('matrixkey',$modules) ? $this->searchMatrixKey($tokenized) : null),
			'map' => 
null,//				(is_array($modules) && in_array('mapkey',$modules) ? $this->searchMap($tokenized,$species['taxonList']) : null),
			'content' => 
null,//				(is_array($modules) && in_array('content',$modules) ? $this->searchContent($tokenized) : null),
			'modules' => 
null,//				$this->searchModules($search,$freeModules)	
		);

	}



	private function searchSpecies($p)
	{

		// taxa
		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'MATCH(taxon)' => "AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
				),
				'columns' => 'id,taxon as label,rank_id,is_hybrid,taxon as '.__CONCAT_RESULT__ ,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$taxa = $this->filterResultsWithTokenizedSearch(array($p,$taxa));
		$taxa = $this->highlightMatches(array($p,$taxa));

		$ranks = $this->newGetProjectRanks();

		foreach((array)$taxa as $key => $val)  {
			$taxonList[$val['id']] = $val['label'];
			$taxa[$key]['label'] = $this->formatTaxon(array('taxon' => $val['label'],'rank_id' => $val['rank_id'],'is_hybrid' => $val['is_hybrid']),$ranks);
			unset($taxa[$key]['rank_id'],$taxa[$key]['is_hybrid']);
		}



		// taxon content
		$content = $this->models->ContentTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'MATCH(content)' => "AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'publish' => 1
				),
				'columns' => 'id,taxon_id,content,page_id,content as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array($p,$content));  // includes highlighting



		// synonyms
		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'MATCH(synonym)' => "AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
				),
				'columns' => 'id,taxon_id,synonym as label,synonym as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$synonyms = $this->filterResultsWithTokenizedSearch(array($p,$synonyms));
		$synonyms = $this->highlightMatches(array($p,$synonyms));




		// common names
		$commonnames = $this->models->Commonname->_get(
			array(
				'id' => array(
					'project_id  = '.$this->getCurrentProjectId(),
					'MATCH(commonname,transliteration)' => "AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
				),
				'columns' => 'id,language_id,taxon_id,commonname,transliteration,concat(commonname,\' \',transliteration) as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);	

		$commonnames = $this->filterResultsWithTokenizedSearch(array($p,$commonnames,array('commonname','transliteration')));

		foreach((array)$commonnames as $key => $val) {
			
			$commonnames[$key]['label'] = 
				(!empty($val['transliteration']) ?
					!empty($val['transliteration']).
					(!empty($val['commonname']) ? 
						' '.sprintf($this->translate('(transliteration of "%s"'),$val['commonname']) :
						'') :
					$val['commonname']
				);

		}

		$commonnames = $this->highlightMatches(array($p,$commonnames));



		// media
		$media = $this->models->MediaDescriptionsTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'MATCH(description)' => "AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
				),
				'columns' => 'id,media_id,description as content,description as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);
		
		$media = $this->filterResultsWithTokenizedSearch(array($p,$media));
		$media = $this->getExcerptsSurroundingMatches(array($p,$media));  // includes highlighting

		foreach((array)$media as $key => $val) {

			$d = $this->models->MediaTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['media_id']
					),
					'columns' => 'id,taxon_id,file_name,taxon as '.__CONCAT_RESULT__,
					'limit' => $p[S_RESULT_LIMIT_PER_CAT]
				)
			);

			$media[$key]['taxon_id'] = $d[0]['taxon_id'];
			$media[$key]['label'] = $d[0]['file_name'];

		}



		return array(
			'results' => array(
				array(
					'label' => $this->translate('Species names'), // when changing the label 'Species names', do the same in searchMap()
					'url' => '../species/edit.php?id=%s',
					'data' => $taxa,
					'numOfResults' => count((array)$taxa)
				),
				array(
					'label' => $this->translate('Species descriptions'),
					'url' => '../species/taxon.php?id=%s&page=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
				array(
					'label' => $this->translate('Species synonyms'),
					'url' => '../species/synonyms.php?id=%s',
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms)
				),
				array(
					'label' => $this->translate('Species common names'),
					'url' => '../species/common.php?id=%s',
					'data' => $commonnames,
					'numOfResults' => count((array)$commonnames)
				),
				array(
					'label' => $this->translate('Species media'),
					'url' => '../species/media.php?id=%s',
					'data' => $media,
					'numOfResults' => count((array)$media)
				),
			),
			'taxonList' => $taxonList,
			'numOfResults' => count((array)$taxa)+count((array)$content)+count((array)$synonyms)+count((array)$commonnames)+count((array)$media)
		);

	}




	private function searchIntroduction($search=null)
	{

		$content = $this->models->ContentIntroduction->_get(
			array(
				'where' => 
				"project_id = ".$this->getCurrentProjectId()." and
					MATCH(topic, content) AGAINST ('".$search."' in boolean mode)",
				'columns' => 'id,topic,content'
			)
		);

q($this->models->ContentIntroduction->q());


die('screaming');
		$hitCount = 0;

		foreach((array)$content as $key => $val)  {

			$content[$key]['replace'] = array(
				'model' => $this->models->ContentIntroduction->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
					'matches' => $this->getColumnMatches($search,$val,array('topic','content'),$hitCount),
				'label' => sprintf($this->translate('Page "%s"'),$val['topic'])
			);
			
			$content[$key]['url'] = '../introduction/edit.php?id='.$val['id'];

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Introduction'),
					'data' => $content,
					'numOfResults' => $hitCount
				)
			),
			'numOfResults' => $hitCount
		);

	}
	
	


    public function searchIndexAction ()
    {

		$this->checkAuthorisation();

		$this->setPageName($this->translate('Search'));

		$this->setControllerMask('utilities','Search');
		
		unset($_SESSION['admin']['user']['search']['results']);

		if ($this->rHasVal('search')) {
			

			$_SESSION['admin']['user']['search']['search'] = array(
				'search' => $this->requestData['search'],
				//'replacement' => $this->rHasVal('replacement') ? $this->requestData['replacement'] : null,
				//'doReplace' => $this->rHasVal('doReplace') ? $this->requestData['doReplace'] : null,
				'modules' => $this->rHasVal('modules') ? $this->requestData['modules'] : null,
				'freeModules' => $this->rHasVal('freeModules') ? $this->requestData['freeModules'] : null,
				'options' =>$this->rHasVal('options') ? $this->requestData['options'] : null,
				);
			
			if ($this->validateSearchString($this->requestData['search'])) {

				$results =
					$this->doSearch(
						$this->requestData['search'],
						$this->rHasVal('modules') ? $this->requestData['modules'] : false,
						$this->rHasVal('freeModules') ? $this->requestData['freeModules'] : false
					);

				$_SESSION['admin']['user']['search'][$this->requestData['search']]['results'] = $results;

				$this->addMessage('Searched for '.$this->requestData['search']);
				
				q($results);

			} else {

				$this->addError(sprintf($this->translate('Search string must be between %s and %s characters in length.'),$this->_minSearchLength,$this->_maxSearchLength));

			}
			
			

		if (1==2) { 
		
			if (
				!isset($_SESSION['admin']['user']['search'][$search]) || 
				$this->noResultCaching
				) {
				
				$this->_replaceId = 0;
	


				$results['numOfResults'] = $results['numOfReplacements'] = 0;

				foreach((array)$results as $key => $val) {
				
					if (isset($val['numOfResults'])) {

						$results['numOfResults'] += $val['numOfResults'];
						$results['numOfReplacements'] += isset($val['numOfReplacements']) ? $val['numOfReplacements'] : $val['numOfResults'];
					
					}
				
				}

				$_SESSION['admin']['user']['search'][$search]['results'] = $results;

				$this->_replaceId = null;

			} else {

				$results = $_SESSION['admin']['user']['search'][$search]['results'];

			}
			
			$_SESSION['admin']['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
			$_SESSION['admin']['user']['search']['lastSearch'] = $search;
				
			return $results;
			
		}
						
			
			

			/*



			$_SESSION['admin']['user']['search']['replace']['index'] = $this->_replaceStatusIndex;

			if ($this->rHasVal('doReplace','on') && $this->rHasVal('replacement') && $this->rHasVal('options','all')) {
			
				$_SESSION['admin']['user']['search']['results']['replace_all'] = true;

				$this->redirect('search_replace_all.php');

			} else
			if ($this->rHasVal('doReplace','on') && $this->rHasVal('replacement') && $this->rHasVal('options','perOccurrence')) {

				$this->redirect('search_replace.php');

			} else {

				$this->redirect('search_results.php');

			}	
			*/	

		}

		if (isset($_SESSION['admin']['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['admin']['user']['search']['search']);
		$this->smarty->assign('modules',$this->getProjectModules());
		$this->smarty->assign('minSearchLength',$this->controllerSettings['minSearchLength']);

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
		
		$this->setPageName($this->translate('Search results'));

		$this->setControllerMask('utilities','Search');

		if ($_SESSION['admin']['user']['search']['search']) {

			if (isset($_SESSION['admin']['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['admin']['user']['search']['search']);
			if (isset($_SESSION['admin']['user']['search']['results'])) $this->smarty->assign('resultData',$_SESSION['admin']['user']['search']['results']);
			if (isset($_SESSION['admin']['user']['search']['replace']['index'] )) $this->smarty->assign('replaceIndex',$_SESSION['admin']['user']['search']['replace']['index']);
			
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
		
		$this->setPageName($this->translate('Search results'));

		$this->setControllerMask('utilities','Search');

		if ($_SESSION['admin']['user']['search']['search']) {

			if (isset($_SESSION['admin']['user']['search']['search'])) $this->smarty->assign('search',$_SESSION['admin']['user']['search']['search']);
			if (isset($_SESSION['admin']['user']['search']['results'])) $this->smarty->assign('resultData',$_SESSION['admin']['user']['search']['results']);
			if (isset($_SESSION['admin']['user']['search']['replace']['index'] )) $this->smarty->assign('replaceIndex',$_SESSION['admin']['user']['search']['replace']['index']);
			
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

		$this->setPageName($this->translate('Replace results'));
		
		$this->setControllerMask('utilities','Search');

		if ($_SESSION['admin']['user']['search']['results']['replace_all']!==true) $this->redirect('search_index.php');

		unset($_SESSION['admin']['user']['search']['results']['replace_all']);

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

		if (!isset($_SESSION['admin']['user']['search']['results']) || !isset($_SESSION['admin']['user']['search']['search']['replacement'])) return null;

		if (is_string($id) && substr($id,0,7)=='module:') {

			$thisModule = substr($id,7);

		} else {

			$thisModule = false;

		}

		if (!is_numeric($id) && $id!='*' && $thisModule===false) return null;
		
		if (is_numeric($id)) $id = intval($id);

		foreach((array)$_SESSION['admin']['user']['search']['results'] as $key1 => $val) {

			foreach((array)$val['results'] as $key2 => $module) {

				foreach((array)$module['data'] as $key3 => $data) {

					if (isset($data['replace']['matches'])) {
					
						if ($thisModule!==false && $module['label']!=$thisModule) continue;
	
						$this->doReplace(
							$_SESSION['admin']['user']['search']['search']['search'],
							$_SESSION['admin']['user']['search']['search']['replacement'],
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
			isset($_SESSION['admin']['user']['search']['replace']['index'][$storedId]) ? $_SESSION['admin']['user']['search']['replace']['index'][$storedId] : null;

		$this->_replaceCounter++;

		// match the id from the request with the one stored in the session search results
		if (($storedId===$requestedId) || ($requestedId=='*')) {
		
			if ($replaceStatus===false) {

				if ($this->_replaceData['matches'][($this->_replaceCounter-1)][0]==$this->_replaceData['search']) {
				
					if ($this->_replaceData['type']=='replace') {
	
						$_SESSION['admin']['user']['search']['replace']['index'][$storedId] = 'replaced';
						
						$this->_replacementResultCounters['replaced']++;
		
						return $this->_replaceData['replacement'];
					
					} else {
	
						$_SESSION['admin']['user']['search']['replace']['index'][$storedId] = $this->_replaceData['type']=='skip' ? 'skipped' : false;
		
						$this->_replacementResultCounters['skipped']++;

						return $this->_replaceData['search'];
	
					}
	
	
				} else {
	
					$_SESSION['admin']['user']['search']['replace']['index'][$storedId] = 'mismatch';
		
					$this->_replacementResultCounters['mismatched']++;

					return $this->_replaceData['search'];
	
				}

			}

		}

		return $this->_replaceData['search'];
	
	}

	private function doReplace($search,$replace,$data,$id,$type)
	{
	
		// matches that have no id cannot be processed
		if (is_null($data['id'])) return;

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

		if (strlen($search)>=$this->controllerSettings['minSearchLength']) { 
		
			if (
				!isset($_SESSION['admin']['user']['search'][$search]) || 
				$this->noResultCaching
				) {
				
				$this->_replaceId = 0;

				$results = $this->doSearch($search,$modules,$freeModules);

				$results['numOfResults'] = $results['numOfReplacements'] = 0;

				foreach((array)$results as $key => $val) {
				
					if (isset($val['numOfResults'])) {

						$results['numOfResults'] += $val['numOfResults'];
						$results['numOfReplacements'] += isset($val['numOfReplacements']) ? $val['numOfReplacements'] : $val['numOfResults'];
					
					}
				
				}

				$_SESSION['admin']['user']['search'][$search]['results'] = $results;

				$this->_replaceId = null;

			} else {

				$results = $_SESSION['admin']['user']['search'][$search]['results'];

			}
			
			$_SESSION['admin']['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
			$_SESSION['admin']['user']['search']['lastSearch'] = $search;
				
			return $results;


		} else {
		
			$this->addMessage(sprintf($this->translate('Search term too short. Minimum is %s characters.'),3));
			
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


	private function makeCategoryList()
	{

		if (!isset($_SESSION['admin']['user']['species']['categories'])) {

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
		
				if ($val['def_page'] == 1) $_SESSION['admin']['user']['species']['defaultCategory'] = $val['id'];
			
			}
			
			$_SESSION['admin']['user']['species']['categories'] = $tp;

		}

		return $_SESSION['admin']['user']['species']['categories'];
	
	}

	private function escapeRegExSpecialChars($matches)
	{
	
		return '\\'.$matches[0];
	
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
//					htmlentities(
					strip_tags(
						substr(
							$content,
							($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $mV[1]-$this->controllerSettings['excerptLengthLeft'] : 0),
							($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $this->controllerSettings['excerptLengthLeft'] : $mV[1])
						)
					).
					'<span class="stringToReplace">'.
					$mV[0].
					'</span>'.
//					htmlentities(
					strip_tags(
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

				$counter = (is_null($counter) ? 0 : $counter) + count((array)$matches[0]);

			}
		}

		return empty($results) ? null : $results;
	
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
	
		if ($freeModules!==false)
			$d['module_id in']  = implode(',',$freeModules).')';					
		else
			return null;

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
		
		$hitCountTot = array();
		$hitCountTotTot = $resultCountTot = 0;

		foreach((array)$content as $key => $val) {

			$matches = $this->getColumnMatches($search,$val,array('topic','content'));

			if (!is_null($matches)) {

				$val['replace'] = array(
					'model' => $this->models->ContentFreeModule->getClassName(),
					'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
					'matches' => $matches,
					'label' => sprintf($this->translate('Page "%s"'),$val['topic'])
				);
				
				$val['url'] = '../module/edit.php?id='.$val['page_id'];
				
				$hitCountTot[$modules[$val['module_id']]['module']] = 
					(isset($hitCountTot[$modules[$val['module_id']]['module']]) ? $hitCountTot[$modules[$val['module_id']]['module']] : 0) + 
					count((array)$matches);
					
				$hitCountTotTot = $hitCountTotTot + count((array)$matches);

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
					'numOfReplacements' => $hitCountTot[$key]
				);

		}

		return array(
			'results' => isset($r) ? $r : null,
			'numOfResults' => $resultCountTot,
			'numOfReplacements' => $hitCountTotTot
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

		$hitCountMatrices = $hitCountChars = $hitCountStates = 0;

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
				'matches' => $this->getColumnMatches($search,$val,array('name'),$hitCountMatrices),
				'label' => sprintf($this->translate('Matrix "%s"'),$val['name'])
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
				'matches' => $this->getColumnMatches($search,$val,array('label'),$hitCountChars),
				'label' => sprintf($this->translate('Character "%s"'),$val['label'])
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
				'matches' => $this->getColumnMatches($search,$val,array('label','text'),$hitCountStates),
				'label' => sprintf($this->translate('State "%s"'),$val['label'])
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
					'label' => $this->translate('Matrix key matrices'),
					'data' => $matrices,
					'numOfResults' => $hitCountMatrices
				),
				array(
					'label' => $this->translate('Matrix key characters'),
					'data' => $characteristics,
					'numOfResults' => $hitCountChars
				),
				array(
					'label' => $this->translate('Matrix key states'),
					'data' => $states,
					'numOfResults' => $hitCountStates
				)
			),
			'numOfResults' => $hitCountMatrices+$hitCountChars+$hitCountStates,
			'matrices' => $matrixNames,
		);

	}

	private function searchDichotomousKey($search)
	{

		$hitCountSteps = $hitCountChoices = 0;
		
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
				'matches' => $this->getColumnMatches($search,$val,array('choice_txt'),$hitCountChoices),
				'label' => '"'.substr($val['content'],0,25).'..."'
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
						'columns' => 'keystep_id,title,content'
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
				'matches' => $this->getColumnMatches($search,$val,array('title','content'),$hitCountSteps)
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
			$steps[$key]['replace']['label'] =sprintf($this->translate('Step %s'),$step[0]['number']);

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Dichotomous key') . ' ' . $this->translate('steps'),
					'data' => $steps,
					'numOfResults' => $hitCountSteps
				),
				array(
					'label' => $this->translate('Dichotomous key') . ' ' . $this->translate('choices'),
					'data' => $choices,
					'numOfResults' => $hitCountChoices
				)
			),
			'numOfResults' => $hitCountSteps+$hitCountChoices
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
		
		$hitCount = 0;
		
		foreach((array)$books as $key => $val) {

			$books[$key]['replace'] = array(
				'model' => $this->models->Literature->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('text','author_full'),$hitCount),
				'label' => $val['author_full'].' '.$val['year']
			);
			
			$books[$key]['url'] = '../literature/edit.php?id='.$val['id'];

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Literary references'),
					'data' => $books,
					'numOfResults' => $hitCount
				)
			),
			'numOfResults' => $hitCount
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
		
		$hitCountGloss = $hitCountSynonym = 0;

		foreach((array)$gloss as $key => $val) {
		
			$gloss[$key]['replace'] = array(
				'model' => $this->models->Glossary->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('term','definition'),$hitCountGloss),
				'label' => sprintf($this->translate('Term "%s"'),$val['term'])
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
				'matches' => $this->getColumnMatches($search,$val,array('synonym'),$hitCountSynonym),
				'label' => sprintf($this->translate('Synonym "%s"'),$val['synonym'])
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
					'label' => $this->translate('Glossary terms'),
					'data' => $gloss,
					'numOfResults' => $hitCountGloss
				),
				array(
					'label' => $this->translate('Glossary synonyms'),
					'data' => $synonyms,
					'numOfResults' => $hitCountSynonym
				)
				/*					,
				array(
					'label' => $this->translate('Glossary media'),
					'data' => $media,
					'numOfResults' => count((array)$media)
				)
				*/
			),
			'numOfResults' => $hitCountGloss  + $hitCountSynonym
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
		
		$hitCount = 0;

		foreach((array)$content as $key => $val) {

			$content[$key]['replace'] = array(
				'model' => $this->models->Content->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('subject','content'),$hitCount),
				'label' => sprintf($this->translate('Page "%s"'),$val['subject'])
			);
			
			$content[$key]['url'] = '../content/content.php?id='.$val['id'];

		}

		return array(
			'results' => array(
				array(
					'label' => $this->translate('Navigator'),
					'data' => $content,
					'numOfResults' => $hitCount
				)
			),
			'numOfResults' =>$hitCount
		);

	}

	private function searchMap($search,$taxonList)
	{

		$titles = $this->models->GeodataTypeTitle->_get(
			array(
				'where' =>
					'project_id = '. $this->getCurrentProjectId().' and
					title regexp \''.$this->models->Content->escapeString($this->makeRegExpCompatSearchString($search)).'\'',
				'columns' => 'id,title'
			)
		);

		$hitCountOccurrences = $hitCountTypes = 0;
		
		foreach((array)$titles as $key => $val) {

			$titles[$key]['replace'] = array(
				'model' => $this->models->GeodataTypeTitle->getClassName(),
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
				'matches' => $this->getColumnMatches($search,$val,array('title'),$hitCountTypes),
				'label' => sprintf($this->translate('Datatype "%s"'),$val['title'])
			);

			$titles[$key]['url'] =  '../mapkey/data_types.php';

		}


		$d = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'distinct taxon_id'
			)
		);
		
		$occurrences = array();
		
		foreach((array)$d as $key => $val) {

			if (isset($taxonList[$val['taxon_id']])) {
			
				$val['taxon'] = $taxonList[$val['taxon_id']];
			
				$occurrences[$key]['replace'] = array(
					'model' => $this->models->GeodataTypeTitle->getClassName(),
					'id' => null,
					'matches' => $this->getColumnMatches($search,$val,array('taxon'),$hitCountOccurrences),
					'label' => $val['taxon']
				);

				$occurrences[$key]['url'] =  '../mapkey/species.php?id='.$val['taxon_id'];

			}

		}		
		
		return array(
			'results' => array(
				array(
					'label' => $this->translate('Distribution datatype'),
					'data' => $titles,
					'numOfResults' => $hitCountTypes
				),
				array(
					'label' => $this->translate('Distribution'),
					'data' => $occurrences,
					'numOfResults' => $hitCountOccurrences,
					'canReplace' => false
				),
			),
			'numOfResults' => $hitCountTypes+$hitCountOccurrences,
			'numOfReplacements' => $hitCountTypes
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
	

	private function getLookupList($search)
	{
	
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

	private function initialize()
	{

		define('S_TOKENIZED_SEARCH',0);
		define('S_FULLTEXT_STRING',1);
		define('S_CONTAINS_LITERALS',2);
		define('S_IS_CASE_SENSITIVE',3);
		define('S_RESULT_LIMIT_PER_CAT',4);
		
		define('__CONCAT_RESULT__','__CONCAT_RESULT__');

		$this->_minSearchLength = isset($this->controllerSettings['minSearchLength']) ? $this->controllerSettings['minSearchLength'] : $this->_minSearchLength;
		$this->_maxSearchLength = isset($this->controllerSettings['maxSearchLength']) ? $this->controllerSettings['maxSearchLength'] : $this->_maxSearchLength;
		$this->_excerptPreMatchLength = isset($this->controllerSettings['excerptPreMatchLength']) ? $this->controllerSettings['excerptPreMatchLength'] : 25;
		$this->_excerptPostMatchLength = isset($this->controllerSettings['excerptPostMatchLength']) ? $this->controllerSettings['excerptPostMatchLength'] : 25;
		$this->_excerptPrePostMatchString = isset($this->controllerSettings['excerptPrePostMatchString']) ? $this->controllerSettings['excerptPrePostMatchString'] : '...';
		
	}



}