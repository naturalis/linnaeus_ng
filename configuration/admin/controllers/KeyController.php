<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once ('Controller.php');
include_once ('MediaController.php');
include_once ('ModuleSettingsReaderController.php');

/**
* KeyController
*
* class handling editing of dichotomous keys. a key consists of steps, each step having several choices.
* each choice leads to a next step or ultimately to a taxon.
*/
class KeyController extends Controller
{

    public $usedModels = array(
        'keysteps',
        'content_keysteps',
        'choices_keysteps',
        'choices_content_keysteps',
        'media_modules',
		'keysteps_taxa'
    );

	public $usedHelpers = array(
		'file_upload_helper'
    );

    public $jsToLoad = array(
        'all' => array(
            'key.js',
			'keys-endpoint.js',
            'int-link.js',
			'nsr_taxon_beheer.js',
        )
    );

    public $cssToLoad = array(
        'key.css',
        'rank-list.css',
        'key-tree.css',
        '../vendor/prettyPhoto/css/prettyPhoto.css',
        'dialog/jquery.modaldialog.css',
		'nsr_taxon_beheer.css',
		'literature2.css'
    );

    public $controllerPublicName = 'Single-access key';

	//private $moduleSession;
	private $endPointsExist;
	private $suppressTaxonDivision;
	private $keyPath;
    private $_taxaStepList;
    private $_stepList = array();
    private $_tempList = array();
    private $_counter = 0;
	private $_choiceList = array();

	private $killSwitch=0;
	private $killSwitchTriggerThreshold=10000;
	private $killSwitchTriggered=false;
	private $taxaInBranch=array();
	private $branchStartStepId=null;  //step id

	private $maxChoicesPerKeystep=16;

	/**
	* constructor
	*
	* class constructor; also calls initialize()
	*
	* @return void
	* @access public
	*/
    public function __construct ()
    {
        parent::__construct();

        $this->initialize();
	}

	/**
	* destructor
	*
	* class destructor
	*
	* @return void
	* @access public
	*/
    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->moduleSettings=new ModuleSettingsReaderController;

		$this->use_media=$this->moduleSettings->getModuleSetting( [ 'setting'=>'no_media','subst'=>0 ] )!=1;
		
		$this->setEndPointsExist();
		
		if ( $this->use_media ) 
		{
			$this->setMediaController();
		}

        if ($this->rHasVal('action','suppress_division'))
		{
			$this->setSuppressTaxonDivision( $this->rGetVal( "suppress_division" )=="on" );
        }
		else
		{
			$this->setSuppressTaxonDivision( $this->moduleSession->getModuleSetting( 'suppressTaxonDivision' ) );
		}

		$this->setKeyPath($this->moduleSession->getModuleSetting('keyPath'));

		if (!$this->getEndPointsExist())
		{
			$this->addWarning($this->translate('Your project contains no taxa that can serve as endpoints for the key.'));
		}

