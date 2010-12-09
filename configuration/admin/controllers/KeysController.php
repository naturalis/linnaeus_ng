<?php

include_once ('Controller.php');

class KeysController extends Controller
{
    
    public $remainingTaxaList;
	private $_stepList;

    public $usedModels = array(
		'keystep',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep'
    );
    
    public $usedHelpers = array(
		'file_upload_helper'
    );

    public $controllerPublicName = 'Dichotomous key';

	public $jsToLoad = array('key.js','jit/jit.js','jit/key-tree.js');

/*<!--[if IE]><script language="javascript" type="text/javascript" src="../../Extras/excanvas.js"></script><![endif]--> */

	public $cssToLoad = array('key.css','rank-list.css','key-tree.css');

    public function __construct ()
    {
        
        parent::__construct();
   
    }

    public function __destruct ()
    {
        
        parent::__destruct();
    
    }

    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

		unset($_SESSION['system']['keyPath']);
		unset($_SESSION['system']['step']);
		unset($_SESSION['system']['keySubsection']);
       
        $this->printPage();
    
    }

    public function stepShowAction()
    {

        $this->checkAuthorisation();

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
			$choices = $this->getKeystepChoices($step['id']);
			
			$this->updateKeyPath(
				$step['id'],
				$step['number'],
				$step['title'],
				$step['is_start'],
				isset($this->requestData['choice']) ? $this->requestData['choice'] : null
			);
	
			$this->smarty->assign('step',$step);
	
			$this->smarty->assign('choices',$choices);
	
			$this->smarty->assign('maxChoicesPerKey',$this->controllerSettings['maxChoicesPerKey']);

		} else {
		
			$this->addError(_('Non-existant keystep ID. Please go back and change the target for the choice.'));

		}

		$this->setPageName(sprintf(_('Show key step "%s"'),$step['title']));

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

				$this->updateKeyPath(null,null,null,null,$_SESSION['system']['refChoice']);
	
			}

			// redirect to self with id
			$this->redirect('step_edit.php?id='.$id);

		} else {

			if ($this->rHasVal('action','delete')) {
			
				$this->deleteKeyStep($this->requestData['id']);

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
	
						$this->addError(
							sprintf(
								_('A step with number %s already exists. The lowest unused number is %s.'),
								$this->requestData['number'],
								$this->getNextLowestStepNumber()
							)
						);
	
					} else {

						if ($this->requestData['number'] != $step['number']) {
		
							$this->models->Keystep->save(
								array(
									'id' => $this->requestData['id'],
									'project_id' => $this->getCurrentProjectId(),
									'number' => $this->requestData['number']
								)
							);
							
							$step['number'] = $this->requestData['number'];
		
							$this->addMessage(_('Number saved.'));
		
						}

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

			$this->models->ChoiceKeystep->delete(
				array(
					'id' => $choice['id'],						
					'project_id' => $this->getCurrentProjectId(),
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

				if ($this->requestData['res_taxon_id']!=='0') unset($_SESSION['system']['remainingTaxa']);
				
				$choice['res_keystep_id'] = $this->requestData['res_keystep_id'];

				$choice['res_taxon_id'] = $this->requestData['res_taxon_id'];
	
				$this->addMessage(_('Target saved.'));

			}

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

		$this->getTaxonTree(null);
		
		$this->getRemainingTaxa();


		if (isset($choice)) $this->smarty->assign('data',$choice);

   		$this->smarty->assign('languages',$_SESSION['project']['languages']);

   		$this->smarty->assign('defaultLanguage',$_SESSION['project']['languageList'][$_SESSION['project']['default_language_id']]);

		$this->smarty->assign('steps',$this->getKeySteps(array('idToExclude'=>$choice['keystep_id'])));

		$this->smarty->assign('taxa',$this->treeList);

		$this->smarty->assign('remainingTaxa',$this->remainingTaxaList);

   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();	
		
    }


	public function sectionAction()
	{
	
		$this->checkAuthorisation();

		// start a new subsection: create a new step and redirect to edit
		if (isset($this->requestData['action']) && $this->requestData['action']=='new') {
				
			$this->redirect('step_edit.php?id='.$this->createNewKeystep());

		}
        
        $this->setPageName( _('Key subsections'));

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

				$ksc = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$val['id']);
			
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

		$key = $this->getKeyTree();

		$this->smarty->assign('json',json_encode($key));

        $this->printPage();

    }


    public function rankAction()
    {

		$this->checkAuthorisation();
        
        $this->setPageName( _('Taxon ranks in key'));
		
		$pr = $this->getProjectRanks(array('lowerTaxonOnly'=>true));

		if (isset($this->requestData['keyRankBorder']) && isset($pr) && !$this->isFormResubmit()) {

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

		$ck = $this->models->ChoiceKeystep->_get(array('id' => 
			'select * from %table% where project_id = '.
				$this->getCurrentProjectId().
				' and (res_keystep_id = -1 or res_keystep_id is null)'.
				' and res_taxon_id is null'.
				' order by show_order desc'
		));
		
		foreach((array)$ck as $key => $val) {

			$k = $this->models->Keystep->_get(array('id'=>$val['keystep_id']));

			$ck[$key]['orderBy'] = $k['number'];
			$ck[$key]['step'] = $k;
	
		}
		
		$this->customSortArray($ck, array(
            'key' => 'orderBy', 
            'dir' => 'asc', 
            'case' => 'i'
        ));

		$this->smarty->assign('keyendings',$ck);

        $this->printPage();
	
	}


    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        
        if ($this->requestData['action'] == 'get_key_step_content') {

            $this->getKeyStepContent();

        } elseif ($this->requestData['action'] == 'save_step_title') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;

            $this->saveKeyStepContent($this->requestData,'title');

        } elseif ($this->requestData['action'] == 'save_step_text') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;
	
            $this->saveKeyStepContent($this->requestData,'text');

        } elseif ($this->requestData['action'] == 'get_key_choice_content') {

            $this->getKeystepChoiceContent();

        } elseif ($this->requestData['action'] == 'save_choice_title') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;

            $this->saveKeystepChoiceContent($this->requestData,'title');

        } elseif ($this->requestData['action'] == 'save_choice_text') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;
	
            $this->saveKeystepChoiceContent($this->requestData,'text');

        }
		
        $this->printPage();
    
    }

	private function updateKeyPath($id,$number,$title,$is_start,$choice) 
	{

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

	private function getRemainingTaxa()
	{

		if (isset($_SESSION['system']['remainingTaxa']) && isset($_SESSION['system']['remainingTaxaList'])) {

			$this->remainingTaxaList = $_SESSION['system']['remainingTaxaList'];
			return $_SESSION['system']['remainingTaxa'];

		}

		unset($this->remainingTaxaList);
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
					
					$this->remainingTaxaList[$tval['id']]=true;
				
				}
			
			}

		}

        $this->customSortArray($taxa, array(
            'key' => 'taxon_order', 
            'dir' => 'asc', 
            'case' => 'i'
        ));
		
		$_SESSION['system']['remainingTaxa'] = $taxa;
		$this->remainingTaxaList = $_SESSION['system']['remainingTaxaList'] = $this->remainingTaxaList;

		return $taxa;

	}

	private function formatKeyTree($tree)
	{
	
	}

	private function getKeyTree($refId=null)
	{
	
		if ($refId==null) {

			$k = $this->models->Keystep->_get(array('id' => array('project_id' => $this->getCurrentProjectId(),'is_start' => 1)));
			

		} else {

			$k = $this->models->Keystep->_get(array('id' => array('project_id' => $this->getCurrentProjectId(),'id' => $refId)));

		}

		$step['id'] = $k[0]['id'];
		$step['name'] = $k[0]['number'].'. '.$k[0]['title']; // required for the JIT script
		$step['data'] = array('number'=>$k[0]['number'],'title'=>$k[0]['title']);
		
		$this->_stepList[$step['id']] = true;

		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $step['id']
				)
			)
		);
		
		foreach((array)$ck as $key => $val) {

			// $this->_stepList check is protection against circular reference
			if ($val['res_keystep_id'] && !isset($this->_stepList[$val['res_keystep_id']])) {

//				$ck[$key]['children'] = $this->getKeyTree($val['res_keystep_id']);
				$step['children'][] = $this->getKeyTree($val['res_keystep_id']);

			} elseif ($val['res_taxon_id']) {
			
//				$ck[$key]['taxon'] = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));
				$step['children'][] = $this->models->Taxon->_get(
					array(
						'id' => $val['res_taxon_id'],
						'columns' => 'id,taxon'
					)
				);
			
			} 

		}
		
		return $step;

	}


	private function getKeySteps($params)
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

			$kc = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$val['id']);
		
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

			$kc = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$step['id']);

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

			$kc = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$k[0]['id']);
		
			$k[0]['title'] = $kc['title'];

			$k[0]['content'] = $kc['content'];

		}
				
		return $k[0];
	
	}

	private function getKeyStepContent($language=null,$id=null)
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


	private function saveKeyStepContent ($data,$type='text')
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
					'language_id' => $data['language']
				);

			if ($type=='title') {

				$d['title'] = trim($data['content']);

			} else {

				$d['content'] = trim($data['content']);

			}

			
			$this->models->ContentKeystep->save($d);

            $this->smarty->assign('returnText', '<ok>');
        
        }
    
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

	private function deleteKeyStep($id)
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
	
			$a =$this->models->ChoiceContentKeystep->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'choice_id' => $val['id'], 
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


	private function getKeystepChoices($step)
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
			
			if (isset($kcc['choice_txt'])) $choices[$key]['choice_txt'] = $kcc['choice_txt'];

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

	private function saveKeystepChoiceContent ($data,$type='text')
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
					'language_id' => $data['language']
				);

			if ($type=='title') {

				$d['title'] = trim($data['content']);

			} else {

				$d['choice_txt'] = trim($data['content']);

			}

			
			$this->models->ChoiceContentKeystep->save($d);

            $this->smarty->assign('returnText', '<ok>');
        
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
	


}
















