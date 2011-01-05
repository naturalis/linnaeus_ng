<?php

/*

	glossary anatomy:
	
	- the glossary consists of terms.
	- a term consists of the term and its definition.
	- the term can consist of one or more words.
	- term and defintion are defined in one language.
	- terms and defintions have no translations into the other languages, as they are intended to complement the text in a specific language. this means that the glossaries for each language might differ in size and content.
	- each term can have one or more synonyms.
	- a synonym consists of a single term (one or more words) in the same language as the term it is a synonym of.


term
  term *+
  definition *+
  synonyms (0,1,n) (translations are useless)
  multimedia & name +

process ALL content to highlight

*/

include_once ('Controller.php');

class GlossaryController extends Controller
{

    public $usedModels = array(
		'glossary',
		'glossary_synonym'
    );
   
    public $controllerPublicName = 'Glossary';


	public $cssToLoad = array('glossary.css');

	public $jsToLoad = array('all' => array('glossary.js'));

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

        $this->printPage();
    
    }

    public function editAction()
    {
    
        $this->checkAuthorisation();

		if ($this->rHasId()) $gloss = $this->getGlossaryTerm();

		if (isset($gloss)) {

    	    $this->setPageName(sprintf(_('Editing glossary term "%s (%s)"'),$gloss['term']));
		
		} else {

    	    $this->setPageName(_('New glossary term'));

		}

		if ($this->rHasVal('term') && $this->rHasVal('definition') && !$this->isFormResubmit()) {

			$data = $this->requestData;

			$data['project_id'] = $this->getCurrentProjectId();

			$data['id'] =  $this->rHasId() ? $this->requestData['id'] : 'null';

            $data['definition'] = strip_tags($data['definition']);

			if ($data['id']=='null' && $this->getGlossaryTerms(array('term' => $data['term'],'language_id' => $data['language_id']))) {

				$this->addError(_('Glossary term already exists.'));

				$gloss = $this->requestData;

			} else
			if ($this->models->Glossary->save($data)) {

				if ($this->rHasVal('synonyms')) {

					$id = $this->rHasId() ? $this->requestData['id'] : $this->models->Glossary->getNewId();

					$this->models->GlossarySynonym->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'glossary_id' => $id
						)
					);

					foreach((array)$this->requestData['synonyms'] as $key => $val) {

						$this->models->GlossarySynonym->save(
							array(
								'id' => 'null',
								'project_id' => $this->getCurrentProjectId(),
								'glossary_id' => $id,
								'synonym' => $val
							)
						);

					}

				}
				
				$_SESSION['system']['glossary']['activeLetter'] = strtolower(substr($this->requestData['term'],0,1));

				$this->redirect('browse.php');

			} else {

				$this->addError(_('Could not save glossary term.'));

			}

		}

        if (isset($gloss)) $this->smarty->assign('gloss', $ref);

        if ($_SESSION['project']['languages']) $this->smarty->assign('languages', $_SESSION['project']['languages']);

        if ($_SESSION['project']['default_language_id']) $this->smarty->assign('defaultLanguage', $_SESSION['project']['default_language_id']);


        $this->printPage();

		return;










		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$_SESSION['system']['literature']['activeLetter'] = strtolower(substr($ref['author_first'],0,1));
			
			$this->deleteReference($this->requestData['id']);

			$this->redirect('browse.php');

		} 

    }

	private function getActualAlphabet()
	{

		if (isset($_SESSION['system']['literature']['alpha'])) return $_SESSION['system']['literature']['alpha'];

		$l = $this->models->Glossary->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'distinct lower(substr(term,1,1)) as letter',
				'order' => 'letter'
			)
		);
		
		$alpha = null;
		
		foreach((array)$l as $key => $val) {

			$alpha[] = $val['letter'];

		}
		
		$_SESSION['system']['glossary']['alpha'] = $alpha;

		return $alpha;
	
	}

	private function getGlossaryTerm($id=null)
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

	private function getGlossaryTerms($search,$order=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if (!empty($search['term'])) $d['term'] = $search['term'];
		if (!empty($search['term like'])) $d['term like'] = $search['term like'];
		if (!empty($search['definition'])) $d['definition'] = $search['definition'];
		if (!empty($search['language_id'])) $d['language_id'] =  $search['language_id'];

		$l = $this->models->Glossary->_get(
				array(
					'id' => $d,
					'order' => !empty($order) ? $order : 'term'
				)
			);

		return $l;

	}

    public function browseAction()
    {
    
        $this->checkAuthorisation();

		$this->setPageName(_('Browsing glossary'));
		
		$alpha = $this->getActualAlphabet();

		if (!$this->rHasVal('letter') && isset($_SESSION['system']['glossary']['activeLetter']))
			$this->requestData['letter'] = $_SESSION['system']['glossary']['activeLetter'];

		if (!$this->rHasVal('letter'))
			$this->requestData['letter'] = $alpha[0];


		if ($this->rHasVal('letter')) {

			$gloss = $this->getGlossaryTerms(array('term like' => $this->requestData['letter'].'%'),'term');

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

        if ($_SESSION['project']['languages']) $this->smarty->assign('languages', $_SESSION['project']['languages']);

        if ($_SESSION['project']['default_language_id']) $this->smarty->assign('defaultLanguage', $_SESSION['project']['default_language_id']);

		$this->smarty->assign('sortBy', $sortBy);

		$this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($gloss)) $this->smarty->assign('gloss',$gloss);

        $this->printPage();

	}



}