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

	private function checkMatrixIdOverride()
	{

		if ($this->rHasVal('mtrx')) {

			$this->setCurrentMatrix($this->requestData['mtrx']);

		}

	}

    public function indexAction()
    {
   
		unset($_SESSION['user']['search']['hasSearchResults']);
  
		$this->checkMatrixIdOverride();

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$matrices = $this->getMatrices();

			$matrix = array_shift($matrices);

			$this->setCurrentMatrix($matrix['id']);

			//$this->redirect('matrices.php');
		}
		
		$this->redirect('identify.php');

		/*
        $this->setPageName(sprintf(_('Matrix "%s"'),$matrix['name']));

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
		*/
    
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

			//$this->redirect('index.php');
			$this->redirect('identify.php');

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

			//$this->redirect('index.php');
			$this->redirect('identify.php');

		} else {

			$this->redirect('matrices.php');

		}
	
	}

	public function identifyAction()
	{

		$this->checkMatrixIdOverride();

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

		$this->smarty->assign('function','Identify');

        $this->setPageName(sprintf(_('Matrix "%s": identify'),$matrix['name']));

		$this->smarty->assign('characteristics',$this->getCharacteristics());

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
	
	}

	public function examineAction()
	{

		$this->checkMatrixIdOverride();

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

		$this->smarty->assign('function','Examine');

        $this->setPageName(sprintf(_('Matrix "%s": examine'),$matrix['name']));

		$this->smarty->assign('taxa',$this->getTaxa());

		$this->smarty->assign('matrixCount',$this->getMatrixCount());

		$this->smarty->assign('matrix',$matrix);

        $this->printPage();
	
	}

	public function compareAction()
	{

		$this->checkMatrixIdOverride();

		$matrix = $this->getCurrentMatrix();

		if (!isset($matrix)) {

			$this->storeHistory = false;

			$this->redirect('matrices.php');

		}

		$this->smarty->assign('function','Compare');

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
						'columns' => 'id,taxon,is_hybrid'
					)
				);
	
				$taxa[] = $t[0];
	
			}

			$_SESSION['user']['matrix']['taxa'][$this->getCurrentMatrixId()] = $taxa;
			
		}

		return $_SESSION['user']['matrix']['taxa'][$this->getCurrentMatrixId()];

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


}


















