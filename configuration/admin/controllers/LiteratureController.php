<?php

include_once ('Controller.php');

class LiteratureController extends Controller
{
    

    public $usedModels = array(
		'literature',
		'literature_taxon',
		'taxon'
    );
   
    public $controllerPublicName = 'Literary references';

	public $cssToLoad = array('literature.css','dialog/jquery.modaldialog.css','lookup.css');

	public $jsToLoad =
		array(
			'all' => array('literature.js','int-link.js','dialog/jquery.modaldialog.js','lookup.js')
		);

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

    public function indexAction()
    {
    
		$this->clearTempValues();

		//$d = $this->getFirstReference();
		
		//$this->redirect('edit.php?id='.$d['id']);
		$this->redirect('edit.php');

		/*

        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));
		
		$this->clearTempValues();

        $this->printPage();

		*/    

    }

    public function editAction()
    {

        $this->checkAuthorisation();

		if ($this->rHasVal('add','hoc') && !isset($_SESSION['system']['literature']['newRef'])) {
		// referred from the taxon content editing page

			$_SESSION['system']['literature']['newRef'] = '<new>';

		}


		if ($this->rHasVal('letter')) {

			$ref = $this->getFirstReference($this->requestData['letter']);
		
		} else
		if ($this->rHasId()) {

			$ref = $this->getReference();

		} else
		if (!$this->rHasVal('action','new')) {

			$ref = $this->getFirstReference();
			
		}

		$alpha = $this->getActualAlphabet();

		$navList = $this->getReferencesNavList();

		if (isset($ref)) {

    	    $this->setPageName(
				sprintf(
					_('Editing literature "%s (%s)"'),
					$ref['author_first'].
						($ref['multiple_authors']==1 ? ' '._('et al.') : ($ref['author_second'] ? ' &amp; '.$ref['author_second'] : '')),
					$ref['year'].$ref['suffix']
				)
			);
		
		} else {

    	    $this->setPageName(_('New reference'));
			
			if(isset($_SESSION['system']['activeTaxon'])) {

				$ref['taxa'][] = $_SESSION['system']['activeTaxon'];

			}

		}

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$_SESSION['system']['literature']['activeLetter'] = strtolower(substr($ref['author_first'],0,1));
			
			$this->deleteReference($this->requestData['id']);

			$navList = $this->getReferencesNavList(true);

			$this->redirect('index.php');

			//$this->redirect('browse.php');

		} else
		if ($this->rHasVal('author_first') && $this->rHasVal('year') && $this->rHasVal('text') && !$this->isFormResubmit()) {

			$data = $this->requestData;

			$data['project_id'] = $this->getCurrentProjectId();

			$data['id'] =  $this->rHasId() ? $this->requestData['id'] : null;

			$data['multiple_authors'] = $data['auths']=='n' ? 1 : 0;

			$data['year'] = $data['year'].'-00-00';
			
            $data['text'] = $this->cleanUpRichContent($data['text']);

			$test = array(
				'author_first' => $data['author_first'],
				'author_second' => $data['author_second'],
				'multiple_authors' => $data['multiple_authors'],
				'year' => $data['year'],
				'suffix' => $data['suffix']
			);

			if ($this->rHasId())
				$test['id !='] = $data['id'];
			else
				$test['id'] = 'null';

			if ($this->getReferences($test)) {

				$this->addError(_('A reference with the same author(s), year and suffix already exists.'));

				$ref = $this->requestData;

				if ($this->rHasVal('selectedTaxa')) $ref['taxa'] = $this->requestData['selectedTaxa'];

			} else
			if ($this->models->Literature->save($data)) {

				$id = $this->rHasId() ? $this->requestData['id'] : $this->models->Literature->getNewId();

				$this->models->LiteratureTaxon->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'literature_id' => $id
					)
				);

