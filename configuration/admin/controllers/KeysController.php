<?php

/*

	purge and limit undo!

*/


include_once ('Controller.php');

class KeysController extends Controller
{
    
    private $_remainingTaxaList;
    private $_taxaStepList;
	private $_stepList = array();
	private $_counter = 0;

    public $usedModels = array(
		'keystep',
		'content_keystep',
		'content_keystep_undo',
		'choice_keystep',
		'choice_content_keystep',
		'choice_content_keystep_undo', 
    );
    
    public $usedHelpers = array(
		'file_upload_helper'
    );

    public $controllerPublicName = 'Dichotomous key';

	public $cssToLoad = array('key.css','rank-list.css','key-tree.css','colorbox/colorbox.css');

	public $jsToLoad =
		array(
			'all' => array('key.js','jit/jit.js','jit/key-tree.js','colorbox/jquery.colorbox.js'),
			'IE' => array('jit/Extras/excanvas.js')
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

    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

		unset($_SESSION['system']['keyPath']);
       
        $this->printPage();
    
    }

    public function stepShowAction()
    {

        $this->checkAuthorisation();

		if ($this->rHasVal('node')) {
		// arrived from key map; have to resolve the keypath from the node's place in the keyTree

			if ($_SESSION['system']['keyTree']) {
			// the keyTree is built when the map is called; if no keyTree exists, redirect to the map

				// find the node in the keyTree and build an array of the path leading to it
				$this->findNodeInTree(array(0 => $_SESSION['system']['keyTree']),$this->requestData['node']);
				
				// loop through the array and add each element to the keyPath
				foreach((array)$this->_stepList as $key => $val) {

					$this->updateKeyPath($val);

				}
					
				// get the step itself, which always is the last element in the keyPath array
				$step = $this->getKeystep($val['id']);

			} else {
			
				$this->redirect('map.php');
			
			}

		} else
		if ($this->rHasId()) {
		// request for specific step

			$step = $this->getKeystep($this->requestData['id']);

		} else {
		// looking for the start step 

			$step = $this->getStartKeystep();
			
			if (!$step) {
			// didn't find it, create it

				$this->redirect('step_edit.php?id='.$this->createNewKeystep(array('is_start'=>1)));

			}

		}
		
		if ($step) {

			if ($this->rHasVal('move') && $this->rHasVal('direction') && !$this->isFormResubmit()) {
			// move choices up and down
	
				$this->moveKeystepChoice($this->requestData['move'],$this->requestData['direction']);
	
			}
	
			// get step's choices
			$choices = $this->getKeystepChoices($step['id'],true);
			
			// update the key's breadcrumb trail
			$this->updateKeyPath(
				array(
					'id' => $step['id'],
					'number' => $step['number'],
					'title' => $step['title'],
					'is_start' => $step['is_start'],
					'choice' => isset($this->requestData['choice']) ? $this->requestData['choice'] : null
				)
			);

			$step['content'] = nl2br($step['content']);

			$this->smarty->assign('step',$step);
	
			$this->smarty->assign('choices',$choices);
	
			$this->smarty->assign('maxChoicesPerKeystep',$this->controllerSettings['maxChoicesPerKeystep']);

		} else {
		
			$this->addError(_('Non-existant keystep ID. Please go back and change the target for the choice.'));

		}

		$this->setPageName(sprintf(_('Show key step "%s"'),$step['title']));

		if (isset($_SESSION['system']['keyTaxaPerStep'][$step['id']])) {

			$this->smarty->assign('remainingTaxa',$_SESSION['system']['keyTaxaPerStep'][$step['id']]);

		}

		$this->smarty->assign('keyPath',$this->getKeyPath());
	
		$this->printPage();

	}

