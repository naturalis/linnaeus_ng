<?php

include_once ('Controller.php');

class KeyController extends Controller
{

    private $_taxaStepList;
	private $_stepList = array();
	public $currentKeyStepId;

    public $usedModels = array(
		'keystep',
		'keymap',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
    );
    
    public $controllerPublicName = 'Dichotomous key';

	public $cssToLoad = array(
		'basics.css',
		'key.css',
		'colorbox/colorbox.css',
		'dialog/jquery.modaldialog.css'
	); //'key-tree.css'

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'key.js',
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

        $this->smarty->assign('keyPathMaxItems', $this->controllerSettings['keyPathMaxItems']);
		
		$this->smarty->assign('keyType',$this->getSetting('keytype'));
		
		//$this->setStoredChoiceList();
		//unset($_SESSION['app']['user']['key']['path']);

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

    /**
     * Main procedure for key
     *
     * @access     public
     */
    public function indexAction()
    {

        $this->setPageName( _('Index'));

		if ($this->rHasVal('step')) {
		// step points at a specific step, from keypath

			$step = $this->getKeystep($this->requestData['step']);

			$this->updateKeyPath(array('step' => $step,'fromPath' => true));

		} else
		if ($this->rHasVal('choice')) {
		// choice is choice clicked by user

			$choice = $this->getKeystepChoice($this->requestData['choice']);

			if (!empty($choice['res_taxon_id'])) {
			// choice points to a taxon

				$this->updateKeyPath(
					array(
						'step' => $step,
						'taxon' => array(
							'id' => $choice['res_taxon_id'],
							'target' => $choice['target']
						)
					)
				);

				$this->redirect('../taxon/taxon.id?id='.$choice['res_taxon_id']);

			} else {
			// choice points to a next step
  
				$step = $this->getKeystep($choice['res_keystep_id']);

				$this->updateKeyPath(array('step' => $step,'choice' => $choice));

			}

		} else {
		// no step or choice specified, must be the start of the key

			$this->resetKeyPath();

			$step = $this->getStartKeystep();

			$this->updateKeyPath(array('step' => $step));

		}

		$taxa = $this->getTaxonDivision();

		$this->setPageName(sprintf(_('Dichotomous key: step %s: "%s"'),$step['number'],$step['title']));
		
		$this->setCurrentKeyStepId($step['id']);

		unset($_SESSION['app']['user']['search']['hasSearchResults']);

		// get step's choices
		if (isset($step)) $choices = $this->getKeystepChoices($step['id']);

		if (isset($step)) $this->smarty->assign('step',$step);

		if (isset($choices)) $this->smarty->assign('choices',$choices);

		if (isset($taxa['list'][$step['id']])) $this->smarty->assign('taxa',$taxa['list'][$step['id']]);
		
		$this->smarty->assign('keypath',$this->getKeyPath());

		if (isset($choices) && $this->choicesHaveL2Attributes($choices)) 
	        $this->printPage('index_l2');
		else
	        $this->printPage();
    
    }

	/* function exists sole for the benefit of the preview overlay's "back to editing"-button */
	public function getCurrentKeyStepId()
	{
	
		return $this->currentKeyStepId;
	
	}
	
	private function choicesHaveL2Attributes($choices)
	{

		foreach((array)$choices as $val) if ($val['choice_image_params']!='') return true;

		return false;	
	
	}
		
	private function setCurrentKeyStepId($id)
	{
	
		$this->currentKeyStepId = $id;
	
	}
		
