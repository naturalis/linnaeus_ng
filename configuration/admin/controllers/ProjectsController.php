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

	// REFAC2015 --> most need to go to module settings
	private $_availableProjectSettings = array(
		array('keytype','l2 / lng','lng','single access-key type'),
		array('maptype','l2 / lng','l2','map type: L2 or Google'),
		array('matrixtype','lng / nbc','nbc','multi entry-key type'),
		array('taxa_use_variations','bool',0),
		array('taxon_tree_type','[recursive*|unfolding]','unfolding'),
		array('taxon_page_ext_classification','bool',1),
		array('skin','[name]','nbc_default'),
		array('skin_mobile','[name]','nbc_default_mobile'),
		//array('suppress_splash','bool',0,'bypass splash screen'),
		array('start_page','[url]',null,'default start page of project'),
		array('nbc_image_root','[url]',null,'for system images'),

		//array('matrix_use_character_groups','bool',0),
		//array('matrix_browse_style','paginate / expand','expand'),
		//array('matrix_items_per_line','int',4),
		//array('matrix_items_per_page','int',16),
		//array('matrix_state_image_per_row','int',4,'number of images on a line in NBC character pop up'),
		//array('matrix_state_image_max_height','[size in px]',200), // should be done through project specific stylesheet
		//array('matrix_use_sc_as_weight','bool',0,'use separation coefficient as character weight; experimental'),
		//array('matrix_use_emerging_characters','bool',0,'treat characters that are specified for only some species as "emerging" (default=true)'),  // nbc only
		//array('matrix_allow_empty_species','bool',1,'make species without content available in matrix'),
		//array('matrix_calc_char_h_val','bool',0,'false: do not calculate characters H-value; enhances performance'),
		//array('matrix_suppress_details','bool',0,'never retrieve characterstates for displaying; enhances performance'),

		array('external_species_url_target','[_self|_blank*|name]','_blank','in NBC-style matrices'),
		array('external_species_url_prefix','[url]','%MEDIA_DIR%','in NBC-style matrices'),

		array('species_default_tab','[id]',-1,'id of the tab ("page") to open with by default'),
		array('species_tab_translate','{a:b},{c:d}',null,'automatically changes tab id a into b etc.'), // to change TAB_MEDIA to default category 'media' etc
		array('species_suppress_autotab_names','bool',1,'suppress automatically generated tab "names" in runtime'),
		array('species_suppress_autotab_classification','bool',1,'suppress automatically generated tab "classification" in runtime'),
		array('species_suppress_autotab_literature','bool',1,'suppress automatically generated tab "literature" in runtime'),
		array('species_suppress_autotab_media','bool',1,'suppress automatically generated tab "media" in runtime'),
		array('species_suppress_autotab_dna_barcodes','bool',1,'suppress automatically generated tab "dna_barcodes" in runtime (and in extensive search)'),

		array('literature2_import_match_threshold','int',75),

		array('include_overview_in_media','bool',0,'show overview image in species media'),
		array('app_search_result_sort','alpha / token_count','token_count','variable to sort search results by'),
		array('admin_species_allow_embedded_images','bool',1,'id.'),
		array('nbc_search_presence_help_url','[URL]',null,'id.'),



	);

