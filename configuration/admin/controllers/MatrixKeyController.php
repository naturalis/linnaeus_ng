<?php

/*
matrix
  object = taxon
  characteristic
    text / long text / picture / movie
    states +
  link object / states

compare object to object
  object 1
  object 2
  distance formula's
    simple dissimilarity coefficient
    russel & rao
    rogers & tanimoto
    hamann
    sokal & sneath
    jaccard
    czekanowski
    kulczyski
    ochiai
  taxonomic distance
  unique states
  states present in both
  states absent in both

examine
  taxon: characters / states

matrix
  characteristic + states: taxa match (complete list with percentage)

STATES HEBBEN HARDE GRENZEN! AUW!



*/

include_once ('Controller.php');

class MatrixKeyController extends Controller
{
    
    public $usedModels = array(
		'matrix',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
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
        
        $this->setPageName( _('Index'));

        $this->printPage();
    
    }

    public function matricesAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Matrices'));

		if ($this->rHasVal('action','delete') && !$this->isFormResubmit()) {

			$this->deleteMatrix($this->requestData['id']);
			
			if ($this->getCurrentMatrixId()==$this->requestData['id']) $this->setCurrentMatrixId(null);
/*
del states
del characteristics
del matrox

*/
			
		} else
		if ($this->rHasVal('action','activate') && !$this->isFormResubmit()) {

			$this->setCurrentMatrixId($this->requestData['id']);
			
			$this->redirect('edit.php');

		}

