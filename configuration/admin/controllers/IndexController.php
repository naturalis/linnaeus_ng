<?php

include_once ('Controller.php');

class IndexController extends Controller
{

	private $_taxonType;

    public $usedModels = array(
		'synonyms',
		'commonnames',
		'glossary',
		'glossary_synonyms',
		'literature',
		'content_free_modules',
		'free_modules_projects'
    );

    public $controllerPublicName = 'Index';

	public $cssToLoad = array(
    	'lookup.css',
    	'index.css',
    	'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
	   'all'=>array(
	       'lookup.js',
    	   'int-link.js'
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

		$_SESSION['admin']['system']['highertaxa'] = false;

    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {

        parent::__destruct();

    }


    /**
     * Set Taxon Type
     * @param $type
     */
    public function setTaxonType ($type)
	{

		$this->_taxonType = ($type=='higher') ? 'higher' : 'lower';

	}

    /**
     * Get Taxon Type
     * @return string
     */
    private function getTaxonType ()
	{

		return isset($this->_taxonType) ? $this->_taxonType : 'lower';

	}


    /**
     * Index of the index module (ha); shows species
     *
     * @access    public
     */
    public function indexAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Species'));

		$this->setTaxonType('lower');

		$this->_indexAction();

    }

    /**
     * Index of the index module (ha); shows species
     *
     * @access    public
     */
    public function higherAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Higher taxa'));

		$this->setTaxonType('higher');

		$this->_indexAction();

	}

    /**
     *
     */
    private function _indexAction ()
    {

		$ranks = $this->getProjectRanks(array('idsAsIndex'=>true));

		foreach((array)$ranks as $key => $val) {

			if ($val['lower_taxon']==1 && $this->getTaxonType()=='lower') $d[] = $val['id'];
			if ($val['lower_taxon']==0 && $this->getTaxonType()=='higher') $d[] = $val['id'];

		}

		$names = (array)$this->getTaxaLookupList(null,(isset($d) ? $d : null));

		$d =  $this->makeAlphabetFromArray($names,'label',($this->rHasVal('letter') ? $this->rGetVal('letter') : null));

		$this->customSortArray($d['names'],array('key' => 'label'));

		$this->smarty->assign('taxa',$d['names']);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('letter',$this->rHasVal('letter') ? htmlentities($this->rGetVal('letter')) : null);

		$this->smarty->assign('ranks',$ranks);

		$this->smarty->assign('taxonType',$this->getTaxonType());

        $this->printPage('index');

	}

    /**
     *
     */
    public function commonAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Common names'));

		$languages = $this->models->Languages->_get(array('id' => '*','fieldAsIndex' => 'id'));

		$names = $this->getCommonnameLookupList();

		$l=array();

		foreach((array)$names as $key => $val)
		{

			if ($this->rHasVal('activeLanguage')) {

				if ($this->rGetVal('activeLanguage')==$val['language_id'] || $this->rGetVal('activeLanguage')=='*') {

					$n[$key] = $val;
					$n[$key]['language'] = $languages[$val['language_id']]['language'];

				}

			} else {

				$names[$key]['language'] = $languages[$val['language_id']]['language'];

			}

			$l[$val['language_id']] = $languages[$val['language_id']];

		}

		$this->customSortArray($n,array('key' => 'label'));

		$this->customSortArray($l,array('key' => 'language','maintainKeys' => true));

		if ($this->rHasVal('activeLanguage')) {

			$activeLanguage = htmlentities($this->rGetVal('activeLanguage'));

		} else {

			$d = current($l);

			$activeLanguage = $d['id'];

			foreach((array)$names as $key => $val) {

				if ($activeLanguage==$val['language_id']) {

					$n[$key] = $val;

				}

			}
		}

		$d =  $this->makeAlphabetFromArray($n,'label',($this->rHasVal('letter') ? $this->rGetVal('letter') : null));

		$pagination = $this->getPagination($d['names']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);

		$this->smarty->assign('nextStart', $pagination['nextStart']);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('letter',$this->rHasVal('letter') ? htmlentities($this->rGetVal('letter')) : (isset($d['alpha'][0]) ? $d['alpha'][0] : null));

		$this->smarty->assign('taxa',$pagination['items']);

		$this->smarty->assign('languages',$l);

		$d = current($l);

		$this->smarty->assign('activeLanguage',$activeLanguage);

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

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search'))) {

