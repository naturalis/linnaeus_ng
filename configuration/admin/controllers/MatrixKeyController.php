<?php

include_once ('Controller.php');
include_once ('MediaController.php');
include_once ('ModuleSettingsReaderController.php');

class MatrixKeyController extends Controller
{
    private $_mc;
    private $_useCharacterGroups = false;

    public $usedModels = array(
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'characteristics',
        'characteristics_matrices',
        'characteristics_labels',
        'characteristics_states',
        'characteristics_labels_states',
        'chargroups_labels',
        'chargroups',
        'characteristics_chargroups',
        'matrices_variations',
		'gui_menu_order'
    );

    public $usedHelpers = array(
        'file_upload_helper',
		'session_module_settings',
    );
    public $controllerPublicName = 'Multi-entry key';

    public $cssToLoad = array(
        'matrix.css',
        'prettyPhoto/prettyPhoto.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'matrix.js',
			'keys-endpoint.js',
            'prettyPhoto/jquery.prettyPhoto.js'
        )
    );

	private $settings;

    private $characteristicTypes=array(
		array('name'=>'text','info'=>'a textual description.'),
		array('name'=>'media','info'=>'an image, video or soundfile.'),
		array('name'=>'range','info'=>'a value range, defined by a lowest and a highest value.'),
		array('name'=>'distribution','info'=>'a value distribution, defined by a mean and values for one and two standard deviations.')
	);

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    private function initialize()
    {
		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->moduleSettings->setUseDefaultWhenNoValue( true );
		$this->moduleSettings->assignModuleSettings( $this->settings );

		$this->_useCharacterGroups = $this->moduleSettings->getModuleSetting('use_character_groups')==1;
		$this->_useVariations=$this->moduleSettings->getModuleSetting(array('module'=>'species','setting'=>'use_variations'))=='1';

        $this->setDefaultMatrix();

        $this->smarty->assign('useCharacterGroups', $this->_useCharacterGroups);
        $this->smarty->assign('languages', $this->getProjectLanguages());
        $this->smarty->assign('activeLanguage', $this->getDefaultProjectLanguage());

		$this->use_media=$this->moduleSettings->getModuleSetting( [ 'setting'=>'no_media','subst'=>0 ] )!=1;

		if ( $this->use_media )
		{
			$this->setMediaController();
		}

		$this->smarty->assign( 'use_media', $this->use_media );
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function indexAction()
    {
        $this->checkAuthorisation();

		// direct access
        if ( $this->rHasId() )
		{
            $this->setCurrentMatrixId( $this->rGetId() );
            $this->redirect( 'edit.php' );
        }

		// fetching the default matrix
		$id=$this->getDefaultMatrixId();
		if ( !is_null($id) )
		{
			$this->setCurrentMatrixId( $id );
            $this->redirect( 'edit.php' );
		}

		// fetching *any* matrix (apparently no default is set)
		$m=$this->getMatrices();
		$m=array_shift( $m );

		if ( !is_null($m) )
		{
			$this->setCurrentMatrixId( $m['id'] );
            $this->redirect( 'edit.php' );
		}

		// there's no matrices! let's make one.
		$this->redirect( 'new.php' );
    }

    public function manageAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Management'));

        $this->checkAuthorisation();

        $this->printPage();
    }

    public function newAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );

        $this->checkAuthorisation();

        $this->setPageName( $this->translate( 'New matrix' ) );

        if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			if ( $this->rHasVar( 'sys_name' ) && !empty($this->rGetVal( 'sys_name' ) ) )
			{
				$id=$this->createNewMatrix( $this->rGetVal( 'sys_name' ) );

				if ( $id )
				{
					$names=$this->rGetVal( 'name' );

					foreach((array)$names as $language_id => $name)
					{
						$this->saveMatrixName( array(
							'matrix_id' => $id,
							'language_id' =>  $language_id,
							'name' => $name
						));
					}

					$this->setCurrentMatrixId( $id );

					$this->redirect('edit.php');
				}
				else
				{
					$this->addError( $this->translate('Could not create new matrix.') );
				}
			}
			else
			{
				$this->addError( 'Internal name is required.' );
				$this->smarty->assign( 'name' ,  $this->rGetVal( 'name' ) );
			}
        }

        $this->printPage();
    }

    public function matricesAction()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('default') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
	        $this->checkAuthorisation();
			$this->setDefaultMatrix($this->rGetVal('default'));
			$this->redirect('matrices.php?');
		}

		if ($this->rHasVal('action','activate') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
	        $this->checkAuthorisation();
            $this->setCurrentMatrixId($this->rGetId());
            $this->redirect('edit.php?');
        }

        if ($this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
	        $this->checkAuthorisation();
            if ($this->getCurrentMatrixId()==$this->rGetId()) $this->setCurrentMatrixId( null );
            $this->deleteMatrix( $this->rGetId() );
			$this->redirect('matrices.php?');
        }

        $matrices=$this->getMatrices();

        if (count((array)$matrices)==0) $this->redirect('matrix.php');

		/*
        if ($this->rHasVal('imgdim'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
	        $this->checkAuthorisation();
			$this->reacquireStateImageDimensions($this->rGetVal('imgdim'));
		}
		*/

        $this->setPageName($this->translate('Matrices'));

        $this->smarty->assign('matrices', $matrices);

        $this->printPage();
    }

    public function matrixAction()
    {
        $this->checkAuthorisation();

        if ( $this->rHasId() )
		{
			if ($this->rHasVal('action','save') && !$this->isFormResubmit())
			{

				$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
				$this->checkAuthorisation();

				if ( $this->rHasVar( 'sys_name' ) && !empty($this->rGetVal( 'sys_name' ) ) )
				{
					$n=$this->saveMatrixSysName( array(
						'matrix_id' => $this->rGetId(),
						'name' => $this->rGetVal( 'sys_name' )
					));

					if ($n>0) $this->addMessage( sprintf( 'Saved "%s".', $this->rGetVal( 'sys_name' ) ) );
				}

				$names=$this->rGetVal( 'name' );

				foreach((array)$names as $language_id => $name)
				{
					$n=$this->saveMatrixName( array(
						'matrix_id' => $this->rGetId(),
						'language_id' =>  $language_id,
						'name' => $name
					));

					if ($n>0) $this->addMessage( sprintf( 'Saved "%s".', $name ) );
				}

			}

            $matrix=$this->getMatrix( $this->rGetId() );
			$this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['names'][$this->getDefaultProjectLanguage()]['name']), $this->translate('Editing matrix'));
            $this->smarty->assign('matrix', $matrix);
        }
		else
		{
			$this->redirect('new.php');
		}

        $this->printPage();
    }

	public function editAction()
    {
        $this->checkAuthorisation();

        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');

        $matrix=$this->getMatrix( $this->getCurrentMatrixId() );

        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['label']), $this->translate('Editing matrix'));

        if ($this->rHasVal('char')) $this->smarty->assign('activeCharacteristic', $this->rGetVal('char'));
        if ($this->_useVariations) $this->smarty->assign('variations', $this->getVariationsInMatrix());

		$this->smarty->assign( 'characteristics', $this->getCharacteristics() );
        $this->smarty->assign( 'taxa', $this->getTaxa() );
        $this->smarty->assign( 'matrix', $matrix );
        $this->smarty->assign( 'matrices', $this->getMatrices() );

        $this->printPage();
    }

    public function charSortAction()
    {
		$this->checkAuthorisation();

        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');

        if ($this->rHasVal('characters') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$i=0;
			foreach((array)$this->rGetVal('characters') as $val)
			{
				$this->updateCharShowOrder($val,$i++);
			}

			$this->addMessage('New state order saved.');
			//$this->renumberCharShowOrder();
		}

        $matrix = $this->getMatrix( $this->getCurrentMatrixId() );

        $this->setPageName( $this->translate('Editing matrix: sort characters') );

        $this->smarty->assign('characteristics', $this->getCharacteristics());
        $this->smarty->assign('matrix', $matrix);

        $this->printPage();
    }

    public function charGroupsAction()
    {
        $this->checkAuthorisation();

        if (!$this->_useCharacterGroups)
            redirect('edit.php');

        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');

        $matrix=$this->getMatrix( $this->getCurrentMatrixId() );

        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['label']), $this->translate('Editing matrix'));

        if ($this->rHasVal('delete') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->deleteCharacteristicFromGroup(array('groupId'=>$this->rGetVal('delete')));
			$this->deleteCharacterGroup(array('groupId'=>$this->rGetVal('delete')));
			$this->deleteGUIMenuOrder();
		}

        if ($this->rHasVal('chars') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->deleteCharacteristicFromGroup();

			foreach((array)$this->rGetVal('chars') as $key => $val)
			{
				$val = explode(':',$val);
				if ($val[1]==0) continue;
				$this->saveCharacteristicToGroup(array('charId'=>$val[0],'groupId'=>$val[1],'showOrder'=>$key));
			}

			$this->deleteGUIMenuOrder();
		}

		if ($this->rHasVal('new') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$c = $this->getCharacterGroups(array('label'=>$this->rGetVal('new')));

			if (!empty($c))
			{
				$this->addError(sprintf($this->translate('A group named "%s" already exists.'),$this->rGetVal('new')));
			}
			else
			{
				$this->saveCharacterGroup(array('label' => $this->rGetVal('new')));
			}

			$this->deleteGUIMenuOrder();

		}

		$g=$this->getCharacterGroups();
		$c=$this->getCharactersNotInGroups();

		$this->smarty->assign('groups', $g);
        $this->smarty->assign('characteristics', $c);
        $this->smarty->assign('matrix', $matrix);

        $this->printPage();
    }

    public function charGroupsSortAction()
    {
        $this->checkAuthorisation();

        if (!$this->_useCharacterGroups)
            $this->redirect('edit.php');

        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');

        $matrix = $this->getMatrix( $this->getCurrentMatrixId() );

        $this->setPageName(sprintf($this->translate('Editing matrix "%s"'), $matrix['label']), $this->translate('Editing matrix'));

		if ($this->rHasVal('order') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->deleteGUIMenuOrder();

			foreach((array)$this->rGetVal('order') as $key => $val)
			{
				$d = explode('-',$val);

				if ($d[1]==1) continue;

				$this->saveGUIMenuOrder(array('type'=>$d[1],'id'=>$d[2],'order'=>$key));
			}
		}

		$g = $this->getCharacterGroups();
		$c = $this->getCharactersNotInGroups();
		$m = $this->getGUIMenuOrder();

		$m = $this->effectuateGUIMenuOrder(array('groups'=>$g,'characters'=>$c,'menu'=>$m,));

        $this->smarty->assign('menuorder', $m);

        $this->smarty->assign('matrix', $matrix);

        $this->printPage();
    }

    public function charAction()
    {
        $this->checkAuthorisation();

        // need an active matrix to assign the charcter to
        if ( is_null($this->getCurrentMatrixId() ) ) $this->redirect('matrices.php');

        // get the current active matrix' id
        $matrix=$this->getMatrix( $this->getCurrentMatrixId() );

        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Matrix'),
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
        ));

        if (!$this->rHasId() && $this->rHasVal('action', 'save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

            $id=$this->createCharacteristic();
			$this->updateCharacteristic( $id, $this->rGetAll() );
			$this->addCharacteristicToMatrix( $id );
			$this->renumberCharShowOrder();
			$character=$this->getCharacteristic( $id );
			$this->addMessage( sprintf( $this->translate('Saved "%s".'), $character['sys_name'] ) );

        }
		else
        if ($this->rHasId() && $this->rHasVal('action', 'save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->updateCharacteristic( $this->rGetId(), $this->rGetAll() );
			$this->addMessage( $this->translate('Saved') );
        }
		else
        if ($this->rHasId() && $this->rHasVal('action', 'delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            // delete the char from this matrix (and automatically delete the char itself if it isn't used in any other matrix)
            $this->deleteCharacteristic( $this->rGetId() );
            $this->renumberCharShowOrder();
            $this->redirect('edit.php');
        } else
		if ($this->rHasVal('existingChar') && $this->rHasVal('action', 'use'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $this->addCharacteristicToMatrix($this->rGetVal('existingChar'));
            $this->renumberCharShowOrder();
            $this->redirect('edit.php');
        }

        if ( $this->rHasId() )
		{
			$character=$this->getCharacteristic( $this->rGetId() );
		}

		if ( isset($character) )
		{
			$this->setPageName(sprintf($this->translate('Editing character "%s"'), $character['label']), $this->translate('Editing character'));
			$this->smarty->assign( 'characteristic', $character );
		}
		else
		{
			$this->setPageName($this->translate('New character'));
		}

        $this->smarty->assign( 'languages', $this->getProjectLanguages() );
        $this->smarty->assign( 'matrix', $matrix );
        $this->smarty->assign( 'charLib', $this->getAllCharacteristics($this->getCurrentMatrixId() ) );
        $this->smarty->assign( 'charTypes', $this->characteristicTypes );

        $this->printPage();


    }

    public function taxaAction()
    {
        $this->checkAuthorisation();

        if ($this->getCurrentMatrixId() == null)
		{
            $this->redirect('matrices.php');
		}

        $this->setPageName($this->translate('Adding taxa'));

        if ($this->rHasVal('taxon') || $this->rHasVal('variation'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            if ($this->rHasVal('taxon'))
			{
                foreach ((array) $this->rGetVal('taxon') as $val) {

                    $this->models->MatricesTaxa->save(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
                        'matrix_id' => $this->getCurrentMatrixId(),
                        'taxon_id' => $val
                    ));
                }
            }

            if ($this->rHasVal('variation'))
			{
                foreach ((array) $this->rGetVal('variation') as $val)
				{
                    $this->models->MatricesVariations->save(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
                        'matrix_id' => $this->getCurrentMatrixId(),
                        'variation_id' => $val
                    ));
                }
            }


            if ($this->rGetVal('action') != 'repeat')
			{
                $this->redirect('edit.php');
            }

            $this->addMessage(sprintf($this->translate('Taxon added.')));
        }


		$d=array(
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => $this->getCurrentMatrixId()
		);

		$this->UserRights->setUserItems();
		$this->userItems=$this->UserRights->getUserItems();

		if ( !empty($this->userItems) )
		{
			$d['branch_tops']=$this->userItems;
		}

        $taxa=$this->models->MatrixkeyModel->getAllTaxaAndMatrixPresence( $d );

		foreach((array)$taxa as $key=>$val)
		{
			$taxa[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		$this->smarty->assign('taxa', $taxa);

        if ($this->_useVariations)
            $this->smarty->assign('variations', $this->getVariations());

        $this->printPage();
    }

    public function stateAction()
    {
        $this->checkAuthorisation();

        if ($this->getCurrentMatrixId() == null)
            $this->redirect('matrices.php');

        if ( !$this->rHasId() && $this->rHasVal('action', 'save') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();
			$d=$this->rGetAll();
			$d['id']=$this->createState();
			$this->saveState( $d );

			if ($this->rHasVal('repeat', '0'))
			{
				$this->redirect('edit.php');
			}
			else if ($this->rHasVal('repeat', '1'))
			{
				$this->redirect('state.php?char=' . $this->rGetVal('char') );
			}
			else {
                $this->redirect('state.php?char=' . $this->rGetVal('char') . '&id=' . $d['id']);
			}
		}
        else
        if ( $this->rHasId() && $this->rHasVal('action', 'save') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->saveState( $this->rGetAll() );

			if ($this->rHasVal('repeat','0'))
			{
				$this->redirect('edit.php');
			}
			else
			{
				$this->redirect('state.php?char=' . $this->rGetVal('char') );
			}
        }
        else
        if ( $this->rHasId() && $this->rHasVal('action', 'delete') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $this->deleteCharacteristicState();
            $this->redirect('edit.php');
        }
        else
		if ( $this->rHasId() && $this->rHasVal('action', 'deleteimage') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
            //$this->deleteCharacteristicStateImage();
			if ( $this->use_media )
			{
				$this->detachAllMedia();
			}
        }

		if ($this->rHasId())
		{
            $state = $this->getState($this->rGetId());

            if (isset($state))
			{
                $this->requestData['char'] = $state['characteristic_id'];
			}
        }
        else
		if (!$this->rHasVal('char'))
		{
			$this->redirect('edit.php');
        }

        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Matrix'),
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/edit.php'
        ));

        $characteristic=$this->getCharacteristic( $this->rGetVal('char') );
        $state=$this->getState( $this->rGetId() );

         // existing state
        if (isset($state['label']))
		{
            $this->setPageName(sprintf($this->translate('Editing state for "%s"'), $characteristic['label']), $this->translate('Editing state'));
        }
        // new state
        else
		{
            $this->setPageName(sprintf($this->translate('New state for "%s"'), $characteristic['label']), $this->translate('New state'));
        }

        if (($this->rHasVal('action', 'save') || $this->rHasVal('action', 'repeat')) && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $filesToSave = $this->getUploadedMediaFiles();

            if (!$this->verifyData($this->rGetAll(), $filesToSave))
			{
                $state = $this->rGetAll();
            }
            else
			{
                $this->models->CharacteristicsStates->save(
                array(
                    'id' => ($this->rHasId() ? $this->rGetId() : null),
                    'project_id' => $this->getCurrentProjectId(),
                    'characteristic_id' => $this->rGetVal('char'),
                    //'file_name' => null !== ($filesToSave[0]['name']) ? $filesToSave[0]['name'] : null,
                    'lower' => null !== $this->rGetVal('lower') ? $this->rGetVal('lower') : null,
                    'upper' => null !== $this->rGetVal('upper') ? $this->rGetVal('upper') : null,
                    'mean' => null !== $this->rGetVal('mean') ? $this->rGetVal('mean') : null,
                    'sd' => null !== $this->rGetVal('sd') ? $this->rGetVal('sd') : null
                ));

				//$this->saveStateImageDimensions($state);
				//$this->reacquireStateImageDimensions();

                unset($state);

                if ($this->rGetVal('action')!='repeat')
				{
                    $this->redirect('edit.php?char=' . $this->rGetVal('char'));
                }

                $this->addMessage(sprintf($this->translate('State "%s" saved.'), $this->rGetVal('label')));

				$state = $this->getState($this->createState());
				//$state = $this->getState($this->rGetId());
            }
        }

        if (isset($state))  $this->smarty->assign('state', $state );

        $this->smarty->assign( 'matrix', $this->getMatrix( $this->getCurrentMatrixId() ) );
        $this->smarty->assign( 'allowedFormats', $this->controllerSettings['media']['allowedFormats'] );
        $this->smarty->assign( 'characteristic', $characteristic );
        $this->smarty->assign( 'module_id', $this->getCurrentModuleId() );

        $this->printPage();
    }

    public function stateSortAction()
    {
        $this->checkAuthorisation();

        $mId = $this->getCurrentMatrixId();

        if ($mId == null)
            $this->redirect('matrices.php');

        if (!$this->rHasVal('sId'))
            $this->redirect('edit.php');

        if ($this->rHasVal('states') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$i=0;
			foreach((array)$this->rGetVal('states') as $val)
			{
				$this->updateStateShowOrder($val,$i++);
			}

			$this->addMessage('New state order saved.');

		}
		else
        if ($this->rHasId() && $this->rHasVal('r') && !$this->isFormResubmit())
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

            $c = $this->getCharacteristicStates($this->rGetVal('sId'));

            if ($this->rHasVal('r', 'alph') || $this->rHasVal('r', 'num'))
			{
                foreach((array)$c as $val)
				{
                    $assoc[$val['label']] = $val['id'];
                    $sort[] = $val['label'];
                }

	            if ($this->rHasVal('r', 'alph')) sort($sort);

	            if ($this->rHasVal('r', 'num')) natcasesort($sort);

	            foreach((array)$sort as $key=>$val)
				{
	                $this->updateStateShowOrder($assoc[$val],$key);
				}

				$this->addMessage('Re-sorted state.');
            }
			else
			{
	            foreach ((array) $c as $key => $val)
				{
	                if ($this->rGetId() == $val['id'])
					{
	                    if ($this->rHasVal('r', 'u'))
						{
	                        if (isset($c[$key - 1]))
	                            $this->updateStateShowOrder($c[$key - 1]['id'], $c[$key - 1]['show_order'] + 1);
	                        $this->updateStateShowOrder($this->rGetId(), $val['show_order'] - 1);
	                        break;
	                    }
	                    else
						if ($this->rHasVal('r', 'd'))
						{
	                        if (isset($c[$key + 1]))
	                            $this->updateStateShowOrder($c[$key + 1]['id'], $c[$key + 1]['show_order'] - 1);
	                        $this->updateStateShowOrder($this->rGetId(), $val['show_order'] + 1);
	                        break;
	                    }
	                }
	            }
	            $this->renumberStateShowOrder($this->rGetVal('sId'));
            }
        }

        $matrix = $this->getMatrix( $mId );

        $this->setPageName( $this->translate('Editing matrix: sort states') );

        $this->smarty->assign('characteristic', $this->getCharacteristic($this->rGetVal('sId')));
        $this->smarty->assign('states', $this->getCharacteristicStates($this->rGetVal('sId')));
        $this->smarty->assign('matrix', $matrix);

        $this->printPage();
    }

    public function linksAction()
    {
        if ($this->getCurrentMatrixId() == null) $this->redirect('matrices.php');

        $this->checkAuthorisation();
        $this->setPageName($this->translate('Taxon-state links'));

        if ($this->rHasVal('taxon'))
		{
            $links = $this->getLinks(array(
                'taxon_id' => $this->rGetVal('taxon')
            ));

            $this->customSortArray($links, array(
                'key' => 'characteristic',
                'case' => 'i'
            ));
        }

        $this->smarty->assign( 'matrix', $this->getMatrix( $this->getCurrentMatrixId() ) );
		$this->smarty->assign( 'taxa', $this->getTaxa() );
        if ( isset($links) ) $this->smarty->assign('links', $links);
        if ($this->rHasVal('taxon'))  $this->smarty->assign('taxon', $this->rGetVal('taxon'));

        $this->printPage();
    }

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action'))
            return;

		if ($this->rGetVal('action') == 'remove_taxon')
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( !$this->getAuthorisationState() ) return;

            $this->removeTaxon();
            $d = $this->getMatrices();

            $matrices = array();

            foreach ((array) $d as $val)
			{
                if ($val['id'] != $this->getCurrentMatrixId())
                    array_push($matrices, $val);
			}

            $this->smarty->assign('returnText', json_encode(array(
                'taxa' => $this->getTaxa(),
                'matrices' => $matrices,
                'variations' => $this->getVariationsInMatrix()
            )));
        }
        else
		if ($this->rGetVal('action') == 'get_states')
		{
            $this->getCharacteristicStates();
        }
        else
		if ($this->rGetVal('action') == 'add_link')
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( !$this->getAuthorisationState() ) return;

            $this->addLink([
				'characteristic_id'=>$this->rGetVal('characteristic'),
				'taxon_id'=>$this->rGetVal('taxon'),
				'state_id'=>$this->rGetVal('state'),
			]);
        }
        else
		if ($this->rGetVal('action') == 'delete_link')
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			if ( !$this->getAuthorisationState() ) return;
            $this->deleteLinks(array(
                'id' => $this->rGetId()
            ));
        }
        else
		if ($this->rGetVal('action') == 'get_links')
		{
            $this->smarty->assign('returnText', json_encode($this->getLinks(array(
                'characteristic_id' => $this->rGetVal('characteristic'),
                'taxon_id' => $this->rGetVal('taxon')
            ))));
        }

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


    private function createNewMatrix( $sys_name )
    {
		if ( empty($sys_name) ) return;

        $this->models->Matrices->save(array(
            'project_id' => $this->getCurrentProjectId(),
			'sys_name' => $sys_name
        ));

		$this->logChange($this->models->Matrices->getDataDelta() + [ 'note'=>'Matrix: created matrix' ]);

        return $this->models->Matrices->getNewId();
    }

    private function setCurrentMatrixId ($id)
    {
        if ($id==null)
		{
			$this->moduleSession->setModuleSetting( array('setting'=>'currentMatrixId') );
        }
        else
		{
			$this->moduleSession->setModuleSetting( array('setting'=>'currentMatrixId','value'=>$id ) );
        }
    }

    private function getCurrentMatrixId ()
    {
		return $this->moduleSession->getModuleSetting( 'currentMatrixId' );
    }

    private function getMatrix( $id )
    {
        if (!isset($id)) return;

        $m = $this->models->Matrices->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $id
            )
        ));

        if (!$m) return;

		$matrix=$m[0];

        $matrix['names']=
			$this->models->MatricesNames->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $id
					),
					'fieldAsIndex' => 'language_id'
				));

		$matrix['label']=
			isset($mn[$this->getDefaultProjectLanguage()]['name']) ?
				$mn[$this->getDefaultProjectLanguage()]['name'] :
					isset($m[0]['sys_name']) ?
						$m[0]['sys_name'] :
						'(matrix)'
				;

        return $matrix;
    }

    private function deleteMatrix( $id )
    {
        if (!isset($id)) return;

        $c = $this->getCharacteristics( $id );

        foreach((array) $c as $key => $val)
		{
            $this->deleteCharacteristic($val['id']);
        }

        $this->models->MatricesTaxa->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->rGetId()
        ));

        $this->models->MatricesTaxaStates->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->rGetId()
        ));

        $this->models->MatricesVariations->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->rGetId()
        ));

        $this->models->MatricesNames->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->rGetId()
        ));

        $this->models->Matrices->delete(array(
            'id' => $id,
            'project_id' => $this->getCurrentProjectId()
        ));

		$this->logChange($this->models->Matrices->getDataDelta() + [ 'note'=>'Matrix: deleted matrix' ]);
    }

    private function getMatrices()
    {
        $m = $this->models->Matrices->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

        foreach ((array)$m as $key => $val)
		{
            $mn = $this->models->MatricesNames->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'matrix_id' => $val['id']
                ),
                'fieldAsIndex' => 'language_id'
            ));

			if (isset($mn[$this->getDefaultProjectLanguage()]['name']))
			{
				$d = $mn[$this->getDefaultProjectLanguage()]['name'];
			}
			else
			{
				$d = @current($mn);
				$d = $d['name'];
			}

            $m[$key]['names'] = $mn;
            $m[$key]['default_name'] = $d;
            $m[$key]['label'] = (!empty($m[$key]['default_name']) ? $m[$key]['default_name'] : (!empty($m[$key]['sys_name']) ? $m[$key]['sys_name'] : '(matrix)' ) );
        }

        $this->customSortArray($m, array(
            'key' => 'default_name',
            'case' => 'i'
        ));

        return $m;
    }

	private function saveMatrixName( $p )
	{
		$matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
		$language_id = isset($p['language_id']) ? $p['language_id'] : null;
		$name = isset($p['name']) ? $p['name'] : null;

		if ( is_null($language_id) || is_null($matrix_id) || is_null($name) )
			return;

		$mn = $this->models->MatricesNames->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrix_id,
				'language_id' => $language_id
			)
		));

		$this->models->MatricesNames->save(
		array(
			'id' => isset($mn[0]['id']) ? $mn[0]['id'] : null,
			'project_id' => $this->getCurrentProjectId(),
			'language_id' => $language_id,
			'matrix_id' => $matrix_id,
			'name' => $name
		));

		$this->logChange($this->models->MatricesNames->getDataDelta() + [ 'note'=>'Matrix: saved name' ]);

		return $this->models->MatricesNames->getAffectedRows();
	}

    private function saveMatrixSysName( $p )
	{
		$matrix_id = isset($p['matrix_id']) ? $p['matrix_id'] : null;
		$name = isset($p['name']) ? $p['name'] : null;

		if ( is_null($matrix_id) || is_null($name) )
			return;

        $this->models->Matrices->update(
			array(
				'sys_name' => $name
			),
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $matrix_id
			)
		);

		return $this->models->Matrices->getAffectedRows();
    }

    private function createCharacteristic()
    {
        $this->models->Characteristics->save(array(
            'project_id' => $this->getCurrentProjectId(),
            'type' => $this->characteristicTypes[0]['name'],
            'sys_name' => 'temp'
        ));

		$this->logChange($this->models->Characteristics->getDataDelta() + [ 'note'=>'Matrix: created character' ]);

        return $this->models->Characteristics->getNewId();
    }

    private function addCharacteristicToMatrix( $id )
    {
        if (!isset($id) ) return;

        $mc = $this->models->CharacteristicsMatrices->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id' => $this->getCurrentMatrixId()
            ),
            'columns' => 'max(show_order) as max'
        ));

        $next = isset($mc[0]['max']) ? $mc[0]['max'] + 1 : 0;

        @$this->models->CharacteristicsMatrices->save(
        array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->getCurrentMatrixId(),
            'characteristic_id' => $id,
            'show_order' => $next
        ));
    }

    private function updateCharacteristic( $id, $data )
    {
		if ( is_null($id) || is_null($data) ) return;

        $this->models->Characteristics->update(
			array(
				'sys_name' => $data['sys_name'],
				'type' => $data['type']
			),
			array('id' => $id,'project_id' => $this->getCurrentProjectId())
		);

		$this->logChange($this->models->Characteristics->getDataDelta() + [ 'note'=>'Matrix: updated character' ]);

		foreach((array)$data['name'] as $key=>$val)
		{
			$this->models->CharacteristicsLabels->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $key,
				'characteristic_id' => $id,
			));

			$this->models->CharacteristicsLabels->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $key,
				'characteristic_id' => $id,
				'label' => trim($val)
			));
		}
    }

	private function getCharacteristic( $id )
    {
		return
			$this->models->MatrixkeyModel->getCharactersInMatrix([
				'id' => $id,
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $this->getCurrentMatrixId(),
				'language_id' => $this->getDefaultProjectLanguage()
			]);
    }

    private function deleteCharacteristic( $id )
    {
        if ( is_null($id) ) return;

        $this->deleteLinks(array(
            'characteristic_id' => $id,
            'matrix_id' => $this->getCurrentMatrixId()
        ));

        // delete from matrix-char table for current matrix
        $this->models->CharacteristicsMatrices->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->getCurrentMatrixId(),
            'characteristic_id' => $id
        ));

        // check if char is used in any other matrix
        $mc = $this->models->CharacteristicsMatrices->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id !=' => $this->getCurrentMatrixId(),
                'characteristic_id' => $id
            ),
            'columns' => 'count(*) as total'
        ));

        if ($mc[0]['total']==0)
		{
            // if not, adieu
            $this->deleteCharacteristicStates($id);

            $this->models->CharacteristicsLabels->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'characteristic_id' => $id
            ));

            $this->models->Characteristics->delete(array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            ));

			$this->logChange($this->models->Characteristics->getDataDelta() + [ 'note'=>'Matrix: deleted character' ]);

        }
    }

    private function getCharacteristics( $matrix_id=null )
    {
		return
			$this->models->MatrixkeyModel->getCharactersInMatrix([
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => isset($matrix_id) ? $matrix_id : $this->getCurrentMatrixId(),
			'language_id' => $this->getDefaultProjectLanguage()
		]);
    }

    private function getAllCharacteristics( $matrixToExclude )
    {
        if ( isset($matrixToExclude) )
		{
            $ce = $this->getCharacteristics($matrixToExclude);

			foreach ((array) $ce as $key => $val)
			{
				$b[]=$val['id'];
			}
        }

        $c = $this->models->MatrixkeyModel->getCharacters([
			'project_id' => $this->getCurrentProjectId(),
			'not_id' => isset($b) ? $b : null,
			'language_id' => $this->getDefaultProjectLanguage()
		]);

        return $c;
    }

    private function createState()
    {
        if (!$this->rHasVal('char'))
            return;

        $this->models->CharacteristicsStates->save(array(
            'project_id' => $this->getCurrentProjectId(),
            'characteristic_id' => $this->rGetVal('char'),
            'sys_name' => 'temp'
        ));

		$this->logChange($this->models->CharacteristicsStates->getDataDelta() + [ 'note'=>'Matrix: created state' ]);

        return $this->models->CharacteristicsStates->getNewId();
    }

    private function getState( $id )
    {
		$state=
			$this->models->MatrixkeyModel->getStates([
			'project_id' => $this->getCurrentProjectId(),
			'language_id' => $this->getDefaultProjectLanguage(),
			'id' => $id
		]);

		if ( $this->use_media )
		{
			// Override media
			$media = $this->getStateMedia();
			$state['file_name'] = $media['file_name'];
			$state['file_dimensions'] = $media['file_dimensions'];
		}

		return $state;
    }

    private function verifyData($data, $file)
    {
        $result = true;

        if (!isset($data['label']) || empty($data['label']))
		{
            // each state has a name, regardless of type
            $this->addError($this->translate('A name is required.'));
            $result = false;
        }

        if ($data['type'] == 'text')
		{
            if (!isset($data['text']) || empty($data['text']))
			{
                $this->addError($this->translate('Text is required.'));
                $result = false;
            }
        }
        else

        if ($data['type'] == 'range')
		{
            if (!isset($data['lower']) || empty($data['lower']) && $data['lower'] !== '0')
			{
                $this->addError($this->translate('The lower boundary is required.'));
                $result = false;
            }
            else
			if ($data['lower'] != strval(floatval($data['lower'])))
			{
                $this->addError($this->translate('Invalid value for the lower boundary (must be integer or real).'));
                $result = false;
            }

            if (!isset($data['upper']) || empty($data['upper']) && $data['upper'] !== '0')
			{
                $this->addError($this->translate('The upper boundary is required.'));
                $result = false;
            }
            else
			if ($data['upper'] != strval(floatval($data['upper'])))
			{
                $this->addError($this->translate('Invalid value for the upper boundary (must be integer or real).'));
                $result = false;
            }

            if ($result && (floatval($data['upper']) < floatval($data['lower'])))
			{
                $this->addError($this->translate('The upper boundary value must be larger than the lower boundary value.'));
                $result = false;
            }
            else
			if ($result && (floatval($data['upper']) == floatval($data['lower'])))
			{
                $this->addError($this->translate('The upper and lower boundary values cannot be the same.'));
                $result = false;
            }
        }
        else
		if ($data['type'] == 'distribution')
		{
            if (!isset($data['mean']) || empty($data['mean']) && $data['mean'] !== '0')
			{
                $this->addError($this->translate('The mean is required.'));
                $result = false;
            }
            else
			if ($data['mean'] != strval(floatval($data['mean'])))
			{
                $this->addError($this->translate('Invalid value for the mean (must be integer or real).'));
                $result = false;
            }

            if (!isset($data['sd']) || empty($data['sd']) && $data['sd'] !== '0')
			{
                $this->addError($this->translate('The value for one standard deviation is required.'));
                $result = false;
            }
            elseif ($data['sd'] != strval(floatval($data['sd'])) && $data['mean'] !== '0')
			{
                $this->addError($this->translate('Invalid value for one standard deviation (must be integer or real).'));
                $result = false;
            }
        }
        else
		if ($data['type'] == 'media')
		{
            if (!$file && !isset($data['existing_file']))
			{
                $this->addError($this->translate('A media file is required.'));
                $result = false;
            }
        }

        return $result;
    }

    private function getCharacteristicStates($id=null)
    {
        $id = isset($id) ? $id : $this->rGetId();

        if (!isset($id))
            return;

        $cs = $this->models->CharacteristicsStates->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'characteristic_id' => $id
            ),
            'columns' => 'id,characteristic_id,show_order',
            'order' => 'show_order'
        ));

        foreach ((array) $cs as $key => $val)
		{
            $cs[$key]['label'] = $this->getCharacteristicStateLabelOrText($val['id'], $this->getDefaultProjectLanguage());
        }

        $this->smarty->assign('returnText', json_encode($cs));

        return $cs;
    }

    private function getCharacteristicStateLabelOrText($id = null, $language = null, $type = 'label')
    {
        $id = isset($id) ? $id : $this->rGetId();
        $language = isset($language) ? $language : $this->rGetVal('language');

        if (!isset($id) || !isset($language))
            return;

        $cls = $this->models->CharacteristicsLabelsStates->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'state_id' => $id,
                'language_id' => $language
            )
        ));

        return $type == 'text' ? $cls[0]['text'] : $cls[0]['label'];
    }

    private function deleteCharacteristicStateImage($id = null)
    {
        $id = isset($id) ? $id : $this->rGetId();

        if (!isset($id))
            return;

        $cs = $this->getState($id);

        if ($cs['file_name'])
            @unlink($this->getProjectsMediaStorageDir() . $cs['file_name']);

        $this->models->CharacteristicsStates->update(array(
            'file_name' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
    }

    private function deleteCharacteristicState($id = null)
    {
        $id = isset($id) ? $id : $this->rGetId();

        if (!isset($id))
            return;

        //$this->deleteCharacteristicStateImage($id);
		if ( $this->use_media )
		{
	        $this->detachAllMedia();
		}

        $this->models->CharacteristicsLabelsStates->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'state_id' => $id
        ));

        $this->models->CharacteristicsStates->delete(array(
            'id' => $id,
            'project_id' => $this->getCurrentProjectId()
        ));

		$this->logChange($this->models->CharacteristicsStates->getDataDelta() + [ 'note'=>'Matrix: deleted state' ]);

    }

    private function deleteCharacteristicStates($charId)
    {
        $cs = $this->getCharacteristicStates($charId);

        $this->deleteLinks(array(
            'characteristic_id' => $charId
        ));

        foreach ((array) $cs as $key => $val)
		{
            if (isset($val['file_name']))
			{
                @unlink($this->getProjectsMediaStorageDir(). $val['file_name']);
            }
        }

        $this->models->CharacteristicsStates->delete(array(
            'characteristic_id' => $charId,
            'project_id' => $this->getCurrentProjectId()
        ));

		$this->logChange($this->models->CharacteristicsStates->getDataDelta() + [ 'note'=>'Matrix: deleted states' ]);

    }

    private function getTaxa()
    {
		$d=array(
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => $this->getCurrentMatrixId()
		);

		$this->UserRights->setUserItems();
		$this->userItems=$this->UserRights->getUserItems();

		if ( !empty($this->userItems) )
		{
			$d['branch_tops']=$this->userItems;
		}

		$taxa=$this->models->MatrixkeyModel->getTaxaInMatrix( $d );

		foreach((array)$taxa as $key=>$val)
		{
			$taxa[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

        return isset($taxa) ? $taxa : null;
    }

    private function removeTaxon($id = null)
    {
        $id = isset($id) ? $id : $this->rGetId();

        if (!isset($id))
            return;

        if (strpos($id, 'var-')===0)
		{
            $id = str_replace('var-', '', $id);

            $this->deleteLinks(array(
                'variation_id' => $id
            ));

            $this->models->MatricesVariations->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id' => $this->getCurrentMatrixId(),
                'variation_id' => $id
            ));
        }
        else
		{
            $this->deleteLinks(array(
                'taxon_id' => $id
            ));
            $this->models->MatricesTaxa->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id' => $this->getCurrentMatrixId(),
                'taxon_id' => $id
            ));
        }
    }

    private function addLink( $params )
    {
		$charId = isset($params['characteristic_id']) ? $params['characteristic_id'] : null;
		$taxonId = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$stateId = isset($params['state_id']) ? $params['state_id'] : null;

        if (strpos($taxonId, 'mx-')===0)
		{
            $refMatrixId = str_replace('mx-', '', $taxonId);
            $taxonId = null;
        }
        else
		if (strpos($taxonId, 'var-')===0)
		{
            $variationId = str_replace('var-', '', $taxonId);
            $taxonId = null;
        }
        else
		{
            $refMatrixId = null;
        }

        if (!isset($charId) || (!isset($taxonId) && !isset($refMatrixId) && !isset($variationId)) || !isset($stateId))
            return;

        $this->models->MatricesTaxaStates->save([
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => $this->getCurrentMatrixId(),
			'taxon_id' => (isset($taxonId) ? $taxonId : null),
			'ref_matrix_id' => (isset($refMatrixId) ? $refMatrixId : null),
			'variation_id' => (isset($variationId) ? $variationId : null),
			'characteristic_id' => $charId,
			'state_id' => $stateId
        ]);
    }

    private function deleteLinks( $params )
    {
        if (isset($params['id']))
            $d['id'] = $params['id'];
        if (isset($params['state_id']))
            $d['state_id'] = $params['state_id'];
        if (isset($params['characteristic_id']))
            $d['characteristic_id'] = $params['characteristic_id'];
        if (isset($params['taxon_id']))
            $d['taxon_id'] = $params['taxon_id'];
        if (isset($params['matrix_id']))
            $d['ref_matrix_id'] = $params['matrix_id'];
        if (isset($params['variation_id']))
            $d['variation_id'] = $params['variation_id'];

        if (!isset($d))
            return;

        $d['project_id'] = $this->getCurrentProjectId();

        $this->models->MatricesTaxaStates->delete($d);
    }

    private function getLinks( $params )
    {
        if (isset($params['id']))
            $d['id'] = $params['id'];

        if (isset($params['characteristic_id']))
            $d['characteristic_id'] = $params['characteristic_id'];

        if (isset($params['taxon_id']))
            $d['taxon_id'] = $params['taxon_id'];

        if (isset($params['matrix_id']))
            $d['matrix_id'] = $params['matrix_id'];

        if (strpos($d['taxon_id'], 'mx-') === 0) {
            $d['ref_matrix_id'] = str_replace('mx-', '', $d['taxon_id']);
            unset($d['taxon_id']);
        }
        else if (strpos($d['taxon_id'], 'var-') === 0) {
            $d['variation_id'] = str_replace('var-', '', $d['taxon_id']);
            unset($d['taxon_id']);
        }

        if (!isset($d))
            return;

        $d['project_id'] = $this->getCurrentProjectId();

        $mts = $this->models->MatricesTaxaStates->_get(array(
            'id' => $d
        ));

        foreach ((array) $mts as $key => $val)
		{

            $a=$this->models->MatrixkeyModel->getStates([
				'id' => $val['state_id'],
				'project_id' => $this->getCurrentProjectId(),
				'language_id'=>$this->getDefaultProjectLanguage()
			]);
            $b=$this->models->MatrixkeyModel->getCharacters([
				'id' => $val['characteristic_id'],
				'project_id' => $this->getCurrentProjectId(),
				'language_id'=>$this->getDefaultProjectLanguage()
			]);

            $mts[$key]['state'] = $a['sys_name'];
            $mts[$key]['characteristic'] = $b['sys_name'];
        }

        return $mts;
    }

    private function updateCharShowOrder($id, $val)
    {
        $this->models->CharacteristicsMatrices->update(array(
            'show_order' => $val
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'matrix_id' => $this->getCurrentMatrixId(),
            'characteristic_id' => $id
        ));
    }

    private function renumberCharShowOrder()
    {
        $c = $this->getCharacteristics();

        foreach ((array) $c as $key => $val)
            $this->updateCharShowOrder($val['id'], $key);
    }

    private function setDefaultMatrix($id = null)
    {
        if (isset($id))
		{
            $this->models->Matrices->update(array(
                'default' => '0'
            ), array(
                'project_id' => $this->getCurrentProjectId()
            ));

            $this->models->Matrices->save(array(
                'id' => $id,
                'default' => 1
            ));

			$this->logChange($this->models->Matrices->getDataDelta() + [ 'note'=>'Matrix: set default matrix' ]);

            return;
        }

        $m=$this->getMatrices();

		if (empty($m)) return;

        if (count((array)$m)<=1)
		{
            $this->models->Matrices->save(array(
                'id' => $m[0]['id'],
                'default' => 1
            ));

			$this->logChange($this->models->Matrices->getDataDelta() + [ 'note'=>'Matrix: set default matrix' ]);

            return;
        }

        $hasDef=false;

        foreach ((array) $m as $val)
		{
            $hasDef = ($hasDef==true || $val['default']==1);
        }

        if (!$hasDef)
		{
            $this->models->Matrices->save(array(
                'id' => $m[0]['id'],
                'default' => 1
            ));

			$this->logChange($this->models->Matrices->getDataDelta() + [ 'note'=>'Matrix: set default matrix' ]);

        }
    }

    private function getDefaultMatrixId()
    {
		$m = $this->models->Matrices->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'default' => 1
			),
			'columns' => 'id'
		));

		return isset($m[0]['id']) ? $m[0]['id'] : null;
    }

    private function updateStateShowOrder($id, $val)
    {
        $this->models->CharacteristicsStates->update(array(
            'show_order' => $val
        ), array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
	}

    private function renumberStateShowOrder($id)
    {
        $c = $this->getCharacteristicStates($id);

        foreach ((array) $c as $key => $val)
            $this->updateStateShowOrder($val['id'], $key);
    }

    private function getVariationsInMatrix()
    {
        if (!$this->_useVariations)
            return;

        $v = $this->getVariations();

        $mv = $this->models->MatricesVariations->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'matrix_id' => $this->getCurrentMatrixId()
            ),
            'fieldAsIndex' => 'variation_id'
        ));

        $d = array();
        foreach ((array) $v as $val) {
            if (isset($mv[$val['id']]))
                $d[] = $val;
        }


        $this->customSortArray($d, array(
            'key' => 'label'
        ));

        return $d;
    }

    private function saveCharacterGroup($p=null)
    {

        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        $label = isset($p['label']) ? trim($p['label']) : null;

		if (is_null($label))
		{
			$this->addError($this->translate('Cannot save a nameless group.'));
			return;
		}

        if ($this->models->Chargroups->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId,
				'label' => $label,
				'show_order' => 99
		))) {

			$this->logChange($this->models->Chargroups->getDataDelta() + [ 'note'=>'Matrix: created character group' ]);

			return $this->models->ChargroupsLabels->save(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'chargroup_id' => $this->models->Chargroups->getNewId(),
					'label' => $label,
					'language_id' => $this->getDefaultProjectLanguage()
			));

		}

    }

    private function deleteCharacterGroup($p=null)
    {
		if (!isset($p['groupId'])) return;

		$this->models->ChargroupsLabels->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'chargroup_id' => $p['groupId']
		));

        $this->models->Chargroups->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $p['groupId']
		));

		$this->logChange($this->models->Chargroups->getDataDelta() + [ 'note'=>'Matrix: deleted character group' ]);
    }

    private function deleteCharacteristicFromGroup ($p=null)
    {
        $charId = isset($p['charId']) ? $p['charId'] : null;
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $matrixId = isset($p['matrixId']) ? $p['matrixId'] : $this->getCurrentMatrixId();

		if ($charId==null && $groupId==null) {

			$cg = $this->models->Chargroups->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $matrixId
				)
			));

			foreach((array)$cg as $val)
			{
				$this->deleteCharacteristicFromGroup(array('groupId' => $val['id']));
			}

		}
		else
		{
			$d = array('project_id' => $this->getCurrentProjectId());

			if (!is_null($charId)) $d['characteristic_id'] = $charId;
			if (!is_null($groupId)) $d['chargroup_id'] = $groupId;

			$this->models->CharacteristicsChargroups->delete($d);
		}
	}

    private function saveCharacteristicToGroup ($p=null)
    {

        $charId = isset($p['charId']) ? $p['charId'] : null;
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $showOrder = isset($p['showOrder']) ? $p['showOrder'] : null;

		if ($charId==null && $groupId==null)
			return null;

		$d = array('project_id' => $this->getCurrentProjectId());

		if (!is_null($charId))
			$d['characteristic_id'] = $charId;

		if (!is_null($groupId))
			$d['chargroup_id'] = $groupId;

		if (!is_null($showOrder))
			$d['show_order'] = $showOrder;

		$this->models->CharacteristicsChargroups->save($d);

	}

    private function getCharacterGroupLabel ($p=null)
    {

        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $langId = isset($p['langId']) ? $p['langId'] : $this->getDefaultProjectLanguage();

		if (is_null($groupId) || is_null($langId))
			return;

        $cl = $this->models->ChargroupsLabels->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'chargroup_id' => $groupId,
                'language_id' => $langId
            )
        ));

        return $cl[0]['label'];
    }

    private function getCharacterGroups ($p=null)
    {

        $matrixId = isset($matrixId) ? $matrixId : $this->getCurrentMatrixId();
        $groupId = isset($p['groupId']) ? $p['groupId'] : null;
        $label = isset($p['label']) ? trim($p['label']) : null;

		$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId
            );

		if (!is_null($groupId))
			$d['id'] = $groupId;

		if (!is_null($label))
			$d['label'] = $label;

        $cg = $this->models->Chargroups->_get(
        array(
            'id' => $d,
            'order' => 'show_order',
            'columns' => 'id,matrix_id,label,show_order',
			'fieldAsIndex' => 'id'
        ));

        foreach ((array) $cg as $key => $val)
		{
            $cg[$key]['type'] = 'group';
            $cg[$key]['label'] = $this->getCharacterGroupLabel(array('groupId'=>$val['id']));

            $cc = $this->models->CharacteristicsChargroups->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'chargroup_id' => $val['id']
                ),
                'order' => 'show_order'
            ));

            foreach ((array) $cc as $cVal) {
                $cg[$key]['chars'][] = $this->getCharacteristic($cVal['characteristic_id']);
            }
        }

        return (!isset($groupId) ? $cg : (isset($cg[0]) ? $cg[0] : null));
    }

    private function getCharactersNotInGroups( $mId=null )
    {
        $mId = isset($mId) ? $mId : $this->getCurrentMatrixId();

		return $this->models->MatrixkeyModel->getCharactersNotInGroups(
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'matrix_id'=>$mId,
				'language_id' => $this->getDefaultProjectLanguage()
			));
    }

	private function saveStateImageDimensions($state)
	{
		$path = $_SESSION['admin']['project']['urls']['project_media'];
		$file = isset($state['file_name']) ? $state['file_name'] : null;

		// if there is a filename, and the file does exist...
		if (!empty($file))
		{
			if (file_exists($path.$file))
			{
				// ...try to get its dimensions...
				$d = getimagesize($path.$file);

				// ...if that works...
				if ($d!==false)
				{
					// ...save them as w:h...
					$this->models->CharacteristicsStates->save(
					array(
						'id' => $state['id'],
						'project_id' => $this->getCurrentProjectId(),
						'file_dimensions' => $d[0].':'.$d[1]
					));

					// ...and return
					return;
				}
			}
			else
			{
				$this->addError($path.$file.' doesn\'t seem to exist.');
			}
		}

		// if filename is empty, the file doesn't exist or we couldn't get any dimensions, reset the dimensions, if there were any in the database
		if (!empty($state['file_dimensions']))
		{
			$this->models->CharacteristicsStates->save(
			array(
				'id' => $state['id'],
				'project_id' => $this->getCurrentProjectId(),
				'file_dimensions' => 'null'
			));
		}
	}

	private function reacquireStateImageDimensions($id=null)
	{
		$id = isset($id) ? $id : $this->getCurrentMatrixId();

		$d = $this->getCharacteristics($id);

		foreach((array)$d as $dVal)
		{
			$v = $this->getCharacteristicStates($dVal['id']);

			foreach((array)$v as $vVal)
			{
				$this->saveStateImageDimensions($this->getState($vVal['id']));
			}

			$this->addMessage(sprintf($this->translate('Updated states for "%s".'),$dVal['label']));
		}
	}

	private function deleteGUIMenuOrder()
	{
		$this->models->GuiMenuOrder->delete(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => $this->getCurrentMatrixId()
		));

	}

	private function saveGUIMenuOrder($p=null)
	{
		$id = isset($p['id']) ? $p['id'] : null;
		$type = isset($p['type']) ? $p['type'] : null;
		$order = isset($p['order']) ? $p['order'] : null;

		if (is_null($id) || is_null($type) || is_null($order))
			return;

		$this->models->GuiMenuOrder->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'matrix_id' => $this->getCurrentMatrixId(),
			'ref_id' => $id,
			'ref_type' => $type,
			'show_order' => $order,
		));

	}

	private function getGUIMenuOrder()
	{
		return $this->models->GuiMenuOrder->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'matrix_id' => $this->getCurrentMatrixId(),
				),
				'order' => 'show_order'
			));

	}

	private function effectuateGUIMenuOrder($p=null)
	{

		$g = isset($p['groups']) ? $p['groups'] : null;
		$c = isset($p['characters']) ? $p['characters'] : null;
		$m = isset($p['menu']) ? $p['menu'] : null;

		if (is_null($g) && is_null($c))
			return;

		$d = array();

		foreach((array)$m as $val) {
			if ($val['ref_id']==0)
				continue;

			if ($val['ref_type']=='group') {
				$d[] = $g[$val['ref_id']];
				unset($g[$val['ref_id']]);
			} else {
				$d[] = $c[$val['ref_id']];
				unset($c[$val['ref_id']]);
			}
		}

		// appand the items for some reason missing from the menu-order
		foreach((array)$c as $val)
			$d[] = $val;

		foreach((array)$g as $val)
			$d[] = $val;

		return $d;

	}

    private function detachAllMedia()
    {
        $media = $this->_mc->getItemMediaFiles();

        if (!empty($media)) {
            foreach ($media as $item) {
                $this->_mc->deleteItemMedia($item['id']);
            }
        }
    }

    private function getStateMedia()
    {
        $media = $this->_mc->getItemMediaFiles();

        if (!empty($media)) {
            return array(
                'file_name' => $media[0]['rs_original'],
                'file_dimensions' => $media[0]['width'] . ':' . $media[0]['height']
            );
        }

        return false;
    }

    private function saveState( $params )
    {
		$id = isset($params['id']) ? $params['id'] : null;
		$labels = isset($params['labels']) ? $params['labels'] : null;
		$texts = isset($params['texts']) ? $params['texts'] : null;
		$sys_name = isset($params['sys_name']) ? $params['sys_name'] : null;

		$lower = isset($params['lower']) ? $params['lower'] : null;
		$upper = isset($params['upper']) ? $params['upper'] : null;

		if ( is_null($id) )
			return;

		$this->models->CharacteristicsStates->save([
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id,
			'sys_name' => trim($sys_name),
			'lower' =>  $lower,
			'upper' => $upper
		]);

		$this->logChange($this->models->CharacteristicsStates->getDataDelta() + [ 'note'=>'Matrix: saved state' ]);

		$d=
			array(
				'project_id' => $this->getCurrentProjectId(),
				'state_id' => $id,
			);

		foreach((array)$labels as $language_id=>$val)
		{
            $c=$this->models->CharacteristicsLabelsStates->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'state_id' => $id,
                    'language_id' => $language_id
                )
            ));

			$d['id'] = $c ? $c[0]['id'] : null;
			$d['label'] = trim($val);
			$d['text'] = isset($texts[$language_id]) ? trim($texts[$language_id]) : null;
			$d['language_id'] = $language_id;

            $this->models->CharacteristicsLabelsStates->save($d);

		}
    }

	private function setMediaController()
	{
        $this->_mc = new MediaController( ['module_settings_reader'=>$this->moduleSettings] );
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
        $this->_mc->setLanguageId($this->getDefaultProjectLanguage());
	}


}
