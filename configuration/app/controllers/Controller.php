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
	private $_checkForProjectId = true;

	private $_currentGlossaryId = false;
	private $_currentHotwordLink = false;
	private $_hotwordTempLinks = array();
	private $_hotwordNoLinks = array();

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
    public $showLowerTaxon = true;
	public $includeLocalMenu = true;
	public $allowEditPageOverlay = true;

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
		'rank',
		'glossary',
		'glossary_synonym',
		'hotword'
    );

    private $usedHelpersBase = array(
		'logging_helper'
    );
	
	public $cssToLoad = array();


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

        $this->setPhpIniVars();

        $this->setDebugMode();

        $this->startSession();

        $this->loadHelpers();

		$this->initLogging();

        $this->setNames();
		
        $this->loadControllerConfig();        
        
        $this->setUrls();
		
		$this->setPaths();
		
        $this->loadModels();

        $this->setRandomValue();

        $this->setSmartySettings();
        
        $this->setRequestData();

        $this->setProjectLanguages();
		
		$this->setCurrentLanguageId();

		$this->checkBackStep();

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

		if ($this->rHasVal('p')) $this->resolveProjectId();
	
		$d = $this->getCurrentProjectId();
		
		if ($d==null) $this->redirect($this->generalSettings['urlNoProjectId']);
		
		$this->setCurrentProjectData();
		$this->setUrls();
        $this->setProjectLanguages();
	
	}

	public function resolveProjectId()
	{
	
		if (!$this->rHasVal('p')) $this->setCurrentProjectId(null);

		if (is_numeric($this->requestData['p'])) {

			$p = $this->models->Project->_get(
				array(
					'id' => $this->requestData['p']
				)			
			);
			
			if (!$p)
				$this->setCurrentProjectId(null);
			else
				$this->setCurrentProjectId(intval($this->requestData['p']));

		} else {

			$pName = str_replace('_',' ',strtolower($this->requestData['p']));

			$p = $this->models->Project->_get(
				array(
					'id' => array('sys_name' => $pName)
				)			
			);

			if (!$p[0]) {

				$this->setCurrentProjectId(null);

			} else {

				$this->setCurrentProjectId(intval($p[0]['id']));

			}

		}
	
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

	public function getTreeList()
	{

		if (!isset($this->treeList)) return null;

		foreach((array)$this->treeList as $key => $val) {

			if ($this->showLowerTaxon) {
			
				if ($val['lower_taxon']=='1')  $d[$key] = $val;
			
			} else {

				if ($val['lower_taxon']=='0')  $d[$key] = $val;

				if ($val['lower_taxon']=='1')  continue;

			}

		}	

		return isset($d) ? $d : null;
	
	}

    public function getTaxonTree($params=null) 
    {

		if (
			(isset($params['forceLookup']) && $params['forceLookup']==true) ||
			!isset($_SESSION['app']['user']['species']['tree']) ||
			!isset($_SESSION['app']['user']['species']['treeList']) ||
			$this->didActiveLanguageChange()
		) {

			$_SESSION['app']['user']['species']['tree'] = $this->_getTaxonTree($params);
			$_SESSION['app']['user']['species']['treeList'] = isset($this->treeList) ? $this->treeList : null;

		} else {

			$this->treeList = $_SESSION['app']['user']['species']['treeList'];

		}

		return $_SESSION['app']['user']['species']['tree'];
	
	}

    public function _getTaxonTree($params) 
    {

		// the parent_id to start with
		$pId = isset($params['pId']) ? $params['pId'] : null;
		// the current level of depth in the tree
		$level = isset($params['level']) ? $params['level'] : 0;
		// a specific rank_id to stop the recursion; taxa below this rank are omitted from the tree
		$stopAtRankId = isset($params['stopAtRankId']) ? $params['stopAtRankId'] : null;
		// taxa without a parent_id that are not of the uppermost rank are orphans; these can be excluded from the tree
		$includeOrphans = isset($params['includeOrphans']) ? $params['includeOrphans'] : false;
		// force lookup
		$forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : null;

		// get all ranks defined within the project	
		$d = $this->getProjectRanks(array('idsAsIndex' => true,'forceLookup' => $forceLookup));
		$pr = $d['ranks'];

		// $this->treeList is an additional non-recursive list of taxa
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

			$id['rank_id'] = $d['topRankId'];

		}

		// get the child taxa of the current parent
        $t = $this->models->Taxon->_get(
				array(
					'id' =>  $id,
					'order' => 'taxon_order'
				)
			);

        foreach((array)$t as $key => $val) {

			// taxon name
			$val['label'] = $val['taxon'];

			// for each taxon, look whether they belong to the lower taxa...
			$val['lower_taxon'] = $pr[$val['rank_id']]['lower_taxon'];
	
			// ...and can be the endpoint of the key
			$val['keypath_endpoint'] = $pr[$val['rank_id']]['keypath_endpoint'];

			// level is effectively the recursive depth of the taxon within the tree
			$val['level'] = $level;

			// count taxa on the same level
			$val['sibling_count'] = count((array)$t);

			// sibling_pos reflects the position amongst taxa on the same level
			$val['sibling_pos'] = ($key==0 ? 'first' : ($key==count((array)$t)-1 ? 'last' : '-' ));

			// get rank label
			$val['rank'] = $pr[$val['rank_id']]['labels'][$this->getCurrentLanguageId()];

			// give do not display flag to taxa that are in brackets
			$val['do_display'] = !preg_match('/^\(.*\)$/',$val['taxon']);

			// fill the treelist (which is a global var)
            $this->treeList[$val['id']] = $val;

			$t[$key]['level'] = $level;
			
			// and call the next recursion for each of the children
			if (!isset($stopAtRankId) || (isset($stopAtRankId) && $stopAtRankId!=$val['rank_id'])) {

				$children = $this->_getTaxonTree(
					array(
						'pId' => $val['id'],
						'level' => $level+1,
						'stopAtRankId' => $stopAtRankId
					)
				);

	            $t[$key]['child_count'] = 
					$this->treeList[$val['id']]['child_count'] = 
					isset($children) ? count((array)$children) : 0;
				
				$t[$key]['children'] = $children;

			}

        }

        return $t;

    }

	public function getProjectRanks($params=false)
	{

		$includeLanguageLabels = isset($params['includeLanguageLabels']) ? $params['includeLanguageLabels'] : true;
		$lowerTaxonOnly = isset($params['lowerTaxonOnly']) ? $params['lowerTaxonOnly'] : false;
		$forceLookup = isset($params['forceLookup']) ? $params['forceLookup'] : $this->didActiveLanguageChange();
		$keypathEndpoint = isset($params['keypathEndpoint']) ? $params['keypathEndpoint'] : false;
		$idsAsIndex = isset($params['idsAsIndex']) ? $params['idsAsIndex'] : false;

		if (!$forceLookup) $forceLookup = !isset($_SESSION['app']['user']['species']['ranks']['projectRanks']);

		if (!$forceLookup) {

			if (
				!isset($_SESSION['app']['user']['species']['ranks']['includeLanguageLabels']) || 
				$_SESSION['app']['user']['species']['ranks']['includeLanguageLabels']!=$includeLanguageLabels ||
				!isset($_SESSION['app']['user']['species']['ranks']['lowerTaxonOnly']) || 
				$_SESSION['app']['user']['species']['ranks']['lowerTaxonOnly']!=$lowerTaxonOnly ||
				!isset($_SESSION['app']['user']['species']['ranks']['keypathEndpoint']) || 
				$_SESSION['app']['user']['species']['ranks']['keypathEndpoint']!=$keypathEndpoint ||
				!isset($_SESSION['app']['user']['species']['ranks']['idsAsIndex']) || 
				$_SESSION['app']['user']['species']['ranks']['idsAsIndex']!=$idsAsIndex
				)

				$forceLookup = true;

		}

		$_SESSION['app']['user']['species']['ranks']['includeLanguageLabels'] = $includeLanguageLabels;
		$_SESSION['app']['user']['species']['ranks']['lowerTaxonOnly'] = $lowerTaxonOnly;
		$_SESSION['app']['user']['species']['ranks']['keypathEndpoint'] = $keypathEndpoint;
		$_SESSION['app']['user']['species']['ranks']['idsAsIndex'] = $idsAsIndex;

		if ($forceLookup) {

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
			
			$topRankId = null;

			foreach((array)$pr as $rankkey => $rank) {
			
				if ($topRankId==null) $topRankId = $rankkey;
	
				$r = $this->models->Rank->_get(array('id' => $rank['rank_id']));
	
				$pr[$rankkey]['rank'] = $r['rank'];
	
				$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];

				if ($includeLanguageLabels) {
	
					$lpr = $this->models->LabelProjectRank->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'project_rank_id' => $rank['id'],
								'language_id' => $this->getCurrentLanguageId()
							),
							'columns' => 'label'
						)
					);
					
					$pr[$rankkey]['labels'][$this->getCurrentLanguageId()] = $lpr[0]['label'];

				}
	
			}
			
			$_SESSION['app']['user']['species']['ranks']['projectRanks'] = $pr;
			$_SESSION['app']['user']['species']['ranks']['topRankId'] = $topRankId;
			
		}

		return
			array(
				'ranks' => $_SESSION['app']['user']['species']['ranks']['projectRanks'],
				'topRankId' => $_SESSION['app']['user']['species']['ranks']['topRankId']
			);

	}
	
	public function getTaxonById($id)
	{

		if (!isset($_SESSION['app']['user']['species']['taxon']['id']) ||
			$_SESSION['app']['user']['species']['taxon']['id']!=$id) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $id
					)
				)
			);

			$_SESSION['app']['user']['species']['taxon'] = $t[0];
			
			$pr = $this->models->ProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $_SESSION['app']['user']['species']['taxon']['rank_id']
					)
				)
			);
			
			$_SESSION['app']['user']['species']['taxon']['lower_taxon'] = $pr[0]['lower_taxon'];


		}

		return $_SESSION['app']['user']['species']['taxon'];
	
	}

	public function getTaxonClassification($taxonId)
	{

		$d = null;

		$this->getTaxonTree();

		foreach((array)$this->treeList as $key => $val) {

			$d[$val['rank_id']] = $val;
			
			if ($val['id'] == $taxonId) {

				$d = array_slice($d,0,$val['level']+1);

				break;

			}

		}

		return $d;

	}

	public function getPagination($items,$maxPerPage=25)
	{

		/*
		
		{if $prevStart!=-1 || $nextStart!=-1}
			<div id="navigation">
				{if $prevStart!=-1}
				<span class="a" onclick="goNavigate({$prevStart});">< previous</span>
				{/if}
				{if $nextStart!=-1}
				<span class="a" onclick="goNavigate({$nextStart});">next ></span>
				{/if}
			</div>
		{/if}
		
		//goNavigate(val,formName) formname default = 'theForm'
		
		*/

		if (!isset($items)) return;

		// determine index of the first taxon to show
		$start = $this->rHasVal('start') ? $this->requestData['start'] : 0;
	
		//determine index of the first taxon to show on the previous page (if any)
		$prevStart = $start==0 ? -1 : (($start-$maxPerPage<1) ? 0 : ($start-$maxPerPage));
	
		//determine index of the first taxon to show on the next page (if any)
		$nextStart = ($start+$maxPerPage>=count((array)$items)) ? -1 : ($start+$maxPerPage);
	
		// slice out only the taxa we need (faster than looping the entire thing in smarty)
		$items = array_slice($items,$start,$maxPerPage);
	
		return
			array(
				'items' => $items,
				'prevStart' => $prevStart,
				'nextStart' => $nextStart,
			);

	}

	public function matchGlossaryTerms($text,$forceLookup=false)
	{

		if ($this->generalSettings['useGlossaryPostIts']===false || empty($text) || !is_string($text)) return $text;

		$wordlist = $this->getWordList($forceLookup);

		$processed = $text;

		foreach((array)$wordlist as $key => $val) {
		
			if ($val['word']=='') continue;

			$this->_currentGlossaryId = $val['id'];

			$expr = '|\b('.$val['word'].')\b|i';
		
			$processed = preg_replace_callback($expr,array($this,'embedGlossaryLink'),$processed);
		
		}

		return $processed;
	
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

	        $_SESSION['app']['project'][$key] = $val;

		}
		
		$_SESSION['app']['project']['hybrid_marker'] = $this->generalSettings['hybridMarker'];

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
        
        $_SESSION['app']['project']['languages'] = $lp;

        if (isset($defaultLanguage)) $_SESSION['app']['project']['default_language_id'] = $defaultLanguage;

