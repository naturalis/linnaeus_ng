<?php

/*
	
	tinyMCE spell checker:
		- requires google: check if online
		- change default lanmguage through js
		- what if language has no iso2?
		- what happens if language does not exist at google?

	tinyMCE images: MCImageManager (€€€)

	hardcoded set_time_limit(3000); for CoL

*/

include_once ('Controller.php');

class SpeciesController extends Controller
{
    
    public $usedModels = array(
        'taxon', 
        'content_taxon', 
        'language_project', 
        'language', 
        'taxon_page', 
        'taxon_page_title', 
        'heartbeat', 
        'content_taxon_undo'
    );
    
    public $usedHelpers = array(
        'col_loader_helper','csv_parser_helper'
    );

	private $_treeIdList;

    public $controllerPublicName = 'Species module';
    public $controllerModuleId = 4; // ref. record for Species module in table 'modules'


    /**
     * Constructor, calls parent's constructor
     *
     * @access 	public
     */
    public function __construct ()
    {
        parent::__construct();
        
		$this->createStandardSubpages();

        $this->smarty->assign('heartbeatFrequency', $this->generalSettings['heartbeatFrequency']);

    }


    /**
     * Destroys
     *
     * @access 	public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    /**
     * Index of the species module
     *
     * @access	public
     */
    public function indexAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Species module overview'));
        
