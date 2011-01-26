<?php
/*

- setRequestData needs recursion for the arrays (fuck knows why, though)
- getTaxonTree could do with some fancy session storing

*/


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
	public $requestDataFiles;
    public $data;
    public $randomValue;
    public $breadcrumbIncludeReferer;
    public $errors;
    public $messages;
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

    private $usedModelsBase = array(
        'helptext', 
        'project', 
        'language_project', 
        'module_project',
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
		
		$this->initLogging();

        $this->setNames();
		
        $this->loadControllerConfig();        
        
        $this->setPaths();

        $this->setUrls();
        
        $this->loadModels();

        $this->setHelpTexts();
        
        $this->setRandomValue();

		$this->setLanguages();
        
        $this->checkLastVisitedPage();
        
        $this->setSessionActivePageValues();
        
        $this->setSmartySettings();
        
        $this->setRequestData();

        $this->doLanguageChange();
        
        $this->checkModuleActivationStatus();

        $this->setProjectLanguages();

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
        
        return isset($cup) ? $cup : false;
    
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

		foreach((array)$data as $key => $val) {

	        $_SESSION['project'][$key] = $val;

		}

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

        $this->setCurrentProjectData($this->models->Project->_get(array('id' => $this->getCurrentProjectId())));

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

                return
					$this->baseUrl.
					$this->getAppName().'/'.
					$this->getAppName().
					$this->generalSettings['controllerIndexNameExtension'];
    
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
						if he already *is* on the index.page (and not authorized to be there),
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
     * Returns the active users role in the current project
     *
     * @return     array    id and name of the role
     * @access     public
     */
    public function getCurrentUserCurrentRole ()
    {

		if (isset($_SESSION['user']['currentRole'])) return $_SESSION['user']['currentRole'];

		$d = $this->getCurrentProjectId();
		$r = false;
        
		foreach((array)$_SESSION['user']['_roles'] as $key => $val) {

			if ($val['project_id']==$d) {
			
				$r['role_id'] = $val['role_id'];
				$r['role_name'] = $val['role_name'];
				break;

			}

		}
		
		$_SESSION['user']['currentRole'] = $r;
		
        return $r;

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
        
        usort($array,
			array(
				$this, 
				'doCustomSortArray'
        	)
		);
    
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
     * the old value of last_rnd is set on destruct.
	 *
     * @return boolean    is resubmit or not
     * @access     public
     */
    public function isFormResubmit ()
    {
        
        $result = false;

        if (
			isset($this->requestData['rnd']) && 
			isset($_SESSION['system']['last_rnd']) && 
			($_SESSION['system']['last_rnd'] == $this->requestData['rnd'])
		)
            $result = true;

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


	/**
	* Allows to addition of an extra step in the breadcrumb trail *before* the current page
	*
	*  example (called at the beginning of an *action() function):
    *
	*    $this->setBreadcrumbIncludeReferer(
    *      array(
    *         'name' => _('Name'), 
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

	public function setNoResubmitvalReset($state)
	{
	
		$this->noResubmitvalReset = $state;
	
	}
	
	public function log($msg,$severity=0)
	{
	
		if (is_array($msg)) {
		
			$d = '';

			foreach($msg as $key => $val) {
			
				$d .= $key .'=>'. $val .', ';
			
			}
			
			$msg = trim($d,' ,');

		}

		if (!@$this->helpers->LoggingHelper->write('('.$this->getCurrentProjectId().') '.$msg,$severity))
			trigger_error(_('Logging not initialised'), E_USER_ERROR);

	}

	public function setLocale ($language=false)
	{

		$language = $language ? $language :  $this->generalSettings['defaultLanguage'];

		if (isset($_SESSION['user']['currentLanguage']) && $language == $_SESSION['user']['currentLanguage']) return;

		$l = $this->models->Language->_get(array('id' => array('language'=> $language)));

		if (count((array)$l)==0) { 

			$this->log('Tried to switch to illegal language "'.$language.'"',1);
			
			return;

		}

		putenv('LC_ALL='.$l[0]['language']);

		if (!setlocale(LC_ALL,$l[0]['locale_lin'])) {

			if (!setlocale(LC_ALL,$l[0]['locale_win'])) { 

				$this->log('Failed attempt to set locale "'.$l[0]['locale_lin'].'" / "'.$l[0]['locale_win'].'"',1);

				return;

			}

		} 

		setlocale(LC_ALL,$l[0]['locale_win']);

		bindtextdomain($this->getAppName(), $this->generalSettings['directories']['locale']);			

		bind_textdomain_codeset($this->getAppName(), 'UTF-8');

		textdomain($this->getAppName());

		$_SESSION['user']['currentLanguage'] = $language;

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

				if (substr($key,0,2)=='_s' && !empty($val)) {

					$c = preg_replace('/\%s/',$val,$c,1);

				}

			}
			
		}
		
		return $c;
	
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

    /**
     * Retrieves all taxa in the form of a recursive array based om parent-child relations (the "tree")
     *
     * function at the same time maintains a second, non-recursive list of taxa ($this->treeList)
     *
     * @param      array    $params    parameters for tree formatting
     * @access     public
     */
	public function getTaxonTree($params=null) 
	{
	
		return $this->_getTaxonTree($params);
	
	}

    /**
     * Catches and saves uploaded files
     *
     * @access     public
     */
	public function getUploadedFiles() 
	{

		if (
			isset($this->helpers->FileUploadHelper) &&
			isset($this->controllerSettings['media']['allowedFormats']) &&
			isset($this->requestDataFiles)
		) {

			$this->helpers->FileUploadHelper->setLegalMimeTypes($this->controllerSettings['media']['allowedFormats']);
			$this->helpers->FileUploadHelper->setTempDir($this->getDefaultImageUploadDir());
			$this->helpers->FileUploadHelper->setStorageDir($this->getProjectsMediaStorageDir());
			$this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);
	
			$this->addError($this->helpers->FileUploadHelper->getErrors());

			return $this->helpers->FileUploadHelper->getResult();

		}

	}				
				
    private function _getTaxonTree($params) 
    {

		// the parent_id to start with
		$pId = isset($params['pId']) ? $params['pId'] : null;
		// the current level of depth in the tree
		$level = isset($params['level']) ? $params['level'] : 0;
		// a specific rank_id to stop the recursion; taxa below this rank are omitted from the tree
		$stopAtRankId = isset($params['stopAtRankId']) ? $params['stopAtRankId'] : null;
		// taxa without a parent_id that are not of the uppermost rank are orphans; these can be excluded from the tree
		$includeOrphans = isset($params['includeOrphans']) ? $params['includeOrphans'] : true;

		// get all ranks defined within the project	
		$pr = $this->getProjectRanks();

		// $this->treeList an additional non-recursive list of taxa
		if ($level==0) unset($this->treeList);

		// setting the parameters for the taxon search
		$id['project_id'] = $this->getCurrentProjectId();

		if ($pId === null) {

			$id['parent_id is'] = $pId;

		} else {

			$id['parent_id'] = $pId;

		}

		// decide whether or not to include orphans, taxa with no parent_id that are not the topmost taxon (which is usually 'kingdom')
		if ($pId === null && $includeOrphans === false) {

			$id['rank_id'] = $pr[0]['id'];

		}

		// get the child taxa of the current parent
        $t = $this->models->Taxon->_get(
				array(
					'id' =>  $id,
					'order' => 'taxon_order'
				)
			);

        foreach((array)$t as $key => $val) {

			// for each taxon, look whether they a) belong to the lower taxa, and b) can be the endpoint of the key
			foreach((array)$pr as $rankkey => $rank) {

				if ($rank['id']==$val['rank_id']) {

					$val['lower_taxon'] = $rank['lower_taxon'];

					$val['keypath_endpoint'] = $rank['keypath_endpoint'];

					break;	

				}

			}

			// level is effectively the recursive depth of the taxon within the tree
			$val['level'] = $level;

			// count taxa on the same level
			$val['sibling_count'] = count((array)$t);

			// sibling_pos reflects the position amongst taxa on the same level
			$val['sibling_pos'] = ($key==0 ? 'first' : ($key==count((array)$t)-1 ? 'last' : '-' ));

			// fill the treelist (which is a global var)
            $this->treeList[$val['id']] = $val;

			$t[$key]['level'] = $level;
			
			// and call the next recursion for each of the children
			if (!isset($stopAtRankId) || (isset($stopAtRankId) && $stopAtRankId!=$val['rank_id'])) {

				$children = $this->getTaxonTree(
					array(
						'pId' => $val['id'],
						'level' => $level+1,
						'stopAtRankId' => $stopAtRankId
					)
				);

	            $t[$key]['children_count'] = 
					$this->treeList[$val['id']]['children_count'] = 
					isset($children) ? count((array)$children) : 0;
				
				$t[$key]['children'] = $children;

			}


        }

        return $t;

    }

	public function getProjectRanks($params=false)
	{

		$includeLanguageLabels = isset($params['includeLanguageLabels']) ? $params['includeLanguageLabels'] : false;
		$lowerTaxonOnly = isset($params['lowerTaxonOnly']) ? $params['lowerTaxonOnly'] : false;
		$forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : false;
		$keypathEndpoint = isset($params['keypathEndpoint']) ? $params['keypathEndpoint'] : false;
		$idsAsIndex = isset($params['idsAsIndex']) ? $params['idsAsIndex'] : false;

		if (!$forceLookup) {

			if (
				!isset($_SESSION['project']['ranks']['includeLanguageLabels']) || 
				$_SESSION['project']['ranks']['includeLanguageLabels']!=$includeLanguageLabels ||
				!isset($_SESSION['project']['ranks']['lowerTaxonOnly']) || 
				$_SESSION['project']['ranks']['lowerTaxonOnly']!=$lowerTaxonOnly ||
				!isset($_SESSION['project']['ranks']['keypathEndpoint']) || 
				$_SESSION['project']['ranks']['keypathEndpoint']!=$keypathEndpoint ||
				!isset($_SESSION['project']['ranks']['idsAsIndex']) || 
				$_SESSION['project']['ranks']['idsAsIndex']!=$idsAsIndex
				)

				$forceLookup = true;

		}

		$_SESSION['project']['ranks']['includeLanguageLabels'] = $includeLanguageLabels;
		$_SESSION['project']['ranks']['lowerTaxonOnly'] = $lowerTaxonOnly;
		$_SESSION['project']['ranks']['keypathEndpoint'] = $keypathEndpoint;
		$_SESSION['project']['ranks']['idsAsIndex'] = $idsAsIndex;

		if (!isset($_SESSION['project']['ranks']['projectRanks']) || $forceLookup) {

			if ($keypathEndpoint)
				$d = array('project_id' => $this->getCurrentProjectId(),'keypath_endpoint' => 1);
			elseif ($lowerTaxonOnly)
				$d = array('project_id' => $this->getCurrentProjectId(),'lower_taxon' => 1);
			else
				$d = array('project_id' => $this->getCurrentProjectId());

			$p = array('id' => $d,'order' => 'rank_id');
			
			if ($idsAsIndex) {

				$p['fieldAsIndex'] = 'id';

			}

			$pr = $this->models->ProjectRank->_get($p);

			foreach((array)$pr as $rankkey => $rank) {
	
				$r = $this->models->Rank->_get(array('id' => $rank['rank_id']));
	
				$pr[$rankkey]['rank'] = $r['rank'];
	
				$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];
				
				if ($includeLanguageLabels) {
	
					foreach((array)$_SESSION['project']['languages'] as $langaugekey => $language) {
		
						$lpr = $this->models->LabelProjectRank->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(),
									'project_rank_id' => $rank['id'],
									'language_id' => $language['language_id']
								),
								'columns' => 'label'
							)
						);
						
						$pr[$rankkey]['labels'][$language['language_id']] = $lpr[0]['label'];
			
					}
	
				}
	
			}
			
			$_SESSION['project']['ranks']['projectRanks'] = $pr;
			
		}

		return $_SESSION['project']['ranks']['projectRanks'];

	}

	public function getAllLanguages()
    {

		/*
        $languages = array_merge(
			$this->models->Language->_get(array('id' => 'select * from %table% where show_order is not null order by show_order asc')), 
	        $this->models->Language->_get(array('id' => 'select * from %table% where show_order is null order by language asc'))
		);
		*/
		
//		unset($_SESSION['project']['system']['languages']);
		
		if (!isset($_SESSION['project']['system']['languages'])) {

			$_SESSION['project']['system']['languages'] = $this->models->Language->_get(array('id' => '*','fieldAsIndex'=>'id'));

		}

		return $_SESSION['project']['system']['languages'];

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

        if (isset($list)) $_SESSION['project']['languageList'] = $list;
		
    }
	
	public function rHasVal($var,$val=null)
	{

		if (isset($val))
			return isset($this->requestData[$var]) && !empty($this->requestData[$var]) && $this->requestData[$var] == $val;
		else
			return isset($this->requestData[$var]) && !empty($this->requestData[$var]);
	
	}

	public function rHasId()
	{
	
		return $this->rHasVal('id');
	
	}


    /**
     * Set a temporary controller base name, different from the current one
     *
     * @access    public
     * @param     string  $controllerBaseName  masking controller base name
     * @param     string  $controllerPublicName  masking controller public name
     */
	public function setControllerMask($controllerBaseName,$controllerPublicName)
	{

		$this->controllerBaseNameMask = $controllerBaseName;

		$this->controllerPublicNameMask = $controllerPublicName;

	}


	public function maskAsHigherTaxa()
	{

		if (isset($_SESSION['system']['highertaxa']) && $_SESSION['system']['highertaxa']===true) {
		// "abusing" this controller for the higher taxa

			$this->setControllerMask('highertaxa','Higher taxa');
			
			return true;

		} else {

			return false;

		}

	}


    /**
     * Assigns basic Smarty variables
     *
     * @access     public
     */
    private function preparePage()
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
        $this->smarty->assign('app', $this->generalSettings['app']);
        $this->smarty->assign('pageName', $this->getPageName());

        $this->smarty->assign('uiLanguages', $this->uiLanguages);
        $this->smarty->assign('uiCurrentLanguage', $this->getCurrentUiLanguage());
        $this->smarty->assign('isMultiLingual', $this->isMultiLingual);

		if (isset($this->cssToLoad)) {
	        $this->smarty->assign('cssToLoad', $this->cssToLoad);
    	}

		if (isset($this->jsToLoad)) {
	        $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);
    	}
    
		if (isset($_SESSION['user']) && !$_SESSION['user']['_said_welcome']) {
		
			$msg =
				sprintf(
					($_SESSION['user']['logins'] <=1 ? _('Welcome, %s.') : _('Welcome back, %s.')),
					$_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']
				);

	        $this->smarty->assign('welcomeMessage', $msg);
			
			$_SESSION['user']['_said_welcome'] = true;

		}

    }


	private function getCurrentUiLanguage()
	{

		return (isset($_SESSION['user']['currentLanguage']) ? 
			$_SESSION['user']['currentLanguage'] : 
			$this->uiLanguages[$this->uiDefaultLanguage]
		);

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
        
        $mp = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->controllerModuleId
				)
			)
		);
        
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

        if (empty($this->appName)) $this->log('No application name set',2);
        if (empty($this->_viewName)) $this->log('No view name set',2);
		if (empty($this->controllerBaseName)) $this->log('No controller basename set',2);
		if (empty($this->baseUrl)) $this->log('No base URL set',2);
        if (empty($this->_fullPath)) $this->log('No full path set',2);
        if (empty($this->_fullPathRelative)) $this->log('No relative full path set',2);

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


    private function doLanguageChange ($unsetRequestVar=true)
    {

		if ($this->isMultiLingual) {

			if (isset($this->requestData['uiLang'])) {
			
				$this->setLocale($this->requestData['uiLang']);

			}

		} else {

			$this->log('Attempt to set language '.$this->requestData['uiLang'].' for non-mulitlanguage page',1);

		}

		if ($unsetRequestVar) {

			unset($this->requestData['uiLang']);

			if (empty($this->requestData)) {

				unset($this->requestData);

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


	private function setLanguages()
	{

		$this->uiLanguages = $this->generalSettings['uiLanguages'];

		$this->uiDefaultLanguage = $this->generalSettings['uiDefaultLanguage'];

	}


    /**
     * Loads the help texts for the current view into the class variable 'helpTexts'
     *
     * @access     private
     */
    private function setHelpTexts ()
    {
        
        $this->_helpTexts = $this->models->Helptext->_get(
			array(
				'id' => array(
					'controller' => $this->getControllerBaseName() ? $this->getControllerBaseName() : '-', 
					'view' => $this->getViewName()
				),
				'order' => 'show_order'
			)
		);
    
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
                
                if (!file_exists($val)) {

                    mkdir($val);

					$this->log('Created directory "'.$val.'"');

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

            $_SESSION['project']['urls']['project_media'] = $this->baseUrl . $this->getAppName() . '/media/project/'.sprintf('%04s', $p).'/';

            $_SESSION['project']['urls']['project_thumbs'] = $_SESSION['project']['urls']['project_media'].'thumbs/';

        }

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
			
			// controller name can be overridden
			$controllerPublicName = ($this->controllerPublicNameMask ? $this->controllerPublicNameMask : $this->controllerPublicName);
			$controllerBaseName = ($this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->controllerBaseName);
            
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

		// is no controller base name is set, we are in /admin/admin-index.php which is the portal to the modules
		if ($controllerBaseName=='') return true;

		if (isset($_SESSION['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName])) {

			$d = $_SESSION['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName];
			
			foreach ((array) $d as $key => $val) {
				
				if ($val == '*' || $val == $this->getViewName()) {
					
					return true;
				
				}
			
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
	
	private function _doMultiArrayFind($var)
	{

		return (isset($var[$this->findField]) && $var[$this->findField]==$this->findValue);

	}

	public function doMultiArrayFind($array,$field,$value)
	{

		if ($field==null || $value==null) return;

		$this->findField = $field;
		$this->findValue = $value;

		return array_filter($array,array($this,'_doMultiArrayFind'));

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
	        $_SESSION['system']['last_rnd'] = isset($this->requestData['rnd']) ? $this->requestData['rnd'] : null;

    }

}