	private function getKeystep($id)
	{

        if (empty($id))  return;

		$k = $this->models->Keystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'id' => $id, 
					)
			)
		);

		if (!$k) return;

		$step = $k[0];

		$ck = $this->models->ContentKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'keystep_id' => $step['id'], 
					'language_id' => $this->getCurrentLanguageId()
					),
				'columns' => 'title,content'
			)
		);
					
		$step['title'] = $ck[0]['title'];

		$step['content'] = $this->matchGlossaryTerms($ck[0]['content']);
		$step['content'] = $this->matchHotwords($step['content']);

		return $step;

	}

	private function getStartKeystep()
	{

		$k = $this->models->Keystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'is_start' => 1
				)
			)
		);
		
		if ($k) return $this->getKeystep($k[0]['id']);
	
	}

	private function getKeystepChoices($step,$choice=null)
	{

		if ($choice == null) {

			// get all choices available for this keystep
			$choices =  $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'keystep_id' => $step
					),
					'order' => 'show_order'
				)
			);
			
		} else {

			// get single choice
			$choices =  $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'id' => $choice,
						'project_id' => $this->getCurrentProjectId(),
					)
				)
			);

		}
		
		foreach((array)$choices as $key => $val) {
			
			// get the actual language-sensitive content for each choice
			$cck = $this->models->ChoiceContentKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'choice_id' => $val['id'], 
						'language_id' => $this->getCurrentLanguageId()
						),
					'columns' => 'choice_txt'
				)
			);

			if (isset($cck[0]['title'])) $choices[$key]['title'] = $cck[0]['title'];
			
			if (isset($cck[0]['choice_txt'])) {

				$choices[$key]['choice_txt'] = $this->matchGlossaryTerms($cck[0]['choice_txt']);
				$choices[$key]['choice_txt'] = $this->matchHotwords($choices[$key]['choice_txt']);

			}

			// resolve the targets to either a next step or a taxon
			if (!empty($val['res_keystep_id']) && $val['res_keystep_id']!=0) {
			
				if ($val['res_keystep_id']=='-1') {
				//unfinished target (shouldn't happen)

					$choices[$key]['target'] = null;

				} else {
				// target is a next step

					$k = $this->getKeystep($val['res_keystep_id']);

					if (isset($k['title'])) $choices[$key]['target'] = $k['title'];

					if (isset($k['number'])) $choices[$key]['target_number'] = $k['number'];

				}
			
			} elseif (!empty($val['res_taxon_id'])) {
			// target is a taxon

				$t = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));

				if (isset($t['taxon'])) {

					$choices[$key]['target'] = $t['taxon'];
					$choices[$key]['is_hybrid'] = $t['is_hybrid'];

				}

			} else {

				$choices[$key]['target'] = null;

			}
			
			$choices[$key]['marker'] = $this->showOrderToMarker($val['show_order']);
			
			if ($val['choice_image_params']!='') {

				foreach((array)json_decode($val['choice_image_params']) as $pKey => $pVal) $params[$pKey] = (string)$pVal;
				$choices[$key]['choice_image_params'] = $params;
				unset($params);

			}

		}

		return $choice == null ? $choices : $choices[0];

	}

	private function getKeystepChoice($id)
	{
	
		return $this->getKeystepChoices(null,$id);
	
	}

	private function getTaxonDivision($forceLookup=false)
	{

		if(!isset($_SESSION['app']['user']['key']['taxonDivision']) || $forceLookup) {

			// get all choices that have a taxon as result
			$ck = $this->models->ChoiceKeystep->_get(
				array('id' => 
					array(
						'project_id' => $this->getCurrentProjectId(),
						'res_taxon_id is not' => 'null'
					)
				)
			);
			
			if(!isset($_SESSION['app']['user']['key']['choiceKeysteps']) || $forceLookup) {
			
				unset($_SESSION['app']['user']['key']['choiceKeysteps']);
	
				$d = $this->models->ChoiceKeystep->_get(
					array('id' => 
						array(
							'project_id' => $this->getCurrentProjectId()
						)
					)
				);
			
				foreach((array)$d as $val) $_SESSION['app']['user']['key']['choiceKeysteps'][$val['res_keystep_id']][] = $val;

			}
			

			// for each...
			foreach((array)$ck as $key => $val) {
	
				unset($this->_taxaStepList);

				// ...work our way back to the top-most step...
				$this->setStepsPerTaxon($val);

				/// ...and save the results
				$results[] =  array(
					'taxon_id' => $val['res_taxon_id'],
					'steps' => $this->_taxaStepList
				);

			}

			$taxa = $this->models->Taxon->_get(array('id'=>array('project_id'=>$this->getCurrentProjectId()),'fieldAsIndex' => 'id'));

			if (isset($results)) {
	
				// turn it from a list of taxa with their steps into a list of steps with their taxa
				foreach((array)$results as $key => $val) {

					foreach($val['steps'] as $key2 => $stepId) {

						if (!isset($d[$stepId][$val['taxon_id']])) {

							$d[$stepId][$val['taxon_id']] = true;
		
							$list[$stepId][] = $taxa[$val['taxon_id']];

						}
		
					}
					
				}

			}

			// sort by taxon name
			foreach((array)$list as $key => $val) {

				$this->customSortArray($val,array('key' => 'taxon'));
				$d[$key] = $val;

			}
			
			$list = $d;

			$_SESSION['app']['user']['key']['taxonDivision'] = array(
				'list' => isset($list) ? $list : null,
				'taxonCount' => isset($ck) ? count($ck) : 0
			);
		
		}

		return $_SESSION['app']['user']['key']['taxonDivision'];

	}

	private function setStepsPerTaxon($choice)
	{

		$this->_taxaStepList[] = $choice['keystep_id'];

		$cks = 
			isset($_SESSION['app']['user']['key']['choiceKeysteps'][$choice['keystep_id']]) ? 
				$_SESSION['app']['user']['key']['choiceKeysteps'][$choice['keystep_id']] : 
				null;

		/*	
		// get choices that have the keystep the choice belongs to as target
		$cks = $this->models->ChoiceKeystep->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'res_keystep_id' => $choice['keystep_id']
				)
			)
		);
		*/
		
		foreach((array)$cks as $key => $val) $this->setStepsPerTaxon($val);
	
	}

	private function resetKeyPath()
	{
	
		unset($_SESSION['app']['user']['key']['path']);

	}

	private function getKeyPath()
	{
	
		return isset($_SESSION['app']['user']['key']['path']) ? $_SESSION['app']['user']['key']['path'] : null;
	
	}

	private function updateKeyPath($params) 
	{

		$step = $params['step'];
		$choice = isset($params['choice']) ? $params['choice'] : null;
		$fromPath = isset($params['fromPath']) ? $params['fromPath'] : null;
		$taxon = isset($params['taxon']) ? $params['taxon'] : null;

		if (!isset($_SESSION['app']['user']['key']['path'])) {

			$this->setStoredKeypath($step);

		}


		if (isset($_SESSION['app']['user']['key']['path'])) {
		// keypath already exists...

			if ($fromPath) {
			// ...user clicked somewhere in the path, so we copy the existing path up to the step he clicked

				foreach((array)$_SESSION['app']['user']['key']['path'] as $key => $val) {
	
					if ($val['id']==$step['id']) break;
	
					if (!empty($val['id'])) $d[] = $val;
	
				}

			} else {
			// user clicked a choice, existing path remains as it is
			
				$d = $_SESSION['app']['user']['key']['path'];
			
			}

		}

		if (!isset($d) || (isset($d) && $d[count((array)$d)-1]['id']!=$step['id'])) {
		// if we have no path, or have a path whose previous step is not the same as the current one, we add the current step

			$d[] = array(
				'id' => $step['id'],
				'step_number' => $step['number'],
				'step_title' => $step['title'],
				'is_start' => $step['is_start'],
				'choice_marker' => null,
			);

		}


		if (!empty($choice) && (count((array)$d)>1)) {
		// the choice clicked to reach the current step belongs to the previous step, and ahs to be added there

			$d[count((array)$d)-2]['choice_marker'] = $choice['marker'];

		}

		$_SESSION['app']['user']['key']['path'] = $d;

	}

	private function setStoredChoiceList()
	{
	
		if(!isset($_SESSION['app']['user']['key']['choiceKeysteps'])) {

			$_SESSION['app']['user']['key']['choiceKeysteps'] = $this->getStoredChoiceList();

		}
	
	}

	private function getStoredChoiceList()
	{
	
		$d = $this->models->Keymap->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'choicelist'
			)
		);
		
		return !empty($d[0]['choicelist']) ? unserialize($d[0]['choicelist']) : null;
	
	}
	
	private function cDva($list,$id)
	{

		foreach((array)$list as $choices) {
	
			foreach((array)$choices as $key => $val) {
	
				if ($val['res_keystep_id']==$id) {
				
					$this->_stepList[$id] = $val;

					$this->cDva($list,$val['keystep_id']);					
				
				}
		
			}
	
		}
		
	}
	
	private function setStoredKeypath($step)
	{
return;
unset($this->_stepList);
$this->cDva($this->getStoredChoiceList(),$step['id']);



foreach(array_reverse((array)$this->_stepList) as $key => $val) {

	$d[] = array(
		'id' => $val['keystep_id'],
		'step_number' => $val['keystep_id'],
		'step_title' => $val['step_title'][$this->getCurrentLanguageId()]['title'],
		'is_start' => isset($val['is_start']) ? $val['is_start'] : null,
		'choice_marker' => $val['marker'],
	);


}

q($d);

die();


//q($_SESSION['app']['user']['key']['path']);

		$step = $params['step'];
		$choice = isset($params['choice']) ? $params['choice'] : null;
		$fromPath = isset($params['fromPath']) ? $params['fromPath'] : null;
		$taxon = isset($params['taxon']) ? $params['taxon'] : null;
		
		foreach((array)$_SESSION['app']['user']['key']['choiceKeysteps'] as $val) {
		
		}

			$d[] = array(
				'id' => $step['id'],
				'step_number' => $step['number'],
				'step_title' => $step['title'],
				'is_start' => $step['is_start'],
				'choice_marker' => null,
			);


	}

}
