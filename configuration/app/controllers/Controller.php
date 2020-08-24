<?php
/*

	on skins:
	if the setting (in the table 'settings') for 'skin' exists, that skin is used
	providing the follwing directories exist ($this->doesSkinExist()):
		app/style/[skinname]/
		app/media/system/skins/[skinname]/
		app/templates/templates/[skinname]/[controller basename]/
	please note they have to exist, even if they remain empty!
	in all other cases, the skin named in $this->generalSettings['app']['skinName'] is
	used. example, for a project with the skin "original_skin", the following
	directories have to exist:
		"www/linnaeus_ng/www/app/style/original_skin/"
		"www/linnaeus_ng/www/app/media/system/skins/original_skin/"
		"www/linnaeus_ng/www/app/templates/templates/original_skin/matrixkey/"
	otherwise, the default skin, "linnaeus_ng", will be used.
	one exception to all this are the webservices. all views in
		app/views/webservices/
	have no design, therefore no skin. their templates all reside in:
		app/templates/templates/webservices


	on stylesheets:
	there are five location where included .css files and styles can be specified:
		____source__________scope___________location_____________________________
		1)	$cssToLoadBase	global			declared in Controller
		2)	$cssToLoad		module			declared in [Module]Controller
		3)	hardcoded		skin			printed in _head.tpl
		4)	dynamic			skin/project	utilities\dynamic-css.tpl
		5)  dynamic			project			app/style/custom/[pId].css or app/style/custom/[pId]--[project name].css or
	ad 1: these files are always loaded, for every module, and loaded first. after
	 being merged with $cssToLoad in Controller::setCssFiles(), they are printed in
	 the skin's _head.tpl file.
	ad 2: module-specific files, are always loaded, directly after $cssToLoadBase. be
	 aware that the file 'basics.css' *needs* to exist in the skin's style-directory
	 for the skin to be recognized as valid. the file may be empty, as long as it's
	 there.
	ad 3: skin-specific files, hardcoded (with project-parametrized paths) in
	 _head.tpl. usually printed after the files in 2), but this can be changed if
	 need be. note that these files do not *need* to change from one skin to the next
	 but may do so. an example is the styling of the jQuery-ui dialog.
	 most skins also include 'cssreset-min.css' and several css-files aimed
	 specifically at internet explorer in the _head.tpl file.
	ad 4: the function UtilitiesController::dynamicCssAction() makes it possible to
	 create dynamic css-data. it prints the template file dynamic-css.tpl,
	 which exists (and can be altered) in each skin. this smarty-template allows for
	 conditional formatting based on project id and other variables that are
	 available to it (in a somewhat cumbersome fashion, as the shared use of the
	 accolade by css and smarty probably requires a lot of {literal}-tags). the file
	 is printed with a 'Content-type:text/css'-header, and printed in all _head.tpl
	 files after being merged with $cssToLoad in Controller::setCssFiles().
	ad 5: the last thing Controller::setCssFiles() looks for is an optional project-
	 specific stylesheet either called "0023.css" or "0023--imaginary-beings.css",
	 where 23 is the project ID and "imaginary beings" is the project's system name.

	on translations:
	after repeated problems with the "official" getText() functions, the function has
	been replaced with a custom one:
		translate($str)
	this function fetches the translation based on the current setting of the language id.
	if no translation exists, it saves the string into the table of strings to be
	translated, and returns it unchanged. the functions:
		javascriptTranslate()
		smartyTranslate()
	are wrappers for accessing translate() from javascript - via the function _() in
	main.js - and smarty - via the registered block function {t}{/t} - respectively.

	on snippets:
	to allow for the inclusion of project-dependent bits of html into general templates,
	there is the concept of the snippet. snippets are bit of html-code that are included
	in template if they exist for the current project. they are included like this:
		{snippet}matrix_main_menu.html{/snippet}
	after which the function smartyGetSnippet searches for the specified file
	in the projects snippet-folder, which is
		[htdocs]/linnaeus_ng/www/app/media/project/_snippets/[project-code]/
	if the file (or the directory) doesn't exist, nothing is included, and no error is
	generated.
	if a snippet contains text and a project is multi-lingual, the snippet code can be
	parametrized with the language ID, like this:
		{snippet language=$currentLanguageId}titles.html{/snippet}
	the smarty variable '$currentLanguageId' is assigned by default (in preparePage()),
	and is always available. when the language ID is thus specified, the system looks
	for a file with that ID (formatted in a similar fashion as the pId, preceded by '--')
	added after the	file's name, before the extension. if that file does not exist,
	the system looks a file with a regular name (without the added ID) instead. so, if
	this is included:
		{snippet language=$currentLanguageId}titles.html{/snippet}
	and $currentLanguageId is '24', the system will first look in the snippet-folder for
		titles--0024.html
	and if that doesn't exist, for
		titles.html
	if none of these exist, the system will subsequently look if the non-language
	specific or the language specific file (in that order) exists in the general snippets
	folder:
		[htdocs]/linnaeus_ng/www/app/media/project/_snippets/
	please note the files are included "as is", and are not run through any server-side
	interperter; therefore php or smarty-codes won't work. javascript will, however, so
	it can be used for google analytics-codes, which can be different per project.
	snippets can also be useful for inlcuding bits of html that depend on which OTAP-server
	you're working on (like "noindex" tags on development servers). note that this last
	strategy only works if the _snippets folder is not included in SVN, so it doesn't
	automatically appear on the production-environment.

*/

if (!defined('GENERAL_SETTINGS_ID')) define('GENERAL_SETTINGS_ID',-1);

include_once (__DIR__ . "/../BaseClass.php");
include_once (__DIR__ . "/../../../vendor/autoload.php");
// include_once (__DIR__ . "/../../../smarty/Smarty.class.php");

class Controller extends BaseClass
{

    private $_smartySettings;
    private $_fullPath;
    private $_fullPathRelative;
    private $_checkForProjectId=true;
    private $_allowEditOverlay=true; // true
    private $_currentHotwordLink=false;
    private $_hotwordTempLinks=array();
    private $_hotwordMightBeHotwords=array();
    private $_hotwordNoLinks=array();
    private $_tmpTree;
    private $_showAutomaticHybridMarkers=true;
    private $_showAutomaticInfixes=true;
	private $_googleAnalyticsCode;
	private $_generalHeaderSubtitle;
	private $_robotsDirective;

    public $viewName;
    public $controllerBaseName;
    public $controllerBaseNameMask = false;
    public $pageName;
    public $controllerPublicName;
    public $controllerPublicNameMask = false;
    public $errors;
    public $messages;
    public $randomValue;
    public $noResubmitvalReset = false;
    public $showBackToSearch = true;
    public $storeHistory = true;
    public $treeList;
    public $includeLocalMenu = true;
    public $allowEditPageOverlay = true;
    public $tmp;
    private $usedModelsBase = array(
		'commonnames',
		'free_modules_projects',
		'glossary',
		'glossary_synonyms',
		'hotwords',
		'interface_texts',
		'interface_translations',
		'labels_projects_ranks',
		'languages',
		'languages_projects',
		'modules',
		'modules_projects',
		'name_types',
		'nbc_extras',
		'projects',
		'projects_ranks',
		'ranks',
		'settings',
		'taxa',
		'taxa_variations',
		'trash_can',
		'variations_labels'
    );
    private $usedHelpersBase = array(
		'session_module_settings',
        'logging_helper',
        'debug_tools',
		'functions',
		'custom_array_sort',
		'paginator',
		'current_url',
		'mobile_detect'
    );
    public $cssToLoadBase = array(
        'basics.css',
        'lookup.css'
    );
	private $_maxBackSteps=100;

	protected $_hybridMarker='×';
	protected $_hybridMarkerHtml='&#215;';
	protected $_formaMarker='f.';
	protected $_hybridMarker_graftChimaera='+';
	protected $_varietyMarker='var.';
	protected $_subspeciesMarker='subsp.';
	protected $_nothoInfixPrefix='notho';

	private $_nameTypeIds=array();

    /**
     * Constructor, calls parent's constructor and all initialisation functions
     *
     * The order in which the functions are called is relevant! Do not change without good reason, plan and extensive tests.
     *
     * @access     public
     */
    public function __construct ($p=null)
    {
        parent::__construct();
        $this->setServerName();
        $this->setControllerParams($p);
        $this->startSession();
        $this->loadHelpers();
        $this->initLogging();
        $this->setNames();
        $this->loadModels();
        $this->loadControllerConfig();
        $this->loadSmartyConfig();
        $this->setLanguageIdConstants();
        $this->setRankIdConstants();
        $this->setRequestData();
		$this->checkForProjectId();
        $this->checkGlobalAuthorization();
		$this->setNameTypeIds();
        $this->startModuleSession();
        $this->restoreState();
        $this->setProjectLanguages();
        $this->setCurrentLanguageId();
		$this->initTranslator();
		$this->setSkinName();
		$this->setUrls();
		$this->setSmartySettings();
		$this->setCssFiles();
        $this->setRandomValue();
		$this->setSearchResultIndexActive();
		$this->setRankIdConstants();
		$this->setShowAutomaticHybridMarkers();
		$this->setShowAutomaticInfixes();
		$this->setGoogleAnalyticsCode();
		$this->setGeneralHeaderSubtitle();
		$this->assignMobileDeviceInfo();
    }

    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        $this->storePageHistory();
        $this->storeState();

        session_write_close();

