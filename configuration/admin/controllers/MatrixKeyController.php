<?php

/*

matrix
  object = taxon
  characteristic
    text / long text / picture / movie
    states +
  link object / states

compare object to object
  object 1
  object 2
  distance formula's
    simple dissimilarity coefficient
    russel & rao
    rogers & tanimoto
    hamann
    sokal & sneath
    jaccard
    czekanowski
    kulczyski
    ochiai
  taxonomic distance
  unique states
  states present in both
  states absent in both

examine
  taxon: characters / states

matrix
  characteristic + states: taxa match (complete list with percentage)

STATES HEBBEN HARDE GRENZEN! AUW!



*/

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
