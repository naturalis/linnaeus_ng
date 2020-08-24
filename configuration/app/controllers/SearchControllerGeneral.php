<?php /** @noinspection PhpMissingParentCallMagicInspection */

/*

	be aware that the MySQL function fnStripTags is only applied in some cases, i.e. large text columns.

	we are now doing LIKE instead of MATCH (as MATCH ignores wildcards at the beginning: *olar only matches olar, not polar)
	(do the same limitations still apply? think not.)

	flow:

		search
			string
			(no parameters)
		validateSearchString

		doSearch:

			$p = array(
				self::S_TOKENIZED_TERMS => tokenizeSearchString($search),
				self::S_LIKETEXT_STRING => prefabFullTextLikeString($tokenized),
				self::S_CONTAINS_LITERALS => doesSearchStringContainLiterals($tokenized),
				self::S_IS_CASE_SENSITIVE => false,
				self::S_RESULT_LIMIT_PER_CAT => 200,
				self::S_UNSET_ORIGINAL_CONTENT => true // if true, unsets the potentially large content fields after they've been excerpted
			);

			$this->searchSpecies( $p ) etc. (but not the matrix):


			// taxon content
			$content = $this->models->Table->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('content')), // _s  --> creates like-clause  ((taxon like '%phyllum a%' or taxon like '%orchid%'))
						'publish' => 1
					),
					'columns' => 'id,taxon_id,content,page_id,content as '.self::__CONCAT_RESULT__,
					'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
				)
			);

			$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
				currently bypassed, because use of LIKE rather than MATCH
			$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
				implements <span class="search-match"> around hits
			$content = $this->sortResultsByMostTokensFound($content);


		private function _s($s,$c)
		{
			$r=array();
			foreach((array)$c as $v)
				$r[] = str_replace(self::S_LIKETEXT_REPLACEMENT,$v,$s);

			return '('.implode(' or ',$r).')';
		}


	WE WILL NOT SEARCH THE MATRIX!

	STRIP TAGS AND SHIT FROM SEARCH STRING!!! (also + and - and * which fuck up the fulltext)

	search is case-insensitive!
	the php post-filtering is designed to allow for case-sensitivity, but it is not actually implemented.
	as the full text search is insensitive by default (unless we start altering the collation of the indexed
	columns), searches that have no literal bits ("...") will be harder to turn into case sensitive ones.
	(eh?)

	THERE IS NO SEARCHING THE MATRIX

*/

include_once ('Controller.php');
include_once ('SearchController.php');

class SearchControllerGeneral extends SearchController
{

	protected $_minSearchLength = 3;
	protected $_maxSearchLength = 50;
	protected $_searchStringGroupDelimiter = '"';
	protected $_excerptPreMatchLength;
	protected $_excerptPostMatchLength;
	protected $_excerptPrePostMatchString = '...';
	protected $_moduleNames;

	public $usedModels = array(
		'choices_keysteps',
		'content_keysteps',
		'content_free_modules',
		'free_modules_projects',
		'geodata_types_titles',
		'choices_content_keysteps',
		'keysteps',
		'literature',
		'glossary',
		'glossary_synonyms',
		'media_descriptions_taxon',
		'content_introduction',
		'media_taxon',
		'names',
		'name_types',
		'commonnames',
		'synonyms',
		'pages_taxa_titles',
		'content_taxa',
		'content'
    );

