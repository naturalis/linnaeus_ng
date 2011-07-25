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
        'content_taxon'
    );
    
    public $usedHelpers = array(
		'file_upload_helper'
    );

    public $controllerPublicName = 'Project administration';

	public $jsToLoad = array('all'=>array('project.js','module.js'));

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
			$this->models->Language->_get(array('id' => 'select * from %table% where show_order is not null order by show_order asc')), 
	        $this->models->Language->_get(array('id' => 'select * from %table% where show_order is null order by language asc'))
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
				
					$this->addUserToProject($this->getCurrentUserId(),$id,ID_ROLE_LEAD_EXPERT);
					
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


}