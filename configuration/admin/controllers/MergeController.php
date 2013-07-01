<?php

include_once ('Controller.php');
class MergeController extends Controller
{
	
	private $_mediaToMove = array();
	
    public $usedModels = array(
        'project', 
        'module', 
        'project_role_user', 
        'user', 
        'role', 
        'module_project', 
        'free_module_project', 
        'module_project_user', 
        'free_module_project_user', 
        'language', 
        'language_project', 
        'content_taxon', 
        'page_taxon', 
        'page_taxon_title', 
        'commonname', 
        'synonym', 
        'media_taxon', 
        'media_descriptions_taxon', 
        'content', 
        'literature', 
        'literature_taxon', 
        'keystep', 
        'content_keystep', 
        'choice_keystep', 
        'choice_content_keystep', 
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'glossary', 
        'glossary_synonym', 
        'glossary_media', 
        'free_module_project', 
        'free_module_project_user', 
        'free_module_page', 
        'free_module_media', 
        'content_free_module', 
        'occurrence_taxon', 
        'geodata_type', 
        'geodata_type_title', 
        'l2_occurrence_taxon', 
        'l2_map',
		'l2_occurrence_taxon_combi',
		'l2_diversity_index',
        'content_introduction', 
        'introduction_page', 
        'introduction_media', 
        'user_taxon', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'variation_label', 
        'taxon_variation', 
        'taxa_relations', 
        'variation_relations', 
        'matrix_variation', 
        'nbc_extras',
        'heartbeat',
        'taxon_variation',
		'hotword',
        'content_taxon_undo', 
		'choice_content_keystep_undo',
        'section',
		'keytree'
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Project merge';
    public $cssToLoad = array(
        'lookup.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'project.js'
        )
    );

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


	public function mergeAction()
    {

        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Merge project'));
        
        if ($this->rHasVal('id')) {

			$merge = $this->models->Project->_get(array(
				'id' => array('id' => $this->requestData['id']),
				'order' => 'title'
			));        

			$modules = $this->getProjectModules(array('project_id'=>$this->requestData['id']));


			$moduleInfo = array(
				MODCODE_INTRODUCTION => 'Pages will be added to current introduction pages, with altered titles.',
				MODCODE_GLOSSARY => 'Items will be added to current glossary, <b>without</b> checking for duplicates.',
				MODCODE_LITERATURE => 'Items will be added to current glossary, <b>without</b> checking for duplicates.',
				MODCODE_SPECIES => 'Tree will be imported as an orphaned branch (including higher taxa, minus the top-most taxon, which will not be imported); ranks will be matched, or added if necessary. Orphans will be imported <i>as is</i>.',
				MODCODE_KEY => 'Key will be imported as a separate key-section, disconnected from the main key.',
				MODCODE_MATRIXKEY => 'Matrix key(s) will be imported as extra matrices, with altered titles.',
				MODCODE_DISTRIBUTION => 'Will be imported <i>as is</i>, with relation to species in tact.',
				'free' => 'Additional ("free") modules will be imported <i>as is</i>, with altered module names.'
			);

			$this->smarty->assign('merge',$merge[0]);
			$this->smarty->assign('modules',$modules);
			$this->smarty->assign('modulesToIgnore',array(MODCODE_INDEX,MODCODE_UTILITIES,MODCODE_CONTENT,MODCODE_HIGHERTAXA));
			$this->smarty->assign('moduleInfo',$moduleInfo);

        }		

        if ($this->rHasVal('action', 'merge') && $this->rHasVal('id')) {// && !$this->isFormResubmit()) {

			$res = $this->doMergeProject(
				array(
					'sourceId' => $this->requestData['id'],
					'targetId' => $this->getCurrentProjectId(),
					'postfix' => ' ('.$merge[0]['title'].')',
					'modules' => isset($this->requestData['modules']) ? $this->requestData['modules'] : null,
					'freeModules' => isset($this->requestData['freeModules']) ? $this->requestData['freeModules'] : null,
				)
			);
			
			if ($res) {
        
				$this->reInitUserRolesAndRights();
				
				$this->addMessage('Project merged.');
				
			} else {

				$this->addError('Project merge failed.');

			}
			
			$this->smarty->assign('processed',true);

        }

		$projects = $this->models->Project->_get(array(
			'id' => array('id !=' => $this->getCurrentProjectId()),
			'order' => 'title'
		));        

		$this->smarty->assign('current', $_SESSION['admin']['project']['title']);
		$this->smarty->assign('projects', $projects);
        $this->printPage();

    }

