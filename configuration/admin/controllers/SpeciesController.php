<?php

/*
    
    tinyMCE
		compressor php

    tinyMCE spell checker:
        - requires google: check if online
        - change default lanmguage through js
        - what if language has no iso2?
        - what happens if language does not exist at google?

    hardcoded set_time_limit(3000); for CoL

	check fileupload ranks with defined

	must delete link taxa - ranks when deleting a rank
	
	ordering of ranks in getProjectRanks might need some reconsidering

*/

include_once ('Controller.php');

class SpeciesController extends Controller
{
    
    public $usedModels = array(
        'user', 
        'role',
        'project_role_user', 
		'user_taxon',
        'taxon', 
        'content_taxon', 
        'language_project', 
		'section',
		'label_section',
        'page_taxon', 
        'page_taxon_title', 
        'heartbeat', 
        'content_taxon_undo',
        'media_taxon',
        'media_descriptions_taxon',
		'hybrid',
		'project_rank',
		'rank',
		'label_project_rank',
    );
    
    public $usedHelpers = array(
        'col_loader_helper','csv_parser_helper','file_upload_helper','image_thumber_helper'
    );

	public $cssToLoad = array('colorbox/colorbox.css','taxon.css');
	public $jsToLoad = array('taxon.js','colorbox/jquery.colorbox.js');

    private $_treeList;

    public $controllerPublicName = 'Species module';
    public $controllerModuleId = 4; // ref. record for Species module in table 'modules'


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {

        parent::__construct();
        
        $this->createStandardCategories();

        $this->setProjectLanguages();

        $this->smarty->assign('heartbeatFrequency', $this->generalSettings['heartbeatFrequency']);

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

        $this->checkAuthorisation();
        
        $this->setPageName(_('Species module overview'));
        
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
        
        $this->setPageName(_('Define categories'));
        
        // adding a new page
        if (!empty($this->requestData['new_page']) && !$this->isFormResubmit()) {
        
            $tp = $this->createTaxonCategory($this->requestData['new_page'],$this->requestData['show_order']);
            
            if ($tp !== true) {
                
                $this->addError(_('Could not save category.'),1);
                $this->addError('(' . $tp . ')',1);
				
            }
        
        }
        
        $lp = $_SESSION['project']['languages'];

        $defaultLanguage = $_SESSION['project']['default_language_id'];
        
        $pages = $this->models->PageTaxon->get(array(
            'project_id' => $this->getCurrentProjectId()
            ),
            false,'show_order'
        );
        
        foreach ((array) $pages as $key => $page) {
            
            foreach ((array) $lp as $k => $language) {
                
                $tpt = $this->models->PageTaxonTitle->get(
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

        $this->smarty->assign('maxCategories', $this->generalSettings['maxCategories']);
        
        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $defaultLanguage);
        
        $this->printPage();
    
    }

    /**
     * Add new taxon action or edit existing
     *
     * @access    public
     */
    public function editAction ()
    {

		$this->checkAuthorisation();

		if (!empty($this->requestData['id'])) {

			$t = $this->models->Taxon->get(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'id' =>$this->requestData['id']
				)
			);

			$data = $t[0];

	        $this->setPageName(sprintf(_('Editing taxon "%s"'),$t[0]['taxon']));

		} else {

			$data = $this->requestData;

	        $this->setPageName(_('New taxon'));

		}

		$ut = $this->models->UserTaxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'user_id' => $this->getCurrentUserId()
			),'taxon_id',false,false,true,'taxon_id'
		);
		
		$this->getTaxonTree(null);
		
		$isEmptyTaxaList = count((array)$this->_treeList)==0;

		if (count((array)$ut)>0 || $isEmptyTaxaList) {

			$allow = false;
	
			foreach((array)$this->_treeList as $key => $val) {
	
				if ($allow && $val['level'] <= $prevLevel) {
				
					$allow = false;
	
				}
	
				if (array_key_exists($val['id'],$ut)) {
	
					$allow = true;
					
					$prevLevel = $val['level'];
	
				}
				
				if ($allow) {
				
					$taxa[] = $val;
				
				}

			}

			if (!empty($this->requestData['taxon'])) {
			
				$isHybrid = isset($this->requestData['is_hybrid']) && $this->requestData['is_hybrid'] == 'on';
				
				$parentId =
					($this->requestData['id']==$this->requestData['parent_id'] ? null : ($isEmptyTaxaList ? null : $this->requestData['parent_id']));

				if ($this->isTaxonNameUnique($this->requestData['taxon'],$this->requestData['id'],false)) {
	
					if ($this->canParentHaveChildTaxa($this->requestData['parent_id']) || $isEmptyTaxaList) {
	
						if (!$isHybrid || ($isHybrid && $this->canRankBeHybrid($this->requestData['rank_id']))) {
		
							$this->models->Taxon->save(
								array(
									'id' => ($this->requestData['id'] ? $this->requestData['id'] : null),
									'project_id' => $this->getCurrentProjectId(),
									'taxon' => $this->requestData['taxon'],
									'parent_id' => $parentId,
									'rank_id' => $this->requestData['rank_id'],
									'is_hybrid' =>  ($isHybrid ? 1 : 0)
								)
							);
							
							$this->reOrderTaxonTree();
	
							$this->addMessage(_('Taxon saved.'));
							
							unset($this->requestData['taxon']);
							
							$this->redirect('list.php');
		
						} else {
			
							$this->addError(_('Rank cannot be hybrid.'));
			
						}
		
					} else {
		
						$this->addError(_('Parent cannot have child taxa.'));
		
					}
	
				} else {
	
					$this->addError(_('Taxon name already in database.'));
	
				}
			
			}
				
			$pr = $this->getProjectRanks();
	
			$this->smarty->assign('allowed',true);

			$this->smarty->assign('data',$data);
	
			$this->smarty->assign('projectRanks',$pr);

			$this->smarty->assign('taxa',$taxa);

		} else {
				
			$this->smarty->assign('allowed',false);

			$this->addMessage(_('No taxa have been assigned to you.'));
		
		}	

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

