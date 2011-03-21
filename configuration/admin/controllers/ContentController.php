<?php

include_once ('Controller.php');

class ContentController extends Controller
{

    public $usedModels = array(
		'content',
		'free_module_project',
		'free_module_project_user'
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
    
        $this->checkAuthorisation();

        $this->printPage();
    
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

    public function contentAction()
    {
    
		$this->checkAuthorisation();

		$currentSubject =
			isset($_SESSION['system']['content']['current-subject']) ?
			$_SESSION['system']['content']['current-subject'] : 
			'Introduction';

        $this->setPageName(_($currentSubject));

		$this->smarty->assign('isFreeModule', $_SESSION['system']['content']['is-free-module']);

		if (isset($_SESSION['system']['content']['free-module-id']))
			$this->smarty->assign('freeModuleId',$_SESSION['system']['content']['free-module-id']);

		$this->smarty->assign('subject', $currentSubject);

		$this->smarty->assign('languages', $_SESSION['project']['languages']);
		
		$this->smarty->assign('activeLanguage', $_SESSION['project']['default_language_id']);

		$this->smarty->assign('includeHtmlEditor', true);

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


	private function ajaxActionGetContent()
	{

        if (!$this->rHasVal('language') || !$this->rHasVal('id')) {
            
            return;
        
        } else {

			$c = $this->models->Content->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'],
						'subject' => $this->requestData['id']
						)
				)
			);
                
            $this->smarty->assign('returnText', $c[0]['content']);
        
        }

	}


	
	
}