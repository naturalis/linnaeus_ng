<?php

include_once ('Controller.php');

class ModuleController extends Controller
{

    public $usedModels = array(
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'content_free_module',
		'free_module_media'
    );

	public $cssToLoad = array(
		'imaginarybeings-basics.css',
		'imaginarybeings-module.css',
		'colorbox/colorbox.css'
	); 

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'colorbox/jquery.colorbox.js'
			),
			'IE' => array()
		);

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();
	
		$this->checkForProjectId();

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
     * Index: sets the current module and displays an index of that module's topics
     *
     * @access    public
     */
	public function indexAction()
	{

		if ($this->rHasVal('modId')) {

			$this->setCurrentModule($this->getFreeModule($this->requestData['modId']));

		}

		if (!$this->getCurrentModuleId()) {

			$this->addError(_('Unknown module ID.'));

		} else {

			$alpha = $this->getPageAlphabet();

			if (!$this->rHasVal('letter') && $alpha) $this->requestData['letter'] = current($alpha);

			if ($this->rHasVal('letter')) {
			
				$this->requestData['letter'] = strtolower($this->requestData['letter']);

				$refs = $this->getPagesByLetter($this->requestData['letter']);

				$this->smarty->assign('letter', $this->requestData['letter']);

			}
			
			$module = $this->getCurrentModule();

			$this->setPageName(_($module['module']));

			if (isset($alpha)) $this->smarty->assign('alpha',$alpha);
			
			if (isset($refs)) $this->smarty->assign('refs',$refs);

			$this->smarty->assign('headerTitles',array('title' => $module['module']));

			$this->smarty->assign('module',$module);

		}

		unset($_SESSION['user']['search']['hasSearchResults']);

        $this->printPage();
	
	}

    /**
     * Show page
     *
     * @access    public
     */
    public function topicAction()
    {

		if ($this->rHasVal('modId')) {

			$this->setCurrentModule($this->getFreeModule($this->requestData['modId']));

		}

		if (!$this->rHasId()) {

			$this->addError(_('No page ID specified.'));

		} else {

			$id = $this->requestData['id'];

			$page = $this->getPage($id);

			$module = $this->getCurrentModule();

			$this->setPageName(sprintf(_($module['module'].': "%s"'),$page['topic']));

			$this->smarty->assign('headerTitles',array('title' => $module['module'],'subtitle' => $page['topic']));
	
			$this->smarty->assign('page', $page);

			$this->smarty->assign('adjacentPages', $this->getAdjacentPages($id));

			$this->smarty->assign('module',$module);
		}

        $this->printPage();
    
    }

    /**
     * Get a module's data
     *
     * @access    private
     */
	private function getFreeModule($id)
	{

		$fmp = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		if ($fmp) return $fmp[0];
	
	}

    /**
     * Set the current active module
     *
     * @access    private
     */
	private function setCurrentModule($module)
	{
	
		$_SESSION['user']['module']['activeModule'] = $module;

	}

    /**
     * Get the current active module's data
     *
     * @access    private
     */
	private function getCurrentModule()
	{

		return isset($_SESSION['user']['module']['activeModule']) ?
			$_SESSION['user']['module']['activeModule'] :
			null;
	
	}

    /**
     * Get the current active module's id
     *
     * @access    private
     */
	private function getCurrentModuleId()
	{

		return isset($_SESSION['user']['module']['activeModule']['id']) ?
				$_SESSION['user']['module']['activeModule']['id'] :
				null;

	}

	private function getPagesByLetter($letter)
	{
	
		if (!isset($_SESSION['user']['module']['alpha'])) $this->getPageAlphabet(true);

		return isset($_SESSION['user']['module']['alpha']['pages'][$letter]) ?
			$_SESSION['user']['module']['alpha']['pages'][$letter]:
			null;
	
	}

	private function getPageAlphabet($forceLookup=false)
	{

		if (!$forceLookup && isset($_SESSION['user']['module']['alpha']['alphabet'])) $_SESSION['user']['module']['alpha']['alphabet'];

		unset($_SESSION['user']['module']['alpha']);

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $this->getCurrentLanguageId(),
				),
				'columns' => 'page_id, topic, lower(substr(topic,1,1)) as letter',
				'order' => 'letter'
			)
		);

		$alpha = null;
		
		foreach((array)$cfm as $key => $val) {

			$alpha[$val['letter']] = $val['letter'];

			$_SESSION['user']['module']['alpha']['pages'][$val['letter']][] = array('id' => $val['page_id'],'topic' => $val['topic']);

		}

		$_SESSION['user']['module']['alpha']['alphabet'] = $alpha;

		return $alpha;
	
	}

	private function getPage($id)
	{

		if (!isset($id)) return;

		$fmp = $this->models->FreeModulePage->_get(
			array(
				'id' => array(
					'id' => $id,
					'module_id' => $this->getCurrentModuleId(),
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		if (!$fmp) {
		
			return;

		} else {
		
			$page = $fmp[0];

			$cfm = $this->models->ContentFreeModule->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $this->getCurrentModuleId(),
						'language_id' => $this->getCurrentLanguageId(),
						'page_id' => $id,
					)
				)
			);

			$page['topic'] = $cfm[0]['topic'];
			$page['content'] = $cfm[0]['content'];

			$fmm = $this->models->FreeModuleMedia->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $id
					)
				)
			);
			
			if ($fmm) {

				$page['image']['file_name'] = $fmm[0]['file_name'];
				$page['image']['thumb_name'] = $fmm[0]['thumb_name'];

			}

			return $page;
		
		}

	}

	private function getAdjacentPages($id)
	{

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'module_id' => $this->getCurrentModuleId(),
					'language_id' => $this->getCurrentLanguageId(),
				),
				'columns' => 'page_id,topic',
				'order' => 'topic'
			)
		);

		foreach((array)$cfm as $key => $val) {

			if ($val['page_id']==$id) {
			
				return array(
					'prev' => isset($cfm[$key-1]) ? $cfm[$key-1] : null,
					'next' => isset($cfm[$key+1]) ? $cfm[$key+1] : null
				);

			}

		}
		
		return null;

	}

}