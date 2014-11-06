<?php

/*

	!!!! needs optimization: $this->getVariationStates($val['id'])

*/

include_once ('Controller.php');
class MatrixKeyController extends Controller
{
    private $_matrixType = 'default';
	private $_useSepCoeffAsWeight = false;
	private $_matrixStateImageMaxHeight = null;
	private $_externalSpeciesUrlTarget = '_blank';
	private $_matrixSuppressDetails = false;
	private $_nbcImageRoot = null;
	private $_externalSpeciesUrlPrefix = null;
	private $_matrix_use_emerging_characters = null;
	private $__matrix_calc_char_h_val=true;

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



    public function __construct ($p = null)
    {
        parent::__construct($p);

        $this->initialize();

    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function indexAction ()
    {
		// reset session-saved matrix id
		$this->setCurrentMatrixId();
		// check for presence of 'mtrx' variable
        $this->checkMatrixIdOverride();
		$this->setStoreHistory(false);
		
		$matrices = $this->getMatrices();

		if (count((array)$matrices)>0) {
        
			$m = $this->getCurrentMatrixId();
			if (isset($m)) $this->redirect('identify.php');
	
			$m = $this->getDefaultMatrixId();
			if (isset($m)) {
				$matrix = $this->getMatrix($m);
				if (!is_null($matrix)) {
					$this->setCurrentMatrixId($m);
					$this->setTotalEntityCount();
					$this->redirect('identify.php');
				}
			}
	
			$m = $this->getFirstMatrixId();
			if (isset($m)) {
				$matrix = $this->getMatrix($m);
				if (!is_null($matrix)) {
					$this->setCurrentMatrixId($m);
					$this->setTotalEntityCount();
					$this->redirect('identify.php');
				}
			}
			
		}

		$this->printGenericError($this->translate('No matrices have been defined.'));

    }

    public function matricesAction ()
    {
		
		$this->storeHistory = false;

		if (!$this->rHasVal('action','popup'))
			$this->redirect('identify.php');

		$matrices = $this->getMatrices();
		$this->smarty->assign('matrices', $matrices);
		$this->smarty->assign('currentMatrixId', $this->getCurrentMatrixId());
        $this->printPage();

    }

    public function useMatrixAction ()
    {
        if ($this->rHasId()) {

            $this->storeHistory = false;

            $this->setCurrentMatrixId($this->requestData['id']);

			$this->setTotalEntityCount();

            $this->redirect('identify.php');
        }
        else {

            $this->printGenericError($this->translate('Missing matrix ID.'));
        }
    }

    public function identifyAction ()
    {
		
//$states = $this->stateMemoryRecall();$taxa = $this->nbcGetTaxaScores($states);die('satan');		
		
        $this->checkMatrixIdOverride();
        $this->checkMasterMatrixId();

        $id = $this->getCurrentMatrixId();
		$this->setTotalEntityCount();

        if (!isset($id)) {
            $this->storeHistory = false;
            $this->redirect('index.php');
        }
        
		$matrix = $this->getMatrix($id);

        if (empty($matrix)) {
            $this->storeHistory = false;
            $this->redirect('index.php');
        }


        $this->setPageName(sprintf($this->translate('Matrix "%s": identify'), $matrix['name']));
		
		$characters = $this->getCharacteristics();

        if ($this->_matrixType == 'nbc')
		{

            $states = $this->stateMemoryRecall();

            $taxa = $this->nbcGetTaxaScores($states);

			$groups = $this->getCharacterGroups();

			$activeChars = array();

            foreach ((array) $states as $val)
                $activeChars[$val['characteristic_id']] = true;

			$countPerState = $this->getRemainingStateCount(array(
				'states' => $states
			));

			$countPerCharacter = $this->getRemainingCharacterCount();

//			q($characters,1);

			$menu = $this->getGUIMenu(
					array(
						'groups'=>$groups,
						'characters'=>$characters,
						'appendExcluded'=>false,
						'checkImages'=>true
					)
				);
				
            $this->smarty->assign('taxaJSON', json_encode(
            array(
                'results' => $taxa, 
				'paramCount' => count((array)$states),
                'count' => array(
                    'results' => count((array) $taxa),
                ),
				'menu' => array(
					'groups' => $menu,
					'activeChars' => $activeChars,
					'storedStates' => $states
				),
				'countPerState' => $countPerState,
				'countPerCharacter' => $countPerCharacter,
				'selectedStates' => $states,
				'matrix' => $this->getCurrentMatrixId()
            ),JSON_HEX_APOS | JSON_HEX_QUOT));

            $this->smarty->assign('guiMenu',$menu);

			if ($this->_useSepCoeffAsWeight)
				$this->smarty->assign('coefficients', $this->getRelevantCoefficients($states));

            $this->smarty->assign('nbcImageRoot', $this->_nbcImageRoot);
            $this->smarty->assign('nbcFullDatasetCount', $_SESSION['app'][$this->spid()]['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']);
            $this->smarty->assign('nbcStart', $this->getSessionSetting('nbcStart'));
            $this->smarty->assign('nbcSimilar', $this->getSessionSetting('nbcSimilar'));
			$this->smarty->assign('nbcPerLine', $this->getSetting('matrix_items_per_line'));
			$this->smarty->assign('nbcPerPage', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('nbcBrowseStyle', $this->getSetting('matrix_browse_style'));
			$this->smarty->assign('matrix_items_per_page', $this->getSetting('matrix_items_per_page'));
			$this->smarty->assign('master_matrix_id', $this->getMasterMatrixId());
			
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
        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('function', 'Identify');
        $this->smarty->assign('characteristics', $characters);
		$this->smarty->assign('matrix_use_emerging_characters', $this->_matrix_use_emerging_characters);
		
        $this->printPage('identify');
    }


    public function examineAction ()
    {
        $this->checkMatrixIdOverride();
        
        $id = $this->getCurrentMatrixId();

        if (!isset($id)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
		$matrix = $this->getMatrix($id);

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
        
        $id = $this->getCurrentMatrixId();
        
        if (!isset($id)) {
            
            $this->storeHistory = false;
            
            $this->redirect('matrices.php');
        }
        
		$matrix = $this->getMatrix($id);

        $this->smarty->assign('function', 'Compare');
        
        $this->setPageName(sprintf($this->translate('Matrix "%s": compare'), $matrix['name']));
        
        $this->smarty->assign('projectId', $this->getCurrentProjectId());
        $this->smarty->assign('taxa', $this->getTaxaInMatrix());
        $this->smarty->assign('matrixCount', $this->getMatrixCount());
        $this->smarty->assign('matrix', $matrix);
        $this->smarty->assign('compareSpeciesRecall', $this->compareSpeciesRecall());
        
        $this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
		
		if ($this->rHasVar('key')) {
			$this->setCurrentMatrixId($this->requestData['key']);
			$this->setTotalEntityCount();
		}
		
		if (!$this->rHasVal('action'))
		{
            $this->smarty->assign('returnText', 'error');
        } else
		if ($this->rHasVal('action', 'get_states'))
		{
            $this->smarty->assign('returnText', json_encode($this->getCharacteristicStates($this->requestData['id'])));
        } else 
		if ($this->rHasVal('action', 'get_taxa'))
		{
            $this->stateMemoryUnset();

            $this->stateMemoryStore($this->requestData['id']);
            
            $this->smarty->assign(
				'returnText',
				json_encode(
					(array)$this->getTaxaScores($this->requestData['id'],
					isset($this->requestData['inc_unknowns']) ? ($this->requestData['inc_unknowns'] == '1') : false)
				)
			);
        }
        else
		if ($this->rHasVal('action', 'get_taxon_states'))
		{
            
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonStates($this->requestData['id'])));
        }
        else
		if ($this->rHasVal('action', 'compare'))
		{
            $this->smarty->assign('returnText', json_encode((array) $this->getTaxonComparison($this->requestData['id'])));
        }
        else
		if ($this->rHasVal('action', 'store_showstate_results'))
		{
            $this->showStateStore('results');
        }
        else
		if ($this->rHasVal('action', 'store_showstate_pattern'))
		{
            $this->showStateStore('pattern');
        }
        else
		if ($this->rHasVal('action', 'store_examine_val'))
		{
            $this->examineSpeciesStore($this->requestData['id']);
        }
        else
		if ($this->rHasVal('action', 'store_compare_vals'))
		{
            $this->compareSpeciesStore($this->requestData['id']);
        }
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','do_search'))
		{
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
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','save_session_setting'))
		{
            $this->saveSessionSetting($this->requestData['setting']);
        }
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','get_formatted_states'))
		{
            $c = $this->getCharacteristic(array('id'=>$this->requestData['id']));

            $c['prefix'] = ($c['type'] == 'media' || $c['type'] == 'text' ? 'c' : 'f');
            
            $s = $this->getCharacteristicStates($this->requestData['id']);
            $s = $this->fixStateLabels($s); // temporary measure (hopefully!)

            $states = $this->stateMemoryRecall();

            $countPerState = $this->getRemainingStateCount(array(
                'charId' => $this->requestData['id'], 
                'states' => $states
            ));
            $this->smarty->assign('remainingStateCount', $countPerState);

            $states = $this->nbcStateMemoryReformat($states);
		
            $this->smarty->assign('stateImagesPerRow',$this->getSetting('matrix_state_image_per_row',4));
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
		else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','get_results_nbc'))
		{
			$includeGroups = isset($this->requestData['params']['noGroups']) ? $this->requestData['params']['noGroups']!='1' : true;
			$includeActiveChars = isset($this->requestData['params']['noActiveChars']) ? $this->requestData['params']['noActiveChars']!='1' : true;

            $states = $this->stateMemoryRecall();

 			if (isset($this->requestData['params']['action']) && $this->requestData['params']['action'] == 'similar')                
                $results = $this->nbcGetSimilar($this->requestData['params']);
            else                
                $results = $this->nbcGetTaxaScores($states);
		
			if ($includeActiveChars) {

				$d = array();
	
				foreach ((array) $states as $val)
					$d[$val['characteristic_id']] = true;
					
			} else {
				
				$d=null;
				
			}

			array_walk($results, create_function('&$v,$k', '$v["l"] = ucfirst($v["l"]);'));

			$countPerState = $this->getRemainingStateCount(array(
				'states' => $states
			));
			
			$countPerCharacter = $this->getRemainingCharacterCount();
			
			if ($includeGroups) {
			
				$groups = $this->getGUIMenu(
					array(
						'groups' => $this->getCharacterGroups(),
						'characters' => $this->getCharacteristics(),
						'appendExcluded' => false,
						'checkImages' => true
					)
				);
	
				if (empty($groups))
					$groups = $this->getCharacterGroups();
					
			} else {
				
				$groups=null;
				
			}

			$result = 
				json_encode(
					array(
						'results' => $results, 
						'paramCount' => count((array)$states),
						'count' => array(
							'results' => count((array)$results)
						),
						'menu' => array(
							'groups' => $groups,
							'activeChars' => $d,
							'storedStates' => $this->stateMemoryRecall()
						),
						'countPerState' => $countPerState,
						'countPerCharacter' => $countPerCharacter,
						'selectedStates' => $states,
						'matrix' => $this->getCurrentMatrixId()	
					)
				);
			
			$this->smarty->assign('returnText', $result	);
			
        }
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','clear_state'))
		{

			if (!$this->rHasVal('state'))
				$this->stateMemoryUnset();
			else
				$this->stateMemoryUnset($this->requestData['state']);
				

		}
        else 
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','set_state') && $this->rHasVal('state'))
		{

			if ($this->rHasVal('value'))
				$state = $this->requestData['state'] . ':' . $this->requestData['value'];
			else
				$state = $this->requestData['state'];
		
			$this->stateMemoryStore($state);
			
			return;

		}
        else
		if ($this->_matrixType=='nbc' && $this->rHasVal('action','get_initial_values'))
		{
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
							'baseUrl' => $this->getProjectUrl('projectMedia'),
							'baseUrlSystem' => $this->getProjectUrl('systemMedia'),
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

    public function setCurrentMatrixId($id=null)
    {
		if (is_null($id))
			unset($_SESSION['app'][$this->spid()]['matrix']['active']);
		else
	        $_SESSION['app'][$this->spid()]['matrix']['active'] = $id;
    }

    public function getCurrentMatrixId ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['active']) ? $_SESSION['app'][$this->spid()]['matrix']['active'] : null;
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
        $this->_matrixType = strtolower($this->getSetting('matrixtype'));
        $this->_useCharacterGroups = $this->getSetting('matrix_use_character_groups') == '1';
        $this->useVariations = $this->getSetting('taxa_use_variations') == '1';
        $this->smarty->assign('useCharacterGroups', $this->_useCharacterGroups);
        $this->_useSepCoeffAsWeight = false; // $this->getSetting('matrix_use_sc_as_weight');
        $this->_matrixStateImageMaxHeight = $this->getSetting('matrix_state_image_max_height');
        $this->_externalSpeciesUrlTarget = $this->getSetting('external_species_url_target');
        $this->_matrixSuppressDetails = $this->getSetting('matrix_suppress_details','0')=='1';
		$this->_externalSpeciesUrlPrefix = $this->getSetting('external_species_url_prefix');
		$this->_matrix_use_emerging_characters = $this->getSetting('matrix_use_emerging_characters',true);
		$this->_matrix_calc_char_h_val = $this->getSetting('matrix_calc_char_h_val',true);


        if ($this->_matrixType == 'nbc') {
			$_SESSION['app']['system']['urls']['nbcImageRoot']=$this->_nbcImageRoot = $this->getSetting('nbc_image_root');
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
			
			$taxa=null;
            
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
        if (!$this->rHasVal('mtrx'))
			return;

		$this->setCurrentMatrixId($this->requestData['mtrx']);
		$this->setTotalEntityCount();
			
    }
	
    private function setMasterMatrixId ($id=null)
    {
		if (is_null($id))
			unset($_SESSION['app'][$this->spid()]['matrix']['masterId']);
		else
			$_SESSION['app'][$this->spid()]['matrix']['masterId']=$id;
    }

    private function getMasterMatrixId ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['masterId']) ? $_SESSION['app'][$this->spid()]['matrix']['masterId'] : null;
    }

    private function checkMasterMatrixId ()
    {

        $mts = $this->models->MatrixTaxonState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id !=' => $this->getCurrentMatrixId(), 
                'ref_matrix_id' => $this->getCurrentMatrixId()
            ), 
            'columns' => 'distinct matrix_id'
        ));		
		
		if ($mts)
			$this->setMasterMatrixId($mts[0]['matrix_id']);
		else
			$this->setMasterMatrixId(null);

		/*		
        if ($this->rHasVal('main'))
			$this->setMasterMatrixId($this->requestData['main']);
		else
			$this->setMasterMatrixId(null);
		*/
    }

