<?php

/*

	variationsAction
		saves all variations in project default language only!!!!

	pre-check
		make sure $iniSettings.upload_max_filesize and $iniSettings.post_max_size are sufficient (see config for allowed filesizes)

	new project order of business (* do immediately, else can be done later):
	
		- select ranks *
		- determine where the distinction between hogher taxa and species module lies *
		- translate ranks
		- import taxa *
			or
		  define taxa by hand *
		- autorize collaborators for taxa *
		  note: this is only useful if
		  a) you have entered a sizeable amount of taxa
		  b) you authorize someone for a level high up in the tree
		- check and possibly change the default categories
		- translate categories
		- check and posibly change sections *
		- translate sections *
		  (must be done before editing taxa because they actively `influence the content)
		- edit taxa
		
		- check project specific css

    tinyMCE
		compressor php

    tinyMCE spell checker:
        - requires google: check if online
        - change default lanmguage through js
        - what if language has no iso2?
        - what happens if language does not exist at google?

	must delete link taxa - ranks when deleting a rank

	purge and limit undo!
	
	[new litref] is hardcoded

*/

include_once ('Controller.php');
class SpeciesController extends Controller
{
    private $_useNBCExtras = false;
    public $usedModels = array(
        'user', 
        'user_taxon', 
        'role', 
        'project_role_user', 
        'user_taxon', 
        'content_taxon', 
        'content_taxon_undo', 
        'section', 
        'label_section', 
        'page_taxon', 
        'page_taxon_title', 
        'heartbeat', 
        'content_taxon_undo', 
        'media_taxon', 
        'media_descriptions_taxon', 
        'hybrid', 
        'synonym', 
        'commonname', 
        'label_language', 
        'choice_keystep', 
        'literature', 
        'literature_taxon', 
        'taxa_relations', 
        'variation_relations', 
        'matrix_variation', 
        'matrix_taxon_state', 
        'nbc_extras'
    );
    public $usedHelpers = array(
        'col_loader_helper', 
        'csv_parser_helper', 
        'file_upload_helper', 
        'image_thumber_helper', 
        'hr_filesize_helper'
    );
    public $cacheFiles = array(
        'key-keyTaxa', 
        'key-taxonDivision*', 
        'tree-KeyTree', 
        'list' => 'species-treeList'
    );
    public $cssToLoad = array(
        'prettyPhoto/prettyPhoto.css', 
        'taxon.css', 
        'rank-list.css', 
        'dialog/jquery.modaldialog.css', 
        'lookup.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js', 
            'prettyPhoto/jquery.prettyPhoto.js', 
            'int-link.js', 
            'dialog/jquery.modaldialog.js', 
            'lookup.js'
        )
    );
    public $controllerPublicName = 'Species module';
    public $controllerModuleId = 4; // ref. record for Species module in table 'modules'
    public $includeLocalMenu = false;



    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
        
        $this->initialize();
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
     * Index of the species module
     *
     * @access    public
     */
    public function indexAction ()
    {
        unset($_SESSION['admin']['system']['activeTaxon']);
        
        if ($this->rHasVal('higher', '1')) {
            
            $d = $this->newGetUserAssignedTaxonTreeList(array(
                'higherOnly' => true
            ));
            
            $_SESSION['admin']['system']['highertaxa'] = true;
            
            foreach ((array) $d as $val) {
                
                if ($val['lower_taxon'] == 0) {
                    
                    $d = $val;
                    
                    break;
                }
            }
        }
        else {
            
            unset($_SESSION['admin']['system']['highertaxa']);
            
            $d = $this->newGetUserAssignedTaxonTreeList();
            
            foreach ((array) $d as $val) {
                
                if ($val['lower_taxon'] == 1) {
                    
                    $d = $val;
                    
                    break;
                }
            }
        }
        

        if (!isset($d['id']) || !$this->userHasTaxon($d['id'])) {
            
            // not sure why this was here...
            //unset($_SESSION['admin']['system']['highertaxa']);
            



            $this->redirect('collaborators.php');
        }
        else {
            
            $this->redirect('taxon.php?id=' . $d['id']);
        }
    }



    public function manageAction ()
    {
        
        //unset($_SESSION['admin']['system']['activeTaxon']);
        //unset($_SESSION['admin']['system']['highertaxa']);
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Species module overview'));
        
        if (count((array) $_SESSION['admin']['project']['languages']) == 0)
            $this->addError(sprintf($this->translate('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'), '<a href="../projects/data.php">', '</a>'));
        
        $this->printPage();
    }



    /**
     * List existing taxa
     *
     * @access    public
     */
    public function listAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxon list'));
        
        unset($_SESSION['admin']['system']['activeTaxon']);
        
        if ($this->rHasId() && $this->rHasVal('move') && !$this->isFormResubmit()) {
            // moving branches up and down the stem
            

            $this->clearCache($this->cacheFiles['list']);
            
            $this->moveIdInTaxonOrder($this->requestData['id'], $this->requestData['move']);
            
            if ($this->rHasVal('scroll'))
                $this->smarty->assign('scroll', $this->requestData['scroll']);
        }
        
        $taxa = $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        ));
        
        if (isset($taxa) && count((array) $taxa) > 0) {
            
            $projectLanguages = $_SESSION['admin']['project']['languages'];
            
            $pageCount = $this->getPageTaxonCount();
            
            $contentCount = $this->getContentTaxaCount();
            
            $synonymsCount = $this->getSynonymCount();
            
            $commonnameCount = $this->getCommonnameCount();
            
            $mediaCount = $this->getMediaTaxonCount();
            
            $literatureCount = $this->getLiteratureTaxonCount();
            
            foreach ((array) $taxa as $key => $taxon) {
                
                $taxa[$key]['pctFinished'] = isset($contentCount[$taxon['id']]) ? round(((isset($contentCount[$taxon['id']]) ? $contentCount[$taxon['id']] : 0) / (count((array) $projectLanguages) * $pageCount)) * 100) : 0;
                
                $taxa[$key]['synonymCount'] = isset($synonymsCount[$taxon['id']]) ? $synonymsCount[$taxon['id']] : 0;
                
                $taxa[$key]['commonnameCount'] = isset($commonnameCount[$taxon['id']]) ? $commonnameCount[$taxon['id']] : 0;
                
                $taxa[$key]['mediaCount'] = isset($mediaCount[$taxon['id']]) ? $mediaCount[$taxon['id']] : 0;
                
                $taxa[$key]['literatureCount'] = isset($literatureCount[$taxon['id']]) ? $literatureCount[$taxon['id']] : 0;
            }
            

            /*
			// user requested a sort of the table
			if ($this->rHasVal('key')) {

				$sortBy = array(
					'key' => $this->requestData['key'], 
					'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
					'case' => 'i'
				);
			
				// sort array of collaborators
				$this->customSortArray($taxa, $sortBy);
	
			} else {
			// default sort order
				
				$sortBy = array(
					'key' => 'taxon', 
					'dir' => 'asc', 
					'case' => 'i'
				);
			
			}
			*/
            
            if (count((array) $taxa) == 0)
                $this->addMessage($this->translate('There are no taxa for you to edit.'));
                

                //if (true) {
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                if (isset($ranks))
                    $this->smarty->assign('ranks', $ranks);
            }
            
            if (isset($taxa))
                $this->smarty->assign('taxa', $taxa);
                
                //$this->smarty->assign('sortBy', $sortBy);
            



            $this->smarty->assign('languages', $projectLanguages);
        }
        else {
            
            $this->addMessage($this->translate('No taxa have been assigned to you.'));
        }
        
        $this->printPage();
    }



    /**
     * Display, add, edit and delete categories' names in all languages
     *
     * @access    public
     */
    public function pageAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Define categories'));
        
        // adding a new page
        if ($this->rHasVal('new_page') && !$this->isFormResubmit()) {
            
            $tp = $this->createTaxonCategory($this->requestData['new_page'], $this->requestData['show_order']);
            
            if ($tp !== true) {
                
                $this->addError($this->translate('Could not save category.'), 1);
                $this->addError('(' . $tp . ')', 1);
            }
        }
        
        $lp = $_SESSION['admin']['project']['languages'];
        
        $pages = $this->models->PageTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'show_order'
        ));
        
        foreach ((array) $pages as $key => $page) {
            
            foreach ((array) $lp as $k => $language) {
                
                $tpt = $this->models->PageTaxonTitle->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'page_id' => $page['id'], 
                        'language_id' => $language['language_id']
                    )
                ));
                
                $pages[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];
            }
            
            $nextShowOrder = $page['show_order'] + 1;
        }
        

        $this->smarty->assign('nextShowOrder', $nextShowOrder);
        
        $this->smarty->assign('maxCategories', $this->generalSettings['maxCategories']);
        
        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
        
        $this->printPage();
    }



    /**
     * Edit existing taxon
     *
     * @access    public
     */
    public function editAction ()
    {
        $this->checkAuthorisation();
        
        if (!$this->rHasId())
            $this->redirect('new.php');
        
        if (!$this->userHasTaxon($this->requestData['id']))
            $this->redirect('index.php');
        
        $data = $this->getTaxonById();
        
        $pr = $this->newGetProjectRanks(array(
            'includeLanguageLabels' => true, 
            'idsAsIndex' => true
        ));
        
        if ($this->maskAsHigherTaxa()) {
            
            $this->setPageName(sprintf($this->translate('Editing %s "%s"'), strtolower($pr[$data['rank_id']]['rank']), $data['taxon']));
        }
        else {
            
            $this->setPageName(sprintf($this->translate('Editing "%s"'), $data['taxon']));
        }
        
        if (count((array) $pr) == 0) {
            
            $this->addMessage($this->translate('No ranks have been defined.'));
        }
        else 

        if (!$this->doLockOutUser($this->requestData['id'], true)) {
            
            $this->newGetTaxonTree();
            
            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;
            
            if ($this->rHasId() && $this->rHasVal('taxon') && $this->rHasVal('action', 'save') && !$this->isFormResubmit()) {
                
                $isHybrid = $this->rHasVal('is_hybrid', 'on');
                
                if ($this->requestData['id'] == $this->requestData['parent_id'])
                    $parentId = $this->requestData['org_parent_id'];
                else if ($isEmptyTaxaList || $this->requestData['parent_id'] == '-1')
                    $parentId = null;
                else
                    $parentId = $this->requestData['parent_id'];
                
                if ($this->isTaxonNameUnique($this->requestData['taxon'], $this->requestData['id'])) {
                    
                    if ($this->canParentHaveChildTaxa($parentId) || $isEmptyTaxaList) {
                        
                        if (!$isHybrid || ($isHybrid && $this->canRankBeHybrid($this->requestData['rank_id']))) {
                            
                            $this->models->Taxon->save(
                            array(
                                'id' => $this->requestData['id'], 
                                'project_id' => $this->getCurrentProjectId(), 
                                'taxon' => $this->requestData['taxon'], 
                                'author' => ($this->requestData['author'] ? $this->requestData['author'] : null), 
                                'parent_id' => $parentId, 
                                'rank_id' => $this->requestData['rank_id'], 
                                'is_hybrid' => ($isHybrid ? 1 : 0)
                            ));
                            
                            $this->reOrderTaxonTree();
                            
                            //$this->addMessage(sprintf($this->translate('"%s" saved.'),$this->requestData['taxon']));
                            



                            //$this->setPageName(sprintf($this->translate('Editing "%s"'),$this->requestData['taxon']));
                            



                            //unset($data['taxon']);
                            unset($_SESSION['admin']['species']['usertaxa']);
                            
                            $this->redirect('taxon.php?id=' . $this->requestData['id']);
                        }
                        else {
                            
                            $this->addError($this->translate('Rank cannot be hybrid.'));
                        }
                    }
                    else {
                        
                        $this->addError($this->translate('Parent cannot have child taxa.'));
                    }
                }
                else {
                    
                    $this->addError($this->translate('Taxon name already in database.'));
                }
            }
            
            $this->smarty->assign('allowed', true);
            
            if (isset($data))
                $this->smarty->assign('data', $data);
            
            $this->smarty->assign('projectRanks', $pr);
            
            if (isset($this->treeList))
                $this->smarty->assign('taxa', $this->treeList);
        }
        else {
            
            $this->smarty->assign('taxon', array(
                'id' => -1
            ));
            
            $this->addError($this->translate('Taxon is already being edited by another editor.'));
        }
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        $this->smarty->assign('navCurrentId', $data['id']);
        
        $this->printPage();
    }



    /**
     * Add new taxon
     *
     * @access    public
     */
    public function newAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->maskAsHigherTaxa()) {
            
            $this->setPageName($this->translate('New higher taxon'));
        }
        else {
            
            $this->setPageName($this->translate('New taxon'));
        }
        
        $pr = $this->newGetProjectRanks();
        
        if (count((array) $pr) == 0) {
            
            $this->addMessage($this->translate('No ranks have been defined.'));
        }
        else {
            
            $this->_newGetTaxonTree();
            
            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;
            
            // save
            if ($this->rHasVal('taxon') && $this->rHasVal('action', 'save')) { // && !$this->isFormResubmit()) {
                


                $isHybrid = $this->rHasVal('is_hybrid', 'on');
                
                $parentId = ((isset($this->requestData['id']) && $this->requestData['id'] == $this->requestData['parent_id']) || $isEmptyTaxaList || $this->requestData['parent_id'] == '-1' ? null : $this->requestData['parent_id']);
                
                $parent = $this->getTaxonById($parentId);
                
                $newName = $this->requestData['taxon'];
                
                $newName = trim(preg_replace('/\s+/', ' ', $newName));
                
                // remove ()'s from subgenus (changed silently)
                $newName = $this->fixSubgenusParentheses($newName, $this->requestData['rank_id']);
                // first letter is capitalized & subgenus parantheses are removed (changed silently)
                $newName = $this->fixNameCasting($newName);
                
                $hasErrorButCanSave = null;
                
                //checks
                /* NON LETHAL */
                if (!$this->checkNameSpaces($newName, $this->requestData['rank_id'], $this->requestData['parent_id'])) {
                    $this->addError($this->translate('The number of spaces in the name does not match the selected rank.'));
                    $hasErrorButCanSave = true;
                }
                
                // no markers
                $d = $this->removeMarkers($newName);
                if ($d != $newName) {
                    $this->addError($this->translate('Markers are inserted automatically.'));
                    $hasErrorButCanSave = true;
                    $newName = $d;
                }
                
                // 3. Names are written in Latin (yeah right) and should not contain special characters or digits.
                if (!$this->checkCharacters($newName)) {
                    $this->addError($this->translate('The name you specified contains invalid characters.'));
                    $hasErrorButCanSave = true;
                }
                
                // 2. Issue warning if a species is not linked to an ideal parent.
                if (isset($pr[$this->requestData['rank_id']]['ideal_parent_id']) && $parent['rank_id'] != $pr[$this->requestData['rank_id']]['ideal_parent_id']) {
                    $this->addError(
                    sprintf($this->translate('A %s should be linked to %s. This relationship is not enforced, so you can link to %s, but this may result in problems with the classification.'), 
                    strtolower($pr[$this->requestData['rank_id']]['rank']), strtolower($pr[$pr[$this->requestData['rank_id']]['ideal_parent_id']]['rank']), strtolower($pr[$parent['rank_id']]['rank'])));
                    $hasErrorButCanSave = true;
                }
                

                /* LETHAL / NON-LETHAL */
                $dummy = $this->newIsTaxonNameUnique(array(
                    'name' => $newName, 
                    'rankId' => $this->requestData['rank_id'], 
                    'parentId' => $parentId
                ));

                if (1 == 1) {
                    $this->addError(sprintf($this->translate('The name "%s" already exists.'), $newName));
                    $hasErrorButCanSave = false;
                }
                


                /* LETHAL */
                if (!$this->canParentHaveChildTaxa($this->requestData['parent_id']) || $isEmptyTaxaList) {
                    $this->addError($this->translate('The selected parent taxon can not have children.'));
                    $hasErrorButCanSave = false;
                }
                else {
                    
                    if (!$this->doNameAndParentMatch($newName, $parent['taxon'])) {
                        $this->addError(sprintf($this->translate('"%s" cannot be selected as a parent for "%s".'), $parent['taxon'], $newName));
                        $hasErrorButCanSave = false;
                    }
                }
                
                if ($isHybrid && !$this->canRankBeHybrid($this->requestData['rank_id'])) {
                    $this->addError($this->translate('Rank cannot be hybrid.'));
                    $hasErrorButCanSave = false;
                }
                
                // save as requested
                if (is_null($hasErrorButCanSave) || $this->rHasVal('override', '1')) {
                    
                    $this->clearErrors();
                    
                    $this->clearCache($this->cacheFiles);
                    
                    $this->models->Taxon->save(
                    array(
                        'id' => ($this->rHasId() ? $this->requestData['id'] : null), 
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon' => $newName, 
                        'author' => ($this->requestData['author'] ? $this->requestData['author'] : null), 
                        'parent_id' => $parentId, 
                        'rank_id' => $this->requestData['rank_id'], 
                        'is_hybrid' => ($isHybrid ? 1 : 0)
                    ));
                    
                    $newId = $this->models->Taxon->getNewId();
                    
                    if (empty($parentId))
                        $this->doAssignUserTaxon($this->getCurrentUserId(), $newId);
                    
                    $this->reOrderTaxonTree();
                    
                    if ($this->rHasVal('next', 'main'))
                        $this->redirect('taxon.php?id=' . $newId);
                    
                    $this->_newGetTaxonTree();
                    
                    $this->addMessage(sprintf($this->translate('"%s" saved.'), $newName));
                }
                else {
                    
                    $this->requestData['taxon'] = $newName;
                    
                    if ($hasErrorButCanSave) {
                        $this->addMessage(
                        '
                        	Please be aware of the warnings above before saving.<br />
                        	<input type="button" onclick="taxonOverrideSaveNew()" value="' . $this->translate('save anyway') . '" />');
                    }
                    else {
                        $this->addError('Taxon not saved.');
                    }
                    
                    $this->smarty->assign('hasErrorButCanSave', $hasErrorButCanSave);
                    
                    $this->smarty->assign('data', $this->requestData);
                }
            } // save
        } // no ranks defined
        



        $this->smarty->assign('projectRanks', $pr);
        
        if (isset($this->treeList))
            $this->smarty->assign('taxa', $this->treeList);
        
        $s = $this->getProjectIdRankByname('Subgenus');
        if ($s)
            $this->smarty->assign('rankIdSubgenus', $s);
        
        $this->printPage();
    }



    /**
     * Edit taxon content
     *
     * Actual saving etc. works via AJAX actions
     *
     * @access    public
     */
    public function taxonAction ()
    {
        

        //        $this->checkAuthorisation();
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
        ));
        
        if ($this->rHasId()) {
            // get existing taxon
            



            if (!$this->userHasTaxon($this->requestData['id']))
                $this->redirect('index.php');
                
                // replace possible [new litref] and [new media] tags with links to newly created reference of media
            $this->filterInternalTags($this->requestData['id']);
            
            $taxon = $this->getTaxonById();
            
            $_SESSION['admin']['system']['activeTaxon'] = array(
                'taxon_id' => $taxon['id'], 
                'taxon' => $taxon['taxon']
            );
            
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Editing %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Editing "%s"'), $taxon['taxon']));
            }
            
            if (!$this->doLockOutUser($this->requestData['id'])) {
                // if new taxon OR existing taxon not being edited by someone else, get languages and content
                



                // get available languages
                $lp = $_SESSION['admin']['project']['languages'];
                
                // determine the language the page will open in
                $startLanguage = $this->rHasVal('lan') ? $this->requestData['lan'] : $this->getDefaultProjectLanguage();
                
                // get the defined categories (just the page definitions, no content yet)
                $tp = $this->models->PageTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId()
                    ), 
                    'order' => 'show_order'
                ));
                
                foreach ((array) $tp as $key => $val) {
                    
                    foreach ((array) $lp as $k => $language) {
                        
                        // for each category in each language, get the category title
                        $tpt = $this->models->PageTaxonTitle->_get(
                        array(
                            'id' => array(
                                'project_id' => $this->getCurrentProjectId(), 
                                'language_id' => $language['language_id'], 
                                'page_id' => $val['id']
                            )
                        ));
                        
                        $tp[$key]['titles'][$language['language_id']] = $tpt[0];
                    }
                    
                    if ($val['def_page'] == 1)
                        $defaultPage = $val['id'];
                }
                
                // determine the page_id the page will open in
                $startPage = $this->rHasVal('page') ? $this->requestData['page'] : (isset($_SESSION['admin']['system']['lastActivePage']) ? $_SESSION['admin']['system']['lastActivePage'] : $defaultPage);
                
                if (isset($taxon)) {
                    
                    $this->smarty->assign('taxon', $taxon);
                    
                    $this->smarty->assign('media', addslashes(json_encode($this->getTaxonMedia($taxon['id']))));
                    
                    $this->smarty->assign('literature', addslashes(json_encode($this->getTaxonLiterature($taxon['id']))));
                }
                
                $this->smarty->assign('pages', $tp);
                
                $this->smarty->assign('languages', $lp);
                
                $this->smarty->assign('includeHtmlEditor', true);
                
                $this->smarty->assign('activeLanguage', $startLanguage);
                
                $this->smarty->assign('activePage', $startPage);
            }
            else {
                // existing taxon already being edited by someone else
                



                $this->smarty->assign('taxon', array(
                    'id' => -1
                ));
                
                $this->addError($this->translate('Taxon is already being edited by another editor.'));
            }
        }
        else {
            // no id
            



            $this->smarty->assign('taxon', array(
                'id' => -1
            ));
            
            $this->addError($this->translate('No taxon ID specified.'));
        }
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('soundPlayerPath', $this->generalSettings['soundPlayerPath']);
        
        $this->smarty->assign('soundPlayerName', $this->generalSettings['soundPlayerName']);
        
        $this->printPage();
    }



    /**
     * Deleting a taxon with children: decide what to do with the progeny
     *
     * @access    public
     */
    public function deleteAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->rHasVal('action', 'process') && $this->rHasId()) {
            
            $this->clearCache($this->cacheFiles);
            
            $taxon = $this->getTaxonById($this->requestData['id']);
            
            foreach ((array) $this->requestData['child'] as $key => $val) {
                
                if ($val == 'delete') {
                    
                    $this->deleteTaxonBranch($key);
                }
                elseif ($val == 'orphan') {
                    
                    // kill off the parent_id and turn it into a orphan
                    $this->models->Taxon->update(array(
                        'parent_id' => 'null'
                    ), array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'id' => $key
                    ));
                }
                elseif ($val == 'attach') {
                    
                    // reacttach to the parent_id of the to-be-deleted taxon
                    $this->models->Taxon->update(array(
                        'parent_id' => $taxon['parent_id']
                    ), array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'id' => $key
                    ));
                }
            }
            
            // delete the taxon
            $this->deleteTaxon($this->requestData['id']);
            
            $this->redirect('list.php');
        }
        elseif ($this->rHasId()) {
            
            $taxon = $this->getTaxonById();
            
            if (isset($taxon)) {
                
                $parent = $this->getTaxonById($taxon['parent_id']);
                
                $this->getTaxonTree(array(
                    'pId' => $taxon['id']
                ));
                
                $this->setPageName(sprintf($this->translate('Deleting taxon "%s"'), $taxon['taxon']));
                
                $pr = $this->getProjectRanks(array(
                    'idsAsIndex' => true
                ));
                
                $this->smarty->assign('ranks', $pr);
                
                $this->smarty->assign('taxon', $taxon);
                
                $this->smarty->assign('parent', $parent);
                
                $this->smarty->assign('taxa', $this->treeList);
            }
            else {
                
                $this->addError($this->translate('Non-existant ID.'));
            }
        }
        else {
            
            $this->redirect('list.php');
        }
        
        $this->printPage();
    }



    /**
     * 
     *
     * @access    public
     */
    public function orphansAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Orphaned taxa'));
        
        if ($this->rHasVal('child')) {
            
            $this->clearCache($this->cacheFiles);
            
            foreach ((array) $this->requestData['child'] as $key => $val) {
                
                if ($val == 'delete') {
                    
                    $this->deleteTaxonBranch($key);
                    
                    $this->deleteTaxon($key);
                }
                elseif ($val == 'attach') {
                    
                    $this->models->Taxon->update(array(
                        'parent_id' => $this->requestData['parent'][$key]
                    ), array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'id' => $key
                    ));
                }
            }
        }
        
        $pr = $this->getProjectRanks(array(
            'idsAsIndex' => true
        ));
        
        $topRank = array_slice($pr, 0, 1);
        
        $isOwnParent = $this->models->Taxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'parent_id' => 'id'
            )
        ));
        
        $hasNoParent = $this->models->Taxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'parent_id is' => 'null', 
                'rank_id !=' => $topRank[0]['id']
            )
        ));
        
        $taxa = array_merge((array) $isOwnParent, (array) $hasNoParent);
        
        $this->newGetTaxonTree();
        
        $this->smarty->assign('tree', $this->treeList);
        
        $this->smarty->assign('ranks', $pr);
        
        if (isset($taxa))
            $this->smarty->assign('taxa', $taxa);
        
        $this->printPage();
    }



    /**
     * See and maintain literary references
     *
     * @access    public
     */
    public function literatureAction ()
    {
        $this->checkAuthorisation();
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
        ));
        
        if ($this->rHasId()) {
            // get existing taxon name
            



            $taxon = $this->getTaxonById();
            
            $_SESSION['admin']['system']['activeTaxon'] = array(
                'taxon_id' => $taxon['id'], 
                'taxon' => $taxon['taxon']
            );
            
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Literature for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Literature for "%s"'), $taxon['taxon']));
            }
            
            $this->smarty->assign('id', $this->requestData['id']);
            
            $refs = $this->getTaxonLiterature($taxon['id']);
            
            // user requested a sort of the table
            if ($this->rHasVal('key')) {
                
                $sortBy = array(
                    'key' => $this->requestData['key'], 
                    'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
                    'case' => 'i'
                );
            }
            else {
                
                $sortBy = array(
                    'key' => 'author_first', 
                    'dir' => 'asc', 
                    'case' => 'i'
                );
            }
            
            $this->customSortArray($refs, $sortBy);
            
            $this->smarty->assign('sortBy', $sortBy);
            
            if (isset($refs))
                $this->smarty->assign('refs', $refs);
            
            if (isset($taxon))
                $this->smarty->assign('taxon', $taxon);
            
            $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
                'higherOnly' => $this->maskAsHigherTaxa()
            )));
            $this->smarty->assign('navCurrentId', $taxon['id']);
        }
        else {
            
            $this->addError($this->translate('No taxon specified.'));
        }
        
        $this->printPage();
    }



    /**
     * See and maintain media for a taxon
     *
     * @access    public
     */
    public function mediaAction ()
    {
        $this->checkAuthorisation();
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
        ));
        
        if ($this->rHasId()) {
            // get existing taxon name
            



            $taxon = $this->getTaxonById();
            
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Media for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Media for "%s"'), $taxon['taxon']));
            }
            
            $this->smarty->assign('id', $this->requestData['id']);
            

            if ($this->rHasVal('mId') && $this->rHasVal('move') && !$this->isFormResubmit()) {
                
                $this->changeMediaSortOrder($this->requestData['id'], $this->requestData['mId'], $this->requestData['move']);
            }
            
            $media = $this->getTaxonMedia($this->requestData['id']);
            
            foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $key => $val) {
                
                $d[$val['mime']] = $val['media_type'];
            }
            
            foreach ((array) $media as $key => $val) {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
                array(
                    'id' => array(
                        'media_id' => $val['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->getDefaultProjectLanguage()
                    )
                ));
                
                $val['description'] = $mdt[0]['description'];
                
                if (isset($d[$val['mime_type']]))
                    $r[$d[$val['mime_type']]][] = $val;
            }
            
            if (isset($r))
                $this->smarty->assign('media', $r);
            
            $this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
            
            $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
            
            $this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);
        }
        else {
            
            $this->addError($this->translate('No taxon specified.'));
        }
        
        if (isset($taxon))
            $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('soundPlayerPath', $this->generalSettings['soundPlayerPath']);
        $this->smarty->assign('soundPlayerName', $this->generalSettings['soundPlayerName']);
        
        $this->printPage();
    }



    /**
     * Upload media for a taxon
     *
     * @access    public
     */
    public function mediaUploadAction ()
    {
        $this->checkAuthorisation();
        
        $this->includeLocalMenu = false;
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
        ));
        
        if ($this->rHasVal('add', 'hoc') && !isset($_SESSION['admin']['system']['media']['newRef'])) {
            // referred from the taxon content editing page
            



            $_SESSION['admin']['system']['media']['newRef'] = '<new>';
            
            $this->requestData['id'] = $_SESSION['admin']['system']['activeTaxon']['taxon_id'];
        }
        
        if ($this->rHasId()) {
            // get existing taxon name
            



            $taxon = $this->getTaxonById();
            
            if ($taxon['id']) {
                
                $this->setPageName(sprintf($this->translate('New media for "%s"'), $taxon['taxon']));
                
                if ($this->requestDataFiles && !$this->isFormResubmit()) {
                    
                    $filesToSave = $this->getUploadedMediaFiles();
                    
                    $firstInsert = false;
                    
                    if ($filesToSave) {
                        
                        foreach ((array) $filesToSave as $key => $file) {
                            
                            $thumb = false;
                            
                            if ($this->helpers->ImageThumberHelper->canResize($file['mime_type']) && $this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir() . $file['name'])) {
                                
                                $pi = pathinfo($file['name']);
                                $this->helpers->ImageThumberHelper->size_width(150);
                                
                                if ($this->helpers->ImageThumberHelper->save($this->getProjectsThumbsStorageDir() . $pi['filename'] . '-thumb.' . $pi['extension'])) {
                                    
                                    $thumb = $pi['filename'] . '-thumb.' . $pi['extension'];
                                }
                            }
                            
                            $mt = $this->models->MediaTaxon->save(
                            array(
                                'id' => null, 
                                'project_id' => $this->getCurrentProjectId(), 
                                'taxon_id' => $this->requestData['id'], 
                                'file_name' => $file['name'], 
                                'original_name' => $file['original_name'], 
                                'mime_type' => $file['mime_type'], 
                                'file_size' => $file['size'], 
                                'thumb_name' => $thumb ? $thumb : null, 
                                'sort_order' => $this->getNextMediaSortOrder($this->requestData['id'])
                            ));
                            
                            if (!$firstInsert) {
                                
                                $firstInsert = array(
                                    'id' => $this->models->MediaTaxon->getNewId(), 
                                    'name' => $file['name']
                                );
                            }
                            
                            if ($mt) {
                                
                                $this->addMessage(sprintf($this->translate('Saved: %s (%s)'), $file['original_name'], $file['media_name']));
                            }
                            else {
                                
                                $this->addError($this->translate('Failed writing uploaded file to database.'), 1);
                            }
                        }
                        
                        if (isset($_SESSION['admin']['system']['media']['newRef']) && $_SESSION['admin']['system']['media']['newRef'] == '<new>') {
                            
                            $_SESSION['admin']['system']['media']['newRef'] = '<span class="inline-' . substr($file['mime_type'], 0, strpos($file['mime_type'], '/')) . '" onclick="showMedia(\'' .
                             addslashes($_SESSION['admin']['project']['urls']['project_media'] . $file['name']) . '\',\'' . addslashes($file['name']) . '\');">' . $firstInsert['name'] . '</span>';
                            
                            $this->redirect('../species/taxon.php?id=' . $_SESSION['admin']['system']['activeTaxon']['taxon_id']);
                        }
                    }
                }
            }
            else {
                
                $this->addError($this->translate('Unknown taxon.'));
            }
            
            $this->smarty->assign('id', $this->requestData['id']);
            
            $this->smarty->assign('allowedFormats', $this->controllerSettings['media']['allowedFormats']);
            
            $this->smarty->assign('iniSettings', array(
                'upload_max_filesize' => ini_get('upload_max_filesize'), 
                'post_max_size' => ini_get('post_max_size')
            ));
        }
        else {
            
            $this->addError($this->translate('No taxon specified.'));
        }
        
        $this->printPage();
    }



    /**
     * Upload a file with taxa
     *
     * @access    public
     */
    public function fileAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxon file upload'));
        
        // uploaded file detected: parse csv
        if ($this->requestDataFiles) {
            
            unset($_SESSION['admin']['system']['csv_data']);
            
            /*
			switch ($this->requestData["enclosure"]) {
				case 'double' :
					$this->helpers->CsvParserHelper->setFieldEnclosure('"');
					break;
				case 'single' :
					$this->helpers->CsvParserHelper->setFieldEnclosure("'");
					break;
				case 'none' :
					$this->helpers->CsvParserHelper->setFieldEnclosure(false);
					break;
			}
			*/
            
            switch ($this->requestData["delimiter"]) {
                case 'comma':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(',');
                    break;
                case 'semi-colon':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(';');
                    break;
                case 'tab':
                    $this->helpers->CsvParserHelper->setFieldDelimiter("\t");
                    break;
            }
            
            $this->helpers->CsvParserHelper->setFieldMax($_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2);
            
            $this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
            
            $this->addError($this->helpers->CsvParserHelper->getErrors());
            
            if (!$this->getErrors()) {
                
                $r = $this->helpers->CsvParserHelper->getResults();
                
                $pr = $this->getProjectRanks();
                
                // get all ranks for this project
                foreach ((array) $pr as $key => $val) {
                    
                    $d[] = trim(strtolower($val['rank']));
                    
                    if ($_SESSION['admin']['project']['includes_hybrids'] && $val['can_hybrid'] == 1)
                        $h[] = trim(strtolower($val['rank']));
                }
                
                $upperTaxonRank = false;
                
                $prevNames = array();
                
                foreach ((array) $r as $key => $val) {
                    
                    // check whether 'has hybrid' is present and legal
                    if ($_SESSION['admin']['project']['includes_hybrids'])
                        $r[$key][2] = (isset($val[2]) && strtolower($val[2]) == 'y' && in_array(strtolower($val[1]), $h));
                    
                    $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2] = 'ok';
                    

                    // check whether the taxon name doesn't already exist
                    $t = $this->models->Taxon->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon' => $r[$key][0]
                        ), 
                        'columns' => 'count(*) as total'
                    ));
                    

                    if (in_array($val[0], $prevNames)) {
                        // set whether the taxon can be imported, based on whether it has duplicates in the import
                        



                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2] = $this->translate('Duplicate name');
                    }
                    else if ($t[0]['total'] != 0) {
                        // set whether the taxon can be imported, based on whether the name already exists
                        



                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2] = $this->translate('Name already exists in the database');
                    }
                    else if (!(isset($val[1]) && in_array(strtolower($val[1]), $d))) {
                        // set whether the taxon can be imported, based on whether it has a legal rank
                        



                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2] = $this->translate('Unknown rank');
                    }
                    else {
                        
                        $prevNames[] = $val[0];
                    }
                    
                    if ($upperTaxonRank == false && $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 3 : 2] == 'ok')
                        $upperTaxonRank = $val[1];
                }
                
                $upperTaxonRank = strtolower($upperTaxonRank);
                
                // check whether the uppermost taxa in the csv have to be connected to a previous taxon
                // if the first to be imported taxon is a kingdom (or rather, of the uppermost rank), do nothing: it can't have a parent
                if ($upperTaxonRank != strtolower($pr[0]['rank'])) {
                    
                    $parentRank = false;
                    
                    // find what rank a parent should be
                    foreach ((array) $pr as $key => $val) {
                        
                        if (strtolower($val['rank']) == $upperTaxonRank) {
                            
                            $parentRank = $pr[$key - 1];
                            
                            break;
                        }
                    }
                    
                    if ($parentRank) {
                        
                        $t = $this->models->Taxon->_get(
                        array(
                            'id' => array(
                                'project_id' => $this->getCurrentProjectId(), 
                                'rank_id' => $parentRank['id']
                            )
                        ));
                        
                        if (count($t) == 1) {
                            
                            $this->addMessage(sprintf($this->translate('The taxon or taxa of the rank "%s" will be connected as child to the already existing taxon "%s".'), $upperTaxonRank, $t[0]['taxon']));
                            
                            $this->smarty->assign('connectToTaxonId', $t[0]['id']);
                        }
                        else {
                            
                            $this->addMessage(sprintf($this->translate('There are multiple possible parents of the uppermost taxon or taxa. Please choose the appropriate one.')));
                            
                            $this->smarty->assign('connectToTaxonIds', $t);
                        }
                    }
                    else {
                        
                        $this->addError(sprintf($this->translate('Uppermost taxon is not a %s, and has a rank that has no immediate parent.'), $pr[0]['rank']));
                    }
                }
                
                $_SESSION['admin']['system']['csv_data'] = $r;
                
                $this->smarty->assign('results', $r);
            }
        }
        else if (isset($this->requestData)) {
            // list of taxa and ranks to be saved deteceted: save taxa
            



            if ($this->rHasVal('rows') && isset($_SESSION['admin']['system']['csv_data'])) {
                
                if ($this->rHasVal('connectToTaxonId')) {
                    
                    $t = $this->models->Taxon->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'id' => $this->requestData['connectToTaxonId']
                        )
                    ));
                    
                    $existingParent = $t[0]['taxon'];
                }
                else {
                    
                    $existingParent = false;
                }
                
                $parenName = false;
                $predecessors = null;
                
                // traverse the list of taxa
                foreach ((array) $this->requestData['rows'] as $key => $val) {
                    
                    $name = $_SESSION['admin']['system']['csv_data'][$val][0];
                    $rank = $_SESSION['admin']['system']['csv_data'][$val][1];
                    $hybrid = $_SESSION['admin']['project']['includes_hybrids'] ? $_SESSION['admin']['system']['csv_data'][$val][2] : false;
                    $parentName = null;
                    
                    if ($key == 0) {
                        // first one never has a parent (top of the tree) unless actively set or chosen
                        



                        $predecessors[] = array(
                            $rank, 
                            $name, 
                            $hybrid
                        );
                        
                        if ($existingParent)
                            $parentName = $existingParent;
                    }
                    else {
                        
                        if ($rank == $predecessors[count((array) $predecessors) - 1][0]) {
                            /* if this taxon has the same rank as the previous one, they must have the same
                            parent, so we go back in the list until we find the first different rank,
                            which must be the parent */
                            
                            $j = 1;
                            $prevRank = $rank;
                            while ($rank == $prevRank) {
                                
                                if (!isset($predecessors[count((array) $predecessors) - $j][0])) {
                                    
                                    if ($existingParent)
                                        $parentName = $existingParent;
                                    break;
                                }
                                else {
                                    
                                    $prevRank = $predecessors[count((array) $predecessors) - $j][0];
                                    $parentName = $predecessors[count((array) $predecessors) - $j][1];
                                }
                                
                                $j++;
                            }
                            
                            $predecessors[] = array(
                                $rank, 
                                $name, 
                                $hybrid
                            );
                        }
                        else {
                            
                            /* if rank came before then we are no longer in the first branch of the tree
                               and need to use the parent of the previous occurrence.
                               we ignore the immediately preceding taxon, because if that is the same as
                               the current one, we are simple still on the same level. */
                            foreach ((array) $predecessors as $key => $val) {
                                
                                if ($rank == $val[0] && $key != count((array) $predecessors) - 1) {
                                    // found a previous occurrence
                                    



                                    if (isset($predecessors[$key - 1])) {
                                        
                                        // get the name of the previous occurrence's parent
                                        $parentName = $predecessors[$key - 1][1];
                                        
                                        // apparantly we are at the start of a new branch, so chop off the previous one
                                        $predecessors = array_slice($predecessors, 0, $key);
                                        
                                        // and add the first child of the next one
                                        $predecessors[] = array(
                                            $rank, 
                                            $name, 
                                            $hybrid
                                        );
                                        
                                        break;
                                    }
                                }
                            }
                            
                            if ($parentName == null) {
                                // did not find a previous occurrence of the current rank, so the previous taxon must be the parent
                                



                                $parentName = $predecessors[count((array) $predecessors) - 1][1];
                                
                                $predecessors[] = array(
                                    $rank, 
                                    $name, 
                                    $hybrid
                                );
                            }
                        }
                    }
                    
                    $newId = $this->importTaxon(
                    array(
                        'taxon_rank' => $rank, 
                        'taxon_name' => $name, 
                        'parent_taxon_name' => $parentName, 
                        'hybrid' => $hybrid
                    ));
                    
                    if (!empty($newId) && empty($taxon['parent_taxon_name'])) {
                        
                        $this->doAssignUserTaxon($this->getCurrentUserId(), $newId);
                    }
                }
                
                $this->reOrderTaxonTree();
                
                unset($_SESSION['admin']['system']['csv_data']);
                
                $this->addMessage($this->translate('Data saved.'));
            }
        }
        
        $pr = $this->getProjectRanks();
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->printPage();
    }



    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;
        
        if ($this->requestData['action'] == 'save_taxon') {
            
            $c = $this->ajaxActionSaveTaxon();
            
            if (!$c)
                $this->smarty->assign('returnText', '<msg>Empty taxa are not shown');
        }
        else if ($this->requestData['action'] == 'get_taxon') {
            
            $this->ajaxActionGetTaxon();
            
            $_SESSION['admin']['system']['lastActivePage'] = $this->requestData['page'];
        }
        else if ($this->requestData['action'] == 'delete_taxon') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->ajaxActionDeleteTaxon();
        }
        else if ($this->requestData['action'] == 'delete_page') {
            
            $this->ajaxActionDeletePage();
        }
        else if ($this->requestData['action'] == 'get_page_labels') {
            
            $this->ajaxActionGetPageTitles();
        }
        else if ($this->requestData['action'] == 'save_page_title') {
            
            $this->ajaxActionSavePageTitle();
        }
        else if ($this->requestData['action'] == 'get_page_states') {
            
            $this->ajaxActionGetPageStates();
        }
        else if ($this->requestData['action'] == 'publish_content') {
            
            $this->ajaxActionPublishContent();
        }
        else if ($this->requestData['action'] == 'get_taxon_undo') {
            
            $this->ajaxActionGetTaxonUndo();
        }
        else if ($this->requestData['action'] == 'get_col') {
            
            $this->getCatalogueOfLifeData();
        }
        else if ($this->requestData['action'] == 'save_col') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->ajaxActionImportTaxa();
        }
        else if ($this->requestData['action'] == 'check_taxon_name') {
            
            if ($this->isTaxonNameUnique($this->requestData['taxon_name'], $this->rHasId() ? $this->requestData['id'] : null)) {
                
                $d = $this->isTaxonNameFirstPartLegal($this->requestData['taxon_name']);
                
                if ($d !== true) {
                    
                    $this->smarty->assign('returnText', sprintf($this->translate('Warning: "%s" does not exist.'), $d));
                }
                else {
                    
                    $this->smarty->assign('returnText', '<ok>');
                }
            }
            else {
                
                $this->smarty->assign('returnText', $this->translate('Taxon name already in database.'));
            }
        }
        else if ($this->requestData['action'] == 'save_taxon_name') {
            
            $this->clearCache($this->cacheFiles['list']);
            
            $this->ajaxActionSaveTaxonName();
        }
        else if ($this->requestData['action'] == 'save_media_desc') {
            
            $this->ajaxActionSaveMediaDescription();
        }
        else if ($this->requestData['action'] == 'get_media_desc') {
            
            $this->ajaxActionGetMediaDescription();
        }
        else if ($this->requestData['action'] == 'get_media_descs') {
            
            $this->ajaxActionGetMediaDescriptions();
        }
        else if ($this->requestData['action'] == 'delete_media') {
            
            $this->deleteTaxonMedia();
        }
        else if ($this->requestData['action'] == 'save_rank_label') {
            
            $this->ajaxActionSaveRankLabel();
        }
        else if ($this->requestData['action'] == 'get_rank_labels') {
            
            $this->ajaxActionGetRankLabels();
        }
        else if ($this->requestData['action'] == 'get_rank_by_parent') {
            
            // get intel on the taxon that will be the parent
            $d = $this->getTaxonById($this->requestData['id']);
            
            //// get the project RANK that is the child of the parent taxon's RANK 
            //$rank = $this->getProjectRankByParentProjectRank($d['rank_id']);
            

            // in some cases, certain children have to be skipped in favour of more likely progeny lower down the tree
            $rank = $this->getCorrectedProjectRankByParentProjectRank($d['rank_id']);
            
            $this->smarty->assign('returnText', $rank);
        }
        else if ($this->requestData['action'] == 'save_section_title') {
            
            $this->ajaxActionSaveSectionTitle();
        }
        else if ($this->requestData['action'] == 'delete_section_title') {
            
            $this->ajaxActionDeleteSectionTitle();
        }
        else if ($this->requestData['action'] == 'get_section_titles') {
            
            $this->ajaxActionGetSectionLabels();
        }
        else if ($this->requestData['action'] == 'get_language_labels') {
            
            $this->ajaxActionGetLanguageLabels();
        }
        else if ($this->requestData['action'] == 'save_language_label') {
            
            $this->ajaxActionSaveLanguageLabel();
        }
        else if ($this->requestData['action'] == 'get_subgenus_child_name_prefix') {
            
            $this->smarty->assign('returnText', $this->getSubgenusChildNamePrefix($this->requestData['id'])); // phew!
        }
        else if ($this->requestData['action'] == 'get_formatted_name') {
            
            $this->smarty->assign('returnText', 
            $this->formatTaxon(array(
                'taxon' => $this->requestData['name'], 
                'rank_id' => $this->requestData['rank_id'], 
                'parent_id' => $this->requestData['parent_id'],
                'is_hybrid' =>  $this->requestData['is_hybrid']
            )));
        }
        else if ($this->requestData['action'] == 'set_overview') {
            
            $this->setOverviewImageState($this->requestData['taxon_id'], $this->requestData['id'], $this->requestData['state']);
        }
        else if ($this->requestData['action'] == 'delete_variation') {
            
            $this->deleteVariation($this->requestData['id']);
        }
        else if ($this->rHasVal('action', 'get_lookup_list') && !empty($this->requestData['search'])) {
            
            $this->getLookupList($this->requestData['search']);
        }
        
        $this->printPage();
    }



    /**
     * Interface for getting taxon data from the Catalogue Of Life webservice (which is somewhat unreliable)
     *
     * @access    public
     */
    public function colAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Import from Catalogue Of Life'));
        
        $this->printPage();
    }



    /**
     * Enables the user to choose taxin ranks for the project
     *
     * @access    public
     */
    public function ranksAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxonomic ranks'));
        
        $pr = $this->models->ProjectRank->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'parent_id', 
            'fieldAsIndex' => 'rank_id'
        ));
        
        if ($this->rHasVal('ranks') && !$this->isFormResubmit()) {
            
            $parent = 'null';
            
            $isLowerTaxon = false;
            
            foreach ((array) $this->requestData['ranks'] as $key => $rank) {
                
                if ($this->requestData['higherTaxaBorder'] == $rank) {
                    
                    $isLowerTaxon = true;
                }
                
                if (!empty($pr[$rank])) {
                    
                    $this->models->ProjectRank->save(
                    array(
                        'id' => $pr[$rank]['id'], 
                        'parent_id' => $parent, 
                        'lower_taxon' => $isLowerTaxon ? 1 : 0
                    ));
                    
                    $parent = $pr[$rank]['id'];
                }
                else {
                    
                    $this->models->ProjectRank->save(
                    array(
                        'id' => null, 
                        'project_id' => $this->getCurrentProjectId(), 
                        'rank_id' => $rank, 
                        'parent_id' => $parent, 
                        'lower_taxon' => $isLowerTaxon ? 1 : 0
                    ));
                    
                    $parent = $this->models->ProjectRank->getNewId();
                }
            }
            
            $this->models->ProjectRank->update(array(
                'keypath_endpoint' => 0
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'lower_taxon' => 0
            ));
            

            foreach ((array) $pr as $key => $rank) {
                
                if (!in_array($rank['rank_id'], $this->requestData['ranks'])) {
                    
                    $pr = $this->models->ProjectRank->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'rank_id' => $rank['rank_id']
                        )
                    ));
                    
                    foreach ((array) $pr as $key => $val) {
                        
                        $this->models->LabelProjectRank->delete(array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'project_rank_id' => $val['id']
                        ));
                    }
                    
                    $this->models->ProjectRank->delete(array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'rank_id' => $rank['rank_id']
                    ));
                }
            }
            
            if (isset($_SESSION['admin']['project']['ranklist']))
                unset($_SESSION['admin']['project']['ranklist']);
            if (isset($_SESSION['admin']['user']['species']['tree']))
                unset($_SESSION['admin']['user']['species']['tree']);
            
            $this->addMessage($this->translate('Ranks saved.'));
        }
        
        $r = array_merge($this->models->Rank->_get(array(
            'id' => array(
                'parent_id is' => 'null'
            ), 
            'order' => 'parent_id', 
            'fieldAsIndex' => 'id'
        )), $this->models->Rank->_get(array(
            'id' => array(
                'parent_id !=' => -1
            ), 
            'order' => 'parent_id', 
            'fieldAsIndex' => 'id'
        )), $this->models->Rank->_get(array(
            'id' => array(
                'parent_id' => -1
            ), 
            'order' => 'parent_id', 
            'fieldAsIndex' => 'id'
        )));
        
        $pr = $this->getProjectRanks(array(
            'forceLookup' => true
        ));
        
        $this->smarty->assign('ranks', $r);
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->printPage();
    }



    /**
     * Enables the user to provide labels in all defined project languages
     *
     * @access    public
     */
    public function ranklabelsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxonomic ranks: labels'));
        
        $pr = $this->getProjectRanks(array(
            'includeLanguageLabels' => true
        ));
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->smarty->assign('languages', $_SESSION['admin']['project']['languages']);
        
        $this->printPage();
    }



    /**
     * Create standard sections for taxon content pages
     *
     * @access    public
     */
    public function sectionsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Define sections'));
        
        if ($this->rHasVal('new') && !$this->isFormResubmit()) {
            
            foreach ((array) $this->requestData['new'] as $key => $val) {
                
                $this->models->Section->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'page_id' => $key, 
                    'section' => $val
                ));
            }
        }
        
        $lp = $_SESSION['admin']['project']['languages'];
        
        $pages = $this->models->PageTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => '*', 
            'order' => 'show_order'
        ));
        
        foreach ((array) $pages as $key => $val) {
            
            $s = $this->models->Section->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'page_id' => $val['id']
                ), 
                'columns' => '*, ifnull(show_order,999) as show_order', 
                'order' => 'show_order'
            ));
            
            $pages[$key]['sections'] = $s;
        }
        
        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());
        
        $this->printPage();
    }



    /**
     * Assign parts of the taxon tree to specific collaborators so they can edit only those
     *
     * @access    public
     */
    public function collaboratorsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Assign taxa to collaborators'));
        
        if (isset($this->requestData) && !$this->isFormResubmit()) {
            
            if ($this->rHasVal('delete')) {
                
                $this->models->UserTaxon->delete(array(
                    'id' => $this->requestData['delete'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            else {
                
                $this->doAssignUserTaxon($this->requestData['user_id'], $this->requestData['taxon_id']);
            }
            
            unset($_SESSION['admin']['species']['usertaxa']);
        }
        
        $users = $this->getProjectUsers();
        
        $this->newGetTaxonTree();
        
        if (isset($this->treeList)) {
            
            $ut = $this->models->UserTaxon->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'order' => 'taxon_id'
            ));
            
            foreach ((array) $ut as $key => $val) {
                
                $ut[$key]['taxon'] = $this->getTaxonById($val['taxon_id']);
            }
            
            $this->smarty->assign('usersTaxa', $ut);
            
            if (isset($users))
                $this->smarty->assign('users', $users);
            
            $this->smarty->assign('taxa', $this->treeList);
        }
        else {
            
            $this->addMessage($this->translate('No taxa have been defined.'));
        }
        
        $this->printPage();
    }



    /**
     * Create synonyms for a taxon
     *
     * @access    public
     */
    public function synonymsAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->rHasId()) {
            
            $taxon = $this->getTaxonById();
            
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Synonyms for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Synonyms for "%s"'), $taxon['taxon']));
            }
            

            /*
			$this->setBreadcrumbIncludeReferer(
				array(
					'name' => $this->translate('Taxon list'), 
					'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
				)
			);			
*/
        }
        else {
            
            $this->redirect();
        }
        
        if (!$this->isFormResubmit()) {
            
            if ($this->rHasVal('action', 'delete')) {
                
                $this->models->Synonym->delete(array(
                    'id' => $this->requestData['synonym_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
                
                $synonyms = $this->models->Synonym->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $this->requestData['id']
                    ), 
                    'order' => 'show_order'
                ));
                
                foreach ((array) $synonyms as $key => $val) {
                    
                    $this->models->Synonym->save(
                    array(
                        'id' => $val['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'show_order' => $key
                    ));
                    
                    $synonyms[$key]['show_order'] = $key;
                }
            }
            
            if ($this->rHasVal('action', 'up') || $this->rHasVal('action', 'down')) {
                
                $s = $this->models->Synonym->_get(
                array(
                    'id' => array(
                        'id' => $this->requestData['synonym_id'], 
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));
                
                $this->models->Synonym->update(array(
                    'show_order' => $s[0]['show_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'show_order' => ($this->requestData['action'] == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ));
                
                $this->models->Synonym->update(array(
                    'show_order' => ($this->requestData['action'] == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ), array(
                    'id' => $this->requestData['synonym_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            
            if ($this->rHasVal('synonym')) {
                
                $s = $this->models->Synonym->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $this->requestData['id']
                    ), 
                    'columns' => 'max(show_order) as next'
                ));
                
                $show_order = $s[0]['next'] == null ? 0 : ($s[0]['next'] + 1);
                
                $this->models->Synonym->save(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id'], 
                    'lit_ref_id' => $this->rHasVal('lit_ref_id') ? $this->requestData['lit_ref_id'] : null, 
                    'synonym' => $this->requestData['synonym'], 
                    'author' => $this->rHasVal('author') ? $this->requestData['author'] : null, 
                    'show_order' => $show_order
                ));
                
                //				echo $this->models->Synonym->getLastQuery();die();
            }
        }
        
        //		$literature = $this->getAllLiterature();
        



        if (!isset($synonyms)) {
            
            $synonyms = $this->models->Synonym->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id']
                ), 
                'order' => 'show_order'
            ));
            /*
			foreach((array)$synonyms as $key => $val) {

				if($val['lit_ref_id']) {

					$synonyms[$key]['literature'] = $this->doMultiArrayFind($literature,'id',$val['lit_ref_id']);
					$synonyms[$key]['literature'] = array_shift($synonyms[$key]['literature']);

				}

			}
*/
        }
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        //		$this->smarty->assign('literature', $literature);
        



        $this->smarty->assign('id', $this->requestData['id']);
        
        $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('synonyms', $synonyms);
        
        $this->printPage();
    }



    /**
     * Create common names for a taxon
     *
     * @access    public
     */
    public function commonAction ()
    {
        $this->checkAuthorisation();
        
        if ($this->rHasId()) {
            
            $taxon = $this->getTaxonById();
            
            if ($this->maskAsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Common names for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Common names for "%s"'), $taxon['taxon']));
            }
            
            /*
			$this->setBreadcrumbIncludeReferer(
				array(
					'name' => $this->translate('Taxon list'), 
					'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
				)
			);
*/
        }
        else {
            
            $this->redirect();
        }
        
        if (!$this->isFormResubmit()) {
            
            if ($this->rHasVal('action', 'delete')) {
                
                $this->models->Commonname->delete(array(
                    'id' => $this->requestData['commonname_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
                
                $commonnames = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $this->requestData['id']
                    ), 
                    'order' => 'show_order'
                ));
                
                foreach ((array) $commonnames as $key => $val) {
                    
                    $this->models->Commonname->save(
                    array(
                        'id' => $val['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'show_order' => $key
                    ));
                    
                    $commonnames[$key]['show_order'] = $key;
                }
            }
            
            if ($this->rHasVal('action', 'up') || $this->rHasVal('action', 'down')) {
                
                $s = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'id' => $this->requestData['commonname_id'], 
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));
                
                $this->models->Commonname->update(array(
                    'show_order' => $s[0]['show_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'show_order' => ($this->requestData['action'] == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ));
                
                $this->models->Commonname->update(array(
                    'show_order' => ($this->requestData['action'] == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ), array(
                    'id' => $this->requestData['commonname_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            
            if ($this->rHasVal('commonname') || $this->rHasVal('transliteration')) {
                
                $s = $this->models->Commonname->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon_id' => $this->requestData['id']
                    ), 
                    'columns' => 'max(show_order) as next'
                ));
                
                $show_order = $s[0]['next'] == null ? 0 : ($s[0]['next'] + 1);
                
                $this->models->Commonname->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id'], 
                    'language_id' => $this->requestData['language_id'], 
                    'commonname' => $this->requestData['commonname'], 
                    'transliteration' => $this->requestData['transliteration'], 
                    'show_order' => $show_order
                ));
                
                $this->smarty->assign('lastLanguage', $this->requestData['language_id']);
            }
        }
        
        // get all languages
        $allLanguages = $this->getAllLanguages();
        
        // get project languages
        $lp = $_SESSION['admin']['project']['languages'];
        
        if (!isset($commonnames)) {
            
            $commonnames = $this->models->Commonname->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id']
                ), 
                'order' => 'show_order'
            ));
        }
        
        if (isset($commonnames)) {
            
            foreach ((array) $commonnames as $key => $val) {
                
                $commonnames[$key]['language_name'] = $allLanguages[$val['language_id']]['language'];
            }
        }
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList());
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('id', $this->requestData['id']);
        
        if ($taxon)
            $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('commonnames', $commonnames);
        
        $this->smarty->assign('allLanguages', $allLanguages);
        
        $this->smarty->assign('languages', $lp);
        
        $this->printPage();
    }



    public function variationsAction ()
    {
        $this->checkAuthorisation();
        
        if (!$this->rHasVal('id') && $this->rHasVal('var')) {
            $d = $this->getVariation($this->requestData['var']);
            $taxon = $this->getTaxonById($d['taxon_id']);
        }
        else {
            $taxon = $this->getTaxonById();
        }
        
        $this->setPageName(sprintf($this->translate('Variations for "%s"'), $taxon['taxon']));
        
        if (!$this->isFormResubmit() && $this->rHasVal('variation')) {
            
            $v = $this->models->TaxonVariation->save(
            array(
                'id' => null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $this->requestData['id'], 
                'label' => trim($this->requestData['variation'])
            ));
            
            if ($v) {
                
                $nId = $this->models->TaxonVariation->getNewId();
                
                $this->models->VariationLabel->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $nId, 
                    'language_id' => $this->getDefaultProjectLanguage(), 
                    'label' => trim($this->requestData['variation']), 
                    'label_type' => 'alternative'
                ));
            }
        }
        
        $variations = $this->getVariations($taxon['id']);
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('id', $taxon['id']);
        
        $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('variations', $variations);
        
        $this->printPage();
    }



    public function relatedAction ()
    {
        if (!$this->useRelated)
            $this->redirect('index.php');
        
        $this->checkAuthorisation();
        
        $taxon = $this->getTaxonById();
        
        if ($this->useVariations)
            $this->setPageName(sprintf($this->translate('Related taxa and variations for "%s"'), $taxon['taxon']));
        else
            $this->setPageName(sprintf($this->translate('Related taxa for "%s"'), $taxon['taxon']));
        
        $related = $this->getRelatedEntities($taxon['id']);
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('id', $taxon['id']);
        
        $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('related', $related);
        
        $this->printPage();
    }



    public function nbcExtrasAction ()
    {
        if (!$this->_useNBCExtras)
            $this->redirect('index.php');
        
        $this->checkAuthorisation();
        
        $taxon = $this->getTaxonById();
        
        $this->setPageName(sprintf($this->translate('Additional NBC data for "%s"'), $taxon['taxon']));
        
        $data = $this->models->NbcExtras->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'ref_id' => $taxon['id'], 
                'ref_type' => 'taxon'
            ), 
            'order' => 'name'
        ));
        
        $varData = $this->models->TaxonVariation->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $taxon['id']
            )
        ));
        


        foreach ((array) $varData as $key => $val) {
            
            $varData[$key]['data'] = $this->models->NbcExtras->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'ref_id' => $val['id'], 
                    'ref_type' => 'variation'
                ), 
                'order' => 'name'
            ));
        }
        
        $this->smarty->assign('navList', $this->newGetUserAssignedTaxonTreeList(array(
            'higherOnly' => $this->maskAsHigherTaxa()
        )));
        
        $this->smarty->assign('navCurrentId', $taxon['id']);
        
        $this->smarty->assign('id', $taxon['id']);
        
        $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('data', $data);
        
        $this->smarty->assign('varData', $varData);
        
        $this->printPage();
    }



    public function previewAction ()
    {
        $this->redirect('../../../app/views/species/taxon.php?p=' . $this->getCurrentProjectId() . '&id=' . $this->requestData['taxon_id'] . '&cat=' . $this->requestData['activePage'] . '&lan=' . $this->getDefaultProjectLanguage());
    }



    private function initialize ()
    {
        $this->createStandardCategories();
        
        $this->createStandardCoLRanks();
        
        $this->smarty->assign('heartbeatFrequency', $this->generalSettings['heartbeatFrequency']);
        
        $this->includeLocalMenu = true;
        
        // variations & related are only shown for NBC matrix projects
        $this->_useNBCExtras = $this->useRelated = $this->useVariations = $this->getSetting('matrixtype') == 'NBC';
        
        $this->smarty->assign('useNBCExtras', $this->_useNBCExtras);
        $this->smarty->assign('useRelated', $this->useRelated);
        $this->smarty->assign('useVariations', $this->useVariations);
        $this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());
    }



    private function getRankList ()
    {
        if (isset($_SESSION['admin']['project']['ranklist']) && (isset($_SESSION['admin']['project']['ranklistsource']) && $_SESSION['admin']['project']['ranklistsource'] == ($this->maskAsHigherTaxa() ? 'highertaxa' : 'lowertaxa'))) {
            
            $rl = $_SESSION['admin']['project']['ranklist'];
        }
        else {
            
            $pr = $this->getProjectRanks();
            
            foreach ((array) $pr as $key => $val) {
                
                if (!$this->maskAsHigherTaxa() && $val['lower_taxon'] == 1) {
                    // only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
                    



                    $rl[$val['id']] = $val['rank'];
                }
                else if ($this->maskAsHigherTaxa() && $val['lower_taxon'] != 1) {
                    // only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
                    



                    $rl[$val['id']] = $val['rank'];
                }
            }
            
            if (isset($rl))
                $_SESSION['admin']['project']['ranklist'] = $rl;
            $_SESSION['admin']['project']['ranklistsource'] = ($this->maskAsHigherTaxa() ? 'highertaxa' : 'lowertaxa');
        }
        
        return $rl;
    }



    private function getCatalogueOfLifeData ()
    {
        if ($this->rHasVal('taxon_name')) {
            
            set_time_limit(TIMEOUT_COL_RETRIEVAL);
            
            $this->helpers->ColLoaderHelper->setTimerInclusion(false);
            
            $this->helpers->ColLoaderHelper->setResultStyle('concise');
            
            if ($this->rHasVal('taxon_name')) {
                
                $this->helpers->ColLoaderHelper->setTaxonName($this->requestData['taxon_name']);
            }
            
            if ($this->rHasVal('taxon_id')) {
                
                $this->helpers->ColLoaderHelper->setTaxonId($this->requestData['taxon_id']);
            }
            
            if ($this->rHasVal('levels')) {
                
                $this->helpers->ColLoaderHelper->setNumberOfChildLevels($this->requestData['levels']);
            }
            
            $this->helpers->ColLoaderHelper->setTimeout(TIMEOUT_COL_RETRIEVAL);
            
            $this->helpers->ColLoaderHelper->getTaxon();
            
            $data = $this->helpers->ColLoaderHelper->getResult();
            
            if (!$data) {
                
                $this->addError($this->helpers->ColLoaderHelper->getErrors());
            }
            else {
                
                $this->smarty->assign('returnText', json_encode($data));
            }
        }
    }



    private function createStandardCategories ()
    {
        $tp = $this->models->PageTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        ));
        

        foreach ((array) $this->controllerSettings['defaultCategories'] as $key => $page) {
            
            if ($tp[0]['total'] == 0) {
                
                if ($this->createTaxonCategory($this->translate($page['name']), $key, isset($page['default']) && $page['default'])) {
                    
                    $this->createTaxonCategorySections($page['sections'], $this->models->PageTaxon->getNewId());
                }
            }
            else {
                
                if (isset($page['mandatory']) && $page['mandatory'] === true) {
                    
                    $d = $this->models->PageTaxon->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'page' => $page['name']
                        ), 
                        'columns' => 'count(*) as total'
                    ));
                    
                    if ($d[0]['total'] == 0) {
                        
                        if ($this->createTaxonCategory($this->translate($page['name']), $key, isset($page['default']) && $page['default'])) {
                            
                            $this->createTaxonCategorySections($page['sections'], $this->models->PageTaxon->getNewId());
                        }
                    }
                }
            }
        }
    }



    private function createTaxonCategory ($name, $show_order = false, $isDefault = false)
    {
        return $this->models->PageTaxon->save(
        array(
            'id' => null, 
            'page' => $name, 
            'show_order' => $show_order !== false ? $show_order : 0, 
            'project_id' => $this->getCurrentProjectId(), 
            'def_page' => $isDefault ? '1' : '0'
        ));
    }



    private function createTaxonCategorySections ($sections, $pageId)
    {
        foreach ((array) $sections as $key => $val) {
            
            $this->models->Section->save(
            array(
                'id' => null, 
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $pageId, 
                'section' => $val, 
                'show_order' => $key
            ));
        }
    }



    private function doLockOutUser ($taxonId, $lockOutOfAllScreens = false)
    {
        if (empty($taxonId))
            return false;
        
        $this->models->Heartbeat->cleanUp($this->getCurrentProjectId(), ($this->generalSettings['heartbeatFrequency']));
        
        $d = array(
            'project_id =' => $this->getCurrentProjectId(), 
            'app' => $this->getAppName(), 
            'ctrllr' => 'species', 
            'params' => serialize(array(
                array(
                    'taxon_id', 
                    $taxonId
                )
            )), 
            'user_id !=' => $this->getCurrentUserId()
        );
        
        if ($lockOutOfAllScreens !== true)
            $d['view'] = $this->getViewName();
        
        $h = $this->models->Heartbeat->_get(array(
            'id' => $d
        ));
        

        return isset($h) ? true : false;
    }



    private function ajaxActionDeletePage ()
    {
        if (!$this->rHasId()) {
            
            return;
        }
        else {
            
            $this->models->ContentTaxon->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['id']
            ));
            
            $this->models->PageTaxonTitle->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['id']
            ));
            
            $this->models->Section->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['id']
            ));
            
            $this->models->PageTaxon->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $this->requestData['id']
            ));
        }
    }



    private function ajaxActionGetPageTitles ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $l = $this->models->Language->_get(array(
                'id' => $this->requestData['language'], 
                'columns' => 'direction'
            ));
            
            $ptt = $this->models->PageTaxonTitle->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language']
                ), 
                'columns' => 'id,title,page_id,language_id,\'' . $l['direction'] . '\' as direction'
            ));
            
            $this->smarty->assign('returnText', json_encode($ptt));
        }
    }



    private function ajaxActionSavePageTitle ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('label')) {
                
                $this->models->PageTaxonTitle->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id']
                ));
            }
            else {
                
                $tpt = $this->models->PageTaxonTitle->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'page_id' => $this->requestData['id']
                    )
                ));
                
                $this->models->PageTaxonTitle->save(
                array(
                    'id' => isset($tpt[0]['id']) ? $tpt[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id'], 
                    'title' => trim($this->requestData['label'])
                ));
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function saveOldTaxonContentData ($data, $newdata = false, $mode = 'auto', $label = false)
    {
        $d = $data[0];
        
        // only back up if something changed (and we ignore the 'publish' setting)
        if (isset($newdata['content']) && ($d['content'] == $newdata['content']))
            return;
        
        $d['save_type'] = $mode;
        
        if ($label)
            $d['save_label'] = $label;
        
        if (isset($d['id']))
            $d['content_taxa_id'] = $d['id'];
        
        $d['id'] = null;
        
        if (isset($d['created']))
            $d['content_taxa_created'] = $d['created'];
        unset($d['created']);
        
        if (isset($d['last_change']))
            $d['content_last_change'] = $d['last_change'];
        unset($d['last_change']);
        
        $this->models->ContentTaxonUndo->save($d);
    }



    private function ajaxActionSaveTaxon ()
    {
        
        // new taxon
        if (!$this->rHasId()) {
            
            $d = $this->models->Taxon->save(
            array(
                'id' => $this->rHasId() ? $this->requestData['id'] : null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => $this->rHasVal('name') ? $this->requestData['name'] : '?'
            ));
            
            $taxonId = $this->models->Taxon->getNewId();
            
            $new = true;
        }
        else {
            // existing taxon 
            



            $d = true;
            
            $taxonId = $this->requestData['id'];
            
            $new = false;
        }
        
        if ($d) {
            // save of new taxon succeeded, or existing taxon
            



            // must have a language
            if ($this->rHasVal('language')) {
                
                // must have a page name
                if ($this->rHasVal('page')) {
                    
                    if (!$this->rHasVal('name') && !$this->rHasVal('content')) {
                        
                        if (!$this->rHasVal('save_type'))
                            $this->requestData['save_type'] = 'auto';
                        
                        $this->models->ContentTaxon->setRetainBeforeAlter();
                        
                        // no page title and no content equals an empty page: delete
                        $ct = $this->models->ContentTaxon->delete(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $this->requestData['language'], 
                            'page_id' => $this->requestData['page']
                        ));
                        
                        $this->saveOldTaxonContentData($this->models->ContentTaxon->getRetainedData(), false, $this->requestData['save_type']);
                        
                        // Mark taxon as 'empty'
                        $this->models->Taxon->update(array(
                            'is_empty' => 1
                        ), array(
                            'id' => $taxonId
                        ));
                    }
                    else {
                        
                        // see if such content already exists
                        $ct = $this->models->ContentTaxon->_get(
                        array(
                            'id' => array(
                                'project_id' => $this->getCurrentProjectId(), 
                                'taxon_id' => $taxonId, 
                                'language_id' => $this->requestData['language'], 
                                'page_id' => $this->requestData['page']
                            )
                        ));
                        
                        $id = count((array) $ct) != 0 ? $ct[0]['id'] : null;
                        
                        if ($id != null)
                            $this->models->ContentTaxon->setRetainBeforeAlter();
                        
                        $filteredContent = $this->filterContent($this->requestData['content']);
                        
                        $newdata = array(
                            'id' => $id, 
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $this->requestData['language'], 
                            'content' => !empty($filteredContent['content']) ? $filteredContent['content'] : '', 
                            'title' => $this->rHasVal('name') ? $this->requestData['name'] : '', 
                            'page_id' => $this->requestData['page']
                        );
                        
                        // save content
                        $d = $this->models->ContentTaxon->save($newdata);
                        
                        // Mark taxon as 'empty/not empty' depending on presence of contents
                        $this->models->Taxon->update(array(
                            'is_empty' => !empty($this->requestData['content']) ? 0 : 1
                        ), array(
                            'id' => $taxonId
                        ));
                        
                        if ($id != null)
                            $this->saveOldTaxonContentData($this->models->ContentTaxon->getRetainedData(), $newdata, $this->requestData['save_type']);
                    }
                    
                    if ($d) {
                        
                        /* the block below changed the taxon's name in the taxon table to whatever the user had 
                           entrered as category title of the default page in the default language, but the assumption
                           that that is the place where the leading name of a taxon is entered might be faulty

                        // if succesful, get the projects default language
                        $lp = $_SESSION['admin']['project']['languages'];
                        
                        $defaultLanguage = $this->getDefaultProjectLanguage();

                        // get the main page content for the default language
                        $ct = $this->models->ContentTaxon->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(), 
									'taxon_id' => $taxonId, 
									'language_id' => $defaultLanguage
								)
							)
						);
                        
                        // save the title of that page as taxon name in the taxon table
                        $this->models->Taxon->save(
                        array(
                            'id' => $taxonId, 
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon' => !empty($ct[0]['title']) ? $ct[0]['title'] : '?'
                        ));
                        */
                        
                        //$this->smarty->assign('returnText', 'id=' . $taxonId);
                        



                        $this->smarty->assign('returnText', 
                        json_encode(
                        array(
                            'id' => $taxonId, 
                            'content' => isset($filteredContent) ? $filteredContent['content'] : null, 
                            'modified' => isset($filteredContent) ? $filteredContent['modified'] : null
                        )));
                    }
                    else {
                        
                        $this->addError($this->translate('Could not save taxon content.'));
                    }
                }
                else {
                    
                    $this->addError($this->translate('No page title specified.'));
                }
            }
            else {
                
                $this->addError($this->translate('No language specified.'));
            }
        }
        else {
            
            $this->addError($this->translate('Could not save taxon.'));
        }
        
        // Return if taxon has content in any language
        $c = $this->models->ContentTaxon->_get(array(
            'where' => 'taxon_id = ' . $taxonId
        ));
        
        return empty($c) && !$new ? false : true;
    }



    private function filterContent ($content)
    {
        if (!$this->controllerSettings['filterContent'])
            return $content;
        
        $modified = $content;
        
        if ($this->controllerSettings['filterContent']['html']['doFilter']) {
            
            $modified = strip_tags($modified, $this->controllerSettings['filterContent']['html']['allowedTags']);
        }
        
        return array(
            'content' => $modified, 
            'modified' => $content != $modified
        );
    }



    private function ajaxActionGetTaxon ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $ct = $this->models->ContentTaxon->_get(
            array(
                'id' => array(
                    'taxon_id' => $this->requestData['id'], 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['page']
                )
            ));
            
            if (empty($ct[0])) {
                
                $c = array(
                    'project_id' => $this->requestData['id'], 
                    'taxon_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['page'], 
                    'content' => $this->getDefaultPageSections($this->requestData['page'], $this->requestData['language']), 
                    'publish' => '0', 
                    'title' => null
                );
            }
            else {
                
                $c = $ct[0];
            }
            
            $this->smarty->assign('returnText', json_encode($c));
        }
    }



    private function deleteTaxon ($id)
    {
        if (!$id)
            return;
            
            // delete literary references
        $this->models->LiteratureTaxon->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $id
        ));
        
        // reset keychoice end-points
        $this->models->ChoiceKeystep->update(array(
            'res_taxon_id' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'res_taxon_id' => $id
        ));
        
        // delete commonnames
        $this->models->Commonname->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $id
        ));
        
        // delete synonyms
        $this->models->Synonym->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $id
        ));
        
        // purge undo
        $this->models->ContentTaxonUndo->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $id
        ));
        
        // delete taxon tree branch rights
        $this->models->UserTaxon->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $id
        ));
        
        // detele media
        $mt = $this->models->MediaTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            )
        ));
        
        foreach ((array) $mt as $key => $val) {
            
            $this->deleteTaxonMedia($val['id'], false);
        }
        
        // reset parentage
        $this->models->Taxon->update(array(
            'parent_id' => 'null'
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'parent_id' => $id
        ));
        
        // delete content
        $this->models->ContentTaxon->delete(array(
            'taxon_id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
        
        // delete taxon
        $this->models->Taxon->delete(array(
            'id' => $id, 
            'project_id' => $this->getCurrentProjectId()
        ));
        
        $this->reOrderTaxonTree();
    }



    private function deleteTaxonBranch ($id)
    {
        if (!$id)
            return;
            
            // get entire branch beneath the taxon
        $this->getTaxonTree(array(
            'pId' => $id
        ));
        
        if (isset($this->treeList)) {
            
            // delete from the bottom up
            foreach ((array) array_reverse($this->treeList) as $treeKey => $val) {
                
                $this->deleteTaxon($val['id']);
            }
        }
    }



    private function ajaxActionDeleteTaxon ()
    {
        if (!$this->rHasId()) {
            
            return;
        }
        else {
            
            $t = $this->models->Taxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'parent_id' => $this->requestData['id']
                ), 
                'columns' => 'count(*) as total'
            ));
            
            if ($t[0]['total'] != 0) {
                
                $this->smarty->assign('returnText', '<redirect>');
            }
            else {
                
                $this->deleteTaxon($this->requestData['id']);
                
                $this->smarty->assign('returnText', '<ok>');
            }
        }
    }



    private function ajaxActionGetPageStates ()
    {
        
        // see if such content already exists
        $ct = $this->models->ContentTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $this->requestData['id'], 
                'language_id' => $this->requestData['language']
            ), 
            'columns' => 'page_id,publish'
        ));
        
        foreach ((array) $ct as $key => $val) {
            
            $d[] = array(
                'page_id' => $val['page_id'], 
                'publish' => $val['publish']
            );
        }
        
        $this->smarty->assign('returnText', isset($d) ? json_encode($d) : null);
    }



    private function ajaxActionPublishContent ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language') || !$this->rHasVal('page') || !$this->rHasVal('state')) {
            
            $this->smarty->assign('returnText', $this->translate('Parameters incomplete.'));
        }
        else {
            
            $ct = $this->models->ContentTaxon->_get(
            array(
                'id' => array(
                    'taxon_id' => $this->requestData['id'], 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['page']
                )
            ));
            
            if (!empty($ct[0])) {
                
                $d = $this->models->ContentTaxon->update(array(
                    'publish' => $this->requestData['state']
                ), 
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id'], 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['page']
                ));
                
                if ($d) {
                    
                    $this->smarty->assign('returnText', '<ok>');
                }
                else {
                    
                    $this->smarty->assign('returnText', $this->translate('Could not save new publish state.'));
                }
            }
            else {
                
                $this->smarty->assign('returnText', $this->translate('Content not found.'));
            }
        }
    }



    private function ajaxActionGetTaxonUndo ()
    {
        if (!$this->rHasId()) {
            
            return;
        }
        else {
            
            $ctu = $this->models->ContentTaxonUndo->_get(
            array(
                'id' => array(
                    'taxon_id' => $this->requestData['id'], 
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'order' => 'content_last_change desc', 
                'limit' => 1
            ));
            
            if ($ctu) {
                
                $d = $ctu[0];
                
                $this->models->ContentTaxonUndo->delete($d['id']);
                
                $d['id'] = $d['content_taxa_id'];
                unset($d['content_taxa_id']);
                
                $d['created'] = $d['content_taxa_created'];
                unset($d['content_taxa_created']);
                
                $d['last_change'] = $d['content_last_change'];
                unset($d['content_last_change']);
                
                $this->smarty->assign('returnText', json_encode($ctu[0]));
            }
        }
    }



    private function importTaxon ($taxon)
    {
        if (empty($taxon['taxon_name']))
            return;
        
        $rankId = null;
        
        if (is_numeric($taxon['taxon_rank'])) {
            
            $rankId = $taxon['taxon_rank'];
        }
        else {
            
            $r = $this->models->Rank->_get(array(
                'id' => array(
                    'rank' => $taxon['taxon_rank']
                )
            ));
            
            if ($r == false)
                return;
            
            $pr = $this->models->ProjectRank->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'rank_id' => $r[0]['id']
                )
            ));
            
            $rankId = $pr[0]['id'];
        }
        
        if (is_null($rankId))
            return;
        
        $t = $this->models->Taxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => $taxon['taxon_name']
            )
        ));
        
        if (count((array) $t[0]) == 0) {
            // taxon does not exist in database
            



            if (!empty($taxon['parent_taxon_name'])) {
                
                // see if the parent taxon already exists
                $p = $this->models->Taxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon' => $taxon['parent_taxon_name']
                    )
                ));
            }
            
            if (isset($p) && count((array) $p) == 1) {
                
                $pId = $p[0]['id'];
            }
            else {
                
                $pId = null;
            }
            
            // save taxon
            $this->models->Taxon->save(
            array(
                'id' => null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => $taxon['taxon_name'], 
                'parent_id' => $pId, 
                'rank_id' => $rankId, 
                'is_hybrid' => isset($taxon['hybrid']) && $this->canRankBeHybrid($rankId) ? 1 : 0
            ));
            
            return $this->models->Taxon->getNewId();
        }
        else {
            // taxon does exist in database
            



            if (empty($t[0]['rank']) || empty($t[0]['parent_id'])) {
                
                $pId = null;
                
                if (empty($t[0]['parent_id']) && !empty($taxon['parent_taxon_name'])) {
                    
                    // see if the parent taxon already exists
                    $p = $this->models->Taxon->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon' => $taxon['parent_taxon_name']
                        )
                    ));
                    
                    if (isset($p) && count((array) $p) == 1) {
                        
                        $pId = $p[0]['id'];
                    }
                }
                
                $this->models->Taxon->save(
                array(
                    'id' => $t[0]['id'], 
                    'project_id' => $this->getCurrentProjectId(), 
                    'parent_id' => (empty($t[0]['parent_id']) ? $pId : $t[0]['parent_id']), 
                    'rank_id' => $rankId, 
                    'is_hybrid' => isset($taxon['hybrid']) && $this->canRankBeHybrid($rankId) ? 1 : 0
                ));
                


                return $t[0]['id'];
            }
        }
    }



    private function newIsTaxonNameUnique ($p)
    {
        $name = trim(isset($p['name']) ? $p['name'] : null);
        $rankId = isset($p['rankId']) ? $p['rankId'] : null;
        $parentId = isset($p['parentId']) ? $p['parentId'] : null;
        $ignoreId = isset($p['ignoreId']) ? $p['ignoreId'] : null;
        
        if (empty($name))
            return;

        $d = array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon' => trim($taxonName)
        );
        
        if (!empty($idToIgnore))
            $d['id !='] = $idToIgnore;
        
        $t = $this->models->Taxon->_get(array(
            'id' => $d
        ));
    }



    private function isTaxonNameUnique ($taxonName = false, $idToIgnore = null)
    {
        $taxonName = $taxonName ? $taxonName : $this->requestData['taxon_name'];
        
        if (empty($taxonName))
            return;
        
        if (!empty($idToIgnore)) {
            
            $t = $this->models->Taxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon' => trim($taxonName), 
                    'id != ' => $idToIgnore
                ), 
                'columns' => 'count(*) as total'
            ));
        }
        else {
            
            $t = $this->models->Taxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon' => trim($taxonName)
                ), 
                'columns' => 'count(*) as total'
            ));
        }
        
        return $t[0]['total'] == 0;
    }



    private function ajaxActionSaveTaxonName ()
    {
        if (!$this->rHasVal('taxon_name') || !$this->rHasVal('taxon_id'))
            return;
        
        $t = $this->models->Taxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $this->requestData['taxon_id']
            ), 
            'columns' => 'count(*) as total'
        ));
        
        if ($t[0]['total'] > 0) {
            
            $d = $this->models->Taxon->save(array(
                'id' => $this->requestData['taxon_id'], 
                'taxon' => trim($this->requestData['taxon_name'])
            ));
            
            if ($d)
                $this->smarty->assign('returnText', '<ok>');
        }
    }



    private function reOrderTaxonTree ()
    {
        $this->getTaxonTree();
        
        $i = 0;
        
        foreach ((array) $this->treeList as $key => $val) {
            
            $this->models->Taxon->save(array(
                'id' => $val['id'], 
                'taxon_order' => $i++, 
                'list_level' => $val['level']
            ));
        }
    }



    private function ajaxActionImportTaxa ()
    {
        if (!$this->rHasVal('data'))
            return;
        
        foreach ((array) $this->requestData['data'] as $key => $val) {
            
            $t['taxon_id'] = $val[0];
            $t['taxon_name'] = $val[1];
            $t['taxon_rank'] = $val[2];
            $t['parent_taxon_name'] = $val[3];
            
            $id = $this->importTaxon($t);
            
            // assign the topmost taxon to the current user, so he can actually see the tree branch
            if ($key == 0)
                $this->doAssignUserTaxon($this->getCurrentUserId(), $id);
        }
    }



    private function getDefaultPageSections ($pageId, $languageId)
    {
        $s = $this->models->Section->_get(
        array(
            'id' => array(
                'page_id' => $pageId, 
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'show_order asc'
        ));
        
        $b = '';
        
        foreach ((array) $s as $key => $val) {
            
            $ls = $this->models->LabelSection->_get(
            array(
                'id' => array(
                    'section_id' => $val['id'], 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $languageId
                ), 
                'columns' => 'label'
            ));
            
            if ($ls[0]['label'])
                $b .= '<p><span class="taxon-section-head">' . $ls[0]['label'] . '</span></p><br />' . chr(10);
        }
        
        return $b;
    }



    private function ajaxActionSaveMediaDescription ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('description')) {
                
                $this->models->MediaDescriptionsTaxon->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id']
                ));
            }
            else {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id']
                    )
                ));
                
                $d = $this->filterContent(trim($this->requestData['description']));
                
                $this->models->MediaDescriptionsTaxon->save(
                array(
                    'id' => isset($mdt[0]['id']) ? $mdt[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id'], 
                    'description' => $d['content']
                ));
            }
            
            $this->smarty->assign('returnText', '<ok>');
        }
    }



    private function ajaxActionGetMediaDescription ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $mdt = $this->models->MediaDescriptionsTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id']
                )
            ));
            
            $this->smarty->assign('returnText', $mdt[0]['description']);
        }
    }



    private function ajaxActionGetMediaDescriptions ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $mt = $this->models->MediaTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id']
                ), 
                'columns' => 'id'
            ));
            
            foreach ((array) $mt as $key => $val) {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $val['id']
                    ), 
                    'columns' => 'description'
                ));
                
                $mt[$key]['description'] = $mdt ? $mdt[0]['description'] : null;
            }
            
            $this->smarty->assign('returnText', json_encode($mt));
        }
    }



    private function deleteTaxonMedia ($id = false, $output = true)
    {
        if ($id === false) {
            
            $id = $this->requestData['id'];
        }
        
        if (empty($id)) {
            
            return;
        }
        else {
            
            $mt = $this->models->MediaTaxon->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                )
            ));
            
            $delRecords = true;
            
            if (file_exists($_SESSION['admin']['project']['paths']['project_media'] . $mt[0]['file_name'])) {
                
                $delRecords = unlink($_SESSION['admin']['project']['paths']['project_media'] . $mt[0]['file_name']);
            }
            
            if ($delRecords) {
                
                if ($mt[0]['thumb_name'] && file_exists($_SESSION['admin']['project']['paths']['project_thumbs'] . $mt[0]['thumb_name'])) {
                    unlink($_SESSION['admin']['project']['paths']['project_thumbs'] . $mt[0]['thumb_name']);
                }
                
                $this->models->MediaDescriptionsTaxon->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'media_id' => $id
                ));
                

                $this->models->MediaTaxon->delete(array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                if ($output)
                    $this->smarty->assign('returnText', '<ok>');
            }
            else {
                
                if ($output)
                    $this->addError(sprintf($this->translate('Could not delete file: %s'), $mt[0]['file_name']));
            }
        }
    }



    private function ajaxActionSaveRankLabel ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('label')) {
                
                $this->models->LabelProjectRank->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'project_rank_id' => $this->requestData['id']
                ));
            }
            else {
                
                $lpr = $this->models->LabelProjectRank->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'project_rank_id' => $this->requestData['id']
                    )
                ));
                
                $this->models->LabelProjectRank->save(
                array(
                    'id' => isset($lpr[0]['id']) ? $lpr[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'project_rank_id' => $this->requestData['id'], 
                    'label' => trim($this->requestData['label'])
                ));
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function ajaxActionGetRankLabels ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            $l = $this->models->Language->_get(array(
                'id' => $this->requestData['language'], 
                'columns' => 'direction'
            ));
            
            $lpr = $this->models->LabelProjectRank->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language']
                ), 
                'columns' => '*, \'' . $l['direction'] . '\' as direction'
            ));
            
            $this->smarty->assign('returnText', json_encode($lpr));
        }
    }



    private function getProjectRankByParentProjectRank ($id = false)
    {
        if ($id === false)
            $id = $this->requestData['id'];
        
        if (empty($id))
            return;
        
        $d = $this->models->ProjectRank->_get(array(
            'id' => array(
                'parent_id' => $id
            )
        ));
        
        $result = $d[0]['id'] ? $d[0]['id'] : -1;
        
        return $result;
    }



    private function getProjectIdRankByname ($name)
    {
        $r = $this->models->Rank->_get(array(
            'id' => array(
                'rank' => $name
            )
        ));
        $r = $this->models->ProjectRank->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'rank_id' => $r[0]['id']
            )
        ));
        
        return $r[0]['id'];
    }



    private function canParentHaveChildTaxa ($parentId)
    {
        
        // get the projected parent taxon...
        $d = $this->getTaxonById($parentId);
        
        // ..and check whether its rank has any child ranks
        return ($this->getProjectRankByParentProjectRank($d['rank_id']) != -1);
    }



    private function canRankBeHybrid ($projectRankId)
    {
        $d = $this->models->ProjectRank->_get(array(
            'id' => $projectRankId
        ));
        
        $r = $this->models->Rank->_get(array(
            'id' => $d['rank_id']
        ));
        
        return ($r['can_hybrid'] == 1);
    }



    private function ajaxActionSaveSectionTitle ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            if (!$this->rHasVal('label')) {
                
                $this->models->LabelSection->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'section_id' => $this->requestData['id']
                ));
            }
            else {
                
                $ls = $this->models->LabelSection->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'section_id' => $this->requestData['id']
                    )
                ));
                
                $this->models->LabelSection->save(
                array(
                    'id' => isset($ls[0]['id']) ? $ls[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'section_id' => $this->requestData['id'], 
                    'language_id' => $this->requestData['language'], 
                    'label' => trim($this->requestData['label'])
                ));
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function ajaxActionDeleteSectionTitle ()
    {
        if (!$this->rHasId()) {
            
            return;
        }
        else {
            
            $this->models->LabelSection->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'section_id' => $this->requestData['id']
            ));
            
            $this->models->Section->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $this->requestData['id']
            ));
        }
    }



    private function ajaxActionGetSectionLabels ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $l = $this->models->Language->_get(array(
                'id' => $this->requestData['language'], 
                'columns' => 'direction'
            ));
            
            $ls = $this->models->LabelSection->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language']
                ), 
                'columns' => '*, \'' . $l['direction'] . '\' as direction'
            ));
            
            $this->smarty->assign('returnText', json_encode($ls));
        }
    }



    private function ajaxActionGetLanguageLabels ()
    {
        if (!$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $ll = $this->models->LabelLanguage->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language']
                )
            ));
            
            $this->smarty->assign('returnText', json_encode($ll));
        }
    }



    private function ajaxActionSaveLanguageLabel ()
    {
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        }
        else {
            
            $this->models->LabelLanguage->delete(
            array(
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->requestData['language'], 
                'label_language_id' => $this->requestData['id']
            ));
            
            if ($this->rHasVal('label')) {
                
                $this->models->LabelLanguage->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'label_language_id' => $this->requestData['id'], 
                    'label' => trim($this->requestData['label'])
                ));
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
    }



    private function moveIdInTaxonOrder ($id, $dir)
    {
        if ($dir != 'up' && $dir != 'down')
            return;
        
        $t1 = $this->getTaxonById($id);
        
        $t2 = $this->models->Taxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'rank_id' => $t1['rank_id'], 
                'taxon_order ' . ($dir == 'up' ? '<' : '>') => $t1['taxon_order']
            ), 
            'order' => 'taxon_order ' . ($dir == 'up' ? 'desc' : 'asc'), 
            'limit' => 1
        ));
        
        if (count((array) $t2) != 0) {
            
            $this->models->Taxon->update(array(
                'taxon_order' => $t2[0]['taxon_order']
            ), array(
                'id' => $t1['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->Taxon->update(array(
                'taxon_order' => $t1['taxon_order']
            ), array(
                'id' => $t2[0]['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->reOrderTaxonTree();
        }
    }



    private function getPageTaxonCount ()
    {
        if ($this->hasTableDataChanged('PageTaxon') || !isset($_SESSION['admin']['project']['PageTaxonCount'])) {
            
            $tp = $this->models->PageTaxon->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'count(*) as total'
            ));
            
            $_SESSION['admin']['project']['PageTaxonCount'] = $tp[0]['total'];
        }
        
        return $_SESSION['admin']['project']['PageTaxonCount'];
    }



    private function getContentTaxaCount ()
    {
        if ($this->hasTableDataChanged('ContentTaxon') || !isset($_SESSION['admin']['project']['ContentTaxonCount'])) {
            

            $ct = $this->models->ContentTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'publish' => 1
                ), 
                'columns' => 'count(*) as total, taxon_id', 
                'group' => 'taxon_id'
            ));
            

            foreach ((array) $ct as $key => $val)
                $d[$val['taxon_id']] = $val['total'];
            
            $_SESSION['admin']['project']['ContentTaxonCount'] = isset($d) ? $d : 0;
        }
        
        return $_SESSION['admin']['project']['ContentTaxonCount'];
    }



    private function getSynonymCount ()
    {
        if ($this->hasTableDataChanged('Synonym') || !isset($_SESSION['admin']['project']['SynonymCount'])) {
            
            $s = $this->models->Synonym->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'count(*) as total,taxon_id', 
                'group' => 'taxon_id'
            ));
            
            foreach ((array) $s as $key => $val)
                $d[$val['taxon_id']] = $val['total'];
            
            $_SESSION['admin']['project']['SynonymCount'] = isset($d) ? $d : 0;
        }
        
        return $_SESSION['admin']['project']['SynonymCount'];
    }



    private function getCommonnameCount ()
    {
        if ($this->hasTableDataChanged('Commonname') || !isset($_SESSION['admin']['project']['CommonnameCount'])) {
            
            $c = $this->models->Commonname->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'count(*) as total,taxon_id', 
                'group' => 'taxon_id'
            ));
            
            foreach ((array) $c as $key => $val)
                $d[$val['taxon_id']] = $val['total'];
            
            $_SESSION['admin']['project']['CommonnameCount'] = isset($d) ? $d : 0;
        }
        
        return $_SESSION['admin']['project']['CommonnameCount'];
    }



    private function getMediaTaxonCount ()
    {
        if ($this->hasTableDataChanged('MediaTaxon') || !isset($_SESSION['admin']['project']['MediaTaxonCount'])) {
            
            $mt = $this->models->MediaTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'count(*) as total, taxon_id', 
                'group' => 'taxon_id'
            ));
            
            foreach ((array) $mt as $key => $val)
                $d[$val['taxon_id']] = $val['total'];
            
            $_SESSION['admin']['project']['MediaTaxonCount'] = isset($d) ? $d : 0;
        }
        
        return $_SESSION['admin']['project']['MediaTaxonCount'];
    }



    private function getLiteratureTaxonCount ()
    {
        if ($this->hasTableDataChanged('LiteratureTaxon') || !isset($_SESSION['admin']['project']['LiteratureTaxonCount'])) {
            
            $lt = $this->models->LiteratureTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'columns' => 'count(*) as total, taxon_id', 
                'group' => 'taxon_id'
            ));
            
            foreach ((array) $lt as $key => $val)
                $d[$val['taxon_id']] = $val['total'];
            
            $_SESSION['admin']['project']['LiteratureTaxonCount'] = isset($d) ? $d : 0;
        }
        
        return $_SESSION['admin']['project']['LiteratureTaxonCount'];
    }



    private function getTaxonSynonymsById ($id = false)
    {
        $id = $id ? $id : ($this->rHasId() ? $this->requestData['id'] : false);
        
        if (!$id)
            return;
        
        $s = $this->models->Synonym->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            )
        ));
        
        return $s;
    }



    private function getTaxonMedia ($id)
    {
        $d = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            ), 
            'columns' => 'id,taxon_id,file_name,thumb_name,original_name,mime_type,file_size,sort_order,overview_image,substring(mime_type,1,locate(\'/\',mime_type)-1) as mime', 
            'order' => 'mime,sort_order,file_name'
        ));
        
        foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $val)
            $mimes[$val['mime']] = $val;
        
        foreach ((array) $d as $key => $val) {
            
            $d[$key]['media_type'] = $mimes[$val['mime_type']];
            if (file_exists($_SESSION['admin']['project']['urls']['project_media'] . $val['file_name'])) {
                $d[$key]['dimensions'] = getimagesize($_SESSION['admin']['project']['urls']['project_media'] . $val['file_name']);
            }
            $d[$key]['hr_file_size'] = $this->helpers->HrFilesizeHelper->convert($val['file_size']);
        }
        
        return $d;
    }



    private function getAllLiterature ()
    {
        return $this->models->Literature->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'order' => 'author_first,author_second,year', 
            'columns' => '*, year(`year`) as `year`,
							concat(
								author_first,
								(
									if(multiple_authors=1,
										\' et al.\',
										if(author_second!=\'\',concat(\' & \',author_second),\'\')
									)
								)
							) as author_full'
        ));
    }



    private function getTaxonLiterature ($id)
    {
        $lt = $this->models->LiteratureTaxon->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $id
            )
        ));
        
        foreach ((array) $lt as $key => $val) {
            
            $l = $this->models->Literature->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $val['literature_id']
                ), 
                'columns' => '*, year(`year`) as `year`,
							concat(
								author_first,
								(
									if(multiple_authors=1,
										\' et al.\',
										if(author_second!=\'\',concat(\' & \',author_second),\'\')
									)
								)
							) as author_full'
            ));
            
            $refs[] = $l[0];
        }
        
        $sortBy = array(
            'key' => 'author_first', 
            'dir' => 'asc', 
            'case' => 'i'
        );
        
        $this->customSortArray($refs, $sortBy);
        
        return $refs;
    }



    private function filterInternalTags ($id)
    {
        if (empty($id))
            return;
        
        if (isset($_SESSION['admin']['system']['literature']['newRef']) && $_SESSION['admin']['system']['literature']['newRef'] != '<new>') {
            
            $this->models->ContentTaxon->execute(
            'update %table% 
					set content = replace(content,"[new litref]","' . mysql_real_escape_string($_SESSION['admin']['system']['literature']['newRef']) . '")
					where project_id = ' . $this->getCurrentProjectId() . '
					and taxon_id = ' . $id);
        }
        
        if (isset($_SESSION['admin']['system']['media']['newRef']) && $_SESSION['admin']['system']['media']['newRef'] != '<new>') {
            
            $this->models->ContentTaxon->execute(
            'update %table% 
					set content = replace(content,"[new media]","' . mysql_real_escape_string($_SESSION['admin']['system']['media']['newRef']) . '")
					where project_id = ' . $this->getCurrentProjectId() . '
					and taxon_id = ' . $id);
        }
        
        $this->models->ContentTaxon->execute('update %table% 
				set content = replace(replace(content,"[new litref]",""),"[new media]","")
				where project_id = ' . $this->getCurrentProjectId() . '
				and taxon_id = ' . $id);
        
        unset($_SESSION['admin']['system']['literature']['newRef']);
        unset($_SESSION['admin']['system']['media']['newRef']);
    }



    private function getLookupList ($search)
    {
        $search = str_replace(array(
            '/', 
            '\\'
        ), '', $search);
        
        if (empty($search))
            return;
        
        $regexp = '/' . preg_quote($search) . '/i';
        
        $ranks = $this->getProjectRanks(array(
            'idsAsIndex' => true
        ));
        
        $l = array();
        
        foreach ((array) $this->newGetUserAssignedTaxonTreeList() as $key => $val) {
            
            if (preg_match($regexp, $val['taxon']) == 1)
                $l[] = array(
                    'id' => $val['id'], 
                    'label' => $val['taxon'], 
                    'source' => $ranks[$val['rank_id']]['rank']
                );
        }
        
        /*
		
		foreach((array)$this->getAllCommonnames() as $key => $val) {
		
			if (preg_match($regexp,$val['commonname']) == 1)
				$l2[] = array(
					'id' => $val['taxon_id'],
					'label' => $val['commonname'],
					'source' => $this->translate('common name')
				);

		}

		foreach((array)$this->getAllSynonyms() as $key => $val) {
		
			if (preg_match($regexp,$val['synonym']) == 1)
				$l2[] = array(
					'id' => $val['taxon_id'],
					'label' => $val['synonym'],
					'source' => $this->translate('synonym')
				);

		}

		$l = array_merge((array)$l1,(array)$l2,(array)$l3);

		*/
        
        $this->smarty->assign('returnText', $this->makeLookupList($l, 'species', '../species/taxon.php?id=%s', true));
    }



    private function getCategories ($taxon = null, $languageId = null)
    {
        if ($this->hasTableDataChanged('PageTaxon') || $this->hasTableDataChanged('PageTaxonTitle') || !isset($_SESSION['admin']['user']['species']['categories'])) {
            
            // get the defined categories (just the page definitions, no content yet)
            $tp = $this->models->PageTaxon->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId()
                ), 
                'order' => 'show_order', 
                'fieldAsIndex' => 'page_id'
            ));
            
            foreach ((array) $tp as $key => $val) {
                
                // for each category, get the category title
                $tpt = $this->models->PageTaxonTitle->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => isset($languageId) ? $languageId : $this->getDefaultProjectLanguage(), 
                        'page_id' => $val['id']
                    ), 
                    'columns' => 'title'
                ));
                
                $tp[$key]['title'] = $tpt[0]['title'];
            }
            
            $_SESSION['admin']['user']['species']['categories'] = array(
                'categories' => $tp, 
                'defaultCategory' => 1
            );
        }
        
        return $_SESSION['admin']['user']['species']['categories'];
    }



    private function setOverviewImageState ($taxon, $id, $state)
    {
        $mt = $this->models->MediaTaxon->update(array(
            'overview_image' => 0
        ), array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $taxon
        ));
        
        if ($state == true) {
            
            return $this->models->MediaTaxon->update(array(
                'overview_image' => 1
            ), array(
                'id' => $id, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $taxon
            ));
        }
        else {
            
            return $mt;
        }
    }



    private function doAssignUserTaxon ($userId, $taxonId)
    {
        if (empty($userId) || empty($taxonId))
            return;
        
        $this->models->UserTaxon->save(array(
            'id' => null, 
            'project_id' => $this->getCurrentProjectId(), 
            'user_id' => $userId, 
            'taxon_id' => $taxonId
        ));
        
        return $this->models->UserTaxon->getNewId();
    }



    private function getNextMediaSortOrder ($taxon)
    {
        $d = $this->models->MediaTaxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $taxon
            ), 
            'columns' => '(max(sort_order) + 1) as next'
        ));
        
        return $d[0]['next'];
    }



    private function reOrderMediaSortOrder ($taxon)
    {
        $tm = $this->getTaxonMedia($taxon);
        
        $prevMime = null;
        
        foreach ((array) $tm as $val) {
            
            if ($prevMime != $val['mime'])
                $i = 0;
            
            $this->models->MediaTaxon->update(array(
                'sort_order' => $i++
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $val['id']
            ));
            
            $prevMime = $val['mime'];
        }
    }



    private function changeMediaSortOrder ($taxon, $id, $dir)
    {
        $this->reOrderMediaSortOrder($taxon);
        
        $tm = $this->getTaxonMedia($taxon);
        
        foreach ((array) $tm as $key => $val) {
            
            if ($val['id'] == $id && $dir == 'down' && $key != (count((array) $tm) - 1)) {
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order'] + 1
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $tm[$key + 1]['id']
                ));
            }
            else if ($val['id'] == $id && $dir == 'up' && $key != 0) {
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order'] - 1
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                ));
                
                $this->models->MediaTaxon->update(array(
                    'sort_order' => $val['sort_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $tm[$key - 1]['id']
                ));
            }
        }
    }



    private function createStandardCoLRanks ()
    {
        $pr = $this->models->ProjectRank->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ), 
            'columns' => 'count(*) as total'
        ));
        
        if ($pr[0]['total'] > 0)
            return;
        
        $r = $this->models->Rank->_get(array(
            'id' => array(
                'in_col' => 1
            ), 
            'order' => 'parent_id'
        ));
        
        $parent = null;
        
        foreach ((array) $r as $key => $val) {
            
            $this->models->ProjectRank->save(
            array(
                'id' => null, 
                'project_id' => $this->getCurrentProjectId(), 
                'rank_id' => $val['id'], 
                'parent_id' => $parent, 
                'lower_taxon' => ($key >= (count((array) $r) - 1) ? 1 : 0)
            ));
            
            $parent = $this->models->ProjectRank->getNewId();
        }
    }



    private function fixSubgenusParentheses ($name, $rankId)
    {
        if ($rankId == $this->getProjectIdRankByname('Subgenus'))
            return str_replace(array(
                '(', 
                ')'
            ), '', $name);
        else
            return ($name);
    }



    private function fixNameCasting ($name)
    {
        return ucfirst(strtolower($name));
    }



    private function checkNameSpaces ($name, $rankId, $parentId)
    {
        
        // trim and replace accidental double spaces by single ones
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        $species_rank_id = $this->getProjectIdRankByname('Species');
        
        //Dit is 'm volgens mij
        //[4:52:12 PM] Ruud Altenburg: 1. rank_id < species_rank_id
        //geen spaties mogelijk
        if ($rankId < $species_rank_id)
            return preg_match_all('/\s/', $name, $d) == 0;
            
            //2. rank_id == species_rank_id
            //een spatie mogeljik
            //twee spaties alleen mogelijk als eerste karakter van twee woord een ( is -> subgenus
            //(dit kun je evt ook testen met parent_id = subgenus_rank_id)
        if ($rankId == $species_rank_id) {
            if (preg_match_all('/\s/', $name, $d) == 1)
                return true;
            if (preg_match_all('/\s/', $name, $d) == 2) {
                $d = explode(' ', $name);
                return substr($d[1], 0, 1) == '(';
            }
            return false;
        }
        
        //3. rank_id > species_rank_id
        //hier moet de parent erbij gesleept worden
        $parent = $this->getTaxonById($parentId);
        
        //parent_id = species_rank_id:
        //twee spaties mogeljik
        //drie spaties alleen mogelijk als eerste karakter van twee woord een ( is
        if ($parent['rank_id'] == $species_rank_id) {
            if (preg_match_all('/\s/', $name, $d) == 2)
                return true;
            if (preg_match_all('/\s/', $name, $d) == 3) {
                $d = explode(' ', $name);
                return substr($d[2], 0, 1) == '(';
            }
            return false;
        }
        
        //parent_id > species_rank_id
        //drie spaties mogeljik
        //vier spaties alleen mogelijk als eerste karakter van twee woord een ( is
        if ($parent['rank_id'] > $species_rank_id) {
            if (preg_match_all('/\s/', $name, $d) == 3)
                return true;
            if (preg_match_all('/\s/', $name, $d) == 4) {
                $d = explode(' ', $name);
                return substr($d[3], 0, 1) == '(';
            }
            return false;
        }
        
        // let's be intolerant
        return false;
    }



    private function checkCharacters ($name)
    {
        
        // 3. Names should not contain special characters (except -) or digits. 
        return (preg_match('/([^A-Za-z\s\(\)\-]+)/', $name) === 0);
    }



    private function isTaxonNameFirstPartLegal ($taxonName = null)
    {
        $taxonName = trim($taxonName);
        
        if (empty($taxonName))
            return;
        
        $d = trim(substr($taxonName, 0, strrpos($taxonName, ' ')));
        
        if (empty($d))
            return true;
        
        $t = $this->models->Taxon->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => $d
            ), 
            'columns' => 'count(*) as total'
        ));
        
        if ($t[0]['total'] > 0)
            return true;
        else
            return $d;
    }



    private function doNameAndParentMatch ($name, $parent)
    {
        if (strpos($name, ' ') == false)
            return true;
        
        return stripos($name, $parent) === 0;
    }



    private function removeMarkers ($name)
    {
        $m = array(
            'ssp', 
            'subsp', 
            'var', 
            'subvar', 
            'subsubvar', 
            'f', 
            'subf', 
            'subsubf'
        );
        
        foreach ((array) $m as $val) {
            
            $name = preg_replace('|\b(' . $val . ')\b(\.){0,1}|', '', $name);
        }
        
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        return $name;
    }



    private function getCorrectedProjectRankByParentProjectRank ($rankId)
    {
        
        /*
	    8. when choosing a parent, default rank of new taxon should be the parent rank's child, with two exceptions:
	    
	        genus should automatically select species, possibly bypassing subgenus (which can be subsequently selected by hand)
	        species should automatically select subspecies, possibly bypassing variety etc (which can be subsequently selected by hand)
	    */
        $d = null;
        
        $pr = $this->models->ProjectRank->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $rankId
            )
        ));
        
        if (isset($pr) && $pr[0]['rank_id'] == GENUS_RANK_ID)
            $d = SPECIES_RANK_ID;
        if (isset($pr) && $pr[0]['rank_id'] == SPECIES_RANK_ID)
            $d = SUBSPECIES_RANK_ID;
        
        if (!is_null($d)) {
            
            $pr = $this->models->ProjectRank->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'rank_id' => $d
                )
            ));
            
            if (!empty($pr[0]['id']))
                return $pr[0]['id'];
        }
        
        return $this->getProjectRankByParentProjectRank($rankId);
    }



    private function getSubgenusChildNamePrefix ($id)
    {
        
        /*
	     10. when user selects subgenus as parent, the input box for the name automatically gets "genus (subgenus) ", which is editable, so the expert can remove the parenthese(s) as he sees fit.
	     */
        $t = $this->getTaxonById($id);
        
        if ($t['rank_id'] == $this->getProjectIdRankByname('Subgenus')) {
            
            $p = $this->getTaxonById($t['parent_id']);
            
            if ($p['rank_id'] == $this->getProjectIdRankByname('Genus')) {
                
                return $p['taxon'] . ' (' . $t['taxon'] . ') ';
            }
        }
    }



    private function deleteVariation ($id)
    {
        $this->models->TaxaRelations->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'relation_id' => $id, 
            'ref_type' => 'variation'
        ));
        $this->models->VariationRelations->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
        $this->models->VariationLabel->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
        $this->models->TaxonVariation->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
        $this->models->MatrixTaxonState->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
        $this->models->MatrixVariation->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
    }



    private function getRelatedEntities ($tId)
    {
        $rel = $this->models->TaxaRelations->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'taxon_id' => $tId
            )
        ));
        
        foreach ((array) $rel as $key => $val) {
            
            if ($val['ref_type'] == 'taxon') {
                $rel[$key]['label'] = $this->formatTaxon($d = $this->getTaxonById($val['relation_id']));
            }
            else {
                $d = $this->getVariation($vVal['relation_id']);
                $var[$key]['relations'][$vKey]['label'] = $d['label'];
                $var[$key]['relations'][$vKey]['labels'] = $d['labels'];
            }
        }
        
        $var = $this->getVariations($tId);
        
        foreach ((array) $var as $key => $val) {
            
            $var[$key]['relations'] = $this->models->VariationRelations->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'variation_id' => $val['id']
                )
            ));
            
            foreach ((array) $var[$key]['relations'] as $vKey => $vVal) {
                
                if ($vVal['ref_type'] == 'taxon') {
                    $var[$key]['relations'][$vKey]['label'] = $this->formatTaxon($d = $this->getTaxonById($vVal['relation_id']));
                }
                else {
                    $d = $this->getVariation($vVal['relation_id']);
                    $var[$key]['relations'][$vKey]['label'] = $d['label'];
                    $var[$key]['relations'][$vKey]['labels'] = $d['labels'];
                }
            }
        }
        
        return array(
            'relations' => $rel, 
            'variations' => $var
        );
    }
}