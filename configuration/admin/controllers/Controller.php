<?php

include_once (dirname(__FILE__) . "/../BaseClass.php");
include_once (dirname(__FILE__) . "/../../../smarty/Smarty.class.php");


class Controller extends BaseClass
{
	private $_smartySettings;
	private $_viewName;
	private $_fullPath;
	private $_fullPathRelative;
	private $_prevTreeId = null;
	private $_breadcrumbRootName = null;
	private $translator;
	public $useVariations = false;
	public $useRelated = false;
	public $tmp;
	public $smarty;
	public $requestData;
	public $requestDataFiles;
	public $data;
	public $randomValue;
	public $breadcrumbIncludeReferer;

	public $errors=array();
	public $messages=array();
	public $warnings=array();

	public $controllerBaseName;
	public $controllerBaseNameMask = false;
	public $pageName;
	public $controllerPublicName;
	public $controllerPublicNameMask = false;
	public $sortField;
	public $sortDirection;
	public $sortCaseSensitivity;
	public $findField;
	public $findValue;
	public $baseUrl;
	public $excludeFromReferer = false;
	public $noResubmitvalReset = false;
	public $isMultiLingual = true;
	public $uiLanguages;
	public $uiDefaultLanguage;
	public $treeList;
	public $suppressProjectInBreadcrumbs;
	public $includeLocalMenu = true;
	public $printBreadcrumbs = true;

	private $usedModelsBase = array(
		'activity_log',
		'free_modules_projects',
		'free_modules_projects_users',
		'interface_texts',
		'interface_translations',
		'labels_projects_ranks',
		'languages',
		'languages_projects',
		'modules',
		'modules_projects',
		'modules_projects_users',
		'projects',
		'projects_ranks',
		'projects_roles_users',
		'ranks',
		'rights',
		'rights_roles',
		'roles',
		'taxa',
		'taxa_variations',
		'users_taxa',
		'users',
		'variations_labels'
	);

    private $usedHelpersBase = array(
		'session_module_settings',
        'logging_helper',
        'email_helper',
		'log_changes',
		'custom_array_sort',
		'paginator'
    );

	protected $moduleSession;
	protected $baseSession;


    /**
     * Constructor, calls parent's constructor and all initialisation functions
     *
     * The order in which the functions are called is relevant! Do not change without good reason and plan.
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();


        $this->setTimeZone();

        $this->setDebugMode();

        $this->startSession();

        $this->loadHelpers();

        $this->initLogging();

        $this->setNames();

        $this->startModuleSession();

        $this->loadControllerConfig();

        $this->loadSmartyConfig();

        $this->checkWriteableDirectories();

        $this->setPaths();

        $this->setUrls();

        $this->loadModels();

        $this->setRandomValue();

        $this->setLanguages();

        $this->checkLastVisitedPage();

        $this->setSmartySettings();

        $this->setRequestData();

        $this->doLanguageChange();

        $this->checkModuleActivationStatus();

        $this->setProjectLanguages();

		$this->initTranslator();

    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        $this->setLastVisitedPage();

        $this->saveFormResubmitVal();

        session_write_close();

        parent::__destruct();
    }

    /**
     * Returns the application name
     *
     * @return     string    application name
     * @access     public
     */
    public function getAppName ()
    {
        return isset($this->appName) ? $this->appName : false;
    }

    /**
     * Returns the controller's base name
     *
     * @return     string    controller's base name
     * @access     public
     */
    public function getControllerBaseName ()
    {
        return $this->controllerBaseName;
    }

    /**
     * Returns the current view's name
     *
     * @return     string    current view's name
     * @access     public
     */
    public function getViewName ()
    {
        return $this->_viewName;
    }

    /**
     * Renders and displays the page
     *
     * @access     public
     */
    public function printPage ($templateName = null)
    {
        $this->preparePage();

        $this->smarty->display(strtolower((!empty($templateName) ? $templateName : $this->getViewName()) . '.tpl'));
    }

    /**
     * Renders and returns the page
     *
     * @access     public
     */
    public function fetchPage ($templateName = null)
    {
        $this->preparePage();

        return $this->smarty->fetch(strtolower((!empty($templateName) ? $templateName : $this->getViewName()) . '.tpl'));
    }

    /**
     * Redirects the user to another page (and avoids circular redirection)
     *
     * @param      string    $url    url to redirect to; can be false, in which case HTTP_REFERER is used
     * @access     public
     */
    public function redirect ($url = false)
    {
        if (!$url && isset($_SERVER['HTTP_REFERER']))
		{
            $url = $_SERVER['HTTP_REFERER'];
        }

        if (basename($url) == $url)
		{
            $circular = (basename($this->_fullPath) == $url);
        }
        else
		{
            $circular = ($this->_fullPath == $url) || ($this->_fullPathRelative == $url);
        }

        if ($url && !$circular)
		{
            header('Location:' . $url);
            die();
        }
    }

    /**
     * Adds an error to the class's stack of errors stored in class variable 'errors'
     *
     * @param      string or array    $error    the error(s)
     * @access     public
     */
    public function addError ($error, $writeToLog = false)
    {
		if (empty($error)) return;

        if (!is_array($error))
		{
            $this->errors[] = $error;

            if ($writeToLog !== false)
                $this->log($error, $writeToLog);
        }
        else
		{
            foreach ($error as $key => $val)
			{
                $this->errors[] = $val;
                if ($writeToLog !== false) $this->log($val, $writeToLog);
            }
        }
    }

    /**
     * Returns whether there are errors or not
     *
     * @return     boolean    errors or not
     * @access     public
     */
    public function hasErrors ()
    {
        return (count((array) $this->errors) > 0);
    }

    /**
     * Returns the class's stack of errors stored in class variable 'errors'
     *
     * @return     array    stack of errors
     * @access     public
     */
    public function getErrors ()
    {
        return $this->errors;
    }

    public function clearErrors ()
    {
        $this->errors = array();
    }

    /**
     * Adds a message to the class's stack of messages stored in class variable 'messages'
     *
     * @param      type    $message    the message
     * @access     public
     */
    public function addMessage ($d)
    {
		if (empty($d)) return;

		if (is_array($d))
		{
			$this->messages=array_merge($this->messages,$d);
		}
		else
		{
	        $this->messages[]=$d;
		}
    }

    public function getMessages ()
    {
        return $this->messages;
    }


