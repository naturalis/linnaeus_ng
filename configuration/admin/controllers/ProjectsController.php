<?php

/*

	set upload maximum for media uploads per projects

    hard coded number of free modules
    deleting of (free) moduels does not as yet delete any data, all the warnings notwithstanding

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

	public $jsToLoad = array('project.js','module.js');

    public function __construct ()
    {
        
        parent::__construct();
   
		$this->isMultiLingual = false;
   
    }



    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));
        
        $this->printPage();
    
    }


    public function modulesAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Project modules'));
        
        if (!empty($this->requestData['module_new'])) {
            
            $fmp = $this->models->FreeModuleProject->get(array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
            if (count((array) $fmp) < 5 && !$this->isFormResubmit()) {
                
                $this->models->FreeModuleProject->save(
                array(
                    'id' => null, 
                    'module' => $this->requestData['module_new'], 
                    'project_id' => $this->getCurrentProjectId(),
                    'active' => 'n'
                ));
                
                $_SESSION['system']['last_rnd'] = $this->requestData['rnd'];
            
            }
        
        }
        
        $modules = $this->models->Module->get(array(
            '1' => '1'
        ), false, 'show_order');
        
        foreach ((array) $modules as $key => $val) {
            
            $mp = $this->models->ModuleProject->get(array(
                'module_id' => $val['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $modules[$key]['module_project_id'] = $mp[0]['id'] ? $mp[0]['id'] : false;
            $modules[$key]['active'] = $mp[0]['id'] ? $mp[0]['active'] : false;
        
        }
        
        $freeModules = $this->models->FreeModuleProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ));

        $this->smarty->assign('modules', $modules);

        $this->smarty->assign('freeModules', $freeModules);

        $this->printPage();
    
    }



    public function collaboratorsAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Assign collaborator to modules'));
        
        $modules = $this->models->ModuleProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ), false, 'module_id asc');
        
        foreach ((array) $modules as $key => $val) {
            
            $mp = $this->models->Module->get($val['module_id']);
            
            $modules[$key]['module'] = $mp['module'];
            
            $modules[$key]['description'] = $mp['description'];
            
            $mpu = $this->models->ModuleProjectUser->get(array(
                'project_id' => $this->getCurrentProjectId(), 
                'module_id' => $val['module_id']
            ));
            
            foreach ((array) $mpu as $k => $v) {
                
                $modules[$key]['collaborators'][$v['user_id']] = $v;
            
            }
        
        }
        
        $free_modules = $this->models->FreeModuleProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        foreach ((array) $free_modules as $key => $val) {
            
            $fpu = $this->models->FreeModuleProjectUser->get(array(
                'project_id' => $this->getCurrentProjectId(), 
                'free_module_id' => $val['id']
            ));
            
            foreach ((array) $fpu as $k => $v) {
                
                $free_modules[$key]['collaborators'][$v['user_id']] = $v;
            
            }
        
        }
        
        $pru = $this->models->ProjectRoleUser->get(array(
            'project_id' => $this->getCurrentProjectId(),
			'role_id !=' => '1'
        ), 'distinct user_id, role_id');
        
        foreach ((array) $pru as $key => $val) {
            
            $u = $this->models->User->get($val['user_id']);
            
            $r = $this->models->Role->get($val['role_id']);
            
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



    public function dataAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Project settings'));

		if (isset($this->requestData['deleteLogo']) && $this->requestData['deleteLogo']=='1' && !$this->isFormResubmit()) {
			
			$data = $this->models->Project->get($this->getCurrentProjectId());
			
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
            
            $this->requestData['id'] = $this->getCurrentProjectId();
            
            $this->models->Project->save($this->requestData);
        
        }
        
        if (isset($this->requestDataFiles)) {

			// save logo
			$this->helpers->FileUploadHelper->setLegalMimeTypes($this->controllerSettings['media']['allowedFormats']);
			$this->helpers->FileUploadHelper->setTempDir($this->getDefaultImageUploadDir());
			$this->helpers->FileUploadHelper->setStorageDir($this->getProjectsMediaStorageDir());
			$this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);
	
			$this->addError($this->helpers->FileUploadHelper->getErrors());
			$filesToSave = $this->helpers->FileUploadHelper->getResult();

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
        
        $data = $this->models->Project->get($this->getCurrentProjectId());
		
		$this->setCurrentProjectData($data);
        
        $languages = array_merge(
			$this->models->Language->get('select * from %table% where show_order is not null order by show_order asc'), 
	        $this->models->Language->get('select * from %table% where show_order is null order by language asc')
		);
        
        foreach ((array) $languages as $key => $val) {
            
            $lp = $this->models->LanguageProject->get(array(
                'language_id' => $val['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $languages[$key]['language_project_id'] = $lp[0]['id'];
            
            $languages[$key]['is_project_default'] = ($lp[0]['def_language'] == 1);
            
            $languages[$key]['is_active'] = ($lp[0]['active'] == 'y');

            $languages[$key]['tranlation_status'] = $lp[0]['tranlation_status'];

        }
        
        $this->smarty->assign('data', $data);
        
        $this->smarty->assign('languages', $languages);
        
        $this->printPage();
    
    }


    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['view'])) return;

        if ($this->requestData['view'] == 'modules') {
            
            $this->ajaxActionModules(
                $this->requestData['type'], 
                $this->requestData['action'], 
                $this->requestData['id']
            );
        
        } elseif ($this->requestData['view'] == 'collaborators') {
       
            $this->ajaxActionCollaborators(
                $this->requestData['type'], 
                $this->requestData['action'], 
                $this->requestData['id'], 
                $this->requestData['user']
            );
        
        } elseif ($this->requestData['view'] == 'languages') {
            
            $this->ajaxActionLanguages(
                $this->requestData['action'], 
                $this->requestData['id']
            );

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

        //NEED CHECKS!
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
    
    }


    private function ajaxActionLanguages ($action, $languageId)
    {
        
        if ($action == 'add') {
            
            $lp = $this->models->LanguageProject->get(array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
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


