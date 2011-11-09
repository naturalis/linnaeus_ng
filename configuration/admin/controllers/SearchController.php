<?php

/*

	$taxa = $this->models->{$species['results'][0]['data'][0]['replace']['model']}->_get(

				$matches = $this->getColumnMatches($search,$val,array('synonym'));
	
				if (!is_null($matches)) {
	
					$synonyms[$key]['replace'] = array(
						'model' => $this->models->GlossarySynonym->getClassName(),
						'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id'],'language_id' => $val['language_id']),
						'matches' => $matches,
					);
					
				}


*/

/*

	$textWithGlossaryMatches = $linnaeusController->matchGlossaryTerms($textWithoutGlossaryMatches));

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
		'occurrence_taxon'
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'basics.css',
		'glossary.css',
		'search.css',
		'key.css',
		'literature.css',
		'map.css',
		'matrix.css',
		'module.css',
		'species.css',
		'index.css',
		'colorbox/colorbox.css',
		'dialog/jquery.modaldialog.css',
		'lookup.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'speciesdetailpage10.js',
		'lookup.js'
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

/*
		if (isset($_SESSION['user']['search']['redo']) && $_SESSION['user']['search']['redo']==true) {
		
			$this->requestData['search'] = $_SESSION['user']['search']['lastSearch'];

			$_SESSION['user']['search']['redo'] = false;

		}
*/
		$results = $this->_searchAction();

		$this->smarty->assign('results',$results);

		if (isset($this->requestData['search'])) $this->smarty->assign('search',$this->requestData['search']);
	
		$this->smarty->assign('modules',$this->getProjectModules());

		$this->smarty->register_block('h', array(&$this,'highlightFound'));

		$this->smarty->register_block('foundContent', array(&$this,'foundContent'));

        $this->printPage();
  
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

	public function redoSearchAction()
	{

		$this->storeHistory = false;

		if ($_SESSION['user']['search']['hasSearchResults'] && $_SESSION['user']['search']['lastSearch']) {

			$_SESSION['user']['search']['redo'] = true;

		}

		$this->redirect('search.php');

	}

    /**
     * Index of project: introduction
     *
     * @access    public
     */
    private function _searchAction ()
    {

		if ($this->rHasVal('search')) {

			if (strlen($this->requestData['search'])>2) { 
			
				if (
					!isset($_SESSION['user']['search'][$this->requestData['search']]) || 
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
		
					$_SESSION['user']['search'][$this->requestData['search']]['results'] = $results;

				} else {

					$results = $_SESSION['user']['search'][$this->requestData['search']]['results'];

				}
				
				$_SESSION['user']['search']['hasSearchResults'] = $results['numOfResults']>0;
				$_SESSION['user']['search']['lastSearch'] = $this->requestData['search'];
					
				return $results;
	

			} else {
			
				$this->addMessage(sprintf(_('Search term too short. Minimum is %s characters.'),3));
				
				return null;
			
			}

		} else {

//			unset($_SESSION['user']['search'][$this->requestData['search']]['results']);

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
$search = 'Backenroth';
		$species = $this->searchSpecies($search);
// INTRODUCTION etc


q($this->searchContent($search),1);
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

	private function makeRegExpCompatSearchString($s,$phpCompat=false)
	{
	
		$s = trim($s);

		// if string enclosed by " take it literally		
		if (preg_match('/^"(.+)"$/',$s))
			return
				($phpCompat ? '/' : '').
				'('.mysql_real_escape_string(substr($s,1,strlen($s)-2)).')'.
				($phpCompat ? '/i' : '');

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0)
			return
				($phpCompat ? '/' : '').
				mysql_real_escape_string($s).
				($phpCompat ? '/i' : '');

		$s = str_replace(' ','|',$s);

		return
			($phpCompat ? '/' : '').
			'('.mysql_real_escape_string($s).')'.
			($phpCompat ? '/i' : '');
	
	}

	private function getColumnMatches($search,$values,$fields)
	{
	
		$results = array();
	
		if (!is_array($fields))
			$columns[] = $fields;
		else
			$columns = $fields;
		
		foreach($columns as $key => $column) {
		
			$content = strip_tags($values[$column]);
	
			preg_match_all($this->makeRegExpCompatSearchString($search,true),$content,$matches,PREG_OFFSET_CAPTURE);

			foreach((array)$matches[0] as $mK => $mV) {
	
				$matches[0][$mK]['column'] = $column;
				$matches[0][$mK]['replaced'] = false;
	
				$matches[0][$mK]['excerpt'] =
					($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? '...' : '').
					substr(
						$content,
						($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $mV[1]-$this->controllerSettings['excerptLengthLeft'] : 0),
						($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $this->controllerSettings['excerptLengthLeft'] : $mV[1])
					).
					$mV[0].
					substr(
						$content,
						$mV[1]+strlen($mV[0]),
						$this->controllerSettings['excerptLengthRight']
					).					
					($mV[1]+strlen($mV[0])+$this->controllerSettings['excerptLengthRight'] >= strlen($content) ? '' : '...')
					;
	
				$matches[0][$mK]['highlighted'] =
					($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? '...' : '').
					substr(
						$content,
						($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $mV[1]-$this->controllerSettings['excerptLengthLeft'] : 0),
						($mV[1] > $this->controllerSettings['excerptLengthLeft'] ? $this->controllerSettings['excerptLengthLeft'] : $mV[1])
					).
					'[span]'.
					$mV[0].
					'[/span]'.
					substr(
						$content,
						$mV[1]+strlen($mV[0]),
						$this->controllerSettings['excerptLengthRight']
					).					
					($mV[1]+strlen($mV[0])+$this->controllerSettings['excerptLengthRight'] >= strlen($content) ? '' : '...')
					;
	
			}
			
			$results = array_merge($results,$matches[0]);
			
		}
		
		return empty($results) ? null : $results;
	
	}

	private function searchSpecies($search,$extensive=true)
	{

		$regExSs = $this->makeRegExpCompatSearchString($search);

		$taxa = $synonyms = $commonnames = $content = $media = array();

		$ranks = $this->getProjectRanks(array('idsAsIndex'=>true));

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon regexp' => $regExSs
				),
				'columns' => 'id as taxon_id,taxon as label,rank_id'
			)
		);

		foreach((array)$taxa as $key => $val)  {
		
			$taxa[$key]['rank'] = $ranks[$val['rank_id']]['rank'];
			$taxa[$key]['replace'] = array(
				'model' => $this->models->Taxon->getClassName(),
				'column' => 'taxon',
				'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['taxon_id'])
			);
			
		}

		$synonyms = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(),
					'synonym regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'id,taxon_id,synonym as label,\'names\' as cat'
			)
		);

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
					if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) as label,
					\'names\' as cat'
			)
		);	
	
	
		foreach((array)$commonnames as $key => $val) {

			$l = $this->models->Language->_get(array('id'=>$val['language_id']));
			$commonnames[$key]['language'] = $l['language'];

		}

		if ($extensive) {

			$content = $this->models->ContentTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						//'language_id' => $this->getCurrentLanguageId(),
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
						//'language_id' => $this->getCurrentLanguageId(),
						'file_name regexp' => $this->makeRegExpCompatSearchString($search)
					),
				'columns' => 'id,taxon_id,id as media_id,file_name as label,\'media\' as cat'
				)
			);
	
			$media2 = $this->models->MediaDescriptionsTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						//'language_id' => $this->getCurrentLanguageId(),
						'description regexp' => $this->makeRegExpCompatSearchString($search)
					),
				'columns' => 'id,taxon_id,media_id,description as content,\'media\' as cat'
				)
			);
	
			$media = array_merge((array)$media1,(array)$media2);

		}		
		
		$d = array_merge((array)$synonyms,(array)$commonnames,(array)$content,(array)$media);

		return array(
			'results' => array(
				array(
					'label' => _('Species names'),
					'data' => $taxa, // when changing the label 'Species names', do the same in searchMap()
					'numOfResults' => count((array)$taxa)
				),
				array(
					'label' => _('Species descriptions'),
					'data' => $content,
					'numOfResults' => count((array)$content)
				),
				array(
					'label' => _('Species synonyms'),
					'data' => $synonyms,
					'numOfResults' => count((array)$synonyms)
				),
				array(
					'label' => _('Species common names'),
					'data' => $commonnames,
					'numOfResults' => count((array)$commonnames)
				),
				array(
					'label' => _('Species media'),
					'data' => $media,
					'numOfResults' => count((array)$media)
				),
			),
			'taxonList' => $this->makeTaxonList($d),
			'categoryList' => $this->makeCategoryList(),
			'numOfResults' => count((array)$d)+count((array)$taxa)
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

	private function searchModules($search)
	{

		$content = $this->models->ContentFreeModule->_get(
			array(
				'where' => 
					'project_id = '.$this->getCurrentProjectId().' and
					(topic regexp \''.$this->makeRegExpCompatSearchString($search).'\' or
					content regexp \''.$this->makeRegExpCompatSearchString($search).'\')',
				'columns' => 
					'page_id,
					module_id,
					topic as label,
					if(content regexp \''.$this->makeRegExpCompatSearchString($search).'\',content,null) as content',
				'order' => 'module_id'
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
			'numOfResults' => count((array)$content)
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

		$matrices = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(),
					'name regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'matrix_id,name as label'
			)
		);

		$characteristics = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(),
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
					//'language_id' => $this->getCurrentLanguageId(),
					'label regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'state_id,label'
			)
		);

		$states2 	 = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(),
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
						//'language_id' => $this->getCurrentLanguageId(),
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
					//'language_id' => $this->getCurrentLanguageId(), 
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
					'numOfResults' => count((array)$matrices)
				),
				array(
					'label' => _('Matrix key characteristics'),
					'data' => $characteristics,
					'numOfResults' => count((array)$characteristics)
				),
				array(
					'label' => _('Matrix key states'),
					'data' => $states,
					'numOfResults' => count((array)$states)
				)
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
					//'language_id' => $this->getCurrentLanguageId(),
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
							//'language_id' => $this->getCurrentLanguageId(),
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
							//'language_id' => $this->getCurrentLanguageId(),
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
					//'language_id' => $this->getCurrentLanguageId(),
					'title regexp' => $this->makeRegExpCompatSearchString($search)
				),
				'columns' => 'keystep_id,title,title as label'
			)
		);

		$steps2 = $this->models->ContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					//'language_id' => $this->getCurrentLanguageId(),
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
						//'language_id' => $this->getCurrentLanguageId(),
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
					'numOfResults' => count((array)$choices)
				),
				array(
					'label' => _('Dichotomous key choices'),
					'data' => $steps,
					'numOfResults' => count((array)$steps)
				)
			),
			'numOfResults' => count((array)$choices)+count((array)$steps)
		);

	}

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
			
			foreach((array)$books as $key => $val) {
			
				$matches = $this->getColumnMatches($search,$val,array('content','author_first'));
	
				if (!is_null($matches)) {
	
					$books[$key]['replace'] = array(
						'model' => $this->models->Literature->getClassName(),
						'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
						'matches' => $matches,
					);
					
				}
		
			}
	
			return array(
				'results' => array(
					array(
						'label' => _('Literary references'),
						'data' => $books,
						'numOfResults' => count((array)$books)
					)
				),
				'numOfResults' => count((array)$books)
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
			
				$matches = $this->getColumnMatches($search,$val,array('term','definition'));
	
				if (!is_null($matches)) {
	
					$gloss[$key]['replace'] = array(
						'model' => $this->models->Glossary->getClassName(),
						'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id']),
						'matches' => $matches,
					);
					
				}
		
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
						'columns' => 'term'
					)
				);
		
				$synonyms[$key]['synonym'] = $g[0]['term'];
	
				$matches = $this->getColumnMatches($search,$val,array('synonym'));
	
				if (!is_null($matches)) {
	
					$synonyms[$key]['replace'] = array(
						'model' => $this->models->GlossarySynonym->getClassName(),
						'id' => array('project_id' => $this->getCurrentProjectId(),'id' => $val['id'],'language_id' => $val['language_id']),
						'matches' => $matches,
					);
					
				}
	
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
						'numOfResults' => count((array)$gloss)
					),
					array(
						'label' => _('Glossary synonyms'),
						'data' => $synonyms,
						'numOfResults' => count((array)$synonyms)
					),
					array(
						'label' => _('Glossary media'),
						'data' => $media,
						'numOfResults' => count((array)$media)
					)
				),
				'numOfResults' => count((array)$gloss)+count((array)$synonyms)+count((array)$media)
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
				'columns' => 'id,subject,content'
			)

		);
