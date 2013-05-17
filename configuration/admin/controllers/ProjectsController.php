<?php

/*

	set upload maximum for media uploads per projects
	deleting of language should delete glossary terms

*/

include_once ('Controller.php');
include_once ('ProjectDeleteController.php');
class ProjectsController extends Controller
{
    public $usedModels = array(
        'commonname', 
        'content_keystep', 
        'content_taxon', 
		'free_module_page', 
		'glossary', 
		'literature', 
		'matrix', 
		'media_taxon', 
		'occurrence_taxon', 
		'synonym', 
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Project administration';
    public $cssToLoad = array(
        'lookup.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'project.js', 
            'module.js', 
            'lookup.js'
        )
    );



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
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
    public function indexAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Index'));
        
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
        
        $this->setPageName($this->translate('Project overview'));
        
        // get all modules activated in this project
        $modules = $this->models->ModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'module_id asc'
        ));
        
        foreach ((array) $modules as $key => $val) {
            
            // get info per module
            $mp = $this->models->Module->_get(array(
                'id' => $val['module_id']
            ));
            
            $modules[$key]['icon'] = $mp['icon'];
            $modules[$key]['module'] = $mp['module'];
            $modules[$key]['controller'] = $mp['controller'];
            $modules[$key]['show_in_menu'] = $mp['show_in_menu'];
            
            // see if the current user has any rights within the module
            if (isset($_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$mp['controller']]) || $this->isCurrentUserSysAdmin())
                $modules[$key]['_rights'] = $_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$mp['controller']];
        }
        
        $freeModules = $this->models->FreeModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        foreach ((array) $freeModules as $key => $val) {
            
            // see if the current user has any rights within the module
            if ((isset($_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()]['_freeModules'][$val['id']]) && $_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()]['_freeModules'][$val['id']] === true) ||
             $this->isCurrentUserSysAdmin())
                $freeModules[$key]['currentUserRights'] = true;
        }
        
        unset($_SESSION['admin']['user']['freeModules']['activeModule']);
        
        $this->smarty->assign('modules', $modules);
        
        $this->smarty->assign('freeModules', $freeModules);
        
        $this->smarty->assign('currentUserRoleId', $this->getCurrentUserRoleId());
        
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
        
        $this->setPageName($this->translate('Project modules'));
        
        if ($this->rHasVal('module_new')) {
            
            $fmp = $this->models->FreeModuleProject->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                )
            ));
            
            if (count((array) $fmp) < $this->controllerSettings['freeModulesMax'] && !$this->isFormResubmit()) {
                
                $this->models->FreeModuleProject->save(
                array(
                    'id' => null, 
                    'module' => $this->requestData['module_new'], 
                    'project_id' => $this->getCurrentProjectId(), 
                    'active' => 'n'
                ));
            }
            else {
                
                $this->addError(sprintf($this->translate('There is a maximum of %s self-defined modules.'), $this->controllerSettings['freeModulesMax']));
            }
        }
        
        $modules = $this->models->Module->_get(array(
            'id' => array(
                '1' => '1'
            ), 
            'order' => 'show_order'
        ));
        
        foreach ((array) $modules as $key => $val) {
            
            $mp = $this->models->ModuleProject->_get(
            array(
                'id' => array(
                    'module_id' => $val['id'], 
                    'project_id' => $this->getCurrentProjectId()
                )
            ));
            
            $modules[$key]['module_project_id'] = $mp[0]['id'] ? $mp[0]['id'] : false;
            $modules[$key]['active'] = $mp[0]['id'] ? $mp[0]['active'] : false;
        }
        
        $freeModules = $this->models->FreeModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
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
        
        $this->setPageName($this->translate('Assign collaborator to modules'));
        
        $modules = $this->models->ModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'module_id asc'
        ));
        
        foreach ((array) $modules as $key => $val) {
            
            $mp = $this->models->Module->_get(array(
                'id' => $val['module_id']
            ));
            
            $modules[$key]['module'] = $mp['module'];
            
            $modules[$key]['description'] = $mp['description'];
            
            $mpu = $this->models->ModuleProjectUser->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'module_id' => $val['module_id']
                )
            ));
            
            foreach ((array) $mpu as $k => $v) {
                
                $modules[$key]['collaborators'][$v['user_id']] = $v;
            }
        }
        
        $free_modules = $this->models->FreeModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));
        
        foreach ((array) $free_modules as $key => $val) {
            
            $fpu = $this->models->FreeModuleProjectUser->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'free_module_id' => $val['id']
                )
            ));
            
            foreach ((array) $fpu as $k => $v) {
                
                $free_modules[$key]['collaborators'][$v['user_id']] = $v;
            }
        }
        
        $pru = $this->models->ProjectRoleUser->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'distinct user_id, role_id'
        ));
        
        foreach ((array) $pru as $key => $val) {
            
            $u = $this->models->User->_get(array(
                'id' => $val['user_id']
            ));
            
            $r = $this->models->Role->_get(array(
                'id' => $val['role_id']
            ));
            
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
        
        $this->setPageName($this->translate('Project data'));

        if (isset($this->requestData) && !$this->isFormResubmit()) {
            // saving all data (except the logo image)
			
			$this->saveProjectData($this->requestData);
        }
        
        $this->setCurrentProjectData();
        
        $languages = $this->getAvailableLanguages();
        
        foreach ((array) $languages as $key => $val) {
            
            $lp = $this->models->LanguageProject->_get(
            array(
                'id' => array(
                    'language_id' => $val['id'], 
                    'project_id' => $this->getCurrentProjectId()
                )
            ));
            
            $languages[$key]['language_project_id'] = $lp[0]['id'];
            
            $languages[$key]['is_project_default'] = ($lp[0]['def_language'] == 1);
            
            $languages[$key]['is_active'] = ($lp[0]['active'] == 'y');
            
            $languages[$key]['tranlation_status'] = $lp[0]['tranlation_status'];
        }
        
        $this->smarty->assign('data', $this->getCurrentProjectData());
        
        $this->smarty->assign('languages', $languages);
        
        $this->printPage();
    }

    public function settingsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Project settings'));

        if ($this->rHasVal('action','save') && !$this->isFormResubmit()) {
			
			$c=0;
			
			if ($this->rHasVar('setting')) {
			
				foreach((array)$this->requestData['setting'] as $key => $val) {
					
					$val = trim($val);
					
					if (empty($val)) {
						
						$c += $this->saveSetting(array('name' => $key,'delete' => true));
						
					} else {
	
						$c += $this->saveSetting(array('name' => $key,'value' => $val));
	
					}
					
				}
				
			}

			if ($this->rHasVal('new_setting')) {

				$v = $this->getSetting($this->requestData['new_setting']);

				if (is_null($v)) {
			
					if ($this->rHasVal('new_setting') && !$this->rHasVal('new_value')) {
						$this->addError(sprintf($this->translate('A value is required for "%s".'),$this->requestData['new_setting']));
						$this->smarty->assign('new_setting',$this->requestData['new_setting']);
					} else
					if ($this->rHasVal('new_setting') && $this->rHasVal('new_value')) {
				
						$c += $this->saveSetting(array('name' => $this->requestData['new_setting'],'value' => $this->requestData['new_value']));
					
					}

				} else {
	
					$this->addError(sprintf($this->translate('A setting with the name "%s" already exists.'),$this->requestData['new_setting']));
					$this->smarty->assign('new_setting',$this->requestData['new_setting']);
					$this->smarty->assign('new_value',$this->requestData['new_value']);
	
				}

			}
			
			if ($c>0)
				$this->addMessage('Data saved.');

        }
		
		$s = $this->models->Settings->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
            ),
            'columns' => 'setting,value',
			'order' => 'setting'
        ));
		
        $this->smarty->assign('settings',$s);
        
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
        if (!$this->rHasVal('view'))
            return;
        
        if ($this->rHasVal('view', 'modules')) {
            
            $this->ajaxActionModules($this->requestData['type'], $this->requestData['action'], $this->requestData['id']);
        }
        else if ($this->rHasVal('view', 'collaborators')) {
            
            $this->ajaxActionCollaborators($this->requestData['type'], $this->requestData['action'], $this->requestData['id'], $this->requestData['user']);
        }
        else if ($this->rHasVal('view', 'languages')) {
            
            $this->ajaxActionLanguages($this->requestData['action'], $this->requestData['id']);
        }
        
        $this->printPage();
    }

    public function createAction ()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Create new project'));
        
        $this->setBreadcrumbRootName($this->translate('System administration'));
        
        $this->setSuppressProjectInBreadcrumbs();
        
        if (isset($this->requestData) && !$this->isFormResubmit()) {
            
            if (!$this->rHasVal('title') || !$this->rHasVal('sys_description') || !$this->rHasVal('language')) {
                
                if (!$this->rHasVal('title'))
                    $this->addError($this->translate('A title is required.'));
                if (!$this->rHasVal('sys_description'))
                    $this->addError($this->translate('A description is required.'));
                if (!$this->rHasVal('language'))
                    $this->addError($this->translate('A default language is required.'));
            }
            else {
                
                $id = $this->createProject(
                array(
                    'title' => $this->requestData['title'], 
                    'version' => isset($this->requestData['version']) ? $this->requestData['version'] : null, 
                    'sys_description' => $this->requestData['sys_description']
                ));
                
                if ($id) {
                    
                    $this->models->LanguageProject->save(
                    array(
                        'id' => null, 
                        'language_id' => $this->requestData['language'], 
                        'project_id' => $id, 
                        'def_language' => 1, 
                        'active' => 'y'
                    ));
                    
                    $this->createProjectCssFile($id, $this->requestData['title']);
                    
                    $this->addAllModulesToProject($id);
                    $this->addUserToProject($this->getCurrentUserId(), $id, ID_ROLE_SYS_ADMIN);
                    
                    $this->unsetProjectSessionData();
                    $this->reInitUserRolesAndRights();
                    $this->setCurrentProjectId($id);
                    $this->setCurrentProjectData();
                    $this->setCurrentUserRoleId();
                    
                    $this->smarty->assign('saved', true);
                    $this->addMessage(sprintf($this->translate('Project \'%s\' saved.'), $this->requestData['title']));
                    $this->addMessage(sprintf('You have been assigned to the new project as system administrator.'));
                }
                else {
                    
                    $this->addError($this->translate('Could not save project (duplicate name?).'));
                }
            }
        }
        
        if (isset($this->requestData))
            $this->smarty->assign('data', $this->requestData);
        
        $this->smarty->assign('languages', $this->getAvailableLanguages());
        
        $this->printPage();
    }

    public function deleteAction ()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Delete a project'));
        
        $this->setBreadcrumbRootName($this->translate('System administration'));
        
        $this->setSuppressProjectInBreadcrumbs();
        
        if ($this->rHasVal('action', 'delete') && $this->rHasVal('id') && !$this->isFormResubmit()) {
            
            $this->doDeleteProjectAction($this->requestData['id']);
            
            $this->reInitUserRolesAndRights();
            
            $this->addMessage('Project deleted.');
        }
        else {
            
            $d = $this->rHasVal('p') ? array(
                'id' => $this->requestData['p']
            ) : '*';
            
            $projects = $this->models->Project->_get(array(
                'id' => $d, 
                'order' => 'title'
            ));
            
            if ($this->rHasVal('p')) {
                
                $this->smarty->assign('project', $projects[0]);
            }
            else {
                
                $this->smarty->assign('projects', $projects);
            }
        }
        
        $this->printPage();
    }

    public function deleteOrphanAction ()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Delete orphaned project'));
        
        $this->setBreadcrumbRootName($this->translate('System administration'));
        
        $this->setSuppressProjectInBreadcrumbs();
        
        if ($this->rHasVal('action', 'delete') && !$this->isFormResubmit()) {
           
			$this->doDeleteOrpahnedData();

            $this->addMessage('Deleted orphaned data.');
			$this->smarty->assign('processed',true);

        }
		
        $this->printPage();

    }

    public function getInfoAction ()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Project info'));
        
        $this->smarty->assign('commonname', 
        $this->models->Commonname->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('contentkeystep', 
        $this->models->ContentKeystep->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('contenttaxon', 
        $this->models->ContentTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('freemodulepage', 
        $this->models->FreeModulePage->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('freemoduleproject', 
        $this->models->FreeModuleProject->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('glossary', 
        $this->models->Glossary->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('literature', 
        $this->models->Literature->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('matrix', $this->models->Matrix->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('mediataxon', 
        $this->models->MediaTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('occurrencetaxon', 
        $this->models->OccurrenceTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('synonym', 
        $this->models->Synonym->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));
        
        $this->smarty->assign('taxon', $this->models->Taxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));


        $this->smarty->assign('variations', $this->models->TaxonVariation->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        )));

        
        $this->printPage();
    }

	public function changeIdAction()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Change a project ID'));
        
        $this->setBreadcrumbRootName($this->translate('System administration'));
        
        $this->setSuppressProjectInBreadcrumbs();

		$projects = $this->models->Project->_get(array(
			'id' => '*'
		));
		
		if ($this->rHasVal('newId') && !$this->isFormResubmit()) {
                
            $p = $this->models->Project->_get(array(
                'id' => array('id' => $this->requestData['newId']),
            ));
			
			if ($p) {
				
				 $this->addError(sprintf($this->translate('A project with ID %s already exists (%s).'),$this->requestData['newId'],$p[0]['title']));

			} else {


				if ($this->rHasVal('action','change')) {
					
					$this->doChangeProjectId($this->requestData['p'],$this->requestData['newId']);
					
					$this->smarty->assign('done', true);

				} else {
					
					$projects = $this->models->Project->_get(array(
						'id' => array('id' => $this->requestData['p']),
					));
								
					$this->smarty->assign('newId', $this->requestData['newId']);
					
				}
				
				$this->smarty->assign('oldId', $this->requestData['p']);
				
			}
			
        }
		
		$this->smarty->assign('projects', $projects);
        
        $this->printPage();

    }
	
	public function clearCacheAction()
    {
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Clear project cache'));
        
		if ($this->rHasVal('action','clear') && !$this->isFormResubmit()) {
                
			$this->emptyCacheFolder();
			
			$this->addMessage('Cleared project cache.');

	        $this->smarty->assign('cleared', true);

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
            }
            elseif ($action == 'module_unpublish') {
                
                $this->models->FreeModuleProject->update(array(
                    'active' => 'n'
                ), array(
                    'id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_delete' && isset($moduleId)) {
                
				$pDel = new ProjectDeleteController;	
				$pDel->deleteFreeModules($this->getCurrentProjectId(), $moduleId);

            }
        }
        elseif ($moduleType == 'regular') {
            
            if ($action == 'module_activate') {
                
                $this->models->ModuleProject->save(
                array(
                    'id' => null, 
                    'module_id' => $moduleId, 
                    'active' => 'n', 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_publish') {
                
                $this->models->ModuleProject->update(array(
                    'active' => 'y'
                ), array(
                    'module_id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_unpublish') {
                
                $this->models->ModuleProject->update(array(
                    'active' => 'n'
                ), array(
                    'module_id' => $moduleId, 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_delete') {

				$pDel = new ProjectDeleteController;
	
				if ($moduleId==MODCODE_INTRODUCTION) {
			        $pDel->deleteIntroduction($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_GLOSSARY) {
			        $pDel->deleteGlossary($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_LITERATURE) {
			        $pDel->deleteLiterature($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_SPECIES) {
					$pDel->deleteCommonnames($this->getCurrentProjectId());
					$pDel->deleteSynonyms($this->getCurrentProjectId());
					$pDel->deleteSpeciesMedia($this->getCurrentProjectId());
					$pDel->deleteSpeciesContent($this->getCurrentProjectId());
					$pDel->deleteStandardCat($this->getCurrentProjectId());
					$pDel->deleteSpecies($this->getCurrentProjectId());
			        $pDel->deleteProjectRanks($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_HIGHERTAXA) {
			        $pDel->deleteIntroduction($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_KEY) {
			        $pDel->deleteDichotomousKey($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_MATRIXKEY) {
					$pDel->deleteMatrices($this->getCurrentProjectId());
					$pDel->deleteNBCKeydata($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_DISTRIBUTION) {
			        $pDel->deleteGeoData($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_CONTENT) {
			        $pDel->deleteProjectContent($this->getCurrentProjectId());
				}

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
                    
                    foreach ((array) $userId as $key => $val) {
                        
                        $this->models->FreeModuleProjectUser->save(
                        array(
                            'id' => null, 
                            'project_id' => $this->getCurrentProjectId(), 
                            'free_module_id' => $moduleId, 
                            'user_id' => $val[0]
                        ));
                    }
                }
                else {
                    
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
        }
        elseif ($moduleType == 'regular') {
            
            if ($action == 'add') {
                
                if (is_array($userId)) {
                    
                    foreach ((array) $userId as $key => $val) {
                        
                        $this->models->ModuleProjectUser->save(
                        array(
                            'id' => null, 
                            'project_id' => $this->getCurrentProjectId(), 
                            'module_id' => $moduleId, 
                            'user_id' => $val[0]
                        ));
                    }
                }
                else {
                    
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
        
        $cur = $this->getUserRights($this->getCurrentUserId());
        
        $this->setUserSessionRights($cur['rights']);
    }

    private function ajaxActionLanguages ($action, $languageId)
    {
        if ($action == 'add') {
            
            $lp = $this->models->LanguageProject->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                )
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
                $this->addError($this->translate('Language already assigned.'));
        }
        elseif ($action == 'default') {
            
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
        }
        elseif ($action == 'deactivate' || $action == 'reactivate') {
            
            $this->models->LanguageProject->update(array(
                'active' => ($action == 'deactivate' ? 'n' : 'y')
            ), array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'delete') {
            
            $this->models->ContentTaxon->delete(array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->LanguageProject->delete(array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'translated' || $action == 'untranslated') {
            
            $this->models->LanguageProject->update(array(
                'tranlation_status' => ($action == 'translated' ? 1 : 0)
            ), array(
                'language_id' => $languageId, 
                'project_id' => $this->getCurrentProjectId()
            ));
        }
    }

    private function getAvailableLanguages ()
    {
        return array_merge((array) $this->models->Language->_get(array(
            'id' => array(
                'show_order is not' => null
            ), 
            'order' => 'show_order asc'
        )), (array) $this->models->Language->_get(array(
            'id' => array(
                'show_order is' => null
            ), 
            'order' => 'language asc'
        )));
    }

    private function addAllModulesToProject ($id)
    {
        $m = $this->models->Module->_get(array(
            'id' => '*', 
            'order' => 'show_order'
        ));
        
        foreach ((array) $m as $val)
            $this->addModuleToProject($val['id'], $id);
    }

    private function doDeleteProjectAction ($pId)
    {
		
		$pDel = new ProjectDeleteController;
		$pDel->doDeleteProjectAction($pId);

    }

	private function doDeleteOrpahnedData()
	{
		
		$data = $this->models->Project->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Project->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val) {

			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			// all user tables, infrastructural tables and the project table itself lack a project_id column.
			$d = $this->models->Project->freeQuery('select distinct project_id from '.$table);

			foreach((array)$d as $dVal)
				$pInUse[$dVal['project_id']]=$dVal['project_id'];
				
			//echo $table;q($d);

		}
		
		foreach(glob($this->generalSettings['directories']['mediaDirProject'].'/*',GLOB_ONLYDIR) as $file) {
			if(is_dir($file)) {
				$boom = explode('/',$file);
				$boom = array_pop($boom);
				if (is_numeric($boom))
					$pInUse[intval($boom)] = intval($boom);
			}
		}
		foreach(glob($this->generalSettings['directories']['cache'].'/*',GLOB_ONLYDIR) as $file) {
			if(is_dir($file)) {
				$boom = explode('/',$file);
				$boom = array_pop($boom);
				if (is_numeric($boom))
					$pInUse[intval($boom)] = intval($boom);
			}
		}

		$d = $this->models->Project->_get(array('id' => '*'));
		
		foreach((array)$d as $val) {

			unset($pInUse[$val['id']]);
			$this->addMessage(sprintf('Ignoring "%s"',$val['sys_name']));

		}
		
		foreach((array)$pInUse as $val) {
	
			$this->doDeleteProjectAction($val);
			$this->addMessage(sprintf('Deleted data for orphan ID %s',$val));

		}
		
	}

	private function doChangeProjectId($oldId,$newId)
	{

		$data = $this->models->Project->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Project->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val) {

			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			$d = $this->models->Project->freeQuery('select count(*) as total from '.$table.' where project_id = '.$oldId);
			
			if ($d[0]['total']>0) {
			
				$this->models->Project->freeQuery('update '.$table.' set project_id = '.$newId.' where project_id = '.$oldId);
				$this->addMessage('Updated '.$table);
				
			}

		}
	
		rename(
			$this->generalSettings['directories']['mediaDirProject'].'/'.$this->getProjectFSCode($oldId),
			$this->generalSettings['directories']['mediaDirProject'].'/'.$this->getProjectFSCode($newId)
		);

		$this->addMessage('Renamed media directory');

		rename(
			$this->generalSettings['directories']['cache'].'/'.$this->getProjectFSCode($oldId),
			$this->generalSettings['directories']['cache'].'/'.$this->getProjectFSCode($newId)
		);

		$this->addMessage('Renamed cache directory');

		$this->models->Project->update(
			array('id'=>$newId),
			array('id'=>$oldId)
		);

		$this->addMessage('Updated project table');
	
	}

	private function saveProjectData($data)
	{

		$data['id'] = $this->getCurrentProjectId();
			
		if (!$this->isCurrentUserSysAdmin() && isset($data['sys_name'])) {
			unset($this->requestData['sys_name']);
		}
			
		$p = $this->models->Project->update(
			array(
				'short_name' => 'null',
				'css_url' => 'null',
				'keywords' => 'null',
				'description' => 'null',
				'group' => 'null'
			),
			array('id'=>$data['id'])
		);
		
		if (isset($data['sys_name'])) {

            $p = $this->models->Project->_get(array(
                'id' => array('id !=' => $data['id'],'sys_name'=>$data['sys_name']),
            ));
			
			if ($p) {
				
				$this->addError('A project with that internal name alreasy exists.');
				unset($this->requestData['sys_name']);
				
			}

		}

		if (isset($data['short_name'])) {

            $p = $this->models->Project->_get(array(
                'id' => array('id !=' => $data['id'],'short_name'=>$data['short_name']),
            ));
			
			if ($p) {
				
				$this->addError(sprintf('A project with that short name name alreasy exists (%s).',$p[0]['sys_name']));
				unset($this->requestData['short_name']);
				
			}

		}

		$this->models->Project->save($data);
		
	}

    private function emptyCacheFolder()
    {

		$cachePath = $this->makeCachePath();
		
		if (empty($cachePath))
			return;
		
        if (file_exists($cachePath))
			array_map('unlink', glob($cachePath.'/*'));
		
    }	
}
