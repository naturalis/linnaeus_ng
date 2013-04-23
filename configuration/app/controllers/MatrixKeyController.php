<?php

/*
	please remove or replace the following tag (also in matrix.css!!!):

	// added details for ALL results, may have to be removed again
*/

include_once ('Controller.php');
class MatrixKeyController extends Controller
{
    private $_matrixType = 'default';
	private $_useSepCoeffAsWeight = false;
	private $_matrixStateImageMaxHeight = null;

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
        'variation_relations',
		'gui_menu_order'
    );
    public $usedHelpers = array();
    public $controllerPublicName = 'Matrix key';
    public $controllerBaseName = 'matrixkey';
    public $cssToLoad = array('matrix.css');
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
        
        $this->initialize();

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
		
        $this->checkMatrixIdOverride();
		$this->setStoreHistory(false);
        
        $m = $this->getCurrentMatrix();
		if (isset($m)) $this->redirect('identify.php');

        $m = $this->getDefaultMatrixId();
		if (isset($m)) {
			$this->setCurrentMatrix($m);
			$this->redirect('identify.php');
		}

        $m = $this->getFirstMatrixId();
		if (isset($m)) {
			$this->setCurrentMatrix($m);
			$this->redirect('identify.php');
		}
        
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


    public function identifyAction ()
    {

        $this->checkMatrixIdOverride();
        
        $matrix = $this->getCurrentMatrix();

        if (!isset($matrix)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));
		
		$characters = $this->getCharacteristics();

        if ($this->_matrixType == 'NBC') {

            $states = $this->stateMemoryRecall();

            $taxa = $this->nbcGetTaxaScores($states);
			
			$groups = $this->getCharacterGroups();

            $this->smarty->assign('taxaJSON', json_encode(
            array(
                'results' => $taxa, 
				'paramCount' => count((array)$states),
                'count' => array(
                    'results' => count((array) $taxa), 
                    'all' => $_SESSION['app']['system']['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']
                )
            ),JSON_HEX_APOS | JSON_HEX_QUOT));

            foreach ((array) $states as $val)
                $d[$val['characteristic_id']] = true;
            
            if (isset($d))
                $this->smarty->assign('activeChars', $d);

			$this->smarty->assign('storedStates', $states);
            $this->smarty->assign('groups',$groups);
            $this->smarty->assign('guiMenu',
				$this->getGUIMenu(
					array(
						'groups'=>$groups,
						'characters'=>$characters,
						'appendExcluded'=>false,
						'checkImages'=>true
					)
				)
			);
		
			if ($this->_useSepCoeffAsWeight)
				$this->smarty->assign('coefficients', $this->getRelevantCoefficients($states));

            $this->smarty->assign('nbcImageRoot', $this->getSetting('nbc_image_root'));
            $this->smarty->assign('nbcFullDatasetCount', $_SESSION['app']['system']['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']);
            $this->smarty->assign('nbcStart', $this->getSessionSetting('nbcStart'));
            $this->smarty->assign('nbcSimilar', $this->getSessionSetting('nbcSimilar'));
			$this->smarty->assign('nbcPerLine', $this->getSetting('matrix_items_per_line'));
			$this->smarty->assign('nbcPerPage', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('nbcBrowseStyle', $this->getSetting('matrix_browse_style'));
			
			$this->smarty->assign('nbcDataSource', 
				array(
					'author' => $this->getSetting('source_author'),
					'title' => $this->getSetting('source_title'),
					'photoCredit' => $this->getSetting('source_photocredit'),
					'url' => $this->getSetting('source_url')
				)
			);
			
        }
        else {
            
            $taxa = $this->getTaxaInMatrix();
            
            $this->smarty->assign('matrices', $this->getMatricesInMatrix());
            $this->smarty->assign('matrixCount', $this->getMatrixCount());
            $this->smarty->assign('storedStates', $this->stateMemoryRecall());
            $this->smarty->assign('storedShowState', $this->showStateRecall());
        }

        if (isset($taxa))
			$this->smarty->assign('taxa', $taxa);
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->smarty->assign('function', 'Identify');

        $this->smarty->assign('characteristics', $characters);
		
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
		
		if (!$this->rHasVal('action')) {
			
            $this->smarty->assign('returnText', 'error');

        }
        else if ($this->rHasVal('action', 'get_states')) {
            
            $this->smarty->assign('returnText', json_encode($this->getCharacteristicStates($this->requestData['id'])));
        }
        else if ($this->rHasVal('action', 'get_taxa')) {
            
            $this->stateMemoryUnset();
            
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
        else if ($this->rHasVal('action', 'store_showstate_pattern')) {
            
            $this->showStateStore('pattern');
        }
        else if ($this->rHasVal('action', 'store_examine_val')) {
            
            $this->examineSpeciesStore($this->requestData['id']);
        }
        else if ($this->rHasVal('action', 'store_compare_vals')) {
            
            $this->compareSpeciesStore($this->requestData['id']);
        }
        else if ($this->rHasVal('action', 'do_search')) {
			
			if ($this->_matrixType == 'NBC') {
				
				$results = $this->nbcDoSearch($this->requestData['params']);

				$this->smarty->assign('returnText', 
					json_encode(
						array(
							'results' => $results, 
							'count' => array(
								'results' => count((array) $results)
							)
						)
					)
				);

			}

        }
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'save_session_setting')) {
            
            $this->saveSessionSetting($this->requestData['setting']);
        }
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'get_session_setting')) {
            
            $this->smarty->assign('returnText', $this->getSessionSetting($this->requestData['name']));
        }
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'get_formatted_states')) {
            
            $c = $this->getCharacteristic(array('id'=>$this->requestData['id']));
            $c['prefix'] = ($c['type'] == 'media' || $c['type'] == 'text' ? 'c' : 'f');
            
            $s = $this->getCharacteristicStates($this->requestData['id']);
            $s = $this->fixStateLabels($s); // temporary measure (hopefully!)
			//$s = $this->sortStates($s);
            
            //$states = $this->stateMemoryRecall(array('charId' => $this->requestData['id']));
            $states = $this->stateMemoryRecall();
            $countPerState = $this->getRemainingStateCount(array(
                'charId' => $this->requestData['id'], 
                'states' => $states
            ));
            $this->smarty->assign('remainingStateCount', $countPerState);

            $states = $this->nbcStateMemoryReformat($states);
		
            $this->smarty->assign('stateImagesPerRow',$this->getSetting('matrix_state_image_per_row'));
            $this->smarty->assign('c', $c);
            $this->smarty->assign('s', $s);
            $this->smarty->assign('states', $states);

            $this->smarty->assign('returnText', 
				json_encode(
				array(
					'character' => $c,
					'page' => 
					$this->fetchPage('formatted_states'),
					'showOk' => ($c['type'] == 'media' || $c['type'] == 'text' ? false : true)
				)));

        }
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'get_results_nbc')) {

            $states = $this->stateMemoryRecall();

 			if (isset($this->requestData['params']['action']) && $this->requestData['params']['action'] == 'similar')                
                $results = $this->nbcGetSimilar($this->requestData['params']);
            else                
                $results = $this->nbcGetTaxaScores($states);

			$d = array();

            foreach ((array) $states as $val)
                $d[$val['characteristic_id']] = true;

			array_walk($results, create_function('&$v,$k', '$v["l"] = ucfirst($v["l"]);'));

			$countPerState = $this->getRemainingStateCount(array(
				'states' => $states
			));

			$GUIMenu = $this->getGUIMenu(
				array(
					'groups' => $this->getCharacterGroups(),
					'characters' => $this->getCharacteristics(),
					'appendExcluded' => false,
					'checkImages' => true
				)
			);


			$result = 
				json_encode(
					array(
						'results' => $results, 
						'paramCount' => count((array)$states),
						'count' => array(
							'results' => count((array)$results)
						),
						'menu' => array(
							'groups' => $GUIMenu,
							'activeChars' => $d,
							'storedStates' => $this->stateMemoryRecall()
						),
						'countPerState' => $countPerState
					)
				);
			
            $this->smarty->assign('returnText', $result	);

			
        }
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'clear_state')) {

			if (!$this->rHasVal('state'))
				$this->stateMemoryUnset();
			else
				$this->stateMemoryUnset($this->requestData['state']);
				

		}
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'set_state') && $this->rHasVal('state')) {

			if ($this->rHasVal('value'))
				$state = $this->requestData['state'] . ':' . $this->requestData['value'];
			else
				$state = $this->requestData['state'];
		
			$this->stateMemoryStore($state);
			
			return;

		}
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'get_groups')) {

            $states = $this->stateMemoryRecall();

			$d = array();

            foreach ((array) $states as $val)
                $d[$val['characteristic_id']] = true;

            $this->smarty->assign('returnText', 
				json_encode(
				array(
					'groups' => $this->getCharacterGroups(),
					'activeChars' => $d,
					'storedStates' => $this->stateMemoryRecall()
				)));

		}
        else if ($this->_matrixType == 'NBC' && $this->rHasVal('action', 'get_initial_values')) {

			// state image urls
			$cs = $this->models->CharacteristicState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					), 
					'columns' => 'id,file_name',
					'fieldAsIndex' => 'id'
				));

			$cl = $this->models->CharacteristicLabel->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId()
					),
					'columns' => 'characteristic_id,label',
					'fieldAsIndex' => 'characteristic_id'
				));

            $this->smarty->assign('returnText', 
				json_encode(
				array(
					'stateImageUrls' => 
						array(
							'baseUrl' => $_SESSION['app']['project']['urls']['projectMedia'],
							'baseUrlSystem' => $_SESSION['app']['project']['urls']['systemMedia'],
							'fileNames' => $cs
						),
					'characterNames' => $cl
					)
				)
			);

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



    private function initialize ($force = false)
    {
        $this->_matrixType = $this->getSetting('matrixtype');
        $this->_useCharacterGroups = $this->getSetting('matrix_use_character_groups') == '1';
        $this->useVariations = $this->getSetting('taxa_use_variations') == '1';
        $this->smarty->assign('useCharacterGroups', $this->_useCharacterGroups);
        $this->_useSepCoeffAsWeight = false; // $this->getSetting('matrix_use_sc_as_weight');
        $this->_matrixStateImageMaxHeight = $this->getSetting('matrix_state_image_max_height');

		if (empty($_SESSION['app']['system']['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']))
			$_SESSION['app']['system']['matrix'][$this->getCurrentMatrixId()]['totalEntityCount'] = $this->getTotalEntityCount();
        
        if ($this->_matrixType == 'NBC') {
			$_SESSION['app']['system']['urls']['nbcImageRoot'] = $this->getSetting('nbc_image_root');
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


    private function getDefaultMatrixId ()
    {

		$m = $this->models->Matrix->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'got_names' => 1,
				'default' => 1
			), 
			'columns' => 'id'
		));
		
		return isset($m[0]['id']) ? $m[0]['id'] : null;

    }


    private function getFirstMatrixId ()
    {

		$m = $this->getMatrices();
		$m = array_shift($m);
		return $m['id'];

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



    private function stateMemoryStore ($data)
    {
        foreach ((array) $data as $key => $val) {
            
            $d = explode(':', $val);
            
            if ($d[0] == 'f') // f:x:n[:sd] (free values)
                $_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()][$d[0] . ':' . $d[1]] = $val;
            else // c:x:y (fixed values)
                $_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()][$val] = $val;
        }
		
    }



    private function stateMemoryUnset ($id = null)
    {
        if (empty($id))
            unset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()]);
        else
            unset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()][$id]);
    }



    private function stateMemoryRecall ($p = null)
    {
        if (isset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()])) {
            
            $charId = isset($p['charId']) ? $p['charId'] : null;
            
            $states = array();
            
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
                else if ($d[0] == 'f' && isset($d[2])) {
                    
                    $states[$key]['type'] = 'f';
                    $states[$key]['characteristic_id'] = $d[1];
                    $states[$key]['value'] = $d[2];
                }
                
                if (!empty($charId) && !in_array($states[$key]['characteristic_id'], (array) $charId))
                    unset($states[$key]);
                if (empty($states[$key]['characteristic_id']))
                    unset($states[$key]);
            }
            
            return !empty($states) ? $states : null;
        }
        else {
            
            return null;
        }
    }
	
	private function scaleDimensions($d)
	{
		
		if (is_null($this->_matrixStateImageMaxHeight))
			return $d;

		return array(round(($this->_matrixStateImageMaxHeight / $d[1]) * $d[0]),$this->_matrixStateImageMaxHeight);
		
	}

    private function getCharacteristicStates ($id)
    {
        if (!isset($id))
            return;
        

        $cs = $this->models->CharacteristicState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'characteristic_id' => $id
            ), 
            'order' => 'show_order', 
            'columns' => 'id,characteristic_id,file_name,file_dimensions,lower,upper,mean,sd,got_labels,show_order'
        ));

		$d = $this->getCharacteristic(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			)
		);

        foreach ((array) $cs as $key => $val) {
            $cs[$key]['type'] = $d['type'];
            $cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id']);
            $cs[$key]['text'] = $this->getCharacteristicStateLabelOrText($val['id'], 'text');
            $cs[$key]['img_dimensions'] = empty($val['file_dimensions']) ? null : $this->scaleDimensions(explode(':',$val['file_dimensions'])); // gives w, h

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



    private function getTaxaScores ($states, $incUnknowns = false)
    {
        $s = $c = array();
        $stateCount = 0;
        
        // we have to find out which states we are looking for
        foreach ((array) $states as $sKey => $sVal) {
            $d = explode(':', $sVal);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;

            // which is easy for the non-range characters...
            if ($d[0] != 'f') {
                
                if (isset($d[2]))
                    $s[$d[2]] = $d[2];
                $stateCount++;
            }
            // ...but requires calculation for the ranged ones
            else {

                // is there a standard dev?
                $sd = (isset($d[3]) ? $d[3] : null);
                
                // where-clause basics
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $charId
                );
				
				$value = str_replace(',','.',$value);

                // calculate the spread around the mean...
                if (isset($sd)) {

					$sd = str_replace(',','.',$sd);
                    
                    $d['mean >=#'] = '(' . strval(floatval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(floatval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                // or mark just mark the upper and lower boundaries of the value
                else {
                    
                    $d['lower <='] = $d['upper >='] =  floatval($value);
                }
                
                // get any states that correspond with these values
                $cs = $this->models->CharacteristicState->_get(array(
                    'id' => $d
                ));

                // and store them
                foreach ((array) $cs as $key => $val)
                    $s[] = $val['id'];
                
                $stateCount++;
            }
            
            $c[$charId] = $charId;
        }
        
        if (empty($s))
            return;
        
        $n = $stateCount + ($incUnknowns ? 1 : 0);
        $s = implode(',', $s);
        $c = implode(',', $c);
        
        $q = "
        	select 'taxon' as type, _a.taxon_id as id,
       				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
       				_c.is_hybrid as h, trim(_c.taxon) as l
        		from %PRE%matrices_taxa _a
        		left join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.matrix_id = _b.matrix_id
        			and _a.taxon_id = _b.taxon_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%taxa _c
			        on _a.taxon_id = _c.id
		        where _a.project_id = " . $this->getCurrentProjectId() . "
			        and _a.matrix_id = " . $this->getCurrentMatrixId() . "
        		group by _a.taxon_id
        	union
        	select 'matrix' as type, _a.id as id, 
			        count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
			        0 as h, trim(_c.name) as l
        		from  %PRE%matrices _a
        		join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.id = _b.ref_matrix_id
        			and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
		        left join %PRE%matrices_names _c
			        on _b.ref_matrix_id = _c.id
        			and _c.language_id = " . $this->getCurrentLanguageId() . "
        		where _a.project_id = " . $this->getCurrentProjectId() . "
        			and _b.matrix_id = " . $this->getCurrentMatrixId() . "
        		group by id" . ($this->_matrixType == 'NBC' ? "
			union
			select 'variation' as type, _a.variation_id as id, 
				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
				0 as h, trim(_c.label) as l
				from  %PRE%matrices_variations _a        		
				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
					and ((_b.state_id in (" . $s . "))" . ($incUnknowns ? "or (_b.state_id not in (" . $s . ") and _b.characteristic_id in (" . $c . "))" : "") . ")
				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by _a.variation_id" : "")
				//."order by s desc"
        ;

        $results = $this->models->MatrixTaxonState->freeQuery($q);
        
        usort($results, array(
            $this, 
            'sortQueryResultsByScoreThenLabel'
        ));
        
        return $results;
    }



    private function sortQueryResultsByScoreThenLabel ($a, $b)
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
            
            $d = $this->getCharacteristic(array('id'=>$val['characteristic_id']));
		
            if ($d) {
				$d['prefix'] = ($d['type'] == 'media' || $d['type'] == 'text' ? 'c' : 'f');
                $states = $this->getCharacteristicStates($val['characteristic_id']);
                $d['sort_by']['alphabet'] = isset($d['label']) ? strtolower(preg_replace('/[^A-Za-z0-9]/', '', $d['label'])) : '';
                $d['sort_by']['separationCoefficient'] = -1 * $this->getCharacteristicHValue($val['characteristic_id'], $states); // -1 to avoid asc/desc hassles in JS-sorting
                $d['sort_by']['characterType'] = strtolower(
                preg_replace('/[^A-Za-z0-9]/', '', $d['type']['name']));
                $d['sort_by']['numberOfStates'] = -1 * count((array) $states); // -1 to avoid asc/desc hassles in JS-sorting
                $d['sort_by']['entryOrder'] = intval($val['show_order']);
                $d['states'] = $states;
                $c[] = $d;
            }
        }
        
        return isset($c) ? $c : null;
    }



    private function getCharacteristic ($p)
    {
		
		$id = isset($p['id']) ? $p['id'] : null;
		// states only used to avoid double queries when looking for min and max values
		$states = isset($p['states']) ? $p['states'] : null;
		
		if (empty($id)) return;
		
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
        
        if ($char['got_labels'] == 1) {
            
            $cl = $this->models->CharacteristicLabel->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->getCurrentLanguageId(), 
                    'characteristic_id' => $id
                )
            ));
            
            $char['label'] = $cl[0]['label'];
			
			if (strpos($char['label'],'|')!==false) {
				
				$d = explode('|',$char['label'],3);

				$char['label'] = isset($d[0]) ? $d[0] : null;
				$char['info'] = isset($d[1]) ? $d[1] : null;
				$char['unit'] = isset($d[2]) ? $d[2] : null;
				
			}
						
			if ($char['type']=='range' || $char['type']=='distribution') {

				$cs = $this->models->CharacteristicState->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'characteristic_id' => $id
						), 
						'columns' => 'min(lower) as lowest,max(upper) as most_upper'
					));

				$char['min'] = 1.5;//$cs[0]['lowest'];
				$char['max'] = $cs[0]['most_upper'];
				$char['min_display'] = round($char['min'],(is_float($char['min']) ? 1 : 0));
				$char['max_display'] = round($char['max'],(is_float($char['max']) ? 1 : 0));
				
			}
			
            unset($char['got_labels']);
        }
        
        return $char;
    }



    private function getEntityStates ($id, $type)
    {

		$d = array(
			'project_id' => $this->getCurrentProjectId(), 
			'matrix_id' => $this->getCurrentMatrixId()
		);
		
		if ($type == 'variation')
			$d['variation_id'] = $id;
		else
		if ($type == 'matrix')
			$d['ref_matrix_id'] = $id;
		else
			$d['taxon_id'] = $id;

		$mts = $this->models->MatrixTaxonState->_get(
		array(
			'id' => $d, 
			'columns' => 'characteristic_id,state_id'
		));
		
		$res = array();
			
        foreach ((array) $mts as $key => $val) {
            
            $d = $this->getCharacteristic(array('id'=>$val['characteristic_id']));
			$res[$val['characteristic_id']]['characteristic'] = $d['label'];
			$res[$val['characteristic_id']]['type'] = $d['type'];
			$res[$val['characteristic_id']]['states'][$val['state_id']] = $this->getCharacteristicState($val['state_id']);
			
        }

		return $res;        
    }

    private function getTaxonStates ($id)
    {
        return $this->getEntityStates ($id,'taxon');
    }

    private function getVariationStates ($id)
    {
        return $this->getEntityStates ($id,'variation');
    }
    
    private function getMatrixStates ($id)
    {
        return $this->getEntityStates ($id,'matrix');
    }
    

    private function getTaxonComparison ($id)
    {
        if (empty($id[0]) || empty($id[1]))
            return;

		$cl = $this->models->CharacteristicLabel->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'language_id' => $this->getCurrentLanguageId()
			),
			'fieldAsIndex' => 'characteristic_id'
		));
			        
        $mts1 = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $id[0]
				), 
            'columns' => 'taxon_id,characteristic_id,state_id',
			'fieldAsIndex' => 'state_id'
        ));

        $mts2 = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $id[1]
				), 
            'columns' => 'taxon_id,characteristic_id,state_id',
			'fieldAsIndex' => 'state_id'
        ));

        $overlap = $states1 = $states2 = array();
        
        foreach ((array) $mts1 as $key => $val) {
			
			$state = $this->getCharacteristicState($key);
			$state['characteristic'] = $cl[$val['characteristic_id']]['label'];
            
            if (isset($mts2[$key]))
                $overlap[] = $state;
            else
                $states1[] = $state;
        }

        foreach ((array) $mts2 as $key => $val) {

            if (!isset($mts1[$key])) {
				$state = $this->getCharacteristicState($key);
				$state['characteristic'] = $cl[$val['characteristic_id']]['label'];
                $states2[] = $state;
			}

        }
	
		$t1 = $this->getTaxonById($id[0]);
		$t2 = $this->getTaxonById($id[1]);
		
		$c = $this->getCharacteristics();

		$total = 0;
		foreach((array)$c as $cVal)
			foreach((array)$cVal['states'] as $sVal)
				$total++;
		
		
		$count1 = count((array)$states1);
		$count2 = count((array)$states2);
		$both = count((array)$overlap);
		$neither = $total - $both - $count1 - $count2;
        
        return array(
            'taxon_1' => $t1['taxon'], 
            'taxon_2' => $t2['taxon'], 
            'count_1' => $count1, 
            'count_2' => $count2, 
			'neither' => $neither, 
			'both' => $both, 
            'total' => $total, 
            'coefficients' => $this->calculateDistances($count1, $count2, $both, $neither), 
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



    private function getCharacterGroups ()
    {
		
        $cg = $this->models->Chargroup->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'order' => 'show_order', 
            'columns' => 'id,matrix_id,label,show_order',
			'fieldAsIndex' => 'id'
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
                $cg[$key]['chars'][] = $this->getCharacteristic(array('id'=>$cVal['characteristic_id']));
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

    private function getVariationsInMatrix ($style = 'long')
    {
        $mv = $this->models->MatrixVariation->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'fieldAsIndex' => 'variation_id'
        ));
        
        $d = array();
        foreach ((array) $mv as $val) {
            
            $var = $this->getVariation($val['variation_id']);
            
            if ($style == 'short')
                $d[] = array(
                    'id' => $var['id'], 
                    'h' => $var['taxon']['is_hybrid'], 
                    'l' => $var['label'], 
                    'type' => 'var'
                );
            else
                $d[] = $var;
        }
        
        $this->customSortArray($d, array(
            'key' => ($style == 'short' ? 'l' : 'label')
        ));
        
        return $d;
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
            
            if ($includeSelf && isset($tId))
                array_unshift($rel, array(
                    'id' => $tId, 
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
            
            if ($includeSelf && isset($vId))
                array_unshift($rel, array(
                    'id' => $vId, 
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
        $common = isset($p['common']) ? $p['common'] : null;
        $gender = isset($p['gender']) ? $p['gender'] : null;
        $related = isset($p['related']) ? $p['related'] : null;
        $type = isset($p['type']) ? $p['type'] : null;
        $inclRelated = isset($p['inclRelated']) ? $p['inclRelated'] : false;
        $highlight = isset($p['highlight']) ? $p['highlight'] : false;
        $details = isset($p['details']) ? $p['details'] : null;
		
		$type = ($type=='variation' ? 'v' : ($type=='taxon' ? 't' : $type));

		$sciName = strip_tags(($type == 't' ? $val['l'] : (isset($val['taxon']) && !is_array($val['taxon']) ? $val['taxon'] : (isset($val['taxon']['taxon']) ? $val['taxon']['taxon'] : null))));

        $d = array(
            'i' => $val['id'], 
            'l' => trim(strip_tags($label)), 
			'c' => $common,
            'y' => $type, 
            's' => trim(strip_tags($sciName)),
            'm' => isset($nbc['url_image']) ? $nbc['url_image']['value'] : $this->getSetting('nbc_image_root').'noimage.gif', 
            'n' => isset($nbc['url_image']), 
            'b' => isset($nbc['url_thumbnail']) ? $nbc['url_thumbnail']['value'] : null, 
            'p' => isset($nbc['source']) ? $nbc['source']['value'] : null, 
            'u' => isset($nbc['url_soortenregister']) ? $nbc['url_soortenregister']['value'] : null, 
            'r' => count((array) $related), 
            'h' => $highlight, 
            'd' => isset($details) ? $details : null
        );

        if (isset($val['taxon_id']))
            $d['t'] = $val['taxon_id'];
        if (isset($gender)) {
            $d['g'] = $gender[0];
            $d['e'] = $gender[1];
		}
        
        if ($inclRelated && !empty($related))
            $d['related'] = $related;
        
        return $d;
    }

    private function nbcStateMemoryReformat ($d)
    {
        $states = array();
        
        foreach ((array) $d as $key => $val) {
            
            $states[$val['characteristic_id']][(isset($val['id']) ? $val['id'] : count((array) $states))] = array(
                'id' => isset($val['id']) ? $val['id'] : null, 
                'label' => isset($val['label']) ? $val['label'] : null, 
                'val' => isset($val['val']) ? $val['val'] : null, 
                'value' => isset($val['value']) ? $val['value'] : null, 
                'key' => $val['type'] == 'f' ? $val['type'] . ':' . $val['characteristic_id'] : $val['val']
            );
        }
        
        return $states;
    }

    private function fixStateLabels ($s)
    {
        
        // from "hs_zijrand_1_zijdoorn" to "1 zijdoorn"
        $shortest = null;
        foreach ((array) $s as $val) {
            if (is_null($shortest) || strlen($val['label']) < $shortest)
                $shortest = $val['label'];
        }
        
        $prefix = '';
        for ($i = strlen($shortest) - 1; $i >= 4; $i--) {
            $bit = substr($shortest, 0, $i);
            //echo $bit;
            

            $hit = true;
            foreach ((array) $s as $val) {
                if (strpos($val['label'], $bit) !== 0)
                    $hit = false;
            }
            
            //echo ':'.($hit ? 1 : 0).'<br/>';
            if ($hit && strlen($prefix) < strlen($bit)) {
                $prefix = $bit;
                //echo '<b>'.$prefix.'</b><br>';
            }
        }
        
        if (strlen($prefix) != 0)
            array_walk($s, create_function('&$elem', '$elem["label"] = preg_replace("/_/"," ",preg_replace("/^(' . $prefix . ')/","",$elem["label"]));'));
        
        return $s;
    }

    private function sortStates ($s)
    {
        uasort($s, create_function('$a,$b', 'return ($a["label"]>$b["label"]?1:($a["label"]<$b["label"]?-1:0));'));
        
        return $s;
    }

    private function getRemainingStateCount ($p=null)
    {
    
        $charIdToShow = isset($p['charId']) ? $p['charId'] : null;
        $states = isset($p['states']) ? $p['states'] : $this->stateMemoryRecall();
        $groupByCharId = isset($p['groupByCharId']) ? $p['groupByCharId'] : false;
    
        $dT = $dV = '';
        $i = 0;
		$s = array();
    
        if (!empty($states)) {
            // we want all taxa/variations that have the already selected states so we create a list of unique state id's of those states
            foreach ((array) $states as $val) {
				
                if ($val['type'] != 'f') {

                    $dT .= "right join %PRE%matrices_taxa_states _c" . $i . "
						on _a.taxon_id = _c" . $i . ".taxon_id
						and _a.matrix_id = _c" . $i . ".matrix_id
						and _c" . $i . ".state_id  = " . $val['id'] . "
						and _a.project_id = _c" . $i++ . ".project_id
						and (_a.taxon_id is not null or _a.variation_id is null)
						";

                } else {
                
					$d = explode(':',$val['val']);
					$value = $val['value'];

					$sd = (isset($d[3]) ? $d[3] : null);
					
					$d = array(
						'project_id' => $this->getCurrentProjectId(), 
						'characteristic_id' => $val['characteristic_id']
					);
					
					if (isset($sd)) {
						
						$d['mean >=#'] = '(' . strval(intval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
						$d['mean <=#'] = '(' . strval(intval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
					} else {
						
						$d['lower <='] = $d['upper >='] = intval($value);
					}					

					$cs = $this->models->CharacteristicState->_get(array(
						'id' => $d
					));

					foreach ((array) $cs as $key => $cVal)
						$s[] = $cVal['id'];
	
				}
            }
        }
		
		
        if ($s) {

			$s = implode(',', $s);

			$fsT = "right join %PRE%matrices_taxa_states _s
					on _s.project_id = _a.project_id 
					and _s.matrix_id = _a.matrix_id
					and _s.taxon_id = _a.taxon_id 
					and _s.taxon_id is not null
					and _s.state_id in (" . $s . ")";

			$fsV = str_replace('variation_id is null', 'taxon_id is null', str_replace('taxon_id', 'variation_id', $fsT));

		}

        if (!empty($dT))
            $dV = str_replace('variation_id is null', 'taxon_id is null', str_replace('taxon_id', 'variation_id', $dT));

        
        /*
         find the number of taxon/state-connections that exist, grouped by state, but only for taxa that
        have the already selected states, unless no states have been selected at all, in which case we just
        return them all
        */
        
        $q = "
        	select sum(tot) as tot, state_id, characteristic_id 
        	from (
        		select count(distinct _a.taxon_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
	        		from %PRE%matrices_taxa_states _a
	        		" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
						and _a.taxon_id not in
							(select taxon_id from %PRE%taxa_variations where project_id = " . $this->getCurrentProjectId() . ")
					group by _a.state_id
				union
				select count(distinct _a.variation_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by _a.state_id
			) as q1 
			group by q1.state_id
			";

        $results = $this->models->MatrixTaxonState->freeQuery($q);
        
        $all = array();
    
        foreach ((array) $results as $val) {

            if (!empty($charIdToShow) && $val['characteristic_id']!=$charIdToShow) continue;
    
			if ($groupByCharId) {
	            $all[$val['characteristic_id']]['states'][$val['state_id']] = intval($val['tot']);
	            $all[$val['characteristic_id']]['tot'] = (isset($all[$val['characteristic_id']]['tot']) ? $all[$val['characteristic_id']]['tot'] : 0) + intval($val['tot']);
			} else {
	            $all[$val['state_id']] = intval($val['tot']);
			}
    
        }

        return empty($all) ? '*' : $all;
    }
	
	private function getTotalEntityCount()
	{

        if ($this->_matrixType == 'NBC')

			$q = 
			"select count(distinct _a.taxon_id) as tot
					from %PRE%matrices_taxa _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				union
				select count(distinct _a.variation_id) as tot
					from  %PRE%matrices_variations _a        		
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId()
				;
		else
			$q = 
			"select count(distinct _a.taxon_id) as tot
					from %PRE%matrices_taxa _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId();
				;

		$results = $this->models->MatrixTaxonState->freeQuery($q);

		return $results[0]['tot']+(isset($results[1]['tot']) ? $results[1]['tot'] : 0);
		
	}
 
    private function nbcGetCompleteDataset ($p = null)
    {

        $res = $this->getCache('matrix-nbc-data');
        
        if (!$res) {

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
                
                $d = $this->nbcExtractGenderTag($label);
				
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $d['label'], 
					'common' => $this->getCommonname($val['taxon_id']),
                    'gender' => array($d['gender'], $d['gender_label']),
                    'related' => $this->getRelatedEntities(array(
                        'vId' => $val['id']
                    )), 
                    'type' => 'v', 
                    'inclRelated' => $inclRelated,
					'details' => $this->getVariationStates($val['id']) // added details for ALL results, may have to be removed again
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
                            'ref_type' => 'taxon'
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
                        'inclRelated' => $inclRelated,
						'details' => $this->getTaxonStates($val['id']) // added details for ALL results, may have to be removed again
                    ));
                }
            }

			// added details for ALL results, may have to be removed again
			$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));
            
            $this->customSortArray($res, array(
                'key' => 'l', 
                'case' => 'i'
            ));
            
            $this->saveCache('matrix-nbc-data', $res);
        }

        return $res;
    }

    private function nbcGetSimilar ($p = null)
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
                
				$d = $this->nbcExtractGenderTag($label);
				
                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'nbc' => $nbc, 
                    'label' => $d['label'], 
					'common' => $this->getCommonname($val['taxon_id']), 
                    'gender' => array($d['gender'], $d['gender_label']),
                    'type' => 'v', 
                    'highlight' => $val['id'] == $p['id'], 
                    'details' => $this->getVariationStates($val['relation_id'])
                ));
            }
            else {
                
                $taxon = $this->getTaxonById($val['relation_id']);
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
                        'ref_type' => 'taxon'
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
                    'highlight' => $val['relation_id'] == $p['id'], 
                    'details' => $this->getTaxonStates($taxon['id'])
                ));
            }
        }
		
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));
        
        return $res;
    }
	
	private function nbcHandleOverlappingItemsFromDetails($p)
	{
		
		//return $p['data'];

        $data = isset($p['data']) ? $p['data'] : null;
        $action = isset($p['action']) ? $p['action'] : 'remove';

		if (count((array)$data)==1)
			return $data;

		$d = array();
		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal)	{
				foreach((array)$cVal['states'] as $state)	{
					$d[$key][] = $characteristic_id.':'.$state['id']; // characteristic_id:state_id
				}
			}
		}

		$common = call_user_func_array('array_intersect',$d);

		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal) {
				foreach((array)$cVal['states'] as $sVal => $state)	{

					if (in_array($characteristic_id.':'.$state['id'],$common)) {
						if ($action=='remove') {
							unset($data[$key]['d'][$characteristic_id]['states'][$sVal]);
						} else
						if ($action=='tag') {
							$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'] = '<span class="overlapState">'.$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'].'</span>';
						}
					}
					
				}
				
				if (count((array)$data[$key]['d'][$characteristic_id]['states'])==0 && $action=='remove') {

					unset($data[$key]['d'][$characteristic_id]);

				}
			}
		}

		return $data;
		
	}

    private function nbcGetTaxaScores ($selectedStates = null)
    {
        $states = array();
        
        $d = isset($selectedStates) ? $selectedStates : $this->stateMemoryRecall();
        
        // get all stored selected states
        foreach ((array) $d as $val)
            $states[] = $val['val'];
        
        if (count($states) == 0)
            return $this->nbcGetCompleteDataset();

		// calculate scores
        $matches = $this->getTaxaScores($states, false);

		$fullhits = 0;
		foreach((array)$matches as $match)
			if ($match['s']) $fullhits++;
		
		$itemsPerPage = $this->getSetting('matrix_items_per_page');
		if ($itemsPerPage) {
			$fetchDetails = $fullhits <= $itemsPerPage;
		}
        
        // only keep the 100% scores, no partial matches
        $res = array();
        foreach ((array) $matches as $match) {
            if ($match['s'] == 100) {
                
                if ($match['type'] == 'variation') {
                    
                    $val = $this->getVariation($match['id']);

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
                    
					$d = $this->nbcExtractGenderTag($label);
                    
                    $res[] = $this->createDatasetEntry(
                    array(
                        'val' => $val, 
                        'nbc' => $nbc, 
                        'label' => $d['label'], 
						'common' => $this->getCommonname($val['taxon_id']),
                        'gender' => array($d['gender'], $d['gender_label']),
                        'related' => $this->getRelatedEntities(array(
                            'vId' => $val['id']
                        )), 
                        'type' => 'v', 
                        'inclRelated' => false,
						'details' => ($fetchDetails ? $this->getVariationStates($val['id']) : null)
                    ));
                }
                else {

                    $c = $this->models->Commonname->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $match['id'], 
                            'language_id' => $this->getCurrentLanguageId()
                        )
                    ));
                    
                    $common = $match['l'];

                    foreach ((array) $c as $cVal) {
                        if ($cVal['commonname'] != $match['l']) {
                            $common = $cVal['commonname'];
                            break;
                        }
                    }
                    
                    $nbc = $this->models->NbcExtras->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'ref_id' => $match['id'], 
                            'ref_type' => 'taxon'
                        ), 
                        'columns' => 'name,value', 
                        'fieldAsIndex' => 'name'
                    ));
                    

                    $res[] = $this->createDatasetEntry(
                    array(
                        'val' => $match, 
                        'nbc' => $nbc, 
                        'label' => $common, 
                        'related' => $this->getRelatedEntities(array(
                            'tId' => $match['id']
                        )), 
                        'type' => 't', 
                        'highlight' => 0,
						'details' => ($fetchDetails ? $this->getTaxonStates($val['id']) : null)
                    ));
                }
            }
        }


        
        if (count((array)$res)==1) {
            
            $res[0]['d'] =
            	$res[0]['y']=='t' ?
	            	$this->getTaxonStates($res[0]['i']) :
	            	$this->getVariationStates($res[0]['i']);
            
        } else {
        
	        $this->customSortArray($res, array(
	            'key' => 'l', 
	            'case' => 'i'
	        ));

        }

        return $res;
    }

	private function nbcExtractGenderTag($label)
	{
		if (preg_match('/\s(man|vrouw|beide)(\s|$)/', $label, $matches)) {
			$gender = trim($matches[1]);
			$label = preg_replace('/\s(' . $gender . ')(\s|$)/', ' ', $label);
			$gender = ($gender=='beide' ? null : $gender);
		} else
			$gender = null;
		
		return array(
			'gender' => $gender,
			'label' => $label,
			'gender_label' => $this->translate($gender)
		);
		
	}
	
	private function nbcDoSearch($p=null)
    {
        if (!isset($p['term']))
            return;
			
		$term = mysql_real_escape_string(strtolower($p['term']));

        $q = "
			select 'variation' as type, _a.variation_id as id, trim(_c.label) as label,trim(_c.label) as l, _c.taxon_id as taxon_id, _d.taxon as taxon, 1 as s, null as commonname
				from  %PRE%matrices_variations _a        		
				left join %PRE%matrices_taxa_states _b
					on _a.project_id = _b.project_id
					and _a.matrix_id = _b.matrix_id
					and _a.variation_id = _b.variation_id
				left join %PRE%taxa_variations _c
					on _a.variation_id = _c.id
				left join %PRE%taxa _d
					on _c.taxon_id = _d.id						
				where _a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				and (lower(_c.label) like '%". $term ."%' or lower(_d.taxon) like '%". $term ."%')
			union
			select 'taxon' as type, _a.taxon_id as id, trim(_c.taxon) as label, trim(_c.taxon) as l, _a.taxon_id as taxon_id, _c.taxon as taxon, 1 as s, _d.commonname as commonname
        		from %PRE%matrices_taxa _a
        		left join %PRE%matrices_taxa_states _b
        			on _a.project_id = _b.project_id
        			and _a.matrix_id = _b.matrix_id
        			and _a.taxon_id = _b.taxon_id
		        left join %PRE%taxa _c
			        on _a.taxon_id = _c.id
		        left join %PRE%commonnames _d
			        on _a.taxon_id = _d.taxon_id
					and _d.language_id = ".$this->getCurrentLanguageId() ." 
		        where _a.project_id = " . $this->getCurrentProjectId() . "
			        and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					and (lower(_c.taxon) like '%". $term ."%' or lower(_d.commonname) like '%". $term ."%')
				";

        $results = $this->models->MatrixTaxonState->freeQuery($q);

        if (!$results)
			return null;
		
		usort($results, array($this, 'sortQueryResultsByScoreThenLabel'));

		$res = $tmp = array();
		$i = 0;
		
		foreach((array)$results as $val) {

			if ($val['type']=='taxon' && isset($tmp[$val['id']]))
				continue;

			if ($val['type']=='variation') {
				
				$d = $this->nbcExtractGenderTag($val['label']);
				$d = $this->nbcExtractGenderTag($val['label']);
				$label = $d['label'];
				$gender = $d['gender'];
				$gender_label = $d['gender_label'];
				$common = $this->getCommonname($val['taxon_id']);
			
			} else {

				$label = $val['label'];

				if ($val['commonname'] != $val['label'])
					$label = $val['commonname'];

				$common = $val['commonname'];
				$gender = $gender_label = null;

			}

			$res[$i] = $this->createDatasetEntry(
				array(
					'val' => $val, 
					'nbc' => $this->models->NbcExtras->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(), 
								'ref_id' => $val['id'], 
								'ref_type' => $val['type']
							), 
							'columns' => 'name,value', 'fieldAsIndex' => 'name'
						)
					), 
					'label' => $label, 
					'common' => $common,
					'gender' => array($gender,$gender_label), 
					'related' => $this->getRelatedEntities(array(($val['type']=='variation' ? 'vId' : 'tId') => $val['id'])), 
					'type' => $val['type'], 
					'inclRelated' => true,
					'details' => ($val['type']=='variation' ? $this->getVariationStates($val['id']) : $this->getTaxonStates($val['id'])) // added details for ALL results, may have to be removed again
				)
			);

			// post processing; createDatasetEntry() strips tages, hence.
			$res[$i]['l'] = preg_replace_callback(
				'/(' . $term . ')/i', 
				create_function('$matches', 'if (trim($matches[0]) == "") return $matches[0]; else return "<span class=\"seachStringHighlight\">".$matches[0]."</span>";'), 
				$res[$i]['l']
			);

			$res[$i]['s'] = preg_replace_callback(
				'/(' . $term . ')/i', 
				create_function('$matches', 'if (trim($matches[0]) == "") return $matches[0]; else return "<span class=\"seachStringHighlight\">".$matches[0]."</span>";'), 
				$res[$i]['s']
			);

			if ($val['type']=='variation')
				$tmp[$val['taxon_id']] = true;
				
			$i++;
                    
		}
		
		// added details for ALL results, may have to be removed again
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));

        return $res;
		
    }
 
	private function getRelevantCoefficients($states=null)
	{

		$smallIsSignificant = true;

		$res = $this->getRemainingStateCount(array('states' => $states, 'groupByCharId' => true));

		$l1['s'] = $l2['s'] = $l3['s'] = ($smallIsSignificant ? 1 : 0);
		
		foreach ((array)$res as $key => $val) {
			$c = $this->getCharacteristic(array('id'=>$key));

            if ($c['type'] != 'media' && $c['type'] != 'text')
				continue;
			
			$v = $this->getCharacteristicHValue($key);
			$res[$key]['separationCoefficient'] = $v;
			if (($smallIsSignificant && $v<$l3['s']) || (!$smallIsSignificant && $v>$l3['s'])) {
				if (($smallIsSignificant && $v<$l2['s']) || (!$smallIsSignificant && $v>$l2['s'])) {
					if (($smallIsSignificant && $v<$l1['s']) || (!$smallIsSignificant && $v>$l1['s'])) {
						$l1['s']=$v;
						$l1['i']=$key;
					} else {
						$l2['s']=$v;
						$l2['i']=$key;
					}
				} else {
					$l3['s']=$v;
					$l3['i']=$key;
				}
			}
			unset($res[$key]['states']);
		}
		
		$res[$l1['i']]['rank']=1;
		$res[$l2['i']]['rank']=2;
		$res[$l3['i']]['rank']=3;
		
		return $res;

	}

	private function getGUIMenu($p=null)
	{

        $checkImages = isset($p['checkImages']) ? $p['checkImages'] : false;

        $g = isset($p['groups']) ? $p['groups'] : null;
        $c = isset($p['characters']) ? $p['characters'] : null;

		if ($checkImages) {

			foreach((array)$c as $key => $val) {
				if (isset($val['states'])) {
					foreach((array)$val['states'] as $sKey => $sVal) {
						$c[$key]['states'][$sKey]['file_exists'] = file_exists($_SESSION['app']['project']['urls']['projectMedia'].$sVal["file_name"]);
					}
				}
			}

		}


		$m = $this->models->GuiMenuOrder->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(), 
			),
			'columns' => 'ref_id,ref_type,show_order',
			'order' => 'show_order'
		));

        $appendExcluded = isset($p['appendExcluded']) ? $p['appendExcluded'] : false;
	
		if (is_null($g) || is_null($c))
			return;

		foreach ((array) $c as $val)
			$d[$val['id']] = $val;

		$c = $d;
		
		$d = array();
		$i=0;		

		foreach((array)$m as $val) {
			if ($val['ref_type']=='group') {
				foreach((array)$g[$val['ref_id']]['chars'] as $ckey => $char)
					$g[$val['ref_id']]['chars'][$ckey]['states'] = $c[$char['id']]['states'];
				$d[$i] = $g[$val['ref_id']];
				unset($g[$val['ref_id']]);
			} else {
				$d[$i] = $c[$val['ref_id']];
				unset($c[$val['ref_id']]);
			}
			$d[$i]['icon'] = '__menu'.preg_replace('/\W/','',ucwords($d[$i]['label'])).'.png';
			if ($checkImages)
				$d[$i]['icon_exists'] = file_exists($_SESSION['app']['project']['urls']['projectMedia'].$d[$i]['icon']);
			$i++;
		}

		if ($appendExcluded) {
	
			// append the items for some reason missing from the menu-order
			foreach((array)$c as $val)
				$d[] = $val;
		
			foreach((array)$g as $val)
				$d[] = $val;

		}

		return $d;		
		
	}




/*

    private function getTaxonComparisonORG ($id)
    {
        if (empty($id[0]) || empty($id[1]))
            return;
        
        $c = $this->getCharacteristics();
        $t1 = $this->getTaxonStates($id[0]);
        $t2 = $this->getTaxonStates($id[1]);

		$both = $neither = $t1_count = $t2_count = 0;
        
		// loop through all available states
        foreach ((array) $c as $key => $val) {
            
			
            if (isset($t1[$val['id']]) && isset($t2[$val['id']]) && ($t1[$val['id']]['states']['id'] == $t2[$val['id']]['states']['id'])) {
                
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


*/

}