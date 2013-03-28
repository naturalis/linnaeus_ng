<?php

/*

	column headers, like 'naam SCI', are hardcoded! 

	$_stdVariantColumns needs to be user invulbaarable --> NOT! assume sekse and variant (pwah)

	need to implement!
	private function translateStateCode($code)

*/

include_once ('Controller.php');
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
    public $controllerPublicName = 'NBC Dierendeterminatie Import';
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
        $this->setPageName($this->translate('Data import options'));
        
        $this->printPage();
    }


    public function nbcDeterminatie1Action ()
    {
        if ($this->rHasVal('process', '1'))
            $this->redirect('nbc_determinatie_2.php');
			
		if (isset($_SESSION['admin']['system']['import']['type']) && $_SESSION['admin']['system']['import']['type']!='nbc_data')			
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

//q($_SESSION['admin']['system']['import']['data']);

		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
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
        if (!isset($_SESSION['admin']['system']['import']['data']))
            $this->redirect('nbc_determinatie_2.php');
        
        $this->setPageName($this->translate('Creating project'));
        
        if (!$this->isFormResubmit() && !isset($_SESSION['admin']['system']['import']['project'])) {
            
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
            
            $this->addUserToProjectAsLeadExpert($this->getNewProjectId());
            $this->addMessage('Added current user as lead expert.');
            
            $this->models->LanguageProject->save(
            array(
                'id' => null, 
                'language_id' => $this->getNewDefaultLanguageId(), 
                'project_id' => $this->getNewProjectId(), 
                'def_language' => 1, 
                'active' => 'y', 
                'tranlation_status' => 1
            ));
            
            $this->addMessage('Added default language.');
        }
        else if (isset($_SESSION['admin']['system']['import']['project'])) {
            
            $this->addMessage('Using project "' . $_SESSION['admin']['system']['import']['project']['title'] . '" with id ' . $_SESSION['admin']['system']['import']['project']['id']);
        }
        
        $this->printPage();
    }


    public function nbcDeterminatie4Action ()
    {
        if (!isset($_SESSION['admin']['system']['import']['project']))
            $this->redirect('nbc_determinatie_3.php');
        
        $this->setPageName($this->translate('Storing ranks, species and variations'));
		
        if (!$this->isFormResubmit() && $this->rHasVal('action', 'species')) {
            
            $ranks = $this->addRanks();
            
            $_SESSION['admin']['system']['import']['project']['ranks'] = $ranks;
            
            if ($this->rHasVal('variant_columns')) $_SESSION['admin']['system']['import']['variantColumns'] = $this->requestData['variant_columns'];

            $data = $_SESSION['admin']['system']['import']['data'];
           
            $data = $this->storeSpeciesAndVariations($data);
            
            $_SESSION['admin']['system']['import']['species_data'] = $data;
            
            $this->addMessage('Added ' . count((array) $_SESSION['admin']['system']['import']['species_data']) . ' species.');
            $this->addMessage('Added ' . $_SESSION['admin']['system']['import']['loaded']['variations'] . ' variations.');
            $this->smarty->assign('processed', true);
        }
		
		$this->smarty->assign('variantColumns',$this->extractVariantColumns($_SESSION['admin']['system']['import']['data']));
		$this->smarty->assign('stdVariantColumns',$this->_stdVariantColumns);
		
		
        
        $this->printPage();
    }


    public function nbcDeterminatie5Action ()
    {
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
        $this->smarty->assign('matrix_items_per_page', 16);
        $this->smarty->assign('matrix_items_per_line', 4);
        $this->smarty->assign('matrix_use_sc_as_weight', 1);
        $this->smarty->assign('matrix_browse_style', 'expand');	//paginate|expand
        $this->smarty->assign('nbc_image_root', '../../media/system/skins/'.$this->_defaultSkinName.'/');
        
        $this->printPage();
    }


    public function nbcDeterminatie6Action ()
    {
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
		
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('nbc_determinatie_1.php');
        
        $this->setPageName($this->translate('Parsed data'));
        
        $raw = $this->getDataFromFile($_SESSION['admin']['system']['import']['file']['path']);
        
        $_SESSION['admin']['system']['import']['data'] = $data = $this->parseLabelData($raw);
		
		if (empty($data['project']['soortgroep'])) {
			$_SESSION['admin']['system']['import']['data']['project']['soortgroep'] = $this->_defaultGroupName;
			$data = $_SESSION['admin']['system']['import']['data'];
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


				foreach((array)$_SESSION['admin']['system']['import']['data']['states'] as $val) {
				
					$d = $this->models->CharacteristicLabel->_get(array('id' =>
						array(
							'project_id' => $pId, 
							'language_id' => LANGUAGECODE_DUTCH, 
							'label' => $val[1]
						
						))
					);
					
					if (!empty($d[0]['id'])) {

						$cId = $d[0]['characteristic_id'];

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

						foreach((array)$states as $sVal) {
							$d[]=$sVal['id'];
						}
						
						$states = '('.implode(',',$d).')';

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

								$this->addMessage(sprintf($this->translate('Updated "%s" for %s.'),$val[2],$val[1]));
								
							} else {

								$this->addMessage(sprintf($this->translate('Skipped state "%s" for %s (no translation).'),$val[2],$val[1]));

							}
	
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
								
								$this->addMessage(sprintf($this->translate('Updated image for "%s" to %s.'),$val[2],$val[4]));

								
							} else {

								$this->addMessage(sprintf($this->translate('Skipped image for "%s" (not specified).'),$val[2]));

							}

						
						} else {
							
							$this->addError(sprintf($this->translate('Could not resolve state "%s" for %s.'),$val[2],$val[1]));
							
						}


					} else {

			            $this->addError(sprintf($this->translate('Could not resolve character "%".'),$val[1]));

					}
					

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
            
            $lineHasData = strlen(implode('', $val)) > 0;
			
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
                        if ($line ==2 && $cKey > 4 && !empty($cVal))
                            $data['characters'][$cKey]['group'] = $cVal;

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
                    	if ($line==3 && $cKey>4 && !empty($cVal)) // && $cVal!='…')
                    		$data['characters'][$cKey]['instruction'] = $cVal;


                    	// line 4, cell > 0: character unit (not currently being saved)
                    	if ($line==4 && $cKey>4 && !empty($cVal))
                    		$data['characters'][$cKey]['unit'] = $cVal;
                            
						// line 6: species column headers
                        if ($line==6  && $cKey<10 && !empty($cVal))
                            $data['columns'][$cKey] = $cVal;


						// line > 6: species records
                        if ($line > 6) {
                            
                            if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'id') {
                                $data['species'][$line]['id'] = $cVal;
                            }
                            else if (isset($data['columns'][$cKey]) && ($data['columns'][$cKey] == 'title' || $data['columns'][$cKey] == 'titel')) {
                                $data['species'][$line]['label'] = $cVal;
                            }
                            else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'gelijkende soorten') {
                                if (strpos($cVal, $this->_valueSep) !== false) {
                                    $data['species'][$line]['related'] = explode($this->_valueSep, $cVal);
                                }
                                else {
                                    $data['species'][$line]['related'][] = trim($cVal);
                                }
                                array_walk($data['species'][$line]['related'], create_function('&$val', '$val = trim($val);'));
                            }
                            else {

                                if (isset($data['characters'][$cKey]['group']) && $data['characters'][$cKey]['group'] == 'hidden') {

                                    $data['species'][$line][$data['characters'][$cKey]['code']] = $cVal;
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
            }
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
				
				$this->addError('Skipping species without scientific name ('.$val['label'].').');
				continue;

			}

            $d[$val['naam SCI']]['taxon'] = $val['naam SCI'];
            $d[$val['naam SCI']]['common name'] = $val['label'];
            $d[$val['naam SCI']]['id'] = $val['id'];
            $d[$val['naam SCI']]['variations'][] = $val;
        }
		
		$variantColumns = isset($_SESSION['admin']['system']['import']['variantColumns']) ? $_SESSION['admin']['system']['import']['variantColumns'] : null;

        foreach ((array) $d as $key => $val) {
            
            foreach ((array) $val['variations'] as $sKey => $sVal) {
          
                $str = null;

                foreach ((array) $variantColumns as $hVal) {
                    if (!isset($sVal[$data['characters'][$hVal]['code']]))
                        continue;
                    $str .= $sVal[$data['characters'][$hVal]['code']]. ' ';
					
                }

                $d[$key]['variations'][$sKey]['add-on'] = trim($str);
                $d[$key]['variations'][$sKey]['variant'] = (isset($sVal['naam NL']) ? $sVal['naam NL'] : $key) . ' ' . $d[$key]['variations'][$sKey]['add-on'];
            }
            

            // if a species has only one variation whose label is the same (empty addition), it should not be stored (ie, species & variation identical) 
            if (count((array) $d[$key]['variations']) == 1 && strlen($d[$key]['variations'][0]['add-on']) == 0) {
                
                if (isset($d[$key]['variations'][0]['states']))
                    $d[$key]['states'] = $d[$key]['variations'][0]['states'];
                if (isset($d[$key]['variations'][0]['related']))
                    $d[$key]['related'] = $d[$key]['variations'][0]['related'];
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
	
		$this->addError(sprintf($this->translate('Could not resolve similar id "%s"'),$id));

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


                    if (isset($vVal['foto id beeldbank']))
                        $this->storeNbcExtra($vId, 'taxon', 'url_image', $vVal['foto id beeldbank']);
                    if (isset($vVal['nsrpage']))
                        $this->storeNbcExtra($vId, 'taxon', 'url_soortenregister', $vVal['nsrpage']);
                    if (isset($vVal['fotograaf']))
                        $this->storeNbcExtra($vId, 'taxon', 'photographer', $vVal['fotograaf']);
                    if (isset($vVal['bronvermelding']))
                        $this->storeNbcExtra($vId, 'taxon', 'source', $vVal['bronvermelding']);
                    if (isset($vVal['sekse op foto']))
                        $this->storeNbcExtra($vId, 'taxon', 'gender_photo', $vVal['sekse op foto']);
                    

            
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
						
						$this->addError(sprintf($this->translate('Skipping variation without variant name ("%s"; double entry?).'),$vKey));
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
                    
                    if (isset($vVal['foto id beeldbank']))
                        $this->storeNbcExtra($vId, 'variation', 'url_image', $vVal['foto id beeldbank']);
                    if (isset($vVal['nsrpage']))
                        $this->storeNbcExtra($vId, 'variation', 'url_soortenregister', $vVal['nsrpage']);
                    if (isset($vVal['fotograaf']))
                        $this->storeNbcExtra($vId, 'variation', 'photographer', $vVal['fotograaf']);
                    if (isset($vVal['bronvermelding']))
                        $this->storeNbcExtra($vId, 'variation', 'source', $vVal['bronvermelding']);
                    if (isset($vVal['sekse op foto']))
                        $this->storeNbcExtra($vId, 'variation', 'gender_photo', $vVal['sekse op foto']);
                    
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

				$this->addError(sprintf($this->translate('Found no states for "%s"'),$sVal['label']));
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
                        'file_name' => $type == 'media' ? $cVal . '.' . $this->_defaultImgExtension : null
                    ));
                    
                    unset($statemin);
                    unset($statemax);
                    
                    $states[$key][$cVal] = $this->models->CharacteristicState->getNewId();
                    
                    $this->models->CharacteristicLabelState->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'state_id' => $states[$key][$cVal], 
                        'language_id' => $this->getNewDefaultLanguageId(), 
                        'label' => $this->translateStateCode($cVal, $this->getNewDefaultLanguageId())
                    ));
                    
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