    private function setTotalEntityCount ($id=null)
    {
		
		if (is_null($id)) $id=$this->getCurrentMatrixId();
		
		if (is_null($id)) return;

		if (empty($_SESSION['app'][$this->spid()]['matrix'][$this->getCurrentMatrixId()]['totalEntityCount']))
			$_SESSION['app'][$this->spid()]['matrix'][$this->getCurrentMatrixId()]['totalEntityCount'] = $this->getTotalEntityCount();

    }

    private function getMatrixCount ()
    {
        $m = $this->getMatrices();
        
        return count((array) $m);
    }

    private function getMatrix($id)
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
                $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$d[0] . ':' . $d[1]] = $val;
            else // c:x:y (fixed values)
                $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$val] = $val;
        }
		
    }

    private function stateMemoryUnset ($id = null)
    {
        if (empty($id))
            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()]);
        else
            unset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()][$id]);
    }

    private function stateMemoryRecall ($p = null)
    {
        if (isset($_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()])) {
            
            $charId = isset($p['charId']) ? $p['charId'] : null;
            
            $states = array();
            
            foreach ((array) $_SESSION['app'][$this->spid()]['matrix']['storedStates'][$this->getCurrentMatrixId()] as $key => $val) {
                
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
		
		if (is_null($this->_matrixStateImageMaxHeight) || ($d[1] < $this->_matrixStateImageMaxHeight))
			return $d;

		return array(round(($this->_matrixStateImageMaxHeight / $d[1]) * $d[0]),$this->_matrixStateImageMaxHeight);
		
	}

    private function getCharacteristicStates ($id)
    {
        if (!isset($id))
            return;

        $cs=$this->models->CharacteristicState->freeQuery("
			select 
				_a.id,
				_a.characteristic_id,
				_a.file_name,
				_a.file_dimensions,
				_a.lower,
				_a.upper,
				_a.mean,
				_a.sd,
				_a.got_labels,
				_a.show_order,
				_b.type,
				_c.label,
				_c.text
				
			from %PRE%characteristics_states _a
			
			left join %PRE%characteristics _b
				on _a.characteristic_id = _b.id
				and _a.project_id=_b.project_id

			left join %PRE%characteristics_labels_states _c
				on _a.id = _c.state_id
				and _a.project_id=_c.project_id
				and _c.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id=".$this->getCurrentProjectId()." 
				".($id=='*' ? "" : "and _a.characteristic_id=".$id)." 

			order by 
				_a.show_order
			"
		);
		
        foreach ((array) $cs as $key => $val)
            $cs[$key]['img_dimensions']=explode(':',$val['file_dimensions']);

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
            'columns' => 'project_id,id,characteristic_id,file_name,lower,upper,mean,sd,got_labels', 
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

	/*
		each selected state further restricts the result set
		example: red AND black AND round
	*/
    private function _getTaxaScoresRestrictive ($states, $incUnknowns = false)
    {
        $s = $c = array();
        $stateCount = 0;
        
        // we have to find out which states we are looking for
        foreach ((array) $states as $sKey => $sVal)
		{
            $d = explode(':', $sVal);
            
            $charId = isset($d[1]) ? $d[1] : null;
            $value = isset($d[2]) ? $d[2] : null;

            // which is easy for the non-range characters...
            if ($d[0] != 'f') {
                
                if (isset($d[2]))
                    $s[$d[2]] = $d[2];
                $stateCount++;
            }
            else
            // ...but requires calculation for the ranged ones
			{

                // is there a standard dev?
                $sd = (isset($d[3]) ? $d[3] : null);
                
                // where-clause basics
                $d = array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $charId
                );
				
				$value = str_replace(',','.',$value);

                if (isset($sd))
                // calculate the spread around the mean...
				{
					$sd = str_replace(',','.',$sd);
                    $d['mean >=#'] = '(' . strval(floatval($value)) . ' - (' . strval(intval($sd)) . ' * sd))';
                    $d['mean <=#'] = '(' . strval(floatval($value)) . ' + (' . strval(intval($sd)) . ' * sd))';
                }
                else
                // or mark just mark the upper and lower boundaries of the value
				{
                    
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
        
        $n = $stateCount;
        $si = implode(',', $s);
        $ci = implode(',', $c);

		// query to get all taxa, matrices and variations, including their matching percentage
        $q = "
        	select 'taxon' as type, _a.taxon_id as id,
       				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
       				_c.is_hybrid as h, trim(_c.taxon) as l
			from %PRE%matrices_taxa _a
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.taxon_id = _b.taxon_id
				and _b.state_id in (" . $si . ")
			left join %PRE%taxa _c
				on _a.taxon_id = _c.id
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "
			group by _a.taxon_id

        	union all

        	select 'matrix' as type, _a.id as id, 
				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
				0 as h, trim(_c.name) as l
			from  %PRE%matrices _a
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _b.matrix_id = " . $this->getCurrentMatrixId() . "
				and _a.id = _b.ref_matrix_id
				and _b.state_id in (" . $si . ")
			left join %PRE%matrices_names _c
				on _a.id = _c.matrix_id
				and _c.language_id = " . $this->getCurrentLanguageId() . "
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.id != " . $this->getCurrentMatrixId() . "
			group by id" . ($this->_matrixType == 'nbc' ? "

			union all

			select 'variation' as type, _a.variation_id as id, 
				count(_b.state_id) as tot, round((if(count(_b.state_id)>" . $n . "," . $n . ",count(_b.state_id))/" . $n . ")*100,0) as s,
				0 as h, trim(_c.label) as l
			from  %PRE%matrices_variations _a        		
			left join %PRE%matrices_taxa_states _b
				on _a.project_id = _b.project_id
				and _a.matrix_id = _b.matrix_id
				and _a.variation_id = _b.variation_id
				and _b.state_id in (" . $si . ")
			left join %PRE%taxa_variations _c
				on _a.variation_id = _c.id
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "
			group by _a.variation_id" : "")
		;

        $results = $this->models->MatrixTaxonState->freeQuery($q);

		/*
			"unknowns" are taxa for which no state has been defined within a certain character.
			note that this is different froam having a differen state within that character. if
			there is a character "colour", and taxon A has the state "green", taxon B has the 
			state "brown" and taxon C has no state for colour, then selecting "brown" with 'Treat 
			unknowns as matches' set to false will yield A:0%, B:100%, C:0%. selecting "brown" 
			with 'Treat unknowns as matches' set to true will yield A:0%, B:100%, C:100%. it can
			be seen as a 'rather safe than sorry' setting.
		*/
		if ($incUnknowns)
		{
			
			$unknowns=array('taxon'=>array(),'matrix'=>array(),'variation'=>array());
			
			foreach((array)$c as $character)
			{
				$q = "
					select
						'taxon' as type, 
						_a.taxon_id as id,
						_c.is_hybrid as h, 
						trim(_c.taxon) as l
					from
						%PRE%matrices_taxa _a
					left join %PRE%taxa _c
						on _a.taxon_id = _c.id
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
						and _b.characteristic_id =".$character."
					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by
						_a.taxon_id
					having count(_b.id)=0

					union all

					select
						'matrix' as type, 
						_a.id as id,
						0 as h, 
						trim(_c.name) as l
					from
						%PRE%matrices _a

					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _b.matrix_id = " . $this->getCurrentMatrixId() . "
						and _a.id = _b.ref_matrix_id
						and _b.characteristic_id =".$character."
					left join %PRE%matrices_names _c
						on _a.id = _c.matrix_id
						and _c.language_id = " . $this->getCurrentLanguageId() . "
					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.id != " . $this->getCurrentMatrixId() . "

					group by
						_a.id
					having count(_b.id)=0

				".($this->_matrixType == 'nbc' ? "
		
					union all

					select
						'variation' as type, 
						_a.variation_id as id,
						0 as h, 
						trim(_c.label) as l
					from
						%PRE%matrices_variations _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.variation_id = _b.variation_id
						and _b.characteristic_id =".$character."
					left join %PRE%taxa_variations _c
						on _a.variation_id = _c.id
					where
						_a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "

					group by
						_a.variation_id
					having count(_b.id)=0
				":"");

		        $rrr=$this->models->MatrixTaxonState->freeQuery($q);

				foreach((array)$rrr as $r)
				{
					switch($r['type'])
					{
						case 'taxon': 
							$unknowns['taxon'][$r['id']]=$r;
							isset($unknowns['taxon'][$r['id']]['tot']) ?
								$unknowns['taxon'][$r['id']]['tot']++ : 
								$unknowns['taxon'][$r['id']]['tot']=1;

							$unknowns['taxon'][$r['id']]['s'] = 
								round(($unknowns['taxon'][$r['id']]['tot']/$n)*100);

							break;
						case 'matrix':
							$unknowns['matrix'][$r['id']]=$r;
							isset($unknowns['matrix'][$r['id']]['tot']) ?
								$unknowns['matrix'][$r['id']]['tot']++ : 
								$unknowns['matrix'][$r['id']]['tot']=1;

							$unknowns['matrix'][$r['id']]['s'] = 
								round(($unknowns['matrix'][$r['id']]['tot']/$n)*100);


							break;
						case 'variation':
							$unknowns['variation'][$r['id']]=$r;
							isset($unknowns['variation'][$r['id']]['tot']) ?
								$unknowns['variation'][$r['id']]['tot']++ : 
								$unknowns['variation'][$r['id']]['tot']=1;

							$unknowns['variation'][$r['id']]['s'] = 
								round(($unknowns['variation'][$r['id']]['tot']/$n)*100);

							break;
					}
				}
			}

			foreach((array)$results as $key => $val)
			{
				if (isset($unknowns[$val['type']][$val['id']]))
				{
					$temp=$unknowns[$val['type']][$val['id']];
					$results[$key]['tot']+=$temp['tot'];
					$results[$key]['s']=round(($results[$key]['tot']/$n)*100);
					unset($unknowns[$val['type']][$val['id']]);
				}
			}
	
			foreach((array)$unknowns as $type)
			{
				foreach((array)$type as $key => $val)
				{
					array_push($results,$val);
				}
			}

		}


        usort($results, array(
            $this, 
            'sortQueryResultsByScoreThenLabel'
        ));

        return $results;
    }
	

	/*
	
		DOES NOT WORK YET - WORK IN PROGRESS
	
		states within the same charachters expand the result set,
		selected states across characters restrict the result set
		example: (red OR black) AND round
	*/
    private function _getTaxaScoresLiberal ($states, $incUnknowns = false)
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
        	select 'taxon' as type, _a.taxon_id as id, _b.state_id, _b.characteristic_id,
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
        	union all
        	select 'matrix' as type, _a.id as id, _b.state_id, _b.characteristic_id,
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
        		group by id" . ($this->_matrixType == 'nbc' ? "
			union all
			select 'variation' as type, _a.variation_id as id, _b.state_id, _b.characteristic_id,
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
				group by _a.variation_id" : "")."
			order by characteristic_id"
        ;

        $results = $this->models->MatrixTaxonState->freeQuery($q);

		$d = array();
		$prevChar = -99;
		foreach((array)$results as $val) {

			if (!isset($d[$val['id']]))
				$d[$val['id']] = $val;

			if (!isset($d[$val['id']]['s']))
				$d[$val['id']]['s'] = 0;
				
			if (!empty($val['state_id'])) {
				if ($val['state_id']==$prevChar)
					$d[$val['id']]['s']++;
				else
					$d[$val['id']]['s']++;
			}
			
			$prevChar = $val['characteristic_id'];

		}

        usort($results, array(
            $this, 
            'sortQueryResultsByScoreThenLabel'
        ));

        return $results;
    }

    private function getTaxaScores ($states, $incUnknowns = false)
	{

		$res = $this->_getTaxaScoresRestrictive ($states,$incUnknowns);
		//$res = $this->_getTaxaScoresLiberal ($states,$incUnknowns);

		return $res;

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

    private function getCharacteristicHValue ($charId,$states=null)
    {
		
		if (!$this->_matrix_calc_char_h_val)
			return null;
		
        $states=is_null($states) ? $this->getCharacteristicStates($charId) : $states;
        
        $taxa=array();
        $tot=0;
			        
        foreach((array)$states as $key=>$val) {
            
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

        $mc = $this->models->CharacteristicMatrix->freeQuery("
			select
				_a.characteristic_id as id,
				_b.type,
				_c.label,
				if (_b.type='media'||_b.type='text','c','f') as prefix,
				_a.show_order,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,1,locate('|',_c.label)-1)) as label_short,
				if(locate('|',_c.label)=0,_c.label,substring(_c.label,locate('|',_c.label)+1)) as description

			from 
				%PRE%characteristics_matrices _a
				
			right join %PRE%characteristics _b
				on _a.project_id = _b.project_id
				and _a.characteristic_id = _b.id

			left join %PRE%characteristics_labels _c
				on _a.project_id=_c.project_id
				and _a.characteristic_id=_c.characteristic_id
				and _c.language_id=".$this->getCurrentLanguageId()."
				
			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.matrix_id = ".$this->getCurrentMatrixId()." 
			order by 
				_a.show_order
		");

		$allStates=array();

		foreach((array)$this->getCharacteristicStates('*') as $val)
			$allStates[$val['characteristic_id']][]=$val;
	
        foreach ((array) $mc as $key => $val) {
                $mc[$key]['states']=$allStates[$val['id']];
                $mc[$key]['sort_by'] = array(
					'alphabet' => isset($val['label']) ? strtolower(preg_replace('/[^A-Za-z0-9]/', '', $val['label'])) : '',
					'characterType' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $val['type'])),
					'numberOfStates' => -1 * count((array)$mc[$key]['states']), // -1 to avoid asc/desc hassles in JS-sorting
					'entryOrder' => intval($val['show_order']),
					'separationCoefficient' => -1 * $this->getCharacteristicHValue($val['id'],$mc[$key]['states']) // -1 to avoid asc/desc hassles in JS-sorting
				);

        }

        return isset($mc) ? $mc : null;

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

    private function getEntityStates($id,$type)
    {
		$mts=$this->models->MatrixTaxonState->freeQuery("
		select 
			_c.label as characteristic,
			_b.type,
			_b.type,
			_a.project_id,
			_a.state_id,
			_d.characteristic_id,
			_d.file_name,
			_d.lower,
			_d.upper,
			_d.mean,
			_d.sd,
			_d.got_labels,
			_e.label
			
		from %PRE%matrices_taxa_states _a
		
		left join %PRE%characteristics_states _d
			on _a.project_id=_d.project_id
			and _a.state_id=_d.id

		left join %PRE%characteristics _b
			on _a.project_id=_b.project_id
			and _a.characteristic_id=_b.id

		left join %PRE%characteristics_labels _c
			on _a.project_id=_c.project_id
			and _a.characteristic_id=_c.characteristic_id
			and _c.language_id=".$this->getCurrentLanguageId()."

		left join %PRE%characteristics_labels_states _e
			on _a.state_id = _e.state_id
			and _a.project_id=_e.project_id
			and _e.language_id=".$this->getCurrentLanguageId()."

		
		where 
			_a.project_id = ".$this->getCurrentProjectId()." 
			and _a.matrix_id = ".$this->getCurrentMatrixId()." 
			and _a.".($type=="variation" ? "variation_id" : ($type=="matrix" ? "ref_matrix_id" : "taxon_id"))."=".$id
		);		
		
		$res=array();
		foreach((array)$mts as $val) {

			$d=explode('|',$val['characteristic']);
			$res[$val['characteristic_id']]['characteristic']=$d[0];
			//$res[$val['characteristic_id']]['explanation']=$d[1];
			$res[$val['characteristic_id']]['type'] = $val['type'];
			$res[$val['characteristic_id']]['states'][$val['state_id']] = array(
				'characteristic_id'=>$val['characteristic_id'],
				'id'=>$val['state_id'],
				'file_name'=>$val['file_name'],
				'lower'=>$val['lower'],
				'upper'=>$val['upper'],
				'mean'=>$val['mean'],
				'sd'=>$val['sd'],
				'got_labels'=>$val['got_labels'],
				'label'=>$val['label']
			);
		}
		
		//q($res,1);
		return $res;
    }

    private function getTaxonStates($id)
    {
        return $this->getEntityStates ($id,'taxon');
    }

    private function getVariationStates($id)
    {
        return $this->getEntityStates ($id,'variation');
    }
    
    private function getMatrixStates($id)
    {
        return $this->getEntityStates ($id,'matrix');
    }

    private function getTaxonComparison($id)
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
        $_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()] = $state;
    }

    private function showStateRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['storesShowState'][$this->getCurrentMatrixId()] : 'pattern';
    }

    private function examineSpeciesStore ($id)
    {
        $_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }

    private function examineSpeciesRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['examineSpeciesState'][$this->getCurrentMatrixId()] : null;
    }

    private function compareSpeciesStore ($id)
    {
        $_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] = $id;
    }

    private function compareSpeciesRecall ()
    {
        return isset($_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()]) ? $_SESSION['app'][$this->spid()]['matrix']['compareSpeciesState'][$this->getCurrentMatrixId()] : null;
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
            unset($_SESSION['app'][$this->spid()]['matrix']['settings'][$setting['name']]);
        else
            $_SESSION['app'][$this->spid()]['matrix']['settings'][$setting['name']] = $setting['value'];
    }

    private function getSessionSetting ($name)
    {
        if (!isset($name) || !isset($_SESSION['app'][$this->spid()]['matrix']['settings'][$name]))
            return;

        return $_SESSION['app'][$this->spid()]['matrix']['settings'][$name];
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
        $mv = $this->models->MatrixVariation->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'fieldAsIndex' => 'variation_id'
        ));
		
		if (is_null($mv))
			return;
        
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
                    $rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id'])); // is that even legal, semantically?
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
        $image = isset($p['image']) ? $p['image'] : null;

		$type = ($type=='variation' ? 'v' : ($type=='matrix' ? 'm' : ($type=='taxon' ? 't' : $type)));

		$sciName = 
			strip_tags(($type=='t'||$type=='m' ? $val['l'] : (isset($val['taxon']) && !is_array($val['taxon']) ? $val['taxon'] : (isset($val['taxon']['taxon']) ? $val['taxon']['taxon'] : null))));

		if (isset($nbc['url_external_page'])) {
			if (preg_match('/^(https?|ftps?):\/\//i',trim($nbc['url_external_page']['value']))===1)
				$urlExternalPage = $nbc['url_external_page']['value'];
			else
				$urlExternalPage = $this->_externalSpeciesUrlPrefix.$nbc['url_external_page']['value'];
		} else {
			$urlExternalPage = null;
		}
		
		$image = (isset($nbc['url_image']) ? $nbc['url_image']['value'] : (isset($image) ? $image : null));

        $d = array(
            'i' => $val['id'], 
            'l' => trim(strip_tags($label)), 
			'c' => $common,
            'y' => $type, 
            's' => trim(strip_tags($sciName)),
            'm' => $image, 
            'n' => isset($image),
			'x' => isset($image) ? null : $this->_nbcImageRoot.'noimage.gif',
            'b' => isset($nbc['url_thumbnail']) ? $nbc['url_thumbnail']['value'] : null, 
            'p' => isset($nbc['photographer']) ? $nbc['photographer']['value'] : null, 
            'u' => $urlExternalPage, 
			'v' => $this->_externalSpeciesUrlTarget,  // default _blank
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

    private function makeRemainingCountClauses ($p=null)
    {
        $charIdToShow = isset($p['charId']) ? $p['charId'] : null;
        $states = isset($p['states']) ? $p['states'] : $this->stateMemoryRecall();
        $groupByCharId = isset($p['groupByCharId']) ? $p['groupByCharId'] : false;

        $dT = $dV = $dM = '';
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
						and (_a.taxon_id is not null or _a.variation_id is null or _a.ref_matrix_id is null)
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

        if (!empty($dT)) {
            $dV = str_replace('variation_id is null', 'taxon_id is null', str_replace('taxon_id', 'variation_id', $dT));
            $dM = str_replace('ref_matrix_id is null', 'variation_id is null', str_replace('taxon_id', 'ref_matrix_id', $dT));
		}
		
		return
			array(
				'dT' => isset($dT) ? $dT : null,
				'fsT' => isset($fsT) ? $fsT : null,
				'dV' => isset($dV) ? $dV : null,
				'fsV' => isset($fsV) ? $fsV : null,
				'dM' => isset($dM) ? $dM : null,
				'fsM' => isset($fsM) ? $fsM : null
			);
						
	}

    private function getRemainingStateCount ($p=null)
    {
        $charIdToShow = isset($p['charId']) ? $p['charId'] : null;
        $states = isset($p['states']) ? $p['states'] : $this->stateMemoryRecall();
        $groupByCharId = isset($p['groupByCharId']) ? $p['groupByCharId'] : false;

		$s = array();
    
		$c=$this->makeRemainingCountClauses($p);

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];

       
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
				union all
				select count(distinct _a.variation_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by _a.state_id
				union all
				select count(distinct _a.ref_matrix_id) as tot, _a.state_id as state_id, _a.characteristic_id as characteristic_id
					from %PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					group by _a.state_id


			) as q1 
			group by q1.state_id
			";

        $results = $this->models->MatrixTaxonState->freeQuery($q);
		//q($this->models->MatrixTaxonState->q(),1);
        
        $all = array();
    
        foreach ((array) $results as $val) {

            if (!empty($charIdToShow) && $val['characteristic_id']!=$charIdToShow) continue;
    
			if ($groupByCharId) {
	            $all[$val['characteristic_id']]['states'][$val['state_id']] = intval($val['tot']);
	            $all[$val['characteristic_id']]['tot'] =
					(isset($all[$val['characteristic_id']]['tot']) ? $all[$val['characteristic_id']]['tot'] : 0) + intval($val['tot']);
			} else {
	            $all[$val['state_id']] = intval($val['tot']);
			}
    
        }

        return empty($all) ? '*' : $all;
    }

    private function getRemainingCharacterCount ($p=null)
    {

		$c=$this->makeRemainingCountClauses($p);

		$dT = $c['dT'];
		$fsT = $c['fsT'];
		$dV = $c['dV'];
		$fsV = $c['fsV'];
		$dM = $c['dM'];
		$fsM = $c['fsM'];

        $q = "
        	select
				sum(taxon_count) as taxon_count, 
				sum(distinct_state_count) as distinct_state_count, 
				characteristic_id 
        	from (
        		select
					_a.characteristic_id as characteristic_id,
					count(distinct _a.taxon_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dT) ? $dT : "") . "
					" . (!empty($fsT) ?  $fsT : ""). "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
					and _a.taxon_id not in
						(select taxon_id from %PRE%taxa_variations where project_id = " . $this->getCurrentProjectId() . ")
				group by
					_a.characteristic_id
				
				union

				select 
					_a.characteristic_id as characteristic_id,
					count(distinct _a.variation_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dV) ? $dV : "") . "
					" . (!empty($fsV) ? $fsV : "") . "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by
					_a.characteristic_id

				union

				select 
					_a.characteristic_id as characteristic_id,
					count(distinct _a.ref_matrix_id) as taxon_count, 
					count(distinct _a.state_id) as distinct_state_count
				from
					%PRE%matrices_taxa_states _a
					" . (!empty($dM) ? $dM : "") . "
					" . (!empty($fsM) ? $fsM : "") . "
				where
					_a.project_id = " . $this->getCurrentProjectId() . "
					and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				group by
					_a.characteristic_id

			) as q1 
			group by q1.characteristic_id
			";
			

        $results = $this->models->MatrixTaxonState->freeQuery($q);

        $characteristics = array();
    
        foreach ((array) $results as $val) {

           $characteristics[$val['characteristic_id']]=array('taxon_count'=>$val['taxon_count'],'distinct_state_count'=>$val['distinct_state_count']);

        }

        return $characteristics;
    }

	private function getTotalEntityCount()
	{

        if ($this->_matrixType == 'nbc')

			$q = 
			"select count(distinct _a.taxon_id) as tot
					from %PRE%matrices_taxa _a
					left join %PRE%matrices_taxa_states _b
						on _a.project_id = _b.project_id
						and _a.matrix_id = _b.matrix_id
						and _a.taxon_id = _b.taxon_id
					where _a.project_id = " . $this->getCurrentProjectId() . "
						and _a.matrix_id = " . $this->getCurrentMatrixId() . "
				union all
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

        $res = $this->getCache('matrix-nbc-data-'.$this->getCurrentMatrixId());

        if (!$res) {

            $inclRelated = isset($p['inclRelated']) ? $p['inclRelated'] : false;
            $tId = isset($p['tId']) ? $p['tId'] : false;
            $vId = isset($p['vId']) ? $p['vId'] : false;
            
            $var = $this->getVariationsInMatrix();

            foreach ((array)$var as $val) {

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
                    'label' => $val['label'], 
					'common' => $this->getCommonname($val['taxon_id']),
                    'gender' => array($d['gender'], $d['gender_label']),
                    'related' => $this->getRelatedEntities(array(
                        'vId' => $val['id']
                    )), 
                    'type' => 'v', 
                    'inclRelated' => $inclRelated,
					'details' => $this->_matrixSuppressDetails ? null : $this->getVariationStates($val['id'])
                ));
                
                $tmp[$val['taxon_id']] = true;
            }

            $taxa = $this->getTaxaInMatrix();

            foreach ((array)$taxa as $val) {

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
						'details' => $this->_matrixSuppressDetails ? null : $this->getTaxonStates($val['id'])
                    ));
                }
            }

            $matrix = $this->getMatricesInMatrix();

            foreach ((array) $matrix as $val) {

                if ($vId && $val['id'] != $vId)
                    continue;
					
				$image = $val['l'].'.jpg';

                $res[] = $this->createDatasetEntry(
                array(
                    'val' => $val, 
                    'label' => $val['l'], 
                    'type' => 'm', 
                    'inclRelated' => false,
					'details' => $this->_matrixSuppressDetails ? null : $this->getMatrixStates($val['id']),
					'image' => file_exists($this->getProjectUrl('projectMedia').$image) ? $image : null,
                ));
                
            }

			$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));

            $this->customSortArray($res, array(
                'key' => 'l', 
                'case' => 'i',
				'dir' => 'asc'
            ));
			
            $this->saveCache('matrix-nbc-data-'.$this->getCurrentMatrixId(), $res);
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
                    'details' => $this->_matrixSuppressDetails ? null : $this->getVariationStates($val['relation_id'])
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
                    'details' => $this->_matrixSuppressDetails ? null : $this->getTaxonStates($taxon['id'])
                ));
            }
        }
		
		$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));
        
        return $res;
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

        // only keep the 100% scores, no partial matches for naturalis
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
                        'label' => $val['label'], 
						'common' => $this->getCommonname($val['taxon_id']),
                        'gender' => array($d['gender'], $d['gender_label']),
                        'related' => $this->getRelatedEntities(array(
                            'vId' => $val['id']
                        )), 
                        'type' => 'v', 
                        'inclRelated' => false,
						'details' => $this->_matrixSuppressDetails ? null : $this->getVariationStates($val['id'])
                    ));
					
                } else
                if ($match['type'] == 'matrix') {

					$image = $match['l'].'.jpg';

                    $res[] = $this->createDatasetEntry(
                    array(
                        'val' => $match, 
                        'label' => $match['l'], 
                        'type' => 'm', 
						'image' => file_exists($this->getProjectUrl('projectMedia').$image) ? $image : null,
                        'inclRelated' => false,
						'details' => $this->_matrixSuppressDetails ? null : $this->getMatrixStates($match['id'])
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
						'details' => $this->_matrixSuppressDetails ? null : $this->getTaxonStates($match['id'])
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
	            'case' => 'i',
				'dir' => 'asc'
	        ));

        }
		
		if ($this->_matrixSuppressDetails!=true && count((array)$res)!=0)
			$res = $this->nbcHandleOverlappingItemsFromDetails(array('data'=>$res,'action'=>'remove'));

        return $res;
    }

	private function nbcExtractGenderTag($label)
	{

		$gender=$gender_label=null;

		if (
			preg_match('/\s(man|vrouw|beide)(\s|$)/', $label, $matches) ||
			preg_match('/\s(male|female|both)(\s|$)/', $label, $matches)
			) {
			
			switch($matches[1]) {
				case 'man' :
				case 'male':
					$gender='m';
					$gender_label='man';
					break;
				case 'vrouw' :
				case 'female':
					$gender='f';
					$gender_label='vrouw';
					break;
			}
				
		}

		return
			array(
				'gender' => $gender,
				'gender_label' => $this->translate($gender_label)
			);
		
	}
	
	private function nbcDoSearch($p=null)
    {
        if (!isset($p['term']))
            return;
			
		$term = mysql_real_escape_string(strtolower($p['term']));

		// n.b. don't change to 'union all'
        $q = "
			select
				'variation' as type,
				_a.variation_id as id,
				trim(_c.label) as label,
				trim(_c.label) as l,
				_c.taxon_id as taxon_id,
				_d.taxon as taxon, 
				1 as s, 
				null as commonname
			from  %PRE%matrices_variations _a        		
			left join %PRE%matrices_taxa_states _b
				on _a.matrix_id = _b.matrix_id
				and _a.variation_id = _b.variation_id
				and _b.project_id = " . $this->getCurrentProjectId() . "
			left join %PRE%taxa_variations _c
				on _a.variation_id = _c.id
				and _c.project_id = " . $this->getCurrentProjectId() . "
			left join %PRE%taxa _d
				on _c.taxon_id = _d.id						
				and _d.project_id = " . $this->getCurrentProjectId() . "
			where _a.project_id = " . $this->getCurrentProjectId() . "
				and _a.matrix_id = " . $this->getCurrentMatrixId() . "
			and (lower(_c.label) like '%". $term ."%' or lower(_d.taxon) like '%". $term ."%')

			union

			select 
				'taxon' as type,
				_a.taxon_id as id, 
				trim(_c.taxon) as label, 
				trim(_c.taxon) as l, 
				_a.taxon_id as taxon_id,
				_c.taxon as taxon, 
				1 as s, 
				_d.commonname as commonname
			from %PRE%matrices_taxa _a
			left join %PRE%matrices_taxa_states _b
				on _a.matrix_id = _b.matrix_id
				and _a.taxon_id = _b.taxon_id
				and _b.project_id = " . $this->getCurrentProjectId() . "
			left join %PRE%taxa _c
				on _a.taxon_id = _c.id
				and _c.project_id = " . $this->getCurrentProjectId() . "
			left join %PRE%commonnames _d
				on _a.taxon_id = _d.taxon_id
				and _d.language_id = ".$this->getCurrentLanguageId() ." 
				and _d.project_id = " . $this->getCurrentProjectId() . "
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
				
				//$label = $d['label']; ???

				$gender = $this->nbcExtractGenderTag($val['label']);

				$common = $this->getCommonname($val['taxon_id']);
			
			} else {

				//$label = $val['label']; ???

				$gender = array();

				if ($val['commonname'] != $val['label'])
					$label = $val['commonname'];

				$common = $val['commonname'];

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
					'label' => $val['label'], 
					'common' => $common,
					'gender' => $gender,
					'related' => $this->getRelatedEntities(array(($val['type']=='variation' ? 'vId' : 'tId') => $val['id'])), 
					'type' => $val['type'], 
					'inclRelated' => true,
					'details' => ($val['type']=='variation' ? $this->getVariationStates($val['id']) : $this->getTaxonStates($val['id']))
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
					if (isset($state['id']))
						$d[$key][] = $characteristic_id.':'.$state['id']; // characteristic_id:state_id
				}
			}
		}

		$common = call_user_func_array('array_intersect',$d);

		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal) {
				foreach((array)$cVal['states'] as $sVal => $state)	{
					
					if (isset($state['id'])) {

						if (in_array($characteristic_id.':'.$state['id'],$common)) {
							if ($action=='remove') {
								unset($data[$key]['d'][$characteristic_id]['states'][$sVal]);
							} else
							if ($action=='tag') {
								$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'] = '<span class="overlapState">'.$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'].'</span>';
							}
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

        $g = isset($p['groups']) ? $p['groups'] : null;
        $c = isset($p['characters']) ? $p['characters'] : null;

		// need groups or isolated characters, otherwise nothing to show
		if (is_null($g) || is_null($c))
			return;
			
		// check if images actually exist
        $checkImages = isset($p['checkImages']) ? $p['checkImages'] : false;
		// append characters that do not appear in the menu order
        $appendExcluded = (isset($p['appendExcluded']) ? $p['appendExcluded'] : false);
		
		if ($checkImages) {

			foreach((array)$c as $key => $val) {
				if (isset($val['states'])) {
					foreach((array)$val['states'] as $sKey => $sVal) {
						$c[$key]['states'][$sKey]['file_exists'] = 
							file_exists($this->getProjectUrl('projectMedia').$sVal["file_name"]);
					}
				}
			}

		}
		
		foreach ((array) $c as $val)
			$dummy[$val['id']] = $val;

		$c = $dummy;

		$m = $this->models->GuiMenuOrder->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $this->getCurrentMatrixId(), 
			),
			'columns' => 'ref_id,ref_type,show_order',
			'order' => 'show_order'
		));

		if (!$m) {
			$i=0;
			foreach((array)$g as $val)
				$m[] = array('ref_id'=>$val['id'],'ref_type'=>'group','show_order'=>$i++);

			$dummy = $this->models->Characteristic->freeQuery("
				select _a.id 
				from %PRE%characteristics _a
				left join %PRE%characteristics_chargroups _b
					on _a.id = _b.characteristic_id
				left join %PRE%characteristics_matrices _c
					on _a.id = _c.characteristic_id
				where _a.project_id = ".$this->getCurrentProjectId()."  
				and _b.id is null 
				and _c.matrix_id = ".$this->getCurrentMatrixId()
			);

			foreach((array)$dummy as $val)
				$m[] = array('ref_id'=>$val['id'],'ref_type'=>'char','show_order'=>$i++);
			
		}

		$d=array();
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
				$d[$i]['icon_exists'] = file_exists($this->getProjectUrl('projectMedia').$d[$i]['icon']);
			$d[$i]['type'] = $val['ref_type'];
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

}	