<?php

/*

	set upload maximum for media uploads per projects
    deleting of (free) moduels does not as yet delete any data, all the warnings notwithstanding
	deleting of language should delete glossary terms

*/


include_once ('Controller.php');

class ProjectsController extends Controller
{
    
    public $usedModels = array(
        'project', 
        'module', 
        'project_role_user', 
        'user', 
        'role', 
        'module_project', 
        'free_module_project', 
        'module_project_user', 
        'free_module_project_user', 
        'language', 
        'language_project', 
		'content_taxon',
		'page_taxon', 
		'page_taxon_title', 
		'commonname',
		'media_taxon',
		'media_descriptions_taxon',
		'content',
		'literature',
		'literature_taxon',
		'keystep',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
		'matrix',
		'matrix_name',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
		'characteristic_label_state',
		'glossary',
		'glossary_synonym',
		'glossary_media',
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'content_free_module',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title'
    );
    
    public $usedHelpers = array(
		'file_upload_helper'
    );

    public $controllerPublicName = 'Project administration';

	public $cssToLoad = array('lookup.css');
	public $jsToLoad = array('all'=>array('project.js','module.js','lookup.js'));

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();
   
		$this->isMultiLingual = false;
   
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
	* Index, showing menu of options
	*
	* @access     public
	*/
    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));
        
        $this->printPage();
    
    }

	/**
	* Project wide index, showing start screen with module icons
	*
	* @access     public
	*/
    public function adminIndexAction ()
    {

        $this->checkAuthorisation();
		
		$this->includeLocalMenu = true;

        $this->setPageName(_('Project overview'));

		// get all modules activated in this project
		$modules = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				), 
				'order' => 'module_id asc'
			)
		);

		foreach ((array) $modules as $key => $val) {
			
			// get info per module
			$mp = $this->models->Module->_get(array('id'=>$val['module_id']));
			
			$modules[$key]['icon'] = $mp['icon'];
			$modules[$key]['module'] = $mp['module'];
			$modules[$key]['controller'] = $mp['controller'];

			// see if the current user has any rights within the module
			if (isset($_SESSION['user']['_rights'][$this->getCurrentProjectId()][$mp['controller']]))
				$modules[$key]['_rights'] = $_SESSION['user']['_rights'][$this->getCurrentProjectId()][$mp['controller']];

		}

		$freeModules = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		foreach ((array) $freeModules as $key => $val) {
			
			// see if the current user has any rights within the module
			if (
				isset($_SESSION['user']['_rights'][$this->getCurrentProjectId()]['_freeModules'][$val['id']]) &&
				$_SESSION['user']['_rights'][$this->getCurrentProjectId()]['_freeModules'][$val['id']]===true
			)
				$freeModules[$key]['currentUserRights'] = true;

		}

		unset($_SESSION['user']['freeModules']['activeModule']);

        $this->smarty->assign('modules',$modules);

        $this->smarty->assign('freeModules',$freeModules);

        $this->smarty->assign('currentRole',$this->getCurrentUserCurrentRole());

        $this->printPage();
    
    }


	/**
	* List of available modules, standard and self-defined, plus possibility of (de)activation
	*
	* @access     public
	*/
    public function modulesAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Project modules'));

        if ($this->rHasVal('module_new')) {
            
            $fmp = $this->models->FreeModuleProject->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));
            
            if (
				count((array) $fmp) < $this->controllerSettings['freeModulesMax']
			 	&& !$this->isFormResubmit()
			) {

                $this->models->FreeModuleProject->save(
                array(
                    'id' => null, 
                    'module' => $this->requestData['module_new'], 
                    'project_id' => $this->getCurrentProjectId(),
                    'active' => 'n'
                ));

            } else {
			
				$this->addError(sprintf(_('There is a maximum of %s self-defined modules.'),$this->controllerSettings['freeModulesMax']));
			
			}
        
        }
        
        $modules = $this->models->Module->_get(
			array(
				'id' => array('1' => '1'), 
				'order' => 'show_order'
			)
		);

        foreach ((array) $modules as $key => $val) {

            $mp = $this->models->ModuleProject->_get(
				array(
					'id' => array(
						'module_id' => $val['id'], 
						'project_id' => $this->getCurrentProjectId()
		            )
				)
			);

            $modules[$key]['module_project_id'] = $mp[0]['id'] ? $mp[0]['id'] : false;
            $modules[$key]['active'] = $mp[0]['id'] ? $mp[0]['active'] : false;

        }

        $freeModules = $this->models->FreeModuleProject->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

        $this->smarty->assign('modules', $modules);

        $this->smarty->assign('freeModules', $freeModules);

        $this->smarty->assign('freeModuleMax', $this->controllerSettings['freeModulesMax']);

        $this->printPage();
    
    }

	/**
	* Assigning collaborator to modules (a listing, mainly; he actual connecting is done through AJAX-calls)
	*
	* @access     public
	*/
    public function collaboratorsAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName(_('Assign collaborator to modules'));

        $modules = $this->models->ModuleProject->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()), 
				'order' => 'module_id asc'
			)
		);

        foreach ((array) $modules as $key => $val) {

            $mp = $this->models->Module->_get(array('id' => $val['module_id']));

            $modules[$key]['module'] = $mp['module'];

            $modules[$key]['description'] = $mp['description'];

            $mpu = $this->models->ModuleProjectUser->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $val['module_id']
					)
				)
			);
            
            foreach ((array) $mpu as $k => $v) {
                
                $modules[$key]['collaborators'][$v['user_id']] = $v;
            
            }
        
        }
        
        $free_modules = $this->models->FreeModuleProject->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));
        
        foreach ((array) $free_modules as $key => $val) {
            
            $fpu = $this->models->FreeModuleProjectUser->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'free_module_id' => $val['id']
					)
				)
			);
            
            foreach ((array) $fpu as $k => $v) {
                
                $free_modules[$key]['collaborators'][$v['user_id']] = $v;
            
            }
        
        }
        
        $pru = $this->models->ProjectRoleUser->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'role_id !=' => '1'
				),
				'columns' => 'distinct user_id, role_id'
			)
		);
        
        foreach ((array) $pru as $key => $val) {
            
            $u = $this->models->User->_get(array('id' => $val['user_id']));

            $r = $this->models->Role->_get(array('id' => $val['role_id']));

            $u['role'] = $r['role'];
            
            $users[] = $u;
        
        }
        
        $this->customSortArray($users, array(
            'key' => 'last_name', 
            'dir' => 'asc', 
            'case' => 'i'
        ));
        
        $this->smarty->assign('users', $users);
        
        $this->smarty->assign('free_modules', $free_modules);
        
        $this->smarty->assign('modules', $modules);
        
        $this->printPage();
    
    }


	/**
	* Interface to the project settings
	*
	* @access     public
	*/
    public function dataAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Project settings'));

		if (isset($this->requestData['deleteLogo']) && $this->requestData['deleteLogo']=='1' && !$this->isFormResubmit()) {
		// deleting the logo
			
			$data = $this->models->Project->_get(array('id' => $this->getCurrentProjectId()));

			if (@unlink($this->getProjectsMediaStorageDir().$data['logo'])) {

				$p = $this->models->Project->save(
					array(
						'id' => $this->getCurrentProjectId(), 
						'logo' => 'null'
					)
				);

			} else {

				$this->addError(_('Could not delete image.'));

			}

		} else
        if (isset($this->requestData) && !$this->isFormResubmit()) {
		// saving all data (except the logo image)
            
            $this->requestData['id'] = $this->getCurrentProjectId();
            
            $this->models->Project->save($this->requestData);
        
        }
        
        if (isset($this->requestDataFiles)) {
		// saving the logo

			$filesToSave =  $this->getUploadedFiles();

			if ($filesToSave) {

				$p = $this->models->Project->save(
					array(
						'id' => $this->getCurrentProjectId(), 
						'logo' => $filesToSave[0]['name']
					)
				);

				if ($p) {
				
					$this->addMessage(_('Image saved.'));

				} else {

					@unlink($_SESSION['project']['paths']['project_media'].$filesToSave[0]['name']);

					$this->addError(_('Could not save image.'));

				}

			}

        }

		$this->setCurrentProjectData();

        $languages = array_merge(
			(array)$this->models->Language->_get(array('id' => 'select * from %table% where show_order is not null order by show_order asc')), 
	        (array)$this->models->Language->_get(array('id' => 'select * from %table% where show_order is null order by language asc'))
		);

        foreach ((array) $languages as $key => $val) {
            
            $lp = $this->models->LanguageProject->_get(
				array(
					'id' => array(
						'language_id' => $val['id'], 
						'project_id' => $this->getCurrentProjectId()
					)
				)
			);
            
            $languages[$key]['language_project_id'] = $lp[0]['id'];
            
            $languages[$key]['is_project_default'] = ($lp[0]['def_language'] == 1);
            
            $languages[$key]['is_active'] = ($lp[0]['active'] == 'y');

            $languages[$key]['tranlation_status'] = $lp[0]['tranlation_status'];

        }
        
        $this->smarty->assign('data', $this->getCurrentProjectData());
        
        $this->smarty->assign('languages', $languages);
        
        $this->printPage();
    
    }


	/**
	* General interface for all AJAX-calls
	*
	* calls ajaxActionModules -> add, remove, status change of modules
	* calls ajaxActionCollaborators -> add, remove, status change of collaborators / modules
	* calls ajaxActionLanguages -> add, remove, status change of project languages
	*
	* @access     public
	*/
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('view')) return;

        if ($this->rHasVal('view','modules')) {
            
            $this->ajaxActionModules(
                $this->requestData['type'], 
                $this->requestData['action'], 
                $this->requestData['id']
            );
        
        } else
        if ($this->rHasVal('view','collaborators')) {
       
            $this->ajaxActionCollaborators(
                $this->requestData['type'], 
                $this->requestData['action'], 
                $this->requestData['id'], 
                $this->requestData['user']
            );
        
        } else
        if ($this->rHasVal('view','languages')) {
            
            $this->ajaxActionLanguages(
                $this->requestData['action'], 
                $this->requestData['id']
            );

        }

        $this->printPage();
    
    }


	public function createAction()
	{

        $this->checkAuthorisation(true);
        
        $this->setPageName(_('Create new project'));

		$this->setSuppressProjectInBreadcrumbs();

        if (isset($this->requestData) && !$this->isFormResubmit()) {
				
			if (!$this->rHasVal('title') || !$this->rHasVal('sys_description')) {
	
				if (!$this->rHasVal('title')) $this->addError(_('A title is required.'));
				if (!$this->rHasVal('sys_description')) $this->addError(_('A description is required.'));
				
			} else {
	
				$id = $this->createProject(
					array(
						'title' => $this->requestData['title'],
						'version' => isset($this->requestData['version']) ? $this->requestData['version'] : null,
						'sys_description' => $this->requestData['sys_description'],
					)
				);
				
				if ($id) {
				
					$this->addUserToProject($this->getCurrentUserId(),$id,ID_ROLE_SYS_ADMIN);
					
					$this->reInitUserRolesAndRights();
	                $this->setCurrentProjectId($id);
	                $this->setCurrentProjectData();
					$this->getCurrentUserCurrentRole(true);

					$this->smarty->assign('saved',true);
					$this->addMessage(sprintf(_('Project \'%s\' saved.'),$this->requestData['title']));
					$this->addMessage(sprintf('You have been assigned to the new project as system administrator.'));
				
				} else {

					$this->addError(_('Could not save project (duplicate name?).'));

				}

			}

        }
        
		if (isset($this->requestData)) $this->smarty->assign('data',$this->requestData);

        $this->printPage();

	}


	public function deleteAction()
	{

        $this->checkAuthorisation(true);
        
        $this->setPageName(_('Delete a project'));

		$this->setSuppressProjectInBreadcrumbs();


        if ($this->rHasVal('action','delete') && $this->rHasVal('id') && !$this->isFormResubmit()) {
		
			$this->doDeleteProjectAction($this->requestData['id']);
			
			$this->reInitUserRolesAndRights();
	
			$this->addMessage('Project deleted.');

		
		} else {

			$d = $this->rHasVal('p') ? array('id' => $this->requestData['p']) : '*';
	
			$projects = $this->models->Project->_get(array('id' => $d));
			
			if ($this->rHasVal('p')) {
	
				$this->smarty->assign('project',$projects[0]);
	
			} else {
			
				$this->smarty->assign('projects',$projects);
	
			}
			
		}
		
        $this->printPage();

	}

    private function ajaxActionModules ($moduleType, $action, $moduleId)
    {

        if ($moduleType == 'free') {
            
            if ($action == 'module_publish') {
                
                $this->models->FreeModuleProject->update(array(
                    'active' => 'y'
                ), array(
                    'id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            } elseif ($action == 'module_unpublish') {
                
                $this->models->FreeModuleProject->update(array(
                    'active' => 'n'
                ), array(
                    'id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            } elseif ($action == 'module_delete') {
                
                $this->models->FreeModuleProjectUser->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'free_module_id' => $moduleId
                ));
                
                $this->models->FreeModuleProject->delete(array(
                    'id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            }
        
        } elseif ($moduleType == 'regular') {

            if ($action == 'module_activate') {
                
                $this->models->ModuleProject->save(array(
                    'id' => null, 
                    'module_id' => $moduleId, 
                    'active' => 'n',
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            } elseif ($action == 'module_publish') {
                
                $this->models->ModuleProject->update(
                    array(
                        'active' => 'y'
                    ), 
                    array(
                        'module_id' => $moduleId, 
                        'project_id' => $this->getCurrentProjectId()
                    )
                );
            

            } elseif ($action == 'module_unpublish') {
                
                $this->models->ModuleProject->update(array(
                    'active' => 'n'
                ), array(
                    'module_id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            } elseif ($action == 'module_delete') {
                
                $this->models->ModuleProjectUser->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'module_id' => $moduleId
                ));
                
                $this->models->ModuleProject->delete(array(
                    'module_id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            
            }
        
        }
    
    }


    private function ajaxActionCollaborators ($moduleType, $action, $moduleId, $userId)
    {

        if ($moduleType == 'free') {
            
            if ($action == 'add') {
                
				if (is_array($userId)) {
				
					foreach((array)$userId as $key => $val) {
		
						$this->models->FreeModuleProjectUser->save(
						array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'free_module_id' => $moduleId, 
							'user_id' => $val[0]
						));
		
					}
				
				} else {

					$this->models->FreeModuleProjectUser->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'free_module_id' => $moduleId, 
						'user_id' => $userId
					));

				}
            
            }
            else if ($action == 'remove') {
                
                $this->models->FreeModuleProjectUser->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'free_module_id' => $moduleId, 
                    'user_id' => $userId
                ));
            
            }
        
        } elseif ($moduleType == 'regular') {
            
            if ($action == 'add') {
                
				if (is_array($userId)) {
				
					foreach((array)$userId as $key => $val) {
		
						$this->models->ModuleProjectUser->save(
						array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'module_id' => $moduleId, 
							'user_id' => $val[0]
						));
		
					}
				
				} else {

					$this->models->ModuleProjectUser->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $moduleId, 
						'user_id' => $userId
					));

				}
            
            }
            else if ($action == 'remove') {
                
                $this->models->ModuleProjectUser->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'module_id' => $moduleId, 
                    'user_id' => $userId
                ));
            
            }
        
        }

		$cur = $this->getCurrentUserRights($this->getCurrentUserId());

		$this->setUserSessionRights($cur['rights']);


    }


    private function ajaxActionLanguages ($action, $languageId)
    {
        
        if ($action == 'add') {
            
            $lp = $this->models->LanguageProject->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId())
				)
			);

            $make_default = (count((array) $lp) == 0);
            
            $this->models->LanguageProject->save(
            array(
                'id' => null, 
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId(), 
                'def_language' => $make_default ? 1 : 0, 
                'active' => 'n'
            ));
            
            if ($this->models->LanguageProject->getNewId() == '')
                $this->addError(_('Language already assigned.'));
        
        } elseif ($action == 'default') {
            
            $this->models->LanguageProject->update(array(
                'def_language' => 0
            ), array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->LanguageProject->update(array(
                'def_language' => 1
            ), array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        
        } elseif ($action == 'deactivate' || $action == 'reactivate') {
            
            $this->models->LanguageProject->update(array(
                'active' => ($action == 'deactivate' ? 'n' : 'y' )
            ), array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        
        } elseif ($action == 'delete') {
            
            $this->models->ContentTaxon->delete(array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->LanguageProject->delete(array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        
        } elseif ($action == 'translated' || $action == 'untranslated') {

            $this->models->LanguageProject->update(array(
                'tranlation_status' => ($action == 'translated' ? 1 : 0 )
            ), array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));

        }
    
    }


	private function doDeleteProjectAction($projectId)
	{

		$this->deleteGeoData($projectId);
		$this->deleteMatrices($projectId);
		$this->deleteDichotomousKey($projectId);
		$this->deleteGlossary($projectId);
		$this->deleteLiterature($projectId);
		$this->deleteProjectContent($projectId);
		$this->deleteCommonnames($projectId);
		$this->deleteSpeciesMedia($projectId);
		$this->deleteSpeciesContent($projectId);
		$this->deleteStandardCat($projectId);
		$this->deleteSpecies($projectId);
		$this->deleteProjectRanks($projectId);
		$this->deleteProjectUsers($projectId);
		$this->deleteProjectLanguage($projectId);
		$this->deleteModulesFromProject($projectId);
		$this->deleteProjectImagePaths($projectId);
		$this->deleteProject($projectId);

	}

	private function deleteGeoData($id)
	{

		$this->models->OccurrenceTaxon->delete(array('project_id' => $id));
		$this->models->GeodataTypeTitle->delete(array('project_id' => $id));
		$this->models->GeodataType->delete(array('project_id' => $id));

	}

	private function deleteMatrices($id)
	{

		$this->models->MatrixTaxonState->delete(array('project_id' => $id));
		$this->models->MatrixTaxon->delete(array('project_id' => $id));
		$this->models->CharacteristicLabelState->delete(array('project_id' => $id));
		$this->models->CharacteristicState->delete(array('project_id' => $id));
		$this->models->CharacteristicMatrix->delete(array('project_id' => $id));
		$this->models->CharacteristicLabel->delete(array('project_id' => $id));
		$this->models->Characteristic->delete(array('project_id' => $id));
		$this->models->MatrixName->delete(array('project_id' => $id));
		$this->models->Matrix->delete(array('project_id' => $id));

	}
	
	private function deleteDichotomousKey($id)
	{

		$this->models->ChoiceContentKeystep->delete(array('project_id' => $id));
		$this->models->ChoiceKeystep->delete(array('project_id' => $id));
		$this->models->ContentKeystep->delete(array('project_id' => $id));
		$this->models->Keystep->delete(array('project_id' => $id));

	}

	private function deleteGlossary($id)
	{

		$paths = $this->makePathNames($id);

		$mt = $this->models->GlossaryMedia->_get(array('id' => array('project_id' => $id)));

		foreach((array)$mt as $val) {

			if (isset($val['file_name'])) @unlink($paths['project_media'].$val['file_name']);
			if (isset($val['thumb_name'])) @unlink($paths['project_thumbs'].$val['thumb_name']);

		}

		$this->models->GlossaryMedia->delete(array('project_id' => $id));
		$this->models->GlossarySynonym->delete(array('project_id' => $id));
		$this->models->Glossary->delete(array('project_id' => $id));
			
	}

	private function deleteLiterature($id)
	{

		$this->models->LiteratureTaxon->delete(array('project_id' => $id));
		$this->models->Literature->delete(array('project_id' => $id));
			
	}

	private function deleteProjectContent($id)
	{
	
		$this->models->Content->delete(array('project_id' => $id));

	}

	private function deleteSpeciesMedia($id)
	{

		$paths = $this->makePathNames($id);

		$mt = $this->models->MediaTaxon->_get(array('id' => array('project_id' => $id)));

		foreach((array)$mt as $val) {

			if (isset($val['file_name'])) @unlink($paths['project_media'].$val['file_name']);
			if (isset($val['thumb_name'])) @unlink($paths['project_thumbs'].$val['thumb_name']);

		}

		$this->models->MediaTaxon->delete(array('project_id' => $id));
		$this->models->MediaDescriptionsTaxon->delete(array('project_id' => $id));

	}

	private function deleteCommonnames($id)
	{

		$this->models->Commonname->delete(array('project_id' => $id));

	}

	private function deleteStandardCat($id)
	{
	
		$this->models->PageTaxonTitle->delete(array('project_id' => $id));
		$this->models->PageTaxon->delete(array('project_id' => $id));

	}

	private function deleteSpeciesContent($id)
	{

		$this->models->ContentTaxon->delete(array('project_id' => $id));

	}

	private function deleteSpecies($id)
	{

		$this->models->Taxon->delete(array('project_id' => $id));

	}

	private function deleteProjectRanks($id)
	{

		$this->models->ProjectRank->delete(array('project_id' => $id));

	}

	private function deleteProjectUsers($id)
	{
	
		$this->models->ProjectRoleUser->delete(array('project_id' => $id));

	}

	private function deleteProjectLanguage($id)
	{
	
		$this->models->LanguageProject->delete(array('project_id' => $id));
	
	}

	private function deleteModulesFromProject($id)
	{

		$this->models->ModuleProject->delete(array('project_id' => $id));	
	
	}

	private function deleteProjectImagePaths($id)
	{
	
		$paths = $this->makePathNames($id);

		@unlink($paths['project_thumbs']);
		@unlink($paths['project_media']);

	}

	private function deleteProject($id)
	{
	
		$p = $this->models->Project->delete(array('id' => $id));
	
	}


}