        $this->smarty->assign('keyPath', $this->getKeyPath());
        $this->smarty->assign('use_media', $this->use_media);
    }

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getDefaultProjectLanguage());
	}



    /**
	* indexAction
	*
	* renders the index page
	*
	* @return void
	* @access public
	*/
    public function indexAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Index'));

        $this->printPage();
    }

	/**
	* stepShowAction
	*
	* renders step_show.php
	* steps can be accessed by ID or by tree-node. when no ID is specified, program fetches the start step ID
	* also handles moving choices up and down in the display order
	*
	* @return void
	* @access public
	*/
    public function stepShowAction()
    {
        $this->checkAuthorisation();

		$step=null;

		// node is the id of a step that is called directly, i.e. not through stepping
		if ($this->rHasVal('node'))
		{
			// generate a complete key tree
			$tree = $this->getKeyTree();

			$this->_stepList = array();

			// find the node in the keyTree and build an array of the path
			// leading to it
			$this->findNodeInTree(array(0 => $tree), $this->rGetVal('node'));

			// loop through the array and add each element to the keyPath
			foreach ((array)$this->_stepList as $key => $val)
			{
				$this->updateKeyPath($val);
			}

			// get the step itself, which always is the last element in the
			// keyPath array
			$step = $this->getKeystep($val['id']);
		}
		// request for specific step
		else
		if ($this->rHasId())
		{
			$step = $this->getKeystep($this->rGetId());
		} else {
            // looking for the start step
            $id=$this->getStartKeystepId();

			// didn't find it, create it
            if (!$id)
			{
				$id=$this->createNewKeystep(array('is_start' => 1));
                $this->redirect('step_edit.php?id=' . $id );
            }
			else
			{
				$step=$this->getKeystep($id);
			}
        }

		if ($step)
		{
            // move choices up and down
            if ($this->rHasVal('move') && $this->rHasVal('direction') && !$this->isFormResubmit())
			{
				$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
				$this->checkAuthorisation();
                $this->moveKeystepChoice( $this->rGetVal('move'), $this->rGetVal('direction') );
            }

            // get step's choices
            $choices = $this->getKeystepChoices($step['id'], true);

			if (!$this->rHasVal('nopath','1') )
			{
				// update the key's breadcrumb trail
				$this->updateKeyPath(
				array(
					'id' => $step['id'],
					'number' => $step['number'],
					'title' => $step['title'],
					'is_start' => $step['is_start'],
					'choice' => $this->rHasVal('choice') ? $this->rGetVal('choice') : null
				));

			}

            $step['content'] = nl2br($step['content']);

            $this->smarty->assign('step', $step);
            $this->smarty->assign('choices', $choices);
            $this->smarty->assign('maxChoicesPerKeystep', $this->maxChoicesPerKeystep);
        }
        else
		{
            $this->addError($this->translate('Non-existant keystep ID. Please go back and change the target for the choice.'));
        }

        $this->setPageName(sprintf($this->translate('Show key step %s'), $step['number']), $this->translate('Show key step'));

        $this->smarty->assign('stepsLeadingToThisOne', $this->getStepsLeadingToThisOne($step['id']));
        $this->smarty->assign('suppressPath', $this->rHasVal('nopath','1'));
        $this->smarty->assign('suppressDivision', $this->getSuppressTaxonDivision());
        $this->smarty->assign('taxa', $this->getLinkedTaxa( $this->rGetId() ) );

		if ( !$this->getSuppressTaxonDivision() )
		{
			$this->smarty->assign('taxonDivision', $this->getTaxonDivision($step['id']));
		}

        $this->printPage();
    }

	/**
	* stepEditAction
	*
	* renders step_edit.php
	* editing step data, creation of new step, inserting a new step between existing ones, deletion of L2 step-images
	*
	* @return void
	* @access public
	*/
    public function stepEditAction()
    {
        // create a new step when no id is specified
        if (!$this->rHasId())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

            $id = $this->createNewKeystep();

            if ($this->rHasVal('insert'))
			{
                $this->insertKeyStep($id, $this->rGetVal('insert'));
            }

            if ($this->rHasVal('ref_choice'))
			{
                // url was called from the 'new step' option of a choice: set
                // the new referring step id

                $this->models->ChoicesKeysteps->save(array(
                    'id' => $this->rGetVal('ref_choice'),
                    'res_keystep_id' => $id
                ));

                $this->updateKeyPath(array(
                    'choice' => $this->rGetVal('ref_choice')
                ));
            }

            // redirect to self with id
            // $this->redirect('step_edit.php?id='.$id);
            $this->redirect('step_show.php?id=' . $id . ($this->rHasVal('insert') ? '&insert=' . $this->rGetVal('insert') : ''));

        } else {

            // id present

			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            // delete L2-legacy image
            if ($this->rHasVal('action', 'deleteImage') || $this->rHasVal('action', 'deleteAllImages'))
			{
                $step = $this->getKeystep($this->rGetId());
                $this->deleteLegacyImage($this->rHasVal('action', 'deleteImage') ? $step : 'all');
                $this->redirect('step_show.php?id='.$step['id']);

            } else
			// delete step
            if ($this->rHasVal('action', 'delete'))
			{
                $this->deleteKeystep($this->rGetId());
                $entry = $this->getPreviousKeypathEntry( array("id"=>$this->rGetId()) );
                $this->redirect($entry ? 'step_show.php?id=' . $entry['id'] : 'index.php');
            }

            // get step data
            $step = $this->getKeystep($this->rGetId());
            $this->setPageName(sprintf($this->translate('Edit step %s'), $step['number']), $this->translate('Edit step'));

            //// saving the number (all the rest is done through ajax)
            // number can now no longer be edited
            if ($this->rHasVal('action', 'save') && !$this->isFormResubmit())
			{
            	// no number specified
                if (empty($this->rGetVal('number')))
				{
                    $next = $this->getNextLowestStepNumber();
                    $this->addError(sprintf($this->translate('Step number is required. The saved number for this step is %s. The lowest unused number is %s.'), $step['number'], $next));
                }
                // non-numeric number specified
                elseif (!is_numeric($this->rGetVal('number')))
				{
                    $this->addError(sprintf($this->translate('"%s" is not a number.'), $this->rGetVal('number')));
                }
                // existing number specified
                else
				{
                    $k = $this->models->Keysteps->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'number' => $this->rGetVal('number'),
                            'id != ' => $this->rGetId()
                        ),
                        'columns' => 'count(*) as total'
                    ));

                    if ($k[0]['total'] != 0)
					{
                        // doublure
                        $this->addError(sprintf($this->translate('A step with number %s already exists. The lowest unused number is %s.'), $this->rGetVal('number'), $this->getNextLowestStepNumber()));
                    } else {
                        // unique numeric number
						// don't update if unchanged
                        if ($this->rGetVal('number') != $step['number'])
						{
                            $this->models->Keysteps->update(
                            array(
                                'number' => $this->rGetVal('number')
                            ),
							array(
                                'id' => $this->rGetId(),
                                'project_id' => $this->getCurrentProjectId(),
                            ));

                            // two steps below unnecessary because of redirect
                            // to step_show
                            $step['number']=$this->rGetVal('number');
                            $this->addMessage($this->translate('Number saved.'));
                        } else {
							$this->addMessage($this->translate('Saved.'));
						}
                    }
                }
            }
		}

        if (isset($step))
            $this->smarty->assign('step', $step);

        $this->smarty->assign('languages', $this->getProjectLanguages());
        $this->smarty->assign('defaultLanguage', $_SESSION['admin']['project']['languageList'][$this->getDefaultProjectLanguage()]);

        $this->printPage();
    }

	/**
	* choiceEditAction
	*
	* renders choice_edit.php
	* editing choice data, creation of new step, inserting a new step between existing ones, deletion of L2 step-images
	*
	* @return void
	* @access public
	*/
    public function choiceEditAction()
    {
		// create a new choice when no id is specified
        if (!$this->rHasId())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

            // need a step to which the choice belongs
            if (!$this->rHasVal('step'))
                $this->redirect('step_show.php');

            // create new choice & renumber
            $id = $this->createNewKeystepChoice($this->rGetVal('step'));
            $this->renumberKeystepChoices($this->rGetVal('step'));

            // redirecting to protect against resubmits
            $this->redirect('choice_edit.php?id=' . $id);
        }

        // resolve id, choice and step
        $id = $this->rGetId();
        $choice = $this->getKeystepChoice($id);
        $step = $this->getKeystep($choice['keystep_id']);

        $this->setPageName(sprintf($this->translate('Edit choice "%s" for step %s'), $choice['show_order'], $step['number'], $step['title']), $this->translate('Edit choice'));

        if ($this->rHasVal('action', 'delete'))
        // delete the complete choice, incl image (if any)
		{

			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

		    // Leave old code just in case...
			if (!empty($choice['choice_img']))
                @unlink($_SESSION['admin']['project']['paths']['project_media'] . $choice['choice_img']);

		    $this->detachAllMedia();

            $this->deleteKeystepChoice($choice['id']);

            unset($_SESSION['admin']['system']['remainingTaxa']);

            $this->redirect('step_show.php?id=' . $choice['keystep_id']);

        } else if ($this->rHasVal('action', 'deleteImage')) {
            // delete just the image
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

		    // Leave old code just in case...
			if (!empty($choice['choice_img']))
                @unlink($_SESSION['admin']['project']['paths']['project_media'] . $choice['choice_img']);

            $this->models->ChoicesKeysteps->save(array(
                'id' => $choice['id'],
                'choice_img' => 'null',
                'choice_image_params' => 'null'
            ));

            unset($choice['choice_img']);

		    $this->detachAllMedia();
		} else if ($this->rHasVal('action', 'save')) {
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->updateChoice( $this->rGetAll() );
	        $choice = $this->getKeystepChoice($id);
        }

		// save choice image
		if ($choice['id'] && isset($this->requestDataFiles) && !$this->isFormResubmit())
		{

			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$filesToSave = $this->getUploadedMediaFiles();

			if ($filesToSave)
			{
				$ck = $this->models->ChoicesKeysteps->save(
				array(
					'id' => $choice['id'],
					'project_id' => $this->getCurrentProjectId(),
					'choice_img' => $filesToSave[0]['name']
				));

				if ($ck)
				{
					$this->addMessage($this->translate('Image saved.'));
					$choice['choice_img'] = $filesToSave[0]['name'];
				} else {
					@unlink($_SESSION['admin']['project']['paths']['project_media'] . $filesToSave[0]['name']);
					$this->addError($this->translate('Could not save image.'));
				}
			}
		}

		$this->smarty->assign( 'choice', $choice );
		$this->smarty->assign( 'step', $this->getKeystep($choice['keystep_id']) );
        $this->smarty->assign( 'languages', $this->getProjectLanguages() );
        $this->smarty->assign( 'defaultLanguage', $_SESSION['admin']['project']['languageList'][$this->getDefaultProjectLanguage()] );
		$this->smarty->assign( 'steps', $this->getKeysteps( array("exclude" => $choice['keystep_id']) ) );
		$this->smarty->assign( 'taxa', $this->getTaxaInKey( array( "order"=>"taxon" ) ) );
		$this->smarty->assign( 'keyPath', $this->getKeyPath() );
        $this->smarty->assign( 'includeHtmlEditor', true );
		$this->smarty->assign( 'module_id', $this->getCurrentModuleId() );
        $this->smarty->assign( 'item_id', $id );

        $this->printPage();
    }

    public function taxaAction()
    {
        $this->checkAuthorisation();

		$this->setPageName( $this->translate('Linked taxa') );

		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->saveLinkedTaxa( $this->rGetall() );
			$this->addMessage( $this->translate( 'Saved.' ) );
		}
		else
		if ($this->rHasVar('link_id') && $this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->deleteLinkedTaxa( $this->rGetall() );
			$this->addMessage( $this->translate( 'Deleted.' ) );
		}

        $this->smarty->assign('id', $this->rGetId() );
        $this->smarty->assign('taxa', $this->getLinkedTaxa( $this->rGetId() ) );

        $this->printPage();
    }

    public function contentsAction()
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

    public function renumberAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

        if ($this->rHasVal('action', 'renumber') && !$this->isFormResubmit())
		{
			$this->renumberKeySteps([$this->getKeyTree()]);
			$this->addMessage($this->translate('Renumbered steps.'));
        }

        $this->printPage();
    }


    public function insertAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		if (!$this->rGetId()) $this->redirect('step_show.php');

        if ($this->rHasVal('step') && $this->rHasVal('action', 'insert') && !$this->isFormResubmit())
		{
            $res = $this->insertKeyStepBeforeKeyStep($this->rGetVal('source'), $this->rGetVal('step'));

			if ( $res['newStepId'] && $this->rHasVal('step_title') )
			{
				$this->saveKeystepContent([
					'id'=>$res['newStepId'],
					'language'=>$this->getDefaultProjectLanguage(),
					'title'=>$this->rGetVal('step_title')
				]);
			}

            $step = $this->getKeystep($res['newStepId']);

			// remove last keyPath entry
			//array_pop($_SESSION['admin']['system']['keyPath']);
			$path = $this->getKeyPath();
            unset($path[count($path) - 1]);
            $this->setKeyPath($path);

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

        $step=$this->getKeystep($this->rGetId());

		if ($this->rGetId()==$this->getStartKeystepId()) {
	        $this->setPageName(sprintf($this->translate('Insert a step before step %s'), $step['number']), $this->translate('Insert a step before step x'));
		} else {
			$prevstep=$this->getKeystep($this->rHasVal('c'));
    	    $this->setPageName(sprintf($this->translate('Insert a step between step %s and %s'), $prevstep['number'], $step['number']), $this->translate('Insert a step between step x and y'));
		}

        $ck = $this->models->ChoicesKeysteps->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'res_keystep_id' => $this->rGetId()
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
        $this->smarty->assign('prevStep', $this->rHasVal('c') ? $this->rGetVal('c') : null);
        $this->smarty->assign('sourceSteps', $d);

        $this->printPage();
    }

    public function sectionAction()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action', 'setstart') && $this->rHasId())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $this->setKeyStartStep($this->rGetId());
            $this->redirect('step_show.php');
        }
        else
		// start a new subsection: create a new step and redirect to edit
		if ($this->rHasVal('action', 'new'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

			$id=$this->createNewKeystep();
            $this->redirect('step_edit.php?id=' . $id);
        }

        $this->cleanUpChoices();

        $this->setPageName($this->translate('Key sections'));

		$keySections = $this->getKeySections();

		$this->smarty->assign('keySections', $keySections);

        $this->printPage();
    }

    public function mapAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Key map'));
        $key = $this->getKeyTree();
        $this->smarty->assign('json', json_encode($key));
        $this->printPage();
    }

    public function rankAction()
    {
		$this->checkAuthorisation();

        $this->setPageName( $this->translate('Taxon ranks in key') );

        $pr = $this->getProjectRanks(array(
            'lowerTaxonOnly' => false
        ));

        if ($this->rHasVal('keyRankBorder') && isset($pr) && !$this->isFormResubmit())
		{
			$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $endPoint = false;

            foreach ((array) $pr as $key => $val)
			{

                if ($val['rank_id'] == $this->rGetVal('keyRankBorder'))
                    $endPoint = true;

                $this->models->ProjectsRanks->save(array(
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

    public function orphansAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Taxa not part of the key'));
        $this->smarty->assign('taxa', $this->getTaxaInKey());
        $this->printPage();
    }

    public function deadEndsAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Key validation'));
		$this->cleanUpChoices();

        $k = $this->getKeysteps();

		$deadSteps =  $sadSteps = array();

        foreach ((array) $k as $key => $val)
		{
            $kc = $this->getKeystepChoices($val['id']);
            if (count((array) $kc) == 0) $deadSteps[] = $val;
            if (count((array) $kc) == 1) $sadSteps[] = $val;
        }

		$deadChoices = $this->models->KeyModel->getDeadEndChoicesKeysteps(
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'language_id'=>$this->getDefaultProjectLanguage()
			)
		);

        $this->smarty->assign('deadSteps',$deadSteps);
        $this->smarty->assign('sadSteps',$sadSteps);
        $this->smarty->assign('deadChoices', $deadChoices);
        $this->printPage();
    }

	/**
	* cleanUpAction
	*
	* renders clean_up.php
	* function:
	* removes choices that belong to a non-existing step
	* removes choices that have no text, image or target
	* resets non-existant target steps
	* resets non-existant target taxa
	*
	* @return void
	* @access public
	*/
    public function cleanUpAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Clean up'));

        if ($this->rHasVal('action', 'clean') && !$this->isFormResubmit())
		{
            $this->cleanUpChoices();
	        $this->smarty->assign('processed', true);
			$this->addMessage($this->translate('Clean up done'));
        }

        $this->printPage();
    }

	/**
	* ajaxInterfaceAction
	*
	* handles all AJAX-calls for this module.
	*
	* @return void
	* @access public
	*/
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_keystep_content'))
		{
			if ( !$this->getAuthorisationState() ) return;
            $this->getKeystepContent();
        }
        else
		if ($this->rHasVal('action','save_keystep_content'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( !$this->getAuthorisationState() ) {
			    return;
            }
            $this->saveKeystepContent($this->rGetAll());
        }

        $this->printPage();
    }

    private function setEndPointsExist()
    {
		$this->endPointsExist=$this->models->KeyModel->getEndPointCount(array('project_id'=>$this->getCurrentProjectId())) > 0;
    }

    private function getEndPointsExist()
    {
		return $this->endPointsExist;
    }

	private function setSuppressTaxonDivision( $state )
	{
		if ( !is_bool($state) ) return;
		$this->suppressTaxonDivision=$state;
		$this->moduleSession->setModuleSetting( array('setting'=>'suppressTaxonDivision','value'=>$state ) );
	}

	private function getSuppressTaxonDivision()
	{
		return $this->suppressTaxonDivision;
	}

    private function getStartKeystepId()
    {
		$d=$this->models->Keysteps->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'is_start' => 1
		)));

		return $d ? $d[0]['id'] : $d;
    }

    private function getTaxonDivision( $step )
    {
		$this->branchStartStepId=$step;

		$this->iterateDownward($step);

		$taxa=$this->models->KeyModel->getAllKeyConnectedTaxa(array('project_id' => $this->getCurrentProjectId()));

		foreach((array)$this->taxaInBranch as $key=>$val)
		{
			$this->taxaInBranch[$key]=
				$this->addHybridMarkerAndInfixes( [ 'name'=>$taxa[$key]['taxon'],'base_rank_id'=>$taxa[$key]['base_rank_id'],'taxon_id'=>$taxa[$key]['id'],'parent_id'=>$taxa[$key]['parent_id'] ] );
			unset( $taxa[$key] );
		}

		$remaining=array();
		foreach((array)$taxa as $key=>$val)
		{
			$remaining[$key]=
				$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
		}

		asort( $this->taxaInBranch );
		asort( $remaining );

        return
			array(
				'remaining' => $this->taxaInBranch,
				'excluded' => $remaining
			);
    }

	private function iterateDownward( $p=null )
	{
		$this->killSwitch++;

		if ( $this->killSwitch >= $this->killSwitchTriggerThreshold )
		{
			if ( !$this->killSwitchTriggered )
			{
				$this->addError( sprintf( $this->translate( "Taxon harvesting triggered loop threshold (%s)." ), $this->killSwitchTriggerThreshold ) );
				$this->killSwitchTriggered=true;
			}
			return;
		}

		$id=isset($p['id']) ? $p['id'] : null;
		$in_branch=isset($p['in_branch']) ? $p['in_branch'] : false;
		$origin=isset($p['origin']) ? $p['origin'] : -1;

		if ( empty( $id ) )
		{
			$k=$this->models->KeyModel->getKeystepChoices(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'is_start' => true
				));

			if ( $k )
			{
				$id = $k[0]['keystep_id'];
			}
			if ( empty($id) ) return;
		}

		if ( empty($this->branchStartStepId) || $id==$this->branchStartStepId )
		{
			$in_branch=true;
		}

		$k=$this->models->KeyModel->getKeystepChoices(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $id
			));


		foreach((array)$k as $val )
		{
			if ( $in_branch && !empty($val['res_taxon_id']) )
			{
				$this->taxaInBranch[$val['res_taxon_id']]=$val['res_taxon_id'];
			}
			if ( !empty($val['res_keystep_id']) )
			{
				$this->iterateDownward( array('id'=>$val['res_keystep_id'],'in_branch'=>$in_branch,'origin'=>$id) );
			}
		}
	}

    private function getTaxaInKey( $p=null )
    {
		$d=
			array(
				'project_id' => $this->getCurrentProjectId(),
				'order' => isset($p['order']) ? $p['order'] : null,
			);

		$this->UserRights->setUserItems();
		$this->userItems=$this->UserRights->getUserItems();

		if ( !empty($this->userItems) )
		{
			$d['branch_tops']=$this->userItems;
		}

		$taxa=$this->models->KeyModel->getTaxaInKey($d);

        foreach ((array)$taxa as $key => $val) {
            $taxa[$key]['taxon'] = $this->addHybridMarkerAndInfixes(['name' => $val['taxon'], 'base_rank_id' => $val['base_rank_id'], 'taxon_id' => $val['id'], 'parent_id' => $val['parent_id']]);
        }

		return $taxa;
    }

    private function getKeysteps( $p=null )
    {
		return
			$this->models->KeyModel->getKeysteps(
				array(
					'language_id' => $this->getDefaultProjectLanguage(),
					'project_id' => $this->getCurrentProjectId(),
					'id' => isset($p['id']) ? $p['id'] : null,
					'exclude' => isset($p['exclude']) ? $p['exclude'] : null,
					'is_start' => isset($paprams['is_start']) ? $p['is_start'] : null,
					'sort' => isset($p['sort']) ? $p['sort'] : 'number'
				));
    }

    private function setKeyPath( $path )
    {
		$this->keyPath=$path;
		$this->moduleSession->setModuleSetting( array('setting'=>'keyPath','value'=>$path ) );
    }

    private function getKeyPath()
    {
       return $this->keyPath;
    }

    private function updateKeyPath( $p )
    {
        $id = isset($p['id']) ? $p['id'] : null;
        $number = isset($p['number']) ? $p['number'] : null;
        $title = isset($p['title']) ? $p['title'] : null;
        $is_start = isset($p['is_start']) ? $p['is_start'] : null;
        $choice = isset($p['choice']) ? $p['choice'] : null;

		$path=$this->getKeyPath();

        if ( $path )
		{
            foreach ((array)$path as $key => $val)
			{
                if ($val['id']==$id) break;
                if (!empty($val['id'])) $d[] = $val;
            }
        }

        $d[] =
			array(
				'id' => $id,
				'number' => $number,
				'title' => $title,
				'is_start' => $is_start,
				'choice' => null,
				'choice_marker' => null
			);

        if ( !empty($choice) && (count((array)$d)-2)>= 0 )
		{
            $choice = $this->getKeystepChoice($choice);
            $d[count((array)$d)-2]['choice'] = $choice;
            $d[count((array)$d)-2]['choice_marker'] = isset($choice['marker']) ? $choice['marker'] : '';
        }

		$this->setKeyPath( $d );
    }

    private function getPreviousKeypathEntry( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$steps_back=isset($p['steps_back']) ? $p['steps_back'] : 1;

        $path=$this->getKeyPath();
        $pathcount=count((array)$path);

        if ( !is_null($id) )
		{
            for ( $i=($pathcount-1); $i>=0; $i-- )
			{
                if ( isset($path[$i+$steps_back]) && $path[$i+$steps_back]['id']==$id )
				{
                    return $path[$i];
                }
            }
            return false;
        }
        else
		{
            return isset($path[$pathcount-($steps_back+1)]) ? $path[$pathcount-($steps_back+1)] : false;
        }
    }

    /*
     * this function is a late addition and also exists in the app controller
     * and should have identical output there! it more or less duplicates the
     * functionality of getKeyTree(), but there has been no time to unify the
     * two
     */
    private function generateKeyTree($id = null, $level = 0)
    {
        if (is_null($id))
		{
            $id = $this->getStartKeystepId();
        }

        $step=$this->getKeystep($id);

		if (!isset($this->_tempList[$step['id']]))
		{
			$this->_tempList[$step['id']] = true;
		}
		else
		{
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

        foreach ((array) $step['choices'] as $key => $val)
		{
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

    private function getKeyTree( $p=null )
    {
		$refId = isset($p['ref_id']) ? $p['ref_id'] : null;
		$choice = isset($p['choice']) ? $p['choice'] : null;

        $s = is_null($refId) ? $this->getKeystep($this->getStartKeystepId()) : $this->getKeystep($refId);

		$s['title']=htmlspecialchars($s['title']);

        $step = array(
            'id' => $s['id'],
            'name' => (isset($choice['marker']) ? '(' . $choice['marker'] . ') ' : '') . $s['number'] . '. ' . $s['title'],
            'src_choice' => isset($choice['txt']) ? htmlspecialchars($choice['txt']) : null,
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
        if (!isset($this->_stepList[$s['id']]))
		{
            $this->_stepList[$step['id']] = true;

            $ck = $this->models->ChoicesKeysteps->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'keystep_id' => $step['id']
                ),
                'order' => 'show_order'
            ));

            foreach ((array) $ck as $key => $val)
			{
				$cck = $this->models->ChoicesContentKeysteps->_get(
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

				$txt=htmlspecialchars($txt);

                if (isset($val['res_taxon_id']))
				{
                    $t = $this->getTaxonById($val['res_taxon_id']);

					$t['taxon']=htmlspecialchars($t['taxon']);

                    $this->tmp = is_null($this->tmp) ? 0 : $this->tmp;

                    $step['children'][] = array(
                        'id' => 't' . $this->tmp++,
						'src_choice' => $txt,
                        'type' => 'taxon',
                        'data' => array(
                            'number' => 't' . $t['id'],
                            'title' => '&rarr; ' . $t['taxon'],
                            'taxon' => $t['taxon'],
                            'id' => $t['id']
                        ),
                        'name' => '(' . $val['show_order'] . ') ' . '<i>' . $t['taxon'] . '</i>'
                    );
                }
                else if (isset($val['res_keystep_id']) && $val['res_keystep_id'] != -1) {

                    $step['children'][] = $this->getKeyTree(array(
						'ref_id' => $val['res_keystep_id'],
						'choice' =>
							array(
								'id' => $val['id'],
								'marker' => $val['show_order'],
								'txt' => $txt,
							)
						));
                }
            }
        }

        return $step;
    }

    private function getKeystep($id)
    {
        if (empty($id)) return;

		$k = $this->models->Keysteps->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			)
		));

		if (!$k) return false;

		$step = $k[0];

		$kc = $this->getKeystepContent(array('language'=>$this->getDefaultProjectLanguage(),'id'=>$step['id']));

		$step['title'] = $kc['title'];
		$step['content'] = $kc['content'];

		$this->smarty->assign('returnText', json_encode($step));

		return $step;

    }

    private function getKeystepContent( $p=null )
    {
        $language = isset($p['language']) ? $p['language'] : $this->rGetval('language');
		$id = isset($p['id']) ? $p['id'] : $this->rGetId();
        if (empty($language) || empty($id))
		{
            return;
        }
        else
		{
            $ck = $this->models->ContentKeysteps->_get(
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

    private function saveKeystepContent($data)
    {
		$keystep_id = isset($data['id']) ? $data['id'] : null;
		$language_id = isset($data['language']) ? $data['language'] : null;

		if ( isset($data['content']) )
		{
			$title = isset($data['content'][0]) ? $data['content'][0] : null;
			$content = isset($data['content'][1]) ? $data['content'][1] : null;
		} else {
			$title = isset($data['title']) ? $data['title'] : null;
			$content = isset($data['content']) ? $data['content'] : null;
		}


        if ( is_null($keystep_id) || is_null($language_id) ) {
            return;
        }

		$ck = $this->models->ContentKeysteps->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $keystep_id,
				'language_id' => $language_id
			)
		));

		$newTitle = trim($title) == '' ? 'null' : trim($title);
		$newContent = trim($content) == '' ? 'null' : trim($content);

		$d = array(
			'id' => isset($ck[0]['id']) ? $ck[0]['id'] : null,
			'project_id' => $this->getCurrentProjectId(),
			'keystep_id' => $keystep_id,
			'language_id' => $language_id,
			'title' => $newTitle,
			'content' => $newContent
		);

		// save step
		$this->models->ContentKeysteps->save($d);

		$this->logChange(array(
		    'note' => 'Key step saved',
            'after' => $d
        ));

		$this->smarty->assign('returnText', $this->models->ContentKeysteps->getAffectedRows() > 0 ? $this->translate('saved') : '');
    }

    private function deleteLegacyImage($step)
    {
        $where['project_id'] = $this->getCurrentProjectId();

        if (is_array($step) && !empty($step['image']))
		{
            @unlink($_SESSION['admin']['project']['paths']['project_media'] . $step['image']);
            $where['id'] = $step['id'];
        }

        $this->models->Keysteps->update(
            array('image' => 'null'),
            $where
        );
    }

    private function getNextLowestStepNumber()
    {
        $k = $this->models->Keysteps->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'number',
            'order' => 'number'
        ));

        $prev = 0;
        $next = false;

        foreach ((array) $k as $key => $val)
		{
            if ($val['number'] - $prev > 1)
			{
                $next = $prev + 1;
                break;
            }
            else
			{
                $prev = $val['number'];
            }
        }

        if (!$next) $next = $prev + 1;
        return $next;
    }

    private function createNewKeystep($data = null)
    {
        $step = array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'number' => !empty($data['number']) ? $data['number'] : $this->getNextLowestStepNumber(),
                'is_start' => !empty($data['is_start']) ? $data['is_start'] : 0
        );
        $this->models->Keysteps->save($step);

        $step['id'] = $this->models->Keysteps->getNewId();

        $this->logChange([
            'note' => 'Create new step',
            'after' => $step
        ]);

        return $step['id'];
    }

    private function deleteKeystepChoice($id)
    {
        $this->models->ChoicesContentKeysteps->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'choice_id' => $id
        ));

        $this->models->ChoicesKeysteps->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
    }

    private function deleteKeystep($id)
    {
        $ck = $this->models->ChoicesKeysteps->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'keystep_id' => $id
            )
        ));

        $hadTaxa = false;

        foreach ((array) $ck as $key => $val)
		{
            $hadTaxa = $hadTaxa == true || !empty($val['res_taxon_id']);
            $this->deleteKeystepChoice($val['choice_id']);
        }

        $this->models->ChoicesKeysteps->update(array(
            'res_keystep_id' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'res_keystep_id' => $id
        ));

        $this->models->ContentKeysteps->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'keystep_id' => $id
        ));

        $this->models->Keysteps->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
    }

    private function createNewKeystepChoice($stepId)
    {
        if (empty($stepId)) {
            return;
        }


        $step = array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'keystep_id' => $stepId,
                'show_order' => 99
        );

        $this->models->ChoicesKeysteps->save($step);

        return $this->models->ChoicesKeysteps->getNewId();
    }

    private function getKeystepChoices($step, $formatHtml = false)
    {
        $choices = $this->models->ChoicesKeysteps->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'keystep_id' => $step
            ),
            'order' => 'show_order'
        ));

        foreach ((array) $choices as $key => $val)
		{

            // Override image, but only when size has not been set!
            if (empty($val['choice_image_params'])) {
                $choices[$key]['choice_img'] = $this->getChoiceImage($val['id']);
            } else {
                $choices[$key]['choice_img'] =
                    $_SESSION['admin']['project']['urls']['project_media'] . $val['choice_img'];
            }

            $kcc = $this->getKeystepChoiceContent($this->getDefaultProjectLanguage(), $val['id']);

            if (isset($kcc['title']))
                $choices[$key]['title'] = $kcc['title'];

            if (isset($kcc['choice_txt']))
                $choices[$key]['choice_txt'] = $formatHtml ? nl2br($kcc['choice_txt']) : $kcc['choice_txt'];

            if (!empty($val['res_keystep_id']) && $val['res_keystep_id'] != 0)
			{
                if ($val['res_keystep_id'] == '-1')
				{

                    $choices[$key]['target'] = $this->translate('(new step)');
                }
                else
				{
                    $k = $this->getKeystep($val['res_keystep_id']);

                    if (isset($k['title']))
                        $choices[$key]['target'] = $k['title'];

                    if (isset($k['number']))
                        $choices[$key]['target_number'] = $k['number'];
                }
            }
            else
			if (!empty($val['res_taxon_id']))
			{
                $t = $this->models->Taxa->_get(array(
                    'id' => $val['res_taxon_id']
                ));

                if (isset($t['taxon']))
                    $choices[$key]['target'] = $t['taxon'];
            } else {
                $choices[$key]['target'] = $this->translate('undefined');
            }

            $choices[$key]['marker'] = $val['show_order'];
        }

        return $choices;
    }

    private function getKeystepChoice($id)
    {
        $ck = $this->models->ChoicesKeysteps->_get(array(
            'id' => array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        $choice = $ck[0];

    	// Override choice image
        $choice['choice_img'] = $this->getChoiceImage();

        $k = $this->models->Keysteps->_get(array(
            'id' => $choice['keystep_id']
        ));

        $choice['keystep_number'] = $k['number'];

		$choice['content'] = $this->models->ChoicesContentKeysteps->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'choice_id' => $id,
			),
			'columns' => 'language_id,choice_txt',
			'fieldAsIndex'=>'language_id'
		));

		$choice['choice_txt'] = $choice['content'][$this->getDefaultProjectLanguage()]['choice_txt'];

        if (!empty($choice['res_keystep_id']) && $choice['res_keystep_id'] != 0)
		{
            if ($choice['res_keystep_id'] == '-1')
			{
                $choice['target'] = $this->translate('(new step)');
            }
            else
			{
                $k = $this->models->Keysteps->_get(array(
                    'id' => $choice['res_keystep_id']
                ));

                if (isset($k['number']))
				{
                    $choice['target_number'] = $k['number'];
				}
            }
        }
        else
		if (!empty($choice['res_taxon_id']))
		{
            $t = $this->models->Taxa->_get(array(
                'id' => $choice['res_taxon_id']
            ));

            if (isset($t['taxon']))
			{
                $choice['target'] = $t['taxon'];
			}
        }
        else
		{
            $choice['target'] = $this->translate('undefined');
        }

        $choice['marker'] = $choice['show_order'];

        return $choice;
    }

    private function getKeystepChoiceContent($language = null, $id = null)
    {
        $language = isset($language) ? $language : $this->rGetVal('language');

        $id = isset($id) ? $id : $this->rGetId();

        if (empty($language) || empty($id)) {

            return;
        } else {

            $ck = $this->models->ChoicesContentKeysteps->_get(
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

    private function moveKeystepChoice($id, $direction)
    {
        $ck = $this->models->ChoicesKeysteps->_get(array(
            'id' => array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        $ck2 = $this->models->ChoicesKeysteps->_get(
        array(
            'id' => array(
                'keystep_id' => $ck[0]['keystep_id'],
                'project_id' => $this->getCurrentProjectId(),
                'id !=' => $id,
                'show_order' => $ck[0]['show_order'] + ($direction == 'up' ? -1 : 1)
            )
        ));

        $this->models->ChoicesKeysteps->save(array(
            'id' => $id,
            'show_order' => $ck2[0]['show_order']
        ));

        $this->models->ChoicesKeysteps->save(array(
            'id' => $ck2[0]['id'],
            'show_order' => $ck[0]['show_order']
        ));
    }

    private function renumberKeystepChoices($step)
    {
        $ck = $this->models->ChoicesKeysteps->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'keystep_id' => $step
            ),
            'order' => 'show_order'
        ));

        foreach ((array) $ck as $key => $val) {

            $this->models->ChoicesKeysteps->save(array(
                'id' => $val['id'],
                'show_order' => $key + 1
            ));
        }
    }

    // inserting between a choice and the next step
    private function insertKeyStep($stepId, $choiceId)
    {
        // get the original values of the source choice
        $srcChoice = $this->getKeystepChoice($choiceId);

        // update the source choice, making it point to the new, inserted step
        $this->models->ChoicesKeysteps->update(array(
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
        $x = $this->models->ChoicesKeysteps->update(array(
            'res_keystep_id' => $srcChoice['res_keystep_id'],
            'res_taxon_id' => $srcChoice['res_taxon_id']
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $newChoice
        ));

    }

    // inserting between a step and the choice that led to it
    private function insertKeyStepBeforeKeyStep($betweenA, $andB)
    {
        if (empty($andB))
            return;

        $d = $this->getKeystep($andB);

        $newStepId = $this->createNewKeystep();
        $newChoiceId = $this->createNewKeystepChoice($newStepId);

        $this->models->ChoicesKeysteps->update(array(
            'res_keystep_id' => $andB
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $newChoiceId
        ));

        $this->renumberKeystepChoices($newStepId);

        if (!empty($betweenA))
		{
            $this->models->ChoicesKeysteps->update(array(
                'res_keystep_id' => $newStepId
            ), array(
                'project_id' => $this->getCurrentProjectId(),
                'keystep_id' => $betweenA,
                'res_keystep_id' => $andB
            ));
        }
        else
		if ($d['is_start']==1)
		{
            $this->setKeyStartStep($newStepId);
        }

        return
			array(
				'newStepId' => $newStepId,
				'newChoiceId' => $newChoiceId
			);
    }

    private function doRenumberKeySteps($tree)
    {
        foreach ((array) $tree as $val)
		{
            if (isset($val['id']))
			{
                $k = $this->models->Keysteps->update(array(
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

    private function renumberKeySteps($tree)
    {

        if (empty($tree))
            return;

        $this->tmp = 1;

        $this->models->Keysteps->update(array(
            'number' => -1
        ), array(
            'project_id' => $this->getCurrentProjectId()
        ));

        $this->doRenumberKeySteps($tree);

        $k = $this->models->Keysteps->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'number' => '-1'
            )
        ));

        foreach ((array) $k as $val) {

            $this->models->Keysteps->update(array(
                'number' => $this->tmp
            ), array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $val['id']
            ));

            $this->tmp++;
        }
    }

    private function setKeyStartStep($id)
    {
        if (empty($id))
            return;

        $this->models->Keysteps->update(array(
            'is_start' => 0
        ), array(
            'project_id' => $this->getCurrentProjectId()
        ));

        $this->models->Keysteps->update(array(
            'is_start' => 1
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
    }

	private function getKeySections()
	{
		return
			$this->models->KeyModel->getKeySections(
				array(
					'language_id' => $this->getDefaultProjectLanguage(),
					'project_id' => $this->getCurrentProjectId(),
				));
	}

    private function cleanUpChoices()
    {
		// deleting choices that belong to a non-existing step
		$steplessChoices = $this->models->KeyModel->getSteplessChoices(array('project_id' => $this->getCurrentProjectId()));

        foreach ((array)$steplessChoices as $val)
		{
			$this->deleteKeystepChoice($val['id']);
		}

		// deleting choices that have no text, image or target
		$emptyChoices = $this->models->KeyModel->getEmptyChoices(array('project_id' => $this->getCurrentProjectId()));

        foreach ((array)$emptyChoices as $val)
		{
			$this->deleteKeystepChoice($val['id']);
		}

		// resetting non-existant target steps
		$nonExistantKeyTargets = $this->models->KeyModel->getNonExistantKeyTargets(array('project_id' => $this->getCurrentProjectId()));

        foreach ((array)$nonExistantKeyTargets as $val)
		{
			$this->models->ChoicesKeysteps->update(array(
				'res_keystep_id' => 'null'
			), array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $val['id']
			));
		}

		// resetting non-existant target taxa
		$nonExistantKeyTaxa = $this->models->KeyModel->getNonExistantKeyTaxa(array('project_id' => $this->getCurrentProjectId()));

        foreach ((array)$nonExistantKeyTaxa as $val)
		{
			$this->models->ChoicesKeysteps->update(array(
				'res_taxon_id' => 'null'
			), array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $val['id']
			));
		}
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

	private function getStepsLeadingToThisOne( $thisOne )
	{
		return
			$this->models->KeyModel->getStepsLeadingToThisOne(
				array(
					'res_keystep_id' => $thisOne,
					'language_id' => $this->getDefaultProjectLanguage(),
					'project_id' => $this->getCurrentProjectId(),
				));
	}

    private function detachAllMedia ()
    {
        if (empty($this->_mc)) {
            return false;
        }
        
        $media = $this->_mc->getItemMediaFiles();

        if (!empty($media)) {
            foreach ($media as $i => $item) {
                $this->_mc->deleteItemMedia($item['id']);
            }
        }
    }

    private function getChoiceImage ($itemId = false)
    {
		
		if ( !$this->use_media ) return;
		
        if (!$itemId) {
            $itemId = $this->rGetId();
        }

        $this->_mc->setItemId($itemId);

    	// Append image to choice
        $img = $this->_mc->getItemMediaFiles();

        if (!empty($img) && $img[0]['media_type'] ==  'image') {
            return $img[0]['rs_original'];
        }

        return null;
    }

	private function getLinkedTaxa( $id )
	{
		return
			$this->models->KeyModel->getLinkedTaxa(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'keystep_id' => $id,
				));
	}

	private function saveLinkedTaxa( $data )
	{
		if ( !isset($data['id']) ) {
		    return;
        }
		if ( !isset($data['new_taxa']) ) {
		    return;
        }

		foreach((array)$data['new_taxa'] as $val)
		{
			$this->models->KeystepsTaxa->save([
				'project_id' => $this->getCurrentProjectId(),
				'keystep_id' => $data['id'],
				'taxon_id' => $val
			]);
		}
	}

	private function deleteLinkedTaxa( $data )
	{
		if ( !isset($data['link_id']) ) {
		    return;
        }

		$this->models->KeystepsTaxa->delete([
			'project_id' => $this->getCurrentProjectId(),
			'id' => $data['link_id']
		]);
	}


	private function updateChoice( $data )
	{
		$id=isset($data['id']) ? $data['id'] : null;
		//$step=isset($data['id']) ? $data['id'] : null;
		$choice_txt=isset($data['choice_txt']) ? $data['choice_txt'] : null;
		$res_keystep_id=isset($data['res_keystep_id']) ? $data['res_keystep_id'] : null;
		$res_taxon_id=isset($data['res_taxon_id']) ? $data['res_taxon_id'] : null;

		if ( is_null($id) ) {
		    return;
        }

		if ( $res_keystep_id==-1 ) {
            $next_step_id = $this->createNewKeystep();
        } else {
			$next_step_id = $res_keystep_id;
        }
		$changes=0;

		$before = $this->models->ChoicesKeysteps->_get(
		    array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        );
		$this->models->ChoicesKeysteps->update(
			array(
				'res_keystep_id' => $next_step_id === '0' ? 'null' : $next_step_id,
				'res_taxon_id' => $next_step_id !== '0' ? 'null' : ( $res_taxon_id === '0' ? 'null' : $res_taxon_id )
			),
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);
        $after = $this->models->ChoicesKeysteps->_get(
            array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        );

		$changes += $this->models->ChoicesKeysteps->getAffectedRows();

		foreach((array)$choice_txt as $language_id=>$txt)
		{
			$txt=trim($txt);

			if (strlen(preg_replace(array('/^\<[^\*?]\/\>/','/\<[^\*?]\/\>$/'),'',$txt))==0)
			{
				$this->models->ChoicesContentKeysteps->delete(
					array(
						'choice_id' => $id,
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $language_id
					)
				);
			} else {

				$before = $this->models->ChoicesContentKeysteps->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'choice_id' => $id,
                            'language_id' => $language_id
                        )
				    )
                );

				$after = array(
					'id' => isset($before[0]['id']) ? $before[0]['id'] : null,
					'project_id' => $this->getCurrentProjectId(),
					'choice_id' => $id,
					'language_id' => $language_id,
					'choice_txt' => $txt
				);

				$this->models->ChoicesContentKeysteps->save( $after );

			}

			$changes+=$this->models->ChoicesContentKeysteps->getAffectedRows();

		}

        $this->logChange(array(
            'before' => $before[0],
            'after' => $after,
            'note' => 'Updated Keys step choice'
        ));

		if ( $changes>0 ) {
			$this->addMessage( $this->translate('Saved.') );
		}

	}
}
