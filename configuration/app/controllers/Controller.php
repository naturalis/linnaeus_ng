<?php

/*

	order of modules in the icon grid and the main menu is determined by two fields:

		ModuleProject.show_order
		FreeModuleProject.show_order

	or just the first one, if there are no free modules. THERE IS NO INTERFACE FOR 
	CHANGING THESE VALUES, so changes will have to be made by hand, directly in the
	tables. when changing these values, bear in mind that list of modules is ordered
	after having been combined from the normal modules (ModuleProject) and possible
	free modules (FreeModuleProject). this means that the values for show_order have
	to be unique across two tables (an 'arc', as it used to be called in oracle);
	again, these is at present no mechanism that actually enforces this - it is up
	to the system administrator.

*/


include_once (dirname(__FILE__) . "/../BaseClass.php");

include_once (dirname(__FILE__) . "/../../../smarty/Smarty.class.php");

class Controller extends BaseClass
{

    private $_smartySettings;
    private $_fullPath;
    private $_fullPathRelative;
	private $_checkForProjectId = true;
	private $_checkForSplash = true;
	
	private $_allowEditOverlay = false; // true

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
	public $includeLocalMenu = true;
	public $allowEditPageOverlay = true;
	public $tmp;

    private $usedModelsBase = array(
        'settings', 
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
		'logging_helper',
		'debug_tools'
    );
	
	public $cssToLoad = array();


    /**
     * Constructor, calls parent's constructor and all initialisation functions
     *
     * The order in which the functions are called is relevant! Do not change without good reason and plan.
     *
     * @access     public
     */
    public function __construct($p=null)
    {
 
        parent::__construct();

		$this->setControllerParams($p);

        $this->setPhpIniVars();

        $this->setDebugMode();

        $this->startSession();

        $this->loadHelpers();

		$this->initLogging();

        $this->setNames();
		
        $this->loadControllerConfig();        
        
        $this->setUrls();
		
		$this->setPaths();

		$this->createCacheFolder();
		
		$this->checkWriteableDirectories();
		
        $this->loadModels();

        $this->setRandomValue();

        $this->setSmartySettings();
        
        $this->setRequestData();
		
		$this->restoreState();

        $this->setProjectLanguages();
		
		$this->setCurrentLanguageId();

		$this->checkBackStep();

		if ($this->getCheckForProjectId()) {

			$this->checkForProjectId();

			$this->setCssFiles();
			
			$this->splashScreen();

		}

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

	public function checkForProjectId()
	{

		$pB = $this->getCurrentProjectId();

		if ($this->rHasVal('p')) {
		
			$this->resolveProjectId();
			
		} elseif ($this->rHasVal($this->generalSettings['addedProjectIDParam'])) {

			$this->requestData['p'] = $this->requestData[$this->generalSettings['addedProjectIDParam']];

			$this->resolveProjectId();

		}
		$d = $this->getCurrentProjectId();
		
		if ($d==null) $this->redirect($this->generalSettings['urlNoProjectId']);

		if ($pB != $d) unset($_SESSION['app']['user']);
		
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

	public function _buildTaxonTree($p=null)
	{

		$pId = isset($p['pId']) ? $p['pId'] : null;
		$depth = isset($p['depth']) ? $p['depth'] : 0;
		$ranks = $this->getProjectRanks();

		if (!isset($p['depth'])) unset($this->treeList);
				
		$t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'parent_id'.(is_null($pId) ? ' is' : '') => (is_null($pId) ? 'null' : $pId)
				),
				'columns' => 'id,taxon,parent_id,rank_id,taxon_order,is_hybrid,list_level,is_empty',
				'fieldAsIndex' => 'id',
				'order' => 'taxon_order,id'
			)
		);

