<?php

include_once ('Controller.php');

class KeyController extends Controller
{

    private $_taxaStepList;
	private $_choiceList = array();
	public $currentKeyStepId;

    public $usedModels = array(
		'keystep',
		'keytree',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
    );
    
    public $controllerPublicName = 'Dichotomous key';
    public $controllerBaseName = 'key';
    
	public $cssToLoad = array(
		'key.css',
	); //'key-tree.css'
	
	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'key.js',
			    'prettyPhoto/jquery.prettyPhoto.js',
				'dialog/jquery.modaldialog.js',
				'lookup.js'
			),
			'IE' => array(
			)
		);


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {

        parent::__construct($p);
		
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

    	$q = $this->getKeyTree();
    	
        $this->setPageName( $this->translate('Index'));
		
		// set the stored key tree (= compact hierarchical representation of the entire key)
		$this->getKeyTree();

		// get user's decision path
		$keyPath = $this->getKeyPath();

		/*
			if user directly access a specific step or choice while there is no keypath (thru bookmark),
			a possible decision path is created from the key tree
			caveat: in current set up, if a user does this while there *is* a keypath, this will do nothing. use forcetree=1 to force
		*/
		if ((is_null($keyPath) && ($this->rHasVal('choice') || $this->rHasVal('step'))) || ($this->rHasVal('forcetree','1') || $this->rHasVal('r'))) {

			$this->createPathInstantly(
				($this->rHasVal('step') ? $this->requestData['step'] : null),
				($this->rHasVal('choice') ? $this->requestData['choice'] : null)
			);
							
		}

		// step points at a specific step, from keypath
		if ($this->rHasVal('step')) {

			$step = $this->getKeystep($this->requestData['step']);

			$this->updateKeyPath(array('step' => $step,'fromPath' => true));

		} else
			
		// choice is choice clicked by user
		if ($this->rHasVal('choice')) {

			$choice = $this->getKeystepChoice($this->requestData['choice']);

			// choice points to a taxon
			if (!empty($choice['res_taxon_id'])) {

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

			} 
			// choice points to a next step
			else {
  
				$step = $this->getKeystep($choice['res_keystep_id']);

				$this->updateKeyPath(array('step' => $step,'choice' => $choice));

			}

		} else
		// restore previous state
		if (!$this->rHasVal('start','1') && !empty($keyPath)) {

			$d = array_pop($keyPath);
			$step = $this->getKeystep($d['id']);

		} 
		// no step or choice specified, must be the start of the key
		else {

			$this->resetKeyPath();

			$step = $this->getKeystep($this->getStartKeystepId());

			$this->updateKeyPath(array('step' => $step));

		} 

		$taxa = $this->getTaxonDivision($step['id']);

        $this->smarty->assign('keyPathMaxItems', $this->controllerSettings['keyPathMaxItems']);
		
		$this->smarty->assign('keyType',$this->getSetting('keytype'));
		
		$this->smarty->assign('taxaState',$this->getTaxaState());

		$this->smarty->assign('remaining',$taxa['remaining']);

		$this->smarty->assign('excluded',$taxa['excluded']);

		$this->setPageName(sprintf($this->translate('Dichotomous key: step %s: "%s"'),$step['number'],$step['title']));
		
		$this->setCurrentKeyStepId($step['id']);
 
		//unset($_SESSION['app']['user']['search']['hasSearchResults']);

		// get step's choices
		if (isset($step)) $choices = $this->getKeystepChoices($step['id']);

		if (isset($step)) $this->smarty->assign('step',$step);

		if (isset($choices)) $this->smarty->assign('choices',$choices);

		$this->smarty->assign('keypath',$this->getKeyPath());
		
		$this->printPage($this->setStepType($choices));

    }
    
   
    private function setStepType ($choices)
    {
        
        $type = 'index_l2_pct';
        
        foreach ($choices as $choice) {
            
            if (empty($choice['choice_image_params'])) {
                
                $type = 'index_l2_txt';
                break;
            }
            
        }
        
        if ($type == 'index_l2_pct') return $type;
        
        if (count($choices) > 4) return null;
        
        foreach ($choices as $choice) {
            
            if (!empty($choice['choice_img'])) {
                
                $type = null;
                break;
            }
            
        }
        
        return $type;
    }

    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list')) {

            $this->getLookupList();

        }

        if ($this->rHasVal('action','store_remaining')) {
        
        	$this->setTaxaState('remaining');
        
        }
        
        if ($this->rHasVal('action','store_excluded')) {
        
        	$this->setTaxaState('excluded');
        
        }

        $this->smarty->assign('keyPathMaxItems', $this->controllerSettings['keyPathMaxItems']);
		
		$this->smarty->assign('keyType',$this->getSetting('keytype'));
        
        $this->printPage();
    
    }

	/* function exists sole for the benefit of the preview overlay's "back to editing"-button */
	public function getCurrentKeyStepId()
	{
	
		return $this->currentKeyStepId;
	
	}

	/* it's in the trees (it's coming this way!) */
	public function getKeyTree()
	{

		$tree = $this->getCache('tree-keyTree');
		$this->_choiceList = $this->getCache('tree-choiceList');
		
		if (!$tree || !$this->_choiceList) {

			$tree = $this->setKeyTree();
			$this->saveCache('tree-keyTree', $tree);
			$this->saveCache('tree-choiceList', $this->_choiceList);

		}
		
		return $tree;

	}
	
	private function setKeyTree()
	{

		// get stored tree from database
		$kt = $this->models->Keytree->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'order' => 'chunk'
			)
		);
		
		// if it doesn't exist, generate it anew (shouldn't happen!)
		if (empty($kt[0]['keytree'])) {

			$d = $this->generateKeyTree();
		
		}
		// store tree in session
		else {

			$tree = $choiceList = '';
			
			foreach((array)$kt as $val) {
			
				if ($val['chunk']!=999)
					$tree .= trim($val['keytree']);
				else
					$choiceList = $val['keytree'];
			
			}
			
			$this->_choiceList = unserialize(utf8_decode($choiceList));
			$d = unserialize(utf8_decode($tree));

		}
		
		return $d;
		
	}
	
	private function _createPathInstantly($stepId=null,$choiceId=null)
	{

		// choice ID present: probably a bookmarked step in the key
		if (!is_null($choiceId)) {

			// get choice requested
			$d = $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $choiceId
					)
				)
			);

			/*
				the url of a specific step always contains the choice that leads to that step.
				the line further down:
					$step = $this->getKeystep($d[0]['keystep_id']);
				will therefore resolve to the parent step of the choice that lead to the page
				the user is looking at, i.e. the previous one. as the current step - the step
				the user is looking at - also needs to be included in the keypath, the step 
				that *follows* from the choice in the url is also added to the keypath. as this
				function works backward, the adding of the last - current - step is the very
				first action, done when the keypath is still empty.
			*/
			if (count((array)$this->tmp['results'])==0) {

				$step = $this->getKeystep($d[0]['res_keystep_id']);
				
				array_push(
					$this->tmp['results'],
					array(
						'id' => $step['id'],
						'step_number' => $step['number'],
						'step_title' => $step['title'],
						'is_start' => $step['is_start'],
						'choice_marker' => null,
						'choice_txt' => null
					)
				);

			}

			// get the choice's parent step
			$step = $this->getKeystep($d[0]['keystep_id']);

			// find the choice that lead to this step, i.e., the previous step in the path
			$prevChoice = $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'res_keystep_id' => $d[0]['keystep_id']
					)
				)
			);
			
			$choice = $this->getKeystepChoice($choiceId);
			$choiceMarker = $this->showOrderToMarker($d[0]['show_order']);

			// add step & choice to the path
			array_unshift(
				$this->tmp['results'],
				array(
					'id' => $step['id'],
					'step_number' => $step['number'],
					'step_title' => $step['title'],
					'is_start' => $step['is_start'],
					'choice_marker' => $choiceMarker,
					'choice_txt' => $this->formatPathChoice($choice, $step['number'], $choiceMarker)
				)
			);
			
		} else
		// no choice, just a step ID defined: most likely a link in the text migrated from L2
		if (!is_null($stepId)) {

			// get current step
			$step = $this->getKeystep($stepId);
			
			// add step to the path
			array_unshift(
				$this->tmp['results'],
				array(
					'id' => $step['id'],
					'step_number' => $step['number'],
					'step_title' => $step['title'],
					'is_start' => $step['is_start'],
					'choice_marker' => null,
					'choice_txt' => null
				)
			);

			// find the choice that lead to this step, i.e., the previous step in the path
			$prevChoice = $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'res_keystep_id' => $step['id']
					)
				)
			);
			
							
		} else {

			return null;
		
		}

		// and iterate to the previous choice
		$this->_createPathInstantly(null,$prevChoice[0]['id']);

	}
		
	private function createPathInstantly($step=null,$choice=null)
	{

		$this->tmp = array();
		$this->tmp['results'] = array();

		$this->_createPathInstantly(
			($this->rHasVal('step') ? $this->requestData['step'] : null),
			($this->rHasVal('choice') ? $this->requestData['choice'] : null)
		);

		$this->setKeyPath($this->tmp['results']);

	}

	
	// be aware that this function also exists in the app controller and should have identical output there!
	private function generateKeyTree($id=null,$level=0)
	{

		if (is_null($id)) {

			$id = $this->getStartKeystepId();
			
		}
		
		$d = $this->getKeystep($id);
		
		$step = 
			array(
				'id' => $d['id'],
				'number' => $d['number'],
				'title' => utf8_decode($d['title']),
				'is_start' => $d['is_start'],
				'level' => $level
			);		

		$step['choices'] = $this->getKeystepChoices($id,null,false);
  
		foreach((array)$step['choices'] as $key => $val) {
			
			$this->_choiceList[] = $val['id'];
		
			$d['choice_id'] = $val['id'];
			$d['choice_marker'] = utf8_decode($val['marker']);
			$d['res_keystep_id'] = $val['res_keystep_id'];
			$d['res_taxon_id'] = $val['res_taxon_id'];

			$step['choices'][$key] = $d;

			if ($val['res_keystep_id']) $step['choices'][$key]['step'] = $this->generateKeyTree($val['res_keystep_id'],($level+1));

		}
		
		return isset($step) ? $step : null;
	
	}


	/* steps and choices */
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
		//$step['content'] = $this->matchHotwords($step['content']);

		return $step;

	}

	private function setCurrentKeyStepId($id)
	{
	
		$this->currentKeyStepId = $id;
	
	}
		
	public function getStartKeystepId()
	{

		$k = $this->models->Keystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'is_start' => 1
				)
			)
		);
		
		if ($k) return $k[0]['id'];
	
	}

	private function getKeystepChoices($step,$choice=null,$includeContent=true)
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
			
			if ($includeContent) {
			
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
				
	
				if (isset($cck[0]['title'])) $choices[$key]['title'] = trim($cck[0]['title']);
				
				if (isset($cck[0]['choice_txt'])) {
	
					$choices[$key]['choice_txt'] = $cck[0]['choice_txt'];
					$choices[$key]['choice_txt'] = $this->matchGlossaryTerms(trim($choices[$key]['choice_txt']));
					$choices[$key]['choice_txt'] = $this->matchHotwords($choices[$key]['choice_txt']);
	
				}
				
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
			
				if ($includeContent) {

					$t = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));
	
					if (isset($t['taxon'])) {
	
						$choices[$key]['target'] = $this->formatTaxon($t);
						$choices[$key]['is_hybrid'] = $t['is_hybrid'];
	
					}
					
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

	
	/* the path */
	private function resetKeyPath()
	{
	
		unset($_SESSION['app']['user']['key']['path']);

	}

	private function getKeyPath()
	{
	
		return isset($_SESSION['app']['user']['key']['path']) ? $_SESSION['app']['user']['key']['path'] : null;
	
	}

	private function setKeyPath($path)
	{
	
		$this->resetKeyPath();
	
		$_SESSION['app']['user']['key']['path'] = $path;
	
	}

	private function updateKeyPath($params) 
	{
		$step = $params['step'];
		$choice = isset($params['choice']) ? $params['choice'] : null;
		$fromPath = isset($params['fromPath']) ? $params['fromPath'] : null;
		$taxon = isset($params['taxon']) ? $params['taxon'] : null;

		if (!isset($_SESSION['app']['user']['key']['path'])) {

			//$this->setStoredKeypath($step);

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
			$d[count((array)$d)-2]['choice_txt'] = $this->formatPathChoice(
				$choice, 
				$d[count((array)$d)-2]['step_number'], 
				$d[count((array)$d)-2]['choice_marker']
			);
				
		}

		$_SESSION['app']['user']['key']['path'] = $d;

	}


	/* branches and fruits */
	public function getTaxonDivision($step)
	{

		$div = $this->getCache('key-taxonDivision-'.$step);
		
if (1==1 || !$div) {

			$this->tmp = null;

			$this->sawOffABranch($this->getKeyTree(),$step);

			$this->reapFruits($this->tmp['branch']);
	
			$excludedTaxa = array();
		
			$allTaxa = $this->getAllTaxaInKey();
			
			foreach((array)$allTaxa as $val) {
			
				if (!isset($this->tmp['remaining'][$val['res_taxon_id']]))
					$excludedTaxa[$val['res_taxon_id']] = null;

			}
	
			$div = 
				array(
					'remaining' => (isset($this->tmp['remaining']) ? $this->tmp['remaining'] : null),
					'excluded' => $excludedTaxa
				);
				
			$this->saveCache('key-taxonDivision-'.$step, $div);
				
		}
		
		$includedTaxa = $excludedTaxa = array();

		$tree = $this->getTreeList(array('includeEmpty'=>true));

		foreach((array)$div['remaining'] as $key => $val) {

			$d = $tree[$key];

			$includedTaxa[$val] = 
				array(
					'id' => $d['id'],
					'taxon' => $this->formatTaxon($d),
					'is_hybrid' => $d['is_hybrid']
				);
		
		}

		foreach((array)$div['excluded'] as $key => $val) {

			$d = $tree[$key];

			$excludedTaxa[$key] = 
				array(
					'id' => $d['id'],
					'taxon' => $this->formatTaxon($d),
					'is_hybrid' => $d['is_hybrid']
				);
		
		}
		$this->customSortArray($includedTaxa,array('key' => 'taxon'));

		$this->customSortArray($excludedTaxa,array('key' => 'taxon'));
		
		return array(
				'remaining' => $includedTaxa,
				'excluded' => $excludedTaxa
			);

	}

	/*
		this gives the same output as getTaxonDivision(), but stores full taxon information in the cache, rather
		than just ID's, as getTaxonDivision() does. because the division for *each* step is cached, the size of 
		the cache will be considerably greater with this function (in heukels flora, the cache file sizes for the 
		first step for each function was 246K and 43K, respectively). potential downside is that the new function
		has to retrieve, and sort, full taxon data evertime it is called. nevertheless, it appears to be slightly 
		faster than this one. go figure.
	*/
	public function originalGetTaxonDivision($step)
	{

		$div = $this->getCache('key-taxonDivision-'.$step);
		
		if (!$div) {

			$this->tmp = null;

			$this->sawOffABranch($this->getKeyTree(),$step);

			$this->reapFruits($this->tmp['branch']);
	
			$includedTaxa = $excludedTaxa = array();
		
			$allTaxa = $this->getAllTaxaInKey();
			
			$tree = $this->getTreeList();
	
			foreach((array)$allTaxa as $val) {
			
				if (!isset($this->tmp['remaining'][$val['res_taxon_id']])) {
	
					$d = $tree[$val['res_taxon_id']];
	
					$excludedTaxa[$val['res_taxon_id']] = 
						array(
							'id' => $d['id'],
							'taxon' => $this->formatTaxon($d),
							'is_hybrid' => $d['is_hybrid']
						);
				}
			
			}
	
			foreach((array)$this->tmp['remaining'] as $val) {
	
				$d = $tree[$val];
	
				$includedTaxa[$val] = 
					array(
						'id' => $d['id'],
						'taxon' => $this->formatTaxon($d),
						'is_hybrid' => $d['is_hybrid']
					);
			
			}
	
			$this->customSortArray($includedTaxa,array('key' => 'taxon'));

			$this->customSortArray($excludedTaxa,array('key' => 'taxon'));

			$div = 
				array(
					'remaining' => $includedTaxa,
					'excluded' => $excludedTaxa
				);
				
			$this->saveCache('key-taxonDivision-'.$step, $div);
				
		}
		
		return $div;

	}

	private function sawOffABranch($branch,$step)
	{
	
		if (isset($branch['id']) && $branch['id']==$step) {

			$this->tmp['branch'] = $branch;
			return;

		}

		foreach((array)$branch['choices'] as $val) {
		
			if (isset($val['step'])) $this->sawOffABranch($val['step'],$step);
		
		}
	
	}
	
	private function reapFruits($branch)
	{

		foreach((array)$branch['choices'] as $val) {
		
			if (isset($val['res_taxon_id'])) $this->tmp['remaining'][$val['res_taxon_id']] = $val['res_taxon_id'];

			if (isset($val['step'])) $this->reapFruits($val['step']);
		
		}
	
	}


	public function getAllTaxaInKey()
	{

		if (!$this->getCache('key-keyTaxa')) {
	
			 $d = $this->models->ChoiceKeystep->_get(
					array('id' =>
							array(
									'project_id' => $this->getCurrentProjectId(),
									'res_taxon_id is not' => 'null',
									'id in' => '('.implode(',',(array)$this->_choiceList).')'
							),
							'columns' => 'res_taxon_id'
					)
			);

			 $this->saveCache('key-keyTaxa', $d);
			 
			 return $d;
	
		}
	
		return $this->getCache('key-keyTaxa');
	
	}
	
	/* the rest */
	private function getKeytype()
	{

		return $this->getSetting('keytype');
	
	}
	
	private function choicesHaveL2Attributes($choices)
	{

		foreach((array)$choices as $val) if ($val['choice_image_params']!='') return true;

		return false;	
	
	}
	
	private  function reapSteps($branch)
	{

		$this->tmp['results'][(int)$branch['number']] =
			array(
				'id' => $branch['id'],
				//'label' => $branch['number'].'. '.$branch['title'],
				'label' => $this->translate('Step').' '.$branch['number'].(!empty($branch['title']) && $branch['title']!=$branch['number'] ? ': '.$branch['title'] : ''),
				'number' => (int)$branch['number'],
			);

		foreach((array)$branch['choices'] as $val) {

			if (isset($val['step'])) $this->reapSteps($val['step']);
		
		}
	
	}

	private function getLookupList()
	{

		$this->tmp = array();
		$this->tmp['found'] = false;
		$this->tmp['excluded'] = array();
		$this->tmp['results'] = array();

		// ploughs the entire key
		$this->reapSteps($this->getKeyTree());

		$this->customSortArray($this->tmp['results'],array('key' => 'number'));

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$this->tmp['results'],
				'key',
				'index.php?forcetree=1&step=%s',
				false,
				true
			)
		);	
		
	}
		
	private function setTaxaState ($state)
	{
		$_SESSION['app']['user']['key']['taxaState'] = $state;
	}
	
	private function getTaxaState ()
	{
		return isset($_SESSION['app']['user']['key']['taxaState']) ? $_SESSION['app']['user']['key']['taxaState'] :
			'remaining';
	}
	
	private function formatPathChoice ($choice, $step = null, $choiceMarker = null)
	{
		if (!isset($choice['choice_txt'])) return;
		$remove = $step . $choiceMarker . '. ';
		$toSpace = array('<br />', '<br>');
		return str_replace($remove, '', strip_tags(str_replace($toSpace, ' ', $choice['choice_txt'])));
	}
	
}
