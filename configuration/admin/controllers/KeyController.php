<?php

include_once ('Controller.php');
class KeyController extends Controller
{
    private $_taxaStepList;
    private $_stepList = array();
    private $_tempList = array();
    private $_counter = 0;
	private $_choiceList = array();
    public $usedModels = array(
        'keystep', 
        'keytree', 
        'content_keystep', 
        'content_keystep_undo', 
        'choice_keystep', 
        'choice_content_keystep', 
        'choice_content_keystep_undo'
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Dichotomous key';
    public $cacheFiles = array(
        'key-keyTaxa', 
        'key-taxonDivision*', 
        'tree-KeyTree', 
        'tree-ranks'
    );
    public $cssToLoad = array(
        'key.css', 
        'rank-list.css', 
        'key-tree.css', 
        'prettyPhoto/prettyPhoto.css', 
        'dialog/jquery.modaldialog.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'key.js', 
            'jit/jit.js', 
            'jit/key-tree.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
            'int-link.js',
			'dialog/jquery.modaldialog.js'
        ), 
        'IE' => array(
            'jit/Extras/excanvas.js'
        )
    );


    /**
     * Constructor, calls parent's constructor
     *
     * @access public
     */
    public function __construct ()
    {
        parent::__construct();
        
        $this->checkSettings();
    }



    /**
     * Destroys
     *
     * @access public
     */
    public function __destruct ()
    {
        parent::__destruct();
    }



    public function indexAction ()
    {
        $this->checkAuthorisation();
        
        $this->checkEndPointsExist();
        
        $this->setPageName($this->translate('Index'));
        
        unset($_SESSION['admin']['system']['keyPath']);
        
        $this->printPage();
    }

    public function stepShowAction ()
    {
        $this->checkAuthorisation();

       // node is the id of a step that is called directly, i.e. not through stepping
        if ($this->rHasVal('node')) {

            // generate a complete key tree
            $tree = $this->getKeyTree();

            $this->_stepList = array();

            // find the node in the keyTree and build an array of the path
            // leading to it
            $this->findNodeInTree(array(0 => $tree), $this->requestData['node']);

			// loop through the array and add each element to the keyPath
            foreach ((array) $this->_stepList as $key => $val) {

                $this->updateKeyPath($val);

			}

            // get the step itself, which always is the last element in the
            // keyPath array
            $step = $this->getKeystep($val['id']);
        }
        // request for specific step
        else if ($this->rHasId()) {
            
            $step = $this->getKeystep($this->requestData['id']);
        }
        // looking for the start step
        else {
            $step = $this->getStartKeystep();
            
			// didn't find it, create it
            if (!$step) {
                $this->redirect('step_edit.php?id=' . $this->createNewKeystep(array(
                    'is_start' => 1
                )));
            }
        }

		if ($step)
		{
            
            // move choices up and down
            if ($this->rHasVal('move') && $this->rHasVal('direction') && !$this->isFormResubmit()) {
                $this->moveKeystepChoice($this->requestData['move'], $this->requestData['direction']);
            }
            
            // get step's choices
            $choices = $this->getKeystepChoices($step['id'], true);
            
			if (!$this->rHasVal('nopath','1')) {
			
				// update the key's breadcrumb trail
				$this->updateKeyPath(
				array(
					'id' => $step['id'], 
					'number' => $step['number'], 
					'title' => $step['title'], 
					'is_start' => $step['is_start'], 
					'choice' => isset($this->requestData['choice']) ? $this->requestData['choice'] : null
				));
				
			}
            
            $step['content'] = nl2br($step['content']);
            
            $this->smarty->assign('step', $step);
            
            $this->smarty->assign('choices', $choices);
            
            $this->smarty->assign('maxChoicesPerKeystep', $this->controllerSettings['maxChoicesPerKeystep']);
        }
        else
		{
            
            $this->addError($this->translate('Non-existant keystep ID. Please go back and change the target for the choice.'));
        }
        
        $this->setPageName(sprintf($this->translate('Show key step %s'), $step['number']));
        
        $c = $this->didKeyTaxaChange();
        
        if (!$c && isset($step['id']))
		{
            
            $div = $this->getTaxonDivision($step['id']);
            $this->getTaxonTree();
            
            foreach ((array) $div['remaining'] as $key => $val)
                $div['remaining'][$key] = array(
                    'id' => $val, 
                    'taxon' => $this->treeList[$val]['taxon']
                );

            foreach ((array) $div['excluded'] as $key => $val)
                $div['excluded'][$key] = array(
                    'id' => $val, 
                    'taxon' => $this->treeList[$val]['taxon']
                );
            
            $this->customSortArray($div['remaining'], array(
                'key' => 'taxon'
            ));
            $this->customSortArray($div['excluded'], array(
                'key' => 'taxon'
            ));
            
            $this->smarty->assign('taxonDivision', $div);
        }
        
        $this->smarty->assign('didKeyTaxaChange', $c);
        
        $this->smarty->assign('keyPath', $this->getKeyPath());

        $this->smarty->assign('stepsLeadingToThisOne', $this->getStepsLeadingToThisOne($step['id']));
        
        $this->smarty->assign('suppressPath', $this->rHasVal('nopath','1'));
        
        $this->printPage();
    }

