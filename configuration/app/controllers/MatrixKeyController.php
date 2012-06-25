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

		$this->checkForProjectId();

		$this->setCssFiles();

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
   
		unset($_SESSION['app']['user']['search']['hasSearchResults']);
  
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

        //$this->setPageName( _('Matrices'));

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

		$this->smarty->assign('storedStates',$this->stateMemoryRecall());

		$this->smarty->assign('characteristics',$this->getCharacteristics());

		$this->smarty->assign('taxa',$this->getTaxaInMatrix());

		$this->smarty->assign('matrices',$this->getMatricesInMatrix());

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

		$this->smarty->assign('taxa',$this->getTaxaInMatrix());

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

		$this->smarty->assign('taxa',$this->getTaxaInMatrix());

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

			$this->stateMemoryStore($this->requestData['id']);

			$this->smarty->assign(
				'returnText',
				json_encode(
					(array)$this->getTaxaScores(
						$this->requestData['id'],
						isset($this->requestData['inc_unknowns']) ? ($this->requestData['inc_unknowns']=='1') : false
					)
				)
			);
		
		} else
			if ($this->rHasVal('action','get_taxon_states')) {

			$this->smarty->assign('returnText',json_encode((array)$this->getTaxonStates($this->requestData['id'])));
		
		} else
		if ($this->rHasVal('action','compare')) {

			$this->smarty->assign('returnText',json_encode((array)$this->getTaxonComparison($this->requestData['id'])));
		
		}

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
	
	}

	public function getCurrentMatrixId()
	{
	
		return isset($_SESSION['app']['user']['matrix']['active']) ? $_SESSION['app']['user']['matrix']['active']['id'] : null;

	}

	private function checkMatrixIdOverride()
	{

		if ($this->rHasVal('mtrx'))  $this->setCurrentMatrix($this->requestData['mtrx']);

	}

	private function getCurrentMatrix()
	{
	
		return isset($_SESSION['app']['user']['matrix']['active']) ? $_SESSION['app']['user']['matrix']['active'] : null;

	}

	private function setCurrentMatrix($id)
	{
	
		$_SESSION['app']['user']['matrix']['active'] = $this->getMatrix($id);

	}

	private function getMatrices()
	{

		if (!isset($_SESSION['app']['user']['matrix']['matrices'])) {

			$m = $this->models->Matrix->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'got_names' => 1
					),
					'fieldAsIndex' => 'id',
					'columns' => 'id,got_names,\'matrix\' as type'
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
			
			$_SESSION['app']['user']['matrix']['matrices'] = $m;
			
		}

		return $_SESSION['app']['user']['matrix']['matrices'];
	
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

	private function getTaxaInMatrix()
	{

		if (!isset($_SESSION['app']['user']['matrix']['taxa'][$this->getCurrentMatrixId()])) {

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
						'columns' => 'id,taxon,is_hybrid,rank_id'
					)
				);
	
				
				$t[0]['label'] = $this->formatSpeciesEtcNames($t[0]['taxon'],$t[0]['rank_id']);
				$taxa[] = $t[0];
	
			}

			$this->customSortArray($taxa, array('key' => 'taxon', 'case' => 'i'));

			$_SESSION['app']['user']['matrix']['taxa'][$this->getCurrentMatrixId()] = isset($taxa) ? $taxa : null;
			
		}

		return $_SESSION['app']['user']['matrix']['taxa'][$this->getCurrentMatrixId()];

	}

	private function getMatricesInMatrix()
	{

		$mts = $this->models->MatrixTaxonState->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
					'ref_matrix_id is not' => 'null'
				),
				'columns' => 'distinct ref_matrix_id,\'matrix\' as type'
			)
		);

		foreach((array)$mts as $key => $val) {

			$d = $this->getMatrix($val['ref_matrix_id']);
			
			if (isset($d)) $matrices[$val['ref_matrix_id']] = $d;

		}

		return isset($matrices) ? $matrices : null;

	}

	private function getImageDimensions($state)
	{
	
		if ($state['type']['name']=='media') {

			$f = $_SESSION['app']['project']['urls']['uploadedMedia'].$state['file_name'];
			
			if (!file_exists($f) || is_dir($f)) return null;
			
			$f = getimagesize($f);
			
			if ($f==false) return null;

			return array('w' => $f[0], 'h' => $f[1]);

		} else {
		
			return null;
		
		}
	
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
			$cs[$key]['img_dimensions'] = $this->getImageDimensions($cs[$key]);

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
				),
				'columns' => 'id,characteristic_id,file_name,lower,upper,mean,sd,got_labels',
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

	private function calculateScore($p)
	{

		$states = $p['states'];
		$item = $p['item'];
		$type = $p['type'];
		$incUnknowns = isset($p['incUnknowns']) ? $p['incUnknowns'] : false;

		$item['hits'] = 0;

		// go through all states that the user has chosen
		foreach((array)$states as $sKey => $sVal) {
		
			// format [f (rang or distro)|c (other)]:[charid]:[value]([: + or - times standard dev (distro only)])
			$d = explode(':',$sVal);

			$charId = isset($d[1]) ? $d[1] : null;
			$value = isset($d[2]) ? $d[2] : null;
			
			// if "unknowns" should be included, taxa that have no state for a given character get scored as a hit
			if ($incUnknowns && $type=='taxon' && isset($charId)) {
			
				$mts = $this->models->MatrixTaxonState->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'matrix_id' => $this->getCurrentMatrixId(),
							'characteristic_id' => $charId,
							'taxon_id' => $item['id']
						),
						'columns' => 'count(*) as total'
					)
				);
				
				if ($mts[0]['total']==0) {
				
					$item['hits']++; 
					
					continue;
				
				}
			
			}

			// ranges and distributions have format f:charid:value (for range or distro)[: + or - times standard dev (distro only)]
			if (isset($d[0]) && $d[0]==='f') {

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

					$d = array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $this->getCurrentMatrixId(),
						'state_id' => $cVal['id']
					);

					if ($type=='taxon')
						$d['taxon_id'] = $item['id'];
					else
						$d['ref_matrix_id'] = $item['id'];
					

					$mts = $this->models->MatrixTaxonState->_get(
						array(
							'id' => $d,
							'columns' => 'count(*) as total'
						)
					);
					
					if ($mts[0]['total']>0) $hasState = true;

				}

				if ($hasState) $item['hits']++; 

			} else 
			if (isset($d[0]) && $d[0]==='c') {

				$d = array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
					'state_id' => $value
				);

				if ($type=='taxon')
					$d['taxon_id'] = $item['id'];
				else
					$d['ref_matrix_id'] = $item['id'];
					
				$mts = $this->models->MatrixTaxonState->_get(array('id' => $d));

				if ($mts) $item['hits']++; 

			}

		}

		$item['score'] = round(($item['hits'] / count((array)$states)) * 100);

		return $item;
	
	}

	private function getTaxaScores($states,$incUnknowns=false)
	{

		$taxa = $this->getTaxaInMatrix();
		$mtcs = $this->getMatricesInMatrix();

		if ($states==-1) return array_merge((array)$taxa,(array)$mtcs);

		foreach((array)$taxa as $key => $val)
			$taxa[$key] = $this->calculateScore(
				array(
					'states' => $states,
					'item' => $val,
					'type' => 'taxon',
					'incUnknowns' => $incUnknowns
				)
			);

		$matrices = array();

		foreach((array)$mtcs as $key => $val) {

			$d = $this->calculateScore(
				array(
					'states' => $states,
					'item' => $val,
					'type' => 'matrix'
				)
			);

			$d['type'] = 'matrix';
			$matrices[$key] = $d;

		}
	
		$results = array_merge((array)$taxa,(array)$matrices);
	
		usort($results,array($this,'sortMatrixScores'));

		array_walk($results, create_function('&$v', '$v["score"] = $v["score"]."%";'));	

		return $results;

	}

	private function sortMatrixScores($a,$b)
	{
	
		if ($a['score'] == $b['score']) {
	
			$aa = (isset($a['type']) && $a['type']=='matrix') ? strtolower($a['name']) : strtolower($a['taxon']);
			$bb = (isset($b['type']) && $b['type']=='matrix') ? strtolower($b['name']) : strtolower($b['taxon']);
		
			if ($aa==$bb) return 0;
	
			return ($aa < $bb) ? -1 : 1;
	
		}
	
		return ($a['score'] > $b['score']) ? -1 : 1;
	
	}
	
	private function getCharacteristicHValue($charId,$states=null)
	{
	
		$states = is_null($states) ? $this->getCharacteristicStates($charId) : $states;
		
		$taxa = array();
		
		$tot = 0;

		foreach((array)$states as $key => $val) {
		
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
				)
			);
			
			foreach((array)$mts as $val2) {
			
				@$taxa[$val2['taxon_id']]['states']++;
				$states[$key]['n']++;
				$tot++;
				
			}
		
		}
		
		if ($tot==0) return 0;

		$hValue = 0;

		foreach((array)$states as $val) $hValue += ($val['n']/$tot) * log($val['n']/$tot,10);

		$hValue = is_nan($hValue) ? 0 : -1 * $hValue;
		
		$uniqueTaxa = 0;

		foreach((array)$taxa as $val) if ($val['states']==1) $uniqueTaxa++;
		
		$corrFactor = $uniqueTaxa / $tot;

		return ($hValue * $corrFactor);
		
		/*
		return array(
			'hValue' => $hValue,
			'corrFactor' => $corrFactor,
			'hValueCorrected' => ($hValue * $corrFactor)
		);
		*/

	}

	private function getCharacteristics()
	{

		$mc = $this->models->CharacteristicMatrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId()
				),
				'columns' => 'characteristic_id,show_order',
				'order' => 'show_order'
			)
		);

		foreach((array)$mc as $key => $val) {
		
			$d = $this->getCharacteristic($val['characteristic_id']);

			if ($d) {
				$states = $this->getCharacteristicStates($val['characteristic_id']);
				$d['sort_by']['alphabet'] = strtolower(preg_replace('/[^A-Za-z0-9]/','',$d['label']));
				$d['sort_by']['separationCoefficient'] = -1 * $this->getCharacteristicHValue($val['characteristic_id'],$states); // -1 to avoid asc/desc hassles in JS-sorting
				$d['sort_by']['characterType'] = strtolower(preg_replace('/[^A-Za-z0-9]/','',$d['type']['name']));
				$d['sort_by']['numberOfStates'] = -1 * count((array)$states); // -1 to avoid asc/desc hassles in JS-sorting
				$d['sort_by']['entryOrder'] = intval($val['show_order']);
				$c[] = $d;
			}
			
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

		if (empty($id[0]) || empty($id[1])) return;
	
		$c = $this->getCharacteristics();
		$t1 = $this->getTaxonStates($id[0]);
		$t2 = $this->getTaxonStates($id[1]);

		$both = $neither = $t1_count = $t2_count = 0;

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
		

		$overlap = $states1 = $states2 = array();

		foreach((array)$t1 as $key => $val) {
		
			if (isset($t2[$key]) && $val==$t2[$key]) {
			
				$overlap[] = $val;
			
			} else {
			
				$states1[] = $val;
			
			}
		
		}
		
		foreach((array)$t2 as $key => $val) {
		
			if (!isset($t1[$key]) || $val!=$t1[$key]) $states2[] = $val;

		}

		//q($overlap,1);

		return array(
			'taxon_1' => $this->getTaxonById($id[0]),
			'taxon_2' => $this->getTaxonById($id[1]),
			'count_1' => $t1_count,
			'count_2' => $t2_count,
			'neither' => $neither,
			'both' => $both,
			'total' => count((array)$c),
			'coefficients' => $this->calculateDistances($t1_count,$t2_count,$both,$neither),
			'taxon_states_1' => $states1,
			'taxon_states_2' => $states2,
			'taxon_states_overlap' => $overlap
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

	private function stateMemoryStore($data) 
	{

		if ($data=='-1') {
			unset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()]);
		} else {
			$_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()] = $data;
		}

	}

	private function stateMemoryRecall() 
	{

		if (isset($_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()])) {

			foreach((array)$_SESSION['app']['user']['matrix']['storedStates'][$this->getCurrentMatrixId()] as $key => $val) {
			
				$states[$key]['val'] = $val;
			
				$d = explode(':',$val);
				
				if ($d[0]=='c' && isset($d[2])) {
				
					$d = $this->getCharacteristicState($d[2]);

					$states[$key]['type'] = 'c';
					$states[$key]['id'] = $d['id'];
					$states[$key]['characteristic_id'] = $d['characteristic_id'];
					$states[$key]['label'] = $d['label'];
				
				}
			
			}

			return $states;

		} else {
		
			return null;

		}

	}

}