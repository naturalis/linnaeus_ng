<?php

include_once ('Controller.php');

class UtilitiesController extends Controller
{
    
    public $usedModels = array(
    );
    
    public $controllerPublicName = 'Utilities';



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
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {
        
        if (!isset($this->requestData['action'])) return;
        
		if ($this->requestData['action'] == 'translate') {
            
			$this->smarty->assign('returnText',$this->javascriptTranslate($this->requestData['text']));

        }
        
        $this->printPage();
    
    }



}






















