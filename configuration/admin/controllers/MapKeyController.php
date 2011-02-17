<?php

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
	);
    
    public $usedHelpers = array();

    public $controllerPublicName = 'Map key';

	public $cssToLoad = array();

	public $jsToLoad = array('all' => array());


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

    public function runMoasAction()
    {

		//$app = 'C:\Users\maarten\htdocs\linnaeus ng\moas\Windows\server.exe';
		//echo file_exists($app) ? 'y' : 'n';
		
		//@system($app);

        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

       $this->printPage();

    }

}