/*
		array('matrixtype','lng / nbc','nbc','multi entry-key type'),
			should use
		if (!defined('MATRIX_STYLE_LNG')) define('MATRIX_STYLE_LNG','lng');
		if (!defined('MATRIX_STYLE_NBC')) define('MATRIX_STYLE_NBC','nbc');

*/


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();

		$this->initialize();

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
	* List of available modules, standard and self-defined, plus possibility of (de)activation
	*
	* @access     public
	*/
    public function modulesAction ()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Project modules'));

        if ($this->rHasVal('module_new')) {

            $fmp = $this->models->FreeModulesProjects->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                )
            ));

            if (count((array) $fmp) < $this->controllerSettings['freeModulesMax'] && !$this->isFormResubmit()) {

                $this->models->FreeModulesProjects->save(
                array(
                    'id' => null,
                    'module' => $this->rGetVal('module_new'),
                    'project_id' => $this->getCurrentProjectId(),
                    'active' => 'n'
                ));
            }
            else {

                $this->addError(sprintf($this->translate('There is a maximum of %s self-defined modules.'), $this->controllerSettings['freeModulesMax']));
            }
        }

        $modules = $this->models->Modules->_get(array(
            'id' => array(
                '1' => '1'
            ),
            'order' => 'show_order'
        ));

        foreach ((array) $modules as $key => $val) {

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

        $modules = $this->models->ModulesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'module_id asc'
        ));

        foreach ((array) $modules as $key => $val) {

            $mp = $this->models->Modules->_get(array(
                'id' => $val['module_id']
            ));

            $modules[$key]['module'] = $mp['module'];

            $modules[$key]['description'] = $mp['description'];

            $mpu = $this->models->ModulesProjectsUsers->_get(
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

        $free_modules = $this->models->FreeModulesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        foreach ((array) $free_modules as $key => $val) {

            $fpu = $this->models->FreeModulesProjectsUsers->_get(
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

        $pru = $this->models->ProjectsRolesUsers->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'distinct user_id, role_id'
        ));

        foreach ((array) $pru as $key => $val) {

            $u = $this->models->Users->_get(array(
                'id' => $val['user_id']
            ));

            $r = $this->models->Roles->_get(array(
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

        if (!is_null($this->rGetId()) && !$this->isFormResubmit()) {
            // saving all data (except the logo image)

			$this->saveProjectData($this->rGetId());
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

    public function settingsAction ()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Project settings'));

        if ($this->rHasVal('action','save') && !$this->isFormResubmit()) {

			$c=0;

			if ($this->rHasVar('setting')) {

				foreach((array)$this->rGetVal('setting') as $key => $val) {

					$val = trim($val);

					if (empty($val) && $val!=='0') {

						$c += $this->saveSetting(array('name' => $key,'delete' => true));

					} else {

						$c += $this->saveSetting(array('name' => $key,'value' => $val));

					}

				}

			}

			if ($this->rHasVal('new_setting')) {

				$v = $this->getSetting($this->rGetVal('new_setting'));

				if (is_null($v)) {

					if ($this->rHasVal('new_setting') && !$this->rHasVal('new_value')) {
						$this->addError(sprintf($this->translate('A value is required for "%s".'),$this->rGetVal('new_setting')));
						$this->smarty->assign('new_setting',$this->rGetVal('new_setting'));
					} else
					if ($this->rHasVal('new_setting') && $this->rHasVal('new_value')) {

						$c += $this->saveSetting(array('name' => $this->rGetVal('new_setting'),'value' => $this->rGetVal('new_value')));

					}

				} else {

					$this->addError(sprintf($this->translate('A setting with the name "%s" already exists.'),$this->rGetVal('new_setting')));
					$this->smarty->assign('new_setting',$this->rGetVal('new_setting'));
					$this->smarty->assign('new_value',$this->rGetVal('new_value'));

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


        $this->smarty->assign('settingsAvailable',$this->_availableProjectSettings);
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

            $this->ajaxActionModules($this->rGetVal('type'), $this->rGetVal('action'), $this->rGetId());
        }
        else if ($this->rHasVal('view', 'collaborators')) {

            $this->ajaxActionCollaborators($this->rGetVal('type'), $this->rGetVal('action'), $this->rGetId(), $this->rGetVal('user'));
        }
        else if ($this->rHasVal('view', 'languages')) {

            $this->ajaxActionLanguages($this->rGetVal('action'), $this->rGetId());
        }

        $this->printPage();
    }

    public function createAction ()
    {
        $this->checkAuthorisation(true);

        $this->setPageName($this->translate('Create new project'));

        $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setSuppressProjectInBreadcrumbs();

        if (!is_null($this->rGetId()) && !$this->isFormResubmit()) {

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
                    'title' => $this->rGetVal('title'),
                    'version' => !is_null($this->rGetVal('version')) ? $this->rGetVal('version') : null,
                    'sys_description' => $this->rGetVal('sys_description')
                ));

                if ($id) {

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
                else {

                    $this->addError($this->translate('Could not save project (duplicate name?).'));
                }
            }
        }

        if (!is_null($this->rGetId()))
            $this->smarty->assign('data', $this->rGetAll());

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

            $this->doDeleteProjectAction($this->rGetId());

            $this->reInitUserRolesAndRights();

            $this->addMessage('Project deleted.');
        }
        else {

            $d = $this->rHasVal('p') ? array(
                'id' => $this->rGetVal('p')
            ) : '*';

            $projects = $this->models->Projects->_get(array(
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

			$this->doDeleteOrphanedData();

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
        $this->models->Commonnames->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('contentkeystep',
        $this->models->ContentKeysteps->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('contenttaxon',
        $this->models->ContentTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('freemodulepage',
        $this->models->FreeModulesPages->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('freemoduleproject',
        $this->models->FreeModulesProjects->_get(array(
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

        $this->smarty->assign('matrix',
        $this->models->Matrices->_get(array(
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
        $this->models->OccurrencesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('synonym',
        $this->models->Synonyms->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));

        $this->smarty->assign('taxon',
        $this->models->Taxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        )));


        $this->smarty->assign('variations',
        $this->models->TaxaVariations->_get(array(
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
					$pDel->deleteMatrices($this->getCurrentProjectId());
					$pDel->deleteNBCKeydata($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_DISTRIBUTION) {
			        $pDel->deleteGeoData($this->getCurrentProjectId());
				} else
				if ($moduleId==MODCODE_CONTENT) {
			        $pDel->deleteProjectContent($this->getCurrentProjectId());
				}

                $this->models->ModulesProjectsUsers->delete(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'module_id' => $moduleId
                ));

                $this->models->ModulesProjects->delete(array(
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

                        $this->models->FreeModulesProjectsUsers->save(
                        array(
                            'id' => null,
                            'project_id' => $this->getCurrentProjectId(),
                            'free_module_id' => $moduleId,
                            'user_id' => $val[0]
                        ));
                    }
                }
                else {

                    $this->models->FreeModulesProjectsUsers->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'free_module_id' => $moduleId,
                        'user_id' => $userId
                    ));
                }
            }
            else if ($action == 'remove') {

                $this->models->FreeModulesProjectsUsers->delete(
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

                        $this->models->ModulesProjectsUsers->save(
                        array(
                            'id' => null,
                            'project_id' => $this->getCurrentProjectId(),
                            'module_id' => $moduleId,
                            'user_id' => $val[0]
                        ));
                    }
                }
                else {

                    $this->models->ModulesProjectsUsers->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getCurrentProjectId(),
                        'module_id' => $moduleId,
                        'user_id' => $userId
                    ));
                }
            }
            else if ($action == 'remove') {

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
        if ($action == 'add') {

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
        elseif ($action == 'default') {

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
        elseif ($action == 'deactivate' || $action == 'reactivate') {

            $this->models->LanguagesProjects->update(array(
                'active' => ($action == 'deactivate' ? 'n' : 'y')
            ), array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'delete') {

            $this->models->ContentTaxa->delete(array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));

            $this->models->LanguagesProjects->delete(array(
                'language_id' => $languageId,
                'project_id' => $this->getCurrentProjectId()
            ));
        }
        elseif ($action == 'translated' || $action == 'untranslated') {

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

		foreach((array)$data as $val) {

			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			// all user tables, infrastructural tables and the project table itself lack a project_id column.
			$d = $this->models->Projects->freeQuery('select distinct project_id from '.$table);

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

		$d = $this->models->Projects->_get(array('id' => '*'));

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

		$data = $this->models->Projects->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Projects->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val) {

			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			$d = $this->models->Projects->freeQuery('select count(*) as total from '.$table.' where project_id = '.$oldId);

			if ($d[0]['total']>0) {

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

	private function saveProjectData($data)
	{

		$data['id'] = $this->getCurrentProjectId();

		if (!$this->isCurrentUserSysAdmin() && isset($data['sys_name'])) {
			unset($this->requestData['sys_name']);
		}

		$p = $this->models->Projects->update(
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

            $p = $this->models->Projects->_get(array(
                'id' => array('id !=' => $data['id'],'sys_name'=>$data['sys_name']),
            ));

			if ($p) {

				$this->addError('A project with that internal name alreasy exists.');
				unset($this->requestData['sys_name']);

			}

		}

		if (isset($data['short_name'])) {

            $p = $this->models->Projects->_get(array(
                'id' => array('id !=' => $data['id'],'short_name'=>$data['short_name']),
            ));

			if ($p) {

				$this->addError(sprintf('A project with that shortname already exists (%s).',$p[0]['sys_name']));
				unset($this->requestData['short_name']);

			}

		}

		$this->models->Projects->save($data);

	}

	private function initialize()
	{

		foreach((array)$this->_availableProjectSettings as $key=>$val) {

			$this->_availableProjectSettings[$key][2] =
				preg_replace_callback(
					'/\%(.*)\%/',
					function($m)
					{
						switch ($m[1]) {
							case 'MEDIA_DIR' :
								return isset($_SESSION['admin']['project']['urls']['project_media']) ? $_SESSION['admin']['project']['urls']['project_media'] : $m[1];
								break;
							case 'ID' :
								return isset($_SESSION['admin']['project']['id']) ? $_SESSION['admin']['project']['id'] : $m[1];
								break;
							default:
								return $m[1];
						}
					},
					$val[2]);
	    }

	}

}
