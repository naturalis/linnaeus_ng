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
		'label_language',
		'glossary_media_captions'
    );
   
    public $controllerPublicName = 'Glossary';

	public $cssToLoad = array(
		'basics.css',
		'glossary.css',
		//'colorbox/colorbox.css',
		'prettyPhoto/prettyPhoto.css',
		'lookup.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				//'colorbox/jquery.colorbox.js',
				'prettyPhoto/jquery.prettyPhoto.js',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
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

    /**
     * Index of glossary
     *
     * @access    public
     */
    public function indexAction()
    {

		if (!$this->rHasVal('id')) {

			$d = $this->getFirstGlossaryTerm($this->rHasVal('letter') ? $this->requestData['letter'] : null);

			$id = (isset($d['id']) ? $d['id'] : null);
			
		} else {
			
			$id = $this->requestData['id'];
				
		}

		//unset($_SESSION['app']['user']['search']['hasSearchResults']);
		
		if ($id) $this->redirect('term.php?id='.$id);

		/*

   		$alpha = $this->getGlossaryAlphabet($this->didActiveLanguageChange());

		if (!$this->rHasVal('letter')) $this->requestData['letter'] = isset($alpha[0]) ? $alpha[0] : null;

		if ($this->rHasVal('letter')) {
		
			$this->requestData['letter'] = strtolower($this->requestData['letter']);

			$gloss = $this->getGlossaryTerms(array('term like' => $this->requestData['letter'].'%'));

			$this->setPageName(sprintf(_('Glossary Index: %s'),strtoupper($this->requestData['letter'])));

		}

		//unset($_SESSION['app']['user']['search']['hasSearchResults']);

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if ($this->rHasVal('letter')) $this->smarty->assign('letter', $this->requestData['letter']);

		if (isset($gloss)) $this->smarty->assign('gloss',$gloss);

		*/

        $this->printPage();

    
    }

    public function termAction()
    {
    
		if ($this->rHasId() && !$this->didActiveLanguageChange()) {

			$term = $this->getGlossaryTerm($this->requestData['id']);

			if (isset($term['term'])) {

				$letter = strtolower(substr($term['term'],0,1));

				$this->setPageName(sprintf(_('Glossary: "%s"'),$term['term']));

			}

		} else {
		
			$this->getGlossaryAlphabet(true);
		
			$this->redirect('index.php');
		
		}

		$alpha = $this->getGlossaryAlphabet($this->didActiveLanguageChange());

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);

		if (isset($letter)) $this->smarty->assign('letter', $letter);

		if (isset($term)) $this->smarty->assign('term', $term);

		if (isset($term['id'])) $this->smarty->assign('adjacentItems', $this->getAdjacentItems($term['id']));

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

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list')) {

            $this->getLookupList($this->requestData['search']);

        }
		
		$this->allowEditPageOverlay = false;
		
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

			if (!isset($_SESSION['app']['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()])) {

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
	
				if ($ll) $gs[$key]['language'] = $_SESSION['app']['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()] = $ll[0]['label'];

			} else {

				$gs[$key]['language'] = $_SESSION['app']['system']['language_names'][$val['language_id']][$this->getCurrentLanguageId()];

			}

		}
		
		return $gs;

	}

	private function getGlossaryMedia($id) {

		$gm = $this->models->GlossaryMedia->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'glossary_id' => $id	
				),
				'columns' => 'file_name,thumb_name,original_name,id,fullname,mime_type,substring(mime_type,1,locate(\'/\',mime_type)-1) as mime',
				'order' => 'mime'
			)
		);

		$this->loadControllerConfig('Species');
		
		$mimes = $this->controllerSettings['mime_types'];

		$this->loadControllerConfig();
		
		foreach((array)$gm as $key => $val) {

			$gmc = $this->models->GlossaryMediaCaptions->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),					
						'media_id' => $val['id']
					),
					'columns' => 'caption'
				)
			);	
			
			$gm[$key]['caption'] = isset($gmc[0]['caption']) ? $this->matchHotwords($gmc[0]['caption']) : null;

			$t = isset($mimes[$val['mime_type']]) ?
					$mimes[$val['mime_type']] :
					null;

			$gm[$key]['category'] = isset($t['type']) ? $t['type'] : 'other';
			$gm[$key]['category_label'] = isset($t['label']) ? $t['label'] : 'Other';
			//$gm[$key]['mime_show_order'] = isset($t['type']) ? $this->controllerSettings['mime_show_order'][$t['type']] : 99;
			$gm[$key]['full_path'] = $_SESSION['app']['project']['urls']['uploadedMedia'].$gm[$key]['file_name'];

		
		}
		
		
		return $gm;

	}

	private function getGlossaryAlphabet($forceLookup=false)
	{

		if (!isset($_SESSION['app']['user']['glossary']['alpha']) or $forceLookup) {

			unset($_SESSION['app']['user']['glossary']['alpha']);

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
			
			$_SESSION['app']['user']['glossary']['alpha'] = null;
			
			foreach((array)$g as $key => $val) {

				$_SESSION['app']['user']['glossary']['alpha'][] = $val['letter'];

			}
			
		}

		return $_SESSION['app']['user']['glossary']['alpha'];
	
	}

	private function getFirstGlossaryTerm($letter=null)
	{

		$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->getCurrentLanguageId(),
			);
			
		if (isset($letter)) $d['term like'] = $letter.'%';

		$g = $this->models->Glossary->_get(
			array(
				'id' => $d,
				'order' => 'term',
				'limit' => 1,
				'columns' => 'id'
			)
		);

		return $g[0];

	}


	private function getAdjacentItems($id)
	{

		$g = $this->models->Glossary->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->getCurrentLanguageId(),
				),
				'columns' => 'id,term as label',
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

	public function getLookupList($search)
	{

		if (empty($search)) return;
		
		$includeSynonyms = false;

		$l = $this->models->Glossary->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'term like' => '%'. ($search=='*' ? '' : mysql_real_escape_string($search)).'%'
					),
				'columns' => 'id,term as label,"glossary" as source'
			)
		);

		if ($includeSynonyms) {

			$l2 = $this->models->GlossarySynonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'synonym like' => '%'.($search=='*' ? '' : mysql_real_escape_string($search)).'%'
						),
					'columns' => 'glossary_id as id,synonym as label,"glossary synonym" as source'
				)
			);
			
			$l = array_merge((array)$l,(array)$l2);
			
		}

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				$this->controllerBaseName,
				'../glossary/term.php?id=%s',
				true
			)
		); // for glossary lookup list
		
		return $l; // for combined lookup list
		
	}



}