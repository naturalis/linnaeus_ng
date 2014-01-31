<?php

/*

	column headers, like 'naam SCI', are hardcoded (see array '_nbcColumns' below)! 

	$_stdVariantColumns needs to be user invulbaarable --> NOT! assuming sekse and variant (pwah)

	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'suppress_splash',1,now(),CURRENT_TIMESTAMP);
	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'start_page','/kreeften/app/views/matrixkey/identify.php',now(),CURRENT_TIMESTAMP);

*/

include_once ('Controller.php');
include_once ('ProjectDeleteController.php');
include_once ('SpeciesController.php');
include_once ('ProjectsController.php');
include_once ('ImportController.php');

class ImportNBCController extends ImportController
{

    private $_delimiter = ',';
    private $_encloser = '"';
    private $_valueSep = ';'; // used to separate values within a single cell
    private $_defaultKingdom = 'Animalia';
    private $_defaultLanguageId = 24; // dutch, hardcoded
    private $_defaultImgExtension = 'jpg';
    private $_defaultSkinName = 'nbc_default';
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
	
    public $usedModels = array(
        'commonname', 
        'media_taxon', 
        'media_descriptions_taxon', 
        'literature', 
        'literature_taxon', 
        'matrix', 
        'matrix_name', 
        'matrix_taxon', 
        'matrix_taxon_state', 
        'characteristic', 
        'characteristic_matrix', 
        'characteristic_label', 
        'characteristic_state', 
        'characteristic_label_state', 
        'chargroup_label', 
        'chargroup', 
        'characteristic_chargroup', 
        'variation_label', 
        'taxon_variation', 
        'taxa_relations', 
        'variation_relations', 
        'matrix_variation', 
        'nbc_extras', 
		'gui_menu_order',
		'content_taxon', 
		'content_taxon_undo', 
		'user_taxon', 
		'variation_label', 
		'variation_relations', 
		'synonym', 
		'taxa_relations', 
		'taxon_variation',
		'commonname', 
		'l2_occurrence_taxon',
		'l2_occurrence_taxon_combi',
		'literature_taxon', 
		'matrix_taxon', 
		'matrix_taxon_state', 
		'matrix_variation', 
		'media_descriptions_taxon', 
		'media_taxon', 
		'nbc_extras',
		'occurrence_taxon', 
		'choice_keystep', 
    );
    public $usedHelpers = array(
        'file_upload_helper'
    );
    public $controllerPublicName = 'NBC multi-entry key import';
    public $cssToLoad = array();
    public $jsToLoad = array();
	

    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();
		
		define('MATRIX_SCIENTIFIC_NAME_STUB','(matrix)');
        
		$this->_defaultLanguageId = LANGUAGECODE_DUTCH;

        $this->setBreadcrumbRootName($this->translate($this->controllerPublicName));
        
        $this->setSuppressProjectInBreadcrumbs();
		
		$this->isAuthorisedForImport();
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
    public function indexAction ()
    {
        $this->setPageName($this->translate('Data import options'));
        
        $this->printPage();
    }


    public function nbcDeterminatie1Action ()
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


    public function nbcDeterminatie2Action ()
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

		if (isset($data['project']['title'])) {

			$d = $this->models->Project->_get(array(
				'id' => array(
				'sys_name' => strtolower($data['project']['title'])
			)));

			$exists = ($d!=false);

			$this->smarty->assign('exists',$exists);
			
			if ($exists) {

				$_SESSION['admin']['system']['import']['existingProjectId'] = $d[0]['id'];

				$i=1;
				while ($d!=false) {
					$suggestedTitle = $data['project']['title'].' ('.$i++.')';
					$d = $this->models->Project->_get(array(
							'id' => array(
							'sys_name' => $suggestedTitle
					)));
				}

				$this->smarty->assign('suggestedTitle',$suggestedTitle);

				$_SESSION['admin']['system']['import']['projectExists'] = true;
				$_SESSION['admin']['system']['import']['newProjectTitle'] = $suggestedTitle;
				
			} else {
				
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


    public function nbcDeterminatie3Action ()
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
					'css_url' => $this->controllerSettings['defaultProjectCss'],
					'group' => $pGroup,
					'published' => 1
				));

				$this->addMessage($this->storeError('Created project "' . $pTitle . '" with id ' . $pId . '.','Project'));
				
