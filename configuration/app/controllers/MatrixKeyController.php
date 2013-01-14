<?php

include_once ('Controller.php');
class MatrixKeyController extends Controller
{
    public $usedModels = array(
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'commonname', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'matrix_variation', 
        'nbc_extras', 
        'taxa_relations', 
        'variation_relations'
    );
    public $usedHelpers = array();
    public $controllerPublicName = 'Matrix key';
    public $cssToLoad = array(
        'basics.css', 
        'lookup.css', 
        'prettyPhoto/prettyPhoto.css', 
        'dialog/jquery.modaldialog.css', 
        'matrix.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js', 
            'matrix.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
            'dialog/jquery.modaldialog.js'
        ), 
        'IE' => array()
    );



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($p = null)
    {
        parent::__construct($p);
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
        
        //unset($_SESSION['app']['user']['search']['hasSearchResults']);
        $this->checkMatrixIdOverride();
        
        $matrix = $this->getCurrentMatrix();
        
        if (!isset($matrix)) {
            
            $this->storeHistory = false;
            
            $matrices = $this->getMatrices();
            
            $matrix = array_shift($matrices);
            
            $this->setCurrentMatrix($matrix['id']);
        }
        
        $this->setStoreHistory(false);
        
        $this->redirect('identify.php');
    }



    public function matricesAction ()
    {
        $matrices = $this->getMatrices();
        
        if (count((array) $matrices) == 0) {
            
            $this->addError($this->translate('No matrices have been defined.'));
        }
        else if (count((array) $matrices) == 1) {
            
            $this->storeHistory = false;
            
            $matrix = array_shift($matrices);
            
            $this->setCurrentMatrix($matrix['id']);
            
            $this->redirect('identify.php');
        }
        else {
            
            $this->smarty->assign('matrices', $matrices);
            
            $this->smarty->assign('currentMatrixId', $this->getCurrentMatrixId());
        }
        
        $this->printPage();
    }



    public function useMatrixAction ()
    {
        if ($this->rHasId()) {
            
            $this->storeHistory = false;
            
            $this->setCurrentMatrix($this->requestData['id']);
            
            $this->redirect('identify.php');
        }
        else {
            
            $this->redirect('matrices.php');
        }
    }



    private function nbcGetTaxaScores($state)
    {
    
        if (empty($state)) return;
        
        // store the newly selected state
        $this->stateMemoryStore($state);
    
        // get all stored selected states
        foreach((array)$this->stateMemoryRecall() as $val)
            $states[] = $val['val'];
    
        // calculate scores
        $taxa = $this->getTaxaScores($states,false);
    
        // onky keep the 100% scores, no partial matches
        $d = array();
        foreach((array)$taxa as $key => $val) {
            if ($val['s']==100)
                $d[]=$val;
        }

        return $d;
    
    }
    
    
    public function identifyAction ()
    {
        $this->checkMatrixIdOverride();
        
        $matrix = $this->getCurrentMatrix();
        
        if (!isset($matrix)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
        
        
        /* NBC */
        if ($this->rHasVal('state')) {
            
            $this->nbcGetTaxaScores($this->requestData['state']);
            $this->smarty->assign('groups', $this->getCharacterGroups());
            $this->smarty->assign('nbcImageRoot', 'http://determinatie.nederlandsesoorten.nl/images/');
            $this->smarty->assign('nbcStart', $this->getSessionSetting('nbcStart'));
            $this->smarty->assign('nbcSimilar', $this->getSessionSetting('nbcSimilar'));
		/* /NBC */            
        } else {
        	
            $this->smarty->assign('taxa', $this->getTaxaInMatrix());
            
            $this->smarty->assign('matrices', $this->getMatricesInMatrix());
            
            $this->smarty->assign('matrixCount', $this->getMatrixCount());
            
            $this->smarty->assign('matrix', $matrix);
            
            
        }

        
        
        
        
        $this->smarty->assign('function', 'Identify');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));
        
        $this->smarty->assign('storedStates', $this->stateMemoryRecall());
        
        $this->smarty->assign('storedShowState', $this->showStateRecall());
        
        $this->smarty->assign('characteristics', $this->getCharacteristics());
        
        
        $this->printPage();
    }



    public function examineAction ()
    {
        $this->checkMatrixIdOverride();
        
        $matrix = $this->getCurrentMatrix();
        
        if (!isset($matrix)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
        $this->smarty->assign('function', 'Examine');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": examine'), $matrix['name']));
        
        $this->smarty->assign('taxa', $this->getTaxaInMatrix());
        
        $this->smarty->assign('matrixCount', $this->getMatrixCount());
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->smarty->assign('examineSpeciesRecall', $this->examineSpeciesRecall());
        
        $this->printPage();
    }



    public function compareAction ()
    {
        $this->checkMatrixIdOverride();
        
        $matrix = $this->getCurrentMatrix();
        
        if (!isset($matrix)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
        $this->smarty->assign('function', 'Compare');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": compare'), $matrix['name']));
        
        $this->smarty->assign('taxa', $this->getTaxaInMatrix());
        
        $this->smarty->assign('matrixCount', $this->getMatrixCount());
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->smarty->assign('compareSpeciesRecall', $this->compareSpeciesRecall());
        
        $this->printPage();
    }



    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action') || !$this->rHasId()) {
            
            $this->smarty->assign('returnText', 'error');
        }
        else if ($this->rHasVal('action', 'get_states')) {
            
            $this->smarty->assign('returnText', json_encode($this->getCharacteristicStates($this->requestData['id'])));
        }
        else if ($this->rHasVal('action', 'get_taxa')) {
            
            $this->stateMemoryStore($this->requestData['id']);
            
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxaScores($this->requestData['id'], isset($this->requestData['inc_unknowns']) ? ($this->requestData['inc_unknowns'] == '1') : false)));
        }
        else if ($this->rHasVal('action', 'get_taxon_states')) {
            
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonStates($this->requestData['id'])));
        }
        else if ($this->rHasVal('action', 'compare')) {
            
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonComparison($this->requestData['id'])));
        }
        else if ($this->rHasVal('action', 'store_showstate_results')) {
            
            $this->showStateStore('results');
        }
        if ($this->rHasVal('action', 'store_showstate_pattern')) {
            
            $this->showStateStore('pattern');
        }
        else if ($this->rHasVal('action', 'store_examine_val')) {
            
            $this->examineSpeciesStore($this->requestData['id']);
        }
        else if ($this->rHasVal('action', 'store_compare_vals')) {
            
            $this->compareSpeciesStore($this->requestData['id']);
        }
        // "nbc-type" functions below
        else if ($this->rHasVal('action', 'save_session_setting')) {
            
            $this->saveSessionSetting($this->requestData['setting']);
        }
        else if ($this->rHasVal('action', 'get_session_setting')) {
            
            $this->smarty->assign('returnText', $this->getSessionSetting($this->requestData['name']));
        }
        else if ($this->rHasVal('action', 'get_formatted_states')) {
            
            $this->getFormattedStates($this->requestData['id']);
            $tpl = 'formatted_states';
        }
        else if ($this->rHasVal('action', 'get_results_nbc')) {
            
            if (isset($this->requestData['params']) && $this->requestData['params']['action'] == 'similar') {
                
                $results = $this->getSimilarNBC($this->requestData['params']);
            }
            else {
                
                $results = $this->getResultsNBC();
            }
            
            $this->smarty->assign('returnText', 
            json_encode(
            array(
                'results' => $results, 
                'count' => array(
                    'results' => count((array) $results), 
                    'all' => count((array) $results), 
                    'perpage' => 16
                )
            )));
        }
        
        $this->allowEditPageOverlay = false;
        
        $this->printPage(isset($tpl) ? $tpl : null);
    }



    public function getCurrentMatrixId ()
    {
        return isset($_SESSION['app']['user']['matrix']['active']) ? $_SESSION['app']['user']['matrix']['active']['id'] : null;
    }



    public function cacheAllTaxaInMatrix ()
    {
        $mt = $this->models->MatrixTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'taxon_id,matrix_id', 
            'order' => 'matrix_id'
        ));
        
        $tree = $this->getTreeList();
        
        $taxa = array();
        
        foreach ((array) $mt as $key => $val) {
            
            if (!isset($tree[$val['taxon_id']]))
                continue;
            
            $d = $tree[$val['taxon_id']];
            
            $taxa[$val['matrix_id']][] = array(
                'id' => $d['id'], 
                'h' => $d['is_hybrid'], 
                'l' => $d['taxon'], 
                'type' => 'tx'
            );
        }
        
        foreach ((array) $taxa as $key => $val) {
            $dummy = array();
            foreach ((array) $val as $tVal) {
                $dummy[] = $tVal;
            }
            $this->customSortArray($dummy, array(
                'key' => 'taxon', 
                'case' => 'i'
            ));
            
            if (!$this->getCache('matrix-taxa-' . $key))
                $this->saveCache('matrix-taxa-' . $key, isset($dummy) ? $dummy : null);
        }
    }





    private function getMatrices ()
    {
        $m = $this->getCache('matrix-matrices');
        
        if (!$m) {
            
            $m = $this->models->Matrix->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'got_names' => 1
                ), 
                'fieldAsIndex' => 'id', 
                'columns' => 'id,got_names,\'matrix\' as type, `default`'
            ));
            
            foreach ((array) $m as $key => $val) {
                
                $mn = $this->models->MatrixName->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $val['id'], 
                        'language_id' => $this->getCurrentLanguageId()
                    ), 
                    'columns' => 'name'
                ));
                
                $m[$key]['name'] = $mn[0]['name'];
            }
            
            $this->customSortArray($m, array(
                'key' => 'default', 
                'dir' => 'desc', 
                'case' => 'i', 
                'maintainKeys' => true
            ));
            
            $this->saveCache('matrix-matrices', $m);
        }
        
        return $m;
    }



    private function getTaxaInMatrix ($matrixId = null)
    {
        $matrixId = is_null($matrixId) ? $this->getCurrentMatrixId() : $matrixId;
        
        if (empty($matrixId))
            return;
        
        $taxa = $this->getCache('matrix-taxa-' . $matrixId);
        
        if (!$taxa) {
            
            $mt = $this->models->MatrixTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $matrixId
                ), 
                'columns' => 'taxon_id'
            ));
            

            $tree = $this->getTreeList(array(
                'includeEmpty' => ($this->getSetting('matrix_allow_empty_species') == '1')
            ));
            
            foreach ((array) $mt as $key => $val) {
                
                $d = $tree[$val['taxon_id']];
                
                $taxa[] = array(
                    'id' => $d['id'], 
                    'h' => $d['is_hybrid'], 
                    'l' => $this->formatTaxon($d), 
                    'type' => 'tx'
                );
            }
            
            $this->customSortArray($taxa, array(
                'key' => 'taxon', 
                'case' => 'i'
            ));
            
            $this->saveCache('matrix-taxa-' . $matrixId, isset($taxa) ? $taxa : null);
        }
        
        return $taxa;
    }
   
 
    private function checkMatrixIdOverride ()
    {
        if ($this->rHasVal('mtrx'))
            $this->setCurrentMatrix($this->requestData['mtrx']);
    }



    private function getCurrentMatrix ()
    {
        return isset($_SESSION['app']['user']['matrix']['active']) ? $_SESSION['app']['user']['matrix']['active'] : null;
    }



    private function setCurrentMatrix ($id)
    {
        $_SESSION['app']['user']['matrix']['active'] = $this->getMatrix($id);
    }



    private function getMatrixCount ()
    {
        $m = $this->getMatrices();
        
        return count((array) $m);
    }



    private function getMatrix ($id)
    {
        if (!isset($id))
            return;
        
        $m = $this->getMatrices();
        
        return isset($m[$id]) ? $m[$id] : null;
    }



    private function getMatricesInMatrix ()
    {
        $mts = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'ref_matrix_id is not' => 'null'
            ), 
            'columns' => 'distinct ref_matrix_id,\'matrix\' as type'
        ));
        
        foreach ((array) $mts as $key => $val) {
            
            $d = $this->getMatrix($val['ref_matrix_id']);
            
            if (isset($d)) {
                
                $matrices[$val['ref_matrix_id']] = array(
                    'id' => $d['id'], 
                    'l' => $d['name'], 
                    'type' => 'mtx'
                );
            }
        }
        
        return isset($matrices) ? $matrices : null;
    }



    private function getImageDimensions ($state)
    {
        if (isset($state['type']) && $state['type'] == 'media') {
            
            $f = $_SESSION['app']['project']['urls']['uploadedMedia'] . $state['file_name'];
            
            if (!file_exists($f) || is_dir($f))
                return null;
            
            $f = getimagesize($f);
            
            if ($f == false)
                return null;
            
            return array(
                'w' => $f[0], 
                'h' => $f[1]
            );
        }
        else {
            
            return null;
        }
    }



    private function getCharacteristicStates ($id)
    {
        if (!isset($id) && !isset($state))
            return;
        

        $cs = $this->models->CharacteristicState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'characteristic_id' => $id
            ), 
            'order' => 'show_order', 
            'columns' => 'id,characteristic_id,file_name,lower,upper,mean,sd,got_labels,show_order'
        ));
        
        foreach ((array) $cs as $key => $val) {
            
            $d = $this->getCharacteristic($val['characteristic_id']);
            $cs[$key]['type'] = $d['type'];
            $cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id']);
            $cs[$key]['text'] = $this->getCharacteristicStateLabelOrText($val['id'], 'text');
            $cs[$key]['img_dimensions'] = $this->getImageDimensions($cs[$key]);
        }
        
        return $cs;
    }



    private function getCharacteristicState ($id)
    {
        if (!isset($id))
            return;
        
        $cs = $this->models->CharacteristicState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $id
            ), 
            'columns' => 'id,characteristic_id,file_name,lower,upper,mean,sd,got_labels', 
            'order' => 'show_order'
        ));
        
        $cs[0]['label'] = $this->getCharacteristicStateLabelOrText($cs[0]['id']);
        
        return $cs[0];
    }



    private function getCharacteristicStateLabelOrText ($id, $type = 'label')
    {
        if (!isset($id))
            return;
        
        $cls = $this->models->CharacteristicLabelState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'state_id' => $id, 
                'language_id' => $this->getCurrentLanguageId()
            ), 
            'columns' => 'text,label'
        ));
        
        return $type == 'text' ? $cls[0]['text'] : $cls[0]['label'];
    }



    private function calculateScore ($p)
    {
        
        $states = $p['states'];
        $item = $p['item'];	// taxon, variations or matrix
        $type = $p['type'];
        $incUnknowns = isset($p['incUnknowns']) ? $p['incUnknowns'] : false;
        
        $item['hits'] = 0;
        
        // go through all states that the user has chosen
        foreach ((array) $states as $sKey => $sVal) {

            // format [f (range or distro)|c (other)]:[charid]:[value]([: + or - times standard dev (distro only)])
            $d = explode(':', $sVal);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;
            
            // if "unknowns" should be included, taxa that have no state for a given character get scored as a hit
            // (only relevant for taxa and variations)
            if ($incUnknowns && $type != 'matrix' && isset($charId)) {
                
                $s = array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $this->getCurrentMatrixId(), 
                        'characteristic_id' => $charId
                    );
                
                if ($type == 'taxon')
                    $s['taxon_id'] = $item['id'];
				else
			    if ($type == 'variation')
			        $s['variation_id'] = $item['id'];
			    else
			        continue;
                
                $mts = $this->models->MatrixTaxonState->_get(
	                array(
	                    'id' => $s, 
	                    'columns' => 'count(*) as total'
	                ));
                
                if ($mts[0]['total'] == 0) {
                    
                    $item['hits']++;
                    
                    continue;
                }
            }
            
            // ranges and distributions have format f:charid:value (for range or distro)[: + or - times standard dev (distro only)]
            if (isset($d[0]) && $d[0] === 'f') {
                
                if (isset($d[3]))
                    $sd = $d[3];
                
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $charId
                );
                
                if (isset($sd)) {
                    
                    $d['mean >=#'] = '(' . strval(intval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(intval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                else {
                    
                    $d['lower <='] = intval($value);
                    $d['upper >='] = intval($value);
                }
                
                $cs = $this->models->CharacteristicState->_get(array(
                    'id' => $d
                ));
                
                $hasState = false;
                
                foreach ((array) $cs as $cKey => $cVal) {
                    
                    $d = array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $this->getCurrentMatrixId(), 
                        'state_id' => $cVal['id']
                    );
                    
                    if ($type == 'taxon')
                        $d['taxon_id'] = $item['id'];
                    else
                    if ($type == 'variation')
                        $d['variation_id'] = $item['id'];
                    else
					if ($type == 'matrix')
                        $d['ref_matrix_id'] = $item['id'];
                    else
                        continue;
                    

                    $mts = $this->models->MatrixTaxonState->_get(array(
                        'id' => $d, 
                        'columns' => 'count(*) as total'
                    ));
                    
                    if ($mts[0]['total'] > 0)
                        $hasState = true;
                }
                
                if ($hasState)
                    $item['hits']++;
            }
            else if (isset($d[0]) && $d[0] === 'c') {
                
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $this->getCurrentMatrixId(), 
                    'state_id' => $value
                );
                
                if ($type == 'taxon')
                    $d['taxon_id'] = $item['id'];
                else
                    if ($type == 'variation')
                    $d['variation_id'] = $item['id'];
                else
                    if ($type == 'matrix')
                    $d['ref_matrix_id'] = $item['id'];
                else
                    continue;
                
                
                $mts = $this->models->MatrixTaxonState->_get(array(
                    'id' => $d
                ));

                if ($mts)
                    $item['hits']++;
            }
        }
        
        $item['s'] = round(($item['hits'] / count((array) $states)) * 100);
        
        unset($item['hits']);
        
        return $item;
    }



    private function getTaxaScores ($states, $incUnknowns = false)
    {
        $taxa = $this->getTaxaInMatrix();
        $mtcs = $this->getMatricesInMatrix();
        

/* NBC */
$vars = $this->getVariationsInMatrix('short');

if ($states == -1)
	return array_merge((array) $taxa, (array) $mtcs, (array) $vars);

		if ($states == -1)
            return array_merge((array) $taxa, (array) $mtcs);
        
        
        foreach ((array) $taxa as $key => $val)
            $taxa[$key] = $this->calculateScore(array(
                'states' => $states, 
                'item' => $val, 
                'type' => 'taxon', 
                'incUnknowns' => $incUnknowns
            ));
        
        $matrices = array();
        
        foreach ((array) $mtcs as $key => $val) {
            
            $d = $this->calculateScore(array(
                'states' => $states, 
                'item' => $val, 
                'type' => 'matrix'
            ));
            
            $d['type'] = 'matrix';
            $matrices[$key] = $d;
        }

$variations = array();
   
foreach ((array) $vars as $key => $val) {
	$d = $this->calculateScore(
		array(
			'states' => $states,
			'item' => $val,
			'type' => 'variation'
		)
	);
        
	$d['type'] = 'variation';
	$variations[$key] = $d;
}     
        
        
        
        //$results = array_merge((array) $taxa, (array) $matrices);
        $results = array_merge((array) $taxa, (array) $matrices, (array) $variations);
        
        usort($results, array(
            $this, 
            'sortMatrixScores'
        ));
        
        //array_walk($results, create_function('&$v', '$v["s"] = $v["s"]."%";'));	

        return $results;
    }



    private function sortMatrixScores ($a, $b)
    {
        if ($a['s'] == $b['s']) {
            
            $aa = strtolower(strip_tags($a['l']));
            $bb = strtolower(strip_tags($b['l']));
            
            if ($aa == $bb)
                return 0;
            
            return ($aa < $bb) ? -1 : 1;
        }
        
        return ($a['s'] > $b['s']) ? -1 : 1;
    }



    private function getCharacteristicHValue ($charId, $states = null)
    {
        $states = is_null($states) ? $this->getCharacteristicStates($charId) : $states;
        
        $taxa = array();
        
        $tot = 0;
        
        foreach ((array) $states as $key => $val) {
            
            $states[$key]['n'] = 0;
            
            $mts = $this->models->MatrixTaxonState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $this->getCurrentMatrixId(), 
                    'characteristic_id' => $charId, 
                    'state_id' => $val['id']
                ), 
                'columns' => 'distinct taxon_id'
            ));
            
            foreach ((array) $mts as $val2) {
                
                @$taxa[$val2['taxon_id']]['states']++;
                $states[$key]['n']++;
                $tot++;
            }
        }
        
        if ($tot == 0)
            return 0;
        
        $hValue = 0;
        
        foreach ((array) $states as $val)
            $hValue += ($val['n'] / $tot) * log($val['n'] / $tot, 10);
        
        $hValue = is_nan($hValue) ? 0 : -1 * $hValue;
        
        $uniqueTaxa = 0;
        
        foreach ((array) $taxa as $val)
            if ($val['states'] == 1)
                $uniqueTaxa++;
        
        $corrFactor = $uniqueTaxa / $tot;
        
        return $hValue * ($this->controllerSettings['useCorrectedHValue'] == true ? $corrFactor : 1);
    }



    private function getCharacteristics ()
    {
        $mc = $this->models->CharacteristicMatrix->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'columns' => 'characteristic_id,show_order', 
            'order' => 'show_order'
        ));
        
        foreach ((array) $mc as $key => $val) {
            
            $d = $this->getCharacteristic($val['characteristic_id']);
            
            if ($d) {
                $states = $this->getCharacteristicStates($val['characteristic_id']);
                $d['sort_by']['alphabet'] = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $d['label']));
                $d['sort_by']['separationCoefficient'] = -1 * $this->getCharacteristicHValue($val['characteristic_id'], $states); // -1 to avoid asc/desc hassles in JS-sorting
                $d['sort_by']['characterType'] = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $d['type']));
                $d['sort_by']['numberOfStates'] = -1 * count((array) $states); // -1 to avoid asc/desc hassles in JS-sorting
                $d['sort_by']['entryOrder'] = intval($val['show_order']);
                $d['states'] = $states;
                $c[] = $d;
            }
        }

        return isset($c) ? $c : null;
    }



    private function getCharacteristicType ($type)
    {
        foreach ((array) $this->controllerSettings['characteristicTypes'] as $key => $val) {
            
            if ($val['name'] == $type)
                return $val;
        }
        
        return $type;
    }



    private function getCharacteristic ($id)
    {
        $c = $this->models->Characteristic->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $id
            ), 
            'columns' => 'id,type,got_labels'
        ));
        
        if (!isset($c))
            return;
        
        $char = $c[0];
        
        $cl = $this->models->CharacteristicLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->getCurrentLanguageId(), 
                'characteristic_id' => $id
            )
        ));
        
        $char['label'] = $cl[0]['label'];
        
        $char['type'] = $this->getCharacteristicType($char['type']);
        
        return $char;
    }



    private function getTaxonStates ($id)
    {
        $mts = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'characteristic_id,state_id', 
            'fieldAsIndex' => 'characteristic_id'
        ));
        
        foreach ((array) $mts as $key => $val) {
            
            $d = $this->getCharacteristic($val['characteristic_id']);
            
            $mts[$key]['characteristic'] = $d['label'];
            
            $mts[$key]['type'] = $d['type'];
            
            $mts[$key]['state'] = $this->getCharacteristicState($val['state_id']);
        }
        
        return $mts;
    }



    private function getTaxonComparison ($id)
    {
        if (empty($id[0]) || empty($id[1]))
            return;
        
        $c = $this->getCharacteristics();
        $t1 = $this->getTaxonStates($id[0]);
        $t2 = $this->getTaxonStates($id[1]);
        
        $both = $neither = $t1_count = $t2_count = 0;
        
        foreach ((array) $c as $key => $val) {
            
            if (isset($t1[$val['id']]) && isset($t2[$val['id']]) && ($t1[$val['id']]['state_id'] == $t2[$val['id']]['state_id'])) {
                
                $both++;
            }
            else {
                
                if (isset($t1[$val['id']]))
                    $t1_count++;
                elseif (isset($t2[$val['id']]))
                    $t2_count++;
                else
                    $neither++;
            }
        }
        

        $overlap = $states1 = $states2 = array();
        
        foreach ((array) $t1 as $key => $val) {
            
            if (isset($t2[$key]) && $val == $t2[$key]) {
                
                $overlap[] = $val;
            }
            else {
                
                $states1[] = $val;
            }
        }
        
        foreach ((array) $t2 as $key => $val) {
            
            if (!isset($t1[$key]) || $val != $t1[$key])
                $states2[] = $val;
        }
        
        //q($overlap,1);
        



        return array(
            'taxon_1' => $this->getTaxonById($id[0]), 
            'taxon_2' => $this->getTaxonById($id[1]), 
            'count_1' => $t1_count, 
            'count_2' => $t2_count, 
            'neither' => $neither, 
            'both' => $both, 
            'total' => count((array) $c), 
            'coefficients' => $this->calculateDistances($t1_count, $t2_count, $both, $neither), 
            'taxon_states_1' => $states1, 
            'taxon_states_2' => $states2, 
            'taxon_states_overlap' => $overlap
        );
    }



    private function calculateDistances ($u1, $u2, $co, $ca)
    {
        $prec = 3;
        
        return array(
            0 => array(
                'name' => $this->translate('Simple dissimilarity coefficient'), 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - (($co + $ca) / ($u1 + $u2 + $co + $ca)), $prec)
            ), 
            1 => array(
                'name' => 'Russel & Rao', 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - ($co / ($u1 + $u2 + $co + $ca)), $prec)
            ), 
            2 => array(
                'name' => 'Rogers & Tanimoto', 
                'value' => ($co + $ca + (2 * $u1) + (2 * $u2)) == 0 ? 'NaN' : round(1 - (($co + $ca) / ($co + $ca + (2 * $u1) + (2 * $u2))), $prec)
            ), 
            3 => array(
                'name' => 'Harmann', 
                'value' => ($u1 + $u2 + $co + $ca) == 0 ? 'NaN' : round(1 - ((($co + $ca - $u1 - $u2) / ($u1 + $u2 + $co + $ca)) + 1) / 2, $prec)
            ), 
            4 => array(
                'name' => 'Sokal & Sneath', 
                'value' => (2 * ($co + $ca) + $u1 + $u2) == 0 ? 'NaN' : round(1 - ((2 * ($co + $ca) / (2 * ($co + $ca) + $u1 + $u2))), $prec)
            ), 
            5 => array(
                'name' => 'Jaccard', 
                'value' => ($co + $u1 + $u2) == 0 ? 'NaN' : round(1 - ($co / ($co + $u1 + $u2)), $prec)
            ), 
            6 => array(
                'name' => 'Czekanowski', 
                'value' => ((2 * $co) + $u1 + $u2) == 0 ? 'NaN' : round(1 - ((2 * $co) / ((2 * $co) + $u1 + $u2)), $prec)
            ), 
            7 => array(
                'name' => 'Kulczyski', 
                'value' => (($co + $u1) == 0 || ($co + $u2) == 0) ? 'NaN' : round(1 - (($co / 2) * ((1 / ($co + $u1)) + (1 / ($co + $u2)))), $prec)
            ), 
            8 => array(
                'name' => 'Ochiai', 
                'value' => ($co + $u1) * ($co + $u2) == 0 ? 'NaN' : round(1 - ($co / sqrt(($co + $u1) * ($co + $u2))), $prec)
            )
        );
    }



    private function showStateStore ($state)
    {
        $_SESSION['app']['user']['matrix']['storesShowState'][$this->getCurrentMatrixId()] = $state;
    }



    private function showStateRecall ()
    {
        return isset($_SESSION['app']['user']['matrix']['storesShowState'][$this->getCurrentMatrixId()]) ? $_SESSION['app']['user']['matrix']['storesShowState'][$this->getCurrentMatrixId()] : 'pattern';
    }



    private function examineSpeciesStore ($id)
    {
        $_SESSION['app']['user']['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }



    private function examineSpeciesRecall ()
    {
        return isset($_SESSION['app']['user']['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app']['user']['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] : null;
    }



    private function compareSpeciesStore ($id)
    {
        $_SESSION['app']['user']['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }



    private function compareSpeciesRecall ()
    {
        return isset($_SESSION['app']['user']['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app']['user']['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] : null;
    }



    private function stateMemoryStore ($data)
    {
        if ($data == '-1') {
            unset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()]);
        }
        else {
            $_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()] = $data;
        }
    }



    private function stateMemoryRecall ()
    {
        if (isset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()])) {
            
            foreach ((array) $_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()] as $key => $val) {
                
                $states[$key]['val'] = $val;
                
                $d = explode(':', $val);
                
                if ($d[0] == 'c' && isset($d[2])) {
                    
                    $d = $this->getCharacteristicState($d[2]);
                    
                    $states[$key]['type'] = 'c';
                    $states[$key]['id'] = $d['id'];
                    $states[$key]['characteristic_id'] = $d['characteristic_id'];
                    $states[$key]['label'] = $d['label'];
                }
            }
            
            return $states;
        }
        else {
            
            return null;
        }
    }



    private function getCharacterGroups ($mId = null)
    {
        $mId = isset($mId) ? $mId : $this->getCurrentMatrixId();
        
        $cg = $this->models->Chargroup->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $mId
            ), 
            'order' => 'show_order', 
            'columns' => 'id,matrix_id,label,show_order'
        ));
        
        foreach ((array) $cg as $key => $val) {
            $cg[$key]['label'] = $this->getCharacterGroupLabel($val['id'], $this->getCurrentLanguageId());
            
            $cc = $this->models->CharacteristicChargroup->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'chargroup_id' => $val['id']
                ), 
                'order' => 'show_order'
            ));
            
            foreach ((array) $cc as $cVal) {
                $cg[$key]['chars'][] = $this->getCharacteristic($cVal['characteristic_id']);
            }
        }
        
        return $cg;
    }



    private function getCharacterGroupLabel ($id, $lId)
    {
        $cl = $this->models->ChargroupLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'chargroup_id' => $id, 
                'language_id' => $lId
            )
        ));
        
        return $cl[0]['label'];
    }
    


    /* "NBC-style" functions below */
    private function saveSessionSetting ($setting)
    {
        if (!isset($setting['name']))
            return;
        
        if (empty($setting['value']))
            unset($_SESSION['app']['user']['matrix']['settings'][$setting['name']]);
        else
            $_SESSION['app']['user']['matrix']['settings'][$setting['name']] = $setting['value'];
    }



    private function getSessionSetting ($name)
    {
        if (!isset($name) || !isset($_SESSION['app']['user']['matrix']['settings'][$name]))
            return;
        
        return $_SESSION['app']['user']['matrix']['settings'][$name];
    }



    private function getFormattedStates ($id)
    {
        $c = $this->getCharacteristic($id);
        $s = $this->getCharacteristicStates($id);
        
		$c['prefix'] = ($c['type']=='media' || $c['type']=='text' ? 'c' : 'f');
        
        $this->smarty->assign('stateImagesPerRow', 4);
        $this->smarty->assign('c', $c);
        $this->smarty->assign('s', $s);
    }



    public function getVariations ($tId = null)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId()
        );
        
        if (isset($tId))
            $d['taxon_id'] = $tId;
        
        $tv = $this->models->TaxonVariation->_get(array(
            'id' => $d, 
            'columns' => 'id,taxon_id,label', 
            'order' => 'label'
        ));
        
        foreach ((array) $tv as $key => $val) {
            
            $tv[$key]['taxon'] = $this->getTaxonById($val['taxon_id']);
            
            $tv[$key]['labels'] = $this->models->VariationLabel->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $val['id']
                ), 
                'columns' => 'id,language_id,label,label_type'
            ));
        }
        
        return $tv;
    }



    private function getVariationsInMatrix ($style='long')
    {
        $v = $this->getVariations();
        
        $mv = $this->models->MatrixVariation->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'fieldAsIndex' => 'variation_id'
        ));
        
        $d = array();
        foreach ((array) $v as $val) {
            if (isset($mv[$val['id']])) {
                if ($style=='short')
                    $d[] = array(
                    	'id' => $val['id'],
                    	'h' => $val['taxon']['is_hybrid'], 
                    	'l' => $val['label'],
                        'type' => 'var',
                    );
				else
	                $d[] = $val;
            }
        }
        
        
        $this->customSortArray($d, array(
            'key' => ($style=='short' ? 'l' : 'label')
        ));
        
        return $d;
    }



    public function getVariation ($id)
    {
        $tv = $this->models->TaxonVariation->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $id
            ), 
            'columns' => 'id,taxon_id,label'
        ));
        
        $tv[0]['labels'] = $this->models->VariationLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'variation_id' => $id
            ), 
            'columns' => 'id,language_id,label,label_type'
        ));
        
        return $tv[0];
    }



    private function getRelatedEntities ($p)
    {
        $tId = isset($p['tId']) ? $p['tId'] : null;
        $vId = isset($p['vId']) ? $p['vId'] : null;
        $includeSelf = isset($p['includeSelf']) ? $p['includeSelf'] : false;
        
        $rel = null;
        
        if ($tId) {
            
            $rel = $this->models->TaxaRelations->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $tId
                )
            ));
            
            if ($includeSelf)
                array_unshift($rel, array(
                    'relation_id' => $tId, 
                    'ref_type' => 'taxon'
                ));
            
            foreach ((array) $rel as $key => $val) {
                
                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                    $rel[$key]['taxon_id'] = $d['taxon_id'];
                }
            }
        }
        else if ($vId) {
            
            $rel = $this->models->VariationRelations->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $vId
                )
            ));
            
            if ($includeSelf)
                array_unshift($rel, array(
                    'relation_id' => $vId, 
                    'ref_type' => 'variation'
                ));
            

            foreach ((array) $rel as $key => $val) {
                
                if ($val['ref_type'] == 'taxon') {
                    $rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id']));
                }
                else {
                    $d = $this->getVariation($val['relation_id']);
                    $rel[$key]['label'] = $d['label'];
                }
            }
        }
        
        return $rel;
    }



    private function createDatasetEntry ($p)
    {
        $val = isset($p['val']) ? $p['val'] : null;
        $nbc = isset($p['nbc']) ? $p['nbc'] : null;
        $label = isset($p['label']) ? $p['label'] : null;
        $gender = isset($p['gender']) ? $p['gender'] : null;
        $related = isset($p['related']) ? $p['related'] : null;
        $type = isset($p['type']) ? $p['type'] : null;
        $inclRelated = isset($p['inclRelated']) ? $p['inclRelated'] : false;
        $highlight = isset($p['highlight']) ? $p['highlight'] : false;
        
        $d = array(
            'i' => $val['id'], 
            'l' => $label, 
            'y' => $type, 
            's' => strip_tags(($type == 't' ? $val['l'] : $val['taxon']['taxon'])), 
            'm' => isset($nbc['url_image']) ? $nbc['url_image']['value'] : 'http://determinatie.nederlandsesoorten.nl/images/noimage_Boktorren%20van%20NL.gif', 
            'p' => isset($nbc['source']) ? $nbc['source']['value'] : null, 
            'u' => isset($nbc['url_soortenregister']) ? $nbc['url_soortenregister']['value'] : null, 
            'r' => count((array) $related),
        	'h' => $highlight
        );

        if (isset($val['taxon_id']))
            $d['t'] = $val['taxon_id'];
        if (isset($gender))
            $d['g'] = $gender;
        
        if ($inclRelated)
            $d['related'] = $rel;
        
        return $d;
    }



    private function getCompleteDatasetNBC ($p = null)
    {
        $inclRelated = isset($p['inclRelated']) ? $p['inclRelated'] : false;
        $tId = isset($p['tId']) ? $p['tId'] : false;
        $vId = isset($p['vId']) ? $p['vId'] : false;
        
        $var = $this->getVariationsInMatrix();
        
        foreach ((array) $var as $val) {
            
            if ($vId && $val['id'] != $vId)
                continue;
            
            $nbc = $this->models->NbcExtras->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'ref_id' => $val['id'], 
                    'ref_type' => 'variation'
                ), 
                'columns' => 'name,value', 
                'fieldAsIndex' => 'name'
            ));
            
            $label = $val['label'];
            
            $gender = null;
            
            if (preg_match('/\s(male|female)$/', $label, $matches)) {
                $gender = $matches[1];
                $label = preg_replace('/(' . $matches[0] . ')$/', '', $label);
            }
            
            $res[] = $this->createDatasetEntry(
            array(
                'val' => $val, 
                'nbc' => $nbc, 
                'label' => $label, 
                'gender' => $gender, 
                'related' => $this->getRelatedEntities(array(
                    'vId' => $val['id']
                )), 
                'type' => 'v', 
                'inclRelated' => $inclRelated
            ));
            
            $tmp[$val['taxon_id']] = true;
        }
        
        $taxa = $this->getTaxaInMatrix();
        
        foreach ((array) $taxa as $val) {
            
            if ($tId && $val['id'] != $tId)
                continue;
            
            if (!isset($tmp[$val['id']])) {
                
                $c = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $val['id'], 
                        'language_id' => $this->getCurrentLanguageId()
                    )
                ));
                

                $common = $val['l'];
                foreach ((array) $c as $cVal) {
                    if ($cVal['commonname'] != $val['l']) {
                        $common = $cVal['commonname'];
                        break;
                    }
                }
                
                $nbc = $this->models->NbcExtras->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'ref_id' => $val['id'], 
                        'ref_type' => 'variation'
                    ), 
                    'columns' => 'name,value', 
                    'fieldAsIndex' => 'name'
                ));
                
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $common, 
                    'related' => $this->getRelatedEntities(array(
                        'tId' => $val['id']
                    )), 
                    'type' => 't', 
                    'inclRelated' => $inclRelated
                ));
            }
        }
        
        $this->customSortArray($d, array(
            'key' => 'l', 
            'case' => 'i'
        ));
        
        return $res;
    }



    private function getResultsNBC ()
    {
        $d = $this->getCompleteDatasetNBC();
        
        return $d;
    }



    private function getSimilarNBC ($p = null)
    {
        if (!isset($p['type']) || !isset($p['id']))
            return;
        
        if ($p['type'] == 'v') {
            $d['vId'] = $p['id'];
        }
        else if ($p['type'] == 't') {
            $d['tId'] = $p['id'];
        }
        else
            return;
        
        $d['includeSelf'] = true;
        
        $rel = $this->getRelatedEntities($d);
        

        foreach ((array) $rel as $val) {
            
            if ($val['ref_type'] == 'variation') {
                
                $variation = $this->getVariation($val['relation_id']);
                $val['taxon'] = $this->getTaxonById($variation['taxon_id']);
                $val['taxon_id'] = $variation['taxon_id'];
                
                $nbc = $this->models->NbcExtras->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'ref_id' => $val['relation_id'], 
                        'ref_type' => 'variation'
                    ), 
                    'columns' => 'name,value', 
                    'fieldAsIndex' => 'name'
                ));
                
                $label = $val['label'];
                $val['id'] = $val['relation_id'];
                
                $gender = null;
                
                if (preg_match('/\s(male|female)$/', $label, $matches)) {
                    $gender = $matches[1];
                    $label = preg_replace('/(' . $matches[0] . ')$/', '', $label);
                }
                
                
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $label, 
                    'gender' => $gender, 
                    'type' => 'v',
                	'highlight' => $val['id'] ==$p['id']
                ));
            }
            else {

                $taxon = $this->gettaxonById($val['relation_id']);
                $val['l'] = $taxon['label'];
                
                $c = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $taxon['id'], 
                        'language_id' => $this->getCurrentLanguageId()
                    )
                ));
                

                $common = $val['l'];
                foreach ((array) $c as $cVal) {
                    if ($cVal['commonname'] != $val['l']) {
                        $common = $cVal['commonname'];
                        break;
                    }
                }
                
                $nbc = $this->models->NbcExtras->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'ref_id' => $val['relation_id'], 
                        'ref_type' => 'variation'
                    ), 
                    'columns' => 'name,value', 
                    'fieldAsIndex' => 'name'
                ));
                
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $common, 
                    'type' => 't',
                	'highlight' => $val['id'] ==$p['id']
                ));
            }
        }
        
        return $res;
    }
}