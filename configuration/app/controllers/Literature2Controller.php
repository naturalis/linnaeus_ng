<?php

include_once ('Controller.php');

class Literature2Controller extends Controller
{

    public $usedModels = array(
		'literature2'
    );
   
    public $controllerPublicName = 'Literary references';

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

	private function getReference($id)
	{
		
		$data=$this->models->Literature2->freeQuery(
			"select
				_a.label,
				_a.date,
				_a.author,
				_a.actor_id,
				_b.name as actor_name,
				_a.publication_type,
				_a.order_number,
				_a.citation,
				_a.source,
				_a.publishedin,
				_a.publishedin_actor_id,
				_c.name as publishedin_actor_name,
				_a.pages,
				_a.volume,
				_a.periodical,
				_a.periodical_actor_id,
				_d.name as periodical_actor_name,
				_a.language_id,
				_e.label as language_name
			from %PRE%literature2 _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id 
				and _a.project_id=_b.project_id

			left join %PRE%actors _c
				on _a.publishedin_actor_id = _c.id 
				and _a.project_id=_c.project_id
				
			left join %PRE%actors _d
				on _a.periodical_actor_id = _d.id 
				and _a.project_id=_d.project_id
				
			left join %PRE%labels_languages _e
				on _a.language_id = _e.language_id 
				and _a.project_id=_e.project_id
				and _e.label_language_id = ".$this->getCurrentLanguageId()."

			where _a.project_id = ".$this->getCurrentProjectId()."
			and _a.id =".$id
		);	

		return $data[0];

	}



    public function referenceAction()
    {

		if (!$this->rHasId()) $this->redirect('index.php');

		$ref = $this->getReference($this->requestData['id']);

		if (!$ref) $this->redirect('index.php');
		
		$this->setPageName($ref['label'].', '.$ref['source']);

		$this->smarty->assign('ref', $ref);

        $this->printPage();

    }













    public function indexAction()
    {
 
 		die('index');
 
 		if (!$this->rHasVal('id')) {

			$d = $this->getFirstReference($this->rHasVal('letter') ? $this->requestData['letter'] : null);

			$id = (isset($d['id']) ? $d['id'] : null);
			
		} else {
			
			$id = $this->requestData['id'];
				
		}
		
 		//unset($_SESSION['app'][$this->spid()]['search']['hasSearchResults']);
		
		$this->setStoreHistory(false);
		
		if (isset($id)) $this->redirect('reference.php?id='.$id);

 		/*
 		$alpha = $this->getLiteratureAlphabet();

		if (!$this->rHasVal('letter')) $this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : null;

		if ($this->rHasVal('letter')) {
		
			$this->requestData['letter'] = strtolower($this->requestData['letter']);

			$refs = $this->getReferences(array('author_first like' => $this->requestData['letter'].'%'));

			$this->setPageName(sprintf($this->translate('Literature Index: %s'),strtoupper($this->requestData['letter'])));

		}

		//unset($_SESSION['app'][$this->spid()]['search']['hasSearchResults']);

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($refs)) $this->smarty->assign('refs',$refs);

		*/
   
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

