<?php 

include_once ('Controller.php');
include_once ('MediaController.php');

class FreeModuleController extends Controller
{
	private $_mc;

    public $usedModels = array(
        'free_modules_projects',
        'free_modules_projects_users',
        'free_modules_pages',
        'content_free_modules',
        'free_module_media'
    );
    public $cssToLoad = array(
        'module.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'main.js',
            'lookup.js',
            'dialog/jquery.modaldialog.js'
        ),
        'IE' => array()
    );

    public $usedHelpers = array('old_show_media_link_replacer');


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ($p = null)
    {
        parent::__construct($p);
		$this->setMediaController();
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

	private function setMediaController()
	{
        $this->_mc = new MediaController();
        $this->_mc->setModuleId($this->getCurrentModuleId());
        $this->_mc->setItemId($this->rGetId());
	}


    /**
     * Index: sets the current module and displays an index of that module's topics
     *
     * @access    public
     */
    public function indexAction()
    {
        // set a new module id, if any (switching between multiple modules)
        if ($this->rHasVal('modId'))
		{
            $this->setCurrentModule($this->getFreeModule($this->rGetVal('modId')));
        }

        // if no module has been set, raise an error
        if (!$this->getCurrentModuleId())
		{
            $this->addError($this->translate('Unknown module ID.'));
            $this->printPage();
        }
        else
		{
            $this->setStoreHistory(false);

            // two ways of requesting a specific page
            $id = ($this->rHasVal('page') ? $this->rGetVal('page') : ($this->rHasVal('id') ? $this->rGetId() : null));

            $page = $this->getPage($id);

            if (!empty($page))
			{
                $id = $page['id'];
				$mId = $this->checkPageInModule($id);
				if ($mId!==true) $this->setCurrentModule($mId);
            }
            else
			{
                $page = $this->getFirstModulePage();
                $id = $page['id'];
            }

            $this->redirect('topic.php?id=' . $id);
        }
    }



    /**
     * Show page
     *
     * @access    public
     */
    public function topicAction()
    {

        if ($this->rHasVal('modId'))
		{
            $this->setCurrentModule($this->getFreeModule($this->rGetVal('modId')));
        }

        $module = $this->getCurrentModule();

        if ($this->rHasVal('letter') && $module['show_alpha'] == '1')
		{
            $refs = $this->getPagesByLetter(strtolower($this->rGetVal('letter')));
            $id = $refs[0]['id'];
        }
        else
		if ($this->rHasId())
		{
            $id = $this->rGetId();
        }
        else
		{
            $this->addError($this->translate('No page ID specified.'));
            $id = null;
        }

        if ($id)
		{
            $mId = $this->checkPageInModule($id);

            if ($mId!==true)
			{
				$this->setCurrentModule($this->getFreeModule($mId));
            	$module = $this->getCurrentModule();
            }
        }

        if (!is_null($id))
		{
            $page = $this->getPage($id);

			if (!empty($page))
			{
				$this->smarty->assign('page', $page);
				$this->smarty->assign('adjacentItems', $this->getAdjacentPages($id));
			}

        }

        if ($module['show_alpha'] == '1')
            $this->smarty->assign('alpha', $this->getPageAlphabet());

        if ($this->rHasVal('letter'))
            $this->smarty->assign('letter', $this->rGetVal('letter'));

		if (!empty($page)) {
	        $this->setPageName(sprintf($this->translate($module['module'] . ': "%s"'), $page['topic']));

			$this->smarty->assign('headerTitles', array(
				'title' => $module['module'],
				'subtitle' => $page['topic']
			));

		}

        $this->smarty->assign('module', $module);

		if ($this->rHasVal('style','inner'))
		{
	        $this->printPage('_topic');
		}
		else
		{
	        $this->printPage();
		}
    }



    /**
	* General interface for all AJAX-calls
	*
	* @access     public
	*/
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

        if ($this->rHasVal('action', 'get_lookup_list') && !empty($this->rGetVal('search')))
		{
            $this->getLookupList($this->rGetAll());
        }

        $this->allowEditPageOverlay = false;

        $this->printPage();
    }



    /**
     * Get the current active module's data
     *
     * @access    public
     */
    public function getCurrentModule ()
    {
        return isset($_SESSION['app']['user']['module']['activeModule']) ? $_SESSION['app']['user']['module']['activeModule'] : null;
    }



    /**
     * Get a module's data
     *
     * @access    private
     */
    private function getFreeModule ($id)
    {
        $fmp = $this->models->FreeModulesProjects->_get(array(
            'id' => array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        if ($fmp)
            return $fmp[0];
    }



    /**
     * Set the current active module
     *
     * @access    private
     */
    private function setCurrentModule ($module)
    {
        $_SESSION['app']['user']['module']['activeModule'] = $module;
    }



    /**
     * Get the current active module's id
     *
     * @access    private
     */
    protected function getCurrentModuleId ($remapModule = false)
    {
        return isset($_SESSION['app']['user']['module']['activeModule']['id']) ? $_SESSION['app']['user']['module']['activeModule']['id'] : null;
    }



    private function getPagesByLetter ($letter, $forceLookup = false)
    {
        if (!isset($_SESSION['app']['user']['module']['alpha']) || $forceLookup == true)
            $this->getPageAlphabet(true);

        return isset($_SESSION['app']['user']['module']['alpha']['pages'][$letter]) ? $_SESSION['app']['user']['module']['alpha']['pages'][$letter] : null;
    }



    private function getPageAlphabet ($forceLookup = false)
    {
        if (!$forceLookup && isset($_SESSION['app']['user']['module']['alpha']['alphabet']))
            $_SESSION['app']['user']['module']['alpha']['alphabet'];

        unset($_SESSION['app']['user']['module']['alpha']);

        $cfm = $this->models->ContentFreeModules->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->getCurrentModuleId(),
                'language_id' => $this->getCurrentLanguageId()
            ),
            'columns' => 'page_id, topic, lower(substr(topic,1,1)) as letter',
            'order' => 'letter'
        ));

        $alpha = null;

        foreach ((array) $cfm as $key => $val) {

            $alpha[$val['letter']] = $val['letter'];

            $_SESSION['app']['user']['module']['alpha']['pages'][$val['letter']][] = array(
                'id' => $val['page_id'],
                'topic' => $val['topic']
            );
        }

        $_SESSION['app']['user']['module']['alpha']['alphabet'] = $alpha;

        return $alpha;
    }



    private function getPage( $id )
    {
        if (!isset($id)) return;

        $t = $this->models->FreeModulesPages->_get(array(
            'id' => array(
                'id' => $id,
                'module_id' => $this->getCurrentModuleId(),
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        if (!$t) return;

		$page = $t[0];

		$t = $this->models->ContentFreeModules->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'module_id' => $this->getCurrentModuleId(),
				'language_id' => $this->getCurrentLanguageId(),
				'page_id' => $id
			)
		));

		$page['topic']=$t[0]['topic'];
		$page['content']=$t[0]['content'];

		if (!is_null($this->helpers->OldShowMediaLinkReplacer))
		{
			$this->helpers->OldShowMediaLinkReplacer->setContent( $page['content'] );
			$this->helpers->OldShowMediaLinkReplacer->replaceLinks();
			$page['content']=$this->helpers->OldShowMediaLinkReplacer->getTransformedContent();
		}

		$page['content'] = $this->matchHotwords( $page['content'] );

		$this->_mc->setItemId($id);
		$media = $this->_mc->getItemMediaFiles();

		if ($media)
		{
			$page['image'] = $media[0]['rs_original'];
		}

		return $page;

    }



    private function getFirstModulePage ()
    {
        return $this->getFirstModulePageByShowOrder();
    }



    private function getFirstModulePageByShowOrder ()
    {
        $cfm = $this->models->FreeModulesPages->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->getCurrentModuleId(),
                'got_content' => 1
            //'language_id' => $this->getCurrentLanguageId(),
            )
            ,
            'order' => 'show_order',
            'limit' => 1
        ));

        return isset($cfm[0]) ? $cfm[0] : null;
    }



    private function getFirstModulePageAlphabetically ()
    {
        $cfm = $this->models->ContentFreeModules->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->getCurrentModuleId(),
                'language_id' => $this->getCurrentLanguageId()
            ),
            'order' => 'topic',
            'columns' => 'page_id',
            'ignoreCase' => true,
            'limit' => 1
        ));

        return isset($cfm[0]) ? $cfm[0] : null;
    }



    private function getAdjacentPages ($id)
    {
        return $this->getAdjacentPagesByShowOrder($id);
    }



    private function getAdjacentPagesByShowOrder ($id)
    {
        if (!isset($id))
            return;

        $fmp = $this->models->FreeModulesPages->_get(array(
            'id' => array(
                'module_id' => $this->getCurrentModuleId(),
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'show_order'
        ));

        if (!$fmp) {

            return;
        }
        else {

            foreach ((array) $fmp as $key => $val) {

                $cfm = $this->models->ContentFreeModules->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'module_id' => $this->getCurrentModuleId(),
                        'language_id' => $this->getCurrentLanguageId(),
                        'page_id' => $val['id']
                    )
                ));

                $fmp[$key]['label'] = $cfm[0]['topic'];
            }
        }

        foreach ((array) $fmp as $key => $val) {

            if ($val['id'] == $id) {

                return array(
                    'prev' => isset($fmp[$key - 1]) ? $fmp[$key - 1] : null,
                    'next' => isset($fmp[$key + 1]) ? $fmp[$key + 1] : null
                );
            }
        }

        return null;
    }



    private function getAdjacentPagesAlphabetically ($id)
    {
        $cfm = $this->models->ContentFreeModules->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'module_id' => $this->getCurrentModuleId(),
                'language_id' => $this->getCurrentLanguageId()
            ),
            'columns' => 'page_id as id,topic as label',
            'order' => 'topic'
        ));

        foreach ((array) $cfm as $key => $val) {

            if ($val['id'] == $id) {

                return array(
                    'prev' => isset($cfm[$key - 1]) ? $cfm[$key - 1] : null,
                    'next' => isset($cfm[$key + 1]) ? $cfm[$key + 1] : null
                );
            }
        }

        return null;
    }

    private function checkPageInModule($id,$mId=null)
    {
        if (!isset($id))
            return;

        $fmp = $this->models->FreeModulesPages->_get(array(
            'id' => array(
                'id' => $id,
                'project_id' => $this->getCurrentProjectId()
            )
        ));

        return ($fmp[0]['module_id']==(empty($mId) ? $this->getCurrentModuleId() : $mId) ? true : $fmp[0]['module_id']);

    }

    private function getLookupList ($p)
    {
        $search = isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start'] == '1' : false;
        $getAll = isset($p['get_all']) ? $p['get_all'] == '1' : false;

        //		$search = str_replace(array('/','\\'),'',$search);


        if (empty($search) && !$getAll)
            return;

        $d = array(
            'project_id' => $this->getCurrentProjectId(),
            'module_id' => $this->getCurrentModuleId()
        );

        if (!$getAll) {

            if ($matchStartOnly)
                $match = mysqli_real_escape_string($this->databaseConnection, $search) . '%';
            else
                $match = '%' . mysqli_real_escape_string($this->databaseConnection, $search) . '%';

            $d['topic like'] = $match;
        }

        $cfm = $this->models->ContentFreeModules->_get(array(
            'id' => $d,
            'order' => 'topic',
            'columns' => 'distinct page_id as id, topic as label'
        ));

        $this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$cfm,
				'module'=>$this->controllerBaseName,
				'url'=>'../module/topic.php?id=%s',
				'sortData'=>true
			))
		);
    }

}