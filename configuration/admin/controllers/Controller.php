<?php

include_once (__DIR__ . "/../BaseClass.php");
include_once (__DIR__ . "/../../../vendor/autoload.php");
//include_once (__DIR__ . "/../../../smarty/Smarty.class.php");

include_once ('UserRightsController.php');
include_once ('TranslatorController.php');

class Controller extends BaseClass
{
	private $_smartySettings;
	private $_viewName;
	private $_fullPath;
	private $_fullPathRelative;
	private $_prevTreeId;
	private $_breadcrumbRootName;
	private $translator;
	public $useVariations=false;
	public $useRelated=false;
	public $tmp;
	public $smarty;
	public $requestData;
	public $requestDataFiles;
	public $data;
	public $randomValue;
	public $breadcrumbIncludeReferer;
	public $cronNextRun;

	public $errors=array();
	public $messages=array();
	public $warnings=array();

	public $controllerBaseName;
	public $controllerBaseNameMask=false;
	public $pageName;
	public $pageNameAltName;
	public $controllerPublicName;
	public $controllerPublicNameMask=false;
	public $sortField;
	public $sortDirection;
	public $sortCaseSensitivity;
	public $findField;
	public $findValue;
	public $baseUrl;
	public $excludeFromReferer=false;
	public $noResubmitvalReset=false;
	public $isMultiLingual=true;
	public $uiLanguages;
	public $uiDefaultLanguage;
	public $treeList;
	public $suppressProjectInBreadcrumbs;
	public $includeLocalMenu=true;
	public $printBreadcrumbs=true;
	public $wikiPageOverride;
	private $_adminMessageFadeOutDelay;
	private $_gitVars;
	private $_nameTypeIds=array();

	private $usedModelsBase = array(
		'activity_log',
		'free_modules_projects',
		'interface_texts',
		'interface_translations',
		'labels_projects_ranks',
		'languages',
		'languages_projects',
		'modules',
		'modules_projects',
		'projects',
		'projects_ranks',
		'projects_roles_users',
		'ranks',
		'roles',
		'taxa',
		'taxa_variations',
		'users_taxa',
		'users',
		'variations_labels',
		'name_types'
	);

    private $usedHelpersBase = array(
		'session_module_settings',
        'logging_helper',
		'log_changes',
		'custom_array_sort',
		'paginator',
        'paginator_with_links',
		'git'

    );

	protected $moduleSession;
	protected $baseSession;

	protected $_hybridMarker='×';
	protected $_hybridMarkerHtml='&#215;';
	protected $_formaMarker='f.';
	protected $_hybridMarker_graftChimaera='+';
	protected $_varietyMarker='var.';
	protected $_subspeciesMarker='subsp.';
	protected $_nothoInfixPrefix='notho';

			
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
        $this->setServerName();
        $this->setTimeZone();
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
        $this->activateBasicModules();
        $this->initUserRights();
        $this->setLanguages();
        $this->checkLastVisitedPage();
        $this->setSmartySettings();
        $this->setLanguageIdConstants();
        $this->setRankIdConstants();
		$this->setNameTypeIds();
        $this->setRequestData();
        $this->doLanguageChange();
        $this->checkModuleActivationStatus();
        $this->setProjectLanguages();
		$this->initTranslator();
        $this->setRandomValue();
		$this->setShowAutomaticHybridMarkers();
		$this->setShowAutomaticInfixes();
		$this->setAdminMessageFadeOutDelay();
        $this->setGitVars();
        $this->setCronNextRun();
    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct()
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
    public function getAppName()
    {
        return isset($this->appName) ? $this->appName : false;
    }

    /**
     * Returns the controller's base name
     *
     * @return     string    controller's base name
     * @access     public
     */
    public function getControllerBaseName()
    {
        return $this->controllerBaseName;
    }

    /**
     * Returns the current view's name
     *
     * @return     string    current view's name
     * @access     public
     */
    public function getViewName()
    {
        return $this->_viewName;
    }

    /**
     * Renders and displays the page
     *
     * @access     public
     */
    public function printPage($templateName = null)
    {
        $this->preparePage();

        $this->smarty->display(strtolower((!empty($templateName) ? $templateName : $this->getViewName()) . '.tpl'));
    }

    /**
     * Renders and returns the page
     *
     * @access     public
     */
    public function fetchPage($templateName = null)
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
    public function redirect($url = false)
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
    public function addError($error, $writeToLog = false)
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
    public function hasErrors()
    {
        return (count((array) $this->errors) > 0);
    }

    /**
     * Returns the class's stack of errors stored in class variable 'errors'
     *
     * @return     array    stack of errors
     * @access     public
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function clearErrors()
    {
        $this->errors = array();
    }

    /**
     * Adds a message to the class's stack of messages stored in class variable 'messages'
     *
     * @param      type    $message    the message
     * @access     public
     */
    public function addMessage($d)
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

    public function getMessages()
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

    public function hasWarnings()
    {
        return (count((array) $this->warnings) > 0);
    }

    /**
     * Sets the name of the current page, for display purposes, in a class variable 'pageName'.
     *
     * @param      string    $name    the page's name
     * @access     public
     */
    public function setPageName($name,$pageName=null)
    {
		$this->pageName=$name;
		$this->pageNameAltName=$pageName;
    }

    /**
     * Returns the name of the current page.
     *
     * @return     string    the page's name
     * @access     public
     */
    public function getPageName()
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
     * Returns the active project's id class variable
     *
     * @return     integer    active project's id
     * @access     public
     */
    public function getCurrentProjectId()
    {
        // This default here was null, now it is -1, but this would cause trouble with general settings
        // if this fix causes other problems, please refactor
        return isset($_SESSION['admin']['project']['id']) ? $_SESSION['admin']['project']['id'] : -1;
    }

    public function getCurrentProjectData()
    {
        return isset($_SESSION['admin']['project']) ? $_SESSION['admin']['project'] : null;
    }

    public function setCurrentUserRoleId()
    {
		$this->UserRights->setUserRoleId();
    }