//    private function parseDataORIGINAL ($raw)
//    {
//        
//        /*
//         	lines:
//         	
//	        l0: title          / character_codes
//	        l1:                / character labels
//	        l2:                / character instructions
//	        l3:                / character units
//	        l4:                / character groups * (+ hidden)
//	        l5:	(empty)
//	        l6: (empty)
//	        l7: column headers /
//	        l8 ev: data     
//        */
//        $data = array();
//        
//        $line = -1;
//        
//        foreach ((array) $raw as $key => $val) {
//            
//            $lineHasData = strlen(implode('', $val)) > 0;
//			
//            if ($lineHasData) {
//
//                $line++;
//                
//                foreach ((array) $val as $cKey => $cVal) {
//
//                    $cVal = trim($cVal);
//                    
//                    if (!empty($cVal)) {
//                        
//                        // line "0", cell 0: title
//                        if ($line == 0 && $cKey == 0)
//                            $data['project']['title'] = $cVal;
//                        // line "0", cell > 0: character codes
//                        if ($line == 0 && $cKey > 0 && !empty($cVal))
//                            $data['characters'][$cKey]['code'] = $cVal;
//                        // line "1", cell 1: title
//                        if ($line == 1 && $cKey == 0)
//                            $data['project']['soortgroep'] = $cVal;
//                        // line "1", cell > 0: character labels
//                        if ($line == 1 && $cKey > 0 && !empty($cVal))
//                            $data['characters'][$cKey]['label'] = $cVal;
//                    	// line "2", cell > 0: character instructions
//                    	if ($line==2 && $cKey>0 && !empty($cVal) && $cVal!='…')
//                    		$data['characters'][$cKey]['instruction'] = $cVal;
//                        /*
//                    	// line "3", cell > 0: character unit (ignored)
//                    	if ($line==3 && $cKey>0 && !empty($cVal))
//                    		$data['characters'][$cKey]['unit'] = $cVal;
//                    	*/
//                            
//                        // line "4", cell > 0: character group (or 'hidden')
//                        if ($line == 4 && $cKey > 0 && !empty($cVal))
//                            $data['characters'][$cKey]['group'] = $cVal;
//                            
//                            // line "5": species column headers
//                        if ($line == 5 && !empty($cVal))
//                            $data['columns'][$cKey] = $cVal;
//                            
//                            // line > "5": species
//                        if ($line > 5) {
//                            
//                            if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'id') {
//                                $data['species'][$line]['id'] = $cVal;
//                            }
//                            else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'title') {
//                                $data['species'][$line]['label'] = $cVal;
//                            }
//                            else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'related') {
//                                if (strpos($cVal, $this->_valueSep) !== false) {
//                                    $data['species'][$line]['related'] = explode($this->_valueSep, $cVal);
//                                }
//                                else {
//                                    $data['species'][$line]['related'][] = trim($cVal);
//                                }
//                                array_walk($data['species'][$line]['related'], create_function('&$val', '$val = trim($val);'));
//                            }
//                            else {
//                                
//
//                                if (isset($data['characters']) && $data['characters'][$cKey]['group'] == 'hidden') {
//                                    
//                                    $data['species'][$line][$data['characters'][$cKey]['label']] = $cVal;
//                                }
//                                else {
//                                    
//                                    if (strpos($cVal, $this->_valueSep) !== false) {
//                                        $data['species'][$line]['states'][$cKey] = explode($this->_valueSep, $cVal);
//                                    }
//                                    else {
//                                        $data['species'][$line]['states'][$cKey][] = trim($cVal);
//                                    }
//                                    array_walk($data['species'][$line]['states'][$cKey], create_function('&$val', '$val = trim($val);'));
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//
//        return $data;
//    }


}