        $this->printPage();
    
    }


    /**
     * Display, add, edit and delete subpages' names in all languages
     *
     * @access	public
     */
    public function pageAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Subpages'));
        
        // adding a new page
        if (!empty($this->requestData['new_page']) && !$this->isFormResubmit()) {
		
            $tp = $this->createTaxonPage($this->requestData['new_page'],$this->requestData['show_order']);
            
            if ($tp !== true) {
                
                $this->addError(_('Could not save page name.'));
                $this->addError('(' . $tp . ')');
            
            }
        
        }
        
        $lp = $this->models->LanguageProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        foreach ((array) $lp as $key => $val) {
            
            $l = $this->models->Language->get($val['language_id']);
            
            $lp[$key]['language'] = $l['language'];
            
            if ($val['def_language'] == 1)
                $defaultLanguage = $val['language_id'];
        
        }
        
        $pages = $this->models->TaxonPage->get(array(
            'project_id' => $this->getCurrentProjectId()
        	),
			false,'show_order'
		);
        
        foreach ((array) $pages as $key => $page) {
            
            foreach ((array) $lp as $k => $language) {
                
                $tpt = $this->models->TaxonPageTitle->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'page_id' => $page['id'], 
                    'language_id' => $language['language_id']
                ));
                
                $pages[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];
            
            }
			
			$nextShowOrder = $page['show_order']+1;
        
        }
        

        $this->smarty->assign('nextShowOrder', $nextShowOrder);

        $this->smarty->assign('maxSubPages', $this->generalSettings['maxSubPages']);
        
        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $defaultLanguage);
        
        $this->printPage();
    
    }


    /**
     * Edit taxon action
     *
     * Actual saving etc. works via AJAX actions
     *
     * @access	public
     */
    public function editAction ()
    {
        
        $this->checkAuthorisation();
        
        // this could do with a little more elegance...
        $this->setBreadcrumbIncludeReferer(
			array(
				'name' => _('Taxon list'), 
				'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
			)
		);
        
        if (!empty($this->requestData['id'])) {
        // get existing taxon name
            
            $t = $this->models->Taxon->get(array(
                'id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $taxon = $t[0];
            
            $this->setPageName(_('Editing') . ' "' . $taxon['taxon'] . '"');
        
        } else {
        // no id, new taxon
            
            $this->setPageName(_('Adding new taxon'));
        
        }
        
        if (empty($this->requestData['id']) || !$this->doLockOutUser($this->requestData['id'])) {
		// if new taxon OR existing taxon not being edited by someone else, get languages and content

            // get available languages
            $lp = $this->models->LanguageProject->get(array(
                'project_id' => $this->getCurrentProjectId()
            ));
            
            foreach ((array) $lp as $key => $val) {
                
                $l = $this->models->Language->get($val['language_id']);
                
                $lp[$key]['language'] = $l['language'];
                
                $lp[$key]['iso2'] = $l['iso2'];
                
                $lp[$key]['iso3'] = $l['iso3'];
                
                if ($val['def_language'] == 1)
                    $defaultLanguage = $val['language_id'];
            
            }
            
            // determine the language the page will open in
            $startLanguage = !empty($this->requestData['lan']) ? $this->requestData['lan'] : $defaultLanguage;
            
            // get the defined subpages (just the page definitions, no content yet)
            $tp = $this->models->TaxonPage->get(array(
                'project_id' => $this->getCurrentProjectId()
            ));

            foreach ((array) $tp as $key => $val) {
                
                foreach ((array) $lp as $k => $language) {
                    
					// for each subpage in each language, get the subpage title
                    $tpt = $this->models->TaxonPageTitle->get(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $language['language_id'], 
                        'page_id' => $val['id']
                    ));
                    
                    $tp[$key]['titles'][$language['language_id']] = $tpt[0];
                
                }
                
                if ($val['def_page'] == 1) $defaultPage = $val['id'];
            
            }
            
            // determine the page_id the page will open in
            $startPage = !empty($this->requestData['page']) ? $this->requestData['page'] : $defaultPage;
            
            // get the content in the language the page will open with
            if (!empty($this->requestData['id']) && !empty($startLanguage)) {
                
                $ct = $this->models->ContentTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id'], 
                    'language_id' => $startLanguage, 
                    'page_id' => $startPage
                ));

				if ($ct == null) {

					$content['content'] = $this->getDefaultPageSections($startPage);

				} else {

	                $content = $ct[0];

            	}
            }
            
            if (isset($taxon))
                $this->smarty->assign('taxon', $taxon);
            
            if (isset($content))
                $this->smarty->assign('content', $content);

            $this->smarty->assign('autosaveFrequency', $this->generalSettings['autosaveFrequency']);
            
            $this->smarty->assign('pages', $tp);
            
            $this->smarty->assign('languages', $lp);
            
            $this->smarty->assign('includeHtmlEditor', true);
            
            $this->smarty->assign('activeLanguage', $startLanguage);
            
            $this->smarty->assign('activePage', $startPage);
        
        } else {
		// existing taxon already being edited by someone else

            $this->smarty->assign('taxon', array(
                'id' => -1
            ));
            
            $this->addError(_('Taxon is already being edited by another editor.'));
        
        }
        
        $this->printPage();
    
    }


    /**
     * List existing taxa
     *
     * @access	public
     */
    public function listAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Taxon list'));
        
        $taxa = $this->models->Taxon->get(array(
            'project_id' => $this->getCurrentProjectId()
        ), '*', 'taxon_order');

        $lp = $this->models->LanguageProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ));
        
        $tp = $this->models->TaxonPage->get(array(
            'project_id' => $this->getCurrentProjectId()
        ), 'count(*) as tot');
        
        foreach ((array) $lp as $key => $val) {
            
            $l = $this->models->Language->get($val['language_id']);
            
            $lp[$key]['language'] = $l['language'];
        
        }
        
        foreach ((array) $taxa as $key => $taxon) {
		
			$taxa[$key]['symbol'] = $this->getRankSymbol($taxon['rank']);
            
            foreach ((array) $lp as $k => $val) {
                
                $ct = $this->models->ContentTaxon->get(
                array(
                    'taxon_id' => $taxon['id'], 
                    'language_id' => $val['language_id'], 
                    'project_id' => $this->getCurrentProjectId()
                ), 'count(*) as tot,if(publish=0,"unpublished","published") as state', false, 'publish');
                
                foreach ((array) $ct as $key3 => $pub) {
                    
                    $lp[$k]['publish'][$taxon['id']][$pub['state']] = $pub['tot'];
                
                }
                
                isset($lp[$k]['publish'][$taxon['id']]['published']) || $lp[$k]['publish'][$taxon['id']]['published'] = 0;
                
                isset($lp[$k]['publish'][$taxon['id']]['unpublished']) || $lp[$k]['publish'][$taxon['id']]['unpublished'] = 0;
                
                $lp[$k]['publish'][$taxon['id']]['total'] = $tp[0]['tot'];
                
                $lp[$k]['publish'][$taxon['id']]['pct_finished'] = round(($lp[$k]['publish'][$taxon['id']]['published'] / $tp[0]['tot']) * 100);
            
            }
        
        }
        


        // user requested a sort of the table
        if (isset($this->requestData['key'])) {
            
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
        
        $this->smarty->assign('sortBy', $sortBy);
        
        $this->smarty->assign('taxa', $taxa);
        
        $this->smarty->assign('languages', $lp);
        
        $this->printPage();
    
    }


    /**
     * Upload a file with taxa
     *
     * @access	public
     */
    public function fileAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Taxon file upload'));
		
		if ($this->requestDataFiles) {

			unset($_SESSION['system']['csv_data']);
			
			$this->helpers->CsvParserHelper->setFieldMax(2);

			$this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
		
			$this->addError($this->helpers->CsvParserHelper->getErrors());
			
			if (!$this->getErrors()) {

				$r = $this->helpers->CsvParserHelper->getResults();

				$_SESSION['system']['csv_data'] = $r;

				$this->smarty->assign('results',$r);

			}	

		} elseif ($this->requestData) {

			if (isset($this->requestData['rows']) && isset($_SESSION['system']['csv_data'])) {
			
				$parenName = false;
				$predecessors = null;

				foreach((array)$this->requestData['rows'] as $key => $val) {

					$rank = $_SESSION['system']['csv_data'][$val][0];
					$name = $_SESSION['system']['csv_data'][$val][1];
					$parentName = null;

					if ($key==0) {
					// first one never has a parent (top of the tree)

						$predecessors[] = array($rank, $name);

					} else {
						if ($rank==$predecessors[count((array)$predecessors)-1][0]) {
						/* if this taxon has the same rank as the previous one, they must have the same
							parent, so we go back in the list until we find the first different rank,
							which must be the parent */
							
							$j=1;
							$prevRank = $rank;
							while($rank==$prevRank) {
							
								$prevRank = $predecessors[count((array)$predecessors)-$j][0];
								$parentName = $predecessors[count((array)$predecessors)-$j][1];
								$j++;

							}

							$predecessors[] = array($rank, $name);

						} else {

							/* if rank came before then we are no longer in the first branch of the tree
							   and need to use the parent of the previous occurrence.
							   we ignore the immediately preceding taxon, because if that is the same as
							   the current one, we are simple still on the same level. */
							foreach((array)$predecessors as $key => $val) {
	
								if ($rank == $val[0] && $key != count((array)$predecessors)-1) {
								// found a previous occurrence
								
									if (isset($predecessors[$key-1])) {
	
										// get the name of the previous occurrence's parent
										$parentName = $predecessors[$key-1][1];
										
										// apparantly we are at the start of a new branch, so chop off the previous one
										$predecessors = array_slice($predecessors,0,$key);
	
										// and add the first child of the next one
										$predecessors[] = array($rank, $name);
	
										break;
	
									}
	
								}
	
							}
							
							
							if ($parentName==null) {
							// did not find a previous occurrence of the current rank, so the previous taxon must be the parent

								$parentName = $predecessors[count((array)$predecessors)-1][1];

								$predecessors[] = array($rank, $name);

							}

						}

					}

					$this->importTaxon(
						array(
							'taxon_rank' => $rank,
							'taxon_name' => $name,
							'parent_taxon_name' => $parentName
						)
					);

				}

				$this->reOrderTaxonTree();

				unset($_SESSION['system']['csv_data']);
				
				$this->addMessage(_('Data saved.'));

			}


		}

        $this->printPage();
    
    }


    /**
     * AJAX interface for this class
     *
     * @access	public
     */
    public function ajaxInterfaceAction ()
    {

        if (!isset($this->requestData['action'])) return;
        
        if ($this->requestData['action'] == 'save_taxon') {
            
            $this->ajaxActionSaveTaxon();
        
        } else if ($this->requestData['action'] == 'get_taxon') {
            
            $this->ajaxActionGetTaxon();
        
        } else if ($this->requestData['action'] == 'delete_taxon') {
            
            $this->ajaxActionDeleteTaxon();
        
        } else if ($this->requestData['action'] == 'delete_page') {
            
            $this->ajaxActionDeletePage();
        
        } else if ($this->requestData['action'] == 'save_page_title') {
            
            $this->ajaxActionSavePageTitle();
        
        } else if ($this->requestData['action'] == 'get_page_states') {
            
            $this->ajaxActionGetPageStates();
        
        } else if ($this->requestData['action'] == 'publish_content') {
            
            $this->ajaxActionPublishContent();
        
        } else if ($this->requestData['action'] == 'get_taxon_undo') {
            
            $this->ajaxActionGetTaxonUndo();
        
        } else if ($this->requestData['action'] == 'get_col') {

            $this->getCatalogueOfLifeData();
        
        } else if ($this->requestData['action'] == 'save_col') {

            $this->ajaxActionImportTaxa();
        
        } else if ($this->requestData['action'] == 'save_taxon_name') {

            $this->ajaxSaveTaxonName();
        
        }
        
        $this->printPage();
    
    }


    /**
     * Interface for getting taxon data from the Catalogue Of Life webservice (which is somewhat unreliable)
     *
     * @access	public
     */
	public function colAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName(_('Import from Catalogue Of Life'));

		$this->printPage();
	
	}


	private function getCatalogueOfLifeData()
	{

		if (!empty($this->requestData['taxon_name'])) {
		
			// needs to go to config
			$timeout = 600;//secs

			set_time_limit($timeout);

			$this->helpers->ColLoaderHelper->setTimerInclusion(false);

			$this->helpers->ColLoaderHelper->setResultStyle('concise');

			if (isset($this->requestData['taxon_name'])) {

				$this->helpers->ColLoaderHelper->setTaxonName($this->requestData['taxon_name']);

			}

			if (isset($this->requestData['taxon_id'])) {

				$this->helpers->ColLoaderHelper->setTaxonId($this->requestData['taxon_id']);

			}

			if (isset($this->requestData['levels'])) {

				$this->helpers->ColLoaderHelper->setNumberOfChildLevels($this->requestData['levels']);

			}

			$this->helpers->ColLoaderHelper->setTimeout($timeout);

			$this->helpers->ColLoaderHelper->getTaxon();
			
			$data = $this->helpers->ColLoaderHelper->getResult();
			
			if (!$data) {

				$this->addError($this->helpers->ColLoaderHelper->getErrors());

			} else {

				$this->smarty->assign('returnText',json_encode($data));
	
			}
		}

	}



	private function createStandardSubpages() 
	{	

		$tp = $this->models->TaxonPage->get(
			array(
				'project_id' => $this->getCurrentProjectId()
			),'count(*) as total'
		);
		

		foreach((array)$this->controllerSettings['defaultSubPages'] as $key => $page) {

			if ($tp[0]['total']==0) {

				$this->createTaxonPage(_($page['name']), $key, isset($page['default']) && $page['default']);

			} else {

				if (isset($page['mandatory'])) {

					$d = $this->models->TaxonPage->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
				            'page' => $page['name'], 
						),'count(*) as total'
					);

					if ($d[0]['total']==0) {
		
						$this->createTaxonPage(_($page['name']), $key, isset($page['default']) && $page['default']);
		
					} 

				}

			}

		}

	}


    /**
     * Create a new subpage.
     *
     * A subpage is page devoted to a specific subject that appears in every taxon's
     * detail page (main, breeding, threats, etc).
     *
     * @access	private
     */
    private function createTaxonPage ($name, $show_order = false, $isDefault = false)
    {

        return $this->models->TaxonPage->save(array(
            'id' => null, 
            'page' => $name, 
            'show_order' => $show_order!==false ? $show_order : 0, 
            'project_id' => $this->getCurrentProjectId(), 
            'def_page' => $isDefault ? '1' : '0'
        ));
    
    }

    private function doLockOutUser ($taxonId)
    {
        
        if (empty($taxonId))
            return false;
        
        $h = $this->models->Heartbeat->get(
        array(
            'project_id =' => $this->getCurrentProjectId(), 
            'app' => $this->getAppName(), 
            'ctrllr' => 'species', 
            'view' => 'edit', 
            'params' => serialize(array(
                array(
                    'taxon_id', 
                    $taxonId
                )
            )), 
            'user_id !=' => $this->getCurrentUserId()
        ));
        
        return isset($h) ? true : false;
    
    }

    private function ajaxActionDeletePage ()
    {
        
        if (empty($this->requestData['id'])) {
            
            return;
        
        }
        else {
            
            $this->models->ContentTaxon->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['id']
            ));
            
            $this->models->TaxonPageTitle->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['id']
            ));
            
            $this->models->TaxonPage->delete(array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $this->requestData['id']
            ));
        
        }
    
    }

    private function ajaxActionSavePageTitle ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        }
        else {
            
            if (empty($this->requestData['title'])) {
                
                $tpt = $this->models->TaxonPageTitle->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id']
                ));
            
            }
            else {
                
                $tpt = $this->models->TaxonPageTitle->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id']
                ));
                
                $this->models->TaxonPageTitle->save(
                array(
                    'id' => isset($tpt[0]['id']) ? $tpt[0]['id'] : null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id'], 
                    'title' => $this->requestData['title']
                ));
            
            }
            
            $this->smarty->assign('returnText', 'saved');
        
        }
    
    }

    private function saveOldTaxonContentData ($data, $newdata = false, $mode = 'auto', $label = false)
    {
        
        $d = $data[0];
        
        // only back up if something changed (and we ignore the 'publish' setting)
        if (($d['content'] == $newdata['content']) && ($d['title'] == $newdata['title']))
            return;
        
        $d['save_type'] = $mode;
        
        if ($label)
            $d['save_label'] = $label;
        
        $d['content_taxa_id'] = $d['id'];
        
        $d['id'] = null;
        
        $d['content_taxa_created'] = $d['created'];
        unset($d['created']);
        
        $d['content_last_change'] = $d['last_change'];
        unset($d['last_change']);
        
        $this->models->ContentTaxonUndo->save($d);
    
    }

    private function ajaxActionSaveTaxon ()
    {
        
        // new taxon
        if (empty($this->requestData['id'])) {
            
            $d = $this->models->Taxon->save(
            array(
                'id' => !empty($this->requestData['id']) ? $this->requestData['id'] : null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => !empty($this->requestData['name']) ? $this->requestData['name'] : '?'
            ));
            
            $taxonId = $this->models->Taxon->getNewId();
        
        }
        // existing taxon 
        else {
            
            $d = true;
            
            $taxonId = $this->requestData['id'];
        
        }
        
        // save of new taxon succeded, or existing taxon
        if ($d) {
            
            // must have a language
            if (!empty($this->requestData['language'])) {
                
                // must have a page name
                if (!empty($this->requestData['page'])) {
                    
                    if (empty($this->requestData['name']) && empty($this->requestData['content'])) {
                        
                        if (!isset($this->requestData['save_type']))
                            $this->requestData['save_type'] = 'auto';
                        
                        $this->models->ContentTaxon->setRetainBeforeAlter();
                        
                        // no page title and no conten equals an empty page: delete
                        $ct = $this->models->ContentTaxon->delete(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $this->requestData['language'], 
                            'page_id' => $this->requestData['page']
                        ));
                        
                        $this->saveOldTaxonContentData($this->models->ContentTaxon->getRetainedData(), false, $this->requestData['save_type']);
                    
                    } else {
                        
                        // see if such content already exists
                        $ct = $this->models->ContentTaxon->get(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $this->requestData['language'], 
                            'page_id' => $this->requestData['page']
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
                            'title' => !empty($this->requestData['name']) ? $this->requestData['name'] : '', 
                            'page_id' => $this->requestData['page']
                        );
                        
                        // save content
                        $d = $this->models->ContentTaxon->save($newdata);
                        
                        if ($id != null)
                            $this->saveOldTaxonContentData($this->models->ContentTaxon->getRetainedData(), $newdata, 
                            $this->requestData['save_type']);
                    
                    }
                    
                    if ($d) {
                        
						/* the block below changed the taxon's name in the taxon table to whatever the user had 
						   entrered as subpage title of the default page in the default language, but the assumption
						   that that is the place where the leading name of a taxon is entered might be faulty

                        // if succesful, get the projects default language
                        $lp = $this->models->LanguageProject->get(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'def_language' => '1'
                        ));
                        
                        $defaultLanguage = isset($lp[0]['language_id']) ? $lp[0]['language_id'] : $this->requestData['language'];
                        
                        // get the main page content for the default language
                        $ct = $this->models->ContentTaxon->get(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $defaultLanguage
                        ));
                        
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
									'content' => $filteredContent['content'],
									'modified' => $filteredContent['modified']
								)
							)
						);
						                    
                    } else {
                        
                        $this->addError(_('Could not save taxon content'));
                    
                    }
                
                }
                else {
                    
                    $this->addError(_('No page title specified'));
                
                }
            
            }
            else {
                
                $this->addError(_('No language specified'));
            
            }
        
        }
        else {
            
            $this->addError(_('Could not save taxon'));
        
        }
    
    }


	private function filterContent($content)
	{
	
		if (!$this->controllerSettings['filterContent'])
			return $content;

		$modified = $content;

		if ($this->controllerSettings['filterContent']['html']['doFilter']) {

			$modified = strip_tags($modified,$this->controllerSettings['filterContent']['html']['allowedTags']);

		}

		return array('content' => $modified, 'modified' => $content!=$modified);

	}


    private function ajaxActionGetTaxon ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        }
        else {
            
            $ct = $this->models->ContentTaxon->get(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->requestData['language'], 
                'page_id' => $this->requestData['page']
            ));
            
            if (empty($ct[0])) {
                
                $c = array(
                    'project_id' => $this->requestData['id'], 
                    'taxon_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['page'], 
                    'content' => null, 
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


    private function ajaxActionDeleteTaxon ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        }
        else {
            
            $ct = $this->models->ContentTaxon->delete(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'page_id' => $this->requestData['page']
            ));
            
            $ct = $this->models->Taxon->delete(array(
                'id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
        
        }
    
    }



    private function ajaxActionGetPageStates ()
    {
        
        // see if such content already exists
        $ct = $this->models->ContentTaxon->get(
        array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon_id' => $this->requestData['id'], 
            'language_id' => $this->requestData['language']
        ), 'page_id,publish');
        
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
        
        if (empty($this->requestData['id']) || empty($this->requestData['language']) || empty($this->requestData['page']) || !isset($this->requestData['state'])) {
            
            $this->smarty->assign('returnText', '<error>');
        
        }
        else {
            
            $ct = $this->models->ContentTaxon->get(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->requestData['language'], 
                'page_id' => $this->requestData['page']
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
                    
                    $this->smarty->assign('returnText', '<error>');
                
                }
            
            }
            else {
                
                $this->smarty->assign('returnText', '<error>');
            
            }
        
        }
    
    }



    private function ajaxActionGetTaxonUndo ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language']) || empty($this->requestData['page'])) {
            
            return;
        
        }
        else {
            
            $ctu = $this->models->ContentTaxonUndo->get(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->requestData['language'], 
                'page_id' => $this->requestData['page']
            ), 'max(content_last_change) as last_change');
            
            $ctu = $this->models->ContentTaxonUndo->get(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId(), 
                'language_id' => $this->requestData['language'], 
                'page_id' => $this->requestData['page'], 
                'content_last_change' => $ctu[0]['last_change']
            ));
            
            if ($ctu) {
                
                $d = $ctu[0];
                
                $this->models->ContentTaxonUndo->delete(array(
                    'id' => $d['id'], 
                    'project_id' => $this->getCurrentProjectId()
                ));
                
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

	private function importTaxon($taxon) 
	{

		if (empty($taxon['taxon_name'])) return;

		$t = $this->models->Taxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon' => $taxon['taxon_name']
			),false,false,false,true
		);
		
		if (count((array)$t[0])==0) {
		// taxon does not exist in database

			if (!empty($taxon['parent_taxon_name'])) {

				// see if the parent taxon already exists
				$p = $this->models->Taxon->get(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => $taxon['parent_taxon_name']
					)
				);

			}

			if (isset($p) && count((array)$p)==1) {
			
				$pId = $p[0]['id'];
			
			} else {

				$pId = null;

			}

			// save taxon
			$this->models->Taxon->save(
				array(
					'id' => null,
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => $taxon['taxon_name'],
					'parent_id' => $pId,
					'rank' => $taxon['taxon_rank']					
				)
			);
			
		} else {
		// taxon does exist in database
		
			if (empty($t[0]['rank']) || empty($t[0]['parent_id'])) {
			
				$pId = null;
			
				if (empty($t[0]['parent_id']) && !empty($taxon['parent_taxon_name'])) {

					// see if the parent taxon already exists
					$p = $this->models->Taxon->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => $taxon['parent_taxon_name']
						)
					);

					if (isset($p) && count((array)$p)==1) {
					
						$pId = $p[0]['id'];
					
					}
				
				}

				$this->models->Taxon->save(
					array(
						'id' => $t[0]['id'],
						'project_id' => $this->getCurrentProjectId(),
						'parent_id' => (empty($t[0]['parent_id']) ? $pId : $t[0]['parent_id']),
						'rank' => (empty($t[0]['rank']) ? $taxon['taxon_rank'] : $t[0]['rank'])
					)

				);

			}

		}

	}

	private function ajaxSaveTaxonName() 
	{

		if (empty($this->requestData['taxon_name']) || empty($this->requestData['taxon_id'])) return;

		$t = $this->models->Taxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $this->requestData['taxon_id']
			),'count(*) as total'
		);

		if ($t[0]['total']>0) {

			$d = $this->models->Taxon->save(
				array(
					'id' => $this->requestData['taxon_id'],
					'taxon' => $this->requestData['taxon_name']
				)
			);

			if ($d) $this->smarty->assign('returnText', '<ok>');

		}

	}

	private function getTaxonTree($pId) 
	{

		$t = $this->models->Taxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				($pId === null ? 'parent_id is' : 'parent_id') => $pId
			),false,'taxon');

		foreach((array)$t as $key => $val) {

			$this->_treeIdList[] = $val['id'];

			$t['children'] = $this->getTaxonTree($val['id']);

		}
		
		return $t;

	}

	private function reOrderTaxonTree() 
	{

		$this->getTaxonTree(null);

		foreach((array)$this->_treeIdList as $key => $val) {

			$this->models->Taxon->save(
				array(
					'id' => $val,
					'taxon_order' => $key
				)
			);
			
		}

	}

	private function ajaxActionImportTaxa() 
	{

		if (empty($this->requestData['data'])) return;

		foreach((array)$this->requestData['data'] as $key => $val) {

			$t['taxon_id'] = $val[0];
			$t['taxon_name'] = $val[1];
			$t['taxon_rank'] = $val[2];
			$t['parent_taxon_name'] = $val[3];

			$this->importTaxon($t);

		}

	}

	private function getDefaultPageSections($pageId)
	{

		$tp = $this->models->TaxonPage->get(
			array(
			'id' => $pageId,
			'project_id' => $this->getCurrentProjectId()
			),'page'
		);
		
		$b = '';

		foreach((array)$this->controllerSettings['defaultSubPages'] as $key => $page) {
		
			if ($page['name']==$tp[0]['page']) {

				foreach((array)$page['sections'] as $sectionkey => $section) {
				
					$b .= '<p class="taxon-section-head">'.$section.'</p>'.chr(10);

				}

			}

		}

		return $b;
	
	}

	private function getRankSymbol($rank)
	{

		switch (strtolower($rank)) {
			/*
			case 'kingdom' :
				return 'K';
				break;
			case 'phylum' :
				return 'P';
				break;
			case 'class' :
				return 'C';
				break;
			case 'order' :
				return 'O';
				break;
			*/
			case 'family' :
				return '.';
				break;
			case 'genus' :
				return '..';
				break;
			case 'species' :
				return '...';
				break;
			case 'infraspecies' :
				return '....';
				break;
			default :
				return '&para;';
		}

	}

}






















