<?php

/*

	column headers, like 'naam SCI', are hardcoded! 

	$_variantColumnHeaders needs to be user invulbaarable

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
    private $_variantColumnHeaders = array();
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
        'related_taxa', 
        'related_variations', 
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
            
            $pId = $this->createProject(
            array(
                'title' => $pTitle, 
                'version' => '1', 
                'sys_description' => 'Created by import from a NBC-export.', 
                'css_url' => $this->controllerSettings['defaultProjectCss']
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
            
            $data = $_SESSION['admin']['system']['import']['data'];
            
            $data = $this->storeSpeciesAndVariations($data);
            
            $_SESSION['admin']['system']['import']['data_altered'] = $data;
            
            $this->addMessage('Added ' . count((array) $_SESSION['admin']['system']['import']['data_altered']) . ' species.');
            $this->addMessage('Added ' . $_SESSION['admin']['system']['import']['loaded']['variations'] . ' variations.');
            $this->smarty->assign('processed', true);
        }
        
        $this->printPage();
    }



    public function nbcDeterminatie5Action ()
    {
        if (!isset($_SESSION['admin']['system']['import']['data_altered']))
            $this->redirect('nbc_determinatie_4.php');
        
        $this->setPageName($this->translate('Saving matrix data'));
        
        if (!$this->isFormResubmit() && $this->rHasVal('action', 'matrix')) {
            
            $data = $_SESSION['admin']['system']['import']['data'];
            
            $mId = $this->createMatrix($_SESSION['admin']['system']['import']['project']['title']);
            $this->addMessage('Created matrix "' . $_SESSION['admin']['system']['import']['project']['title'] . '"');
            
            $data = $this->storeCharacterGroups($_SESSION['admin']['system']['import']['data']);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['chargroups'] . ' character groups.');
            
            $data = $this->storeCharacters($data, $mId);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['characters'] . ' characters.');
            
            $data = $this->storeStates($data);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['states'] . ' states.');
            
            $this->storeVariationStateConnections($_SESSION['admin']['system']['import']['data_altered'], $data, $mId);
            $this->addMessage('Created ' . $_SESSION['admin']['system']['import']['loaded']['connections'] . ' variation-state connections.');
            
            $this->smarty->assign('processed', true);
        }
        
        $this->printPage();
    }



    public function nbcDeterminatie6Action ()
    {
        $this->addModuleToProject(MODCODE_SPECIES, $this->getNewProjectId(), 0);
        $this->grantModuleAccessRights(MODCODE_SPECIES, $this->getNewProjectId());
        
        $this->addModuleToProject(MODCODE_MATRIXKEY, $this->getNewProjectId(), 1);
        $this->grantModuleAccessRights(MODCODE_MATRIXKEY, $this->getNewProjectId());
        
        $this->saveSetting(array(
            'name' => 'keytype', 
            'value' => 'NBC', 
            'pId' => $this->getNewProjectId()
        ));
        
        $this->unsetProjectSessionData();
        $this->setCurrentProjectId($this->getNewProjectId());
        $this->setCurrentProjectData();
        $this->reInitUserRolesAndRights();
        $this->setCurrentUserRoleId();
        
        unset($_SESSION['admin']['system']['import']);
        unset($_SESSION['admin']['project']['ranks']);
        
        $this->redirect($this->getLoggedInMainIndex());
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
	        l0: title          / character_codes
	        l1:                / character labels
	        l2:                / character instructions
	        l3:                / character units
	        l4:                / character groups * (+ hidden)
	        l5:	(empty)
	        l6: (empty)
	        l7: column headers /
	        l8 ev: data     
        */
        $data = array();
        
        $line = -1;
        
        foreach ((array) $raw as $key => $val) {
            
            $lineHasData = strlen(implode('', $val)) > 0;
            
            if ($lineHasData) {
                
                $line++;
                
                foreach ((array) $val as $cKey => $cVal) {
                    
                    $cVal = trim($cVal);
                    
                    if (!empty($cVal)) {
                        
                        // line "0", cell 0: title
                        if ($line == 0 && $cKey == 0)
                            $data['project']['title'] = $cVal;
                            // line "0", cell > 0: character codes
                        if ($line == 0 && $cKey > 0 && !empty($cVal))
                            $data['characters'][$cKey]['code'] = $cVal;
                            // line "1", cell > 0: character labels
                        if ($line == 1 && $cKey > 0 && !empty($cVal))
                            $data['characters'][$cKey]['label'] = $cVal;
                            /*
                    	// line "2", cell > 0: character instructions (ignored)
                    	if ($line==2 && $cKey>0 && !empty($cVal))
                    		$data['characters'][$cKey]['instruction'] = $cVal;
                    	// line "3", cell > 0: character unit (ignored)
                    	if ($line==3 && $cKey>0 && !empty($cVal))
                    		$data['characters'][$cKey]['unit'] = $cVal;
                    	*/
                            
                        // line "4", cell > 0: character group (or 'hidden')
                        if ($line == 4 && $cKey > 0 && !empty($cVal))
                            $data['characters'][$cKey]['group'] = $cVal;
                            
                            // line "5": species column headers
                        if ($line == 5 && !empty($cVal))
                            $data['columns'][$cKey] = $cVal;
                            
                            // line > "5": species
                        if ($line > 5) {
                            
                            if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'id') {
                                $data['species'][$line]['id'] = $cVal;
                            }
                            else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'title') {
                                $data['species'][$line]['label'] = $cVal;
                            }
                            else if (isset($data['columns'][$cKey]) && $data['columns'][$cKey] == 'related') {
                                if (strpos($cVal, $this->_valueSep) !== false) {
                                    $data['species'][$line]['related'] = explode($this->_valueSep, $cVal);
                                }
                                else {
                                    $data['species'][$line]['related'][] = trim($cVal);
                                }
                                array_walk($data['species'][$line]['related'], create_function('&$val', '$val = trim($val);'));
                            }
                            else {
                                

                                if ($data['characters'][$cKey]['group'] == 'hidden') {
                                    
                                    $data['species'][$line][$data['characters'][$cKey]['label']] = $cVal;
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
        array_push($this->_variantColumnHeaders, 'sekse');
        
        $d = array();
        
        foreach ((array) $data['species'] as $key => $val) {
            $d[$val['naam SCI']]['taxon'] = $val['naam SCI'];
            $d[$val['naam SCI']]['common name'] = $val['label'];
            $d[$val['naam SCI']]['variations'][] = $val;
        }
        
        foreach ((array) $d as $key => $val) {
            
            foreach ((array) $val['variations'] as $sKey => $sVal) {
                
                $str = null;
                
                foreach ((array) $this->_variantColumnHeaders as $hVal) {
                    if (!isset($sVal[$hVal]))
                        continue;
                    $str .= $this->translateStateCode($sVal[$hVal], $this->getNewDefaultLanguageId()) . ' ';
                }
                
                $d[$key]['variations'][$sKey]['variant'] = (isset($sVal['naam NL']) ? $sVal['naam NL'] : $key) . ' ' . $str;
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



    private function storeSpeciesAndVariations ($data)
    {
        $_SESSION['admin']['system']['import']['loaded']['species'] = 0;
        $_SESSION['admin']['system']['import']['loaded']['variations'] = 0;
        
        $tmpIndex = array();
        $variations = $this->resolveSpeciesAndVariations($data);
        
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
        
        $_SESSION['admin']['system']['import']['loaded']['species']++;
        
        $i = 1;
        
        foreach ((array) $variations as $key => $val) {
            
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
            
            $variations[$key]['id'] = $this->models->Taxon->getNewId();
            
            if (isset($val['common name'])) {
                
                $this->models->Commonname->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getNewProjectId(), 
                    'taxon_id' => $variations[$key]['id'], 
                    'language_id' => $this->getNewDefaultLanguageId(), 
                    'commonname' => $val['common name']
                ));
            }
            
            if (isset($val['variations'])) {
                
                foreach ((array) $val['variations'] as $vKey => $vVal) {
                    
                    $this->models->TaxonVariation->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'taxon_id' => $variations[$key]['id'], 
                        'label' => $vVal['variant']
                    ));
                    
                    $vId = $variations[$key]['variations'][$vKey]['lng_id'] = $this->models->TaxonVariation->getNewId();
                    
                    $tmpIndex[$vVal['id']] = $vId;
                    
                    if (isset($vVal['foto id beeldbank']))
                        $this->storeNbcExtra($vId, 'variation', 'url_image', $vVal['foto id beeldbank']);
                    if (isset($vVal['nsrpage']))
                        $this->storeNbcExtra($vId, 'variation', 'url_soortenregister', $vVal['nsrpage']);
                    if (isset($vVal['fotograaf']))
                        $this->storeNbcExtra($vId, 'variation', 'photographer', $vVal['fotograaf']);
                    if (isset($vVal['bronvermelding']))
                        $this->storeNbcExtra($vId, 'variation', 'source', $vVal['bronvermelding']);
                    
                    $this->models->VariationLabel->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'variation_id' => $vId, 
                        'language_id' => $this->getNewDefaultLanguageId(), 
                        'label' => $vVal['variant'], 
                        'label_type' => 'alternative'
                    ));
                    
                    $_SESSION['admin']['system']['import']['loaded']['variations']++;
                }
            }
        }
        
        foreach ((array) $variations as $key => $val) {
            
            if (isset($val['variations'])) {
                
                foreach ((array) $val['variations'] as $vKey => $vVal) {
                    
                    if (isset($vVal['related'])) {
                        
                        foreach ((array) $vVal['related'] as $rKey => $rVal) {
                            
                            if (!isset($tmpIndex[$rVal]))
                                continue;
                            
                            $this->models->RelatedVariations->save(
                            array(
                                'id' => null, 
                                'project_id' => $this->getNewProjectId(), 
                                'variation_id' => $variations[$key]['id'], 
                                'ref_id' => $tmpIndex[$rVal], 
                                'ref_type' => 'variation'
                            ));
                        }
                    }
                }
            }
        }
        
        return $variations;
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



    private function storeCharacterGroups ($data)
    {
        $_SESSION['admin']['system']['import']['loaded']['chargroups'] = 0;
        
        $d = array();
        
        foreach ((array) $data['characters'] as $key => $val) {
            
            if (!empty($val['group']) && $val['group'] != 'hidden' && !isset($d[$val['group']])) {
                
                $c = $this->models->Chargroup->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getNewProjectId(), 
                        'label' => $val['group']
                    )
                ));
                
                if (!is_null($c)) {
                    
                    $d[$val['group']] = $c[0]['id'];
                }
                else {
                    
                    $this->models->Chargroup->save(array(
                        'id' => null, 
                        'project_id' => $this->getNewProjectId(), 
                        'label' => $val['group']
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



    private function resolveCharType ($t)
    {
        switch ($t) {
            //return 'distribution';
            //return 'media';
            //return 'text';
            case 'Lengte_mm':
                return 'range';
                break;
            default:
                return 'media';
        }
    }



    private function storeCharacters ($data, $mId)
    {
        $showOrder = $_SESSION['admin']['system']['import']['loaded']['characters'] = 0;
        
        foreach ((array) $data['characters'] as $cKey => $cVal) {
            
            if ($cVal['group'] == 'hidden')
                continue;
            
            $type = $this->resolveCharType($cVal['code']);
            
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
                'label' => $cVal['label']
            ));
            
            $this->models->CharacteristicMatrix->save(
            array(
                'id' => null, 
                'project_id' => $this->getNewProjectId(), 
                'matrix_id' => $mId, 
                'characteristic_id' => $data['characters'][$cKey]['id'], 
                'show_order' => $showOrder++
            ));
            
            if (isset($cVal['group']) && $cVal['group'] !== 'hidden') {
                
                $this->models->CharacteristicChargroup->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getNewProjectId(), 
                    'characteristic_id' => $data['characters'][$cKey]['id'], 
                    'chargroup_id' => $data['characterGroups'][$cVal['group']]
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
                24 => 'mannetje', 
                26 => 'male'
            ), 
            'female' => array(
                24 => 'vrouwtje', 
                26 => 'female'
            )
        );
        
        return isset($translations[$code][$languageId]) ? $translations[$code][$languageId] : $code;
    }



    private function storeStates ($data)
    {
        $_SESSION['admin']['system']['import']['loaded']['states'] = 0;
        
        $states = array();
        
        foreach ((array) $data['species'] as $sVal) {
            
            foreach ((array) $sVal['states'] as $key => $val) {
                
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
                        'file_name' => $type=='range' ? null : $cVal.$this->_defaultImgExtension
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



    function storeVariationStateConnections ($taxa, $mData, $mId)
    {
        $_SESSION['admin']['system']['import']['loaded']['connections'] = 0;
        
        foreach ((array) $taxa as $tVal) {
            foreach ((array) $tVal['variations'] as $val) {
                
                $this->models->MatrixVariation->setNoKeyViolationLogging(true);
                
                $this->models->MatrixVariation->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getNewProjectId(), 
                    'matrix_id' => $mId, 
                    'variation_id' => $val['lng_id']
                ));
                
                foreach ((array) $val['states'] as $sKey => $sVal) {
                    
                    foreach ((array) $sVal as $state) {
                        
                        $this->models->MatrixTaxonState->setNoKeyViolationLogging(true);
                        
                        $this->models->MatrixTaxonState->save(
                        array(
                            'id' => null, 
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
}