        parent::__destruct();
    }

	protected function spid()
	{
		$p=$this->getCurrentProjectId();

		if (empty($p))
			return 'p-general';
		else
			return 'p-'.$p;
	}

    public function checkForProjectId()
    {
        if (!$this->getCheckForProjectId()) return;

        $pB = $this->getCurrentProjectId();

        if ($this->rHasVal('p'))
		{
 		    $this->resolveProjectId();
        }
        else
		if ($this->rHasVal($this->generalSettings['addedProjectIDParam']))
		{
            $this->requestData['p'] = $this->requestData[$this->generalSettings['addedProjectIDParam']];
            $this->resolveProjectId();
        }

        $d = $this->getCurrentProjectId();

        // Last resort: if there's only one published project and
        // a pid has not been set yet, accept and use this id
        if (is_null($d)) {
            $projects = $this->models->Projects->_get([
                'id'=>array('published'=>1),
            ]);
            if (count($projects) == 1 && is_null($pB)) {
                $d = $pB = $projects[0]['id'];
                $this->setCurrentProjectId($d);
            }
        }

        // Also check if project is published
        if ($d == null || $d !== null && !$this->projectIsPublished($d)) {
            $this->redirect($this->generalSettings['urlNoProjectId']);
        }

		if ($pB != $d)
			unset($_SESSION['app']['user']);

        $this->setCurrentProjectData();
        $this->setUrls();
        $this->setProjectLanguages();
    }

    public function projectIsPublished ($pId)
    {
        if ($pId)
		{
            $p = $this->models->Projects->_get(array(
                'id' => (int)$pId
            ));

            return $p['published'] == 1;
        }
        return false;
    }

    public function resolveProjectId()
    {
        if (!$this->rHasVal('p'))
            $this->setCurrentProjectId(null);

        if (is_numeric($this->rGetVal('p'))) {

            $p = $this->models->Projects->_get(array(
                'id' => $this->rGetVal('p')
            ));

            if (!$p)
                $this->setCurrentProjectId(null);
            else
                $this->setCurrentProjectId((int)$this->rGetVal('p'));
        }
        else {

            $pName = str_replace('_', ' ', strtolower($this->rGetVal('p')));

			$this->setCurrentProjectId(null);

            $p = $this->models->Projects->_get(array('id'=>array('short_name !='=>'null'),'columns'=>'id,short_name'));

			if ($p) {
				foreach((array)$p as $val) {
					if (empty($val['short_name']))
						continue;
					$d=explode(';',$val['short_name']);
					array_walk($d,function(&$a,$b){$a=trim($a);});
					if (in_array($pName,$d)) {
						$this->setCurrentProjectId($val['id']);
						//exit;
					}
				}
			}
        }
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

    public function _buildTaxonTree ($p = null)
    {
        $pId = isset($p['pId']) ? $p['pId'] : null;
        $depth = isset($p['depth']) ? $p['depth'] : 0;
        $ranks = $this->getProjectRanks();

        if (!isset($p['depth']))
            unset($this->treeList);

        $t = $this->getTaxonChildren($pId);

        foreach ((array) $t as $key => $val)
		{
            $t[$key]['lower_taxon'] = $ranks[$val['rank_id']]['lower_taxon'];
            $t[$key]['keypath_endpoint'] = $ranks[$val['rank_id']]['keypath_endpoint'];
            $t[$key]['sibling_count'] = count((array) $t);
            $t[$key]['depth'] = $t[$key]['level'] = $depth;
            // give do-not-display-flag to taxa that are in brackets
            $t[$key]['do_display'] = !preg_match('/^\(.*\)$/', $val['taxon']);
            // taxon name
            $t[$key]['label'] = $this->formatTaxon(array('taxon'=>$val,'ranks'=>$ranks));
            $t[$key]['author'] = $val['author'];
            $t[$key]['variations'] = $this->getVariations($val['id']);

            $this->treeList[$key] = $t[$key];

            $t[$key]['children'] = $this->_buildTaxonTree(array(
                'pId' => $val['id'],
                'depth' => $depth + 1
            ));

            $this->treeList[$key]['child_count'] = count((array) $t[$key]['children']);
        }

        return $t;
    }

    public function getProjectRanks ()
    {
		$pr = $this->models->ProjectsRanks->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'fieldAsIndex' => 'id',
			'columns' => 'id,rank_id,parent_id,lower_taxon,keypath_endpoint'
		));

		$pl = $this->getProjectLanguages();

		foreach ((array) $pr as $rankkey => $rank)
		{
			if (empty($rank['rank_id']))
				continue;

			$r = $this->models->Ranks->_get(array(
				'id' => $rank['rank_id']
			));

			$pr[$rankkey]['rank'] = $r['rank'];
			$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];
			$pr[$rankkey]['abbreviation'] = $r['abbreviation'];

			foreach ((array) $pl as $val) {

				$lpr = $this->models->LabelsProjectsRanks->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'project_rank_id' => $rank['id'],
						'language_id' => $val['language_id']
					),
					'columns' => 'label'
				));

				$pr[$rankkey]['labels'][$val['id']] = $lpr[0]['label'];
			}
		}

        return $pr;
    }

    public function getTaxonById($id,$formatTaxon=true)
    {
        if (empty($id) || !is_numeric($id) || $id==0)
            return;

		return $this->models->ControllerModel->getTaxonById(array(
            'trashCanExists' => $this->models->TrashCan->getTableExists(),
		    'projectId' => $this->getCurrentProjectId(),
		    'languageId'=> $this->getCurrentLanguageId(),
		    'taxonId' => $id,
            'predicateValidNameId' => $this->getNameTypeId(PREDICATE_VALID_NAME),
            'predicatePreferredNameId' => $this->getNameTypeId(PREDICATE_PREFERRED_NAME),
		    'scientificLanguageId' => LANGUAGE_ID_SCIENTIFIC,
		));
    }

    /* Fetches preferred common name from names table rather than common_names */
    public function getTaxonCommonNameAlternate($id)
    {
        return $this->models->ControllerModel->getTaxonCommonNameAlternate(array(
		    'project_id' => $this->getCurrentProjectId(),
		    'language_id'=> $this->getCurrentLanguageId(),
		    'taxon_id' => $id
		));
    }

	public function setSearchResultIndexActive()
	{
		if ($this->rHasVar('sidx'))
		{
			$_SESSION['app'][$this->spid()]['search']['lastResultSetIndexActive'] = $this->rHasVar('sidx');
		}
	}

	public function getSearchResultIndexActive()
	{
		return isset($_SESSION['app'][$this->spid()]['search']['lastResultSetIndexActive']) ? $_SESSION['app'][$this->spid()]['search']['lastResultSetIndexActive'] : null;
	}

	//REFAC2015
	public function getNbcExtras($p=null)
	{
        $id = isset($p['id']) ? $p['id'] : null;
        $type = isset($p['type']) ? $p['type'] : 'taxon';
        $name = isset($p['name']) ? $p['name'] : null;

		if (is_null($id) || is_null($type))
			return;

		$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'ref_id' => $id,
				'ref_type' => $type
			);

		if (isset($name))
			$d['name'] = $name;

		$extras = $this->models->NbcExtras->_get(
			array(
				'id' => $d,
				'columns' => 'name,value',
			));

		if (isset($name))
			return $extras[0]['value'];

		$d=array();
		foreach((array)$extras as $val)
			$d[$val['name']] = $val['value'];

		return $d;

	}

    public function getTreeList ($p = null)
    {

		if (!$this->projectHasTaxa())
			return;

        if (!isset($this->treeList))
            $this->buildTaxonTree(); // return null;

        $d = array();

        foreach ((array) $this->treeList as $key => $val)
		{
            if (!isset($p['includeEmpty']) && $p['includeEmpty'] !== true && $val['is_empty'] == '1')
                continue;

            $d[$key] = $val;
        }

        return isset($d) ? $d : null;
    }

    public function buildTaxonTree($p = null)
    {
		$this->_buildTaxonTree();

		if (isset($this->treeList))
			uasort($this->treeList,function($a,$b){ return ($a['taxon_order'] > $b['taxon_order'] ? 1 : ($a['taxon_order'] < $b['taxon_order'] ? -1 : 0)); });

        return $this->getTreeList($p);
    }

    public function getTaxonClassification ($taxonId)
    {
		$this->tmp = array();

		$this->_getTaxonClassification($taxonId);

		return $this->tmp;
    }

    public function getPagination ($items, $maxPerPage = 25)
    {
		$this->helpers->Paginator->setItemsPerPage( $maxPerPage );
		$this->helpers->Paginator->setStart( $this->rHasVal('start') ? $this->rGetVal('start') : 0 );
		$this->helpers->Paginator->setItems( $items );
		$this->helpers->Paginator->paginate();

		return $this->helpers->Paginator->getItems();
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

    public function getfullPath ()
    {
        return isset($this->_fullPath) ? $this->_fullPath : null;
    }

    /**
     * Adds an error to the class's stack of errors stored in class variable 'errors'
     *
     * @param      string or array    $error    the error(s)
     * @access     public
     */
    public function addError ($error, $writeToLog = false)
    {
        if (!$error)
            return;

        if (!is_array($error)) {

            $this->errors[] = $error;

            if ($writeToLog !== false)
                $this->log($error, $writeToLog);
        }
        else {

            foreach ($error as $key => $val) {

                $this->errors[] = $val;

                if ($writeToLog !== false)
                    $this->log($val, $writeToLog);
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

    /**
     * Adds a message to the class's stack of messages stored in class variable 'messages'
     *
     * @param      string    $message    the message
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

        if ($url) {

            $p = $this->generalSettings['addedProjectIDParam'] . '=' . $this->getCurrentProjectId();
            $d = parse_url($url);

            // let's ignore the possbilities of fragments, shall we?
            if (isset($d['query']) && !empty($d['query']) && strpos($d['query'], $p) === false)
                $url .= '&' . $p;
            else if (strpos($url, $p) === false)
                $url .= '?' . $p;

            if (basename($url) == $url) {

                $circular = (basename($this->_fullPath) == $url);
            }
            else {

                $circular = ($this->_fullPath == $url) || ($this->_fullPathRelative == $url);
            }

            if (!$circular)
                header('Location:' . $url);

            die();
        }
    }

    /**
     * Sets the active project's name as a session variable (for display purposes)
     *
     * @access     public
     */
    public function setCurrentProjectData($data = null)
    {
        if ($data == null)
		{
            $id = $this->getCurrentProjectId();
            if (isset($id))
			{
                $data = $this->models->Projects->_get(array(
                    'id' => $id
                ));
            }
            else
			{
                return;
            }
        }

        foreach ((array) $data as $key => $val)
		{
            $_SESSION['app']['project'][$key] = $val;
        }

        $_SESSION['app']['project']['filesys_name'] = strtolower(
        preg_replace(array(
            '/\s/',
            '/[^A-Za-z0-9-]/'
        ), array(
            '-',
            ''
        ), $_SESSION['app']['project']['sys_name']));
    }
    
    public function setProjectLanguages()
    {
        $lp = $this->models->LanguagesProjects->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'def_language desc'
        ));

        foreach ((array) $lp as $key => $val)
		{

            $l = $this->models->Languages->_get(array(
                'id' => $val['language_id']
            ));

            $lp[$key]['language'] = $l['language'];
            $lp[$key]['direction'] = $l['direction'];
            $lp[$key]['iso2'] = $l['iso2'];
            $lp[$key]['iso3'] = $l['iso3'];

            if ($val['def_language'] == 1)
			{
                $defaultLanguage = $val['language_id'];
			}

            $list[$val['language_id']] = array(
                'language' => $l['language'],
                'direction' => $l['direction']
            );
        }

        $_SESSION['app'][$this->spid()]['project']['languages'] = $lp;

        if (isset($defaultLanguage))
            $_SESSION['app'][$this->spid()]['project']['default_language_id'] = $defaultLanguage;

        //        if (isset($list)) $_SESSION['app']['project']['languageList'] = $list;
    }

    public function getProjectLanguages()
    {
        return isset($_SESSION['app'][$this->spid()]['project']['languages']) ? $_SESSION['app'][$this->spid()]['project']['languages'] : null;
    }

    /**
     * Sets the active project's id as class variable
     *
     * @param      integer    $id    new active project's id
     * @access     public
     */
    public function setCurrentProjectId($id)
    {
        $_SESSION['app']['project']['id'] = $id;
    }

    public function didActiveLanguageChange()
    {
        return isset($_SESSION['app'][$this->spid()]['user']['languageChanged']) ? $_SESSION['app'][$this->spid()]['user']['languageChanged'] : false;
    }

    public function getCurrentLanguageId()
    {
        if (empty($_SESSION['app'][$this->spid()]['project']['activeLanguageId']))
            $this->setCurrentLanguageId();

        return $_SESSION['app'][$this->spid()]['project']['activeLanguageId'];
    }

    public function getDefaultLanguageId()
    {
        return isset($_SESSION['app'][$this->spid()]['project']['default_language_id']) ?
            $_SESSION['app'][$this->spid()]['project']['default_language_id'] : null;
    }

    public function setCurrentLanguageId ($l = null)
    {
        if ($l)
		{
            $_SESSION['app'][$this->spid()]['user']['languageChanged'] =
                $_SESSION['app'][$this->spid()]['project']['activeLanguageId'] != $l;
            $_SESSION['app'][$this->spid()]['project']['activeLanguageId'] = $l;
        }
        else if ($this->rHasVal('languageId'))
		{
            $_SESSION['app'][$this->spid()]['user']['languageChanged'] =
                $_SESSION['app'][$this->spid()]['project']['activeLanguageId'] != $this->rGetVal('languageId');
            $_SESSION['app'][$this->spid()]['project']['activeLanguageId'] = $this->rGetVal('languageId');
        }
        else
		{
            $_SESSION['app'][$this->spid()]['user']['languageChanged'] = false;
        }

        if (!isset($_SESSION['app'][$this->spid()]['project']['activeLanguageId']))
		{
            $_SESSION['app'][$this->spid()]['project']['activeLanguageId'] = $this->getDefaultLanguageId();
            $_SESSION['app'][$this->spid()]['user']['languageChanged'] = true;
        }

        if (!isset($_SESSION['app'][$this->spid()]['user']['languageChanged']))
		{
            $_SESSION['app'][$this->spid()]['user']['languageChanged'] = true;
		}

        unset($this->requestData['languageId']);

        $_SESSION['app']['user']['currentLanguage'] = $_SESSION['app'][$this->spid()]['project']['activeLanguageId'];

		if ( isset($_SESSION['app']['user']['currentLanguage']) )
		$this->setDatabaseLocaleSettings( $_SESSION['app']['user']['currentLanguage'] );

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
     * Returns the active project's id class variable
     *
     * @return     integer    active project's id
     * @access     public
     */
    public function getCurrentProjectId()
    {
		if (defined('FIXED_PROJECT_ID'))
		{
			return FIXED_PROJECT_ID;
		}
		else
		{
        	return isset($_SESSION['app']['project']['id']) ? $_SESSION['app']['project']['id'] : null;
		}
    }

    public function log ($msg, $severity = 0)
    {
        if (!isset($this->helpers->LoggingHelper))
            return;

        if (is_array($msg)) {

            $d = '';

            foreach ($msg as $key => $val) {

                $d .= $key . '=>' . $val . ', ';
            }

            $msg = trim($d, ' ,');
        }

        if (!$this->helpers->LoggingHelper->write('(' . $this->getCurrentProjectId() . ') ' . $msg, $severity))
            trigger_error($this->translate('Logging not initialised'), E_USER_ERROR);
    }


    public function rHasVar ($var)
    {

		return isset($this->requestData[$var]);

    }

    public function rHasVal($var, $val = null)
    {
        if ($val !== null) {
            return isset($this->requestData[$var]) && $this->requestData[$var] === $val;
        } else {
            return isset($this->requestData[$var]) && $this->requestData[$var] !== '';
        }
    }

    public function rGetVal($var)
    {
		return isset($this->requestData[$var]) ? $this->requestData[$var] : null;
    }

    public function rHasId()
    {
        return $this->rHasVal('id');
    }

    public function rGetId ()
    {
        return (int)$this->rGetVal('id');
    }

    public function rGetAll()
    {
		return isset($this->requestData) ? $this->requestData : null;
    }

    public function getPreferredName($id)
	{
	    $name = $this->models->ControllerModel->getPreferredName(array(
    	    'predicatePreferredName' => PREDICATE_PREFERRED_NAME,
    		'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getCurrentLanguageId(),
    		'taxonId' => $id
	    ));

		return $name[0]['name'];
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

        $tv[0]['taxon'] = $this->getTaxonById($tv[0]['taxon_id']);

        return $tv[0];
    }

	public function matchHotwords( $text )
	{
        if ( empty($text) || !is_string($text) ) return $text;

        $processed = $text;

        // get all hotwords from database
        $wordlist = $this->models->ControllerModel->getHotwords([
			'project_id'=>$this->getCurrentProjectId(),
			'language_id'=>$this->getCurrentLanguageId()
		]);

        // replace the not-to-be-linked words with a unique numbered string
        $exprNoLink = '|(\[no\])(.*)(\[\/no\])|i';
        $processed = preg_replace_callback($exprNoLink, array($this,'embedNoLink'), $processed);

		// replacing existing a-tags
		$expr = '/\<a([^\>]*?)\>(.*)(\<\/a\>)/iUms';
		$processed = preg_replace_callback($expr, array($this,'embedNoLink'), $processed);

		// replacing remaining existing open-tags
		$expr = '/<([A-Z][A-Z0-9]*)\b[^>]*>/siU';
		$processed = preg_replace_callback($expr, array($this,'embedNoLink'), $processed);

		$currUrl = $this->getCurrentPathWithProjectlessQuery();

		// loop through wordlist
        foreach ((array) $wordlist as $key => $val)
		{
            if ( $val['hotword']=='' ) continue;

            if ( stripos($processed,$val['hotword'])===false ) continue;

			// compile the link for the given hotword
            $this->_currentHotwordLink = '../' . $val['controller'] . '/' . $val['view'] . '.php' . (!empty($val['params']) ? '?' . $val['params'] : '');

			// don't link if we're on that page already
			if ($this->_currentHotwordLink==$currUrl)
				continue;

			// replace occurrences of the hotword
            $exprHot = '/\b(' . preg_quote($val['hotword'],'/') . ')\b/i';
            $processed = preg_replace_callback($exprHot, array($this,'embedHotwordLink'), $processed);
        }

        $processed = $this->effectuateHotwordLinks($processed);
        $processed = $this->restoreNoLinks($processed);

        return $processed;
    }

    public function getSetting($setting,$substitute=null)
    {
		return $this->models->ControllerModel->getGeneralSetting(
			array(
				'module_id'=>GENERAL_SETTINGS_ID,
				'project_id'=>$this->getCurrentProjectId(),
				'setting'=>$setting,
				'substitute'=>$substitute
			)
		);
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
        $r = is_null($ranks) ? $this->getProjectRanks() : $ranks;

		if (!isset($taxon['rank_id'])||$taxon['rank_id']==0) { // shouldn't happen!
			 return $taxon['taxon'];
        }

        if (isset($r[$taxon['rank_id']]['labels'][$this->getCurrentLanguageId()]))
            $d = $r[$taxon['rank_id']]['labels'][$this->getCurrentLanguageId()];
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
                $parent = $this->getTaxonById($taxon['parent_id'],false);
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
            $name = $e[0] . ' ' . $e[1] . ' ' . $e[2];
        }

        // Single infraspecies with subgenus
        if (count($e) == 4 && $e[1][0] == '(') {
            //$name = '<span class="italics">' . $e[0] . ' ' . $e[1] . ' ' . $e[2] . (!empty($abbreviation) && $addInfixes ? '</span> ' . $abbreviation . ' <span class="italics">' : ' ') . $e[3] . '</span>';
			// abbreviation handled by addHybridMarkerAndInfixes
            $name = $e[0] . ' ' . $e[1] . ' ' . $e[2] . ' '. $e[3];
        }

        // Return now if name has been set
        if (isset($name)) {
            //return $this->addHybridMarker(array('name' => $name, 'base_rank_id' => $rankId, 'taxon_id' => $p['id'])) . $author;
            return '<span class="italics">' . $this->addHybridMarkerAndInfixes( [ 'name' => $name, 'base_rank_id' => $rankId, 'taxon_id' => isset($p['id']) ? $p['id'] : $p['taxon']['id'] ] ) . '</span>' . $author;
        }

        // Now we're handling more complicated cases. We need the parent before continuing
        // say goodbye to the orphans
		if (empty($taxon['parent_id'])) {
            return '<span class="italics">' . $taxon['taxon'] . '</span>';
        }

        $parent = $this->getTaxonById($taxon['parent_id'],false);
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

    public function formatSynonym($name)
    {
        return '<span class="italics">' . $name . '</span>';
    }

	static function generateTaxonParentageId( $id ) 
	{
		return sprintf('%05s',$id);
	}

	private function setHybridMarker($name, $rankId, $isHybrid)
	{
		if ($isHybrid == 0)
		{
			return $name;
		}
		
		$marker = ($rankId == GRAFT_CHIMAERA_RANK_ID ? $this->_hybridMarker_graftChimaera : $this->_hybridMarkerHtml );
		
		// intergeneric hybrid
		if ($isHybrid == 2 || $rankId < SPECIES_RANK_ID)
		{
			return $marker . ' ' . $name;
		}
		
		// interspecific hybrid; string is already formatted so take second space!!
		return implode(' ' . $marker . ' ', explode(' ', $name, 3));

	}

    private function getMainMenu ()
    {
		$modules=$this->getProjectModules();

		if (isset($modules['modules'])) array_walk($modules['modules'],function(&$value) {$value['type']='regular';});
		if (isset($modules['freeModules'])) array_walk($modules['freeModules'],function(&$value) {$value['type']='custom';$value['show_in_public_menu']='1';});

		$automatic=[];

		if ( $this->getSetting('show_advanced_search_in_public_menu',1)==1 )
		{
			$automatic[]=
				[
					'active' => 'y',
					'module' => $this->translate('Advanced search'),
					'controller' => 'search',
					'show_in_public_menu' => 1,
					'type' => 'automatic',
					'url'=> '../search/search.php'
				];
		}

		return
			array_merge(
				isset($modules['modules']) ? $modules['modules']:array(),
				isset($modules['freeModules'])?$modules['freeModules']:array(),
				$automatic
				);
    }

	private function _getTaxonClassification($id)
	{
		$taxon = $this->getTaxonById($id);
		$taxon['label']=$this->formatTaxon($taxon);
		$taxon['do_display'] = !preg_match('/^\(.*\)$/', $taxon['taxon']);

		array_unshift($this->tmp,$taxon);

		if (!empty($taxon['parent_id'])) {
			$this->_getTaxonClassification($taxon['parent_id']);
		}

	}

	private function getContactLink()
	{
        $d = $this->models->ControllerModel->getProjectLeadExpert(array(
            'projectId' => $this->getCurrentProjectId()
        ));
        $email = 'linnaeus@naturalis.nl';
        if (!empty($d)) {
            $email = rawurlencode($d['first_name'] . ' ' . $d['last_name'] . '<' . $d['email_address'] . '>');
        }

		if (isset($_SESSION['app']['project']) && isset($_SESSION['app']['project']['title'])) {
			return str_rot13('<a title="' .
                (!empty($d['email_address']) ? $d['email_address'] : $email) .
			    '" href="mailto:' . $email . '?subject=' . rawurlencode($_SESSION['app']['project']['title']) .
				'" rel="nofollow">' . $this->translate('contact') . '</a>');
		}
	}

    private function setControllerParams ($p)
    {
        if (isset($p['checkForProjectId']))
		{
            $this->setCheckForProjectId($p['checkForProjectId']);
		}
    }

    private function setCheckForProjectId ($state)
    {
        if (is_bool($state))
            $this->_checkForProjectId = $state;
    }

    private function getCheckForProjectId ()
    {
        return $this->_checkForProjectId;
    }

    private function previewOverlay ()
    {
        if ($this->_allowEditOverlay === false)
            return;

        $d = $this->controllerBaseName . ':' . $this->viewName;

        if ($this->rHasVar('cat') && !is_numeric($this->rGetVal('cat')) && isset($this->generalSettings['urlsToAdminEdit'][$d . ':' . $this->rGetVal('cat')])) {

            $d = $d . ':' . $this->rGetVal('cat');
        }

        if ($this->isLoggedInAdmin() && $this->allowEditPageOverlay && isset($this->generalSettings['urlsToAdminEdit'][$d])) {

            if ($this->rHasId()) {

                $id = $this->rGetId();

                if ($this->controllerBaseName == 'module') {
                    $modId = $this->getCurrentModule();
                    $modId = $modId['id'];
                }
                else if ($this->controllerBaseName == 'matrixkey') {
                    $id = $this->getCurrentMatrixId();
                }
                else if ($this->controllerBaseName == 'key') {
                    $id = $this->getCurrentKeyStepId();
                }

                $this->smarty->assign('urlBackToAdmin',
                sprintf($this->generalSettings['urlsToAdminEdit'][$d], $id, ($this->rHasVar('cat') && is_numeric($this->rGetVal('cat')) ? $this->rGetVal('cat') : ($this->controllerBaseName == 'module' ? $modId : null))));
                $this->smarty->display('../shared/preview-overlay.tpl');
            }
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
        
        $this->smarty->display(strtolower((!empty($templateName) ? $templateName : $this->getViewName()) . '.tpl'), 
            $this->setSmartyCacheId());

        $this->previewOverlay();
    }

    /**
     * Renders and returns the page
     *
     * @access     public
     */
    public function fetchPage ($templateName = null)
    {
        $this->preparePage();

        return $this->smarty->fetch(strtolower((!empty($templateName) ? $templateName : $this->getViewName()) . '.tpl'),
            $this->setSmartyCacheId());

        //$this->previewOverlay(); // not implemented in (the rarely used) fetch
    }
    
	public function smartyGetSnippet($params, $content, &$smarty, &$repeat)
	{
		if ( is_null($content) ) return;

		$pu = $this->getProjectUrl('projectSnippets'); // project-specific dir
		$gu = $_SESSION['app']['system']['urls']['snippets']; // general dir

		$possibilities[1] = $pu . $content;

		if( isset($params['language']) )
		{
			$d=pathinfo( $possibilities[1] );
			$possibilities[0] = $pu . $d["filename"] . '--' . sprintf('%04s',$params['language']) . '.' . $d["extension"];
		}

		$possibilities[3] = $gu . $content;

		if( isset($params['language']) )
		{
			$d=pathinfo( $possibilities[3] );
			$possibilities[2] = $gu . $d["filename"] . '--' . sprintf('%04s',$params['language']) . '.' . $d["extension"];
		}

		foreach($possibilities as $file)
		{
			if (file_exists($file)) return @file_get_contents($file);
		}
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

		foreach ((array) $modules as $key => $val) {

			if (isset($p['ignore']) && in_array($val['module_id'],(array)$p['ignore'])) continue;

			$mp = $this->models->Modules->_get(array(
				'id' => $val['module_id']
			));

			$modules[$key]['module'] = $mp['module'];
			$modules[$key]['description'] = $mp['description'];
			$modules[$key]['controller'] = $mp['controller'];
			$modules[$key]['show_order'] = $mp['show_order']; //isset($val['show_order']) ? $val['show_order'] : $mp['show_order'];
			$modules[$key]['show_in_public_menu'] = $mp['show_in_public_menu'];
		}

		$this->customSortArray($modules, array(
			'key' => 'show_order',
			'maintainKeys' => true
		));

		$freeModules = $this->models->FreeModulesProjects->_get($p);

		return array(
			'modules' => $modules,
		    'freeModules' => !empty($freeModules) ? $freeModules : []
		);

    }
    
    /**
     * Checks if module is published/active
     * 
     * Check is necessary to see if data is module is published. If not, data should be hidden.
     * 
     * @param string $m Module controller or name
     * @return boolean
     */
    public function isProjectModulePublished ($m)
    {
        foreach ($this->getProjectModules(['active' => 'y']) as $modules) {
            foreach ($modules as $module) {
                if ((isset($module['controller']) && $module['controller'] == strtolower($m)) || 
                    strtolower($module['module']) == strtolower($m)) {
                    return true;
                }
            }
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
		$this->helpers->CustomArraySort->setSortyBy( $sortBy );
		$this->helpers->CustomArraySort->sortArray( $array );
		$array=$this->helpers->CustomArraySort->getSortedArray();
    }

    /**
     * Sets project URL for project images
     *
	 * @todo	take out hard reference to /media/
     * @access     public
     */
    public function setUrls ()
    {
        $_SESSION['app']['system']['urls']['systemMedia'] = $this->baseUrl . $this->getAppName() . '/media/system/skins/' . $this->getSkinName() . '/';
        $_SESSION['app']['system']['urls']['snippets'] = $this->baseUrl . 'app/media/project/_snippets/' ;

        $p = $this->getCurrentProjectId();

        if (!$p)
            return;

        $pCode = $this->getProjectFSCode($p);

        // url of the directory containing user-uploaded media files
        if (isset($this->generalSettings['urlUploadedProjectMedia'])) {
            $u['uploadedMedia'] = $this->generalSettings['urlUploadedProjectMedia'] . $pCode . '/';
        }
        else {
            $u['uploadedMedia'] = $this->baseUrl . $this->getAppName() . '/media/project/' . $pCode . '/';
        }

        $u['uploadedMediaThumbs'] = $u['uploadedMedia'] . 'thumbs/';
        $u['cache'] = $this->baseUrl . 'shared/cache/' . $pCode . '/';

        // urls of the directory containing project specific media part of the interface, but not of the content (background etc)
        $u['projectMedia'] = $this->baseUrl . 'shared/media/project/' . $pCode . '/';
        $u['projectL2Maps'] = $u['projectMedia'] . 'l2_maps/';
        $u['projectSystemOverride'] = $u['projectMedia'] . 'system_override/';

        // urls of the directory containing media that are constant across projects (but can be skinned)
        $u['systemMedia'] = $_SESSION['app']['system']['urls']['systemMedia'];
        $u['systemL2Maps'] = $this->baseUrl . 'shared/media/system/l2_maps/';

        // urls of css-files, either project-specific - if they exist - or generic
        $u['cssRootDir'] = $this->baseUrl . $this->getAppName() . '/style/';

        /*
		if (file_exists($projectCssDir.$pCode.'/basics.css')) {
			$u['projectCSS'] = $u['cssRootDir'].$pCode.'/';
		} else {
			$u['projectCSS'] = $u['cssRootDir'].'default/'.$this->getSkinName().'/';
		}
		*/

        if (file_exists($u['cssRootDir'] . $this->getSkinName() . '/basics.css')) {
            $u['projectCSS'] = $u['cssRootDir'] . $this->getSkinName() . '/';
        }
        else {
            $u['projectCSS'] = $u['cssRootDir'] . 'default/' . $this->getSkinName() . '/';
        }

        $u['projectSnippets'] =  $_SESSION['app']['system']['urls']['snippets'] . $pCode . '/';

        $_SESSION['app']['project'][$this->spid()]['urls'] = $u;
    }

    public function getProjectUrl($type=null)
    {
		if (is_null($type) && isset($_SESSION['app']['project'][$this->spid()]['urls']))
			return $_SESSION['app']['project'][$this->spid()]['urls'];

		if (isset($_SESSION['app']['project'][$this->spid()]['urls'][$type]))
			return $_SESSION['app']['project'][$this->spid()]['urls'][$type];
    }

    public function makeCustomCssFileName ($incProjectName = true, $p = null)
    {
        if ($incProjectName && isset($_SESSION['app']['project']['filesys_name']))
            return $this->baseUrl . $this->getAppName() . '/style/custom/' . $this->getProjectFSCode($p) . '--' . $_SESSION['app']['project']['filesys_name'] . '.css';
        else
            return $this->baseUrl . $this->getAppName() . '/style/custom/' . $this->getProjectFSCode($p) . '.css';
    }

    public function setCssFiles ()
    {

		if (isset($this->cssToLoad))
			$this->cssToLoad=array_merge($this->cssToLoadBase,$this->cssToLoad);
		else
			$this->cssToLoad=$this->cssToLoadBase;

        if (!is_null($this->getProjectUrl('projectCSS'))) {

            foreach ((array) $this->cssToLoad as $key => $val)
                $this->cssToLoad[$key] = $this->getProjectUrl('projectCSS').$val;
        }

        array_unshift($this->cssToLoad, '../utilities/dynamic-css.php');

        if (!is_null($this->getCurrentProjectId())) {

            $d = $this->makeCustomCssFileName();

            if (file_exists($d)) {

                array_push($this->cssToLoad, $d);
            }
            else {

                $d = $this->makeCustomCssFileName(false);

                if (file_exists($d))
                    array_push($this->cssToLoad, $d);
            }
        }
    }


    /*

		expected format of returned data:

			json_encode(
				array(
					'module' => 'module name (optional)',
					'url' => '../relative/url/to/item?id=%s',
					'results' => array(
						'id' => id of item,
						'label' => 'text to display',
						'source' => 'data source (optional)'
					)
				)
			);

	*/
    public function makeLookupList ($p)
    {

		$data=isset($p['data']) ? $p['data'] : null;
		$module=isset($p['module']) ? $p['module'] : null;
		$url=isset($p['url']) ? $p['url'] : null;
		$sortData=isset($p['sortData']) ? $p['sortData'] : false;
		$encode=isset($p['encode']) ? $p['encode'] : true;
		$total=isset($p['total']) ? $p['total'] : null;

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
            'total' => $total
        );

        return $encode ? json_encode($d) : $d;
    }

    public function isLoggedInAdmin ()
    {
        if (!isset($_SESSION['admin']['project']['id']))
            return false;
        else
            return $this->getCurrentProjectId() == $_SESSION['admin']['project']['id'];
    }

    public function doesEntryProgramExist ()
    {

        // ofcourse just these three aren't nowhere near sufficient to run the admin, but it's a good indication
        return (file_exists($this->generalSettings['lngFileRoot'] . 'configuration/admin/configuration.php') && file_exists($this->generalSettings['lngFileRoot'] . 'configuration/admin/controllers/Controller.php') &&
         file_exists($this->generalSettings['lngFileRoot'] . 'www/admin/views/utilities/admin_index.php'));
    }

    public function doesCurrentProjectHaveModule ($mpCode)
    {
		$d=$this->models->ModulesProjects->_get(array(
		  'id'=> array(
		      'project_id' => $this->getCurrentProjectId(),'
		      active' => 'y',
		      'module_id'=>$mpCode)
		));
		return isset($d[0]) ? true : false;
    }

    /**
     * Starts the user's session
     *
     * @access     private
     */
    private function startSession ()
    {

        //session_name('lng-application');
        if (session_id() == '')
            session_start();

            /* DEBUG */
        // $_SESSION['app']['system']['server_addr'] = $_SERVER['SERVER_ADDR'];
    }

    private function getProjectDependentTemplates ()
    {
        $p = $this->getCurrentProjectId();

        if (is_null($p))
            return;

        $r = null;

        $d = $this->_smartySettings['dir_template'] . $this->getSkinName() . '/' . 'shared/' . $this->getProjectFSCode($p) . '/';

        if (file_exists($d . '_header-container.tpl'))
            $r['header_container'] = $d . '_header-container.tpl';
        if (file_exists($d . '_main-menu.tpl'))
            $r['main_menu'] = $d . '_main-menu.tpl';
        if (file_exists($d . '_page-start.tpl'))
            $r['main_menu'] = $d . '_page-start.tpl';
        if (file_exists($d . '_footer.tpl'))
            $r['footer'] = $d . '_footer.tpl';

        return $r;
    }

    /**
     * Assigns basic Smarty variables
     *
     * @access     public
     */
    private function preparePage ()
    {
        if (isset($this->cssToLoad))
            $this->smarty->assign('cssToLoad', $this->cssToLoad);
        if (isset($this->jsToLoad))
            $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);
        if (isset($_SESSION['app'][$this->spid()]['project']['languages']))
            $this->smarty->assign('languages', $_SESSION['app'][$this->spid()]['project']['languages']);

        if (!empty($this->getRobotsDirective()))
			$this->smarty->assign('robotsDirective', $this->getRobotsDirective());	


        $this->smarty->assign('currentLanguageId', $this->getCurrentLanguageId());
        $this->smarty->assign('menu', $this->getMainMenu());
        $this->smarty->assign('controllerMenuExists', $this->includeLocalMenu && file_exists($this->smarty->getTemplateDir(0) . '_menu.tpl'));
        $this->smarty->assign('customTemplatePaths', $this->getProjectDependentTemplates());
        $this->smarty->assign('session', $_SESSION);
        $this->smarty->assign('rnd', $this->getRandomValue());
        $this->smarty->assign('requestData', $this->requestData);
        $this->smarty->assign('baseUrl', $this->baseUrl);
        $this->smarty->assign('projectUrls', $this->getProjectUrl());
        $this->smarty->assign('controllerBaseName', $this->controllerBaseName);
        $this->smarty->assign('controllerPublicName', $this->controllerPublicName);
        $this->smarty->assign('backlink', $this->getBackLink());
        $this->smarty->assign('errors', $this->getErrors());
        $this->smarty->assign('messages', $this->getMessages());
        $this->smarty->assign('pageName', $this->getPageName());
        $this->smarty->assign('showBackToSearch', $this->showBackToSearch);
        $this->smarty->assign('addedProjectIDParam', $this->generalSettings['addedProjectIDParam']);
        $this->smarty->assign('searchResultIndexActive', $this->getSearchResultIndexActive());
        $this->smarty->assign('spid', $this->spid());
        $this->smarty->assign('currdate', array('year'=>date('Y'),'month'=>date('m'),'day'=>date('d')));
        $this->smarty->assign('contact', $this->getContactLink());
		$this->smarty->assign('server_name', $this->server_name);
		$this->smarty->assign('current_url', $this->helpers->CurrentUrl->getParts());
		$this->smarty->assign('show_advanced_search_in_public_menu', $this->getSetting('show_advanced_search_in_public_menu',1)==1 );
		$this->smarty->assign('googleAnalyticsCode', $this->getGoogleAnalyticsCode());
		$this->smarty->assign('generalHeaderSubtitle', $this->getGeneralHeaderSubtitle());
		$this->smarty->assign('hasTraits', $this->isProjectModulePublished('traits'));
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

    private function loadSmartyConfig ()
    {
        $this->_smartySettings = $this->config->getSmartySettings();
    }

    /**
     * Sets class variables, based on a page's url
     *
     * Sets the following:
     * full path ('/admin/views/projects/collaborators.php')
     * application name ('app')
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

        $_SESSION['app']['system']['path'] = $path;

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
            $this->viewName = $path['filename'];

        $this->_fullPathRelative = $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/' . $this->viewName . '.php';

        if (empty($this->appName))
            $this->log('No application name set', 2);
        if (empty($this->viewName))
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
		$this->moduleSession->setModule(array(
            'environment' => 'app',
		    'controller' => $this->controllerBaseName,
		    'projectId' => $this->spid()
		));
	}

	private function getCurrentPathWithProjectlessQuery()
	{

		$d = array();
		$boom = explode('&',$_SERVER['QUERY_STRING']);
		$boom = array_map('strtolower', $boom);
		foreach((array)$boom as $val) {
			if (strpos($val,$this->generalSettings['addedProjectIDParam'].'=')===0)
				continue;
			$d[]=$val;
		}
		sort($d);
		return '../' . $this->controllerBaseName . '/' . $this->viewName . '.php' . (count($d)>0 ? '?'.implode('&',$d) : '');

	}

    private function setSkinName ()
    {
		if ($this->controllerBaseName=='webservices')
		{
			$_SESSION['app']['system']['skinName'] = $this->generalSettings['app']['skinNameWebservices'];

		} 
		else
		{
			$skin = $this->getSetting('skin');

			if (isset($skin) && $this->doesSkinExist($skin))
			{
				$_SESSION['app']['system']['skinName'] = $skin;
			}
			else
			{
				$_SESSION['app']['system']['skinName'] = $this->generalSettings['app']['skinName'];
			}
		}
    }

    private function doesSkinExist ($skin)
    {
		$d = array(
			$this->baseUrl . $this->getAppName() . '/style/' . $skin . '/',
			$this->baseUrl . $this->getAppName() . '/media/system/skins/' . $skin . '/',
			$this->_smartySettings['dir_template'] . $skin . '/' . $this->getControllerBaseName() . '/'
		);

		if (false)
		{
			echo '<!-- template folders:'.chr(10);
			foreach((array)$d as $val)
				echo '  '.$val.': '.(file_exists($val) ? 'ok' : 'missing' ).chr(10);
			echo '-->'.chr(10);
		}

		foreach((array)$d as $val)
			if (!file_exists($val)) return false;

		return true;

    }

    public function getSkinName ()
    {
        if (!isset($_SESSION['app']['system']['skinName']))
            $this->setSkinName();

        return $_SESSION['app']['system']['skinName'];
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
        }
        elseif (isset($this->usedModelsBase)) {

            $d = $this->usedModelsBase;
        }
        elseif (isset($this->usedModels)) {

            $d = $this->usedModels;
        }
        else {

            return;
        }

        $this->models = new stdClass();

        // Load base controller model first
		require_once __DIR__ . '/../models/ControllerModel.php';
		$this->models->ControllerModel = new ControllerModel;

        $t = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $this->getControllerBaseName())))) . 'Model';

        if (file_exists(__DIR__ . '/../models/' . $t . '.php'))
		{
            require_once __DIR__ . '/../models/' . $t . '.php';
            $this->models->$t = new $t;
        }

		// Load controller-specific model by override
		if ( isset($this->modelNameOverride) )
		{
			if (file_exists(__DIR__ . '/../models/' . $this->modelNameOverride . '.php'))
			{
				require_once __DIR__ . '/../models/' . $this->modelNameOverride . '.php';
				$this->models->{$this->modelNameOverride} = new $this->modelNameOverride;
			}
		}

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

    protected function extendUsedModels ()
    {
        if (isset($this->usedModelsExtended) && is_array($this->usedModelsExtended)) {
            $this->usedModels = array_unique(array_merge((array) $this->usedModels, (array) $this->usedModelsExtended));
        }
    }

    /**
     * Sets general Smarty variables (paths, compilder directives)
     *
     * @access     private
     */
    private function setSmartySettings ()
    {
        // this is now done in $this->loadSmartyConfig(); because some settings are needed earlier in the bootstrap
        //$this->_smartySettings = $this->config->getSmartySettings();
        $this->smarty = new Smarty();

        /* DEBUG */
        //$this->smarty->force_compile = true;

        $this->smarty->template_dir = $this->_smartySettings['dir_template'] . $this->getSkinName() . '/' . $this->getControllerBaseName() . '/';
        $this->smarty->compile_dir = $this->_smartySettings['dir_compile'];
        $this->smarty->cache_dir = $this->_smartySettings['dir_cache'];
        $this->smarty->config_dir = $this->_smartySettings['dir_config'];
        $this->smarty->caching = $this->_smartySettings['caching'];
        $this->smarty->compile_check = $this->_smartySettings['compile_check'];
		$this->smarty->registerPlugin("block","t", array($this,"smartyTranslate"));
		$this->smarty->registerPlugin("block","snippet", array($this,"smartyGetSnippet"));
		$this->smarty->error_reporting = E_ALL & ~E_NOTICE;
		
		// Ruud: test!
		$this->smarty->caching = 0;
		
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
        }
        elseif (isset($this->usedHelpersBase)) {

            $d = $this->usedHelpersBase;
        }
        elseif (isset($this->usedHelpers)) {

            $d = $this->usedHelpers;
        }
        else {

            return;
        }

        $this->helpers = new stdClass();

        foreach ((array) $d as $key) {
        	
             if (file_exists(__DIR__ . '/../helpers/' . $key . '.php')) {

                require_once (__DIR__ . '/../helpers/' . $key . '.php');

                $c = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

                if (class_exists($c)) {

                    $this->helpers->$c = new $c();
                }
            }
        }
    }

    private function initLogging ()
    {

		$logDir = $this->generalSettings['directories']['log'] . '/';
        $logFile = $this->getAppName() ? $this->getAppName() : 'general.log';

		if ((!file_exists($logDir) || !is_writable($logDir)) && @!mkdir($logDir)) {

        	echo '<p>The log file path does not exist or is not writeable.
        	    Linnaeus NG cannot progress until this is corrected:</p>'.
				$logDir;

        	die();
		}
        if (file_exists($logDir . $logFile) && !is_writable($logDir . $logFile)) {

            echo '<p>The main log file is not writeable. Linnaeus NG cannot progress until this is corrected:</p>' .
                $logDir . $logFile;

            die();
        }

        $this->helpers->LoggingHelper->setLogFile($logDir . $logFile );
        $this->helpers->LoggingHelper->setLevel(0);
    }

    private function removeBackstepFromUrl ($url)
    {
        return str_replace(array(
            '?backstep=1',
            '&backstep=1'
        ), '', $url);
    }

    private function getBackLink ()
    {
        // if there is no history, there is no going back
        if (!isset($_SESSION['app']['user']['history']))
            return;

            // get the last history item (this does *not* include the current page, which is saved at destriction)
        $page = end($_SESSION['app']['user']['history']);

        // check whether the current page is the same as the previous (reload); if so, get on earlier from history
        if ($page == array(
            'name' => $this->pageName,
            'url' => $this->removeBackstepFromUrl($_SERVER['REQUEST_URI'])
        ))
            $page = prev($_SESSION['app']['user']['history']);

            // clean up the name
        if (isset($page['name']))
            $page['name'] = strip_tags(str_replace('"', '', $page['name']));

            // add backstep=1 to the url to be able to identify it as a step back in history
        if (isset($page['url']))
            $page['url'] .= (strpos($page['url'], '?') === false ? '?' : '&') . 'backstep=1';

        return $page;
    }

    private function storePageHistory ()
    {

        // no pagename or explicit no storing: don't store
        if (empty($this->pageName) || $this->storeHistory == false)
            return;

            // undocumented history reset for development purposes through ?reset=history
        if ($this->rHasVal('reset', 'history'))
            unset($_SESSION['app']['user']['history']);

            // current page name & url (combination considered to be unique *after* stripping the backstep parameter)
        $thisPage = array(
            'name' => $this->pageName,
            'url' => $this->removeBackstepFromUrl($_SERVER['REQUEST_URI'])
        );

        // check if history exists, if so, get the last item: the previous visited page
        if (isset($_SESSION['app']['user']['history']))
            $prevPage = end($_SESSION['app']['user']['history']);

            // if there is no previous page (if there is no history) or there is and it is different from the current (no reload), store in history
        if (!isset($prevPage) || $thisPage != $prevPage)
            $_SESSION['app']['user']['history'][] = $thisPage;

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

    private function embedNoLink ($matches)
    {
        if (trim($matches[0]) == '')
		{
            return $matches[0];
        }
        else
		{
            $d = $this->generateRandomHexString('###','###');

            while (isset($this->_hotwordNoLinks[$d]))
			{
                $d = $this->generateRandomHexString('###','###');
            }

            $this->_hotwordNoLinks[$d] = array('str'=>$d,'orig'=>$matches[0]);

            return $d;
        }
    }

    private function embedHotwordLink( $matches )
    {
        if (trim($matches[0]) == '')
		{
            return $matches[0];
        }
        else
		{
            $d = $this->generateRandomHexString('@@@','@@@');

            while (isset($this->_hotwordTempLinks[$d]))
			{
                $this->generateRandomHexString('@@@','@@@');
            }

            $this->_hotwordTempLinks[] = array(
                'str' => $d,
                'link' => '<a href="' . $this->_currentHotwordLink . '">' . $matches[0] . '</a>'
            );
            return $d;
        }
    }

    private function effectuateHotwordLinks( $txt )
    {
        $this->_hotwordTempLinks = array_reverse($this->_hotwordTempLinks);
        foreach ((array) $this->_hotwordTempLinks as $val)
		{
            $txt = str_replace($val['str'], $val['link'], $txt);
        }

        $this->_hotwordTempLinks = array();

        return $txt;
    }

    private function restoreNoLinks( $txt )
    {
        foreach ((array) $this->_hotwordNoLinks as $val)
		{
            $txt = str_replace($val['str'], str_ireplace(array(
                '[no]',
                '[/no]'
            ), '', $val['orig']), $txt);
        }

        $this->_hotwordNoLinks = array();

        return $txt;
    }


    public function getProjectFSCode($p = null)
    {
        $p = is_null($p) ? $this->getCurrentProjectId() : $p;
        return sprintf('%04s', $p);
    }


    public function setStoreHistory($state)
    {
        $this->storeHistory = $state;
    }


    /*

		stores state my 'remembering' the value of four possible variables.
		also remembers the last visited page, as some modules have other pages than index linked from
		  the same tabs that in other modules link to the index with just different variables

		caveats
		-> the place in the keypath is stored in the KeyController itself
		-> matrix states are stored in the MatrixController itself

	*/
    public function storeState ()
    {
        if ($this->storeHistory === false || session_id() == '')
            return;

        unset($_SESSION['app']['user']['states'][$this->controllerBaseName]);

        $d = null;

        if ($this->rHasId())
            $d['id'] = $this->rGetId();
        if ($this->rHasVal('cat'))
            $d['cat'] = $this->rGetVal('cat');
        if ($this->rHasVal('m'))
            $d['m'] = $this->rGetVal('m');
        if ($this->rHasVal('letter'))
            $d['letter'] = $this->rGetVal('letter');

        $d['lastPage'] = $_SERVER['REQUEST_URI'];

        $_SESSION['app']['user']['states'][$this->getControllerBaseName()] = $d;
    }

    /*

		restores state by recalling variable values and manipulating the corresponding values in $this->requestData
		in some cases, redirects to another page within the module

		caveats
		-> restoration of the place in the keypath is done in KeyController::indexAction (when called without any parameters)
		-> restoration of matrix states is done in MatrixController::identifyAction
		-> the function SpeciesController::setLastViewedTaxonIdForTheBenefitOfTheMapkey in some cases destroys the value of
		   $_SESSION['app']['user']['states']['mapkey'], so that the mapkey, when accesses from the main menu, automatically shows
		   the distribution of the taxon last seen in the species module

		update: the matrix has now been removed from this function

	*/
    private function restoreState ()
    {
		if ($this->getSetting('suppress_restore_state')==1)
			return;

		if ($this->getControllerBaseName() == 'mapkey')
			return;

		if ($this->getControllerBaseName() == 'matrixkey')
			return;

        if (!isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()]))
            return;

        $thisUrl = $this->removeBackstepFromUrl($_SERVER['REQUEST_URI']);

        // /app/views/mapkey/ vs /app/views/mapkey/index.php
        $d = strpos($thisUrl, '?') == false ? $thisUrl : substr($thisUrl, 0, strpos($thisUrl, '?'));
        $requestHasNoFileName = $this->getViewName() == 'index' && ($d !== $_SERVER['PHP_SELF']);

        if (
			($this->getControllerBaseName() == 'index') && $requestHasNoFileName &&
			(isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage']) && $_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage'] != $thisUrl)
		)
        {

            $this->redirect($_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage']);
        }

        if (isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()]))
		{
            foreach ((array) $_SESSION['app']['user']['states'][$this->getControllerBaseName()] as $key => $val)
			{
                if (!isset($this->requestData[$key]) && $key!='lastPage')
                    $this->requestData[$key] = $val;
            }
        }
    }

    private function checkWriteableDirectories ()
    {

        $paths = array(
            $this->_smartySettings['dir_compile'] => 'www/app/templates/templates_c',
            $this->_smartySettings['dir_cache'] => 'www/app/templates/cache',
            $this->generalSettings['directories']['mediaDirProject'] => 'www/shared/media/project',
            $this->generalSettings['directories']['log'] => 'log',
            //$this->generalSettings['directories']['customStyle'] => 'www/app/style/custom'
        );

        $p = $this->getCurrentProjectId();

        foreach ((array) $paths as $val => $display)
		{
            if ((!file_exists($val) || !is_writable($val)) && @!mkdir($val))
			{
                 $fixPaths[] = $display;
            }
        }

        if (isset($fixPaths))
		{
        	echo '<p>Some required paths do not exist or are not writeable. Linnaeus NG cannot proceed until this has been corrected:</p>';

        	foreach ($fixPaths as $message)
			{
        		echo $message . '<br>';
        	}

        	die();
        }
    }

    private function saveInterfaceText ($text)
    {
        @$this->models->InterfaceTexts->save(array(
            'id' => null,
            'text' => $text,
            'env' => $this->getAppName()
        ));
    }

    private function doTranslate ($text)
    {
		// test (returns reversed strings)
        //return strrev($text);

        // get id of the text
        $i = $this->models->InterfaceTexts->_get(array(
            'id' => array(
                'text' => $text,
                'env' => $this->getAppName()
            ),
            'columns' => 'id'
        ));

        // if not found, return unchanged
        if (empty($i[0]['id']))
            return $text;

		// resolve language id
        $languageId = $this->getCurrentLanguageId();
        if (is_null($languageId))
            $languageId = $this->getDefaultLanguageId();

		// fetch appropriate translation
        $it = $this->models->InterfaceTranslations->_get(
        array(
            'id' => array(
                'interface_text_id' => $i[0]['id'],
                'language_id' => $languageId
            ),
            'columns' => 'translation'
        ));

        // if not found, return unchanged
        if (empty($it[0]['translation']))
            return $text;

            // return translation
        return $it[0]['translation'];
    }


	private function projectHasTaxa()
	{
		$t = $this->models->Taxa->_get(array('id' => array('project_id' => $this->getCurrentProjectId()), 'columns' => 'count(*) as total'));
		return ($t[0]['total']>0);
	}

    private function getTaxonChildren ($id)
    {
        if (is_null($this->_tmpTree)) {

            $d = $this->models->ControllerModel->getTaxa(array(
                'projectId' => $this->getCurrentProjectId()
            ));

            foreach ((array) $d as $val) {

				$val['commonnames'] = $this->models->ControllerModel->getTaxonCommonNames(array(
				    'projectId' => $this->getCurrentProjectId(),
    				'taxonId' => $val['id']
				));

                $this->_tmpTree[$val['parent_id']][$val['id']] = $val;
            }
        }

        return isset($this->_tmpTree[$id]) ? $this->_tmpTree[$id] : null;
    }

	public function setSessionVar($name,$value=null)
	{
		if (is_null($name))
			return;

		if (is_null($value))
		{
			if (!is_array($name))
				unset($_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name]);
			else
				unset($_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name[0]][$name[1]]);
		}

		if (!is_array($name))
			$_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name]=$value;
		else
			$_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name[0]][$name[1]]=$value;
	}

	public function getSessionVar($name)
	{
		if (is_null($name))
			return;

		if (!is_array($name))
		{
			if (!isset($_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name]))
				return null;
			else
				return $_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name];
		}
		else
		{
			if (!isset($_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name[0]][$name[1]]))
				return null;
			else
				return $_SESSION['app'][$this->spid()][$this->getControllerBaseName()][$name[0]][$name[1]];
		}

	}

	private function setDatabaseLocaleSettings( $language_id )
	{
		$lng=$this->models->Languages->_get(array("id"=>array("id"=>$language_id)));

		$locale=
			isset($lng) && !empty($lng[0]['locale_lin']) ?
				$lng[0]['locale_lin'] :
				$this->getSetting('db_lc_time_names','nl_NL');

		$this->models->ControllerModel->setLocale($locale);
	}

    private function generateRandomHexString ($pre=null,$post=null)
    {
        return $pre.substr(md5(rand()), 0, 16).$post;
    }

	private function initTranslator()
	{
		include_once ('TranslatorController.php');
		$this->translator = new TranslatorController('app',$this->getCurrentLanguageId());
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


	protected function addHybridMarkerAndInfixes( $p )
	{
		$base_rank_id=isset($p['base_rank_id']) ? $p['base_rank_id'] : null;

		if ( defined('NOTHOVARIETAS_RANK_ID') && $base_rank_id==NOTHOVARIETAS_RANK_ID )
		{
			$p['name']=$this->addHybridMarker( $p );
			return $this->addVarietasInfix( $p );
		}
		else
		if ( defined('NOTHOSUBSPECIES_RANK_ID') && $base_rank_id==NOTHOSUBSPECIES_RANK_ID )
		{
			$p['name']=$this->addHybridMarker( $p );
			return $this->addSubspeciesInfix( $p );
		}
		else
		if ( defined('NOTHOGENUS_RANK_ID') && defined('NOTHOSPECIES_RANK_ID') && ($base_rank_id==NOTHOGENUS_RANK_ID ||
			 $base_rank_id==NOTHOSPECIES_RANK_ID) )
		{
			return $this->addHybridMarker( $p );
		}
		else
		if ( $base_rank_id==VARIETAS_RANK_ID  )
		{
			return $this->addVarietasInfix( $p );
		}
		else
		if ( $base_rank_id==SUBSPECIES_RANK_ID  )
		{
			return $this->addSubspeciesInfix( $p );
		}
		else
		if ( $base_rank_id==FORMA_RANK_ID )
		{
			return $this->addFormaInfix( $p );
		}

		if ( !empty($p['specific_epithet']) )
		{
			return $p['specific_epithet'];
		}
		else
		if ( !empty($p['uninomial']) )
		{
			return $p['uninomial'];
		}
		else
		if ( !empty($p['name']) )
		{
			return $p['name'];
		}
	}

	protected function addHybridMarker( $p )
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
				$parent_id=$this->getTaxonById($taxon_id)['parent_id'];
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
			}
			else
			{
				/*
				/$ied=explode(' ', $name, 2);
				return $ied[0]. '  ' . $marker . $ied[1];
				*/
			    $f = strip_tags($name);
			    $ied=explode(' ', $f, 2);
				$r = $ied[0]. ' ' . $marker . $ied[1];
				return str_replace($f, $r, $name);

			}
		}
		else
		{
			if ( !empty($specific_epithet) )
			{
				return $specific_epithet;
			}
			else
			if ( !empty($uninomial) )
			{
				return $uninomial;
			}
			else
			{
				return $name;
			}
		}
	}

	protected function addVarietasInfix( $p )
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
				$ied[2] = '<span class="no-italics">' . $marker . '</span>' . ' ' . $ied[2];
				return implode(' ',$ied);
			}
		}
		else
		if ( $base_rank_id==NOTHOVARIETAS_RANK_ID )
		{
			// REFAC2015: $this->_nothoInfixPrefix . $marker --> should come from ranks.abbreviation
			$marker=$this->getShowAutomaticInfixes() ? $this->_nothoInfixPrefix . $marker : '';

			if ( !empty($infra_specific_epithet) )
			{
				return $marker . $specific_epithet;
			}
			else
			if ( !empty($name) && strpos($name,' ')!==false )
			{
				$ied=explode( ' ',  $name );
				$ied[2] = '<span class="no-italics">' . $marker . '</span>' . ' ' . $ied[2];
				return implode(' ',$ied);
			}
		}

		return $name;
	}

	protected function addSubspeciesInfix( $p )
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
					$ied[2] = '<span class="no-italics">' . $marker . '</span>' . ' ' . $ied[2];
					return implode(' ',$ied);
				}
			}
		}
		else
		if ( $base_rank_id==NOTHOSUBSPECIES_RANK_ID )
		{
			// REFAC2015: $this->_nothoInfixPrefix . $marker --> should come from ranks.abbreviation
			$marker=$this->getShowAutomaticInfixes() ? $this->_nothoInfixPrefix . $marker : '';
			
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
					$ied[2] = '<span class="no-italics">' . $marker . '</span>' . ' ' . $ied[2];
					return implode(' ',$ied);
				}
				else
				{
					return $name;
				}

			}
		}
		
		return $name;
	}

	protected function addFormaInfix( $p )
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
				$ied[2] = '<span class="no-italics">' . $marker . '</span>' . ' ' . $ied[2];
				return implode(' ',$ied);
			}
		}
		return $name;
	}


	private function setShowAutomaticHybridMarkers()
	{
		$this->_showAutomaticHybridMarkers=$this->getSetting('show_automatic_hybrid_markers')==1;
	}

	private function setShowAutomaticInfixes()
	{
		$this->_showAutomaticInfixes=$this->getSetting('show_automatic_infixes')==1;
	}

	protected function getShowAutomaticHybridMarkers()
	{
		return $this->_showAutomaticHybridMarkers;
	}

	protected function getShowAutomaticInfixes()
	{
		return $this->_showAutomaticInfixes;
	}

	private function setRankIdConstants()
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

    protected function getCurrentModuleId ($remapModule = false)
    {
		$remap = array(
            'species' => 'nsr',
		    'literature' => 'literature2'
		);

        $c = $this->getControllerBaseName();

        // Required to map app controller to admin equivalent
		if ($remapModule && isset($remap[$remapModule])) {
            $c = $remap[$remapModule];
        }

        $d = $this->models->Modules->_get(array(
            "id" => array("controller" => $c)
		));

		return $d ? $d[0]['id'] : false;
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
	
	private function setSmartyCacheId ()
	{
	    if (!empty($this->requestData)) {
	        array_multisort($this->requestData);
	        return md5(json_encode($this->requestData));
	    }
	    return null;
	}

	protected function getNameTypeId( $predicate )
	{
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

	protected function checkGlobalAuthorization()
	{
		if ( $this->getSetting('front_end_use_basic_auth',0)==1)
		{
			$proceed=false;

			if ( isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) )
			{
				$proceed=$this->models->ControllerModel->verifyProjectUser(array(
					'project_id' => $this->getCurrentProjectId(),
					'username' => $_SERVER['PHP_AUTH_USER'],
					'password' => $_SERVER['PHP_AUTH_PW']
				));
			}

			if (!$proceed)
			{
				//header('WWW-Authenticate: Basic realm="' . $_SESSION['app']['project']['title'] . '"');
				header('WWW-Authenticate: Basic realm="Linnaeus NG"');
				header('HTTP/1.0 401 Unauthorized');
				die();
			}
		}
	}

	protected function setServerName()
	{
		$this->server_name=trim(@shell_exec( "hostname" ));
	}

    protected function getProtocol()
    {
        if (isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], ['on', 1]) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return 'https://';
        }
        return 'http://';
    }

    protected function setGoogleAnalyticsCode()
	{
		$d=$this->models->ControllerModel->getSetting(array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => GENERAL_SETTINGS_ID,
			'setting' => 'google_analytics_code',
		    'use_default' => true,
		));

		if ($d)
		{
			$this->_googleAnalyticsCode = empty(json_decode($d)) ? $d : json_decode($d);
		}
	}

	protected function getGoogleAnalyticsCode()
	{
		return $this->_googleAnalyticsCode;
	}

	protected function getProjectRanksAbbreviations ()
	{
        $ranks = $this->getProjectRanks();
        foreach ($ranks as $r) {
            if (!empty($r['abbreviation'])) {
                $abr[] = $r['abbreviation'];
            }
        }
        return isset($abr) ? $abr : null;
	}

	protected function setGeneralHeaderSubtitle()
	{
		$d=$this->models->ControllerModel->getSetting(array(
			'project_id' => $this->getCurrentProjectId(),
			'module_id' => GENERAL_SETTINGS_ID,
			'setting' => 'site_header_subtitle'
		));

        $this->_generalHeaderSubtitle = $d ? $d : null;
	}

	protected function getGeneralHeaderSubtitle()
	{
		return $this->_generalHeaderSubtitle;
	}

	protected function setRobotsDirective( $r )
	{
		$this->_robotsDirective=$r;
	}

	protected function getRobotsDirective()
	{
		if (!is_null($this->_robotsDirective)) return is_array($this->_robotsDirective) ? $this->_robotsDirective : [$this->_robotsDirective];
	}

    /*
     * Used to pull external data from an api using a curl request. Parameter can
     * either be a url or an array with additional parameters (post data and timeout).
     */
    protected function getCurlResult($p)
    {
        $url = is_array($p) && isset($p['url']) ? $p['url'] : (!empty($p) ? $p : false);
        $post = isset($p['post']) ? $p['post'] : false;
        $timeout = isset($p['timeout']) ? $p['timeout'] : 10;
        $verify = isset($p['verify']) ? $p['verify']  : true;
        $assoc = isset($p['assoc' ]) ? $p['assoc'] : true;

        if (!$url) {
            return '';
        }

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,$verify);
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

        $output = json_decode($result, $assoc);

        // Return raw output if result is no (valid) json
        return !is_null($output) ? $output : $result;
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
            if (!defined($code[0])) define($code[0], $id);
        }
     }
    
	protected function assignMobileDeviceInfo()
	{
		$this->smarty->assign('deviceInfo',[
			'isMobile'=>$this->helpers->MobileDetect->isMobile(),
			'isTablet'=>$this->helpers->MobileDetect->isTablet(),
			'isPhone'=>$this->helpers->MobileDetect->isPhone(),
			'isAndroidOS'=>$this->helpers->MobileDetect->isAndroidOS(),
			'isiOS'=>$this->helpers->MobileDetect->isiOS()
		]);
	}

    /**
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
     * MUST MATCH THE SAME METHOD IN ADMIN!
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

        // Base part
        $url = '<a href="' . $this->baseUrl . $this->appName . '/views/literature2/reference.php?id=' .
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
        if (!empty($r['periodical_id']) && isset($r['periodical_ref'])) {
            $pub .= $r['periodical_ref']['label'] . ' ';
        } else if (!empty($r['periodical'])) {
            $pub .= $r['periodical'] . ' ';
        }
        if (!empty($r['publishedin_id']) && !empty($r['publishedin_id'] && !empty($r['publishedin_ref']['label']))) {
            $pub .= 'In: ' . $r['publishedin_ref']['label'] . '. ';
        } else if (!empty($r['publishedin'])) {
            $pub .= 'In: ' . $r['publishedin'] . '. ';
        }

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

    public function getHttpHost ()
    {
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER["HTTP_HOST"];
        $parts = explode(',', $host);
        return $parts[0];
    }

}
 
 
 
