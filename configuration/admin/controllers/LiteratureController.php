<?php

/*

	unset session dinges
	on
	  save
	  delete
	  update
	
	case-sensitivity in getReferences because strtolower() on urdu seems to mess up characters

*/


include_once ('Controller.php');

class LiteratureController extends Controller
{
    

    public $usedModels = array(
		'literature',
		'literature_taxon',
		'taxon'
    );
   
    public $controllerPublicName = 'Literary references';


	public $cssToLoad = array('literature.css');

	public $jsToLoad =
		array(
			'all' => array('literature.js')
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
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));
		
		unset($_SESSION['system']['literature']['activeLetter']);

		unset($_SESSION['system']['search']);

        $this->printPage();
    
    }

    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        

        if ($this->requestData['action'] == 'get_authors') {

            $this->getAuthors();

        }
		
        $this->printPage();
    
    }

    public function editAction()
    {

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();

		if ($this->rHasVal('add','hoc') && !isset($_SESSION['system']['literature']['newRef'])) {
		// referred from the taxon content editing page

			$_SESSION['system']['literature']['newRef'] = '<new>';

		}


		if ($this->rHasId()) $ref = $this->getReference();

		if (isset($ref)) {

    	    $this->setPageName(
				sprintf(
					_('Editing literary reference "%s (%s)"'),
					$ref['author_first'].
						($ref['multiple_authors']==1 ? ' '._('et al.') : ($ref['author_second'] ? ' &amp; '.$ref['author_second'] : '')),
					$ref['year']
				)
			);
		
		} else {

    	    $this->setPageName(_('New literary reference'));
			
			if(isset($_SESSION['system']['activeTaxon'])) {

				$ref['taxa'][] = $_SESSION['system']['activeTaxon'];

			}

		}

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$_SESSION['system']['literature']['activeLetter'] = strtolower(substr($ref['author_first'],0,1));
			
			$this->deleteReference($this->requestData['id']);

			$this->redirect('browse.php');

		} else
		if ($this->rHasVal('author_first') && $this->rHasVal('year') && $this->rHasVal('text') && !$this->isFormResubmit()) {

			$data = $this->requestData;

			$data['project_id'] = $this->getCurrentProjectId();

			$data['id'] =  $this->rHasId() ? $this->requestData['id'] : 'null';

			$data['multiple_authors'] = $data['auths']=='n' ? 1 : 0;

			$data['year'] = $data['year'].'-00-00';

            $data['text'] = strip_tags($data['text'],$this->controllerSettings['allowedTags']);

			if ($data['id']=='null' && $this->getReferences($data)) {

				$this->addError(_('Reference already exists.'));

				$ref = $this->requestData;

			} else
			if ($this->models->Literature->save($data)) {

				if ($this->rHasVal('selectedTaxa')) {

					$id = $this->rHasId() ? $this->requestData['id'] : $this->models->Literature->getNewId();

					$this->models->LiteratureTaxon->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'literature_id' => $id
						)
					);

					foreach((array)$this->requestData['selectedTaxa'] as $key => $val) {

						$this->models->LiteratureTaxon->save(
							array(
								'id' => 'null',
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
						' ('.$this->requestData['year'].')'.
						'</span>';

					$this->redirect('../species/taxon.php?id='.$_SESSION['system']['activeTaxon']['taxon_id']);

				} else
				if(isset($_SESSION['system']['activeTaxon'])) {

					$this->redirect('../species/literature.php?id='.$_SESSION['system']['activeTaxon']['taxon_id']);

				} else {

					$_SESSION['system']['literature']['activeLetter'] = strtolower(substr($this->requestData['author_first'],0,1));

					$this->redirect('browse.php');

				}

			} else {

				$this->addError(_('Could not save reference.'));

			}

		}

		$this->getTaxonTree();

		if (isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

        if (isset($ref)) $this->smarty->assign('ref', $ref);

        $this->printPage();

    }

    public function browseAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Browsing literary references'));
		
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

		$this->setPageName(_('Search for literary references'));
		
		if ($this->rHasVal('search')) {

			if (isset($_SESSION['system']['search']) && $_SESSION['system']['search']['search'] == $this->requestData['search']) {
			
				$refs = $_SESSION['system']['search']['results'];
			
			} else {

				$refs = $this->models->Literature->_get(
					array('id' =>
						'select *, year(`year`) as `year`, concat(author_first,author_second) as author_both
						from %table%
						where
							author_first like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							author_second like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							text like "%'.mysql_real_escape_string($this->requestData['search']).'%"
						order by author_first,author_second,year'
					)
				);

				$_SESSION['system']['search']['search'] = $this->requestData['search'];
	
				$_SESSION['system']['search']['results'] = $refs;

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
				'columns' => '*, year(`year`) as `year`'
			)
		);
		
		if ($l) {
		
			$ref = $l[0];
			
			$lt = $this->models->LiteratureTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'literature_id' => $thisId	
					)
				)
			);
			
			foreach((array)$lt as $key => $val) {

				if (isset($val['taxon_id'])) {

					$t = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $val['taxon_id']
							)
						)
					);

					$lt[$key]['taxon'] = $t[0]['taxon'];

				}

			}
			
			$ref['taxa'] = $lt;

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
		if (!empty($search['text'])) $d['text'] = $search['text'];
		if (!empty($search['multiple_authors'])) $d['multiple_authors'] = $search['multiple_authors'];

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'author_first',
					'columns' => '*, year(`year`) as `year`, concat(author_first,author_second) as author_both',
					'ignoreCase' => false
				)
			);

		return $l;

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



	
}




























