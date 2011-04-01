<?php

/*
	glossary anatomy:
	
	- the glossary consists of terms.
	- a term consists of the term and its definition.
	- the term can consist of one or more words.
	- term and defintion are defined in one language.
	- terms and defintions have no translations into the other languages, as they are intended to complement the text in a 
	  specific language. this means that the glossaries for each language might differ in size and content.
	- each term can have one or more synonyms.
	- a synonym consists of a single term (one or more words) in the same language as the term it is a synonym of.



*/

include_once ('Controller.php');

class GlossaryController extends Controller
{

    public $usedModels = array(
		'glossary_media',
		'label_language'
    );
   
    public $controllerPublicName = 'Glossary';

	public $cssToLoad = array(
		'basics.css',
		'glossary.css',
		'colorbox/colorbox.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'colorbox/jquery.colorbox.js'
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

    /**
     * Index of glossary
     *
     * @access    public
     */
    public function indexAction()
    {
	
   		$alpha = $this->getGlossaryAlphabet($this->didActiveLanguageChange());

		if (!$this->rHasVal('letter')) $this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : null;

		if ($this->rHasVal('letter')) {
		
			$this->requestData['letter'] = strtolower($this->requestData['letter']);

			$gloss = $this->getGlossaryTerms(array('term like' => $this->requestData['letter'].'%'));

			$this->setPageName(sprintf(_('Glossary Index: %s'),strtoupper($this->requestData['letter'])));

		}

		unset($_SESSION['user']['search']['hasSearchResults']);

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($gloss)) $this->smarty->assign('gloss',$gloss);

        $this->printPage();
    
    }

    public function termAction()
    {
    
		if ($this->rHasId() && !$this->didActiveLanguageChange()) {

			$term = $this->getGlossaryTerm($this->requestData['id']);

			$letter = strtolower(substr($term['term'],0,1));

			$this->setPageName(sprintf(_('Glossary: "%s"'),$term['term']));

		} else {
		
			$this->getGlossaryAlphabet(true);
		
			$this->redirect('index.php');
		
		}

		$alpha = $this->getGlossaryAlphabet($this->didActiveLanguageChange());

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if (isset($letter)) $this->smarty->assign('letter', $letter);

		if (isset($term)) $this->smarty->assign('term', $term);

		$this->smarty->assign('adjacentTerms', $this->getAdjacentTerms($term['id']));

        $this->printPage();
    
    }


    public function hintAction()
    {
    
		if (!$this->rHasId()) return;

		$term = $this->getGlossaryTerm($this->requestData['id']);

		$this->smarty->assign('id', $this->requestData['id']);

		if (isset($term)) $this->smarty->assign('term', $term);

        $this->printPage();
    
    }

	private function getGlossaryTerms($search)
	{

		if (!empty($search['term'])) $d['term'] = $search['term'];
		if (!empty($search['term like'])) $d['term like'] = $search['term like'];
		if (!empty($search['definition'])) $d['definition'] = $search['definition'];

		$d['project_id'] = $this->getCurrentProjectId();
		$d['language_id'] =  $this->getCurrentLanguageId();

		$g = $this->models->Glossary->_get(array('id' => $d,'order' => 'term'));

		foreach((array)$g as $key => $val) {

			$g[$key]['synonyms'] = $this->getGlossarySynonyms($val['id']);

		}
		
		return $g;

	}

	private function getGlossaryTerm($id)
	{

		$g = $this->models->Glossary->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
				)
			)
		);

		$g[0]['synonyms'] = $this->getGlossarySynonyms($id);
		$g[0]['media'] = $this->getGlossaryMedia($id);

		return $g[0];

	}

	private function getGlossarySynonyms($id) {

		$gs = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id	
				),
				'columns' => 'id,language_id,synonym',
				'order' => 'synonym'
			)
		);
		
		foreach((array)$gs as $key => $val) {

			if (!isset($_SESSION['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()])) {

				$ll = $this->models->LabelLanguage->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->getCurrentLanguageId(),
							'label_language_id' => $val['language_id']
						),
						'columns' => 'label'
					)
				);
	
				if ($ll) $gs[$key]['language'] = $_SESSION['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()] = $ll[0]['label'];

			} else {

				$gs[$key]['language'] = $_SESSION['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()];

			}

		}
		
		return $gs;

	}

	private function getGlossaryMedia($id) {

		return $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id	
				),
				'columns' => 'file_name,thumb_name,original_name'
			)
		);

	}

	private function getGlossaryAlphabet($forceLookup=false)
	{

		if (!isset($_SESSION['user']['glossary']['alpha']) or $forceLookup) {

			unset($_SESSION['user']['glossary']['alpha']);

			$g = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
					),
					'columns' => 'distinct lower(substr(term,1,1)) as letter',
					'order' => 'letter'
				)
			);
			
			$_SESSION['user']['glossary']['alpha'] = null;
			
			foreach((array)$g as $key => $val) {

				$_SESSION['user']['glossary']['alpha'][] = $val['letter'];

			}
			
		}

		return $_SESSION['user']['glossary']['alpha'];
	
	}

	private function getAdjacentTerms($id)
	{

		$g = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->getCurrentLanguageId(),
				),
				'columns' => 'id,term',
				'order' => 'term'
			)
		);

		foreach((array)$g as $key => $val) {

			if ($val['id']==$id) {
			
				return array(
					'prev' => isset($g[$key-1]) ? $g[$key-1] : null,
					'next' => isset($g[$key+1]) ? $g[$key+1] : null
				);

			}

		}
		
		return null;

	}


}