    public function stepEditAction ()
    {
        $this->checkAuthorisation();
        
        // create a new step when no id is specified
        if (!$this->rHasId()) {

            $id = $this->createNewKeystep();
            
            $this->renumberKeySteps(array(
                0 => $this->getKeyTree()
            ));
            
            if ($this->rHasVal('insert')) {
                
                $this->insertKeyStep($id, $this->requestData['insert']);
            }
            
            if ($this->rHasVal('ref_choice')) {
                // url was called from the 'new step' option of a choice: set
                // the new referring step id

                $this->models->ChoiceKeystep->save(array(
                    'id' => $this->requestData['ref_choice'], 
                    'res_keystep_id' => $id
                ));
                
                $this->updateKeyPath(array(
                    'choice' => $this->requestData['ref_choice']
                ));
            }
            
            // redirect to self with id
            // $this->redirect('step_edit.php?id='.$id);
            $this->redirect('step_show.php?id=' . $id . ($this->rHasVal('insert') ? '&insert=' . $this->requestData['insert'] : ''));
        }
        // id has been specified
        else {

            // delete L2-legacy image
            if ($this->rHasVal('action', 'deleteImage') || $this->rHasVal('action', 'deleteAllImages')) {
            
                $step = $this->getKeystep($this->requestData['id']);

                $this->deleteLegacyImage($this->rHasVal('action', 'deleteImage') ? $step : 'all');
                
                $this->redirect('step_show.php?id='.$step['id']);
                                 
            } else
			// delete step
            if ($this->rHasVal('action', 'delete')) {

                $this->deleteKeystep($this->requestData['id']);
                
                $entry = $this->getPreviousKeypathEntry($this->requestData['id']);
                
                $this->redirect($entry ? 'step_show.php?id=' . $entry['id'] : 'index.php');
            }
            
            
            // get step data
            $step = $this->getKeystep($this->requestData['id']);
            
            $this->setPageName(sprintf($this->translate('Edit step %s'), $step['number']));
            
            //// saving the number (all the rest is done through ajax)
            // number can now no longer be edited
            if ($this->rHasVal('action', 'save') && !$this->isFormResubmit()) {
            
            	//$this->redirect('step_show.php?id=' . $this->requestData['id']);
                
            	// no number specified
                if (empty($this->requestData['number'])) {

                    $next = $this->getNextLowestStepNumber();
                    
                    $this->addError(sprintf($this->translate('Step number is required. The saved number for this step is %s. The lowest unused number is %s.'), $step['number'], $next));
                }
                // non-numeric number specified
                elseif (!is_numeric($this->requestData['number'])) {

                    $this->addError(sprintf($this->translate('"%s" is not a number.'), $this->requestData['number']));
                }
                // existing number specified
                else {

                    $k = $this->models->Keystep->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'number' => $this->requestData['number'], 
                            'id != ' => $this->requestData['id']
                        ), 
                        'columns' => 'count(*) as total'
                    ));
                    
                    if ($k[0]['total'] != 0) {
                        // doublure

                        $this->addError(sprintf($this->translate('A step with number %s already exists. The lowest unused number is %s.'), $this->requestData['number'], $this->getNextLowestStepNumber()));
                    }
                    // unique numeric number
                    else {
						// don't update if unchanged
                        if ($this->requestData['number'] != $step['number']) {

                            $this->models->Keystep->update(
                            array(
                                'number' => $this->requestData['number']
                            ),
							array(
                                'id' => $this->requestData['id'], 
                                'project_id' => $this->getCurrentProjectId(), 
                            ));
                            
                            // two steps below unnecessary because of redirect
                            // to step_show
                            $step['number'] = $this->requestData['number'];
                            $this->addMessage($this->translate('Number saved.'));
                        } else {
							
							$this->addMessage($this->translate('Saved.'));
							
						}
                        
                        //$this->redirect('step_show.php?id=' . $this->requestData['id']);
                    }
                }

            }

		}
        
        if (isset($step))
            $this->smarty->assign('step', $step);
        
        $this->smarty->assign('languages', $this->getProjectLanguages());
        
        $this->smarty->assign('defaultLanguage', $_SESSION['admin']['project']['languageList'][$this->getDefaultProjectLanguage()]);
        
        $this->smarty->assign('keyPath', $this->getKeyPath());
        
        $this->printPage();
    }

    public function choiceEditAction ()
    {
        $this->checkAuthorisation();

		// create a new choice when no id is specified
        if (!$this->rHasId()) {
            
            // need a step to which the choice belongs
            if (!$this->rHasVal('step'))
                $this->redirect('step_show.php');
                
            // cache becomes obsolete
            $this->clearCache($this->cacheFiles);
            
            // create new choice & renumber
            $id = $this->createNewKeystepChoice($this->requestData['step']);
            $this->renumberKeystepChoices($this->requestData['step']);
            
            // redirecting to protect against resubmits
            $this->redirect('choice_edit.php?id=' . $id);
        }
        
        // resolve id, choice and step
        $id = $this->requestData['id'];
        $choice = $this->getKeystepChoice($id);
        $step = $this->getKeystep($choice['keystep_id']);

        $this->setPageName(sprintf($this->translate('Edit choice "%s" for step %s'), $choice['show_order'], $step['number'], $step['title']));
        
        // delete the complete choice, incl image (if any)
        if ($this->rHasVal('action', 'delete')) {
            
            if (!empty($choice['choice_img']))
                @unlink($_SESSION['admin']['project']['paths']['project_media'] . $choice['choice_img']);
            
            $this->deleteKeystepChoice($choice['id']);
            
            $this->clearCache($this->cacheFiles);
            
            unset($_SESSION['admin']['system']['remainingTaxa']);
            
            $this->redirect('step_show.php?id=' . $choice['keystep_id']);

        } // delete just the image
        elseif ($this->rHasVal('action', 'deleteImage')) {
            
            if (!empty($choice['choice_img']))
                @unlink($_SESSION['admin']['project']['paths']['project_media'] . $choice['choice_img']);
            
            $this->models->ChoiceKeystep->save(array(
                'id' => $choice['id'], 
                'choice_img' => 'null', 
                'choice_image_params' => 'null'
            ));
            
            unset($choice['choice_img']);
        }

		// save new target
        if (($this->rHasVal('res_keystep_id') || $this->rHasVal('res_taxon_id')) && !$this->isFormResubmit()) {
            

            $this->clearCache($this->cacheFiles);
            
			if ($this->rHasVal('res_keystep_id','-1'))
				$newStepId = $this->createNewKeystep();
			else 
				$newStepId = $this->requestData['res_keystep_id'];
			
			
            $ck = $this->models->ChoiceKeystep->update(
            array(
                'res_keystep_id' => $newStepId === '0' ? 'null' : $newStepId, 
                'res_taxon_id' => $newStepId !== '0' ? 'null' : ($this->requestData['res_taxon_id'] === '0' ? 'null' : $this->requestData['res_taxon_id'])
            ), array(
                'id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            if ($this->models->ChoiceKeystep->getAffectedRows() > 0) {
                
                if ($this->requestData['res_taxon_id'] !== '0') {
                    
                    $this->setKeyTaxaChanged();
                }
                
                $choice['res_keystep_id'] = $this->requestData['res_keystep_id'];
                
                $choice['res_taxon_id'] = $this->requestData['res_taxon_id'];
                
                // $this->addMessage($this->translate('Saved.'));
            }
            
			// save choice image
            if ($choice['id'] && isset($this->requestDataFiles) && !$this->isFormResubmit()) {

                $filesToSave = $this->getUploadedMediaFiles();
                
                if ($filesToSave) {
                    
                    $ck = $this->models->ChoiceKeystep->save(
                    array(
                        'id' => $choice['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'choice_img' => $filesToSave[0]['name']
                    ));
                    
                    if ($ck) {
                        
                        $this->addMessage($this->translate('Image saved.'));
                        
                        $choice['choice_img'] = $filesToSave[0]['name'];
                                                
                    }
                    else {
                        
                        @unlink($_SESSION['admin']['project']['paths']['project_media'] . $filesToSave[0]['name']);
                        
                        $this->addError($this->translate('Could not save image.'));
                    }
                }
            }
            
            $this->redirect('step_show.php?id=' . $step['id']);
        }
        
        if (isset($choice))
            $this->smarty->assign('data', $choice);
        
        $this->smarty->assign('languages', $this->getProjectLanguages());
        
        $this->smarty->assign('defaultLanguage', $_SESSION['admin']['project']['languageList'][$this->getDefaultProjectLanguage()]);
        
        $this->smarty->assign('steps', $this->getKeysteps(array(
            'idToExclude' => $choice['keystep_id']
        )));

        $this->smarty->assign('taxa', $this->getRemainingTaxa());

        $this->smarty->assign('keyPath', $this->getKeyPath());
        
        $this->smarty->assign('includeHtmlEditor', true);
        
        $this->printPage();

    }

    public function insertAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->rHasVal('step') && $this->rHasVal('action', 'insert') && !$this->isFormResubmit()) {
            
            $this->clearCache($this->cacheFiles);
            
            $res = $this->insertKeyStepBeforeKeyStep($this->requestData['source'], $this->requestData['step']);
            
            $this->renumberKeySteps(array(
                0 => $this->getKeyTree()
            ));
            
            $step = $this->getKeystep($res['newStepId']);
            
            $this->removeLastKeyPathEntry();
            
            $this->updateKeyPath(
            array(
                'id' => $step['id'], 
                'number' => $step['number'], 
                'title' => $step['title'], 
                'is_start' => $step['is_start'], 
                'choice' => null
            ));
            
            $this->redirect('step_show.php?id=' . $res['newStepId']);
        }
        else if ($this->rHasVal('action', 'renumber') && !$this->isFormResubmit()) {
            
            $this->clearCache($this->cacheFiles);
            
            $this->renumberKeySteps(array(
                0 => $this->getKeyTree()
            ));
            
            $this->redirect('step_show.php');
        }
        
        $step = $this->getKeystep($this->requestData['id']);
        
        $this->setPageName(sprintf($this->translate('Insert a step before step %s'), $step['number']));
        
        $ck = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'res_keystep_id' => $this->requestData['id']
            ), 
            'columns' => 'keystep_id'
        ));
        
        foreach ((array) $ck as $key => $val)
            $d[] = $this->getKeystep($val['keystep_id']);
        
        $this->customSortArray($d, array(
            'key' => 'number'
        ));
        
        $this->smarty->assign('hideInsertMenu', true);
        
        $this->smarty->assign('step', $step);
        
        $this->smarty->assign('prevStep', isset($this->requestData['c']) ? $this->requestData['c'] : null);
        
        $this->smarty->assign('sourceSteps', $d);
        
        $this->printPage();
    }

    public function sectionAction ()
    {
        $this->checkAuthorisation();
        
        $this->clearCache($this->cacheFiles);
        
        if ($this->rHasVal('action', 'setstart') && $this->rHasId()) {
            
            $this->setKeyStartStep($this->requestData['id']);
            
            $this->redirect('step_show.php');
        }
        else 
		// start a new subsection: create a new step and redirect to edit
		if ($this->rHasVal('action', 'new')) {

            $this->redirect('step_edit.php?id=' . $this->createNewKeystep());
        }
        
        $this->cleanUpChoices();
        
        $this->setPageName($this->translate('Key sections'));

		$keySections = $this->getKeySections();

		$this->smarty->assign('keySections', $keySections);
        
        $this->printPage();
    }

    public function mapAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Key map'));
        
        $key = $this->getKeyTree();
        
        $this->smarty->assign('json', json_encode($key));
        
        $this->printPage();
    }

    public function rankAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxon ranks in key'));
        
        $pr = $this->getProjectRanks(array(
            'lowerTaxonOnly' => false
        ));
        
        if ($this->rHasVal('keyRankBorder') && isset($pr) && !$this->isFormResubmit()) {
            
            $endPoint = false;
            
            foreach ((array) $pr as $key => $val) {
                
                if ($val['rank_id'] == $this->requestData['keyRankBorder'])
                    $endPoint = true;
                
                $this->models->ProjectRank->save(array(
                    'id' => $val['id'], 
                    'keypath_endpoint' => $endPoint ? 1 : 0
                ));
            }
            
            $this->addMessage($this->translate('Saved.'));
            
            $pr = $this->getProjectRanks(array(
                'lowerTaxonOnly' => false, 
                'forceLookup' => true
            ));
        }
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->printPage();
    }

    public function orphansAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxa not part of the key'));
        
        $this->smarty->assign('taxa', $this->getRemainingTaxa());
        
        $this->printPage();
    }

    public function deadEndsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Key validation'));
		
		$this->cleanUpChoices();

        $k = $this->getKeysteps();
		
		$deadSteps =  $sadSteps = array();

        foreach ((array) $k as $key => $val) {

            $kc = $this->getKeystepChoices($val['id']);
            
            if (count((array) $kc) == 0)
                $deadSteps[] = $val;
            if (count((array) $kc) == 1)
                $sadSteps[] = $val;
        }

		$deadChoices = $this->models->ChoiceKeystep->freeQuery("
			SELECT
				_a.*, 
				_b.title, 
				_c.number, 
				_d.choice_txt
				
			from %PRE%choices_keysteps _a

			left join %PRE%content_keysteps _b
				on _b.keystep_id = _a.keystep_id
				and _b.language_id = ".$this->getDefaultProjectLanguage()."
				and _a.project_id = _b.project_id

			left join %PRE%keysteps _c
				on _c.id = _a.keystep_id
				and _a.project_id = _c.project_id
				
			left join %PRE%choices_content_keysteps _d
				on _a.id = _d.choice_id
				and _a.project_id = _d.project_id
				
			where _a.project_id = " . $this->getCurrentProjectId() ."
				and (_a.res_keystep_id = -1 or _a.res_keystep_id is null) and _a.res_taxon_id is null 
				
			order by 
				_a.keystep_id, _a.id
			");

        $this->smarty->assign('deadSteps',$deadSteps);
        
        $this->smarty->assign('sadSteps',$sadSteps);
        
        $this->smarty->assign('deadChoices', $deadChoices);
        
        $this->printPage();
    }


    public function storeAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Store key tree'));
        
        if ($this->rHasVal('action', 'store') && !$this->isFormResubmit()) {
            
            $k = $this->saveKeyTree();
            
            if ($k === true) {
                
                $this->setKeyTaxaChanged();
                
                $this->addMessage($this->translate('Key tree saved'));
                
                if ($this->rHasVal('step'))
                    $this->addMessage('<a href="step_show.php?step=' . $this->requestData['step'] . '">' . $this->translate('Back to key') . '</a>');
            }
            else {
                
                $this->addError($k);
            }
        }
        
        if ($this->rHasVal('step'))
            $this->smarty->assign('step', $this->requestData['step']);
        
        $this->smarty->assign('keyinfo', $this->getKeyInfo());
        
        $this->smarty->assign('didKeyTaxaChange', $this->didKeyTaxaChange());
        
        $this->printPage();
    }


    public function cleanUpAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Clean up'));
        
        if ($this->rHasVal('action', 'clean') && !$this->isFormResubmit()) {
            
            $this->cleanUpChoices();
	        $this->smarty->assign('processed', true);
			$this->addMessage($this->translate('Clean up done'));
        }
        
        $this->printPage();
    }

    public function typeAction ()
    {
        $this->checkAuthorisation();
		
		$this->setPageName($this->translate('Set runtime key type'));
        
        if ($this->rHasVal('keytype') && !$this->isFormResubmit()) {
            
            $this->saveSetting(array(
                'name' => 'keytype', 
                'value' => $this->requestData['keytype']
            ));
            
            $this->addMessage('Saved');
        }
        
        $this->smarty->assign('keytype', $this->getSetting('keytype'));
        
       
        
        $this->printPage();
    }

    public function contentsAction ()
    {
        $this->checkAuthorisation();

		$this->setPageName($this->translate('Contents'));

        $list = $this->getLookupList();
        
        $pagination = $this->getPagination($list, 25);
        
        $this->smarty->assign('prevStart', $pagination['prevStart']);
        
        $this->smarty->assign('nextStart', $pagination['nextStart']);
        
        $this->smarty->assign('list', $pagination['items']);
        
        $this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!isset($this->requestData['action']))
            return;
        
        if ($this->requestData['action'] == 'get_keystep_content') {
            
            $this->getKeystepContent();
        }
        elseif ($this->requestData['action'] == 'save_keystep_content') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->saveKeystepContent($this->requestData);
        }
        elseif ($this->requestData['action'] == 'get_keystep_undo') {
            
            $this->getKeystepUndo($this->requestData);
        }
        elseif ($this->requestData['action'] == 'get_key_choice_content') {
            
            $this->getKeystepChoiceContent();
        }
        elseif ($this->requestData['action'] == 'save_key_choice_content') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->saveKeystepChoiceContent($this->requestData);
        }
        elseif ($this->requestData['action'] == 'get_key_choice_undo') {
            
            $this->getKeystepChoiceUndo($this->requestData);
        }
        
        $this->printPage();
    }

    public function previewAction ()
    {
        $this->redirect('../../../app/views/key/index.php?p=' . $this->getCurrentProjectId() . '&step=' . $this->requestData['step']);
    }

    private function setStepsPerTaxon ($choice)
    {
        $this->_taxaStepList[] = $choice['keystep_id'];
        
        // get choices that have the keystep the choice belongs to as target
        $cks = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'res_keystep_id' => $choice['keystep_id']
            )
        ));
        
        foreach ((array) $cks as $key => $val)
            $this->setStepsPerTaxon($val);
    }

    private function removeLastKeyPathEntry ()
    {
        return array_pop($_SESSION['admin']['system']['keyPath']);
    }

    private function updateKeyPath ($params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        $number = isset($params['number']) ? $params['number'] : null;
        $title = isset($params['title']) ? $params['title'] : null;
        $is_start = isset($params['is_start']) ? $params['is_start'] : null;
        $choice = isset($params['choice']) ? $params['choice'] : null;
        
        if (isset($_SESSION['admin']['system']['keyPath'])) {
            
            foreach ((array) $_SESSION['admin']['system']['keyPath'] as $key => $val) {
                
                if ($val['id'] == $id)
                    break;
                
                if (!empty($val['id']))
                    $d[] = $val;
            }
        }
        
        $d[] = array(
            'id' => $id, 
            'number' => $number, 
            'title' => $title, 
            'is_start' => $is_start, 
            'choice' => null, 
            'choice_marker' => null
        );
        
        if (!empty($choice) && (count((array) $d) - 2) >= 0) {
            
            $choice = $this->getKeystepChoice($choice);
            
            $d[count((array) $d) - 2]['choice'] = $choice;
            
            $d[count((array) $d) - 2]['choice_marker'] = isset($choice['marker']) ? $choice['marker'] : '';
        }
        
        $_SESSION['admin']['system']['keyPath'] = $d;
    }

    private function getKeyPath ()
    {
        return isset($_SESSION['admin']['system']['keyPath']) ? $_SESSION['admin']['system']['keyPath'] : false;
    }

    private function getPreviousKeypathEntry ($id = false, $stepsBack = 1)
    {
        $kp = $this->getKeyPath();
        
        $c = count((array) $kp);
        
        if ($id) {
            
            for ($i = ($c - 1); $i >= 0; $i--) {
                
                if (isset($kp[$i + $stepsBack]) && $kp[$i + $stepsBack]['id'] == $id) {
                    
                    return $kp[$i];
                }
            }
            
            return false;
        }
        else {
            
            return isset($kp[$c - ($stepsBack + 1)]) ? $kp[$c - ($stepsBack + 1)] : false;
        }
    }

    private function getRemainingTaxa ()
    {
		
        $q = "
				select distinct _a.id, _a.taxon, _a.rank_id, _b.res_taxon_id, _d.rank, _c.lower_taxon, _c.keypath_endpoint
					from %PRE%taxa _a
					left join %PRE%choices_keysteps _b
						on _a.id = _b.res_taxon_id
						and _a.project_id = _b.project_id 
					left join %PRE%projects_ranks _c
						on _a.rank_id = _c.id
						and _a.project_id = _c.project_id 
					left join %PRE%ranks _d
						on _c.rank_id = _d.id
					where _a.project_id = " . $this->getCurrentProjectId() . "
					order by _a.taxon
			";


        return $this->models->Taxon->freeQuery($q);	

    }

    private function getKeyTree ($refId = null, $choice = null)
    {
        $s = $refId == null ? $this->getStartKeystep() : $this->getKeystep($refId);
        
        $step = array(
            'id' => $s['id'], 
            'name' => (isset($choice['marker']) ? '(' . $choice['marker'] . ') ' : '') . $s['number'] . '. ' . $s['title'], 
            'src_choice' => isset($choice['txt']) ? $choice['txt'] : null,
            'type' => 'step', 
            'data' => array(
                'number' => $s['number'], 
                'title' => $s['title'], 
                'is_start' => $s['is_start'], 
                'node' => $this->_counter++, 
                'referringChoiceId' => $choice['id']
            )
        );
        
        // $this->_stepList check is protection against circular reference
        if (!isset($this->_stepList[$s['id']])) {
            
            $this->_stepList[$step['id']] = true;
            
            $ck = $this->models->ChoiceKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'keystep_id' => $step['id']
                ), 
                'order' => 'show_order'
            ));
            
            foreach ((array) $ck as $key => $val) {
				
				$cck = $this->models->ChoiceContentKeystep->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'choice_id' => $val['id'], 
							'language_id' => $this->getDefaultProjectLanguage()
						)
					));
				
				if ($cck[0]['choice_txt'])
					$txt = substr(strip_tags($cck[0]['choice_txt']),0,75).(strlen(strip_tags($cck[0]['choice_txt'])) > 75 ? '...' : '' );
				else
					$txt = null;

				
                if (isset($val['res_taxon_id'])) {
                    
                    $t = $this->getTaxonById($val['res_taxon_id']);

                    $this->tmp = is_null($this->tmp) ? 0 : $this->tmp;
                    
                    $step['children'][] = array(
                        'id' => 't' . $this->tmp++,  // $t['id'], // using the
                        // id
                        // makes for weird
                        // loopbacks in the map
                        // when
                        // the same id appears
                        // multiple times
						'src_choice' => $txt,
                        'type' => 'taxon', 
                        'data' => array(
                            'number' => 't' . $t['id'], 
                            'title' => '&rarr; ' . $t['taxon'], 
                            'taxon' => $t['label'], 
                            'id' => $t['id']
                        ), 
                        'name' => '(' . $this->showOrderToMarker($val['show_order']) . ') ' . '<i>' . $t['taxon'] . '</i>'
                    );
                }
                else if (isset($val['res_keystep_id']) && $val['res_keystep_id'] != -1) {
                    
                    $step['children'][] = $this->getKeyTree(
						$val['res_keystep_id'], 
						array(
							'id' => $val['id'], 
							'marker' => $this->showOrderToMarker($val['show_order']),
							'txt' => $txt,
						));
                }
            }
        }
        
        return $step;
    }



    private function getKeysteps ($params = null)
    {
        $id = isset($params['id']) ? $params['id'] : false;
        
        $idToExclude = isset($params['idToExclude']) ? $params['idToExclude'] : false;
        
        $isStart = isset($params['isStart']) ? $params['isStart'] : false;
        
        $includeContent = isset($params['includeContent']) ? $params['includeContent'] : true;
        
        $p['columns'] = isset($params['columns']) ? $params['columns'] : '*';
        
        $p['order'] = isset($params['order']) ? $params['order'] : 'number';
        
        $p['id']['project_id'] = $this->getCurrentProjectId();
        
        if ($id)
            $p['id']['id'] = $id;
        
        if ($idToExclude)
            $p['id']['id !='] = $idToExclude;
        
        if ($isStart)
            $p['id']['isStart'] = $isStart;
        
        $k = $this->models->Keystep->_get($p);
        
        foreach ((array) $k as $key => $val) {
            
            $kc = $this->getKeystepContent($this->getDefaultProjectLanguage(), $val['id']);
            
            $k[$key]['title'] = $kc['title'];
            
            $k[$key]['content'] = $kc['content'];
        }
        
        return $k;
    }



    private function getKeystep ($id = null)
    {
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (empty($id)) {
            
            return;
        }
        else {
            
            $k = $this->models->Keystep->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                )
            ));
            
            if (!$k)
                return false;
            
            $step = $k[0];
            
            $kc = $this->getKeystepContent($this->getDefaultProjectLanguage(), $step['id']);
            
            $step['title'] = $kc['title'];
            
            $step['content'] = $kc['content'];
            
            $this->smarty->assign('returnText', json_encode($step));
            
            return $step;
        }
    }



    private function getStartKeystep ()
    {
        $k = $this->models->Keystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'is_start' => 1
            )
        ));
        
        if ($k) {
            
            $kc = $this->getKeystepContent($this->getDefaultProjectLanguage(), $k[0]['id']);
            
            $k[0]['title'] = $kc['title'];
            
            $k[0]['content'] = $kc['content'];
        }
        
        return $k[0];
    }



    private function getKeystepContent ($language = null, $id = null)
    {
        $language = isset($language) ? $language : $this->requestData['language'];
        
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (empty($language) || empty($id)) {
            
            return;
        }
        else {
            
            $ck = $this->models->ContentKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'keystep_id' => $id, 
                    'language_id' => $language
                ), 
                'columns' => 'title,content'
            ));
            
            $this->smarty->assign('returnText', json_encode($ck[0]));
            
            return $ck[0];
        }
    }



    private function saveKeystepContent ($data)
    {
        if (empty($data['language'])) {
            
            return;
        }
        else {
            
            $ck = $this->models->ContentKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'keystep_id' => $data['id'], 
                    'language_id' => $data['language']
                )
            ));
            
            $newContent = trim($data['content'][1]) == '' ? 'null' : trim($data['content'][1]);
            $newTitle = trim($data['content'][0]) == '' ? 'null' : trim($data['content'][0]);
            
            $d = array(
                'id' => isset($ck[0]['id']) ? $ck[0]['id'] : null, 
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $data['id'], 
                'language_id' => $data['language'], 
                'title' => $newTitle, 
                'content' => $newContent
            );
            
            // initiate save to undo buffer
            $this->models->ContentKeystep->setRetainBeforeAlter();
            
            // save step
            $this->models->ContentKeystep->save($d);
            
            // save to undo buffer
            $this->saveOldKeyData($this->models->ContentKeystep->getRetainedData(), $d, 'manual');
            
            $this->smarty->assign('returnText', $this->models->ContentKeystep->getAffectedRows() > 0 ? $this->translate('saved') : '');
        }
    }

    private function deleteLegacyImage ($step)
    { 
        $where['project_id'] = $this->getCurrentProjectId();
        
        if (is_array($step) && !empty($step['image'])) {

            @unlink($_SESSION['admin']['project']['paths']['project_media'] . $step['image']);
            
            $where['id'] = $step['id'];
            
        }  
      
        $this->models->Keystep->update(
            array('image' => 'null'), 
            $where
        );
        
    }
       
    private function getNextLowestStepNumber ()
    {
        $k = $this->models->Keystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'number', 
            'order' => 'number'
        ));
        
        $prev = 0;
        $next = false;
        
        foreach ((array) $k as $key => $val) {
            
            if ($val['number'] - $prev > 1) {
                
                $next = $prev + 1;
                
                break;
            }
            else {
                
                $prev = $val['number'];
            }
        }
        
        if (!$next)
            $next = $prev + 1;
        
        return $next;
    }



    private function createNewKeystep ($data = null)
    {
        $this->models->Keystep->save(
        array(
            'id' => null, 
            'project_id' => $this->getCurrentProjectId(), 
            'number' => !empty($data['number']) ? $data['number'] : $this->getNextLowestStepNumber(), 
            'is_start' => !empty($data['is_start']) ? $data['is_start'] : 0
        ));
        
        return $this->models->Keystep->getNewId();
    }



    private function deleteKeystepChoice ($id)
    {
        $this->models->ChoiceContentKeystep->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'choice_id' => $id
        ));
        
        $this->models->ChoiceContentKeystepUndo->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'choice_id' => $id
        ));
        
        $this->models->ChoiceKeystep->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
        
        $this->setKeyTaxaChanged();
    }



    private function deleteKeystep ($id)
    {
        $ck = $this->models->ChoiceKeystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $id
            )
        ));
        
        $hadTaxa = false;
        
        foreach ((array) $ck as $key => $val) {
            
            $hadTaxa = $hadTaxa == true || !empty($val['res_taxon_id']);
            
            $this->deleteKeystepChoice($val['choice_id']);
        }
        
        if ($hadTaxa)
            $this->setKeyTaxaChanged();
        
        $this->models->ChoiceKeystep->update(array(
            'res_keystep_id' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'res_keystep_id' => $id
        ));
        
        $this->models->ContentKeystep->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'keystep_id' => $id
        ));
        
        $this->models->ContentKeystepUndo->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'keystep_id' => $id
        ));
        
        $this->models->Keystep->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
    }



    private function createNewKeystepChoice ($stepId)
    {
        if (empty($stepId))
            return;
        
        $this->models->ChoiceKeystep->save(array(
            'id' => null, 
            'project_id' => $this->getCurrentProjectId(), 
            'keystep_id' => $stepId, 
            'show_order' => 99
        ));
        
        return $this->models->ChoiceKeystep->getNewId();
    }



    private function getKeystepChoices ($step, $formatHtml = false)
    {
        $choices = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $step
            ), 
            'order' => 'show_order'
        ));
        
        foreach ((array) $choices as $key => $val) {
            
            $kcc = $this->getKeystepChoiceContent($this->getDefaultProjectLanguage(), $val['id']);
            
            if (isset($kcc['title']))
                $choices[$key]['title'] = $kcc['title'];
            
            if (isset($kcc['choice_txt']))
                $choices[$key]['choice_txt'] = $formatHtml ? nl2br($kcc['choice_txt']) : $kcc['choice_txt'];
            
            if (!empty($val['res_keystep_id']) && $val['res_keystep_id'] != 0) {
                
                if ($val['res_keystep_id'] == '-1') {
                    
                    $choices[$key]['target'] = $this->translate('(new step)');
                }
                else {
                    
                    $k = $this->getKeystep($val['res_keystep_id']);
                    
                    if (isset($k['title']))
                        $choices[$key]['target'] = $k['title'];
                    
                    if (isset($k['number']))
                        $choices[$key]['target_number'] = $k['number'];
                }
            }
            elseif (!empty($val['res_taxon_id'])) {
                
                $t = $this->models->Taxon->_get(array(
                    'id' => $val['res_taxon_id']
                ));
                
                if (isset($t['taxon']))
                    $choices[$key]['target'] = $t['taxon'];
            }
            else {
                
                $choices[$key]['target'] = $this->translate('undefined');
            }
            
            $choices[$key]['marker'] = $this->showOrderToMarker($val['show_order']);
        }
        
        return $choices;
    }

    private function getKeystepChoice ($id)
    {
        $ck = $this->models->ChoiceKeystep->_get(array(
            'id' => array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        $choice = $ck[0];
        
        $k = $this->models->Keystep->_get(array(
            'id' => $choice['keystep_id']
        ));
        
        $choice['keystep_number'] = $k['number'];
        
        $kcc = $this->getKeystepChoiceContent($this->getDefaultProjectLanguage(), $choice['id']);
        
        if (isset($kcc['choice_txt']))
            $choice['choice_txt'] = $kcc['choice_txt'];
        
        if (!empty($choice['res_keystep_id']) && $choice['res_keystep_id'] != 0) {
            
            if ($choice['res_keystep_id'] == '-1') {
                
                $choice['target'] = $this->translate('(new step)');
            }
            else {
                
                $k = $this->models->Keystep->_get(array(
                    'id' => $choice['res_keystep_id']
                ));
                
                if (isset($k['number']))
                    $choice['target_number'] = $k['number'];
            }
        }
        elseif (!empty($choice['res_taxon_id'])) {
            
            $t = $this->models->Taxon->_get(array(
                'id' => $choice['res_taxon_id']
            ));
            
            if (isset($t['taxon']))
                $choice['target'] = $t['taxon'];
        }
        else {
            
            $choice['target'] = $this->translate('undefined');
        }
        
        $choice['marker'] = $this->showOrderToMarker($choice['show_order']);
        
        return $choice;
    }

    private function saveKeystepChoiceContent ($data)
    {
        if (empty($data['language'])) {
            
            return;
        }
        else {
            
            $ck = $this->models->ChoiceContentKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'choice_id' => $data['id'], 
                    'language_id' => $data['language']
                )
            ));
            
            $d = array(
                'id' => isset($ck[0]['id']) ? $ck[0]['id'] : null, 
                'project_id' => $this->getCurrentProjectId(), 
                'choice_id' => $data['id'], 
                'language_id' => $data['language'], 
                'choice_txt' => trim($data['content'][1])
            );
            
            // initiate save to undo buffer
            $this->models->ChoiceContentKeystep->setRetainBeforeAlter();
            
            // save choice
            $this->models->ChoiceContentKeystep->save($d);
            
            // save to undo buffer
            $this->saveOldKeyChoiceData($this->models->ChoiceContentKeystep->getRetainedData(), $d, 'manual');
            
            $this->smarty->assign('returnText', $this->models->ChoiceContentKeystep->getAffectedRows() > 0 ? $this->translate('saved') : '');
        }
    }

    private function getKeystepChoiceContent ($language = null, $id = null)
    {
        $language = isset($language) ? $language : $this->requestData['language'];
        
        $id = isset($id) ? $id : $this->requestData['id'];
        
        if (empty($language) || empty($id)) {
            
            return;
        }
        else {
            
            $ck = $this->models->ChoiceContentKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'choice_id' => $id, 
                    'language_id' => $language
                ), 
                'columns' => 'choice_txt'
            ));
            
            $this->smarty->assign('returnText', json_encode($ck[0]));
            
            return $ck[0];
        }
    }

    private function moveKeystepChoice ($id, $direction)
    {
        $ck = $this->models->ChoiceKeystep->_get(array(
            'id' => array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        $ck2 = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'keystep_id' => $ck[0]['keystep_id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'id !=' => $id, 
                'show_order' => $ck[0]['show_order'] + ($direction == 'up' ? -1 : 1)
            )
        ));
        
        $this->models->ChoiceKeystep->save(array(
            'id' => $id, 
            'show_order' => $ck2[0]['show_order']
        ));
        
        $this->models->ChoiceKeystep->save(array(
            'id' => $ck2[0]['id'], 
            'show_order' => $ck[0]['show_order']
        ));
    }

    private function renumberKeystepChoices ($step)
    {
        $ck = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $step
            ), 
            'order' => 'show_order'
        ));
        
        foreach ((array) $ck as $key => $val) {
            
            $this->models->ChoiceKeystep->save(array(
                'id' => $val['id'], 
                'show_order' => $key + 1
            ));
        }
    }

    private function saveOldKeyChoiceData ($data, $newdata = false, $mode = 'auto')
    {
        
        // if there is no "old" data, there's nothing to undo
        if ($data === false)
            return;
        
        $d = $data[0];
        
        if ($newdata !== false && (isset($d['choice_txt']) && isset($newdata['choice_txt']) && $d['choice_txt'] == $newdata['choice_txt']))
            return;
        
        $d['save_type'] = $mode;
        
        $d['choice_content_id'] = $d['id'];
        
        $d['id'] = null;
        
        $d['choice_content_created'] = $d['created'];
        unset($d['created']);
        
        $d['choice_last_change'] = $d['last_change'];
        unset($d['last_change']);
        
        $this->models->ChoiceContentKeystepUndo->save($d);
    }

    private function saveOldKeyData ($data, $newdata = false, $mode = 'auto')
    {
        // if there is no "old" data, there's nothing to undo
        if ($data === false)
            return;
        
        $d = $data[0];
        
        if ($newdata !== false && ((isset($d['title']) && isset($newdata['title']) && $d['title'] == $newdata['title']) && (isset($d['content']) && isset($newdata['content']) && $d['content'] == $newdata['content'])))
            return;
        
        $d['save_type'] = $mode;
        
        $d['keystep_content_id'] = $d['id'];
        
        $d['id'] = null;
        
        $d['keystep_content_created'] = $d['created'];
        unset($d['created']);
        
        $d['keystep_content_last_change'] = $d['last_change'];
        unset($d['last_change']);
        
        $this->models->ContentKeystepUndo->save($d);
    }

    private function getKeystepUndo ($data)
    {
        if (!isset($data['id']))
            return;
            
            // determine last insert
        $d = $this->models->ContentKeystepUndo->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $data['id']
            ), 
            'columns' => ' max(created) as last'
        ));
        
        // retrieve data
        $d = $this->models->ContentKeystepUndo->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $data['id'], 
                'created' => $d[0]['last']
            )
        ));
        
        // delete from undo buffer
        $this->models->ContentKeystepUndo->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'keystep_id' => $data['id'], 
            'id' => $d[0]['id']
        ));
        
        $this->smarty->assign('returnText', json_encode($d[0]));
    }

    private function getKeystepChoiceUndo ($data)
    {
        if (!isset($data['id']))
            return;
            
            // determine last insert
        $d = $this->models->ChoiceContentKeystepUndo->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'choice_id' => $data['id']
            ), 
            'columns' => ' max(created) as last'
        ));
        
        // retrieve data
        $d = $this->models->ChoiceContentKeystepUndo->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'choice_id' => $data['id'], 
                'created' => $d[0]['last']
            )
        ));
        
        // delete from undo buffer
        $this->models->ChoiceContentKeystepUndo->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'choice_id' => $data['id'], 
            'id' => $d[0]['id']
        ));
        
        $this->smarty->assign('returnText', json_encode($d[0]));
    }

    private function showOrderToMarker ($showOrder)
    {
        return chr($showOrder + 96);
    }

    private function checkEndPointsExist ()
    {
        $pr = $this->models->ProjectRank->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'keypath_endpoint' => 1
            ), 
            'columns' => 'count(*) as total'
        ));
        
        if ($pr[0]['total'] == 0) {
            
            $this->models->ProjectRank->update(array(
                'project_id' => $this->getCurrentProjectId(), 
                'keypath_endpoint' => 1
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'lower_taxon' => 1
            ));
        }
    }
    
    // inserting between a choice and the next step
    private function insertKeyStep ($stepId, $choiceId)
    {
        
        // get the original values of the source choice
        $srcChoice = $this->getKeystepChoice($choiceId);
        
        // update the source choice, making it point to the new, inserted step
        $this->models->ChoiceKeystep->update(array(
            'res_keystep_id' => $stepId, 
            'res_taxon_id' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $choiceId
        ));
        
        // create a new choice for the new keystep
        $newChoice = $this->createNewKeystepChoice($stepId);
        
        $this->renumberKeystepChoices($stepId);
        
        // set the target for the new choice to the original target of the
        // source choice
        $x = $this->models->ChoiceKeystep->update(array(
            'res_keystep_id' => $srcChoice['res_keystep_id'], 
            'res_taxon_id' => $srcChoice['res_taxon_id']
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $newChoice
        ));
        
        $this->renumberKeySteps(array(
            0 => $this->getKeyTree()
        ));
    }
    
    // inserting between a step and the choice that led to it
    private function insertKeyStepBeforeKeyStep ($betweenA, $andB)
    {
        if (empty($andB))
            return;
        
        $d = $this->getKeystep($andB);
        
        $newStepId = $this->createNewKeystep();
        $newChoiceId = $this->createNewKeystepChoice($newStepId);
        
        $this->models->ChoiceKeystep->update(array(
            'res_keystep_id' => $andB
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $newChoiceId
        ));
        
        $this->renumberKeystepChoices($newStepId);
        
        if (!empty($betweenA)) {
            
            $this->models->ChoiceKeystep->update(array(
                'res_keystep_id' => $newStepId
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'keystep_id' => $betweenA, 
                'res_keystep_id' => $andB
            ));
        }
        else if ($d['is_start'] == 1) {
            
            $this->setKeyStartStep($newStepId);
        }
        
        return array(
            'newStepId' => $newStepId, 
            'newChoiceId' => $newChoiceId
        );
    }

    private function getChoiceList ($id = null)
    {
        $choices = $this->models->ChoiceKeystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        $d = array();
        
        foreach ((array) $choices as $key => $val) {
            
            $ck = $this->models->ContentKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'keystep_id' => $val['keystep_id']
                ), 
                'columns' => 'title,language_id', 
                'fieldAsIndex' => 'language_id'
            ));
            
            $d[$val['res_keystep_id']][] = array(
                'id' => $val['id'], 
                'keystep_id' => $val['keystep_id'], 
                'marker' => $this->showOrderToMarker($val['show_order']), 
                'res_keystep_id' => $val['res_keystep_id'], 
                'res_taxon_id' => $val['res_taxon_id'], 
                'step_title' => $ck
            );
        }
        
        return $d;
    }

    private function doRenumberKeySteps ($tree)
    {
		
        foreach ((array) $tree as $val) {
            
            if (isset($val['id'])) {
                
                $k = $this->models->Keystep->update(array(
                    'number' => $this->tmp
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['id'], 
                    'number' => -1
                ));
                
                $this->tmp++;
            }
            
            if (isset($val['children']))
                $this->doRenumberKeySteps($val['children'], false);
        }
    }

    private function renumberKeySteps ($tree)
    {
		
        if (empty($tree))
            return;
        
        $this->tmp = 1;
        
        $this->models->Keystep->update(array(
            'number' => -1
        ), array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        $this->doRenumberKeySteps($tree);
        
        $k = $this->models->Keystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'number' => '-1'
            )
        ));
        
        foreach ((array) $k as $val) {
            
            $this->models->Keystep->update(array(
                'number' => $this->tmp
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $val['id']
            ));
            
            $this->tmp++;
        }
    }

    private function setKeyStartStep ($id)
    {
        if (empty($id))
            return;
        
        $this->models->Keystep->update(array(
            'is_start' => 0
        ), array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        $this->models->Keystep->update(array(
            'is_start' => 1
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
    }

	private function getKeySections()
	{
	
		return
			$this->models->Keystep->freeQuery("        
				SELECT _a.id, _a.number, _b.res_keystep_id,
				_c.title, _c.content
				from %PRE%keysteps _a
				left join %PRE%choices_keysteps _b
				on _b.project_id = _a.project_id
				and _b.res_keystep_id = _a.id
				left join %PRE%content_keysteps _c
				on _c.project_id = _a.project_id
				and _c.keystep_id = _a.id
				and _c.language_id = ".$this->getDefaultProjectLanguage()."
				where _a.project_id = ".$this->getCurrentProjectId()."
				and _a.is_start = 0
				and _b.res_keystep_id is null
				order by _c.title
			");

	}

    private function cleanUpChoices ()
    {
		
		// deleting choices that belong to a non-existing step
		$steplessChoices = $this->models->ChoiceKeystep->freeQuery("        
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%keysteps _b
				on _a.keystep_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			and _b.id is null
		");

        foreach ((array)$steplessChoices as $val) {

			$this->deleteKeystepChoice($val['id']);

		}


		// deleting choices that have no text, image or target
		$emptyChoices = $this->models->ChoiceKeystep->freeQuery("        
			select _a.*,_b.choice_txt
			from %PRE%choices_keysteps _a
			left join %PRE%choices_content_keysteps _b
				on _a.id = _b.choice_id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
				and _a.choice_img is null
				and _a.res_keystep_id is null
				and _a.res_taxon_id is null			
				and _b.choice_txt is null
			and _b.id is null
		");

        foreach ((array)$emptyChoices as $val) {

			$this->deleteKeystepChoice($val['id']);

		}


		// resetting non-existant target steps
		$nonExistantKeyTargets = $this->models->ChoiceKeystep->freeQuery("        
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%keysteps _b
				on _a.res_keystep_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			and _b.id is null
		");

        foreach ((array)$nonExistantKeyTargets as $val) {

			$this->models->ChoiceKeystep->update(array(
				'res_keystep_id' => 'null'
			), array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $val['id']
			));

		}

		// resetting non-existant target taxa
		$nonExistantKeyTaxa = $this->models->ChoiceKeystep->freeQuery("        
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%taxa _b
				on _a.res_taxon_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			and _b.id is null
		");

        foreach ((array)$nonExistantKeyTaxa as $val) {

			$this->models->ChoiceKeystep->update(array(
				'res_taxon_id' => 'null'
			), array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $val['id']
			));
			
			$this->setKeyTaxaChanged();

		}

    }

    private function checkSettings()
    {
        if ($this->getSetting('keytype') == null) {
            
            $this->saveSetting(array(
                'name' => 'keytype', 
                'value' => 'lng'
            ));
        }
    }

    private function getKeyInfo()
    {
        $d1 = $this->models->Keytree->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'date_format(last_change,"%d-%m-%Y, %H:%i:%s") as date_hr, unix_timestamp(last_change) as date_x'
        ));
        
        $d2 = $this->models->Keystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'date_format(last_change,"%d-%m-%Y, %H:%i:%s") as date_hr, unix_timestamp(last_change) as date_x', 
            'order' => 'last_change desc', 
            'limit' => 1
        ));
        
        $d3 = $this->models->ChoiceKeystep->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'date_format(last_change,"%d-%m-%Y, %H:%i:%s") as date_hr, unix_timestamp(last_change) as date_x', 
            'order' => 'last_change desc', 
            'limit' => 1
        ));
        
        return array(
            'keytree' => $d1[0], 
            'keystep' => $d2[0], 
            'choice' => $d3[0]
        );
    }
    
    /*
     * this function is a late addition and also exists in the app controller
     * and should have identical output there! it more or less duplicates the
     * functionality of getKeyTree(), but there has been no time to unify the
     * two
     */
    private function generateKeyTree($id = null, $level = 0)
    {
        if (is_null($id)) {
            
            $step = $this->getStartKeystep();
            $id = $step['id'];
        }
        else {
            
            $step = $this->getKeystep($id);
        }

		if (!isset($this->_tempList[$step['id']])) {
			$this->_tempList[$step['id']] = true;
		} else {
			$this->addError(sprintf($this->translate('possible loop detected: %s &rarr; id %s'),$this->tmp,$step['id']));
			$this->tmp=null;
			return null;
		}

        $step = array(
            'id' => $step['id'], 
            'number' => $step['number'], 
            'title' => utf8_decode($step['title']), 
            'is_start' => $step['is_start'], 
            'level' => $level
        );
        
        $step['choices'] = $this->getKeystepChoices($id);
        
        foreach ((array) $step['choices'] as $key => $val) {

			$this->_choiceList[] = $val['id'];
            
            $d['choice_id'] = $val['id'];
            $d['choice_marker'] = utf8_decode($val['marker']);
            $d['res_keystep_id'] = $val['res_keystep_id'];
            $d['res_taxon_id'] = $val['res_taxon_id'];
            
            $step['choices'][$key] = $d;
            
            if ($val['res_keystep_id']) {
				$this->tmp =  $step['number'].$d['choice_marker'];
				//$this->addMessage($step['number'].$d['choice_marker'].' &rarr; '.$val['res_keystep_id']);
                $step['choices'][$key]['step'] = $this->generateKeyTree($val['res_keystep_id'], ($level + 1));
			}
        }
        
        return isset($step) ? $step : null;
    }

    private function saveKeyTree()
    {
		
		unset($this->_tempList);

        $tree = $this->generateKeyTree();

		unset($this->_tempList);

        if ($tree) {
            
            $_SESSION['admin']['system']['keyTreeV2'] = $tree;
            
            $tree = utf8_encode(serialize($tree));
            
            $this->models->Keytree->delete(array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $d = str_split($tree, 100000);
            
            foreach ((array) $d as $key => $val) {
                
                $this->models->Keytree->save(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'chunk' => $key, 
                    'keytree' => $val
                ));
            }

			$this->models->Keytree->save(array(
				'project_id' => $this->getCurrentProjectId(), 
				'chunk' => 999, 
				'keytree' => utf8_encode(serialize($this->_choiceList))
			));
            
            return true;
        }
        else {
            
            return false;
        }
    }

    private function setKeyTaxaChanged()
    {
        $this->saveSetting(array(
            'name' => 'keyTaxaChanged', 
            'value' => time()
        ));
    }

    private function didKeyTaxaChange()
    {
        $d = $this->getSetting('keyTaxaChanged');
        
        if ($d == null)
            return true;
        
        $k = $this->models->Keytree->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'date_format(last_change,"%d-%m-%Y, %H:%i:%s") as date_hr, unix_timestamp(last_change) as date_x'
        ));
        
        return $k[0]['date_x'] < $d;
    }


    /* branches and fruits */
    private function getTaxonDivision($step)
    {

        $this->tmp = array();
        $this->tmp['found'] = false;
        $this->tmp['excluded'] = array();
        $this->tmp['results'] = array();
        
        if (!isset($_SESSION['admin']['system']['keyTreeV2']))
            $_SESSION['admin']['system']['keyTreeV2'] = $this->generateKeyTree();
        
        $this->sawOffABranch($_SESSION['admin']['system']['keyTreeV2'], $step);
        if (isset($this->tmp['branch'])) $this->reapFruits($this->tmp['branch']);
        
        $excludedTaxa = array();
        
        $allTaxa = $this->getAllTaxaInKey();
        
        foreach ((array) $allTaxa as $val) {
            
            if (!isset($this->tmp['remaining'][$val['res_taxon_id']]))
                $excludedTaxa[$val['res_taxon_id']] = $val['res_taxon_id'];
        }
        
        return array(
            'remaining' => isset($this->tmp['remaining']) ? $this->tmp['remaining'] : null, 
            'excluded' => $excludedTaxa
        );
    }

    private function sawOffABranch($branch, $step)
    {
        if (isset($branch['id']) && $branch['id'] == $step) {
            
            $this->tmp['branch'] = $branch;
            return;
        }
        
        foreach ((array) $branch['choices'] as $val) {
            
            if (isset($val['step']))
                $this->sawOffABranch($val['step'], $step);
        }
    }

    private function reapFruits($branch)
    {
        foreach ((array) $branch['choices'] as $val) {
            
            if (isset($val['res_taxon_id']))
                $this->tmp['remaining'][$val['res_taxon_id']] = $val['res_taxon_id'];
            
            if (isset($val['step']))
                $this->reapFruits($val['step']);
        }
    }

    private function getAllTaxaInKey()
    {
        if (!isset($_SESSION['admin']['system']['key']['keyTaxa'])) {
            
            $_SESSION['admin']['system']['key']['keyTaxa'] = $this->models->ChoiceKeystep->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'res_taxon_id is not' => 'null'
                ), 
                'columns' => 'res_taxon_id'
            ));
        }
        
        return $_SESSION['admin']['system']['key']['keyTaxa'];
    }

    private function reapSteps($branch)
    {
        if (!is_numeric($branch['id']))
            return;
        
        $this->_tempList[(int) $branch['data']['number']] = array(
            'id' => $branch['id'], 
            // 'label' => $branch['number'].'. '.$branch['title'],
            'label' => $this->translate('Step') . ' ' . $branch['data']['number'] . (!empty($branch['data']['title']) && $branch['data']['title'] != $branch['data']['number'] ? ': ' . $branch['data']['title'] : ''), 
            'number' => (int) $branch['data']['number'],
            'node' => (int) $branch['data']['node']
        );
        
        if (!isset($branch['children']))
            return;
        
        foreach ((array) $branch['children'] as $val) {
            
            if (isset($val))
                $this->reapSteps($val);
        }
    }

    private function findNodeInTree($branch, $node)
    {
        foreach ((array) $branch as $val) {

            $isNode = (isset($val['data']['node']) && $val['data']['node'] == $node);
            
            $result = false;
            
            if (!$isNode && isset($val['children']))
                $result = $this->findNodeInTree($val['children'], $node);
            
            if ($isNode || $result == true) {
                
                array_unshift($this->_stepList, 
                array(
                    'id' => $val['id'], 
                    'number' => $val['data']['number'], 
                    'title' => $val['data']['title'], 
                    'is_start' => $val['data']['is_start'], 
                    'choice' => $val['data']['referringChoiceId']
                ));
                
                return true;
            }
        }
        
        return false;
    }

    private function getLookupList()
    {
        $this->_stepList = array();
        
        $list = $this->getKeyTree();
        
        // ploughs the entire key
        $this->reapSteps($list);
        
        $this->customSortArray($this->_tempList, array(
            'key' => 'number'
        ));
        
        return $this->_tempList;
    }

	private function getStepsLeadingToThisOne($thisOne)
	{
		return
			$this->models->ChoiceKeystep->freeQuery("        
				SELECT 
					_a.id, 
					_a.number, 
					_b.res_keystep_id,
					_c.title, 
					_c.content
				from %PRE%keysteps _a

				left join %PRE%choices_keysteps _b
					on _b.project_id = _a.project_id
					and _b.keystep_id = _a.id

				left join %PRE%content_keysteps _c
					on _c.project_id = _a.project_id
					and _c.keystep_id = _a.id
					and _c.language_id = ".$this->getDefaultProjectLanguage()."

				where _a.project_id = ".$this->getCurrentProjectId()."
					and _a.is_start = 0
					and _b.res_keystep_id = ".$thisOne."

				order by _c.title
			");
	}

            
	
}
