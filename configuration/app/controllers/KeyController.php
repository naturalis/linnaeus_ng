<?php

include_once ('Controller.php');
include_once ('ModuleSettingsController.php');
include_once ('MediaController.php');

class KeyController extends Controller
{
    private $_taxaStepList;
	private $_choiceList = array();
    private $_tempList = array();
	private $_keyPathMaxItems=3;
	public $currentKeyStepId;
	private $choiceKeystepTable;
	private $stepsDone;
	private $startStep;


    public $usedModels = array(
		'keysteps',
		'content_keysteps',
		'choices_keysteps',
		'choices_content_keysteps',
    );

    public $controllerPublicName = 'Dichotomous key';
    public $controllerBaseName = 'key';

	public $cssToLoad = array(
		'key.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'key.js',
				'dialog/jquery.modaldialog.js',
				'lookup.js'
			),
			'IE' => array(
			)
		);


    public function __construct($p=null)
    {
        parent::__construct($p);
        $this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->moduleSettings=new ModuleSettingsController;
        $this->setMediaController();
	}

    public function indexAction()
    {

        $this->setPageName( $this->translate('Index'));

		// get user's decision path
		$keyPath = $this->getKeyPath();

		/*
			if user directly access a specific step or choice while there is no keypath (thru bookmark), a possible decision path is created
			caveat: in current set up, if a user does this while there *is* a keypath, this will do nothing. use forcetree=1 to force.
		*/
		if ((is_null($keyPath) && ($this->rHasVal('choice') || $this->rHasVal('step'))) || ($this->rHasVal('forcetree','1') || $this->rHasVal('r')))
		{
			$this->createPathInstantly(
				($this->rHasVal('step') ? $this->rGetVal('step') : null),
				($this->rHasVal('choice') ? $this->rGetVal('choice') : null)
			);
		}

		if ($this->rHasVal('step'))
		{
			// step points at a specific step, from keypath
			$step = $this->getKeystep($this->rGetVal('step'));
			$this->updateKeyPath(array('step' => $step,'fromPath' => true));
		}
		else
		if ($this->rHasVal('choice'))
		{
			// choice is choice clicked by user
			$choice = $this->getKeystepChoice($this->rGetVal('choice'));

			// choice points to a taxon
			if (!empty($choice['res_taxon_id']))
			{
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
			else
			{
				// choice points to a next step
				$step = $this->getKeystep($choice['res_keystep_id']);
				$this->updateKeyPath(array('step' => $step,'choice' => $choice));
			}
		}
		else
		if (!$this->rHasVal('start','1') && !empty($keyPath))
		{
			// restore previous state
			$d = array_pop($keyPath);
			$step = $this->getKeystep($d['id']);
		}
		else
		{
			// no step or choice specified, must be the start of the key
			$this->resetKeyPath();
			$step = $this->getKeystep($this->getStartKeystepId());
			$this->updateKeyPath(array('step' => $step));
		}

		$taxa = $this->getTaxonDivision($step['id']);

        $this->smarty->assign('keyPathMaxItems', $this->_keyPathMaxItems);
		$this->smarty->assign('keyType',$this->getKeytype());
		$this->smarty->assign('taxaState',$this->getTaxaState());
		$this->smarty->assign('remaining',$taxa['remaining']);
		$this->smarty->assign('excluded',$taxa['excluded']);

		$this->setPageName(sprintf($this->translate('Dichotomous key: step %s: "%s"'),$step['number'],$step['title']));

		$this->setCurrentKeyStepId($step['id']);

		//unset($_SESSION['app'][$this->spid()]['search']['hasSearchResults']);

		// get step's choices
		if (isset($step)) $choices = $this->getKeystepChoices($step['id']);
		if (isset($step)) $this->smarty->assign('step',$step);
		if (isset($choices)) $this->smarty->assign('choices',$choices);
		$this->smarty->assign('keypath',$this->getKeyPath());

		$this->printPage();
    }

    private function setStepType ($choices)
    {
		/*

			this type overrides the type set in the settings (l2 or lng)
			and decides on how to display the key based on the actual available
			values.
			index_l2_txt: text choices based on the name and text of the step
						  and choices
			index_l2_pct: l2 picture key, clickable pictures without *any* text,
						  even if there is some

		*/

        foreach ((array)$choices as $choice)
		{
            if (!empty($choice['choice_image_params']))
                return 'index_l2_pct';
        }

		return 'index_l2_txt';

    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list'))
		{
            $this->getLookupList();
        }

        if ($this->rHasVal('action','store_remaining'))
		{
        	$this->setTaxaState('remaining');
        }

        if ($this->rHasVal('action','store_excluded'))
		{
        	$this->setTaxaState('excluded');
        }

        $this->smarty->assign('keyPathMaxItems', $this->_keyPathMaxItems);
		$this->smarty->assign('keyType',$this->getKeytype());

        $this->printPage();
    }

	/* function exists sole for the benefit of the preview overlay's "back to editing"-button */
	public function getCurrentKeyStepId()
	{
		return $this->currentKeyStepId;
	}

	private function _createPathInstantly($stepId=null,$choiceId=null)
	{

		// choice ID present: probably a bookmarked step in the key
		if (!is_null($choiceId))
		{
			// get choice requested
			$d = $this->models->ChoicesKeysteps->_get(
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
			if (count((array)$this->tmp['results'])==0)
			{
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
			$parentStep = $this->getKeystep($d[0]['keystep_id']);

			// find the choice that lead to this step, i.e., the previous step in the path
			$prevChoice = $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'res_keystep_id' => $d[0]['keystep_id']
					)
				)
			);

			$choice = $this->getKeystepChoice($choiceId);
			$choiceMarker = chr($d[0]['show_order'] + 96);

			// add step & choice to the path
			array_unshift(
				$this->tmp['results'],
				array(
					'id' => $parentStep['id'],
					'step_number' => $parentStep['number'],
					'step_title' => $parentStep['title'],
					'is_start' => $parentStep['is_start'],
					'choice_marker' => $choiceMarker,
					'choice_txt' => $this->formatPathChoice($choice, $parentStep['number'], $choiceMarker)
				)
			);

		} else
		// no choice, just a step ID defined: most likely a link in the text migrated from L2
		if (!is_null($stepId))
		{
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
			$prevChoice = $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'res_keystep_id' => $step['id']
					)
				)
			);
		}
		else
		{
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
			($this->rHasVal('step') ? $this->rGetVal('step') : null),
			($this->rHasVal('choice') ? $this->rGetVal('choice') : null)
		);

		$this->setKeyPath($this->tmp['results']);
	}


	/* steps and choices */
	private function getKeystep($id)
	{
        if (empty($id))  return;

		$k = $this->models->Keysteps->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		if (!$k) return;

		$step = $k[0];

		$ck = $this->models->ContentKeysteps->_get(
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
		$step['content'] = $ck[0]['content'];
		$step['content'] = $this->matchHotwords($step['content']);

		return $step;
	}

	private function setCurrentKeyStepId($id)
	{
		$this->currentKeyStepId = $id;
	}

	public function getStartKeystepId()
	{
		$k = $this->models->Keysteps->_get(
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
		if ($choice == null)
		{
			// get all choices available for this keystep
			$choices =  $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'keystep_id' => $step
					),
					'order' => 'show_order'
				)
			);
		}
		else
		{
			// get single choice
			$choices =  $this->models->ChoicesKeysteps->_get(
				array(
					'id' => array(
						'id' => $choice,
						'project_id' => $this->getCurrentProjectId(),
					)
				)
			);
		}

		foreach((array)$choices as $key => $val)
		{

			$choices[$key]['choice_img'] = $this->getChoiceImage($val['id']);

			if ($includeContent)
			{
				// get the actual language-sensitive content for each choice
				$cck = $this->models->ChoicesContentKeysteps->_get(
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

				if (isset($cck[0]['choice_txt']))
				{
					$choices[$key]['choice_txt'] = $cck[0]['choice_txt'];
					$choices[$key]['choice_txt'] = $this->matchHotwords(trim($choices[$key]['choice_txt']));
				}
			}

			// resolve the targets to either a next step or a taxon
			if (!empty($val['res_keystep_id']) && $val['res_keystep_id']!=0)
			{
				if ($val['res_keystep_id']=='-1')
				{
					//unfinished target (shouldn't happen)
					$choices[$key]['target'] = null;
				}
				else
				{
					// target is a next step
					$k = $this->getKeystep($val['res_keystep_id']);
					if (isset($k['title'])) $choices[$key]['target'] = $k['title'];
					if (isset($k['number'])) $choices[$key]['target_number'] = $k['number'];
				}
			} else
			if (!empty($val['res_taxon_id']))
			{
				// target is a taxon
				if ($includeContent)
				{
					$t = $this->models->Taxa->_get(array('id' => $val['res_taxon_id']));

					if (isset($t['taxon']))
					{
						$choices[$key]['target'] = $this->formatTaxon($t);
						$choices[$key]['is_hybrid'] = $t['is_hybrid'];
					}
				}
			}
			else
			{
				$choices[$key]['target'] = null;
			}

			$choices[$key]['marker'] = chr($val['show_order'] + 96);

			if ($val['choice_image_params']!='')
			{
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
		$this->moduleSession->setModuleSetting(array('setting'=>'path'));
	}

	private function getKeyPath()
	{
		return !is_null($this->moduleSession->getModuleSetting('path')) ? $this->moduleSession->getModuleSetting('path') : null;
	}

	private function setKeyPath($path)
	{
		$this->resetKeyPath();
		$this->moduleSession->setModuleSetting(array('setting' => 'path', 'value' => $path));
	}

	private function updateKeyPath($params)
	{
		$step = $params['step'];
		$choice = isset($params['choice']) ? $params['choice'] : null;
		$fromPath = isset($params['fromPath']) ? $params['fromPath'] : null;

		// @TODO: taxon is not used at all in this function
		$taxon = isset($params['taxon']) ? $params['taxon'] : null;

		/*
		 * @TODO: can we get rid of this?
		 *
		if (is_null($this->moduleSession->getModuleSetting('path')))
		{
			//$this->setStoredKeypath($step);
		}
		*/

		if (!is_null($this->moduleSession->getModuleSetting('path')))
		{
			// keypath already exists...
			if ($fromPath)
			{
				// ...user clicked somewhere in the path, so we copy the existing path up to the step he clicked
				foreach((array)$this->moduleSession->getModuleSetting('path') as $key => $val)
				{
					if ($val['id']==$step['id']) break;
					if (!empty($val['id'])) $d[] = $val;
				}
			}
			else
			{
				// user clicked a choice, existing path remains as it is
				$d = $this->moduleSession->getModuleSetting('path');
			}
		}

		if (!isset($d) || (isset($d) && $d[count((array)$d)-1]['id']!=$step['id']))
		{
			// if we have no path, or have a path whose previous step is not the same as the current one, we add the current step
			$d[] = array(
				'id' => $step['id'],
				'step_number' => $step['number'],
				'step_title' => $step['title'],
				'is_start' => $step['is_start'],
				'choice_marker' => null,
			);

		}


		if (!empty($choice) && (count((array)$d)>1))
		{
			// the choice clicked to reach the current step belongs to the previous step, and ahs to be added there
			$d[count((array)$d)-2]['choice_marker'] = $choice['marker'];
			$d[count((array)$d)-2]['choice_txt'] = $this->formatPathChoice(
				$choice,
				$d[count((array)$d)-2]['step_number'],
				$d[count((array)$d)-2]['choice_marker']
			);

		}

		$this->moduleSession->setModuleSetting(array('setting' => 'path', 'value' => $d));

	}

	private function getStepsByTarget($step)
	{
		$data=array();

		if (!isset($this->stepsDone[$step]) || $this->stepsDone[$step]!=true)
		{
			$this->stepsDone[$step]=true;
			array_push($data,$step);

			if (isset($this->choiceKeystepTable[$step]))
			{
				$d=$this->choiceKeystepTable[$step];

				foreach((array)$d as $val)
				{
					$r=$this->getStepsByTarget($val['keystep_id']);
					if ($r) $data=array_merge($data,$r);
				}
			}
		}

		return $data;
	}

	private function setChoiceKeystepTable()
	{
		$d = $this->models->KeyModel->setChoiceKeystepTable($this->getCurrentProjectId());

		foreach((array)$d as $key=>$val)
		{
			$this->choiceKeystepTable[$val['res_keystep_id']][]['keystep_id']=$val['keystep_id'];
		}
	}

	private function getAllStepsByTarget()
	{
		$this->setChoiceKeystepTable();

		$choiceLeadingToATaxon = $this->models->KeyModel->getChoicesLeadingToATaxon(array(
            'projectId' => $this->getCurrentProjectId(),
		    'languageId' => $this->getCurrentLanguageId(),
		    'nametype_id_preferredname' => $this->getNameTypeId( PREDICATE_PREFERRED_NAME )
		));

		$stepsByTarget=array();

		foreach((array)$choiceLeadingToATaxon as $val)
		{
			$stepsByTarget[$val['res_taxon_id']]['steps'][]=$val['keystep_id'];
			$stepsByTarget[$val['res_taxon_id']]['data']=array(
				'id'=>$val['res_taxon_id'],
				'taxon'=>$val['taxon'],
				//'is_hybrid'=>$val['is_hybrid'],
				'commonname'=>$val['commonname'],
				//'label'=>$this->formatTaxon($val,$ranks)
			);
		}

		foreach((array)$stepsByTarget as $key=>$steps)
		{
			foreach($steps['steps'] as $step)
			{
				$r=$this->getStepsByTarget($step);
				if ($r)
					$stepsByTarget[$key]['steps']=
						array_unique(array_merge($stepsByTarget[$key]['steps'],$r));
				unset($this->stepsDone);
			}
		}

		usort(
			$stepsByTarget,
			function($a,$b)
			{
				return
					($a['data']['taxon'] > $b['data']['taxon'] ?
						1 :
						($a['data']['taxon'] < $b['data']['taxon'] ?
							-1 :
							0
						)
					);
			}
		);


		return $stepsByTarget;

	}

	/* branches and fruits */
	public function getTaxonDivision($step)
	{
		$in=$out=array();

		$allsteps=$this->getAllStepsByTarget();
		$start=$this->getStartKeystepId();
		$this->startStep=$start;

		foreach($allsteps as $key=>$val)
		{
			// taxa that are present but unreachable from the start of the key (separate key sections)
			if (!in_array($start,$val['steps']))
				continue;

			if (in_array($step,$val['steps']))
			{
				array_push($in,$val['data']);
			}
			else
			{
				array_push($out,$val['data']);
			}
		}

		return
			array(
				'remaining'=>$in,
				'excluded'=>$out
			);
	}

	private function setTaxaState($state)
	{
		$this->moduleSession->setModuleSetting(array('setting' => 'taxaState', 'value' => $state));
	}

	private function getTaxaState()
	{
		return
			(!empty($this->moduleSession->getModuleSetting('taxaState')) ?
				$this->moduleSession->getModuleSetting('taxaState') :
				'remaining');
	}

	/* the rest */
	private function getKeytype()
	{
		return $this->moduleSettings->getSetting('keytype');
	}

	private function getLookupList()
	{
		$steps=$this->models->KeyModel->getKeystepList( [
            'project_id' => $this->getCurrentProjectId(),
		    'language_id' => $this->getCurrentLanguageId(),
		] );
		
		foreach((array)$steps as $key=>$val)
		{
			$steps[$key]['label']=$this->translate('Step').' '.$val['number'].(!empty($val['label']) && $val['label']!=$val['number'] ? '. '.$val['label'] : '');
		}
		
		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$steps,
				'module'=>'key',
				'url'=>'index.php?forcetree=1&step=%s',
				'encode'=>true
			))
		);
	}

	private function formatPathChoice($choice, $step = null, $choiceMarker = null)
	{
		if (!isset($choice['choice_txt'])) return;
		$remove = $step . $choiceMarker . '. ';
		$toSpace = array('<br />', '<br>');
		return str_replace($remove, '', strip_tags(str_replace($toSpace, ' ', $choice['choice_txt'])));
	}

    private function getChoiceImage($itemId = false)
    {
        if (!$itemId) {
            $itemId = $this->rGetId();
        }

        $this->_mc->setItemId($itemId);

    	// Append image to choice
        $img = $this->_mc->getItemMediaFiles();
        if (!empty($img) && $img[0]['media_type'] == 'image') {
            return $img[0]['rs_original'];
        }

        return null;
    }

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId('key'));
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getCurrentLanguageId());
	}

}
