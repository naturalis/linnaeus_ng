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
include_once ('ModuleSettingsReaderController.php');

class LinnaeusController extends Controller
{

    public $usedModels = array(
		'content'
	);

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
	    'linnaeus.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'lookup.js',
		'dialog/jquery.modaldialog.js'
	));

	public $controllerBaseName = 'linnaeus';

    public function __construct( $p=null )
    {
        parent::__construct( $p );
		$this->initialize( $p );
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize( $p )
    {
		$this->moduleSettings=new ModuleSettingsReaderController( $p );
    }
	
    public function setProjectAction()
    {
		$this->unsetProjectSession();
		$this->unsetUserSession();
		$this->resolveProjectId();
		$this->defaultToFirstPublishedProject();
		$this->setProjectData();
		$this->redirectProjectUrl();
		$this->addError($this->translate('No published projects available.'));
		$this->printPage();
    }

    public function homeAction()
    {
		$this->redirectProjectUrl();
    }

    public function indexAction()
    {

		$this->moduleSettings=new ModuleSettingsReaderController();

		$start_page=$this->moduleSettings->getGeneralSetting( 'start_page' );

		if ( !empty($start_page) )
		{
			if (( strtolower($start_page) !== '/linnaeus_ng/app/views/linnaeus/' ) && 
				( strtolower($start_page) !== '/linnaeus_ng/app/views/linnaeus/index.php' ))
			{
				$this->redirect( $start_page );
			}
		}

		$welcome_topic_id=$this->moduleSettings->getModuleSetting( [ 'module'=>'introduction', 'setting'=>'welcome_topic_id' ] );

		if ( !empty($welcome_topic_id) )
		{
			$this->redirect( '../introduction/topic.php?id=' . $welcome_topic_id );
		}

		if ( $this->doesCurrentProjectHaveModule(MODCODE_INTRODUCTION) )
		{
			$id = $this->models->LinnaeusModel->getFirstIntroductionTopicId(array(
				'project_id' => $this->getCurrentProjectId()
			));	
			
			if ( !is_null($id) ) 
			{
				$this->redirect( '../introduction/topic.php?id=' . $id );
			}
		}

		if ( $this->doesCurrentProjectHaveModule(MODCODE_SPECIES) )
		{
			$this->redirect( '../species/' );
		}
	
		if ( $this->doesCurrentProjectHaveModule(MODCODE_SPECIES) )
		{
			$this->redirect( '../index/' );
		}

		$this->printPage();
    }

    public function contentAction ()
    {
		if (!$this->rHasVal('sub') && !$this->rHasVal('id')) {

			$d = $this->getContent('Welcome');

		} else {

			$d = $this->getContent(
				($this->rHasVar('sub') ? $this->rGetVal('sub') : null),
				($this->rHasVar('id') ? $this->rGetId() : null)
			);

		}

		$this->setPageName($this->translate($d['subject']));

		$this->smarty->assign('subject',$this->matchHotwords($d['subject']));
		$this->smarty->assign('content',$this->matchHotwords($d['content']));
		//$this->smarty->assign('subject',$d['subject']);
		//$this->smarty->assign('content',$d['content']);

        $this->printPage();

    }

    public function rootIndexAction()
	{
		// have FIXED_PROJECT_ID constant
		if (defined('FIXED_PROJECT_ID')) $this->redirect('app/views/linnaeus/set_project.php?p='.FIXED_PROJECT_ID);

		// resolve slug (if any)
		$id=$this->resolveProjectShortName();
		if ($id) $this->redirect('app/views/linnaeus/set_project.php?p='.$id);

		// fetch all available published projects
		$projects = $this->models->Projects->_get(
			array(
				'id' => array('published' => 1),
				'order' => 'title'
			)
		);

		// if only one is available, use that
		if ( count((array)$projects)==1 ) $this->redirect('app/views/linnaeus/set_project.php?p='.$projects[0]['id']);

		// program redirected here after wrong project ID was attempted
		if ($this->rHasVar('nopid')) $this->smarty->assign('error',$this->translate('No or illegal project ID specified.'));

		if ( method_exists( $this->customConfig , 'getProjectIndexTexts' ) ) 
		{
			$texts=$this->customConfig->getProjectIndexTexts();
		}
		else
		{
			$texts=
				array(
					'page_title'=>'Select a project to work on',
					'page_header'=>'Select a project to work on',
					'search_placeholder'=>'',
					'left_bar_title'=>'',
					'left_bar_text'=>'',
				);
		}

		$this->smarty->assign('hasEntryProgram',$this->doesEntryProgramExist());
		$this->smarty->assign('showEntryProgramLink',$this->generalSettings['showEntryProgramLink']);
		$this->smarty->assign('projects',$projects);
		$this->smarty->assign('excludeLogout',true);
		$this->smarty->assign('texts',$texts);
		
		/**
		 * Documentation!
		 * 
		 * If the app refuses to start with the error 
		 * 
		 * PHP Fatal error:  Uncaught  --> Smarty: Unable to load template 'file:root_index.tpl' <-- \n  
		 * thrown in /var/www/html/vendor/smarty/smarty/libs/sysplugins/smarty_internal_template.php on line 195
		 * 
		 * Check the following: configuration.php getGeneralSettings()['app']['skinName'] = ***'linnaeus_2'***
		 * 
		 * For projects that display this error, the setting was 'linnaeus_ng_responsive'
		 */

        $this->printPage('root_index');

	}

    public function noProjectAction()
	{
	    $this->redirect($this->baseUrl.'?nopid');
	}

	public function getContent($sub=null,$id=null)
	{

		// see note at top of file
		if ($sub=='About ETI') {

			$d = array(
				'project_id' => -10,
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

	private function resolveProjectShortName()
	{
		$path = explode('/',$this->getfullPath());
		$n = strtolower($path[1]);

		if ($n!=$this->getAppName() && $n!='linnaeus_ng') {

            $p = $this->models->Projects->_get(array('id'=>array('short_name !='=>'null'),'columns'=>'id,short_name'));

			if ($p) {
				foreach((array)$p as $val) {
					if (empty($val['short_name']))
						continue;
					$d=explode(';',$val['short_name']);
					array_walk($d,function(&$a,$b){$a=trim($a);});
					if (in_array($n,$d)) {
						return $val['id'];
						exit;
					}
				}
			}


		}

		return null;

	}

    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search'))) {

            $this->getLookupList($this->rGetAll());

        }

		$this->allowEditPageOverlay = false;

        $this->printPage();

    }

	// general
	public function getLookupList($p)
	{

		$search=isset($p['search']) ? $p['search'] : null;
		$matchStart=isset($p['match_start']) ? $p['match_start']==1 : false;

		/* Method moved to ControllerModel as it's kind of generic; ControllerModel included in $usedModels() */
		$data = $this->models->ControllerModel->getLookupList(array(
            'search' => $search,
    		'matchStart' => $matchStart,
    		'projectId' => $this->getCurrentProjectId()
		));

		foreach((array)$data as $key=>$val)
		{
			if ($val['source']=='species')
				$data[$key]['label']=$this->formatTaxon(array(
				    'taxon'=>array(
				        'taxon'=>$val['label'],
				        'rank_id'=>$val['rank_id']
				    ),
				     'rankpos'=>'post'

				));
		}

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$data,
				'module'=>$this->controllerBaseName,
				'sortData'=>false
			))
		);

	}


	private function unsetProjectSession()
	{
		unset($_SESSION['app']['project']);
	}

	private function unsetUserSession()
	{
		unset($_SESSION['app']['user']);
	}

	private function defaultToFirstPublishedProject()
	{
		if ($this->getCurrentProjectId()) return;
		
		$d=$this->models->Projects->_get(array(
			'id'=>array('published'=>1),
			'order'=>'title,sys_name',
			'limit'=>1
		));
	
		if ($d)
		{
			$this->setCurrentProjectId( $d[0]['id'] );
		}
	}

	private function setProjectData()
	{
		if (!$this->getCurrentProjectId()) return;
		
		$this->setUrls();
		$this->setCurrentProjectData();
		$this->setCssFiles();
	}

	private function redirectProjectUrl()
	{
		if (!$this->getCurrentProjectId()) return;
		// extra check for FIXED_PROJECT_ID projects

		if ($_SESSION['app']['project']['published']!=1) return;

		$url='index.php';

		if ($this->rHasVal('r'))
		{
			$url = $this->rGetVal('r');
		} 
		else
		{
			$this->moduleSettings=new ModuleSettingsReaderController();

			if ( !empty($this->moduleSettings->getGeneralSetting( 'start_page' )) )
			{
				$url=$this->moduleSettings->getGeneralSetting( 'start_page' );
			}
		}

		$this->redirect($url);
	}




}
