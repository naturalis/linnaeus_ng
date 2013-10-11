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

		$this->redirect('../species/index.php?higher=1');

    }

    public function editAction ()
    {

        $this->checkAuthorisation();

//		$_SESSION['admin']['system']['highertaxa'] = true;
		
		$this->redirect('../species/edit.php?higher=1&id='.$this->requestData['id']);

    }

}

