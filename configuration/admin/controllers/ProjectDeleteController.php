<?php 
/**
 * Controller used for deleting before a new import.
 */

/*

	deleting of language should delete glossary terms

*/

include_once ('Controller.php');
class ProjectDeleteController extends Controller
{
    public $usedModels = array(
        'activity_log',
        'actors',
        'actors_addresses',
        'beelduitwisselaar_batches',
        'characteristics',
        'characteristics_chargroups',
        'characteristics_labels',
        'characteristics_labels_states',
        'characteristics_matrices',
        'characteristics_states',
        'chargroups',
        'chargroups_labels',
        'choices_content_keysteps',
        'choices_keysteps',
        'commonnames',
        'content',
        'content_free_modules',
        'content_introduction',
        'content_keysteps',
        'content_taxa',
        'diversity_index',
        'dna_barcodes',
        'external_ids',
        'external_orgs',
        'free_modules_pages',
        'free_modules_projects',
        'free_module_media',
        'geodata_types',
        'geodata_types_titles',
        'glossary',
        'glossary_media',
        'glossary_media_captions',
        'glossary_synonyms',
        'gui_menu_order',
        'habitats',
        'habitat_labels',
        'hotwords',
        'introduction_media',
        'introduction_pages',
        'keysteps',
        'keysteps_taxa',
        'keytrees',
        'l2_diversity_index',
        'l2_maps',
        'l2_occurrences_taxa',
        'l2_occurrences_taxa_combi',
        'labels_languages',
        'labels_projects_ranks',
        'labels_sections',
        'languages_projects',
        'literature',
        'literature2',
        'literature2_authors',
        'literature2_publication_types',
        'literature2_publication_types_labels',
        'literature_taxa',
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'matrices_variations',
        'media',
        'media_captions',
        'media_conversion_log',
        'media_descriptions_taxon',
        'media_meta',
        'media_metadata',
        'media_modules',
        'media_tags',
        'media_taxon',
        'modules_projects',
        'module_settings_values',
        'names',
        'names_additions',
        'name_types',
        'nbc_extras',
        'nsr_ids',
        'occurrences_taxa',
        'pages_taxa',
        'pages_taxa_titles',
        'presence',
        'presence_labels',
        'presence_taxa',
        'projects_ranks',
        'projects_roles_users',
        'rdf',
        'sections',
        'settings',
        'synonyms',
        'tab_order',
        'taxa',
        'taxa_relations',
        'taxa_variations',
        'taxongroups',
        'taxongroups_labels',
        'taxongroups_taxa',
        'taxon_quick_parentage',
        'taxon_trends',
        'taxon_trend_years',
        'text_translations',
        'traits_groups',
        'traits_project_types',
        'traits_settings',
        'traits_taxon_freevalues',
        'traits_taxon_references',
        'traits_taxon_values',
        'traits_traits',
        'traits_values',
        'trash_can',
        'trend_sources',
        'users_taxa',
        'user_item_access',
        'user_module_access',
        'variations_labels',
        'variation_relations'
    );

    /**
     * ProjectDeleteController constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
    }

    /**
     * Initialize with the models.
     */
    private function initialize ()
    {
        // nothing
    }

    /**
     * Destructor
     */
    public function __destruct ()
    {
        parent::__destruct();
    }

    /**
     * Delete the whole project
     *
     * @param $projectId
     */
    public function doDeleteProjectAction ($projectId)
    {
		set_time_limit(600);
		$this->doDeleteAllButProjectItself($projectId);
		$this->doDeleteProjectItself($projectId);
	}

