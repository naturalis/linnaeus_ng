<?php

include_once ('Controller.php');

class IntroductionController extends Controller
{

    public $usedModels = array(
		'content_introduction',
		'introduction_page',
		'introduction_media'
    );
   
    public $controllerPublicName = 'Introduction';

	public $cssToLoad = array(
		'basics.css',
		'module.css',
		'colorbox/colorbox.css',
		'lookup.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'colorbox/jquery.colorbox.js',
				'lookup.js'
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

		unset($_SESSION['app']['user']['search']['hasSearchResults']);

		if (!$this->rHasVal('page')) {

			$this->redirect('topic.php?id='.$this->getFirstPageId());

		} else {

			$this->redirect('topic.php?id='.$this->requestData['page']);

		}

	}


    /**
     * Create new page or edit existing
     *
     * @access    public
     */
    public function topicAction()
    {

		if (!$this->rHasId()) {

			$this->addError(_('No page ID specified.'));

		} else {

			$id = $this->requestData['id'];

			$page = $this->getPage($id);
		
			$page['content'] = $this->matchGlossaryTerms($page['content']);

			$this->setPageName($page['topic']);

			$this->smarty->assign('headerTitles',array('title' => $page['topic']));

			$this->smarty->assign('page', $page);

			$this->smarty->assign('adjacentItems', $this->getAdjacentPages($id));

		}

        $this->printPage();
    
    }


	public function listAction()
	{

		$refs = $this->getPageHeaders();
	
		$this->setPageName(_('Introduction contents'));
		
		if (isset($refs)) $this->smarty->assign('refs',$refs);
	
		$this->printPage();
	
	}
	
	
	/**
	* General interface for all AJAX-calls
	*
	* @access     public
	*/
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'save_content') {
            
            $this->ajaxActionSaveContent();
        
        } else
        if ($this->requestData['action'] == 'get_content') {
            
            $this->ajaxActionGetContent();
        
        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
    
    }

	private function getPageHeaders()
	{

		$ip =  $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created'
			)
		);


		foreach((array)$ip as $key => $val) {

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $_SESSION['app']['project']['default_language_id'], 
						'page_id' => $val['id']
					),
					'columns' => 'topic',
				)
			);
			
			$ip[$key]['topic'] = $ip[$key]['label'] = $cfm[0]['topic'];

		}
		
		return $ip;

	}

	private function getPage($id=null)
	{

		$id = isset($id) ? $id : $this->requestData['id'];
		
		if (!isset($id)) return;

		$pfm = $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				)
			)
		);

		if ($pfm) {
		
			$page =  $pfm[0];

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->getCurrentLanguageId(),
						'page_id' => $id
					)
				)
			);

			if ($cfm) {

				$page['topic'] = $cfm[0]['topic'];

				$page['content'] = $cfm[0]['content'];

			}
			

			$fmm = $this->models->IntroductionMedia->_get(
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
		
		} else {

			return null;

		}

	}

	private function getFirstPageId()
	{

		$ip =  $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created',
				'columns' => 'id',
				'limit' => 1
			)
		);


		return isset($ip[0]['id']) ? $ip[0]['id'] : null;
		
	}

	private function getAdjacentPages($id)
	{

		$ip = $this->models->IntroductionPage->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->getCurrentLanguageId(),
					'got_content' => 1
				),
				'order' => 'show_order'
			)
		);

		foreach((array)$ip as $key => $val) {

			if ($val['id']==$id) {
			
				return array(
					'prev' => isset($ip[$key-1]) ? $ip[$key-1] : null,
					'next' => isset($ip[$key+1]) ? $ip[$key+1] : null
				);

			}

		}
		
		return null;
		
	}

	private function getLookupList($search)
	{

		if (empty($search)) return;

		$cfm = $this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' =>  $_SESSION['app']['project']['default_language_id'],
					'topic like' => '%'.$search.'%'
					),
				'order' => 'topic',
				'columns' => 'distinct page_id as id, topic as label',
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$cfm,
				$this->controllerBaseName,
				'../introduction/topic.php?id=%s',
				true
			)
		);
		
	}

}
