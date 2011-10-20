<?php

/*

check deletes when deleting matrix etc

*/

include_once ('Controller.php');

class MatrixKeyController extends Controller
{
    
    public $usedModels = array(
		'matrix',
		'matrix_name',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
		'characteristic_label_state'
	);
    
    public $usedHelpers = array('file_upload_helper');

    public $controllerPublicName = 'Matrix key';

	public $cssToLoad = array('matrix.css','colorbox/colorbox.css');

	public $jsToLoad = array('all' => array('matrix.js','colorbox/jquery.colorbox.js'));


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		$this->smarty->assign('languages', $_SESSION['project']['languages']);
		
		$this->smarty->assign('activeLanguage', $_SESSION['project']['default_language_id']);

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

        $this->checkAuthorisation();
		
		$this->cleanUpEmptyVariables();

		if ($this->getCurrentMatrixId()==null) {

			$matrices = $this->getMatrices();
			
			if (count((array)$matrices)>0) {
			
				$this->setCurrentMatrixId($matrices[0]['id']);
	
				$this->redirect('edit.php');
	
			} else {
	
				$this->redirect('matrices.php');

			}

		} else {

			$this->redirect('edit.php');

		}

		/*
        $this->setPageName( _('Index'));

        $this->printPage();
	    */

    }

    public function matricesAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Matrices'));

		if ($this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$this->deleteMatrix($this->requestData['id']);
			
			if ($this->getCurrentMatrixId()==$this->requestData['id']) $this->setCurrentMatrixId(null);
			
		} else
		if ($this->rHasVal('action','activate') && !$this->isFormResubmit()) {

			$this->setCurrentMatrixId($this->requestData['id']);
			
			$this->redirect('edit.php');

		}

		$matrices = $this->getMatrices();
			
		$this->smarty->assign('matrices',$matrices);

        $this->printPage();
    
    }


    public function matrixAction()
    {

        $this->checkAuthorisation();
        
		if ($this->rHasId()) {

			$matrix = $this->getMatrix();
			
			if (isset($matrix['names'][$_SESSION['project']['default_language_id']]['name'])) {

		        $this->setPageName(sprintf(_('Editing matrix "%s"'),$matrix['names'][$_SESSION['project']['default_language_id']]['name']));

			} else {

		        $this->setPageName(_('New matrix'));

			}

		} else {

			$id = $this->createNewMatrix();

			if ($id) {

				$this->redirect('matrix.php?id='.$id);

			} else {
			
				$this->addError(_('Could not create new matrix.'));			
			
			}
		
		}


		if (isset($matrix)) $this->smarty->assign('matrix',$matrix);

        $this->printPage();
    
    }


    public function editAction()
    {
    
        $this->checkAuthorisation();
		
		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');
		
		$matrix = $this->getMatrix($this->getCurrentMatrixId());

        $this->setPageName(sprintf(_('Editing matrix "%s"'),$matrix['matrix']));

		$this->smarty->assign('characteristics',$this->getCharacteristics());

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
    
    }


	public function charAction()
	{

        $this->checkAuthorisation();
        
		// need an active matrix to assign the characteristic to
		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');

		if (!$this->rHasId()) {

			$id = $this->createCharacteristic();
			
			if ($id) {

				$this->addCharacteristicToMatrix($id);

				$this->redirect('char.php?id='.$id);

			} else {

				$this->addError(_('Could not create characteristic.'));

			}

		}

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Matrix'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
            )
        );

		// get the current active matrix' id
		$matrix = $this->getMatrix($this->getCurrentMatrixId());

		if ($this->rHasId() && $this->rHasVal('action','delete')) {
		
			// delete the char from this matrix (and automatically delete the char itself if it isn't used in any other matrix)
			$this->deleteCharacteristic();
		
			$this->redirect('edit.php');
		
		} else
		if ($this->rHasVal('existingChar') && $this->rHasVal('action','use')) {
		
			$this->addCharacteristicToMatrix($this->requestData['existingChar']);
		
			$this->redirect('edit.php');
		
		} else
		if ($this->rHasId()) {

			$c = $this->getCharacteristic($this->requestData['id']);
			
			$this->smarty->assign('characteristic',$c);
			
			if (isset($c['label'])) {

		        $this->setPageName(sprintf(_('Editing characteristic "%s"'),$c['label']));

			} else {

		        $this->setPageName( _('New characteristic'));

			}

		}

		if ($this->rHasVal('type') && !$this->isFormResubmit()) {

			$charId = $this->updateCharacteristic();

			//$this->addCharacteristicToMatrix($charId);

			$this->redirect('edit.php');

		}

		$this->smarty->assign('languages',$_SESSION['project']['languages']);

		$this->smarty->assign('matrix',$matrix);

		$this->smarty->assign('charLib',$this->getAllCharacteristics($this->getCurrentMatrixId()));

		$this->smarty->assign('charTypes',$this->controllerSettings['characteristicTypes']);

        $this->printPage();
	
	}

    public function taxaAction ()
    {

		$this->checkAuthorisation();

		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');

		$this->setPageName(_('Adding taxa'));

		if ($this->rHasVal('taxon')) { 

			$this->models->MatrixTaxon->save(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
					'taxon_id' => $this->requestData['taxon']
				)
			);

			if ($this->requestData['action']!='repeat') {

				$this->redirect('edit.php');

			}

			$this->addMessage(sprintf(_('Taxon added.')));			

		}
			
		$this->getTaxonTree();

		if (isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

		$this->printPage();

	}


	public function stateAction()
	{
	
        $this->checkAuthorisation();

		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');

		if ($this->rHasId() && $this->rHasVal('action','delete')) {
		
			$this->deleteCharacteristicState();
		
			$this->redirect('edit.php');
		
		} else
		if ($this->rHasId()) {
		
			$state = $this->getCharacteristicState($this->requestData['id']);
			
			$this->requestData['char'] = $state['characteristic_id'];
		
		} else {
		
			if (!$this->rHasVal('char')) {
			// must have a characteristic to define a state for
			
				$this->redirect('edit.php');
			
			} else {

				$id = $this->createState();
				
				if ($id) {
		
					$this->redirect('state.php?char='.$this->requestData['char'].'&id='.$id);

				} else {

					$this->addError(_('Cannot create new state.'));

				}

			}

		}

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Matrix'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
            )
        );

		$characteristic = $this->getCharacteristic($this->requestData['char']);

		$state = $this->getCharacteristicState($this->requestData['id']);

		if ($state['label']) {
		// existing state

			$this->setPageName(sprintf(_('Editing state for "%s"'),$characteristic['label']));

		} else {
		// new state

			$this->setPageName(sprintf(_('New state for "%s"'),$characteristic['label']));

		}

		if ($this->rHasVal('rnd')) {
		// it's a submitted form

			if (!$this->isFormResubmit()) {

				$filesToSave = $this->getUploadedFiles();
	
				if (!$this->verifyData($this->requestData,$filesToSave)) {

					$state = $this->requestData;

					$this->deleteCharacteristicState($this->requestData['id']);

				} else {

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
						)
					);
					
					unset($state);
					
					if ($this->requestData['action']!='repeat') {

						$this->redirect('edit.php');

					}

					$this->addMessage(sprintf(_('State "%s" saved.'),$this->requestData['label']));

					$state = $this->getCharacteristicState($this->createState());

				}

			}

		}

		$this->smarty->assign('matrix',$this->getMatrix($this->getCurrentMatrixId()));

		$this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

		if (isset($state)) $this->smarty->assign('state',$state);

		$this->smarty->assign('characteristic',$characteristic);

        $this->printPage();
	
	}


    public function linksAction ()
    {

		$this->checkAuthorisation();

		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');

		$this->setPageName(_('Taxon-state links'));
		
		if ($this->rHasVal('taxon')) {
		
			$links = $this->getLinks(array('taxon_id'=>$this->requestData['taxon']));

			$this->customSortArray($links, array('key'=>'characteristic','case'=>'i'));

		}

		$this->getTaxonTree();

		if (isset($this->treeList)) $this->smarty->assign('taxa',$this->getTaxa());

		if (isset($links)) $this->smarty->assign('links',$links);

		$this->smarty->assign('matrix',$this->getMatrix($this->getCurrentMatrixId()));

		if ($this->rHasVal('taxon')) $this->smarty->assign('taxon',$this->requestData['taxon']);

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
        
        if ($this->requestData['action'] == 'save_matrix_name') {

			$this->ajaxSaveMatrixName();

        } else
        if ($this->requestData['action'] == 'get_matrix_name') {

			$this->ajaxGetMatrixName();

        } else
        if ($this->requestData['action'] == 'save_characteristic_label') {

			$this->ajaxActionSaveCharacteristicLabel();

        } else
        if ($this->requestData['action'] == 'get_characteristic_label') {

			$this->smarty->assign('returnText',$this->getCharacteristicLabel());

        } else
        if ($this->requestData['action'] == 'get_state_label') {

			$this->ajaxActionGetCharacteristicStateLabel();

        } else
        if ($this->requestData['action'] == 'get_state_text') {

			$this->ajaxActionGetCharacteristicStateText();

        } else
        if ($this->requestData['action'] == 'save_state_label') {

			$this->ajaxActionSaveCharacteristicStateLabel();

        } else
        if ($this->requestData['action'] == 'save_state_text') {

			$this->ajaxActionSaveCharacteristicStateText();

        } else
        if ($this->requestData['action'] == 'remove_taxon') {

			$this->removeTaxon();
			$this->smarty->assign('returnText',json_encode($this->getTaxa()));

        } else
		if ($this->requestData['action'] == 'get_states') {

			$this->getCharacteristicStates();

        } else
        if ($this->requestData['action'] == 'add_link') {

			$this->addLink();

        } else
        if ($this->requestData['action'] == 'delete_link') {

			$this->deleteLinks(array('id'=>$this->requestData['id']));

        } else
        if ($this->requestData['action'] == 'get_links') {

			$this->smarty->assign(
				'returnText',
				json_encode(
					$this->getLinks(
						array(
							'characteristic_id'=>$this->requestData['characteristic'],
							'taxon_id'=>$this->requestData['taxon']
						)
					)
				)
			);

        }

        $this->printPage();
    
    }

	/* matrix functions */
	private function createNewMatrix()
	{
	
		$this->models->Matrix->save(
			array(
				'project_id' => $this->getCurrentProjectId()
			)
		);

		return $this->models->Matrix->getNewId();
		
	}

	private function setMatrixGotNames($id,$state=null)
	{

		if ($state==null) {

			$mn = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $id,
					),
					'columns' => 'count(*) as total'
				)
			);

			$state = ($mn[0]['total']==0 ? false : true);

		}

		$this->models->Matrix->update(
			array(
				'got_names' => ($state==false ? '0' : '1'),
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}

	private function setCurrentMatrixId($id,$name=null)
	{
	
		if ($id == null) {

			unset($_SESSION['matrixkey']['id']);
			unset($_SESSION['matrixkey']['name']);

		} else {
	
			$_SESSION['matrixkey']['id'] = $id;
			if ($name) $_SESSION['matrixkey']['name'] = $name;

		}

	}

	private function getCurrentMatrixId()
	{

		return isset($_SESSION['matrixkey']['id']) ? $_SESSION['matrixkey']['id'] : null;

	}

	private function getMatrix($id=null)
	{

		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);


		$mn = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $id
				),
				'fieldAsIndex' => 'language_id'
			)
		);

		$m[0]['names']=$mn;

		$m[0]['matrix'] = $m[0]['names'][$_SESSION['project']['default_language_id']]['name'];

		return $m[0];

	}

	private function deleteMatrix($id)
	{

		if (!isset($id)) return;

		$c = $this->getCharacteristics($id);
		
		foreach((array)$c as $key => $val) {

			// deletes characteristics, states and links
			$this->deleteCharacteristic($val['id']);

		}

		$this->models->MatrixName->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $this->requestData['id']
			)
		);

		$this->models->Matrix->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}

	private function getMatrices()
	{

		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_names' => 1
				)
			)
		);
		
		foreach((array)$m as $key => $val) {

			$mn = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $val['id']
					),
					'fieldAsIndex' => 'language_id'
				)
			);

			$m[$key]['names'] = $mn;

		}

		return $m;
	
	}

	private function ajaxSaveMatrixName()
	{

       if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('content')) {

                $this->models->MatrixName->delete(
                    array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $this->requestData['id'],
						'language_id' => $this->requestData['language']
                    )
                );
				
				$this->setMatrixGotNames($this->requestData['id']);

            } else {

				$mn = $this->models->MatrixName->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'matrix_id' => $this->requestData['id'],
							'language_id' => $this->requestData['language']
						)
					)
				);

                
                $this->models->MatrixName->save(
					array(
						'id' => isset($mn[0]['id']) ? $mn[0]['id'] : null, 
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'matrix_id' => $this->requestData['id'],
						'name' => trim($this->requestData['content'])
					)
				);

				$this->setMatrixGotNames($this->requestData['id'],true);

            }

            $this->smarty->assign('returnText', 'saved');
        
        }

	}

	private function ajaxGetMatrixName()
	{
	
        if (!$this->rHasVal('id') || !$this->rHasVal('language')) {
            
            return;
        
        } else {

			$mn = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $this->requestData['id'],
						'language_id' => $this->requestData['language']
					),
					'columns' => 'name'
				)
			);

            $this->smarty->assign('returnText', $mn[0]['name']);

		}	
	
	}

	/* characteristics functions */
    private function createCharacteristic()
    {

		$this->models->Characteristic->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'type' => $this->controllerSettings['characteristicTypes'][0]['name']
			)
		);

		return $this->models->Characteristic->getNewId();

    }

	private function addCharacteristicToMatrix($charId,$matrixId=null)
	{

		$matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();

		if (!isset($charId) || !isset($matrixId)) return;

		@$this->models->CharacteristicMatrix->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId,
				'characteristic_id' => $charId
			)
		);

	}

    private function updateCharacteristic()
    {

		$this->models->Characteristic->update(
			array(
				'type' => $this->requestData['type']
			),
			array(
				'id' => $this->requestData['id'],
				'project_id' => $this->getCurrentProjectId(),
			)
		);

    }

	private function setCharacteristicGotLabels($id,$state=null)
	{

		if ($state==null) {

			$cl = $this->models->CharacteristicLabel->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic_id' => $id,
					),
					'columns' => 'count(*) as total'
				)
			);

			$state = ($cl[0]['total']==0 ? false : true);

		}

		$this->models->Characteristic->update(
			array(
				'got_labels' => ($state==false ? '0' : '1'),
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}

	private function ajaxActionSaveCharacteristicLabel()
	{

		if (!$this->rHasVal('language')) {
			
			return;
		
		} else {
			
			if (!$this->rHasVal('label') && !$this->rHasId()) {
	
				$this->models->CharacteristicLabel->delete(
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'characteristic_id' => $this->requestData['id']
					)
				);

				$this->setCharacteristicGotLabels($this->requestData['id']);
		
			} else {
				
				if ($this->rHasId()) {
				
					$cl = $this->models->CharacteristicLabel->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(), 
								'language_id' => $this->requestData['language'], 
								'characteristic_id' => $this->requestData['id']
							)
						)
					);
					
					$charId = isset($cl[0]['id']) ? $cl[0]['id'] : null;

				} else {
				
					$charId = null;
				
				}
				
				$this->models->CharacteristicLabel->save(
					array(
						'id' => $charId, 
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'characteristic_id' => $this->requestData['id'], 
						'label' => trim($this->requestData['content'])
					)
				);

				$this->setCharacteristicGotLabels($this->requestData['id'],true);

			}
			
			$this->smarty->assign('returnText', 'saved');

		}

	}

	private function getCharacteristicLabel($id=null,$language=null)
	{


		$id = isset($id) ? $id : $this->requestData['id'];
		$language = isset($language) ? $language : $this->requestData['language'];

        if (!isset($id) || !isset($language)) return;
        
		$cl = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $language,
					'characteristic_id' => $id,
					)
			)
		);
			
		return $cl[0]['label'];
			
	}

	private function getCharacteristicLabels($id)
	{

		$cl = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'characteristic_id' => $id,
				),
				'fieldAsIndex' => 'language_id'
			)
		);

		return array(
			'labels' => $cl, 
			'label' => $cl[$_SESSION['project']['default_language_id']]['label']
		);
		
	}

	private function getCharacteristic($id)
	{

		$c = $this->models->Characteristic->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
					'id' => $id
				)
			)
		);
		
		if (!$c)
			return;
		else
			$char = $c[0];

		$char['type'] = $this->getCharacteristicType($char['type']);
		
		$d = $this->getCharacteristicLabels($id);

		$char['labels'] = $d['labels'];
		$char['label'] = $d['label'];

		return $char;

	}

    private function deleteCharacteristic($id=null)
    {
	
		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;
		
		$this->deleteLinks(array('characteristic_id'=>$id,'matrix_id'=>$this->getCurrentMatrixId()));

		// delete from matrix-char table for current matrix
		$this->models->CharacteristicMatrix->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $this->getCurrentMatrixId(),
				'characteristic_id' => $id
			)
		);

		// check if char is used in any other matrix
		$mc = $this->models->CharacteristicMatrix->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id !=' => $this->getCurrentMatrixId(),
					'characteristic_id' => $id
				),
				'columns' => 'count(*) as total'
			)
		);

		if ($mc[0]['total']==0) {
		// if not, adieu

			$this->deleteCharacteristicStates($id);

			$this->models->CharacteristicLabel->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'characteristic_id' => $id,
				)
			);	

			$this->models->Characteristic->delete(
				array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId()
				)
			);

		}

    }

	private function getCharacteristics($matrixId=null)
	{

		$matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();

		$mc = $this->models->CharacteristicMatrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $matrixId,
				),
				'columns' => 'characteristic_id'
			)
		);

		foreach((array)$mc as $key => $val) {

			$labels = $this->getCharacteristicLabels($val['characteristic_id']);
			
			if (isset($labels['label'])) {

				$d[] = array_merge(
					$this->getCharacteristic($val['characteristic_id']),
					$labels
				);

			}

		}

		return isset($d) ? $d : null;
	
	}

	private function getAllCharacteristics($matrixToExclude=null)
	{

		if (isset($matrixToExclude)) {
		
			$ce = $this->getCharacteristics($matrixToExclude);

			if (isset($ce)) {

				$b = null;
	
				foreach((array)$ce as $key => $val) {
	
					$b .= $val['id'].', ';
	
				}
				
				$b = '('.rtrim($b,', ').')';
	
				$id['id not in'] = $b;

			}

		}

		$id['project_id'] = $this->getCurrentProjectId();

		$id['got_labels'] = '1';

		$c = $this->models->Characteristic->_get(array('id' => $id));
		
		foreach((array)$c as $key => $val) {

			$d = $this->getCharacteristicLabels($val['id']);
			$c[$key]['label'] = $d['label'];

		}

		return $c;
	
	}

	private function getCharacteristicType($type)
	{

		foreach ((array)$this->controllerSettings['characteristicTypes'] as $key => $val) {
		
			if ($val['name']==$type) return $val;

		}
	
	}

	/* state functions*/
	private function createState()
	{

		if (!$this->rHasVal('char')) return;

		$this->models->CharacteristicState->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'characteristic_id' => $this->requestData['char'],
			)
		);

		return $this->models->CharacteristicState->getNewId();

	}

	private function getCharacteristicStateLabels($id)
	{

		$cls = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'state_id' => $id
				),
				'fieldAsIndex' => 'language_id'
			)
		);

		return array('label' => $cls[$_SESSION['project']['default_language_id']]['label'], 'labels' => $cls);

	}

	private function getCharacteristicState($id)
	{

		$cs = $this->models->CharacteristicState->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$state = $cs[0];
		
		$d = $this->getCharacteristicStateLabels($id);

		$state['labels'] = $d['labels'];
		$state['label'] = $d['label'];

		return $state;

	}

	private function verifyData($data,$file)
	{

		$result = true;

		if (!isset($data['label']) || empty($data['label'])) {
		// each state has a name, regardless of type

			$this->addError(_('A name is required.'));

			$result = false;

		}

		if ($data['type']=='text') {

			if (!isset($data['text']) || empty($data['text'])) {

				$this->addError(_('Text is required.'));

				$result = false;

			}

		} else
		if ($data['type']=='range') {

			if (!isset($data['lower']) || empty($data['lower']) && $data['lower']!=='0') {

				$this->addError(_('The lower boundary is required.'));

				$result = false;

			} elseif ($data['lower'] != strval(floatval($data['lower']))) {

				$this->addError(_('Invalid value for the lower boundary (must be integer or real).'));

				$result = false;

			}

			if (!isset($data['upper']) || empty($data['upper']) && $data['upper']!=='0') {

				$this->addError(_('The upper boundary is required.'));

				$result = false;

			} elseif ($data['upper'] != strval(floatval($data['upper']))) {

				$this->addError(_('Invalid value for the upper boundary (must be integer or real).'));

				$result = false;

			}

			if ($result  && (floatval($data['upper']) < floatval($data['lower']))) {

				$this->addError(_('The upper boundary value must be larger than the lower boundary value.'));

				$result = false;

			} elseif ($result  && (floatval($data['upper']) == floatval($data['lower']))) {

				$this->addError(_('The upper and lower boundary values cannot be the same.'));

				$result = false;

			}


		} else
		if ($data['type']=='distribution') {

			if (!isset($data['mean']) || empty($data['mean']) && $data['mean']!=='0') {

				$this->addError(_('The mean is required.'));

				$result = false;

			} elseif ($data['mean'] != strval(floatval($data['mean']))) {

				$this->addError(_('Invalid value for the mean (must be integer or real).'));

				$result = false;

			}

			if (!isset($data['sd']) || empty($data['sd']) && $data['sd']!=='0') {

				$this->addError(_('The value for one standard deviation is required.'));

				$result = false;

			} elseif ($data['sd'] !=  strval(floatval($data['sd'])) && $data['mean']!=='0') {

				$this->addError(_('Invalid value for one standard deviation (must be integer or real).'));

				$result = false;

			}

		} else
		if ($data['type']=='media') {

			if (!$file && !isset($data['existing_file'])) {
			
				$this->addError(_('A media file is required.'));
				
				$result = false;

			}

		}

		return $result;

	}

	private function getCharacteristicStates($id=null)
	{
	
		$id = isset($id) ? $id : $this->requestData['id'];

		if (!isset($id)) return;

		$cs = $this->models->CharacteristicState->_get(
			array('id' =>
				array(
					'project_id' => $this->getCurrentProjectId(),
					'characteristic_id' => $id,
				),
				'columns' => 'id,characteristic_id'
			)
		);

		foreach((array)$cs as $key => $val) {

			$cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id'],$_SESSION['project']['default_language_id']);

		}

		$this->smarty->assign('returnText', json_encode($cs));

	}

	private function getCharacteristicStateLabelOrText($id=null,$language=null,$type='label')
	{

		$id = isset($id) ? $id : $this->requestData['id'];
		$language = isset($language) ? $language : $this->requestData['language'];
		
		if (!isset($id) || !isset($language)) return;
        
		$cls = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'state_id' => $id,
					'language_id' => $language
				)
			)
		);

		return $type=='text' ? $cls[0]['text'] : $cls[0]['label'];

	}


	private function ajaxActionGetCharacteristicStateLabel()
	{

		$this->smarty->assign(
			'returnText',
			$this->getCharacteristicStateLabelOrText($this->requestData['id'],$this->requestData['language'])
		);

	}


	private function ajaxActionGetCharacteristicStateText()
	{

		$this->smarty->assign(
			'returnText',
			$this->getCharacteristicStateLabelOrText($this->requestData['id'],$this->requestData['language'],'text')
		);

	}


	private function setCharacteristicStateGotLabels($id,$state=null)
	{

		if ($state==null) {

			$cl = $this->models->CharacteristicLabelState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'state_id' => $id,
					),
					'columns' => 'count(*) as total'
				)
			);

			$state = ($cl[0]['total']==0 ? false : true);

		}

		$this->models->CharacteristicState->update(
			array(
				'got_labels' => ($state==false ? '0' : '1'),
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}


	private function saveCharacteristicStateLabelOrText($id,$language,$content,$type='label')
	{

		if (!$content) {

			$this->models->CharacteristicLabelState->delete(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'state_id' => $id,
					'language_id' => $language
				)
			);

			$this->setCharacteristicStateGotLabels($id);
	
		} else {

			$cls = $this->models->CharacteristicLabelState->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'state_id' => $id,
						'language_id' => $language
					)
				)
			);
			
			$clsId = isset($cls[0]['id']) ? $cls[0]['id'] : null;

			$s = array(
				'id' => $clsId, 
				'project_id' => $this->getCurrentProjectId(),
				'state_id' => $id,
				'language_id' => $language
			);

			if ($type=='label') { 

				$s['label'] = trim($content);

			} else {

				$s['text'] = trim($content);

			}

			$this->models->CharacteristicLabelState->save($s);

			$this->setCharacteristicStateGotLabels($id,true);

		}

	}


	private function ajaxActionSaveCharacteristicStateLabel()
	{

		if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
			
			return;
		
		} else {

			$this->saveCharacteristicStateLabelOrText(
				$this->requestData['id'],
				$this->requestData['language'],
				$this->requestData['content']
			);
	
			$this->smarty->assign('returnText', 'saved');

		}

	}

	private function ajaxActionSaveCharacteristicStateText()
	{
	
		if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
			
			return;
		
		} else {

			$this->saveCharacteristicStateLabelOrText(
				$this->requestData['id'],
				$this->requestData['language'],
				$this->requestData['content'],
				'text'
			);

			$this->smarty->assign('returnText', 'saved');

		}

	}


    private function deleteCharacteristicState ($id=null)
    {

		$id = isset($id) ? $id : $this->requestData['id'];

		if (!isset($id)) return;

		$cs = $this->getCharacteristicState($id);

		if ($cs['file_name']) {

			@unlink($_SESSION['project']['paths']['project_media'].$cs['file_name']);

		}

		$this->models->CharacteristicLabelState->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'state_id' => $id
			)
		);

		$this->models->CharacteristicState->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

    }

    private function deleteCharacteristicStates ($charId)
    {
	
		$cs = $this->getCharacteristicStates($charId);

		$this->deleteLinks(array('characteristic_id'=>$charId));

		foreach((array)$cs as $key => $val) {

			if ($val['file_name']) {
	
				@unlink($_SESSION['project']['paths']['project_media'].$val['file_name']);
	
			}

		}
	
		$this->models->CharacteristicState->delete(
			array(
				'characteristic_id' => $charId,
				'project_id' => $this->getCurrentProjectId()
			)
		);
    
    }

	private function getTaxa()
	{

		$mt = $this->models->MatrixTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId()
				),
				'columns' => 'taxon_id'
			)
		);

		$this->getTaxonTree();

		foreach((array)$mt as $key => $val) {

			$t[] = array(
				'id' => $this->treeList[$val['taxon_id']]['id'],
				'taxon' => $this->treeList[$val['taxon_id']]['taxon']
			);

		}

		return isset($t) ? $t : null;

	}
	

	private function removeTaxon($id=null)
	{

		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$this->deleteLinks(array('taxon_id'=>$id));

		$this->models->MatrixTaxon->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $id
			)
		);

	}

	private function addLink($charId=null,$taxonId=null,$stateId=null)
	{

		$charId = isset($charId) ? $charId : $this->requestData['characteristic'];
		$taxonId = isset($taxonId) ? $taxonId : $this->requestData['taxon'];
		$stateId = isset($stateId) ? $stateId : $this->requestData['state'];
		
		if (!isset($charId) || !isset($taxonId) || !isset($stateId)) return;

		@$this->models->MatrixTaxonState->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $this->getCurrentMatrixId(),
				'taxon_id' => $taxonId,
				'characteristic_id' => $charId,
				'state_id' => $stateId
			)
		);
	
	}

	private function deleteLinks($params=null)
	{

		if (isset($params['id'])) $d['id'] = $params['id'];
		if (isset($params['state_id'])) $d['state_id'] = $params['state_id'];
		if (isset($params['characteristic_id'])) $d['characteristic_id'] = $params['characteristic_id'];
		if (isset($params['taxon_id'])) $d['taxon_id'] = $params['taxon_id'];
		if (isset($params['matrix_id'])) $d['matrix_id'] = $params['matrix_id'];

		if (!isset($d)) return;

		$d['project_id'] =$this->getCurrentProjectId();

		$this->models->MatrixTaxonState->delete($d);

	}

	private function getLinks($params=null)
	{

		if (isset($params['id'])) $d['id'] = $params['id'];
		if (isset($params['characteristic_id'])) $d['characteristic_id'] = $params['characteristic_id'];
		if (isset($params['taxon_id'])) $d['taxon_id'] = $params['taxon_id'];
		if (isset($params['matrix_id'])) $d['matrix_id'] = $params['matrix_id'];

		if (!isset($d)) return;

		$d['project_id'] = $this->getCurrentProjectId();

		$mts = $this->models->MatrixTaxonState->_get(array('id' => $d));
		
		foreach((array)$mts as $key => $val) {

			$cs = $this->models->CharacteristicState->_get(
				array('id' =>
					array(
						'id' => $val['state_id'],
						'project_id' => $this->getCurrentProjectId(),
					),
					'columns' => 'characteristic_id'
				)
			);
			
			$mts[$key]['state'] = $this->getCharacteristicStateLabelOrText($val['state_id'],$_SESSION['project']['default_language_id']);
			$mts[$key]['characteristic'] = $this->getCharacteristicLabel($val['characteristic_id'],$_SESSION['project']['default_language_id']);
		
		}
		
		return $mts;

	}

	private function cleanUpEmptyVariables()
	{

		$this->models->Matrix->delete('delete from %table% 
			where project_id =  '.$this->getCurrentProjectId().'
			and got_names = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)'
		);

		$this->models->Characteristic->delete('delete from %table% 
			where project_id =  '.$this->getCurrentProjectId().'
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)'
		);

		$this->models->CharacteristicState->delete('delete from %table% 
			where project_id =  '.$this->getCurrentProjectId().'
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)'
		);

	}	

}


















