<?php

/*

	column headers, like 'naam SCI', are hardcoded! 

	$_stdVariantColumns needs to be user invulbaarable --> NOT! assuming sekse and variant (pwah)


	insert into settings 
		(id, project_id, setting, value, created, last_change) 
	values 
		(NULL,123, 'setting', 'value', now(), CURRENT_TIMESTAMP)



	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'suppress_splash',1,now(),CURRENT_TIMESTAMP);
	insert into settings (id, project_id, setting, value, created, last_change) values (null,3,'start_page','/kreeften/app/views/matrixkey/identify.php',now(),CURRENT_TIMESTAMP);

*/

include_once ('Controller.php');
include_once ('ProjectDeleteController.php');
class ImportNBCController extends Controller
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
		'nsrpage' => 'url_soortenregister',
		'nsrpage nieuw' => 'url_soortenregister',
		'fotograaf' => 'photographer',
		'bronvermelding' => 'source',
		'sekse op foto' => 'gender_photo',
		'thumbnail' => 'url_thumbnail',
		'levensfase' => 'life_stage'
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
        'nbc_extras'
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
		
		$this->_defaultLanguageId = LANGUAGECODE_DUTCH;
        
        $this->setBreadcrumbRootName($this->translate($this->controllerPublicName));
        
        $this->setSuppressProjectInBreadcrumbs();
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
        $this->checkAuthorisation(true);
        
        $this->setPageName($this->translate('Data import options'));
        
        $this->printPage();
    }


    public function nbcDeterminatie1Action ()
    {
		
        $this->checkAuthorisation(true);
        
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
		
        $this->checkAuthorisation(true);
        
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('nbc_determinatie_1.php');
        
        $this->setPageName($this->translate('Parsed data example'));
        
        $raw = $this->getDataFromFile($_SESSION['admin']['system']['import']['file']['path']);
        
        $_SESSION['admin']['system']['import']['data'] = $data = $this->parseData($raw);

		//q($_SESSION['admin']['system']['import']['data']);

		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
		}

		if (isset($data['project']['title'])) {

			$d = $this->models->Project->_get(array(
					'id' => array(
					'sys_name' => $data['project']['title']
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
        $this->checkAuthorisation(true);
        
        if (!isset($_SESSION['admin']['system']['import']['data']))
            $this->redirect('nbc_determinatie_2.php');
        
        $this->setPageName($this->translate('Creating project'));
		
        if (!isset($_SESSION['admin']['system']['import']['project']) && !$this->isFormResubmit()) {

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
					'group' => $pGroup
				));
				$this->addMessage('Created project "' . $pTitle . '" with id ' . $pId . '.');
				
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
				
			} else {

				$pId = $_SESSION['admin']['system']['import']['project']['id'] = $_SESSION['admin']['system']['import']['existingProjectId'];
				$_SESSION['admin']['system']['import']['project']['title'] = $_SESSION['admin']['system']['import']['data']['project']['title'];
				$this->addMessage('Using project "' . $_SESSION['admin']['system']['import']['data']['project']['title'] . '" with id ' . $pId . '.');
				
			}

            $this->addUserToProjectAsLeadExpert($pId);

            $this->addMessage('Added current user as lead expert to project.');

			if ($this->rHasVal('action')) {

				$_SESSION['admin']['system']['import']['existingDataTreatment'] = $this->requestData['action'];

				if ($this->rHasVal('action','replace_data')) {

					$pDel = new ProjectDeleteController;

					$pDel->deleteNBCKeydata($pId);
					$pDel->deleteMatrices($pId);
					$pDel->deleteCommonnames($pId);
					$pDel->deleteSynonyms($pId);
					$pDel->deleteSpeciesMedia($pId);
					$pDel->deleteSpeciesContent($pId);
					$pDel->deleteStandardCat($pId);
					$pDel->deleteSpecies($pId);
					$pDel->deleteProjectRanks($pId);

				}

			}


        }

        $this->printPage();
    }


    public function nbcDeterminatie4Action ()
    {
		
        $this->checkAuthorisation(true);
        
        if (!isset($_SESSION['admin']['system']['import']['project']))
            $this->redirect('nbc_determinatie_3.php');
        
        $this->setPageName($this->translate('Storing ranks, species and variations'));

        if (!$this->isFormResubmit() && $this->rHasVal('action', 'species')) {
            
			if ($this->rHasVal('nbcColumns'))
				$_SESSION['admin']['system']['import']['data']['nbcColumns'] = $this->requestData['nbcColumns'];

            $ranks = $this->addRanks();
            
            $_SESSION['admin']['system']['import']['project']['ranks'] = $ranks;
            
            if ($this->rHasVal('variant_columns'))
				$_SESSION['admin']['system']['import']['variantColumns'] = $this->requestData['variant_columns'];

            $data = $_SESSION['admin']['system']['import']['data'];
           
            $data = $this->storeSpeciesAndVariations($data);
            
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
        $this->checkAuthorisation(true);
        
        if (!isset($_SESSION['admin']['system']['import']['species_data']))
            $this->redirect('nbc_determinatie_4.php');
        
        $this->setPageName($this->translate('Saving matrix data'));
        
        $data = $_SESSION['admin']['system']['import']['data'];
        
        if (!$this->isFormResubmit() && $this->rHasVal('action', 'matrix')) {
            
            $mId = $this->createMatrix($_SESSION['admin']['system']['import']['project']['title']);
            $this->addMessage('Created matrix "' . $_SESSION['admin']['system']['import']['project']['title'] . '"');
            
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
	
        $this->checkAuthorisation(true);
        
		if ($this->rHasVal('action','download')) {
			$this->doDownload();
			die();
		}
		if ($this->rHasVal('action','errorlog')) {
			$this->downloadErrorLog();
			die();
		}

		if (!$_SESSION['admin']['system']['import']['projectExists']) {


			$this->addModuleToProject(MODCODE_SPECIES, $this->getNewProjectId(), 0);
			$this->grantModuleAccessRights(MODCODE_SPECIES, $this->getNewProjectId());
			
			$this->addModuleToProject(MODCODE_MATRIXKEY, $this->getNewProjectId(), 1);
			$this->grantModuleAccessRights(MODCODE_MATRIXKEY, $this->getNewProjectId());
	
			$settings = array(
				'matrixtype' => 'NBC',
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
			
		}

        $this->printPage();

    }


    public function nbcDeterminatie7Action ()
    {
	
        $this->checkAuthorisation(true);

        $this->unsetProjectSessionData();
        $this->setCurrentProjectId($this->getNewProjectId());
        $this->setCurrentProjectData();
        $this->reInitUserRolesAndRights();
        $this->setCurrentUserRoleId();
        
        unset($_SESSION['admin']['system']['import']);
        unset($_SESSION['admin']['project']['ranks']);
        
        $this->redirect($this->getLoggedInMainIndex());
    }


	public function nbcLabels1Action()
    {
        $this->checkAuthorisation(true);
        
        if ($this->rHasVal('process', '1'))
            $this->redirect('nbc_labels_2.php');

		if (isset($_SESSION['admin']['system']['import']['type']) && $_SESSION['admin']['system']['import']['type']!='nbc_labels')			
			unset($_SESSION['admin']['system']['import']);
			
        $this->setPageName($this->translate('Choose file'));
        
        $this->setSuppressProjectInBreadcrumbs();
		
        if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear', 'file')) {
            
            $tmp = tempnam(sys_get_temp_dir(), 'lng');
            
            if (copy($this->requestDataFiles[0]['tmp_name'], $tmp)) {
                
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


    public function nbcLabels2Action ()
    {

        $this->checkAuthorisation(true);
		
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('nbc_determinatie_1.php');
        
        $this->setPageName($this->translate('Parsed data'));
        
        $raw = $this->getDataFromFile($_SESSION['admin']['system']['import']['file']['path']);
      
        $_SESSION['admin']['system']['import']['data'] = $data = $this->parseLabelData($raw);

		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
		}


		if ($data['project']['title']) {

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


    public function nbcLabels3Action ()
    {
		
        $this->checkAuthorisation(true);
        
        if (!isset($_SESSION['admin']['system']['import']['data']))
            $this->redirect('nbc_determinatie_2.php');

        $this->setPageName($this->translate('Saving labels'));
        
        if (1==1 || !$this->isFormResubmit()) {

            $pTitle = $_SESSION['admin']['system']['import']['data']['project']['title'];
			//$pGroup = $_SESSION['admin']['system']['import']['data']['project']['soortgroep'];
			
			$d = $this->models->Project->_get(array(
				'id' => array(
				'sys_name' => $pTitle
			)));
			
			if (!empty($d[0]['id'])) {
				
				$pId = $d[0]['id'];
				
				$dummy = array();

				foreach((array)$_SESSION['admin']['system']['import']['data']['states'] as $val) {
					
					if (empty($val[1]))
						continue;

					// get the character that matches the 'kenmerk'-label (col 2; col 1 is the group, which is ignored here)
					$d = $this->models->CharacteristicLabel->_get(
						array(
							'where' =>
								'project_id  = '.$pId. ' and
								language_id = '.LANGUAGECODE_DUTCH. ' and
								(
									lower(label) = \''. mysql_real_escape_string(strtolower($val[1])) .'\' or
									label like \'%'. mysql_real_escape_string(strtolower($val[1])) .'|%\'
								)'
						)
					);	
		
					// if a char is found...					
					if (!empty($d[0]['id'])) {

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
									'language_id' => LANGUAGECODE_DUTCH, 
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
										'language_id' => LANGUAGECODE_DUTCH
									)
								);								

								$this->addMessage(sprintf($this->translate('Saved translation "%s".'),$val[3]));
								
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
								
								$this->addMessage(sprintf($this->translate('Updated image for "%s" to \'%s\'.'),$val[2],$val[4]));
		
								$dummy[$cId]['state'] = (!isset($dummy[$cId]['state']) ? 'all_images' : ($dummy[$cId]['state']=='all_images' ? 'all_images' : ($dummy[$cId]['state']=='no_images' ? 'partial_images' : 'partial_images' )));

								$dummy[$cId]['label'] = $val[1];

							} else {

								//$this->addMessage(sprintf($this->translate('Skipped image for "%s" (not specified).'),$val[2]));

								$dummy[$cId]['state'] = (!isset($dummy[$cId]['state']) ? 'no_images' : ($dummy[$cId]['state']=='all_images' ? 'partial_images' : ($dummy[$cId]['state']=='no_images' ? 'no_images' : 'partial_images' )));

								$dummy[$cId]['label'] = $val[1];

							}

						} else {

							$this->addError($this->storeError(sprintf($this->translate('Could not resolve state "%s" for %s.'),$val[2],$val[1]), 'Matrix import'));
							
						}

					} else {

			            $this->addError($this->storeError(sprintf($this->translate('Could not resolve character "%s".'),$val[1]), 'Matrix import'));

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
						
						$this->addMessage(sprintf($this->translate('Set character type for "%s" to %s.'),$char['label'],$type));
						
					}

				} else {
					
					$this->addMessage($this->translate('Skipped re-evaluating character types.'));
					
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



    private function getNewProjectId ()
    {
        return (isset($_SESSION['admin']['system']['import']['project']['id'])) ? $_SESSION['admin']['system']['import']['project']['id'] : null;
    }


    private function getNewDefaultLanguageId ()
    {
        return $this->_defaultLanguageId;
    }


    private function getDataFromFile ($file)
    {

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
        
        foreach ((array) $raw as $line => $val) {
            
			$d = $val;
			unset($d[0]);
            $lineHasData = strlen(implode('', $d)) > 0;

            if ($lineHasData) {

                foreach ((array) $val as $cKey => $cVal) {

                    $cVal = trim($cVal);
                    
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

                        // line 2, cell > 0: character group (or 'hidden')
                        if ($line == 2 && $cKey > 4 && !empty($cVal)) {
                            $data['characters'][$cKey]['group'] = $cVal;
						}

						/*
							because it is easier for the ppl making the import-files,
							two characters - gender and variant - are grouped
							with the taxon's name, rather than with the other
							characters.
							i love the smell of exceptions in the morning.
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
                            
						// line 6: species column headers
                        if ($line==6  && $cKey<10 && !empty($cVal))
                            $data['columns'][$cKey] = $cVal;


						// line > 6: species records
						if ($line > 6) {
							
							// line number (is uniq id during import)
							if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'id') {
								$data['species'][$line]['id'] = $cVal;
							}
							// species' main label
							else if (isset($data['columns'][$cKey]) && ($data['columns'][$cKey] == 'title' || $data['columns'][$cKey] == 'titel')) {
								$data['species'][$line]['label'] = $cVal;
							}
							// related species' main id's (or names) 
							else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'gelijkende soorten') {
								if (strpos($cVal, $this->_valueSep) !== false) {
									$data['species'][$line]['related'] = explode($this->_valueSep, $cVal);
								}
								else {
									$data['species'][$line]['related'][] = trim($cVal);
								}
								array_walk($data['species'][$line]['related'], create_function('&$val', '$val = trim($val);'));
							}
							// character states per species
							else {

								if (isset($data['characters'][$cKey]['group']) && $data['characters'][$cKey]['group'] == 'hidden') {

									$data['species'][$line][$data['characters'][$cKey]['code']] = $cVal;
									$data['hidden'][$data['characters'][$cKey]['code']] = null;
									
									
								}
								else {
									
									if (strpos($cVal, $this->_valueSep) !== false) {
										$data['species'][$line]['states'][$cKey] = explode($this->_valueSep, $cVal);
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

					$data['species'][$line]['naam SCI'] = $data['species'][$line]['label'];
					
				}
            }
        }

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

        return $data;
    }


    private function addProjectRank ($label, $rankId, $isLower, $parentId)
    {
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
        
        return $id;
    }


    private function addRanks ()
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
        }
        
        return array(
            'kingdom' => $projectKingdomId, 
            'species' => $projectSpeciesId
        );
    }



    private function resolveSpeciesAndVariations ($data)
    {

        $d = array();

        foreach ((array) $data['species'] as $key => $val) {

			if (empty($val['naam SCI'])) {
				
				$this->addError($this->storeError('Skipping species without scientific name ('.@$val['label'].').', 'Species import'));
				continue;

			}

            $d[$val['naam SCI']]['taxon'] = $val['naam SCI'];
            if (isset($val['naam NL'])) $d[$val['naam SCI']]['common name'] = $val['naam NL']; // $val['label'];
            $d[$val['naam SCI']]['id'] = $val['id'];
            $d[$val['naam SCI']]['variations'][] = $val;
        }
		
		//$variantColumns = isset($_SESSION['admin']['system']['import']['variantColumns']) ? $_SESSION['admin']['system']['import']['variantColumns'] : null;
		$variantColumns = $this->_stdVariantColumns;

        foreach ((array) $d as $key => $val) {
            
            foreach ((array) $val['variations'] as $sKey => $sVal) {
          
                $str = null;

                foreach ((array) $variantColumns as $hVal) {

                    //if (!isset($data['characters'][$hVal]['code']) || !isset($sVal[$data['characters'][$hVal]['code']]))
                    if (!isset($sVal[$hVal]))
                        continue;

                    $str .= $sVal[$hVal]. ' ';
					
                }

                $d[$key]['variations'][$sKey]['add-on'] = trim($str);
                $d[$key]['variations'][$sKey]['variant'] = (isset($sVal['naam NL']) ? $sVal['naam NL'] : $key) . ' ' . $d[$key]['variations'][$sKey]['add-on'];
            }



            // if a species has only one variation, it should not be stored (ie, species & variation identical)
            //if (count((array) $d[$key]['variations']) == 1 && strlen($d[$key]['variations'][0]['add-on']) == 0) {
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

        return $d;

    }



    private function storeNbcExtra ($id, $type, $name, $value)
    {
        $this->models->NbcExtras->save(
        array(
            'id' => null, 
            'project_id' => $this->getNewProjectId(), 
            'ref_id' => $id, 
            'ref_type' => $type, 
            'name' => $name, 
            'value' => $value
        ));
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

    private function storeSpeciesAndVariations ($data)
    {

        $_SESSION['admin']['system']['import']['loaded']['species'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['variations'] = 0;

        $tmpIndex = array();
        $species = $this->resolveSpeciesAndVariations($data);

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
        
        $parent = $this->models->Taxon->getNewId();
        
        $i = 1;

       // save all taxa
        foreach ((array) $species as $key => $val) {
            
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

			foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey => $cVal) {
			
				if (!empty($cVal) && isset($val[$cKey]))
					$this->storeNbcExtra($species[$key]['lng_id'], 'taxon', $cVal, $val[$cKey]);
			
			}
            
            $_SESSION['admin']['system']['import']['loaded']['species']++;
            
            if (isset($val['common name'])) {
                
                $this->models->Commonname->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getNewProjectId(), 
                    'taxon_id' => $species[$key]['lng_id'], 
                    'language_id' => $this->getNewDefaultLanguageId(), 
                    'commonname' => $val['common name']
                ));
            }
			
            // if there's variations, save those as well 
            if (isset($val['variations'])) {

                foreach ((array) $val['variations'] as $vKey => $vVal) {

					if (empty($vVal['variant'])) {
						
						$this->addError($this->storeError(sprintf($this->translate('Skipping variation without variant name ("%s"; double entry?).'),$vKey), 'Species import'));
						continue;
		
					}

                    $this->models->TaxonVariation->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'taxon_id' => $species[$key]['lng_id'], 
                        'label' => $vVal['variant']
                    ));
                    
                    $_SESSION['admin']['system']['import']['loaded']['variations']++;
                    
                    $vId = $species[$key]['variations'][$vKey]['lng_id'] = $this->models->TaxonVariation->getNewId();
                    
                    $tmpIndex[$vVal['id']] = array(
                        'type' => 'var', 
                        'id' => $vId,
						'name' => $vVal['variant'] // for Dierenzoeker
                    );

					foreach((array)$_SESSION['admin']['system']['import']['data']['nbcColumns'] as $cKey => $cVal) {
					
						if (isset($vVal[$cKey]))
							$this->storeNbcExtra($vId, 'variation', $cVal, $vVal[$cKey]);
					
					}
                    
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
            }
            else {
                
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
            else {
                
                foreach ((array) $val['variations'] as $vKey => $vVal) {
                    
                    if (isset($vVal['related'])) {
                        
                        foreach ((array) $vVal['related'] as $rKey => $rVal) {
                            
							$rValId = $this->resolveSimilarIdentifier($rVal,$tmpIndex);

                            if (!isset($tmpIndex[$rValId]) || $vVal['lng_id']==$tmpIndex[$rValId]['id'])
                                continue;
                            
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
        
        return $species;
    }



    private function createMatrix ($name)
    {
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
        
        return $id;
    }



    private function storeCharacterGroups ($data, $mId)
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


    private function storeCharacters ($data, $mId, $types)
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



    private function storeStates ($data)
    {
        $_SESSION['admin']['system']['import']['loaded']['states'] = 0;
        
        $states = array();
        
        foreach ((array)$data['species'] as $sVal) {

			if (!isset($sVal['states'])) {

				$this->addError($this->storeError(sprintf($this->translate('Found no states for "%s"'),$sVal['label']), 'Matrix import'));
				continue;

			}
            
            foreach ((array)$sVal['states'] as $key => $val) {
                
                foreach ((array) $val as $cKey => $cVal) {
                    
                    if (isset($states[$key][$cVal]))
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


    private function storeVariationStateConnections ($taxa, $mData, $mId)
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
                
                $this->models->MatrixTaxon->save(array(
                    'project_id' => $this->getNewProjectId(), 
                    'matrix_id' => $mId, 
                    'taxon_id' => $tVal['lng_id']
                ));
                

                if (isset($tVal['states'])) {
                    
                    foreach ((array) $tVal['states'] as $sKey => $sVal) {
                        
                        foreach ((array) $sVal as $state) {
                            
                            $this->models->MatrixTaxonState->setNoKeyViolationLogging(true);
                            
                            $this->models->MatrixTaxonState->save(
                            array(
                                'project_id' => $this->getNewProjectId(), 
                                'matrix_id' => $mId, 
                                'characteristic_id' => $mData['characters'][$sKey]['id'], 
                                'state_id' => $mData['states'][$sKey][$state], 
                                'taxon_id' => $tVal['lng_id']
                            ));
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


    private function parseLabelData ($raw)
    {
        
        $data = array();
        
        foreach ((array) $raw as $line => $val) {
            
            $lineHasData = strlen(implode('', $val)) > 0;
			
            if ($lineHasData) {

                foreach ((array) $val as $cKey => $cVal) {

                    $cVal = trim($cVal);
                    
                    if ($cKey<5) {
                        
                        // line 0, cell 0: title
                        if ($line == 0 && $cKey == 0)
                            $data['project']['title'] = $cVal;
                        // line 1, cell 0: group
                        if ($line == 1 && $cKey == 0)
                            $data['project']['soortgroep'] = $cVal;

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

    private function storeError ($err, $mod)
    {
        $_SESSION['admin']['system']['import']['errorlog']['errors'][] = array(
            $mod, 
            $err
        );
        
        return $err;
    }

    private function downloadErrorLog ()
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
                
        die();
    }

}