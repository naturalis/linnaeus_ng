<?php

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

class NsrTaxonActorsController extends NsrController
{
    public $usedModels = array(
		'nsr_ids',
		'trash_can',
		'actors_taxa'
    );
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );

    public $modelNameOverride = 'ActorsModel';
    public $controllerPublicName = 'Taxon editor';
	
	private $taxonActors;

    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
	}

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
		$this->setConceptId($this->rGetId());
		$this->setTaxonActors();
	}

    public function actorsAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Taxon experts'));

		$concept=$this->getConcept( $this->getConceptId() );

		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$b = $this->getTaxonActors();
			
			if ($this->saveTaxonActors($this->rGetAll()))
			{
				$this->addMessage( $this->translate('Experts saved.') );
				$this->setTaxonActors();

				$a = $this->getTaxonActors();
				$this->logChange(array('before'=>$b,'after'=>$a,'note'=>sprintf('Updated taxon experts for "%s"',$concept['taxon'])));
			}
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','delete') && $this->rHasVal('actors_taxa_id') && !$this->isFormResubmit())
		{
			$b = $this->getTaxonActors();
			
			$this->deleteTaxonActor($this->rGetVal('actors_taxa_id'));
			$this->addMessage( $this->translate('Expert deleted.') );
			$this->setTaxonActors();

			$a = $this->getTaxonActors();
			$this->logChange(array('before'=>$b,'after'=>$a,'note'=>sprintf('Deleted taxon expert for "%s"',$concept['taxon'])));
		}

		$this->smarty->assign('concept', $concept);
		$this->smarty->assign('actors', $this->getTaxonActors());
		$this->printPage();
	}


    private function setTaxonActors ()
    {
		$id = $this->getConceptId();

		if (empty($id)) {
			return;
		}

		$this->taxonActors =
			$this->models->ActorsModel->getTaxonActors([
				"project_id" => $this->getCurrentProjectId(),
				"taxon_id" => $id,
			]);
    }

    private function getTaxonActors()
    {
		return $this->taxonActors;
	}

	private function saveTaxonActors ($data)
	{
		$taxon_id = $this->getConceptId();

		if (is_null($taxon_id) || empty($data['new_actors'])) {
			return;
		}
			
		$i = 0;

		foreach((array)$data['new_actors'] as $val) {
			$d = $this->models->ActorsModel->saveTaxonActor([
				"project_id" => $this->getCurrentProjectId(),
				"taxon_id" => $taxon_id,
				"actor_id" => $val
			]);
			$i += $d;
		}
		
		return $i > 0;
	}

	private function deleteTaxonActor ($actors_taxa_id)
	{
		$taxon_id = $this->getConceptId();
		
		if (is_null($taxon_id) || is_null($actors_taxa_id) )
			return;
			
		$this->models->ActorsModel->deleteTaxonActor([
			"project_id" => $this->getCurrentProjectId(),
			"taxon_id" => $taxon_id,
		    "actors_taxa_id" => $actors_taxa_id
		]);
	}

}

