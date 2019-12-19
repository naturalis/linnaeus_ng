<?php
/*
	module
		taxon editor
		media
		traits
		
	variations!?
	
	NBC
	

*/


/*

	column headers, like 'naam SCI', are hardcoded (see array '_nbcColumns' below)!

	$_stdVariantColumns needs to be user invulbaarable --> NOT! assuming sekse and variant (pwah)

	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'suppress_splash',1,now(),CURRENT_TIMESTAMP);
	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'start_page','/kreeften/app/views/matrixkey/identify.php',now(),CURRENT_TIMESTAMP);

*/

include_once ('ProjectDeleteController.php');
include_once ('ImportController.php');
include_once ('ModuleSettingsReaderController.php');

class ImportNBCController extends ImportController
{

    private $_delimiter = ',';
    private $_encloser = '"';
    private $_valueSep = ';'; // used to separate values within a single cell
    private $_defaultKingdom = 'Animalia';
    private $_defaultLanguageId = 24; // dutch, hardcoded
    private $_defaultImgExtension = 'jpg';
    private $_defaultSkinName = 'responsive_matrix';//'nbc_default';
	private $_defaultGroupName = 'NBC default soortgroep';
	private $_nbcColumns = array(
		'sekse' => 'gender',
		'variant' => '-',
		'naam SCI' => '-',
		'naam NL' => '-',
		'foto id beeldbank' => 'url_image',
		'url image' => 'url_image',
		'illustratie soortpagina' => 'url_image',
		'nsrpage' => 'url_external_page',
		'nsrpage nieuw' => 'url_external_page',
		'url' => 'url_external_page',
		'fotograaf' => 'photographer',
		'bronvermelding' => 'source',
		'sekse op foto' => 'gender_photo',
		'thumbnail' => 'url_thumbnail',
		'levensfase' => 'life_stage',
		'Sexe op foto' => 'gender_photo'
	);
    private $_stdVariantColumns = array('sekse','variant');
	private $defaultProjectCss = '../../style/import-default-stylesheet.css';

    public $usedModelsExtended = array(
		'gui_menu_order',
		'l2_occurrences_taxa_combi',
		'matrices_variations',
        'taxa_relations',
        'taxa_variations',
        'variations_labels',
        'variation_relations',
		'names',
		'name_types',
		'module_settings_values'
    );

    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'Matrix Import';
    public $cssToLoad = array();
    public $jsToLoad = array();
    public $modelNameOverride='ImportNBCModel';


	private $settings=[
		'general' => 
			[
				'image_root_skin'=>'../../media/system/skins/responsive_matrix/',
				'skin'=>'responsive_matrix',
				'start_page'=>'../../../app/views/matrixkey/identify.php',
			],
		'matrixkey' =>
			[
				'allow_empty_species'=>1,
				'always_show_details'=>1,
				'always_sort_by_initial'=>0,
				'browse_style'=>'expand',
				'calc_char_h_val'=>1,
				'enable_treat_unknowns_as_matches'=>0,
				'image_orientation'=>'portrait',
				'img_to_thumb_regexp_pattern'=>'/http:\/\/images.naturalis.nl\/original\//',
				'img_to_thumb_regexp_replacement'=>'http://images.naturalis.nl/comping/',
				'introduction_topic_colophon_citation'=>'Matrix colophon & citation',
				'introduction_topic_inline_info'=>'Matrix additional info',
				'introduction_topic_versions'=>'Matrix version history',
				'items_per_line'=>4,
				'items_per_page'=>16,
				'popup_species_link_text'=>'Ga naar soort op Nederlands Soortenregister',
				'score_threshold'=>100,
				'show_scores'=>0,
				'similar_species_show_distinct_details_only'=>1,
				'species_info_url'=>'http://www.nederlandsesoorten.nl/linnaeus_ng/app/views/webservices/taxon_page?pid=1&taxon=%TAXON%&cat=163',
				'use_character_groups'=>1,
				'use_emerging_characters'=>1,
				'suppress_details'=>0,
				'no_media'=>1,
			],
		'introduction'=>
			[
				'no_media'=>1
			]
		];
		
	private $nameTypes=[
        'isValidNameOf'=>0,
        'isSynonymOf'=>0,
        'isSynonymSLOf'=>0,
        'isBasionymOf'=>0,
        'isHomonymOf'=>0,
        'isAlternativeNameOf'=>0,
        'isPreferredNameOf'=>0,
        'isMisspelledNameOf'=>0,
        'isInvalidNameOf'=>0
	];
	

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct()
    {
        // Add specific models for this extended class to $usedModels
        $this->extendUsedModels();

        parent::__construct();
		
		$this->initialize();
	}
	