    /**
     * First delete all, except the main project
     *
     * @param $projectId
     */
    public function doDeleteAllButProjectItself ($projectId)
    {
        $this->deleteNBCKeydata($projectId);
        $this->deleteIntroduction($projectId);
        $this->deleteGeoData($projectId);
        $this->deleteMatrices( [ 'project_id'=>$projectId ] );
        $this->deleteDichotomousKey($projectId);
        $this->deleteGlossary($projectId);
        $this->deleteLiterature($projectId);
        $this->deleteLiterature2($projectId);
        $this->deleteActors($projectId);
        $this->deleteProjectContent($projectId);
        $this->deleteCommonnames($projectId);
        $this->deleteSynonyms($projectId);
        $this->deleteTaxa($projectId);
        $this->deleteStandardCat($projectId);
        $this->deleteNames($projectId);
        $this->deleteMedia($projectId);
        $this->deleteFreeModules($projectId);
        $this->deleteProjectRanks($projectId);
        $this->deleteModulesFromProject($projectId);
        $this->deleteNsrSpecificStuff($projectId);
        $this->deleteOtherStuff($projectId);
    }

    /**
     * Also delete the main project
     *
     * @param $projectId
     */
    public function doDeleteProjectItself ($projectId)
    {
        $this->deleteProjectUsers($projectId);
        $this->deleteProjectLanguage($projectId);
        $this->deleteProjectCssFile($projectId);
        $this->deleteProjectSettings($projectId);
        $this->deleteProjectDirectories($projectId);
        $this->deleteProject($projectId);
    }



