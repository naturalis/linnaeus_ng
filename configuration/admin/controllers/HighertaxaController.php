<?php

include_once ('Controller.php');

class HighertaxaController extends Controller
{

    public $controllerPublicName = 'Higher taxa';


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
     * Index of the species module
     *
     * @access    public
     */
    public function indexAction ()
    {

        $this->checkAuthorisation();

		//$_SESSION['system']['highertaxa'] = true;
		
		$this->redirect('../species/index.php?higher=1');
		
		/*
        $this->setPageName(_('Higher taxa overview'));

        $this->printPage();
  		*/
  
    }

    public function editAction ()
    {

        $this->checkAuthorisation();

		$_SESSION['system']['highertaxa'] = true;
		
		$this->redirect('../species/edit.php?id='.$this->requestData['id']);

    }

}