		foreach((array)$t as $key => $val) {

			$t[$key]['lower_taxon'] = $ranks[$val['rank_id']]['lower_taxon'];
			$t[$key]['keypath_endpoint'] = $ranks[$val['rank_id']]['keypath_endpoint'];
			$t[$key]['sibling_count'] = count((array)$t);
			$t[$key]['depth'] = $t[$key]['level'] = $depth;
			// give do not display flag to taxa that are in brackets
			$t[$key]['do_display'] = !preg_match('/^\(.*\)$/',$val['taxon']);
			// taxon name
			$t[$key]['label'] = $this->formatSpeciesEtcNames($val['taxon'],$val['rank_id']);

			//// level is effectively the recursive depth of the taxon within the tree
			//$t[$key]['level'] = $level;

			//// sibling_pos reflects the position amongst taxa on the same level
			//$t[$key]['sibling_pos'] = ($key==0 ? 'first' : ($key==count((array)$t)-1 ? 'last' : '-' ));

			//// get rank label
			//$t[$key]['rank'] = $pr[$val['rank_id']]['labels'][$this->getCurrentLanguageId()];

			$this->treeList[$key] = $t[$key];

			$t[$key]['children'] = $this->_buildTaxonTree(
				array(
					'pId' => $val['id'],
					'depth' => $depth+1
				)
			);

			$this->treeList[$key]['child_count'] = count((array)$t[$key]['children']);

					
		}
		
