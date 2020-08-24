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
include_once ('MediaController.php');

class GlossaryController extends Controller
{
	private $_mc;

    public $usedModels = array(
		'glossary_synonyms',
		'labels_languages',
		'glossary_media',
		'glossary_media_captions'
    );

    public $controllerPublicName = 'Glossary';

	public $cssToLoad = array(
		'glossary.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
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
    public function __construct($p=null)
    {
        parent::__construct($p);
		$this->setMediaController();
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

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
	}

    /**
     * Index of glossary
     *
     * @access    public
     */
    public function indexAction()
    {
		if ( !$this->rHasId() )
		{
			$this->redirect( 'contents.php' );
			//$d=$this->getFirstGlossaryTerm($this->rHasVal('letter') ? $this->rGetVal('letter') : null);
			//$id=(isset($d['id']) ? $d['id'] : null);
		}
		else
		{
			$this->redirect( 'term.php?id=' . $this->rGetId() );
			//$id = $this->rGetId();
		}
		
		/*
		if ($id)
		{
			$this->setStoreHistory(false);
			$this->redirect('term.php?id='.$id);
		}

        $this->printPage();
		*/
    }

    public function termAction()
    {
		if ($this->rHasId())
		{
			$term=$this->getGlossaryTerm($this->rGetId());

			if (isset($term['term']))
			{

				$letter = strtolower(substr($term['term'],0,1));
				$this->setPageName(sprintf($this->translate('Glossary: "%s"'),$term['term']));
			}
		}
		else
		{
			$this->redirect('index.php');
		}

		$alpha=$this->getGlossaryAlphabet();

		if (isset($alpha)) $this->smarty->assign('alpha', $alpha);
		if (isset($letter)) $this->smarty->assign('letter', $letter);
		if (isset($term)) $this->smarty->assign('term', $term);
		if (isset($term['id'])) $this->smarty->assign('adjacentItems', $this->getAdjacentItems($term['id']));

        $this->printPage();
    }

    public function hintAction()
    {
		if (!$this->rHasId()) return;

		$term=$this->getGlossaryTerm($this->rGetId());

		if (isset($term)) $this->smarty->assign('term', $term);
		$this->smarty->assign('id', $this->rGetId());

        $this->printPage();
    }

    public function contentsAction()
    {

		$alpha = $this->getGlossaryAlphabet();

		if (!$this->rHasVal('letter') || !in_array($this->rGetVal('letter'),(array)$alpha))
		{
			$letter = isset($alpha[0]) ? $alpha[0] : '-';
		}
		else
		{
			$letter = $this->rGetVal('letter');
		}

		if (!empty($letter))
		{
			$gloss = $this->getGlossaryTerms(
				array(
					'term like' => $letter.'%',
					'language_id' => $this->getCurrentLanguageId()
				),
				'term');

			$this->smarty->assign('letter', $letter);
			$this->smarty->assign('gloss',$gloss);

		}

		$this->smarty->assign('alpha', $alpha);
		$this->printPage();

	}

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list'))
		{
            $this->getLookupList($this->rGetVal('search'));
        }

		$this->allowEditPageOverlay = false;
        $this->printPage();
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
						'term like' => '%'. ($search=='*' ? '' : 
							mysqli_real_escape_string($this->databaseConnection, $search)).'%'
					),
				'columns' => 'id,term as label,"glossary" as source'
			)
		);

		if ($includeSynonyms)
		{
			$l2 = $this->models->GlossarySynonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'synonym like' => '%'.($search=='*' ? '' : 
							mysqli_real_escape_string($this->databaseConnection, $search)).'%'
						),
					'columns' => 'glossary_id as id,synonym as label,"glossary synonym" as source'
				)
			);

			$l = array_merge((array)$l,(array)$l2);
		}

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$l,
				'module'=>$this->controllerBaseName,
				'url'=>'../glossary/term.php?id=%s',
				'sortData'=>true
			))
		); // for glossary lookup list

		return $l; // for combined lookup list
	}

	private function getGlossaryTerms($p)
	{
		if (!empty($p['term'])) $d['term'] = $p['term'];
		if (!empty($p['term like'])) $d['term like'] = $p['term like'];
		if (!empty($p['definition'])) $d['definition'] = $p['definition'];

		$d['project_id'] = $this->getCurrentProjectId();
		$d['language_id'] =  $this->getCurrentLanguageId();

		$g = $this->models->Glossary->_get(array('id' => $d,'order' => 'term'));

		foreach((array)$g as $key => $val)
		{
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

	private function getGlossarySynonyms($id)
	{
		return $this->models->GlossaryModel->getGlossarySynonyms(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id ,
			'language_id' => $this->getCurrentLanguageId()
		));
	}
/*
	private function getGlossaryMedia($id)
	{
		$gm=$this->models->GlossaryModel->getGlossaryMedia(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id ,
			'language_id' => $this->getCurrentLanguageId()
		));


		$this->loadControllerConfig('Species');
		$mimes = $this->controllerSettings['mime_types'];
		$this->loadControllerConfig();

		foreach((array)$gm as $key => $val)
		{
			$gm[$key]['caption'] = isset($gmc[0]['caption']) ? $this->matchHotwords($gmc[0]['caption']) : null;

			$t = isset($mimes[$val['mime_type']]) ? $mimes[$val['mime_type']] : null;

			$gm[$key]['category'] = isset($t['type']) ? $t['type'] : 'other';
			$gm[$key]['category_label'] = isset($t['label']) ? $t['label'] : 'Other';
			$gm[$key]['full_path']=$this->getProjectUrl('uploadedMedia').$gm[$key]['file_name'];
		}

		return $gm;
	}
*/

	private function getGlossaryMedia ($id)
	{
	    $this->_mc->setItemId($id);
	    $media = $this->_mc->getItemMediaFiles();
		return $this->_mc->reformatOutput($media);
	}


	private function getGlossaryAlphabet()
	{

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

		foreach((array)$g as $key => $val)
		{
			$d[]=$val['letter'];
		}

		return $d;
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

		foreach((array)$g as $key => $val)
		{
			if ($val['id']==$id)
			{
				return array(
					'prev' => isset($g[$key-1]) ? $g[$key-1] : null,
					'next' => isset($g[$key+1]) ? $g[$key+1] : null
				);
			}
		}

		return null;

	}




}