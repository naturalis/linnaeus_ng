<?php

include_once ('Controller.php');

class IndexController extends Controller
{

	public $noResultCaching = true;

    public $usedModels = array(
		'synonym',
		'commonname',
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'basics.css',
		'index.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'lookup.js',
		'dialog/jquery.modaldialog.js'
	));
	
	private $_usePagination = false;
	

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($params=null)
    {
	
		$this->setControllerParams($params);

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

	public function indexAction()
	{
	
        $this->setPageName(_('Index: species'));

		$this->setTaxonType('lower');

		$this->_speciesIndexAction();

	}

    /**
     * Index of the index module (ha); shows species
     *
     * @access    public
     */
    public function higherAction ()
    {

        $this->setPageName(_('Index: higher taxa'));

		$this->setTaxonType('higher');

		$this->_speciesIndexAction();

	}

    public function commonAction ()
    {

        $this->setPageName(_('Index: comon names'));
		
		$languages = $this->models->Language->_get(array('id' => '*','fieldAsIndex' => 'id'));

		$names = $this->searchCommonNames();

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

		$this->customSortArray($l,array('key' => 'language','maintainKeys' => true));

		if ($this->rHasVal('activeLanguage')) {
		
			$activeLanguage = $this->requestData['activeLanguage'];
		
		} else {
		
			$d = current($l);

			$activeLanguage = $d['id'];

			foreach((array)$names as $key => $val) {
			
				if ($activeLanguage==$val['language_id']) $n[$key] = $val;
			
			}
		}

		$this->customSortArray($n,array('key' => 'label'));

		$letterToShow = $this->getFirstUsefulLetter($n, 'label');
		
		$d =  $this->makeAlphabetFromArray($n,'label',$letterToShow);

		$this->smarty->assign('usePagination',$this->_usePagination);
		
		if ($this->_usePagination) {
		
			$pagination = $this->getPagination($d['names']);

			$this->smarty->assign('prevStart', $pagination['prevStart']);
		
			$this->smarty->assign('nextStart', $pagination['nextStart']);

			$this->smarty->assign('taxa',$pagination['items']);
		
		} else {

			$this->smarty->assign('taxa',$d['names']);

			$this->smarty->assign('alphaNav',$d['alphaNav']);

		}

		$this->smarty->assign('showSpeciesIndexMenu', true);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('letter',$letterToShow);

		$this->smarty->assign('nameLanguages',$l);

		$d = current($l);

		$this->smarty->assign('activeLanguage',$activeLanguage);

		$this->smarty->assign('taxonType','common');

        $this->printPage();

		
	}

	private function setTaxonType ($type)
	{

		$this->_taxonType = ($type=='higher') ? 'higher' : 'lower';
	
	}

    private function _speciesIndexAction ()
    {

		$ranks = $this->getProjectRanks(array('idsAsIndex'=>true));

		foreach((array)$ranks['ranks'] as $key => $val) {

			if ($val['lower_taxon']==1 && $this->getTaxonType()=='lower') $d[] = $val['id'];
			if ($val['lower_taxon']==0 && $this->getTaxonType()=='higher') $d[] = $val['id'];

		}

		$this->showLowerTaxon = ($this->getTaxonType()=='lower');

		$this->getTaxonTree(array('includeOrphans' => false));
		
		$names = $taxa = (array)$this->getTreeList();
		
		if ($this->getTaxonType()=='lower') {

			$syn = $this->searchSynonyms();

			$taxa = array_merge((array)$taxa,(array)$syn);

		}

		$this->customSortArray($taxa,array('key' => 'taxon'));

		$letterToShow = $this->getFirstUsefulLetter($taxa, 'taxon');
	
		$d =  $this->makeAlphabetFromArray($taxa,'taxon',$letterToShow);

		$this->smarty->assign('usePagination',$this->_usePagination);
		
		if ($this->_usePagination) {
		
			$pagination = $this->getPagination($d['names']);

			$this->smarty->assign('prevStart', $pagination['prevStart']);
		
			$this->smarty->assign('nextStart', $pagination['nextStart']);

			$this->smarty->assign('taxa',$pagination['items']);
			
		} else {

			$this->smarty->assign('taxa',$d['names']);

			$this->smarty->assign('alphaNav',$d['alphaNav']);

		}

		$this->smarty->assign('showSpeciesIndexMenu', true);

		$this->smarty->assign('alpha',$d['alpha']);

		$this->smarty->assign('hasNonAlpha',$d['hasNonAlpha']);

		$this->smarty->assign('letter',$letterToShow);

		$this->smarty->assign('ranks',$ranks);

		$this->smarty->assign('names',$names);

		$this->smarty->assign('taxonType',$this->getTaxonType());

        $this->printPage('species_index');

	}

	private function getTaxonType ()
	{

		return isset($this->_taxonType) ? $this->_taxonType : 'lower';
	
	}	

	private function searchSynonyms($search=null)
	{

		$d['project_id'] = $this->getCurrentProjectId();
		
		if ($search) $d['synonym regexp'] = $this->makeRegExpCompatSearchString($search);

		$s = $this->models->Synonym->_get(
			array(
				'id' => $d,
				'columns' => 'taxon_id as id,synonym as label,synonym as taxon,\'synonym\' as source, concat(\'views/species/synonyms.php?id=\',taxon_id) as url'
			)
		);
		
		foreach((array)$s as $key => $val) {
		
			$s[$key]['label'] = $this->formatSpeciesEtcNames($val['label'],'syn');

		}

		return $s;
		
	}

	private function searchCommonNames($search=null)
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
						'commonname' ) .' as label,
						transliteration,
					\'common name\' as source, 
					concat(\'views/species/common.php?id=\',taxon_id) as url,
					language_id'
			)
		);

	}
	
	private function makeAlphabetFromArray($names,$field,$letter)
	{

		$a = array();
		
		$hasNonAlpha = false;
		
		$letter = strtolower($letter);
		
		foreach((array)$names as $key => $val) {
		
			$x = strtolower(substr(strip_tags($val[$field]),0,1));

			$a[$x] = $x;

			$hasNonAlpha = $hasNonAlpha || (ord($x) < 97 || ord($x) > 122);

			if (!is_null($letter) && ($x==$letter || ($letter=='#' && (ord($x) < 97 || ord($x) > 122)))) {
			
				$n[$key] = $val;
			
			}

		}

		if (!is_null($letter) && empty($n)) $letter = null;

		asort($a);

		$stopNext = $prev = $next = null;
		
		$i = 0;
		
		foreach((array)$a as $key => $val) {
		
			if ($stopNext===true) {
			
				$next = $val;
				
				break;
			
			}
		
			if ($val==$letter || ($letter=='#' && $i==0)) $stopNext = true;
		
			if ($stopNext!==true) $prev = $val;
			
			$i++;

		}
		
		$prev = ((ord($prev) < 97 || ord($prev) > 122) && !is_null($prev) ? '#' : $prev);
		
		return array(
			'alpha' => isset($a) ? $a : null,
			'names' => isset($n) ? $n : null,
			'hasNonAlpha' => $hasNonAlpha,
			'alphaNav' => array(
				'prev' => $prev,
				'next' => $next
			)
		);
	
	}	

	private function getFirstUsefulLetter($list,$field)
	{

		if (empty($list)) return;
		
		if ($this->rHasVal('letter')) {
		
			if ($this->requestData['letter']=='#') return '#';

			$l = strtolower($this->requestData['letter']);
			
			foreach((array)$list as $val) {
			
				if (strtolower(substr($val[$field],0,1))==$l) return $l;
			
			}

		}

		$d = array_slice($list,0,1);

		return strtolower(substr($d[0][$field],0,1));
	
	}


}