		$matrices = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'order' => 'matrix'
			)
		);

		$this->smarty->assign('matrices',$matrices);

        $this->printPage();
    
    }

    public function matrixAction()
    {
    
        $this->checkAuthorisation();
        
		if ($this->rHasId()) {

			$matrix = $this->getMatrix();

	        $this->setPageName(sprintf(_('Editing matrix "%s"'),$matrix['matrix']));

		} else {

	        $this->setPageName(_('New matrix'));
		
		}

		if ($this->rHasVal('matrix') && !$this->isFormResubmit()) {

			$v = array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix' => $this->requestData['matrix']
			);
			
			if ($this->rHasId()) {

				$v['id !='] = $this->requestData['id'];

			}

			$m = $this->models->Matrix->_get(
				array(
					'id' => $v,
					'columns' => 'count(*) as total'
				)
			);
			
			if ($m[0]['total']>0) {
			
				$this->addError(_('A matrix with that name already exists.'));
				
				$matrix = $this->requestData;
			
			} else {

				$this->models->Matrix->save(
					array(
						'id' => ($this->rHasId() ? $this->requestData['id'] : 'null'),
						'project_id' => $this->getCurrentProjectId(),
						'matrix' =>  $this->requestData['matrix']
					)
				);

				$this->redirect('matrices.php');

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

	        $this->setPageName(sprintf(_('Editing characteristic "%s"'),$c['characteristic']));

		} else {

	        $this->setPageName( _('New characteristic'));

		}

		if ($this->rHasVal('characteristic') && $this->rHasVal('type') && !$this->isFormResubmit()) {

			// avoid duplicate names
			$c = $this->models->Characteristic->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic' => $this->requestData['characteristic'],
						'id !=' => $this->rHasId() ? $this->requestData['id'] : -1
					),
					'columns' => 'count(*) as total'
				)
			);

			if ($c[0]['total']==0) {

				$charId = $this->saveCharacteristic();

				$this->addCharacteristicToMatrix($charId);

				$this->redirect('edit.php');
				
			} else {

				$this->smarty->assign('characteristic',$this->requestData);

				$this->addError(_('A characteristic with that name already exists.'));

			}

		}

		$this->smarty->assign('matrix',$matrix);

		if (!$this->rHasId()) $this->smarty->assign('charLib',$this->getAllCharacteristics($this->getCurrentMatrixId()));

		$this->smarty->assign('charTypes',$this->controllerSettings['characteristicTypes']);

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

		} else
		if (!$this->rHasVal('char')) {
		// must have a characteristic to define a state for
		
			$this->redirect('edit.php');
		
		}

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Matrix'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
            )
        );

		$characteristic = $this->getCharacteristic($this->requestData['char']);

		if ($this->rHasId()) {
		// existing state

			$this->setPageName(sprintf(_('Editing state for "%s"'),$characteristic['characteristic']));

		} else {
		// new state

			$this->setPageName(sprintf(_('New state for "%s"'),$characteristic['characteristic']));

		}

		if ($this->rHasVal('rnd')) {
		// it's a submitted form

			if (!$this->isFormResubmit()) {

				$filesToSave = $this->getUploadedFiles();
	
				if (!$this->verifyData($this->requestData,$filesToSave)) {
	
					$state = $this->requestData;

				} else {

					$this->models->CharacteristicState->save(
						array(
							'id' => ($this->rHasId() ? $this->requestData['id'] : 'null'),
							'project_id' => $this->getCurrentProjectId(),
							'characteristic_id' => $this->requestData['char'],
							'label' => $this->requestData['label'],
							'text' => isset($this->requestData['text']) ? strip_tags($this->requestData['text']) : null,
							'file_name' => isset($filesToSave[0]['name']) ? $filesToSave[0]['name'] : null,
							'lower' => isset($this->requestData['lower']) ? $this->requestData['lower'] : null,
							'upper' => isset($this->requestData['upper']) ? $this->requestData['upper'] : null,
							'mean' => isset($this->requestData['mean']) ? $this->requestData['mean'] : null,
							'sd1' => isset($this->requestData['sd1']) ? $this->requestData['sd1'] : null,
							'sd2' => isset($this->requestData['sd2']) ? $this->requestData['sd2'] : null
						)
					);
					
					unset($state);
					
					if ($this->requestData['action']!='repeat') {

						$this->redirect('edit.php');

					}

					$this->addMessage(sprintf(_('State "%s" saved.'),$this->requestData['label']));

				}

			}

		}

		$this->smarty->assign('matrix',$this->getMatrix($this->getCurrentMatrixId()));

		$this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

		if (isset($state)) $this->smarty->assign('state',$state);

		$this->smarty->assign('characteristic',$characteristic);

        $this->printPage();
	
	}


    public function taxaAction ()
    {

		$this->checkAuthorisation();

		if ($this->getCurrentMatrixId()==null) $this->redirect('matrices.php');

		$this->setPageName(_('Adding axa'));

		if ($this->rHasVal('taxon')) { 

			$this->models->MatrixTaxon->save(
				array(
					'id' => 'null',
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
        
        if ($this->requestData['action'] == 'save_characteristic') {

			$this->saveCharacteristic();

        } else
        if ($this->requestData['action'] == 'remove_taxon') {

			$this->removeTaxon();
			$this->smarty->assign('returnText',json_encode($this->getTaxa()));

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

        } else
		if ($this->requestData['action'] == 'get_states') {

			$this->getCharacteristicStates();

        }

        $this->printPage();
    
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

		return $_SESSION['matrixkey']['id'];

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

		$this->models->Matrix->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}

    private function saveCharacteristic ()
    {

		$this->models->Characteristic->save(
			array(
				'id' => ($this->rHasId() ? $this->requestData['id'] : 'null'),
				'project_id' => $this->getCurrentProjectId(),
				'type' => $this->requestData['type'],
				'characteristic' => $this->requestData['characteristic']
			)
		);
		
		return $this->rHasId() ? $this->requestData['id'] : $this->models->Characteristic->getNewId();

    }

	private function addCharacteristicToMatrix($charId,$matrixId=null)
	{

		$matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();

		if (!isset($charId) || !isset($matrixId)) return;

		@$this->models->CharacteristicMatrix->save(
			array(
				'id' => 'null',
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId,
				'characteristic_id' => $charId
			)
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
		
		if (!$c) return;

		$c[0]['type'] = $this->getCharacteristicType($c[0]['type']);

		return $c[0];

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
					'matrix_id' => $matrixId
				),
				'columns' => 'characteristic_id'
			)
		);
		
		foreach((array)$mc as $key => $val) {

			$d[] = $this->getCharacteristic($val['characteristic_id']);

		}
		
		return isset($d) ? $d : null;
	
	}

	private function getAllCharacteristics($matrixToExclude=null)
	{

		if (isset($matrixToExclude)) {
		
			$ce = $this->getCharacteristics($matrixToExclude);
			
			$b = null;

			foreach((array)$ce as $key => $val) {

				$b .= $val['id'].', ';

			}
			
			$b = '('.rtrim($b,', ').')';

			$id['id not in'] = $b;

		}

		$id['project_id'] = $this->getCurrentProjectId();

		$c = $this->models->Characteristic->_get(
			array(
				'id' => $id,
				'order' => 'characteristic'
			)
		);
		
		return $c;
	
	}

	private function getCharacteristicType($type)
	{

		foreach ((array)$this->controllerSettings['characteristicTypes'] as $key => $val) {
		
			if ($val['name']==$type) return $val;

		}
	
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

			if (!isset($data['sd1']) || empty($data['sd1']) && $data['sd1']!=='0') {

				$this->addError(_('The value for one standard deviation is required.'));

				$result = false;

			} elseif ($data['sd1'] !=  strval(floatval($data['sd1'])) && $data['mean']!=='0') {

				$this->addError(_('Invalid value for one standard deviation (must be integer or real).'));

				$result = false;

			}

			if (!isset($data['sd2']) || empty($data['sd2'])) {

				$this->addError(_('The value for two standard deviation is required.'));

				$result = false;

			} elseif ($data['sd2'] !=  strval(floatval($data['sd2']))) {

				$this->addError(_('Invalid value for two standard deviation (must be integer or real).'));

				$result = false;

			}

		} else
		if ($data['type']=='media') {

			if (!$file && !isset($data['existing_file'])) $this->addError(_('A media file is required.'));

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
				'columns' => 'id,label,characteristic_id',
				'order' => 'label'
			)
		);
		
		$this->smarty->assign('returnText', json_encode($cs));

	}


	private function getCharacteristicState($id)
	{

		$cs = $this->models->CharacteristicState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		return $cs[0];

	}

    private function deleteCharacteristicState ($id=null)
    {
	
		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$cs = $this->getCharacteristicState($id);

		if ($cs['file_name']) {

			@unlink($_SESSION['project']['paths']['project_media'].$cs['file_name']);

		}

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
				'id' => 'null',
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

		$d['project_id'] =$this->getCurrentProjectId();

		$mts = $this->models->MatrixTaxonState->_get(array('id' => $d));
		
		foreach((array)$mts as $key => $val) {

			$cs = $this->models->CharacteristicState->_get(
				array('id' =>
					array(
						'id' => $val['state_id'],
						'project_id' => $this->getCurrentProjectId(),
					),
					'columns' => 'label,characteristic_id'
				)
			);

			$c = $this->models->Characteristic->_get(
				array('id' =>
					array(
						'id' => $cs[0]['characteristic_id'],
						'project_id' => $this->getCurrentProjectId(),
					),
					'columns' => 'characteristic'
				)
			);

			$mts[$key]['state'] = $cs[0]['label']; 
			$mts[$key]['characteristic'] = $c[0]['characteristic']; 
		
		}
		
		return $mts;

	}
	
	
}


















