<?php

	/*

	the content for 'About ETI' is the same for all projects. it is stored in the same table 
	as the project specific content, using project ID=-10 (defined in the configuration file).
	it needs to be available in the same language(s) that the project uses in order for
	it to be displayed.
	there is no editing interface for the 'About ETI' content. it should be edited directly
	in the database (SELECT * FROM dev_content WHERE project_id = -10)
	(dutch = 24, english = 26)

	*/

include_once ('Controller.php');

class LinnaeusController extends Controller
{

    public $usedModels = array(
		'content'
    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
		'basics.css',
		'lookup.css',
	    'linnaeus.css',
		'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'lookup.js',
		'dialog/jquery.modaldialog.js'
	));
	
		
    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($params=null)
    {

		$this->setControllerParams($params);

        parent::__construct();
		
		if ($this->getCheckForProjectId()) {

			$this->checkForProjectId();
			$this->setCssFiles();
			
		}

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
     * Set the active project ID
     *
     * @access    public
     */
    public function setProjectAction ()
    {

		unset($_SESSION['app']['project']);
		unset($_SESSION['app']['user']);

		$this->resolveProjectId();

		if (!$this->getCurrentProjectId()) {

			$this->addError(_('Unknown project or invalid project ID.'));

	        $this->printPage();

		} else {

	        $this->setUrls();

			$this->setPaths();

			$this->setCurrentProjectData();
			
			$this->setCssFiles();
		
			if ($this->rHasVal('r')) 
				$this->redirect($this->requestData['r']);
			else
				$this->redirect('index.php');

		}

    }


    /**
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function indexAction ()
    {

		if ($this->rHasVal('show','icongrid'))
			$this->printPage();
		else
			$this->redirect('../linnaeus/content.php?sub=Welcome');


    }

    /**
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function contentAction ()
    {

		//unset($_SESSION['app']['user']['search']['hasSearchResults']);

		if (!$this->rHasVal('sub')) {

			$d = $this->getContent('Welcome');

		} else {
		
			$d = $this->getContent($this->requestData['sub']);

		}

		$this->setPageName(_($d['subject']));

		$this->smarty->assign('subject',$this->matchHotwords($this->matchGlossaryTerms($d['subject'])));
		$this->smarty->assign('content',$this->matchHotwords($this->matchGlossaryTerms($d['content'])));

        $this->printPage();
  
    }
	
    public function rootIndexAction()
	{

		$projects = $this->models->Project->_get(array('id' => array('published' => 1)));

		$this->smarty->assign('hasEntryProgram',$this->doesEntryProgramExist());
		$this->smarty->assign('showEntryProgramLink',$this->generalSettings['showEntryProgramLink']);
		$this->smarty->assign('projects',$projects);
		$this->smarty->assign('excludeLogout',true);

        $this->printPage('linnaeus/root_index');
	
	}

	private function getContent($sub=null,$id=null)
	{

		// see note at top of file
		if ($sub==$this->controllerSettings['contentAboutETI']['sub']) {

			$d = array(
				'project_id' => $this->controllerSettings['contentAboutETI']['projectID'],
				'language_id' => $this->getCurrentLanguageId()
			);

		} else {

			$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->getCurrentLanguageId()
			);
			
			if ($id!=null) $d['id'] = $id;
			elseif ($sub!=null) $d['subject'] = $sub;
			else return;
		
		}
		
		$c = $this->models->Content->_get(array('id' => $d));

		return isset($c[0]) ? $c[0] : null;
	
	}


}
