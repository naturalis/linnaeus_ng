<?php

/*

delete stuff besides char

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
		'characteristic',
		'characteristic_label',
		'characteristic_state'
	);
    
    public $usedHelpers = array('file_upload_helper');

    public $controllerPublicName = 'Matrix key';

	public $cssToLoad = array('matrix.css');

	public $jsToLoad = array('all' => array('matrix.js'));


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

    public function matrixAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Matrix'));

		$this->smarty->assign('characteristics',$this->getCharacteristics());

        $this->printPage();
    
    }

	public function charAction()
	{
	
        $this->checkAuthorisation();
        

		if ($this->rHasId() && $this->rHasVal('action','delete')) {

			$this->deleteCharacteristic();

			$this->redirect('matrix.php');

		} else
		if ($this->rHasId()) {

			$c = $this->models->Characteristic->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $this->requestData['id']
					)
				)
			);

			$this->smarty->assign('characteristic',$c[0]);

	        $this->setPageName(sprintf(_('Editing characteristic "%s"'),$c[0]['characteristic']));

		} else {

	        $this->setPageName( _('New characteristic'));

		}

		if ($this->rHasVal('characteristic') && $this->rHasVal('type') && !$this->isFormResubmit()) {

			if (!$this->rHasId()) {

				$c = $this->models->Characteristic->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'characteristic' => $this->requestData['characteristic']
						),
						'columns' => 'count(*) as total'
					)
				);

			}

			if ($this->rHasId() || $c[0]['total']==0) {

				$this->saveCharacteristic();
				
				$this->redirect('matrix.php');
				
			} else {

				$this->smarty->assign('characteristic',$this->requestData);

				$this->addError(_('A characteristic with that name already exists.'));

			}

		}

		$this->smarty->assign('charTypes',$this->controllerSettings['characteristicTypes']);

        $this->printPage();
	
	}

	public function stateAction()
	{
	
        $this->checkAuthorisation();


		if ($this->rHasId() && $this->rHasVal('action','delete')) {

			$this->deleteCharacteristicState();

			$this->redirect('matrix.php');

		} else
		if ($this->rHasId()) {
		
			$state = $this->getCharacteristicState($this->requestData['id']);
			
			$this->requestData['char'] = $state['characteristic_id'];

		} else
		if (!$this->rHasVal('char')) {
		// must have a characteristic to define a state for
		
			$this->redirect('matrix.php');
		
		}

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Matrix'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/matrix.php'
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

						$this->redirect('matrix.php');

					}

					$this->addMessage(_('State saved.'));

				}

			}


		}


		$this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

		if (isset($state)) $this->smarty->assign('state',$state);

		$this->smarty->assign('characteristic',$characteristic);

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

        }
        elseif ($this->requestData['action'] == 'get_states') {

			$this->getCharacteristicStates();

        }

        $this->printPage();
    
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

/*
		$id = ($this->rHasId() ? $this->requestData['id'] : $this->models->Characteristic->getNewId());

		$this->models->CharacteristicLabel->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'characteristic_id' => $id,
				'language_id' => $this->requestData['language']
			)
		);

		$this->models->CharacteristicLabel->save(
			array(
				'id' => 'null',
				'project_id' => $this->getCurrentProjectId(),
				'characteristic_id' => $id,
				'language_id' => $this->requestData['language'],
				'label' => $this->requestData['label']
			)
		);
*/
    
    }

    private function deleteCharacteristic ($id=null)
    {
	
		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$this->deleteCharacteristicStates($id);

		$this->models->Characteristic->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);
    
    }

	private function getCharacteristics()
	{
	
		return $this->models->Characteristic->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'order' => 'characteristic'
			)
		);
	
	}

	private function getCharacteristic($id)
	{
	
		$c = $this->models->Characteristic->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);
		
		if (!$c) return;

		$c[0]['type'] = $this->getCharacteristicType($c[0]['type']);

		return $c[0];

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


		if ($data['type']=='range') {

			if (!isset($data['lower']) || empty($data['lower'])) {

				$this->addError(_('The lower boundary is required.'));

				$result = false;

			} elseif ($data['lower'] !=  strval(floatval($data['lower']))) {

				$this->addError(_('Invalid value for the lower boundary (must be integer or real).'));

				$result = false;

			}

			if (!isset($data['upper']) || empty($data['upper'])) {

				$this->addError(_('The upper boundary is required.'));

				$result = false;

			} elseif ($data['upper'] !=  strval(floatval($data['upper']))) {

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

			if (!isset($data['mean']) || empty($data['mean'])) {

				$this->addError(_('The mean is required.'));

				$result = false;

			} elseif ($data['mean'] !=  strval(floatval($data['mean']))) {

				$this->addError(_('Invalid value for the mean (must be integer or real).'));

				$result = false;

			}

			if (!isset($data['sd1']) || empty($data['sd1'])) {

				$this->addError(_('The value for one standard deviation is required.'));

				$result = false;

			} elseif ($data['sd1'] !=  strval(floatval($data['sd1']))) {

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

			if (!$file) $this->addError(_('A media file is required.'));

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
	
		$cs = $this-> getCharacteristicStates($charId);
		
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



}


