    public function addWarning($d)
    {
		if (empty($d)) return;

		if (is_array($d))
		{
			$this->warnings=array_merge($this->warnings,$d);
		}
		else
		{
	        $this->warnings[]=$d;
		}
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function hasWarnings ()
    {
        return (count((array) $this->warnings) > 0);
    }




    /**
     * Sets the name of the current page, for display purposes, in a class variable 'pageName'.
     *
     * @param      string    $name    the page's name
     * @access     public
     */
    public function setPageName ($name)
    {
        $this->pageName = $name;
    }



    /**
     * Returns the name of the current page.
     *
     * @return     string    the page's name
     * @access     public
     */
    public function getPageName ()
    {
        return $this->pageName;
    }



    /**
     * Returns the current user's id class variable
     *
     * @return     integer    user id
     * @access     public
     */
    public function getCurrentUserId()
    {
        return isset($_SESSION['admin']['user']['id']) ? $_SESSION['admin']['user']['id'] : null;
    }



    /**
     * Returns the projects the current user has been assigned to
     *
     * @return     array    array of project's id's, names and user's active states
     * @access     public
     */
    public function getCurrentUserProjects()
    {

		$this->reInitUserRolesAndRights();

		$p = $this->models->Projects->_get(array('id'=>'*','fieldAsIndex'=>'id'));

        foreach ((array) $_SESSION['admin']['user']['_roles'] as $key => $val)
		{
            $r = array(
                'id' => $val['project_id'],
                'name' => $val['project_name'],
                'title' => $val['project_title'],
                'active' => $val['active'],
                'member' => $val['member'],
				'published' => isset($p[$val['project_id']]['published']) ? $p[$val['project_id']]['published']=='1' : false
            );

            if (!isset($cup) || !in_array($r, (array) $cup))
			{
                $cup[$val['project_id']] = $r;
            }
        }

        $this->customSortArray($cup, array(
            'key' => 'title',
            'dir' => 'asc',
            'case' => 'i'
        ));

        return isset($cup) ? $cup : false;
    }



    /**
     * Sets the active project's id as class variable
     *
     * @param      integer    $id    new active project's id
     * @access     public
     */
    public function setCurrentProjectId($id)
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



    /**
     * Returns the active project's id class variable
     *
     * @return     integer    active project's id
     * @access     public
     */
    public function getCurrentProjectId()
    {
        return isset($_SESSION['admin']['project']['id']) ? $_SESSION['admin']['project']['id'] : null;
    }



    /**
     * Sets the active project's data as a session variable
     *
     * @access     public
     */
    public function setCurrentProjectData ($data = null)
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



    /**
     * Rerturns the active project's data
     *
     * @access     public
     */
    public function getCurrentProjectData ()
    {
        return isset($_SESSION['admin']['project']) ? $_SESSION['admin']['project'] : null;
    }



    /**
     * Sets the default project for the current user
     *
     * After logging in, the app requires an active project is set, the project the user actually works on.
     * If the user is assigned to several projects, a choice of project is required; if he's assigned to only one,
     * the choice should be automatic. This function decides what project should be the active one, and sets it.
     *
     * @access     public
     */
    public function setDefaultProject ()
    {
        foreach ((array) $_SESSION['admin']['user']['_roles'] as $key => $val)
		{
            if ($val['active'] == '1') $d[] = $val;
        }

        // if user has no roles, do nothing
        if (!isset($d)) return;

		// if user has only one role, set the corresponding project as the active project
        if (count($d) == 1)
		{
            $this->setCurrentProjectId($d[0]['project_id']);
        }
        else
		{
            // if user has more than one project assigned, he has to choose himself
            return;
        }

        $this->setCurrentProjectData();
    }



    /**
     * Retrieves all rights and roles of the current user. Results are stored in the user's session.
     *
     * @return     array    array of roles, rights and the number of projects the user is involved with
     * @access     public
     */
    public function getUserRights ($id = false)
    {
        $pru = $this->models->ProjectsRolesUsers->_get(
        array(
            'id' => array(
                'user_id' => $id ? $id : $this->getCurrentUserId()
            ),
            'columns' => 'project_id,role_id,active,\'1\' as member',
            'fieldAsIndex' => 'project_id'
        ));


        if ($this->isCurrentUserSysAdmin())
		{
            $p = $this->models->Projects->_get(array(
                'id' => '*'
            ));

            foreach ((array) $p as $val)
			{
                if (!isset($pru[$val['id']]))
				{
                    $pru[$val['id']] = array(
                        'project_id' => $val['id'],
                        'role_id' => (string) ID_ROLE_SYS_ADMIN,
                        'active' => (string) 1,
                        'member' => 0
                    );
                }
            }
        }

        foreach ((array) $pru as $key => $val)
		{
            $p = $this->models->Projects->_get(array(
                'id' => $val['project_id']
            ));

            // $val['project_id']==0 is the stub for all round system admin
            if ($p || $val['project_id'] == 0)
			{
                $r = $this->models->Roles->_get(array(
                    'id' => $val['role_id']
                ));

                if ($r) {

                    $userProjectRoles[] = array_merge($val,
                    array(
                        'project_name' => $p['sys_name'],
                        'project_title' => $p['title'],
                        'role_name' => $r['role'],
                        'role_description' => $r['description']
                    ));

                    $rr = $this->models->RightsRoles->_get(array(
                        'id' => array(
                            'role_id' => $val['role_id']
                        )
                    ));

                    foreach ((array) $rr as $rr_key => $rr_val)
					{
                        $r = $this->models->Rights->_get(array(
                            'id' => $rr_val['right_id']
                        ));

                        $rs[$val['project_id']][$r['controller']][$r['id']] = $r['view'];
                    }

                    $projectCount[$val['project_id']] = $val['project_id'];
                }
            }
        }


        $fmpu = $this->models->FreeModulesProjectsUsers->_get(array(
            'id' => array(
                'user_id' => $id ? $id : $this->getCurrentUserId()
            )
        ));

        foreach ((array) $fmpu as $key => $val)
		{
            $rs[$val['project_id']]['_freeModules'][$val['free_module_id']] = true;
        }

        $d = $this->getCurrentProjectId();

        return array(
            'roles' => isset($userProjectRoles) ? $userProjectRoles : null,
            'rights' => isset($rs) ? $rs : null,
            'number_of_projects' => isset($projectCount) ? count((array) $projectCount) : 0
        );
    }



    /**
     * Sets the user session var that holds the rights per view per project array
     *
     * @access     public
     */
    public function setUserSessionRights($rights)
    {
        $_SESSION['admin']['user']['_rights'] = $rights;
    }



    /**
     * Sets the user session var that holds the roles per project array
     *
     * @access     public
     */
    public function setUserSessionRoles($roles)
    {
        $_SESSION['admin']['user']['_roles'] = $roles;
    }



    /**
     * Sets the user session var that describes the number of projects the user has been assigned to
     *
     * @access     public
     */
    public function setUserSessionNumberOfProjects($numberOfProjects)
    {
        $_SESSION['admin']['user']['_number_of_projects'] = $numberOfProjects;
    }



    public function setCurrentUserRoleId()
    {
        if (!isset($_SESSION['admin']['user']['_roles']))
		{
            $_SESSION['admin']['user']['currentRole'] = null;
        }
        else
		{
            $d = $this->getCurrentProjectId();

            if (is_null($d))
			{
                $_SESSION['admin']['user']['currentRole'] = null;
            }
            else
			{
                foreach ((array) $_SESSION['admin']['user']['_roles'] as $val)
				{
                    if ($val['project_id'] == $d)
                        $_SESSION['admin']['user']['currentRole'] = $val["role_id"];
                }
            }
        }
    }

    public function getCurrentUserRoleId()
    {
        return isset($_SESSION['admin']['user']['currentRole']) ? $_SESSION['admin']['user']['currentRole'] : null;
    }

    public function getCurrentUserIsSuperuser ()
    {
        return $_SESSION['admin']['user']['superuser'] == 1;
    }


    /**
     * Checks whether a user is logged in
     *
     * @return     boolean        logged in or not
     * @access     public
     */
    public function isUserLoggedIn()
    {
        return (!empty($_SESSION['admin']['user']['id']));
    }



    /**
     * Checks whether a user is authorized to view/use a certain page and redirects if necessary
     *
     * Subsequently checks:
     * Is the user logged in?
     * Has the user selected an active project?
     * Is the user authorized to see a specific page?
     *
     * @return     boolean        returns true if authorized, or redirects if not
     * @access     public
     */
    public function checkAuthorisation($allowNoProjectId = false)
    {
        // check if user is logged in, otherwise redirect to login page
        if ($this->isUserLoggedIn())
		{
            // check if there is an active project, otherwise redirect to choose project page
            if ($this->getCurrentProjectId() || $allowNoProjectId)
			{
                // check if the user is authorised for the combination of current page / current project
                if ($this->isUserAuthorisedForProjectPage() || $this->isCurrentUserSysAdmin())
				{
                    return true;
                }
                else
				{
                    $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);

                    /*
						user is not authorized and redirected to the index.page;
						if he already *is* on the index.page (and not authorized to be there),
						he is logged out to avoid circular reference.
					*/
                    if ($this->getViewName() == 'Index')
					{
                        $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['logout']);
                    }
                    else
					{
                        $this->redirect('index.php');
                    }
                }
            }
            else
			{
                $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject']);
            }
        }
        else
		{
            $this->setLoginStartPage();
            $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['login']);
        }
    }



    /**
     * Judges whether the user is authorized to work at a specific project
     *
     * @param      integer    $id    project id
     * @return     boolean    is or is not authorized
     * @access     public
     */
    public function isCurrentUserAuthorizedForProject ($id)
    {
        foreach ((array) $this->getCurrentUserProjects() as $key => $val)
		{
            if ($val['id'] == $id && $val['active'] == '1')
                return true;
        }

        return false;
    }



    /**
     * Returns whether the active user is a general sysadmin (i.e., sysadmin rights for project 0)
     *
     * @return     boolean	true or false
     * @access     public
     */
    public function isCurrentUserSysAdmin ()
    {
        if (!isset($_SESSION['admin']['user']))
		{
            $u = $this->models->Users->_get(array(
                'id' => $this->getCurrentUserId()
            ));

            return $u['superuser'] == '1';
        }

        if ($_SESSION['admin']['user']['superuser'] == 1)
            return true;

        if (!isset($_SESSION['admin']['user']['_roles']))
            return false;

        foreach ((array) $_SESSION['admin']['user']['_roles'] as $key => $val)
		{
            if ($val['project_id'] == 0 && $val['role_id'] == ID_ROLE_SYS_ADMIN)
                return true;
        }

        return false;
    }



    public function isCurrentUserLeadExpert ()
    {
        if (!isset($_SESSION['admin']['user']['_roles']))
            return false;

        foreach ((array) $_SESSION['admin']['user']['_roles'] as $key => $val) {

            if ($val['project_id'] == $this->getCurrentProjectId() && $val['role_id'] == ID_ROLE_LEAD_EXPERT)
                return true;
        }

        return false;
    }



    /**
     * Re-initialises the user's projects and roles, without loggin out and in
     *
     * @access     public
     */
    public function reInitUserRolesAndRights ($userId = null)
    {
        $cur = $this->getUserRights(isset($userId) ? $userId : $this->getCurrentUserId());
        $this->setUserSessionRights($cur['rights']);
        $this->setUserSessionRoles($cur['roles']);
        $this->setUserSessionNumberOfProjects($cur['number_of_projects']);
    }



    public function getProjectModules ($params = null)
    {

		$d['project_id'] = isset($params['project_id']) ? $params['project_id'] : $this->getCurrentProjectId();

		if (isset($params['active']) && ($params['active'] == 'y' || $params['active'] == 'n'))
			$d['active'] = $params['active'];

		$p['id'] = $d;

		if (isset($params['order']))
			$p['order'] = $params['order'];

		if (isset($params['ignore']))
			$p['ignore'] = $params['ignore'];

		$modules = $this->models->ModulesProjects->_get($p);

		foreach ((array) $modules as $key => $val)
		{
			if (isset($p['ignore']) && in_array($val['module_id'],(array)$p['ignore'])) continue;

			$mp = $this->models->Modules->_get(array(
				'id' => $val['module_id']
			));

			$modules[$key]['module'] = $mp['module'];
			$modules[$key]['description'] = $mp['description'];
			$modules[$key]['controller'] = $mp['controller'];
			$modules[$key]['show_order'] = $mp['show_order'];
		}

		$this->customSortArray($modules, array(
			'key' => 'show_order',
			'maintainKeys' => true
		));

		$freeModules = $this->models->FreeModulesProjects->_get(array(
			'id' => array(
				'project_id' => $d['project_id']
			)
		));

		return array(
			'modules' => $modules,
			'freeModules' => $freeModules
		);

    }



    public function checkModuleActivationStatus ()
    {
        if ($this->getModuleActivationStatus() == -1) {

            $_SESSION['admin']['system']['last_module_name'] = $this->controllerPublicName;

            $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['moduleNotPresent']);
        }
    }



    /**
     * Returns the default save path for file uploads
     *
     * @return string    path
     * @access     public
     */
    public function getDefaultImageUploadDir ()
    {
        return sys_get_temp_dir();
        //return isset($_SESSION['admin']['project']['paths']['uploads_media']) ? $_SESSION['admin']['project']['paths']['uploads_media'] : null;
    }



    /**
     * Returns the default save path for project images
     *
     * @return string    path
     * @access     public
     */
    public function getProjectsMediaStorageDir ()
    {
		return $this->baseSession->getModuleSetting( 'project_media_path' );
    }



    /**
     * Returns the default save path for project thumbs
     *
     * @return string    path
     * @access     public
     */
    public function getProjectsThumbsStorageDir ()
    {
		return $this->baseSession->getModuleSetting( 'project_thumbs_path' );
    }



    /**
     * Returns the address of the root index for someone who is logged in
     *
     * @access     public
     * @ return    string    url
     */
    public function getLoggedInMainIndex ()
    {
        return $this->baseUrl . $this->appName . '/views/utilities/admin_index.php';
    }



    /**
	* Allows to addition of an extra step in the breadcrumb trail *before* the current page
	*
	*  example (called at the beginning of an *action() function):
    *
	*    $this->setBreadcrumbIncludeReferer(
    *      array(
    *         'name' => $this->translate('Name'),
    *         'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/url.php'
    *       )
    *     );
	*
	*  this would include " -> Name " in the trail as last crumb before the current page
	*
	* @param  	type	$varname	description
	* @return 	type	description
	*/
    public function setBreadcrumbIncludeReferer ($value = true)
    {
        $this->breadcrumbIncludeReferer=$value;
    }



    /**
     * Checks if a form submit is new or a resubmit (through user refresh)
     *
     * Add this line to the form in your template
     *        <input type="hidden" name="rnd" value="{$rnd}" />
     * and you can call
     *        $this->isFormResubmit()
     * in the receiving controller function to make sure whether the submit is a resubmit
     * of the same instance of the form (as when the user reloads a posted form)
     * the old value of last_rnd is set on destruct.
	 *
     * @return boolean    is resubmit or not
     * @access     public
     */
    public function isFormResubmit ()
    {
		return
			$this->rHasVal('rnd') &&
			null!==$this->baseSession->getModuleSetting( 'last_rnd' ) &&
			($this->baseSession->getModuleSetting( 'last_rnd' ) == $this->rGetVal('rnd'));
    }



    public function setExcludeFromReferer ($state)
    {
        $this->excludeFromReferer = $state;
    }



    public function setNoResubmitvalReset ($state)
    {
        $this->noResubmitvalReset = $state;
    }



    public function log ($msg, $severity = 0)
    {
        if (is_array($msg)) {

            $d = '';

            foreach ($msg as $key => $val) {

                $d .= $key . '=>' . $val . ', ';
            }

            $msg = trim($d, ' ,');
        }

        if (!@$this->helpers->LoggingHelper->write('(' . $this->getCurrentProjectId() . ') ' . $msg, $severity))
            trigger_error($this->translate('Logging not initialised'), E_USER_ERROR);
    }



    public function setLocale ($languageId)
    {
        if (isset($_SESSION['admin']['user']['currentLanguage']) && $languageId == $_SESSION['admin']['user']['currentLanguage'])
            return;

        $l = $this->models->Languages->_get(array(
            'id' => array(
                'id' => $languageId
            )
        ));

        if (count((array) $l) == 0) {

            $this->log('Tried to switch to illegal language', 1);

            return;
        }

        $_SESSION['admin']['user']['currentLanguage'] = $languageId;
    }

    /**
     * Perfoms a usort, using user defined sort by-field, sort direction and case-sensitivity
     *
     * @param array    $array    array to sort
     * @param array    $sortBy    array to array of key, direction, case-sensitivity and whether or not to maintain key-association
     * @access     public
     */
    public function customSortArray (&$array, $sortBy)
    {
		$this->helpers->CustomArraySort->setSortyBy( $sortBy );
		$this->helpers->CustomArraySort->sortArray( $array );
		$array=$this->helpers->CustomArraySort->getSortedArray();
    }


    public function getTaxonById ($id = false)
    {

        $id = $id===false ? ($this->rGetId() ? $this->rGetId() : null) : $id;

        if (empty($id) || $id == 0)
            return;

		$t = $this->models->Taxa->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $id
			),
			'columns' => 'id,taxon,author,parent_id,rank_id,taxon_order,is_hybrid,list_level'
		));


        if (empty($t))
            return;

		$t[0]['label'] = $this->formatTaxon($t[0]);

		$pr = $this->models->ProjectsRanks->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $t[0]['rank_id']
			)
		));

		$t[0]['lower_taxon'] = $pr[0]['lower_taxon'];
		$t[0]['base_rank'] = $pr[0]['rank_id'];

		return $t[0];


    }


    public function getTaxonByName($name)
    {
		$name=trim($name);
        if (empty($name))
            return;

		$t=$this->models->Taxa->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon' => $name
			)
		));
		return isset($t[0]) ? $t[0] : null;

    }

    /**
     * Catches and saves uploaded files
     *
     * @access     public
     */
    public function getUploadedFiles ($allowedMimeTypes = '*')
    {
        if (isset($this->helpers->FileUploadHelper) && isset($this->requestDataFiles)) {

            $this->helpers->FileUploadHelper->setLegalMimeTypes('*');
            $this->helpers->FileUploadHelper->setTempDir($this->getDefaultImageUploadDir());
            $this->helpers->FileUploadHelper->setStorageDir($this->getProjectsMediaStorageDir());
            $this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);

            $this->addError($this->helpers->FileUploadHelper->getErrors());

            return $this->helpers->FileUploadHelper->getResult();
        }
    }



    /**
     * Catches and saves uploaded files
     *
     * @access     public
     */
    public function getUploadedMediaFiles ($p=null)
    {

		$allowedFormats = isset($p['allowedFormats']) ? $p['allowedFormats'] : $this->controllerSettings['media']['allowedFormats'];
		$storageDir = isset($p['storageDir']) ? $p['storageDir'] : $this->getProjectsMediaStorageDir();
		$overwrite = isset($p['overwrite']) ? $p['overwrite'] : 'rename';

        if (isset($this->helpers->FileUploadHelper) && isset($allowedFormats) && isset($this->requestDataFiles)) {

            $this->helpers->FileUploadHelper->setLegalMimeTypes($allowedFormats);
            $this->helpers->FileUploadHelper->setTempDir(rtrim(sys_get_temp_dir(),'/').'/');
            $this->helpers->FileUploadHelper->setStorageDir($storageDir);
            $this->helpers->FileUploadHelper->setOverwrite($overwrite);
            $this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);

            $this->addError($this->helpers->FileUploadHelper->getErrors());

            return $this->helpers->FileUploadHelper->getResult();
        }
        else {

            return null;
        }
    }

    public function getPagination ($items, $maxPerPage = 25)
    {
        $this->helpers->Paginator->setItemsPerPage($maxPerPage);
		$this->helpers->Paginator->setStart($this->rHasVal('start') ? $this->rGetVal('start') : 0);
		$this->helpers->Paginator->setItems($items);
		$this->helpers->Paginator->paginate();

		return $this->helpers->Paginator->getItems();
    }

    public function sendEmail ($params)
    {

        /*
			params:

			mailto_address
			mailto_name
			mailfrom_address
			mailfrom_name
			subject
			plain
			html
			smtp_server
			debug (boolean; optional)
			mail_name (for identification in the log file; optional)

		*/
        $d = isset($params['mail_name']) ? ' "' . $params['mail_name'] . '"' : '';

        if (!isset($params['mailto_address']) || !isset($params['mailfrom_address']) || (!isset($params['plain']) && !isset($params['html'])) || !isset($params['smtp_server'])) {

            if (!isset($params['mailto_address']))
                $this->log('Can\'t send email' . $d . ': lacking rcpt address)', 1);
            if (!isset($params['mailfrom_address']))
                $this->log('Can\'t send email' . $d . ': lacking sender address)', 1);
            if (!isset($params['plain']) && !isset($params['html']))
                $this->log('Can\'t send email' . $d . ': lacking body)', 1);
            if (!isset($params['smtp_server']))
                $this->log('Can\'t send email' . $d . ': lacking server)', 1);

            return false;
        }

        $res = $this->helpers->EmailHelper->send(
        array(
            'mailto_address' => $params['mailto_address'],
            'mailto_name' => isset($params['mailto_name']) ? $params['mailto_name'] : null,
            'mailfrom_address' => $params['mailfrom_address'],
            'mailfrom_name' => isset($params['mailfrom_name']) ? $params['mailfrom_name'] : null,
            'subject' => $params['subject'],
            'plain' => isset($params['plain']) ? $params['plain'] : null,
            'html' => isset($params['html']) ? $params['html'] : null,
            'smtp_server' => $params['smtp_server'],
            'debug' => isset($params['debug']) ? $params['debug'] : false
        ));

        if (!$res)
            $this->log('Failed sending email' . $d, 1);

        return $res;
    }



    public function createProject ($d)
    {
        $d['id'] = null;
        $d['sys_name'] = $d['title'];// . (isset($d['version']) ? ' v' . $d['version'] : $d['version']);

        $p = $this->models->Projects->save($d);

        return ($p) ? $this->models->Projects->getNewId() : false;
    }



    public function addUserToProject ($uid, $pId, $roleId, $active = 1, $addToAllModules = true)
    {
        $this->models->ProjectsRolesUsers->save(array(
            'id' => null,
            'project_id' => $pId,
            'role_id' => $roleId,
            'user_id' => $uid,
            'active' => $active
        ));

        if ($addToAllModules === false)
            return;

        $d = array(
            'id' => null,
            'user_id' => $uid,
            'project_id' => $pId
        );

        $modules = $this->getProjectModules(array(
            'project_id' => $pId
        ));

        foreach ((array) $modules['modules'] as $key => $val) {

            $d['module_id'] = $val['module_id'];
            $this->models->ModulesProjectsUsers->save($d);
        }

        unset($d['module_id']);

        foreach ((array) $modules['freeModules'] as $key => $val) {

            $d['free_module_id'] = $val['id'];
            $this->models->FreeModulesProjectsUsers->save($d);
        }
    }



    public function makeLookupList($p)
    {

		$data=isset($p['data']) ? $p['data'] : null;
		$module=isset($p['module']) ? $p['module'] : null;
		$url=isset($p['url']) ? $p['url'] : null;
		$sortData=isset($p['sortData']) ? $p['sortData'] : false;
		$encode=isset($p['encode']) ? $p['encode'] : true;
		$isFullSet=isset($p['isFullSet']) ? $p['isFullSet'] : true;

        if ($sortData)
		{
			$sortBy = array(
				'key' => 'label',
				'dir' => 'asc',
				'case' => 'i'
			);

            $this->customSortArray($data, $sortBy);
		}

        $d = array(
            'module' => $module,
            'url' => $url,
            'results' => $data,
			'fullset'=> $isFullSet
        );

        return $encode ? json_encode($d) : $d;
    }



    public function cleanUpRichContent ($content)
    {
        return preg_replace('/<img[^>]+\>/i', '', $content);
    }



    public function unsetProjectSessionData ()
    {
        unset($_SESSION['admin']['system']);
        unset($_SESSION['admin']['project']);
        unset($_SESSION['admin']['glossary']);
        unset($_SESSION['admin']['literature']);
        unset($_SESSION['admin']['species']);
        unset($_SESSION['admin']['matrixkey']);
        unset($_SESSION['admin']['import']);
        unset($_SESSION['admin']['data']);
    }



    /*

		"new" functions below are replacements for the "taxon tree"-functions, with improved
		caching and - hopefully - performance.


	*/
    public function newGetProjectRanks ($p = null)
    {
        $includeLanguageLabels = isset($p['includeLanguageLabels']) ? $p['includeLanguageLabels'] : false;
        $pId = isset($p['pId']) ? $p['pId'] :  $this->getCurrentProjectId();

		$pr = $this->models->ControllerModel->getProjectRanks(array(
			'project_id'=>$pId,
			'language_id'=>$this->getDefaultProjectLanguage()
		));

		if ($includeLanguageLabels)
		{

			foreach ((array) $pr as $rankkey => $rank)
			{

				foreach ((array) $this->getProjectLanguages() as $langaugekey => $language)
				{

					$lpr = $this->models->LabelsProjectsRanks->_get(
						array(
							'id' => array(
								'project_id' => $pId,
								'project_rank_id' => $rank['id'],
								'language_id' => $language['language_id']
							),
							'columns' => 'label'
						));

					$pr[$rankkey]['labels'][$language['language_id']] = $lpr[0]['label'];
				}
			}
		}

        return $pr;
    }

	public function setActiveTaxonId($id=null)
	{
		if (is_null($id))
			unset($_SESSION['admin']['system']['activeTaxon']);
		else
			$_SESSION['admin']['system']['activeTaxon'] = $id;
	}

	public function getActiveTaxonId()
	{
		return isset($_SESSION['admin']['system']['activeTaxon']) ? $_SESSION['admin']['system']['activeTaxon'] : null;
	}

	public function logChange($p)
	{
		$changed=isset($p['changed']) ? $p['changed'] : true;
		$note=isset($p['note']) ? $p['note'] : null;
		$before=isset($p['before']) ? $p['before'] : null;
		$after=isset($p['after']) ? $p['after'] : null;
		$user=isset($p['user']) ? $p['user'] :
			(
				@$_SESSION['admin']['user']['first_name'].' '.
				@$_SESSION['admin']['user']['last_name'].' ('.
				@$_SESSION['admin']['user']['username'].' - '.
				@$_SESSION['admin']['user']['email_address'].')'
			);

		if ($changed!==true)
			return;

		if (!$this->models->ActivityLog->getTableExists())
			return;

		$this->models->ActivityLog->insert(
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$this->getCurrentUserId(),
				'user'=>$user,
				'controller'=>$this->getControllerBaseName(),
				'view'=>$this->getViewName(),
				'note'=>$note,
				'data_before'=> !empty($before) ? serialize($before) : null,
				'data_after'=> !empty($after) ? serialize($after) : null,
			)
		);
	}


    private function getTaxonChildren($id,$alphabeticalTree)
    {
        if (is_null($this->tmp))
		{

			$p = array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'columns' => 'id,taxon,parent_id,rank_id,taxon_order,is_hybrid,list_level'
				);

			if ($alphabeticalTree)
				$p['order'] = 'taxon';

            $d = $this->models->Taxa->_get($p);

            foreach((array)$d as $val)
			{
                $this->tmp[$val['parent_id']][$val['id']] = $val;
            }

        }

        return isset($this->tmp[$id]) ? $this->tmp[$id] : null;

    }

    public function getProjectUsers ($pId = null)
    {
        $pId = is_null($pId) ? $this->getCurrentProjectId() : $pId;

        $pru = $this->models->ProjectsRolesUsers->_get(array(
            'id' => array(
                'project_id' => $pId,
                'role_id !=' => '1',
                'active' => 1
            )
        ));

        $d = array();

        foreach ((array) $pru as $key => $val) {

            $u = $this->models->Users->_get(array(
                'id' => $val['user_id']
            ));

            $r = $this->models->Roles->_get(array(
                'id' => $val['role_id']
            ));

            $u['role'] = $r['role'];
            $u['role_id'] = $r['id'];

            $users[] = $u;

            $d[] = $u['id'];
        }

        // adding superusers (don't need assigned roles)
        $superusers = $this->models->Users->_get(array(
            'id' => array(
                'superuser' => '1'
            ),
            'columns' => '*,\'System Admin\' as role'
        ));

        foreach ((array) $superusers as $key => $val) {

            if (!in_array($val['id'], $d))
                $users[] = $val;
        }

        return $users;
    }


    public function setBreadcrumbRootName ($name)
    {
        $this->_breadcrumbRootName = $name;
    }

    public function getBreadcrumbRootName ()
    {
        return $this->_breadcrumbRootName;
    }

    public function addModuleToProject ($mId, $pId = null, $showOrder = 0)
    {

        /*
			 1 | Introduction
			 2 | Glossary
			 3 | Literature
			 4 | Species module
			 5 | Higher taxa
			 6 | Dichotomous key
			 7 | Matrix key
			 8 | Map key
			10 | Additional texts
			11 | Index
			12 | Search
		*/
        $this->models->ModulesProjects->save(
        array(
            'id' => null,
            'project_id' => is_null($pId) ? $this->getCurrentProjectId() : $pId,
            'module_id' => $mId,
            'active' => 'y',
            'show_order' => $showOrder
        ));
    }

    public function getProjectFSCode ($p)
    {
        return sprintf('%04s', $p);
    }

    public function createProjectCssFile ($id, $title)
    {
        $s = $this->generalSettings['directories']['runtimeStyleRoot'] . '/default/' . $this->generalSettings['projectCssTemplateFile'];
        $t = $this->makeCustomCssFileName($id, $title);


        if (file_exists($s)) {

            if (!copy($s, $t)) {

                $this->addError(sprintf($this->translate('Could not create %s'), $t));
            }
            else {

                return true;
            }
        }
        else {

            $this->addError(sprintf($this->translate('Template not found: %s'), $s));
        }

        return false;
    }



    public function makeCustomCssFileName ($p, $title)
    {
        return $this->generalSettings['directories']['runtimeStyleRoot'] . '/custom/' . $this->getProjectFSCode($p) . '--' . strtolower(
        preg_replace(array(
            '/\s/',
            '/[^A-Za-z0-9-]/'
        ), array(
            '-',
            ''
        ), $title) . '.css');
    }

    public function formatTaxon($p=null) //($taxon,$ranks=null)
    {

		if (is_null($p))
			return;

		// switching between $p being an array of parameters (taxon, ranks, rankpos) and $p just being the taxon (which is an array in itself)
		if (isset($p['taxon']) && is_array($p['taxon'])) {

			$taxon=$p['taxon'];
			$ranks=isset($p['ranks']) ? $p['ranks'] : null;
			$rankpos=(isset($p['rankpos']) && in_array($p['rankpos'],array('pre','post')) ? $p['rankpos'] : 'pre');

		} else {

			$taxon=$p;
			$ranks=null;
			$rankpos='pre';

		}

		if (empty($taxon))
			return;

        $e = explode(' ', $taxon['taxon']);
        $r = is_null($ranks) ? $this->newGetProjectRanks() : $ranks;

		if (!isset($taxon['rank_id'])||$taxon['rank_id']==0) // shouldn't happen!
			 return $taxon['taxon'];

        if (isset($r[$taxon['rank_id']]['labels'][$this->getDefaultProjectLanguage()]))
            $d = $r[$taxon['rank_id']]['labels'][$this->getDefaultProjectLanguage()];
        else
            $d = $r[$taxon['rank_id']]['rank'];

        $rankId = $r[$taxon['rank_id']]['rank_id'];
        $rankName = ucfirst($d);
        $abbreviation = $r[$taxon['rank_id']]['abbreviation'];

        // Rank level is above genus; no formatting
        if ($rankId < GENUS_RANK_ID) {
			return ($rankpos=='post' ? $taxon['taxon'].', '.$rankName : $rankName . ' ' . $taxon['taxon']);
            //return $rankName . ' ' . $taxon['taxon'];
        }

        // Genus or subgenus; add italics
        if ($rankId < SPECIES_RANK_ID && count($e) == 1) {
			$name = ($rankpos=='post' ? '<span class="italics">' . $taxon['taxon'] . '</span>, '.$rankName : $rankName . '  <span class="italics">' . $taxon['taxon'] . '</span>');
            //$name = $rankName . ' <span class="italics">' . $taxon['taxon'] . '</span>';
        }

        // Species
        if ($rankId > GENUS_RANK_ID && count($e) == 2) {
            $name = '<span class="italics">' . $taxon['taxon'] . '</span>';
        }

        // Regular infraspecies, name consists of three parts
        if (count($e) == 3) {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . (!empty($abbreviation) ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[2] . '</span>';
        }

        // Single infraspecies with subgenus
        if (count($e) == 4 && $e[1][0] == '(') {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . ' ' . $e[2] . (!empty($abbreviation) ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[3] . '</span>';
        }

        // Return now if name has been set
        if (isset($name)) {
			return $this->setHybridMarker($name, $rankId, isset($taxon['is_hybrid']) ? $taxon['is_hybrid'] : 0);
        }


        // Now we're handling more complicated cases. We need the parent before continuing
        // say goodbye to the orphans
		if (empty($taxon['parent_id'])) {
            return $taxon['taxon'];
        }

        $parent = $this->getTaxonById($taxon['parent_id']);
        // say goodbye to the misguided orphans
        if (empty($parent['rank_id'])) {
            return $taxon['taxon'];
        }
        $parentAbbreviation = $r[$parent['rank_id']]['abbreviation'];

        // Double infraspecies
        if (count($e) == 4) {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . (!empty($parentAbbreviation) ? '</span> ' . $parentAbbreviation . ' <span class="italics">' : ' ') . $e[2] .
             (!empty($abbreviation) ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[3] . '</span>';
        }

        // Double infraspecies with subgenus
        if (count($e) == 5 && $e[1][0] == '(') {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . ' ' . $e[2] . (!empty($parentAbbreviation) ? '</span> ' . $parentAbbreviation . ' <span class="italics">' : ' ') . $e[3] .
             (!empty($abbreviation) ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[4] . '</span>';
        }

        // Return now if name has been set
        if (isset($name)) {
            return $this->setHybridMarker($name, $rankId, $taxon['is_hybrid']);
        }

        // If we end up here something must be wrong, just return name sans formatting
        return $taxon['taxon'];
    }


    private function setHybridMarker ($name, $rankId, $isHybrid)
    {
        if ($isHybrid == 0) {
            return $name;
        }

        $marker = ($rankId == GRAFT_CHIMERA_RANK_ID ? '+' : '&#215;');

        // intergeneric hybrid
        if ($isHybrid == 2 || $rankId < SPECIES_RANK_ID) {
            return $marker . ' ' . $name;
        }

        // interspecific hybrid; string is already formatted so take second space!!
        return implode(' ' . $marker . ' ', explode(' ', $name, 3));

    }


    public function grantModuleAccessRights ($mId, $pId, $uId = null)
    {
        $this->models->ModulesProjectsUsers->save(array(
            'id' => null,
            'project_id' => $pId,
            'module_id' => $mId,
            'user_id' => isset($uId) ? $uId : $this->getCurrentUserId()
        ));
    }



    public function addUserToProjectAsLeadExpert ($pId, $uId = null)
    {
        $this->models->ProjectsRolesUsers->save(
        array(
            'id' => null,
            'project_id' => $pId,
            'role_id' => ID_ROLE_LEAD_EXPERT,
            'user_id' => isset($uId) ? $uId : $this->getCurrentUserId(),
            'active' => 1
        ));
    }



    public function getVariations ($tId = null)
    {
        $d = array(
            'project_id' => $this->getCurrentProjectId()
        );

        if (isset($tId))
            $d['taxon_id'] = $tId;

        $tv = $this->models->TaxaVariations->_get(array(
            'id' => $d,
            'columns' => 'id,taxon_id,label',
            'order' => 'label'
        ));

        foreach ((array) $tv as $key => $val) {

            $tv[$key]['taxon'] = $this->getTaxonById($val['taxon_id']);

            $tv[$key]['labels'] = $this->models->VariationsLabels->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'variation_id' => $val['id']
                ),
                'columns' => 'id,language_id,label,label_type'
            ));
        }

        return $tv;
    }



    public function getVariation ($id)
    {

        $tv = $this->models->TaxaVariations->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $id
            ),
            'columns' => 'id,taxon_id,label'
        ));

        $tv[0]['labels'] = $this->models->VariationsLabels->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'variation_id' => $id
            ),
            'columns' => 'id,language_id,label,label_type'
        ));

        return $tv[0];
    }



    private function getFrontEndMainMenu ()
    {
        $modules = $this->models->ModulesProjects->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'active' => 'y'
            ),
            'order' => 'module_id asc'
        ));

        foreach ((array) $modules as $key => $val) {

            $mp = $this->models->Modules->_get(array(
                'id' => $val['module_id']
            ));

            $modules[$key]['type'] = 'regular';
            $modules[$key]['icon'] = $mp['icon'];
            $modules[$key]['module'] = $mp['module'];
            $modules[$key]['controller'] = $mp['controller'];
        }

        $freeModules = $this->models->FreeModulesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'active' => 'y'
            )
        ));

        foreach ((array) $freeModules as $key => $val) {

            $val['type'] = 'free';
            $modules[] = $val;
        }

        return $modules;
    }

    public function getProjectRanks ($params = false)
    {
        $includeLanguageLabels = isset($params['includeLanguageLabels']) ? $params['includeLanguageLabels'] : false;
        $lowerTaxonOnly = isset($params['lowerTaxonOnly']) ? $params['lowerTaxonOnly'] : false;
        $forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : false;
        $keypathEndpoint = isset($params['keypathEndpoint']) ? $params['keypathEndpoint'] : false;
        $idsAsIndex = isset($params['idsAsIndex']) ? $params['idsAsIndex'] : false;

        $forceLookup = true;

        if (!$forceLookup) {

            if (!isset($_SESSION['admin']['project']['ranks']['includeLanguageLabels']) || $_SESSION['admin']['project']['ranks']['includeLanguageLabels'] != $includeLanguageLabels ||
             !isset($_SESSION['admin']['project']['ranks']['lowerTaxonOnly']) || $_SESSION['admin']['project']['ranks']['lowerTaxonOnly'] != $lowerTaxonOnly || !isset($_SESSION['admin']['project']['ranks']['keypathEndpoint']) ||
             $_SESSION['admin']['project']['ranks']['keypathEndpoint'] != $keypathEndpoint || !isset($_SESSION['admin']['project']['ranks']['idsAsIndex']) || $_SESSION['admin']['project']['ranks']['idsAsIndex'] != $idsAsIndex)

                $forceLookup = true;
        }

        $_SESSION['admin']['project']['ranks']['includeLanguageLabels'] = $includeLanguageLabels;
        $_SESSION['admin']['project']['ranks']['lowerTaxonOnly'] = $lowerTaxonOnly;
        $_SESSION['admin']['project']['ranks']['keypathEndpoint'] = $keypathEndpoint;
        $_SESSION['admin']['project']['ranks']['idsAsIndex'] = $idsAsIndex;

        if (!isset($_SESSION['admin']['project']['ranks']['projectRanks']) || $forceLookup) {

            if ($keypathEndpoint)
                $d = array(
                    'project_id' => $this->getCurrentProjectId(),
                    'keypath_endpoint' => 1
                );
            elseif ($lowerTaxonOnly)
                $d = array(
                    'project_id' => $this->getCurrentProjectId(),
                    'lower_taxon' => 1
                );
            else
                $d = array(
                    'project_id' => $this->getCurrentProjectId()
                );

            $p = array(
                'id' => $d,
                'order' => 'rank_id'
            );

            if ($idsAsIndex) {

                $p['fieldAsIndex'] = 'id';
            }

            $pr = $this->models->ProjectsRanks->_get($p);

            foreach ((array) $pr as $rankkey => $rank) {

                $r = $this->models->Ranks->_get(array(
                    'id' => $rank['rank_id']
                ));

                $pr[$rankkey]['rank'] = $r['rank'];

                $pr[$rankkey]['abbreviation'] = $r['abbreviation'];

                $pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];

                $pr[$rankkey]['ideal_parent_id'] = $r['ideal_parent_id'];

                if ($includeLanguageLabels) {

                    foreach ((array) $this->getProjectLanguages() as $langaugekey => $language) {

                        $lpr = $this->models->LabelsProjectsRanks->_get(
                        array(
                            'id' => array(
                                'project_id' => $this->getCurrentProjectId(),
                                'project_rank_id' => $rank['id'],
                                'language_id' => $language['language_id']
                            ),
                            'columns' => 'label'
                        ));

                        $pr[$rankkey]['labels'][$language['language_id']] = $lpr[0]['label'];
                    }
                }
            }

            $_SESSION['admin']['project']['ranks']['projectRanks'] = $pr;
        }

        return $_SESSION['admin']['project']['ranks']['projectRanks'];
    }



    public function getAllLanguages ()
    {

        /*
        $languages = array_merge(
			$this->models->Languages->_get(array('id' => 'select * from %table% where show_order is not null order by show_order asc')),
	        $this->models->Languages->_get(array('id' => 'select * from %table% where show_order is null order by language asc'))
		);
		*/

        //		unset($_SESSION['admin']['project']['system']['languages']);
        if (!isset($_SESSION['admin']['project']['system']['languages'])) {

            $_SESSION['admin']['project']['system']['languages'] = $this->models->Languages->_get(array(
                'id' => '*',
                'fieldAsIndex' => 'id'
            ));
        }

        return $_SESSION['admin']['project']['system']['languages'];
    }



    public function setProjectLanguages()
    {
        $lp = $this->models->LanguagesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'def_language desc'
        ));

        foreach ((array) $lp as $key => $val) {

            $l = $this->models->Languages->_get(array(
                'id' => $val['language_id']
            ));

            $lp[$key]['language'] = $l['language'];
            $lp[$key]['direction'] = $l['direction'];
            $lp[$key]['iso2'] = $l['iso2'];
            $lp[$key]['iso3'] = $l['iso3'];

            if ($val['def_language'] == 1)
                $defaultLanguage = $val['language_id'];

            $list[$val['language_id']] = array(
                'language' => $l['language'],
                'direction' => $l['direction']
            );
        }

        $_SESSION['admin']['project']['languages'] = $lp;

        if (isset($defaultLanguage))
            $_SESSION['admin']['project']['default_language_id'] = $defaultLanguage;

        if (isset($list))
            $_SESSION['admin']['project']['languageList'] = $list;
    }

    public function getProjectLanguages()
	{
		return isset($_SESSION['admin']['project']['languages']) ? $_SESSION['admin']['project']['languages'] : null;
	}


    public function getDefaultProjectLanguage ()
    {
        return isset($_SESSION['admin']['project']['default_language_id']) ? $_SESSION['admin']['project']['default_language_id'] : null;
    }

    public function rHasVar($var)
    {

		return isset($this->requestData[$var]);

    }

    public function rHasVal($var, $val = null)
    {
        if ($val !== null) {

            return isset($this->requestData[$var]) && $this->requestData[$var] === $val;
        }
        else {

            return isset($this->requestData[$var]) && $this->requestData[$var] !== '';
        }
    }

    public function rGetVal($var)
    {
		return isset($this->requestData[$var]) ? $this->requestData[$var] : null;
    }


    public function rGetAll()
    {
		return isset($this->requestData) ? $this->requestData : null;
    }


    public function rHasId ()
    {
        return $this->rHasVal('id');
    }

    public function rGetId ()
    {
        return $this->rGetVal('id');
    }




    /**
     * Set a temporary controller base name, different from the current one
     *
     * @access    public
     * @param     string  $controllerBaseName  masking controller base name
     * @param     string  $controllerPublicName  masking controller public name
     */
    public function setControllerMask ($controllerBaseName, $controllerPublicName)
    {
        $this->controllerBaseNameMask = $controllerBaseName;

        $this->controllerPublicNameMask = $controllerPublicName;
    }


    public function loadControllerConfig ($controllerBaseName = null)
    {
        if (isset($controllerBaseName))
            $t = 'getControllerSettings' . $controllerBaseName;
        else
            $t = 'getControllerSettings' . $this->controllerBaseName;

        if (method_exists($this->config, $t)) {

            $this->controllerSettings = $this->config->$t();
        }
        else {

            $this->controllerSettings = false;
        }
    }

    /**
     * Assigns basic Smarty variables
     *
     * @access     public
     */
    private function preparePage ()
    {
        $this->setBreadcrumbs();

        $this->smarty->assign('debugMode', $this->debugMode);
        $this->smarty->assign('session', $_SESSION);
        $this->smarty->assign('database', $this->config->getDatabaseSettings());
        $this->smarty->assign('baseUrl', $this->baseUrl);
        $this->smarty->assign('controllerPublicName', $this->controllerPublicName);
        $this->smarty->assign('controllerBaseName', $this->controllerBaseName);
        $this->smarty->assign('rnd', $this->getRandomValue());
        $this->smarty->assign('printBreadcrumbs', $this->printBreadcrumbs);
        $this->smarty->assign('breadcrumbs', $this->getBreadcrumbs());
        $this->smarty->assign('errors', $this->getErrors());
        $this->smarty->assign('messages', $this->getMessages());
        $this->smarty->assign('warnings', $this->getWarnings());
        $this->smarty->assign('app', $this->generalSettings['app']);
        $this->smarty->assign('pageName', $this->getPageName());

        $this->smarty->assign('uiLanguages', $this->uiLanguages);
        $this->smarty->assign('uiCurrentLanguage', $this->getCurrentUiLanguage());
        $this->smarty->assign('isMultiLingual', $this->isMultiLingual);
        $this->smarty->assign('useVariations', $this->useVariations);
        $this->smarty->assign('useRelated', $this->useRelated);

        $this->smarty->assign('isSysAdmin', $this->isCurrentUserSysAdmin());
        $this->smarty->assign('currentUserRole', $this->getCurrentUserRole());

        $this->smarty->assign('useJavascriptLinks', $this->generalSettings['useJavascriptLinks']);
        $this->smarty->assign('autoSaveFrequency', $this->generalSettings['autoSaveFrequency']);

        if (isset($this->cssToLoad))
            $this->smarty->assign('cssToLoad', $this->cssToLoad);

        if (isset($this->jsToLoad))
            $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);

        $this->smarty->assign('controllerMenuExists', $this->includeLocalMenu && file_exists($this->smarty->getTemplateDir(0) . '_menu.tpl'));

        if (isset($_SESSION['admin']['user']) && !$_SESSION['admin']['user']['_said_welcome'])
		{

            $msg=
				sprintf(($_SESSION['admin']['user']['logins'] <= 1 ?
					$this->translate('Welcome, %s.') :
					$this->translate('Welcome back, %s.')),
				$_SESSION['admin']['user']['first_name'] . ' ' . $_SESSION['admin']['user']['last_name']);

            $this->smarty->assign('welcomeMessage', $msg);

            $_SESSION['admin']['user']['_said_welcome'] = true;
        }

		if (isset($_SESSION['admin']['user']['search']['results']) && $_SESSION['admin']['user']['search']['results']['count']>0)
		{
            $this->smarty->assign('userSearch',$_SESSION['admin']['user']['search']);
		}

    }



    private function getCurrentUiLanguage ()
    {
        return (isset($_SESSION['admin']['user']['currentLanguage']) ? $_SESSION['admin']['user']['currentLanguage'] : $this->uiDefaultLanguage);
    }



    private function getModuleActivationStatus ()
    {

		// NEEDS TO BE REPAIRED!
		// REFAC2015
		return 1;


        // if a controller has no module id, it is accessible at all times
        if (!isset($this->controllerBaseName))
            return 1;

        $mp = $this->models->ModulesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->controllerModuleId
            )
        ));

        // return 1 for present and activated modules, 0 for just present, and -1 for not present
        return ($mp[0]['active'] == 'n' ? 0 : ($mp[0]['active'] == 'y' ? 1 : -1));
    }



    /**
     * Starts the user's session
     *
     * @access     private
     */
    private function startSession ()
    {
        if (!isset($_SESSION)) session_start();
    }



    private function setTimeZone ()
    {
        date_default_timezone_set($this->generalSettings['serverTimeZone']);
    }



    /**
     * Sets a global 'debug' mode, based on a general setting in the config file
     *
     * @access     private
     */
    private function setDebugMode ()
    {
        $this->debugMode = $this->generalSettings['debugMode'];
    }



    /**
     * Sets class variables, based on a page's url
     *
     * Sets the following:
     * full path ('/admin/views/projects/collaborators.php')
     * application name ('admin')
     * controller's base name ('projects' for 'ProjectsController')
     * view name ('collaborators')
     *
     * @access     private
     */
    private function setNames ()
    {
        $this->appName = $this->generalSettings['app']['pathName'];

        $this->_fullPath = $_SERVER['PHP_SELF'];

        $path = pathinfo($this->_fullPath);

        $dirnames = explode('/', $path['dirname']);

        $this->baseUrl = '../';

        for ($i = count((array) $dirnames) - 1; $i >= 1; $i--) {

            if (strtolower($dirnames[$i]) == $this->appName) {
                if (isset($dirnames[$i + 2]))
                    $this->controllerBaseName = strtolower($dirnames[$i + 2]);
                break;
            }

            $this->baseUrl .= '../';
        }

        if ($path['filename'])
            $this->_viewName = $path['filename'];

        $this->_fullPathRelative = $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/' . $this->_viewName . '.php';

        if (empty($this->appName))
            $this->log('No application name set', 2);
        if (empty($this->_viewName))
            $this->log('No view name set', 2);
            //if (empty($this->controllerBaseName)) $this->log('No controller basename set',2);
        if (empty($this->baseUrl))
            $this->log('No base URL set', 2);
        if (empty($this->_fullPath))
            $this->log('No full path set', 2);
        if (empty($this->_fullPathRelative))
            $this->log('No relative full path set', 2);
    }


    private function startModuleSession()
	{
		$this->moduleSession=$this->helpers->SessionModuleSettings;
		$this->moduleSession->setModule( array('environment'=>'admin','controller'=>$this->controllerBaseName) );

		$this->baseSession=$this->helpers->SessionModuleSettings;
		$this->baseSession->setModule( array('environment'=>'admin','controller'=>'base') );
	}


    /**
     * Sets general Smarty variables (paths, compilder directives)
     *
     * @access     private
     */
    private function setSmartySettings ()
    {
        $this->_smartySettings = $this->config->getSmartySettings();

        $this->smarty = new Smarty();

        /* DEBUG */
        $this->smarty->force_compile = true;

        $cbn = $this->getControllerBaseName();

        $this->smarty->template_dir = $this->_smartySettings['dir_template'] . '/' . (isset($cbn) ? $cbn . '/' : '');
        $this->smarty->compile_dir = $this->_smartySettings['dir_compile'];
        $this->smarty->cache_dir = $this->_smartySettings['dir_cache'];
        $this->smarty->config_dir = $this->_smartySettings['dir_config'];
        $this->smarty->caching = $this->_smartySettings['caching'];
        $this->smarty->compile_check = $this->_smartySettings['compile_check'];
		$this->smarty->registerPlugin("block","t", array($this,"smartyTranslate"));
		$this->smarty->error_reporting = E_ALL & ~E_NOTICE;

		/*
				$this->smarty->register_block('t', array(
					&$this,
					'smartyTranslate'
				));
		*/

    }



    /**
     * Assigns POST and GET variables to a class variable 'requestData'; posted files to 'requestDataFiles'
     *
     * @access     private
     */
    private function setRequestData ()
    {
        $this->requestData = false;

        if (!empty($_GET) || !empty($_POST)) {

            //$this->requestData = $_REQUEST; // also contains cookies
            $this->requestData = array_merge((array) $_GET, (array) $_POST); // don't want no cookies!

            foreach ((array) $this->requestData as $key => $val) {

                if (is_array($val)) {

                    foreach ((array) $val as $key2 => $val2) {

                        if (is_array($val2)) {

                            foreach ((array) $val2 as $key3 => $val3) {

                                $this->requestData[$key][$key2][$key3] = get_magic_quotes_gpc() ? stripslashes($val3) : $val3;
								array_walk($this->requestData[$key][$key2],function(&$n){$n=($n=='false'?false:($n=='true'?true:$n));});
                            }
                        }
                        else {

                            $this->requestData[$key][$key2] = get_magic_quotes_gpc() ? stripslashes($val2) : $val2;
							array_walk($this->requestData[$key],function(&$n){$n=($n=='false'?false:($n=='true'?true:$n));});
                        }
                    }
                }
                else {

                    $this->requestData[$key] = get_magic_quotes_gpc() ? stripslashes($val) : $val;
					array_walk($this->requestData,function(&$n){$n=($n=='false'?false:($n=='true'?true:$n));});
                }
            }
        }

        foreach ((array) $_FILES as $key => $val) {

            if (isset($val['size']) && $val['size'] > 0) {

                $this->requestDataFiles[] = $val;
            }
        }
    }



    private function doLanguageChange ($unsetRequestVar = true)
    {
        if ($this->isMultiLingual)
		{
            if ($this->rHasVar('uiLang'))
			{
                $this->setLocale($this->rGetVal('uiLang'));
            }
        }
        else
		{
            $this->log('Attempt to set language ' . $this->rGetVal('uiLang') . ' for non-mulitlanguage page', 1);
        }

        if ($unsetRequestVar)
		{
            unset($this->requestData['uiLang']);

            if (empty($this->requestData ))
			{
                unset($this->requestData );
            }
        }
    }



    /**
     * Loads the required models (database abstraction classes for the various tables)
     *
     * Takes the model's names specified in the class variables usedModelsBase and usedModels,
     * loads the corresponding class files, and initiates an instance of each model class
     * as object of the class variable 'models'.
     *
     * @access     private
     */
    private function loadModels ()
    {
        if (isset($this->usedModelsBase) && isset($this->usedModels))
		{
            $d = array_unique(array_merge((array) $this->usedModelsBase, (array) $this->usedModels));
        }
        else
		if (isset($this->usedModelsBase))
		{
            $d = $this->usedModelsBase;
        }
        else
		if (isset($this->usedModels))
		{
            $d = $this->usedModels;
        }
        else
		{
            return;
        }

        $this->models = new stdClass();

        // Load base controller model first
		require_once dirname(__FILE__) . '/../models/ControllerModel.php';
		$this->models->ControllerModel = new ControllerModel;

		// Load controller-specific model
        $t = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getControllerBaseName())))) . 'Model';

        if (file_exists(dirname(__FILE__) . '/../models/' . $t . '.php'))
		{
            require_once dirname(__FILE__) . '/../models/' . $t . '.php';
            $this->models->$t = new $t;
        }

		// Load controller-specific model by override
		if ( isset($this->modelNameOverride) )
		{
			if (file_exists(dirname(__FILE__) . '/../models/' . $this->modelNameOverride . '.php'))
			{
				require_once dirname(__FILE__) . '/../models/' . $this->modelNameOverride . '.php';
				$this->models->{$this->modelNameOverride} = new $this->modelNameOverride;
			}
		}

        // Load models for each table, as specified in used models
        require_once dirname(__FILE__) . '/../models/Table.php';
        foreach ((array) $d as $key)
		{
            $t = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $this->models->$t = new Table($key);

            if (isset($this->helpers->LoggingHelper))
			{
                 $this->models->$t->setLogger($this->helpers->LoggingHelper);
            }
        }
    }

    protected function extendUsedModels ()
    {
        if (isset($this->usedModelsExtended) && is_array($this->usedModelsExtended)) {
            $this->usedModels = array_unique(array_merge((array) $this->usedModels, (array) $this->usedModelsExtended));
        }
    }

    /**
     * Loads the required helpers (separate multi-use classes)
     *
     * Takes the helper's names specified in the class variables usedHelpers,
     * loads the corresponding class files, and initiates an instance of each helper class
     * as object of the class variable 'helpers'.
     *
     * @access     private
     */
    private function loadHelpers ()
    {
        if (isset($this->usedHelpersBase) && isset($this->usedHelpers))
		{
            $d = array_unique(array_merge((array) $this->usedHelpersBase, (array) $this->usedHelpers));
        }
        else
		if (isset($this->usedHelpersBase))
		{
            $d = $this->usedHelpersBase;
        }
        else
		if (isset($this->usedHelpers))
		{
            $d = $this->usedHelpers;
        }
        else
		{
            return;
        }

        $this->helpers = new stdClass();

        foreach ((array) $d as $key)
		{
            if (file_exists(dirname(__FILE__) . '/../helpers/' . $key . '.php'))
			{
                require_once (dirname(__FILE__) . '/../helpers/' . $key . '.php');

                $d = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

                if (class_exists($d))
				{
                    $this->helpers->$d = new $d();
                }
            }
        }
    }



    private function initLogging ()
    {
        $fn = $this->getAppName() ? $this->getAppName() : 'general';

        $this->helpers->LoggingHelper->setLogFile($this->generalSettings['directories']['log'] . '/' . $fn . '.log');

        $this->helpers->LoggingHelper->setLevel(0);
    }



    private function setLanguages ()
    {
        foreach ((array) $this->generalSettings['uiLanguages'] as $key => $val)
		{
            if ($key == 0)
                $this->uiDefaultLanguage = $val;

            $l = $this->models->Languages->_get(array(
                'id' => array(
                    'id' => $val
                ),
                'columns' => 'id,language'
            ));

            $this->uiLanguages[] = $l[0];
        }
    }



    /**
     * Makes project paths for image uploads etc.
     *
     * @access     public
     */
    public function makePathNames ($p)
    {
        if ($p)
            return array(
                'project_media' => $this->generalSettings['directories']['mediaDirProject'] . '/' . $this->getProjectFSCode($p) . '/',
                'project_thumbs' => $this->generalSettings['directories']['mediaDirProject'] . '/' . $this->getProjectFSCode($p) . '/thumbs/',
                'project_media_l2_maps' => $this->generalSettings['directories']['mediaDirProject'] . '/' . $this->getProjectFSCode($p) . '/l2_maps/',
                'media_url' => $this->generalSettings['paths']['mediaBasePath'] . '/' . $this->getProjectFSCode($p) . '/',
            );
        else
            return null;
    }



    /**
     * Sets project paths for image uploads etc. and makes sure they actually exist
     *
     * @access     private
     */
    private function setPaths ()
    {
        $p = $this->getCurrentProjectId();

        if ($p) {

            $paths = $this->makePathNames($p);

            $_SESSION['admin']['project']['paths']['project_media'] = $paths['project_media'];
            $_SESSION['admin']['project']['paths']['project_thumbs'] = $paths['project_thumbs'];
            $_SESSION['admin']['project']['paths']['project_media_l2_maps'] = $paths['project_media_l2_maps'];

			$this->baseSession->setModuleSetting( array( 'setting'=>'project_media_path', 'value' => $paths['project_media'] ) );
			$this->baseSession->setModuleSetting( array( 'setting'=>'project_thumbs_path', 'value' => $paths['project_thumbs'] ) );
			$this->baseSession->setModuleSetting( array( 'setting'=>'project_media_l2_maps_path', 'value' => $paths['project_media_l2_maps'] ) );

            foreach ((array) $_SESSION['admin']['project']['paths'] as $key => $val) {

                if (!file_exists($val)) {

                    if (mkdir($val))
                        $this->log('Created directory "' . $val . '"');
                    else
                        $this->log('Failed creating directory "' . $val . '"');
                }
            }
        }
    }



    /**
     * Sets project URL for project images
     *
	 * @todo	take out hard reference to /media/
     * @access     private
     */
    private function setUrls ()
    {
        $p = $this->getCurrentProjectId();

        if ($p) {

            $_SESSION['admin']['project']['urls']['project_media'] = $this->baseUrl . 'shared/media/project/' . $this->getProjectFSCode($p) . '/';
            $_SESSION['admin']['project']['urls']['project_thumbs'] = $_SESSION['admin']['project']['urls']['project_media'] . 'thumbs/';
            $_SESSION['admin']['project']['urls']['project_media_l2_maps'] = $_SESSION['admin']['project']['urls']['project_media'] . 'l2_maps/';
            $_SESSION['admin']['project']['urls']['system_media_l2_maps'] = $this->baseUrl . 'shared/media/system/l2_maps/';
        }
    }



    /**
     * Sets a "custom http_referer", including the page's name, in the session
     *
     * @access     private
     */
    private function setLastVisitedPage ()
    {
        if (!$this->excludeFromReferer)
		{
            if (null!==$this->baseSession->getModuleSetting( 'referer_url' ))
				$this->baseSession->setModuleSetting( array('setting'=>'prev_referer_url','value'=>$this->baseSession->getModuleSetting( 'referer_url' ) ) );

            if (null!==$this->baseSession->getModuleSetting( 'referer_name' ))
				$this->baseSession->setModuleSetting( array('setting'=>'prev_referer_name','value'=>$this->baseSession->getModuleSetting( 'referer_name' ) ) );

			$this->baseSession->setModuleSetting( array('setting'=>'referer_url','value'=> $_SERVER['REQUEST_URI'] ));
			$this->baseSession->setModuleSetting( array('setting'=>'referer_name','value'=> $this->getPageName() ));
        }
    }



    /**
     * Makes sure the custom http_referer points at the actual previous page on a user reload of the current page
     *
     * @access     private
     */
    private function checkLastVisitedPage ()
    {
        if (
			null!==$this->baseSession->getModuleSetting( 'referer_url' ) &&
			null!==$this->baseSession->getModuleSetting( 'prev_referer_url' ) &&
			$this->baseSession->getModuleSetting( 'referer_url' )==$_SERVER['REQUEST_URI'])
		{
			$this->baseSession->setModuleSetting( array('setting'=>'referer_url','value'=> $this->baseSession->getModuleSetting( 'prev_referer_url' ) ) );
			$this->baseSession->setModuleSetting( array('setting'=>'referer_name','value'=> $this->baseSession->getModuleSetting( 'prev_referer_name' ) ) );
			$this->baseSession->setModuleSetting( array('setting'=>'prev_referer_url') );
			$this->baseSession->setModuleSetting( array('setting'=>'prev_referer_name') );
        }
    }



    /**
     * Sets the page to redirect to after logging in
     *
     * Pages that require login redirect the user towards the login. By setting the 'login_start_page'
     * the app can direct the to the desired page after they have succesfully logged in.
     *
     * @access     private
     */
    private function setLoginStartPage ()
    {
        $_SESSION['admin']['login_start_page'] = $this->_fullPath;
    }



    public function setSuppressProjectInBreadcrumbs ($state = true)
    {
        $this->suppressProjectInBreadcrumbs = $state;
    }



    /**
     * Create the breadcrumb trail
     *
     * @access     private
     */
    private function setBreadcrumbs ()
    {
        if (!isset($this->appName))
            return;

        $breadcrumbRootName = (!is_null($this->getBreadcrumbRootName()) ? $this->getBreadcrumbRootName() : 'Projects');

        // root of each trail: "choose project" page
        $cp = $this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject'];

        $this->breadcrumbs[] = array(
            'name' => $this->translate($breadcrumbRootName),
            'url' => $cp
        );


        // controller name can be overridden
        $controllerPublicName = ($this->controllerPublicNameMask ? $this->controllerPublicNameMask : $this->controllerPublicName);
        $controllerBaseName = ($this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->controllerBaseName);

        if ($this->_fullPathRelative != $cp && isset($_SESSION['admin']['project']['title']) && !$this->suppressProjectInBreadcrumbs) {

            $this->breadcrumbs[] = array(
                'name' => $_SESSION['admin']['project']['title'],
                'url' => $this->getLoggedInMainIndex()
            );


            if (!empty($controllerPublicName) && $this->_fullPath != $this->getLoggedInMainIndex()) {

                $curl = $this->baseUrl . $this->appName . '/views/' . $controllerBaseName;

                $this->breadcrumbs[] = array(
                    'name' => $controllerPublicName,
                    'url' => $curl
                );

                if ($this->getViewName() != 'index') {

                    // all views are on the same level, but sometimes we might want another level to the trail when
                    // moving one view to the next, for logic's sake (for instance: taxon list -> edit taxon, two views
                    // that are on the same level, but are perceived by the user to be on different levels)
                    if ($this->breadcrumbIncludeReferer === true) {

                        $this->breadcrumbs[] = array(
                            'name' => $this->baseSession->getModuleSetting( 'referer_name' ),
                            'url' => $this->baseSession->getModuleSetting( 'referer_url' )
                        );
                    }
                    elseif (is_array($this->breadcrumbIncludeReferer)) {

                        $this->breadcrumbs[] = $this->breadcrumbIncludeReferer;
                    }

                    $this->breadcrumbs[] = array(
                        'name' => $this->getPageName(),
                        'url' => $curl . '/' . $this->getViewName() . '.php'
                    );
                }
            }
        }
        else
		if ($this->_fullPathRelative != $cp)
		{
            // for special cases in which no project has been set (like 'create project')
            $curl = $this->baseUrl . $this->appName . '/views/' . $controllerBaseName;

            $this->breadcrumbs[] = array(
                'name' => $this->getPageName(),
                'url' => $curl . '/' . $this->getViewName() . '.php'
            );
        }
    }



    /**
     * Returns the breadcrumb trail
     *
     * @access     private
     * @return    array    breadcrumb trail: array of crumbname => crumbpath
     */
    private function getBreadcrumbs ()
    {
        if (isset($this->breadcrumbs)) return $this->breadcrumbs;
    }



    /**
     * Checks whether a user is authorized to view/use a page within a project
     *
     * @return     boolean        authorized or not
     * @access     private
     */
    private function isUserAuthorisedForProjectPage ()
    {
        $controllerBaseName = ($this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->getControllerBaseName());

        // is no controller base name is set, we are in /admin/views/utilities/admin_index.php, which is the portal to the modules
        if ($controllerBaseName == '')
            return true;

        if (isset($_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName])) {

            $d = $_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName];

            foreach ((array) $d as $key => $val) {

                if ($val == '*' || $val == $this->getViewName()) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Sets a random integer value for general use
     *
     * @access     private
     */
    private function setRandomValue ()
    {
        $this->randomValue = mt_rand(99999, mt_getrandmax());
    }



    /**
     * Returns random integer value
     *
     * @return integer    anything between 99999 and mt_getrandmax()
     * @access     private
     */
    private function getRandomValue ()
    {
        return $this->randomValue;
    }

    private function saveFormResubmitVal ()
    {
        if (!$this->noResubmitvalReset)
			$this->baseSession->setModuleSetting( array('setting'=>'last_rnd','value'=>$this->rHasVal('rnd') ? $this->rGetVal('rnd') : null ) );
    }

	private function getCurrentUserRole()
	{

		$pId = $this->getCurrentProjectId();

		if (empty($pId))
			return;

		   $pru = $this->models->ProjectsRolesUsers->_get(
			array(
				'id' => array(
					'project_id' => $pId,
					'user_id' => $this->getCurrentUserId()
				)
			));

		return $pru[0]['role_id'];

	}

    private function loadSmartyConfig ()
    {
        $this->_smartySettings = $this->config->getSmartySettings();
    }


    private function checkWriteableDirectories ()
    {
        $paths = array(
            $this->_smartySettings['dir_compile'] => 'www/admin/templates/templates_c',
            $this->_smartySettings['dir_cache'] => 'www/admin/templates/cache',
            $this->generalSettings['directories']['mediaDirProject'] => 'www/shared/media/project',
            $this->generalSettings['directories']['log'] => 'log'
        );

        foreach ((array) $paths as $val => $display)
		{
            if ((!file_exists($val) || !is_writable($val)) && @!mkdir($val)) {
                 $fixPaths[] = $display;
            }

        }

        if (isset($fixPaths)) {

        	echo '<p>Some required paths do not exist or are not writeable.
        	   Linnaeus NG cannot proceed until this has been corrected:</p>';

        	foreach ($fixPaths as $message) {

        		echo $message . '<br>';
        	}

        	die();

        }

    }

	private function initTranslator()
	{
		include_once ('TranslatorController.php');
		$this->translator = new TranslatorController('admin',$this->getDefaultProjectLanguage());
	}

	public function translate($content)
	{
		return $this->translator->translate($content);
	}

	public function javascriptTranslate($content)
	{
		return $this->translator->translate($content);
	}

	public function smartyTranslate ($params, $content, &$smarty, &$repeat)
	{
		$c = $this->translator->translate($content);

		if (isset($params))
		{
			foreach ((array) $params as $key => $val)
			{
				if (substr($key, 0, 2) == '_s' && isset($val))
				{
					$c = preg_replace('/\%s/', $val, $c, 1);
				}
			}
		}
		return $c;
	}


	/*
	 * Used to load an external model. Useful if the required methods of a controller's
	 * model are more logically stored in a model associated with a different controller;
	 * e.g. the methods for ModuleSettingsReaderController are available in ModuleSettingsModel rather
	 * than in its own model.
	 */
	protected function loadExternalModel ($model)
	{
        if (file_exists(dirname(__FILE__) . "/../models/{$model}.php")) {
            include_once dirname(__FILE__) . "/../models/{$model}.php";
            $this->models->{$model} = new $model;
        }
	}


}