		return $t;
	
	}

	public function getProjectRanks()
	{
	
		$pr = $this->getCache('tree-ranks');
		
		if (!$pr) {

			$pr = $this->models->ProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'fieldAsIndex' => 'id',
					'columns' => 'id,rank_id,parent_id,lower_taxon,keypath_endpoint'
				)
			);
			
			$pl = $this->getProjectLanguages();

			foreach((array)$pr as $rankkey => $rank) {
			
				if (empty($rank['rank_id'])) continue;
	
				$r = $this->models->Rank->_get(array('id' => $rank['rank_id']));
	
				$pr[$rankkey]['rank'] = $r['rank'];
	
				$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];
				
				foreach((array)$pl as $val) {

					$lpr = $this->models->LabelProjectRank->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'project_rank_id' => $rank['id'],
								'language_id' => $val['language_id']
							),
							'columns' => 'label'
						)
					);
					
					$pr[$rankkey]['labels'][$val['id']] = $lpr[0]['label'];

				}
	
			}

			$this->saveCache('tree-ranks', $pr);
		
		}
		
		return $pr;

	}
	
	public function getTaxonById($id)
	{
	
		if (empty($id) || $id==0) {
			return;
		}
		
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

			$t[0]['label'] = $this->formatSpeciesEtcNames($t[0]['taxon'],$t[0]['rank_id']);

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

		$this->buildTaxonTree();
		
		foreach((array)$this->treeList as $key => $val) {
			
			$d[$val['level']] = $val;
			
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
    public function redirect($url=false)
    {
        
        if (!$url && isset($_SERVER['HTTP_REFERER'])) {
            
            $url = $_SERVER['HTTP_REFERER'];
        
        } 
        
        if ($url) {

			$p = $this->generalSettings['addedProjectIDParam'].'='.$this->getCurrentProjectId();
  			$d = parse_url($url);

			// let's ignore the possbilities of fragments, shall we?
			if (isset($d['query']) && !empty($d['query']) && strpos($d['query'],$p)===false)
				$url .= '&'.$p;
			else
			if (strpos($url,$p)===false)
				$url .= '?'.$p;

			if (basename($url) == $url) {
				
				$circular = (basename($this->_fullPath) == $url);
			
			} else {
		
				$circular = ($this->_fullPath == $url) || ($this->_fullPathRelative == $url);
		
			}

            if (!$circular) header('Location:' . $url);

            die();
        
        }
    
    }


    /**
     * Sets the active project's name as a session variable (for display purposes)
     *
     * @access     public
     */
    public function setCurrentProjectData($data=null)
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
		$_SESSION['app']['project']['filesys_name'] = strtolower(preg_replace(array('/\s/','/[^A-Za-z0-9-]/'),array('-',''),$_SESSION['app']['project']['title']));

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
	
	public function getProjectLanguages()
    {

		return isset($_SESSION['app']['project']['languages']) ? $_SESSION['app']['project']['languages'] : null;
	
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

        return isset($_SESSION['app']['user']['languageChanged']) ? $_SESSION['app']['user']['languageChanged'] : false;

	}

	public function getCurrentLanguageId()
	{

		if (empty($_SESSION['app']['project']['activeLanguageId'])) $this->setCurrentLanguageId();

        return $_SESSION['app']['project']['activeLanguageId'];

	}

	public function getDefaultLanguageId()
	{

        return isset($_SESSION['app']['project']['default_language_id']) ? $_SESSION['app']['project']['default_language_id'] : null;

	}

	public function setCurrentLanguageId($l=null)
	{

		if ($l) {

			$_SESSION['app']['user']['languageChanged'] = $_SESSION['app']['project']['activeLanguageId'] != $l;

			$_SESSION['app']['project']['activeLanguageId'] = $l;

		} else
		if ($this->rHasVal('languageId')) {

			$_SESSION['app']['user']['languageChanged'] = $_SESSION['app']['project']['activeLanguageId'] != $this->requestData['languageId'];

			$_SESSION['app']['project']['activeLanguageId'] = $this->requestData['languageId'];

		} else {

			$_SESSION['app']['user']['languageChanged'] = false;

		}
		
		if (!isset($_SESSION['app']['project']['activeLanguageId'])) {

			$_SESSION['app']['project']['activeLanguageId'] = $this->getDefaultLanguageId();
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

	public function getSetting($name)
	{
	
		$s = $this->models->Settings->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'setting' => $name
				),
				'columns' => 'value',
				'limit' => 1
			)
		);
		
		if (isset($s[0]))
			return $s[0]['value'];
		else
			return null;
	
	}
	
	public function formatSpeciesEtcNames($name,$projRankId)
	{
		/*
		
			In italics worden geschreven
			
			Genus
			Subgenus
			Species
			Infraspecies
			
			De rest niet
			In titel zou het dus worden
			
			Genus <span class="italics">Fuscus</span>
			Subgenus (<span class="italics">Fuscus</span>) [subgenera worden altijd tussen haakjes geschreven]
			Species <span class="italics">Fuscus fuscus</span>
			Subspecies <span class="italics">Fuscus fuscus</span> ssp.  <span class="italics">fuscus</span>
			
			De rest is gewoon
			Family Fuscidae [geen flauwekul met italics]
			
			PLEASE NOTE
			the rank ID's are hardcoded, so the ranks-table should NEVER change
		
		*/
		
		if (empty($projRankId)) return $name;
		
		if ($projRankId=='synonym' || $projRankId=='syn' ) return '<span class="italics">'.$name.'</span>';
	
		$r = $this->getProjectRanks();

		$rankId = $r[$projRankId]['rank_id'];

		if (isset($r[$projRankId]['labels'][$this->getCurrentLanguageId()]))
			$d = $r[$projRankId]['labels'][$this->getCurrentLanguageId()];
		else
			$d = $r[$projRankId]['rank'];

		$rankName = ucfirst($d);

		$g = $s = $i = false;
		$elements = explode(' ', $name);
			if (count($elements) > 2) {
			list($g, $s, $i) = $elements;
		}
				
		switch ($rankId) {
			case '63': 
				return $rankName.' <span class="italics">'.$name.'</span>';
			case '64':
				return $rankName.' <span class="italics">'.$name.'</span>';
			case '74':
				return '<span class="italics">'.$name.'</span>';
			case '77':
				return '<span class="italics">'.$g.' '.$s.'</span> subsp. <span class="italics">'.$i.'</span>';
			case '78':
				return '<span class="italics">'.$g.' '.$s.'</span> var. <span class="italics">'.$i.'</span>';
			case '79':
				return '<span class="italics">'.$g.' '.$s.'</span> subvar. <span class="italics">'.$i.'</span>';
			case '81':
				return '<span class="italics">'.$g.' '.$s.'</span> f. <span class="italics">'.$i.'</span>';
			case '82':
				return '<span class="italics">'.$g.' '.$s.'</span> subf. <span class="italics">'.$i.'</span>';
			default:
				return $rankName.' '.$name;
		
		}
	
	}
	
	private function setControllerParams($params)
	{

		if (isset($params['checkForSplash'])) $this->setCheckForSplash($params['checkForSplash']);
		if (isset($params['checkForProjectId'])) $this->setCheckForProjectId($params['checkForProjectId']);
	
	}

	private function setCheckForSplash($state)
	{
	
		if (is_bool($state)) $this->_checkForSplash = $state;
	
	}

	private function getCheckForSplash()
	{

		return $this->_checkForSplash;
	
	}

	private function setCheckForProjectId($state)
	{
	
		if (is_bool($state)) $this->_checkForProjectId = $state;
	
	}

	private function getCheckForProjectId()
	{
	
		return $this->_checkForProjectId;
	
	}

	private function previewOverlay()
	{
	
		if ($this->_allowEditOverlay===false) return;

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
		
			if (isset($this->requestData['id'])) {
			
				$id = $this->requestData['id'];

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
	
	}

    /**
     * Renders and displays the page
     *
     * @access     public
     */
    public function printPage ($templateName=null)
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
				'columns' => 'id,module_id,show_order,active'
			)
		);
		
		$m = $this->models->Module->_get(array('id'=>'*','fieldAsIndex' => 'id'));

		foreach ((array) $modules as $key => $val) {

			if (isset($m[$val['module_id']])) {

				$mp = $m[$val['module_id']];				
				$modules[$key]['type'] = 'regular';
				$modules[$key]['icon'] = $mp['icon'];
				$modules[$key]['module'] = _($mp['module']);
				$modules[$key]['controller'] = $mp['controller'];
				$modules[$key]['show_in_public_menu'] = $mp['show_in_public_menu'];
				$modules[$key]['show_order'] = $mp['show_order'];

				$_SESSION['app']['project']['active-modules'][$mp['id']] = $mp['module'];

			}

		}

		$freeModules = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'active' => 'y'
				),
				'columns' => 'id,module,description,show_order,show_alpha,active'
			)
		);
		
		foreach ((array) $freeModules as $key => $val) {

			$val['type'] = 'free';
			$val['show_in_public_menu'] = 1;
			$modules[] = $val;

		}
		
		
		$this->customSortArray($modules, array('key' => 'show_order', 'dir' => 'asc'));

		return $modules;

	}

    /**
     * Perfoms a usort, using user defined sort by-field, sort direction and case-sensitivity
     *
     * @param array    $array    array to sort
     * @param array    $sortBy    array to array of key, direction and case-sensitivity
     * @access     public
     */
    public function customSortArray(&$array, $sortBy)
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
		$u['cache'] = $this->baseUrl.'shared/cache/'.$pCode.'/';

		// urls of the directory containing project specific media part of the interface, but not of the content (background etc)
		$u['projectMedia'] = $this->baseUrl.$this->getAppName().'/media/project/'.$pCode.'/';
		$u['projectL2Maps'] = $u['projectMedia'].'l2_maps/';
		$u['projectSystemOverride'] = $u['projectMedia'].'system_override/';

		// urls of the directory containing media that are constant across projects (but can be skinned)
		$u['systemMedia'] = $_SESSION['app']['system']['urls']['systemMedia'];
		$u['systemL2Maps'] = $this->baseUrl.'shared/media/system/l2_maps/';

		// urls of css-files, either project-specific - if they exist - or generic
		$u['cssRootDir'] = $this->baseUrl.$this->getAppName().'/style/';

		/*
		if (file_exists($projectCssDir.$pCode.'/basics.css')) {
			$u['projectCSS'] = $u['cssRootDir'].$pCode.'/';
		} else {
			$u['projectCSS'] = $u['cssRootDir'].'default/'.$this->generalSettings['app']['skinName'].'/';
		}
		*/

		$u['projectCSS'] = $u['cssRootDir'].'default/'.$this->generalSettings['app']['skinName'].'/';

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
	
	public function makeCustomCssFileName($incProjectName=true,$p=null)
	{

		if ($incProjectName)
			return $this->baseUrl.$this->getAppName().'/style/custom/'.$this->getProjectFSCode($p).'--'.$_SESSION['app']['project']['filesys_name'].'.css';
		else
			return $this->baseUrl.$this->getAppName().'/style/custom/'.$this->getProjectFSCode($p).'.css';

	}
	
	public function setCssFiles()
	{

		if (isset($_SESSION['app']['project']['urls']['projectCSS'])) {

			foreach((array)$this->cssToLoad as $key => $val)
				$this->cssToLoad[$key] = $_SESSION['app']['project']['urls']['projectCSS'].$val;

		}

		array_push($this->cssToLoad,'../utilities/dynamic-css.php');

		if (!is_null($this->getCurrentProjectId())) {

			$d = $this->makeCustomCssFileName();
	
			if (file_exists($d)) {
	
				array_push($this->cssToLoad,$d);
	
			} else {
	
				$d =  $this->makeCustomCssFileName(false);
	
				if (file_exists($d)) array_push($this->cssToLoad,$d);
	
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
	public function makeLookupList($data,$module,$url,$sortData=false,$encode=true)
	{

		$sortBy =
			array(
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

	public function doesProjectHaveModule($mpCode)
	{

		return isset($_SESSION['app']['project']['active-modules'][$mpCode]);

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
		if (session_id()=='') session_start();

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
        $this->smarty->assign('addedProjectIDParam', $this->generalSettings['addedProjectIDParam']);

    }

	public function loadControllerConfig($controllerBaseName=null)
    {

		if (isset($controllerBaseName))
	        $t = 'getControllerSettings'.$controllerBaseName;
		else
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
     * application name ('app')
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
                    
                    @$this->models->$t = new $t();
					
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

			// should be parametrized
			$a[$val] = strip_tags($a[$val]);
			$b[$val] = strip_tags($b[$val]);

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
                
                $c = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                
                if (class_exists($c)) {

                    @$this->helpers->$c = new $c();
                
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

	private function removeBackstepFromUrl($url)
	{
	
		return str_replace(array('?backstep=1','&backstep=1'),'',$url);
	
	}

	private function checkBackStep()
	{

		// check whether this is a clicked "back"-link
		if ($this->rHasVal('backstep','1')) {

			// take off the last page from the history (the one the user clicked "back" from)
			array_pop($_SESSION['app']['user']['history']);
			
			// remove the variable from the requestdata to avoid its accidental inclusion in any constructed URL's
			unset($this->requestData['backstep']);

			// as this page was already retrieved from history, it should not be added again
			$this->storeHistory = false;

		}
		
	}

	private function getBackLink()
	{

		// if there is no history, there is no going back
		if (!isset($_SESSION['app']['user']['history'])) return;

		// get the last history item (this does *not* include the current page, which is saved at destriction)
		$page = end($_SESSION['app']['user']['history']);

		// check whether the current page is the same as the previous (reload); if so, get on earlier from history
		if ($page == array('name' => $this->pageName,'url' => $this->removeBackstepFromUrl($_SERVER['REQUEST_URI'])))
			$page = prev($_SESSION['app']['user']['history']);

		// clean up the name
		if (isset($page['name'])) $page['name'] = str_replace('"', '', $page['name']);
	
		// add backstep=1 to the url to be able to identify it as a step back in history
		if (isset($page['url'])) $page['url'] .= (strpos($page['url'],'?')===false ? '?' : '&' ).'backstep=1';

		return $page;

	}

	private function storePageHistory()
	{

		// no pagename or explicit no storing: don't store
		if (empty($this->pageName) || $this->storeHistory==false) return;
		
		// undocumented history reset for development purposes through ?reset=history
		if ($this->rHasVal('reset','history')) unset($_SESSION['app']['user']['history']);
		
		// current page name & url (combination considered to be unique *after* stripping the backstep parameter)
		$thisPage = array('name' => $this->pageName,'url' => $this->removeBackstepFromUrl($_SERVER['REQUEST_URI']));

		// check if history exists, if so, get the last item: the previous visited page
		if (isset($_SESSION['app']['user']['history'])) $prevPage = end($_SESSION['app']['user']['history']);
		
		// if there is no previous page (if there is no history) or there is and it is different from the current (no reload), store in history
		if (!isset($prevPage) || $thisPage!=$prevPage) $_SESSION['app']['user']['history'][] = $thisPage;

		// see if a maximum number of steps to store is defined; if so, slice off the excess
		if (count((array)$_SESSION['app']['user']['history']) > $this->generalSettings['maxBackSteps']) {

			$_SESSION['app']['user']['history'] = 
				array_slice(
					$_SESSION['app']['user']['history'],
					count((array)$_SESSION['app']['user']['history']) - $this->generalSettings['maxBackSteps']
				);
				
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


	/*

		stores state my 'remembering' the value of four possible variables.
		also remembers the last visited page, as some modules have other pages than index linked from
		  the same tabs that in other modules link to the index with just different variables
		 
		caveats
		-> the place in the keypath is stored in the KeyController itself
		-> matrix states are stored in the MatrixController itself
	
	*/
	public function storeState()
	{

		if ($this->storeHistory===false || session_id()=='') return;

		unset($_SESSION['app']['user']['states'][$this->controllerBaseName]);
		
		$d = null;
		
		if ($this->rHasVal('id')) $d['id'] = $this->requestData['id'];
		if ($this->rHasVal('cat')) $d['cat'] = $this->requestData['cat'];
		if ($this->rHasVal('m')) $d['m'] = $this->requestData['m'];
		if ($this->rHasVal('letter')) $d['letter'] = $this->requestData['letter'];
		
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
	
	*/
	private function restoreState()
	{

		if (!isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()])) return;
		
		$thisUrl = $this->removeBackstepFromUrl($_SERVER['REQUEST_URI']);

		// /app/views/mapkey/ vs /app/views/mapkey/index.php
		$d = strpos($thisUrl,'?')==false ? $thisUrl : substr($thisUrl,0,strpos($thisUrl,'?'));
		$requestHasNoFileName = $this->getViewName()=='index' && ($d !== $_SERVER['PHP_SELF']);

		if (
			(
				$this->getControllerBaseName()=='mapkey' || 
				$this->getControllerBaseName()=='matrixkey' || 
				$this->getControllerBaseName()=='index'
			)  && 
			$requestHasNoFileName && 
			(
				isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage']) && 
				$_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage'] != $thisUrl)
			
			) 
		{

			$this->redirect($_SESSION['app']['user']['states'][$this->getControllerBaseName()]['lastPage']);

		}

		if (isset($_SESSION['app']['user']['states'][$this->getControllerBaseName()])) {
		
			foreach((array)$_SESSION['app']['user']['states'][$this->getControllerBaseName()] as $key => $val) {
			
				if (!isset($this->requestData[$key])) $this->requestData[$key] = $val;
			
			}
		
		}
		
	}
	
	// Timeout in seconds
	// Key something like path in session, e.g. 'species-tree'
	protected function getCache($key, $timeOut = false) 
	{
		$cacheFile = $_SESSION['app']['project']['urls']['cache'] . $key;
		if (file_exists($cacheFile)) {
			// Timeout provided and expired
			if ($timeOut && time()-$timeOut >= filemtime($cacheFile)) {
				// Delete from cache
				unlink($cacheFile);
				return false;
			}
			return unserialize(file_get_contents($cacheFile));
		}
		return false;
	}
	
	protected function saveCache($key, $data)
	{
		$cacheFile = $_SESSION['app']['project']['urls']['cache'] . $key;

		if (!file_put_contents($cacheFile, serialize($data))) {
			die('Cannot write to cache folder '. $_SESSION['app']['project']['urls']['cache']);
		}
		
		
	}

	public function getTreeList($p=null)
	{

		if (!isset($this->treeList)) $this->buildTaxonTree(); // return null;
		
		$d = array();

		foreach((array)$this->treeList as $key => $val) {
			
			if (!isset($p['includeEmpty']) && $p['includeEmpty']!==true && $val['is_empty']=='1') continue;

			$d[$key] = $val;

		}	

		return isset($d) ? $d : null;
	
	}

	public function buildTaxonTree($p=null)
	{

		if (!$this->getCache('species-treeList')) {
	
			$this->_buildTaxonTree();
			$this->saveCache('species-treeList', isset($this->treeList) ? $this->treeList : null);
	
		} else {
	
			$this->treeList = $this->getCache('species-treeList');
	
		}
		
		return $this->getTreeList($p);
	
		//return $this->getCache('species-tree'); // return value is unused!
	
	}

	private function createCacheFolder()
	{

		$p = $this->getCurrentProjectId();

		if (!$p) return;
		
		$cachePath = $this->generalSettings['directories']['cache'].'/'.$this->getProjectFSCode($p);
	
		if (!file_exists($cachePath)) mkdir($cachePath);	
	
	}

	private function checkWriteableDirectories()
	{

		$p = $this->getCurrentProjectId();

		if (!$p) return;
		
		$paths = array(
			$this->generalSettings['directories']['cache'].'/'.$this->getProjectFSCode($p)
		);
		
		foreach((array)$paths as $val) {

			if (file_exists($val) && !is_writable($val)) die('FATAL: cannot write to '.$val);
		
		}

	}

	public function splashScreen()
	{
	
		if ($this->getCheckForSplash()==false) return;
		
		if (
			(!isset($_SESSION['app']['project']['showedSplash']) || $_SESSION['app']['project']['showedSplash']===false) &&
			isset($this->generalSettings['urlSplashScreen'])) {

			$_SESSION['app']['project']['splashEntryUrl'] = $_SERVER['REQUEST_URI'];

			$this->redirect($this->generalSettings['urlSplashScreen']);
		
		}
	
	}
	
}