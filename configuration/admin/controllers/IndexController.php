<?php

include_once ('Controller.php');

class IndexController extends Controller
{

	private $_taxonType;

    public $usedModels = array(
		'synonym',
		'commonname',
		'glossary',
		'glossary_synonym',
		'literature',
		'content_free_module',
		'free_module_project'
    );
    
    public $controllerPublicName = 'Index';

	public $cssToLoad = array('lookup.css','index.css');
	public $jsToLoad = array('all'=>array('lookup.js'));

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		$_SESSION['system']['highertaxa'] = false;

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


	public function setTaxonType ($type)
	{

		$this->_taxonType = ($type=='higher') ? 'higher' : 'lower';
	
	}

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

        $this->setPageName(_('Index: species'));

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

        $this->setPageName(_('Index: higher taxa'));

		$this->setTaxonType('higher');

		$this->_indexAction();

	}

    private function _indexAction ()
    {

		$ranks = $this->getProjectRanks(array('idsAsIndex'=>true));
		
		foreach((array)$ranks as $key => $val) {

			if ($val['lower_taxon']==1 && $this->getTaxonType()=='lower') $d[] = $val['id'];
			if ($val['lower_taxon']==0 && $this->getTaxonType()=='higher') $d[] = $val['id'];

		}

		$names = (array)$this->getTaxaLookupList(null,(isset($d) ? $d : null));

		$d =  $this->makeAlphabetFromArray($names,'label',($this->rHasVal('letter') ? $this->requestData['letter'] : null));

		$pagination = $this->getPagination($d['names']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('letter',$this->rHasVal('letter') ? $this->requestData['letter'] : $d['alpha'][0]);

		$this->smarty->assign('ranks',$ranks);

		$this->smarty->assign('taxa',$pagination['items']);

		$this->smarty->assign('taxonType',$this->getTaxonType());

        $this->printPage('index');

		
	}

    public function commonAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName(_('Index: comon names'));
		
		$languages = $this->models->Language->_get(array('id' => '*','fieldAsIndex' => 'id'));

		$names = $this->getCommonnameLookupList();

		foreach((array)$names as $key => $val) {
		
			if ($this->rHasVal('activeLanguage')) {

				if ($this->requestData['activeLanguage']==$val['language_id'] || $this->requestData['activeLanguage']=='*') {

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
		
			$activeLanguage = $this->requestData['activeLanguage'];
		
		} else {
		
			$d = current($l);

			$activeLanguage = $d['id'];

			foreach((array)$names as $key => $val) {
			
				if ($activeLanguage==$val['language_id']) {

					$n[$key] = $val;

				}
			
			}
		}

		$d =  $this->makeAlphabetFromArray($n,'label',($this->rHasVal('letter') ? $this->requestData['letter'] : null));

		$pagination = $this->getPagination($d['names']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('letter',$this->rHasVal('letter') ? $this->requestData['letter'] : $d['alpha'][0]);

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

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }
		
        $this->printPage();
    
    }

	private function getLookupList($search)
	{
	
		/*
			excluded:
			- Introduction
			- Dichotomous key
			- Matrix key 
			- Map key
		
		*/

		$g = $this->getGlossaryLookupList($search);
		$l = $this->getLiteratureLookupList($search);
		$s = $this->getSpeciesLookupList($search);
		$m = $this->getModuleLookupList($search);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				array_merge(
					(array)$g,
					(array)$l,
					(array)$s,
					(array)$m
				),
				$this->controllerBaseName,
				null,
				true
			)
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

		$l2 = $this->models->GlossarySynonym->_get(
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
					`year`,
					"literature" as source,
					concat("views/literature/edit.php?id=",id) as url
				from %table%
				where
					(author_first like "%'.mysql_real_escape_string($search).'%" or
					author_second like "%'.mysql_real_escape_string($search).'%" or
					`year` like "%'.mysql_real_escape_string($search).'%")
					and project_id = '.$this->getCurrentProjectId().'
				order by _a1,_a2,`year`'
			)
		);

		return $l;

	}

	private function makeRegExpCompatSearchString($s)
	{
	
		$s = trim($s);

		// if string enclosed by " take it literally		
		if (preg_match('/^"(.+)"$/',$s)) return '('.mysql_real_escape_string(substr($s,1,strlen($s)-2)).')';

		$s = preg_replace('/(\s+)/',' ',$s);

		if (strpos($s,' ')===0) return mysql_real_escape_string($s);

		$s = str_replace(' ','|',$s);

		return '('.mysql_real_escape_string($s).')';
	
	}

	private function getTaxaLookupList($search=null,$ranks=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();

		if ($search) $d['taxon regexp'] = $this->makeRegExpCompatSearchString($search);

		if ($ranks) $d['rank_id in'] = '('.implode(',',$ranks).')';
		
		$pr = $this->getProjectRanks(array('idsAsIndex' => true));
		
		$t = $this->models->Taxon->_get(
			array(
				'id' => $d,
				'columns' => 'id,taxon as label,\'taxon\' as source, concat(\'views/species/taxon.php?id=\',id) as url,rank_id'
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

		return $this->models->Synonym->_get(
			array(
				'id' => $d,
				'columns' => 'taxon_id as id,synonym as label,\'synonym\' as source, concat(\'views/species/synonyms.php?id=\',taxon_id) as url'
			)
		);

	}


	private function getCommonnameLookupList($search=null)
	{

		return $this->models->Commonname->_get(
			array(
				'where' =>
					'project_id  = '.$this->getCurrentProjectId().
						($search ? 
							' and
							(
								commonname regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\' or
								transliteration regexp \''.$this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)).'\'
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
					concat(\'views/species/common.php?id=\',taxon_id) as url,
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

		$fmp = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'fieldAsIndex' => 'id',
				'columns' => 'id, module'
			)
		);

		$cfm = $this->models->ContentFreeModule->_get(
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

		$a = array();
		
		if (!is_null($letter)) $letter = strtolower($letter);
	
		foreach((array)$names as $key => $val) {
		
			$x = strtolower(substr($val[$field],0,1));

			$a[$x] = $x;

			if (!is_null($letter) && $x==$letter) {
			
				$n[$key] = $val;
			
			}

		}

		if (!is_null($letter) && empty($n)) $letter = null;

		sort($a);

		if (is_null($letter)) {

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