            $this->getLookupList(htmlentities($this->rGetVal('search')));

        }

        $this->printPage();

    }

    /**
     * Search the species lookup
     *
     * @param $search
     */
    private function getLookupList($search)
	{

		/*
			excluded:
			- Introduction
			- Dichotomous key
			- Matrix key
			- Map key

		*/

		//$g = $this->getGlossaryLookupList($search);
		//$l = $this->getLiteratureLookupList($search);
		$s = $this->getSpeciesLookupList($search);
		//$m = $this->getModuleLookupList($search);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>(array)$s
				/*
				array_merge(
					(array)$g,
					(array)$l,
					(array)$s,
					(array)$m
				)
				*/,
				'module'=>$this->controllerBaseName,
				'sortData'=>true
			))
		);

	}

	private function getGlossaryLookupList($search)
	{

		if (empty($search)) return;

		$l1 = $this->models->Glossary->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'term like' => '%'.$search.'%'
					),
				'columns' => '
					id,
					term as label,
					"glossary" as source,
					concat("views/glossary/edit.php?id=",id) as url'
			)
		);

		$l2 = $this->models->GlossarySynonyms->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'synonym like' => '%'.$search.'%'
					),
				'columns' => '
					glossary_id as id,
					synonym as label,
					"glossary synonym" as source,
					concat("views/glossary/edit.php?id=",glossary_id) as url'
			)
		);

		return array_merge((array)$l1,(array)$l2);

	}

	private function getLiteratureLookupList($search)
	{

		return $this->models->IndexController->getLiteratureLookupList(array(
            'projectId' => $this->getCurrentProjectId(),
		    'search' => $search,
		    'path' => "views/literature/edit.php?id="
		));

	}

	private function makeRegExpCompatSearchString($s)
	{

		// Moved to ControllerModel

		return $this->models->ControllerModel->makeRegExpCompatSearchString($s);

	}

	private function getTaxaLookupList($search=null,$ranks=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();
		if ($search) $d['taxon regexp'] = $this->makeRegExpCompatSearchString($search);

		if ($ranks) $d['rank_id in'] = '('.implode(',',$ranks).')';

		$pr = $this->getProjectRanks(array('idsAsIndex' => true));

		$t = $this->models->Taxa->_get(
			array(
				'id' => $d,
				'columns' => 'id,taxon as label,\'taxon\' as source, concat(\'../species/taxon.php?id=\',id) as url,rank_id'
			)
		);

		foreach((array)$t as $key => $val) {

			$t[$key]['source'] = strtolower($pr[$val['rank_id']]['rank']);

		}

		return $t;

	}

	private function getSynonymLookupList($search=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if ($search) $d['synonym regexp'] = $this->makeRegExpCompatSearchString($search);

		return $this->models->Synonyms->_get(
			array(
				'id' => $d,
				'columns' => 'taxon_id as id,synonym as label,\'synonym\' as source, concat(\'../species/synonyms.php?id=\',taxon_id) as url'
			)
		);

	}

	private function getCommonnameLookupList($search=null)
	{

		return $this->models->Commonnames->_get(
			array(
				'where' =>
					'project_id  = '.$this->getCurrentProjectId().
						($search ?
							' and
							(
								commonname regexp \''.$this->models->Commonnames->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
								transliteration regexp \''.$this->models->Commonnames->escapeString($this->makeRegExpCompatSearchString($search)).'\'
							)' :
							''
						),
				'columns' =>
					'taxon_id as id,'.
					($search ? '
						if(commonname regexp \''.$this->makeRegExpCompatSearchString($search).'\',commonname,transliteration) ' :
						'ifnull(commonname,transliteration)' ) .' as label,
						transliteration,
					\'common name\' as source,
					concat(\'../species/common.php?id=\',taxon_id) as url,
					language_id'
			)
		);

	}

	private function getSpeciesLookupList($search=null)
	{

		$taxa = (array)$this->getTaxaLookupList($search);

		$synonyms = (array)$this->getSynonymLookupList($search);

		$commonnames = (array)$this->getCommonnameLookupList($search);

		return array_merge($taxa,$synonyms,$commonnames);

	}

	private function getModuleLookupList($search)
	{

		if (empty($search)) return;

		$fmp = $this->models->FreeModulesProjects->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'fieldAsIndex' => 'id',
				'columns' => 'id, module'
			)
		);

		$cfm = $this->models->ContentFreeModules->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'topic like' => '%'.$search.'%'
					),
				'order' => 'topic',
				'columns' => 'distinct page_id as id, topic as label,
					concat("views/module/index.php?page=",page_id,"&freeId=",module_id) as url,
					module_id'
			)
		);

		foreach((array)$cfm as $key => $val) {

			$cfm[$key]['source'] = $fmp[$val['module_id']]['module'].' topic';

		}

		return $cfm;

	}

	private function makeAlphabetFromArray($names,$field,$letter=null)
	{

		$a=$n=array();

		if (!is_null($letter)) $letter = strtolower($letter);

		foreach((array)$names as $key => $val) {

			$x = strtolower(substr($val[$field],0,1));

			$a[$x] = $x;

			if (is_null($letter) || (!is_null($letter) && $x==$letter)) {

				$n[$key] = $val;

			}

		}

		if (!is_null($letter) && empty($n)) $letter = null;

		sort($a);

		if (is_null($letter) && isset($a[0]))
		{

			$letter = $a[0];

			foreach((array)$names as $key => $val) {

				if (strtolower(substr($val[$field],0,1))==$letter) $n[$key] = $val;

			}

		}

		return array(
			'alpha' => $a,
			'names' => $n
		);

	}

}