q($content,1);
		return array(
			'results' => array(
				array(
					'label' => _('Other pages'),
					'data' => $content,
					'numOfResults' => count((array)$content)
				)
			),
			'numOfResults' => count((array)$content)
		);

	}

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
					'label' => _('geographical data'),
					'data' => (isset($geo) ? $geo : null),
					'numOfResults' => (isset($geo) ? count((array)$geo) : 0)
				),
			),
			'numOfResults' => isset($geo) ? count((array)$geo) : 0
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

	private function DECtoDMS($dec,$formatted=false)
	{

		// Converts decimal longitude / latitude to DMS
		// ( Degrees / minutes / seconds ) 
		
		// This is the piece of code which may appear to 
		// be inefficient, but to avoid issues with floating
		// point math we extract the integer part and the float
		// part by using a string function.
		$vars = explode(".",$dec);
		$deg = $vars[0];
		$tempma = "0.".$vars[1];
	
		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);
		
		if ($formatted)
			return $deg.'&deg;'.$min."'".$sec."''";
		else	
			return array("deg"=>$deg,"min"=>$min,"sec"=>$sec);

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

	private function getTaxonStates($matrix,$id)
	{
	
		$mts = $this->models->MatrixTaxonState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $matrix,
					'taxon_id' => $id,
				),
				'columns' => 'characteristic_id,state_id'
			)
		);


		foreach((array)$mts as $key => $val) {

			$d = $this->getCharacteristic($val['characteristic_id']);

			$mts[$key] = array(
				'characteristic_id' => $val['characteristic_id'],
				'characteristic' => $d['label'],
				'type' => $d['type'],
				'state' => $this->getCharacteristicState($val['state_id'])
			);

		}

		return $mts;

	}
	
	private function checkForStylesheet()
	{
	
		foreach((array)$this->cssToLoad as $val) {
		
			if (!file_exists($_SESSION['project']['urls']['project_css'].$val)) {
			
				if (dirname($_SESSION['project']['paths']['default_css'].$val) != dirname($_SESSION['project']['paths']['default_css'])) {

					@mkdir(
						str_replace(
							$_SESSION['project']['paths']['default_css'],
							$_SESSION['project']['paths']['project_css'],
							dirname($_SESSION['project']['paths']['default_css'].$val)
						)
					);

				}

				copy($_SESSION['project']['paths']['default_css'].$val,$_SESSION['project']['paths']['project_css'].$val);
			
			}

		}
		
		// this dir name should probably go somewhere more manageable.
		$d = $_SESSION['project']['paths']['default_css'].'colorbox/images/';
		$e = $_SESSION['project']['paths']['default_css'].'colorbox/images/internet_explorer/';
		
		if (!file_exists($d)) mkdir($d);
		if (!file_exists($e)) mkdir($e);

		if (!file_exists($_SESSION['project']['paths']['project_css'].'colorbox/images/'))
			mkdir($_SESSION['project']['paths']['project_css'].'colorbox/images/');

		if (!file_exists($_SESSION['project']['paths']['project_css'].'colorbox/images/internet_explorer/'))
			mkdir($_SESSION['project']['paths']['project_css'].'colorbox/images/internet_explorer/');

		$f = array(
				'border1.png',
				'border2.png',
				'loading.gif',
				'internet_explorer/borderBottomCenter.png',
				'internet_explorer/borderBottomLeft.png',
				'internet_explorer/borderBottomRight.png',
				'internet_explorer/borderMiddleLeft.png',
				'internet_explorer/borderMiddleRight.png',
				'internet_explorer/borderTopCenter.png',
				'internet_explorer/borderTopLeft.png',
				'internet_explorer/borderTopRight.png',
			);
			
		foreach((array)$f as $val) {
		
			if (file_exists($d.$val))
				copy($d.$val,$_SESSION['project']['paths']['project_css'].'colorbox/images/'.$val);

		}
	
	}

	private function makeAlphabetFromArray($names,$field,$letter=null)
	{

		$a = array();
		
		if (!is_null($letter)) $letter = strtolower($letter);
	
		foreach((array)$names as $key => $val) {
		
			$x = strtolower(substr($val[$field],0,1));

			$a[$x] = $x;

			if (!is_null($letter) && $x==$letter) {
			
				$n[$key] = $val;
			
			}

		}

		if (!is_null($letter) && empty($n)) $letter = null;

		sort($a);

		if (is_null($letter)) {

			$letter = $a[0];

			foreach((array)$names as $key => $val) {

				if (strtolower(substr($val[$field],0,1))==$letter) $n[$key] = $val;
	
			}
		
		}

		return array(
			'alpha' => $a,
			'names' => $n
		);
	
	}	

}
