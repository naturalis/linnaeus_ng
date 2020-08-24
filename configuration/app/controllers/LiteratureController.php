<?php 

include_once ('Controller.php');

class LiteratureController extends Controller
{
    public $usedModels = array(
		'literature',
		'literature_taxa',
		'synonyms',
		'taxa'
    );

    public $controllerPublicName = 'Literary references';
    public $modelNameOverride = 'Literature2Model';
    
	public $cssToLoad = array(
		'literature.css'
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

    public function indexAction()
    {
 		if (!$this->rHasVal('id'))
		{
			$d = $this->getFirstReference($this->rHasVal('letter') ? $this->requestData['letter'] : null);
			$id = (isset($d['id']) ? $d['id'] : null);
		}
		else
		{
			$id = $this->requestData['id'];
		}

		$this->setStoreHistory(false);

		if (isset($id)) $this->redirect('reference.php?id='.$id);

        $this->printPage();
	}

    public function referenceAction()
    {
		if ($this->rHasId())
		{
			$ref = $this->getReference($this->requestData['id']);

			$ref['text'] = $this->matchHotwords($ref['text']);

			$letter = strtolower(substr($ref['author_first'],0,1));

			$this->setPageName(sprintf($this->translate('Literature: "%s"'),$ref['author_full'].' ('.$ref['year'].')'));

		}
		else
		{
			$this->redirect('index.php');
		}

		$alpha = $this->getLiteratureAlphabet();

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);
		if (isset($letter)) $this->smarty->assign('letter', $letter);
		if (isset($ref)) $this->smarty->assign('ref', $ref);
		if (isset($ref)) $this->smarty->assign('adjacentItems', $this->getAdjacentItems($ref['id']));

        $this->printPage();
    }


    public function contentsAction()
    {
		$alpha = $this->getLiteratureAlphabet();

		if (!$this->rHasVal('letter') && isset($_SESSION['admin']['system']['literature']['activeLetter']))
			$this->requestData['letter'] = $_SESSION['admin']['system']['literature']['activeLetter'];

		if (!$this->rHasVal('letter'))
			$this->requestData['letter'] = $alpha[0];

		if ($this->rHasVal('letter'))
		{
			$refs = $this->getReferences(array('author_first like' => $this->requestData['letter'].'%'),'author_first,author_second,year');
		}

        // user requested a sort of the table
        if ($this->rHasVal('key'))
		{
            $sortBy = array(
                'key' => $this->requestData['key'],
                'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'),
                'case' => 'i'
            );

			$this->customSortArray($refs, $sortBy);
        }
		else
		{
            $sortBy = array(
                'key' => 'author_first',
                'dir' => 'asc',
                'case' => 'i'
            );
		}

		$this->smarty->assign('sortBy', $sortBy);
		$this->smarty->assign('alpha', $alpha);
		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);
		if (isset($refs)) $this->smarty->assign('refs',$refs);

        $this->printPage();
	}

    public function ajaxInterfaceAction()
    {
        if (!isset($this->requestData['action'])) return;

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search']))
		{
            $this->getLookupList($this->requestData);
        }

		$this->allowEditPageOverlay = false;

        $this->printPage();
    }

	private function getLiteratureAlphabet($forceLookup=false)
	{
		$l=$this->models->Literature->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'distinct lower(substr(author_first,1,1)) as letter',
				'order' => 'author_first'
			)
		);

		$alpha=array();

		foreach((array)$l as $key => $val)
		{
			$alpha[] = $val['letter'];
		}

		return $alpha;
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
					'columns' => '*,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full,
							concat(
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
							) as year_full',
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
				'columns' => '*, concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									)
								) as author_full,
								concat(
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
								) as year_full',
			)
		);

		if ($l)
		{

			$ref = $l[0];

			$lt = $this->models->LiteratureTaxa->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'literature_id' => $id
					),
					'order' => 'sort_order'
				)
			);

			$tc = 'id,taxon,rank_id,list_level,is_hybrid';

			foreach((array)$lt as $key => $val) {

				if (isset($val['taxon_id'])) {

					$t = $this->models->Taxa->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $val['taxon_id']
							),
							'columns' => $tc
						)
					);

					$lt[$key]['taxon'] = $t[0];
					$lt[$key]['taxon']['label'] = $this->formatTaxon($lt[$key]['taxon']);

				}

			}

			$ref['taxa'] = $lt;

			$s = $this->models->Synonyms->_get(
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
		}
		else
		{
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

		if (!$l)
		{
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
				'columns' => 'id,
					 			concat(
									author_first,
									(
										if(multiple_authors=1,
											\' et al.\',
											if(author_second!=\'\',concat(\' & \',author_second),\'\')
										)
									),\', \',`year`,ifnull(suffix,\'\')
								) as label'
			)
		);

		foreach((array)$l as $key => $val)
		{
			if ($val['id']==$id)
			{
				return array(
					'prev' => isset($l[$key-1]) ? $l[$key-1] : null,
					'next' => isset($l[$key+1]) ? $l[$key+1] : null
				);
			}
		}

		return null;
	}

	public function getLookupList($p)
	{
		$search = isset($p['search']) ? $p['search'] : null;
		$matchStartOnly = isset($p['match_start']) ? $p['match_start']=='1' : false;
		$getAll = isset($p['get_all']) ? $p['get_all']=='1' : false;

		$l = $this->reallyGetLookupList(array(
            'project_id' => $this->getCurrentProjectId(),
    		'search' => $search,
    		'match_start' => $matchStartOnly,
    		'get_all' => $getAll
		));

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$l,
				'module'=>$this->controllerBaseName,
				'url'=>'../literature/reference.php?id=%s'
			))
		);

		return $l;

	}



    // Used in original LiteratureController
	public function reallyGetLookupList ($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$search = isset($params['search']) ? $params['search'] : null;
		$matchStartOnly = isset($params['match_start']) ? $params['match_start']=='1' : false;
		$getAll = isset($params['get_all']) ? $params['get_all']=='1' : false;

		if (is_null($project_id) || is_null($search))
		{
			return;
		}

		$match = $matchStartOnly ?
            mysqli_real_escape_string($this->databaseConnection, $search).'%' :
		    '%'.mysqli_real_escape_string($this->databaseConnection, $search).'%';

		$query = '
    		select
            id,
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
            lower(author_first) as _a1,
            lower(author_second) as _a2,
            `year`
            from %PRE%literature
            where project_id = '.
            	$project_id .
            	(!$getAll ? ' and (author_first like "'.$match.'" or author_second like "'.$match.'" or `year` like "'.$match.'")' : null ).'
            order by _a1,_a2,`year`';

		return $this->freeQuery($query);
    }

}

