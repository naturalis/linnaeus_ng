<?php

include_once ('Controller.php');

class KeysController extends Controller
{
    
    public $usedModels = array(
		'keystep',
		'choice_keystep'
    );
    
    public $usedHelpers = array(
		'file_upload_helper'
    );

    public $controllerPublicName = 'Dichotomous key';

	public $jsToLoad = array('key.js');

	public $cssToLoad = array('key.css','rank-list.css');

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
	
	public function stepStartAction()
	{

		//

	}

    public function stepShowAction()
    {

        $this->checkAuthorisation();

		if (isset($this->requestData['id']) || isset($_SESSION['system']['step'])) {
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

				$step = $k[0];

			} else {

				$this->redirect('step_edit.php');

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

		if (isset($this->requestData['ref_choice'])) {

			$_SESSION['system']['refChoice'] = $this->requestData['ref_choice'];
			
		}

		if (isset($_SESSION['system']['refChoice'])) {

			$this->updateKeyPath(null,null,null,$_SESSION['system']['refChoice']);

			$choice = $this->models->ChoiceKeystep->_get(array('id' => $_SESSION['system']['refChoice']));

			$this->smarty->assign('choice',$choice);

		}

		if (isset($this->requestData['title']) || isset($this->requestData['title'])) {
		
			$step = $this->requestData;

			// save step data
			if (empty($this->requestData['number'])) {
			
				$next = $this->getNextLowestStepNumber();

				$this->addError(sprintf(_('Step number is required. The lowest unused number is %s.'),$next));

				if (empty($this->requestData['id'])) $step['number'] = $next;

			} else
			if (!is_numeric($this->requestData['number'])) {

				$this->addError(sprintf(_('"%s" is not a number.'),$this->requestData['number']));

			} else {

				$k = $this->models->Keystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'number' => $this->requestData['number'],
							'id != ' => (empty($this->requestData['id']) ? -1 : $this->requestData['id'])
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

				}

			}

			if (empty($this->requestData['title'])) {

				$this->addError(_('Step title is required.'));

			}


			if (!$this->hasErrors()) {

				$k = $this->models->Keystep->save(
					array(
						'id' => !empty($this->requestData['id']) ? $this->requestData['id'] : null,
						'project_id' => $this->getCurrentProjectId(),
						'title' => $this->requestData['title'],
						'content' => $this->requestData['content'],
						'number' => !empty($this->requestData['number']) ? $this->requestData['number'] : 0,
					)
				);

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

					//$this->addMessage(_('Step data saved.'));
					
					$_SESSION['system']['step'] = $step['id'];
	
					$this->redirect('step_show.php');
					
				}

			}

		}

		if (!empty($this->requestData['id'])) {  

			$k = $this->models->Keystep->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $this->requestData['id']
					)
				)
			);
			
			$step = $k[0];

	        $this->setPageName(sprintf(_('Edit step "%s"'),$step['title']));

		} else {

	        $this->setPageName(_('New step'));
			
			$step['number'] = $this->getNextLowestStepNumber();

		}

		if (isset($step)) $this->smarty->assign('step',$step);
   
   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();

	}
	
    public function choiceEditAction()
    {

        $this->checkAuthorisation();

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

        
		if (isset($_SESSION['system']['step'])) {
		
			if (!empty($this->requestData['id'])) {
			
				$id = $this->requestData['id'];

			}

			if ((isset($this->requestData['title']) || isset($this->requestData['choice_txt'])) && !$this->isFormResubmit()) {

				if (empty($this->requestData['title'])) {

					$this->addError(_('A title is required.'));	
					
					$data = $this->requestData;

				} else {

					$ck = $this->models->ChoiceKeystep->save(
						array(
							'id' => !empty($this->requestData['id']) ? $this->requestData['id'] : null,
							'project_id' => $this->getCurrentProjectId(),
							'keystep_id' => $_SESSION['system']['step'],
							'show_order' => 99,
							'title' => $this->requestData['title'],
							'choice_txt' => $this->requestData['choice_txt'],
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
						)
					);

					if ($ck) {

						$id = !empty($this->requestData['id']) ? $this->requestData['id'] : $this->models->ChoiceKeystep->getNewId();

						$this->addMessage(_('Choice saved.'));
						
						$this->renumberChoices($_SESSION['system']['step']);

						if (isset($this->requestDataFiles) && !$this->isFormResubmit() && isset($id)) {
			
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
										'id' => $id,
										'project_id' => $this->getCurrentProjectId(),
										'choice_img' => $filesToSave[0]['name']
									)
								);
							
								if ($ck) {
								
									$this->addMessage(_('Image saved.'));
	
								} else {
				
									@unlink($_SESSION['project']['paths']['project_media'].$filesToSave[0]['name']);
	
									$this->addError(_('Could not save image.'));
				
								}
				
							}

						}
						
					} else {
	
						$this->addError(_('Could not save choice.'));
	
					}
					
				}

			} else {
			
				if ($this->isFormResubmit()) {

					if (isset($_SESSION['system']['choice'])) {

						$id = $_SESSION['system']['choice'];

					}

				}

			}

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

				$k = $this->getKeySteps(array('id' => $_SESSION['system']['step']));

		        $this->setPageName(sprintf(_('Add choice for step %s: "%s"'),$k[0]['number'],$k[0]['title']));

			}

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

			} // elseif (isset($this->requestData['action']) && $this->requestData['action']=='deleteImage')

	
		} else {

			$this->redirect('step_edit.php');

		} // if (isset($_SESSION['system']['step']))


		$k = $this->getKeySteps(array('idToExclude'=>$_SESSION['system']['step']));

		$this->getTaxonTree(null);

		if (isset($data)) $this->smarty->assign('data',$data);

		$this->smarty->assign('steps',$k);

		$this->smarty->assign('taxa',$this->_treeList);

   		$this->smarty->assign('keyPath',$this->getKeyPath());

        $this->printPage();

    }


	public function sectionAction()
	{
	
		$this->checkAuthorisation();

		unset($_SESSION['system']['step']);

		if (isset($this->requestData['action']) && $this->requestData['action']=='new') {

			$_SESSION['system']['keySubsection'] = true;
				
			$this->redirect('step_show.php');

		}
        
        $this->setPageName( _('Key subsections'));

		$l = $this->models->Keystep->_get(
			array(
				'id'=>array(
					'project_id'=>$this->getCurrentProjectId(),
					'is_start' => 0
				)
			)
		);
		
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
			
			if ($ck[0]['total']==0) $d[] = $val;

		}

		if (isset($d)) $this->smarty->assign('keySections',$d);

        $this->printPage();
	
	}


    public function mapAction()
    {

		$this->checkAuthorisation();
        
        $this->setPageName( _('Key map'));

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
		
		$pr = $this->getProjectRanks(array('keypathEndpoint'=>true,'forceLookup'=>true));

		foreach((array)$pr as $key => $val) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'rank_id' => $val['id']
					),
					'order' => 'taxon_order'
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
				
				}
			
			}

		}

		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

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



}

