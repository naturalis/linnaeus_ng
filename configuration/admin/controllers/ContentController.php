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
	public $jsToLoad = array('all' => array('content.js','dialog/jquery.modaldialog.js','int-link.js'));


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
    
		$_SESSION['system']['content']['current-subject'] = 'Introduction';
		$_SESSION['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');
    
    }

    /**
     * Contributors
     *
     * @access    public
     */

    public function contributorsAction()
    {
    
		$_SESSION['system']['content']['current-subject'] = 'Contributors';
		$_SESSION['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');
    
    }

    /**
     * About ETI
     *
     * @access    public
     */

    public function aboutEtiAction()
    {
    
		$_SESSION['system']['content']['current-subject'] = 'About ETI';
		$_SESSION['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');
    
    }


    /**
     * Welcome
     *
     * @access    public
     */
    public function welcomeAction()
    {
    
		$_SESSION['system']['content']['current-subject'] = 'Welcome';
		$_SESSION['system']['content']['is-free-module'] = false;

		$this->redirect('content.php');
    
    }
	
    public function contentAction()
    {
    
		$this->checkAuthorisation();

		$currentSubject =
			isset($_SESSION['system']['content']['current-subject']) ?
			$_SESSION['system']['content']['current-subject'] : 
			'Introduction';

        $this->setPageName(_($currentSubject));

		$this->smarty->assign('isFreeModule', isset($_SESSION['system']['content']['is-free-module']) ? $_SESSION['system']['content']['is-free-module'] : false);

		if (isset($_SESSION['system']['content']['free-module-id']))
			$this->smarty->assign('freeModuleId',$_SESSION['system']['content']['free-module-id']);

		$this->smarty->assign('subject', $currentSubject);

		$this->smarty->assign('subjects', $this->_subjects);

		$this->smarty->assign('languages', $_SESSION['project']['languages']);
		
		$this->smarty->assign('activeLanguage', $_SESSION['project']['default_language_id']);

		$this->smarty->assign('includeHtmlEditor', true);

        $this->printPage();
    
    }

    public function previewAction ()
    {

		$content = $this->getContent($this->requestData['subject'],$_SESSION['project']['default_language_id']);

		$this->smarty->assign('backUrl','content.php?sub='.$this->requestData['subject']);
		//$this->smarty->assign('nextUrl','edit.php?id='.$navList[$this->requestData['id']]['next']['id']);

		$this->smarty->assign('subject', $this->requestData['subject']);
		if (isset($content)) $this->smarty->assign('content', $content);

		$this->printPreviewPage(
			'../../../../app/templates/templates/linnaeus/_index',
			'index.css',
			'../../../../app/templates/templates/linnaeus/_menu.tpl'
		);
    
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


	private function getContent($id,$languageId)
	{

        if (!$languageId || !$id) {
            
            return;
        
        } else {

			$c = $this->models->Content->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $languageId,
						'subject' => $id
						)
				)
			);
                
           return $c[0]['content'];
        
        }

	}

	private function ajaxActionGetContent()
	{

        if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
            
            return;
        
        } else {

            $this->smarty->assign('returnText', $this->getContent($this->requestData['id'],$this->requestData['language']));

        }

	}

	
	
}