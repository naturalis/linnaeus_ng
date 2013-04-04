<?php

include_once ('Controller.php');
class MatrixKeyController extends Controller
{
    private $_useCharacterGroups = false;
    public $usedModels = array(
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'matrix_variation'
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Matrix key';
    public $cacheFiles = array(
        'matrices' => 'matrix-matrices', 
        'taxa' => 'matrix-taxa-*'
    );
    public $cssToLoad = array(
        'matrix.css', 
        'prettyPhoto/prettyPhoto.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'matrix.js', 
            'prettyPhoto/jquery.prettyPhoto.js'
        )
    );


    public function __construct ()
    {
        parent::__construct();
        
        $this->initialize();
    }


    public function __destruct ()
    {
        parent::__destruct();
    }


    public function indexAction ()
    {
        $this->checkAuthorisation();
        
        $this->cleanUpEmptyVariables();
        
        if ($this->rHasId()) {
            
            $this->setCurrentMatrixId($this->requestData['id']);
            
            $this->redirect('edit.php');
        }
        else if ($this->getCurrentMatrixId() == null) {
            
            $matrices = $this->getMatrices();
            
            if (count((array) $matrices) > 0) {
                
                $this->setCurrentMatrixId($matrices[0]['id']);
                
                $this->redirect('edit.php');
            }
            else {
                
                $this->redirect('matrices.php');
            }
        }
        else {
            
            $this->redirect('edit.php');
        }
        
        /*
        $this->setPageName( $this->translate('Index'));

        $this->printPage();
	    */
    }


    public function matricesAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Matrices'));
        
        $matrices = $this->getMatrices();
        
        if (count((array) $matrices) == 0)
            $this->redirect('matrix.php');
			
        if ($this->rHasVal('imgdim'))
			$this->reacquireStateImageDimensions($this->requestData['imgdim']);

        
        if ($this->rHasVal('action', 'delete') && !$this->isFormResubmit()) {
            
            $this->clearCache($this->cacheFiles['matrices']);
            
            $this->deleteMatrix($this->requestData['id']);
            
            if ($this->getCurrentMatrixId() == $this->requestData['id'])
                $this->setCurrentMatrixId(null);
            
            $matrices = $this->getMatrices();
        }
        else if ($this->rHasVal('action', 'activate') && !$this->isFormResubmit()) {
            
            $this->setCurrentMatrixId($this->requestData['id']);
            
            $this->redirect('edit.php');
        }
        
        $this->smarty->assign('matrices', $matrices);
        
        $this->printPage();
    }


    public function matrixAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->rHasId()) {
            
            $matrix = $this->getMatrix();
            
            if (isset($matrix['names'][$this->getDefaultProjectLanguage()]['name'])) {
                
                $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['names'][$this->getDefaultProjectLanguage()]['name']));
            }
            else {
                
                $this->setPageName($this->translate('New matrix'));
            }
        }
        else {
            
            $this->clearCache($this->cacheFiles['matrices']);
            
            $id = $this->createNewMatrix();
            
            if ($id) {
                
                $this->redirect('matrix.php?id=' . $id);
            }
            else {
                
                $this->addError($this->translate('Could not create new matrix.'));
            }
        }
        

        if (isset($matrix))
            $this->smarty->assign('matrix', $matrix);
        
        $this->printPage();
    }


    public function editAction ()
    {
        $this->checkAuthorisation();
        
        $this->cleanUpEmptyVariables();
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        if ($this->rHasId() && $this->rHasVal('action', 'def') && !$this->isFormResubmit()) {
            
            $this->setDefaultMatrix($this->requestData['id']);
        }
        
        $matrix = $this->getMatrix($this->getCurrentMatrixId());
        
        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['matrix']));
        
        if ($this->rHasVal('char'))
            $this->smarty->assign('activeCharacteristic', $this->requestData['char']);
        
        $this->smarty->assign('characteristics', $this->getCharacteristics());
        
        $this->smarty->assign('taxa', $this->getTaxa());
        
        if ($this->useVariations)
            $this->smarty->assign('variations', $this->getVariationsInMatrix());
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->smarty->assign('matrices', $this->getMatrices(true));
        
        $this->printPage();
    }


    public function charSortAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        if ($this->rHasId() && $this->rHasVal('r') && !$this->isFormResubmit()) {
            
            $c = $this->getCharacteristics();
            
            foreach ((array) $c as $key => $val) {
                
                if ($this->requestData['id'] == $val['id']) {
                    
                    if ($this->rHasVal('r', 'u')) {
                        
                        if (isset($c[$key - 1]))
                            $this->updateCharShowOrder($c[$key - 1]['id'], $c[$key - 1]['show_order'] + 1);
                        
                        $this->updateCharShowOrder($this->requestData['id'], $val['show_order'] - 1);
                        
                        break;
                    }
                    else if ($this->rHasVal('r', 'd')) {
                        //q(($c[$key+1]));
                        if (isset($c[$key + 1]))
                            $this->updateCharShowOrder($c[$key + 1]['id'], $c[$key + 1]['show_order'] - 1);
                        
                        $this->updateCharShowOrder($this->requestData['id'], $val['show_order'] + 1);
                        
                        break;
                    }
                }
            }
            
            $this->renumberCharShowOrder();
        }
        
        $matrix = $this->getMatrix($this->getCurrentMatrixId());
        
        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['matrix']));
        
        $this->smarty->assign('characteristics', $this->getCharacteristics());
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->printPage();
    }


    public function charGroupsAction ()
    {
        $this->checkAuthorisation();
        
        if (!$this->_useCharacterGroups)
            redirect('edit.php');
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        $matrix = $this->getMatrix($this->getCurrentMatrixId());
        
        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['matrix']));

        if ($this->rHasVal('delete') && !$this->isFormResubmit()) {
			
			$this->deleteCharacteristicFromGroup(array('groupId'=>$this->requestData['delete']));
			$this->deleteCharacterGroup(array('groupId'=>$this->requestData['delete']));

			$d = $this->getSetting('matrix-key-order');
			
			foreach((array)$d as $key => $val) {

				if ($d=='g-'.$this->requestData['delete'])
					unset($d[$key]);
			
			}
			
//			$this->saveSetting(array('name' => 'matrix-key-order','value' => serialize($d)));
			
		}

        if ($this->rHasVal('chars') && !$this->isFormResubmit()) {
			
			$this->deleteCharacteristicFromGroup();
			
			foreach((array)$this->requestData['chars'] as $key => $val) {

				$val = explode(':',$val);
				if ($val[1]==0)
					continue;
				$this->saveCharacteristicToGroup(array('charId'=>$val[0],'groupId'=>$val[1],'showOrder'=>$key));

			}
			
		}
		        
		if ($this->rHasVal('new') && !$this->isFormResubmit()) {
			
			$c = $this->getCharacterGroups(array('label'=>$this->requestData['new']));

			if (!empty($c)) {
				
				$this->addError(sprintf($this->translate('A group named "%s" already exists.'),$this->requestData['new']));

			} else {
				
				$this->saveCharacterGroup(array('label' => $this->requestData['new']));
				
			}
			
		}

		if ($this->rHasVal('order') && !$this->isFormResubmit()) {
/*			
			$this->saveSetting(
				array(
					'name' => 'matrix-key-order',
					'value' => serialize($this->requestData['order'])
				)
			);
*/	
		}


