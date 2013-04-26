<?php

include_once ('Controller.php');
class IndexController extends Controller
{
    public $noResultCaching = true;
    public $usedModels = array(
        'synonym', 
        'commonname'
    );
    public $usedHelpers = array();
    public $cssToLoad = array(
        'index.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js', 
            'lookup.js', 
            'dialog/jquery.modaldialog.js'
        )
    );
    private $_usePagination = false;



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($p = null)
    {
        parent::__construct($p);
        
        $this->setIndexTabs();
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



    public function indexAction ()
    {
        $this->setPageName($this->translate('Index: Species and lower taxa'));
        
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
        $this->setPageName($this->translate('Index: Higher taxa'));
        
        $this->setTaxonType('higher');
        
        $this->_speciesIndexAction();
    }



    public function commonAction ()
    {
        $this->setPageName($this->translate('Index: Common names'));
        
        $languages = $this->models->Language->_get(array(
            'id' => '*', 
            'fieldAsIndex' => 'id'
        ));
        
        $names = $this->searchCommonNames();
        
        foreach ((array) $names as $key => $val) {
            
            if ($this->rHasVal('activeLanguage')) {
                
                if ($this->requestData['activeLanguage'] == $val['language_id'] || $this->requestData['activeLanguage'] == '*') {
                    
                    $n[$key] = $val;
                    $n[$key]['language'] = $languages[$val['language_id']]['language'];
                }
            }
            else {
                
                $names[$key]['language'] = $languages[$val['language_id']]['language'];
            }
            
            $l[$val['language_id']] = $languages[$val['language_id']];
        }
        
        $this->customSortArray($l, array(
            'key' => 'language', 
            'maintainKeys' => true
        ));
        
        if ($this->rHasVal('activeLanguage')) {
            
            $activeLanguage = $this->requestData['activeLanguage'];
        }
        else {
            
            $d = current($l);
            
            $activeLanguage = $d['id'];
            
            foreach ((array) $names as $key => $val) {
                
                if ($activeLanguage == $val['language_id'])
                    $n[$key] = $val;
            }
        }
        
        $this->customSortArray($n, array(
            'key' => 'label'
        ));
        
        $letterToShow = $this->getFirstUsefulLetter($n, 'label');
        
        $d = $this->makeAlphabetFromArray(array(
            'names' => $n, 
            'field' => 'label', 
            'letter' => $letterToShow,
        	'taxonType' => null
        ));
        
        $this->smarty->assign('usePagination', $this->_usePagination);
        
        if ($this->_usePagination) {
            
            $pagination = $this->getPagination($d['names']);
            
            $this->smarty->assign('prevStart', $pagination['prevStart']);
            
            $this->smarty->assign('nextStart', $pagination['nextStart']);
            
            $this->smarty->assign('taxa', $pagination['items']);
        }
        else {
            
            $this->smarty->assign('taxa', $d['names']);
            
            $this->smarty->assign('alphaNav', $d['alphaNav']);
        }
        
        $this->smarty->assign('showSpeciesIndexMenu', true);
        
        $this->smarty->assign('alpha', $d['alpha']);
        
        $this->smarty->assign('letter', $letterToShow);
        
        $this->smarty->assign('nameLanguages', $l);
        
        $d = current($l);
        
        $this->smarty->assign('activeLanguage', $activeLanguage);
        
        $this->smarty->assign('taxonType', 'common');
        
        $this->smarty->assign('common', true);
        
        $this->printPage();
    }



    private function setTaxonType ($type)
    {
        $this->_taxonType = ($type == 'higher') ? 'higher' : 'lower';
    }



    private function _speciesIndexAction ()
    {
        $ranks = $this->getProjectRanks();
        
        foreach ((array) $ranks as $key => $val) {
            
            if ($val['lower_taxon'] == 1 && $this->getTaxonType() == 'lower')
                $d[] = $val['id'];
            if ($val['lower_taxon'] == 0 && $this->getTaxonType() == 'higher')
                $d[] = $val['id'];
        }
        
        $taxa = $this->buildTaxonTree();
//q($taxa);	// where is 17736?	
		$d = array();
		
		foreach((array)$taxa as $key => $val) {
			
			if(
				($this->getTaxonType() == 'lower' && $val["lower_taxon"]==0) ||
				($this->getTaxonType() == 'higher' && $val["lower_taxon"]==1)
			) continue;
			
			$d[$key] = $val;

		}
		
		$names = $taxa = $d;
        
        $syn = $this->searchSynonyms();
//q($names,1);      
        $taxa = array_merge((array) $taxa, (array) $syn);
        
        $this->customSortArray($taxa, array(
            'key' => 'taxon'
        ));
        
        $letterToShow = $this->getFirstUsefulLetter($taxa, 'taxon');
        
        $d = $this->makeAlphabetFromArray(array(
            'names' => $taxa, 
            'field' => 'taxon', 
            'letter' => $letterToShow,
        	'taxonType' => $this->getTaxonType()
        ));
        

        $this->smarty->assign('usePagination', $this->_usePagination);
        
        if ($this->_usePagination) {
            
            $pagination = $this->getPagination($d['names']);
            
            $this->smarty->assign('prevStart', $pagination['prevStart']);
            
            $this->smarty->assign('nextStart', $pagination['nextStart']);
            
            $this->smarty->assign('taxa', $pagination['items']);
        }
        else {
            
            $this->smarty->assign('taxa', $d['names']);
            
            $this->smarty->assign('alphaNav', $d['alphaNav']);
        }
        
        $this->smarty->assign('showSpeciesIndexMenu', true);
        
        $this->smarty->assign('alpha', $d['alpha']);
        
        $this->smarty->assign('hasNonAlpha', $d['hasNonAlpha']);
        
        $this->smarty->assign('letter', $letterToShow);
        
        $this->smarty->assign('names', $names);
        
        $this->smarty->assign('taxonType', $this->getTaxonType());
        
        $this->printPage('species_index');
    }



    private function getTaxonType ()
    {
        return isset($this->_taxonType) ? $this->_taxonType : 'lower';
    }



    private function searchSynonyms ($search = null)
    {
        $d['project_id'] = $this->getCurrentProjectId();
        
        if ($search)
            $d['synonym regexp'] = $this->makeRegExpCompatSearchString($search);
        
        $s = $this->models->Synonym->_get(array(
            'id' => $d, 
            'columns' => 'taxon_id as id,synonym as label,synonym as taxon,\'synonym\' as source, concat(\'views/species/synonyms.php?id=\',taxon_id) as url,author'
        ));
        
        foreach ((array) $s as $key => $val) {
            
            $d = $this->getTaxonById($val['id']);
            
            if (($d['lower_taxon'] == '0' && $this->getTaxonType() == 'higher') || ($d['lower_taxon'] == '1' && $this->getTaxonType() == 'lower')) {
                
                //$s[$key]['label'] = $this->formatSynonym($val['label']);
                $s[$key]['label'] = $this->formatSynonym($val['label']);

            }
            else {
                
                unset($s[$key]);
            }
        }
        
        return $s;
    }



    private function searchCommonNames ($search = null)
    {
        return $this->models->Commonname->_get(
        array(
            'where' => 'project_id  = ' . $this->getCurrentProjectId() . ($search ? ' and
							(
								commonname regexp \'' . $this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)) . '\' or
								transliteration regexp \'' . $this->models->Commonname->escapeString($this->makeRegExpCompatSearchString($search)) . '\'
							)' : ''), 
            'columns' => 'taxon_id as id,' . ($search ? '
						if(commonname regexp \'' . $this->makeRegExpCompatSearchString($search) . '\',commonname,transliteration) ' : 'commonname') . ' as label,
						transliteration,
					\'common name\' as source, 
					concat(\'views/species/common.php?id=\',taxon_id) as url,
					language_id'
        ));
    }



    private function makeAlphabetFromArray ($p)
    {
        $names = isset($p['names']) ? $p['names'] : null;
        $field = isset($p['field']) ? $p['field'] : null;
        $letter = isset($p['letter']) ? $p['letter'] : null;
        $taxonType = isset($p['taxonType']) ? $p['taxonType'] : null;

        $a = array();
        
        $hasNonAlpha = false;
        
        $letter = strtolower($letter);
        
        foreach ((array) $names as $key => $val) {
            
            if (
            	($taxonType=='higher' && isset($val['lower_taxon']) && $val['lower_taxon']=='1') ||
            	($taxonType=='lower' && isset($val['lower_taxon']) && $val['lower_taxon']=='0')
            ) continue;

            $x = strtolower(substr(strip_tags($val[$field]), 0, 1));
            
            $a[$x] = $x;
            
            $hasNonAlpha = $hasNonAlpha || (ord($x) < 97 || ord($x) > 122);
            
            if (!is_null($letter) && ($x == $letter || ($letter == '#' && (ord($x) < 97 || ord($x) > 122)))) {
                
                $n[$key] = $val;
            }
        }
        
        if (!is_null($letter) && empty($n))
            $letter = null;
        
        asort($a);
        
        $stopNext = $prev = $next = null;
        
        $i = 0;
        
        foreach ((array) $a as $key => $val) {
            
            if ($stopNext === true) {
                
                $next = $val;
                
                break;
            }
            
            if ($val == $letter || ($letter == '#' && $i == 0))
                $stopNext = true;
            
            if ($stopNext !== true)
                $prev = $val;
            
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



    private function getFirstUsefulLetter ($list, $field)
    {
        if (empty($list))
            return;

        if ($this->rHasVal('letter')) {
            
            if ($this->requestData['letter'] == '#')
                return '#';
            
            $l = strtolower($this->requestData['letter']);
            
            foreach ((array) $list as $val) {
               
                if (strtolower(substr($val[$field], 0, 1)) == $l)
                    return $l;
            }
        }
        
        $d = array_slice($list, 0, 1);
        
        return strtolower(substr($d[0][$field], 0, 1));
    }



    private function setIndexTabs ()
    {
        // Check if results have been stored in session; if so return
        if (isset($_SESSION['app']['user']['indexModule']['hasSpecies']))
            return;
            
            // Check taxa
        $_SESSION['app']['user']['indexModule']['hasSpecies'] = $_SESSION['app']['user']['indexModule']['hasHigherTaxa'] = 0;
        $taxa = $this->buildTaxonTree();
        foreach ($taxa as $taxon) {
            if ($taxon['lower_taxon'] == 1 && $taxon['is_empty'] == 0)
                $_SESSION['app']['user']['indexModule']['hasSpecies'] = 1;
            if ($taxon['lower_taxon'] == 0 && $taxon['is_empty'] == 0)
                $_SESSION['app']['user']['indexModule']['hasHigherTaxa'] = 1;
            if ($_SESSION['app']['user']['indexModule']['hasSpecies'] == 1 && $_SESSION['app']['user']['indexModule']['hasHigherTaxa'] == 1)
                break;
        }
        
        // Check common names
        $_SESSION['app']['user']['indexModule']['hasCommonNames'] = ($this->countCommonNames() > 0 ? 1 : 0);
    }



    private function countCommonNames ()
    {
        $r = $this->models->Commonname->_get(array(
            'where' => 'project_id = ' . $this->getCurrentProjectId(), 
            'columns' => 'COUNT(1) AS c'
        ));
        return $r[0]['c'];
    }
}