    public function deleteIntroduction ($id)
    {
        $this->models->ContentIntroduction->delete(array(
            'project_id' => $id
        ));
        $this->models->IntroductionPages->delete(array(
            'project_id' => $id
        ));
        $this->models->IntroductionMedia->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteActors ($id)
    {
        $this->models->Actors->delete(array(
            'project_id' => $id
        ));
        $this->models->ActorsAddresses->delete(array(
            'project_id' => $id
        ));
        $this->models->Literature2Authors->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteMedia ($id)
    {
        $this->models->Media->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaCaptions->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaConversionLog->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaMetadata->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaModules->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaTags->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteGeoData ($id)
    {
        $this->models->OccurrencesTaxa->delete(array(
            'project_id' => $id
        ));
        $this->models->GeodataTypesTitles->delete(array(
            'project_id' => $id
        ));
        $this->models->GeodataTypes->delete(array(
            'project_id' => $id
        ));
        $this->models->DiversityIndex->delete(array(
            'project_id' => $id
        ));

        $this->models->L2Maps->delete(array(
            'project_id' => $id
        ));
        $this->models->L2OccurrencesTaxa->delete(array(
            'project_id' => $id
        ));
        $this->models->L2OccurrencesTaxaCombi->delete(array(
            'project_id' => $id
        ));
        $this->models->L2DiversityIndex->delete(array(
            'project_id' => $id
        ));

    }


    public function deleteMatrices( $p )
    {
		$id=isset($p['project_id']) ? $p['project_id'] : null;
		$keep_files=isset($p['keep_files']) ? $p['keep_files'] : false;

		if ( is_null($id) ) return;

		$d=['project_id' => $id];

        $this->models->GuiMenuOrder->delete($d);
        $this->models->MatricesVariations->delete($d);
        $this->models->MatricesTaxaStates->delete($d);
        $this->models->MatricesTaxa->delete($d);
        $this->models->CharacteristicsLabelsStates->delete($d);

		if (!$keep_files)
		{
			$cs = $this->models->CharacteristicsStates->_get( [ 'id' => $d ] );

			foreach ((array)$cs as $key => $val)
			{
				if (isset($val['file_name']))
				{
					@unlink($_SESSION['admin']['project']['paths']['project_media'] . $val['file_name']);
				}
			}
		}

        $this->models->CharacteristicsStates->delete($d);
        $this->models->CharacteristicsMatrices->delete($d);
        $this->models->CharacteristicsLabels->delete($d);
        $this->models->Characteristics->delete($d);

        $this->models->ChargroupsLabels->delete($d);
        $this->models->Chargroups->delete($d);
        $this->models->CharacteristicsChargroups->delete($d);

        $this->models->MatricesNames->delete($d);
        $this->models->Matrices->delete($d);

    }

    public function deleteDichotomousKey ($id)
    {
        $this->models->ChoicesContentKeysteps->delete(array(
            'project_id' => $id
        ));
        $this->models->ChoicesKeysteps->delete(array(
            'project_id' => $id
        ));
        $this->models->ContentKeysteps->delete(array(
            'project_id' => $id
        ));
        $this->models->Keysteps->delete(array(
            'project_id' => $id
        ));
        $this->models->KeystepsTaxa->delete(array(
            'project_id' => $id
        ));
        $this->models->Keytrees->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteGlossary ($id, $keepFiles=false)
    {
        $paths = $this->makePathNames($id);

		if (!$keepFiles) {

			$mt = $this->models->GlossaryMedia->_get(array(
				'id' => array(
					'project_id' => $id
				)
			));

			foreach ((array) $mt as $val) {

				if (isset($val['file_name']))
					@unlink($paths['project_media'] . $val['file_name']);
				if (isset($val['thumb_name']))
					@unlink($paths['project_thumbs'] . $val['thumb_name']);
			}

		}

        $this->models->GlossaryMedia->delete(array(
            'project_id' => $id
        ));

        $this->models->GlossaryMediaCaptions->delete(array(
            'project_id' => $id
        ));

        $this->models->GlossarySynonyms->delete(array(
            'project_id' => $id
        ));
        $this->models->Glossary->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteLiterature ($id)
    {
        $this->models->LiteratureTaxa->delete(array(
            'project_id' => $id
        ));
        $this->models->Literature->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteLiterature2 ($id)
    {
        $this->models->Literature2->delete(array(
            'project_id' => $id
        ));
        $this->models->Literature2Authors->delete(array(
            'project_id' => $id
        ));
        $this->models->Literature2PublicationTypes->delete(array(
            'project_id' => $id
        ));
       $this->models->Literature2PublicationTypesLabels->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteProjectContent ($id)
    {
        $this->models->Content->delete(array(
            'project_id' => $id
        ));
    }



    private function deleteTaxonMedia ($id,$keepFiles=false)
    {
        $paths = $this->makePathNames($id);

		if (!$keepFiles) {

			$mt = $this->models->MediaTaxon->_get(array(
				'id' => array(
					'project_id' => $id
				)
			));

			foreach ((array) $mt as $val) {

				if (isset($val['file_name']))
					@unlink($paths['project_media'] . $val['file_name']);
				if (isset($val['thumb_name']))
					@unlink($paths['project_thumbs'] . $val['thumb_name']);
			}

		}

        $this->models->MediaTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaDescriptionsTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaMeta->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteCommonnames ($id)
    {
        $this->models->Commonnames->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteSynonyms ($id)
    {
        $this->models->Synonyms->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteStandardCat ($id)
    {
        $this->models->PagesTaxaTitles->delete(array(
            'project_id' => $id
        ));
        $this->models->PagesTaxa->delete(array(
            'project_id' => $id
        ));
    }

    private function deleteTaxonContent ($id,$deleteGeneralData=true)
    {
        $this->models->ContentTaxa->delete(array(
            'project_id' => $id
        ));

		if ($deleteGeneralData) {
			$this->models->Sections->delete(array(
				'project_id' => $id
			));
			$this->models->LabelsSections->delete(array(
				'project_id' => $id
			));
		}
    }

    public function deleteTaxa ($id, $keepFiles=false)
    {
        $this->models->Taxa->delete(array(
            'project_id' => $id
        ));

        $this->deleteTaxonContent($id);
        $this->deleteTaxonMedia($id, $keepFiles);

        $this->models->TaxonQuickParentage->delete(array(
            'project_id' => $id
        ));

        $this->deleteTaxonGroups($id);
        $this->deleteTaxonTrends($id);
        $this->deletePresence($id);
        $this->deleteTraits($id);
    }

    public function deleteTraits ($id)
    {
        $this->models->TraitsGroups->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsProjectTypes->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsSettings->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsTaxonFreevalues->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsTaxonReferences->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsTaxonValues->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsTraits->delete(array(
            'project_id' => $id
        ));
        $this->models->TraitsValues->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteTaxonTrends ($id)
    {
        $this->models->TaxonTrends->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxonTrendYears->delete(array(
            'project_id' => $id
        ));
        $this->models->TrendSources->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteTaxonGroups ($id)
    {
        $this->models->Taxongroups->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxongroupsLabels->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxongroupsTaxa->delete(array(
            'project_id' => $id
        ));
    }

    public function deletePresence ($id)
    {
        $this->models->Presence->delete(array(
            'project_id' => $id
        ));
        $this->models->PresenceLabels->delete(array(
            'project_id' => $id
        ));
        $this->models->PresenceTaxa->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteNames ($id)
    {
        $this->models->Names->delete(array(
            'project_id' => $id
        ));
        $this->models->NamesAdditions->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteProjectRanks ($id)
    {
        $this->models->LabelsProjectsRanks->delete(array(
            'project_id' => $id
        ));
        $this->models->ProjectsRanks->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteProjectUsers ($id)
    {
        $this->models->UsersTaxa->delete(array(
            'project_id' => $id
        ));
        $this->models->ProjectsRolesUsers->delete(array(
            'project_id' => $id
        ));
        $this->models->UserItemAccess->delete(array(
            'project_id' => $id
        ));
        $this->models->UserModuleAccess->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteProjectLanguage ($id)
    {
        $this->models->LanguagesProjects->delete(array(
            'project_id' => $id
        ));
        $this->models->LabelsLanguages->delete(array(
            'project_id' => $id
        ));
        $this->models->TextTranslations->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteFreeModuleMedia ($projectId, $pageId, $paths, $keepFiles=false)
    {
        if (empty($projectId) || empty($pageId))
            return;

		if (!$keepFiles) {

			$fmm = $this->models->FreeModuleMedia->_get(array(
				'id' => array(
					'project_id' => $projectId,
					'page_id' => $pageId
				)
			));

			if (file_exists($paths['project_media'] . $fmm[0]['file_name'])) {

				if (@unlink($paths['project_media'] . $fmm[0]['file_name'])) {

					if ($fmm[0]['thumb_name'] && file_exists($paths['project_thumbs'] . $fmm[0]['thumb_name'])) {

						@unlink($paths['project_thumbs'] . $fmm[0]['thumb_name']);
					}
				}
			}

		}

        $this->models->FreeModuleMedia->delete(array(
            'project_id' => $projectId,
            'page_id' => $pageId
        ));
    }



    public function deleteFreeModules ($id, $moduleId = null)
    {
        if ($id == null)
            return;

        $d['project_id'] = $id;

        if (isset($moduleId))
            $d['module_id'] = $moduleId;

        $this->models->ContentFreeModules->delete($d);

        $fmp = $this->models->FreeModulesPages->_get(array(
            'id' => $d
        ));


        $paths = $this->makePathNames($id);

        foreach ((array) $fmp as $key => $val) {

            $this->deleteFreeModuleMedia($id, $val['id'], $paths);
        }

        $this->models->FreeModulesPages->delete($d);
        $this->models->FreeModuleMedia->delete($d);

        unset($d);

        /*
        $d['project_id'] = $id;

        if (isset($moduleId))
            $d['free_module_id'] = $moduleId;

        $this->models->FreeModulesProjectsUsers->delete($d);

        unset($d);
        */

        $d['project_id'] = $id;

        if (isset($moduleId))
            $d['id'] = $moduleId;

        $this->models->FreeModulesProjects->delete($d);
    }



    public function deleteModulesFromProject ($id)
    {
        $this->models->ModulesProjects->delete(array(
            'project_id' => $id
        ));
    }


	public function rrmdir($dir)
	{
		if (!file_exists($dir)) return;
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				$this->rrmdir($file);
			else
				@unlink($file);
		}

		if (!@rmdir($dir)) return;
	}

    public function deleteProjectDirectories ($id)
    {
        $paths = $this->makePathNames($id);

        $this->rrmdir($paths['project_media_l2_maps']);
        $this->rrmdir($paths['project_thumbs']);
        $this->rrmdir($paths['project_media']);
    }


    public function deleteProject ($id)
    {
        $p = $this->models->Projects->delete(array(
            'id' => $id
        ));
    }


    public function deleteProjectCssFile ($id)
    {
        $p = $this->models->Projects->_get(array(
            'id' => $id
        ));

        @unlink($this->makeCustomCssFileName($id, $p['title']));
    }


    public function deleteProjectSettings ($id)
    {
        $this->models->ModuleSettingsValues->delete(array(
            'project_id' => $id
        ));
        $this->models->Settings->delete(array(
            'project_id' => $id
        ));
        $this->models->TabOrder->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteNsrSpecificStuff ($id)
    {
        $this->models->BeelduitwisselaarBatches->delete(array(
            'project_id' => $id
        ));
        $this->models->DnaBarcodes->delete(array(
            'project_id' => $id
        ));
        $this->models->ExternalIds->delete(array(
            'project_id' => $id
        ));
        $this->models->ExternalOrgs->delete(array(
            'project_id' => $id
        ));
        $this->models->Habitats->delete(array(
            'project_id' => $id
        ));
        $this->models->HabitatLabels->delete(array(
            'project_id' => $id
        ));
        $this->models->NsrIds->delete(array(
            'project_id' => $id
        ));
        $this->models->Rdf->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteOtherStuff ($id)
    {
        $this->models->Hotwords->delete(array(
            'project_id' => $id
        ));
        $this->models->ActivityLog->delete(array(
            'project_id' => $id
        ));
        $this->models->ModuleSettingsValues->delete(array(
            'project_id' => $id
        ));
        $this->models->TrashCan->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteNBCKeydata ($id)
    {
        $this->models->ChargroupsLabels->delete(array(
            'project_id' => $id
        ));
        $this->models->Chargroups->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicsChargroups->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxaVariations->delete(array(
            'project_id' => $id
        ));
        $this->models->VariationsLabels->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxaRelations->delete(array(
            'project_id' => $id
        ));
        $this->models->VariationRelations->delete(array(
            'project_id' => $id
        ));
        $this->models->MatricesVariations->delete(array(
            'project_id' => $id
        ));
        $this->models->NbcExtras->delete(array(
            'project_id' => $id
        ));
    }


	public function doDeleteOrphanedData()
	{

		$data = $this->models->Project->freeQuery('show tables');

		$key = key($data[0]);
		$prefix = $this->models->Project->getTablePrefix();
		$pInUse = array();

		foreach((array)$data as $val) {

			$table = ($val[$key]);

			if (substr($table,0,strlen($prefix))!==$prefix)
				continue;

			// all user tables, infrastructural tables and the project table itself lack a project_id column.
			$d = $this->models->Project->freeQuery('select distinct project_id from '.$table);

			foreach((array)$d as $dVal)
				$pInUse[$dVal['project_id']]=$dVal['project_id'];

			//echo $table;q($d);

		}

		foreach(glob($this->generalSettings['directories']['mediaDirProject'].'/*',GLOB_ONLYDIR) as $file) {
			if(is_dir($file)) {
				$boom = explode('/',$file);
				$boom = array_pop($boom);
				if (is_numeric($boom))
					$pInUse[(int)$boom] = (int)$boom;
			}
		}

		$d = $this->models->Project->_get(array('id' => '*'));

		foreach((array)$d as $val) {

			unset($pInUse[$val['id']]);
			$this->addMessage(sprintf('Ignoring "%s"',$val['sys_name']));

		}

		foreach((array)$pInUse as $val) {

			$this->doDeleteProjectAction($val);
			$this->addMessage(sprintf('Deleted data for orphan ID %s',$val));

		}

	}

}