//		$order = $this->getSetting('matrix-key-order');
		
		if ($order) {
			
			$order = unserialize($order);
			
			foreach((array)$order as $val) {
				
				echo $val;
				
			}
			
		}

		$this->smarty->assign('groups', $this->getCharacterGroups());
        $this->smarty->assign('characteristics', $this->getCharactersNotInGroups());
       
        $this->smarty->assign('matrix', $matrix);
        
        $this->printPage();
    }


    public function charAction ()
    {
        $this->checkAuthorisation();
        
        // need an active matrix to assign the charcter to
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        if (!$this->rHasId()) {
            
            $id = $this->createCharacteristic();
            
            if ($id) {
                
                $this->addCharacteristicToMatrix($id);
                
                $this->redirect('char.php?id=' . $id);
            }
            else {
                
                $this->addError($this->translate('Could not create character.'));
            }
        }
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Matrix'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
        ));
        
        // get the current active matrix' id
        $matrix = $this->getMatrix($this->getCurrentMatrixId());
        
        if ($this->rHasId() && $this->rHasVal('action', 'delete')) {
            
            // delete the char from this matrix (and automatically delete the char itself if it isn't used in any other matrix)
            $this->deleteCharacteristic();
            
            $this->renumberCharShowOrder();
            
            $this->redirect('edit.php');
        }
        else if ($this->rHasVal('existingChar') && $this->rHasVal('action', 'use')) {
            
            $this->addCharacteristicToMatrix($this->requestData['existingChar']);
            
            $this->renumberCharShowOrder();
            
            $this->redirect('edit.php');
        }
        else if ($this->rHasId()) {
            
            $c = $this->getCharacteristic($this->requestData['id']);
            
            $this->smarty->assign('characteristic', $c);
            
            if (isset($c['label'])) {
                
                $this->setPageName(sprintf($this->translate('Editing character "%s"'), $c['label']));
            }
            else {
                
                $this->setPageName($this->translate('New character'));
            }
        }
        
        if ($this->rHasVal('type') && !$this->isFormResubmit()) {
            
            $charId = $this->updateCharacteristic();
            
            $this->renumberCharShowOrder();
            
            //$this->addCharacteristicToMatrix($charId);
            



            $this->redirect('edit.php');
        }
        
        $this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->smarty->assign('charLib', $this->getAllCharacteristics($this->getCurrentMatrixId()));
        
        $this->smarty->assign('charTypes', $this->controllerSettings['characteristicTypes']);
        
        $this->printPage();
    }


    public function taxaAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        $this->setPageName($this->translate('Adding taxa'));
        
        if ($this->rHasVal('taxon') || $this->rHasVal('variation')) {
            
            $this->clearCache($this->cacheFiles['taxa']);
            
            if ($this->rHasVal('taxon')) {
                
                foreach ((array) $this->requestData['taxon'] as $val) {
                    
                    $this->models->MatrixTaxon->save(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $this->getCurrentMatrixId(), 
                        'taxon_id' => $val
                    ));
                }
            }
            
            if ($this->rHasVal('variation')) {
                

                foreach ((array) $this->requestData['variation'] as $val) {
                    
                    $this->models->MatrixVariation->save(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $this->getCurrentMatrixId(), 
                        'variation_id' => $val
                    ));
                }
            }
            

            if ($this->requestData['action'] != 'repeat') {
                
                $this->redirect('edit.php');
            }
            
            $this->addMessage(sprintf($this->translate('Taxon added.')));
        }
        
        $this->newGetTaxonTree();
        
        if (isset($this->treeList))
            $this->smarty->assign('taxa', $this->treeList);
        
        if ($this->useVariations)
            $this->smarty->assign('variations', $this->getVariations());
        
        $this->printPage();
    }


    public function stateAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        if ($this->rHasId() && $this->rHasVal('action', 'delete')) {
            
            $this->deleteCharacteristicState();
            
            $this->redirect('edit.php');
        }
        else if ($this->rHasId() && $this->rHasVal('action', 'deleteimage')) {
            
            $this->deleteCharacteristicStateImage();
        }
        else if ($this->rHasId()) {
            
            $state = $this->getCharacteristicState($this->requestData['id']);
            
            if (isset($state))
                $this->requestData['char'] = $state['characteristic_id'];
        }
        else {
            
			// must have a characteristic to define a state for
            if (!$this->rHasVal('char')) {
                $this->redirect('edit.php');
            }
            else {
                
                $id = $this->createState();
                
                if ($id) {
                    
                    $this->redirect('state.php?char=' . $this->requestData['char'] . '&id=' . $id);
                }
                else {
                    
                    $this->addError($this->translate('Cannot create new state.'));
                }
            }
        }
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Matrix'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
        ));
        
        $characteristic = $this->getCharacteristic($this->requestData['char']);
        
        $state = $this->getCharacteristicState($this->requestData['id']);
        
         // existing state
        if ($state['label']) {

            $this->setPageName(sprintf($this->translate('Editing state for "%s"'), $characteristic['label']));
        }
        // new state
        else {

            $this->setPageName(sprintf($this->translate('New state for "%s"'), $characteristic['label']));
        }
        
        if (($this->rHasVal('action', 'save') || $this->rHasVal('action', 'repeat')) && !$this->isFormResubmit()) {
			
            $filesToSave = $this->getUploadedMediaFiles();
            
            if (!$this->verifyData($this->requestData, $filesToSave)) {
                
                $state = $this->requestData;
            }
            else {
                
                $this->models->CharacteristicState->save(
                array(
                    'id' => ($this->rHasId() ? $this->requestData['id'] : null), 
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $this->requestData['char'], 
                    'file_name' => isset($filesToSave[0]['name']) ? $filesToSave[0]['name'] : null, 
                    'lower' => isset($this->requestData['lower']) ? $this->requestData['lower'] : null, 
                    'upper' => isset($this->requestData['upper']) ? $this->requestData['upper'] : null, 
                    'mean' => isset($this->requestData['mean']) ? $this->requestData['mean'] : null, 
                    'sd' => isset($this->requestData['sd']) ? $this->requestData['sd'] : null
                ));

				$this->saveStateImageDimensions($state);
                
                unset($state);
                
                if ($this->requestData['action'] != 'repeat') {
                    
                    $this->redirect('edit.php?char=' . $this->requestData['char']);
                }
                
                $this->addMessage(sprintf($this->translate('State "%s" saved.'), $this->requestData['label']));
                
				$state = $this->getCharacteristicState($this->createState());
				//$state = $this->getCharacteristicState($this->requestData['id']);
				
            }
        }
        
        $this->smarty->assign('matrix', $this->getMatrix($this->getCurrentMatrixId()));
        
        $this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);
        
        if (isset($state))
            $this->smarty->assign('state', $state);
        
        $this->smarty->assign('characteristic', $characteristic);
        
        $this->printPage();
    }


    public function stateSortAction ()
    {
        $this->checkAuthorisation();
        
        $mId = $this->getCurrentMatrixId();
        
        if ($mId == null)
            $this->redirect('matrices.php');
        
        if (!$this->rHasVal('sId'))
            $this->redirect('edit.php');
        
        if ($this->rHasId() && $this->rHasVal('r') && !$this->isFormResubmit()) {
            
            $c = $this->getCharacteristicStates($this->requestData['sId']);
            
            if ($this->rHasVal('r', 'alph') || $this->rHasVal('r', 'num')) {

                foreach((array)$c as $val) {
                    $assoc[$val['label']] = $val['id'];
                    $sort[] = $val['label'];
                }

	            if ($this->rHasVal('r', 'alph'))
	            	sort($sort);

	            if ($this->rHasVal('r', 'num'))
	            	natcasesort($sort);
            
	            $i=0;
	            foreach((array)$sort as $val)
	                $this->updateStateShowOrder($assoc[$val],$i++);
	            

            } else {

	            foreach ((array) $c as $key => $val) {
	                
	                if ($this->requestData['id'] == $val['id']) {
	                    
	                    if ($this->rHasVal('r', 'u')) {
	                        
	                        if (isset($c[$key - 1]))
	                            $this->updateStateShowOrder($c[$key - 1]['id'], $c[$key - 1]['show_order'] + 1);
	                        
	                        $this->updateStateShowOrder($this->requestData['id'], $val['show_order'] - 1);
	                        
	                        break;
	                    }
	                    else if ($this->rHasVal('r', 'd')) {
	                        
	                        if (isset($c[$key + 1]))
	                            $this->updateStateShowOrder($c[$key + 1]['id'], $c[$key + 1]['show_order'] - 1);
	                        
	                        $this->updateStateShowOrder($this->requestData['id'], $val['show_order'] + 1);
	                        
	                        break;
	                    }
	                }
	            }
	            
	            $this->renumberStateShowOrder($this->requestData['sId']);

            }
            
        }
        
        $matrix = $this->getMatrix($mId);
        
        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['matrix']));
        
        $this->smarty->assign('characteristic', $this->getCharacteristic($this->requestData['sId']));
        
        $this->smarty->assign('states', $this->getCharacteristicStates($this->requestData['sId']));
        
        $this->smarty->assign('matrix', $matrix);
        
        $this->printPage();
    }


    public function linksAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');
        
        $this->setPageName($this->translate('Taxon-state links'));
        
        if ($this->rHasVal('taxon')) {
            
            $links = $this->getLinks(array(
                'taxon_id' => $this->requestData['taxon']
            ));
            
            $this->customSortArray($links, array(
                'key' => 'characteristic', 
                'case' => 'i'
            ));
        }
        
        $this->getTaxonTree();
        
        if (isset($this->treeList))
            $this->smarty->assign('taxa', $this->getTaxa());
        
        if (isset($links))
            $this->smarty->assign('links', $links);
        
        $this->smarty->assign('matrix', $this->getMatrix($this->getCurrentMatrixId()));
        
        if ($this->rHasVal('taxon'))
            $this->smarty->assign('taxon', $this->requestData['taxon']);
        
        $this->printPage();
    }


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;
        
        if ($this->requestData['action'] == 'save_matrix_name') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->ajaxSaveMatrixName();
        }
        else if ($this->requestData['action'] == 'get_matrix_name') {
            
            $this->ajaxGetMatrixName();
        }
        else if ($this->requestData['action'] == 'save_characteristic_label') {
            
            $this->ajaxActionSaveCharacteristicLabel();
        }
        else if ($this->requestData['action'] == 'get_characteristic_label') {
            
            $this->smarty->assign('returnText', $this->getCharacteristicLabel());
        }
        else if ($this->requestData['action'] == 'get_state_label') {
            
            $this->ajaxActionGetCharacteristicStateLabel();
        }
        else if ($this->requestData['action'] == 'get_state_text') {
            
            $this->ajaxActionGetCharacteristicStateText();
        }
        else if ($this->requestData['action'] == 'save_state_label') {
            
            $this->ajaxActionSaveCharacteristicStateLabel();
        }
        else if ($this->requestData['action'] == 'save_state_text') {
            
            $this->ajaxActionSaveCharacteristicStateText();
        }
        else if ($this->requestData['action'] == 'remove_taxon') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->removeTaxon();
            
            $d = $this->getMatrices();
            
            $matrices = array();
            
            foreach ((array) $d as $val)
                if ($val['id'] != $this->getCurrentMatrixId())
                    array_push($matrices, $val);
            
            $this->smarty->assign('returnText', json_encode(array(
                'taxa' => $this->getTaxa(), 
                'matrices' => $matrices, 
                'variations' => $this->getVariationsInMatrix()
            )));
        }
        else if ($this->requestData['action'] == 'get_states') {
            
            $this->getCharacteristicStates();
        }
        else if ($this->requestData['action'] == 'add_link') {
            
            $this->addLink();
        }
        else if ($this->requestData['action'] == 'delete_link') {
            
            $this->deleteLinks(array(
                'id' => $this->requestData['id']
            ));
        }
        else if ($this->requestData['action'] == 'get_links') {
            
            $this->smarty->assign('returnText', json_encode($this->getLinks(array(
                'characteristic_id' => $this->requestData['characteristic'], 
                'taxon_id' => $this->requestData['taxon']
            ))));
        }
        
        $this->printPage();
    }



    public function previewAction ()
    {
        $this->redirect('../../../app/views/matrixkey/use_matrix.php?p=' . $this->getCurrentProjectId() . '&id=' . $this->getCurrentMatrixId());
    }
    
    /* matrix functions */
    private function initialize ()
    {
        $this->setDefaultMatrix();
        
		$this->_useCharacterGroups = $this->getSetting('matrix_use_character_groups')=='1'; 
		$this->useVariations = $this->getSetting('taxa_use_variations')=='1';
        
        $this->smarty->assign('useCharacterGroups', $this->_useCharacterGroups);
        $this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
        $this->smarty->assign('activeLanguage', $this->getDefaultProjectLanguage());
    }



    private function createNewMatrix ()
    {
        $this->models->Matrix->save(array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        return $this->models->Matrix->getNewId();
    }



    private function setMatrixGotNames ($id, $state = null)
    {
        if ($state == null) {
            
            $mn = $this->models->MatrixName->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $id
                ), 
                'columns' => 'count(*) as total'
            ));
            
            $state = ($mn[0]['total'] == 0 ? false : true);
        }
        
        $this->models->Matrix->update(array(
            'got_names' => ($state == false ? '0' : '1')
        ), array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function setCurrentMatrixId ($id, $name = null)
    {
        if ($id == null) {
            
            unset($_SESSION['admin']['matrixkey']['id']);
            unset($_SESSION['admin']['matrixkey']['name']);
        }
        else {
            
            $_SESSION['admin']['matrixkey']['id'] = $id;
            if ($name)
                $_SESSION['admin']['matrixkey']['name'] = $name;
        }
    }



    private function getCurrentMatrixId ()
    {
        return isset($_SESSION['admin']['matrixkey']['id']) ? $_SESSION['admin']['matrixkey']['id'] : null;
    }



    private function getMatrix ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        $m = $this->models->Matrix->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $id
            )
        ));
        

        $mn = $this->models->MatrixName->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $id
            ), 
            'fieldAsIndex' => 'language_id'
        ));
        
        $m[0]['names'] = $mn;
        
        $m[0]['matrix'] = $m[0]['names'][$this->getDefaultProjectLanguage()]['name'];
        
        return $m[0];
    }



    private function deleteMatrix ($id)
    {
        if (!isset($id))
            return;
        
        $c = $this->getCharacteristics($id);
        
        foreach ((array) $c as $key => $val) {
            
            // deletes characteristics, states and links
            $this->deleteCharacteristic($val['id']);
        }
        
        $this->models->MatrixName->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'matrix_id' => $this->requestData['id']
        ));
        
        $this->models->Matrix->delete(array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function getMatrices ($skipCurrent = false)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId(), 
            'got_names' => 1
        );
        
        if ($skipCurrent)
            $d['id !='] = $this->getCurrentMatrixId();
        
        $m = $this->models->Matrix->_get(array(
            'id' => $d
        ));
        
        foreach ((array) $m as $key => $val) {
            
            $mn = $this->models->MatrixName->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $val['id']
                ), 
                'fieldAsIndex' => 'language_id'
            ));
            
            $m[$key]['names'] = $mn;
            $m[$key]['default_name'] = $mn[$this->getDefaultProjectLanguage()]['name'];
        }
        
        $this->customSortArray($m, array(
            'key' => 'default_name', 
            'case' => 'i'
        ));
        
        return $m;
    }



    private function ajaxSaveMatrixName ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('content')) {
                
                $this->models->MatrixName->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $this->requestData['id'], 
                    'language_id' => $this->requestData['language']
                ));
                
                $this->setMatrixGotNames($this->requestData['id']);
            }
            else {
                
                $mn = $this->models->MatrixName->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'matrix_id' => $this->requestData['id'], 
                        'language_id' => $this->requestData['language']
                    )
                ));
                

                $this->models->MatrixName->save(
                array(
                    'id' => isset($mn[0]['id']) ? $mn[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'matrix_id' => $this->requestData['id'], 
                    'name' => trim($this->requestData['content'])
                ));
                
                $this->setMatrixGotNames($this->requestData['id'], true);
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function ajaxGetMatrixName ()
    {
        if (!$this->rHasVal('id') || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $mn = $this->models->MatrixName->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'matrix_id' => $this->requestData['id'], 
                    'language_id' => $this->requestData['language']
                ), 
                'columns' => 'name'
            ));
            
            $this->smarty->assign('returnText', $mn[0]['name']);
        }
    }
    
    /* character functions */
    private function createCharacteristic ()
    {
        $this->models->Characteristic->save(array(
            'project_id' => $this->getCurrentProjectId(), 
            'type' => $this->controllerSettings['characteristicTypes'][0]['name']
        ));
        
        return $this->models->Characteristic->getNewId();
    }



    private function addCharacteristicToMatrix ($charId, $matrixId = null)
    {
        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        
        if (!isset($charId) || !isset($matrixId))
            return;
        
        $mc = $this->models->CharacteristicMatrix->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $matrixId
            ), 
            'columns' => 'max(show_order) as max'
        ));
        
        $next = isset($mc[0]['max']) ? $mc[0]['max'] + 1 : 0;
        
        @$this->models->CharacteristicMatrix->save(
        array(
            'project_id' => $this->getCurrentProjectId(), 
            'matrix_id' => $matrixId, 
            'characteristic_id' => $charId, 
            'show_order' => $next
        ));
    }



    private function updateCharacteristic ()
    {
        $this->models->Characteristic->update(array(
            'type' => $this->requestData['type']
        ), array(
            'id' => $this->requestData['id'], 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function setCharacteristicGotLabels ($id, $state = null)
    {
        if ($state == null) {
            
            $cl = $this->models->CharacteristicLabel->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'characteristic_id' => $id
                ), 
                'columns' => 'count(*) as total'
            ));
            
            $state = ($cl[0]['total'] == 0 ? false : true);
        }
        
        $this->models->Characteristic->update(array(
            'got_labels' => ($state == false ? '0' : '1')
        ), array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function ajaxActionSaveCharacteristicLabel ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('label') && !$this->rHasId()) {
                
                $this->models->CharacteristicLabel->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'characteristic_id' => $this->requestData['id']
                ));
                
                $this->setCharacteristicGotLabels($this->requestData['id']);
            }
            else {
                
                if ($this->rHasId()) {
                    
                    $cl = $this->models->CharacteristicLabel->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'language_id' => $this->requestData['language'], 
                            'characteristic_id' => $this->requestData['id']
                        )
                    ));
                    
                    $charId = isset($cl[0]['id']) ? $cl[0]['id'] : null;
                }
                else {
                    
                    $charId = null;
                }
                
                $this->models->CharacteristicLabel->save(
                array(
                    'id' => $charId, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'characteristic_id' => $this->requestData['id'], 
                    'label' => trim($this->requestData['content'])
                ));
                
                $this->setCharacteristicGotLabels($this->requestData['id'], true);
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function getCharacteristicLabel ($id = null, $language = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        $language = isset($language) ? $language : $this->requestData['language'];
        
        if (!isset($id) || !isset($language))
            return;
        
        $cl = $this->models->CharacteristicLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $language, 
                'characteristic_id' => $id
            )
        ));
        
        return $cl[0]['label'];
    }



    private function getCharacteristicLabels ($id)
    {
        $cl = $this->models->CharacteristicLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'characteristic_id' => $id
            ), 
            'fieldAsIndex' => 'language_id', 
            'columns' => 'id,characteristic_id,language_id,label'
        ));
        
		if (strpos($cl[$this->getDefaultProjectLanguage()]['label'],'|')!==false)
			$d = explode('|',$cl[$this->getDefaultProjectLanguage()]['label']);
		else
			$d = null;
		
        return array(
            'labels' => $cl, 
            'label' => $cl[$this->getDefaultProjectLanguage()]['label'],
			'short_label' => is_null($d) ? $cl[$this->getDefaultProjectLanguage()]['label'] : $d[0]
        );
    }



    private function getCharacteristic ($id)
    {
        $c = $this->models->Characteristic->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'id' => $id
            ), 
            'columns' => 'id,type,got_labels'
        ));
        
        if (!$c)
            return;
        else
            $char = $c[0];
        
        $char['type'] = $this->getCharacteristicType($char['type']);
        
        $d = $this->getCharacteristicLabels($id);
        
        $char['labels'] = $d['labels'];
        $char['label'] = $d['label'];
        $char['short_label'] = $d['short_label'];
		
        
        return $char;
    }



    private function deleteCharacteristic ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        $this->deleteLinks(array(
            'characteristic_id' => $id, 
            'matrix_id' => $this->getCurrentMatrixId()
        ));
        
        // delete from matrix-char table for current matrix
        $this->models->CharacteristicMatrix->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'matrix_id' => $this->getCurrentMatrixId(), 
            'characteristic_id' => $id
        ));
        
        // check if char is used in any other matrix
        $mc = $this->models->CharacteristicMatrix->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id !=' => $this->getCurrentMatrixId(), 
                'characteristic_id' => $id
            ), 
            'columns' => 'count(*) as total'
        ));
        
        if ($mc[0]['total'] == 0) {
            // if not, adieu
            



            $this->deleteCharacteristicStates($id);
            
            $this->models->CharacteristicLabel->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'characteristic_id' => $id
            ));
            
            $this->models->Characteristic->delete(array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId()
            ));
        }
    }



    private function getCharacteristics ($matrixId = null)
    {
        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        
        $mc = $this->models->CharacteristicMatrix->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $matrixId
            ), 
            'columns' => 'characteristic_id,show_order,id', 
            'order' => 'show_order'
        ));
        
        foreach ((array) $mc as $key => $val) {
            
            $labels = $this->getCharacteristicLabels($val['characteristic_id']);
            
            if (isset($labels['label'])) {
                
                $d[] = array_merge($this->getCharacteristic($val['characteristic_id']), $labels, array(
                    'show_order' => $val['show_order']
                ));
            }
        }
        
        return isset($d) ? $d : null;
    }



    private function getAllCharacteristics ($matrixToExclude = null)
    {
        if (isset($matrixToExclude)) {
            
            $ce = $this->getCharacteristics($matrixToExclude);
            
            if (isset($ce)) {
                
                $b = null;
                
                foreach ((array) $ce as $key => $val) {
                    
                    $b .= $val['id'] . ', ';
                }
                
                $b = '(' . rtrim($b, ', ') . ')';
                
                $id['id not in'] = $b;
            }
        }
        
        $id['project_id'] = $this->getCurrentProjectId();
        
        $id['got_labels'] = '1';
        
        $c = $this->models->Characteristic->_get(array(
            'id' => $id
        ));
        
        foreach ((array) $c as $key => $val) {
            
            $d = $this->getCharacteristicLabels($val['id']);
            $c[$key]['label'] = $d['label'];
        }
        
        return $c;
    }



    private function getCharacteristicType ($type)
    {
        foreach ((array) $this->controllerSettings['characteristicTypes'] as $key => $val) {
            
            if ($val['name'] == $type)
                return $val;
        }
    }
    
    /* state functions*/
    private function createState ()
    {
        if (!$this->rHasVal('char'))
            return;
        
        $this->models->CharacteristicState->save(array(
            'project_id' => $this->getCurrentProjectId(), 
            'characteristic_id' => $this->requestData['char']
        ));
        
        return $this->models->CharacteristicState->getNewId();
    }



    private function getCharacteristicStateLabels ($id)
    {
        $cls = $this->models->CharacteristicLabelState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'state_id' => $id
            ), 
            'fieldAsIndex' => 'language_id'
        ));
        
        return array(
            'label' => $cls[$this->getDefaultProjectLanguage()]['label'], 
            'labels' => $cls
        );
    }



    private function getCharacteristicState ($id)
    {
        $cs = $this->models->CharacteristicState->_get(array(
            'id' => array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        $state = $cs[0];
        
        if (isset($state['id'])) {
            
            $d = $this->getCharacteristicStateLabels($state['id']);
            
            $state['labels'] = $d['labels'];
            $state['label'] = $d['label'];
        }
        
        return $state;
    }



    private function verifyData ($data, $file)
    {
        $result = true;
        
        if (!isset($data['label']) || empty($data['label'])) {
            // each state has a name, regardless of type
            



            $this->addError($this->translate('A name is required.'));
            
            $result = false;
        }
        
        if ($data['type'] == 'text') {
            
            if (!isset($data['text']) || empty($data['text'])) {
                
                $this->addError($this->translate('Text is required.'));
                
                $result = false;
            }
        }
        else 

        if ($data['type'] == 'range') {
            
            if (!isset($data['lower']) || empty($data['lower']) && $data['lower'] !== '0') {
                
                $this->addError($this->translate('The lower boundary is required.'));
                
                $result = false;
            }
            elseif ($data['lower'] != strval(floatval($data['lower']))) {
                
                $this->addError($this->translate('Invalid value for the lower boundary (must be integer or real).'));
                
                $result = false;
            }
            
            if (!isset($data['upper']) || empty($data['upper']) && $data['upper'] !== '0') {
                
                $this->addError($this->translate('The upper boundary is required.'));
                
                $result = false;
            }
            elseif ($data['upper'] != strval(floatval($data['upper']))) {
                
                $this->addError($this->translate('Invalid value for the upper boundary (must be integer or real).'));
                
                $result = false;
            }
            
            if ($result && (floatval($data['upper']) < floatval($data['lower']))) {
                
                $this->addError($this->translate('The upper boundary value must be larger than the lower boundary value.'));
                
                $result = false;
            }
            elseif ($result && (floatval($data['upper']) == floatval($data['lower']))) {
                
                $this->addError($this->translate('The upper and lower boundary values cannot be the same.'));
                
                $result = false;
            }
        }
        else if ($data['type'] == 'distribution') {
            
            if (!isset($data['mean']) || empty($data['mean']) && $data['mean'] !== '0') {
                
                $this->addError($this->translate('The mean is required.'));
                
                $result = false;
            }
            elseif ($data['mean'] != strval(floatval($data['mean']))) {
                
                $this->addError($this->translate('Invalid value for the mean (must be integer or real).'));
                
                $result = false;
            }
            
            if (!isset($data['sd']) || empty($data['sd']) && $data['sd'] !== '0') {
                
                $this->addError($this->translate('The value for one standard deviation is required.'));
                
                $result = false;
            }
            elseif ($data['sd'] != strval(floatval($data['sd'])) && $data['mean'] !== '0') {
                
                $this->addError($this->translate('Invalid value for one standard deviation (must be integer or real).'));
                
                $result = false;
            }
        }
        else if ($data['type'] == 'media') {
            
            if (!$file && !isset($data['existing_file'])) {
                
                $this->addError($this->translate('A media file is required.'));
                
                $result = false;
            }
        }
        
        return $result;
    }



    private function getCharacteristicStates ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        $cs = $this->models->CharacteristicState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'characteristic_id' => $id
            ), 
            'columns' => 'id,characteristic_id,show_order', 
            'order' => 'show_order'
        ));
        
        foreach ((array) $cs as $key => $val) {
            
            $cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id'], $this->getDefaultProjectLanguage());
        }
        
        $this->smarty->assign('returnText', json_encode($cs));
        
        return $cs;
    }



    private function getCharacteristicStateLabelOrText ($id = null, $language = null, $type = 'label')
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        $language = isset($language) ? $language : $this->requestData['language'];
        
        if (!isset($id) || !isset($language))
            return;
        
        $cls = $this->models->CharacteristicLabelState->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'state_id' => $id, 
                'language_id' => $language
            )
        ));
        
        return $type == 'text' ? $cls[0]['text'] : $cls[0]['label'];
    }



    private function ajaxActionGetCharacteristicStateLabel ()
    {
        $this->smarty->assign('returnText', $this->getCharacteristicStateLabelOrText($this->requestData['id'], $this->requestData['language']));
    }



    private function ajaxActionGetCharacteristicStateText ()
    {
        $this->smarty->assign('returnText', $this->getCharacteristicStateLabelOrText($this->requestData['id'], $this->requestData['language'], 'text'));
    }



    private function setCharacteristicStateGotLabels ($id, $state = null)
    {
        if ($state == null) {
            
            $cl = $this->models->CharacteristicLabelState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'state_id' => $id
                ), 
                'columns' => 'count(*) as total'
            ));
            
            $state = ($cl[0]['total'] == 0 ? false : true);
        }
        
        $this->models->CharacteristicState->update(array(
            'got_labels' => ($state == false ? '0' : '1')
        ), array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function saveCharacteristicStateLabelOrText ($id, $language, $content, $type = 'label')
    {
        if (!$content) {
            
            $this->models->CharacteristicLabelState->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'state_id' => $id, 
                'language_id' => $language
            ));
            
            $this->setCharacteristicStateGotLabels($id);
        }
        else {
            
            $cls = $this->models->CharacteristicLabelState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'state_id' => $id, 
                    'language_id' => $language
                )
            ));
            
            $clsId = isset($cls[0]['id']) ? $cls[0]['id'] : null;
            
            $s = array(
                'id' => $clsId, 
                'project_id' => $this->getCurrentProjectId(), 
                'state_id' => $id, 
                'language_id' => $language
            );
            
            if ($type == 'label') {
                
                $s['label'] = trim($content);
            }
            else {
                
                $s['text'] = trim($content);
            }
            
            $this->models->CharacteristicLabelState->save($s);
            
            $this->setCharacteristicStateGotLabels($id, true);
        }
    }



    private function ajaxActionSaveCharacteristicStateLabel ()
    {
        if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
            
            return;
        }
        else {
            
            $this->saveCharacteristicStateLabelOrText($this->requestData['id'], $this->requestData['language'], $this->requestData['content']);
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function ajaxActionSaveCharacteristicStateText ()
    {
        if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
            
            return;
        }
        else {
            
            $this->saveCharacteristicStateLabelOrText($this->requestData['id'], $this->requestData['language'], $this->requestData['content'], 'text');
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function deleteCharacteristicStateImage ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        $cs = $this->getCharacteristicState($id);
        
        if ($cs['file_name'])
            @unlink($_SESSION['admin']['project']['paths']['project_media'] . $cs['file_name']);
        
        $this->models->CharacteristicState->update(array(
            'file_name' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
    }



    private function deleteCharacteristicState ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        $this->deleteCharacteristicStateImage($id);
        
        $this->models->CharacteristicLabelState->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'state_id' => $id
        ));
        
        $this->models->CharacteristicState->delete(array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function deleteCharacteristicStates ($charId)
    {
        $cs = $this->getCharacteristicStates($charId);
        
        $this->deleteLinks(array(
            'characteristic_id' => $charId
        ));
        
        foreach ((array) $cs as $key => $val) {
            
            if ($val['file_name']) {
                
                @unlink($_SESSION['admin']['project']['paths']['project_media'] . $val['file_name']);
            }
        }
        
        $this->models->CharacteristicState->delete(array(
            'characteristic_id' => $charId, 
            'project_id' => $this->getCurrentProjectId()
        ));
    }



    private function getTaxa ()
    {
        $mt = $this->models->MatrixTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId()
            ), 
            'columns' => 'taxon_id'
        ));
        
        $this->getTaxonTree();
        
        foreach ((array) $mt as $key => $val) {
            if (isset($this->treeList[$val['taxon_id']])) {
                $t[] = array(
                    'id' => $this->treeList[$val['taxon_id']]['id'], 
                    'taxon' => $this->treeList[$val['taxon_id']]['taxon']
                );
            }
        }
        
        $this->customSortArray($t, array(
            'key' => 'taxon'
        ));
        return isset($t) ? $t : null;
    }



    private function removeTaxon ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (!isset($id))
            return;
        
        if (strpos($id, 'var-') === 0) {
            
            $id = str_replace('var-', '', $id);
            
            $this->deleteLinks(array(
                'variation_id' => $id
            ));
            
            $this->models->MatrixVariation->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'variation_id' => $id
            ));
        }
        else {
            
            $this->deleteLinks(array(
                'taxon_id' => $id
            ));
            $this->models->MatrixTaxon->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'matrix_id' => $this->getCurrentMatrixId(), 
                'taxon_id' => $id
            ));
        }
    }



    private function addLink ($charId = null, $taxonId = null, $stateId = null)
    {
        $charId = isset($charId) ? $charId : $this->requestData['characteristic'];
        $taxonId = isset($taxonId) ? $taxonId : $this->requestData['taxon'];
        $stateId = isset($stateId) ? $stateId : $this->requestData['state'];
        
        if (strpos($taxonId, 'mx-') === 0) {
            
            $refMatrixId = str_replace('mx-', '', $taxonId);
            $taxonId = null;
        }
        else if (strpos($taxonId, 'var-') === 0) {
            
            $variationId = str_replace('var-', '', $taxonId);
            $taxonId = null;
        }
        else {
            
            $refMatrixId = null;
        }
        
        if (!isset($charId) || (!isset($taxonId) && !isset($refMatrixId) && !isset($variationId)) || !isset($stateId))
            return;
        
        $this->models->MatrixTaxonState->save(
        array(
            'project_id' => $this->getCurrentProjectId(), 
            'matrix_id' => $this->getCurrentMatrixId(), 
            'taxon_id' => (isset($taxonId) ? $taxonId : null), 
            'ref_matrix_id' => (isset($refMatrixId) ? $refMatrixId : null), 
            'variation_id' => (isset($variationId) ? $variationId : null), 
            'characteristic_id' => $charId, 
            'state_id' => $stateId
        ));
    }



    private function deleteLinks ($params = null)
    {
        if (isset($params['id']))
            $d['id'] = $params['id'];
        if (isset($params['state_id']))
            $d['state_id'] = $params['state_id'];
        if (isset($params['characteristic_id']))
            $d['characteristic_id'] = $params['characteristic_id'];
        if (isset($params['taxon_id']))
            $d['taxon_id'] = $params['taxon_id'];
        if (isset($params['matrix_id']))
            $d['ref_matrix_id'] = $params['matrix_id'];
        if (isset($params['variation_id']))
            $d['variation_id'] = $params['variation_id'];
        
        if (!isset($d))
            return;
        
        $d['project_id'] = $this->getCurrentProjectId();
        
        $this->models->MatrixTaxonState->delete($d);
    }



    private function getLinks ($params = null)
    {
        if (isset($params['id']))
            $d['id'] = $params['id'];
        
        if (isset($params['characteristic_id']))
            $d['characteristic_id'] = $params['characteristic_id'];
        
        if (isset($params['taxon_id']))
            $d['taxon_id'] = $params['taxon_id'];
        
        if (isset($params['matrix_id']))
            $d['matrix_id'] = $params['matrix_id'];
        
        if (strpos($d['taxon_id'], 'mx-') === 0) {
            $d['ref_matrix_id'] = str_replace('mx-', '', $d['taxon_id']);
            unset($d['taxon_id']);
        }
        else if (strpos($d['taxon_id'], 'var-') === 0) {
            $d['variation_id'] = str_replace('var-', '', $d['taxon_id']);
            unset($d['taxon_id']);
        }
        
        if (!isset($d))
            return;
        
        $d['project_id'] = $this->getCurrentProjectId();
        
        $mts = $this->models->MatrixTaxonState->_get(array(
            'id' => $d
        ));
        
        foreach ((array) $mts as $key => $val) {
            
            $cs = $this->models->CharacteristicState->_get(
            array(
                'id' => array(
                    'id' => $val['state_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'characteristic_id', 
                'order' => 'show_order'
            ));
            
            $mts[$key]['state'] = $this->getCharacteristicStateLabelOrText($val['state_id'], $this->getDefaultProjectLanguage());
            $mts[$key]['characteristic'] = $this->getCharacteristicLabel($val['characteristic_id'], $this->getDefaultProjectLanguage());
        }
        
        return $mts;
    }



    private function cleanUpEmptyVariables ()
    {
        $this->models->Matrix->delete('delete from %table% 
			where project_id =  ' . $this->getCurrentProjectId() . '
			and got_names = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)');
        
        $this->models->Characteristic->delete('delete from %table% 
			where project_id =  ' . $this->getCurrentProjectId() . '
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)');
        
        /*
		$this->models->CharacteristicState->delete('delete from %table% 
			where project_id =  '.$this->getCurrentProjectId().'
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)'
		);
		*/
        
        // delete labelless states
        $cs = $this->models->CharacteristicState->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        foreach ((array) $cs as $val) {
            
            $cs = $this->models->CharacteristicLabelState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'state_id' => $val['id']
                ), 
                'columns' => 'count(*) as total'
            ));
            
            if ($cs[0]['total'] == 0) {
                
                $this->models->CharacteristicState->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['id']
                ));
            }
        }
        

        // delete orphan state labels
        $cls = $this->models->CharacteristicLabelState->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        foreach ((array) $cls as $val) {
            
            $cs = $this->models->CharacteristicState->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['state_id'], 
                    'got_labels' => '1'
                ), 
                'columns' => 'count(*) as total'
            ));
            
            if ($cs[0]['total'] == 0) {
                
                $this->models->CharacteristicLabelState->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['id']
                ));
            }
        }
    }



    private function updateCharShowOrder ($id, $val)
    {
        $this->models->CharacteristicMatrix->update(array(
            'show_order' => $val
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'matrix_id' => $this->getCurrentMatrixId(), 
            'characteristic_id' => $id
        ));
    }



    private function renumberCharShowOrder ()
    {
        $c = $this->getCharacteristics();
        
        foreach ((array) $c as $key => $val)
            $this->updateCharShowOrder($val['id'], $key);
    }

    private function setDefaultMatrix ($id = null)
    {
        if (isset($id)) {
            
            $this->models->Matrix->update(array(
                'default' => '0'
            ), array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->Matrix->save(array(
                'id' => $id, 
                'default' => 1
            ));
            
            return;
        }
        
        $m = $this->getMatrices();
        
        if (count((array) $m) <= 1) {
            
            $this->models->Matrix->save(array(
                'id' => $m[0]['id'], 
                'default' => 1
            ));
            
            return;
        }
        
        $hasDef = false;
        
        foreach ((array) $m as $val) {
            
            $hasDef = $hasDef == true || $val['default'] == 1;
        }
        
        if (!$hasDef) {
            
            $this->models->Matrix->save(array(
                'id' => $m[0]['id'], 
                'default' => 1
            ));
        }
    }

    private function updateStateShowOrder ($id, $val)
    {
        $this->models->CharacteristicState->update(array(
            'show_order' => $val
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
    }

    private function renumberStateShowOrder ($id)
    {
        $c = $this->getCharacteristicStates($id);
        
        foreach ((array) $c as $key => $val)
            $this->updateStateShowOrder($val['id'], $key);
    }

    private function getVariationsInMatrix ()
    {
        if (!$this->useVariations)
            return;
        
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
            if (isset($mv[$val['id']]))
                $d[] = $val;
        }
        

        $this->customSortArray($d, array(
            'key' => 'label'
        ));
        
        return $d;
    }

    private function saveCharacterGroup($p=null)
    {
	
        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        $label = isset($p['label']) ? trim($p['label']) : null;
		
		if (is_null($label)) {
			$this->addError($this->translate('Cannot save a nameless group.'));
			return; 
		}

        if ($this->models->Chargroup->save(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $matrixId,
				'label' => $label,
				'show_order' => 99
		))) {

			return $this->models->ChargroupLabel->save(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'chargroup_id' => $this->models->Chargroup->getNewId(),
					'label' => $label,
					'language_id' => $this->getDefaultProjectLanguage()
			));		
			
		}

    }

    private function deleteCharacterGroup($p=null)
    {
		
		if (!isset($p['groupId']))
			return;
		else
			$groupId =  $p['groupId'];
		
		$this->models->ChargroupLabel->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'chargroup_id' => $groupId
		));		

        $this->models->Chargroup->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $groupId,
		));

    }

    private function deleteCharacteristicFromGroup ($p=null)
    {

        $charId = isset($p['charId']) ? $p['charId'] : null;
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $matrixId = isset($p['matrixId']) ? $p['matrixId'] : $this->getCurrentMatrixId();
		
		if ($charId==null && $groupId==null) {

			$cg = $this->models->Chargroup->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'matrix_id' => $matrixId
				)
			));
			
			foreach((array)$cg as $val) {

				$this->deleteCharacteristicFromGroup(array('groupId' => $val['id']));

			}
			
		} else {
			
			$d = array('project_id' => $this->getCurrentProjectId());
			
			if (!is_null($charId))
				$d['characteristic_id'] = $charId;

			if (!is_null($groupId))
				$d['chargroup_id'] = $groupId;

			$this->models->CharacteristicChargroup->delete($d);

		}

	}

    private function saveCharacteristicToGroup ($p=null)
    {

        $charId = isset($p['charId']) ? $p['charId'] : null;
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $showOrder = isset($p['showOrder']) ? $p['showOrder'] : null;
		
		if ($charId==null && $groupId==null)
			return null;
		
		$d = array('project_id' => $this->getCurrentProjectId());
		
		if (!is_null($charId))
			$d['characteristic_id'] = $charId;

		if (!is_null($groupId))
			$d['chargroup_id'] = $groupId;

		if (!is_null($showOrder))
			$d['show_order'] = $showOrder;

		$this->models->CharacteristicChargroup->save($d);

	}

    private function getCharacterGroupLabel ($p=null)
    {

        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $langId = isset($p['langId']) ? $p['langId'] : $this->getDefaultProjectLanguage();
		
		if (is_null($groupId) || is_null($langId))
			return;

        $cl = $this->models->ChargroupLabel->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'chargroup_id' => $groupId, 
                'language_id' => $langId
            )
        ));
        
        return $cl[0]['label'];
    }

    private function getCharacterGroups ($p=null)
    {
	
        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $label = isset($p['label']) ? trim($p['label']) : null;
		
		$d = array(
				'project_id' => $this->getCurrentProjectId(), 
				'matrix_id' => $matrixId
            );
			
		if (!is_null($groupId))
			$d['id'] = $groupId;

		if (!is_null($label))
			$d['label'] = $label;
        
        $cg = $this->models->Chargroup->_get(
        array(
            'id' => $d, 
            'order' => 'show_order', 
            'columns' => 'id,matrix_id,label,show_order'
        ));
        
        foreach ((array) $cg as $key => $val) {
            $cg[$key]['label'] = $this->getCharacterGroupLabel(array('groupId'=>$val['id']));
            
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
        
        return (!isset($groupId) ? $cg : (isset($cg[0]) ? $cg[0] : null));
    }

    private function getCharactersNotInGroups ($mId = null)
    {

        $mId = isset($mId) ? $mId : $this->getCurrentMatrixId();
        
        $mc = $this->models->CharacteristicMatrix->freeQuery(
			'select _a.characteristic_id, _a.show_order, _c.id as characteristic_chargroup_id from %PRE%characteristics_matrices _a
			left join %PRE%characteristics_chargroups _c
				on _c.characteristic_id = _a.characteristic_id
			where _a.matrix_id ='.$mId.'
			and _c.id is null'
        );
		
       
        foreach ((array) $mc as $key => $val) {
            
            $labels = $this->getCharacteristicLabels($val['characteristic_id']);
            
            if (isset($labels['label'])) {
                
                $d[] = array_merge($this->getCharacteristic($val['characteristic_id']), $labels, array(
                    'show_order' => $val['show_order']
                ));
            }
        }
        
        return isset($d) ? $d : null;

    }

	private function saveStateImageDimensions($state)
	{
		
		$path = $_SESSION['admin']['project']['urls']['project_media'];
		$file = isset($state['file_name']) ? $state['file_name'] : null;
		
		// if there is a filename, and the file does exist...
		if (!empty($file) && file_exists($path.$file)) {
			
			// ...try to get its dimensions...
			$d = getimagesize($path.$file);

			// ...if that works...
			if ($d!==false) {

				// ...save them as w:h...
				$this->models->CharacteristicState->save(
				array(
					'id' => $state['id'], 
					'project_id' => $this->getCurrentProjectId(), 
					'file_dimensions' => $d[0].':'.$d[1]
				));
				
				// ...and return
				return;

			}

		} 

		// if filename is empty, the file doesn't exist or we couldn't get any dimensions, reset the dimensions, if there were any in the database
		if (!empty($state['file_dimensions'])) {

			$this->models->CharacteristicState->save(
			array(
				'id' => $state['id'], 
				'project_id' => $this->getCurrentProjectId(), 
				'file_dimensions' => 'null'
			));

		}

	}
				
	private function reacquireStateImageDimensions($id)
	{
		
		$d = $this->getCharacteristics($id);
		
		foreach((array)$d as $dVal) {
		
			$v = $this->getCharacteristicStates($dVal['id']);

			foreach((array)$v as $vVal) {
			
				$this->saveStateImageDimensions($this->getCharacteristicState($vVal['id']));
				
			}

			$this->addMessage(sprintf($this->translate('Updated states for "%s".'),$dVal['label']));
			
		}

		
	}


}
