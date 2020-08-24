<?php /** @noinspection PhpMissingParentCallMagicInspection */

/*

	we are now doing LIKE instead of MATCH (as MATCH ignores wildcards at the beginning: *olar only matches olar, not polar) - do the same limitations still apply? think not.

	flow:

		search
			string
			(no parameters)
		validateSearchString

		doSearch:

			$p = array(
				S_TOKENIZED_TERMS => tokenizeSearchString($search),
				S_LIKETEXT_STRING => prefabFullTextLikeString($tokenized),
				S_CONTAINS_LITERALS => doesSearchStringContainLiterals($tokenized),
				S_IS_CASE_SENSITIVE => false,
				S_RESULT_LIMIT_PER_CAT => 200,
				S_UNSET_ORIGINAL_CONTENT => true // if true, unsets the potentially large content fields after they've been excerpted
			);

			$this->searchSpecies( $p ) etc:


			// taxon content
			$content = $this->models->Table->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('content')), // _s  --> creates like-clause  ((taxon like '%phyllum a%' or taxon like '%orchid%'))
						'publish' => 1
					),
					'columns' => 'id,taxon_id,content,page_id,content as '.__CONCAT_RESULT__,
					'limit' => $p[S_RESULT_LIMIT_PER_CAT]
				)
			);

			$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
				currently bypassed, because use of LIKE rather than MATCH
			$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
				implements <span class="searchResultMatch"> around hits
			$content = $this->sortResultsByMostTokensFound($content);


		private function _s($s,$c)
		{
			$r=array();
			foreach((array)$c as $v)
				$r[] = str_replace(S_LIKETEXT_REPLACEMENT,$v,$s);

			return '('.implode(' or ',$r).')';
		}


WE WILL NOT SEARCH THE MATRIX!

STRIP TAGS AND SHIT FROM SEARCH STRING!!! (also + and - and * which fuck up the fulltext)


	search is case-insensitive!
	the php post-filtering is designed to allow for case-sensitivity, but it is not actually implemented.
	as the full text search is insensitive by default (unless we start altering the collation of the indexed
	columns), searches that have no literal bits ("...") will be harder to turn into case sensitive ones.



 Some words are ignored in full-text searches (is this not MATCH only? we're doing LIKE now! [how social media of you] ):

    Any word that is too short is ignored. The default minimum length of words that are found by full-text searches is four characters.

    Words in the stopword list are ignored. A stopword is a word such as “the” or “some” that is so common that it is considered to have zero semantic value. There is a built-in stopword list, but it can be overwritten by a user-defined list.

The default stopword list is given in Section 12.9.4, “Full-Text Stopwords”.
	http://dev.mysql.com/doc/refman/5.0/en/fulltext-stopwords.html
The default minimum word length and stopword list can be changed as described in Section 12.9.6, “Fine-Tuning MySQL Full-Text Search”.

//WHERE MATCH(title, body) AGAINST ('vnurk vnork' in boolean mode) // returns AND vnurk AND vnork

*/

include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');

class SearchController extends Controller
{
	private $_searchStringGroupDelimiter = '"';
	private $_excerptPreMatchLength;
	private $_excerptPostMatchLength;
	private $_excerptPrePostMatchString = '...';

    public $usedModels = array(
		'content',
		'content_taxa',
		'pages_taxa',
		'pages_taxa_titles',
		'media_taxon',
		'media_descriptions_taxon',
		'synonyms',
		'commonnames',
		'literature',
		'content_free_modules',
		'choices_content_keysteps',
		'content_keysteps',
		'choices_keysteps',
		'keysteps',
		'literature',
		'glossary',
		'glossary_media',
		'glossary_synonyms',
		'geodata_types_titles',
		'content_introduction'
    );

    public $controllerPublicName = 'Search';

    public $usedHelpers = array(
		'session_module_settings',
    );

	public $cssToLoad = array(
		'search.css'
	);

	public $jsToLoad = array('all' => array(
		'search.js'
	));

	protected $moduleSession;

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		$this->moduleSession=$this->helpers->SessionModuleSettings;
		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->moduleSettings->setUseDefaultWhenNoValue( true );
		$this->moduleSettings->assignModuleSettings( $this->settings );