				if ($this->rHasVal('selectedTaxa')) {

					foreach((array)$this->requestData['selectedTaxa'] as $key => $val) {

						$this->models->LiteratureTaxon->save(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'literature_id' => $id,
								'taxon_id' => $val
							)
						);

					}

				}
				
				unset($_SESSION['system']['literature']['alpha']);

				if (isset($_SESSION['system']['literature']['newRef']) && $_SESSION['system']['literature']['newRef'] == '<new>') {

					$_SESSION['system']['literature']['newRef'] =
						'<span class="taxonContentLiteratureLink" onclick="taxonContentOpenLiteratureLink('.$id.');">'.
						$data['author_first'].
						(isset($data['author_second']) ?
							' &amp; '.$data['author_second'] :
							($data['author_second']=='1' ? _(' et al.') : '' )
						).
						' ('.$this->requestData['year'].$this->requestData['suffix'].')'.
						'</span>';

					$this->redirect('../species/taxon.php?id='.$_SESSION['system']['activeTaxon']['taxon_id']);

				} else
				if(isset($_SESSION['system']['activeTaxon'])) {

					$this->redirect('../species/literature.php?id='.$_SESSION['system']['activeTaxon']['taxon_id']);

				} else
				if ($this->rHasVal('action','preview')) {

					$this->redirect('preview.php?id='.$id);

				} else {

					$_SESSION['system']['literature']['activeLetter'] = strtolower(substr($this->requestData['author_first'],0,1));

					$navList = $this->getReferencesNavList(true);

					$this->redirect('edit.php?id='.$id);

					//$this->redirect('browse.php');

				}

			} else {

				$this->addError(_('Could not save reference.'));

			}

		}

		$this->getTaxonTree();

		if (isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

		if (isset($navList)) $this->smarty->assign('navList', $navList);

		if (isset($ref))  {
		
			$this->smarty->assign('navCurrentId',$ref['id']);

	        $this->smarty->assign('ref', $ref);

		}

		$this->smarty->assign('includeHtmlEditor', true);

		$this->smarty->assign('alpha', $alpha);

        $this->printPage();

    }

    public function previewAction()
    {

		$ref = $this->getReference($this->requestData['id']);
		$navList = $this->getReferencesNavList();

		$this->smarty->assign('backUrl','edit.php?id='.$this->requestData['id']);
		$this->smarty->assign('nextUrl','edit.php?id='.$navList[$this->requestData['id']]['next']['id']);

		if (isset($ref)) $this->smarty->assign('ref', $ref);

		$this->printPreviewPage(
			'../../../../app/templates/templates/literature/_reference',
			'literature.css'
		);

    }

    public function browseAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Browsing literature'));
		
		$alpha = $this->getActualAlphabet();

		if (!$this->rHasVal('letter') && isset($_SESSION['system']['literature']['activeLetter']))
			$this->requestData['letter'] = $_SESSION['system']['literature']['activeLetter'];

		if (!$this->rHasVal('letter'))
			$this->requestData['letter'] = $alpha[0];


		if ($this->rHasVal('letter')) {

			$refs = $this->getReferences(array('author_first like' => $this->requestData['letter'].'%'),'author_first,author_second,year');

		}

        // user requested a sort of the table
        if ($this->rHasVal('key')) {

            $sortBy = array(
                'key' => $this->requestData['key'], 
                'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
                'case' => 'i'
            );
        
			$this->customSortArray($refs, $sortBy);

        } else {

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

    public function searchAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Search for literature'));
		
		if ($this->rHasVal('search')) {

			if (
				isset($_SESSION['system']['literature']['search']) && 
				$_SESSION['system']['literature']['search']['search'] == $this->requestData['search']) 
			{
			
				$refs = $_SESSION['system']['literature']['search']['results'];
			
			} else {

				$refs = $this->models->Literature->_get(
					array('id' =>
						'select *, year(`year`) as `year`, concat(author_first,author_second) as author_both
						from %table%
						where
							(author_first like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							author_second like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							text like "%'.mysql_real_escape_string($this->requestData['search']).'%")
							and project_id = '.$this->getCurrentProjectId().'
						order by author_first,author_second,year'
					)
				);

				$_SESSION['system']['literature']['search']['search'] = $this->requestData['search'];
	
				$_SESSION['system']['literature']['search']['results'] = $refs;

			}

		}

        // user requested a sort of the table
        if ($this->rHasVal('key')) {

            $sortBy = array(
                'key' => $this->requestData['key'], 
                'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
                'case' => 'i'
            );

			$this->customSortArray($refs, $sortBy);

        } else {

            $sortBy = array(
                'key' => 'author_first', 
                'dir' => 'asc', 
                'case' => 'i'
            );
	
		}
        
		$this->smarty->assign('sortBy', $sortBy);

		if (isset($refs)) $this->smarty->assign('refs',$refs);

		if ($this->rHasVal('search')) $this->smarty->assign('search',$this->requestData['search']);

        $this->printPage();

	}

    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        

        if ($this->requestData['action'] == 'get_authors') {

            $this->getAuthors();

        } else
        if ($this->requestData['action'] == 'get_references') {

            $this->getReferenceList();

        } else
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }
		
        $this->printPage();
    
    }

	private function getReference($id=null)
	{

		if (!isset($id) && !$this->rHasId()) return false;

		$thisId = isset($id) ? $id : $this->requestData['id'];

		$l = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $thisId
				),
				'columns' =>
					'*,
					year(`year`) as `year`,
					concat(
							author_first,
							(
								if(multiple_authors=1,
									\' et al.\',
									if(author_second!=\'\',concat(\' & \',author_second),\'\')
								)
							)
						) as author_full'
			)
		);
		
		if ($l) {
		
			$ref = $l[0];
			
			$lt = $this->models->LiteratureTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'literature_id' => $thisId	
					),
					'columns' => 'taxon_id'
				)
			);
			
			foreach((array)$lt as $key => $val) {

				if (isset($val['taxon_id'])) {

					$ref['taxa'][] = $val['taxon_id'];

				}

			}
			
			return $ref;

		} else {
		
			return false;

		}

	}

	private function getAuthors($str=null)
	{
	
		if (!isset($str) && !$this->rHasVal('str')) return false;

		$thisStr = isset($str) ? $str : $this->requestData['str'];
		
		if (empty($thisStr)) return;

		$l1 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'author_first like' => $thisStr.'%',
					'fieldAsIndex' => 'author_first'
				),
				'columns' => 'distinct author_first as author',
				'fieldAsIndex' => 'author'
			)
		);

		$l2 = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'author_second like' => $thisStr.'%',
					'fieldAsIndex' => 'author_second'
				),
				'columns' => 'distinct author_second as author',
				'fieldAsIndex' => 'author'
			)
		);

		$auths = array_merge((array)$l1,(array)$l2);

        $this->customSortArray($auths, array(
				'key' => 'author', 
				'dir' => 'asc', 
				'case' => 'i'
			)
		);

		$this->smarty->assign('returnText',json_encode($auths));

	}

	private function getReferenceList($str=null)
	{
	
		if (!isset($str) && !$this->rHasVal('str')) return false;

		$thisStr = isset($str) ? $str : $this->requestData['str'];
		
		if (empty($thisStr)) return;

		$l = $this->models->Literature->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'author_first like' => $thisStr.'%',
					'fieldAsIndex' => 'author_first'
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
								) as author_full'
			)
		);

		$this->smarty->assign('returnText',json_encode($l));

	}

	private function deleteReference($id)
	{

		if (empty($id)) return false;

		$this->models->LiteratureTaxon->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'literature_id' => $id
			)
		);

		$this->models->Literature->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			)
		);

	}

	private function getReferences($search,$order=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (!empty($search['author_first'])) $d['author_first'] = $search['author_first'];
		if (!empty($search['author_first like'])) $d['author_first like'] = $search['author_first like'];
		if (!empty($search['author_second'])) $d['author_second'] =  $search['author_second'];
		if (!empty($search['year'])) $d['year'] = $search['year'];
		if (!empty($search['suffix'])) $d['suffix'] = $search['suffix'];
		if (!empty($search['text'])) $d['text'] = $search['text'];
		if (!empty($search['id'])) $d['id'] = $search['id'];
		if (!empty($search['id !='])) $d['id !='] = $search['id !='];

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'author_first',
					'columns' => '*, year(`year`) as `year`, concat(author_first,author_second) as author_both,
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

	private function getFirstReference($letter=null)
	{
	
		$d = array('project_id' => $this->getCurrentProjectId());
		
		if (isset($letter)) $d['author_first like'] = $letter.'%';

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => 'author_first',
					'ignoreCase' => true,
					'limit' => 1,
					'columns' => '*, year(`year`) as `year`'
				)
			);

		return $l[0];

	}

	private function getActualAlphabet()
	{

		if (isset($_SESSION['system']['literature']['alpha'])) return $_SESSION['system']['literature']['alpha'];

		$l = $this->models->Literature->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'distinct lower(substr(author_first,1,1)) as letter',
				'order' => 'author_first'
			)
		);
		
		$alpha = null;
		
		foreach((array)$l as $key => $val) {

			$alpha[] = $val['letter'];

		}
		
		$_SESSION['system']['literature']['alpha'] = $alpha;

		return $alpha;
	
	}

	private function getReferencesNavList($forceLookup=false) {
	
		if (empty($_SESSION['literature']['navList']) || $forceLookup) {
		
			$d = $this->getReferences(null);
			
			foreach((array)$d as $key => $val) {

				$res[$val['id']] = array(
					'prev' => array(
						'id' => isset($d[$key-1]['id']) ? $d[$key-1]['id'] : null, 
						'title' =>
							(isset($d[$key-1]['author_full']) ? $d[$key-1]['author_full'] : null).
							(isset($d[$key-1]['year']) ? ' ('.$d[$key-1]['year'].(isset($d[$key-1]['suffix']) ? $d[$key-1]['suffix'] : null).')' : null)
					),
					'next' => array(
						'id' => isset($d[$key+1]['id']) ? $d[$key+1]['id'] : null,
						'title' =>
							(isset($d[$key+1]['author_full']) ? $d[$key+1]['author_full'] : null).
							(isset($d[$key+1]['year']) ? ' ('.$d[$key+1]['year'].(isset($d[$key+1]['suffix']) ? $d[$key+1]['suffix'] : null).')' : null)
					),
				);

			}
		
			$_SESSION['literature']['navList'] = $res;
		
		}
		
		return $_SESSION['literature']['navList'];

	}

	private function clearTempValues()
	{
	
		unset($_SESSION['system']['literature']['activeLetter']);
		unset($_SESSION['system']['literature']['search']);
		unset($_SESSION['system']['literature']['alpha']);

	}

	private function getLookupList($search)
	{

		if (empty($search)) return;

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
				where
					(author_first like "%'.mysql_real_escape_string($search).'%" or
					author_second like "%'.mysql_real_escape_string($search).'%" or
					`year` like "%'.mysql_real_escape_string($search).'%")
					and project_id = '.$this->getCurrentProjectId().'
				order by _a1,_a2,`year`'
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				$this->controllerBaseName,
				'../literature/edit.php?id=%s'
			)
		);
		
	}

}
