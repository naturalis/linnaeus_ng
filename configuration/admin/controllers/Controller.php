<?php

include_once (dirname(__FILE__) . "/../BaseClass.php");

include_once (dirname(__FILE__) . "/../../../smarty/Smarty.class.php");

class Controller extends BaseClass
{
    
    private $_smartySettings;
    private $_viewName;
    private $_fullPath;
    private $_fullPathRelative;
    private $_helpTexts;

    public $smarty;
    public $requestData;
    public $data;
    public $randomValue;
    public $breadcrumbIncludeReferer;
    public $errors;
    public $messages;
    public $controllerBaseName;
    public $pageName;
    public $controllerPublicName;
    public $sortField;
    public $sortDirection;
    public $sortCaseSensitivity;
    public $baseUrl;
	public $excludeFromReferer = false;

    private $usedModelsBase = array(
        'helptext', 
        'project', 
        'module_project'
    );


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
        
        $this->setDebugMode();
        
        $this->startSession();
        
        $this->setNames();
        
        $this->loadControllerConfig();        
        
        $this->checkLastVisitedPage();
        
        $this->setSessionActivePageValues();
        
        $this->setSmarty();
        
        $this->setRequestData();
        
        $this->loadModels();
        
        $this->loadHelpers();
        
        $this->setHelpTexts();
        
        $this->setMiscellaneous();
        
        $this->setPaths();

        $this->setUrls();
        
        $this->checkModuleActivationStatus();

    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        $this->setLastVisitedPage();
        
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
        
        return $this->appName;
    
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
     * Assigns basic Smarty variables and renders the page
     *
     * @access     public
     */
    public function printPage ()
    {
        
        $this->setBreadcrumbs();
        
        $this->smarty->assign('debugMode', $this->debugMode);
        
        $this->smarty->assign('session', $_SESSION);

        $this->smarty->assign('baseUrl', $this->baseUrl);
        $this->smarty->assign('controllerPublicName', $this->controllerPublicName);
        
        $this->smarty->assign('rnd', $this->getRandomValue());
        $this->smarty->assign('breadcrumbs', $this->getBreadcrumbs());
        $this->smarty->assign('errors', $this->getErrors());
        $this->smarty->assign('messages', $this->getMessages());
        $this->smarty->assign('helpTexts', $this->getHelpTexts());

		if (isset($this->cssToLoad)) {
	        $this->smarty->assign('cssToLoad', $this->cssToLoad);
    	}

		if (isset($this->jsToLoad)) {
	        $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);
    	}
    
        $this->smarty->assign('app', $this->generalSettings['app']);
        $this->smarty->assign('pageName', $this->getPageName());

		if (isset($_SESSION['user']) && !$_SESSION['user']['_said_welcome']) {
		
			$msg =
				sprintf(
					($_SESSION['user']['logins'] <=1 ? _('Welcome, %s.') : _('Welcome back, %s.')),
					$_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']
				);

	        $this->smarty->assign('welcomeMessage', $msg);
			
			$_SESSION['user']['_said_welcome'] = true;

		}

