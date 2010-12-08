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

		if (isset($this->requestData['id']) && isset($this->requestData['action']) && $this->requestData['action'] == 'delete') {

			$this->deleteKeyStep($this->requestData['id']);
			
			$_SESSION['system']['step'] = $kp[count($kp)-2]['id'];

			$kp = $this->getKeyPath();
					
			$this->redirect('step_show.php');
		
		} else if (isset($this->requestData['id']) || isset($_SESSION['system']['step'])) {
		// request for specific key

			$k = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => isset($this->requestData['id']) ? $this->requestData['id'] : $_SESSION['system']['step']
					)
				)
			);

			$step = $k[0];

			$kc = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$step['id']);

			$step['title'] = $kc['title'];

			$step['content'] = $kc['content'];

			unset($_SESSION['system']['step']);

		} else {
		// looking for the start key 

			$k = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'is_start' => 1
					)
				)
			);
			
			if ($k) {
			// found it

				$step = $k[0];

				$data = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$step['id']);
			
				$step['title'] = $data['title'];

				$step['content'] = $data['content'];

			} else {
			// if it doesn't exist, create it and go edit

				$this->redirect('step_edit.php?id='.$this->createNewKeystep(array('is_start'=>1)));

			}

		}
		
		$_SESSION['system']['step'] = $step['id'];

		if (isset($this->requestData['move']) && isset($this->requestData['direction']) && !$this->isFormResubmit()) {

			$this->moveChoice($this->requestData['move'],$this->requestData['direction']);

		}

		$choices = $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $step['id']
				),
				'order' => 'show_order'
			)
		);

		foreach((array)$choices as $key => $val) {

			if (!empty($val['res_keystep_id']) && $val['res_keystep_id']!=0) {
			
				if ($val['res_keystep_id']=='-1') {

					$choices[$key]['target'] = _('(new step)');

				} else {
			
					$k = $this->models->Keystep->_get(array('id' => $val['res_keystep_id']));
					
					$choices[$key]['target'] = $k['title'];

					$choices[$key]['target_number'] = $k['number'];

				}
			
			} elseif (!empty($val['res_taxon_id'])) {

				$t = $this->models->Taxon->_get(array('id' => $val['res_taxon_id']));

				$choices[$key]['target'] = $t['taxon'];

			} else {

				$choices[$key]['target'] = _('undefined');

			}

		}
		
		$this->updateKeyPath(
			$step['id'],
			$step['number'],
			$step['title'],
			isset($this->requestData['choice']) ? $this->requestData['choice'] : null
		);

		$this->setPageName(sprintf(_('Show key step "%s"'),$step['title']));

		$this->smarty->assign('keyPath',$this->getKeyPath());

		$this->smarty->assign('step',$step);

		$this->smarty->assign('choices',$choices);

		$this->smarty->assign('maxChoicesPerKey',$this->controllerSettings['maxChoicesPerKey']);

		$this->printPage();

	}


    public function stepEditAction()
    {

        $this->checkAuthorisation();

		if (!isset($this->requestData['id']) || empty($this->requestData['id'])) {

			$this->addError(_('No step ID specified.'));

		} else {

			// get step data
			$k = $this->models->Keystep->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $this->requestData['id']
							)
						)
					);

			$step = $k[0];
		
			$data = $this->getKeyStepContent($_SESSION['project']['default_language_id'],$step['id']);
		
			$step['title'] = $data['title'];

	        $this->setPageName(sprintf(_('Edit step "%s"'),($step['title'] ? $step['title'] : '...')));

			// saving the number (rest is done through ajax)
			if (isset($this->requestData['action']) && $this->requestData['action']=='save' && !$this->isFormResubmit()) {
			
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


/*
		if (isset($this->requestData['ref_choice'])) {

			$_SESSION['system']['refChoice'] = $this->requestData['ref_choice'];
			
		}

		if (isset($_SESSION['system']['refChoice'])) {

			$this->updateKeyPath(null,null,null,$_SESSION['system']['refChoice']);

			$choice = $this->models->ChoiceKeystep->_get(array('id' => $_SESSION['system']['refChoice']));

			$this->smarty->assign('choice',$choice);

		}


				if (!$k) {

					$this->addError(_('Could not save step data.'));
	
				} else {

					$step['id'] = !empty($this->requestData['id']) ? $this->requestData['id'] : $this->models->Keystep->getNewId();
				
					$k = $this->models->Keystep->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'is_start' => 1
							),
							'columns' => 'count(*) as total'
						)
					);

					if ($k[0]['total']==0 && !isset($_SESSION['system']['keySubsection'])) {

						$k = $this->models->Keystep->save(array('id' => $step['id'],'is_start' => 1));

					} else {

						if (isset($_SESSION['system']['refChoice'])) {

							if ($choice['keystep_id'] != $step['id']) {

								$this->models->ChoiceKeystep->save(
									array(
										'id' => $_SESSION['system']['refChoice'],
										'res_keystep_id' => $step['id']
									)
								);

							}

							unset($_SESSION['system']['refChoice']);

						}

					}

					unset($_SESSION['system']['keySubsection']);

					$this->addMessage(_('Step data saved.'));
					
					$_SESSION['system']['step'] = $step['id'];
	
					//$this->redirect('step_show.php');
					
				}

			}

		}


*/

	}

    public function choiceEditAction()
    {

        $this->checkAuthorisation();

		if ((!isset($this->requestData['id']) || empty($this->requestData['id'])) && !$this->isFormResubmit()) {
		// create a new choice when no id is specified
		
			if (!isset($_SESSION['system']['step']) || empty($_SESSION['system']['step'])) {
			// need a step to which the choice belongs

				$this->redirect('step_show.php');

			}

			$id = $this->createNewChoice($_SESSION['system']['step']);

			$this->renumberChoices($_SESSION['system']['step']);

			$this->redirect('choice_edit.php?id='.$id);

		} else {

			$id = $this->requestData['id'];

		}
	
		$ck = $this->models->ChoiceKeystep->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		$data = $ck[0];


		if (isset($this->requestData['action']) && $this->requestData['action']=='delete') {
		// delete the entire choice, incl image (if any)
		
			if (!empty($data['choice_img']))
				@unlink($_SESSION['project']['paths']['project_media'].$data['choice_img']);

			$this->models->ChoiceKeystep->delete(
				array(
					'id' => $data['id'],						
					'project_id' => $this->getCurrentProjectId(),
				)
			);

			unset($_SESSION['system']['remainingTaxa']);

			$this->redirect('step_show.php');
		
		} elseif (isset($this->requestData['action']) && $this->requestData['action']=='deleteImage') {
		// delete just the image

			if (!empty($data['choice_img']))
				@unlink($_SESSION['project']['paths']['project_media'].$data['choice_img']);

			$this->models->ChoiceKeystep->save(
				array(
					'id' => $data['id'],						
					'choice_img' => 'null'
				)
			);
			
			unset($data['choice_img']);

		} // elseif (isset($this->requestData['action']) && $this->requestData['action']=='deleteImage')
			

		if ((isset($this->requestData['res_keystep_id']) || isset($this->requestData['res_taxon_id'])) && !$this->isFormResubmit()) {		
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
				
				$data['res_keystep_id'] = $this->requestData['res_keystep_id'];

				$data['res_taxon_id'] = $this->requestData['res_taxon_id'];
	
				$this->addMessage(_('Target saved.'));

			}

		}

		if (isset($this->requestDataFiles) && !$this->isFormResubmit() && $data['id']) {
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
						'id' => $data['id'],
						'project_id' => $this->getCurrentProjectId(),
						'choice_img' => $filesToSave[0]['name']
					)
				);
			
				if ($ck) {
				
					$this->addMessage(_('Image saved.'));
					
					$data['choice_img'] = $filesToSave[0]['name'];

				} else {

					@unlink($_SESSION['project']['paths']['project_media'].$filesToSave[0]['name']);

					$this->addError(_('Could not save image.'));

				}

			}

		}

		$k = $this->getKeySteps(array('idToExclude'=>$_SESSION['system']['step']));

		$this->getTaxonTree(null);
		
		$this->getRemainingTaxa();

   		$this->smarty->assign('languages',$_SESSION['project']['languages']);

   		$this->smarty->assign('defaultLanguage',$_SESSION['project']['languageList'][$_SESSION['project']['default_language_id']]);

		if (isset($data)) $this->smarty->assign('data',$data);

		$this->smarty->assign('steps',$k);

		$this->smarty->assign('taxa',$this->treeList);

		$this->smarty->assign('remainingTaxa',$this->remainingTaxaList);

   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();	
		
		
		
		
