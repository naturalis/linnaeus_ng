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
		'imaginarybeings-basics.css',
		'imaginarybeings-literature.css',
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'tablesorter/jquery-latest.js',
				'tablesorter/jquery.tablesorter.js',
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
 
 		$alpha = $this->getLiteratureAlphabet();

		if (!$this->rHasVal('letter')) $this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : null;

		if ($this->rHasVal('letter')) {
		
			$this->requestData['letter'] = strtolower($this->requestData['letter']);

			$refs = $this->getReferences(array('author_first like' => $this->requestData['letter'].'%'));

			$this->setPageName(sprintf(_('Literature Index: %s'),strtoupper($this->requestData['letter'])));

		}

		unset($_SESSION['user']['search']['hasSearchResults']);

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($refs)) $this->smarty->assign('refs',$refs);

        $this->printPage();
   
    }

    public function referenceAction()
    {

		if ($this->rHasId()) {

			$ref = $this->getReference($this->requestData['id']);

			$letter = strtolower(substr($ref['author_first'],0,1));

			$this->setPageName(sprintf(_('Literature: "%s"'),$ref['author_full'].' ('.$ref['year'].')'));

		} else {
		
			$this->redirect('index.php');
		
		}

		$alpha = $this->getLiteratureAlphabet();

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if (isset($letter)) $this->smarty->assign('letter', $letter);

		if (isset($ref)) $this->smarty->assign('ref', $ref);

        $this->printPage();

    }

	private function getLiteratureAlphabet($forceLookup=false)
	{

		if (!isset($_SESSION['user']['literature']['alpha']) or $forceLookup) {

			unset($_SESSION['user']['literature']['alpha']);

			$l = $this->models->Literature->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'columns' => 'distinct lower(substr(author_first,1,1)) as letter',
					'order' => 'author_first'
				)
			);

			$_SESSION['user']['literature']['alpha'] = null;
		
			foreach((array)$l as $key => $val) {

				$_SESSION['user']['literature']['alpha'][] = $val['letter'];

			}

		}

		return $_SESSION['user']['literature']['alpha'];
	
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
	
}




























