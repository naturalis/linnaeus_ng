<?php

include_once ('Controller.php');

class MatrixKeyController extends Controller
{
    
    public $usedModels = array();
    
    public $usedHelpers = array();

    public $controllerPublicName = 'Matrix key';

	public $cssToLoad = array();

	public $jsToLoad = array('all' => array('matrix.js'));


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

    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

        $this->printPage();
    
    }

    public function matrixAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Matrix'));

        $this->printPage();
    
    }

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'page_char_add') {

			$this->pageCharAdd();

        }

        $this->printPage();
    
    }

	private function pageCharAdd()
	{

		$this->smarty->assign('languages',$_SESSION['project']['languages']);

		$this->smarty->assign('returnText',$this->fetchPage('_char_add'));
		
	}


}