        $this->checkAuthorisation();
        
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
            
            $this->setPageName(sprintf(_('Editing "%s"'),$taxon['taxon']));
        
        
			if (!$this->doLockOutUser($this->requestData['id'])) {
			// if new taxon OR existing taxon not being edited by someone else, get languages and content
	
				// get available languages
				$lp = $_SESSION['project']['languages'];
	
				foreach ((array) $lp as $key => $val) {
					
					$l = $this->models->Language->get($val['language_id']);
					
					$lp[$key]['language'] = $l['language'];
					
					$lp[$key]['iso2'] = $l['iso2'];
					
					$lp[$key]['iso3'] = $l['iso3'];
	
				}
				
				// determine the language the page will open in
				$startLanguage = !empty($this->requestData['lan']) ? $this->requestData['lan'] : $_SESSION['project']['default_language_id'];
	
				// get the defined categories (just the page definitions, no content yet)
				$tp = $this->models->PageTaxon->get(array(
					'project_id' => $this->getCurrentProjectId()
				));
	
				foreach ((array) $tp as $key => $val) {
					
					foreach ((array) $lp as $k => $language) {
						
						// for each category in each language, get the category title
						$tpt = $this->models->PageTaxonTitle->get(
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
				
				if (isset($taxon))
					$this->smarty->assign('taxon', $taxon);
	
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

		} else {
		// no id
		
			$this->smarty->assign('taxon', array(
				'id' => -1
			));
				
			$this->addError(_('No taxon ID specified.'));
		
		}

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
        
        $this->setPageName(_('Taxon list'));

		if (isset($this->requestData['id']) && isset($this->requestData['move']) && !$this->isFormResubmit()) {

			$this->moveIdInTaxonOrder($this->requestData['id'],$this->requestData['move']);

			if (isset($this->requestData['scroll'])) $this->smarty->assign('scroll', $this->requestData['scroll']);

		}

		// get taxa assigned to user
		$ut = $this->models->UserTaxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'user_id' => $this->getCurrentUserId()
			),'taxon_id',false,false,true,'taxon_id'
		);

		// get complete taxon tree
		$this->getTaxonTree(null);

		$allow = false;
		$prevAllowedLevel = -1;

		// discard all taxa above the levels (which means a smaller level-value) assigned to the user
		foreach((array)$this->_treeList as $key => $val) {

			if ($allow && $val['level'] <= $prevAllowedLevel) {
			
				$allow = false;

			}

			if (isset($ut) && array_key_exists($val['id'],$ut)) {

				$allow = true;
				
				$prevAllowedLevel = $val['level'];

			}

			if ($allow) {

				$taxa[] = $val;
			
			}
			
			$prevLevel = $val['level'];

		}

		if (isset($taxa) && count((array)$taxa)>0) {

			// get (or store) languages and the ranks list from session
			$lp = $_SESSION['project']['languages'];

			if (isset($_SESSION['project']['pages'])) {
	
				$tp = $_SESSION['project']['pages'];
	
			} else {
		
				$tp = $this->models->PageTaxon->get(array(
					'project_id' => $this->getCurrentProjectId()
				), 'count(*) as tot');
	
				$_SESSION['project']['pages'] = $tp;
			}
        
			if (isset($_SESSION['project']['ranklist'])) {
	
				$rl = $_SESSION['project']['ranklist'];
			
			} else {
	
				$pr = $this->getProjectRanks();
		
				foreach((array)$pr as $key => $val) {
				
					$rl[$val['id']] = $val['rank'];
	
				}
				
				$_SESSION['project']['ranklist'] = $rl;
	
			}


			foreach((array)$this->controllerSettings['media']['allowedFormats'] as $key => $val) {
	
				$d[$val['mime']] = $val['media_type'];
	
			}

			foreach ((array) $taxa as $key => $taxon) {
			
				$taxa[$key]['rank'] = $rl[$taxon['rank_id']];
				
				$ct = $this->models->ContentTaxon->get(
				array(
					'taxon_id' => $taxon['id'], 
					'publish' => 1, 
					'project_id' => $this->getCurrentProjectId()
				), 'count(*) as tot');

				$taxa[$key]['pct_finished'] = round(($ct[0]['tot'] / (count($lp) * $tp[0]['tot']))*100);

				$mt = $this->models->MediaTaxon->get(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $taxon['id']
					),'count(*) as total, mime_type',false,'mime_type'
				);
            
				foreach((array)$mt as $mtkey => $mtval) {
	
					$taxa[$key]['mediaCount'][$d[$mtval['mime_type']]] = 
						(isset($taxa[$key]['mediaCount'][$d[$mtval['mime_type']]]) ? 
							$taxa[$key]['mediaCount'][$d[$mtval['mime_type']]]: 
							0) 
						+ $mtval['total'];
							
					$taxa[$key]['totMediaCount'] =
						(isset($taxa[$key]['totMediaCount']) ? 
							$taxa[$key]['totMediaCount'] : 
							0 ) 
						+ $taxa[$key]['mediaCount'][$d[$mtval['mime_type']]];

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
			
			if (isset($taxa)) $this->smarty->assign('taxa', $taxa);
			
			$this->smarty->assign('languages', $lp);

		} else {
				
			$this->addMessage(_('No taxa have been assigned to you.'));
		
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
            
            $this->setPageName(sprintf(_('Media for "%s"'),$taxon['taxon']));

            $this->smarty->assign('id',$this->requestData['id']);

            $media = $this->models->MediaTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->requestData['id']
                ),false,'mime_type, file_name'
            );
            
            
            foreach((array)$this->controllerSettings['media']['allowedFormats'] as $key => $val) {
            
                $d[$val['mime']] = $val['media_type'];
            
            }

            foreach((array)$media as $key => $val) {


                $mdt = $this->models->MediaDescriptionsTaxon->get(
                    array(
                        'media_id' => $val['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $_SESSION['project']['default_language_id']
                    )
                );
                
                $val['description'] = $mdt[0]['description'];

                if (isset($d[$val['mime_type']])) $r[$d[$val['mime_type']]][] = $val;

            }

            if (isset($r)) $this->smarty->assign('media',$r);

            $this->smarty->assign('languages', $_SESSION['project']['languages']);
            
            $this->smarty->assign('defaultLanguage', $_SESSION['project']['default_language_id']);

            $this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

        } else {

            $this->addError(_('No taxon specified.'));

        } 
        
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
            
            if ($taxon['id']) {

                $this->setPageName(sprintf(_('New media for "%s"'),$taxon['taxon']));

                if ($this->requestDataFiles && !$this->isFormResubmit()) {
                    $this->helpers->FileUploadHelper->setLegalMimeTypes($this->controllerSettings['media']['allowedFormats']);
                    $this->helpers->FileUploadHelper->setTempDir($this->getDefaultImageUploadDir());
                    $this->helpers->FileUploadHelper->setStorageDir($this->getProjectsMediaStorageDir());
                    $this->helpers->FileUploadHelper->handleTaxonMediaUpload($this->requestDataFiles);
    
                    $this->addError($this->helpers->FileUploadHelper->getErrors());
                    $filesToSave = $this->helpers->FileUploadHelper->getResult();
    
                    if ($filesToSave) {
    
                        foreach((array)$filesToSave as $key => $file) {

                            $thumb = false;

                            if (
                                $this->helpers->ImageThumberHelper->canResize($file['mime_type']) &&
                                $this->helpers->ImageThumberHelper->thumbnail($this->getProjectsMediaStorageDir().$file['name'])
                            ) {

                                $pi = pathinfo($file['name']);
                                $this->helpers->ImageThumberHelper->size_width(150);
                                
                                if ($this->helpers->ImageThumberHelper->save(
                                    $this->getProjectsThumbsStorageDir().$pi['filename'].'-thumb.'.$pi['extension']
                                )) {
                                
                                    $thumb = $pi['filename'].'-thumb.'.$pi['extension'];
                                
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
                                )
                            );
                
                            if ($mt) {
                                 
                                $this->addMessage(sprintf(_('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
    
                            } else {
    
                                $this->addError(_('Failed writing uploaded file to database.'),1);
    
                            }
                
                        }
            
                    }

                }
    
            } else {

                $this->addError(_('Unknown taxon.'));

            }

            $this->smarty->assign('id',$this->requestData['id']);

            $this->smarty->assign('allowedFormats',$this->controllerSettings['media']['allowedFormats']);

            $this->smarty->assign('iniSettings',
                array(
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size')
                )
            );

        } else {

            $this->addError(_('No taxon specified.'));

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
        
        $this->setPageName(_('Taxon file upload'));
        
        if ($this->requestDataFiles) {

            unset($_SESSION['system']['csv_data']);
            
            $this->helpers->CsvParserHelper->setFieldMax(3);

            $this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
        
            $this->addError($this->helpers->CsvParserHelper->getErrors());
            
            if (!$this->getErrors()) {

                $r = $this->helpers->CsvParserHelper->getResults();

				$pr = $this->getProjectRanks();

				foreach((array)$pr as $key => $val) {
				
					$d[] = trim(strtolower($val['rank']));

					if ($val['can_hybrid']==1) $h[] = trim(strtolower($val['rank']));

				}

				foreach((array)$r as $key => $val) {

					$r[$key][2] = (isset($val[2]) && strtolower($val[2])=='y' && in_array(strtolower($val[1]),$h));
					$r[$key][3] = in_array(strtolower($val[1]),$d);
				}

                $_SESSION['system']['csv_data'] = $r;

                $this->smarty->assign('results',$r);

            }    

        } elseif ($this->requestData) {

            if (isset($this->requestData['rows']) && isset($_SESSION['system']['csv_data'])) {
            
                $parenName = false;
                $predecessors = null;

                foreach((array)$this->requestData['rows'] as $key => $val) {

                    $name = $_SESSION['system']['csv_data'][$val][0];
                    $rank = $_SESSION['system']['csv_data'][$val][1];
                    $hybrid = $_SESSION['system']['csv_data'][$val][2];
                    $parentName = null;

                    if ($key==0) {
                    // first one never has a parent (top of the tree)

                        $predecessors[] = array($rank, $name, $hybrid);

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

                            $predecessors[] = array($rank, $name, $hybrid);

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
                                        $predecessors[] = array($rank, $name, $hybrid);
    
                                        break;
    
                                    }
    
                                }
    
                            }
                            
                            
                            if ($parentName==null) {
                            // did not find a previous occurrence of the current rank, so the previous taxon must be the parent

                                $parentName = $predecessors[count((array)$predecessors)-1][1];

                                $predecessors[] = array($rank, $name, $hybrid);

                            }

                        }

                    }

                    $this->importTaxon(
                        array(
                            'taxon_rank' => $rank,
                            'taxon_name' => $name,
                            'parent_taxon_name' => $parentName,
							'hybrid' => $hybrid
                        )
                    );

                }

                $this->reOrderTaxonTree();

                unset($_SESSION['system']['csv_data']);
                
                $this->addMessage(_('Data saved.'));

            }

        }