//        if (isset($list)) $_SESSION['app']['project']['languageList'] = $list;
		
    }

   /**
     * Sets the active project's id as class variable
     *
     * @param      integer    $id    new active project's id
     * @access     public
     */
    public function setCurrentProjectId ($id)
    {
        
        $_SESSION['app']['project']['id'] = $id;
 
    }


	public function didActiveLanguageChange()
	{

        return $_SESSION['app']['user']['languageChanged'];

	}

	public function getCurrentLanguageId()
	{

		if (empty($_SESSION['app']['user']['activeLanguageId'])) $this->setCurrentLanguageId();

        return $_SESSION['app']['user']['activeLanguageId'];

	}

	public function getDefaultLanguageId()
	{

        return isset($_SESSION['app']['project']['default_language_id']) ? $_SESSION['app']['project']['default_language_id'] : null;

	}

	public function setCurrentLanguageId($l=null)
	{

		if ($l) {

			$_SESSION['app']['user']['languageChanged'] = $_SESSION['app']['user']['activeLanguageId'] != $l;

			$_SESSION['app']['user']['activeLanguageId'] = $l;

		} else
		if ($this->rHasVal('languageId')) {

			$_SESSION['app']['user']['languageChanged'] = $_SESSION['app']['user']['activeLanguageId'] != $this->requestData['languageId'];

			$_SESSION['app']['user']['activeLanguageId'] = $this->requestData['languageId'];

		} else {

			$_SESSION['app']['user']['languageChanged'] = false;

		}
		
		if (!isset($_SESSION['app']['user']['activeLanguageId'])) {

			$_SESSION['app']['user']['activeLanguageId'] = $this->getDefaultLanguageId();
			$_SESSION['app']['user']['languageChanged'] = true;

		}
		
		if (!isset($_SESSION['app']['user']['languageChanged'])) $_SESSION['app']['user']['languageChanged'] = true;
		
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

        return isset($_SESSION['app']['project']['id']) ? $_SESSION['app']['project']['id'] : null;
    
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
				'content' => $content,
				'env' => 'front'
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


	public function setControllerParams($params)
	{
	
		if (isset($params['checkForProjectId'])) $this->setCheckForProjectId($params['checkForProjectId']);
	
	}

	public function matchHotwords($text,$forceLookup=false)
	{

		if (empty($text) || !is_string($text)) return $text;
		
		$wordlist = $this->getHotwords($forceLookup);

		$processed = $text;
		
		$expr = '|(\[no\])(.*)(\[\/no\])|i';

		$processed = preg_replace_callback($expr,array($this,'embedNoLink'),$processed);

		foreach((array)$wordlist as $key => $val) {
		
			if ($val['hotword']=='') continue;
			
			$this->_currentHotwordLink = '../'.$val['controller'].'/'.$val['view'].'.php'.(!empty($val['params']) ? '?'.$val['params'] : '');

			$expr = '|\b('.$val['hotword'].')\b|i';
		
			$processed = preg_replace_callback($expr,array($this,'embedHotwordLink'),$processed);
		
		}

		$processed = $this->restoreNoLinks($this->effectuateHotwordLinks($processed));

		return $processed;
	
	}

	private function setCheckForProjectId($state)
	{
	
		if (is_bool($state)) $this->_checkForProjectId = $state;
	
	}

	public function getCheckForProjectId()
	{
	
		return $this->_checkForProjectId;
	
	}

	private function previewOverlay()
	{

		$d = $this->controllerBaseName.':'.$this->viewName;

		if (
			isset($this->requestData['cat']) && 
			!is_numeric($this->requestData['cat']) && 
			isset($this->generalSettings['urlsToAdminEdit'][$d.':'.$this->requestData['cat']]))
		{
	
			$d = $d.':'.$this->requestData['cat'];

		}

		if (
			$this->isLoggedInAdmin() && 
			$this->allowEditPageOverlay && 
			isset($this->generalSettings['urlsToAdminEdit'][$d])
		) {
		
			if (isset($this->requestData['id'])) $id = $this->requestData['id'];

			if ($this->controllerBaseName=='module') {
				$modId = $this->getCurrentModule();
				$modId = $modId['id'];
			} else
			if ($this->controllerBaseName=='matrixkey') {
				$id = $this->getCurrentMatrixId();
			} else
			if ($this->controllerBaseName=='key') {
				$id = $this->getCurrentKeyStepId();
			}

			$this->smarty->assign(
				'urlBackToAdmin',
				sprintf(
					$this->generalSettings['urlsToAdminEdit'][$d],
					$id,
					(isset($this->requestData['cat']) && is_numeric($this->requestData['cat']) ? 
						$this->requestData['cat'] : 
						($this->controllerBaseName=='module' ? 
							$modId : 
							null
						)
					)
				)
			);
			$this->smarty->display('../shared/preview-overlay.tpl');
			
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
				'content' => $content,
				'env' => 'front'
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
			$modules[$key]['show_in_public_menu'] = $mp['show_in_public_menu'];

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
			$val['show_in_public_menu'] = 1;
			$modules[] = $val;

		}

		return $modules;

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
        
        if (!isset($array) || !is_array($array)) return;

        if (isset($sortBy['key'])) $this->setSortField($sortBy['key']);
        
        if (isset($sortBy['dir'])) $this->setSortDirection($sortBy['dir']);
        
        if (isset($sortBy['case'])) $this->setSortCaseSensitivity($sortBy['case']);

		$maintainKeys = isset($sortBy['maintainKeys']) && ($sortBy['maintainKeys']===true);

        if ($maintainKeys) {

			$keys = array();
			
			$f = md5(uniqid(null,true));
	
			foreach((array)$array as $key => $val) {
	
				$x = md5(json_encode($val).$key);
				$array[$key][$f] = $x;
				$keys[$x] = $key;
	
			}
			
		}

        usort($array,
			array(
				$this, 
				'doCustomSortArray'
        	)
		);

        if ($maintainKeys) {

			foreach((array)$array as $val) {
			
				if (is_array($val)) {
				
					$y = array() ;
				
					foreach($val as $key2 => $val2) {
					
						if ($key2!=$f) $y[$key2] = $val2;
					
					}

					$d[$keys[$val[$f]]] = $y;

				} else {
			
					$d[$keys[$val[$f]]] = $val;

				}
			
			}
			
			$array = $d;
			
		}
    
    }

	public function getControllerConfig($controller)
    {

        $t = 'getControllerSettings'.$controller;

        if (method_exists($this->config,$t)) {

            return $this->config->$t();

        } else {

            return false;

        }

    }


    /**
     * Sets project URL for project images
     * 
	 * @todo	take out hard reference to /media/
     * @access     public
     */
    public function setUrls()
    {

		$_SESSION['app']['system']['urls']['systemMedia'] =
			$this->baseUrl.$this->getAppName().'/media/system/skins/'.$this->generalSettings['app']['skinName'].'/';
        
		$p = $this->getCurrentProjectId();

		if (!$p) return;
		
		$pCode = $this->getProjectFSCode($p);

		// url of the directory containing user-uploaded media files
		if (isset($this->generalSettings['urlUploadedProjectMedia'])) {
			$u['uploadedMedia'] = $this->generalSettings['urlUploadedProjectMedia'].$pCode.'/';
		} else {
			$u['uploadedMedia'] = $this->baseUrl.$this->getAppName().'/media/project/'.$pCode.'/';
		}

		$u['uploadedMediaThumbs'] = $u['uploadedMedia'].'thumbs/';

		// urls of the directory containing project specific media part of the interface, but not of the content (background etc)
		$u['projectMedia'] = $this->baseUrl.$this->getAppName().'/media/project/'.$pCode.'/';
		$u['projectL2Maps'] = $u['projectMedia'].'l2_maps/';

		// urls of the directory containing media that are constant across projects (but can be skinned)
		$u['systemMedia'] = $_SESSION['app']['system']['urls']['systemMedia'];
		$u['systemL2Maps'] = $this->baseUrl.$this->getAppName().'/media/system/l2_maps/';

		// urls of css-files, either project-specific - if they exist - or generic
		$projectCssDir = $this->baseUrl.$this->getAppName().'/style/';

		if (file_exists($projectCssDir.$pCode.'/basics.css')) {
			$u['projectCSS'] = $projectCssDir.$pCode.'/';
		} else {
			$u['projectCSS'] = $projectCssDir.'default/'.$this->generalSettings['app']['skinName'].'/';
		}

		// home
		$u['projectHome'] = $this->baseUrl.$this->getAppName().'/views/'.$this->generalSettings['defaultController'].'/';

		$_SESSION['app']['project']['urls'] = $u;

    }

    /**
     * Sets project paths for image uploads etc. and makes sure they actually exist
     * 
     * @access     public
     */
    public function setPaths()
    {

		$_SESSION['app']['project']['paths']['defaultCSS'] = $this->generalSettings['app']['fileRoot'].'style/default/';

        $p = $this->getCurrentProjectId();

        if ($p) {

            $_SESSION['app']['project']['paths']['defaultCSS'] = $this->generalSettings['app']['fileRoot'].'style/default/';
        
        }
    
    }
	
	public function  setCssFiles()
	{


		if (isset($_SESSION['app']['project']['urls']['projectCSS'])) {

			foreach((array)$this->cssToLoad as $key => $val)
				$this->cssToLoad[$key] = $_SESSION['app']['project']['urls']['projectCSS'].$val;

		}

		array_push($this->cssToLoad,'../utilities/dynamic-css.php');

		$d = $this->baseUrl.$this->getAppName().'/style/custom/'.$this->getProjectFSCode().'.css';

		if (file_exists($d)) array_push($this->cssToLoad,$d);

	}
	

	public function makeLookupList($data,$module,$url,$sortData=false,$encode=true)
	{

		$sortBy = array(
			'key' => 'label', 
			'dir' => 'asc', 
			'case' => 'i'
		);

		if ($sortData) $this->customSortArray($data, $sortBy);

		$d = array(
			'module' => $module,
			'url' => $url,
			'results' => $data
		);

		return $encode ? json_encode($d) : $d;
	
	}
	
	public function isLoggedInAdmin()
	{

		if (!isset($_SESSION['admin']['project']['id']))
			return false;
		else
			return $this->getCurrentProjectId() == $_SESSION['admin']['project']['id'];
	
	}
	
	public function doesEntryProgramExist()
	{
	
		// ofcourse just these three aren't nowhere near sufficient to run the admin, but it's a good indication
		return (
			file_exists($this->generalSettings['lngFileRoot'].'configuration/admin/configuration.php') &&
			file_exists($this->generalSettings['lngFileRoot'].'configuration/admin/controllers/Controller.php') &&
			file_exists($this->generalSettings['lngFileRoot'].'www/admin/admin-index.php')
		);
	
	}

	
	private function setPhpIniVars()
	{

		// avoid "page expired" messages on using browser's "back"-button
		//ini_set('session.cache_limiter', 'private');
	
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

		//session_name('lng-application');
        session_start();

        /* DEBUG */        
        $_SESSION['app']['system']['server_addr'] = $_SERVER['SERVER_ADDR'];

    }

	private function getProjectDependentTemplates()
	{

		$p = $this->getCurrentProjectId();

		if (is_null($p)) return;

		$r = null;
		
		$d = $this->_smartySettings['dir_template'].'/'.'shared/'. $this->getProjectFSCode($p). '/';
		
		if (file_exists($d.'_header-container.tpl')) $r['header_container'] = $d.'_header-container.tpl';
		if (file_exists($d.'_main-menu.tpl')) $r['main_menu'] = $d.'_main-menu.tpl';
		if (file_exists($d.'_page-start.tpl')) $r['main_menu'] = $d.'_page-start.tpl';
		if (file_exists($d.'_footer.tpl')) $r['footer'] = $d.'_footer.tpl';

		return $r;

	}


    /**
     * Assigns basic Smarty variables
     *
     * @access     public
     */
    private function preparePage()
    {
 
		if (isset($this->cssToLoad))  $this->smarty->assign('cssToLoad', $this->cssToLoad);
		if (isset($this->jsToLoad)) $this->smarty->assign('javascriptsToLoad', $this->jsToLoad);
 		if (isset($_SESSION['app']['project']['languages'])) $this->smarty->assign('languages',$_SESSION['app']['project']['languages']);

 		$this->smarty->assign('currentLanguageId',$this->getCurrentLanguageId());
		$this->smarty->assign('menu',$this->getMainMenu());
 		$this->smarty->assign('controllerMenuExists',$this->includeLocalMenu && file_exists($this->smarty->template_dir.'_menu.tpl'));
		$this->smarty->assign('customTemplatePaths',$this->getProjectDependentTemplates());
        $this->smarty->assign('useJavascriptLinks', $this->generalSettings['useJavascriptLinks']);
        $this->smarty->assign('session', $_SESSION);
        $this->smarty->assign('rnd', $this->getRandomValue());
        $this->smarty->assign('requestData', $this->requestData);
        $this->smarty->assign('baseUrl', $this->baseUrl);
        $this->smarty->assign('controllerBaseName', $this->controllerBaseName);
        $this->smarty->assign('controllerPublicName', $this->controllerPublicName);
        $this->smarty->assign('backlink', $this->getBackLink());
        $this->smarty->assign('errors', $this->getErrors());
        $this->smarty->assign('messages', $this->getMessages());
        $this->smarty->assign('pageName', $this->getPageName());
        $this->smarty->assign('showBackToSearch', $this->showBackToSearch);

    }

	private function loadControllerConfig()
    {

		$this->controllerSettings = $this->getControllerConfig($this->controllerBaseName);

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
		
		$_SESSION['app']['system']['path'] = $path;

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
		//if (empty($this->controllerBaseName)) $this->log('No controller basename set',2);
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
    private function loadModels()
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
    private function setSmartySettings()
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
     * Sets key to sort by for doCustomSortArray
     *
     * @param string    name of the field to sort by
     * @access     private
     */
    private function setSortField($field)
    {
        
        $this->sortField = $field;
    
    }


    /**
     * Returns key to sort by; called by doCustomSortArray
     *
     * @return string    name of the field to sort by; defaults to 'id'
     * @access     private
     */
    private function getSortField()
    {
        
        return !empty($this->sortField) ? $this->sortField : 'id';
    
    }


    /**
     * Sets sort direction for doCustomSortArray
     *
     * @param string    $a    asc or desc
     * @access     private
     */
    private function setSortDirection($dir)
    {
        
        $this->sortDirection = $dir;
    
    }


    /**
     * Returns direction to sort in; called by doCustomSortArray
     *
     * @return string    asc or desc
     * @access     private
     */
    private function getSortDirection()
    {
        
        return !empty($this->sortDirection) ? $this->sortDirection : 'asc';
    
    }


    /**
     * Sets case sensitivity for doCustomSortArray
     *
     * @param string    $a    i(nsensitive) or s(ensitive)
     * @access     private
     */
    private function setSortCaseSensitivity($sens)
    {
        
        $this->sortCaseSensitivity = $sens;
    
    }


    /**
     * Returns setting for case-sensitivity while sorting; called by doCustomSortArray
     *
     * @return string    i(nsensitive) or s(ensitive)
     * @access     private
     */
    private function getSortCaseSensitivity()
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
    private function doCustomSortArray($a, $b)
    {
        
        $f = $this->getSortField();
        
        $d = $this->getSortDirection();
        
        $c = $this->getSortCaseSensitivity();

		if (!is_array($f)) $f = array($f);
		
		$res = 0;
		
		$dir = !is_array($d) ? $d : null;

		foreach($f as $key => $val) {
		
			if (!isset($a[$val]) || !isset($b[$val])) continue;

			if (is_array($d) && isset($d[$key])) $dir = $d[$key];

			if ($c != 's') {
				
				$a[$val] = strtolower($a[$val]);
				$b[$val] = strtolower($b[$val]);
			
			}

			$res = ($a[$val] > $b[$val] ? ($dir == 'asc' ? 1 : -1) : ($a[$val] < $b[$val] ? ($dir == 'asc' ? -1 : 1) : 0));


			if ($res!=0) return $res;

		}
		
		return $res;
    
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

	private function getBackLink()
	{

		$d = 
			(isset($_SESSION['app']['user']['breadcrumbs']) &&
			isset($_SESSION['app']['user']['breadcrumbs'][count($_SESSION['app']['user']['breadcrumbs'])-1])) ?
				$_SESSION['app']['user']['breadcrumbs'][count($_SESSION['app']['user']['breadcrumbs'])-1] :
				null;

		if (isset($d['name'])) $d['name'] = str_replace('"', '', $d['name']);
		if (isset($d['data'])) $d['data'] = json_encode($d['data']);
		
		return $d;

	}

	private function setBreadCrumb()
	{

		if (empty($this->pageName) || $this->storeHistory==false) return;

		foreach((array)$this->requestData as $key => $val) {

			$p[] = array('vari' => $key,'val' => $val); // IE takes exception to variables called 'var' in javascript, hence 'vari'

		}

		$_SESSION['app']['user']['breadcrumbs'][] = array(
			'name' => $this->pageName,
			'url' => $_SERVER['REQUEST_URI'],
			'data' => isset($p) ? $p : null
		);
		
		$d = count((array)$_SESSION['app']['user']['breadcrumbs']);

		if (isset($_SESSION['app']['user']['breadcrumbs'][$d-2])) {
		
			if ($_SESSION['app']['user']['breadcrumbs'][$d-2]==$_SESSION['app']['user']['breadcrumbs'][$d-1]) {

				array_pop($_SESSION['app']['user']['breadcrumbs']);
				
				$d--;

			}
		
		}

		if ($d>$this->generalSettings['maxBackSteps'])
			$_SESSION['app']['user']['breadcrumbs'] = array_slice($_SESSION['app']['user']['breadcrumbs'],$d-$this->generalSettings['maxBackSteps']);

		//q($_SESSION['app']['user']['breadcrumbs']);

	}

	private function checkBackStep()
	{
	
		if ($this->rHasVal('backstep','1')) {

			array_pop($_SESSION['app']['user']['breadcrumbs']);
			
			$this->storeHistory = false;

			unset($this->requestData['backstep']);

		}
		
	}

	private function getWordList($forceUpdate=false)
	{

		if ($forceUpdate || !isset($_SESSION['app']['user']['glossary'][$this->getCurrentLanguageId()]['wordlist'])) {

			$terms = $this->models->Glossary->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId()
					),
					'columns' => 'id,term as word,\'term\' as source'
				)
			);

			$synonyms = $this->models->GlossarySynonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId()
					),
					'columns' => 'glossary_id as id,synonym as word,\'synonym\' as source'
				)
			);

			$_SESSION['app']['user']['glossary'][$this->getCurrentLanguageId()]['wordlist'] = array_merge((array)$terms,(array)$synonyms);

		}

		return $_SESSION['app']['user']['glossary'][$this->getCurrentLanguageId()]['wordlist'];

	}

	private function embedGlossaryLink($matches)
	{

		if (trim($matches[0])=='')
			return $matches[0];
		else
			return '<span class="glossary-term-highlight" onmouseover="glossTextOver('.$this->_currentGlossaryId.',this)">'.$matches[0].'</span>';

	}
	
	private function getBreadcrumbs()
	{

		return isset($_SESSION['app']['user']['breadcrumbs']) ? $_SESSION['app']['user']['breadcrumbs'] : null;

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




	private function getHotwords($forceUpdate=false)
	{

		if ($forceUpdate || !isset($_SESSION['app']['user']['hotwords'][$this->getCurrentLanguageId()])) {

			$d = 
				$this->models->Hotword->_get(
					array(
						'id' => 
							'select hotword,controller,view,params
								from %table% where project_id = '.
								$this->getCurrentProjectId().' '.
								'and (language_id = '.$this->getCurrentLanguageId().' or language_id = 0)'
					)
				);
				
			foreach((array)$d as $key => $val) $d[$key]['num_of_words'] = substr_count($val['hotword'],' ')+1;
				
			$this->customSortArray($d, array('key' => 'num_of_words', 'dir' => 'desc', 'case' => 'i'));
			
			$_SESSION['app']['user']['hotwords'][$this->getCurrentLanguageId()] = $d;		

		}

		return $_SESSION['app']['user']['hotwords'][$this->getCurrentLanguageId()];

	}

	private function embedNoLink($matches)
	{

		if (trim($matches[0])=='') {

			return $matches[0];

		} else {

			$d = '~~@#@@#@'.count((array)$this->_hotwordNoLinks).'}}\\||';

			$this->_hotwordNoLinks[] = array(
				'str' => $d,
				'orig' => $matches[0]
			);

			return $d;
			
		}

	}
		
	private function embedHotwordLink($matches)
	{

		if (trim($matches[0])=='') {

			return $matches[0];

		} else {

			$d = '~~&&^%%'.count((array)$this->_hotwordTempLinks).'||::--+';
			$this->_hotwordTempLinks[] = array(
				'str' => $d,
				'link' => '<a href="'.$this->_currentHotwordLink.'">'.$matches[0].'</a>'
			);
			return $d;
			
		}

	}

	private function effectuateHotwordLinks($txt)
	{

		$this->_hotwordTempLinks = array_reverse($this->_hotwordTempLinks);

		foreach((array)$this->_hotwordTempLinks as $val) {
		
			$txt = str_replace($val['str'],$val['link'],$txt);

		}
		
		$this->_hotwordTempLinks = array();
		
		return $txt;

	}

	private function restoreNoLinks($txt)
	{
	
		foreach((array)$this->_hotwordNoLinks as $val) {
		
			$txt = str_replace(
				$val['str'],
				str_ireplace(array('[no]','[/no]'),'',$val['orig']),
				$txt
			);

		}
		
		$this->_hotwordNoLinks = array();
		
		return $txt;

	}

	private function getProjectFSCode($p=null)
	{
	
		$p = is_null($p) ? $this->getCurrentProjectId() : $p;
	
		return sprintf('%04s',$p);
	
	}	
	
	public function setStoreHistory($state)
	{
	
		$this->storeHistory = $state;
	
	}

	
}