    public function stepEditAction()
    {

        $this->checkAuthorisation();

		if (!$this->rHasId()) {
		// create a new step when no id is specified
		
			$id = $this->createNewKeystep();

			if ($this->rHasVal('ref_choice')) {
			// url was called from the 'new step' option of a choice: set the new referring step id

				$this->models->ChoiceKeystep->save(
					array(
						'id' => $this->requestData['ref_choice'],
						'res_keystep_id' => $id
					)
				);

				$this->updateKeyPath(array('choice'=>$this->requestData['ref_choice']));
	
			}

			// redirect to self with id
			$this->redirect('step_edit.php?id='.$id);

		} else {

			if ($this->rHasVal('action','delete')) {
			// deleting the step
			
				$this->deleteKeystep($this->requestData['id']);

				$entry = $this->getPreviousKeypathEntry($this->requestData['id']);

				$this->redirect($entry ? 'step_show.php?id='.$entry['id'] : 'index.php');

			}

			// get step data
			$step = $this->getKeystep($this->requestData['id']);
		
	        $this->setPageName(sprintf(_('Edit step "%s"'),($step['title'] ? $step['title'] : '...')));

			// saving the number (all the rest is done through ajax)
			if ($this->rHasVal('action','save') && !$this->isFormResubmit()) {
			
				// checking the number
				if (empty($this->requestData['number'])) {
				// no number specified

					$next = $this->getNextLowestStepNumber();
	
					$this->addError(
						sprintf(
							_('Step number is required. The saved number for this step is %s. The lowest unused number is %s.'),
							$step['number'],
							$next
							)
						);

				} elseif (!is_numeric($this->requestData['number'])) {
				// non-numeric number specified

					$this->addError(sprintf(_('"%s" is not a number.'),$this->requestData['number']));
	
				} else {
				// existing number specified

					$k = $this->models->Keystep->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'number' => $this->requestData['number'],
								'id != ' => $this->requestData['id']
								),
							'columns' => 'count(*) as total'
						)
					);
				
					if ($k[0]['total']!=0) {
					// doublure
	
						$this->addError(
							sprintf(
								_('A step with number %s already exists. The lowest unused number is %s.'),
								$this->requestData['number'],
								$this->getNextLowestStepNumber()
							)
						);
	
					} else {
					// unique numeric number

						if ($this->requestData['number'] != $step['number']) {
						// don't update if unchanged

							$this->models->Keystep->save(
								array(
									'id' => $this->requestData['id'],
									'project_id' => $this->getCurrentProjectId(),
									'number' => $this->requestData['number']
								)
							);

							// two steps below unnecessary because of redirect to step_show
							//$step['number'] = $this->requestData['number'];
							//$this->addMessage(_('Number saved.'));
		
						}

						$this->redirect('step_show.php?id='.$this->requestData['id']);

					}

				}

			}

		}

		if (isset($step)) $this->smarty->assign('step',$step);

   		$this->smarty->assign('languages',$_SESSION['project']['languages']);

   		$this->smarty->assign('defaultLanguage',$_SESSION['project']['languageList'][$_SESSION['project']['default_language_id']]);

   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();

	}


    public function choiceEditAction()
    {

        $this->checkAuthorisation();

		if (!$this->rHasId()) {
		// create a new choice when no id is specified
		
			if (!$this->rHasVal('step')) {
			// need a step to which the choice belongs

				$this->redirect('step_show.php');

			}

			$id = $this->createNewKeystepChoice($this->requestData['step']);

			$this->renumberKeystepChoices($this->requestData['step']);

			// redirecting to protect against resubmits
			$this->redirect('choice_edit.php?id='.$id);

		} else {

			$id = $this->requestData['id'];

		}

		$choice = $this->getKeystepChoice($id);

		$step = $this->getKeystep($choice['keystep_id']);

		$this->setPageName(
			sprintf(
				_('Edit choice "%s" for step %s: "%s"'),
				$choice['show_order'],
				$step['number'],
				$step['title']
			)
		);

		if ($this->rHasVal('action','delete')) {
		// delete the entire choice, incl image (if any)
		
			if (!empty($choice['choice_img']))
				@unlink($_SESSION['project']['paths']['project_media'].$choice['choice_img']);

			$this->models->ChoiceContentKeystep->delete(
				array(
					'choice_id' => $choice['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->models->ChoiceContentKeystepUndo->delete(
				array(
					'choice_id' => $choice['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->models->ChoiceKeystep->delete(
				array(
					'id' => $choice['id'],						
					'project_id' => $this->getCurrentProjectId()
				)
			);

			unset($_SESSION['system']['remainingTaxa']);

			$this->redirect('step_show.php?id='.$choice['keystep_id']);
		
		} elseif ($this->rHasVal('action','deleteImage')) {
		// delete just the image

			if (!empty($choice['choice_img']))
				@unlink($_SESSION['project']['paths']['project_media'].$choice['choice_img']);

			$this->models->ChoiceKeystep->save(
				array(
					'id' => $choice['id'],						
					'choice_img' => 'null'
				)
			);
			
			unset($choice['choice_img']);

		} 

		if (($this->rHasVal('res_keystep_id') || $this->rHasVal('res_taxon_id')) && !$this->isFormResubmit()) {		
		// save new target

			$ck = $this->models->ChoiceKeystep->update(
				array(
					'res_keystep_id' => 
						$this->requestData['res_keystep_id']==='0' ? 
							'null' : 
							$this->requestData['res_keystep_id'],
					'res_taxon_id' =>
						$this->requestData['res_keystep_id']!=='0' ? 
							'null' : 
							($this->requestData['res_taxon_id']==='0' ? 
								'null' : 
								$this->requestData['res_taxon_id']
							)
				),
				array(
					'id' => $this->requestData['id'],
					'project_id' => $this->getCurrentProjectId(),

				)
			);
			
			if ($this->models->ChoiceKeystep->getAffectedRows()>0) {

				if ($this->requestData['res_taxon_id']!=='0') {

					unset($_SESSION['system']['remainingTaxa']);

					unset($_SESSION['system']['keyTaxaPerStep']);

				}
				
				$choice['res_keystep_id'] = $this->requestData['res_keystep_id'];

				$choice['res_taxon_id'] = $this->requestData['res_taxon_id'];
	
				//$this->addMessage(_('Saved.'));

			} 

			$this->redirect('step_show.php?id='.$step['id']);

		}

		if ($choice['id'] && isset($this->requestDataFiles) && !$this->isFormResubmit()) {
		// save image

			// save choice image
			$this->helpers->FileUploadHelper->setLegalMimeTypes($this->controllerSettings['media']['allowedFormats']);
			$this->helpers->FileUploadHelper->setTempDir($this->getDefaultImageUploadDir());
			$this->helpers->FileUploadHelper->setStorageDir($this->getProjectsMediaStorageDir());
			$this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);

			$this->addError($this->helpers->FileUploadHelper->getErrors());
			$filesToSave = $this->helpers->FileUploadHelper->getResult();

			if ($filesToSave) {

				$ck = $this->models->ChoiceKeystep->save(
					array(
						'id' => $choice['id'],
						'project_id' => $this->getCurrentProjectId(),
						'choice_img' => $filesToSave[0]['name']
					)
				);
			
				if ($ck) {
				
					$this->addMessage(_('Image saved.'));
					
					$choice['choice_img'] = $filesToSave[0]['name'];

				} else {

					@unlink($_SESSION['project']['paths']['project_media'].$filesToSave[0]['name']);

					$this->addError(_('Could not save image.'));

				}

			}

		}

		$this->getTaxonTree();
		
		$this->getRemainingTaxa();


		if (isset($choice)) $this->smarty->assign('data',$choice);

   		$this->smarty->assign('languages',$_SESSION['project']['languages']);

   		$this->smarty->assign('defaultLanguage',$_SESSION['project']['languageList'][$_SESSION['project']['default_language_id']]);

		$this->smarty->assign('steps',$this->getKeysteps(array('idToExclude'=>$choice['keystep_id'])));

		$this->smarty->assign('taxa',$this->treeList);

		$this->smarty->assign('remainingTaxa',$this->_remainingTaxaList);

   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();	
		
    }


	public function sectionAction()
	{
	
		$this->checkAuthorisation();

		if ($this->rHasVal('action','new')) {
		// start a new subsection: create a new step and redirect to edit
				
			$this->redirect('step_edit.php?id='.$this->createNewKeystep());

		}
        
        $this->setPageName( _('Key sections'));

		// get all keys that have is_start = 0
		$l = $this->models->Keystep->_get(
			array(
				'id'=>array(
					'project_id'=>$this->getCurrentProjectId(),
					'is_start' => 0
				)
			)
		);
		
		// ...and check that they are not the target of some other keystep choice
		foreach((array)$l as $key => $val) {

			$ck = $this->models->ChoiceKeystep->_get(
				array(
					'id'=>array(
						'project_id' => $this->getCurrentProjectId(),
						'res_keystep_id' => $val['id']
					),
					'columns' => 'count(*) as total'
				)
			);
			
			// if not, they are the start of a section
			if ($ck[0]['total']==0) {

				$ksc = $this->getKeystepContent($_SESSION['project']['default_language_id'],$val['id']);
			
				$val['title'] = $ksc['title'];

				$d[] = $val;

			}

		}

		if (isset($d)) $this->smarty->assign('keySections',$d);

        $this->printPage();
	
	}


    public function mapAction()
    {

		$this->checkAuthorisation();
        
        $this->setPageName( _('Key map'));

		$key = $_SESSION['system']['keyTree'] = $this->getKeyTree();

		$this->smarty->assign('json',json_encode($key));

        $this->printPage();

    }


    public function rankAction()
    {

		$this->checkAuthorisation();
        
        $this->setPageName( _('Taxon ranks in key'));

		$pr = $this->getProjectRanks(array('lowerTaxonOnly'=>true));

		if ($this->rHasVal('keyRankBorder') && isset($pr) && !$this->isFormResubmit()) {

			$endPoint = false;

			foreach((array)$pr as $key => $val) {

				if ($val['rank_id']==$this->requestData['keyRankBorder']) $endPoint = true;
				
				$this->models->ProjectRank->save(
					array(
						'id' => $val['id'],
						'keypath_endpoint' => $endPoint ? 1 : 0
					)
				);

			}
			
			$this->addMessage(_('Ranks saved.'));

			$pr = $this->getProjectRanks(array('lowerTaxonOnly'=>true,'forceLookup'=>true));

		}

		$this->smarty->assign('projectRanks',$pr);

        $this->printPage();

    }

    public function orphansAction()
    {

		$this->checkAuthorisation();
        
        $this->setPageName( _('Taxa not part of the key'));

		$this->smarty->assign('taxa',$this->getRemainingTaxa());

        $this->printPage();

    }

	public function deadEndsAction()
	{
	
		$this->checkAuthorisation();
        
        $this->setPageName( _('Unconnected key endings'));

		$k = $this->getKeysteps();
		
		foreach((array)$k as $key => $val) {

			$kc = $this->getKeystepChoices($val['id']);
			
			if (count((array)$kc)==0) $deadSteps[] = $val;

		}

		$deadChoices = $this->models->ChoiceKeystep->_get(
			array('id' => 
					'select * from %table% where project_id = '.
						$this->getCurrentProjectId().' '.
						'and (res_keystep_id = -1 or res_keystep_id is null) '.
						'and res_taxon_id is null '.
						'order by show_order desc'
			)
		);
		
		foreach((array)$deadChoices as $key => $val) {

			$k = $this->getKeystep($val['keystep_id']);

			$deadChoices[$key]['orderBy'] = $k['number'];
			$deadChoices[$key]['step'] = $k;

			$kc = $this->getKeystepChoice($val['id']);

			$deadChoices[$key]['title'] = isset($kc['title']) ? $kc['title'] : '...';
	
		}
		
		$this->customSortArray($deadChoices, array(
            'key' => 'orderBy', 
            'dir' => 'asc', 
            'case' => 'i'
        ));

		$this->smarty->assign('deadSteps',$deadSteps);

		$this->smarty->assign('deadChoices',$deadChoices);

        $this->printPage();
	
	}

	public function processAction()
	{
	
		$this->checkAuthorisation();
        
        $this->setPageName( _('Compute taxon division'));
		
		if ($this->rHasVal('action','process') && !$this->isFormResubmit()) {

			$d = $this->getTaxonDivision();

			$_SESSION['system']['keyTaxaPerStep'] = $d['list'];

			$this->smarty->assign('taxonCount',$d['taxonCount']);

			$this->smarty->assign('stepCount',count((array)$d['list']));

		} elseif (isset($_SESSION['system']['keyTaxaPerStep'])) {

			$this->addMessage(_('Be aware that you have already generated a taxon per step division, and have not changed your key since. It is not necessary to re-generate it.'));

		}
	
        $this->printPage();

	}

    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        

        if ($this->requestData['action'] == 'get_keystep_content') {

            $this->getKeystepContent();

        } elseif ($this->requestData['action'] == 'save_keystep_content') {

            $this->saveKeystepContent($this->requestData);

        } elseif ($this->requestData['action'] == 'get_keystep_undo') {

            $this->getKeystepUndo($this->requestData);

        } elseif ($this->requestData['action'] == 'get_key_choice_content') {

            $this->getKeystepChoiceContent();

        } elseif ($this->requestData['action'] == 'save_key_choice_content') {

            $this->saveKeystepChoiceContent($this->requestData);

        } elseif ($this->requestData['action'] == 'get_key_choice_undo') {

            $this->getKeystepChoiceUndo($this->requestData);

        }
		
        $this->printPage();
    
    }

	private function setStepsPerTaxon($choice)
	{

		$this->_taxaStepList[] = $choice['keystep_id'];

		// get choices that have the keystep the choice belongs to as target
		$cks = $this->models->ChoiceKeystep->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'res_keystep_id' => $choice['keystep_id']
				)
			)
		);
		
		foreach((array)$cks as $key => $val) {
		
			$this->setStepsPerTaxon($val);

		}
	
	}

	private function getTaxonDivision()
	{

		// get all choices that have a taxon as result
		$ck = $this->models->ChoiceKeystep->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'res_taxon_id is not' => 'null'
				)
			)
		);

		// for each...
		foreach((array)$ck as $key => $val) {

			unset($this->_taxaStepList);
			
			// ...work our way back to the top most step...
			$this->setStepsPerTaxon($val);
			
			/// ...and save the results
			$results[$val['res_taxon_id']] =  $this->_taxaStepList;

		}

		// turn it from a list of taxa with their steps into a list of steps with their taxa
		foreach((array)$results as $taxonId => $stepIds) {

			foreach($stepIds as $key2 => $stepId) {

				if (!isset($d[$stepId][$taxonId])) {

					$d[$stepId][$taxonId] = true;

					$list[$stepId][] = $this->models->Taxon->_get(array('id'=>$taxonId));

				}

			}

		}

		return array(
			'list' => $list,
			'taxonCount' => count($ck)
		);

	}

	private function updateKeyPath($params) 
	{

		$id = isset($params['id']) ? $params['id'] : null;
		$number = isset($params['number']) ? $params['number'] : null;
		$title = isset($params['title']) ? $params['title'] : null;
		$is_start = isset($params['is_start']) ? $params['is_start'] : null;
		$choice = isset($params['choice']) ? $params['choice'] : null;
					
		if (isset($_SESSION['system']['keyPath'])) {

			foreach((array)$_SESSION['system']['keyPath'] as $key => $val) {

				if ($val['id']==$id) break;

				if (!empty($val['id'])) $d[] = $val;

			}

		}

		$d[] = array(
			'id' => $id,
			'number' => $number,
			'title' => $title,
			'is_start' => $is_start,
			'choice' => null,
			'choiceTitle' => null
		);

		if (!empty($choice) && (count((array)$d)-2)>=0) {

			$choice = $this->getKeystepChoice($choice);

			$d[count((array)$d)-2]['choice'] = $choice;

			$d[count((array)$d)-2]['choiceTitle'] = isset($choice['title']) ? $choice['title'] : '...';

		}

		$_SESSION['system']['keyPath'] = $d;

	}
	
	private function getKeyPath()
	{

		return isset($_SESSION['system']['keyPath']) ? $_SESSION['system']['keyPath'] : false;

	}

	private function getPreviousKeypathEntry($id=false,$stepsBack=1)
	{

		$kp = $this->getKeyPath();
		
		$c = count((array)$kp);
		
		if ($id) {

			for($i=($c-1);$i>=0;$i--) {

				if (isset($kp[$i+$stepsBack]) && $kp[$i+$stepsBack]['id']==$id) {

					return $kp[$i];

				}

			}
			
			return false;

		} else {

			return isset($kp[$c-($stepsBack+1)]) ? $kp[$c-($stepsBack+1)] : false;

		}

	}

	private function getRemainingTaxa()
	{

		if (isset($_SESSION['system']['remainingTaxa']) && isset($_SESSION['system']['_remainingTaxaList'])) {

			$this->_remainingTaxaList = $_SESSION['system']['_remainingTaxaList'];
			return $_SESSION['system']['remainingTaxa'];

		}

		unset($this->_remainingTaxaList);
		unset($_SESSION['system']['remainingTaxa']);
		
		$taxa = false;

		$pr = $this->getProjectRanks(array('keypathEndpoint'=>true,'forceLookup'=>true));

		foreach((array)$pr as $key => $val) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'rank_id' => $val['id']
					)
				)
			);

			foreach((array)$t as $tkey => $tval) {
			
				$ck = $this->models->ChoiceKeystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'res_taxon_id' => $tval['id']
						),
						'columns' => 'count(*) as total'
					)
				);

				if ($ck[0]['total']==0) {
				
					$taxa[] = $tval;
					
					$this->_remainingTaxaList[$tval['id']]=true;
				
				}
			
			}

		}

        $this->customSortArray($taxa, array(
            'key' => 'taxon_order', 
            'dir' => 'asc', 
            'case' => 'i'
        ));
		
		$_SESSION['system']['remainingTaxa'] = $taxa;
		$this->_remainingTaxaList = $_SESSION['system']['_remainingTaxaList'] = $this->_remainingTaxaList;

		return $taxa;

	}

	private function getKeyTree($refId=null,$choiceId=null)
	{
	
		$s = $refId==null ? $this->getStartKeystep() : $this->getKeystep($refId);

		$step = array(
			'id' => $s['id'],
			'name' => $s['number'].'. '.$s['title'], // required for the JIT script
			'type' => 'step',
			'data' => array(
				'number'=>$s['number'],
				'title'=>$s['title'],
				'is_start'=>$s['is_start'],
				'node' => $this->_counter++,
				'referringChoiceId' => $choiceId
			)
		);

		$this->_stepList[$step['id']] = true;

		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $step['id']
				),
				'order' => 'show_order'
			)
		);
		
		foreach((array)$ck as $key => $val) {

			// $this->_stepList check is protection against circular reference
			if ($val['res_keystep_id'] && !isset($this->_stepList[$val['res_keystep_id']])) {

//				$ck[$key]['children'] = $this->getKeyTree($val['res_keystep_id']);
				$step['children'][] = $this->getKeyTree($val['res_keystep_id'],$val['id']);

			} elseif ($val['res_taxon_id']) {
			
//				$ck[$key]['taxon'] = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));
				$t = $this->models->Taxon->_get(
					array(
						'id' => $val['res_taxon_id'],
						'columns' => 'id,taxon'
					)
				);
				$step['children'][] = array(
					'id' => 't'.$t['id'],
					'type' => 'taxon',
					'data' => array(
						'number'=>'t'.$t['id'],
						'title'=>'&rarr; '.$t['taxon'],
						'taxon'=> $t['taxon'],
						'id'=>$t['id']
					),
					'name' => '<i>'.$t['taxon'].'</i>'
				);
			
			} 

		}
		
		return $step;

	}


	private function getKeysteps($params=null)
	{

		$id = isset($params['id']) ? $params['id'] : false;

		$idToExclude = isset($params['idToExclude']) ? $params['idToExclude'] : false;

		$isStart = isset($params['isStart']) ? $params['isStart'] : false;
		

		$p['columns'] = isset($params['columns']) ? $params['columns'] : '*';

		$p['order'] = isset($params['order']) ? $params['order'] : 'number';

		$p['id']['project_id'] = $this->getCurrentProjectId();


		if ($id) $p['id']['id'] = $id;

		if ($idToExclude) $p['id']['id !='] = $idToExclude;

		if ($isStart) $p['id']['isStart'] = $isStart;
		
		$k = $this->models->Keystep->_get($p);
		
		foreach((array)$k as $key => $val) {

			$kc = $this->getKeystepContent($_SESSION['project']['default_language_id'],$val['id']);
		
			$k[$key]['title'] = $kc['title'];

			$k[$key]['content'] = $kc['content'];

		}
		
		return $k;

	}

	private function getKeystep($id=null)
	{

 		$id = isset($id) ? $id : $this->requestData['id'];

        if (empty($id)) {
            
            return;
        
        } else {

			$k = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $id, 
						)
				)
			);

			if (!$k) return false;

			$step = $k[0];

			$kc = $this->getKeystepContent($_SESSION['project']['default_language_id'],$step['id']);

			$step['title'] = $kc['title'];

			$step['content'] = $kc['content'];

            $this->smarty->assign('returnText', json_encode($step));

			return $step;
        
        }

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
		
		if ($k) {

			$kc = $this->getKeystepContent($_SESSION['project']['default_language_id'],$k[0]['id']);
		
			$k[0]['title'] = $kc['title'];

			$k[0]['content'] = $kc['content'];

		}
				
		return $k[0];
	
	}

	private function getKeystepContent($language=null,$id=null)
	{

		$language = isset($language) ? $language : $this->requestData['language'];

 		$id = isset($id) ? $id : $this->requestData['id'];

        if (empty($language) || empty($id)) {
            
            return;
        
        } else {

			$ck = $this->models->ContentKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'keystep_id' => $id, 
						'language_id' => $language
						),
					'columns' => 'title,content'
				)
			);
                
            $this->smarty->assign('returnText', json_encode($ck[0]));

			return $ck[0];
        
        }

	}

	private function saveKeystepContent ($data)
    {
        
        if (empty($data['language'])) {
            
            return;
        
        } else {

			$ck = $this->models->ContentKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'keystep_id' => $data['id'], 
						'language_id' => $data['language']
					)
				)
			);


			$d = array(
					'id' => isset($ck[0]['id']) ? $ck[0]['id'] : null, 
					'project_id' => $this->getCurrentProjectId(), 
					'keystep_id' => $data['id'], 
					'language_id' => $data['language'],
					'title' => trim($data['content'][0]),
					'content' => trim($data['content'][1])
				);

			// initiate save to undo buffer
			$this->models->ContentKeystep->setRetainBeforeAlter();

			// save step
			$this->models->ContentKeystep->save($d);

			// save to undo buffer
			$this->saveOldKeyData($this->models->ContentKeystep->getRetainedData(), $d, 'manual');

            $this->smarty->assign('returnText', $this->models->ContentKeystep->getAffectedRows()>0 ? _('saved') : '');
        
        }
    
    }
	
	private function getNextLowestStepNumber()
	{

		$k = $this->models->Keystep->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'number',
				'order' => 'number'
			)
		);
		
		$prev = 0;
		$next = false;
	
		foreach((array)$k as $key => $val) {
	
			if ($val['number'] - $prev > 1) {
	
				$next = $prev +1;
				
				break;
	
			} else {
	
				$prev = $val['number'];
	
			}
	
		}
		
		if (!$next) $next = $prev + 1;
		
		return $next;

	}

	private function createNewKeystep($data=null)
	{

		$this->models->Keystep->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'number' => !empty($data['number']) ? $data['number'] : $this->getNextLowestStepNumber(),
				'is_start' => !empty($data['is_start']) ? $data['is_start'] : 0,
			)
		);
		
		return $this->models->Keystep->getNewId();
	
	}

	private function deleteKeystep($id)
	{

		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $id
				)
			)
		);
		
		foreach((array)$ck as $key => $val) {
	
			$this->models->ChoiceContentKeystep->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'choice_id' => $val['id'], 
				)
			);

			$this->models->ChoiceContentKeystepUndo->delete(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'choice_id' => $val['id']
				)
			);

		}

		$this->models->ChoiceKeystep->delete(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $id
				)
			)
		);

		$this->models->ChoiceKeystep->update(
			array(
				'res_keystep_id' => 'null'
			),
			array(
				'project_id' => $this->getCurrentProjectId(),
				'res_keystep_id' => $id
			)
		);

		$this->models->ContentKeystep->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'keystep_id' => $id, 
			)
		);

		$this->models->ContentKeystepUndo->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $id
			)
		);

		$this->models->Keystep->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $id, 
			)
		);

	}

	private function createNewKeystepChoice($stepId)
	{

		if (empty($stepId)) return;

		$this->models->ChoiceKeystep->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $stepId,
				'show_order' => 99
			)
		);

		return $this->models->ChoiceKeystep->getNewId();
	
	}


	private function getKeystepChoices($step,$formatHtml=false)
	{

		$choices =  $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $step
				),
				'order' => 'show_order'
			)
		);
		
		foreach((array)$choices as $key => $val) {
		
			$kcc = $this->getKeystepChoiceContent($_SESSION['project']['default_language_id'],$val['id']);
			
			if (isset($kcc['title'])) $choices[$key]['title'] = $kcc['title'];
			
			if (isset($kcc['choice_txt'])) $choices[$key]['choice_txt'] = $formatHtml ? nl2br($kcc['choice_txt']) : $kcc['choice_txt'];

			if (!empty($val['res_keystep_id']) && $val['res_keystep_id']!=0) {
			
				if ($val['res_keystep_id']=='-1') {

					$choices[$key]['target'] = _('(new step)');

				} else {

					$k = $this->getKeystep($val['res_keystep_id']);

					if (isset($k['title'])) $choices[$key]['target'] = $k['title'];

					if (isset($k['number'])) $choices[$key]['target_number'] = $k['number'];

				}
			
			} elseif (!empty($val['res_taxon_id'])) {

				$t = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));

				if (isset($t['taxon'])) $choices[$key]['target'] = $t['taxon'];

			} else {

				$choices[$key]['target'] = _('undefined');

			}
	
		}

		return $choices;

	}

	private function getKeystepChoice($id)
	{

		$ck =  $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);
		
		$choice = $ck[0];
		
		$kcc = $this->getKeystepChoiceContent($_SESSION['project']['default_language_id'],$choice['id']);
		
		if (isset($kcc['title'])) $choice['title'] = $kcc['title'];
		
		if (isset($kcc['choice_txt'])) $choice['choice_txt'] = $kcc['choice_txt'];

		if (!empty($choice['res_keystep_id']) && $choice['res_keystep_id']!=0) {
		
			if ($choice['res_keystep_id']=='-1') {

				$choice['target'] = _('(new step)');

			} else {

				$k = $this->models->Keystep->_get(array('id' => $choice['res_keystep_id']));
				
				if (isset($k['title'])) $choice['target'] = $k['title'];

				if (isset($k['number'])) $choice['target_number'] = $k['number'];

			}
		
		} elseif (!empty($choice['res_taxon_id'])) {

			$t = $this->models->Taxon->_get(array('id' => $choice['res_taxon_id']));

			if (isset($t['taxon'])) $choice['target'] = $t['taxon'];

		} else {

			$choice['target'] = _('undefined');

		}

		return $choice;

	}

	private function saveKeystepChoiceContent ($data)
    {
        
        if (empty($data['language'])) {
            
            return;
        
        } else {

			$ck = $this->models->ChoiceContentKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'choice_id' => $data['id'], 
						'language_id' => $data['language']
					)
				)
			);


			$d = array(
				'id' => isset($ck[0]['id']) ? $ck[0]['id'] : null, 
				'project_id' => $this->getCurrentProjectId(), 
				'choice_id' => $data['id'], 
				'language_id' => $data['language'],
				'title' => trim($data['content'][0]),
				'choice_txt' => trim($data['content'][1])
			);

			// initiate save to undo buffer
			$this->models->ChoiceContentKeystep->setRetainBeforeAlter();

			// save choice
			$this->models->ChoiceContentKeystep->save($d);

			// save to undo buffer
			$this->saveOldKeyChoiceData($this->models->ChoiceContentKeystep->getRetainedData(), $d, 'manual');

            $this->smarty->assign('returnText', $this->models->ChoiceContentKeystep->getAffectedRows()>0 ? _('saved') : '');
        
        }
    
    }

	private function getKeystepChoiceContent($language=null,$id=null)
	{

		$language = isset($language) ? $language : $this->requestData['language'];

 		$id = isset($id) ? $id : $this->requestData['id'];

        if (empty($language) || empty($id)) {
            
            return;
        
        } else {

			$ck = $this->models->ChoiceContentKeystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'choice_id' => $id, 
						'language_id' => $language
						),
					'columns' => 'title,choice_txt'
				)
			);

            $this->smarty->assign('returnText', json_encode($ck[0]));

			return $ck[0];
        
        }

	}

	private function moveKeystepChoice($id,$direction)
	{

		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' =>  array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$ck2 = $this->models->ChoiceKeystep->_get(
			array(
				'id' =>  array(
					'keystep_id' => $ck[0]['keystep_id'],
					'project_id' => $this->getCurrentProjectId(),
					'id !=' => $id,
					'show_order' => $ck[0]['show_order']+($direction=='up' ? -1 : 1)					
				)
			)
		);

		$this->models->ChoiceKeystep->save(
			array(
				'id' => $id,
				'show_order' => $ck2[0]['show_order']
			)
		);

		$this->models->ChoiceKeystep->save(
			array(
				'id' => $ck2[0]['id'],
				'show_order' => $ck[0]['show_order']
			)
		);
		
	}


	private function renumberKeystepChoices($step)
	{

		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' =>  array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $step
				),
				'order' => 'show_order'
			)
		);
		
		foreach((array)$ck as $key => $val) {

			$this->models->ChoiceKeystep->save(
				array(
					'id' => $val['id'],
					'show_order' => $key+1
				)
			);

		}

	}

	private function findNodeInTree($branch,$node)
	{

		foreach((array)$branch as $key => $val) {
	
			$isNode = (isset($val['data']['node']) && $val['data']['node']==$node);

			$result = false;

			if (!$isNode && isset($val['children'])) $result = $this->findNodeInTree($val['children'],$node);

			if ($isNode || $result==true) {

				array_unshift($this->_stepList,
					array(
						'id' => $val['id'],
						'number' => $val['data']['number'],
						'title' => $val['data']['title'],
						'is_start' => $val['data']['is_start'],
						'choice' => $val['data']['referringChoiceId']
					)
				);

				return true;

			}

		}
		
		return false;
	
	}

    private function saveOldKeyChoiceData($data, $newdata=false, $mode = 'auto')
    {

		// if there is no "old" data, there's nothing to undo
		if ($data===false) return;
		
        $d = $data[0];

		if ($newdata!==false && 
			(
				(isset($d['title']) && isset($newdata['title']) && $d['title'] == $newdata['title']) &&
				(isset($d['choice_txt']) && isset($newdata['choice_txt']) && $d['choice_txt'] == $newdata['choice_txt'])
			)
		) return;

        $d['save_type'] = $mode;
        
        $d['choice_content_id'] = $d['id'];
        
        $d['id'] = null;
        
        $d['choice_content_created'] = $d['created'];
        unset($d['created']);
        
        $d['choice_last_change'] = $d['last_change'];
        unset($d['last_change']);
        
        $this->models->ChoiceContentKeystepUndo->save($d);
    
    }

    private function saveOldKeyData($data, $newdata = false, $mode = 'auto')
    {

		// if there is no "old" data, there's nothing to undo
		if ($data===false) return;

        $d = $data[0];

		if ($newdata!==false && 
			(
				(isset($d['title']) && isset($newdata['title']) && $d['title'] == $newdata['title']) &&
				(isset($d['content']) && isset($newdata['content']) && $d['content'] == $newdata['content'])
			)
		) return; 

        $d['save_type'] = $mode;
        
        $d['keystep_content_id'] = $d['id'];
        
        $d['id'] = null;
        
        $d['keystep_content_created'] = $d['created'];
        unset($d['created']);
        
        $d['keystep_content_last_change'] = $d['last_change'];
        unset($d['last_change']);

        $this->models->ContentKeystepUndo->save($d);

    }

	private function getKeystepUndo($data)
	{

		if (!isset($data['id'])) return;
		
		// determine last insert
		$d = $this->models->ContentKeystepUndo->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $data['id']
				),
				'columns' => ' max(created) as last'
			)
		);

		// retrieve data
		$d = $this->models->ContentKeystepUndo->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $data['id'],
					'created' => $d[0]['last']
				)
			)
		);


		// delete from undo buffer
		$this->models->ContentKeystepUndo->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $data['id'],
				'id' => $d[0]['id']
			)
		);

		$this->smarty->assign('returnText', json_encode($d[0]));		
	
	}

	private function getKeystepChoiceUndo($data)
	{

		if (!isset($data['id'])) return;
		
		// determine last insert
		$d = $this->models->ChoiceContentKeystepUndo->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'choice_id' => $data['id']
				),
				'columns' => ' max(created) as last'
			)
		);

		// retrieve data
		$d = $this->models->ChoiceContentKeystepUndo->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'choice_id' => $data['id'],
					'created' => $d[0]['last']
				)
			)
		);


		// delete from undo buffer
		$this->models->ChoiceContentKeystepUndo->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'choice_id' => $data['id'],
				'id' => $d[0]['id']
			)
		);

		$this->smarty->assign('returnText', json_encode($d[0]));		
	
	}

}
