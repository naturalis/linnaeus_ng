<?php

include_once ('Controller.php');

class ContentController extends Controller
{

	private $_subjects = array(
		0 => array('name' => 'Introduction', 'url' => 'introduction.php'),
		1 => array('name' => 'Contributors', 'url' => 'contributors.php')
	);


    public $usedModels = array(
		'content'
    );

    public $usedHelpers = array(
    );

    public $controllerPublicName = 'Content';

	public $cssToLoad = array('dialog/jquery.modaldialog.css');
	public $jsToLoad = array('all' => array('content.js','int-link.js','dialog/jquery.modaldialog.js'));


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();

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
     * Index
     *
     * @access    public
     */
    public function indexAction()
    {

		$this->redirect('content.php');

		/*
        $this->checkAuthorisation();

        $this->printPage();
	    */

    }


    /**
     * Introduction
     *
     * @access    public
     */

    public function introductionAction()
    {

		$_SESSION['admin']['system']['content']['current-subject'] = 'Introduction';
		$_SESSION['admin']['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');

    }

    /**
     * Contributors
     *
     * @access    public
     */

    public function contributorsAction()
    {

		$_SESSION['admin']['system']['content']['current-subject'] = 'Contributors';
		$_SESSION['admin']['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');

    }

    /**
     * About ETI
     *
     * @access    public
     */
	/*
    public function aboutEtiAction()
    {

		$_SESSION['admin']['system']['content']['current-subject'] = 'About ETI';
		$_SESSION['admin']['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');

    }
	*/

    /**
     * Welcome
     *
     * @access    public
     */
    public function welcomeAction()
    {

		$_SESSION['admin']['system']['content']['current-subject'] = 'Welcome';
		$_SESSION['admin']['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');

    }

    public function contentAction()
    {

		$this->checkAuthorisation();

		if ($this->rHasId()) {

			$d = $this->getContentById($this->requestData['id'],$_SESSION['admin']['project']['default_language_id']);
			$_SESSION['admin']['system']['content']['current-subject'] = $d['subject'];

		}

		$currentSubject =
			isset($_SESSION['admin']['system']['content']['current-subject']) ?
			$_SESSION['admin']['system']['content']['current-subject'] :
			'Introduction';

        $this->setPageName($this->translate($currentSubject));

		$this->smarty->assign('isFreeModule', isset($_SESSION['admin']['system']['content']['is-free-module']) ? $_SESSION['admin']['system']['content']['is-free-module'] : false);

		if (isset($_SESSION['admin']['system']['content']['free-module-id']))
			$this->smarty->assign('freeModuleId',$_SESSION['admin']['system']['content']['free-module-id']);

		$this->smarty->assign('subject', $currentSubject);

		$this->smarty->assign('subjects', $this->_subjects);

		$this->smarty->assign('languages', $this->getProjectLanguages());

		$this->smarty->assign('activeLanguage', $_SESSION['admin']['project']['default_language_id']);

		$this->smarty->assign('includeHtmlEditor', true);

        $this->printPage();

    }

    public function previewAction ()
    {

		$this->redirect('../../../app/views/linnaeus/index.php?p='.$this->getCurrentProjectId().'&sub='.$this->requestData['subject']);

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

        $this->printPage();

    }

	private function ajaxActionSaveContent()
	{

       if (!$this->rHasId() || !$this->rHasVal('language')) {

            return;

        } else {

            if (!$this->rHasVal('content')) {

                $this->models->Content->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->requestData['language'],
                        'subject' => $this->requestData['id']
                    )
                );

            } else {

                $ls = $this->models->Content->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->requestData['language'],
							'subject' => $this->requestData['id']
						)
					)
				);

                $this->models->Content->save(
					array(
						'id' => isset($ls[0]['id']) ? $ls[0]['id'] : null,
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->requestData['language'],
						'subject' => $this->requestData['id'],
						'content' => trim($this->requestData['content'])
					));

            }

            $this->smarty->assign('returnText', 'saved');

        }

	}


	private function getContentBySubject($id,$languageId)
	{

        if (!$languageId || !$id)  return;

		$c = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $languageId,
					'subject' => $id
					)
			)
		);

	   return $c[0];

	}

	private function getContentById($id)
	{

        if (!$id) return;

		$c = $this->models->Content->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
					)
			)
		);

	   return $c[0];

	}

	private function ajaxActionGetContent()
	{

        if (!$this->rHasVal('language') || !$this->rHasVal('id')) {

            return;

        } else {

			$d = $this->getContentBySubject($this->requestData['id'],$this->requestData['language']);

            $this->smarty->assign('returnText', $d['content']);

        }

	}

}