        $this->smarty->display(strtolower($this->getViewName() . '.tpl'));
    
    }



    /**
     * Redirects the user to another page (and avoids circular redirection)
     *
     * @param      string    $url    url to redirect to; can be false, in which case HTTP_REFERER is used
     * @access     public
     */
    public function redirect ($url = false)
    {
        
        if (!$url) {
            
            $url = $_SERVER['HTTP_REFERER'];
        
        }
        
        if (basename($url) == $url) {
            
            $circular = (basename($this->_fullPath) == $url);
        
        } else {

            $circular = ($this->_fullPath == $url) || ($this->_fullPathRelative == $url);

        }
        
        if ($url && !$circular) {
            
            header('Location:' . $url);
            
            die();
        
        }
    
    }



    /**
     * Adds an error to the class's stack of errors stored in class variable 'errors'
     *
     * @param      string or arrayu    $error    the error(s)
     * @access     public
     */
    public function addError ($error)
    {

        if (!$error) return;

        if (!is_array($error)) {
            
            $this->errors[] = $error;
        
        } else {
            
            foreach ($error as $key => $val) {
                
                $this->errors[] = $val;
            
            }
        
        }
    
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



    /**
     * Adds a message to the class's stack of messages stored in class variable 'messages'
     *
     * @param      type    $message    the message
     * @access     public
     */
    public function addMessage ($message)
    {
        
        $this->messages[] = $message;
    
    }



    /**
     * Returns the class's stack of messages stored in class variable 'messages'
     *
     * @return     array    stack of messages
     * @access     public
     */
    public function getMessages ()
    {
        
        return $this->messages;
    
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
    public function getCurrentUserId ()
    {
        
        return isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    
    }



    /**
     * Returns the projects the current user has been assigned to
     *
     * @return     array    array of project's id's, names and user's active states
     * @access     public
     */
    public function getCurrentUserProjects ()
    {
        
        foreach ((array) $_SESSION['user']['_roles'] as $key => $val) {
            
            $r = array(
                'id' => $val['project_id'], 
                'name' => $val['project_name'],
                'active' => $val['active']
            );
            
            if (!isset($cup) || !in_array($r, (array) $cup)) {
                
                $cup[] = $r;
            
            }
        
        }
        
        return $cup;
    
    }


    /**
     * Sets the active project's id as class variable
     *
     * @param      integer    $id    new active project's id
     * @access     public
     */
    public function setCurrentProjectId ($id)
    {
        
        $_SESSION['project']['id'] = $id;
  
        $this->models->ProjectRoleUser->update(
			array(
                'last_project_select' => 'now()', 
                'project_selects' => 'project_selects+1'
			),
            array(
				'user_id' => $this->getCurrentUserId(),
				'project_id' => $this->getCurrentProjectId(),
            )
        );

    }



    /**
     * Returns the active project's id class variable
     *
     * @return     integer    active project's id
     * @access     public
     */
    public function getCurrentProjectId ()
    {
        
        return isset($_SESSION['project']['id']) ? $_SESSION['project']['id'] : null;
    
    }



    /**
     * Sets the active project's name as a session variable (for display purposes)
     *
     * @access     public
     */
    public function setCurrentProjectData ($data)
    {
        
        $_SESSION['project'] = $data;

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
        
        //$d = (array) $_SESSION['user']['_roles'];
		foreach((array) $_SESSION['user']['_roles'] as $key => $val){

			if ($val['active']=='1') $d[] = $val;

		}
		
        // if user has no roles, do nothing
        if (count($d) == 0) return;
            
        // if user has only one role, set the corresponding project as the active project
        if (count($d) == 1) {
            
            $this->setCurrentProjectId($d[0]['project_id']);
        
        } else {
        // new plan: if user has more than one project assigned, he has to choose himself

            return;

			// old plan: if user has more roles, set the project in which he has the lowest role_id as the active project
			// (this assumes that the roles with the most permissions have the lowest ids)
            
            $t = false;
            
            foreach ((array) $d as $key => $val) {
                
                if (!$t || $val['role_id'] < $t) {
                    
                    $t = $val['role_id'];
                    
                    $p = $val['project_id'];
                
                }
            
            }
            
            $this->setCurrentProjectId($p);
        
        }
        
        $this->setCurrentProjectData($this->models->Project->get($this->getCurrentProjectId()));

    }



    /**
     * Returns the page to redirect to after logging in
     *
     * @return     string    path if page to redirect to
     * @access     public
     */
    public function getLoginStartPage ()
    {

        if (!empty($_SESSION['login_start_page'])) {
            
            return $_SESSION['login_start_page'];
        
        } else {

            if ($_SESSION["user"]["_number_of_projects"]==1) {

                return $this->baseUrl . $this->getAppName() . '/' . $this->getAppName() . $this->generalSettings['controllerIndexNameExtension'];
    
            } else {

                return $this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject'];

            }    
        }
    
    }


    /**
     * Checks whether a user is logged in
     *
     * @return     boolean        logged in or not
     * @access     public
     */
    public function isUserLoggedIn ()
    {
        
        return (!empty($_SESSION['user']));
    
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
    public function checkAuthorisation ()
    {
        
        // check if user is logged in, otherwise redirect to login page
        if ($this->isUserLoggedIn()) {
            
            // check if there is an active project, otherwise redirect to choose project page
            if ($this->getCurrentProjectId()) {
                
                // check if the user is authorised for the combination of current page / current project
                if ($this->isUserAuthorisedForProjectPage()) {
                    
                    return true;
                
                } else {
                    
                    $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);
                    
                    /*
                            user is not authorized and redirected to the index.page; 
                            if he already *is* on the index.page (and not authorized for that),
                            he is logged out to avoid circular reference.
                        */
                    if ($this->getViewName() == 'Index') {
                        
                        $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['logout']);
                    
                    } else {
                        
                        $this->redirect('index.php');
                    
                    }
                
                }
            
            } else {
            
                $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject']);
            
            }
        
        } else {
            
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
        
        foreach ((array) $this->getCurrentUserProjects() as $key => $val) {
            
            if ($val['id'] == $id && $val['active'] == '1')
                return true;
        
        }
        
        return false;
    
    }



    /**
     * Perfoms a usort, using user defined sort by-field, sort direction and case-sensitivity
     *
     * @param array    $array    array to sort
     * @param array    $sortBy    array to array of key, direction and case-sensitivity
     * @access     public
     */
    public function customSortArray (&$array, $sortBy)
    {
        
        if (!isset($array))
            return;
        
        $this->setSortField($sortBy['key']);
        
        $this->setSortDirection($sortBy['dir']);
        
        $this->setSortCaseSensitivity($sortBy['case']);
        
        usort($array, array(
            $this, 
            'doCustomSortArray'
        ));
    
    }



    /**
     * Returns the default save path for file uploads
     *
     * @return string    path
     * @access     public
     */
    public function getDefaultImageUploadDir ()
    {
        
        return isset($_SESSION['project']['paths']['uploads_media']) ? $_SESSION['project']['paths']['uploads_media'] : null;
    
    }



    /**
     * Returns the default save path for project images
     *
     * @return string    path
     * @access     public
     */
    public function getProjectsMediaStorageDir ()
    {
        
        return isset($_SESSION['project']['paths']['project_media']) ? $_SESSION['project']['paths']['project_media'] : null;
    
    }


    /**
     * Returns the default save path for project thumbs
     *
     * @return string    path
     * @access     public
     */
    public function getProjectsThumbsStorageDir ()
    {
        
        return isset($_SESSION['project']['paths']['project_thumbs']) ? $_SESSION['project']['paths']['project_thumbs'] : null;
    
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
     *
     * @return boolean    is resubmit or not
     * @access     public
     */
    public function isFormResubmit ()
    {
        
        $result = false;
        
        if (isset($this->requestData['rnd']) && isset($_SESSION['system']['last_rnd']) && ($_SESSION['system']['last_rnd'] == $this->requestData['rnd']))
            $result = true;
        
        $_SESSION['system']['last_rnd'] = isset($this->requestData['rnd']) ? $this->requestData['rnd'] : null;
        
        return $result;
    
    }



    /**
     * Returns the address of the root index for someone who is logged in
     *
     * @access     public
     * @ return    string    url
     */
    public function getLoggedInMainIndex ()
    {
        
        return $this->baseUrl . $this->appName . '/admin-index.php';
    
    }



    public function setBreadcrumbIncludeReferer ($value = true)
    {
        
        $this->breadcrumbIncludeReferer = $value;
    
    }



    public function checkModuleActivationStatus ()
    {
        
        if ($this->getModuleActivationStatus() == -1) {
            
            $_SESSION['system']['last_module_name'] = $this->controllerPublicName;
            
            $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['moduleNotPresent']);
        
        }
    
    }
	
	public function setExcludeFromReferer($state)
	{
	
		$this->excludeFromReferer = $state;
	
	}

    private function loadControllerConfig()
    {

        $t = 'getControllerSettings'.$this->controllerBaseName;

        if (method_exists($this->config,$t)) {

            $this->controllerSettings = $this->config->$t();

        } else {

            $this->controllerSettings = false;

        }

    }

    private function getModuleActivationStatus ()
    {
        
        // if a controller has no module id, it is accessible at all times
        if (!isset($this->controllerModuleId))
            return 1;
        
        $mp = $this->models->ModuleProject->get(array(
            'project_id' => $this->getCurrentProjectId(), 
            'module_id' => $this->controllerModuleId
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
        
        session_start();

        /* DEBUG */        
        $_SESSION['system']['server_addr'] = $_SERVER['SERVER_ADDR'];

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

        $dirnames = explode('/',$path['dirname']);
        
        $this->baseUrl = '../';

        for($i=count((array)$dirnames)-1;$i>=1;$i--) {

            if (strtolower($dirnames[$i])==$this->appName) {
                if (isset($dirnames[$i+2]))
                    $this->controllerBaseName = strtolower($dirnames[$i+2]);
                break ;
            }
            
            $this->baseUrl .= '../';

        }

        if ($path['filename']) $this->_viewName = $path['filename'];

        $this->_fullPathRelative = $this->baseUrl.$this->appName.'/views/'.$this->controllerBaseName.'/'.$this->_viewName .'.php';

    }

    /**
     * Sets general Smarty variables (paths, compilder directives)
     *
     * @access     private
     */
    private function setSmarty ()
    {
        
        $this->_smartySettings = $this->config->getSmartySettings();
        
        $this->smarty = new Smarty();
        
        /* DEBUG */
        $this->smarty->force_compile = true;
        
        $this->smarty->template_dir = $this->_smartySettings['dir_template'] . '/' . $this->getControllerBaseName() . '/';

        $this->smarty->compile_dir = $this->_smartySettings['dir_compile'];
        $this->smarty->cache_dir = $this->_smartySettings['dir_cache'];
        $this->smarty->config_dir = $this->_smartySettings['dir_config'];
        $this->smarty->caching = $this->_smartySettings['caching'];
        $this->smarty->compile_check = $this->_smartySettings['compile_check'];
    
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
				
				if (get_magic_quotes_gpc()) {

					if (is_array($val)) {

						foreach ((array) $val as $key2 => $val2) {

							$this->requestData[$key][$key2] = stripslashes($val2);

						}

					} else {

						$this->requestData[$key] = stripslashes($val);

					}				
				}
	
			}

		}

        $this->requestDataFiles = false;

        foreach ((array) $_FILES as $key => $val) {
            
            if (isset($val['size']) && $val['size'] > 0)
                $this->requestDataFiles[] = $val;
        
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
        
        $d = array_unique(array_merge((array) $this->usedModelsBase, (array) $this->usedModels));
        
        foreach ((array) $d as $key) {
            
            if (file_exists(dirname(__FILE__) . '/../models/' . $key . '.php')) {
                
                require_once (dirname(__FILE__) . '/../models/' . $key . '.php');
                
                $t = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                

                if (class_exists($t)) {
                    
                    $this->models->$t = new $t();
                    
                //echo $t.chr(10);
                }
            
            }
        
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
        
        if (!isset($this->usedHelpers)) return;

        foreach ((array) $this->usedHelpers as $key) {
            
            if (file_exists(dirname(__FILE__) . '/../helpers/' . $key . '.php')) {
                
                require_once (dirname(__FILE__) . '/../helpers/' . $key . '.php');
                
                $d = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                
                if (class_exists($d)) {
                    
                    $this->helpers->$d = new $d();
                
                }
            
            }
        
        }
    
    }



    /**
     * Loads the help texts for the current view into the class variable 'helpTexts'
     *
     * @access     private
     */
    private function setHelpTexts ()
    {
        
        $this->_helpTexts = $this->models->Helptext->get(array(
            'controller' => $this->getControllerBaseName() ? $this->getControllerBaseName() : '-', 
            'view' => $this->getViewName()
        ), false, 'show_order');
    
    }



    /**
     * Returns the class variable 'helptexts', which contains all the pages's help texts
     *
     * @return     array    array with all help texts
     * @access     private
     */
    private function getHelpTexts ()
    {
        
        return $this->_helpTexts;
    
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
            
            $_SESSION['project']['paths']['project_media'] = $this->generalSettings['directories']['mediaDirProject'] . '/' . sprintf('%04s', $p) . '/';

            $_SESSION['project']['paths']['project_thumbs'] = $this->generalSettings['directories']['mediaDirProject'] . '/' . sprintf('%04s', $p) . '/thumbs/';
            
            $_SESSION['project']['paths']['uploads_media'] = $this->generalSettings['directories']['mediaDirUpload'] . '/' . sprintf('%04s', $p) . '/';
            
            foreach ((array) $_SESSION['project']['paths'] as $key => $val) {
                
                if (!file_exists($val))
                    mkdir($val);
            
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

            $_SESSION['project']['urls']['project_media'] = $this->baseUrl . $this->getAppName() . '/media/project/'.sprintf('%04s', $p).'/';

            $_SESSION['project']['urls']['project_thumbs'] = $_SESSION['project']['urls']['project_media'].'thumbs/';

        }
    
    }

    /**
     * Initialises miscellaneous variables
     *
     * sets default filemask
     * sets default max upload size
     * sets a random value
     *
     * @access     private
     */
    private function setMiscellaneous ()
    {
        
        $this->setRandomValue();
    
    }



    /**
     * Sets a "custom http_referer", including the page's name, in the session
     *
     * @access     private
     */
    private function setLastVisitedPage ()
    {

		if (!$this->excludeFromReferer) {
		
			if (isset($_SESSION['system']['referer'])) {
        
				$_SESSION['system']['prev_referer']['url'] = $_SESSION['system']['referer']['url'];
				
				$_SESSION['system']['prev_referer']['name'] = $_SESSION['system']['referer']['name'];

			}
	
			$_SESSION['system']['referer']['url'] = $_SERVER['REQUEST_URI'];
			
			$_SESSION['system']['referer']['name'] = $this->getPageName();
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
			isset($_SESSION['system']['referer']) && 
			isset($_SESSION['system']['prev_referer']) && 
			$_SESSION['system']['referer']['url'] == $_SERVER['REQUEST_URI']
			) {
	
			$_SESSION['system']['referer'] = $_SESSION['system']['prev_referer'];
			
			unset($_SESSION['system']['prev_referer']);

		}

	}


    /**
     * Stores current page's name etc. in the session for easy access by smarty for js lock out-function
     *
     * @access     private
     */
    private function setSessionActivePageValues ()
    {
        
        $_SESSION['system']['active_page']['url'] = $this->_fullPath;
        
        if (isset($this->appName)) $_SESSION['system']['active_page']['appName'] = $this->appName;
        
        if (isset($this->controllerBaseName)) $_SESSION['system']['active_page']['controllerBaseName'] = $this->controllerBaseName;
        
        if (isset($this->_viewName)) $_SESSION['system']['active_page']['viewName'] = $this->_viewName;
    
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
        
        $_SESSION['login_start_page'] = $this->_fullPath;
    
    }



    /**
     * Create the breadcrumb trail
     *
     * @access     private
     */
    private function setBreadcrumbs ()
    {
        
        if (!isset($this->appName)) return;

        // root of each trail: "choose project" page
        $cp = $this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject'];

        $this->breadcrumbs[] = array(
            'name' => 'Projects', 
            'url' => $cp
        );
        
        if ($this->_fullPathRelative != $cp && isset($_SESSION['project']['title'])) {
            
            $this->breadcrumbs[] = array(
                'name' => $_SESSION['project']['title'], 
                'url' => $this->getLoggedInMainIndex()
            );
            
            if (!empty($this->controllerPublicName) && $this->_fullPath != $this->getLoggedInMainIndex()) {
                
                $curl = $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName;
                
                $this->breadcrumbs[] = array(
                    'name' => $this->controllerPublicName, 
                    'url' => $curl
                );
                
                if ($this->getViewName() != 'index') {
                    
                    // all views are on the same level, but sometimes we might want another level to the trail when 
                    // moving one view to the next, for logic's sake (for instance: taxon list -> edit taxon, two views
                    // that are on the same level, but are perceived by the user to be on different levels)
                    if ($this->breadcrumbIncludeReferer === true) {
                        
                        $this->breadcrumbs[] = array(
                            'name' => $_SESSION['system']['referer']['name'], 
                            'url' => $_SESSION['system']['referer']['url']
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
    
    }



    /**
     * Returns the breadcrumb trail
     *
     * @access     private
     * @return    array    breadcrumb trail: array of crumbname => crumbpath
     */
    private function getBreadcrumbs ()
    {
        
        if (isset($this->breadcrumbs))
            return $this->breadcrumbs;
    
    }



    /**
     * Checks whether a user is authorized to view/use a page within a project
     *
     * @return     boolean        authorized or not
     * @access     private
     */
    private function isUserAuthorisedForProjectPage ()
    {
        
		// is no controller base name is set, we are in /admin/admin-index.php which is the portal to the modules
		if ($this->getControllerBaseName()=='') return true;

        $d = $_SESSION['user']['_rights'][$this->getCurrentProjectId()][$this->getControllerBaseName()];
		
        foreach ((array) $d as $key => $val) {
            
            if ($val == '*' || $val == $this->getViewName()) {
                
                return true;
            
            }
        
        }
        
        return false;
    
    }



    /**
     * Sets key to sort by for doCustomSortArray
     *
     * @param string    name of the field to sort by
     * @access     private
     */
    private function setSortField ($field)
    {
        
        $this->sortField = $field;
    
    }



    /**
     * Returns key to sort by; called by doCustomSortArray
     *
     * @return string    name of the field to sort by; defaults to 'id'
     * @access     private
     */
    private function getSortField ()
    {
        
        return !empty($this->sortField) ? $this->sortField : 'id';
    
    }



    /**
     * Sets sort direction for doCustomSortArray
     *
     * @param string    $a    asc or desc
     * @access     private
     */
    private function setSortDirection ($dir)
    {
        
        $this->sortDirection = $dir;
    
    }



    /**
     * Returns direction to sort in; called by doCustomSortArray
     *
     * @return string    asc or desc
     * @access     private
     */
    private function getSortDirection ()
    {
        
        return !empty($this->sortDirection) ? $this->sortDirection : 'asc';
    
    }



    /**
     * Sets case sensitivity for doCustomSortArray
     *
     * @param string    $a    i(nsensitive) or s(ensitive)
     * @access     private
     */
    private function setSortCaseSensitivity ($sens)
    {
        
        $this->sortCaseSensitivity = $sens;
    
    }



    /**
     * Returns setting for case-sensitivity while sorting; called by doCustomSortArray
     *
     * @return string    i(nsensitive) or s(ensitive)
     * @access     private
     */
    private function getSortCaseSensitivity ()
    {
        
        return !empty($this->sortCaseSensitivity) ? $this->sortCaseSensitivity : 'i';
    
    }



    /**
     * Performs the actual usort; called by customSortArray
     *
     * @param array    $a    value of one array-element
     * @param array    $b    value of the other
     * @access     private
     */
    private function doCustomSortArray ($a, $b)
    {
        
        $f = $this->getSortField();
        
        $d = $this->getSortDirection();
        
        $c = $this->getSortCaseSensitivity();

        if (!isset($a[$f]) || !isset($b[$f]))
            return;
        
        if ($c != 's') {
            
            $a[$f] = strtolower($a[$f]);
            $b[$f] = strtolower($b[$f]);
        
        }
        
        return ($a[$f] > $b[$f] ? ($d == 'asc' ? 1 : -1) : ($a[$f] < $b[$f] ? ($d == 'asc' ? -1 : 1) : 0));
    
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


	public function setLocale ($locale=false)
	{

if (!file_exists($this->generalSettings['directories']['locale'].'/'.$locale.'/LC_MESSAGES/'.$this->getAppName().'.po'))
	die('.po file does not exist');

		$locale = ($locale ? $locale : $this->generalSettings['defaultLocale']);

		putenv('LC_ALL='.$locale);
		setlocale(LC_ALL,$locale);
		bindtextdomain($this->getAppName(), $this->generalSettings['directories']['locale']);
		textdomain($this->getAppName());

		$_SESSION['user']['currentLocale'] = $locale;

	}

}

?>