    public $controllerPublicName = 'Search';

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'search.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js'
	));


    public function __construct ()
    {
    	parent::__construct();
    	$this->initialize();
    }

	private function initialize()
	{
		$this->_minSearchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'min_search_length','subst'=>3));
		$this->_maxSearchLength=$this->moduleSettings->getModuleSetting(array('setting'=>'max_search_length','subst'=>50));

		$this->_excerptPostMatchLength = $this->moduleSettings->getModuleSetting( 'excerpt_post-match_length',35);
		$this->_excerptPreMatchLength = $this->moduleSettings->getModuleSetting( 'excerpt_pre-match_length',35);
		$this->_excerptPrePostMatchString = $this->moduleSettings->getModuleSetting( 'excerpt_pre_post_match_string', '...');
	}

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) {
            return;
        }
        if ($this->rHasVal('action','get_search_result_index'))
		{
			$this->smarty->assign(
				'returnText',
				$this->makeLookupList(array(
					'data'=>(array)$this->getSearchResultIndex(),
					'module'=>$this->controllerBaseName,
					'sortData'=>false
				))
			);
        }

		$this->allowEditPageOverlay = false;
        $this->printPage();
    }

    public function searchResetAction ()
    {
		$this->moduleSession->setModuleSetting( array('setting'=>'search') );
		$this->moduleSession->setModuleSetting( array('setting'=>'modules') );
		$this->moduleSession->setModuleSetting( array('setting'=>'freeModules') );
		$this->moduleSession->setModuleSetting( array('setting'=>'results') );
		$this->redirect('search.php');
	}

    public function searchAction ()
    {
   	if ($this->rHasVal('search'))
		{
			$modified_search=$this->removeSearchNoise( $this->rGetVal('search') );
			$this->moduleSession->setModuleSetting( array('setting'=>'search','value'=>$modified_search) );
			$this->moduleSession->setModuleSetting( array('setting'=>'search_original','value'=>$this->rGetVal('search')) );
			$this->moduleSession->setModuleSetting( array('setting'=>'modules','value'=>$this->rHasVal('modules') ? $this->rGetVal('modules') : null) );
			$this->moduleSession->setModuleSetting( array('setting'=>'freeModules','value'=>$this->rHasVal('freeModules') ? $this->rGetVal('freeModules') : null) );

			if ($this->validateSearchString($modified_search))
			{

				if ($this->rHasVal('extended','1'))
				{
					$results =
						$this->doSearch(
							array(
								'search'=>$modified_search,
								'modules'=>$this->rHasVal('modules') ? $this->rGetVal('modules') : null ,
								'freeModules'=>$this->rHasVal('freeModules') ? $this->rGetVal('freeModules') : null,
								'extended'=>true
							)
						);
				} else {
					$search='"'.trim($modified_search,'"').'"';
					$results=
						$this->doSearch(
							array(
								'search'=>$search,
								'modules'=>array('species'),
								'freeModules'=>false,
								'extended'=>false
							)
						);

				}
			
				$this->addMessage(sprintf('Searched for <span class="searched-term">%s</span>',$this->rGetVal('search')));
				$this->moduleSession->setModuleSetting( array('setting'=>'results','value'=>$results) );
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
		else
		{
			$this->moduleSession->setModuleSetting( array('setting'=>'results') );
		}

		$this->smarty->assign('CONSTANTS',
			array(
				'C_TAXA_SCI_NAMES'=>self::C_TAXA_SCI_NAMES,
				'C_TAXA_DESCRIPTIONS'=>self::C_TAXA_DESCRIPTIONS,
				'C_TAXA_SYNONYMS'=>self::C_TAXA_SYNONYMS,
				'C_TAXA_VERNACULARS'=>self::C_TAXA_VERNACULARS,
				'C_TAXA_ALL_NAMES'=>self::C_TAXA_ALL_NAMES,
				'C_SPECIES_MEDIA'=>self::C_SPECIES_MEDIA,
			)
		);

		$this->smarty->assign('modules',
		     $this->getProjectModules(['active' => 'y', 'ignore'=>MODCODE_MATRIXKEY]));
		$this->smarty->assign('minSearchLength',$this->_minSearchLength);
		$this->smarty->assign('search',
			array(
				'search'=>$this->moduleSession->getModuleSetting( 'search' ),
				'modules'=>$this->moduleSession->getModuleSetting( 'modules' ),
				'freeModules'=>$this->moduleSession->getModuleSetting( 'freeModules' )
			)
		);
		$this->smarty->assign('results',$this->moduleSession->getModuleSetting( 'results' ));

        $this->printPage();
    }

    /**
     * splits search string in groups delimited by ". if there's an uneven number of ", the last one is ignored.
     *
     * @param $s
     * @param bool $skipMarkers
     * @return array
     */
    private function tokenizeSearchString($s, $skipMarkers = false )
	{
		$parts = preg_split('/('.$this->_searchStringGroupDelimiter.')/i',$s,-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$b = null;    // buffer
		$t = array(); // resulting array of parts
		$r = false;   // "rec"-toggle

		foreach($parts as $val)
		{
			if ($val=='"')
			{
				// if "rec" is on, add the concatenated string to the results and reset the buffer
				if ($r)
				{
					if (!empty($b))
						array_push($t,$b);
					$b = null;
				}
				// and toggle "rec"
				$r = !$r;
			} else {
				// concatenate consecutive parts when "rec" is on (i.e., we are inside a "...")
				if($r) {
                    $b .= $val;
                } else {
                    // else split the part on spaces and add them as separate results
                    $t = array_merge($t, explode(' ', $val));
                }
			}
		}
		// take out the empty ones and return
		$terms=array_filter($t);

        // Not so fast, first we're going to append terms stripped off infraspecific markers
        if ($skipMarkers) {
            $markers = $this->getProjectRanksAbbreviations();
            if (!empty($markers)) {
                foreach ($terms as $t) {
                    foreach ($markers as $m) {
                        if (strpos($t, ' ' . $m . ' ') !== false) {
                            $strippedTerms[] = str_replace(' ' . $m . ' ', ' ', $t);
                        }
                    }
                }
                $terms = isset($strippedTerms) ? array_merge($terms, $strippedTerms) : $terms;
            }
        }

		return $terms;
	}

	private function prefabFullTextLikeString( $s )
	{
		array_walk($s,function(&$n){$n=str_replace(array("'","%","_"),array("\'","\%","\_"),$n);});

		// make tokens into a single string for mysql LIKE statement; add ### to replace with column name
		$r = "(".self::S_LIKETEXT_REPLACEMENT." like '%".implode("%' or ".self::S_LIKETEXT_REPLACEMENT." like '%",$s)."%')";

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
	    // @todo: Ask Maarten about this
		// OVERRIDE: the use of LIKE rather than MATCH make the post-filtering in PHP superfluous (i think)
        //
		foreach((array)$p[1] as $key => $val) {
			if (isset($p[1][$key][self::__CONCAT_RESULT__])) {
                unset($p[1][$key][self::__CONCAT_RESULT__]);
            }
		}
		return $p[1];

		$s = isset($p[0]) ? $p[0] : null;
		$r = isset($p[1]) ? $p[1] : null;

		if (!isset($s) || !isset($s[self::S_CONTAINS_LITERALS]) || !isset($s[self::S_TOKENIZED_TERMS]) || $s[self::S_CONTAINS_LITERALS] == false) {
            return $r;
        }

		// really shouldn't happen but just in case i should forget to add the self::__CONCAT_RESULT__ field in the query
        $concatField = isset($r[0][self::__CONCAT_RESULT__]) ? self::__CONCAT_RESULT__ : 'content';


		if ($s[self::S_CONTAINS_LITERALS]) {

			$filtered = array();

			// loop all results
			foreach((array)$r as $key => $result) {

				if (!isset($result[$concatField])) continue;


				$d = $this->stripTagsForSearchExcerpt($result[$concatField]);

				$match = false;

				// loop through all tokens
				foreach((array)$s[self::S_TOKENIZED_TERMS] as $token) {

					if ($match) {
                        break;
                    }

					// match if token exists in value of specific field of each result
					$match = $s[self::S_IS_CASE_SENSITIVE] ? strpos($d,$token)!==false : stripos($d,$token)!==false;

				}

				if ($match) {
					if ($concatField == self::__CONCAT_RESULT__)
					{
                        unset($result[self::__CONCAT_RESULT__]);
                    } else {
                        $result['warning'] = 'you forgot to add self::__CONCAT_RESULT__ to your query! these results based on matches in the assumed \'content\' column.';
                    }
					array_push($filtered,$result);
				}

			}

			return $filtered;

		}

		return $r;
	}

	private function getExcerptsSurroundingMatches( $p )
	{
		$s = isset($p['param']) ? $p['param'] : null;						// search parameters
		$r = isset($p['results']) ? $p['results'] : null;					// results array
		$f = isset($p['fields']) ? $p['fields'] : array('label','content');	// fields to match
		$x = isset($p['excerpt']) ? $p['excerpt'] : array('content');		// fields to be excerpted (rather than returned completely)

		if (!is_array($x)) {
            $x = array();
        } // for when called with 'excerpt' => false (excerpt none of the fields)

		if (!isset($s) || !isset($s[self::S_TOKENIZED_TERMS]) ) {
            return $r;
        }
        
		foreach((array)$r as $rKey => $result)
		{

			foreach((array)$f as $fKey => $field)
			{

				$fullmatches = array();

				if (isset($result[$field]))
				{

					$stripped = $this->stripTagsForSearchExcerpt($result[$field]);

					foreach((array)$s[self::S_TOKENIZED_TERMS] as $token)
					{
						$r[$rKey]['tokens_found'][$token]=!isset($r[$rKey]['tokens_found'][$token]) ? 0 : $r[$rKey]['tokens_found'][$token];

						$matches=array();
						preg_match_all('/'.$token.'/'.($s[self::S_IS_CASE_SENSITIVE] ? '' : 'i'),$stripped,$matches,PREG_OFFSET_CAPTURE);

						if (isset($matches[0]))
						{
							foreach((array)$matches[0] as $match)
							{
								if (isset($match[0]))
								{
									$fullmatches[]=$match;
									$r[$rKey]['tokens_found'][$token]++;
								}
							}
						}

						unset($matches);

					}

					foreach((array)$fullmatches as $match) {

						if (in_array($field,$x)) {

							$start = ($match[1] < $this->_excerptPreMatchLength ? 0 : ($match[1] - $this->_excerptPreMatchLength));
							$r[$rKey]['matches'][]=
								($start>0 ? $this->_excerptPrePostMatchString : '').
								substr($stripped,$start,($match[1]-$start)).
								'<span class="search-match">'.$match[0].'</span>'.
								substr($stripped,$match[1]+strlen($match[0]),$this->_excerptPostMatchLength).
								($match[1]+strlen($match[0])+$this->_excerptPostMatchLength<strlen($stripped) ? $this->_excerptPrePostMatchString : '');

						} else {

							$r[$rKey]['matches'][]=
								substr($stripped,0,$match[1]).
								'<span class="search-match">'.$match[0].'</span>'.
								substr($stripped,$match[1]+strlen($match[0]));

						}

					}

					if ($s[self::S_UNSET_ORIGINAL_CONTENT] && in_array($field,$x))
						unset($r[$rKey][$field]);

				}

			}

		}

		return $r;
	}

	private function sortResultsByMostTokensFound( $data, $secondaryfield=null)
	{
		if (count((array)$data)<2) {
            return $data;
        }

		uasort($data,function($a,$b) use ($secondaryfield)
		{
			$aCount=$bCount=0;

			if (!isset($a['matches']) || !isset($b['matches']))
			{
				foreach((array)$a['tokens_found'] as $token=>$count)
				{
					$aCount+=$count;
				}
				foreach((array)$b['tokens_found'] as $token=>$count)
				{
					$bCount+=$count;
				}
			} else {
				$aCount=count((array)$a['matches']);
				$bCount=count((array)$b['matches']);
			}

			$r=0;
			if ($aCount>$bCount) {
                $r = -1;
            } else if ($aCount<$bCount) {
                $r = 1;
            } else if (!empty($secondaryfield)) {
				if ($a[$secondaryfield]<$b[$secondaryfield]) {
                    $r = -1;
                } else if ($a[$secondaryfield]>$b[$secondaryfield]) {
                    $r = 1;
                }
			}
			return $r;
		});

		return $data;

	}

	private function makeLikeClause( $s, $c)
	{
		// creates like-clause  ((taxon like '%phyllum a%' or taxon like '%orchid%'))
		$r=array();
		foreach((array)$c as $v) {
            $r[] = str_replace(self::S_LIKETEXT_REPLACEMENT, $v, $s);
        }

		return '('.implode(' or ',$r).')';
	}

	private function getModuleName( $id )
	{
		if (is_null($this->_moduleNames)) {
			$d=$this->getProjectModules();
			foreach((array)$d['modules'] as $val) {
				if (!isset($val['module_id'])) continue;
				$this->_moduleNames[$val['module_id']]=$val['module'];
			}
		}

		return isset($this->_moduleNames[$id]) ? $this->_moduleNames[$id] : $id;
	}

	private function doSearch( $p=null )
	{
		$search=isset($p['search']) ? $p['search'] : null;
		$modules=isset($p['modules']) ? $p['modules'] : null;
		$freeModules=isset($p['freeModules']) ? $p['freeModules'] : null;
		$extended=isset($p['extended']) ? $p['extended'] : true;

		// Ruud 07-12-16: option to duplicate the search term WITHOUT infraspecific markers.
		// Only applies to literal search strings and for markers present in project
		// This is passed to $this->tokenizeSearchString as a second parameter
		$skipMarkers = isset($p['skip_markers']) ? $p['skip_markers'] : true;

		if (empty($search))
			return null;

		$searchAll=($modules=='*');
		$tokenized = $this->tokenizeSearchString($search, $skipMarkers);
		$liketxt = $this->prefabFullTextLikeString($tokenized);
		$containsLiterals = $this->doesSearchStringContainLiterals($tokenized);

		$p = array(
			self::S_TOKENIZED_TERMS => $tokenized,
			self::S_LIKETEXT_STRING => $liketxt,
			self::S_CONTAINS_LITERALS => $containsLiterals,
			self::S_IS_CASE_SENSITIVE => false,
			self::S_RESULT_LIMIT_PER_CAT => self::V_RESULT_LIMIT_PER_CAT, // max results per category (module)
			self::S_UNSET_ORIGINAL_CONTENT => true, // if true, unsets the potentially large content fields after they've been excerpted
			self::S_EXTENDED_SEARCH => $extended
		);

        // @check_this: restrict not defined or set in this scope
		if (isset($restrict))
			$p['restrict']=$restrict;

		if ( $searchAll ||
			(
				( is_array($modules) && in_array('species',$modules) ) ||
				( is_array($modules) && in_array('key',$modules) )
			)
		)
		{
			$species=$this->searchSpecies( $p );
			$p['species_results']=$species;
		}

		$results =
			array(
				'introduction' =>
					($searchAll || (is_array($modules) && in_array('introduction',$modules)) ? $this->searchIntroduction( $p ) : null),
				'glossary' =>
					($searchAll || (is_array($modules) && in_array('glossary',$modules)) ? $this->searchGlossary( $p ) : null),
				'literature' =>
					($searchAll || (is_array($modules) && in_array('literature',$modules)) ? $this->searchLiterature( $p ) : null),
				//'species' =>
				//	($searchAll || (is_array($modules) && in_array('species',$modules)) ? $this->searchSpecies( $p ) : null),
				'species' =>
					($searchAll || (is_array($modules) && in_array('species',$modules)) ? $species : null),
				'dichkey' =>
					($searchAll || (is_array($modules) && in_array('key',$modules)) ? $this->searchDichotomousKey( $p ) : null),
				'matrixkey' =>
					($searchAll || (is_array($modules) && in_array('matrixkey',$modules)) ? $this->searchMatrixKey( $p ) : null), // stub
				'map' =>
					($searchAll || (is_array($modules) && in_array('mapkey',$modules)) ? $this->searchMap( $p ) : null),
				'content' =>
					($searchAll || (is_array($modules) && in_array('content',$modules)) ? $this->searchContent( $p ) : null),
			);

		$d=$this->searchModules($p,$freeModules);
		$results=array_merge($results,(array)$d);

		$totalcount=0;

		foreach((array)$results as $val)
			$totalcount += $val['numOfResults'];

		return array('data'=>$results,'count'=>$totalcount);
	}

	private function searchSpecies( $p )
	{
		// taxa
		$taxa = $this->models->Taxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('taxon'))
				),
				'columns' => 'id,taxon as label,rank_id,is_hybrid,parent_id,taxon as '.self::__CONCAT_RESULT__ ,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
				'order'=>'taxon'
			)
		);
		
		$taxa = $this->filterResultsWithTokenizedSearch(array($p,$taxa));
		$taxa = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$taxa));
		// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
		//$taxa = $this->sortResultsByMostTokensFound($taxa);

		//$ranks = $this->newGetProjectRanks();
		$ranks = $this->getProjectRanks();

		foreach((array)$taxa as $key => $val)  {
			$taxa[$key]['label']=
				$this->formatTaxon(
					array(
						'taxon' =>
							array(
								'id' => $val['id'],
								'taxon' => $val['label'],
								'parent_id'=>$val['parent_id'],
								'rank_id' => $val['rank_id'],
								'is_hybrid' => $val['is_hybrid']
							),
						'ranks'=> $ranks
					)
				);
			unset($taxa[$key]['rank_id'],$taxa[$key]['is_hybrid']);
		}

		if ($p[self::S_EXTENDED_SEARCH])
		{
			// taxon content
			$content = $this->models->ContentTaxa->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						//'%LITERAL%' => "MATCH(content) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
						'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('fnStripTags(content)')),
						'publish' => 1
					),
					'columns' => 'taxon_id as id,taxon_id,content,page_id as cat,content as '.self::__CONCAT_RESULT__,
					'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
				)
			);

			$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
			$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
			// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
			// actual ordering by taxon name lower below (usort)
			//$content = $this->sortResultsByMostTokensFound($content);

			$pagenames=array();
			foreach((array)$content as $key=>$val)
			{
				$t=$this->getTaxonById($val['taxon_id']);
                $ct = $this->models->PagesTaxaTitles->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->getCurrentLanguageId(),
                        'taxon_id' => $val['taxon_id'],
                        'page_id' => $val['cat']
                    ),
                    'columns' => 'title'
                ));
				$content[$key]['label']=$t['taxon'].' ('.strtolower($ct[0]['title']).')';
			}

		}

		if ( !empty($content) )
			usort( $content, function($a,$b){ return $a['label']>$b['label'];  });


		// synonyms
		$synonyms = $this->models->Synonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(synonym) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('synonym')),
				),
				'columns' => 'id,taxon_id,synonym as label,synonym as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
				'order'=>'synonym'
			)
		);

		$synonyms = $this->filterResultsWithTokenizedSearch(array($p,$synonyms));
		$synonyms = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$synonyms));
		// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
		//$synonyms = $this->sortResultsByMostTokensFound($synonyms);

		// common names
		$commonnames = $this->models->Commonnames->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(commonname,transliteration) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('commonname','transliteration')),
				),
				'columns' =>
					'taxon_id as id,language_id,taxon_id,commonname,transliteration,
					concat(ifnull(commonname,\'\'),\' \',ifnull(transliteration,\'\')) as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
				'order'=>'commonname,transliteration'
			)
		);

		$commonnames = $this->filterResultsWithTokenizedSearch(array($p,$commonnames,array('commonname','transliteration')));

		foreach((array)$commonnames as $key => $val) {

			$commonnames[$key]['label'] =
				(!empty($val['transliteration']) ?
					($val['transliteration']).
					(!empty($val['commonname']) ?
						' '.sprintf($this->translate('(transliteration of "%s")'),$val['commonname']) :
						'') :
					$val['commonname']
				);

		}

		$commonnames = $this->getExcerptsSurroundingMatches(
			array('param'=>$p,'results'=>$commonnames,'fields'=>array('commonname','transliteration'),'excerpt'=>false)
		);

		// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
		//$commonnames = $this->sortResultsByMostTokensFound($commonnames);



		if ($this->models->Names->getTableExists())
		{
			$nameTypes = $this->models->NameTypes->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'columns' => 'nametype,id',
					'fieldAsIndex' => 'id'
				)
			);

			$names = $this->models->Names->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('name')),
					),
					'columns' => 'taxon_id as id,language_id,taxon_id,name,type_id,name as '.self::__CONCAT_RESULT__,
					'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
					'order'=>'name'
				)
			);

			$names = $this->filterResultsWithTokenizedSearch(array($p,$names,array('name')));

			foreach((array)$names as $key => $val) {
				$names[$key]['label'] = $val['name'];
				$names[$key]['predicate'] = $nameTypes[$val['type_id']]['nametype'];
				if ($names[$key]['predicate']!=PREDICATE_VALID_NAME)
					$names[$key]['subject'] = $this->getTaxonById($val['taxon_id']);

			}

			$names = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$names,'fields'=>array('name'),'excerpt'=>false));
			// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
			//	$names = $this->sortResultsByMostTokensFound($names);

		}


		if ($p[self::S_EXTENDED_SEARCH])
		{
			// media
			$media = $this->models->MediaDescriptionsTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						//'%LITERAL%' => "MATCH(description) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
						'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('description')),
					),
					'columns' => 'id,media_id,description as content,description as '.self::__CONCAT_RESULT__,
					'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
				)
			);

			$media = $this->filterResultsWithTokenizedSearch(array($p,$media));
			$media = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$media));
			// REFAC2015 - choice of alphabetical sort or sort by most tokens should be a setting
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
				$media[$key]['label'] = $d[$val['media_id']]['file_name'];
			}

		}

		$results=array();
		$numOfResults=0;
