<?php

include_once ('Controller.php');

class LiteratureController extends Controller
{

    public $usedModels = array(
		'literature',
		'literature_taxon',
		'taxon',
		'synonym'
    );
   
    public $controllerPublicName = 'Literary references';

    public $cacheFiles = array(
    	'search-contentsLiterature'
    );
    
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

		$this->setActiveTaxonId(null);
    
		$this->clearTempValues();

		$d = $this->getFirstReference();
		
		$this->redirect('edit.php?id='.$d['id']);

    }

    public function editAction()
    {

        $this->checkAuthorisation();

		if ($this->rHasVal('add','hoc') && !isset($_SESSION['admin']['system']['literature']['newRef'])) {
		// referred from the taxon content editing page

			$_SESSION['admin']['system']['literature']['newRef'] = '<new>';

		}


		if ($this->rHasVal('letter')) {

			$ref = $this->getFirstReference($this->requestData['letter']);
		
		} else
		if ($this->rHasId()) {

			$ref = $this->getReference($this->requestData['id']);

		} else
		if (!$this->rHasVal('action','new')) {

			$ref = $this->getFirstReference();
			
		}

		$alpha = $this->getActualAlphabet();

		$navList = $this->getReferencesNavList();

		if (isset($ref)) {

    	    $this->setPageName(
				sprintf(
					$this->translate('Editing literature "%s (%s)"'),
					$ref['author_first'].
						($ref['multiple_authors']==1 ? ' '.$this->translate('et al.') : ($ref['author_second'] ? ' &amp; '.$ref['author_second'] : '')),
					$ref['year'].$ref['suffix']
				)
			);
		
		} else {

    	    $this->setPageName($this->translate('New reference'));
			
			if(!is_null($this->getActiveTaxonId())) {
				
				$taxon=$this->getTaxonById($this->getActiveTaxonId());
				
				$ref['taxa'][] = array(
						'taxon_id' => $taxon['id'],
						'taxon' => $taxon['taxon']
				);

			}

		}

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$this->clearCache($this->cacheFiles);
			
			$_SESSION['admin']['system']['literature']['activeLetter'] = strtolower(substr($ref['author_first'],0,1));

			$this->deleteReference($this->requestData['id']);

			$this->clearCache($this->cacheFiles);

			$navList = $this->getReferencesNavList(true);

			$this->redirect('index.php');

			//$this->redirect('browse.php');

		} else
		if ($this->rHasVal('author_first') && $this->rHasVal('year') && $this->rHasVal('text') && !$this->isFormResubmit()) {

			$data = $this->requestData;

			$data['project_id'] = $this->getCurrentProjectId();

			$data['id'] =  $this->rHasId() ? $this->requestData['id'] : null;

			$data['multiple_authors'] = $data['auths']=='n' ? 1 : 0;

			//$data['year'] = $data['year'].'-00-00';
			
            $data['text'] = $this->cleanUpRichContent($data['text']);

			$test = array(
				'author_first' => $data['author_first'],
				'author_second' => $data['author_second'],
				'multiple_authors' => $data['multiple_authors'],
				'year' => $data['year'],
				'suffix' => $data['suffix']
			);

			//$data['author_second'] = $test['author_second'] = empty($data['author_second']) || $data['multiple_authors']==0 ? 'null' : $data['author_second'];

			$data['year_separator'] = $test['year_separator'] = empty($data['year_separator']) || $data['use_year_range']=='0' ? 'null' : $data['year_separator'];
			$data['year_2'] = $test['year_2'] = empty($data['year_2']) || $data['use_year_range']=='0' ? 'null' : $data['year_2'];
			$data['suffix_2'] = $test['suffix_2'] = empty($data['suffix_2']) || $data['use_year_range']=='0' ? 'null' : $data['suffix_2'];

			if ($this->rHasId())
				$test['id !='] = $data['id'];
			else
				$test['id'] = 'null';


			if ($this->getReferences($test)) {

				$this->addError($this->translate('A reference with the same author(s), year and suffix already exists.'));

				$ref = $this->requestData;

				if ($this->rHasVal('selectedTaxa')) $ref['taxa'] = $this->requestData['selectedTaxa'];

			} else
			if ($this->models->Literature->save($data)) {

				$this->clearCache($this->cacheFiles);
				
				$id = $this->rHasId() ? $this->requestData['id'] : $this->models->Literature->getNewId();

				$this->deleteLiteratureTaxon($id);

				if ($this->rHasVal('selectedTaxa')) {
				
					foreach((array)$this->requestData['selectedTaxa'] as $key => $val) {
	
						$this->saveLiteratureTaxon($id,$val,$key);
	
					}

				}
				
				unset($_SESSION['admin']['system']['literature']['alpha']);

				if (isset($_SESSION['admin']['system']['literature']['newRef']) && $_SESSION['admin']['system']['literature']['newRef'] == '<new>') {

					$ref = $this->getReference($id);

					$_SESSION['admin']['system']['literature']['newRef'] =
						'<span class="taxonContentLiteratureLink" onclick="goLiterature('.$id.');">'.
							$ref['author_full'].', '.$ref['year'].$ref['suffix'].
						'</span>';

					$this->redirect('../species/taxon.php?id='.$this->getActiveTaxonId());

				} else
				if(!is_null($this->getActiveTaxonId())) {

					$this->redirect('../species/literature.php?id='.$this->getActiveTaxonId());

				} else
				if ($this->rHasVal('action','preview')) {

					$this->redirect('preview.php?id='.$id);

				} else {

					$_SESSION['admin']['system']['literature']['activeLetter'] = strtolower(substr($this->requestData['author_first'],0,1));

					$navList = $this->getReferencesNavList(true);

					$this->redirect('edit.php?id='.$id);

					//$this->redirect('browse.php');

				}

			} else {

				$this->addError($this->translate('Could not save reference.'));

			}

		}

		$this->getTaxonTree();

		if (isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

		if (isset($navList)) $this->smarty->assign('navList', $navList);

		if (isset($ref)) $this->smarty->assign('ref', $ref);

		if (isset($ref['id'])) $this->smarty->assign('navCurrentId',$ref['id']);

		$this->smarty->assign('includeHtmlEditor', true);

		$this->smarty->assign('alpha', $alpha);

        $this->printPage();

    }

    public function previewAction()
    {

		$this->redirect('../../../app/views/literature/reference.php?p='.$this->getCurrentProjectId().'&id='.$this->requestData['id']);

    }

    public function contentsAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Browsing literature'));
		
		$alpha = $this->getActualAlphabet();

		if (!$this->rHasVal('letter') && isset($_SESSION['admin']['system']['literature']['activeLetter']))
			$this->requestData['letter'] = $_SESSION['admin']['system']['literature']['activeLetter'];

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

		$this->setPageName($this->translate('Search for literature'));
		
		if ($this->rHasVal('search')) {

			if (
				isset($_SESSION['admin']['system']['literature']['search']) && 
				$_SESSION['admin']['system']['literature']['search']['search'] == $this->requestData['search']) 
			{
			
				$refs = $_SESSION['admin']['system']['literature']['search']['results'];
			
			} else {

				$refs = $this->models->Literature->_get(
					array('id' =>
						'select *, concat(author_first,author_second) as author_both
						from %table%
						where
							(author_first like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							author_second like "%'.mysql_real_escape_string($this->requestData['search']).'%" or
							text like "%'.mysql_real_escape_string($this->requestData['search']).'%")
							and project_id = '.$this->getCurrentProjectId().'
						order by author_first,author_second,year'
					)
				);

				$_SESSION['admin']['system']['literature']['search']['search'] = $this->requestData['search'];
	
				$_SESSION['admin']['system']['literature']['search']['results'] = $refs;

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

        } else
        if ($this->rHasVal('action','save_taxa')) {
		
			if ($this->rHasId()) {
				
				$this->deleteLiteratureTaxon($this->requestData['id']);
				
				foreach((array)$this->requestData['taxa'] as $key => $val) {

					$this->saveLiteratureTaxon($this->requestData['id'],$val,$key);

				}
				
	        	$this->clearCache($this->cacheFiles);

			}

        }
		
        $this->printPage();
    
    }




	private function deleteLiteratureTaxon($id,$taxonId=null)
	{

		if (!isset($id)) return;

		$d = array(
			'project_id' => $this->getCurrentProjectId(),
			'literature_id' => $id
		);
		
		if (!is_null($taxonId)) $d['taxon_id'] = $taxonId;

		return $this->models->LiteratureTaxon->delete($d);
				
	}

	private function saveLiteratureTaxon($id,$taxonId,$sortOrder=null)
	{
	
		if (empty($id) || empty($taxonId)) return;

		$d = array(
			'id' => null,
			'project_id' => $this->getCurrentProjectId(),
			'literature_id' => $id,
			'taxon_id' => $taxonId
		);

		if (!is_null($sortOrder)) $d['sort_order'] = $sortOrder;

		$x =  $this->models->LiteratureTaxon->save($d);
	
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
				'columns' => '*,'.$this->getSQLColumnFullAuthor(),
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
					'columns' => 'id,taxon_id,sort_order',
					'order' => 'sort_order'
				)
			);

			foreach((array)$lt as $key => $val) {

				if (isset($val['taxon_id'])) {

					$t = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $val['taxon_id']
							),
							'columns' => 'id,taxon,rank_id,list_level,is_hybrid'
						)
					);

					$lt[$key]['taxon'] = $t[0];

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

	private function _getReference($id=null)
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
					'*,'.$this->getSQLColumnFullAuthor()
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
				'columns' => '*,'.$this->getSQLColumnFullAuthor()
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

	private function getReferences($p,$order=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (!empty($p['author_first'])) $d['author_first'] = $p['author_first'];
		if (!empty($p['author_first like'])) $d['author_first like'] = $p['author_first like'];
		if (!empty($p['author_second'])) $d['author_second'] =  $p['author_second'];
		if (!empty($p['multiple_authors'])) $d['multiple_authors'] =  $p['multiple_authors'];
		if (!empty($p['year'])) $d['year'] = $p['year'];
		if (!empty($p['suffix'])) $d['suffix'] = $p['suffix'];
		if (!empty($p['text'])) $d['text'] = $p['text'];
		if (!empty($p['id'])) $d['id'] = $p['id'];
		if (!empty($p['id !='])) $d['id !='] = $p['id !='];

		$l = $this->models->Literature->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'author_first',
					'columns' => '*, concat(author_first,author_second) as author_both, '.$this->getSQLColumnFullAuthor().', '.$this->getSQLColumnFullYear(),
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
					'columns' => '*'
				)
			);

		return $l[0];

	}

	private function getActualAlphabet()
	{

		if (isset($_SESSION['admin']['system']['literature']['alpha'])) return $_SESSION['admin']['system']['literature']['alpha'];

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
		
		$_SESSION['admin']['system']['literature']['alpha'] = $alpha;

		return $alpha;
	
	}

	private function getReferencesNavList($forceLookup=false) {
	
		if (empty($_SESSION['admin']['literature']['navList']) || $forceLookup) {
		
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
		
			$_SESSION['admin']['literature']['navList'] = isset($res) ? $res : null;
		
		}
		
		return $_SESSION['admin']['literature']['navList'];

	}

	private function clearTempValues()
	{
	
		unset($_SESSION['admin']['system']['literature']['activeLetter']);
		unset($_SESSION['admin']['system']['literature']['search']);
		unset($_SESSION['admin']['system']['literature']['alpha']);

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
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\' (\',
						`year`,
						(
							if(isnull(suffix)!=1,
									suffix,
									\'\'
								)
						),
						\')\'
					) as label2,
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

	private function getSQLColumnFullAuthor()
	{
	
		return '
			concat(
				author_first,
				(
					if(multiple_authors=1,
						\' et al.\',
						if(author_second!=\'\',concat(\' & \',author_second),\'\')
					)
				)
			) as author_full';
	
	}

	private function getSQLColumnFullYear()
	{
	
		return '
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
			) as year_full';
	}

}
