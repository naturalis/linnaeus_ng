<?php

/*
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
		  (must be done before editing taxa because they actively influence the content)
		- edit taxa
		
		- check project specific css
		- check project specific JS (taxonContentOpenMediaLink etc.)

  
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
		'choice_keystep' ,
		'literature',
		'literature_taxon'
    );
    
    public $usedHelpers = array(
        'col_loader_helper','csv_parser_helper','file_upload_helper','image_thumber_helper'
    );

	public $cssToLoad = array(
		'colorbox/colorbox.css',
		'taxon.css',
		'rank-list.css',
		'dialog/jquery.modaldialog.css'
	);
	public $jsToLoad = array(
		'all' => array(
			'taxon.js',
			'colorbox/jquery.colorbox.js',
			'front-end.js',
			'int-link.js',
			'dialog/jquery.modaldialog.js'
		)
	);


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

		unset($_SESSION['system']['activeTaxon']);
		unset($_SESSION['system']['highertaxa']);

        $this->checkAuthorisation();
        
        $this->setPageName(_('Species module overview'));
        
		if (count((array)$_SESSION['project']['languages'])==0)
			$this->addError(
				sprintf(
					_('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'),
					'<a href="../projects/data.php">','</a>')
				);

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
        if ($this->rHasVal('new_page') && !$this->isFormResubmit()) {
        
            $tp = $this->createTaxonCategory($this->requestData['new_page'],$this->requestData['show_order']);
            
            if ($tp !== true) {
                
                $this->addError(_('Could not save category.'),1);
                $this->addError('(' . $tp . ')',1);
				
            }
        
        }
        
        $lp = $_SESSION['project']['languages'];

        $defaultLanguage = $_SESSION['project']['default_language_id'];
        
        $pages = $this->models->PageTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'order' => 'show_order'
			)
        );
        
        foreach ((array) $pages as $key => $page) {
            
            foreach ((array) $lp as $k => $language) {
                
                $tpt = $this->models->PageTaxonTitle->_get(
					array(
						'id' =>
							array(
								'project_id' => $this->getCurrentProjectId(), 
								'page_id' => $page['id'], 
								'language_id' => $language['language_id']
							)
					)
				);
                
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

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

		$this->checkAuthorisation();

		if ($this->rHasId()) {

			$data = $this->getTaxonById();

	        $this->setPageName(sprintf(_('Editing taxon "%s"'),$data['taxon']));

		} else {

			$data = isset($this->requestData) ? $this->requestData : null;

	        $this->setPageName(_('New taxon'));

		}


		$pr = $this->getProjectRanks();
		
		if (count((array)$pr)==0) {

			$this->addMessage(_('No ranks have been defined.'));

		} else {

			/*
			// code also looked at what taxa were assigned to what user; now replaced by assigning rights to the entire page
			$ut = $this->models->UserTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->getCurrentUserId()
					),
				'columns' => 'taxon_id',
				'fieldAsIndex' => 'taxon_id'
				)
			);
			*/
			
			$this->getTaxonTree();

			$isEmptyTaxaList = !isset($this->treeList) || count((array)$this->treeList)==0;
			
			/*
			// code also looked at what taxa were assigned to what user; now replaced by assigning rights to the entire page
			if (count((array)$ut)>0 || $isEmptyTaxaList) {
	
				$allow = false;
	
				if (!$isEmptyTaxaList) {
		
					foreach((array)$this->treeList as $key => $val) {
			
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
	
				}
			*/
	
			if ($this->rHasVal('taxon')) {
			
				$isHybrid = $this->rHasVal('is_hybrid','on');
				
				$parentId =
					($this->requestData['id']==$this->requestData['parent_id'] ? 
						null : 
						($isEmptyTaxaList ? 
							null : 
							$this->requestData['parent_id']
						)
					);

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
	
							$this->addMessage(sprintf(_('Taxon "%s" saved.'),$this->requestData['taxon']));
							
							unset($data['taxon']);
							
							//$this->redirect('list.php');
		
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
				
			$this->smarty->assign('allowed',true);

			if (isset($data)) $this->smarty->assign('data',$data);
	
			$this->smarty->assign('projectRanks',$pr);

			if (isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

			/*
			// code also looked at what taxa were assigned to what user; now replaced by assigning rights to the entire page
			} else {
	
				$this->smarty->assign('allowed',false);
	
				$this->addMessage(_('No taxa have been assigned to you.'));
			
			}
			*/
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

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Taxon list'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
            )
        );
		
        if ($this->rHasId()) {
        // get existing taxon

			// replace possible [new litref] and [new media] tags with links to newly created reference of media
			$this->filterInternalTags($this->requestData['id']);

            $taxon = $this->getTaxonById();
			
			$_SESSION['system']['activeTaxon'] = array('taxon_id' => $taxon['id'],'taxon' => $taxon['taxon']);

            $this->setPageName(sprintf(_('Editing "%s"'),$taxon['taxon']));

			if (!$this->doLockOutUser($this->requestData['id'])) {
			// if new taxon OR existing taxon not being edited by someone else, get languages and content
	
				// get available languages
				$lp = $_SESSION['project']['languages'];

				// determine the language the page will open in
				$startLanguage = 
					$this->rHasVal('lan')? 
						$this->requestData['lan'] : 
						$_SESSION['project']['default_language_id'];
	
				// get the defined categories (just the page definitions, no content yet)
				$tp = $this->models->PageTaxon->_get(array('id'=>array(
					'project_id' => $this->getCurrentProjectId()
				),'order' => 'show_order'));
	
				foreach ((array) $tp as $key => $val) {
					
					foreach ((array) $lp as $k => $language) {
						
						// for each category in each language, get the category title
						$tpt = $this->models->PageTaxonTitle->_get(
							array('id'=>array(
								'project_id' => $this->getCurrentProjectId(), 
								'language_id' => $language['language_id'], 
								'page_id' => $val['id']
							)));
						
						$tp[$key]['titles'][$language['language_id']] = $tpt[0];
					
					}
					
					if ($val['def_page'] == 1) $defaultPage = $val['id'];
				
				}
				
				// determine the page_id the page will open in
				$startPage = $this->rHasVal('page') ? $this->requestData['page'] : $defaultPage;

				if (isset($taxon)) {

					$this->smarty->assign('taxon', $taxon);

					$this->smarty->assign('media', addslashes(json_encode($this->getTaxonMedia($taxon['id']))));

					$this->smarty->assign('literature', addslashes(json_encode($this->getTaxonLiterature($taxon['id']))));
					
				}

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
     * Deleting a taxon with children: decide what to do with the progeny
     *
     * @access    public
     */
    public function deleteAction ()
    {

		$this->checkAuthorisation();

		if ($this->rHasVal('action','process') && $this->rHasId()) {
		
			$taxon = $this->getTaxonById($this->requestData['id']);
		
			foreach((array)$this->requestData['child'] as $key => $val) {
			
				if ($val=='delete') {

					$this->deleteTaxonBranch($key);

				} elseif ($val=='orphan') {

					// kill off the parent_id and turn it into a orphan
					$this->models->Taxon->update(
						array('parent_id' => 'null'),
						array('project_id' => $this->getCurrentProjectId(),'id' => $key)
					);
				
				} elseif ($val=='attach') {
				
					// reacttach to the parent_id of the to-be-deleted taxon
					$this->models->Taxon->update(
						array('parent_id' => $taxon['parent_id']),
						array('project_id' => $this->getCurrentProjectId(),'id' => $key)
					);

				}

			}

			// delete the taxon
			$this->deleteTaxon($this->requestData['id']);

			$this->redirect('list.php');

		} elseif ($this->rHasId()) {

			$taxon = $this->getTaxonById();
			
			if (isset($taxon)) {

				$parent = $this->getTaxonById($taxon['parent_id']);
	
				$this->getTaxonTree(array('pId' => $taxon['id']));
	
				$this->setPageName(sprintf(_('Deleting taxon "%s"'),$taxon['taxon']));
				
				$pr = $this->getProjectRanks(array('idsAsIndex' => true));

				$this->smarty->assign('ranks',$pr);
		
				$this->smarty->assign('taxon',$taxon);
		
				$this->smarty->assign('parent',$parent);
		
				$this->smarty->assign('taxa',$this->treeList);
		
			} else {

				$this->addError(_('Non-existant ID.'));

			}

		} else {
		
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
		
		$this->setPageName(_('Orphaned taxa'));

		if ($this->rHasVal('child')) {

			foreach((array)$this->requestData['child'] as $key => $val) {
			
				if ($val=='delete') {
				
					$this->deleteTaxonBranch($key);
		
					$this->deleteTaxon($key);

				} elseif ($val=='attach') {

					$this->models->Taxon->update(
						array('parent_id' => $this->requestData['parent'][$key]),
						array('project_id' => $this->getCurrentProjectId(),'id' => $key)
					);

				}
						
			}

		}

		$pr = $this->getProjectRanks(array('idsAsIndex' => true));

		$topRank = array_slice($pr,0,1);

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'parent_id is' => 'null',
					'rank_id !=' => $topRank[0]['id']				
				)
			)
		);

		foreach((array)$taxa as $key => $val) {

			$this->getTaxonTree(array('pId' => $val['id']));

			if (isset($this->treeList)) $taxa[$key]['tree'] = $this->treeList;

			$d = $this->models->ProjectRank->_get(
				array(
				'id' => $val['rank_id'],
				'columns' => 'parent_id'
				)
			);

			$this->getTaxonTree(
				array(
					'stopAtRankId' => $d['parent_id'],
					'includeOrphans' => false
				)
			);

			if (isset($this->treeList)) $taxa[$key]['parents'] = $this->treeList;

		}

		$this->smarty->assign('tree',$this->treeList);

		$this->smarty->assign('ranks',$pr);

		if (isset($taxa)) $this->smarty->assign('taxa',$taxa);

		$this->printPage();

	}
	
	
    /**
     * List existing taxa
     *
     * @access    public
     */
    public function listAction ()
    {
	
		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();
        
        $this->setPageName(_('Taxon list'));
		
		unset($_SESSION['system']['activeTaxon']);

		if ($this->rHasId() && $this->rHasVal('move') && !$this->isFormResubmit()) {
		// moving branches up and down the stem

			$this->moveIdInTaxonOrder($this->requestData['id'],$this->requestData['move']);

			if ($this->rHasVal('scroll')) $this->smarty->assign('scroll', $this->requestData['scroll']);

		}

		// get complete taxon tree
		$this->getTaxonTree();

		if (isset($this->treeList)) { 

			if (isset($_SESSION['project']['ranklist']) && 
				(
					isset($_SESSION['project']['ranklistsource']) && 
					$_SESSION['project']['ranklistsource'] == ($this->maskAsHigherTaxa() ? 'highertaxa' : 'lowertaxa')
				)
			) {
	
				$rl = $_SESSION['project']['ranklist'];

			} else {
	
				$pr = $this->getProjectRanks();

				foreach((array)$pr as $key => $val) {

					if (!$this->maskAsHigherTaxa() && $val['lower_taxon']==1) {
					// only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
				
						$rl[$val['id']] = $val['rank'];

					} else
					if ($this->maskAsHigherTaxa() && $val['lower_taxon']!=1) {
					// only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
				
						$rl[$val['id']] = $val['rank'];
					}
	
				}

				if (isset($rl)) $_SESSION['project']['ranklist'] = $rl;
				$_SESSION['project']['ranklistsource'] = ($this->maskAsHigherTaxa() ? 'highertaxa' : 'lowertaxa');

			}

			// get taxa assigned to user
			$ut = $this->models->UserTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->getCurrentUserId()
					),
					'columns' => 'taxon_id',
					'fieldAsIndex' => 'taxon_id'
				)
			);
		
			$allow = false;
			$prevAllowedLevel = -1;

			// discard all taxa above the levels (i.e. smaller level-value) assigned to the user
			foreach((array)$this->treeList as $key => $val) {
	
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

		}

		if (isset($taxa) && count((array)$taxa)>0) {

			$projectLanguages = $_SESSION['project']['languages'];

			$pageCount = $this->getTaxonPageCount();

			$contentCount = $this->getTaxaContentCount();

			$synonymsCount = $this->getTaxaSynonymCount();

			$commonnameCount = $this->getTaxaCommonnameCount();

			$mediaCount = $this->getTaxaMediaCount();

			$literatureCount = $this->getTaxaLiteratureCount();

			foreach ((array) $taxa as $key => $taxon) {

				if (isset($rl[$taxon['rank_id']])) {

					$taxa[$key]['pctFinished'] = 
						isset($contentCount[$taxon['id']]) ? 
							round(
								(
									(isset($contentCount[$taxon['id']]) ? $contentCount[$taxon['id']] : 0) / 
									(count((array)$projectLanguages) * $pageCount)
							) * 100) :
							0;

					$taxa[$key]['synonymCount'] = isset($synonymsCount[$taxon['id']]) ? $synonymsCount[$taxon['id']] : 0;

					$taxa[$key]['commonnameCount'] = isset($commonnameCount[$taxon['id']]) ? $commonnameCount[$taxon['id']] : 0;

					$taxa[$key]['mediaCount'] = isset($mediaCount[$taxon['id']]) ? $mediaCount[$taxon['id']] : 0;

					$taxa[$key]['literatureCount'] = isset($literatureCount[$taxon['id']]) ? $literatureCount[$taxon['id']] : 0;

					$taxa[$key]['rank'] = $rl[$taxon['rank_id']];
					
					$d[] = $taxa[$key];

				}
        
	        }

			$taxa = isset($d) ? $d : null;
			
			if (count((array)$taxa)==0) $this->addMessage(_('There are no taxa for you to edit.'));

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

			$this->smarty->assign('sortBy', $sortBy);
			
			if (isset($taxa)) $this->smarty->assign('taxa', $taxa);
			
			$this->smarty->assign('languages', $projectLanguages);

		} else {
				
			$this->addMessage(_('No taxa have been assigned to you.'));
		
		}

        $this->printPage();
    
    }

    /**
     * See and maintain literary references
     *
     * @access    public
     */
    public function literatureAction ()
    {

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Taxon list'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
            )
        );

        if ($this->rHasId()) {
        // get existing taxon name
		
            $taxon = $this->getTaxonById();

			$_SESSION['system']['activeTaxon'] = array('taxon_id' => $taxon['id'],'taxon' => $taxon['taxon']);

            $this->setPageName(sprintf(_('Literature for "%s"'),$taxon['taxon']));

            $this->smarty->assign('id',$this->requestData['id']);

			$refs = $this->getTaxonLiterature($taxon['id']);
		
			// user requested a sort of the table
			if ($this->rHasVal('key')) {
	
				$sortBy = array(
					'key' => $this->requestData['key'], 
					'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
					'case' => 'i'
				);

			} else {
	
				$sortBy = array(
					'key' => 'author_first', 
					'dir' => 'asc', 
					'case' => 'i'
				);
		
			}

			$this->customSortArray($refs, $sortBy);	

			$this->smarty->assign('sortBy', $sortBy);

            if (isset($refs)) $this->smarty->assign('refs',$refs);

        } else {

            $this->addError(_('No taxon specified.'));

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

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Taxon list'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
            )
        );

        if ($this->rHasId()) {
        // get existing taxon name

            $taxon = $this->getTaxonById();

            $this->setPageName(sprintf(_('Media for "%s"'),$taxon['taxon']));

            $this->smarty->assign('id',$this->requestData['id']);

            $media = $this->getTaxonMedia($this->requestData['id']);

            foreach((array)$this->controllerSettings['media']['allowedFormats'] as $key => $val) {

                $d[$val['mime']] = $val['media_type'];

            }

            foreach((array)$media as $key => $val) {


                $mdt = $this->models->MediaDescriptionsTaxon->_get(
					array(
						'id' => array(
							'media_id' => $val['id'], 
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $_SESSION['project']['default_language_id']
						)
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

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Taxon list'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
            )
        );

		if ($this->rHasVal('add','hoc') && !isset($_SESSION['system']['media']['newRef'])) {
		// referred from the taxon content editing page

			$_SESSION['system']['media']['newRef'] = '<new>';

			$this->requestData['id'] = $_SESSION['system']['activeTaxon']['taxon_id'];

		}

        if ($this->rHasId()) {
        // get existing taxon name

            $taxon = $this->getTaxonById();
            
            if ($taxon['id']) {

                $this->setPageName(sprintf(_('New media for "%s"'),$taxon['taxon']));

                if ($this->requestDataFiles && !$this->isFormResubmit()) {

                    $filesToSave = $this->getUploadedFiles();
					
					$firstInsert = false;
    
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
							
							if (!$firstInsert) {
							
								$firstInsert = array('id'=>$this->models->MediaTaxon->getNewId(),'name'=>$file['name']);

							}
                
                            if ($mt) {
                                 
                                $this->addMessage(sprintf(_('Saved: %s (%s)'),$file['original_name'],$file['media_name']));
    
                            } else {
    
                                $this->addError(_('Failed writing uploaded file to database.'),1);
    
                            }
                
                        }

						if (isset($_SESSION['system']['media']['newRef']) && $_SESSION['system']['media']['newRef'] == '<new>') {
		
							$_SESSION['system']['media']['newRef'] =
								'<span class="taxonContentMediaLink" onclick="taxonContentOpenMediaLink('.$firstInsert['id'].');">'.
									$firstInsert['name'].
								'</span>';
		
							$this->redirect('../species/taxon.php?id='.$_SESSION['system']['activeTaxon']['taxon_id']);
		
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
				case 'comma' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(',');
					break;
				case 'semi-colon' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(';');
					break;
				case 'tab' :
					$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
					break;
			}

            $this->helpers->CsvParserHelper->setFieldMax($_SESSION['project']['includes_hybrids'] ? 3 : 2);

            $this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
        
            $this->addError($this->helpers->CsvParserHelper->getErrors());
            
            if (!$this->getErrors()) {

                $r = $this->helpers->CsvParserHelper->getResults();

				$pr = $this->getProjectRanks();
				
				// get all ranks for this project
				foreach((array)$pr as $key => $val) {
				
					$d[] = trim(strtolower($val['rank']));

					if ($_SESSION['project']['includes_hybrids'] && $val['can_hybrid']==1) $h[] = trim(strtolower($val['rank']));

				}
				
				$upperTaxonRank = false;
				
				$prevNames = array();

				foreach((array)$r as $key => $val) {

					// check whether 'has hybrid' is present and legal
					if ($_SESSION['project']['includes_hybrids'])
						$r[$key][2] = (isset($val[2]) && strtolower($val[2])=='y' && in_array(strtolower($val[1]),$h));

					$r[$key][$_SESSION['project']['includes_hybrids'] ? 3 : 2] = 'ok';


					// check whether the taxon name doesn't already exist
					$t = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'taxon' => $r[$key][0]
							),
							'columns' => 'count(*) as total'
						)
					);

					
					if (in_array($val[0],$prevNames)) {
					// set whether the taxon can be imported, based on whether it has duplicates in the import

						$r[$key][$_SESSION['project']['includes_hybrids'] ? 3 : 2] = _('Duplicate name');
						
					} else
					if ($t[0]['total']!=0) {
					// set whether the taxon can be imported, based on whether the name already exists
					
						$r[$key][$_SESSION['project']['includes_hybrids'] ? 3 : 2] = _('Name already exists in the database');
					
					} else
					if (!(isset($val[1]) && in_array(strtolower($val[1]),$d))) {
					// set whether the taxon can be imported, based on whether it has a legal rank

						$r[$key][$_SESSION['project']['includes_hybrids'] ? 3 : 2] = _('Unknown rank');

					} else {

						$prevNames[] = $val[0];

					}

					if ($upperTaxonRank==false && $r[$key][$_SESSION['project']['includes_hybrids'] ? 3 : 2]=='ok') $upperTaxonRank=$val[1];

				}

				$upperTaxonRank = strtolower($upperTaxonRank);

				// check whether the uppermost taxa in the csv have to be connected to a previous taxon
				// if the first to be imported taxon is a kingdom (or rather, of the uppermost rank), do nothing: it can't have a parent
				if ($upperTaxonRank!=strtolower($pr[0]['rank'])) {

					$parentRank = false;

					// find what rank a parent should be
					foreach((array)$pr as $key => $val) {

						if (strtolower($val['rank'])==$upperTaxonRank) {

							$parentRank = $pr[$key-1];

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
							)
						);
						
						if (count($t)==1) {

							$this->addMessage(
								sprintf(
									_('The taxon or taxa of the rank "%s" will be connected as child to the already existing taxon "%s".'),
									$upperTaxonRank,
									$t[0]['taxon']
									)
								);

							$this->smarty->assign('connectToTaxonId',$t[0]['id']);

						} else {

							$this->addMessage(
								sprintf(
									_('There are multiple possible parents of the uppermost taxon or taxa. Please choose the appropriate one.')
									)
								);

							$this->smarty->assign('connectToTaxonIds',$t);

						}

					} else {
					
						$this->addError(sprintf(
							_('Uppermost taxon is not a %s, and has a rank that has no immediate parent.'),$pr[0]['rank']));

					}

				}

                $_SESSION['system']['csv_data'] = $r;

                $this->smarty->assign('results',$r);

            }    

        } elseif (isset($this->requestData)) {

            if ($this->rHasVal('rows') && isset($_SESSION['system']['csv_data'])) {

				if ($this->rHasVal('connectToTaxonId')) {
	
					$t = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $this->requestData['connectToTaxonId']
							)
						)
					);
	
					$existingParent = $t[0]['taxon'];
	
				} else {

					$existingParent = false;
				
				}
	
                $parenName = false;
                $predecessors = null;

                foreach((array)$this->requestData['rows'] as $key => $val) {

                    $name = $_SESSION['system']['csv_data'][$val][0];
                    $rank = $_SESSION['system']['csv_data'][$val][1];
                    $hybrid = $_SESSION['project']['includes_hybrids'] ? $_SESSION['system']['csv_data'][$val][2] : false;
                    $parentName = null;

                    if ($key==0) {
                    // first one never has a parent (top of the tree) unless actively set or chosen

                        $predecessors[] = array($rank, $name, $hybrid);
						
						if ($existingParent) $parentName = $existingParent;

                    } else {

                        if ($rank==$predecessors[count((array)$predecessors)-1][0]) {
                        /* if this taxon has the same rank as the previous one, they must have the same
                            parent, so we go back in the list until we find the first different rank,
                            which must be the parent */
                            
                            $j=1;
                            $prevRank = $rank;
                            while($rank==$prevRank) {
                            
								if (!isset($predecessors[count((array)$predecessors)-$j][0])) {

									if ($existingParent) $parentName = $existingParent;
									break;

								} else {

									$prevRank = $predecessors[count((array)$predecessors)-$j][0];
									$parentName = $predecessors[count((array)$predecessors)-$j][1];

								}

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

        if (!$this->rHasVal('action')) return;
        
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

			$d = $this->getTaxonById();
            $this->getRankByParent($d['rank_id']);

        } else if ($this->requestData['action'] == 'save_section_title') {

            $this->ajaxActionSaveSectionTitle();
        
        } else if ($this->requestData['action'] == 'delete_section_title') {

            $this->ajaxActionDeleteSectionTitle();
        
        } else if ($this->requestData['action'] == 'get_section_titles') {

            $this->ajaxActionGetSectionLabels();
        
        } else if ($this->requestData['action'] == 'get_language_labels') {

            $this->ajaxActionGetLanguageLabels();

        } else if ($this->requestData['action'] == 'save_language_label') {

            $this->ajaxActionSaveLanguageLabel();

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

		$pr = $this->models->ProjectRank->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'order' => 'parent_id',
				'fieldAsIndex' => 'rank_id'
			)
		);

		if ($this->rHasVal('ranks') && !$this->isFormResubmit()) {

			$parent = 'null';
			
			$isLowerTaxon = false;

			foreach((array)$this->requestData['ranks'] as $key => $rank) {

				if ($this->requestData['higherTaxaBorder']==$rank) {

					$isLowerTaxon = true;

				}

				if (!empty($pr[$rank])) {

					$this->models->ProjectRank->save(
						array(
							'id' => $pr[$rank]['id'],
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? 1 : 0
						)
					);
					
					$parent = $pr[$rank]['id'];

				} else {

					$this->models->ProjectRank->save(
						array(
		                    'id' => null, 
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank,
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? 1 : 0
						)
					);
					
					$parent = $this->models->ProjectRank->getNewId();

				}

			}

			$this->models->ProjectRank->update(
				array('keypath_endpoint' => 0),
				array('project_id' => $this->getCurrentProjectId(),'lower_taxon' => 0)
			);

			
			foreach((array)$pr as $key => $rank) {

				if(!in_array($rank['rank_id'],$this->requestData['ranks'])) {

					$pr = $this->models->ProjectRank->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'rank_id' => $rank['rank_id']
							)
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

			if (isset($_SESSION['project']['ranklist'])) unset($_SESSION['project']['ranklist']);
			
			$this->addMessage(_('Ranks saved.'));

		}

		$r = 
			array_merge(
				$this->models->Rank->_get(
					array(
						'id' => array('parent_id !=' => -1),
						'order' => 'parent_id',
						'fieldAsIndex' => 'id'
					)
				),
				$this->models->Rank->_get(
					array(
						'id' => array('parent_id' => -1),
						'order' => 'parent_id',
						'fieldAsIndex' => 'id'
					)
				)
			);

		$pr = $this->getProjectRanks(array('forceLookup'=>true));

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

		$pr = $this->getProjectRanks(array('includeLanguageLabels'=>true));

		$this->smarty->assign('projectRanks',$pr);

		$this->smarty->assign('languages',$_SESSION['project']['languages']);

        $this->printPage();

	}
	

    /**
     * Create standard sections for taxon content pages
     *
     * @access    public
     */
	public function sectionsAction()
	{

        $this->checkAuthorisation();
        
        $this->setPageName(_('Define sections'));

		if ($this->rHasVal('new') && !$this->isFormResubmit()) {

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
        
        $pages = $this->models->PageTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => '*',
				'order' => 'show_order'
			)
        );

       foreach((array)$pages as $key => $val) {

			$s = $this->models->Section->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'page_id' => $val['id'],
					),
					'columns' => '*, ifnull(show_order,999) as show_order',
					'order' => 'show_order'
				)
			);
			
			$pages[$key]['sections'] = $s;

        }

        $this->smarty->assign('languages', $lp);
        
        $this->smarty->assign('pages', $pages);
        
        $this->smarty->assign('defaultLanguage', $defaultLanguage);
        
        $this->printPage();

	}	


    /**
     * Assign parts of the taxon tree to specific collaborators so they can edit only those
     *
     * @access    public
     */
	public function collaboratorsAction()
	{

		$this->checkAuthorisation();

        $this->setPageName(_('Assign taxa to collaborators'));

		if (isset($this->requestData) && !$this->isFormResubmit()) {

			if ($this->rHasVal('delete')) {

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
		
		$pru = $this->models->ProjectRoleUser->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'role_id !=' => '1',
					'active' => 1
				)
			)
		);

		foreach ((array) $pru as $key => $val) {

			$u = $this->models->User->_get(array('id' => $val['user_id']));

			$r = $this->models->Role->_get(array('id' => $val['role_id']));
			
			$u['role'] = $r['role'];
			$u['role_id'] = $r['id'];

			$users[] = $u;
		
		}

		$this->getTaxonTree();
	
		if (isset($this->treeList)) {

			$ut = $this->models->UserTaxon->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'order' => 'taxon_id'
				)
			);
	
			foreach((array)$ut as $key => $val) {
	
				$ut[$key]['taxon'] = $this->getTaxonById($val['taxon_id']);
	
			}
	
			$this->smarty->assign('usersTaxa', $ut);
	
			$this->smarty->assign('users', $users);
	
			$this->smarty->assign('taxa',$this->treeList);
		
		} else {
	
			$this->addMessage(_('No taxon have been defined.'));
	
		}

		$this->printPage();

	}	


    /**
     * Create synonyms for a taxon
     *
     * @access    public
     */
	public function synonymsAction()
	{

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

		$this->checkAuthorisation();

		if ($this->rHasId()) {

			$t = $this->getTaxonById();

	        $this->setPageName(sprintf(_('Synonyms')));

			$this->setBreadcrumbIncludeReferer(
				array(
					'name' => _('Taxon list'), 
					'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
				)
			);			

		} else {

			$this->redirect();

		}
		
		if (!$this->isFormResubmit()) {

			if ($this->rHasVal('action','delete')) {
	
				$this->models->Synonym->delete(
					array(
						'id' => $this->requestData['synonym_id'],
						'project_id' => $this->getCurrentProjectId()
					)
				);
	
				$synonyms = $this->models->Synonym->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $this->requestData['id']
						),
						'order' => 'show_order'
					)
				);
				
				foreach((array)$synonyms as $key => $val) {
	
					$this->models->Synonym->save(
						array(
							'id' => $val['id'],
							'project_id' => $this->getCurrentProjectId(),
							'show_order' => $key
						)
					);
					
					$synonyms[$key]['show_order'] = $key;
	
				}
	
			}
			
			if ($this->rHasVal('action','up') || $this->rHasVal('action','down')) {

				$s = $this->models->Synonym->_get(
					array(
						'id' => array(
							'id' => $this->requestData['synonym_id'],
							'project_id' => $this->getCurrentProjectId(),
						)
					)
				);

				$this->models->Synonym->update(
					array('show_order' => $s[0]['show_order']),
					array('project_id' => $this->getCurrentProjectId(),'show_order' =>
						($this->requestData['action']=='up' ? $s[0]['show_order']-1 : $s[0]['show_order']+1))
				);

				$this->models->Synonym->update(
					array('show_order' => ($this->requestData['action']=='up' ? $s[0]['show_order']-1 : $s[0]['show_order']+1)),
					array('id' => $this->requestData['synonym_id'],'project_id' => $this->getCurrentProjectId())
				);
	
			}
	
			if ($this->rHasVal('synonym')) {
	
				$s = $this->models->Synonym->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $this->requestData['id']
						),
						'columns' => 'max(show_order) as next'
					)
				);
				
				$show_order = $s[0]['next']==null ? 0 : ($s[0]['next']+1);
	
				$this->models->Synonym->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->requestData['id'],
						'lit_ref_id' => $this->rHasVal('lit_ref_id') ? $this->requestData['lit_ref_id'] : null,
						'synonym' => $this->requestData['synonym'],
						'remark' => $this->rHasVal('remark') ? $this->requestData['remark'] : null,
						'show_order' => $show_order
					)
				);
	
			}
			
		}
		
		$literature = $this->getAllLiterature();

		if (!isset($synonyms)) {

			$synonyms = $this->models->Synonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->requestData['id']
					),
					'order' => 'show_order'
				)
			);

			foreach((array)$synonyms as $key => $val) {

				if($val['lit_ref_id']) {

					$synonyms[$key]['literature'] = $this->doMultiArrayFind($literature,'id',$val['lit_ref_id']);
					$synonyms[$key]['literature'] = array_shift($synonyms[$key]['literature']);

				}

			}

		}

		$this->smarty->assign('literature', $literature);

		$this->smarty->assign('id',$this->requestData['id']);

		$this->smarty->assign('taxon',$t['taxon']);

		$this->smarty->assign('synonyms',$synonyms);

		$this->printPage();

	}


    /**
     * Create common names for a taxon
     *
     * @access    public
     */
	public function commonAction()
	{

		$this->smarty->assign('isHigherTaxa', $this->maskAsHigherTaxa());

		$this->checkAuthorisation();
		
		if ($this->rHasId()) {

			$t = $this->getTaxonById();

	        $this->setPageName(sprintf(_('Common names')));

			$this->setBreadcrumbIncludeReferer(
				array(
					'name' => _('Taxon list'), 
					'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
				)
			);

		} else {

			$this->redirect();

		}
		
		if (!$this->isFormResubmit()) {

			if ($this->rHasVal('action','delete')) {
	
				$this->models->Commonname->delete(
					array(
						'id' => $this->requestData['commonname_id'],
						'project_id' => $this->getCurrentProjectId()
					)
				);
	
				$commonnames = $this->models->Commonname->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $this->requestData['id']
						),
						'order' => 'show_order'
					)
				);
				
				foreach((array)$commonnames as $key => $val) {
	
					$this->models->Commonname->save(
						array(
							'id' => $val['id'],
							'project_id' => $this->getCurrentProjectId(),
							'show_order' => $key
						)
					);
					
					$commonnames[$key]['show_order'] = $key;
	
				}
	
			}
			
			if ($this->rHasVal('action','up') || $this->rHasVal('action','down')) {

				$s = $this->models->Commonname->_get(
					array(
						'id' => array(
							'id' => $this->requestData['commonname_id'],
							'project_id' => $this->getCurrentProjectId(),
						)
					)
				);

				$this->models->Commonname->update(
					array('show_order' => $s[0]['show_order']),
					array('project_id' => $this->getCurrentProjectId(),'show_order' =>
						($this->requestData['action']=='up' ? $s[0]['show_order']-1 : $s[0]['show_order']+1))
				);

				$this->models->Commonname->update(
					array('show_order' => ($this->requestData['action']=='up' ? $s[0]['show_order']-1 : $s[0]['show_order']+1)),
					array('id' => $this->requestData['commonname_id'],'project_id' => $this->getCurrentProjectId())
				);
	
			}
	
			if ($this->rHasVal('commonname') || $this->rHasVal('transliteration')) {
	
				$s = $this->models->Commonname->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $this->requestData['id']
						),
						'columns' => 'max(show_order) as next'
					)
				);
				
				$show_order = $s[0]['next']==null ? 0 : ($s[0]['next']+1);
	
				$this->models->Commonname->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->requestData['id'],
						'language_id' => $this->requestData['language_id'],
						'commonname' => $this->requestData['commonname'],
						'transliteration' => $this->requestData['transliteration'],
						'show_order' => $show_order
					)
				);
				
				$this->smarty->assign('lastLanguage',$this->requestData['language_id']);
	
			}
			
		}

		// get all languages
		$allLanguages = $this->getAllLanguages();

		// get project languages
		$lp = $_SESSION['project']['languages'];

		if (!isset($commonnames)) {

			$commonnames = $this->models->Commonname->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->requestData['id']
					),
					'order' => 'show_order'
				)
			);
			
		}
			
		if (isset($commonnames)) {

			foreach((array)$commonnames as $key => $val) {

				$commonnames[$key]['language_name'] = $allLanguages[$val['language_id']]['language'];
				
			}

		}

		$this->smarty->assign('id',$this->requestData['id']);

		$this->smarty->assign('taxon',$t['taxon']);

		$this->smarty->assign('commonnames',$commonnames);

		$this->smarty->assign('allLanguages',$allLanguages);

		$this->smarty->assign('languages',$lp);

		$this->printPage();

	}

    private function getCatalogueOfLifeData()
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

            } else {

                $this->smarty->assign('returnText',json_encode($data));
    
            }
        }

    }

    private function createStandardCategories() 
    {    

        $tp = $this->models->PageTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'count(*) as total'
			)
        );
        

        foreach((array)$this->controllerSettings['defaultCategories'] as $key => $page) {

            if ($tp[0]['total']==0) {

                if ($this->createTaxonCategory(_($page['name']), $key, isset($page['default']) && $page['default'])) {

	                $this->createTaxonCategorySections($page['sections'],  $this->models->PageTaxon->getNewId());
	
				}

            } else {

                if (isset($page['mandatory']) && $page['mandatory']===true) {

                    $d = $this->models->PageTaxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'page' => $page['name'], 
							),
							'columns' => 'count(*) as total'
						)
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

		unset($_SESSION['project']['pageCount']);

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
        
        $h = $this->models->Heartbeat->_get(
			array(
				'id' => array(
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
				)
			)
		);
        
        return isset($h) ? true : false;
    
    }

    private function ajaxActionDeletePage ()
    {

		if (!$this->rHasId()) {
			
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
        
        if (!$this->rHasVal('language')) {
            
            return;
        
        } else {
		
			$l = $this->models->Language->_get(
				array(
					'id' => $this->requestData['language'],
					'columns' => 'direction'
				)
			);

			$ptt = $this->models->PageTaxonTitle->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language']
					),
					'columns' => 'id,title,page_id,language_id,\''.$l['direction'].'\' as direction'
				)
			);
                
            $this->smarty->assign('returnText', json_encode($ptt));
        
        }
    
    }


    private function ajaxActionSavePageTitle ()
    {
        
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('label')) {
                
                $this->models->PageTaxonTitle->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'page_id' => $this->requestData['id']
                    )
                );
            
            } else {
                
                $tpt = $this->models->PageTaxonTitle->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'], 
							'page_id' => $this->requestData['id']
						)
					)
				);
                
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
        if (isset($newdata['content']) && ($d['content'] == $newdata['content'])) return;
        
        $d['save_type'] = $mode;
        
        if ($label) $d['save_label'] = $label;
        
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
        if (!$this->rHasId()) {
            
            $d = $this->models->Taxon->save(
            array(
                'id' => $this->rHasId() ? $this->requestData['id'] : null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => $this->rHasVal('name') ? $this->requestData['name'] : '?'
            ));
            
            $taxonId = $this->models->Taxon->getNewId();
        
        } else {
        // existing taxon 
            
            $d = true;
            
            $taxonId = $this->requestData['id'];
        
        }
        
        if ($d) {
        // save of new taxon succeded, or existing taxon
            
            // must have a language
            if ($this->rHasVal('language')) {
                
                // must have a page name
                if ($this->rHasVal('page')) {
                    
                    if ($this->rHasVal('name') && !$this->rHasVal('content')) {
                        
                        if (!$this->rHasVal('save_type'))
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
                        $ct = $this->models->ContentTaxon->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(), 
									'taxon_id' => $taxonId, 
									'language_id' => $this->requestData['language'], 
									'page_id' => $this->requestData['page']
								)
							)
						);
                        
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
				)
			);
            
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

	private function deleteTaxon($id)
	{
	
		if (!$id) return;

		// delete literary references
		$this->models->LiteratureTaxon->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $id
			)
		);

		// reset keychoice end-points
		$this->models->ChoiceKeystep->update(
			array('res_taxon_id' => 'null'),
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'res_taxon_id' => $id
			)
		);

		// delete commonnames
		$this->models->Commonname->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id
			)
		);

		// delete synonyms
		$this->models->Synonym->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id
			)
		);

		// purge undo
		$this->models->ContentTaxonUndo->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id
			)
		);

		// delete taxon tree branch rights
		$this->models->UserTaxon->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id
			)
		);

		// detele media
		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'taxon_id' => $id
				)
			)
		);

		foreach((array)$mt as $key => $val) {

			$this->deleteTaxonMedia($val['id'],false);

		}

		// reset parentage
		$this->models->Taxon->update(
			array('parent_id' => 'null'),
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'parent_id' => $id
			)
		);

		// delete content
		$this->models->ContentTaxon->delete(
			array(
				'taxon_id' => $id, 
				'project_id' => $this->getCurrentProjectId()
			)
		);

		// delete taxon
		$this->models->Taxon->delete(
			array(
				'id' => $id, 
				'project_id' => $this->getCurrentProjectId()
			)
		);

		$this->reOrderTaxonTree();
	
	}

	private function deleteTaxonBranch($id)
	{
	
		if (!$id) return;

		// get entire branch beneath the taxon
		$this->getTaxonTree(array('pId' => $id));

		if (isset($this->treeList)) {

			// delete from the bottom up
			foreach((array)array_reverse($this->treeList) as $treeKey => $val) {
	
				$this->deleteTaxon($val['id']);
	
			}

		}

	}

    private function ajaxActionDeleteTaxon ()
    {
        
        if (!$this->rHasId()) {

            return;
        
        } else {
		
			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'parent_id' => $this->requestData['id']						
					),
					'columns' => 'count(*) as total'
				)
			);

			if ($t[0]['total']!=0) {
			
				$this->smarty->assign('returnText', '<redirect>');
			
			} else {

//				$this->deleteTaxon($this->requestData['id']);

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
			)
		);

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
   
        if (
			!$this->rHasId() || 
			!$this->rHasVal('language') || 
			!$this->rHasVal('page') || 
			!$this->rHasVal('state')) {
            
            $this->smarty->assign('returnText', _('Parameters incomplete.'));
        
        } else {
            
            $ct = $this->models->ContentTaxon->_get(
				array(
					'id' => array(
						'taxon_id' => $this->requestData['id'], 
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['page']
					)
				)
			);

            if (!empty($ct[0])) {
                
                $d = $this->models->ContentTaxon->update(
					array(
						'publish' => $this->requestData['state']
					), 
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'taxon_id' => $this->requestData['id'], 
						'language_id' => $this->requestData['language'], 
						'page_id' => $this->requestData['page']
					)
				);
                
                if ($d) {
                    
                    $this->smarty->assign('returnText', '<ok>');
                
                } else {
                    
                    $this->smarty->assign('returnText', _('Could not save new publish state.'));
                
                }
            
            } else {
                
                $this->smarty->assign('returnText', _('Content not found.'));
            
            }
        
        }
    
    }

    private function ajaxActionGetTaxonUndo ()
    {
        
		//if (!$this->rHasId() || !$this->rHasVal('language') || !$this->rHasVal('page')) {
        if (!$this->rHasId()) {

            return;
        
        } else {
            
            $ctu = $this->models->ContentTaxonUndo->_get(
				array(
					'id' => 
						array(
							'taxon_id' => $this->requestData['id'], 
							'project_id' => $this->getCurrentProjectId()
						),
					'order' => 'content_last_change desc',
					'limit' => 1
					)
				);

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

    private function importTaxon($taxon) 
    {

        if (empty($taxon['taxon_name'])) return;
		
		$rankId = false;

		if (is_numeric($taxon['taxon_rank'])) {

			$rankId = $taxon['taxon_rank'];

		} else {

			$r = $this->models->Rank->_get(array('id' => array('rank' => $taxon['taxon_rank'])));

			if ($r==false) return;

			$pr = $this->models->ProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'rank_id' => $r[0]['id']
					)
				)
			);

			$rankId = $pr[0]['id'];

		}

		if (!$rankId) return;

        $t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => $taxon['taxon_name']
				)
            )
        );
        
        if (count((array)$t[0])==0) {
        // taxon does not exist in database

            if (!empty($taxon['parent_taxon_name'])) {

                // see if the parent taxon already exists
                $p = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => $taxon['parent_taxon_name']
						)
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
                    $p = $this->models->Taxon->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'taxon' => $taxon['parent_taxon_name']
							)
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

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => trim($taxonName),
						'id != ' => $idToIgnore,
					),
					'columns' => 'count(*) as total'
				)
			);

		} else {
		
			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => trim($taxonName)
					),
					'columns' => 'count(*) as total'
				)
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

        if (!$this->rHasVal('taxon_name') || !$this->rHasVal('taxon_id')) return;

        $t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $this->requestData['taxon_id']
	            ),
				'columns' => 'count(*) as total'
			)
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

    private function reOrderTaxonTree() 
    {

        $this->getTaxonTree();
		
		$i = 0;

        foreach((array)$this->treeList as $key => $val) {

            $this->models->Taxon->save(
                array(
                    'id' => $val['id'],
                    'taxon_order' => $i++,
					'list_level' => $val['level']
                )
            );
            
        }

    }

    private function ajaxActionImportTaxa() 
    {

        if (!$this->rHasVal('data')) return;

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
	
        $s = $this->models->Section->_get(
			array(
				'id' =>  array(
					'page_id' => $pageId,
					'project_id' => $this->getCurrentProjectId()
            	),
				'order' => 'show_order asc'
			)
        );
		
        $b = '';

		foreach((array)$s as $key => $val) {
		
			$ls = $this->models->LabelSection->_get(
				array(
					'id' => array(
						'section_id' => $val['id'],
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId
						),
					'columns' => 'label'
				)
			);
			
			if ($ls[0]['label']) $b .= '<p><span class="taxon-section-head">'.$ls[0]['label'].'</span></p><br />'.chr(10);

		}

        return $b;
    
    }

    private function ajaxActionSaveMediaDescription()
    {

        if (!$this->rHasId() || !$this->rHasVal('language')) {

            return;

        } else {
            
            if (!$this->rHasVal('description')) {
                
                $this->models->MediaDescriptionsTaxon->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'media_id' => $this->requestData['id']
                    ));
            
            } else {
                
                $mdt = $this->models->MediaDescriptionsTaxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'], 
							'media_id' => $this->requestData['id']
						)
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

        if (!$this->rHasId() || !$this->rHasVal('language')) {

            return;

        } else {
            
            $mdt = $this->models->MediaDescriptionsTaxon->_get(
				array(
					'id' =>  array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'media_id' => $this->requestData['id']
					)
				)
			);

            $this->smarty->assign('returnText', $mdt[0]['description']);
        
        }

    }

    private function ajaxActionGetMediaDescriptions()
    {

        if (!$this->rHasVal('language')) {

            return;

        } else {
            
            $mt = $this->models->MediaTaxon->_get(
				array(
					'id' =>  array(
						'project_id' => $this->getCurrentProjectId(), 
					),
					'columns' => 'id'
				)
			);

            foreach((array)$mt as $key => $val) {

                $mdt = $this->models->MediaDescriptionsTaxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'],
							'media_id' => $val['id']
						),
						'columns' => 'description'
					)
				);

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

            $mt = $this->models->MediaTaxon->_get(
				array(
					'id' =>  array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $id
					)
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
        
        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('label')) {

                $this->models->LabelProjectRank->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'project_rank_id' => $this->requestData['id']
                    )
                );

            } else {
                
                $lpr = $this->models->LabelProjectRank->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'], 
							'project_rank_id' => $this->requestData['id']
						)
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

        if (!$this->rHasVal('language')) {
            
            return;
        
        } else {
			$l = $this->models->Language->_get(
				array(
					'id' => $this->requestData['language'],
					'columns' => 'direction'
				)
			);

			$lpr = $this->models->LabelProjectRank->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language']
						),
					'columns' => '*, \''.$l['direction'].'\' as direction'
				)
			);
                
            $this->smarty->assign('returnText', json_encode($lpr));
        
        }

	}

	private function getRankByParent($id = false,$output = true)
	{

        if ($id === false) {

            $id = $this->requestData['id'];

        }

        if (empty($id)) {
            
            return;
        
        } else {

			$d = $this->models->ProjectRank->_get(array('id' => array('parent_id' => $id)));
			
			$result = $d[0]['id'] ? $d[0]['id'] : -1;
			
            if ($output) $this->smarty->assign('returnText', $result);

			return $result;
        
        }

	}

	private function canParentHaveChildTaxa($parentId)
	{

		$d = $this->getTaxonById($parentId);

		return ($this->getRankByParent($d['rank_id'],false) != -1);

	}

	private function canRankBeHybrid($projectRankId)
	{

		$d = $this->models->ProjectRank->_get(array('id' => $projectRankId));

		$r = $this->models->Rank->_get(array('id' => $d['rank_id']));

		return ($r['can_hybrid']==1);

	}

	private function ajaxActionSaveSectionTitle()
	{

        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
            
            if (!$this->rHasVal('label')) {
                
                $this->models->LabelSection->delete(
                    array(
                        'project_id' => $this->getCurrentProjectId(), 
                        'language_id' => $this->requestData['language'], 
                        'section_id' => $this->requestData['id']
                    )
                );
            
            } else {
                
                $ls = $this->models->LabelSection->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $this->requestData['language'], 
							'section_id' => $this->requestData['id']
						)
					)
				);
                
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
	
		if (!$this->rHasId()) {
		
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

        if (!$this->rHasVal('language')) {
            
            return;
        
        } else {

			$l = $this->models->Language->_get(
				array(
					'id' => $this->requestData['language'],
					'columns' => 'direction'
				)
			);

			$ls = $this->models->LabelSection->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'],
						),
					'columns' => '*, \''.$l['direction'].'\' as direction'
				)
			);
   
            $this->smarty->assign('returnText', json_encode($ls));
        
        }

	}

	private function ajaxActionGetLanguageLabels()
	{

        if (!$this->rHasVal('language')) {
            
            return;
        
        } else {

			$ll = $this->models->LabelLanguage->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->requestData['language']
					)
				)
			);

            $this->smarty->assign('returnText', json_encode($ll));
        
        }

	}

	private function ajaxActionSaveLanguageLabel()
	{

        if (!$this->rHasId() || !$this->rHasVal('language')) {
            
            return;
        
        } else {
           
			$this->models->LabelLanguage->delete(
				array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->requestData['language'], 
					'label_language_id' => $this->requestData['id'], 
				)
			);

            if ($this->rHasVal('label')) {
                
                $this->models->LabelLanguage->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'language_id' => $this->requestData['language'], 
						'label_language_id' => $this->requestData['id'], 
						'label' => trim($this->requestData['label'])
					)
				);
            
            }
            
            $this->smarty->assign('returnText', 'saved');
        
        }
	
	}


	private function moveIdInTaxonOrder($id,$dir)
	{
	
		if ($dir != 'up' && $dir != 'down') return;

		$t1 = $this->getTaxonById($id);

		$t2 = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'rank_id' => $t1['rank_id'],
					'taxon_order '.($dir=='up' ? '<' : '>') => $t1['taxon_order']
				),
				'order' => 'taxon_order '.($dir=='up' ? 'desc' : 'asc'),
				'limit' => 1
			)
		);

		if (count((array)$t2)!=0) {

			$this->models->Taxon->update(
				array(
					'taxon_order' => $t2[0]['taxon_order']
				),
				array(
					'id' => $t1['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->models->Taxon->update(
				array(
					'taxon_order' => $t1['taxon_order']
				),
				array(
					'id' => $t2[0]['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);

			$this->reOrderTaxonTree();

		}

	}
	
	private function getTaxonById($id=false)
	{
	
        $id = $id ? $id : (isset($this->requestData['id']) ? $this->requestData['id'] : null);

		$t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $id
				)
			)
		);

		return $t[0];
	
	}

	private function getTaxonPageCount()
	{
	
		if (!isset($_SESSION['project']['pageCount'])) {
		
			$tp = $this->models->PageTaxon->_get(
				array(
					'id'=> array('project_id' => $this->getCurrentProjectId()), 
					'columns' => 'count(*) as total'
				)
			);
		
			$_SESSION['project']['pageCount'] = isset($tp[0]['total']) ? $tp[0]['total'] : 0;
		}
		
		return $_SESSION['project']['pageCount'];

	}

	private function getTaxaSynonymCount()
	{
	
		$s = $this->models->Synonym->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'count(*) as total,taxon_id',
				'group' => 'taxon_id'
			)
		);

		foreach((array)$s as $key => $val) {

			$d[$val['taxon_id']] = $val['total'];

		}
		
		return isset($d) ? $d : 0;
	
	}

	private function getTaxaCommonnameCount()
	{
	
		$c = $this->models->Commonname->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'count(*) as total,taxon_id',
				'group' => 'taxon_id'
			)
		);

		foreach((array)$c as $key => $val) {

			$d[$val['taxon_id']] = $val['total'];

		}
		
		return isset($d) ? $d : 0;

	}

	private function getTaxaMediaCount()
	{

		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'count(*) as total, taxon_id',
				'group' => 'taxon_id'
			)
		);
	
		foreach((array)$mt as $key => $val) {

			$d[$val['taxon_id']] = $val['total'];

		}
		
		return isset($d) ? $d : 0;
	
	}
	

	private function getTaxaContentCount()
	{

		$ct = $this->models->ContentTaxon->_get(
			array(
				'id' =>	array(
					'publish' => 1, 
					'project_id' => $this->getCurrentProjectId()
				), 
				'columns' => 'count(*) as total, taxon_id',
				'group' => 'taxon_id'
			)
		);

		foreach((array)$ct as $key => $val) {

			$d[$val['taxon_id']] = $val['total'];

		}
		
		return isset($d) ? $d : 0;

	}


	private function getTaxaLiteratureCount()
	{

		$lt = $this->models->LiteratureTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'count(*) as total, taxon_id',
				'group' => 'taxon_id'
			)
		);
	
		foreach((array)$lt as $key => $val) {

			$d[$val['taxon_id']] = $val['total'];

		}
		
		return isset($d) ? $d : 0;
	
	}

	private function getTaxonSynonymsById($id=false)
	{
	
		$id = $id ? $id : ($this->rHasId() ? $this->requestData['id'] : false);
		
		if (!$id) return;

		$s = $this->models->Synonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id
				)
			)
		);

		return $s;
	
	}

	private function getTaxonMedia($id)
	{

		return $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id
				),
				'order' => 'mime_type, file_name'
			)
		);

	}

	private function getAllLiterature()
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
			)
		);
	
	}

	private function getTaxonLiterature($id)
	{

		$lt =  $this->models->LiteratureTaxon->_get(
			array(
				'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $id
					)
				)
			);

		foreach((array)$lt as $key => $val) {

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
				)
			);
			
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


	private function filterInternalTags($id)
	{

		if (empty($id)) return;

		if (isset($_SESSION['system']['literature']['newRef']) && $_SESSION['system']['literature']['newRef'] != '<new>') {
		
			$this->models->ContentTaxon->execute(
				'update %table% 
					set content = replace(content,"[new litref]","'.
						mysql_real_escape_string($_SESSION['system']['literature']['newRef']) .'")
					where project_id = '.$this->getCurrentProjectId().'
					and taxon_id = '. $id
			);
		
		}
		
		if (isset($_SESSION['system']['media']['newRef']) && $_SESSION['system']['media']['newRef'] != '<new>') {
		
			$this->models->ContentTaxon->execute(
				'update %table% 
					set content = replace(content,"[new media]","'.
						mysql_real_escape_string($_SESSION['system']['media']['newRef']) .'")
					where project_id = '.$this->getCurrentProjectId().'
					and taxon_id = '. $id
			);
		
		}
		
		$this->models->ContentTaxon->execute(
			'update %table% 
				set content = replace(replace(content,"[new litref]",""),"[new media]","")
				where project_id = '.$this->getCurrentProjectId().'
				and taxon_id = '. $id
		);
		
		unset($_SESSION['system']['literature']['newRef']);
		unset($_SESSION['system']['media']['newRef']);

	}

}