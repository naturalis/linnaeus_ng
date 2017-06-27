<?php

include_once ('NsrController.php');

class NsrTaxonDeleteController extends NsrController
{
    public $usedModels = [
		'taxa_variations',
		'matrices_variations',
		'nbc_extras',
		'variation_relations',
		'variations_labels',
		'taxa_variations',
		'taxa_relations',
		'commonnames',
		'synonyms',
		'content_taxa',
		'dna_barcodes',
		'external_ids',
		'keysteps_taxa',
		'l2_occurrences_taxa',
		'l2_occurrences_taxa_combi',
		'literature_taxa',
		'matrices_taxa',
		'matrices_taxa_states',
		'traits_taxon_freevalues',
		'traits_taxon_references',
		'traits_taxon_values',
		'taxongroups_taxa',
		'taxon_quick_parentage',
		'taxon_trend_years',
		'taxon_trends',
		'occurrences_taxa',
		'presence_taxa',
		'taxa_relations',
		'users_taxa',
		'names',
		'names_additions',
		'nsr_ids',
		'rdf',
		'user_item_access',
		'trash_can',
		'choices_keysteps',
		'media_taxon'
	];

    public $usedHelpers=[];
    public $cssToLoad=[];
    public $jsToLoad=[];

	private $taxon;
	private $deletedNsrMedia;
	

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
		$this->taxon=$this->getTaxonById( $this->getConceptId() );
	}

    public function irrevocablyDeleteAction()
    {
		if ( is_null($this->getConceptId()) ) $this->redirect( "taxon_deleted.php" );
//		if ( $this->isFormResubmit() ) $this->redirect( "taxon.php?id=" . $this->_conceptId );

		$this->UserRights->setItemId( $this->rGetId() );
		$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
		$this->checkAuthorisation();

		$this->doOtherStuff();
		$this->doProcessVariations();
		$this->doDeleteTaxonAndRelated();

		$this->smarty->assign('concept',$this->taxon);
		$this->smarty->assign('deletedNsrMedia',$this->deletedNsrMedia);
		
		$this->printPage();
		
	}
	
	private function doProcessVariations()
	{
		$base=[ 'project_id'=>$this->getCurrentProjectId() ];

		$variations=$this->models->TaxaVariations->_get( [ 'id' => ( $base + [ 'taxon_id'=>$this->getConceptId() ] ) ] );

		foreach((array)$variations as $val)
		{
			$this->models->MatricesTaxaStates->resetAffectedRows();
			$before=$this->models->MatricesTaxaStates->_get( [ 'id' => ( $base + [ 'variation_id'=>$val['id'] ] ) ] );
			$this->models->MatricesTaxaStates->delete( $base + [ 'variation_id'=>$val['id'] ] );
			if ($this->models->MatricesTaxaStates->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted matrix variations states for '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted matrix variations states.'));
			}

			$this->models->MatricesVariations->resetAffectedRows();
			$before=$this->models->MatricesVariations->_get( [ 'id' => ( $base + [ 'variation_id'=>$val['id'] ] ) ] );
			$this->models->MatricesVariations->delete( $base + [ 'variation_id'=>$val['id'] ] );
			if ($this->models->MatricesVariations->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted matrix entries for variations for '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted matrix entries for variations.'));
			}

			$this->models->NbcExtras->resetAffectedRows();
			$before=$this->models->NbcExtras->_get( [ 'id' => ( $base + [ 'ref_id'=>$val['id'], 'ref_type'=>'variation' ] ) ] );
			$this->models->NbcExtras->delete( $base + [ 'ref_id'=>$val['id'], 'ref_type'=>'variation' ] );
			if ($this->models->NbcExtras->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted matrix extra data for variations for '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted matrix extra data for variations.'));
			}

			$this->models->VariationRelations->resetAffectedRows();
			$before=$this->models->VariationRelations->_get( [ 'id' => ( $base + [ 'relation_id'=>$val['id'], 'ref_type'=>'variation'  ] ) ] );
			$this->models->VariationRelations->delete( $base + [ 'relation_id'=>$val['id'], 'ref_type'=>'variation'  ] );
			if ($this->models->VariationRelations->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted references of related variations for variations of '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted references of related variations for variations.'));
			}

			$this->models->VariationRelations->resetAffectedRows();
			$before=$this->models->VariationRelations->_get( [ 'id' => ( $base + [ 'variation_id'=>$val['id'] ] ) ] );
			$this->models->VariationRelations->delete( $base + [ 'variation_id'=>$val['id'] ] );
			if ($this->models->VariationRelations->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted related variations for '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted related variations.'));
			}

			$this->models->VariationLabels->resetAffectedRows();
			$before=$this->models->VariationLabels->_get( [ 'id' => ( $base + [ 'variation_id'=>$val['id'] ] ) ] );
			$this->models->VariationLabels->delete( $base + [ 'variation_id'=>$val['id'] ] );
			if ($this->models->VariationLabels->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted variation labels for '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted variation labels.'));
			}

			$this->models->TaxaRelations->resetAffectedRows();
			$before=$this->models->TaxaRelations->_get( [ 'id' => ( $base + [ 'relation_id'=>$val['id'], 'ref_type'=>'variation' ] ) ] );
			$this->models->TaxaRelations->delete( $base + [ 'relation_id'=>$val['id'], 'ref_type'=>'variation' ] );
			if ($this->models->TaxaRelations->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted references of related variations of '.$this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted references of related variations.'));
			}
		}

		$this->models->VariationRelations->resetAffectedRows();
		$before=$this->models->VariationRelations->_get( [ 'id' => ( $base + [ 'relation_id'=>$this->getConceptId(), 'ref_type'=>'taxon' ] ) ] );
		$this->models->VariationRelations->delete( $base + [ 'relation_id'=>$this->getConceptId(), 'ref_type'=>'taxon'  ] );
		if ($this->models->VariationRelations->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted references of related taxa for variations of '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted references of related taxa for variations.'));
		}

		$this->models->TaxaVariations->resetAffectedRows();
		$this->models->TaxaVariations->delete( $base + [ 'taxon_id'=>$this->getConceptId() ] );
		if ($this->models->TaxaVariations->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$variations,'note'=>'deleted taxon variations for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted taxon variations.'));
		}
	}	
	
	private function doOtherStuff()
	{
		$this->models->ChoicesKeysteps->resetAffectedRows();
		$before=$this->models->ChoicesKeysteps->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'res_taxon_id'=>$this->getConceptId() ] ] );
		$this->models->ChoicesKeysteps->update(
			 [ 'res_taxon_id'=>'null' ],
			 [ 'project_id'=>$this->getCurrentProjectId(), 'res_taxon_id'=>$this->getConceptId() ]
		  );
		if ($this->models->ChoicesKeysteps->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'removed keystep endpoints for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Removed keystep endpoints.'));
		}

		$this->models->MediaTaxon->resetAffectedRows();
		$before=$this->models->MediaTaxon->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'taxon_id'=>$this->getConceptId() ] ] );
		$this->models->MediaTaxon->delete( [ 'project_id'=>$this->getCurrentProjectId(), 'taxon_id'=>$this->getConceptId() ] );
		if ($this->models->MediaTaxon->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'removed media references for '.$this->taxon['taxon'] . ' (but no actual images)'));
			$this->addMessage($this->translate('Removed media references (but no actual images).'));
			$this->deletedNsrMedia=$before;
		}
	}

	private function doDeleteTaxonAndRelated()
	{

		$base=[ 'project_id'=>$this->getCurrentProjectId(), 'taxon_id'=>$this->getConceptId() ];

		$this->models->Commonnames->resetAffectedRows();
		$before=$this->models->Commonnames->_get( [ 'id' => $base ] );
		$this->models->Commonnames->delete( $base );
		if ($this->models->Commonnames->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted (old) common names for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted (old) common names.'));
		}

		$this->models->Synonyms->resetAffectedRows();
		$before=$this->models->Synonyms->_get( [ 'id' => $base ] );
		$this->models->Synonyms->delete( $base );
		if ($this->models->Synonyms->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted (old) synonyms for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted (old) synonyms.'));
		}

		$this->models->ContentTaxa->resetAffectedRows();
		$before=$this->models->ContentTaxa->_get( [ 'id' => $base ] );
		$this->models->ContentTaxa->delete( $base );
		if ($this->models->ContentTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted content pages for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted content pages.'));
		}

		$this->models->DnaBarcodes->resetAffectedRows();
		$before=$this->models->DnaBarcodes->_get( [ 'id' => $base ] );
		$this->models->DnaBarcodes->delete( $base );
		if ($this->models->DnaBarcodes->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted dna barcodes for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted DNA barcodes.'));
		}

		$this->models->ExternalIds->resetAffectedRows();
		$before=$this->models->ExternalIds->_get( [ 'id' => $base ] );
		$this->models->ExternalIds->delete( $base );
		if ($this->models->ExternalIds->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted external id\'s for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted external ID\'s.'));
		}

		$this->models->KeystepsTaxa->resetAffectedRows();
		$before=$this->models->KeystepsTaxa->_get( [ 'id' => $base ] );
		$this->models->KeystepsTaxa->delete( $base );
		if ($this->models->KeystepsTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted keystep-taxon references for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted keystep-taxon references.'));
		}

		$this->models->L2OccurrencesTaxa->resetAffectedRows();
		$before=$this->models->L2OccurrencesTaxa->_get( [ 'id' => $base ] );
		$this->models->L2OccurrencesTaxa->delete( $base );
		if ($this->models->L2OccurrencesTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted map occurrences (L2) for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted L2 map occurrences.'));
		}

		$this->models->L2OccurrencesTaxaCombi->resetAffectedRows();
		$before=$this->models->L2OccurrencesTaxaCombi->_get( [ 'id' => $base ] );
		$this->models->L2OccurrencesTaxaCombi->delete( $base );
		if ($this->models->L2OccurrencesTaxaCombi->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted combined map occurrences (L2) for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted L2 map occurrences.'));
		}

		$this->models->LiteratureTaxa->resetAffectedRows();
		$before=$this->models->LiteratureTaxa->_get( [ 'id' => $base ] );
		$this->models->LiteratureTaxa->delete( $base );
		if ($this->models->LiteratureTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted literature references for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted literature references.'));
		}

		$this->models->MatricesTaxa->resetAffectedRows();
		$before=$this->models->MatricesTaxa->_get( [ 'id' => $base ] );
		$this->models->MatricesTaxa->delete( $base );
		if ($this->models->MatricesTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted matrix references for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted matrix references.'));
		}

		$this->models->MatricesTaxaStates->resetAffectedRows();
		$before=$this->models->MatricesTaxaStates->_get( [ 'id' => $base ] );
		$this->models->MatricesTaxaStates->delete( $base );
		if ($this->models->MatricesTaxaStates->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted matrix states for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted matrix states.'));
		}

		$this->models->TraitsTaxonFreevalues->resetAffectedRows();
		$before=$this->models->TraitsTaxonFreevalues->_get( [ 'id' => $base ] );
		$this->models->TraitsTaxonFreevalues->delete( $base );
		if ($this->models->TraitsTaxonFreevalues->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted traits free values for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted traits free values.'));
		}

		$this->models->TraitsTaxonReferences->resetAffectedRows();
		$before=$this->models->TraitsTaxonReferences->_get( [ 'id' => $base ] );
		$this->models->TraitsTaxonReferences->delete( $base );
		if ($this->models->TraitsTaxonReferences->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted traits references for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted traits references.'));
		}

		$this->models->TraitsTaxonValues->resetAffectedRows();
		$before=$this->models->TraitsTaxonValues->_get( [ 'id' => $base ] );
		$this->models->TraitsTaxonValues->delete( $base );
		if ($this->models->TraitsTaxonValues->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted traits values for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted traits values.'));
		}

		$this->models->TaxongroupsTaxa->resetAffectedRows();
		$before=$this->models->TaxongroupsTaxa->_get( [ 'id' => $base ] );
		$this->models->TaxongroupsTaxa->delete( $base );
		if ($this->models->TaxongroupsTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted taxongroups memberships for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted taxongroups memberships.'));
		}

		$this->models->TaxonQuickParentage->resetAffectedRows();
		$before=$this->models->TaxonQuickParentage->_get( [ 'id' => $base ] );
		$this->models->TaxonQuickParentage->delete( $base );
		if ($this->models->TaxonQuickParentage->getAffectedRows()!==0)
		{
//			$this->logChange(array('before'=>$before,'note'=>'deleted quick parentage for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted quick parentage.'));
		}

		$this->models->TaxonTrendYears->resetAffectedRows();
		$before=$this->models->TaxonTrendYears->_get( [ 'id' => $base ] );
		$this->models->TaxonTrendYears->delete( $base );
		if ($this->models->TaxonTrendYears->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted trend years for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted trend years.'));
		}

		$this->models->TaxonTrends->resetAffectedRows();
		$before=$this->models->TaxonTrends->_get( [ 'id' => $base ] );
		$this->models->TaxonTrends->delete( $base );
		if ($this->models->TaxonTrends->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted trends for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted trends.'));
		}

		$this->models->OccurrencesTaxa->resetAffectedRows();
		$before=$this->models->OccurrencesTaxa->_get( [ 'id' => $base ] );
		$this->models->OccurrencesTaxa->delete( $base );
		if ($this->models->OccurrencesTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted map occurrences for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted map occurrences.'));
		}

		$this->models->PresenceTaxa->resetAffectedRows();
		$before=$this->models->PresenceTaxa->_get( [ 'id' => $base ] );		
		$this->models->PresenceTaxa->delete( $base );
		if ($this->models->PresenceTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted presence specification for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted presence specification.'));
		}

		$this->models->TaxaRelations->resetAffectedRows();
		$before=$this->models->TaxaRelations->_get( [ 'id' => $base ] );
		$this->models->TaxaRelations->delete( $base );
		if ($this->models->TaxaRelations->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted relations for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted relations.'));
		}

		$this->models->UsersTaxa->resetAffectedRows();
		$before=$this->models->UsersTaxa->_get( [ 'id' => $base ] );
		$this->models->UsersTaxa->delete( $base );
		if ($this->models->UsersTaxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted user reference (old) for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted user reference (old).'));
		}

		$names=$this->models->Names->_get( [ 'id' => $base ] );

		foreach((array)$names as $val)
		{
			$this->models->NamesAdditions->resetAffectedRows();
			$before=$this->models->NamesAdditions->_get( [ 'id' => ( $base + [ "name_id" => $val["id"] ] ) ] );
			$this->models->NamesAdditions->delete( $base + [ "name_id" => $val["id"] ] );
			if ($this->models->NamesAdditions->getAffectedRows()!==0)
			{
				$this->logChange(array('before'=>$before,'note'=>'deleted name addition for name '.$val['name'] . ' of ' . $this->taxon['taxon']));
				$this->addMessage($this->translate('Deleted name addition for '. $val['name'] .'.'));
			}
		
		}

		$this->models->Names->resetAffectedRows();
		$this->models->Names->delete( $base );
		if ($this->models->Names->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$names,'note'=>'deleted names of ' . $this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted names.'));
		}

		$this->models->NbcExtras->resetAffectedRows();
		$before=$this->models->NbcExtras->_get( [ 'id' => ( $base + [ 'ref_id'=>$this->getConceptId(), 'ref_type'=>'taxon' ] ) ] );
		$this->models->NbcExtras->delete( $base + [ 'ref_id'=>$this->getConceptId(), 'ref_type'=>'taxon' ] );
		if ($this->models->NbcExtras->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted matrix extra data for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted matrix extra data.'));
		}

		$this->models->Rdf->resetAffectedRows();
		$before=$this->models->Rdf->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'object_id'=>$this->getConceptId(), 'object_type'=>'taxon' ] ] );
		$this->models->Rdf->delete( [ 'project_id'=>$this->getCurrentProjectId(), 'object_id'=>$this->getConceptId(), 'object_type'=>'taxon' ] );
		if ($this->models->Rdf->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted rdf data for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted RDF data.'));
		}

		$this->models->UserItemAccess->resetAffectedRows();
		$before=$this->models->UserItemAccess->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'item_id'=>$this->getConceptId(), 'item_type'=>'taxon' ] ] );
		$this->models->UserItemAccess->delete( [ 'project_id'=>$this->getCurrentProjectId(), 'item_id'=>$this->getConceptId(), 'item_type'=>'taxon' ] );
		if ($this->models->UserItemAccess->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted specific user access for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted specific user access.'));
		}

		$this->models->TrashCan->resetAffectedRows();
		$before=$this->models->TrashCan->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'lng_id'=>$this->getConceptId(), 'item_type'=>'taxon' ] ] );
		$this->models->TrashCan->delete( [ 'project_id'=>$this->getCurrentProjectId(), 'lng_id'=>$this->getConceptId(), 'item_type'=>'taxon' ] );
		if ($this->models->TrashCan->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted specific user access for '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted specific user access.'));
		}

		$this->models->Taxa->resetAffectedRows();
		$before=$this->models->Taxa->_get( [ 'id' => [ 'project_id'=>$this->getCurrentProjectId(), 'id'=>$this->getConceptId() ] ] );
		$this->models->Taxa->delete( [ 'project_id'=>$this->getCurrentProjectId(), 'id'=>$this->getConceptId() ] );
		if ($this->models->Taxa->getAffectedRows()!==0)
		{
			$this->logChange(array('before'=>$before,'note'=>'deleted taxon '.$this->taxon['taxon']));
			$this->addMessage($this->translate('Deleted taxon.'));
		}

	}

}
