<?php

include_once ('Controller.php');

class IntroductionController extends Controller
{

    public $usedModels = array(
		'content_introduction',
		'introduction_pages',
		'introduction_media'
    );

    public $controllerPublicName = 'Introduction';

	public $cssToLoad = array(
		'module.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'prettyPhoto/jquery.prettyPhoto.js',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
			),
			'IE' => array()
		);


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {
		parent::__construct($p);
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
		$this->setStoreHistory(false);

		if (!$this->rHasVal('page') && !$this->rHasVal('id'))
		{
			$this->redirect('topic.php?id='.$this->getFirstPageId());
		}
		else
		{
			$id = $this->rHasVal('page') ? $this->rGetVal('page') : $this->rGetId();
			$this->redirect('topic.php?id='.$id);
		}
	}

    /**
     * Create new page or edit existing
     *
     * @access    public
     */
    public function topicAction()
    {
		if (!$this->rHasId())
		{
			$page = array(
				'content' => $this->translate('No ID specified, or no content available.')
			);
			$this->smarty->assign('page', $page);
		}
		else
		{

			if (!is_numeric($this->rGetId()))
			{
				$id = $this->resolvePageName(
					$this->rGetId(),
					($this->rHasVal('lan') ? $this->rGetVal('lan') : $this->getDefaultLanguageId())
				);
			}
			else
			{
				$id = $this->rGetId();
			}

			$page = $this->getPage($id);

			$page['content'] = $this->matchGlossaryTerms($page['content']);
			$page['content'] = $this->matchHotwords($page['content']);

			$this->setPageName($page['topic']);

			$this->smarty->assign('headerTitles',array('title' => $page['topic']));
			$this->smarty->assign('page', $page);
			$this->smarty->assign('adjacentItems', $this->getAdjacentPages($id));
		}

		if ( $this->rHasVal('format','plain') )
		{
	        $this->printPage('topic_plain');
		}
		else
		{
	        $this->printPage();
		}
    }


	public function listAction()
	{
		$refs = $this->getPageHeaders();

		$this->setPageName($this->translate('Introduction contents'));

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

        if ($this->rHasVal('action', 'get_content'))
		{
            $this->ajaxActionGetContent();
        }
        if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search')))
		{
            $this->getLookupList($this->requestData);
        }

		$this->allowEditPageOverlay = false;
        $this->printPage();
    }

	private function getPageHeaders()
	{
		$ip =  $this->models->IntroductionPages->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				),
				'order' => 'show_order,created'
			)
		);

		foreach((array)$ip as $key => $val)
		{
			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getDefaultLanguageId(),
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

		$id = isset($id) ? $id : $this->rGetId();

		if (!isset($id)) return;

		$pfm = $this->models->IntroductionPages->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => 1
				)
			)
		);

		if ($pfm)
		{
			$page = $pfm[0];

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->getCurrentLanguageId(),
						'page_id' => $id
					)
				)
			);

			if ($cfm)
			{
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

			if ($fmm)
			{
				$page['image']['file_name'] = $fmm[0]['file_name'];
				$page['image']['thumb_name'] = $fmm[0]['thumb_name'];
			}

			return $page;

		}
		else
		{

			return null;

		}

	}

	private function getFirstPageId()
	{
		$ip =  $this->models->IntroductionPages->_get(
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
		$ip = $this->models->IntroductionPages->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getCurrentLanguageId(),
					'got_content' => 1
				),
				'order' => 'show_order'
			)
		);

		foreach((array)$ip as $key => $val)
		{
			if ($val['id']==$id)
			{
				return array(
					'prev' => isset($ip[$key-1]) ? $ip[$key-1] : null,
					'next' => isset($ip[$key+1]) ? $ip[$key+1] : null
				);
			}
		}

		return null;
	}

	private function getLookupList($p)
	{
		$search = isset($p['search']) ? $p['search'] : null;
		$matchStartOnly = isset($p['match_start']) ? $p['match_start']=='1' : false;
		$getAll = isset($p['get_all']) ? $p['get_all']=='1' : false;

		if (empty($search) && !$getAll) return;

		if (!$getAll)
		{
			$d = array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $this->getDefaultLanguageId()
				);

			if ($matchStartOnly)
			{
				$match = mysql_real_escape_string($search).'%';
			}
			else
			{
				$match = '%'.mysql_real_escape_string($search).'%';
			}

			$d['topic like'] = $match;

			$cfm = $this->models->ContentIntroduction->_get(
				array(
					'id' => $d,
					'columns' => 'distinct page_id as id, topic as label',
					'order' => 'label asc'
				)
			);

		}
		else
		{
			$pages = $this->getPageHeaders();
		}

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$pages,
				'module'=>$this->controllerBaseName,
				'url'=>'../introduction/topic.php?id=%s'
			))
		);

	}

	private function resolvePageName($name,$languageId)
	{
		$d =  $this->models->ContentIntroduction->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $languageId,
					'topic' => $name
				)
			)
		);

		return $d ? $d[0]['page_id'] : null;
	}

}
