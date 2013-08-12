<?php

/*

	deleting of language should delete glossary terms

*/

include_once ('Controller.php');
class ProjectDeleteController extends Controller
{
    public $usedModels = array(
		'characteristic', 
		'characteristic_chargroup', 
		'characteristic_label', 
		'characteristic_label_state', 
		'characteristic_matrix', 
		'characteristic_state', 
		'chargroup', 
		'chargroup_label', 
		'choice_content_keystep', 
		'choice_content_keystep_undo',
		'choice_keystep', 
		'commonname', 
		'content', 
		'content_free_module', 
		'content_introduction', 
		'content_keystep', 
		'content_taxon', 
		'content_taxon_undo', 
		'free_module_media', 
		'free_module_page', 
		'free_module_project', 
		'free_module_project', 
		'free_module_project_user', 
		'free_module_project_user', 
		'geodata_type', 
		'geodata_type_title', 
		'glossary', 
		'glossary_media',
		'glossary_media_captions',
		'glossary_synonym', 
		'heartbeat',
		'hotword',
		'introduction_media', 
		'introduction_page', 
		'keystep', 
		'keytree',
		'l2_diversity_index',
		'l2_map',
		'l2_occurrence_taxon',
		'l2_occurrence_taxon_combi',
		'language', 
		'language_project', 
		'literature', 
		'literature_taxon', 
		'matrix', 
		'matrix_name', 
		'matrix_taxon', 
		'matrix_taxon_state', 
		'matrix_variation', 
		'media_descriptions_taxon', 
		'media_taxon', 
		'module', 
		'module_project', 
		'module_project_user', 
		'nbc_extras',
		'occurrence_taxon', 
		'page_taxon', 
		'page_taxon_title', 
		'project', 
		'project_role_user', 
		'role', 
		'section',
		'synonym', 
		'taxa_relations', 
		'taxon_variation',
		'taxon_variation', 
		'user', 
		'user_taxon', 
		'variation_label', 
		'variation_relations', 
		'gui_menu_order'
    );

    public function __construct ()
    {
        parent::__construct();
    }


    public function __destruct ()
    {
        parent::__destruct();
    }

    public function doDeleteProjectAction ($projectId)
    {
		
		set_time_limit(600);
		
        $this->deleteNBCKeydata($projectId);
        $this->deleteIntroduction($projectId);
        $this->deleteGeoData($projectId);
        $this->deleteMatrices($projectId);
        $this->deleteDichotomousKey($projectId);
        $this->deleteGlossary($projectId);
        $this->deleteLiterature($projectId);
        $this->deleteProjectContent($projectId);
        $this->deleteCommonnames($projectId);
        $this->deleteSynonyms($projectId);
        $this->deleteSpeciesMedia($projectId);
        $this->deleteSpeciesContent($projectId);
        $this->deleteStandardCat($projectId);
        $this->deleteSpecies($projectId);
        $this->deleteFreeModules($projectId);
        $this->deleteProjectRanks($projectId);
        $this->deleteProjectUsers($projectId);
        $this->deleteProjectLanguage($projectId);
        $this->deleteModulesFromProject($projectId);
        $this->deleteProjectCssFile($projectId);
        $this->deleteProjectSettings($projectId);
        $this->deleteProjectDirectories($projectId);
        $this->deleteOtherStuff($projectId);
        $this->deleteProject($projectId);
    }