	private function initialize()
	{
		define('MATRIX_SCIENTIFIC_NAME_STUB','(matrix)');

		$this->_defaultLanguageId = LANGUAGECODE_DUTCH;
        $this->setBreadcrumbRootName($this->translate($this->controllerPublicName));
        $this->setSuppressProjectInBreadcrumbs();
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();

		$this->moduleSettings=new ModuleSettingsReaderController;
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


    /**
     * Index
     *
     * @access    public
     */
    public function indexAction()
    {
        $this->setPageName($this->translate('Data import options'));
        $this->printPage();
    }

    public function nbcDeterminatie1Action()
    {
        if ($this->rHasVal('process', '1'))
            $this->redirect('nbc_determinatie_2.php');

		if ((isset($_SESSION['admin']['system']['import']['type']) && $_SESSION['admin']['system']['import']['type']!='nbc_data') || $this->rHasVal('action','new'))
			unset($_SESSION['admin']['system']['import']);

        $this->setPageName($this->translate('Choose file'));

        $this->setSuppressProjectInBreadcrumbs();

        if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear', 'file')) {

            $tmp = tempnam(sys_get_temp_dir(), 'lng');

            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp)) {

                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $tmp,
                    'name' => $this->requestDataFiles[0]['name'],
                    'src' => 'upload',
                );
				$_SESSION['admin']['system']['import']['type']='nbc_data';
            }
            else {
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }

        $_SESSION['admin']['system']['import']['imagePath'] = false;

        if ($this->rHasVal('clear', 'file')) {

            unset($_SESSION['admin']['system']['import']['file']);
            unset($_SESSION['admin']['system']['import']['raw']);
        }

        if ($this->rHasVal('clear', 'imagePath'))
            unset($_SESSION['admin']['system']['import']['imagePath']);
        if ($this->rHasVal('clear', 'thumbsPath'))
            unset($_SESSION['admin']['system']['import']['thumbsPath']);

        if (isset($_SESSION['admin']['system']['import']))
            $this->smarty->assign('s', $_SESSION['admin']['system']['import']);

        $this->printPage();
    }

    public function nbcDeterminatie2Action()
    {
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('nbc_determinatie_1.php');

        $this->setPageName($this->translate('Parsed data example'));

        $raw = $this->getDataFromFile($_SESSION['admin']['system']['import']['file']['path']);

        $_SESSION['admin']['system']['import']['data'] = $data = $this->parseData($raw);

		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
		}

		if (isset($data['project']['title']))
		{

			$d = $this->models->Projects->_get(array(
				'id' => array(
				'sys_name' => strtolower($data['project']['title'])
			)));

			$exists = ($d!=false);

			$this->smarty->assign('exists',$exists);

			if ($exists)
			{
				$_SESSION['admin']['system']['import']['existingProjectId'] = $d[0]['id'];

				$i=1;
				while ($d!=false)
				{
					$suggestedTitle = $data['project']['title'].' ('.$i++.')';
					$d = $this->models->Projects->_get(array(
							'id' => array(
							'sys_name' => $suggestedTitle
					)));
				}

				$this->smarty->assign('suggestedTitle',$suggestedTitle);

				$_SESSION['admin']['system']['import']['projectExists'] = true;
				$_SESSION['admin']['system']['import']['newProjectTitle'] = $suggestedTitle;

			} 
			else 
			{
				$_SESSION['admin']['system']['import']['projectExists'] = false;
			}

		}

        if (isset($data['project']['soortgroep']))
            $this->smarty->assign('soortgroep', $data['project']['soortgroep']);

        if (isset($data['project']['matrix_name']))
            $this->smarty->assign('matrix_name', $data['project']['matrix_name']);

        if (isset($data['project']['title']))
            $this->smarty->assign('title', $data['project']['title']);

        if (isset($data['species']))
            $this->smarty->assign('species', $data['species']);

        if (isset($data['characters']))
            $this->smarty->assign('characters', $data['characters']);

        $this->printPage();
    }

    public function nbcDeterminatie3Action()
    {

        if (!isset($_SESSION['admin']['system']['import']['data']))
            $this->redirect('nbc_determinatie_2.php');

        $this->setPageName($this->translate('Creating project'));

        if (!isset($_SESSION['admin']['system']['import']['project'])) {// && !$this->isFormResubmit()) {

			// create a new project
			if (!$_SESSION['admin']['system']['import']['projectExists'] ||
				($_SESSION['admin']['system']['import']['projectExists'] && $this->rHasVal('action','new_project'))) {


				if ($_SESSION['admin']['system']['import']['projectExists'])
					$pTitle = $_SESSION['admin']['system']['import']['newProjectTitle'];
				else
					$pTitle = $_SESSION['admin']['system']['import']['data']['project']['title'];

				$pGroup = $_SESSION['admin']['system']['import']['data']['project']['soortgroep'];

				$pId = $this->createProject(
				array(
					'title' => $pTitle,
					'version' => '1',
					'sys_description' => 'Created by import from a NBC-export.',
					'css_url' => $this->defaultProjectCss,
					'group' => $pGroup,
					'published' => 1
				));

				$this->addMessage($this->storeError('Created project "' . $pTitle . '" with id ' . $pId . '.','Project'));

				$_SESSION['admin']['system']['import']['project'] = array(
					'id' => $pId,
					'title' => $pTitle
				);

				$pId = $this->getNewProjectId();

				$this->models->LanguagesProjects->save(
				array(
					'id' => null,
					'language_id' => $this->getNewDefaultLanguageId(),
					'project_id' => $pId,
					'def_language' => 1,
					'active' => 'y',
					'tranlation_status' => 1
				));

				$this->addMessage('Added default language.');

				foreach ($this->nameTypes as $type=>$x)
				{
					$this->models->NameTypes->save( [
						"id"=>null,
						"project_id"=>$pId,
						"nametype"=>$type
					] );
				}

				$this->addMessage('Added name types.');

			}
			// use an existing project
			else
			{
				$pId = $_SESSION['admin']['system']['import']['project']['id'] = $_SESSION['admin']['system']['import']['existingProjectId'];
				$_SESSION['admin']['system']['import']['project']['title'] = $_SESSION['admin']['system']['import']['data']['project']['title'];
				$this->addMessage($this->storeError('Using project "' . $_SESSION['admin']['system']['import']['data']['project']['title'] . '" with id ' . $pId . '.','Project'));
			}

			$this->addModuleToProject(MODCODE_SPECIES, $pId, 0);
			$this->addModuleToProject(MODCODE_HIGHERTAXA, $pId, 0);
			$this->addModuleToProject(MODCODE_MATRIXKEY, $pId, 1);
			$this->addUserToProjectAsLeadExpert($pId);

			$this->addMessage('Added current user as lead expert to project.');

			if ($this->rHasVal('action'))
			{

				$_SESSION['admin']['system']['import']['existingProjectTreatment'] = $this->rGetVal('action');

				if ($this->rHasVal('action','replace_data') || $this->rHasVal('action','replace_species_data')) {

					$pDel = new ProjectDeleteController;
					$pDel->deleteMatrices( [ 'project_id'=>$pId,'keep_files'=>true ] );

					if ($this->rHasVal('action','replace_data'))
					{
						$pDel->doDeleteAllButProjectItself($pId);
					} 
					else
					if ($this->rHasVal('action','replace_species_data'))
					{
						$pr = $this->models->ProjectsRanks->_get(array(
							'id' => array(
								'project_id' => $pId,
								'rank_id' => SPECIES_RANK_ID
							)
						));

						$taxa = $this->models->Taxa->_get(array(
							'id' => array(
								'project_id' => $pId,
								'rank_id' => $pr[0]['id']
							)
						));

						$d=0;

						$pDel->deleteMatrices( [ 'project_id'=>$pId,'keep_files'=>true ] );

						foreach((array)$taxa as $key => $val) {
							$this->deleteTaxon($val['id'],$pId,false);
							$d++;
						}

						$this->addMessage(sprintf('Deleted %s species.',$d));

					}

				}

			}

        }

        $this->printPage();
    }

    public function nbcDeterminatie4Action()
    {

        if (!isset($_SESSION['admin']['system']['import']['project']))
            $this->redirect('nbc_determinatie_3.php');

        $this->setPageName($this->translate('Storing ranks, species and variations'));

        if ($this->rHasVal('action', 'save') && !$this->isFormResubmit())
		{

			if ($this->rHasVal('nbcColumns'))
				$_SESSION['admin']['system']['import']['data']['nbcColumns'] = $this->rGetVal('nbcColumns');

            $ranks = $this->addRanks();

            $_SESSION['admin']['system']['import']['project']['ranks'] = $ranks;

            if ($this->rHasVal('variant_columns'))
				$_SESSION['admin']['system']['import']['variantColumns'] = $this->rGetVal('variant_columns');

            $data = $_SESSION['admin']['system']['import']['data'];

            $data = $this->storeSpeciesAndVariationsAndMatrices($data);

            $_SESSION['admin']['system']['import']['species_data'] = $data;

            $this->addMessage('Added ' . count((array) $_SESSION['admin']['system']['import']['species_data']) . ' species.');
            $this->addMessage('Added ' . $_SESSION['admin']['system']['import']['loaded']['variations'] . ' variations.');

            $this->smarty->assign('processed', true);
        }

		//$this->smarty->assign('variantColumns',$this->extractVariantColumns($_SESSION['admin']['system']['import']['data']));
		//$this->smarty->assign('stdVariantColumns',$this->_stdVariantColumns);


        if (isset($_SESSION['admin']['system']['import']['data']['hidden']))
            $this->smarty->assign('hidden', $_SESSION['admin']['system']['import']['data']['hidden']);

		$this->smarty->assign('nbcColumns',$this->_nbcColumns);

		$this->smarty->assign('characters',$_SESSION['admin']['system']['import']['data']['characters']);

        $this->printPage();
    }

    public function nbcDeterminatie5Action()
    {
        if (!isset($_SESSION['admin']['system']['import']['species_data']))
            $this->redirect('nbc_determinatie_4.php');

        $this->setPageName($this->translate('Saving matrix data'));

        $data = $_SESSION['admin']['system']['import']['data'];
		$matrixName = isset($data['project']['matrix_name']) ? $data['project']['matrix_name'] : $data['project']['title'];

		// does a matrix with this name already exist?
		$mId = $this->getExistingMatrixId($matrixName);

		if ($this->rHasVal('action', 'matrix') && !$this->isFormResubmit()) {

			// it does...
			if (!is_null($mId))
			{

				$this->addMessage($this->storeError('Using matrix "' .$matrixName . '"','Matrix'));
				$this->smarty->assign('usingExisting', true);

				// ...and we want to replace it, so we delete all existing matrix-data first
				if ($this->rHasVal('data_treatment','replace_data'))
				{

					$this->models->MatricesTaxaStates->delete(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId
					));
					$this->models->MatricesTaxa->delete(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId
					));
					$this->models->MatricesVariations->delete(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId
					));
					$this->models->CharacteristicsMatrices->delete(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId
					));

					$l = $this->models->CharacteristicsMatrices->_get(array(
						'id' =>
							array(
								'project_id' => $this->getNewProjectId(),
								'matrix_id !=' => $mId
							),
						'columns' => 'characteristic_id'
						)
					);


					$d=array(0=>-1);

					foreach((array)$l as $key => $val)
						$d[]=$val['characteristic_id'];

					$d=implode(',',$d);

					$this->models->CharacteristicsLabelsStates->delete(array(
						'project_id' => $this->getNewProjectId(),
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicsStates->delete(array(
						'project_id' => $this->getNewProjectId(),
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicsChargroups->delete(array(
						'project_id' => $this->getNewProjectId(),
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicsLabels->delete(array(
						'project_id' => $this->getNewProjectId(),
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->Characteristics->delete(array(
						'project_id' => $this->getNewProjectId(),
						'id not in#' => '('.$d.')'
					));
					$this->models->GuiMenuOrder->delete(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId
					));
				}
				// ...but we don't want to overwrite it, so we create a matrix with a new name
				else
				{

					$m = $this->createMatrixIfNotExists($_SESSION['admin']['system']['import']['newMatrixTitle']);
					$mId = $m['id'];
					$this->addMessage($this->storeError('Created matrix "' . $m['name'] . '"','Matrix'));

				}

			} else {

				// a matrix with this name does not exist yet, so we create one
				$m = $this->createMatrixIfNotExists($matrixName);
				$mId = $m['id'];
				$this->addMessage($this->storeError('Created matrix "' . $m['name'] . '"','Matrix'));

			}

            $data = $this->storeCharacterGroups($_SESSION['admin']['system']['import']['data'], $mId);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['chargroups'] . ' character groups.');

            $data = $this->storeCharacters($data, $mId, $this->rGetVal('char_type'));
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['characters'] . ' characters.');

            $data = $this->storeStates($data);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['states'] . ' states.');

            $this->storeVariationStateConnections($_SESSION['admin']['system']['import']['species_data'], $data, $mId);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['connections'] . ' variation-state connections.');

            $this->smarty->assign('processed', true);
        }


		// a matrix with this name already exists, so we fabricate a new unique name
		if (!is_null($mId)) {

			$_SESSION['admin']['system']['import']['existingMatrixId'] = $mId;

			$i=1;
			$d=true;
			while ($d!=false) {
				$suggestedTitle = $matrixName.' ('.$i++.')';
				$d = $this->models->MatricesNames->_get(array(
					'id' => array(
						'language_id' => $this->getNewDefaultLanguageId(),
						'name' => $suggestedTitle
				)));
			}

			if (isset($suggestedTitle))
				$this->smarty->assign('suggestedTitle',$suggestedTitle);

			$_SESSION['admin']['system']['import']['matrixExists'] = true;
			$_SESSION['admin']['system']['import']['newMatrixTitle'] = $suggestedTitle;
			$this->smarty->assign('suggestedTitle',$suggestedTitle);

		} else {

			$_SESSION['admin']['system']['import']['matrixExists'] = false;

		}

		$this->smarty->assign('matrix',$matrixName);
		$this->smarty->assign('matrixExists',!is_null($mId));
		$this->smarty->assign('projectExists',$_SESSION['admin']['system']['import']['projectExists']);

        $this->smarty->assign('characters', $data['characters']);
        //$this->smarty->assign('skin', $this->_defaultSkinName);
        //$this->smarty->assign('matrix_state_image_per_row', 4);
        //$this->smarty->assign('matrix_state_image_max_height', 300);
        $this->smarty->assign('items_per_page', 16);
        $this->smarty->assign('items_per_line', 4);
        //$this->smarty->assign('matrix_use_sc_as_weight', 1);
        $this->smarty->assign('browse_style', 'expand');	//paginate|expand
       	//$this->smarty->assign('image_root_skin', '../../media/system/skins/'.$this->_defaultSkinName.'/');

        $this->printPage();
    }

    public function nbcDeterminatie6Action()
    {
		if ($this->rHasVal('action','download'))
		{
			$this->doDownload();
			die();
		}

		if ($this->rHasVal('action','errorlog'))
		{
			$this->downloadErrorLog();
			die();
		}

		foreach((array)$this->settings as $module=>$settings)
		{
			foreach((array)$settings as $setting=>$value)
			{
				$this->saveSettingIfNew( [ 
					'module'=>$module,
					'setting'=>$setting,
					'value'=>$value,
				] );
			}
		}

        $this->setPageName($this->translate('Import finished'));

		$this->printPage();
    }

    public function nbcDeterminatie7Action()
    {
		$this->redirect('../projects/choose_project.php?project_id='.$this->getNewProjectId());
    }



	public function nbcLabels1Action()
    {
        if ($this->rHasVal('process', '1'))
            $this->redirect('nbc_labels_2.php');

		if (isset($_SESSION['admin']['system']['import']['type']) && $_SESSION['admin']['system']['import']['type']!='nbc_labels')
			unset($_SESSION['admin']['system']['import']);

        $this->setPageName($this->translate('Choose file'));

        $this->setSuppressProjectInBreadcrumbs();

        if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear', 'file'))
		{

            $tmp = tempnam(sys_get_temp_dir(), 'lng');

            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp))
			{

                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $tmp,
                    'name' => $this->requestDataFiles[0]['name'],
                    'src' => 'upload'
                );
				$_SESSION['admin']['system']['import']['type']='nbc_labels';
            }
            else {

                unset($_SESSION['admin']['system']['import']['file']);
            }
        }


        $_SESSION['admin']['system']['import']['imagePath'] = false;


        if ($this->rHasVal('clear', 'file'))
		{

            unset($_SESSION['admin']['system']['import']['file']);
            unset($_SESSION['admin']['system']['import']['raw']);
        }

        if ($this->rHasVal('clear', 'imagePath'))
            unset($_SESSION['admin']['system']['import']['imagePath']);
        if ($this->rHasVal('clear', 'thumbsPath'))
            unset($_SESSION['admin']['system']['import']['thumbsPath']);

        if (isset($_SESSION['admin']['system']['import']))
            $this->smarty->assign('s', $_SESSION['admin']['system']['import']);

        $this->printPage();
    }

    public function nbcLabels2Action()
    {
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('nbc_determinatie_1.php');

        $this->setPageName($this->translate('Parsed data'));

        $raw = $this->getDataFromFile($_SESSION['admin']['system']['import']['file']['path']);

        $_SESSION['admin']['system']['import']['data'] = $data = $this->parseLabelData($raw);

		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
		}

		if ($data['project']['title'])
		{

			$d = $this->models->Projects->_get(array(
					'id' => array(
					'sys_name' => $data['project']['title']
				)));

			//q($this->models->Project->q());

            $this->smarty->assign('exists',$d!=false);

		}

        if (isset($data['project']['matrix']['label']))
            $this->smarty->assign('matrix', $data['project']['matrix']['label']);

        if (isset($data['project']['soortgroep']))
            $this->smarty->assign('soortgroep', $data['project']['soortgroep']);

        if (isset($data['project']['title']))
            $this->smarty->assign('title', $data['project']['title']);

        if (isset($data['states']))
            $this->smarty->assign('states', $data['states']);

        $this->printPage();
    }

    public function nbcLabels3Action()
    {

        if (!isset($_SESSION['admin']['system']['import']['data']))
            $this->redirect('nbc_determinatie_2.php');

        $this->setPageName($this->translate('Saving labels'));

        if (!$this->isFormResubmit())
		{

			$data=$_SESSION['admin']['system']['import']['data'];
		
			$p=$this->models->Projects->_get( [ 'id' => [ 'sys_name' => $data['project']['title'] ] ] );
			$this->_project_id=$p[0]['id'];
			$m=$this->models->Matrices->_get( [ 'id' => [ 'project_id' => $this->_project_id, 'sys_name' => $data['project']['matrix']['label'] ] ] );
			$this->_matrix_id=$m[0]['id'];
			$this->_language_id=$this->getNewDefaultLanguageId($this->_project_id);
			$dummy=array();
			
			foreach((array)$data['states'] as $key=>$val)
			{
				$this->_group_name=$val[0];
				$this->_char_name=$val[1];
				$this->_state_name=$val[2];
				$this->_translation=$val[3];
				$this->_image=$val[4];

				$state=$this->models->ImportNBCModel->resolveState( [
					'project_id' => $this->_project_id,
					'matrix_id' => $this->_matrix_id,
					'state_name' => $this->_state_name,
					'char_name' => $this->_char_name,
					'group_name' => $this->_group_name,
				] );

				if ( $state )
				{

					if ( !empty($this->_translation) )
					{
						$this->models->CharacteristicsLabelsStates->update(
							[ 'label' => $this->_translation ],
							[ 'project_id' => $this->_project_id, 'state_id' => $state['id'], 'language_id' => $this->_language_id ]
						);

						$this->addMessage(
							$this->storeError(
								sprintf($this->translate('Saved translation "%s" for %s:%s:%s.'),$this->_translation,$this->_group_name,$this->_char_name,$this->_state_name),'Matrix characters'));
					}
					else
					{
						//
					}

					if ( !empty($this->_image) )
					{

						$this->models->CharacteristicsStates->update(
							[ 'file_name' => $this->_image ],
							[ 'project_id' => $this->_project_id, 'id' => $state['id'] ]
						);

						$this->addMessage(
							$this->storeError(
								sprintf($this->translate('Saved image "%s" for %s:%s:%s.'),$this->_image,$this->_group_name,$this->_char_name,$this->_state_name),'Matrix characters'));

						$dummy[$state['characteristic_id']]['state']=
							(!isset($dummy[$state['characteristic_id']]['state']) ?
								'all_images' :
								($dummy[$state['characteristic_id']]['state']=='all_images' ?
									'all_images' :
									($dummy[$state['characteristic_id']]['state']=='no_images' ? 'partial_images' : 'partial_images' )));
									
						$dummy[$state['characteristic_id']]['label']=$this->_char_name;

					} 
					else
					{
						$dummy[$state['characteristic_id']]['state']=
							(!isset($dummy[$state['characteristic_id']]['state']) ?
								'no_images' :
								($dummy[$state['characteristic_id']]['state']=='all_images' ?
									'partial_images' :
									($dummy[$state['characteristic_id']]['state']=='no_images' ? 'no_images' : 'partial_images' )));

						$dummy[$state['characteristic_id']]['label']=$this->_char_name;
					}

				}
				else
				{
					$this->addError($this->storeError(sprintf($this->translate('Could not resolve state %s:%s:%s.'),$this->_group_name,$this->_char_name,$this->_state_name),'Matrix characters'));
				}
										
			}

			if ($this->rHasval('re_type_chars','all') || $this->rHasval('re_type_chars','partial'))
			{

				$this->addMessage($this->translate('Re-evaluating character types (using setting "'.($this->rHasval('re_type_chars','partial') ? 'need some' : 'need all' ).'").'));

				foreach((array)$dummy as $cId => $char)
				{

					$type =
						(($this->rHasval('re_type_chars','partial') && ($char['state']=='all_images' || $char['state'] =='partial_images')) ||
						($this->rHasval('re_type_chars','all') && $char['state']=='all_images')) ? 'media' : 'text';
	
					$this->models->Characteristics->update(
						[ 'type' => $type ],
						[ 'id' => $cId, 'project_id' => $this->_project_id ]
					);

					$this->addMessage($this->storeError(sprintf($this->translate('Set character type for "%s" to %s.'),$char['label'],$type),'Matrix characters'));

				}

			} 
			else
			{
				$this->addMessage($this->storeError($this->translate('Skipped re-evaluating character types.'),'Matrix characters'));
			}
				
			unset($_SESSION['admin']['system']['import']);

			$this->addMessage($this->translate('Done.'));

		}
	
        $this->printPage();
    }



	private function saveSettingIfNew( $p ) 
	{
        $module = isset($p['module']) ? $p['module'] : 'general';
        $setting = isset($p['setting']) ? $p['setting'] : null;
        $value = isset($p['value']) ? $p['value'] : null;

        if (is_null($value) || is_null($setting) || is_null($module)) return;

		if ($module=='general')
			$this->moduleSettings->getGeneralSetting( [ 'setting'=>$setting, 'no_auth_check'=>true ] );
		else
			$this->moduleSettings->getModuleSetting( [ 'module'=>$module, 'setting'=>$setting ] );

		$settingId=$this->moduleSettings->getLastSettingId();
		
		$d=$this->models->ModuleSettingsValues->_get( [ "id" => ["project_id"=>$this->getNewProjectId(), "setting_id"=>$settingId]	] );

		if (!$d)
		{
			$this->models->ModuleSettingsValues->save( [
				"id"=>null,
				"project_id"=>$this->getNewProjectId(),
				"setting_id"=>$settingId,
				"value"=>$value
			] );

			$this->addMessage($this->storeError(sprintf('Saved setting %s:%s:%s',$module,$setting,$value),'Matrix'));
		}
	}

    private function getNewProjectId()
    {
        return (isset($_SESSION['admin']['system']['import']['project']['id'])) ? $_SESSION['admin']['system']['import']['project']['id'] : null;
    }

    private function getNewDefaultLanguageId($pId=null)
    {

		$pId=is_null($pId) ? $this->getNewProjectId() : $pId;

        $lp = $this->models->LanguagesProjects->_get(array(
            'id' => array(
                'project_id' => $pId,
				'def_language' => 1
            )
        ));

		if ($lp)
			return $lp[0]['language_id'];

        $lp = $this->models->LanguagesProjects->_get(array(
            'id' => array(
                'project_id' => $pId,
            )
        ));

		if ($lp)
			return $lp[0]['language_id'];

        return $this->_defaultLanguageId;
    }

    private function getDataFromFile ($file)
    {

		// counting possible field separators to find the (very likely) one in this particular flavour of CSV
		$buffer = file_get_contents($file);

		$separators =
			array(
				'tab' => array('count'=>0,'str'=>chr(9)),
				'comma' => array('count'=>0,'str'=>','),
				'semicolon' => array('count'=>0,'str'=>';')
			);

		foreach($separators as $key => $val)
			$separators[$key]['count'] = substr_count($buffer,$val['str']);

		$prev = -1;

		foreach($separators as $key => $val) {

			if ($val['count']>$prev) {
				$prev = $val['count'];
				$this->_delimiter = $val['str'];

			}

		}

        $raw = array();

        if (($handle = fopen($file, "r")) !== FALSE) {
            $i = 0;
            while (($dummy = fgetcsv($handle, 8192, $this->_delimiter, $this->_encloser)) !== FALSE) {
                foreach ((array) $dummy as $val) {
                    $raw[$i][] = $val;
                }
                $i++;
            }
            fclose($handle);
        }

        return $raw;
    }

    private function parseData($raw)
    {

        /*
         	excel lines:
			NOTE: Excel starts counting lines at 1, but we - naturally - at 0.

			Exc	PHP
	        1	0: title (A1) / character_codes
	        2	1: soortgroep (A2) / label of what follows (E2) / character instructions (mostly empty)
	        3	2: label of what follows (E3) / character groups (+ hidden)
	        4	3: label of what follows (E4) / instructions
	        5	4: unit of measurement
	        6	5: (empty)
	        7	6: column headers / (empty)
	        8	7 ev: data

        */
        $data = array();

		$isReadyColumn=$diergroepColumn=-1;

        foreach ((array) $raw as $line => $val) {

			$d = $val;
			unset($d[0]);
            $lineHasData = strlen(implode('', $d)) > 0;

            if ($lineHasData) {

                foreach ((array) $val as $cKey => $cVal) {

					$cVal=trim($cVal,chr(239).chr(187).chr(191).chr(9).chr(32).chr(10).chr(13));
					// that's BOM, tab, space, returns

                    if (is_numeric($cVal) || !empty($cVal)) {

                        // line 0, cell 0: title
                        if ($line == 0 && $cKey == 0)
                            $data['project']['title'] = $cVal;
                        // line 0, cell > 0: character codes
                        if ($line == 0 && $cKey > 4 && !empty($cVal))
                            $data['characters'][$cKey]['code'] = $cVal;

                        // line 1, cell 0: group
                        if ($line == 1 && $cKey == 0)
                            $data['project']['soortgroep'] = $cVal;

                        // line 2, cell 0: optional matrix name
                        if ($line == 2 && $cKey == 0)
                            $data['project']['matrix_name'] = $cVal;

                        // line 2, cell > 0: character group (or 'hidden')
                        if ($line == 2 && $cKey > 4 && !empty($cVal)) {
                            $data['characters'][$cKey]['group'] = $cVal;
						}

						/*
							because it is easier for the ppl making the import-files,
							two characters - gender and variant - are grouped
							with the taxon's name, rather than with the other
							characters.
							we love the smell of exceptions in the morning.
						*/
						if ($line==6 && ($cKey==2 || $cKey==3) && !empty($cVal)) {

							$data['characters'][$cKey] = array(
								'code' => $cVal,
								'group' => 'hidden',
								'unit' => 'string',
							);

						}

                    	// line 3, cell > 0: character instructions
                    	if ($line==3 && $cKey>4 && !empty($cVal)) // && $cVal!='�')
                    		$data['characters'][$cKey]['instruction'] = $cVal;


                    	// line 4, cell > 0: character unit (not currently being saved)
                    	if ($line==4 && $cKey>4 && !empty($cVal))
                    		$data['characters'][$cKey]['unit'] = $cVal;

						// line 6: home of the optional "ready?" toggle (no toggle? everyone ready!)
                        if ($line==6  && in_array(strtolower($cVal),array('klaar','klaar?')))
                            $isReadyColumn=$cKey;
						else
						// line 6: ...and of the optional "diergroep"
                        if ($line==6  && strtolower($cVal)=='diergroep')
                            $diergroepColumn=$cKey;
						else
						// line 6: species column headers
                        if ($line==6  && $cKey<10 && !empty($cVal))
                            $data['columns'][$cKey] = $cVal;

						// line > 6: species records
						if ($line > 6) {

							if ($isReadyColumn>0 && preg_match('/(ja|yes|j|y)/i',$val[$isReadyColumn])!==1) {
								if ($cKey==0)
									$this->addMessage($this->storeError('Skipping line '.($line+1).' (ready="'.$val[$isReadyColumn].'")','Species import'));
								continue;
							}

							// line number (is uniq id during import)
							if (isset($data['columns'][$cKey]) && preg_match('/id/i',$data['columns'][$cKey])===1) {
								$data['species'][$line]['id'] = $cVal;
							}
							// species' main label
							else if (isset($data['columns'][$cKey]) && preg_match('/^(title|titel|name|naam)$/i',$data['columns'][$cKey])===1) {
								$data['species'][$line]['label'] = $cVal;
							}
							// related species' main id's (or names)
							else if (isset($data['columns'][$cKey]) && preg_match('/^gelijkende(.*)/i',$data['columns'][$cKey])==1) {
								if (strpos($cVal, $this->_valueSep) !== false) {
									$data['species'][$line]['related'] = explode($this->_valueSep, $cVal);
								}
								else {
									$data['species'][$line]['related'][] = trim($cVal);
								}
                                $data['species'][$line]['related'] = array_map('trim', $data['species'][$line]['related']);
							}
							// catch optional group (will turn into parent of rank family)
							else if ($diergroepColumn>0 && $cKey==$diergroepColumn) {
								$data['species'][$line]['inGroup'] = trim($cVal);
							}
							else if ($cKey==$isReadyColumn) {
								// do nothing, just skip
							}
							// character states per species
							else {

								if (isset($data['characters'][$cKey]['group']) && $data['characters'][$cKey]['group'] == 'hidden') {

									$data['species'][$line][$data['characters'][$cKey]['code']] = $cVal;
									$data['hidden'][$data['characters'][$cKey]['code']] = null;


								}
								else {
									if (strpos($cVal, $this->_valueSep) !== false) {
										$data['species'][$line]['states'][$cKey] = preg_split('/'.$this->_valueSep.'/', $cVal,-1,PREG_SPLIT_NO_EMPTY);
									}
									else {
										$data['species'][$line]['states'][$cKey][] = trim($cVal);
									}
                                    $data['species'][$line]['states'][$cKey] = array_map('trim', $data['species'][$line]['states'][$cKey]);
								}
							}
						}
                    }
                }

				// discarding species without ID in col 0
				if (isset($data['species']) && isset ($data['species'][$line]) && empty($data['species'][$line]['id'])) {

					unset($data['species'][$line]);

				} else
				/*
					when label (= 'title' column) has the scientific name, ppl tend to leave the
					'sci name'-column empty, but we are going to need it, so assumptions are made.
				*/
				if (isset($data['species']) && isset ($data['species'][$line]) && empty($data['species'][$line]['naam SCI'])) {

					if (!isset($data['species'][$line]['label'])) {

						$this->addError('Ignoring line '.($line+1).': lacks value for \'title\'.<br />("'.implode(',', $val).'")');

						unset($data['species'][$line]);

					} else {

						$data['species'][$line]['naam SCI'] = $data['species'][$line]['label'];

					}

				}
            }
        }

		if (isset($data['characters'])) {

			foreach((array)$data['characters'] as $cKey => $cVal) {

				$unused = true;

				foreach((array)$data['species'] as $sKey => $sVal) {

					if (!empty($sVal['states'][$cKey])) {

						$unused = false;

					}

				}

				if ($unused)
					unset($data['characters'][$cKey]);

			}

			ksort($data['characters']);

		}

        return $data;
    }

    private function addProjectRank($label, $rankId, $isLower, $parentId)
    {

        $d = $this->models->ProjectsRanks->_get(array('id' =>
			array(
				'project_id' => $this->getNewProjectId(),
				'rank_id' => $rankId,
			)));

		if ($d) {

			$id = $d[0]['id'];

		} else {

			$this->models->ProjectsRanks->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'rank_id' => $rankId,
				'parent_id' => isset($parentId) ? $parentId : null,
				'lower_taxon' => $isLower ? '1' : '0'
			));

			$id = $this->models->ProjectsRanks->getNewId();

			$this->models->LabelsProjectsRanks->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'project_rank_id' => $id,
				'language_id' => $this->getNewDefaultLanguageId(),
				'label' => $label
			));

		}

        return $id;
    }

    private function addRanks()
    {
        $r = $this->models->Ranks->_get(array(
            'id' => array(
                'in_col' => '1'
            ),
            'order' => 'id'
        ));

        $id = null;

        foreach ((array) $r as $val) {

            $id = $this->addProjectRank($val['default_label'], $val['id'], ($val['id'] == SPECIES_RANK_ID), $id);
            if ($val['id'] == SPECIES_RANK_ID)
                $projectSpeciesId = $id;
            if ($val['id'] == REGNUM_RANK_ID)
                $projectKingdomId = $id;
            if ($val['id'] == FAMILIA_RANK_ID)
                $projectFamilyId = $id;

        }

        return array(
            'kingdom' => $projectKingdomId,
            'species' => $projectSpeciesId,
            'family' => $projectFamilyId
        );
    }

    private function resolveSpeciesAndVariationsAndMatrices($data)
    {

        $d = array();

        foreach ((array) $data['species'] as $key => $val) {

			if (empty($val['naam SCI'])) {

				$this->addError($this->storeError('Skipping species without scientific name ('.@$val['label'].').', 'Species import'));
				continue;

			}

			if ($val['naam SCI']==MATRIX_SCIENTIFIC_NAME_STUB) {

				$sciName = $val['label'];
				$d[$sciName]['isMatrix'] = true;

			} else {

				$sciName = $val['naam SCI'];
				$d[$sciName]['isMatrix'] = false;

			}

			$d[$sciName]['taxon'] = $sciName;
			if (isset($val['naam NL'])) $d[$sciName]['common name'] = $val['naam NL']; // $val['label'];
			$d[$sciName]['id'] = $val['id'];
			if (isset($val['inGroup'])) $d[$sciName]['parent_name'] = $val['inGroup'];
			$d[$sciName]['variations'][] = $val;

        }

		//$variantColumns = isset($_SESSION['admin']['system']['import']['variantColumns']) ? $_SESSION['admin']['system']['import']['variantColumns'] : null;
		$variantColumns = $this->_stdVariantColumns;

        foreach ((array) $d as $key => $val) {


			if (isset($val['variations'])) {

				foreach ((array)$val['variations'] as $sKey => $sVal) {

					$str = null;

					foreach ((array) $variantColumns as $hVal) {

						//if (!isset($data['characters'][$hVal]['code']) || !isset($sVal[$data['characters'][$hVal]['code']]))
						if (!isset($sVal[$hVal]))
							continue;

						$str .= $sVal[$hVal]. ' ';

					}

					$d[$key]['variations'][$sKey]['add-on'] = trim($str);
					//$d[$key]['variations'][$sKey]['variant'] = (isset($sVal['naam NL']) ? $sVal['naam NL'] : $key) . ' ' . $d[$key]['variations'][$sKey]['add-on'];
					$d[$key]['variations'][$sKey]['variant'] = $d[$key]['variations'][$sKey]['add-on'];
				}


				/*
					if a species has only one variation, it should not be stored (ie, species & variation identical)
				   if (count((array) $d[$key]['variations']) == 1 && strlen($d[$key]['variations'][0]['add-on']) == 0)
				*/
				if (count((array) $d[$key]['variations']) == 1) {

					foreach((array)$d[$key]['variations'][0] as $vKey => $vVal) {

						if ($vKey=='id' || $vKey=='label' || $vKey=='add-on' || $vKey=='variant')
							continue;

						$d[$key][$vKey] = $vVal;

					}

					/*
					if (isset($d[$key]['variations'][0]['states']))
						$d[$key]['states'] = $d[$key]['variations'][0]['states'];

					if (isset($d[$key]['variations'][0]['related']))
						$d[$key]['related'] = $d[$key]['variations'][0]['related'];
					*/

					unset($d[$key]['variations']);

				}

			}
        }

        return $d;

    }

	private function resolveSimilarIdentifier($id,$lst)
	{
		$id = trim($id);

		if (empty($id)) return;

		if (is_numeric($id)) return $id;

		foreach ((array)$lst as $key => $val)
		{
			//if (trim(strtolower($val['name']))==strtolower($id))
			if (in_array(strtolower($id),$val['name']))
			{
				return $key;
			}
		}

		$this->addError($this->storeError(sprintf($this->translate('Could not resolve similar species "%s"'),$id), 'Species import'));

		return $id;
	}

    private function storeSpeciesAndVariationsAndMatrices($data)
    {
        $_SESSION['admin']['system']['import']['loaded']['species'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['variations'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['matrices'] = 0;

        $tmpIndex = array();
        $species = $this->resolveSpeciesAndVariationsAndMatrices($data);


		foreach ($this->nameTypes as $type=>$x)
		{
			$d=$this->models->NameTypes->_get( [ "id" => 
			[
				"project_id"=>$this->getNewProjectId(),
				"nametype"=>$type
			] ] );
			
			$this->nameTypes[$type]=$d[0]['id'];
		}				



        $d = $this->models->Taxa->_get(array('id' =>
			array(
				'project_id' => $this->getNewProjectId(),
				'taxon' => $this->_defaultKingdom
			)));

		if ($d)
		{
			$kingdomId = $d[0]['id'];
		} 
		else
		{
			// default kingdom
			$this->models->Taxa->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'taxon' => $this->_defaultKingdom,
				'parent_id' => 'null',
				'rank_id' => $_SESSION['admin']['system']['import']['project']['ranks']['kingdom'],
				'taxon_order' => 0,
				'is_hybrid' => 0,
				'list_level' => 0
			));

			$kingdomId = $this->models->Taxa->getNewId();
		}

        $i = 1;

		// save all taxa
        foreach ((array) $species as $key => $val)
		{

			if ( $val['isMatrix'] )
			{
				$d=$this->createMatrixIfNotExists($key);
				$species[$key]['lng_id'] = $d['id'];
				if ($d['type']=='new') $this->addMessage(sprintf('Added new referenced matrix "%s" (name only)',$key));

				foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey=>$cVal)
				{
					if (!empty($cVal) && isset($val[$cKey]))
					{
						$this->models->NbcExtras->save(
							array(
								'id' => null,
								'project_id' => $this->getNewProjectId(),
								'ref_id' => $species[$key]['lng_id'],
								'ref_type' => 'matrix',
								'name' => $cVal,
								'value' => $val[$cKey]
							));
					}
				}
				continue;
			}

			// getting the parent
			if ( isset($val['parent_name']) )
			{
				// find parent by sci name
				$d = $this->getTaxonByName($val['parent_name']);
				if (isset($d[0]['id']))
				{
					$parent = $d[0]['id'];
				}
				else
				{
					// find taxon by common name
					$d = $this->models->Names->_get(array('id' =>
					array(
						'project_id' => $this->getNewProjectId(),
						//'language_id' => $this->getNewDefaultLanguageId(),
						'name' => $val['parent_name']
					)));

					if ($d)
					{
						$parent = $d[0]['taxon_id'];
					} 
					else 
					{
						// create the parent taxon
						$this->models->Taxa->save(
						array(
							'id' => null,
							'project_id' => $this->getNewProjectId(),
							'taxon' => $val['parent_name'],
							'parent_id' => $kingdomId,
							'rank_id' => $_SESSION['admin']['system']['import']['project']['ranks']['family'],
							'taxon_order' => 0,
							'is_hybrid' => 0,
							'list_level' => 0
						));

						$parent = $this->models->Taxa->getNewId();
					}
				}
			} 
			else
			{
				// give the topmost the default uppermost parent
				$parent = $kingdomId;
			}

			// does taxon already exist?
			$d = $this->models->Taxa->_get(array('id' =>
				array(
					'project_id' => $this->getNewProjectId(),
					'parent_id' => $parent,
					'taxon' => $key
				)));

			if ($d)
			{
				$species[$key]['lng_id'] = $d[0]['id'];

				// cleaning up
				$this->models->NbcExtras->delete(
					array(
						'project_id' => $this->getNewProjectId(),
						'ref_id' => $species[$key]['lng_id'],
						'ref_type' => 'taxon'
					));
					
				$this->models->TaxaRelations->delete(array(
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $species[$key]['lng_id']
				));

				$tv=$this->models->TaxaVariations->_get(array('id'=>
					array(
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $species[$key]['lng_id']
					)));
					
				foreach((array)$tv as $bla)
				{
					$this->models->VariationsLabels->delete(
						array(
							'project_id' => $this->getNewProjectId(),
							'variation_id' => $bla['id'],
						));

					$this->models->NbcExtras->delete(
						array(
							'project_id' => $this->getNewProjectId(),
							'ref_id' => $bla['id'],
							'ref_type' => 'variation'
						));
				}

				$this->models->TaxaVariations->delete(array(
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $species[$key]['lng_id']
				));

				$this->models->Names->delete(array(
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $species[$key]['lng_id']
				));

			}
			else
			{
				// save new taxon
				$this->models->Taxa->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon' => $key,
					'parent_id' => $parent,
					'rank_id' => $_SESSION['admin']['system']['import']['project']['ranks']['species'],
					'taxon_order' => $i++,
					'is_hybrid' => 0,
					'list_level' => 0
				));

				$species[$key]['lng_id'] = $this->models->Taxa->getNewId();

			}

			$this->models->Names->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'taxon_id' => $species[$key]['lng_id'],
				'language_id' => $this->getNewDefaultLanguageId(),
				'name' =>$key,
				'type_id'=> $this->nameTypes[PREDICATE_VALID_NAME]
			));

			// if it's not a matrix and if NBC-data columns have been defined, save the NBC-data
			if (isset($_SESSION['admin']['system']['import']['data']['nbcColumns']))
			{
				foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey=>$cVal)
				{
					if (!empty($cVal) && isset($val[$cKey]))
					{
						$this->models->NbcExtras->save(
							array(
								'id' => null,
								'project_id' => $this->getNewProjectId(),
								'ref_id' => $species[$key]['lng_id'],
								'ref_type' => 'taxon',
								'name' => $cVal,
								'value' => $val[$cKey]
							));
					}
				}
			}

			$_SESSION['admin']['system']['import']['loaded']['species']++;

			if (isset($val['common name']))
			{
				$this->models->Names->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $species[$key]['lng_id'],
					'language_id' => $this->getNewDefaultLanguageId(),
					'name' => $val['common name'],
					'type_id'=> $this->nameTypes[PREDICATE_PREFERRED_NAME]
				));
			}

			// if there's variations, save those as well
			if (isset($val['variations']))
			{
				foreach ((array) $val['variations'] as $vKey => $vVal)
				{
					if (empty($vVal['variant']))
					{
						$this->addError($this->storeError(sprintf($this->translate('Skipping variation without variant name ("%s"; double entry?).'),$vKey), 'Species import'));
						continue;
					}

					$this->models->TaxaVariations->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $species[$key]['lng_id'],
						'label' => $vVal['variant']
					));

					$vId = $species[$key]['variations'][$vKey]['lng_id'] = $this->models->TaxaVariations->getNewId();

					$this->models->VariationsLabels->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'variation_id' => $vId,
						'language_id' => $this->getNewDefaultLanguageId(),
						'label' => $vVal['variant'],
						'label_type' => 'alternative'
					));

					if (isset($_SESSION['admin']['system']['import']['data']['nbcColumns']))
					{
						foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey => $cVal)
						{
							if (isset($vVal[$cKey]))
							{
								$this->models->NbcExtras->save(
									array(
										'id' => null,
										'project_id' => $this->getNewProjectId(),
										'ref_id' => $vId,
										'ref_type' => 'variation',
										'name' => $cVal,
										'value' => $vVal[$cKey]
									));
							}
						}
					}

					$_SESSION['admin']['system']['import']['loaded']['variations']++;

					$tmpIndex[$vVal['id']] = array(
						'type' => 'var',
						'id' => $vId,
						'name' => [ strtolower($vVal['variant']) ] // for Dierenzoeker
					);
				}
			} 
			else
			{
				$tmpIndex[$val['id']] = array(
					'type' => 'sp',
					'id' => $species[$key]['lng_id'],
					'name' => [ strtolower($key), strtolower(@$val['common name']), strtolower(@$val['common name']) ]
				);
			}
		}

		// save relations
        foreach ((array) $species as $key => $val)
		{
            if (!isset($val['variations']))
			{
                if (isset($val['related']))
				{
                    foreach ((array) $val['related'] as $rKey => $rVal)
					{
						$rValId = $this->resolveSimilarIdentifier($rVal,$tmpIndex);

                        if (!isset($tmpIndex[$rValId]) || $val['lng_id']==$tmpIndex[$rValId]['id'])
                            continue;

                        $d = $this->models->TaxaRelations->_get(array('id' =>
							array(
								'project_id' => $this->getNewProjectId(),
								'taxon_id' => $val['lng_id'],
								'relation_id' => $tmpIndex[$rValId]['id'],
								'ref_type' => $tmpIndex[$rValId]['type'] == 'var' ? 'variation' : 'taxon'
							)));

						if (!$d)
						{
							$this->models->TaxaRelations->save(
							array(
								'id' => null,
								'project_id' => $this->getNewProjectId(),
								'taxon_id' => $val['lng_id'],
								'relation_id' => $tmpIndex[$rValId]['id'],
								'ref_type' => $tmpIndex[$rValId]['type'] == 'var' ? 'variation' : 'taxon'
							));
						}
                    }
                }
            }
            else
			{
                foreach ((array) $val['variations'] as $vKey => $vVal)
				{
					
					$this->models->TaxaRelations->delete(
						array(
							'project_id' => $this->getNewProjectId(),
							'taxon_id' => $vVal['lng_id'],
							'ref_type' => 'variation'
						));
	
					
                    if (isset($vVal['related']))
					{
                        foreach ((array) $vVal['related'] as $rKey => $rVal)
						{
							$rValId = $this->resolveSimilarIdentifier($rVal,$tmpIndex);

                            if (!isset($tmpIndex[$rValId]) || !isset($vVal['lng_id']) || $vVal['lng_id']==$tmpIndex[$rValId]['id'])
                                continue;

                            $d = $this->models->VariationRelations->_get(array('id'=>
                            array(
                                'project_id' => $this->getNewProjectId(),
                                'variation_id' => $vVal['lng_id'],
                                'relation_id' => $tmpIndex[$rValId]['id'],
                                'ref_type' => $tmpIndex[$rValId]['type'] == 'var' ? 'variation' : 'taxon'
                            )));

							if (!$d)
							{
								$this->models->VariationRelations->save(
								array(
									'id' => null,
									'project_id' => $this->getNewProjectId(),
									'variation_id' => $vVal['lng_id'],
									'relation_id' => $tmpIndex[$rValId]['id'],
									'ref_type' => $tmpIndex[$rValId]['type'] == 'var' ? 'variation' : 'taxon'
								));
							}
                        }
                    }
                }
            }
        }

        return $species;
    }

	private function getExistingMatrixId($name)
	{

        $d = $this->models->MatricesNames->_get(
			array(
				'id' => array(
					'project_id' => $this->getNewProjectId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'name' => $name
				)
			)
		);

		return isset($d[0]['matrix_id']) ? $d[0]['matrix_id'] : null;

	}

    private function createMatrixIfNotExists($name)
    {

        $d=$this->models->MatricesNames->_get(array('id' =>
        array(
            'project_id' => $this->getNewProjectId(),
            'language_id' => $this->getNewDefaultLanguageId(),
            'name' => $name
        )));

		if (!empty($d[0]['matrix_id']))
			  return
			  	array(
					'id' => $d[0]['matrix_id'],
					'type' => 'existing',
					'name' => $name
				);

        $this->models->Matrices->save(array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
			'sys_name' => $name
        ));

        $id = $this->models->Matrices->getNewId();

        $this->models->MatricesNames->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'matrix_id' => $id,
            'language_id' => $this->getNewDefaultLanguageId(),
            'name' => $name
        ));

	  return array('id' => $id, 'type' => 'new', 'name' => $name);

    }

    private function storeCharacterGroups($data, $mId)
    {
        $_SESSION['admin']['system']['import']['loaded']['chargroups'] = 0;

        $d = array();

        $i = 0;

        foreach ((array) $data['characters'] as $key => $val) {

            if (!empty($val['group']) && $val['group'] != 'hidden' && !isset($d[$val['group']])) {

                $c = $this->models->Chargroups->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getNewProjectId(),
                        'matrix_id' => $mId,
                        'label' => $val['group']
                    )
                ));

                if (!is_null($c)) {

                    $d[$val['group']] = $c[0]['id'];
                }
                else {

                    $this->models->Chargroups->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'matrix_id' => $mId,
                        'label' => $val['group'],
                        'show_order' => $i++
                    ));

                    $d[$val['group']] = $this->models->Chargroups->getNewId();

                    $_SESSION['admin']['system']['import']['loaded']['chargroups']++;

                    $this->models->ChargroupsLabels->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'chargroup_id' => $d[$val['group']],
                        'language_id' => $this->getNewDefaultLanguageId(),
                        'label' => $val['group']
                    ));
                }
            }
        }

        $data['characterGroups'] = $d;

        return $data;
    }

    private function storeCharacters($data, $mId, $types)
    {
        $showOrder1 = $showOrder2 = $_SESSION['admin']['system']['import']['loaded']['characters'] = 0;

        foreach ((array) $data['characters'] as $cKey => $cVal) {

            if (isset($cVal['group']) && $cVal['group'] == 'hidden')
                continue;

            $type = isset($types[$cVal['code']]) ? $types[$cVal['code']] : 'media';

            $this->models->Characteristics->save(array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'type' => $type,
                'sys_name' => $cVal['code']
            ));

            $data['characters'][$cKey]['id'] = $this->models->Characteristics->getNewId();
            $data['characters'][$cKey]['type'] = $type;

            $this->models->CharacteristicsLabels->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'characteristic_id' => $data['characters'][$cKey]['id'],
                'language_id' => $this->getNewDefaultLanguageId(),
                'label' => $cVal['code'].(isset($cVal['instruction']) ? '|'.$cVal['instruction'] : null)

            ));

            $this->models->CharacteristicsMatrices->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'matrix_id' => $mId,
                'characteristic_id' => $data['characters'][$cKey]['id'],
                'show_order' => $showOrder1++
            ));

            if (isset($cVal['group']) && $cVal['group'] !== 'hidden') {

                $this->models->CharacteristicsChargroups->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'characteristic_id' => $data['characters'][$cKey]['id'],
                    'chargroup_id' => $data['characterGroups'][$cVal['group']],
                    'show_order' => $showOrder2++
                ));
            }

            $_SESSION['admin']['system']['import']['loaded']['characters']++;
        }

        return $data;
    }

    private function translateStateCode($code, $languageId)
    {
        $translations = array(
            'male' => array(
                LANGUAGECODE_DUTCH => 'mannelijk',
                LANGUAGECODE_ENGLISH => 'male'
            ),
            'female' => array(
                LANGUAGECODE_DUTCH => 'vrouwelijk',
                LANGUAGECODE_ENGLISH => 'female'
            )
        );

        return isset($translations[$code][$languageId]) ? $translations[$code][$languageId] : $code;
    }

    private function storeStates($data)
    {

        $_SESSION['admin']['system']['import']['loaded']['states'] = 0;

        $states = array();

        foreach ((array)$data['species'] as $sVal)
		{
			if (!isset($sVal['states']))
			{
				$this->addError($this->storeError(sprintf($this->translate('Found no states for "%s"'),$sVal['label']), 'Matrix states'));
				continue;
			}

            foreach ((array)$sVal['states'] as $key => $val)
			{
                foreach ((array) $val as $cKey => $cVal)
				{
					$cVal=trim($cVal);

                    if (isset($states[$key][$cVal]))
                        continue;

                    if (empty($cVal) && !is_numeric($cVal))
                        continue;

					if (!isset($data['characters'][$key]['id']) || !isset($data['characters'][$key]['type']))
						continue;

                    $cId = $data['characters'][$key]['id'];
                    $type = $data['characters'][$key]['type'];

                    if ($type == 'range')
					{
                        if (strpos($cVal, '-'))
						{
                            $d = explode('-', $cVal);

                            $statemin = (int) $d[0];
                            $statemax = (int) $d[1];
                        }
                        else
						{
                            $statemin = $statemax = (int) $cVal;
                        }
                    }

                    $this->models->CharacteristicsStates->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'characteristic_id' => $cId,
                        'lower' => isset($statemin) ? $statemin : null,
                        'upper' => isset($statemax) ? $statemax : null,
						'sys_name' => $cVal
                        //'file_name' => $type == 'media' ? $cVal . '.' . $this->_defaultImgExtension : null
                    ));

                    unset($statemin);
                    unset($statemax);

                    $states[$key][$cVal] = $this->models->CharacteristicsStates->getNewId();

					if (!empty($val))
					{
						$this->models->CharacteristicsLabelsStates->save(
						array(
							'id' => null,
							'project_id' => $this->getNewProjectId(),
							'state_id' => $states[$key][$cVal],
							'language_id' => $this->getNewDefaultLanguageId(),
							'label' => $this->translateStateCode($cVal, $this->getNewDefaultLanguageId())
						));
					}

                    $_SESSION['admin']['system']['import']['loaded']['states']++;
                }
            }
        }

        $data['states'] = $states;

        return $data;
    }

    private function storeVariationStateConnections($taxa, $mData, $mId)
    {
        $_SESSION['admin']['system']['import']['loaded']['connections'] = 0;

        foreach ((array) $taxa as $tVal) {

            if (isset($tVal['variations'])) {

                foreach ((array) $tVal['variations'] as $val) {

                    $this->models->MatricesVariations->setNoKeyViolationLogging(true);

                    $this->models->MatricesVariations->save(
                    array(
                        'project_id' => $this->getNewProjectId(),
                        'matrix_id' => $mId,
                        'variation_id' => $val['lng_id']
                    ));

                    if (isset($val['states'])) {

                        foreach ((array) $val['states'] as $sKey => $sVal) {

                            foreach ((array) $sVal as $state) {

                                //$this->models->MatrixTaxonState->setNoKeyViolationLogging(true);

                                @$this->models->MatricesTaxaStates->save(
                                array(
                                    'project_id' => $this->getNewProjectId(),
                                    'matrix_id' => $mId,
                                    'characteristic_id' => $mData['characters'][$sKey]['id'],
                                    'state_id' => $mData['states'][$sKey][$state],
                                    'variation_id' => $val['lng_id']
                                ));

                                $_SESSION['admin']['system']['import']['loaded']['connections']++;
                            }
                        }
                    }
                }
            }
            else {

                $this->models->MatricesTaxa->setNoKeyViolationLogging(true);

				if ($tVal['isMatrix']==false) {

					$this->models->MatricesTaxa->save(array(
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $mId,
						'taxon_id' => $tVal['lng_id']
					));

				}

                if (isset($tVal['states'])) {

                    foreach ((array) $tVal['states'] as $sKey => $sVal) {

                        foreach ((array) $sVal as $state) {

							if (!isset($mData['characters'][$sKey]['id']))
								continue;

                            $this->models->MatricesTaxaStates->setNoKeyViolationLogging(true);

								$d=array(
									'project_id' => $this->getNewProjectId(),
									'matrix_id' => $mId,
									'characteristic_id' => $mData['characters'][$sKey]['id'],
									'state_id' => $mData['states'][$sKey][$state]
								);

								if ($tVal['isMatrix']==false)
									$d['taxon_id']=$tVal['lng_id'];
								else
									$d['ref_matrix_id']=$tVal['lng_id'];

								$this->models->MatricesTaxaStates->save($d);

                            $_SESSION['admin']['system']['import']['loaded']['connections']++;
                        }
                    }
                }
            }
        }
    }

	private function extractVariantColumns($data)
	{
		$d = array();

		foreach((array)$data['characters'] as $key => $val) {

			if (isset($val['group']) && $val['group']=='hidden') {

				array_push($d,array('id'=>$key,'code'=>$val['code'],'label'=>$val['code']));

			}
		}

		return $d;

	}

    private function parseLabelData($raw)
    {
        $data = array();

        foreach ((array) $raw as $line => $val) {

            $lineHasData = strlen(implode('', $val)) > 0;

            if ($lineHasData) {

                foreach ((array) $val as $cKey => $cVal) {

					$cVal=trim($cVal,chr(239).chr(187).chr(191).chr(9).chr(32).chr(10).chr(13));
					// that's BOM, tab, space, returns

                    if ($cKey<5) {

                        // line 0, cell 0: title
                        if ($line == 0 && $cKey == 0)
                            $data['project']['title'] = $cVal;
                        // line 1, cell 0: group
                        if ($line == 1 && $cKey == 0)
                            $data['project']['soortgroep'] = $cVal;
                        // line 2, cell 0: matrix (optional)
                        if ($line == 2 && $cKey == 0)
                            $data['project']['matrix']['label'] = $cVal;

						// line > 3: labels etc
                        if ($line > 3) {

							$data['states'][$line][$cKey] = $cVal;

                        }
                    }
                }
            }
        }

        return $data;
    }

	private function doDownload()
	{
		define('FIELD_SEP',',');

        header('Content-disposition:attachment;filename=combined-ids--'.
			strtolower(
				preg_replace(
					'/\W/',
					'',
					$_SESSION['admin']['system']['import']['data']['project']['title']
				).
				'--'.
				str_replace('.','-',$_SERVER['SERVER_NAME'])
			).
			'.csv'
		);

        header('Content-type:text/csv');

		$t = $v = '';

		foreach((array)$_SESSION['admin']['system']['import']['species_data'] as $val) {

			$t .= $val['id'].FIELD_SEP.'"'.$val['common name'].'"'.FIELD_SEP.$val['lng_id'].chr(10);

			if (!isset($val['variations'])) continue;

			foreach((array)$val['variations'] as $vVal) {

				$v .= $vVal['id'].FIELD_SEP.'"'.$vVal['label'].'"'.FIELD_SEP.$vVal['lng_id'].FIELD_SEP.$val['lng_id'].chr(10);

			}

		}

		echo 'TAXA'.chr(10);
		echo 'id'.FIELD_SEP.'common_name'.FIELD_SEP.'lng_id'.chr(10);
		echo $t;
		echo chr(10).chr(10);
		echo 'VARIATIONS'.chr(10);
		echo 'id'.FIELD_SEP.'label'.FIELD_SEP.'lng_id'.FIELD_SEP.'taxon_id'.chr(10);
		echo $v;
	}

    private function storeError($err, $mod)
    {

        $_SESSION['admin']['system']['import']['errorlog']['errors'][] = array(
            $mod,
            $err
        );

        return $err;
    }

    private function downloadErrorLog()
    {
        header('Content-disposition:attachment;filename=import-log--'.
			strtolower(
				preg_replace(
					'/\W/',
					'',
					$_SESSION['admin']['system']['import']['data']['project']['title']
				).
				'--'.
				str_replace('.','-',$_SERVER['SERVER_NAME'])
			).
			'.log'
		);

        header('Content-type:text/txt');

        echo 'project: ' . $_SESSION['admin']['system']['import']['data']['project']['title'] . chr(10);
        echo 'created: ' . date('c') . chr(10);
		echo 'server: '. $_SERVER['SERVER_NAME'] . chr(10);
        echo '--------------------------------------------------------------------------------' . chr(10);

        $prevMod = null;

		if ($_SESSION['admin']['system']['import']['errorlog']) {

			foreach ((array) $_SESSION['admin']['system']['import']['errorlog']['errors'] as $val) {

				$mod = @strtolower($val[0]);

				if ($mod !== $prevMod) {

					if (!is_null($prevMod))
						echo chr(10);

					echo 'while loading ' . $mod . ':' . chr(10);
				}

				echo strip_tags($val[1]) . chr(10);

				$prevMod = $mod;
			}

		}

        die();
    }

    public function deleteTaxon($id,$pId,$deleteMedia=true)
	{
		if (!$id) return;

		$this->models->L2OccurrencesTaxa->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));
		$this->models->L2OccurrencesTaxaCombi->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));
		$this->models->MatricesTaxaStates->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));
		$this->models->MatricesTaxa->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));
		$this->models->OccurrencesTaxa->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));
		$this->models->TaxaRelations->delete(array(
			'project_id' => $pId,
			'taxon_id' => $id
		));

		$tv=$this->models->TaxaVariations->get( [ 'id' => [ 'project_id' => $pId, 'taxon_id' => $id ] ] );

		foreach((array)$tv as $key => $val)
		{
			$this->models->VariationsLabels->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->VariationRelations->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->MatricesVariations->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->NbcExtras->delete(array(
				'project_id' => $pId,
				'ref_type' => 'variation',
				'ref_id' => $val['id']
			));
			$this->models->TaxaVariations->delete(array(
				'project_id' => $pId,
				'id' => $val['id']
			));
		}

		$this->models->NbcExtras->delete(array(
			'project_id' => $pId,
			'ref_type' => 'taxon',
			'ref_id' => $id
		));

        // delete literary references
        $this->models->LiteratureTaxa->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));

        // reset keychoice end-points
        $this->models->ChoicesKeysteps->update(array(
            'res_taxon_id' => 'null'
        ), array(
            'project_id' => $pId,
            'res_taxon_id' => $id
        ));

        // delete commonnames
        $this->models->Names->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));

        // delete synonyms
        $this->models->Synonyms->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
/*
        // purge undo
        $this->models->ContentTaxonUndo->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
*/
        // delete taxon tree branch rights
        $this->models->UsersTaxa->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));

        // detele media
        $mt = $this->models->MediaTaxon->_get(array(
            'id' => array(
                'project_id' => $pId,
                'taxon_id' => $id
            )
        ));

		if ($deleteMedia) {

			foreach ((array) $mt as $key => $val) {

				$this->deleteTaxonMedia($val['id'], false);
			}

		}

        // reset parentage
        $this->models->Taxa->update(array(
            'parent_id' => 'null'
        ), array(
            'project_id' => $pId,
            'parent_id' => $id
        ));

        // delete content
        $this->models->ContentTaxa->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id,
        ));

        // delete taxon
        $this->models->Taxa->delete(array(
            'project_id' => $pId,
            'id' => $id,
        ));

    }
}