				$_SESSION['admin']['system']['import']['project'] = array(
					'id' => $pId, 
					'title' => $pTitle
				);
				
				$pId = $this->getNewProjectId();

				$this->models->LanguageProject->save(
				array(
					'id' => null, 
					'language_id' => $this->getNewDefaultLanguageId(), 
					'project_id' => $pId, 
					'def_language' => 1, 
					'active' => 'y', 
					'tranlation_status' => 1
				));
				
				$this->addMessage('Added default language.');
				
			} 
			// use an existing project
			else {

				$pId = $_SESSION['admin']['system']['import']['project']['id'] = $_SESSION['admin']['system']['import']['existingProjectId'];
				$_SESSION['admin']['system']['import']['project']['title'] = $_SESSION['admin']['system']['import']['data']['project']['title'];
				$this->addMessage($this->storeError('Using project "' . $_SESSION['admin']['system']['import']['data']['project']['title'] . '" with id ' . $pId . '.','Project'));
				
			}

			$this->addModuleToProject(MODCODE_SPECIES, $pId, 0);
			$this->grantModuleAccessRights(MODCODE_SPECIES, $pId);
			
			$this->addModuleToProject(MODCODE_HIGHERTAXA, $pId, 0);
			$this->grantModuleAccessRights(MODCODE_HIGHERTAXA, $pId);
			
			$this->addModuleToProject(MODCODE_MATRIXKEY, $pId, 1);
			$this->grantModuleAccessRights(MODCODE_MATRIXKEY, $pId);

            $this->addUserToProjectAsLeadExpert($pId);
			
            $this->addMessage('Added current user as lead expert to project.');


			if ($this->rHasVal('action')) {

				$_SESSION['admin']['system']['import']['existingProjectTreatment'] = $this->requestData['action'];

				if ($this->rHasVal('action','replace_data') || $this->rHasVal('action','replace_species_data')) {

					$pDel = new ProjectDeleteController;
					$pDel->deleteMatrices($pId,true);

					if ($this->rHasVal('action','replace_data')) {

						$pDel->deleteNBCKeydata($pId);
						$pDel->deleteCommonnames($pId);
						$pDel->deleteSynonyms($pId);
						$pDel->deleteSpeciesMedia($pId,true);
						$pDel->deleteSpeciesContent($pId,false);
						$pDel->deleteSpecies($pId);
						$pDel->deleteProjectRanks($pId);
						
					} else
					if ($this->rHasVal('action','replace_species_data')) {

						$pr = $this->models->ProjectRank->_get(array(
							'id' => array(
								'project_id' => $pId, 
								'rank_id' => SPECIES_RANK_ID
							)
						));

						$taxa = $this->models->Taxon->_get(array(
							'id' => array(
								'project_id' => $pId,
								'rank_id' => $pr[0]['id']
							)
						));
						
						$d=0;

						$pDel->deleteMatrices($pId,true);

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


    public function nbcDeterminatie4Action ()
    {
		
        if (!isset($_SESSION['admin']['system']['import']['project']))
            $this->redirect('nbc_determinatie_3.php');

        $this->setPageName($this->translate('Storing ranks, species and variations'));

        if ($this->rHasVal('action', 'save') && !$this->isFormResubmit()) {
            
			if ($this->rHasVal('nbcColumns'))
				$_SESSION['admin']['system']['import']['data']['nbcColumns'] = $this->requestData['nbcColumns'];

            $ranks = $this->addRanks();
            
            $_SESSION['admin']['system']['import']['project']['ranks'] = $ranks;
            
            if ($this->rHasVal('variant_columns'))
				$_SESSION['admin']['system']['import']['variantColumns'] = $this->requestData['variant_columns'];

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


    public function nbcDeterminatie5Action ()
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

					$this->models->MatrixTaxonState->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'matrix_id' => $mId
					));
					$this->models->MatrixTaxon->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'matrix_id' => $mId
					));
					$this->models->MatrixVariation->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'matrix_id' => $mId
					));
					$this->models->CharacteristicMatrix->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'matrix_id' => $mId
					));

					$l = $this->models->CharacteristicMatrix->_get(array(
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
					
					$this->models->CharacteristicLabelState->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicState->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicChargroup->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->CharacteristicLabel->delete(array(
						'project_id' => $this->getNewProjectId(), 
						'characteristic_id not in#' => '('.$d.')'
					));
					$this->models->Characteristic->delete(array(
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
            
            $data = $this->storeCharacters($data, $mId, $this->requestData['char_type']);
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
				$d = $this->models->MatrixName->_get(array(
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
        $this->smarty->assign('skin', $this->_defaultSkinName);
        $this->smarty->assign('matrix_state_image_per_row', 4);
        $this->smarty->assign('matrix_state_image_max_height', 300);
        $this->smarty->assign('matrix_items_per_page', 16);
        $this->smarty->assign('matrix_items_per_line', 4);
        $this->smarty->assign('matrix_use_sc_as_weight', 1);
        $this->smarty->assign('matrix_browse_style', 'expand');	//paginate|expand
        $this->smarty->assign('nbc_image_root', '../../media/system/skins/'.$this->_defaultSkinName.'/');
        
        $this->printPage();
    }


    public function nbcDeterminatie6Action ()
    {
	
		if ($this->rHasVal('action','download')) {
			$this->doDownload();
			die();
		}
		if ($this->rHasVal('action','errorlog')) {
			$this->downloadErrorLog();
			die();
		}

        $this->setPageName($this->translate('Import finished'));

		if (!$_SESSION['admin']['system']['import']['projectExists']) {

			/*
			$this->addModuleToProject(MODCODE_SPECIES, $this->getNewProjectId(), 0);
			$this->grantModuleAccessRights(MODCODE_SPECIES, $this->getNewProjectId());
			
			$this->addModuleToProject(MODCODE_HIGHERTAXA, $this->getNewProjectId(), 0);
			$this->grantModuleAccessRights(MODCODE_HIGHERTAXA, $this->getNewProjectId());
			
			$this->addModuleToProject(MODCODE_MATRIXKEY, $this->getNewProjectId(), 1);
			$this->grantModuleAccessRights(MODCODE_MATRIXKEY, $this->getNewProjectId());
			*/

			$settings = array(
				'matrixtype' => 'nbc',
				'matrix_allow_empty_species' => true,
				'matrix_use_character_groups' => true,
				'taxa_use_variations' => true,
			);
			
			foreach((array)$settings as $key => $val) {
				
				if (!empty($val))
					$this->saveSetting(array(
						'name' => $key, 
						'value' => $val, 
						'pId' => $this->getNewProjectId()
					));			
			}
	
			foreach((array)$this->requestData['settings'] as $key => $val) {
				
				if (!empty($val))
					$this->saveSetting(array(
						'name' => $key, 
						'value' => $val, 
						'pId' => $this->getNewProjectId()
					));			
			}
			
			
			if (empty($this->requestData['settings']['skin'])) {
	
				$this->saveSetting(array(
					'name' => 'skin', 
					'value' => $this->_defaultSkinName, 
					'pId' => $this->getNewProjectId()
				));
				
			}
			
		} else  {
			
			$this->addMessage('Existing project, skipping settings.');
			
		}

        $this->printPage();

    }


    public function nbcDeterminatie7Action ()
    {
        $this->unsetProjectSessionData();
        $this->setCurrentProjectId($this->getNewProjectId());
        $this->setCurrentProjectData();
        $this->reInitUserRolesAndRights();
        $this->setCurrentUserRoleId();

        $this->emptyCacheFolder($this->getCurrentProjectId());  // does this even work?
		
        unset($_SESSION['admin']['system']['import']);
        unset($_SESSION['admin']['project']['ranks']);
        
        $this->redirect($this->getLoggedInMainIndex());
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

			$d = $this->models->Project->_get(array(
					'id' => array(
					'sys_name' => $data['project']['title']
				)));
				
			//q($this->models->Project->q());

            $this->smarty->assign('exists',$d!=false);

		}
		
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
        
        if (!$this->isFormResubmit()) {

            $pTitle = $_SESSION['admin']['system']['import']['data']['project']['title'];
			//$pGroup = $_SESSION['admin']['system']['import']['data']['project']['soortgroep'];
			
			$d = $this->models->Project->_get(array(
				'id' => array(
				'sys_name' => $pTitle
			)));
			
			if (!empty($d[0]['id'])) {
				
				$pId = $d[0]['id'];
				
				$dummy = array();
				
				$thisLanguage=$this->getNewDefaultLanguageId($pId);

				foreach((array)$_SESSION['admin']['system']['import']['data']['states'] as $val) {
					
					if (empty($val[0])||empty($val[1]))
						continue;

					// get the character that matches the 'kenmerk'-label (col 2; col 1 is the group, which is ignored here)
					$d = $this->models->CharacteristicLabel->_get(
						array(
							'where' =>
								'project_id  = '.$pId. ' and
								language_id = '.$thisLanguage. ' and
								(
									lower(label) = \''. mysql_real_escape_string(strtolower($val[1])) .'\' or
									label like \''. mysql_real_escape_string(strtolower($val[1])) .'|%\'
								)'
						)
					);	


					// warning, UGLY code ahead
					if ($d && count((array)$d)>1) {
						
						/*
							two (or more) characters can exist with the same name, but not
							in the same group (although this is enforced nowhere).
						*/
					
						$somethingElse=array();
						foreach((array)$d as $x) {
							$somethingElse[]=$x['characteristic_id'];
						}
						
						$something=array();
						foreach((array)$this->models->ChargroupLabel->_get(
							array('id'=>
								array(
									'project_id' => $pId, 
									'language_id' => $thisLanguage, 
									'label' => $val[0]				
								)
							)
						) as $x) {
							$something[]=$x['chargroup_id'];
						}
					
						$d=$this->models->CharacteristicChargroup->_get(
							array('id'=>
								array(
									'project_id' => $pId, 
									'characteristic_id in#' => '('.implode(',',$somethingElse).')',
									'chargroup_id in#' => '('.implode(',',$something).')'
								)
							)
						);	
					
						if ($d && count((array)$d)>1)
							$this->addError(
								$this->storeError(
									sprintf(
										$this->translate('There appear to be multiple characters with the name "%s", within the same group. Unable to make out which is which'),
										$val[1]
									),
									'Matrix states'
								)
							);
						
					}

					// if a char is found...					
					if (!empty($d[0]['characteristic_id'])) {

						$cId = $d[0]['characteristic_id'];

						// ...find all its states
						$states = $this->models->CharacteristicState->_get(array(
							'id' =>
								array(
									'project_id' => $pId, 
									'characteristic_id' => $cId, 
								),
							'columns' => 'id'
							)
						);
						
						$d = array();

						foreach((array)$states as $sVal)
							$d[]=$sVal['id'];
						
						$states = '('.implode(',',$d).')';

						// in those states, find one that matches the state's label (col 3)
						$l = $this->models->CharacteristicLabelState->_get(array(
							'id' =>
								array(
									'project_id' => $pId, 
									'state_id in' => $states, 
									'language_id' => $thisLanguage, 
									'label' => $val[2]
								)
							)
						);
						
						if (!empty($l[0]['state_id'])) {
							
							$sId = $l[0]['state_id'];
							
							// if there is a translation, save it
							if (!empty($val[3])) {

								$l = $this->models->CharacteristicLabelState->update(
									array(
										'label' => $val[3]
									),
									array(
										'project_id' => $pId, 
										'state_id' => $sId, 
										'language_id' => $thisLanguage
									)
								);								

								$this->addMessage($this->storeError(sprintf($this->translate('Saved translation "%s".'),$val[3]),'Matrix characters'));
								
							} else {

								//$this->addMessage(sprintf($this->translate('Skipped "%s" for %s (no translation).'),$val[2],$val[1]));

							}
	
							// if there is an image, save it
							if (!empty($val[4])) {

								$this->models->CharacteristicState->update(
									array(
										'file_name' => $val[4]
									),
									array(
										'project_id' => $pId, 
										'id' => $sId, 
									)
								);				

								$this->addMessage($this->storeError(sprintf($this->translate('Updated image for "%s" to \'%s\'.'),$val[2],$val[4]),'Matrix characters'));
		
								$dummy[$cId]['state'] = (!isset($dummy[$cId]['state']) ? 'all_images' : ($dummy[$cId]['state']=='all_images' ? 'all_images' : ($dummy[$cId]['state']=='no_images' ? 'partial_images' : 'partial_images' )));

								$dummy[$cId]['label'] = $val[1];

							} else {

								//$this->addMessage(sprintf($this->translate('Skipped image for "%s" (not specified).'),$val[2]));

								$dummy[$cId]['state'] = (!isset($dummy[$cId]['state']) ? 'no_images' : ($dummy[$cId]['state']=='all_images' ? 'partial_images' : ($dummy[$cId]['state']=='no_images' ? 'no_images' : 'partial_images' )));

								$dummy[$cId]['label'] = $val[1];

							}

						} else {

							$this->addError($this->storeError(sprintf($this->translate('Could not resolve state "%s" for "%s".'),$val[2],$val[1]),'Matrix states'));
							
						}

					} else {

			            $this->addError($this->storeError(sprintf($this->translate('Could not resolve character "%s".'),$val[1]),'Matrix states'));

					}
					

				}
				
				if ($this->rHasval('re_type_chars','all') || $this->rHasval('re_type_chars','partial')) {

					$this->addMessage($this->translate('Re-evaluating character types (using setting "'.($this->rHasval('re_type_chars','partial') ? 'need some' : 'need all' ).'").'));
	
					foreach((array)$dummy as $cId => $char) {
						
						$type = 
							(($this->rHasval('re_type_chars','partial') && ($char['state']=='all_images' || $char['state'] =='partial_images')) ||
							($this->rHasval('re_type_chars','all') && $char['state']=='all_images')) ? 'media' : 'text';
						
						$this->models->Characteristic->update(array(
							'type' => $type
						), array(
							'id' => $cId, 
							'project_id' => $pId
						));
						
						$this->addMessage($this->storeError(sprintf($this->translate('Set character type for "%s" to %s.'),$char['label'],$type),'Matrix characters'));
						
					}

				} else {
					
					$this->addMessage($this->storeError($this->translate('Skipped re-evaluating character types.'),'Matrix characters'));
					
				}
			
				
			} else {
				
	            $this->addError(sprintf($this->translate('Project "%" not found in the database.'),$pTitle));
	            $this->addError($this->translate('Import halted.'));
				
			}

			unset($_SESSION['admin']['system']['import']);

			$this->addMessage($this->translate('Done.'));

        }
        
        $this->printPage();
    }


    private function getNewProjectId()
    {
        return (isset($_SESSION['admin']['system']['import']['project']['id'])) ? $_SESSION['admin']['system']['import']['project']['id'] : null;
    }

    private function getNewDefaultLanguageId($pId=null)
    {
		
		$pId=is_null($pId) ? $this->getNewProjectId() : $pId;
		
        $lp = $this->models->LanguageProject->_get(array(
            'id' => array(
                'project_id' => $pId,
				'def_language' => 1
            )
        ));

		if ($lp)
			return $lp[0]['language_id'];

        $lp = $this->models->LanguageProject->_get(array(
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

    private function parseData ($raw)
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
                    	if ($line==3 && $cKey>4 && !empty($cVal)) // && $cVal!='…')
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
								array_walk($data['species'][$line]['related'], create_function('&$val', '$val = trim($val);'));
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
									array_walk($data['species'][$line]['states'][$cKey], create_function('&$val', '$val = trim($val);'));
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

        $d = $this->models->ProjectRank->_get(array('id' =>
			array(
				'project_id' => $this->getNewProjectId(), 
				'rank_id' => $rankId, 
			)));

		if ($d) {
			
			$id = $d[0]['id'];
			
		} else {

			$this->models->ProjectRank->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(), 
				'rank_id' => $rankId, 
				'parent_id' => isset($parentId) ? $parentId : null, 
				'lower_taxon' => $isLower ? '1' : '0'
			));
			
			$id = $this->models->ProjectRank->getNewId();
			
			$this->models->LabelProjectRank->save(
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
        $r = $this->models->Rank->_get(array(
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
            if ($val['id'] == KINGDOM_RANK_ID)
                $projectKingdomId = $id;
            if ($val['id'] == FAMILY_RANK_ID)
                $projectFamilyId = $id;
				
        }
        
        return array(
            'kingdom' => $projectKingdomId, 
            'species' => $projectSpeciesId,
            'family' => $projectFamilyId
        );
    }

    private function resolveSpeciesAndVariationsAndMatrices ($data)
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
		
		if (empty($id))
			return;

		if (is_numeric($id))
			return $id;

		foreach ((array)$lst as $key => $val) {
			if (trim(strtolower($val['name']))==strtolower($id)) {
				return $key;
			}
			
		}
	
		$this->addError($this->storeError(sprintf($this->translate('Could not resolve similar species "%s"'),$id), 'Species import'));

		return $id;

	}

    private function storeSpeciesAndVariationsAndMatrices ($data)
    {

        $_SESSION['admin']['system']['import']['loaded']['species'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['variations'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['matrices'] = 0;

        $tmpIndex = array();
        $species = $this->resolveSpeciesAndVariationsAndMatrices($data);

        $d = $this->models->Taxon->_get(array('id' =>
			array(
				'project_id' => $this->getNewProjectId(), 
				'taxon' => $this->_defaultKingdom
			)));

		if ($d) { 

			$kingdomId = $d[0]['id'];
		
		} else {

			// default kingdom
			$this->models->Taxon->save(
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
			
			$kingdomId = $this->models->Taxon->getNewId();
			
		}
        
        $i = 1;

		// save all taxa
        foreach ((array) $species as $key => $val) {
			
			if ($val['isMatrix']==true) {
				
				$d=$this->createMatrixIfNotExists($key);
				$species[$key]['lng_id'] = $d['id'];
				if ($d['type']=='new')
					$this->addMessage(sprintf('Added new referenced matrix "%s" (name only)',$key));
				continue;
				
			}
			
			if (isset($val['parent_name'])) {

				// find parent by sci name
				$d = $this->getTaxonByName($val['parent_name']);
					
				if ($d) { 

					$parent = $d[0]['id'];
				
				} else {

					// find taxon by common name
					$d = $this->models->Commonname->_get(array('id' =>
					array(
						'project_id' => $this->getNewProjectId(), 
						'language_id' => $this->getNewDefaultLanguageId(), 
						'commonname' => $val['parent_name']
					)));					
					
					if ($d) {

						$parent = $d[0]['taxon_id'];

					} else {

						// create the parent taxon
						$this->models->Taxon->save(
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
						
						$parent = $this->models->Taxon->getNewId();
						
					}

				}

			} else {
				
				// give the topmost the default uppermost parent
				$parent = $kingdomId;
				
			}
	
			// does taxon already exist?
			$d = $this->models->Taxon->_get(array('id' =>
				array(
					'project_id' => $this->getNewProjectId(), 
					'parent_id' => $parent, 
					'taxon' => $key
				)));
					
			if ($d) { 

				$species[$key]['lng_id'] = $d[0]['id'];
			
			} else {

				// save new taxon
				$this->models->Taxon->save(
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
				
				$species[$key]['lng_id'] = $this->models->Taxon->getNewId();

			}
				
			// if it's not a matrix and if NBC-data columns have been defined, save the NBC-data
			if (isset($_SESSION['admin']['system']['import']['data']['nbcColumns'])) {

				$this->models->NbcExtras->delete(
					array(
						'project_id' => $this->getNewProjectId(), 
						'ref_id' => $species[$key]['lng_id'], 
						'ref_type' => 'taxon'
					));
				
				foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey => $cVal) {
				
					if (!empty($cVal) && isset($val[$cKey]))
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

			$_SESSION['admin']['system']['import']['loaded']['species']++;
				
			if (isset($val['common name'])) {
				
				$d = $this->models->Commonname->_get(array('id' =>
				array(
					'project_id' => $this->getNewProjectId(), 
					'taxon_id' => $species[$key]['lng_id'], 
					'language_id' => $this->getNewDefaultLanguageId(), 
					'commonname' => $val['common name']
				)));
				
				if (!$d) {

					$this->models->Commonname->save(
					array(
						'id' => null, 
						'project_id' => $this->getNewProjectId(), 
						'taxon_id' => $species[$key]['lng_id'], 
						'language_id' => $this->getNewDefaultLanguageId(), 
						'commonname' => $val['common name']
					));
					
				}

			}
				
			// if there's variations, save those as well 
			if (isset($val['variations'])) {

				foreach ((array) $val['variations'] as $vKey => $vVal) {

					if (empty($vVal['variant'])) {
						
						$this->addError($this->storeError(sprintf($this->translate('Skipping variation without variant name ("%s"; double entry?).'),$vKey), 'Species import'));
						continue;
		
					}

					$d = $this->models->TaxonVariation->_get(array('id'=>
						array(
							'project_id' => $this->getNewProjectId(), 
							'taxon_id' => $species[$key]['lng_id'], 
							'label' => $vVal['variant']
						)));

					if ($d) {

						$vId = $species[$key]['variations'][$vKey]['lng_id'] = $d[0]['id'];

					} else {
			
						$this->models->TaxonVariation->save(
						array(
							'id' => null, 
							'project_id' => $this->getNewProjectId(), 
							'taxon_id' => $species[$key]['lng_id'], 
							'label' => $vVal['variant']
						));
						
						$vId = $species[$key]['variations'][$vKey]['lng_id'] = $this->models->TaxonVariation->getNewId();

						$this->models->VariationLabel->save(
						array(
							'id' => null, 
							'project_id' => $this->getNewProjectId(), 
							'variation_id' => $vId, 
							'language_id' => $this->getNewDefaultLanguageId(), 
							'label' => $vVal['variant'], 
							'label_type' => 'alternative'
						));						

					}
					
					if (isset($_SESSION['admin']['system']['import']['data']['nbcColumns'])) {

						$this->models->NbcExtras->delete(
							array(
								'project_id' => $this->getNewProjectId(), 
								'ref_id' => $vId, 
								'ref_type' => 'variation'
							));
				
						foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey => $cVal) {
						
							if (isset($vVal[$cKey]))
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

					$_SESSION['admin']['system']['import']['loaded']['variations']++;
					
					$tmpIndex[$vVal['id']] = array(
						'type' => 'var', 
						'id' => $vId,
						'name' => $vVal['variant'] // for Dierenzoeker
					);

				}

			} else {
					
				$tmpIndex[$val['id']] = array(
					'type' => 'sp', 
					'id' => $species[$key]['lng_id'],
					'name' => isset($val['common name']) ? $val['common name'] :  $key // for Dierenzoeker
				);
			}
		}

		// save relations
        foreach ((array) $species as $key => $val) {
            
            if (!isset($val['variations'])) {
                
                if (isset($val['related'])) {
                    
                    foreach ((array) $val['related'] as $rKey => $rVal) {
						
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
						
						if (!$d) {

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
            else {
                
                foreach ((array) $val['variations'] as $vKey => $vVal) {
                    
                    if (isset($vVal['related'])) {
                        
                        foreach ((array) $vVal['related'] as $rKey => $rVal) {
                            
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

							if (!$d) {
							
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

        $d = $this->models->MatrixName->_get(
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

        $d=$this->models->MatrixName->_get(array('id' =>
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

        $this->models->Matrix->save(array(
            'id' => null, 
            'project_id' => $this->getNewProjectId(), 
            'got_names' => 1
        ));
        
        $id = $this->models->Matrix->getNewId();
        
        $this->models->MatrixName->save(
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
                
                $c = $this->models->Chargroup->_get(
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
                    
                    $this->models->Chargroup->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'matrix_id' => $mId, 
                        'label' => $val['group'], 
                        'show_order' => $i++
                    ));
                    
                    $d[$val['group']] = $this->models->Chargroup->getNewId();
                    
                    $_SESSION['admin']['system']['import']['loaded']['chargroups']++;
                    
                    $this->models->ChargroupLabel->save(
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
            
            $this->models->Characteristic->save(array(
                'id' => null, 
                'project_id' => $this->getNewProjectId(), 
                'type' => $type, 
                'got_labels' => 1
            ));
            
            $data['characters'][$cKey]['id'] = $this->models->Characteristic->getNewId();
            $data['characters'][$cKey]['type'] = $type;
            
            $this->models->CharacteristicLabel->save(
            array(
                'id' => null, 
                'project_id' => $this->getNewProjectId(), 
                'characteristic_id' => $data['characters'][$cKey]['id'], 
                'language_id' => $this->getNewDefaultLanguageId(), 
                'label' => $cVal['code'].(isset($cVal['instruction']) ? '|'.$cVal['instruction'] : null)
            
            ));
            
            $this->models->CharacteristicMatrix->save(
            array(
                'id' => null, 
                'project_id' => $this->getNewProjectId(), 
                'matrix_id' => $mId, 
                'characteristic_id' => $data['characters'][$cKey]['id'], 
                'show_order' => $showOrder1++
            ));
            
            if (isset($cVal['group']) && $cVal['group'] !== 'hidden') {
                
                $this->models->CharacteristicChargroup->save(
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


    private function translateStateCode ($code, $languageId)
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

        foreach ((array)$data['species'] as $sVal) {

			if (!isset($sVal['states'])) {

				$this->addError($this->storeError(sprintf($this->translate('Found no states for "%s"'),$sVal['label']), 'Matrix states'));
				continue;

			}
            
            foreach ((array)$sVal['states'] as $key => $val) {
                
                foreach ((array) $val as $cKey => $cVal) {

					$cVal=trim($cVal);
                    
                    if (isset($states[$key][$cVal]))
                        continue;

                    if (empty($cVal) && !is_numeric($cVal))
                        continue;

					if (!isset($data['characters'][$key]['id']) || !isset($data['characters'][$key]['type']))
						continue;

                    $cId = $data['characters'][$key]['id'];
                    $type = $data['characters'][$key]['type'];
                    
                    if ($type == 'range') {
                        
                        if (strpos($cVal, '-')) {
                            
                            $d = explode('-', $cVal);
                            
                            $statemin = (int) $d[0];
                            $statemax = (int) $d[1];
                        }
                        else {
                            
                            $statemin = $statemax = (int) $cVal;
                        }
                    }
                    
                    $this->models->CharacteristicState->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'characteristic_id' => $cId, 
                        'lower' => isset($statemin) ? $statemin : null, 
                        'upper' => isset($statemax) ? $statemax : null, 
                        'got_labels' => 1, 
                        //'file_name' => $type == 'media' ? $cVal . '.' . $this->_defaultImgExtension : null
                    ));
                    
                    unset($statemin);
                    unset($statemax);
                    
                    $states[$key][$cVal] = $this->models->CharacteristicState->getNewId();
                    
					if (!empty($val)) {
					
						$this->models->CharacteristicLabelState->save(
						array(
							'id' => null, 
							'project_id' => $this->getNewProjectId(), 
							'state_id' => $states[$key][$cVal], 
							'language_id' => $this->getNewDefaultLanguageId(), 
							'label' => $this->translateStateCode($cVal, $this->getNewDefaultLanguageId())
						));
						
					} else {
						//
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
                    
                    $this->models->MatrixVariation->setNoKeyViolationLogging(true);
                    
                    $this->models->MatrixVariation->save(
                    array(
                        'project_id' => $this->getNewProjectId(), 
                        'matrix_id' => $mId, 
                        'variation_id' => $val['lng_id']
                    ));
                    
                    if (isset($val['states'])) {
                        
                        foreach ((array) $val['states'] as $sKey => $sVal) {
                            
                            foreach ((array) $sVal as $state) {
                                
                                //$this->models->MatrixTaxonState->setNoKeyViolationLogging(true);
                                
                                @$this->models->MatrixTaxonState->save(
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
                
                $this->models->MatrixTaxon->setNoKeyViolationLogging(true);
				
				if ($tVal['isMatrix']==false) {
                
					$this->models->MatrixTaxon->save(array(
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
                            
                            $this->models->MatrixTaxonState->setNoKeyViolationLogging(true);
							
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

								$this->models->MatrixTaxonState->save($d);

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
        if (!$id)
            return;
			
        $this->models->L2OccurrenceTaxon->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $this->models->L2OccurrenceTaxonCombi->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $this->models->MatrixTaxonState->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $this->models->MatrixTaxon->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $this->models->OccurrenceTaxon->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $this->models->TaxaRelations->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        $tv = $this->models->TaxonVariation->delete(
			array('id' =>
					array(
						'project_id' => $pId,
						'taxon_id' => $id
					)
				)
		);
		
		foreach((array)$tv as $key => $val) {

			$this->models->VariationLabel->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->VariationRelations->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->MatrixVariation->delete(array(
				'project_id' => $pId,
				'variation_id' => $val['id']
			));
			$this->models->NbcExtras->delete(array(
				'project_id' => $pId,
				'ref_type' => 'variation',
				'ref_id' => $val['id']
			));
			$this->models->TaxonVariation->delete(array(
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
        $this->models->LiteratureTaxon->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        
        // reset keychoice end-points
        $this->models->ChoiceKeystep->update(array(
            'res_taxon_id' => 'null'
        ), array(
            'project_id' => $pId,
            'res_taxon_id' => $id
        ));
        
        // delete commonnames
        $this->models->Commonname->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        
        // delete synonyms
        $this->models->Synonym->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        
        // purge undo
        $this->models->ContentTaxonUndo->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));
        
        // delete taxon tree branch rights
        $this->models->UserTaxon->delete(array(
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
        $this->models->Taxon->update(array(
            'parent_id' => 'null'
        ), array(
            'project_id' => $pId,
            'parent_id' => $id
        ));
        
        // delete content
        $this->models->ContentTaxon->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id, 
        ));
        
        // delete taxon
        $this->models->Taxon->delete(array(
            'project_id' => $pId,
            'id' => $id, 
        ));
        
    }
}