<?php

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
    
    public $usedHelpers = array();

    public $controllerPublicName = 'Matrix key';

	public $cssToLoad = array(
		'basics.css',
		'matrix.css',
		'colorbox/colorbox.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'matrix.js',
				'colorbox/jquery.colorbox.js',
				'dialog/jquery.modaldialog.js'
			),
			'IE' => array(
			)
		);

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

	private function getCurrentMatrixId()
	{
	
		return isset($_SESSION['user']['matrix']['active']) ? $_SESSION['user']['matrix']['active']['id'] : null;

	}

	private function getCurrentMatrix()
	{
	
		return isset($_SESSION['user']['matrix']['active']) ? $_SESSION['user']['matrix']['active'] : null;

	}

	private function setCurrentMatrix($id)
	{
	
		$_SESSION['user']['matrix']['active'] = $this->getMatrix($id);

	}

	private function getMatrices()
	{

		if (!isset($_SESSION['user']['matrix']['matrices'])) {

			$m = $this->models->Matrix->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'got_names' => 1
					),
					'fieldAsIndex' => 'id'
				)
			);
			
			foreach((array)$m as $key => $val) {
	
				$mn = $this->models->MatrixName->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'matrix_id' => $val['id'],
							'language_id' => $this->getCurrentLanguageId()
						),
						'columns' => 'name'
					)
				);
	
				$m[$key]['name'] = $mn[0]['name'];
	
			}
			
			$_SESSION['user']['matrix']['matrices'] = $m;
			
		}

		return $_SESSION['user']['matrix']['matrices'];
	
	}

	private function getMatrixCount()
	{

		$m = $this->getMatrices();

		return count((array)$m);
	
	}

	private function getMatrix($id)
	{

		if (!isset($id)) return;
		
		$m = $this->getMatrices();
		
		return isset($m[$id]) ? $m[$id] : null;

	}


    public function indexAction()
    {
    
		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

        $this->setPageName(sprintf(_('Matrix "%s"'),$matrix['name']));

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
    
    }

    public function matricesAction()
    {

        $this->setPageName( _('Matrices'));

		$matrices = $this->getMatrices();

		if (count((array)$matrices)==0) {
	
			$this->addError(_('No matrices have been defined.'));

		} else
		if (count((array)$matrices)==1) {

			$this->storeHistory = false;

			$matrix = array_shift($matrices);

			$this->setCurrentMatrix($matrix['id']);

			$this->redirect('index.php');

		} else {
		
			$this->smarty->assign('matrices',$matrices);

			$this->smarty->assign('currentMatrixId',$this->getCurrentMatrixId());

		}

        $this->printPage();
    
    }

	public function useMatrixAction()
	{
	
		if ($this->rHasId()) {

			$this->storeHistory = false;

			$this->setCurrentMatrix($this->requestData['id']);

			$this->redirect('index.php');

		} else {

			$this->redirect('matrices.php');

		}
	
	}

	private function getTaxa()
	{

		if (!isset($_SESSION['user']['matrix']['taxa'][$this->getCurrentMatrixId()])) {

			$mt = $this->models->MatrixTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $this->getCurrentMatrixId()
					),
					'columns' => 'taxon_id'
				)
			);
	
			foreach((array)$mt as $key => $val) {
			
				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'id' => $val['taxon_id']
						),
						'columns' => 'id,taxon'
					)
				);
	
				$taxa[] = $t[0];
	
			}

			$_SESSION['user']['matrix']['taxa'][$this->getCurrentMatrixId()] = $taxa;
			
		}

		return $_SESSION['user']['matrix']['taxa'][$this->getCurrentMatrixId()];

	}

	private function getTaxon($id)
	{

		$t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);
	
		return $t[0];

	}

	public function identifyAction()
	{

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

        $this->setPageName(sprintf(_('Matrix "%s": identify'),$matrix['name']));

		$this->smarty->assign('characteristics',$this->getCharacteristics());

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
	
	}

	public function examineAction()
	{

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

        $this->setPageName(sprintf(_('Matrix "%s": examine'),$matrix['name']));

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
	
	}

	public function compareAction()
	{

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

        $this->setPageName(sprintf(_('Matrix "%s": compare'),$matrix['name']));

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
	
	}

	public function ajaxInterfaceAction()
	{

		if (!$this->rHasVal('action') || !$this->rHasId()) {

			$this->smarty->assign('returnText','error');
		
		} else
		if ($this->rHasVal('action','get_states')) {

			$this->smarty->assign('returnText',json_encode($this->getCharacteristicStates($this->requestData['id'])));
		
		} else
		if ($this->rHasVal('action','get_taxa')) {

			$this->smarty->assign('returnText',json_encode((array)$this->getTaxaScores($this->requestData['id'])));
		
		} else
		if ($this->rHasVal('action','get_taxon_states')) {

			$this->smarty->assign('returnText',json_encode((array)$this->getTaxonStates($this->requestData['id'])));
		
		} else
		if ($this->rHasVal('action','compare')) {

			$this->smarty->assign('returnText',json_encode((array)$this->getTaxonComparison($this->requestData['id'])));
		
		}

        $this->printPage();
	
	}

	private function getCharacteristicStates($id)
	{

		if (!isset($id) && !isset($state)) return;


		$cs = $this->models->CharacteristicState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'characteristic_id' => $id
				)
			)
		);

		foreach((array)$cs as $key => $val) {

			$d = $this->getCharacteristic($val['characteristic_id']);
			$cs[$key]['type'] = $d['type'];
			$cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id']);
			$cs[$key]['text'] = $this->getCharacteristicStateLabelOrText($val['id'],'text');

		}

		return $cs;

	}

	private function getCharacteristicState($id)
	{

		if (!isset($id)) return;

		$cs = $this->models->CharacteristicState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		$cs[0]['label'] = $this->getCharacteristicStateLabelOrText($cs[0]['id']);

		return $cs[0];

	}

	private function getCharacteristicStateLabelOrText($id,$type='label')
	{

		if (!isset($id)) return;
        
		$cls = $this->models->CharacteristicLabelState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'state_id' => $id,
					'language_id' => $this->getCurrentLanguageId()
				),
				'columns' => 'text,label'
			)
		);

		return $type=='text' ? $cls[0]['text'] : $cls[0]['label'];

	}

	private function getTaxaScores($states)
	{
	
		$taxa = $this->getTaxa();

		if ($states==-1) return $taxa;

		foreach((array)$taxa as $key => $val) {

			$taxa[$key]['hits'] = 0;

			foreach((array)$states as $sKey => $sVal) {
			
				// ranges and distributions have format f:charid:value (for range or distro)[: + or - times standard dev (distro only)]
				if (substr($sVal,0,1)==='f') {
			
					$d = explode(':',$sVal);

					$charId = $d[1];
					$value = $d[2];
					if (isset($d[3])) $sd = $d[3];

					$d = array(
						'project_id' => $this->getCurrentProjectId(),
						'characteristic_id' => $charId,
					);
					
					if (isset($sd)) {

						$d['mean >=#'] = '('. strval(intval($value)).' - ('.strval(intval($sd)).' * sd))';
						$d['mean <=#'] = '('. strval(intval($value)).' + ('.strval(intval($sd)).' * sd))';

					} else {
			
						$d['lower <='] = intval($value);
						$d['upper >='] = intval($value);

					}

					$cs = $this->models->CharacteristicState->_get(array('id' => $d));

					$hasState = false;
					
					foreach((array)$cs as $cKey => $cVal) {

						$mts = $this->models->MatrixTaxonState->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(),
									'matrix_id' => $this->getCurrentMatrixId(),
									'taxon_id' => $val['id'],
									'state_id' => $cVal['id']
								),
								'columns' => 'count(*) as total'
							)
						);
						
						if ($mts[0]['total']>0) $hasState = true;

					}

					if ($hasState) $taxa[$key]['hits']++; 

				} else {

					$mts = $this->models->MatrixTaxonState->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'matrix_id' => $this->getCurrentMatrixId(),
								'taxon_id' => $val['id'],
								'state_id' => $sVal
							)
						)
					);

					if ($mts) $taxa[$key]['hits']++; 
	
				}
	
			}

			$taxa[$key]['score'] = round(($taxa[$key]['hits'] / count((array)$states)) * 100);

		}

		$sortBy = array(
			'key' => 'score', 
			'dir' => 'desc', 
			'case' => 'i'
		);

		$this->customSortArray($taxa, $sortBy);		

		return $taxa;

	}

	private function getCharacteristics()
	{

		$mc = $this->models->CharacteristicMatrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId()
				),
				'columns' => 'characteristic_id'
			)
		);

		foreach((array)$mc as $key => $val) {
		
			$d = $this->getCharacteristic($val['characteristic_id']);
			
			if ($d) $c[] = $d;

		}

		return isset($c) ? $c : null;
	
	}

	private function getCharacteristicType($type)
	{

		foreach ((array)$this->controllerSettings['characteristicTypes'] as $key => $val) {
		
			if ($val['name']==$type) return $val;

		}
	
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

		if (!isset($c)) return;
		
		$char = $c[0];

		$cl = $this->models->CharacteristicLabel->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->getCurrentLanguageId(),
					'characteristic_id' => $id,
					)
			)
		);

		$char['label'] = $cl[0]['label'];

		$char['type'] = $this->getCharacteristicType($char['type']);
		
		return $char;

	}

	private function getTaxonStates($id)
	{
	
		$mts = $this->models->MatrixTaxonState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
					'taxon_id' => $id,
				),
				'columns' => 'characteristic_id,state_id',
				'fieldAsIndex' => 'characteristic_id'
			)
		);


		foreach((array)$mts as $key => $val) {

			$d = $this->getCharacteristic($val['characteristic_id']);

			$mts[$key]['characteristic'] = $d['label'];
			
			$mts[$key]['type'] = $d['type'];

			$mts[$key]['state'] = $this->getCharacteristicState($val['state_id']);

		}

		return $mts;

	}
	
	private function getTaxonComparison($id)
	{
	
		$c = $this->getCharacteristics();
		$t1 = $this->getTaxonStates($id[0]);
		$t2 = $this->getTaxonStates($id[1]);

		$both = 0;
		$neither = 0;
		$t1_count = 0;
		$t2_count = 0;

		foreach((array)$c as $key => $val) {

			if (
				isset($t1[$val['id']]) && 
				isset($t2[$val['id']]) && 
				($t1[$val['id']]['state_id']==$t2[$val['id']]['state_id'])) {

				$both++;

			} else {

				if (isset($t1[$val['id']])) $t1_count++;
				elseif (isset($t2[$val['id']])) $t2_count++;
				else $neither++;

			}

		}

		return array(
			'taxon_1' => $this->getTaxon($id[0]),
			'taxon_2' => $this->getTaxon($id[1]),
			'count_1' => $t1_count,
			'count_2' => $t2_count,
			'neither' => $neither,
			'both' => $both,
			'total' => count((array)$c),
			'coefficients' => $this->calculateDistances($t1_count,$t2_count,$both,$neither)
		);

	}

	private function calculateDistances($u1,$u2,$co,$ca)
	{

		$prec = 3;

		return array(
			0 => array(
				'name' => _('Simple dissimilarity coefficient'), 
				'value' => ($u1+$u2+$co+$ca)==0 ? 'NaN' : round(1-(($co+$ca)/($u1+$u2+$co+$ca)),$prec)
			),
			1 => array(
				'name' => 'Russel & Rao', 
				'value' => ($u1+$u2+$co+$ca)==0 ? 'NaN' : round(1-($co/($u1+$u2+$co+$ca)),$prec)
			),
			2 => array(
				'name' => 'Rogers & Tanimoto', 
				'value' => ($co+$ca+(2*$u1)+(2*$u2))==0 ? 'NaN' : round(1-(($co+$ca)/($co+$ca+(2*$u1)+(2*$u2))),$prec)
			),
			3 => array(
				'name' => 'Harmann', 
				'value' => ($u1+$u2+$co+$ca)==0 ? 'NaN' : round(1-((($co+$ca-$u1-$u2)/($u1+$u2+$co+$ca))+1)/2,$prec)
			),
			4 => array(
				'name' => 'Sokal & Sneath', 
				'value' => (2*($co+$ca)+$u1+$u2)==0 ? 'NaN' : round(1-((2*($co+$ca)/(2*($co+$ca)+$u1+$u2))),$prec)
			),
			5 => array(
				'name' => 'Jaccard', 
				'value' => ($co+$u1+$u2)==0 ? 'NaN' : round(1-($co/($co+$u1+$u2)),$prec)
			),
			6 => array(
				'name' => 'Czekanowski', 
				'value' => ((2*$co)+$u1+$u2)==0 ? 'NaN' : round(1-((2*$co)/((2*$co)+$u1+$u2)),$prec)
			),
			7 => array(
				'name' => 'Kulczyski', 
				'value' => (($co+$u1)==0 || ($co+$u2)==0) ? 'NaN' : round(1-(($co/2)*((1/($co+$u1))+(1/($co+$u2)))),$prec)
			),
			8 => array(
				'name' => 'Ochiai', 
				'value' => ($co+$u1)*($co+$u2)==0 ? 'NaN' : round(1-($co/sqrt(($co+$u1)*($co+$u2))),$prec)
			)
		);
	
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

				} else {

					$this->models->CharacteristicState->save(
						array(
							'id' => ($this->rHasId() ? $this->requestData['id'] : 'null'),
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
    public function ajaxIntaaerfaceAction ()
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
				'id' => 'null',
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
				'id' => 'null',
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
				'id' => 'null',
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


	/* state functions*/
	private function createState()
	{

		if (!$this->rHasVal('char')) return;

		$this->models->CharacteristicState->save(
			array(
				'id' => 'null',
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

	private function getCharacteristicStatse($id)
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
		
		}
		
		return $mts;

	}



}


















