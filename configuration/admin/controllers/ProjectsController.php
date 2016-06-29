<?php

/*
	deleting of language should delete glossary terms
*/

include_once ('Controller.php');
include_once ('ProjectDeleteController.php');
include_once ('ModuleSettingsReaderController.php');

class ProjectsController extends Controller
{
    public $usedModels = array(
        'commonnames',
        'content_keysteps',
        'content_taxa',
		'free_modules_pages',
		'glossary',
		'literature',
		'matrices',
		'media_taxon',
		'occurrences_taxa',
		'synonyms',
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Project management';
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
	private $freeModulesMax=5;

    public function __construct ()
    {
        parent::__construct();
		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->show_hidden_modules_in_select_list=$this->moduleSettings->getGeneralSetting( [ 'setting'=>'show_hidden_modules_in_select_list', 'subst'=>false ] );
	}

    public function __destruct ()
    {
        parent::__destruct();
    }

	public function chooseProjectAction()
	{
        $this->checkDefaultProjectSelect();
		$this->UserRights->setAllowNoProjectId( true );
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Select a project to work on'));

        if ( $this->rHasVal('project_id') && $this->isCurrentUserAuthorizedForProject($this->rGetVal('project_id')) )
		{
			$this->doSetProject( $this->rGetVal('project_id') );
			$this->redirect( $this->getLoggedInMainIndex() );
		}

        $this->smarty->assign('projects', $this->getCurrentUserProjects());
        $this->printPage();
    }

	public function doChooseProject( $project_id )
	{
        if ( $project_id && $this->isCurrentUserAuthorizedForProject($project_id) )
		{
			$this->doSetProject( $project_id );
		}
    }

    public function indexAction()
    {
        $this->checkAuthorisation();
        $this->setPageName( $this->translate('Management') );
        $this->printPage();
    }

    public function overviewAction()
    {
		$this->UserRights->setDisableUserAccesModuleCheck( true );
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Project overview'));

		$this->wikiPageOverride['basename']='ProjectOverview';


		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->smarty->assign('show_lead_expert_modules', $this->UserRights->hasAppropriateLevel() );

		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
        $this->smarty->assign('show_sys_management_modules', $this->UserRights->hasAppropriateLevel() );

        $this->smarty->assign('modules', $this->models->ProjectsModel->getProjectModules( array('project_id'=>$this->getCurrentProjectId() ) ) );
        $this->printPage();
    }

    /**
	* List of available modules, standard and self-defined, plus possibility of (de)activation
	*
	* @access     public
	*/
    public function modulesAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Project modules'));

        if ($this->rHasVal('module_new'))
		{
            $fmp = $this->models->FreeModulesProjects->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                )
            ));

            if (count((array) $fmp) < $this->freeModulesMax && !$this->isFormResubmit())
			{
                $this->models->FreeModulesProjects->save(
                array(
                    'id' => null,
                    'module' => $this->rGetVal('module_new'),
                    'project_id' => $this->getCurrentProjectId(),
                    'active' => 'n'
                ));
            }
            else
			{
                $this->addError(sprintf($this->translate('There is a maximum of %s self-defined modules.'), $this->freeModulesMax));
            }
        }


		$d = $this->show_hidden_modules_in_select_list ? '*' : ['show_in_menu'=>1];

        $modules = $this->models->Modules->_get(array(
            'id' => $d,
            'order' => 'show_order'
        ));

        foreach ((array) $modules as $key => $val)
		{
            $mp = $this->models->ModulesProjects->_get(
            array(
                'id' => array(
                    'module_id' => $val['id'],
                    'project_id' => $this->getCurrentProjectId()
                )
            ));

            $modules[$key]['module_project_id'] = $mp[0]['id'] ? $mp[0]['id'] : false;
            $modules[$key]['active'] = $mp[0]['id'] ? $mp[0]['active'] : false;
        }

        $freeModules = $this->models->FreeModulesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        $this->smarty->assign('modules', $modules);
        $this->smarty->assign('freeModules', $freeModules);
        $this->smarty->assign('freeModuleMax', $this->freeModulesMax);

        $this->printPage();
    }

    /**
	* Interface to the project settings
	*
	* @access     public
	*/
    public function dataAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Project information'));

        if ( $this->rHasVal('action','save') && !$this->isFormResubmit() )
		{
            // saving all data (except the logo image)
			$this->saveProjectData( $this->rGetAll() );
        }

        $this->setCurrentProjectData();

        $languages = $this->getAvailableLanguages();

        foreach ((array) $languages as $key => $val) {

            $lp = $this->models->LanguagesProjects->_get(
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

        if ($this->rHasVal('view', 'modules'))
		{
			$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
			if ( !$this->getAuthorisationState() ) return;
            $this->ajaxActionModules($this->rGetVal('type'), $this->rGetVal('action'), $this->rGetId());
        }
        elseif ($this->rHasVal('view', 'languages'))
		{
			$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
			if ( !$this->getAuthorisationState() ) return;
            $this->ajaxActionLanguages($this->rGetVal('action'), $this->rGetId());
        }

        $this->printPage();
    }

    public function createAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
		$this->UserRights->setAllowNoProjectId( true );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Create new project'));

        $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setSuppressProjectInBreadcrumbs();

        if (isset($this->requestData) && !$this->isFormResubmit())
		{
            if (!$this->rHasVal('title') || !$this->rHasVal('sys_description') || !$this->rHasVal('language'))
			{
                if (!$this->rHasVal('title'))
                    $this->addError($this->translate('A title is required.'));
                if (!$this->rHasVal('sys_description'))
                    $this->addError($this->translate('A description is required.'));
                if (!$this->rHasVal('language'))
                    $this->addError($this->translate('A default language is required.'));
            }
            else
			{
                $id = $this->createProject(
                array(
                    'title' => $this->rGetVal('title'),
                    'version' => !is_null($this->rGetVal('version')) ? $this->rGetVal('version') : null,
                    'sys_description' => $this->rGetVal('sys_description')
                ));

                if ($id)
				{
                    $this->models->LanguagesProjects->save(
                    array(
                        'id' => null,
                        'language_id' => $this->rGetVal('language'),
                        'project_id' => $id,
                        'def_language' => 1,
                        'active' => 'y'
                    ));

                    $this->createProjectCssFile($id, $this->rGetVal('title'));

                    $this->addAllModulesToProject($id);
                    $this->addUserToProject($this->getCurrentUserId(), $id, ID_ROLE_SYS_ADMIN);

                    $this->unsetProjectSessionData();
                    $this->reInitUserRolesAndRights();
                    $this->setCurrentProjectId($id);
                    $this->setCurrentProjectData();
                    $this->setCurrentUserRoleId();

                    $this->smarty->assign('saved', true);
                    $this->addMessage(sprintf($this->translate('Project \'%s\' saved.'), $this->rGetVal('title')));
                    $this->addMessage(sprintf('You have been assigned to the new project as system administrator.'));
                }
                else
				{
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
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
		$this->UserRights->setAllowNoProjectId( true );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Delete a project'));

        $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setSuppressProjectInBreadcrumbs();

        if ($this->rHasVal('action', 'delete') && $this->rHasVal('id') && !$this->isFormResubmit())
		{
            $this->doDeleteProjectAction($this->rGetId());
            $this->addMessage('Project deleted.');
        }
        else
		{
            $d = $this->rHasVal('p') ? array(
                'id' => $this->rGetVal('p')
            ) : '*';

            $projects = $this->models->Projects->_get(array(
                'id' => $d,
                'order' => 'title'
            ));

            if ($this->rHasVal('p'))
			{
                $this->smarty->assign('project', $projects[0]);
            }
            else
			{
                $this->smarty->assign('projects', $projects);
            }
        }

        $this->printPage();
    }

    public function deleteOrphanAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
		$this->UserRights->setAllowNoProjectId( true );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Delete orphaned data'));

        $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setSuppressProjectInBreadcrumbs();

        if ($this->rHasVal('action', 'delete') && !$this->isFormResubmit())
		{
			$this->doDeleteOrphanedData();
            $this->addMessage('Deleted orphaned data.');
			$this->smarty->assign('processed',true);
        }

        $this->printPage();

    }

	public function changeIdAction()
    {
        $this->checkAuthorisation(true);

        $this->setPageName($this->translate('Change a project ID'));

        $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setSuppressProjectInBreadcrumbs();

		$projects = $this->models->Projects->_get(array(
			'id' => '*'
		));

		if ($this->rHasVal('newId') && !$this->isFormResubmit()) {

            $p = $this->models->Projects->_get(array(
                'id' => array('id' => $this->rGetVal('newId')),
            ));

			if ($p) {

				 $this->addError(sprintf($this->translate('A project with ID %s already exists (%s).'),$this->rGetVal('newId'),$p[0]['title']));

			} else {


				if ($this->rHasVal('action','change')) {

					$this->doChangeProjectId($this->rGetVal('p'),$this->rGetVal('newId'));

					$this->smarty->assign('done', true);

				} else {

					$projects = $this->models->Projects->_get(array(
						'id' => array('id' => $this->rGetVal('p')),
					));

					$this->smarty->assign('newId', $this->rGetVal('newId'));

				}

				$this->smarty->assign('oldId', $this->rGetVal('p'));

			}

        }

		$this->smarty->assign('projects', $projects);

        $this->printPage();

    }


    private function ajaxActionModules ($moduleType, $action, $moduleId)
    {
        if ($moduleType == 'free') {

            if ($action == 'module_publish') {

                $this->models->FreeModulesProjects->update(array(
                    'active' => 'y'
                ), array(
                    'id' => $moduleId,
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_unpublish') {

                $this->models->FreeModulesProjects->update(array(
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

                $this->models->ModulesProjects->save(
                array(
                    'id' => null,
                    'module_id' => $moduleId,
                    'active' => 'n',
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_publish') {

                $this->models->ModulesProjects->update(array(
                    'active' => 'y'
                ), array(
                    'module_id' => $moduleId,
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            elseif ($action == 'module_unpublish') {

                $this->models->ModulesProjects->update(array(
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
					$pDel->deleteMatrices( [ 'project_id'=>$this->getCurrentProjectId() ] );
					$pDel->deleteNBCKeydata($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_DISTRIBUTION) {
			        $pDel->deleteGeoData($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_CONTENT) {
			        $pDel->deleteProjectContent($this->getCurrentProjectId());
				}

                $this->models->ModulesProjects->delete(array(
                    'module_id' => $moduleId,
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
        }
    }

    private function ajaxActionCollaborators ($moduleType, $action, $moduleId, $userId)
    {
        if ($moduleType == 'free')
		{
            if ($action == 'add')
			{
                if (is_array($userId))
				{
                    foreach ((array) $userId as $key => $val)
					{
                        $this->models->FreeModulesProjectsUsers->save(
                        array(
                            'id' => null,
                            'project_id' => $this->getCurrentProjectId(),
                            'free_module_id' => $moduleId,
                            'user_id' => $val[0]
                        ));
                    }
                }
                else
				{
                    $this->models->FreeModulesProjectsUsers->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'free_module_id' => $moduleId,
                        'user_id' => $userId
                    ));
                }
            }
            elseif ($action == 'remove')
			{
                $this->models->FreeModulesProjectsUsers->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'free_module_id' => $moduleId,
                    'user_id' => $userId
                ));
            }
        }
        elseif ($moduleType == 'regular')
		{
            if ($action == 'add')
			{
                if (is_array($userId))
				{
                    foreach ((array) $userId as $key => $val)
					{
                        $this->models->ModulesProjectsUsers->save(
                        array(
                            'id' => null,
                            'project_id' => $this->getCurrentProjectId(),
                            'module_id' => $moduleId,
                            'user_id' => $val[0]
                        ));
                    }
                }
                else
				{
                    $this->models->ModulesProjectsUsers->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'module_id' => $moduleId,
                        'user_id' => $userId
                    ));
                }
            }
            elseif ($action == 'remove')
			{
                $this->models->ModulesProjectsUsers->delete(
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
        if ($action == 'add')
		{
            $lp = $this->models->LanguagesProjects->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                )
            ));

            $make_default = (count((array) $lp) == 0);

            $this->models->LanguagesProjects->save(
            array(
                'id' => null,
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId(),
                'def_language' => $make_default ? 1 : 0,
                'active' => 'n'
            ));

            if ($this->models->LanguagesProjects->getNewId() == '')
                $this->addError($this->translate('Language already assigned.'));
        }
        elseif ($action == 'default')
		{
            $this->models->LanguagesProjects->update(array(
                'def_language' => 0
            ), array(
                'project_id' => $this->getCurrentProjectId()
            ));

            $this->models->LanguagesProjects->update(array(
                'def_language' => 1
            ), array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'deactivate' || $action == 'reactivate')
		{
            $this->models->LanguagesProjects->update(array(
                'active' => ($action == 'deactivate' ? 'n' : 'y')
            ), array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'delete')
		{
            $this->models->ContentTaxa->delete(array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));

            $this->models->LanguagesProjects->delete(array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'translated' || $action == 'untranslated')
		{
            $this->models->LanguagesProjects->update(array(
                'tranlation_status' => ($action == 'translated' ? 1 : 0)
            ), array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
    }

    private function getAvailableLanguages ()
    {
        return array_merge((array) $this->models->Languages->_get(array(
            'id' => array(
                'show_order is not' => null
            ),
            'order' => 'show_order asc'
        )), (array) $this->models->Languages->_get(array(
            'id' => array(
                'show_order is' => null
            ),
            'order' => 'language asc'
        )));
    }

    private function addAllModulesToProject ($id)
    {
        $m = $this->models->Modules->_get(array(
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

	private function doDeleteOrphanedData()
	{

		$data = $this->models->Projects->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Projects->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val)
		{
			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			// all user tables, infrastructural tables and the project table itself lack a project_id column.
			$d = $this->models->Projects->freeQuery('select distinct project_id from '.$table);

			foreach((array)$d as $dVal)
				$pInUse[$dVal['project_id']]=$dVal['project_id'];
		}

		foreach(glob($this->generalSettings['directories']['mediaDirProject'].'/*',GLOB_ONLYDIR) as $file) {
			if(is_dir($file)) {
				$boom = explode('/',$file);
				$boom = array_pop($boom);
				if (is_numeric($boom))
					$pInUse[intval($boom)] = intval($boom);
			}
		}

		$d = $this->models->Projects->_get(array('id' => '*'));

		foreach((array)$d as $val)
		{
			unset($pInUse[$val['id']]);
			$this->addMessage(sprintf('Ignoring "%s"',$val['sys_name']));
		}

		foreach((array)$pInUse as $val)
		{
			$this->doDeleteProjectAction($val);
			$this->addMessage(sprintf('Deleted data for orphan ID %s',$val));
		}

	}

	private function doChangeProjectId($oldId,$newId)
	{

		$data = $this->models->Projects->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Projects->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val)
		{
			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			$d = $this->models->Projects->freeQuery('select count(*) as total from '.$table.' where project_id = '.$oldId);

			if ($d[0]['total']>0)
			{
				$this->models->Projects->freeQuery('update '.$table.' set project_id = '.$newId.' where project_id = '.$oldId);
				$this->addMessage('Updated '.$table);
			}
		}

		rename(
			$this->generalSettings['directories']['mediaDirProject'].'/'.$this->getProjectFSCode($oldId),
			$this->generalSettings['directories']['mediaDirProject'].'/'.$this->getProjectFSCode($newId)
		);

		$this->addMessage('Renamed media directory');

		$this->models->Projects->update(
			array('id'=>$newId),
			array('id'=>$oldId)
		);

		$this->addMessage('Updated project table');

	}

	private function saveProjectData( $data )
	{

		$data['id']=$this->getCurrentProjectId();

		if ( !$this->isCurrentUserSysAdmin() )
		{
			if ( isset($data['sys_name'])) unset($data['sys_name']);
			if ( isset($data['sys_description'])) unset($data['sys_description']);
		}

		if ( isset($data['sys_name']) )
		{
            $p=$this->models->Projects->_get(array('id' => array(
				'id !=' => $data['id'],
				'sys_name'=>$data['sys_name']),
            ));

			if ($p)
			{
				$this->addError( sprintf( $this->translate('A project with the internal name "%s" already exists.'), $data['sys_name'] ) );
				unset($data['sys_name']);
			}
		}

		if ( isset($data['short_name']) )
		{
            $p = $this->models->Projects->_get(array(
                'id' => array('id !=' => $data['id'],'short_name'=>$data['short_name']),
            ));

			if ($p)
			{
				$this->addError( sprintf( $this->translate('A project with the shortname "%s" already exists (project: %s).'), $data['short_name'], $p[0]['sys_name'] ) );
				unset($data['short_name']);
			}
		}

		$this->models->Projects->save( $data );

	}

	private function getCurrentUserProjects()
	{
		$d=array( 'user_id'=>$this->getCurrentUserId() );

		if ( $this->UserRights->isSysAdmin() )
		{
			$d['show_all']=true;
		}

		return $this->models->ProjectsModel->getUserProjects( $d );
	}

	private function isCurrentUserAuthorizedForProject($id)
	{
		if ( $this->UserRights->isSysAdmin() ) return true;

		foreach ((array) $this->getCurrentUserProjects() as $key => $val)
		{
			if ($val['id'] == $id && $val['user_project_active'] == '1')
				return true;
		}

		return false;
	}

    private function setCurrentProjectId($id)
    {
        $_SESSION['admin']['project']['id'] = $id;

        $this->models->ProjectsRolesUsers->update(array(
            'last_project_select' => 'now()',
            'project_selects' => 'project_selects+1'
        ), array(
            'user_id' => $this->getCurrentUserId(),
            'project_id' => $this->getCurrentProjectId()
        ));
    }

	private function doSetProject( $id )
	{
		$this->unsetProjectSessionData();
		$this->setCurrentProjectId( $id );
		$this->setCurrentProjectData();
		//$this->setCurrentUserRoleId();
	}

    private function checkDefaultProjectSelect()
    {
		$projects=$this->getCurrentUserProjects();

		if ( $this->UserRights->isSysAdmin() && count((array)$projects)>1)
			return;

		$p=null;

		foreach((array)$projects as $key=>$val)
		{
			if ( $val['user_project_active']==1 )
			{
				if ( is_null($p) )
				{
					$p=$val['id'];
				}
				else
				{
					$p=false;
				}
			}
		}

		if ( $p && $this->isCurrentUserAuthorizedForProject( $p ) )
		{
			$this->doSetProject( $p );
			$this->redirect( $this->getLoggedInMainIndex() );
		}
    }

    private function setCurrentProjectData ($data = null)
    {
        if ($data == null)
		{
            $id = $this->getCurrentProjectId();

            if (isset($id))
			{
                $data = $this->models->Projects->_get(array(
                    'id' => $id
                ));

                $pru = $this->models->ProjectsRolesUsers->_get(
                array(
                    'id' => array(
                        'project_id' => $id,
                        'role_id' => ID_ROLE_LEAD_EXPERT
                    ),
                    'columns' => 'user_id'
                ));

                foreach ((array) $pru as $key => $val)
				{
                    $u = $this->models->Users->_get(
                    array(
                        'id' => array(
                            'id' => $val['user_id'],
                            'active' => 1
                        )
                    ));

                    $pru[$key]['first_name'] = $u[0]['first_name'];
                    $pru[$key]['last_name'] = $u[0]['last_name'];
                    $pru[$key]['email_address'] = $u[0]['email_address'];
                }

                $_SESSION['admin']['project']['lead_experts'] = $pru;
            }
        }

        foreach ((array) $data as $key => $val)
		{
            $_SESSION['admin']['project'][$key] = $val;
        }
    }








}