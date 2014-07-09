<?php

include_once ('SpeciesController.php');

class TreeController extends Controller
{
    public $usedModels = array(
		'name_types'
    );
	
	private $_idPreferredName=0;
	private $_idValidName=0;
		
    public function __construct()
    {
        parent::__construct();
		$this->initialise();
	}
	
	private function initialise()
	{
		$d=$this->models->NameTypes->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'nametype' => PREDICATE_PREFERRED_NAME,
				'language_id' => $this->getCurrentLanguageId()
			)
		));
		
		if ($d) $this->_idPreferredName=$d[0]['id'];
		
		$d=$this->models->NameTypes->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'nametype' => PREDICATE_VALID_NAME,
				'language_id' => LANGUAGE_ID_SCIENTIFIC
			)
		));
		
		if ($d) $this->_idValidName=$d[0]['id'];

	}
					
    public function __destruct()
    {
        parent::__destruct();
    }

    public function indexAction()
    {
        $this->redirect('tree.php');
    }

	public function treeAction()
	{

	}
	
    public function ajaxInterfaceAction ()
    {
		$return='error';
        
    }
	


}