    private function mergeIntroduction ($p)
    {

		$d = $this->models->IntroductionMedia->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['file_name'])) 
				$this->_mediaToMove['files'][] = $val['file_name'];
		}
        $this->models->ContentIntroduction->update(
			array('project_id' => $p['t'],'topic' => '#concat(topic,\''.mysql_real_escape_string($p['p']).'\')'),
			array('project_id' => $p['s'])
		);
        $this->models->IntroductionPage->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->IntroductionMedia->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
		
		$this->addMessage('Merged introduction.');

    }

    private function mergeGlossary ($p)
    {

		$d = $this->models->GlossaryMedia->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['file_name'])) 
				$this->_mediaToMove['files'][] = $val['file_name'];
		}

        $this->models->Glossary->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GlossarySynonym->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GlossaryMedia->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged glossary.');

    }

    private function mergeLiterature ($p)
    {
        $this->models->LiteratureTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Literature->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged literature.');

    }

	private function mergeTaxonContent($p)
	{

		// all source categories
        $oldCats = $this->models->PageTaxon->_get(array(
            'id' => array('id' => $p['s']),
			'fieldAsIndex' => 'id'
        ));
		
		$del = array();
		
		// see if any source categories already exist by name in the target
		foreach((array)$oldCats as $key => $oldCat) {

			$d = $this->models->PageTaxon->_get(array(
				'id' => array('id' => $p['t'],'page' => $oldCat['page'])
			));

			// if so, we are going to give the pages in that category a new category (the matching one in the target) and later on delete the category in the source
			if ($d[0]['id']) {
				$del[] = $oldCat;
				$oldCats[$key]['id'] = $d[0]['id'];
			}

		}
		if (!empty($del)) {
			// delete the duplicate categories in the source
			$this->models->PageTaxonTitle->delete(
				array(
					'project_id' => $p['s'],
					'page_id in'=> '('.implode(',',$del).')'
				)
			);
			$this->models->PageTaxon->delete(
				array(
					'project_id' => $p['s'],
					'id in'=> '('.implode(',',$del).')'
				)
			);
		}
				
		// update the rest to the target
		$this->models->PageTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
		
		$this->models->PageTaxonTitle->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
		
		foreach((array)$oldCats as $key => $val) {

			// set new category for the pages of the now deleted source categories
			$this->models->ContentTaxon->update(
				array('page_id' => $val['id']),
				array(
					'project_id' => $p['s'],
					'page_id' => $key
				)
			);

		}
	
		// update the rest to the target
        $this->models->ContentTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		// fuck the undo
        $this->models->ContentTaxonUndo->delete(array(
            'project_id' => $p['s']
        ));

		// and the sections (they're just templates anyway)
        $this->models->Section->delete(array(
            'project_id' => $p['s']
        ));
		
		$this->addMessage('Merged taxon content.');

	}			

    private function mergeTaxa ($p)
    {

		// get ranks in source
		$oldranks = $this->newGetProjectRanks(array('pId'=>$p['s']));

		// get top rank in target
		$d = $this->models->ProjectRank->_get(array('id' => array('project_id' => $p['t'], 'parent_id is' => 'null'), 'columns' => 'id'));
		$newTopRankProjectId = $d[0]['id'];
				
		$resolvedRanks = array();
		
		// go through old ranks
		foreach((array)$oldranks as $key => $val) {

			// see if we've already resolved this rank (shouldn't happen twice for a rank, but anyway)
			if (!empty($resolvedRanks[$key]))
				continue;

			// get the corresponding project rank id in the target
			$d = $this->models->ProjectRank->_get(
			array(
				'id' => array(
					'project_id' => $p['t'], 
					'rank_id' => $val['rank_id']
				), 
				'columns' => 'id'
			));
			
			// if found, remember
			if (!empty($d[0]['id'])) {
				
				$resolvedRanks[$key] = $d[0]['id'];

			} 
			// if not found, create it in the target and remember
			else {

				$this->models->ProjectRank->save(
				array(
					'id' => null, 
					'project_id' => $p['t'], 
					'rank_id' => $val['rank_id'], 
					'parent_id' => $newTopRankProjectId, // i know, but soit, no one will ever notice as they parent-child relations in project ranks are unused
					'lower_taxon' => $val['lower_taxon'],
					'keypath_endpoint' => $val['keypath_endpoint']
				));

				$resolvedRanks[$key] = $this->models->ProjectRank->getNewId();

			}

		}

		// checking for a top taxon in the source
		$d = $this->models->ProjectRank->_get(array('id' => array('project_id' => $p['s'], 'rank_id' => EMPIRE_RANK_ID), 'columns' => 'id'));
		if (empty($d[0]['id']))
			$d = $this->models->ProjectRank->_get(array('id' => array('project_id' => $p['s'], 'rank_id' => KINGDOM_RANK_ID), 'columns' => 'id'));

		if (!empty($d[0]['id'])) {
					
			$taxonToIgnore = $this->models->Taxon->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'rank_id' => $d[0]['id']
				)
			));
			
			if (!empty($taxonToIgnore[0]['id'])) {
				$taxonToIgnore = $taxonToIgnore[0]['id'];

				$this->models->Taxon->update(
					array('parent_id' => 'null'),
					array('project_id' => $p['s'],'parent_id' => $taxonToIgnore)
				);
	
			}
			
		}

        $this->models->Taxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
		
		if (isset($taxonToIgnore)) {

			$this->models->Taxon->update(
				array('project_id' => $p['s']),
				array('project_id' => $p['t'],'id' => $taxonToIgnore)
			);

		}

		foreach((array)$resolvedRanks as $key => $val) {

			// set new rank_id for the taxa 
			$this->models->Taxon->update(
				array('rank_id' => $val),
				array('project_id' => $p['t'],'rank_id' => $key)
			);

		}

		$this->addMessage('Merged taxa.');

    }

    private function mergeTaxaAdditional ($p)
	{

		$d = $this->models->MediaTaxon->_get(array('id' => array('project_id' => $p['s'])));
	
		foreach((array)$d as $val) {
			if (!empty($val['file_name'])) 
				$this->_mediaToMove['files'][] = $val['file_name'];
			if (!empty($val['thumb_name'])) 
				$this->_mediaToMove['thumbs'][] = $val['thumb_name'];
		}
	
        $this->models->Commonname->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Synonym->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MediaTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MediaDescriptionsTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->TaxaRelations->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->TaxonVariation->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->VariationRelations->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->VariationLabel->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged additional taxon content.');

	}

    private function mergeKey ($p)
	{

		$d = $this->models->ChoiceKeystep->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['choice_img'])) 
				$this->_mediaToMove['files'][] = $val['choice_img'];
		}

		$d = $this->models->Keystep->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['image'])) 
				$this->_mediaToMove['files'][] = $val['image'];
		}
	 
        $this->models->Keystep->update(
			array('project_id' => $p['t'],'is_start' => 0),
			array('project_id' => $p['s'])
		);
        $this->models->ContentKeystep->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChoiceKeystep->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChoiceContentKeystep->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged key.');

	}

    private function mergeMatrices ($p)
	{

		$d = $this->models->CharacteristicState->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['file_name'])) 
				$this->_mediaToMove['files'][] = $val['file_name'];
		}

        $this->models->Matrix->update(
			array('project_id' => $p['t'],'default' => 0),
			array('project_id' => $p['s'])
		);
        $this->models->MatrixName->update(
			array('project_id' => $p['t'],'name' => '#concat(name,\''.mysql_real_escape_string($p['p']).'\')'),
			array('project_id' => $p['s'])
		);
        $this->models->MatrixTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MatrixTaxonState->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Characteristic->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicMatrix->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicLabel->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicState->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicLabelState->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChargroupLabel->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Chargroup->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicChargroup->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MatrixVariation->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->NbcExtras->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
		$this->addMessage('Merged matrices.');

	}

    private function mergeDistribution ($p)
	{

		$d = $this->models->L2Map->_get(array('id' => array('project_id' => $p['s'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['image'])) 
				$this->_mediaToMove['l2maps'][] = $val['image'];
		}

        $this->models->OccurrenceTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GeodataType->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GeodataTypeTitle->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2OccurrenceTaxon->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2OccurrenceTaxonCombi->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2DiversityIndex->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2Map->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged mapdata.');

	}

    private function mergeFreeModule ($p)
    {

		$d = $this->models->FreeModulePage->_get(array('id' => array('project_id' => $p['s'],'module_id' => $p['mId'])));
		
		foreach((array)$d as $val) {
			if (!empty($val['image'])) 
				$this->_mediaToMove['files'][] = $val['image'];
		}

		$fmp = $this->models->FreeModuleProject->_get(
			array(
				'id' => array('project_id' => $p['s'],'id' => $p['mId'])
			)
		);

        $this->models->ContentFreeModule->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'module_id' => $p['mId'])
		);

        $this->models->FreeModulePage->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'module_id' => $p['mId'])
		);

        $this->models->FreeModuleProjectUser->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'free_module_id' => $p['mId'])
		);

        $this->models->FreeModuleProject->update(
			array('project_id' => $p['t'],'module' => '#concat(module,\''.mysql_real_escape_string($p['p']).'\')'),
			array('project_id' => $p['s'],'id' => $p['mId'])
		);

		$this->addMessage(sprintf('Merged "%s".',$fmp[0]['module']));

    }

    private function cRename ($from, $to)
    {

		if (!file_exists($from))
			return false;

        if (copy($from, $to)) {
			unlink($from);
            return true;
        } else {
            return false;
        }
    }



		
	private function doMergeProject($p)
	{
		
		/*
		
			we MIGHT need a duplicate doorverwijs system (glossary etc)? sod off, just use hotwords.

		*/

		$sourceId = isset($p['sourceId']) ? $p['sourceId'] : null;
		$targetId = isset($p['targetId']) ? $p['targetId'] : null;
		$modules = isset($p['modules']) ? $p['modules'] : null;
		$freeModules = isset($p['freeModules']) ? $p['freeModules'] : null;
		$postfix = isset($p['postfix']) ? $p['postfix'] : null;
		
		if (empty($sourceId))
			$this->addError('No source project specified.');
		if (empty($targetId))
			$this->addError('No target project specified.');
		if (empty($modules) && empty($freeModules))
			$this->addError('No modules specified.');

		if (empty($sourceId) || empty($targetId) || (empty($modules) && empty($freeModules)))
			return false;
			
		$p = array('s' => $sourceId,'t' => $targetId,'p' => $postfix);
		
		set_time_limit(900);
        
		foreach((array)$modules as $val) {

			/*
			
			v MODCODE_INTRODUCTION => 'Pages will be added to current introduction pages, with altered titles',
			v MODCODE_GLOSSARY => 'Items will be added to current glossary, <b>without</b> checking for duplicates',
			v MODCODE_LITERATURE => 'Items will be added to current glossary, <b>without</b> checking for duplicates',
			v MODCODE_SPECIES => 'Taxon-tree will be imported as one, orphaned branch (including higher taxa); ranks will be matched, or added if necessary',
			v MODCODE_KEY => 'Key will be imported as a separate key-section, disconnected from the main key',
			v MODCODE_MATRIXKEY => 'Matrix key(s) will be imported as extra matrices, with altered titles',
			v MODCODE_DISTRIBUTION => 'Will be imported <i>as is</i>, with relation to species in tact',
			v 'free' => 'Additional ("free") modules will be imported <i>as is</i>, with altered module names'
			
			*/
	
			switch($val) {

				case MODCODE_INTRODUCTION:
					$this->mergeIntroduction($p);
					break;
				case MODCODE_GLOSSARY:
					$this->mergeGlossary($p);
					break;
				case MODCODE_LITERATURE:
					$this->mergeLiterature($p);
					break;
				case MODCODE_SPECIES:
					$this->mergeTaxonContent($p);
					$this->mergeTaxa($p);
					$this->mergeTaxaAdditional($p);
					break;
				case MODCODE_KEY:
					$this->mergeKey($p);
					break;
				case MODCODE_MATRIXKEY:
					$this->mergeMatrices($p);
					break;
				case MODCODE_DISTRIBUTION:
					$this->mergeDistribution($p);
					break;
				
			}

	        $d = $this->models->ModuleProject->_get(array(
				'id' =>
					array(
						'project_id' => $targetId, 
						'module_id' => $val, 
					)
			));
			
			if (empty($d)) {
			
				$this->addModuleToProject($val,$targetId,99);
				$this->grantModuleAccessRights($val,$targetId);
				
			}

		}

		foreach((array)$freeModules as $val) {
			
			
			
			$p['mId'] = $val;
	        $this->mergeFreeModule($p);
			$this->models->FreeModuleProjectUser->save(
			array(
				'id' => null, 
				'project_id' => $p['t'],
				'free_module_id' => $p['mId'], 
				'user_id' => $this->getCurrentUserId()
			));
		}
		
		$srcPaths = $this->makePathNames($sourceId);
		$tgtPaths = $this->makePathNames($targetId);
		
		if (isset($this->_mediaToMove['files'])) {

			$i=0;
			foreach((array)$this->_mediaToMove['files'] as $file) {
				if ($this->cRename(
					$srcPaths['project_media'].$file,
					$tgtPaths['project_media'].$file
				)) $i++;
			}
			
			$this->addMessage(sprintf('Moved %s images.',$i));
			
		}


		if (isset($this->_mediaToMove['thumbs'])) {

			$i=0;
			foreach((array)$this->_mediaToMove['thumbs'] as $file) {
				if ($this->cRename(
					$srcPaths['project_thumbs'].$file,
					$tgtPaths['project_thumbs'].$file
				)) $i++;
			}
	
			$this->addMessage(sprintf('Moved %s thumbs.',$i));

		}


		if (isset($this->_mediaToMove['l2maps'])) {

			$i=0;
			foreach((array)$this->_mediaToMove['l2maps'] as $file) {
				if ($this->cRename(
					$srcPaths['project_media_l2_maps'].$file,
					$tgtPaths['project_media_l2_maps'].$file
				)) $i++;
			}
	
			$this->addMessage(sprintf('Moved %s Linnaues 2-maps.',$i));

		}
		
		return true;

	}


}

