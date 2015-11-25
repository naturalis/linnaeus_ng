<?php


/*

	at some point column literature2.actor_id can be dropped; now replaced with literature2_authors

*/


include_once ('Controller.php');

class Literature2Controller extends Controller
{

    public $usedModels = array(
		'literature2',
		'literature2_authors'
    );
   
    public $controllerPublicName = 'Literary references';

	public $cssToLoad = array(
		'literature.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'lookup.js',
				'dialog/jquery.modaldialog.js'
//				'tablesorter/jquery-latest.js',
//				'tablesorter/jquery.tablesorter.js',
			),
			'IE' => array(
			)
		);

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {
        parent::__construct($p);
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

    public function referenceAction()
    {
		if (!$this->rHasId()) $this->redirect('index.php');

		$ref=$this->getReference($this->rGetId());

		if (!$ref['id']) $this->redirect('index.php');

		$this->setPageName($ref['label'].', '.$ref['source']);

		$this->smarty->assign('ref', $ref);

        $this->printPage();

    }

    public function indexAction()
    {
        $this->printPage();
    }

	private function getReference($id)
	{
		return $this->models->Literature2Model->getReference(array(
			"project_id"=>$this->getCurrentProjectId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"literature2_id"=>$id
		));
	}


}