/*
		if (isset($taxa))
		{
			$results[self::C_TAXA_SCI_NAMES]=
				array(
					'label' => $this->translate('Species names'), // when changing the label 'Species names', do the same in searchMap() & searchDichKey
					'url' => '../species/taxon.php?id=%s',
					'data' => $taxa,
					'numOfResults' => count((array)$taxa)
				);

			$numOfResults+=count((array)$taxa);
		}
*/
		if (isset($content))
		{
			$results[self::C_TAXA_DESCRIPTIONS]=
				array(
					'label' => $this->translate('Species descriptions'),
					'url' => '../species/taxon.php?id=%s&cat=#CAT#',
					'data' => $content,
					'numOfResults' => count((array)$content)
				);

			$numOfResults+=count((array)$content);

		}

		if (isset($names))
		{

			$results[self::C_TAXA_ALL_NAMES]=
				array(
					'label' => $this->translate('Taxon names'),
					'url' => '../species/taxon.php?cat=names&id=%s',
					'data' => $names,
					'numOfResults' => count((array)$names)
				);

			$numOfResults+=count((array)$names);

		}

		if (isset($media)) {

			$results[self::C_SPECIES_MEDIA]=
				array(
					'label' => $this->translate('Species media'),
					'url' => '../species/taxon.php?cat=media&id=%s',
					'data' => $media,
					'numOfResults' => count((array)$media)
				);

			$numOfResults+=count((array)$media);

		}

		foreach((array)$results as $rKey => $rVal) {
			foreach((array)$rVal['data'] as $dKey => $dVal) {
				if (!isset($dVal['predicate']) || $dVal['predicate']!=PREDICATE_PREFERRED_NAME) {
					$id=isset($dVal['taxon_id']) ? $dVal['taxon_id'] : $dVal['id'];
					$results[$rKey]['data'][$dKey]['preferredName']=$this->getPreferredName($id);
				}
			}
		}
		
		return
			array(
				'label'=> $this->getModuleName(MODCODE_SPECIES),
				'results'=>$results,
				'numOfResults'=>$numOfResults
			);

	}

	private function searchIntroduction( $p )
	{
		$content = $this->models->ContentIntroduction->_get(
			array(
				'id' => array(

					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(topic,content) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('topic','fnStripTags(content)')),
				),
				'columns' => 'page_id as id,topic as label,content,content as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		return array(
			'label'=> $this->getModuleName(MODCODE_INTRODUCTION),
			'results' => array(
				array(
					'label' => $this->translate('Introduction'),
					'url' =>'../introduction/topic.php?id=%s',
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
					//'%LITERAL%' => "MATCH(term,definition) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('term','fnStripTags(definition)')),
				),
				'columns' => 'id,term as label,definition as content,concat(ifnull(term,\'\'),\' \',ifnull(definition,\'\')) as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
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
					//'%LITERAL%' => "MATCH(synonym) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('synonym')),
				),
				'columns' => 'id,glossary_id,synonym as label,language_id,synonym as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		$synonym = $this->filterResultsWithTokenizedSearch(array($p,$synonym));
		$synonym = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$synonym));
		$synonym = $this->sortResultsByMostTokensFound($synonym);

		foreach((array)$synonym as $key => $val)
		{
			$d = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['glossary_id']
					),
					'columns' => 'term'
				)
			);
			$synonym[$key]['label'].=' '.sprintf($this->translate('(synonym of "%s")'),$d[0]['term']);
			$synonym[$key]['id']=$val['glossary_id'];
			unset($synonym[$key]['glossary_id']);
		}

		return array(
			'label'=> $this->getModuleName(MODCODE_GLOSSARY),
			'results' => array(
				array(
					'label' => $this->translate('Items'),
					'url' =>'../glossary/term.php?id=%s',
					'data' => $gloss,
					'numOfResults' => count((array)$gloss)
				),
				array(
					'label' => $this->translate('Synonyms'),
					'url' =>'../glossary/term.php?id=%s',
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
					concat(ifnull(text,\'\'),\' \',ifnull(author_first,\'\'),\' \',ifnull(author_second,\'\'),\' \',ifnull(year,\'\'),\' \',ifnull(year_2,\'\')) as '.self::__CONCAT_RESULT__;

		// literature
		$books = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(text) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('fnStripTags(text)')),
				),
				'columns' => $c,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
				'fieldAsIndex' => 'id'
			)
		);



		// literature by year (numbers, cannot be full text indexed)
		$more = $this->models->Literature->_get(
			array(
				'where' =>
					"project_id = ".$this->getCurrentProjectId()."
					and (year like '%".implode("%' or year like '%",$p[self::S_TOKENIZED_TERMS])."%')",
				'columns' => $c,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT],
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
			'label'=> $this->getModuleName(MODCODE_LITERATURE),
			'results' => array(
				array(
					'label' => $this->translate('Literature'),
					'url' => '../literature/reference.php?id=%s',
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
				'columns' => 'id, number',
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
				if ($val['label']=='Species names')
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
						_a.id as _id,
						_a.keystep_id as id,
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

					limit " . $p[self::S_RESULT_LIMIT_PER_CAT] . "

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
					//'%LITERAL%' => "MATCH(choice_txt) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('fnStripTags(choice_txt)')),
				),
				'columns' => 'id,choice_id,choice_txt as content,choice_txt as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		foreach((array)$choices as $key => $val)
		{
			$step = $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $val['choice_id'],
					),
					'columns' => 'keystep_id,show_order'
				)
			);

			$choices[$key]['label']=sprintf($this->translate('Step %s, choice %s'),$keysteps[$step[0]['keystep_id']]['number'],$step[0]['show_order']);
			$choices[$key]['id']=$step[0]['keystep_id'];
		}

 		$choices = $this->filterResultsWithTokenizedSearch(array($p,$choices));
		$choices = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$choices));
		$choices = $this->sortResultsByMostTokensFound($choices,'label');


		// steps
		$steps = $this->models->ContentKeysteps->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(title,content) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)"
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('title','fnStripTags(content)')),
				),
				'columns' => 'keystep_id as id,title as label,content,concat(ifnull(title,\'\'),\' \',ifnull(content,\'\')) as '.self::__CONCAT_RESULT__
			)
		);

		foreach((array)$steps as $key => $val)
		{
			$steps[$key]['label'] = sprintf($this->translate('Step %s'),$keysteps[$val['id']]['number']);
		}

		$steps = $this->filterResultsWithTokenizedSearch(array($p,$steps));
		$steps = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$steps));
		$steps = $this->sortResultsByMostTokensFound($steps,'label');

		return array(
			'label'=> $this->getModuleName(MODCODE_KEY),
			'results' => array(
				array(
					'label' => $this->translate('Steps'),
					'url' =>'../key/index.php?step=%s',
					'data' => $steps,
					'numOfResults' => count((array)$steps)
				),
				array(
					'label' => $this->translate('Choices'),
					'url' =>'../key/index.php?step=%s',
					//'url' =>'../key/index.php?choice=%s',
					'data' => $choices,
					'numOfResults' => count((array)$choices)
				),
				array(
					'label' => $this->translate('endpoints'),
					'url' =>'../key/index.php?step=%s',
					'data' => $endpoints,
					'numOfResults' => count((array)$endpoints)
				)
			),
			'numOfResults' => count((array)$choices)+count((array)$steps)+count((array)$endpoints)
		);

	}

	private function searchMatrixKey( $p )
	{
		// NO! NO! NO!
		return null;
	}

	private function searchMap( $p )
	{

		// data types
		$titles = $this->models->GeodataTypesTitles->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'%LITERAL%' => "MATCH(title) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('title')),
				),
				'columns' => 'id,title as label,title as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		$titles = $this->filterResultsWithTokenizedSearch(array($p,$titles));
		$titles = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$titles));
		$titles = $this->sortResultsByMostTokensFound($titles);

		return array(
			'label'=> $this->getModuleName(MODCODE_DISTRIBUTION),
			'results' => array(
				array(
					'label' => $this->translate('Datatypes'),
					'url' => '../mapkey/',
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
					//'%LITERAL%' => "MATCH(subject,content) AGAINST ('".$p[self::S_FULLTEXT_STRING]."' in boolean mode)",
					'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('subject','fnStripTags(content)')),
				),
				'columns' => 'id,subject as label,content,language_id,concat(ifnull(subject,\'\'),\' \',ifnull(content,\'\')) as '.self::__CONCAT_RESULT__,
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		return array(
			'label'=> $this->translate('Navigator'),
			'results' => array(
				array(
					'label' => $this->translate('Navigator'),
					'url' => '../linnaeus/content.php?id=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
			),
			'numOfResults' => count((array)$content)
		);

	}

	private function searchModule( $p )
	{
		$id=isset($p['module']['id']) ? $p['module']['id'] : null;
		$name=isset($p['module']['name']) ? $p['module']['name'] : null;

		if (is_null($id))
			return null;

		$d=array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => $id,
			'%LITERAL%' => $this->makeLikeClause($p[self::S_LIKETEXT_STRING],array('topic','fnStripTags(content)')),
		);

		$content = $this->models->ContentFreeModules->_get(
			array(
				'id' => $d,
				'columns' => 'page_id as id,module_id,topic as label,content,concat(ifnull(topic,\'\'),\' \',ifnull(content,\'\')) as '.self::__CONCAT_RESULT__,
				'order' => 'module_id',
				'limit' => $p[self::S_RESULT_LIMIT_PER_CAT]
			)
		);

		$content = $this->filterResultsWithTokenizedSearch(array($p,$content));
		$content = $this->getExcerptsSurroundingMatches(array('param'=>$p,'results'=>$content));
		$content = $this->sortResultsByMostTokensFound($content);

		return array(
			'label'=> $name,
			'results' => array(
				array(
					'label' => $name,
					'url' => '../module/topic.php?modId='.$id.'&id=%s',
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
			),
			'numOfResults' => count((array)$content)
		);

	}

	private function searchModules( $p, $fMod=null )
	{

		if (is_null($fMod))
			return null;

		$r=array();

		if ($fMod=='*') {
			$fMod=array();
			$m=$this->models->FreeModulesProjects->_get(array('id'=>array('project_id' => $this->getCurrentProjectId()),'columns' => 'id'));
			foreach((array)$m as $val) {
				$fMod[]=$val['id'];
			}
		}

		foreach((array)$fMod as $mod) {
			$m=$this->models->FreeModulesProjects->_get(
				array(
					'id'=>array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $mod
					),
					'columns' => 'module',
				)
			);
			$p['module']=array(
				'id'=>$mod,
				'name'=>$m[0]['module']
			);
			$r[$m[0]['module']]=$this->searchModule( $p );
		}

		return $r;

	}

}

