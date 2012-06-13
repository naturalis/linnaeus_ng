<?php

include_once ('Controller.php');

class LiteratureController extends Controller
{
    

    public $usedModels = array(
		'literature',
		'literature_taxon',
		'synonym',
		'taxon'
    );
   
    public $controllerPublicName = 'Literary references';

	public $cssToLoad = array(
		'basics.css',
		'literature.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
//				'tablesorter/jquery-latest.js',
//				'tablesorter/jquery.tablesorter.js',
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

		$this->setCssFiles();

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

    public function indexAction()
    {
 
 		unset($_SESSION['app']['user']['search']['hasSearchResults']);

		$d = $this->getFirstReference($this->rHasVal('letter') ? $this->requestData['letter'] : null);
		
		if (isset($d['id'])) $this->redirect('reference.php?id='.(isset($d['id']) ? $d['id'] : null));

 		/*
 		$alpha = $this->getLiteratureAlphabet();

		if (!$this->rHasVal('letter')) $this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : null;

		if ($this->rHasVal('letter')) {
		
			$this->requestData['letter'] = strtolower($this->requestData['letter']);

			$refs = $this->getReferences(array('author_first like' => $this->requestData['letter'].'%'));

			$this->setPageName(sprintf(_('Literature Index: %s'),strtoupper($this->requestData['letter'])));

		}

		unset($_SESSION['app']['user']['search']['hasSearchResults']);

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($refs)) $this->smarty->assign('refs',$refs);

		*/
   
        $this->printPage();

    }

    public function referenceAction()
    {

		if ($this->rHasId()) {

			$ref = $this->getReference($this->requestData['id']);
			
			$ref['text'] = $this->matchGlossaryTerms($ref['text']);
			$ref['text'] = $this->matchHotwords($ref['text']);

			$letter = strtolower(substr($ref['author_first'],0,1));

			$this->setPageName(sprintf(_('Literature: "%s"'),$ref['author_full'].' ('.$ref['year'].')'));

		} else {
		
			$this->redirect('index.php');
		
		}


		$alpha = $this->getLiteratureAlphabet();

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if (isset($letter)) $this->smarty->assign('letter', $letter);

		if (isset($ref)) $this->smarty->assign('ref', $ref);

		if (isset($ref)) $this->smarty->assign('adjacentItems', $this->getAdjacentItems($ref['id']));

        $this->printPage();

    }

    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);

        }

		$this->allowEditPageOverlay = false;
				
        $this->printPage();
    
    }
	
	private function getLiteratureAlphabet($forceLookup=false)
	{

		if (!isset($_SESSION['app']['user']['literature']['alpha']) or $forceLookup) {

			unset($_SESSION['app']['user']['literature']['alpha']);

			$l = $this->models->Literature->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'columns' => 'distinct lower(substr(author_first,1,1)) as letter',
					'order' => 'author_first'
				)
			);

			$_SESSION['app']['user']['literature']['alpha'] = null;
		
			foreach((array)$l as $key => $val) {

				$_SESSION['app']['user']['literature']['alpha'][] = $val['letter'];

			}

		}

		return $_SESSION['app']['user']['literature']['alpha'];
	
	}

	private function getReferences($search,$order=null)
	{

		if (!empty($search['id'])) $d['id'] = $search['id'];
		if (!empty($search['author_first'])) $d['author_first'] = $search['author_first'];
		if (!empty($search['author_first like'])) $d['author_first like'] = $search['author_first like'];
		if (!empty($search['author_second'])) $d['author_second'] =  $search['author_second'];
		if (!empty($search['year'])) $d['year'] = $search['year'];
		if (!empty($search['text'])) $d['text'] = $search['text'];
		if (!empty($search['multiple_authors'])) $d['multiple_authors'] = $search['multiple_authors'];

		$d['project_id'] = $this->getCurrentProjectId();

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'author_first',
					'columns' => '*, year(`year`) as `year`,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full',
					'ignoreCase' => false
				)
			);


		return $l;

	}

	private function getReference($id)
	{

		if (!isset($id)) return;

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
								) as author_full',
			)
		);
		
		if ($l) {
		
			$ref = $l[0];
			
			$lt = $this->models->LiteratureTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'literature_id' => $id	
					),
					'order' => 'sort_order'
				)
			);

			$tc = 
				'id,taxon,rank_id,list_level'.
				(isset($_SESSION['app']['project']['includes_hybrids']) && $_SESSION['app']['project']['includes_hybrids']==1 ? ',is_hybrid' : '');

			foreach((array)$lt as $key => $val) {

				if (isset($val['taxon_id'])) {

					$t = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $val['taxon_id']
							),
							'columns' => $tc
						)
					);

					$lt[$key]['taxon'] = $t[0];
					$lt[$key]['taxon']['label'] = $this->formatSpeciesEtcNames($lt[$key]['taxon']['taxon']);

				}

			}
			
			$ref['taxa'] = $lt;

			$s = $this->models->Synonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'lit_ref_id' => $id	
					),
					'columns' => 'synonym,taxon_id'
				)
			);

			$ref['synonyms'] = $s;

			return $ref;

		} else {
		
			return;

		}

	}

	private function getFirstReference($letter=null)
	{

		$d = array('project_id' => $this->getCurrentProjectId());
			
		if (isset($letter)) $d['author_first like'] = $letter.'%';

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => 'author_first',
					'columns' => 'id',
					'ignoreCase' => true,
					'limit' => 1
				)
			);
			
		if (!$l) {
		
			$l = $this->models->Literature->_get(
					array(
						'id' => array('project_id' => $this->getCurrentProjectId()),
						'order' => 'author_first',
						'columns' => 'id',
						'limit' => 1
					)
				);
		
		}

		return $l[0];

	}

	private function getAdjacentItems($id)
	{

		$l = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'order' => 'author_first',
				'columns' => 'id, year(`year`) as `year`,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									),\', \',year(`year`),ifnull(suffix,\'\')
								) as label'
			)
		);

		foreach((array)$l as $key => $val) {

			if ($val['id']==$id) {
			
				return array(
					'prev' => isset($l[$key-1]) ? $l[$key-1] : null,
					'next' => isset($l[$key+1]) ? $l[$key+1] : null
				);

			}

		}

		return null;

	}

	private function getLookupList($p)
	{

		$search = isset($p['search']) ? $p['search'] : null;
		$matchStartOnly = isset($p['match_start']) ? $p['match_start']=='1' : false;
		$getAll = isset($p['get_all']) ? $p['get_all']=='1' : false;

//		$search = str_replace(array('/','\\'),'',$search);

		if (empty($search) && !$getAll) return;

		if ($matchStartOnly)
			$match = mysql_real_escape_string($search).'%';
		else
			$match = '%'.mysql_real_escape_string($search).'%';

		$l = $this->models->Literature->_get(
			array('id' =>
				'select
					id, 
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\' (\',
						year(`year`),
						(
							if(isnull(suffix)!=1,
									suffix,
									\'\'
								)
						),
						\')\'
					) as label,
					lower(author_first) as _a1,
					lower(author_second) as _a2,
					`year`					
				from %table%
				where project_id = '.
					$this->getCurrentProjectId().
					(!$getAll ? ' and (author_first like "'.$match.'" or author_second like "'.$match.'" or `year` like "'.$match.'")' : null ).'
				order by _a1,_a2,`year`'
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				$this->controllerBaseName,
				'../literature/reference.php?id=%s'
			)
		);
		
	}

}