/*
		if (!isset($_SESSION['system']['step']) && !empty($this->requestData['id'])) {

			$ck = $this->models->ChoiceKeystep->_get(
				array(
					'id' => array(
						'id' => $this->requestData['id'],
						'project_id' => $this->getCurrentProjectId()
					)
				)
			);
			
			if ($ck[0]['keystep_id']) {

				$_SESSION['system']['step'] = $ck[0]['keystep_id'];

			}

		}

			} else {
			
				if ($this->isFormResubmit()) {

					if (isset($_SESSION['system']['choice'])) {

						$id = $_SESSION['system']['choice'];

					}

				}

			}

			$k = $this->getKeySteps(array('id' => $_SESSION['system']['step']));

			if (!empty($id)) {

				$ck = $this->models->ChoiceKeystep->_get(
					array(
						'id' => array(
							'id' => $id,
							'project_id' => $this->getCurrentProjectId()
						)
					)
				);

				$data = $ck[0];
				
				$_SESSION['system']['choice'] = $id;

		        $this->setPageName(sprintf(_('Edit choice "%s" for step %s: "%s"'),$data['title'],$k[0]['number'],$k[0]['title']));

			} else {

		        $this->setPageName(sprintf(_('Add choice for step %s: "%s"'),$k[0]['number'],$k[0]['title']));

			}


	
		} else {


		} // if (isset($_SESSION['system']['step']))



*/
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

	private function updateKeyPath($id,$number,$title,$choice) 
	{

		if (isset($_SESSION['system']['keyPath'])) {

			foreach((array)$_SESSION['system']['keyPath'] as $key => $val) {

				if ($val['id']==$id) break;

				if (!empty($val['id'])) $d[] = $val;

			}

		}
	

		$d[] = array('id' => $id,'number' => $number,'title' => $title,'choice' => null,'choiceTitle' => null);


		if (!empty($choice) && (count((array)$d)-2)>=0) {

			$ck = $this->models->ChoiceKeystep->_get(array('id' => $choice));
			
			$d[count((array)$d)-2]['choice'] = $choice;

			$d[count((array)$d)-2]['choiceTitle'] = $ck['title'];
			
		}

		$_SESSION['system']['keyPath'] = $d;

	}
	
	private function getKeyPath()
	{

		return isset($_SESSION['system']['keyPath']) ? $_SESSION['system']['keyPath'] : false;

	}
	
	private function moveChoice($id,$direction)
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


	private function renumberChoices($step)
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
		
		return $k;

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

            $this->getKeyChoiceContent();

        } elseif ($this->requestData['action'] == 'save_choice_title') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;

            $this->saveKeyChoiceContent($this->requestData,'title');

        } elseif ($this->requestData['action'] == 'save_choice_text') {

			$this->requestData = $this->requestData=='-1' ? null : $this->requestData;
	
            $this->saveKeyChoiceContent($this->requestData,'text');

        }
		
        $this->printPage();
    
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
	
			$this->models->ChoiceContentKeystep->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'choice_id' => $va['id'], 
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
				'res_taxon_id' => 'null'
			),
			array(
				'project_id' => $this->getCurrentProjectId(),
				'res_taxon_id' => $id
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

	private function createNewChoice($stepId)
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

    private function saveKeyChoiceContent ($data,$type='text')
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

	private function getKeyChoiceContent($language=null,$id=null)
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
	

}
















