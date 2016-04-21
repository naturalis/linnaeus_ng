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

		$ref=$this->getReference( $this->rGetId() );

		$this->setPageName($ref['label'].', '.$ref['source']);

		$this->smarty->assign( 'ref', $ref );
		$this->smarty->assign( 'taxa', $this->getReferencedTaxa( $this->rGetId() ) );

        $this->printPage();

    }

    public function indexAction()
    {
		$this->smarty->assign( 'authorAlphabet', $this->getAuthorAlphabet() );
		$this->smarty->assign( 'titleAlphabet', $this->getTitleAlphabet() );
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

	private function getReferencedTaxa( $id )
	{
		return $this->models->Literature2Model->getReferencedTaxa(array(
            'project_id' => $this->getCurrentProjectId(),
    		'literature_id' => $id
		));
	}

	private function getTitleAlphabet()
	{
		return $this->models->Literature2Model->getTitleAlphabet( array( 'project_id'=>$this->getCurrentProjectId() ) );
	}

	private function getAuthorAlphabet()
	{
		return $this->models->Literature2Model->getAuthorAlphabet(array( 'project_id'=>$this->getCurrentProjectId() ) );
	}

}

