<?php

/*

check setLocale

*/


include_once (dirname(__FILE__) . "/../BaseClass.php");

include_once (dirname(__FILE__) . "/../../../smarty/Smarty.class.php");

class Controller extends BaseClass
{

    private $_smartySettings;
    private $_fullPath;
    private $_fullPathRelative;

    public $viewName;
    public $controllerBaseName;
    public $controllerBaseNameMask = false;
    public $pageName;
    public $controllerPublicName;
    public $controllerPublicNameMask = false;
    public $errors;
    public $messages;
	public $excludeFromReferer = false;
	public $noResubmitvalReset = false;
	public $showBackToSearch = true;
	public $storeHistory = true;

    private $usedModelsBase = array(
        'project', 
        'language_project', 
		'module',
        'module_project',
        'free_module_project',
		'language',
		'translate_me',
		'taxon',
		'project_rank',
		'label_project_rank',
		'rank'
    );

    private $usedHelpersBase = array(
		'logging_helper'
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

        $this->loadHelpers();
		
        $this->setNames();
		
		$this->initLogging();

        $this->loadControllerConfig();        
        
        $this->setUrls();
        
        $this->loadModels();

        $this->setSmartySettings();
        
        $this->setRequestData();

        $this->setProjectLanguages();
		
		$this->setCurrentLanguageId();

    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {

		$this->setBreadCrumb();

		session_write_close();

        parent::__destruct();
    
    }

	public function checkForProjectId ()
	{
	
		$d = $this->getCurrentProjectId();
		
		if ($d==null) $this->redirect($this->generalSettings['urlNoProjectId']);
	
	}


    /**
     * Makes a numeric show order into another list marker
     *
     * Used in the dich. key and in the search results, hence it's inclusion here
     *
     * @access     public
     */
	public function showOrderToMarker($showOrder)
	{
	
		return chr($showOrder+96);
	
	}

	private function getBreadcrumbs()
	{

		return isset($_SESSION['user']['breadcrumbs']) ? $_SESSION['user']['breadcrumbs'] : null;

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
     * Starts the user's session
     *
     * @access     private
     */
    private function startSession ()
    {

		session_name('lng-application');

        session_start();

        /* DEBUG */        
        $_SESSION['system']['server_addr'] = $_SERVER['SERVER_ADDR'];

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
     * Adds an error to the class's stack of errors stored in class variable 'errors'
     *
     * @param      string or array    $error    the error(s)
     * @access     public
     */
    public function addError ($error,$writeToLog=false)
    {

        if (!$error) return;

        if (!is_array($error)) {
            
            $this->errors[] = $error;

			if ($writeToLog!==false ) $this->log($error,$writeToLog);

        } else {
            
            foreach ($error as $key => $val) {
                
                $this->errors[] = $val;

				if ($writeToLog!==false ) $this->log($val,$writeToLog);
            
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
        
        return (count((array)$this->errors)>0);
    
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
     * Returns the current view's name
     *
     * @return     string    current view's name
     * @access     public
     */
    public function getViewName ()
    {
        
        return $this->viewName;
    
    }

    /**
     * Redirects the user to another page (and avoids circular redirection)
     *
     * @param      string    $url    url to redirect to; can be false, in which case HTTP_REFERER is used
     * @access     public
     */
    public function redirect ($url = false)
    {
        
        if (!$url && isset($_SERVER['HTTP_REFERER'])) {
            
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
     * Sets the active project's name as a session variable (for display purposes)
     *
     * @access     public
     */
    public function setCurrentProjectData ($data=null)
    {

		if ($data==null) {

			$id = $this->getCurrentProjectId();

			if (isset($id)) {

				$data = $this->models->Project->_get(array('id' => $id));

			} else {

				return;

			}

		}

		foreach((array)$data as $key => $val) {

	        $_SESSION['project'][$key] = $val;

		}
    }


	public function setProjectLanguages()
    {

        $lp = $this->models->LanguageProject->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'order' => 'def_language desc'
			)
		);
        
        foreach ((array) $lp as $key => $val) {
            
            $l = $this->models->Language->_get(array('id'=>$val['language_id']));
            
            $lp[$key]['language'] = $l['language'];
            $lp[$key]['direction'] = $l['direction'];
			$lp[$key]['iso2'] = $l['iso2'];
			$lp[$key]['iso3'] = $l['iso3'];
            
            if ($val['def_language'] == 1)
                $defaultLanguage = $val['language_id'];
        
			$list[$val['language_id']]= array('language'=>$l['language'],'direction'=>$l['direction']);
        }
        
        $_SESSION['project']['languages'] = $lp;

        if (isset($defaultLanguage)) $_SESSION['project']['default_language_id'] = $defaultLanguage;

//        if (isset($list)) $_SESSION['project']['languageList'] = $list;
		
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
 
    }


	public function didActiveLanguageChange()
	{

        return $_SESSION['user']['languageChanged'];

	}

	public function getCurrentLanguageId()
	{

        return $_SESSION['user']['activeLanguageId'];

	}

	public function getDefaultLanguageId()
	{

        return isset($_SESSION['project']['default_language_id']) ? $_SESSION['project']['default_language_id'] : null;

	}

	public function setCurrentLanguageId($id=null)
	{

		if ($id) {

			$_SESSION['user']['languageChanged'] = $_SESSION['user']['activeLanguageId'] != $id;

			$_SESSION['user']['activeLanguageId'] = $id;

		} else
		if ($this->rHasVal('languageId')) {

			$_SESSION['user']['languageChanged'] = $_SESSION['user']['activeLanguageId'] != $this->requestData['languageId'];

			$_SESSION['user']['activeLanguageId'] = $this->requestData['languageId'];

		} else {

			$_SESSION['user']['languageChanged'] = false;

		}
		
		if (!isset($_SESSION['user']['activeLanguageId'])) {

			$_SESSION['user']['activeLanguageId'] = $this->getDefaultLanguageId();
			$_SESSION['user']['languageChanged'] = true;

		}
		
		if (!isset($_SESSION['user']['languageChanged'])) $_SESSION['user']['languageChanged'] = true;
		
		unset($this->requestData['languageId']);

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
     * Returns the active project's id class variable
     *
     * @return     integer    active project's id
     * @access     public
     */
    public function getCurrentProjectId ()
    {

        return isset($_SESSION['project']['id']) ? $_SESSION['project']['id'] : null;
    
    }

	public function log($msg,$severity=0)
	{
	
		if (!isset($this->helpers->LoggingHelper)) return;

		if (is_array($msg)) {
		
			$d = '';

			foreach($msg as $key => $val) {
			
				$d .= $key .'=>'. $val .', ';
			
			}
			
			$msg = trim($d,' ,');

		}

		if (!$this->helpers->LoggingHelper->write('('.$this->getCurrentProjectId().') '.$msg,$severity))
			trigger_error(_('Logging not initialised'), E_USER_ERROR);

	}

    /**
     * Gettext wrapper, to be called from javascript (through the utilities controller)
     *
     * @access     public
     */
	public function javascriptTranslate($content)
	{

		if (empty($content)) return;

		/* DEBUG */
		$this->models->TranslateMe->save(
			array(
				'id' => null,
				'controller' => 'javascript',
				'content' => $content
			)
		);

		return _($content);

	}

	public function rHasVal($var,$val=null)
	{

		if ($val!==null) {

			return isset($this->requestData[$var]) && $this->requestData[$var] === $val;

		} else {

			return isset($this->requestData[$var]) && $this->requestData[$var]!=='';

		}

	}


	public function rHasId()
	{
	
		return $this->rHasVal('id');
	
	}

    /**
     * Assigns basic Smarty variables
     *
     * @access     public
     */
    private function preparePage()
    {
 
 		if (isset($_SESSION['project']['languages'])) $this->smarty->assign('languages',$_SESSION['project']['languages']);

 		$this->smarty->assign('currentLanguageId',$this->getCurrentLanguageId());

		$this->smarty->assign('menu',$this->getMainMenu());
 
//        $this->setBreadcrumbs();
        $this->smarty->assign('session', $_SESSION);
        $this->smarty->assign('requestData', $this->requestData);
        $this->smarty->assign('baseUrl', $this->baseUrl);
        $this->smarty->assign('controllerBaseName', $this->controllerBaseName);
        $this->smarty->assign('controllerPublicName', $this->controllerPublicName);
        $this->smarty->assign('breadcrumbs', $this->getBreadcrumbs());
        $this->smarty->assign('errors', $this->getErrors());
        $this->smarty->assign('messages', $this->getMessages());
        $this->smarty->assign('pageName', $this->getPageName());
        $this->smarty->assign('showBackToSearch', $this->showBackToSearch);

		if (isset($this->cssToLoad)) {
	        $this->smarty->assign('cssToLoad', $this->cssToLoad);
    	}

		if (isset($this->jsToLoad)) {
	        $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);
    	}

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
     * Gettext wrapper, to be called from a registered block function within Smarty
     *
	 * parametrization: {t _s1='one' _s2='two' _s3='three'}The 1st parameter is %s, the 2nd is %s and the 3nd %s.{/t}
	 *
     * @access     public
     */
	public function smartyTranslate($params, $content, &$smarty, &$repeat)
	{

		if (empty($content)) return;

		/* DEBUG */
		@$this->models->TranslateMe->save(
			array(
				'id' => null,
				'controller' => $this->getControllerBaseName(),
				'content' => $content
			)
		);

		$c = _($content);
	
		if (isset($params)) {

			foreach((array)$params as $key => $val) {

				if (substr($key,0,2)=='_s' && isset($val)) {

					$c = preg_replace('/\%s/',$val,$c,1);

				}

			}
			
		}
		
		return $c;
	
	}


	public function getMainMenu()
	{

		$modules = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'active' => 'y'
				), 
				'order' => 'module_id asc'
			)
		);

		foreach ((array) $modules as $key => $val) {
			
			$mp = $this->models->Module->_get(array('id'=>$val['module_id']));
			
			$modules[$key]['type'] = 'regular';
			$modules[$key]['icon'] = $mp['icon'];
			$modules[$key]['module'] = $mp['module'];
			$modules[$key]['controller'] = $mp['controller'];

		}

		$freeModules = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'active' => 'y'
				)
			)
		);
		
		foreach ((array) $freeModules as $key => $val) {

			$val['type'] = 'free';
			$modules[] = $val;

		}
		
		return $modules;

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

		if (!$p) return;

		if (isset($this->generalSettings['imageRootUrlOverride'])) {

			$_SESSION['project']['urls']['project_media'] = $this->generalSettings['imageRootUrlOverride'].sprintf('%04s', $p).'/';
	
			$_SESSION['project']['urls']['project_thumbs'] = $_SESSION['project']['urls']['project_media'].'thumbs/';

		} else {
	
			$_SESSION['project']['urls']['project_media'] = $this->baseUrl . $this->getAppName() . '/media/project/'.sprintf('%04s', $p).'/';

			$_SESSION['project']['urls']['project_thumbs'] = $_SESSION['project']['urls']['project_media'].'thumbs/';

		}

		$_SESSION['project']['urls']['project_css'] = $this->baseUrl . $this->getAppName() . '/style/';

		$_SESSION['project']['urls']['project_start'] =
			$this->baseUrl . $this->getAppName() . '/views/'.$this->generalSettings['defaultController'].'/';

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

        if ($path['filename']) $this->viewName = $path['filename'];

        $this->_fullPathRelative = $this->baseUrl.$this->appName.'/views/'.$this->controllerBaseName.'/'.$this->viewName .'.php';

        if (empty($this->appName)) $this->log('No application name set',2);
        if (empty($this->viewName)) $this->log('No view name set',2);
		if (empty($this->controllerBaseName)) $this->log('No controller basename set',2);
		if (empty($this->baseUrl)) $this->log('No base URL set',2);
        if (empty($this->_fullPath)) $this->log('No full path set',2);
        if (empty($this->_fullPathRelative)) $this->log('No relative full path set',2);

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

		if (isset($this->usedModelsBase) && isset($this->usedModels)) { 

	        $d = array_unique(array_merge((array) $this->usedModelsBase, (array) $this->usedModels));
        
		} elseif (isset($this->usedModelsBase)) { 

	        $d = $this->usedModelsBase;
        
		} elseif (isset($this->usedModels)) { 

	        $d = $this->usedModels;
        
		} else {

			return;

		}
		
        foreach ((array) $d as $key) {

            if (file_exists(dirname(__FILE__) . '/../models/' . $key . '.php')) {
                
                require_once (dirname(__FILE__) . '/../models/' . $key . '.php');
                
                $t = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                

                if (class_exists($t)) {
                    
                    $this->models->$t = new $t();
					
					if (isset($this->helpers->LoggingHelper)) $this->models->$t->setLogger($this->helpers->LoggingHelper);
                    
                //echo $t.chr(10);
                } else {

					$this->log('Attempted to initiate non-existing model class "'.$t.'"',2);

				}
            
            } else {

				$this->log('Attempted to load non-existing model file "'.$key.'"',2);

			}
        
        }
    
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
        
        $this->smarty->template_dir = $this->_smartySettings['dir_template'] . '/' . $this->getControllerBaseName() . '/';
        $this->smarty->compile_dir = $this->_smartySettings['dir_compile'];
        $this->smarty->cache_dir = $this->_smartySettings['dir_cache'];
        $this->smarty->config_dir = $this->_smartySettings['dir_config'];
        $this->smarty->caching = $this->_smartySettings['caching'];
        $this->smarty->compile_check = $this->_smartySettings['compile_check'];

		$this->smarty->register_block('t', array(&$this,'smartyTranslate'));

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
		
							}
		
						} else {
		
							$this->requestData[$key][$key2] = get_magic_quotes_gpc() ? stripslashes($val2) : $val2;
		
						}				

					}

				} else {

					$this->requestData[$key] = get_magic_quotes_gpc() ? stripslashes($val) : $val;

				}				
	
			}

		}

        foreach ((array)$_FILES as $key => $val) {

            if (isset($val['size']) && $val['size'] > 0) {

                $this->requestDataFiles[] = $val;
        
			}
		
        }

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
        
        if (isset($sortBy['key'])) $this->setSortField($sortBy['key']);
        
        if (isset($sortBy['dir'])) $this->setSortDirection($sortBy['dir']);
        
        if (isset($sortBy['case'])) $this->setSortCaseSensitivity($sortBy['case']);
        
        usort($array,
			array(
				$this, 
				'doCustomSortArray'
        	)
		);
    
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

		if (isset($this->usedHelpersBase) && isset($this->usedHelpers)) { 

	        $d = array_unique(array_merge((array) $this->usedHelpersBase, (array) $this->usedHelpers));
        
		} elseif (isset($this->usedHelpersBase)) { 

	        $d = $this->usedHelpersBase;
        
		} elseif (isset($this->usedHelpers)) { 

	        $d = $this->usedHelpers;
        
		} else {

			return;

		}

        foreach ((array) $d as $key) {
    
            if (file_exists(dirname(__FILE__) . '/../helpers/' . $key . '.php')) {
                
                require_once (dirname(__FILE__) . '/../helpers/' . $key . '.php');
                
                $d = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                
                if (class_exists($d)) {
                    
                    $this->helpers->$d = new $d();
                
                }
            
            }
        
        }
    
    }


	private function initLogging()
	{

		$fn = $this->getAppName() ? $this->getAppName() : 'general';

		$this->helpers->LoggingHelper->setLogFile($this->generalSettings['directories']['log'].'/'.$fn.'.log');

		$this->helpers->LoggingHelper->setLevel(0);

	}

	private function setBreadCrumb()
	{

		if (empty($this->pageName) || $this->storeHistory==false) return;

		$_SESSION['user']['breadcrumbs'][] = array(
			'name' => $this->pageName,
			'url' => $_SERVER['REQUEST_URI'],
			'data' => $this->requestData
		);
		
		$d = count((array)$_SESSION['user']['breadcrumbs']);

		if (isset($_SESSION['user']['breadcrumbs'][$d-2])) {
		
			if ($_SESSION['user']['breadcrumbs'][$d-2]==$_SESSION['user']['breadcrumbs'][$d-1]) {

				array_pop($_SESSION['user']['breadcrumbs']);
				
				$d--;

			}
		
		}

		if ($d>100) $_SESSION['user']['breadcrumbs'] = array_slice($_SESSION['user']['breadcrumbs'],$d-100);

	}

}