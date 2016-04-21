<?php

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

class NsrTaxonLiteratureController extends NsrController
{
    public $usedModels = array(
		'nsr_ids',
		'trash_can',
		'literature_taxa'
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

    public $modelNameOverride='Literature2Model';
    public $controllerPublicName = 'Taxon editor';
	
	private $taxonReferences;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
	}

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->setConceptId( $this->rGetId() );
		$this->setTaxonLiterature();
	}

    public function literatureAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Taxon literature'));

		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			if ($this->saveTaxonLiterature( $this->rGetAll() ) )
			{
				$this->addMessage( $this->translate('Literature saved.') );
				$this->setTaxonLiterature();
			}
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','delete') && $this->rHasVal('literature_taxa_id') && !$this->isFormResubmit())
		{
			$this->deleteTaxonLiterature( $this->rGetVal('literature_taxa_id') );
			$this->addMessage( $this->translate('Reference deleted.') );
			$this->setTaxonLiterature();
		}

		$this->smarty->assign('concept',$this->getConcept($this->getConceptId()));
		$this->smarty->assign('literature',$this->getTaxonLiterature());
		$this->printPage();
	}


    private function setTaxonLiterature()
    {
		$id=$this->getConceptId();

		if ( empty($id) )
			return;

		$this->taxonReferences=
			$this->models->Literature2Model->getTaxonReferences(array(
				"project_id"=>$this->getCurrentProjectId(),
				"taxon_id"=>$id,
			));
    }

    private function getTaxonLiterature()
    {
		return $this->taxonReferences;
	}

	private function saveTaxonLiterature( $data )
	{
		$taxon_id=$this->getConceptId();

		if ( is_null($taxon_id) || empty($data['new_refs']) )
			return;
			
		$i=0;

		foreach((array)$data['new_refs'] as $val)
		{
			$d=$this->models->Literature2Model->saveTaxonReference(array(
				"project_id"=>$this->getCurrentProjectId(),
				"taxon_id"=>$taxon_id,
				"literature_id"=>$val
			));
			$i+=$d;
		}
		
		return $i>0;
	}

	private function deleteTaxonLiterature( $literature_taxa_id )
	{
		$taxon_id=$this->getConceptId();

		if ( is_null($taxon_id) || is_null($literature_taxa_id) )
			return;
			
		$d=$this->models->Literature2Model->deleteTaxonReference(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$taxon_id,
			"literature_taxa_id"=>$literature_taxa_id
		));
	}

}