    public function checkAuthorisation( )
    {
		if ( $this->UserRights->getCheckOnlyIfLoggedIn()===true )
		{
			if ( null==$this->getCurrentUserId() )
			{
				$this->redirect( '../users/login.php' );
				$_SESSION['admin']['user']['authorization_fail_message']='Not logged in';
				$_SESSION['admin']['user']['authorization_fail_page']="https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$_SESSION['admin']['user']['authorization_fail_add']="!getCheckOnlyIfLoggedIn";
				$this->redirect( '../users/login.php' );
				//$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);
			}
			else
			{
				unset( $_SESSION['admin']['user']['authorization_fail_message'] );
				unset( $_SESSION['admin']['user']['authorization_fail_page'] );
				unset( $_SESSION['admin']['user']['authorization_fail_add'] );
			}
		}
		else
		if ( !$this->UserRights->canAccessModule() )
		{
			if ( null==$this->getCurrentUserId() )
			{
				$this->redirect( '../users/login.php' );
			}
			else
			{
				$_SESSION['admin']['user']['authorization_fail_message']=$this->UserRights->getStatus();
				$_SESSION['admin']['user']['authorization_fail_page']="https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$_SESSION['admin']['user']['authorization_fail_add']="!canAccessModule";
				$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);
			}
		}
		else
		if ( !$this->UserRights->canManageItem() || !$this->UserRights->canPerformAction() || !$this->UserRights->hasAppropriateLevel() )
		{
			$_SESSION['admin']['user']['authorization_fail_message']=$this->UserRights->getStatus();
			$_SESSION['admin']['user']['authorization_fail_page']="https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			$d="";
			if ( !$this->UserRights->canManageItem() ) $d.="!canManageItem &&";
			if ( !$this->UserRights->canPerformAction() ) $d.="!canPerformAction &&";
			if ( !$this->UserRights->hasAppropriateLevel() ) $d.="!hasAppropriateLevel &&";
			$_SESSION['admin']['user']['authorization_fail_add']=rtrim($d,"& ");

			$this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);
		}
		else
		{
			unset( $_SESSION['admin']['user']['authorization_fail_message'] );
			unset( $_SESSION['admin']['user']['authorization_fail_page'] );
			unset( $_SESSION['admin']['user']['authorization_fail_add'] );
		}
    }

    public function getAuthorisationState()
    {
		return
			$this->UserRights->canAccessModule()==true &&
			$this->UserRights->canManageItem()==true &&
			$this->UserRights->canPerformAction()==true &&
			$this->UserRights->hasAppropriateLevel()==true;
	}

    public function getCRUDstates()
    {
		$d=array();
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$d['can_create']=$this->getAuthorisationState();
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );
		$d['can_read']=$this->getAuthorisationState();
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$d['can_update']=$this->getAuthorisationState();
		$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
		$d['can_delete']=$this->getAuthorisationState();
		return $d;
	}


    public function isCurrentUserSysAdmin ()
    {
		return $this->UserRights->isSysAdmin();
    }

    public function getProjectModules($params = null)
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
			$modules[$key]['show_in_menu'] = $mp['show_in_menu'];
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
		// REFAC2015
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

    public function getTaxonById( $id, $addNoMarkers=false )
    {
		$taxon=$this->models->ControllerModel->getTaxon( [ 'project_id'=>$this->getCurrentProjectId(),'taxon_id'=>$id,'type_id_valid_name'=>$this->getNameTypeId(PREDICATE_VALID_NAME) ] );
		
		if ( !empty($taxon['taxon']) )
		{
			$taxon['taxon_no_infix']=$taxon['taxon'];
			// Added check for parent_id, as otherwise we may end up in an eternal loop
			if (!$addNoMarkers && !empty($taxon['parent_id'])) {
				$taxon['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$taxon['taxon'],'base_rank_id'=>$taxon['base_rank_id'],'taxon_id'=>$taxon['id'],'parent_id'=>$taxon['parent_id'] ] );
			}
		}
		return $taxon;
    }

    public function getTaxonByName($name)
    {
		$taxon=$this->models->ControllerModel->getTaxon( [ 'project_id'=>$this->getCurrentProjectId(),'name'=>trim($name),'type_id_valid_name'=>$this->getNameTypeId(PREDICATE_VALID_NAME) ] );
		if ( !empty($taxon['taxon']) )
		{
			$taxon['taxon_no_infix']=$taxon['taxon'];
			$taxon['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$taxon['taxon'],'base_rank_id'=>$taxon['base_rank_id'],'taxon_id'=>$taxon['id'],'parent_id'=>$taxon['parent_id'] ] );
		}
		return $taxon;
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

    public function getPaginationWithPager ($items, $maxPerPage = 25)
    {
        $this->helpers->Paginator->setItemsPerPage($maxPerPage);
		$this->helpers->Paginator->setStart($this->rHasVal('start') ? $this->rGetVal('start') : 0);
		$this->helpers->Paginator->setItems($items);
		$this->helpers->Paginator->getItemsWithPrintedPager();

		return $this->helpers->Paginator->getItems();
    }

    public function createProject ($d)
    {
        $d['id'] = null;
        $d['sys_name'] = $d['title'];// . (isset($d['version']) ? ' v' . $d['version'] : $d['version']);

        $p = $this->models->Projects->save($d);

        return ($p) ? $this->models->Projects->getNewId() : false;
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
		if (!isset($p['changed']))
		{
			$b=serialize((isset($p['before']) ? $p['before'] : null));
			$a=serialize((isset($p['after']) ? $p['after'] : null));
			$p['changed']=md5($b)!=md5($a);
		}

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

		if (!(is_null($before) && is_null($after)) && $changed!==true)
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
	
	static function generateTaxonParentageId( $id ) 
	{
		return sprintf('%05s',$id);
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

    public function formatTaxon($p=null)
    {

		if (is_null($p))
			return;

		// switching between $p being an array of parameters (taxon, ranks, rankpos) and $p just being the taxon (which is an array in itself)
		if (isset($p['taxon']) && is_array($p['taxon'])) {
			$taxon=$p['taxon'];
		} else {
			$taxon=$p;
		}

		$ranks=isset($p['ranks']) ? $p['ranks'] : null;
		$rankpos=(isset($p['rankpos']) && in_array($p['rankpos'],array('pre','post','none')) ? $p['rankpos'] : null);

		if (empty($taxon))
			return;

		$addInfixes = $this->getShowAutomaticInfixes();

        $author = '';
        // Strip author from taxon if present
        if (isset($taxon['authorship']) &&
            substr_compare($taxon['taxon'], $taxon['authorship'], -strlen($taxon['authorship'])) === 0) {
            $taxon['taxon'] = trim(str_replace($taxon['authorship'], '', $taxon['taxon']));
            $author = ' ' . $taxon['authorship'];
        }

		$e = explode(' ', $taxon['taxon']);
        $r = is_null($ranks) ? $this->newGetProjectRanks() : $ranks;

		if (!isset($taxon['rank_id'])||$taxon['rank_id']==0) { // shouldn't happen!
			 return $taxon['taxon'];
        }

        if (isset($r[$taxon['rank_id']]['labels'][$this->getDefaultProjectLanguage()]))
            $d = $r[$taxon['rank_id']]['labels'][$this->getDefaultProjectLanguage()];
        else
            $d = $r[$taxon['rank_id']]['rank'];

        $rankId = $r[$taxon['rank_id']]['rank_id'];
        $rankName = ucfirst($d);
        $abbreviation = $r[$taxon['rank_id']]['abbreviation'];

        // Rank level is above genus; no formatting
        if ($rankId < GENUS_RANK_ID) {
            switch ($rankpos) {
                case 'none':
                    return $taxon['taxon'] . $author;
                case 'post':
                    return $taxon['taxon'] . $author .', ' . $rankName;
                default:
                    return $rankName . ' ' . $taxon['taxon'] . $author;
            }
        }

        // Genus or subgenus; add italics
        if ($rankId < SPECIES_RANK_ID && count($e) == 1) {

            // Species case for subgenus and section: append genus name
            // Set constant for section, may not be present in constants.php yet...
            $subscript = '';
            if (((defined('SECTION_2_RANK_ID') && $rankId == SECTION_2_RANK_ID) ||
                $rankId == SUBGENUS_RANK_ID) && isset($taxon['parent_id'])) {
                $parent = $this->getTaxonById($taxon['parent_id']);
                $subscript = ' <span class="italics">(' . $parent['taxon'] . ')</span>';
             }

			//$txn=$taxon['taxon'];
			$txn=$this->addHybridMarkerAndInfixes(array('name' => $taxon['taxon'], 'base_rank_id' => $rankId, 'taxon_id' => $taxon['id'], 'parent_id' => $taxon['parent_id']));

            switch ($rankpos) {
                case 'none':
                    return '<span class="italics">' . $txn . '</span>' . $author;
                case 'post':
                    return '<span class="italics">' . $txn . '</span>' . $author . ', ' . $rankName . $subscript;
                default:
                    return $rankName . '  <span class="italics">' . $txn . '</span>' . $author;
            }
        }

        // Species
        if ($rankId > GENUS_RANK_ID && count($e) == 2) {
            //$name = '<span class="italics">' . $taxon['taxon'] . '</span>';
            $name = $taxon['taxon'];
        }

        // Regular infraspecies, name consists of three parts
        if (count($e) == 3) {
            //$name = '<span class="italics">' . $e[0] . ' ' . $e[1] . (!empty($abbreviation) && $addInfixes ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[2] . '</span>';
			// abbreviation handled by addHybridMarkerAndInfixes
            $name = $e[0] . ' ' . $e[1] . ' ' . $e[2] ;
        }

        // Single infraspecies with subgenus
        if (count($e) == 4 && $e[1][0] == '(') {
            //$name = '<span class="italics">' . $e[0] . ' ' . $e[1] . ' ' . $e[2] . (!empty($abbreviation) && $addInfixes ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[3] . '</span>';
			// abbreviation handled by addHybridMarkerAndInfixes
            $name = $e[0] . ' ' . $e[1] . ' ' . $e[2] . ' '. $e[3];
        }

        // Return now if name has been set
        if (isset($name)) {
            return '<span class="italics">' . $this->addHybridMarkerAndInfixes( [ 'name' => $name, 'base_rank_id' => $rankId, 'taxon_id' => $p['id'] ] )  . '</span>' . $author;
        }

        // Now we're handling more complicated cases. We need the parent before continuing
        // say goodbye to the orphans
		if (empty($taxon['parent_id'])) {
            return $taxon['taxon'];
        }

        $parent = $this->getTaxonById($taxon['parent_id'],true);
        // say goodbye to the misguided orphans
        if (empty($parent['rank_id'])) {
            return $taxon['taxon'];
        }
        $parentAbbreviation = $r[$parent['rank_id']]['abbreviation'];

        // Double infraspecies
        if (count($e) == 4) {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . (!empty($parentAbbreviation) && $addInfixes ? '</span> ' . $parentAbbreviation . ' <span class="italics">' : ' ') . $e[2] .
             (!empty($abbreviation) ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[3] . '</span>';
        }

        // Double infraspecies with subgenus
        if (count($e) == 5 && (isset($e[1][0]) && $e[1][0] == '(')) {
            $name = '<span class="italics">' . $e[0] . ' ' . $e[1] . ' ' . $e[2] . (!empty($parentAbbreviation) && $addInfixes ? '</span> ' . $parentAbbreviation . ' <span class="italics">' : ' ') . $e[3] .
             (!empty($abbreviation) && $addInfixes ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[4] . '</span>';
        }

        // Return now if name has been set
        if (isset($name)) {
            return $this->addHybridMarkerAndInfixes(array('name' => $name, 'base_rank_id' => $rankId, 'taxon_id' => $taxon['id'], 'parent_id' => $taxon['parent_id'])) . $author;
			//return $this->addHybridMarker(array('name' => $name, 'base_rank_id' => $rankId)) . $author;
        }

        // If we end up here something must be wrong, just return name sans formatting
        return $taxon['taxon'];
    }

    private function setHybridMarker($name, $rankId, $isHybrid)
    {
        if ($isHybrid == 0)
		{
            return $name;
        }

        $marker = ($rankId == GRAFT_CHIMAERA_RANK_ID ? $this->_hybridMarker_graftChimaera : $this->_hybridMarkerHtml);

        // intergeneric hybrid
        if ($isHybrid == 2 || $rankId < SPECIES_RANK_ID)
		{
            return $marker . ' ' . $name;
        }

        // interspecific hybrid; string is already formatted so take second space!!
        return implode(' ' . $marker . ' ', explode(' ', $name, 3));

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

    public function getProjectTitle( $filesystem_safe=false )
    {
        $title=isset($_SESSION['admin']['project']['title']) ? $_SESSION['admin']['project']['title'] : null;
		return $filesystem_safe ? preg_replace( [ "/[^A-Za-z0-9 ]/" , "/\s/"] , ['','_'] , $title) : $title;
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
        return (int)$this->rGetVal('id');
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
		$this->smarty->assign('viewName', $this->getViewName());
		$this->smarty->assign('wikiUrl', $this->getWikiUrl());
		$this->smarty->assign('adminMessageFadeOutDelay', $this->_adminMessageFadeOutDelay);
		$this->smarty->assign('GitVars', $this->getGitVars());
		$this->smarty->assign('server_name', $this->server_name);

        $this->smarty->assign('uiLanguages', $this->uiLanguages);
        $this->smarty->assign('uiCurrentLanguage', $this->getCurrentUiLanguage());
        $this->smarty->assign('isMultiLingual', $this->isMultiLingual);
        $this->smarty->assign('useVariations', $this->useVariations);
        $this->smarty->assign('useRelated', $this->useRelated);

        $this->smarty->assign('isSysAdmin', $this->isCurrentUserSysAdmin());
        $this->smarty->assign('currentUserId', $this->getCurrentUserId());

        if (isset($this->cssToLoad))
            $this->smarty->assign('cssToLoad', $this->cssToLoad);

        if (isset($this->jsToLoad))
            $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);

        $this->smarty->assign('controllerMenuExists', $this->includeLocalMenu && file_exists($this->smarty->getTemplateDir(0) . '_menu.tpl'));

        if (isset($_SESSION['admin']['user']) && isset($_SESSION['admin']['user']['_said_welcome']) &&
            !$_SESSION['admin']['user']['_said_welcome'])
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

    	if (!empty($this->cronNextRun))
		{
            $this->smarty->assign('cronNextRun', $this->cronNextRun);
		}
    }



    private function getCurrentUiLanguage ()
    {
        return (isset($_SESSION['admin']['user']['currentLanguage']) ? $_SESSION['admin']['user']['currentLanguage'] : $this->uiDefaultLanguage);
    }



    private function getModuleActivationStatus()
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
    private function startSession()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            if (session_id() == '') {
               session_start();
            }
        } else {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
    }



    private function setTimeZone()
    {
        date_default_timezone_set($this->generalSettings['serverTimeZone']);
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
    private function setNames()
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
    private function setSmartySettings()
    {
        $this->_smartySettings = $this->config->getSmartySettings();

        $this->smarty = new Smarty();

        /* DEBUG */
        // $this->smarty->force_compile = true;
        

        $cbn = $this->getControllerBaseName();

        //$this->smarty->caching = $this->_smartySettings['caching'];
        //
        // Disable caching for admin pages, it serves no purpose and slows things down!
        $this->smarty->caching = 0;

        $this->smarty->template_dir = $this->_smartySettings['dir_template'] . '/' . (isset($cbn) ? $cbn . '/' : '');
        $this->smarty->compile_dir = $this->_smartySettings['dir_compile'];
        $this->smarty->cache_dir = $this->_smartySettings['dir_cache'];
        $this->smarty->config_dir = $this->_smartySettings['dir_config'];
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
    private function setRequestData()
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



    private function doLanguageChange($unsetRequestVar = true)
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
    private function loadModels()
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
		require_once __DIR__ . '/../models/ControllerModel.php';
		$this->models->ControllerModel = new ControllerModel;

		// Load controller-specific model
        $t = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getControllerBaseName())))) . 'Model';

        if (file_exists(__DIR__ . '/../models/' . $t . '.php'))
		{
            require_once __DIR__ . '/../models/' . $t . '.php';
            $this->models->$t = new $t;
        }

        // Load controller-specific model by override
		if ( isset($this->modelNameOverride) )
		{
			if (!isset($this->models->{$this->modelNameOverride}) &&
                file_exists(__DIR__ . '/../models/' . $this->modelNameOverride . '.php'))
			{
			    require_once __DIR__ . '/../models/' . $this->modelNameOverride . '.php';
				$this->models->{$this->modelNameOverride} = new $this->modelNameOverride;
			}
		}

        // Load some more models
		if ( isset($this->extraModels) )
		{
			foreach ((array) $this->extraModels as $key)
			{
				if (!isset($this->models->{$key}) &&
					file_exists(__DIR__ . '/../models/' . $key . '.php'))
				{
					require_once __DIR__ . '/../models/' . $key . '.php';
					$this->models->{$key} = new $key;
				}
			}
		}

        // Load models for each table, as specified in used models
        require_once __DIR__ . '/../models/Table.php';
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

    protected function extendUsedModels()
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
    private function loadHelpers()
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
            if (file_exists(__DIR__ . '/../helpers/' . $key . '.php'))
			{
                require_once (__DIR__ . '/../helpers/' . $key . '.php');

                $d = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

                if (class_exists($d))
				{
                    $this->helpers->$d = new $d();
                }
            }
        }
    }



    private function initLogging()
    {
        $fn = $this->getAppName() ? $this->getAppName() : 'general';
        $this->helpers->LoggingHelper->setLogFile($this->generalSettings['directories']['log'] . '/' . $fn . '.log');
        $this->helpers->LoggingHelper->setLevel(0);
    }



    private function setLanguages()
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
    public function makePathNames($p)
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
    private function setPaths()
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
    private function setUrls()
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
    private function setLastVisitedPage()
    {
		if (!isset($this->baseSession)) return;

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
    private function checkLastVisitedPage()
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
    private function setLoginStartPage()
    {
        $_SESSION['admin']['login_start_page'] = $this->_fullPath;
    }



    public function setSuppressProjectInBreadcrumbs($state = true)
    {
        $this->suppressProjectInBreadcrumbs = $state;
    }



    /**
     * Create the breadcrumb trail
     *
     * @access     private
     */
    private function setBreadcrumbs()
    {
        if (!isset($this->appName))
            return;

        $breadcrumbRootName = (!is_null($this->getBreadcrumbRootName()) ? $this->getBreadcrumbRootName() : 'Projects');

        // root of each trail: "choose project" page
        $cp = $this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject'];

        $this->breadcrumbs[] = array(
            //'name' => $this->translate($breadcrumbRootName),
            'name' => $breadcrumbRootName,
            'url' => $cp
        );

        // controller name can be overridden
        $controllerPublicName = ($this->controllerPublicNameMask ? $this->controllerPublicNameMask : $this->controllerPublicName);
        $controllerBaseName = ($this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->controllerBaseName);

        if ($this->_fullPathRelative != $cp && isset($_SESSION['admin']['project']['title']) && !$this->suppressProjectInBreadcrumbs)
		{

            $this->breadcrumbs[] = array(
                'name' => $_SESSION['admin']['project']['title'],
                'url' => $this->getLoggedInMainIndex()
            );


            if (!empty($controllerPublicName) && $this->_fullPath != $this->getLoggedInMainIndex())
			{
                $curl = $this->baseUrl . $this->appName . '/views/' . $controllerBaseName;

                $this->breadcrumbs[] = array(
                    'name' => $controllerPublicName,
                    'url' => $curl
                );

                //if ($this->getViewName() != 'index')
				{
                    // all views are on the same level, but sometimes we might want another level to the trail when
                    // moving one view to the next, for logic's sake (for instance: taxon list -> edit taxon, two views
                    // that are on the same level, but are perceived by the user to be on different levels)
                    if ($this->breadcrumbIncludeReferer === true)
					{
                        $this->breadcrumbs[] = array(
                            'name' => $this->baseSession->getModuleSetting( 'referer_name' ),
                            'url' => $this->baseSession->getModuleSetting( 'referer_url' )
                        );
                    }
                    else
					if (is_array($this->breadcrumbIncludeReferer))
					{
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
    private function getBreadcrumbs()
    {
        if (isset($this->breadcrumbs)) return $this->breadcrumbs;
    }




    /**
     * Sets a random integer value for general use
     *
     * @access     private
     */
    private function setRandomValue()
    {
        $this->randomValue = mt_rand(99999, mt_getrandmax());
    }



    /**
     * Returns random integer value
     *
     * @return integer    anything between 99999 and mt_getrandmax()
     * @access     private
     */
    private function getRandomValue()
    {
        return $this->randomValue;
    }

    private function saveFormResubmitVal()
    {
		if (!isset($this->baseSession)) return;

        if (!$this->noResubmitvalReset)
			$this->baseSession->setModuleSetting( array('setting'=>'last_rnd','value'=>$this->rHasVal('rnd') ? $this->rGetVal('rnd') : null ) );
    }

    private function loadSmartyConfig()
    {
        $this->_smartySettings = $this->config->getSmartySettings();
    }

    private function checkWriteableDirectories()
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
		$this->translator = new TranslatorController([
			'model' => $this->models->Taxa,
			'envirnonment'=>'admin',
			'language_id'=>$this->getDefaultProjectLanguage()
		]);
	}

	public function translate( $content )
	{
		return $this->translator->translate( $content );
	}

	public function javascriptTranslate( $content )
	{
		return $this->translator->translate( $content );
	}

	public function smartyTranslate($params, $content, &$smarty, &$repeat)
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
        if (file_exists(__DIR__ . "/../models/{$model}.php")) {
            include_once __DIR__ . "/../models/{$model}.php";
            $this->models->{$model} = new $model;
        }
	}

    /*
     * Used to pull external data from an api using a curl request. Parameter can
     * either be a url or an array with additional parameters (post data and timeout).
     */
	protected function getCurlResult ($p)
	{
        $url = is_array($p) && isset($p['url']) ? $p['url'] : (!empty($p) ? $p : false);
        $post = isset($p['post']) ? $p['post'] : false;
        $timeout = isset($p['timeout']) ? $p['timeout'] : 10;

        if (!$url) {
           return '';
        }

		$ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
		if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
		if ($timeout) {
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		}

		$result=curl_exec($ch);
		curl_close($ch);

		$output = json_decode($result);
		// Return raw output if result is no (valid) json
		return !is_null($output) ? $output : $result;
	}

	protected function activateBasicModules()
	{
		$d=$this->models->Modules->_get(
			array(
				'id'=> array('controller in #'=>"('users','projects')")
			));

		foreach((array)$d as $key=>$val)
		{
			$m=$this->models->ModulesProjects->_get(
				array(
					'id'=> array(
						'project_id'=>$this->getCurrentProjectId(),
						'module_id'=>$val['id']
					),
				));

			if ($m && $m[0]['active']=='y') continue;

			if ($m && $m[0]['active']=='n')
			{
				$this->models->ModulesProjects->update(
					array('active'=>'y'),
					array('id'=>$m[0]['id'])
				);
			}
			else
			if (!$m)
			{
				$this->models->ModulesProjects->save(array(
					'project_id'=>$this->getCurrentProjectId(),
					'module_id'=>$val['id'],
					'show_order'=>99,
					'active'=>'y',
					'created'=>'now()'
				));
			}

		}

	}

	protected function initUserRights()
	{
		$this->UserRights = new UserRights([
			'model' => $this->models->Users,
			'userid' => $this->getCurrentUserId(),
			'projectid' => $this->getCurrentProjectId(),
			'controller' => $this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->getControllerBaseName()
		]);
	}

    public function arrayHasData ($p = array())
    {
        foreach ($p as $k => $v) {
            if (is_array($v)) {
                $this->arrayHasData($v);
            }
            if ($v != '') {
                return true;
            }
        }
        return false;
    }

	protected function getCurrentModuleId ()
    {
		$d = $this->models->Modules->_get(array(
            "id" => array("controller" => $this->getControllerBaseName())
		));

		return $d ? $d[0]['id'] : false;
    }

	protected function setRankIdConstants()
	{
		foreach((array)$this->models->Ranks->_get(array('id'=>'*')) as $val)
		{
			if ( strpos($val['rank'],"/")!==false )
			{
				$val['rank']=substr($val['rank'],0,strpos($val['rank'],"/"));
			}

			$const=strtoupper(str_replace(array('-',' '),'_',$val['rank'])).'_RANK_ID';

			if (!defined($const))
			{
				define($const,$val['id']);
			}
			else
			{
				if (stripos($val['additional'],'zoology')!==false)
				{
					$extra='zoology';
				}
				else
				if (stripos($val['additional'],'botany')!==false)
				{
					$extra='botany';
				}
				else
				{
					$extra='2';
				}

				$const=strtoupper(str_replace(array('-',' '),'_',$val['rank'].'_'.$extra)).'_RANK_ID';

				if (!defined($const)) define($const,$val['id']);
			}
		}
	}

	private function setNameTypeIds()
	{
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
	}

	protected function getNameTypeId( $predicate )
	{
		
		//$this->getNameTypeId(PREDICATE_PREFERRED_NAME),

		/*
			PREDICATE_VALID_NAME
			PREDICATE_PREFERRED_NAME
			PREDICATE_HOMONYM
			PREDICATE_BASIONYM
			PREDICATE_SYNONYM
			PREDICATE_SYNONYM_SL
			PREDICATE_MISSPELLED_NAME
			PREDICATE_INVALID_NAME
			PREDICATE_ALTERNATIVE_NAME
		*/

		if ( isset($this->_nameTypeIds[$predicate]) ) return $this->_nameTypeIds[$predicate]['id'];
	}

	protected function getWikiUrl()
	{
	    /** @setting wiki_base_url (string) */
		$wiki_base_url=$this->models->ControllerModel->getGeneralSettingValue(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'setting' => 'wiki_base_url'
			));

		if ( isset($this->wikiPageOverride['basename']) )
		{
			$basename=$this->wikiPageOverride['basename'];
		}
		else
		{
			$basename=!empty($this->controllerPublicNameMask) ? $this->controllerPublicNameMask : $this->controllerPublicName;
		}

		if ( empty($basename) ) return;

		if ( isset($this->wikiPageOverride['pagename']) )
		{
			$pagename=$this->wikiPageOverride['pagename'];
		}
		else
		{
			$pagename=!empty($this->pageNameAltName) ? $this->pageNameAltName : $this->getPageName();
			$pagename=preg_replace('/[^A-Za-z0-9 ]/', '', $pagename);
			$pagename=str_replace(' ','_',$pagename);
		}

		return
			str_replace(
				['%module%','%page%'],
				[str_replace(' ','',ucwords($basename)),$pagename],
				$wiki_base_url
			);
	}

	protected function addHybridMarkerAndInfixes( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;

		if ( defined("NOTHOVARIETAS_RANK_ID") && $base_rank_id==NOTHOVARIETAS_RANK_ID ) {
			$p['name']=$this->addHybridMarker( $p );
			return $this->addVarietasInfix( $p );
		} else if ( defined("NOTHOSUBSPECIES_RANK_ID") && $base_rank_id==NOTHOSUBSPECIES_RANK_ID ) {
			$p['name']=$this->addHybridMarker( $p );
			return $this->addSubspeciesInfix( $p );
		} else if ( (defined("NOTHOGENUS_RANK_ID") && $base_rank_id==NOTHOGENUS_RANK_ID) ||
			 (defined("NOTHOSPECIES_RANK_ID") && $base_rank_id==NOTHOSPECIES_RANK_ID ) ) {
			return $this->addHybridMarker( $p );
		} else if ( defined("VARIETAS_RANK_ID") && $base_rank_id==VARIETAS_RANK_ID ) {
			return $this->addVarietasInfix( $p );
		} else if ( defined("SUBSPECIES_RANK_ID") && $base_rank_id==SUBSPECIES_RANK_ID  ) {
			return $this->addSubspeciesInfix( $p );
		} else if ( defined("FORMA_RANK_ID") && $base_rank_id==FORMA_RANK_ID ) {
			return $this->addFormaInfix( $p );
		}

		if ( !empty($p['specific_epithet']) ) {
			return $p['specific_epithet'];
		} else if ( !empty($p['uninomial']) ) {
			return $p['uninomial'];
		} else if ( !empty($p['name']) ) {
			return $p['name'];
		}
	}

	public function addHybridMarker( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$uninomial=isset($p['uninomial']) ? $p['uninomial'] : null;
		$specific_epithet=isset($p['specific_epithet']) ? $p['specific_epithet'] : null;
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$parent_id=isset($p['parent_id']) ? $p['parent_id'] : null;

		$marker=$this->getShowAutomaticHybridMarkers() ? $this->_hybridMarkerHtml : '';

		if ( $base_rank_id==NOTHOGENUS_RANK_ID )
		{
			return $marker . ( isset($uninomial) ? $uninomial : $name );
		}
		else
		if ( $base_rank_id==NOTHOSPECIES_RANK_ID ||
			 $base_rank_id==NOTHOSUBSPECIES_RANK_ID ||
			 $base_rank_id==NOTHOVARIETAS_RANK_ID )
		{

			if ( is_null($parent_id) && !is_null($taxon_id) )
			{
				$parent_id=$this->getTaxonById($taxon_id,true)['parent_id'];
			}

			if ( !is_null($parent_id) )
			{
				$parent=$this->getTaxonById($parent_id);
				if ($parent['rank_id']==NOTHOGENUS_RANK_ID)
				{
					return $marker . ( isset($uninomial) ? $uninomial : $name );
				}
			}
			
			if ( !empty($specific_epithet) )
			{
				return $marker . $specific_epithet;
			}
			else
			if ( !empty($uninomial) )
			{
				return $marker . $uninomial;
			}

			if ( is_null($parent_id) && !is_null($taxon_id) )
			{
				$parent_id=$this->getTaxonById($this->getTaxonById($taxon_id)['parent_id'])['id'];
			}
			

			if ( !is_null($parent_id) )
			{
				$parent=$this->getTaxonById($parent_id);
				if ($parent['base_rank_id']==NOTHOGENUS_RANK_ID)
				{
					return $marker . ( isset($uninomial) ? $uninomial : $name );
				}
			}

			if ( empty($name) )
			{
				return $marker;
			} else {
			    $f = strip_tags($name);
			    $ied=explode(' ', $f, 2);
				$r = $ied[0]. ' ' . $marker . $ied[1];

				return str_replace($f, $r, $name);
			}
		} else {
			if ( !empty($specific_epithet) ) {
				return $specific_epithet;
			} else if ( !empty($uninomial) ) {
				return $uninomial;
			} else {
				return $name;
			}
		}
	}

	public function addVarietasInfix( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$uninomial=isset($p['uninomial']) ? $p['uninomial'] : null;
		$specific_epithet=isset($p['specific_epithet']) ? $p['specific_epithet'] : null;
		$infra_specific_epithet=isset($p['infra_specific_epithet']) ? $p['infra_specific_epithet'] : null;

		$marker=$this->getShowAutomaticInfixes() ? $this->_varietyMarker : '';

		if ( $base_rank_id==VARIETAS_RANK_ID )
		{
			if ( !empty($infra_specific_epithet) )
			{
				return $marker . $specific_epithet;
			}
			else
			if ( !empty($name) && strpos($name,' ')!==false )
			{
				$ied=explode( ' ',  $name );
				$ied[2] = '</span><span>' . $marker . '</span>' . ' <span class="italics">' . $ied[2];
				return implode(' ',$ied);
			}
		} else if ( $base_rank_id==NOTHOVARIETAS_RANK_ID ) {
			$marker=$this->getShowAutomaticInfixes() ? 'notho' . $marker : '';

			if ( !empty($infra_specific_epithet) ) {
			    return $marker . $specific_epithet;
			} else if ( !empty($name) && strpos($name,' ')!==false ) {
				$ied=explode( ' ',  $name );
				$ied[2] = '</span><span>' . $marker . '</span>' . ' <span class="italics">' . $ied[2];
				return implode(' ',$ied);
			}
		}

		return $name;
	}

	public function addSubspeciesInfix( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$uninomial=isset($p['uninomial']) ? $p['uninomial'] : null;
		$specific_epithet=isset($p['specific_epithet']) ? $p['specific_epithet'] : null;
		$infra_specific_epithet=isset($p['infra_specific_epithet']) ? $p['infra_specific_epithet'] : null;

		$marker=$this->getShowAutomaticInfixes() ? $this->_subspeciesMarker : '';

		if ( $base_rank_id==SUBSPECIES_RANK_ID )
		{
			if ( !empty($infra_specific_epithet) )
			{
				return $marker . $specific_epithet;
			}
			else
			if ( !empty($name) && strpos($name,' ')!==false )
			{
				$ied=explode( ' ',  $name );
				if ( isset($ied[2]) )
				{
					$ied[2] = '</span><span>' . $marker . '</span>' . ' <span class="italics">' . $ied[2];
					return implode(' ',$ied);
				} else {
					return $name;
				}

			}
		}
		else
		if ( $base_rank_id==NOTHOSUBSPECIES_RANK_ID ) {

			// REFAC2015: $this->_nothoInfixPrefix . $marker --> should come from ranks.abbreviation

			$marker=$this->getShowAutomaticInfixes() ? $this->_nothoInfixPrefix . $marker : '';
			
			if ( !empty($infra_specific_epithet) ) {
				return $marker . $specific_epithet;
			} else if ( !empty($name) && strpos($name,' ')!==false ) {
				$ied=explode( ' ',  $name );

				if ( isset($ied[2]) ) {
					$ied[2] = '</span><span>' . $marker . '</span>' . ' <span class="italics">' . $ied[2];
					return implode(' ',$ied);
				} else {
					return $name;
				}

			}
		}

		return $name;
	}

	public function addFormaInfix( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$uninomial=isset($p['uninomial']) ? $p['uninomial'] : null;
		$specific_epithet=isset($p['specific_epithet']) ? $p['specific_epithet'] : null;
		$infra_specific_epithet=isset($p['infra_specific_epithet']) ? $p['infra_specific_epithet'] : null;

		$marker=$this->getShowAutomaticInfixes() ? $this->_formaMarker . ' ' : '';

		if ( $base_rank_id==FORMA_RANK_ID || $base_rank_id==FORMA_SPECIALIS_RANK_ID )
		{
			if ( !empty($infra_specific_epithet) )
			{
				return $marker . $specific_epithet;
			}
			else
			if ( !empty($name) && strpos($name,' ')!==false )
			{
				$ied=explode( ' ',  $name );
				$ied[2] = '</span><span>' . $marker . '</span>' . ' <span class="italics">' . $ied[2];
				return implode(' ',$ied);
			}
		}
		return $name;
	}

	protected function setShowAutomaticHybridMarkers()
	{
		$this->_showAutomaticHybridMarkers =
			$this->models->ControllerModel->getSetting(array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => GENERAL_SETTINGS_ID,
			'setting' => 'show_automatic_hybrid_markers'
		))==1;
	}

	protected function setShowAutomaticInfixes()
	{
		$this->_showAutomaticInfixes =
            ($this->models->ControllerModel->getSetting(array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => GENERAL_SETTINGS_ID,
			'setting' => 'show_automatic_infixes'
		)) == 1);
	}

	protected function getShowAutomaticHybridMarkers()
	{
		return $this->_showAutomaticHybridMarkers;
	}

	protected function getShowAutomaticInfixes()
	{
		return $this->_showAutomaticInfixes;
	}

	protected function setAdminMessageFadeOutDelay()
	{
		$d=$this->models->ControllerModel->getSetting(array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => GENERAL_SETTINGS_ID,
			'setting' => 'admin_message_fade_out_delay'
		));

		$this->_adminMessageFadeOutDelay = $d ? $d : 10000;
	}

	protected function setGitVars()
	{
		if (defined("PATH_GIT_EXECUTABLE")) $this->helpers->Git->setGitExe( PATH_GIT_EXECUTABLE );

		$this->helpers->Git->setRepoPath( isset($this->generalSettings['applicationFileRoot']) ? $this->generalSettings['applicationFileRoot'] : '/var/www/linnaeusng/' );
		$this->helpers->Git->setData();
		$this->_gitVars = new stdClass;
		$this->_gitVars->branch=$this->helpers->Git->getBranch();
		$this->_gitVars->commit=$this->helpers->Git->getCommit();
		$this->_gitVars->origin_commit_hash=$this->helpers->Git->getOriginCommitHash();
		$this->_gitVars->git_tags=$this->helpers->Git->getTags();
		$this->_gitVars->describe=$this->helpers->Git->getDescribe();
	}

	protected function getGitVars()
	{
		return $this->_gitVars;
	}

	protected function setCronNextRun ()
	{
	    $d = $this->models->ControllerModel->getCronNextRun();

	    if (!empty($d)) {
	        // Interval fixed at 48 hours
	        $d = strtotime($d) + 60 * 60 * 48;
	        $this->cronNextRun = date('M d Y H:i:s', $d);
	    }
	}

	protected function setServerName()
	{
		$this->server_name=trim(@shell_exec( "hostname" ));
	}

	protected function setLanguageIdConstants()
	{
	    $lookup = [
	        'dut' => ['LANGUAGE_ID_DUTCH', 2],
	        'eng' => ['LANGUAGE_ID_ENGLISH' , 26],
	        'sci' => ['LANGUAGE_ID_SCIENTIFIC', 123],
	    ];
	    
	    $l = $this->models->Languages->_get(array('where' => 'iso3 in ("sci", "dut", "eng")'));
	    $iso3s = array_column($l, 'iso3');
	    
	    foreach ($lookup as $iso3 => $code) {
	        $id = in_array($iso3, $iso3s) ? $l[array_search($iso3, $iso3s)]['id'] : $code[1];
	        if (!defined($code[0])) {
	            define($code[0], $id);
	        }
	    }
	    
	    // Admin only
	    if (!defined('LANGUAGECODE_DUTCH')) define('LANGUAGECODE_DUTCH',LANGUAGE_ID_DUTCH);
	    if (!defined('LANGUAGECODE_ENGLISH')) define('LANGUAGECODE_ENGLISH',LANGUAGE_ID_ENGLISH);
	    
	}

    /**
     * Offers an option to switch to a different, persistent MySQL connection.
     * See Db2 class for more info!
     */
    public function switchToPersistentConnection ()
    {
        include_once (__DIR__ . "/../Db2.php");

        $config = new configuration;
        $settings = $config->getDatabaseSettings();
        $pc = new Db2($settings);

        if (!empty($this->models)) {
            foreach ($this->models as $model) {
                $model->switchConnection($pc);
            }
        }
    }

    public function formatDateTime ($dateTime)
    {
        if (!strtotime($dateTime)) {
            return $dateTime;
        }
        list($date, $time) = explode(' ', $dateTime);
        return date('j F Y', strtotime($date)) . ' ' .
            $this->translate('at') . ' ' . $time;
    }

    /**
     * MUST MATCH THE SAME METHOD IN APP!

     * @param $reference reference object/array
     * @return string
     *
     * Takes a reference object/array and returns the author string. This is necessary because
     * in NSR the author string is stored either in the author field (no action required) or
     * in the authors field. The latter contains an array of individual authors. In this case,
     * the author string is compiled and returned.
     */
    public function setAuthorString ($reference = [])
    {
        $reference = (array)$reference;

        if (!empty($reference['author'])) {
            return $reference['author'];
        }

        if (!empty($reference['authors'])) {
            $authors = array_map('trim', array_column($reference['authors'], 'name'));
            if (!empty($authors)) {
                $author = array_pop($authors);
                if ($authors) {
                    $author = implode(', ', $authors) . " & " . $author;
                }
                return $author;
            }
        }
        return null;
    }

    /**
     * MUST MATCH THE SAME METHOD IN APP!
     *
     * @param array $reference
     * @return string
     *
     * Unified way to format a reference in Linnaeus. Previously this was formatted using smarty
     * in various templates in several variations.
     */
    public function formatReference ($reference = [])
    {
        $r = (array)$reference;
        if (empty($r)) {
            return '';
        }

        // Trim white space
        array_walk_recursive($r, function(&$v) {
            $v = trim($v);
        });

        // Base part -- different from app!
        $url = '<a href="' . $this->baseUrl . $this->appName . '/views/literature2/edit.php?id=' .
            $r['id'] . '">%s</a>';
        $author = $this->setAuthorString($r);
        if (!empty($r['date'])) {
            $author .= ' ' . $r['date'];
        }
        // Wrap in link
        $str = '';
        if ($author != '') {
            $str = sprintf($url, $author);
            if (substr($author, -1) !== '.') {
                $str .= '.';
            }
            $str .= ' ';
        }

        // Append the rest
        $str .= $r['label'];
        if (!in_array(substr($r['label'], -1), ['?','!','.'])) {
            $str .= '.';
        }

        $pub = '';
        /*
        * This part is different from the app method! Do not
        * copy/paste directly from app :)
        */
        if (!empty($r['periodical_label'])) {
            $pub .= $r['periodical_label'] . ' ';
        } else if (!empty($r['periodical'])) {
            $pub .= $r['periodical'] . ' ';
        }
        if (!empty($r['publishedin_label'])) {
            $pub .= 'In: ' . $r['publishedin_label'] . '. ';
        } else if (!empty($r['publishedin'])) {
            $pub .= 'In: ' . $r['publishedin'] . '. ';
        }
        /*
         * End of different part!
         */

        // Strip dot if volume directly after label
        if (trim($pub) == '' && !empty($r['volume'])) {
            $str = substr(trim($str), 0, -1);
        } else {
            $str .= " $pub";
        }

        if (!empty($r['volume']) && !empty($r['pages'])) {
            $str .= ' ' . $r['volume'] . ': ';
        } else if (!empty($r['volume'])) {
            $str .= ' ' . $r['volume'] . '. ';
        }
        if (!empty($r['pages'])) {
            $str .= $r['pages'] . '. ';
        }

        if (!empty($r['publisher'])) {
            $str .= $r['publisher'] . '.';
        }
        $str = trim($str);
        // Add closing dot if this is lacking
        if (substr($str, -1) !== '.') {
            $str .= '.';
        }
        if (!empty($r['external_link'])) {
            $str .= ' [<a href="' . $r['external_link'] . '" target="_blank">link</a>]';
        }
        // Remove any double spaces if necessary
        return preg_replace('/\s+/', ' ', $str);
    }

}
