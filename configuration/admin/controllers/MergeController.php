<?php /** @noinspection PhpMissingParentCallMagicInspection */
/**
 * Merges the data of projects after an import
 */

include_once ('Controller.php');

class MergeController extends Controller
{

	private $_mediaToMove = array();

    public $usedModels = array(
        'projects',
        'modules',
        'projects_roles_users',
        'users',
        'roles',
        'modules_projects',
        'free_modules_projects',
        'modules_projects_users',
        'free_modules_projects_users',
        'languages',
        'languages_projects',
        'content_taxa',
        'pages_taxa',
        'pages_taxa_titles',
        'commonnames',
        'synonyms',
        'media_taxon',
        'media_descriptions_taxon',
        'content',
        'literature',
        'literature_taxa',
        'keysteps',
        'content_keysteps',
        'choices_keysteps',
        'choices_content_keysteps',
        'matrices',
        'matrices_names',
        'matrices_taxa',
        'matrices_taxa_states',
        'characteristics',
        'characteristics_matrices',
        'characteristics_labels',
        'characteristics_states',
        'characteristics_labels_states',
        'glossary',
        'glossary_synonyms',
        'glossary_media',
        'free_modules_pages',
        'free_module_media',
        'content_free_modules',
        'occurrences_taxa',
        'geodata_types',
        'geodata_types_titles',
        'l2_occurrences_taxa',
        'l2_maps',
		'l2_occurrences_taxa_combi',
		'l2_diversity_index',
        'content_introduction',
        'introduction_pages',
        'introduction_media',
        'users_taxa',
        'chargroups_labels',
        'chargroups',
        'characteristics_chargroups',
        'variations_labels',
        'taxa_variations',
        'taxa_relations',
        'variation_relations',
        'matrices_variations',
        'nbc_extras',
		'hotwords',
        'sections',
		'keytrees'
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Project merge';
    public $controllerPublicNameMask = 'Project administration';

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

			$merge = $this->models->Projects->_get(array(
				'id' => array('id' => $this->rGetId()),
				'order' => 'title'
			));

			$modules = $this->getProjectModules(array('project_id'=>$this->rGetId()));


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
					'sourceId' => $this->rGetId(),
					'targetId' => $this->getCurrentProjectId(),
					'postfix' => ' ('.$merge[0]['title'].')',
					'modules' => $this->rHasVar('modules') ? $this->rGetVal('modules') : null,
					'freeModules' => $this->rHasVar('freeModules') ? $this->rGetVal('freeModules') : null
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

		$projects = $this->models->Projects->_get(array(
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
			array('project_id' => $p['t'],'topic' => '#concat(topic,\''.$p['p'].'\')'),
			array('project_id' => $p['s'])
		);
        $this->models->IntroductionPages->update(
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
        $this->models->GlossarySynonyms->update(
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
        $this->models->LiteratureTaxa->update(
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
        $oldCats = $this->models->PagesTaxa->_get(array(
            'id' => array('id' => $p['s']),
			'fieldAsIndex' => 'id'
        ));

		$del = array();

		// see if any source categories already exist by name in the target
		foreach((array)$oldCats as $key => $oldCat) {

			$d = $this->models->PagesTaxa->_get(array(
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
			$this->models->PagesTaxaTitles->delete(
				array(
					'project_id' => $p['s'],
					'page_id in'=> '('.implode(',',$del).')'
				)
			);
			$this->models->PagesTaxa->delete(
				array(
					'project_id' => $p['s'],
					'id in'=> '('.implode(',',$del).')'
				)
			);
		}

		// update the rest to the target
		$this->models->PagesTaxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->models->PagesTaxaTitles->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		foreach((array)$oldCats as $key => $val) {

			// set new category for the pages of the now deleted source categories
			$this->models->ContentTaxa->update(
				array('page_id' => $val['id']),
				array(
					'project_id' => $p['s'],
					'page_id' => $key
				)
			);

		}

		// update the rest to the target
        $this->models->ContentTaxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		// and the sections (they're just templates anyway)
        $this->models->Sections->delete(array(
            'project_id' => $p['s']
        ));

		$this->addMessage('Merged taxon content.');

	}

    private function mergeTaxa ($p)
    {

		// get ranks in source
		$oldranks = $this->newGetProjectRanks(array('pId'=>$p['s']));

		// get top rank in target
		$d = $this->models->ProjectsRanks->_get(array('id' => array('project_id' => $p['t'], 'parent_id is' => 'null'), 'columns' => 'id'));
		$newTopRankProjectId = $d[0]['id'];

		$resolvedRanks = array();

		// go through old ranks
		foreach((array)$oldranks as $key => $val) {

			// see if we've already resolved this rank (shouldn't happen twice for a rank, but anyway)
			if (!empty($resolvedRanks[$key]))
				continue;

			// get the corresponding project rank id in the target
			$d = $this->models->ProjectsRanks->_get(
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

				$this->models->ProjectsRanks->save(
				array(
					'id' => null,
					'project_id' => $p['t'],
					'rank_id' => $val['rank_id'],
					'parent_id' => $newTopRankProjectId, // i know, but soit, no one will ever notice as they parent-child relations in project ranks are unused
					'lower_taxon' => $val['lower_taxon'],
					'keypath_endpoint' => $val['keypath_endpoint']
				));

				$resolvedRanks[$key] = $this->models->ProjectsRanks->getNewId();

			}

		}

		// checking for a top taxon in the source
		$d = $this->models->ProjectsRanks->_get(array('id' => array('project_id' => $p['s'], 'rank_id' => REGIO_RANK_ID), 'columns' => 'id'));
		if (empty($d[0]['id']))
			$d = $this->models->ProjectsRanks->_get(array('id' => array('project_id' => $p['s'], 'rank_id' => REGNUM_RANK_ID), 'columns' => 'id'));

		if (!empty($d[0]['id'])) {

			$taxonToIgnore = $this->models->Taxa->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'rank_id' => $d[0]['id']
				)
			));

			if (!empty($taxonToIgnore[0]['id'])) {
				$taxonToIgnore = $taxonToIgnore[0]['id'];

				$this->models->Taxa->update(
					array('parent_id' => 'null'),
					array('project_id' => $p['s'],'parent_id' => $taxonToIgnore)
				);

			}

		}

        $this->models->Taxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		if (isset($taxonToIgnore)) {

			$this->models->Taxa->update(
				array('project_id' => $p['s']),
				array('project_id' => $p['t'],'id' => $taxonToIgnore)
			);

		}

		foreach((array)$resolvedRanks as $key => $val) {

			// set new rank_id for the taxa
			$this->models->Taxa->update(
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

        $this->models->Commonnames->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Synonyms->update(
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
        $this->models->TaxaVariations->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->VariationRelations->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->VariationsLabels->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged additional taxon content.');

	}

    private function mergeKey ($p)
	{

		$d = $this->models->ChoicesKeysteps->_get(array('id' => array('project_id' => $p['s'])));

		foreach((array)$d as $val) {
			if (!empty($val['choice_img']))
				$this->_mediaToMove['files'][] = $val['choice_img'];
		}

		$d = $this->models->Keysteps->_get(array('id' => array('project_id' => $p['s'])));

		foreach((array)$d as $val) {
			if (!empty($val['image']))
				$this->_mediaToMove['files'][] = $val['image'];
		}

        $this->models->Keysteps->update(
			array('project_id' => $p['t'],'is_start' => 0),
			array('project_id' => $p['s'])
		);
        $this->models->ContentKeysteps->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChoicesKeysteps->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChoicesContentKeysteps->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged key.');

	}

    private function mergeMatrices ($p)
	{

		$d = $this->models->CharacteristicsStates->_get(array('id' => array('project_id' => $p['s'])));

		foreach((array)$d as $val) {
			if (!empty($val['file_name']))
				$this->_mediaToMove['files'][] = $val['file_name'];
		}

        $this->models->Matrices->update(
			array('project_id' => $p['t'],'default' => 0),
			array('project_id' => $p['s'])
		);
        $this->models->MatricesNames->update(
			array('project_id' => $p['t'],'name' => '#concat(name,\''.$p['p'].'\')'),
			array('project_id' => $p['s'])
		);
        $this->models->MatricesTaxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MatricesTaxaStates->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Characteristics->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicsMatrices->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicsLabels->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicsStates->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicsLabelsStates->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->ChargroupsLabels->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->Chargroups->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->CharacteristicsChargroups->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->MatricesVariations->update(
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

		$d = $this->models->L2Maps->_get(array('id' => array('project_id' => $p['s'])));

		foreach((array)$d as $val) {
			if (!empty($val['image']))
				$this->_mediaToMove['l2maps'][] = $val['image'];
		}

        $this->models->OccurrencesTaxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GeodataTypes->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->GeodataTypesTitles->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2OccurrencesTaxa->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2OccurrencesTaxaCombi->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2DiversityIndex->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);
        $this->models->L2Maps->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'])
		);

		$this->addMessage('Merged mapdata.');

	}

    private function mergeFreeModule ($p)
    {

		$d = $this->models->FreeModulesPages->_get(array('id' => array('project_id' => $p['s'],'module_id' => $p['mId'])));

		foreach((array)$d as $val) {
			if (!empty($val['image']))
				$this->_mediaToMove['files'][] = $val['image'];
		}

		$fmp = $this->models->FreeModulesProjects->_get(
			array(
				'id' => array('project_id' => $p['s'],'id' => $p['mId'])
			)
		);

        $this->models->ContentFreeModules->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'module_id' => $p['mId'])
		);

        $this->models->FreeModulesPages->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'module_id' => $p['mId'])
		);

        $this->models->FreeModulesProjectsUsers->update(
			array('project_id' => $p['t']),
			array('project_id' => $p['s'],'free_module_id' => $p['mId'])
		);

        $this->models->FreeModulesProjects->update(
			array('project_id' => $p['t'],'module' => '#concat(module,\''.$p['p'].'\')'),
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

	        $d = $this->models->ModulesProjects->_get(array(
				'id' =>
					array(
						'project_id' => $targetId,
						'module_id' => $val,
					)
			));

			if (empty($d)) {

				$this->addModuleToProject($val,$targetId,99);
			}

		}

		foreach((array)$freeModules as $val) {



			$p['mId'] = $val;
	        $this->mergeFreeModule($p);
			$this->models->FreeModulesProjectsUsers->save(
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