		define('S_TOKENIZED_TERMS',0);
		define('S_FULLTEXT_STRING',1);
		define('S_CONTAINS_LITERALS',2);
		define('S_IS_CASE_SENSITIVE',3);
		define('S_RESULT_LIMIT_PER_CAT',4);
		define('S_LIKETEXT_STRING',5);
		define('S_UNSET_ORIGINAL_CONTENT',6);
		define('S_LIKETEXT_REPLACEMENT','###');
		define('__CONCAT_RESULT__','__CONCAT_RESULT__');
		define('V_RESULT_LIMIT_PER_CAT',200);

		$this->_minSearchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'min_search_length','subst'=>3,'module'=>'utilities'));
		$this->_maxSearchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'max_search_length','subst'=>50,'module'=>'utilities'));
		$this->_excerptPreMatchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'excerpt_pre-match_length','subst'=>35,'module'=>'utilities'));
		$this->_excerptPostMatchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'excerpt_post-match_length','subst'=>35,'module'=>'utilities'));
		$this->_excerptPrePostMatchString=$this->moduleSettings->getModuleSetting(array('setting'=>'excerpt_pre_post_match_string','subst'=>'...','module'=>'utilities'));

		$this->UserRights->setNoModule( true );
		
	}

    public function indexAction ()
    {
        $this->checkAuthorisation();

		$this->setPageName( $this->translate('Extensive search') );

		if ($this->rHasVal('search'))
		{
			$this->setPageName( $this->translate('Results') );
			
			$this->moduleSession->setModuleSetting( array('setting'=>'search','value'=>$this->rGetVal('search')) );
			$this->moduleSession->setModuleSetting( array('setting'=>'modules','value'=>($this->rHasVal('modules') ? $this->rGetVal('modules') : null)) );
			$this->moduleSession->setModuleSetting( array('setting'=>'freeModules','value'=>($this->rHasVal('freeModules') ? $this->rGetVal('freeModules') : null)) );

			if ($this->validateSearchString($this->rGetVal('search')))
			{
				$results =
					$this->doSearch(
						$this->rGetVal('search'),
						$this->rHasVal('modules') ? $this->rGetVal('modules') : false,
						$this->rHasVal('freeModules') ? $this->rGetVal('freeModules') : false
					);

				$this->moduleSession->setModuleSetting( array('setting'=>'results','value'=>$results) );

				$this->addMessage(sprintf('Searched for <span class="searched-term">%s</span>',$this->rGetVal('search')));
				$this->smarty->assign('results',$results);
			}
			else
			{
				$this->addError(
					sprintf(
						$this->translate('Search string must be between %s and %s characters in length.'),
						$this->_minSearchLength,
						$this->_maxSearchLength
					)
				);
			}
		}

		if (null!=($this->moduleSession->getModuleSetting('search')))
		{
			$this->smarty->assign('search',
				array(
					'search'=>$this->moduleSession->getModuleSetting( 'search' ),
					'modules'=>$this->moduleSession->getModuleSetting( 'modules' ),
					'freeModules'=>$this->moduleSession->getModuleSetting( 'freeModules' ),
					'results'=>$this->moduleSession->getModuleSetting( 'results')
				)
			);
		}

		$this->smarty->assign('modules',$this->getProjectModules(array('ignore' => MODCODE_MATRIXKEY)));
		$this->smarty->assign('minSearchLength',$this->_minSearchLength);

        $this->printPage();
    }

    public function searchResetAction ()
    {
		$this->moduleSession->setModuleSetting( array('setting'=>'search') );
		$this->moduleSession->setModuleSetting( array('setting'=>'modules') );
		$this->moduleSession->setModuleSetting( array('setting'=>'freeModules') );
		$this->moduleSession->setModuleSetting( array('setting'=>'results') );
		$this->redirect('index.php');
	}

	private function validateSearchString( $s )
	{
		return
			(strlen($s)>=$this->_minSearchLength) &&  // is it long enough?
			(strlen($s)<=$this->_maxSearchLength);    // is it short enough?
	}

	private function tokenizeSearchString( $s )
	{
		/*
			splits search string in groups delimited by ". if there's an
			uneven number the last one is ignored.
		*/

		$parts = preg_split('/('.$this->_searchStringGroupDelimiter.')/i',$s,-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$b = null;    // buffer
		$t = array(); // resulting array of parts
		$r = false;   // "rec"-toggle

		foreach($parts as $val)
		{
			if ($val=='"')
			{
				// if "rec" is on, add the concatenated string to the results and reset the buffer
				if ($r) {
					if (!empty($b))
						array_push($t,$b);
					$b = null;
				}
				// and toggle "rec"
				$r = !$r;
			}
			else
			{
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

	private function prefabFullTextLikeString( $s )
	{
		array_walk($s,function(&$n){$n=str_replace(array("'","%","_"),array("\'","\%","\_"),$n);});

		// make tokens into a single string for mysql LIKE statement; add ### to replace with column name
		$r = "(".S_LIKETEXT_REPLACEMENT." like '%".implode("%' or ".S_LIKETEXT_REPLACEMENT." like '%",$s)."%')";

		return trim($r);
	}

	private function doesSearchStringContainLiterals( $s )
	{
		foreach((array)$s as $val)
		{
			if (strpos($val,' ')!==false)
				return true;
		}
		return false;
	}

	private function stripTagsForSearchExcerpt( $s )
	{
		// replace <br> and ends of block elements with spaces to avoid words being concatenated
		return strip_tags(str_replace('  ',' ',str_ireplace(array('<br>','<br />','</p>','</div>','</td>','</li>','</blockquote>','</h1>','</h2>','</h3>','</h4>','</h5>','</h6>'),' ',$s)));
	}

	private function filterResultsWithTokenizedSearch( $p )
	{

		//OVERRIDE: the use of LIKE rather than MATCH make the post-filtering in PHP superfluous (i think)
		foreach((array)$p[1] as $key => $val)
		{
			if (isset($p[1][$key][__CONCAT_RESULT__])) unset($p[1][$key][__CONCAT_RESULT__]);
		}
		return $p[1];
		//OVERRIDE


		/*
			$p[0] : array of search parameters:
				$s[S_TOKENIZED_TERMS]	: array of tokens
				$s[S_FULLTEXT_STRING]	: string for fulltext search (not used in this function)
				$s[S_CONTAINS_LITERALS]	: boolean, indicates the presence of literal token(s) ("aa bb")
			$p[1] : array of results
			$p[2] : array of fields to check (optional; defaults to array('label','content'))
		*/

		$s = isset($p[0]) ? $p[0] : null;
		$r = isset($p[1]) ? $p[1] : null;

		if (!isset($s) || !isset($s[S_CONTAINS_LITERALS]) || !isset($s[S_TOKENIZED_TERMS]) ||$s[S_CONTAINS_LITERALS]==false) return $r;

		// really shouldn't happen but just in case i should forget to add the __CONCAT_RESULT__ field in the query
		if (isset($r[0][__CONCAT_RESULT__]))
			$concatField = __CONCAT_RESULT__;
		else
			$concatField = 'content';


		if ($s[S_CONTAINS_LITERALS]) {

			$filtered = array();

			// loop all results
			foreach((array)$r as $key => $result) {

				if (!isset($result[$concatField])) continue;


				$d = $this->stripTagsForSearchExcerpt($result[$concatField]);

				$match = false;

				// loop through all tokens
				foreach((array)$s[S_TOKENIZED_TERMS] as $token) {

					if ($match==true) break;

					// match if token exists in value of specific field of each result
					$match = $s[S_IS_CASE_SENSITIVE] ? strpos($d,$token)!==false : stripos($d,$token)!==false;

				}

				if ($match) {
					if ($concatField== __CONCAT_RESULT__)
						unset($result[__CONCAT_RESULT__]);
					else
						$result['warning'] = 'you forgot to add __CONCAT_RESULT__ to your query! these results based on matches in the assumed \'content\' column.';
					array_push($filtered,$result);
				}

			}

			return $filtered;

		}

		// just in case
		return $r;

	}

	private function getExcerptsSurroundingMatches( $p )
	{
		$s = isset($p['param']) ? $p['param'] : null;						// search parameters
		$r = isset($p['results']) ? $p['results'] : null;					// results array
		$f = isset($p['fields']) ? $p['fields'] : array('label','content');	// fields to match
		$x = isset($p['excerpt']) ? $p['excerpt'] : array('content');		// fields to be excerpted (rather than return completely)

		if (!is_array($x)) $x = array(); // for when called with 'excerpt' => false (excerpt none of the fields)

		if (!isset($s) || !isset($s[S_TOKENIZED_TERMS]) ) return $r;

		foreach((array)$r as $rKey => $result)
		{
			foreach((array)$f as $fKey => $field)
			{
				$fullmatches = array();

				if (isset($result[$field]))
				{
					$stripped = $this->stripTagsForSearchExcerpt($result[$field]);

					foreach((array)$s[S_TOKENIZED_TERMS] as $token)
					{
						$r[$rKey]['tokens_found'][$token]=!isset($r[$rKey]['tokens_found'][$token]) ? 0 : $r[$rKey]['tokens_found'][$token];

						$matches=array();
						preg_match_all('/'.$token.'/'.($s[S_IS_CASE_SENSITIVE] ? '' : 'i'),$stripped,$matches,PREG_OFFSET_CAPTURE);

						if (isset($matches[0]))
						{
							foreach((array)$matches[0] as $match)
							{
								if (isset($match[0])) {
									$fullmatches[]=$match;
									$r[$rKey]['tokens_found'][$token]++;
								}
							}
						}
						unset($matches);
					}

					foreach((array)$fullmatches as $match)
					{
						if (in_array($field,$x))
						{
							$start = ($match[1] < $this->_excerptPreMatchLength ? 0 : ($match[1] - $this->_excerptPreMatchLength));
							$r[$rKey]['matches'][]=
								($start>0 ? $this->_excerptPrePostMatchString : '').
								substr($stripped,$start,($match[1]-$start)).
								'<span class="searchResultMatch">'.$match[0].'</span>'.
								substr($stripped,$match[1]+strlen($match[0]),$this->_excerptPostMatchLength).
								($match[1]+strlen($match[0])+$this->_excerptPostMatchLength<strlen($stripped) ? $this->_excerptPrePostMatchString : '');
						}
						else
						{
							$r[$rKey]['matches'][]=
								substr($stripped,0,$match[1]).
								'<span class="searchResultMatch">'.$match[0].'</span>'.
								substr($stripped,$match[1]+strlen($match[0]));
						}
					}

					if ($s[S_UNSET_ORIGINAL_CONTENT] && in_array($field,$x))
						unset($r[$rKey][$field]);

				}

			}

		}

		return $r;

	}

	private function sortResultsByMostTokensFound( $data )
	{
		if (count((array)$data)<2)
			return $data;

		foreach((array)$data as $key=>$val)
		{
			$scores[$key]=0;
			if (isset($val['tokens_found'])) {
				foreach((array)$val['tokens_found'] as $token)
					if ($token>0) $scores[$key]++;
			}
		}
		uasort($scores,function($a,$b){return($a>$b?-1:($a<$b?1:0));});
		$res=array();
		foreach((array)$scores as $key => $val)
		{
			$res[]=$data[$key];
		}
		return $res;
	}

	private function makeLikeClause( $s, $c )
	{
		// creates like-clause  ((taxon like '%phyllum a%' or taxon like '%orchid%'))
		$r=array();
		foreach((array)$c as $v)
			$r[] = str_replace(S_LIKETEXT_REPLACEMENT,$v,$s);

		return '('.implode(' or ',$r).')';
	}

	private function doSearch( $search, $modules, $freeModules )
	{
		$searchAll=($modules=='*');

		$tokenized = $this->tokenizeSearchString($search);
		$liketxt = $this->prefabFullTextLikeString($tokenized);
		$containsLiterals = $this->doesSearchStringContainLiterals($tokenized);

		$p = array(
			S_TOKENIZED_TERMS => $tokenized,
			//S_FULLTEXT_STRING => $fulltext,
			S_LIKETEXT_STRING => $liketxt,
			S_CONTAINS_LITERALS => $containsLiterals,
			S_IS_CASE_SENSITIVE => false,
			S_RESULT_LIMIT_PER_CAT => V_RESULT_LIMIT_PER_CAT, // max results per category (module)
			S_UNSET_ORIGINAL_CONTENT => true // if true, unsets the potentially large content fields after they've been excerpted
		);

		if (
			( is_array($modules) && in_array('species',$modules) ) ||
			( is_array($modules) && in_array('key',$modules) )
		)
		{
			$species=$this->searchSpecies( $p );
			$p['species_results']=$species;
		}


		$results =
			array(
				'content' =>
					(is_array($modules) && in_array('content',$modules) ? $this->searchContent( $p ) : null),
				'map' =>
					(is_array($modules) && in_array('mapkey',$modules) ? $this->searchMap( $p ) : null),
				'matrixkey' =>
					(is_array($modules) && in_array('matrixkey',$modules) ? $this->searchMatrixKey( $p ) : null), // stub
				'dichkey' =>
					(is_array($modules) && in_array('key',$modules) ? $this->searchDichotomousKey( $p ) : null),
				'literature' =>
					(is_array($modules) && in_array('literature',$modules) ? $this->searchLiterature( $p ) : null),
				'glossary' =>
					(is_array($modules) && in_array('glossary',$modules) ? $this->searchGlossary( $p ) : null),
				'introduction' =>
					(is_array($modules) && in_array('introduction',$modules) ? $this->searchIntroduction( $p ) : null),
				'species' =>
					(is_array($modules) && in_array('species',$modules) ? $species : null),
				'modules' =>
					$this->searchModules($p,$freeModules)
			);

		$totalcount = 0;

		foreach((array)$results as $val)
			$totalcount += $val['numOfResults'];

		//echo '<h2>'.$totalcount.'</h2>';

		return array('data'=>$results,'count'=>$totalcount);
	}

	private function searchSpecies( $p )
	{

		// taxa
		$taxa = $this->models->Taxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(taxon) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('taxon'))
				),
				'columns' => 'id,taxon as label,rank_id,is_hybrid,taxon as '.__CONCAT_RESULT__ ,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$taxa = $this->filterResultsWithTokenizedSearch(array($p,$taxa));
		$taxa = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$taxa));
		$taxa = $this->sortResultsByMostTokensFound($taxa);

		$ranks = $this->newGetProjectRanks();

		foreach((array)$taxa as $key => $val)  {
			$taxa[$key]['label'] =
			$this->formatTaxon(
				array(
					'taxon'=>array('taxon' => $val['label'],'rank_id' => $val['rank_id'],'is_hybrid' => $val['is_hybrid']),
					'ranks'=>$ranks
				)
			);
			unset($taxa[$key]['rank_id'],$taxa[$key]['is_hybrid']);
		}



		// taxon content
		$content = $this->models->ContentTaxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(content) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('content')),
					'publish' => 1
				),
				'columns' => 'taxon_id as id,taxon_id,content,page_id,content as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		foreach((array)$content as $key => $val)
		{
			$tpt = $this->models->PagesTaxaTitles->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getDefaultProjectLanguage(),
						'page_id' => $val['page_id']
					),
					'column' => 'title'
				));
			$taxon=$this->getTaxonById($val['taxon_id']);
			$content[$key]['label'] =
				$this->formatTaxon(
					array(
						'taxon' => array('taxon' => $taxon['taxon'],'rank_id' => $taxon['rank_id'],'is_hybrid' => $taxon['is_hybrid']),
						'ranks' => $ranks
					)
				).' ('.$tpt[0]['title'].')';
		}



		// synonyms
		$synonyms = $this->models->Synonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(synonym) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('synonym')),
				),
				'columns' => 'id,taxon_id,synonym as label,synonym as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$synonyms = $this->filterResultsWithTokenizedSearch(array($p,$synonyms));
		$synonyms = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$synonyms));
		$synonyms = $this->sortResultsByMostTokensFound($synonyms);

		// common names
		$commonnames = $this->models->Commonnames->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(commonname,transliteration) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('commonname','transliteration')),
				),
				'columns' => 'id,language_id,taxon_id,commonname,transliteration,concat(ifnull(commonname,\'\'),\' \',ifnull(transliteration,\'\')) as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$commonnames = $this->filterResultsWithTokenizedSearch(array($p,$commonnames,array('commonname','transliteration')));

		foreach((array)$commonnames as $key => $val)
		{
			$commonnames[$key]['label'] =
				(!empty($val['transliteration']) ?
					($val['transliteration']).
					(!empty($val['commonname']) ?
						' '.sprintf($this->translate('(transliteration of "%s")'),$val['commonname']) :
						'') :
					$val['commonname']
				);

		}

		$commonnames = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$commonnames,'fields'=>array('commonname','transliteration'),'excerpt'=>false));
		$commonnames = $this->sortResultsByMostTokensFound($commonnames);

		// media
		$media = $this->models->MediaDescriptionsTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(description) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('description')),
				),
				'columns' => 'id,media_id,description as content,description as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);
		$media = $this->filterResultsWithTokenizedSearch(array($p,$media));
		$media = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$media));
		$media = $this->sortResultsByMostTokensFound($media);

		$d = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'taxon_id,file_name',
				'fieldAsIndex' => 'id'
			)
		);

		foreach((array)$media as $key => $val)
		{
			$media[$key]['id'] = $d[$val['media_id']]['taxon_id'];
			$media[$key]['taxon_id'] = $d[$val['media_id']]['taxon_id'];
			$media[$key]['label'] = $d[$val['media_id']]['file_name'];
		}

		return array(
			'label' => $this->translate('Species'),
			'results' => array(
				array(
					'label' => $this->translate('names'), // when changing the label 'Species names', do the same in searchMap()
					//'url' => '../species/edit.php?id=%s',
					'url' => '../nsr/taxon.php?id=%s',
					'data' => $taxa,
					'numOfResults' => count((array)$taxa)
				),
				array(
					'label' => $this->translate('descriptions'),
					//'url' => '../species/taxon.php?id=%s&page=%s',
					'url' => '../nsr/paspoort.php?id=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
				array(
					'label' => $this->translate('synonyms'),
					//'url' => '../species/synonyms.php?id=%s',
					'url' => '../nsr/taxon.php?id=%s',
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms)
				),
				array(
					'label' => $this->translate('common names'),
					//'url' => '../species/common.php?id=%s',
					'url' => '../nsr/taxon.php?id=%s',
					'data' => $commonnames,
					'numOfResults' => count((array)$commonnames)
				),
				array(
					'label' => $this->translate('media'),
					//'url' => '../species/media.php?id=%s',
					'url' => '../nsr/media.php?id=%s',
					'data' => $media,
					'numOfResults' => count((array)$media)
				),
			),
			'numOfResults' => count((array)$taxa)+count((array)$content)+count((array)$synonyms)+count((array)$commonnames)+count((array)$media)
		);

	}

	private function searchIntroduction( $p )
	{

		$content = $this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(topic,content) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('topic','content')),
				),
				'columns' => 'id,topic as label,content,content as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		return array(
			'label' => $this->translate('Introduction'),
			'results' => array(
				array(
					'label' => $this->translate('pages'),
					'url' =>'../introduction/edit.php?id=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				)
			),
			'numOfResults' => count((array)$content)
		);

	}

	private function searchGlossary( $p )
	{

		// glossary items
		$gloss = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(term,definition) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('term','definition')),
				),
				'columns' => 'id,term as label,definition as content,concat(ifnull(term,\'\'),\' \',ifnull(definition,\'\')) as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$gloss = $this->filterResultsWithTokenizedSearch(array($p,$gloss));
		$gloss = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$gloss));
		$gloss = $this->sortResultsByMostTokensFound($gloss);

		// glossary synonyms
		$synonym = $this->models->GlossarySynonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(synonym) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('synonym')),
				),
				'columns' => 'id,glossary_id,synonym as label,language_id,synonym as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$synonym = $this->filterResultsWithTokenizedSearch(array($p,$synonym));
		$synonym = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$synonym));
		$synonym = $this->sortResultsByMostTokensFound($synonym);

		return array(
			'label' => $this->translate('Glossary'),
			'results' => array(
				array(
					'label' => $this->translate('items'),
					'url' =>'../glossary/edit.php?id=%s',
					'data' => $gloss,
					'numOfResults' => count((array)$gloss)
				),
				array(
					'label' => $this->translate('synonyms'),
					'url' =>'../glossary/edit.php?id=%s',
					'data' => $synonym,
					'numOfResults' => count((array)$synonym)
				)
			),
			'numOfResults' => count((array)$gloss)+count((array)$synonym)
		);

	}

	private function searchLiterature( $p )
	{

		$c = 'id,
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\', \',
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
					) as label,
					text as content,
					concat(ifnull(text,\'\'),\' \',ifnull(author_first,\'\'),\' \',ifnull(author_second,\'\'),\' \',ifnull(year,\'\'),\' \',ifnull(year_2,\'\')) as '.__CONCAT_RESULT__;

		// literature
		$books = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(text) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('text')),
				),
				'columns' => $c,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT],
				'fieldAsIndex' => 'id'
			)
		);



		// literature by year (numbers, cannot be full text indexed)
		$more = $this->models->Literature->_get(
			array(
				'where' =>
					"project_id = ".$this->getCurrentProjectId()."
					and (year like '%".implode("%' or year like '%",$p[S_TOKENIZED_TERMS])."%')",
				'columns' => $c,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT],
				'fieldAsIndex' => 'id'
			)
		);

		foreach((array)$books as $key => $val)
			if (isset($more[$key])) unset($more[$key]);

		$books = array_merge((array)$books,(array)$more); // and resets the keys as well. how neat.
		$books = $this->filterResultsWithTokenizedSearch(array($p,$books));
		$books = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$books));
		$books = $this->sortResultsByMostTokensFound($books);

		return array(
			'label' => $this->translate('Literature'),
			'results' => array(
				array(
					'label' => $this->translate('references'),
					'url' => '../literature/edit.php?id=%s',
					'data' => $books,
					'numOfResults' => count((array)$books)
				)
			),
			'numOfResults' => count((array)$books)
		);

	}

	private function searchDichotomousKey( $p )
	{
		$keysteps = $this->models->Keysteps->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'number',
				'fieldAsIndex' => 'id'
			)
		);

		// endpoints
		$endpoints=array();
		if ( isset($p['species_results']) && isset($p['species_results']['results']) )
		{
			// harvest taxon id's from the search results from the species module
			$taxon_ids=array();
			foreach((array)$p['species_results']['results'] as $val)
			{
				if ($val['label']=='names')
				{
					foreach((array)$val['data'] as $match)
					{
						$taxon_ids[]=$match['id'];
					}
				}
			}

			if ( !empty($taxon_ids) )
			{
				$a=$this->translate('Step');
				$b=$this->translate('choice');

				$endpoints = $this->models->ChoicesKeysteps->freeQuery("
					select
						_a.id,
						_a.keystep_id,
						_a.show_order,
						_b.number,
						concat('".$a." ',_b.number,', ".$b." ',_a.show_order,' &rarr; ',_c.taxon) as label

					from
						%PRE%choices_keysteps _a

					left join %PRE%keysteps _b
						on _a.project_id = _b.project_id
						and _a.keystep_id = _b.id

					left join %PRE%taxa _c
						on _a.project_id = _c.project_id
						and _a.res_taxon_id = _c.id

					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.res_taxon_id in (" . implode(",",$taxon_ids) . ")

					order by
						concat('".$a." ',_b.number,', ".$b." ',_a.show_order)

					limit " . $p[S_RESULT_LIMIT_PER_CAT] . "
				");

				//_c.taxon as ".__CONCAT_RESULT__."
 				//$endpoints = $this->filterResultsWithTokenizedSearch(array($p,$endpoints));
				$endpoints = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$endpoints));
				//$endpoints = $this->sortResultsByMostTokensFound($endpoints);
			}
		}

		// choices
		$choices = $this->models->ChoicesContentKeysteps->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(choice_txt) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('choice_txt')),
				),
				'columns' => 'choice_id as id,choice_txt as content,choice_txt as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

 		$choices = $this->filterResultsWithTokenizedSearch(array($p,$choices));
		$choices = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$choices));
		$choices = $this->sortResultsByMostTokensFound($choices);

		foreach((array)$choices as $key => $val)
		{
			$step = $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['id'],
					),
					'columns' => 'keystep_id,show_order'
				)
			);

			$choices[$key]['label'] =
				sprintf(
					$this->translate('Step %s, choice %s'),
					$keysteps[$step[0]['keystep_id']]['number'],
					$step[0]['show_order']
					);

		}

		// steps
		$steps = $this->models->ContentKeysteps->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(title,content) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)"
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('title','content')),
				),
				'columns' => 'keystep_id as id,title as label,content,concat(ifnull(title,\'\'),\' \',ifnull(content,\'\')) as '.__CONCAT_RESULT__
			)
		);

		$steps = $this->filterResultsWithTokenizedSearch(array($p,$steps));
		$steps = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$steps));
		$steps = $this->sortResultsByMostTokensFound($steps);

		foreach((array)$steps as $key => $val)
			$steps[$key]['label'] = sprintf($this->translate('Step %s'),$keysteps[$val['id']]['number']);


		return array(
			'label' => $this->translate('Dichotomous key'),
			'results' => array(
				array(
					'label' => $this->translate('steps'),
					'url' =>'../key/step_show.php?id=%s',
					'data' => $steps,
					'numOfResults' => count((array)$steps)
				),
				array(
					'label' => $this->translate('choices'),
					'url' =>'../key/choice_edit.php?id=%s',
					'data' => $choices,
					'numOfResults' => count((array)$choices)
				),
				array(
					'label' => $this->translate('endpoints'),
					'url' =>'../key/choice_edit.php?id=%s',
					'data' => $endpoints,
					'numOfResults' => count((array)$endpoints)
				)
			),
			'numOfResults' => count((array)$choices)+count((array)$steps)+count((array)$endpoints)
		);

	}

	private function searchMatrixKey( $p )
	{
		//what IS the matrix?
		return null;

	}

	private function searchMap( $p )
	{

		// data types
		$titles = $this->models->GeodataTypesTitles->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(title) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('title')),
				),
				'columns' => 'id,title as label,title as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);

		$titles = $this->filterResultsWithTokenizedSearch(array($p,$titles));
		$titles = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$titles));
		$titles = $this->sortResultsByMostTokensFound($titles);

		return array(
			'label' => $this->translate('Map'),
			'results' => array(
				array(
					'label' => $this->translate('datatypes'),
					'url' => '../mapkey/data_types.php',
					'data' => $titles,
					'numOfResults' => count((array)$titles)
				),
			),
			'numOfResults' => count((array)$titles)
		);

	}

	private function searchContent( $p )
	{

		// content
		$content = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(subject,content) AGAINST ('".$p[S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('subject','content')),
				),
				'columns' => 'id,subject as label,content,language_id,concat(ifnull(subject,\'\'),\' \',ifnull(content,\'\')) as '.__CONCAT_RESULT__,
				'limit' => $p[S_RESULT_LIMIT_PER_CAT]
			)
		);


		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		return array(
			'label' => $this->translate('Content'),
			'results' => array(
				array(
					'label' => $this->translate('navigator'),
					'url' => '../content/content.php?id=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
			),
			'numOfResults' => count((array)$content)
		);

	}

	private function searchModules( $p, $freeModules=null )
	{
		if ($freeModules==false)
			return null;

		$d=array(
			'project_id' => $this->getCurrentProjectId(),
			'%LITERAL%' => $this->makeLikeClause($p[S_LIKETEXT_STRING],array('topic','content')),
		);

		if ($freeModules!='*')
			$d['module_id in']='('.implode(',',$freeModules).')';

		$content = $this->models->ContentFreeModules->_get(
			array(
				'id' => $d,
				'columns' => 'page_id,module_id,topic as label,content,concat(ifnull(topic,\'\'),\' \',ifnull(content,\'\')) as '.__CONCAT_RESULT__,
				'order' => 'module_id'
			)
		);

		// get appropriate free modules
		$modules = $this->models->FreeModulesProjects->_get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'columns' => 'id,module',
				'fieldAsIndex' => 'id'
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		$r = array();

		foreach((array)$content as $val) {
			$m = $modules[$val['module_id']]['module'];
			if (isset($r[$m]) && count((array)$r[$m])>=$p[S_RESULT_LIMIT_PER_CAT]) continue;
			$r[$m][] = $val;
		}

		$content = array();
		$t = 0;

		foreach((array)$r as $key => $val) {

			$content[] = array(
				'label' => $key,
				'url' => '../module/edit.php?id=%s',
				'data' => $val,
				'numOfResults' => count((array)$val)
			);

			$t += count((array)$val);

		}

		return array(
			'label' => $this->translate('Other modules'),
			'results' => $content,
			'numOfResults' => $t
		);

	}

}