		$pr = $this->getProjectRanks();

		$this->smarty->assign('projectRanks',$pr);

        $this->printPage();
    
    }


    /**
     * AJAX interface for this class
     *
     * @access    public
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
        
        } else if ($this->requestData['action'] == 'get_page_labels') {
            
            $this->ajaxActionGetPageTitles();
        
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
        
        } else if ($this->requestData['action'] == 'check_taxon_name') {

            $this->isTaxonNameUnique($this->requestData['taxon_name'],$this->requestData['id']);
        
        } else if ($this->requestData['action'] == 'save_taxon_name') {

            $this->ajaxActionSaveTaxonName();
        
        } else if ($this->requestData['action'] == 'save_media_desc') {

            $this->ajaxActionSaveMediaDescription();
        
        } else if ($this->requestData['action'] == 'get_media_desc') {

            $this->ajaxActionGetMediaDescription();
        
        } else if ($this->requestData['action'] == 'get_media_descs') {

            $this->ajaxActionGetMediaDescriptions();
        
        } else if ($this->requestData['action'] == 'delete_media') {

            $this->deleteTaxonMedia();
        
        } else if ($this->requestData['action'] == 'save_rank_label') {

            $this->ajaxActionSaveRankLabel();
        
        } else if ($this->requestData['action'] == 'get_rank_labels') {

            $this->ajaxActionGetRankLabels();
        
        } else if ($this->requestData['action'] == 'get_rank_by_parent') {

            $this->getRankByParent();
        
        } else if ($this->requestData['action'] == 'save_section_title') {

            $this->ajaxActionSaveSectionTitle();
        
        } else if ($this->requestData['action'] == 'delete_section_title') {

            $this->ajaxActionDeleteSectionTitle();
        
        } else if ($this->requestData['action'] == 'get_section_titles') {

            $this->ajaxActionGetSectionLabels();
        
        }
		
        $this->printPage();
    
    }


    /**
     * Interface for getting taxon data from the Catalogue Of Life webservice (which is somewhat unreliable)
     *
     * @access    public
     */
    public function colAction()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Import from Catalogue Of Life'));

        $this->printPage();
    
    }
    
    /**
     * Enables the user to choose taxin ranks for the project
     *
     * @access    public
     */
	public function ranksAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName(_('Taxonomic ranks'));

		$pr = $this->models->ProjectRank->get(array('project_id' => $this->getCurrentProjectId()),false,'parent_id',false,true,'rank_id');

		if (isset($this->requestData['ranks'])) {

			$parent = 'null';

			foreach((array)$this->requestData['ranks'] as $key => $rank) {

				if (!empty($pr[$rank])) {

					$this->models->ProjectRank->save(
						array(
							'id' => $pr[$rank]['id'],
							'parent_id' => $parent
						)
					);
					
					$parent = $pr[$rank]['id'];

				} else {

					$this->models->ProjectRank->save(
						array(
		                    'id' => null, 
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank,
							'parent_id' => $parent
						)
					);
					
					$parent = $this->models->ProjectRank->getNewId();

				}

			}
			
			foreach((array)$pr as $key => $rank) {

				if(!in_array($rank['rank_id'],$this->requestData['ranks'])) {

					$pr = $this->models->ProjectRank->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank['rank_id']
						)
					);

					foreach((array)$pr as $key => $val) {

						$this->models->LabelProjectRank->delete(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'project_rank_id' =>  $val['id']
							)
						);

					}

					$this->models->ProjectRank->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank['rank_id']
						)
					);

				}

			}
			
			$this->addMessage(_('Ranks saved.'));

		}

		$r = 
			array_merge(
				$this->models->Rank->get(array('parent_id !=' => -1),false,'parent_id',false,true,'id'),
				$this->models->Rank->get(array('parent_id' => -1),false,'parent_id',false,true,'id')
			);

		$pr = $this->getProjectRanks();

		$this->smarty->assign('ranks',$r);

		$this->smarty->assign('projectRanks',$pr);

        $this->printPage();

	}
	
    /**
     * Enables the user to provide labels in all defined project languages
     *
     * @access    public
     */
	public function ranklabelsAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName(_('Taxonomic ranks: labels'));

		$pr = $this->getProjectRanks(true);

		$this->smarty->assign('projectRanks',$pr);

		$this->smarty->assign('languages',$_SESSION['project']['languages']);

        $this->printPage();

	}
	
	public function sectionsAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName(_('Define sections'));

		if (isset($this->requestData['new']) && !$this->isFormResubmit()) {

			foreach((array)$this->requestData['new'] as $key => $val) {

				$this->models->Section->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(), 
						'page_id' => $key,
						'section' => $val
					)
				);
		
			}

		}

        $lp = $_SESSION['project']['languages'];

        $defaultLanguage = $_SESSION['project']['default_language_id'];
        
        $pages = $this->models->PageTaxon->get(array(
            'project_id' => $this->getCurrentProjectId()
            ),'*','show_order'
        );

       foreach((array)$pages as $key => $val) {

			$s = $this->models->Section->get(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'page_id' => $val['id'],
			),'*, ifnull(show_order,999) as show_order','show_order');
			
			$pages[$key]['sections'] = $s;

        }

        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $defaultLanguage);
        
        $this->printPage();

	}	

	public function collaboratorsAction()
	{

		$this->checkAuthorisation();

        $this->setPageName(_('Assign taxa to collaborators'));

		if (isset($this->requestData) && !$this->isFormResubmit()) {

			if (!empty($this->requestData['delete'])) {

				$this->models->UserTaxon->delete(
					array(
						'id' => $this->requestData['delete'],
						'project_id' => $this->getCurrentProjectId(),
					)
				);


			} else {

				$this->models->UserTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->requestData['user_id'],
						'taxon_id' => $this->requestData['taxon_id'],
					)
				);

			}

		}

        $pru = $this->models->ProjectRoleUser->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'role_id !=' => '1',
				'active' => 1
			)
		);

        foreach ((array) $pru as $key => $val) {

            $u = $this->models->User->get($val['user_id']);

            $r = $this->models->Role->get($val['role_id']);
            
            $u['role'] = $r['role'];
            $u['role_id'] = $r['id'];

            $users[] = $u;
        
        }

        $this->getTaxonTree(null);

		$ut = $this->models->UserTaxon->get(
				array(
					'project_id' => $this->getCurrentProjectId()
					),false,'taxon_id'
				);

		foreach((array)$ut as $key => $val) {

			$ut[$key]['taxon'] = $this->models->Taxon->get($val['taxon_id']);

		}

        $this->smarty->assign('usersTaxa', $ut);

        $this->smarty->assign('users', $users);

		$this->smarty->assign('taxa',$this->_treeList);

		$this->printPage();

	}	


	private function setProjectLanguages()
    {

        $lp = $this->models->LanguageProject->get(array(
            'project_id' => $this->getCurrentProjectId()
        ),false,'def_language desc');
        
        foreach ((array) $lp as $key => $val) {
            
            $l = $this->models->Language->get($val['language_id']);
            
            $lp[$key]['language'] = $l['language'];
            $lp[$key]['direction'] = $l['direction'];
            
            if ($val['def_language'] == 1)
                $defaultLanguage = $val['language_id'];
        
        }
        
        $_SESSION['project']['languages'] = $lp;

        $_SESSION['project']['default_language_id'] = $defaultLanguage;

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

    private function createStandardCategories() 
    {    

        $tp = $this->models->PageTaxon->get(
            array(
                'project_id' => $this->getCurrentProjectId()
            ),'count(*) as total'
        );
        

        foreach((array)$this->controllerSettings['defaultCategories'] as $key => $page) {

            if ($tp[0]['total']==0) {

                if ($this->createTaxonCategory(_($page['name']), $key, isset($page['default']) && $page['default'])) {

	                $this->createTaxonCategorySections($page['sections'],  $this->models->PageTaxon->getNewId());
	
				}

            } else {

                if (isset($page['mandatory']) && $page['mandatory']===true) {

                    $d = $this->models->PageTaxon->get(
                        array(
                            'project_id' => $this->getCurrentProjectId(),
                            'page' => $page['name'], 
                        ),'count(*) as total'
                    );

                    if ($d[0]['total']==0) {
        
                        if ($this->createTaxonCategory(_($page['name']), $key, isset($page['default']) && $page['default'])) {

							$this->createTaxonCategorySections($page['sections'],  $this->models->PageTaxon->getNewId());
			
						}

                    } 

                }

            }

        }

    }

    private function createTaxonCategory ($name, $show_order = false, $isDefault = false)
    {

        return $this->models->PageTaxon->save(array(
            'id' => null, 
            'page' => $name, 
            'show_order' => $show_order!==false ? $show_order : 0, 
            'project_id' => $this->getCurrentProjectId(), 
            'def_page' => $isDefault ? '1' : '0'
        ));
    
    }
	
	private function createTaxonCategorySections($sections, $pageId)
	{

		foreach((array)$sections as $key => $val) {

			$this->models->Section->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(), 
				'page_id' => $pageId,
				'section' => $val, 
				'show_order' => $key, 
			));

		}

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
	
	} else {
		
		$this->models->ContentTaxon->delete(array(
			'project_id' => $this->getCurrentProjectId(), 
			'page_id' => $this->requestData['id']
		));
		
		$this->models->PageTaxonTitle->delete(array(
			'project_id' => $this->getCurrentProjectId(), 
			'page_id' => $this->requestData['id']
		));

		$this->models->Section->delete(
		array(
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
        
        if (empty($this->requestData['language'])) {
            
            return;
        
        } else {
		
			$l = $this->models->Language->get($this->requestData['language'],'direction');

			$ptt = $this->models->PageTaxonTitle->get(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->requestData['language']
				),'id,title,page_id,language_id,\''.$l['direction'].'\' as direction'
			);
                
            $this->smarty->assign('returnText', json_encode($ptt));
        
        }
    
    }


    private function ajaxActionSavePageTitle ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        } else {
            
            if (empty($this->requestData['label'])) {
                
                $this->models->PageTaxonTitle->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'page_id' => $this->requestData['id']
                    )
                );
            
            } else {
                
                $tpt = $this->models->PageTaxonTitle->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'page_id' => $this->requestData['id']
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
                           entrered as category title of the default page in the default language, but the assumption
                           that that is the place where the leading name of a taxon is entered might be faulty

                        // if succesful, get the projects default language
                        $lp = $_SESSION['project']['languages'];
                        
                        $defaultLanguage = $_SESSION['project']['default_language_id'];

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
                                    'content' => isset($filteredContent) ? $filteredContent['content'] : null,
                                    'modified' => isset($filteredContent) ? $filteredContent['modified'] : null
                                )
                            )
                        );
                                            
                    } else {
                        
                        $this->addError(_('Could not save taxon content.'));
                    
                    }
                
                }
                else {
                    
                    $this->addError(_('No page title specified.'));
                
                }
            
            }
            else {
                
                $this->addError(_('No language specified.'));
            
            }
        
        } else {
            
            $this->addError(_('Could not save taxon.'));
        
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
                    'content' => $this->getDefaultPageSections($this->requestData['page'],$this->requestData['language']), 
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
        
        if (empty($this->requestData['id'])) {

            return;
        
        } else {

            $mt = $this->models->MediaTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'taxon_id' => $this->requestData['id']
                )
            );

            foreach((array)$mt as $key => $val) {

                $this->deleteTaxonMedia($val['id'],false);

            }

            $this->models->ContentTaxon->delete(
            array(
                'taxon_id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
            
            $this->models->Taxon->delete(array(
                'id' => $this->requestData['id'], 
                'project_id' => $this->getCurrentProjectId()
            ));
			
			$this->reOrderTaxonTree();
        
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
            
            } else {
                
                $this->smarty->assign('returnText', '<error>');
            
            }
        
        }
    
    }

    private function ajaxActionGetTaxonUndo ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language']) || empty($this->requestData['page'])) {
            
            return;
        
        } else {
            
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
		
		$rankId = false;

		if (is_numeric($taxon['taxon_rank'])) {

			$rankId = $taxon['taxon_rank'];

		} else {

			$r = $this->models->Rank->get(array('rank' => $taxon['taxon_rank']));

			if ($r==false) return;

			$pr = $this->models->ProjectRank->get(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'rank_id' => $r[0]['id']
				)
			);

			$rankId = $pr[0]['id'];

		}

		if (!$rankId) return;

        $t = $this->models->Taxon->get(
            array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon' => $taxon['taxon_name']
            )
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
                    'rank_id' => $rankId,
                    'is_hybrid' => $taxon['hybrid'] && $this->canRankBeHybrid($rankId) ? 1 : 0
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
                        'rank_id' => $rankId,
	                    'is_hybrid' => $taxon['hybrid'] && $this->canRankBeHybrid($rankId) ? 1 : 0
                    )

                );

            }

        }

    }

    private function isTaxonNameUnique($taxonName=false,$idToIgnore=false,$output=true)
    {

		$taxonName = $taxonName ? $taxonName : $this->requestData['taxon_name'];

        if (empty($taxonName)) return;

		if ($idToIgnore) {

			$t = $this->models->Taxon->get(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => trim($taxonName),
					'id != ' => $idToIgnore,
				),'count(*) as total',false,false,true
			);

		} else {
		
			$t = $this->models->Taxon->get(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => trim($taxonName)
				),'count(*) as total',false,false,true
			);

		}


        if ($t[0]['total']>0) {
		
			if ($output) $this->smarty->assign('returnText', _('Taxon name already in database.'));

			return false;

        } else {

			if ($output) $this->smarty->assign('returnText', '<ok>');

			return true;

		}
		
    }

    private function ajaxActionSaveTaxonName () 
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
                    'taxon' => trim($this->requestData['taxon_name'])
                )
            );

            if ($d) $this->smarty->assign('returnText', '<ok>');

        }

    }

    private function getTaxonTree($pId=null,$level=0) 
    {
	
		if ($level==0) unset($this->_treeList);

        $t = $this->models->Taxon->get(
            array(
                'project_id' => $this->getCurrentProjectId(),
                ($pId === null ? 'parent_id is' : 'parent_id') => $pId
            ),false,'taxon_order');

        foreach((array)$t as $key => $val) {

			$val['level'] = $level;

			$val['sibling_count'] = count((array)$t);

			$val['sibling_pos'] = ($key==0 ? 'first' : ($key==count((array)$t)-1 ? 'last' : '-' ));

            $this->_treeList[] = $val;

			$t[$key]['level'] = $level;

			$t[$key]['children'] = $this->getTaxonTree($val['id'],$level+1);

        }

        return $t;

    }

    private function reOrderTaxonTree() 
    {

        $this->getTaxonTree(null);

        foreach((array)$this->_treeList as $key => $val) {

            $this->models->Taxon->save(
                array(
                    'id' => $val['id'],
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

    private function getDefaultPageSections($pageId,$languageId)
    {
	
        $s = $this->models->Section->get(
            array(
            'page_id' => $pageId,
            'project_id' => $this->getCurrentProjectId()
            ),false,'show_order asc'
        );
		
        $b = '';

		foreach((array)$s as $key => $val) {
		
			$ls = $this->models->LabelSection->get(
				array(
				'section_id' => $val['id'],
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $languageId
				),'label'
			);
			

			$b .= '<span class="taxon-section-head">'.$ls[0]['label'].'</span>'.chr(10);

		}

        return $b;
    
    }

    private function ajaxActionSaveMediaDescription()
    {

        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {

            return;

        } else {
            
            if (empty($this->requestData['description'])) {
                
                $this->models->MediaDescriptionsTaxon->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id']
                    ));
            
            } else {
                
                $mdt = $this->models->MediaDescriptionsTaxon->get(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id']
                    )
                );
                
                $this->models->MediaDescriptionsTaxon->save(
                    array(
                        'id' => isset($mdt[0]['id']) ? $mdt[0]['id'] : null, 
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id'], 
                        'description' => trim($this->requestData['description'])
                    )
                );
            
            }
            
            $this->smarty->assign('returnText', '<ok>');
        
        }
    
    }

    private function ajaxActionGetMediaDescription()
    {

        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {

            return;

        } else {
            
            $mdt = $this->models->MediaDescriptionsTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'media_id' => $this->requestData['id']
                ));

            $this->smarty->assign('returnText', $mdt[0]['description']);
        
        }

    }

    private function ajaxActionGetMediaDescriptions()
    {

        if (empty($this->requestData['language'])) {

            return;

        } else {
            
            $mt = $this->models->MediaTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                ),'id');

            foreach((array)$mt as $key => $val) {

                $mdt = $this->models->MediaDescriptionsTaxon->get(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'],
                        'media_id' => $val['id']
                    ),'description');

                $mt[$key]['description'] = $mdt ? $mdt[0]['description'] : null;
            }
                            
            $this->smarty->assign('returnText', json_encode($mt));
        
        }

    }

    private function deleteTaxonMedia($id = false,$output = true)
    {

        if ($id === false) {

            $id = $this->requestData['id'];

        }

        if (empty($id)) {

            return;

        } else {

            $mt = $this->models->MediaTaxon->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'id' => $id
                )
            );
			
			$delRecords = true;			
			
			if (file_exists($_SESSION['project']['paths']['project_media'].$mt[0]['file_name'])) {

				$delRecords = unlink($_SESSION['project']['paths']['project_media'].$mt[0]['file_name']);

			}

            if ($delRecords) {

                if ($mt[0]['thumb_name'] && file_exists($_SESSION['project']['paths']['project_thumbs'].$mt[0]['thumb_name'])) {
                    unlink($_SESSION['project']['paths']['project_thumbs'].$mt[0]['thumb_name']);
                }

                $this->models->MediaDescriptionsTaxon->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'media_id' => $id
                    )
                );


                $this->models->MediaTaxon->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'id' => $id
                    )
                );
                
                if ($output) $this->smarty->assign('returnText', '<ok>');

            } else {
            
                if ($output) $this->addError(sprintf(_('Could not delete file: %s'),$mt[0]['file_name']));
        
            }
    
        }

    }

    private function ajaxActionSaveRankLabel ()
    {
        
        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        } else {
            
            if (empty($this->requestData['label'])) {

                $this->models->LabelProjectRank->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'project_rank_id' => $this->requestData['id']
                    )
                );

            } else {
                
                $lpr = $this->models->LabelProjectRank->get(
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'project_rank_id' => $this->requestData['id']
					)
				);
                
                $this->models->LabelProjectRank->save(
					array(
						'id' => isset($lpr[0]['id']) ? $lpr[0]['id'] : null, 
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'project_rank_id' => $this->requestData['id'], 
						'label' => trim($this->requestData['label'])
					)
				);
            
            }
            
            $this->smarty->assign('returnText', 'saved');
        
        }
    
    }
	
	private function ajaxActionGetRankLabels()
	{

        if (empty($this->requestData['language'])) {
            
            return;
        
        } else {
			$l = $this->models->Language->get($this->requestData['language'],'direction');

			$lpr = $this->models->LabelProjectRank->get(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->requestData['language']
				),'*, \''.$l['direction'].'\' as direction'
			);
                
            $this->smarty->assign('returnText', json_encode($lpr));
        
        }

	}

	private function getProjectRanks($includeLanguageLabels=false)
	{

		$pr = $this->models->ProjectRank->get(array('project_id' => $this->getCurrentProjectId()),false,'rank_id');

		foreach((array)$pr as $rankkey => $rank) {

			$r = $this->models->Rank->get($rank['rank_id']);

			$pr[$rankkey]['rank'] = $r['rank'];

			$pr[$rankkey]['can_hybrid'] = $r['can_hybrid'];
			
			if ($includeLanguageLabels) {

				foreach((array)$_SESSION['project']['languages'] as $langaugekey => $language) {
	
					$lpr = $this->models->LabelProjectRank->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'project_rank_id' => $rank['id'],
							'language_id' => $language['language_id']
						),'label'
					);
					
					$pr[$rankkey]['labels'][$language['language_id']] = $lpr[0]['label'];
		
				}

			}

		}

		return $pr;

	}

	private function getRankByParent($id = false,$output = true)
	{

        if ($id === false) {

            $id = $this->requestData['id'];

        }

        if (empty($id)) {
            
            return;
        
        } else {

			$d = $this->models->Taxon->get($id);

			$d = $this->models->ProjectRank->get(array('parent_id' => $d['rank_id']));
			
			$result = $d[0]['id'] ? $d[0]['id'] : -1;
			
            if ($output) $this->smarty->assign('returnText', $result);

			return $result;
        
        }

	}

	private function canParentHaveChildTaxa($parentId)
	{

		return ($this->getRankByParent($parentId,false) != -1);

	}

	private function canRankBeHybrid($projectRankId)
	{

		$d = $this->models->ProjectRank->get($projectRankId);

		$r = $this->models->Rank->get($d['rank_id']);

		return ($r['can_hybrid']==1);

	}

	private function ajaxActionSaveSectionTitle()
	{

        if (empty($this->requestData['id']) || empty($this->requestData['language'])) {
            
            return;
        
        } else {
            
            if (empty($this->requestData['label'])) {
                
                $this->models->LabelSection->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'section_id' => $this->requestData['id']
                    )
                );
            
            } else {
                
                $ls = $this->models->LabelSection->get(
                array(
                    'project_id' => $this->getCurrentProjectId(), 
                    'language_id' => $this->requestData['language'], 
                    'section_id' => $this->requestData['id']
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

	private function ajaxActionDeleteSectionTitle()
	{
	
		if (empty($this->requestData['id'])) {
		
			return;
	
		} else {
		
			$this->models->LabelSection->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'section_id' => $this->requestData['id']
				)
			);

			$this->models->Section->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'id' => $this->requestData['id']
				)
			);
	
		}

	}

	private function ajaxActionGetSectionLabels()
	{

        if (empty($this->requestData['language'])) {
            
            return;
        
        } else {

			$l = $this->models->Language->get($this->requestData['language'],'direction');

			$ls = $this->models->LabelSection->get(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->requestData['language'],
				),'*, \''.$l['direction'].'\' as direction'
			);
   
            $this->smarty->assign('returnText', json_encode($ls));
        
        }

	}

	private function moveIdInTaxonOrder($id,$dir)
	{
	
		if ($dir != 'up' && $dir != 'down') return;

		$t1 = $this->models->Taxon->get(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

		$t2 = $this->models->Taxon->get(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'rank_id' => $t1[0]['rank_id'],
				'taxon_order '.($dir=='up' ? '<' : '>') => $t1[0]['taxon_order']
			),false,'taxon_order '.($dir=='up' ? 'desc' : 'asc')
		);

		if (count((array)$t2)!=0) {

			$this->models->Taxon->update(
				array(
					'taxon_order' => $t2[0]['taxon_order']
				),
				array(
					'id' => $t1[0]['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->models->Taxon->update(
				array(
					'taxon_order' => $t1[0]['taxon_order']
				),
				array(
					'id' => $t2[0]['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->reOrderTaxonTree();

		}

	}


}