    public function deleteIntroduction ($id)
    {
        $this->models->ContentIntroduction->delete(array(
            'project_id' => $id
        ));
        $this->models->IntroductionPage->delete(array(
            'project_id' => $id
        ));
        $this->models->IntroductionMedia->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteGeoData ($id)
    {
        $this->models->OccurrenceTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->GeodataTypeTitle->delete(array(
            'project_id' => $id
        ));
        $this->models->GeodataType->delete(array(
            'project_id' => $id
        ));
        
        $this->models->L2Map->delete(array(
            'project_id' => $id
        ));
        $this->models->L2OccurrenceTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->L2OccurrenceTaxonCombi->delete(array(
            'project_id' => $id
        ));
        $this->models->L2DiversityIndex->delete(array(
            'project_id' => $id
        ));

    }



    public function deleteMatrices ($id)
    {
        $this->models->GuiMenuOrder->delete(array(
            'project_id' => $id
        ));
        $this->models->MatrixVariation->delete(array(
            'project_id' => $id
        ));
        $this->models->MatrixTaxonState->delete(array(
            'project_id' => $id
        ));
        $this->models->MatrixTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicLabelState->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicState->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicMatrix->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicLabel->delete(array(
            'project_id' => $id
        ));
        $this->models->Characteristic->delete(array(
            'project_id' => $id
        ));
        $this->models->MatrixName->delete(array(
            'project_id' => $id
        ));
        $this->models->Matrix->delete(array(
            'project_id' => $id
        ));
		
    }



    public function deleteDichotomousKey ($id)
    {
        $this->models->ChoiceContentKeystep->delete(array(
            'project_id' => $id
        ));
        $this->models->ChoiceKeystep->delete(array(
            'project_id' => $id
        ));
        $this->models->ContentKeystep->delete(array(
            'project_id' => $id
        ));
        $this->models->Keystep->delete(array(
            'project_id' => $id
        ));
        $this->models->ChoiceContentKeystepUndo->delete(array(
            'project_id' => $id
        ));
        $this->models->Keytree->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteGlossary ($id)
    {
        $paths = $this->makePathNames($id);
        
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
        
        $this->models->GlossaryMedia->delete(array(
            'project_id' => $id
        ));

        $this->models->GlossaryMediaCaptions->delete(array(
            'project_id' => $id
        ));

        $this->models->GlossarySynonym->delete(array(
            'project_id' => $id
        ));
        $this->models->Glossary->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteLiterature ($id)
    {
        $this->models->LiteratureTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->Literature->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteProjectContent ($id)
    {
        $this->models->Content->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteSpeciesMedia ($id)
    {
        $paths = $this->makePathNames($id);
        
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
        
        $this->models->MediaTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->MediaDescriptionsTaxon->delete(array(
            'project_id' => $id
        ));
    }

    public function deleteCommonnames ($id)
    {
        $this->models->Commonname->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteSynonyms ($id)
    {
        $this->models->Synonym->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteStandardCat ($id)
    {
        $this->models->PageTaxonTitle->delete(array(
            'project_id' => $id
        ));
        $this->models->PageTaxon->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteSpeciesContent ($id)
    {
        $this->models->ContentTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->ContentTaxonUndo->delete(array(
            'project_id' => $id
        ));
        $this->models->Section->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteSpecies ($id)
    {
        $this->models->Taxon->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteProjectRanks ($id)
    {
        $this->models->LabelProjectRank->delete(array(
            'project_id' => $id
        ));
        $this->models->ProjectRank->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteProjectUsers ($id)
    {
        $this->models->UserTaxon->delete(array(
            'project_id' => $id
        ));
        $this->models->ProjectRoleUser->delete(array(
            'project_id' => $id
        ));
        $this->models->ModuleProjectUser->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteProjectLanguage ($id)
    {
        $this->models->LanguageProject->delete(array(
            'project_id' => $id
        ));
    }



    public function deleteFreeModuleMedia ($projectId, $pageId, $paths)
    {
        if (empty($projectId) || empty($pageId))
            return;
        
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
        
        $this->models->ContentFreeModule->delete($d);
        
        $fmp = $this->models->FreeModulePage->_get(array(
            'id' => $d
        ));


        $paths = $this->makePathNames($id);
        
        foreach ((array) $fmp as $key => $val) {
            
            $this->deleteFreeModuleMedia($id, $val['id'], $paths);
        }
        
        $this->models->FreeModulePage->delete($d);
        $this->models->FreeModuleMedia->delete($d);
        
        unset($d);
        
        $d['project_id'] = $id;
        
        if (isset($moduleId))
            $d['free_module_id'] = $moduleId;
        
        $this->models->FreeModuleProjectUser->delete($d);
        
        unset($d);
        
        $d['project_id'] = $id;
        
        if (isset($moduleId))
            $d['id'] = $moduleId;
        
        $this->models->FreeModuleProject->delete($d);
    }



    public function deleteModulesFromProject ($id)
    {
        $this->models->ModuleProject->delete(array(
            'project_id' => $id
        ));
    }


	public function rrmdir($dir)
	{
		if (!file_exists($dir)) return;
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}

    public function deleteProjectDirectories ($id)
    {
        $paths = $this->makePathNames($id);
        
        $this->rrmdir($paths['project_media_l2_maps']);
        $this->rrmdir($paths['project_thumbs']);
        $this->rrmdir($paths['project_media']);
		
		$this->clearAllCaches();
		
        $this->rrmdir($paths['cache']);
    }


    public function deleteProject ($id)
    {
        $p = $this->models->Project->delete(array(
            'id' => $id
        ));
    }


    public function deleteProjectCssFile ($id)
    {
        $p = $this->models->Project->_get(array(
            'id' => $id
        ));
        
        @unlink($this->makeCustomCssFileName($id, $p['title']));
    }


    public function deleteProjectSettings ($id)
    {
        $this->models->Settings->delete(array(
            'project_id' => $id
        ));
    }


    public function deleteOtherStuff ($id)
    {
        $this->models->Heartbeat->delete(array(
        'project_id' => $id
        ));
        $this->models->Hotword->delete(array(
        'project_id' => $id
        ));

    }
    
    
    public function deleteNBCKeydata ($id)
    {
        $this->models->ChargroupLabel->delete(array(
            'project_id' => $id
        ));
        $this->models->Chargroup->delete(array(
            'project_id' => $id
        ));
        $this->models->CharacteristicChargroup->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxonVariation->delete(array(
            'project_id' => $id
        ));
        $this->models->VariationLabel->delete(array(
            'project_id' => $id
        ));
        $this->models->TaxaRelations->delete(array(
            'project_id' => $id
        ));
        $this->models->VariationRelations->delete(array(
            'project_id' => $id
        ));
        $this->models->MatrixVariation->delete(array(
            'project_id' => $id
        ));
        $this->models->NbcExtras->delete(array(
            'project_id' => $id
        ));
    }
	
	
	public function doDeleteOrpahnedData()
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
					$pInUse[intval($boom)] = intval($boom);
			}
		}
		foreach(glob($this->generalSettings['directories']['cache'].'/*',GLOB_ONLYDIR) as $file) {
			if(is_dir($file)) {
				$boom = explode('/',$file);
				$boom = array_pop($boom);
				if (is_numeric($boom))
					$pInUse[intval($boom)] = intval($boom);
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
