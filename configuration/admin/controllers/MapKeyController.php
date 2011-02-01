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

        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));

        $this->printPage();

    }

}