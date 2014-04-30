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
	private $_lookupListMaxResults=99999;
    public $usedModels = array(
        'user', 
        'user_taxon', 
        'role', 
        'project_role_user', 
        'user_taxon', 
        'content_taxon', 
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
        'nbc_extras',
        'occurrence_taxon', 
        'l2_occurrence_taxon', 
		'l2_occurrence_taxon_combi',
        'matrix_taxon', 
        'matrix_taxon_state', 
		'taxon_quick_parentage',
		'names',
		'name_types'
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
		'species-adjacency-tree',
        'list' => 'species-treeList'
    );
    public $cssToLoad = array(
        'prettyPhoto/prettyPhoto.css', 
        'taxon.css', 
        'rank-list.css', 
        'dialog/jquery.modaldialog.css', 
        'lookup.css',
		'../javascript/jqTree/jqtree.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js',
			'taxon_extra.js',
            'prettyPhoto/jquery.prettyPhoto.js', 
            'int-link.js', 
            'dialog/jquery.modaldialog.js', 
            'lookup.js',
            'jqTree/tree.jquery.js', 
        )
    );
    public $controllerPublicName = 'Species module';
    public $includeLocalMenu = false;
	private $_nameTypeIds;


	/* initialise */

    public function __construct ()
    {
        parent::__construct();   
        $this->initialize();
    }


    public function __destruct ()
    {
        parent::__destruct();
    }


    private function initialize ()
    {
        $this->createStandardCategories();
        $this->createStandardCoLRanks();
        $this->verifyProjectRanksRelations();

		$this->setHigherTaxaControllerMask();
		
        $this->smarty->assign('heartbeatFrequency', $this->generalSettings['heartbeatFrequency']);
        $this->smarty->assign('useNBCExtras', $this->_useNBCExtras);
        $this->smarty->assign('useRelated', $this->useRelated);
        $this->smarty->assign('useVariations', $this->useVariations);
        $this->smarty->assign('isHigherTaxa', $this->getIsHigherTaxa());

        $this->includeLocalMenu = true;
        // variations & related are only shown for NBC matrix projects
        $this->_useNBCExtras = $this->useRelated = $this->useVariations = ($this->getSetting('matrixtype')=='nbc');
        $this->_lookupListMaxResults=$this->getSetting('lookup_list_species_max_results',$this->_lookupListMaxResults);

		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));

    }



	/* public */
    public function indexHtAction ()
    {
        $this->setIsHigherTaxa(true);
		$this->doIndex();
    }

    public function indexAction ()
    {
        $this->setIsHigherTaxa(false);
		$this->doIndex();
    }
	
	private function doIndex()
	{
        $this->setActiveTaxonId(null);
        
		$id=$this->getFirstTaxonId();

		if (!$this->userHasTaxon($id))
			$this->redirect('collaborators.php');
		else
			$this->redirect('taxon.php?id='.$id);
	}


    public function taxonAction()
    {

		$this->checkAuthorisation();

		$taxon=$this->getTaxonById($this->rGetVal('id'));

        if (!$taxon)
		{
            $this->addError($this->translate('No or illegal taxon ID'));
		}
		else
        if ($this->doLockOutUser($taxon['id']))
		{
            $this->addError($this->translate('Taxon is already being edited by another editor.'));
		}	
		else
		{
//			$this->setIsHigherTaxa($taxon['lower_taxon']?false:true);
			
			if ($this->rHasVal('action','save_and_preview'))
			{
				$p['id'] = $this->rGetVal('id');
				$p['page'] = $this->rGetVal('activePage');
				$p['language'] = $this->rGetVal('language-default');
				$p['content'] = $this->rGetVal('content-default');

				$this->saveTaxon($p);
				
				if ($this->rHasVal('language-other') && $this->rHasVal('content-other'))
				{
					$p['language'] = $this->rGetVal('language-other');
					$p['content'] = $this->rGetVal('content-other');
					$this->saveTaxon($p);
				}

				$this->previewAction();

			 }

            // replace possible [new litref] and [new media] tags with links to newly created reference of media
            $this->filterInternalTags($this->rGetVal('id'));
//            $taxon=$this->getTaxonById();
         
			$this->setActiveTaxonId($taxon['id']);
			$this->setPageName(sprintf($this->translate('Editing "%s"'),$this->formatTaxon($taxon)));
				
			// determine the language the page will open in
			$projectLanguages=$this->getProjectLanguages();
			$startLanguage = $this->rHasVal('lan') ? $this->requestData['lan'] : $this->getDefaultProjectLanguage();
					
			// get the defined categories (just the page definitions, no content yet)
			$taxonPages = $this->models->PageTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				), 
				'order' => 'show_order'
			));
					
			foreach ((array)$taxonPages as $key => $val)
			{

				foreach ((array) $projectLanguages as $k => $language)
				{
					
					// for each category in each language, get the category title
					$tpt = $this->models->PageTaxonTitle->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'language_id' => $language['language_id'], 
							'page_id' => $val['id']
						)
					));
					
					$taxonPages[$key]['titles'][$language['language_id']] = $tpt[0];
				}
				
				if ($val['def_page'] == 1)
					$defaultPage = $val['id'];
			}
					
			// determine the page_id the page will open in
			$startPage=
				$this->rHasVal('page') ? 
					$this->requestData['page'] : 
					(isset($_SESSION['admin']['system']['lastActivePage']) ? 
						$_SESSION['admin']['system']['lastActivePage'] : 
						$defaultPage
					);
					
			$this->smarty->assign('taxon',$taxon);
			$this->smarty->assign('media',addslashes(json_encode($this->getTaxonMedia($taxon['id']))));
			$this->smarty->assign('literature',addslashes(json_encode($this->getTaxonLiterature($taxon['id']))));
			$this->smarty->assign('pages',$taxonPages);
			$this->smarty->assign('languages',$projectLanguages);
			$this->smarty->assign('includeHtmlEditor',true);
			$this->smarty->assign('activeLanguage',$startLanguage);
			$this->smarty->assign('activePage',$startPage);
			$this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
		}

		$this->printPage();
    }

    public function manageAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Species module overview'));
        
        if (count((array) $this->getProjectLanguages()) == 0)
            $this->addError(sprintf($this->translate('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'), '<a href="../projects/data.php">', '</a>'));
        
        $this->printPage();
    }

    public function parentageAction ()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Generate parentage table'));

        if ($this->rHasVal('action','generate')) {
			$i=$this->saveParentage();
	        $this->smarty->assign('cleared', true);
			$this->addMessage('Generated parentage for '.$i.' taxa');
		}

		$this->printPage();
    }

    public function allSynonymsAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('All synonyms'));

		$s = $this->models->Synonym->freeQuery("        
			select _a.*,_b.taxon
			from %PRE%synonyms _a
			left join %PRE%taxa _b
				on _a.taxon_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			order by taxon,synonym");
				
		$this->smarty->assign('synonyms',$s);
				       
        $this->printPage();
    }

    public function allSynonyms2Action ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('All synonyms'));

		$s = $this->models->Synonym->freeQuery("        
			select _a.*,_b.taxon
			from %PRE%synonyms _a
			left join %PRE%taxa _b
				on _a.taxon_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			order by synonym limit 10");

//		$splitpoint = '<span splitpoint="%s" onmouseover="highlightSplit(this,true)" onmouseout="highlightSplit(this,false)" ondblclick="splitClick(this)" style="cursor:pointer">&nbsp; &nbsp;</span>';
		$splitpoint = '<split p="%s"></split>';

		foreach((array)$s as $key => $val) {
			$str = $val['synonym'];
			$str = preg_replace('/(\s+)/',' ',$str);
			$buffer = '';
			$start = 0;
			if (preg_match_all('/(\s)/',$str,$m,PREG_OFFSET_CAPTURE)!==false) {
				foreach((array)$m[0] as $val) {
					$end=$val[1];
					$buffer.=($start!=0 ? sprintf($splitpoint,$start) : '').trim(substr($str,$start,$end-$start));
					$start=$end;
				}
				$buffer.=sprintf($splitpoint,$start).trim(substr($str,$start));
			}
	
			$s[$key]['splitter']= $buffer;
			
		}

				
		$this->smarty->assign('synonyms',$s);
				       
        $this->printPage();
    }
	
    public function allCommonAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('All common names'));

		$c = $this->models->Commonname->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
			), 
			'order' => 'commonname'
		));
		
		$c = $this->models->Commonname->freeQuery("        
			select _a.*,_b.taxon
			from %PRE%commonnames _a
			left join %PRE%taxa _b
				on _a.taxon_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = ".$this->getCurrentProjectId()."
			order by taxon,commonname");
				
		$this->smarty->assign('commonnames',$c);
				       
        $this->printPage();
    }

	public function sortTaxaAction()
	{

        $this->checkAuthorisation();

		// sorting alphabetically within a rank, and the entire ranks in descending hierarchical order 
        if ($this->rHasVal('sortTaxonLevel','1')  && !$this->isFormResubmit()) {

			$this->newGetTaxonTree();

			if ($this->treeList) {
				uasort($this->treeList,function($a,$b){ return ($a['level'] > $b['level'] ? 1 : ($a['level'] < $b['level'] ? -1 : 0)); });


				$prevlevel=-1;		
				$dummy=$sorted=array();
	
				foreach((array)$this->treeList as $key => $val) {
	
					if ($prevlevel!=$val['level']) {
						
						if (count($dummy)>1)
							uasort($dummy,function($a,$b){ return ($a['taxon'] > $b['taxon'] ? 1 : ($a['taxon'] < $b['taxon'] ? -1 : 0)); });
						
						foreach((array)$dummy as $dKey=>$dVal)
							$sorted[$dKey]=$dVal;
	
						$dummy=array();
					}
	
					$dummy[$key]=$val;
	
					$prevlevel=$val['level'];
				}
	
	
				if (count($dummy)>1)
					uasort($dummy,function($a,$b){ return ($a['taxon'] > $b['taxon'] ? 1 : ($a['taxon'] < $b['taxon'] ? -1 : 0)); });
	
				foreach((array)$dummy as $dKey=>$dVal)
					$sorted[$dKey]=$dVal;

				$i=0;
				foreach((array)$sorted as $key => $val) {
					$this->models->Taxon->update(array(
						'taxon_order' => $i++
					), array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $val['id']
					));
				}

				$this->treeList=$sorted;

			}

        } else

        if ($this->rHasVal('sortAlpha','1') && !$this->isFormResubmit())
		{

			$this->newGetTaxonTree();

			if ($this->treeList)
				uasort($this->treeList,function($a,$b){ return ($a['taxon'] > $b['taxon'] ? 1 : ($a['taxon'] < $b['taxon'] ? -1 : 0)); });

			$i=0;

			foreach((array)$this->treeList as $key => $val) {
				if ($this->rHasVal('sortAll','1') || (($val['lower_taxon']=='1' && $this->rHasVal('taxatype','Sp')) || ($val['lower_taxon']=='0' && $this->rHasVal('taxatype','Ht')))) {
					$this->models->Taxon->update(array(
						'taxon_order' => $i++ + ($this->rHasVal('sortAll','1') ? 0 : 999999)
						//'taxon_order' => $val['id'] // for resetting!
					), array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $val['id']
					));

				}
			}
			
			if (!$this->rHasVal('sortAll','1')) {
			
				$taxa = $this->models->Taxon->_get(array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					), 
					'order' => 'taxon_order',
					'columns' => 'id,taxon_order'
				));
	
				foreach((array)$taxa as $key => $val) {
					$this->models->Taxon->update(array(
						'taxon_order' => $key
					), array(
						'project_id' => $this->getCurrentProjectId(), 
						'id' => $val['id']
					));
				}
				
			}
			
        } else

        if ($this->rHasVal('newOrder') && !$this->isFormResubmit())
		{

			foreach((array)$this->requestData['newOrder'] as $key => $val) {

				$this->models->Taxon->update(array(
					'taxon_order' => $key
				), array(
					'project_id' => $this->getCurrentProjectId(), 
					'id' => $val
				));

			}
	
        }

		$this->clearCache('tree-KeyTree');
		$this->clearCache('species-treeList');

		$this->redirect('list.php');
				
	}

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
        
        $lp = $this->getProjectLanguages();
        
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

    public function editAction ()
    {
if ($_SESSION['admin']['project']['sys_name']=='Nederlands Soortenregister')
	$this->redirect('taxonomy.php?id='.$this->rGetVal('id'));

        $this->checkAuthorisation();

        if (!$this->rHasId())
            $this->redirect('new.php');
        
        if (!$this->userHasTaxon($this->requestData['id']))
            $this->redirect('index.php');
        
        $data = $this->getTaxonById();

        $pr = $this->newGetProjectRanks();

        if (count((array) $pr) == 0) {
            
            $this->addMessage($this->translate('No ranks have been defined.'));
        }

        else 
        if (!$this->doLockOutUser($this->requestData['id'], true)) {

	        if (isset($data))
	            $this->smarty->assign('data', $data);

	        $this->smarty->assign('projectRanks', $pr);

			$this->newGetTaxonTree();

            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;

            // save
            if ($this->rHasId() && $this->rHasVal('taxon') && $this->rHasVal('rank_id') && $this->rHasVal('action', 'save') && !$this->isFormResubmit())
			{
                
                $isHybrid = $this->requestData['is_hybrid'];
                
                if ($this->requestData['id'] == $this->requestData['parent_id'])
                    $parentId = $this->requestData['org_parent_id'];
                else if ($isEmptyTaxaList || $this->requestData['parent_id'] == '-1')
                    $parentId = null;
                else
                    $parentId = $this->requestData['parent_id'];

                $parent = $this->getTaxonById($parentId);
				
                $newName = $this->requestData['taxon'];
                
                $newName = trim(preg_replace('/\s+/', ' ', $newName));
                
                // remove ()'s from subgenus (changed silently)
                $newName = $this->fixSubgenusParentheses($newName, $this->requestData['rank_id']);
                // first letter is capitalized & subgenus parantheses are removed (changed silently)
                $newName = $this->fixNameCasting($newName);
                
                $hasErrorButCanSave = null;
                
                // Test if children have to be renamed; 
                // only applies to genus and below and when name != newName
                $dummy = $this->models->ProjectRank->_get(array(
                    'id' => array(
                        'id' => $this->requestData['rank_id'],
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                if ($dummy[0]['rank_id'] >= GENUS_RANK_ID && $data['taxon'] != $newName)
				{

                    $this->getTaxonTree(array('pId' => $this->requestData['id']));
                    
                    $children = isset($this->treeList) ? $this->treeList : false;
                    
                    $listOfChangedNames = '';
                    
                    if (!empty($children)) {
                        
                        foreach ($children as $child) {
                            
                            $listOfChangedNames .= $child['taxon'] . ' &rarr; ' . 
                                preg_replace('/^('.$data['taxon'].')\b/', $newName, $child['taxon']). '<br>';
                            
                        }
                        
                    }
                    
                    if (!empty($listOfChangedNames)) {
                    
                        $this->addError($this->translate('The following children will be renamed as well:') . 
                            '<br>' . substr($listOfChangedNames, 0, -4));
                        $hasErrorButCanSave = true;
                        
                    }
                
                }
                
                
                
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
				if (is_null($parent)) {
                    $this->addError($this->translate('No parent selected (you can still save).'));
                    $hasErrorButCanSave = true;
                } else
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
                    'parentId' => $parentId,
	                'ignoreId' => $this->requestData['id']
                ));
                if ($dummy === false) {
                    $this->addError(sprintf($this->translate('The name "%s" already exists.'), $newName));
                    $hasErrorButCanSave = false;
                }
                else if ($dummy !== true) {
                    $this->addError($dummy);
                    $hasErrorButCanSave = true;
                }
                

                /* LETHAL */
                if (!is_null($parent) && !$this->canParentHaveChildTaxa($this->requestData['parent_id']) || $isEmptyTaxaList) {
                    $this->addError($this->translate('The selected parent taxon can not have children.'));
                    $hasErrorButCanSave = false;
                }
                else
				if(!is_null($parent)) {
                    
                    if (!$this->doNameAndParentMatch($newName, $parent['taxon'])) {
                        $this->addError(sprintf($this->translate('"%s" cannot be selected as a parent for "%s".'), $parent['taxon'], $newName));
                        $hasErrorButCanSave = false;
                    }
                }
                
                if ($isHybrid != '0' && !$this->canRankBeHybrid($this->requestData['rank_id'])) {
                    $this->addError($this->translate('Rank cannot be hybrid.'));
                    $hasErrorButCanSave = false;
                }
                
                // save as requested
                if (is_null($hasErrorButCanSave) || $this->rHasVal('override', '1')) {
                    
                    $this->clearErrors();
                    
                    $this->clearCache($this->cacheFiles);
					
                    $this->models->Taxon->save(
                    array(
                        'id' => $this->requestData['id'], 
                        'project_id' => $this->getCurrentProjectId(), 
                        'taxon' => $newName, 
                        'author' => ($this->requestData['author'] ? $this->requestData['author'] : null), 
                        'parent_id' => (empty($parentId) ? 'null' : $parentId), 
                        'rank_id' => $this->requestData['rank_id'], 
                        'is_hybrid' => $isHybrid
                    ));
					
					$this->logChange($this->models->Taxon->getDataDelta());
					
					$this->saveParentage($this->requestData['id']);
					
                    if (!empty($children)) {
                        
                        foreach ($children as $child) {
                            
                            $this->models->Taxon->save(
                            array(
                                'id' => $child['id'], 
                                'project_id' => $this->getCurrentProjectId(), 
                                'taxon' => preg_replace('/^('.$data['taxon'].')\b/', $newName, $child['taxon'])
                            ));
							
							$this->logChange($this->models->Taxon->getDataDelta());
                            
                        }
                        
                    }
                    
                    if ($this->rHasVal('next', 'main'))
                        $this->redirect('taxon.php?id=' . $this->requestData['id']);
                    
                    $d = $this->getTaxonById();
                    
                    $this->addMessage(sprintf($this->translate('"%s" saved.'), $this->formatTaxon($d)));
                    
					$data = $this->getTaxonById();
				
                    $this->smarty->assign('data', $d);
					
					$this->clearCache($this->cacheFiles['list']);

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
            

            $this->smarty->assign('allowed', true);

			$this->newGetTaxonTree();

			$this->smarty->assign('taxa', $this->treeList);

            $s = $this->getProjectIdRankByname('Subgenus');
            if ($s)
                $this->smarty->assign('rankIdSubgenus', $s);

        }
        else {

            $this->smarty->assign('taxon', array(
                'id' => -1
            ));

            $this->addError($this->translate('Taxon is already being edited by another editor.'));
        }

        $this->setPageName(sprintf($this->translate('Editing "%s"'), $this->formatTaxon($data)));

        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($data));

        $this->printPage();
    }

    public function newAction ()
    {
if ($_SESSION['admin']['project']['sys_name']=='Nederlands Soortenregister')
	$this->redirect('taxonomy.php');

        $this->checkAuthorisation();
        
        if ($this->getIsHigherTaxa()) {
            
            $this->setPageName($this->translate('New higher taxon'));
        }
        else {
            
            $this->setPageName($this->translate('New taxon'));
        }
        
        $pr = $this->newGetProjectRanks();

        $this->newGetTaxonTree();
        
        if (count((array) $pr) == 0) {
            
            $this->addMessage($this->translate('No ranks have been defined.'));
        }
        else {
            
            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;
            
            // save
            if ($this->rHasVal('taxon') && $this->rHasVal('rank_id') && $this->rHasVal('action', 'save') && !$this->isFormResubmit()) {
                
                $isHybrid = $this->requestData['is_hybrid'];
                
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
                if ($dummy === false) {
                    $this->addError(sprintf($this->translate('The name "%s" already exists.'), $newName));
                    $hasErrorButCanSave = false;
                }
                else if ($dummy !== true) {
                    $this->addError($dummy);
                    $hasErrorButCanSave = true;
                }
                

                /* LETHAL */
                if (!$this->canParentHaveChildTaxa($this->requestData['parent_id']) || $isEmptyTaxaList) {
// causes problems when saving the very first taxon
//                    $this->addError($this->translate('The selected parent taxon can not have children.'));
//                    $hasErrorButCanSave = false;
                }
                else {
                    
                    if (!$this->doNameAndParentMatch($newName, $parent['taxon'])) {
                        $this->addError(sprintf($this->translate('"%s" cannot be selected as a parent for "%s".'), $parent['taxon'], $newName));
                        $hasErrorButCanSave = false;
                    }
                }
                
                if ($isHybrid != '0' && !$this->canRankBeHybrid($this->requestData['rank_id'])) {
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
                        'is_hybrid' => $isHybrid
                    ));
					
					$this->logChange($this->models->Taxon->getDataDelta());
                    
                    $newId = $this->models->Taxon->getNewId();
					
					$this->saveParentage($newId);
                    
                    if (empty($parentId))
                        $this->doAssignUserTaxon($this->getCurrentUserId(), $newId);
                    
                    if ($this->rHasVal('next', 'main'))
                        $this->redirect('taxon.php?id=' . $newId);
                    
                    $this->newGetTaxonTree();
                    
                    $d = $this->getTaxonById($newId);
                    
                    $this->addMessage(sprintf($this->translate('"%s" saved.'), $this->formatTaxon($d)));
                    
                    $this->smarty->assign('data', array('parent_id' => $d['parent_id']));
                
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

    public function deleteAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action', 'process') && $this->rHasId()) {
            
            $this->clearCache($this->cacheFiles);
			
			set_time_limit(600);
            
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
                
                $this->smarty->assign('taxa', isset($this->treeList) ? $this->treeList : null);
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
        
        if (isset($this->treeList)) 
			$this->smarty->assign('tree', $this->treeList);
        
        $this->smarty->assign('ranks', $pr);
        
        if (isset($taxa))
            $this->smarty->assign('taxa', $taxa);
        
        $this->printPage();
    }

    public function literatureAction ()
    {
if ($_SESSION['admin']['project']['sys_name']=='Nederlands Soortenregister')
	$this->redirect('literature2.php?id='.$this->rGetVal('id'));

        $this->checkAuthorisation();
        
        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'), 
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/list.php'
        ));
        
        if ($this->rHasId()) {
            // get existing taxon name

            $taxon = $this->getTaxonById();
            
			$this->setActiveTaxonId($taxon['id']);
            
            if ($this->getIsHigherTaxa()) {
                
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
            
            $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        }
        else {
            
            $this->addError($this->translate('No taxon specified.'));
        }
        
        $this->printPage();
    }

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
            
            $this->helpers->CsvParserHelper->setFieldMax($_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3);
            
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

					array_walk($val,function(&$a){$a = trim($a,chr(239).chr(187).chr(191).chr(9).chr(32).chr(10).chr(13));});
                   
                    // check whether 'has hybrid' is present and legal
                    if ($_SESSION['admin']['project']['includes_hybrids'])
                        $r[$key][2] = (isset($val[2]) && strtolower($val[2]) == 'y' && in_array(strtolower($val[1]), $h));
                    
                    $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3] = 'ok';
                    

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

                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3] = $this->translate('Duplicate name');
                    }
                    else if ($t[0]['total'] != 0) {
                        // set whether the taxon can be imported, based on whether the name already exists

                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3] = $this->translate('Name already exists in the database');
                    }
                    else if (!(isset($val[1]) && in_array(strtolower($val[1]), $d))) {
                        // set whether the taxon can be imported, based on whether it has a legal rank

                        $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3] = $this->translate('Unknown rank');
                    }
                    else {
                        $prevNames[] = $val[0];
                    }
                    
                    if ($upperTaxonRank == false && $r[$key][$_SESSION['admin']['project']['includes_hybrids'] ? 4 : 3] == 'ok')
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
        else if (isset($this->requestData) && !$this->isFormResubmit()) {
            // list of taxa and ranks to be saved detected: save taxa

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
                    $common = 
						$_SESSION['admin']['project']['includes_hybrids'] ? 
							(isset($_SESSION['admin']['system']['csv_data'][$val][3]) ? $_SESSION['admin']['system']['csv_data'][$val][3] : null) : 
							(isset($_SESSION['admin']['system']['csv_data'][$val][2]) ? $_SESSION['admin']['system']['csv_data'][$val][2] : null);

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
					
					if (!empty($common)) {

						$d = $this->models->Commonname->_get(array('id' =>
						array(
							'project_id' => $this->getCurrentProjectId(), 
							'taxon_id' => $newId, 
							'language_id' => $this->getDefaultProjectLanguage(), 
							'commonname' => $common
						)));
						
						if (!$d) {
		
							$this->models->Commonname->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(),
								'taxon_id' => $newId, 
								'language_id' => $this->getDefaultProjectLanguage(), 
								'commonname' => $common
							));
							
							$this->logChange($this->models->Commonname->getDataDelta());
							
						}

					}
                   
                    if (!empty($newId) && empty($taxon['parent_taxon_name'])) {
                        
                        $this->doAssignUserTaxon($this->getCurrentUserId(), $newId);
                    }
                }
				
				$this->saveParentage();
                
                unset($_SESSION['admin']['system']['csv_data']);
                
                $this->addMessage($this->translate('Data saved.'));
            }
        }
        
        $pr = $this->getProjectRanks();
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->printPage();
    }

    public function importAction ()
    {
		
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxon file upload'));
        
        if ($this->requestDataFiles) { // && !$this->isFormResubmit()) {

			$raw = array();

			$saved = $failed = $odd = $skipped = 0;
	
			if (($handle = fopen($this->requestDataFiles[0]["tmp_name"], "r")) !== FALSE) {
				$i = 0;
				while (($dummy = fgetcsv($handle)) !== FALSE) {
					foreach ((array) $dummy as $val) {
						$raw[$i][] = $val;
					}
					$i++;
				}
				fclose($handle);
			}

			$cats = array();
			$clearedTaxa = array();

			foreach ((array) $raw as $key => $line) {
				
				$d = implode('',$line);
				
				if (empty($d))
					continue;
					
				foreach((array)$line as $fKey => $fVal) {
					
					$fVal = trim($fVal,chr(239).chr(187).chr(191));  //BOM!
					
					if (empty($fVal))
						continue;
					
					if ($key==0) {

						$cats[$fKey] = $fVal;
						
					} else {
						
						if ($fKey==0) {

							$tIdOrName = $fVal;

							if (!empty($tIdOrName)) {
								
								if (is_numeric($tIdOrName)) {
								
									$t = $this->models->Taxon->_get(
										array(
											'id' => array(
												'project_id' => $this->getCurrentProjectId(), 
												'id' => (int)$tIdOrName
											)
										));

									if ($t[0]['id']!=$tIdOrName)
										$tId = null;
								
								} else {

									$t = $this->models->Taxon->_get(
										array(
											'id' => array(
												'project_id' => $this->getCurrentProjectId(), 
												'taxon' => trim($tIdOrName)
											)
										));

									if (empty($t[0]['id']))
										$tId = null;
									else
										$tId = $t[0]['id'];

								}


							}

						} else
						if ($fKey==1) {

							$lId = $fVal;

						} else {

							$catId = isset($cats[$fKey]) ? $cats[$fKey] : null;
							
							if (empty($tId) || empty($lId) || empty($catId) || empty($fVal)) {
								
								if ((empty($tId) || empty($lId)) && $fKey==2)
									$this->addError(sprintf('Could not resolve taxon "%s" and/or language ID "%s".',$tIdOrName,$lId));
								$skipped++;
								continue;
							}

							if($this->rHasVal('del_all','1') && !isset($clearedTaxa[$tId][$lId])) {

								$this->models->ContentTaxon->delete(array(
									'project_id' => $this->getCurrentProjectId(), 
									'taxon_id' => $tId, 
									'language_id' => $lId,
								));
								
								$clearedTaxa[$tId][$lId] = true;
								
							} else
							if(!$this->rHasVal('del_all','1')) {
								
								$this->models->ContentTaxon->delete(array(
									'project_id' => $this->getCurrentProjectId(), 
									'taxon_id' => $tId, 
									'language_id' => $lId,
									'page_id' => $catId
								));
								
								$this->logChange($this->models->ContentTaxon->getDataDelta());

							}
	
							$d = $this->models->ContentTaxon->save(                        
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'taxon_id' => $tId, 
								'language_id' => $lId,
								'page_id' => $catId,
								'content' => $fVal, 
								'title' => '', 
								'publish' => 1
							));
							
							$this->logChange($this->models->ContentTaxon->getDataDelta());

							if ($d) {

								$argh = $this->models->ContentTaxon->_get(
								array(
									'id' => array(
										'id' => $this->models->ContentTaxon->getNewId(),
										'project_id' => $this->getCurrentProjectId(), 
									),
									'columns' => 'length(content) as l'
								));
								
								if (intval($argh[0]['l']) != strlen($fVal)) {
									$odd++;
									$this->addMessage(sprintf('mismatched content size for %s (%s)',$tIdOrName,$this->models->ContentTaxon->getNewId()));
								}
	
								$saved++;
							} else
								$failed++;
	
						}
						
					}
					
				}
				
				if ($key==0) {

					foreach((array)$cats as $cKey => $cVal) {
						
						if ($cKey>1) {
							
							$tp = $this->models->PageTaxon->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(),
									'page' => $cVal
								)
							));
							
							if ($tp)
								$cats[$cKey] = $tp[0]['id'];
							else
								$cats[$cKey] = null;
							
						}
						
					}

				} 

            }
			
			$this->addMessage(sprintf('Saved %s pages, skipped %s, failed %s.',$saved,$skipped,$failed));

			if ($skipped)
				$this->addMessage('Skipped pages are due to either missing or incorrect taxon id, or non-existent category.');
			if ($failed)
				$this->addMessage('Failed pages are due to botched inserts.');
			if ($odd>0)
				$this->addError(sprintf('%s inserted pages have different lengths than the data in your file. This might be due to an an encoding problem, please check and reload.',$odd));

        }
       
        $this->smarty->assign('categories', $this->getCategories());
        
        $this->printPage();
    }

    public function remoteImgFileAction ()
    {
		
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Remote image file file upload'));
        
        if ($this->requestDataFiles && !$this->isFormResubmit()) {
			
			set_time_limit(2400);

			$raw = array();

			$saved = $failed = $odd = $skipped = 0;
	
			if (($handle = fopen($this->requestDataFiles[0]["tmp_name"], "r")) !== FALSE) {
				$i = 0;
				while (($dummy = fgetcsv($handle)) !== FALSE) {
					foreach ((array) $dummy as $val) {
						$raw[$i][] = $val;
					}
					$i++;
				}
				fclose($handle);
			}

			$clearedTaxa = array();

			foreach ((array) $raw as $key => $line) {
				
				$d = implode('',$line);
				
				if (empty($d))
					continue;
					
				foreach((array)$line as $fKey => $fVal) {
					
					$fVal = trim($fVal,chr(239).chr(187).chr(191));  //BOM!
					
					if (empty($fVal))
						continue;
						
					if ($fKey==0) {

						$tIdOrName=$fVal;
						$tId = $this->resolveTaxonByIdOrname($tIdOrName);

					} else {

						if (empty($tId)) {
							
							if (empty($tId))
								$this->addError(sprintf('Could not resolve taxon "%s".',$tIdOrName));
							$skipped++;
							continue;
						}

						if($this->rHasVal('del_existing','1') && !isset($clearedTaxa[$tId])) {

							$this->models->MediaTaxon->delete(array(
								'project_id' => $this->getCurrentProjectId(), 
								'taxon_id' => $tId,
								'file_name like' => 'http://%'
							));

							$this->models->MediaTaxon->delete(array(
								'project_id' => $this->getCurrentProjectId(), 
								'taxon_id' => $tId,
								'file_name like' => 'https://%'
							));
							
							$clearedTaxa[$tId] = true;
							
						}
						
						$images=array_map('trim',explode(';',$fVal));

						foreach((array)$images as $iKey => $iVal) {
							
							if (empty($iVal)) continue;
							
							$mime=null;
							
							/*
							$headers = get_headers($iVal);
							
							if ($headers!==false) {
								foreach((array)$headers as $hVal) {
									if (stripos($hVal,'Content-Type:')!==false)
										$mime=trim(str_ireplace('Content-Type:','',$hVal));
								}
							}
							
							*/

							if ($mime==null) {
								$mimes=array('jpg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','bmp'=>'image/bmp');
								$d=pathinfo($iVal);
								$mime=isset($mimes[strtolower($d['extension'])]) ? $mimes[strtolower($d['extension'])] : '?';
								//$odd++;
							}


							$mt = $this->models->MediaTaxon->save(
							array(
								'id' => null, 
								'project_id' => $this->getCurrentProjectId(), 
								'taxon_id' => $tId, 
								'file_name' => $iVal, 
								'original_name' => $iVal, 
								'mime_type' => $mime, 
								'file_size' => 0, 
								'thumb_name' => null, 
								'sort_order' => $this->getNextMediaSortOrder($tId)
							));
							
							$this->logChange($this->models->MediaTaxon->getDataDelta());
							
							if ($mt)
								$saved++;
							else
								$failed++;
							
						}
						
					}
					
				}
				
            }
			
			$this->addMessage(sprintf('Saved %s images, skipped %s line, failed %s image.',$saved,$skipped,$failed));

			if ($skipped)
				$this->addMessage('Skipped lines are due to missing or incorrect taxon id.');
			if ($failed)
				$this->addMessage('Failed pages are due to botched inserts.');
			if ($odd>0)
				$this->addError(sprintf('%s images could not be resolved, but were saved anyway.',$odd));

        }
       
        $this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;
        
        if ($this->requestData['action'] == 'save_taxon') {
            
            $c = $this->saveTaxon($this->requestData);
            
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
        else if ($this->requestData['action'] == 'get_col') {
            
            $this->getCatalogueOfLifeData();
        }
        else if ($this->requestData['action'] == 'save_col') {
            
            $this->clearCache($this->cacheFiles);
            
            $this->ajaxActionImportTaxa();
        }
        else if ($this->requestData['action'] == 'save_taxon_name') {
            
            $this->clearCache($this->cacheFiles['list']);
            
            $this->ajaxActionSaveTaxonName();
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
            $this->formatTaxon(
            array(
                'taxon' => $this->requestData['name'], 
                'rank_id' => $this->requestData['rank_id'], 
                'parent_id' => $this->requestData['parent_id'], 
                'is_hybrid' => $this->requestData['is_hybrid']
            )));
        }
        else if ($this->requestData['action'] == 'delete_variation') {
            
            $this->deleteVariation($this->requestData['id']);
        }
        else if ($this->rHasVal('action', 'get_lookup_list') && !empty($this->requestData['search'])) {
            
            $list=$this->getLookupList($this->requestData);
			$this->smarty->assign('returnText',$list);         

        }
        else if ($this->rHasVal('action', 'delete_synonym') && !empty($this->requestData['id'])) {

			$d = $this->models->Synonym->delete(array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $this->requestData['id']
			)); 
			$this->smarty->assign('returnText', $d ? '<ok>' : 'error' );         

        }
        else if ($this->rHasVal('action', 'delete_common') && !empty($this->requestData['id'])) {

			$d = $this->models->Commonname->delete(array(
				'project_id' => $this->getCurrentProjectId(), 
				'id' => $this->requestData['id']
			)); 
			$this->smarty->assign('returnText', $d ? '<ok>' : 'error' );         

        }
		 else if ($this->rHasVal('action', 'save_synonym_data') && !empty($this->requestData['id']) && !empty($this->requestData['val']) && !empty($this->requestData['col'])) {
            
            $this->clearCache($this->cacheFiles['list']);
			
			if ($this->requestData['col']=='s')
				$what = array('synonym' => trim($this->requestData['val']));
			elseif ($this->requestData['col']=='a')
				$what = array('author' => trim($this->requestData['val']));
			else
				$what = null;
		
			if (isset($what))
			{
				$d = $this->models->Synonym->update(
					$what, 
					array(
					'project_id' => $this->getCurrentProjectId(), 
					'id' => $this->requestData['id']
				));
				$this->logChange($this->models->Synonym->getDataDelta());
				$this->smarty->assign('returnText', $d ? '<ok>' : 'error' );         
			}

        }
        
        $this->printPage();
    }

    public function colAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Import from Catalogue Of Life'));
        
        $this->printPage();
    }

    public function ranksAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxonomic ranks'));
        
        if ($this->rHasVal('ranks') && !$this->isFormResubmit()) {
            
			$pr = $this->newGetProjectRanks();
			
            $parent = 'null';
            
            $isLowerTaxon = false;

            foreach ((array) $this->requestData['ranks'] as $key => $rank) {
                
                if ($this->requestData['higherTaxaBorder'] == $rank) {
                    
                    $isLowerTaxon = true;

                }

                $d = $this->models->ProjectRank->_get(array(
                    'id' => array(
                        'rank_id' => $rank,
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                if ($d) {

                    $this->models->ProjectRank->save(
						array(
							'id' => $d[0]['id'], 
							'parent_id' => $parent, 
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));
						
                    $parent = $d[0]['id'];
					
                }
                else {
                    
					$this->models->ProjectRank->save(
						array(
							'id' => null, 
							'project_id' => $this->getCurrentProjectId(), 
							'rank_id' => $rank, 
							'parent_id' => $parent, 
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));
					
					$this->logChange($this->models->ProjectRank->getDataDelta());
                    
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
        
        $pr = $this->newGetProjectRanks(array(
            'forceLookup' => true
        ));
        
        $this->smarty->assign('ranks', $r);
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->printPage();
    }

    public function ranklabelsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Taxonomic ranks: labels'));
        
        $pr = $this->getProjectRanks(array(
            'includeLanguageLabels' => true
        ));
        
        $this->smarty->assign('projectRanks', $pr);
        
        $this->smarty->assign('languages', $this->getProjectLanguages());
        
        $this->printPage();
    }

    public function sectionsAction ()
    {
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Define sections'));
        
        if ($this->rHasVal('new') && !$this->isFormResubmit()) {

            foreach ((array) $this->requestData['new'] as $key => $val) {
				
				if (empty($val)) continue;
                
				$d = $this->models->Section->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(), 
						'page_id' => $key
					), 
					'columns' => 'max(show_order) as max_show_order', 
				));

				$d = $d ? $d[0]['max_show_order']+1 : 0;
							
                $this->models->Section->save(
                array(
                    'id' => null, 
                    'project_id' => $this->getCurrentProjectId(), 
                    'page_id' => $key, 
                    'section' => $val,
                    'show_order' => $d
                ));
				
				$this->logChange($this->models->Section->getDataDelta());
            }
        }
      
        $lp = $this->getProjectLanguages();
        
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

    public function synonymsAction ()
    {
		
if ($_SESSION['admin']['project']['sys_name']=='Nederlands Soortenregister')
	$this->redirect('names.php?id='.$this->rGetVal('id'));
		
        $this->checkAuthorisation();
        
        if ($this->rHasId()) {
            
            $taxon = $this->getTaxonById();
            
            if ($this->getIsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Synonyms for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Synonyms for "%s"'), $taxon['taxon']));
            }
            
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
					
					$this->logChange($this->models->Synonym->getDataDelta());
                    
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
				
				$this->logChange($this->models->Synonym->getDataDelta());
                
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
        
        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        
        //		$this->smarty->assign('literature', $literature);
        



        $this->smarty->assign('id', $this->requestData['id']);
        
        $this->smarty->assign('taxon', $taxon);
        
        $this->smarty->assign('synonyms', $synonyms);
        
        $this->printPage();
    }

    public function commonAction ()
    {

if ($_SESSION['admin']['project']['sys_name']=='Nederlands Soortenregister')
	$this->redirect('names.php?id='.$this->rGetVal('id'));
		
        $this->checkAuthorisation();
        
        if ($this->rHasId()) {
            
            $taxon = $this->getTaxonById();
            
            if ($this->getIsHigherTaxa()) {
                
                $ranks = $this->getProjectRanks(array(
                    'includeLanguageLabels' => true, 
                    'idsAsIndex' => true
                ));
                
                $this->setPageName(sprintf($this->translate('Common names for %s "%s"'), strtolower($ranks[$taxon['rank_id']]['rank']), $taxon['taxon']));
            }
            else {
                
                $this->setPageName(sprintf($this->translate('Common names for "%s"'), $taxon['taxon']));
            }
            
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
					
					$this->logChange($this->models->Commonname->getDataDelta());
                    
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
				
				$this->logChange($this->models->Commonname->getDataDelta());
                
                $this->smarty->assign('lastLanguage', $this->requestData['language_id']);
            }
        }
        
        // get all languages
        $allLanguages = $this->getAllLanguages();
        
        // get project languages
        $lp = $this->getProjectLanguages();
        
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
        
        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        
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
			
			$this->logChange($this->models->TaxonVariation->getDataDelta());
            
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

				$this->logChange($this->models->VariationLabel->getDataDelta());
            }
        }
        
        $variations = $this->getVariations($taxon['id']);
        
        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        
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
        
        $taxon = $this->getTaxonById($this->requestData['id']);
        
        if ($this->useVariations)
            $this->setPageName(sprintf($this->translate('Related taxa and variations for "%s"'), $taxon['taxon']));
        else
            $this->setPageName(sprintf($this->translate('Related taxa for "%s"'), $taxon['taxon']));
        
        $related = $this->getRelatedEntities($taxon['id']);
        
        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        
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
        
        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));
        
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

	private function getProgeny($parent,$level,$family)
	{
		$result = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'parent_id' => $parent
				), 
				'columns' => 'id,parent_id,taxon,'.$level.' as level'
			)
		);
		
		$family[]=$parent;

		foreach((array)$result as $row)
		{
			$row['parentage']=$family;
			$this->tmp[]=$row;
			$this->getProgeny($row['id'],$level+1,$family);
		}
	}

	private function saveParentage($id=null)
	{

		$t = $this->models->Taxon->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'parent_id is' => null
			), 
			'columns' => 'id'
		));
		
		if (empty($t[0]['id']))
			die('no top!?');

		if (count((array)$t)>1)
			die('multiple tops!?');
			
		$this->tmp=array();
	
		$this->getProgeny($t[0]['id'],0,array());

		$d=array('project_id' => $this->getCurrentProjectId());

		if (!is_null($id)) $d['taxon_id']=$id;
	
		$this->models->TaxonQuickParentage->delete($d);
	
		$i=0;
		foreach((array)$this->tmp as $key=>$val)
		{

			if (!is_null($id) && $val['id']!=$id)
				continue;

			$this->models->TaxonQuickParentage->save(
			array(
				'id' => null, 
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $val['id'],
				'parentage' => implode(' ',$val['parentage'])

			));

			$i++;
		}
		
		return $i;

	}

	private function getTreeRoots()
	{
		
		$q='
			select _a.id,_a.taxon,_a.parent_id,_a.rank_id,_a.taxon_order,_a.is_hybrid,_a.list_level,_b.rank_id as base_level
			from %staxa _a
			left join %sprojects_ranks _b on _a.rank_id = _b.id and _a.project_id =_b.project_id
			where _a.project_id = '.$this->getCurrentProjectId().'
			and _a.parent_id %s
			order by _a.taxon_order,base_level,_a.taxon
			limit %s
		';
		
		$list=array();

		$d0=$this->models->Taxon->freeQuery(sprintf($q,'%PRE%','%PRE%','is null',1));
		$d0[0]['list_level']=0;
		$list[]=$d0[0];
		foreach((array)$d0 as $val0) {
			$d1=$this->models->Taxon->freeQuery(sprintf($q,'%PRE%','%PRE%','='.$val0['id'],1000));
			foreach((array)$d1 as $val1) {
				$val1['list_level']=1;
				$list[]=$val1;
				$d2=$this->models->Taxon->freeQuery(sprintf($q,'%PRE%','%PRE%','='.$val1['id'],1000));
				foreach((array)$d2 as $val2) {
					$val2['list_level']=2;
					$list[]=$val2;
				}
			}
		}
		
		return $list;
		
	}

    private function setIsHigherTaxa($state=true)
	{
		if (!is_bool($state)) return;
		$_SESSION['admin']['system']['highertaxa']=$state;
	}

    private function getIsHigherTaxa()
    {
        if (!isset($_SESSION['admin']['system']['highertaxa'])) return false;
		return $_SESSION['admin']['system']['highertaxa'];
    }
	
	private function setHigherTaxaControllerMask()
	{
		if ($this->getIsHigherTaxa())
			$this->setControllerMask('highertaxa', 'Higher taxa');
	}

    private function getFirstTaxonId()
    {
        $t = $this->models->Taxon->freeQuery(
        array(
			'query' => '
				select _a.id
				from %PRE%taxa _a 
				left join %PRE%projects_ranks _b on _a.rank_id=_b.id
				left join %PRE%ranks _c on _b.rank_id=_c.id
				where _a.project_id = '.$this->getCurrentProjectId().'
				and _b.lower_taxon = '.($this->getIsHigherTaxa() ? 0 : 1).'
				order by _a.taxon_order, _a.taxon
				limit 1'
        ));

		return isset($t) ? $t[0]['id'] : null;
    }

	private function resolveTaxonByIdOrname($whatisit)
	{
		
		$tId=null;
		
		
		if (!empty($whatisit)) {

			if (is_numeric($whatisit)) {
			
				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'id' => (int)$whatisit
						)
					));
		
				if ($t[0]['id']!=$whatisit)
					$tId = null;
			
			} else {
		
				$t = $this->models->Taxon->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'taxon' => trim($whatisit)
						)
					));
		
				if (empty($t[0]['id']))
					$tId = null;
				else
					$tId = $t[0]['id'];
		
			}
			
		}
		
		return $tId;
										
	}

    private function getRankList ()
    {
        if (isset($_SESSION['admin']['project']['ranklist']) && (isset($_SESSION['admin']['project']['ranklistsource']) && $_SESSION['admin']['project']['ranklistsource'] == ($this->getIsHigherTaxa() ? 'highertaxa' : 'lowertaxa'))) {
            
            $rl = $_SESSION['admin']['project']['ranklist'];
        }
        else {
            
            $pr = $this->getProjectRanks();
            
            foreach ((array) $pr as $key => $val) {
                
                if (!$this->getIsHigherTaxa() && $val['lower_taxon'] == 1) {
                    // only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
                    



                    $rl[$val['id']] = $val['rank'];
                }
                else if ($this->getIsHigherTaxa() && $val['lower_taxon'] != 1) {
                    // only include taxa that are set to be 'lower_taxon', the rest is in the 'higher taxa' module
                    



                    $rl[$val['id']] = $val['rank'];
                }
            }
            
            if (isset($rl))
                $_SESSION['admin']['project']['ranklist'] = $rl;
            $_SESSION['admin']['project']['ranklistsource'] = ($this->getIsHigherTaxa() ? 'highertaxa' : 'lowertaxa');
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
        $d=$this->models->PageTaxon->save(
        array(
            'id' => null, 
            'page' => $name, 
            'show_order' => $show_order !== false ? $show_order : 0, 
            'project_id' => $this->getCurrentProjectId(), 
            'def_page' => $isDefault ? '1' : '0'
        ));
		$this->logChange($this->models->PageTaxon->getDataDelta());
		return $d;
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
			
			$this->logChange($this->models->Section->getDataDelta());
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
        
    }

    private function saveTaxon($p=null)
    {

		$id = isset($p['id']) ? $p['id'] : null;
		$name = isset($p['name']) ? $p['name'] : null;
		$language = isset($p['language']) ? $p['language'] : null;
		$page = isset($p['page']) ? $p['page'] : null;
		$content = isset($p['content']) ? $p['content'] : null;
		$save_type = isset($p['save_type']) ? $p['save_type'] : 'auto';

        // new taxon
        if (empty($id))
		{
            $d = $this->models->Taxon->save(
            array(
                'id' => null, 
                'project_id' => $this->getCurrentProjectId(), 
                'taxon' => !empty($name) ? $name : '?'
            ));
            
			$this->logChange($this->models->Taxon->getDataDelta());
            $taxonId = $this->models->Taxon->getNewId();
            $new = true;
			$this->saveParentage($taxonId);
        }
        else
		{
            // existing taxon 
            $d = true;
            $taxonId = $id;
            $new = false;
        }
        
        if ($d) {
            // save of new taxon succeeded, or existing taxon

            // must have a language
            if (!empty($language))
			{
                
                // must have a page name
                if (!empty($page))
				{
                    
                    if (empty($name) && empty($content))
					{
                        
                        // no page title and no content equals an empty page: delete
                        $ct = $this->models->ContentTaxon->delete(
                        array(
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $language, 
                            'page_id' => $page
                        ));
                        
                        // Mark taxon as 'empty'
                        $this->models->Taxon->update(array(
                            'is_empty' => 1
                        ), array(
                            'id' => $taxonId
                        ));
                    }
                    else
					{
                        
                        // see if such content already exists
                        $ct = $this->models->ContentTaxon->_get(
                        array(
                            'id' => array(
                                'project_id' => $this->getCurrentProjectId(), 
                                'taxon_id' => $taxonId, 
                                'language_id' => $language, 
                                'page_id' => $page
                            )
                        ));
                        
                        $oldId = count((array) $ct) != 0 ? $ct[0]['id'] : null;
                        
                        $filteredContent = $this->filterContent($content);
                        
                        $newdata = array(
                            'id' => $oldId, 
                            'project_id' => $this->getCurrentProjectId(), 
                            'taxon_id' => $taxonId, 
                            'language_id' => $language, 
                            'content' => !empty($filteredContent['content']) ? $filteredContent['content'] : '', 
                            'title' => !empty($name) ? $name : '', 
                            'page_id' => $page
                        );
                        
                        // save content
                        $d = $this->models->ContentTaxon->save($newdata);
						
						$this->logChange($this->models->ContentTaxon->getDataDelta());
                        
                        // Mark taxon as 'empty/not empty' depending on presence of contents
                        $this->models->Taxon->update(array(
                            'is_empty' => empty($content) ? 1 : 0
                        ), array(
                            'id' => $taxonId
                        ));
						
						$this->logChange($this->models->Taxon->getDataDelta());

                    }
                    
                    if ($d) {

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
        
        // return if taxon has content in any language
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

    private function deleteTaxonBranch ($id)
    {
        if (!$id)
            return;
            
        // get entire branch beneath the taxon
        $this->newGetTaxonTree(array(
            'pId' => $id
        ));

        if (isset($this->treeList)) {
            
            // delete from the bottom up
            foreach ((array) array_reverse($this->treeList) as $treeKey => $val) {

                $this->deleteTaxon($val['id']);
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
                'is_hybrid' => isset($taxon['hybrid']) && $taxon['hybrid']===true && $this->canRankBeHybrid($rankId) ? 1 : 0
            ));
			
			$this->logChange($this->models->Taxon->getDataDelta());
            
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
                    'is_hybrid' => isset($taxon['hybrid']) && $taxon['hybrid']===true && $this->canRankBeHybrid($rankId) ? 1 : 0
                ));
				
				$this->logChange($this->models->Taxon->getDataDelta());
                


                return $t[0]['id'];
            }
        }
    }

    private function newIsTaxonNameUnique ($p)
    {
        $name = isset($p['name']) ? $p['name'] : null;
        $rankId = isset($p['rankId']) ? $p['rankId'] : null;
        $parentId = isset($p['parentId']) ? $p['parentId'] : null;
        $ignoreId = isset($p['ignoreId']) ? $p['ignoreId'] : null;
        
        if (empty($name))
            return;
        
        $d = array(
            'project_id' => $this->getCurrentProjectId(), 
            'taxon' => $name
        );
        
        if (!empty($ignoreId))
            $d['id !='] = $ignoreId;
        
        $t = $this->models->Taxon->_get(array(
            'id' => $d
        ));
        
        if (empty($t))
            return true;
        
        if ($t[0]['parent_id'] != $parentId)
            return $this->translate('That name already exists, albeit with a different parent.');
        else
            return false;
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
				
				$this->logChange($this->models->PageTaxonTitle->getDataDelta());
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
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
				
				$this->logChange($this->models->ContentTaxon->getDataDelta());
                
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

			$this->logChange($this->models->Taxon->getDataDelta());
            
            if ($d)
                $this->smarty->assign('returnText', '<ok>');
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
				
				$this->logChange($this->models->LabelProjectRank->getDataDelta());
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
				
				$this->logChange($this->models->LabelSection->getDataDelta());
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
				
				$this->logChange($this->models->LabelLanguage->getDataDelta());
            }
            
            $this->smarty->assign('returnText', 'saved');
        }
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
            
            if ($val['mime_type']) $d[$key]['media_type'] = $mimes[$val['mime_type']];
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
								) as author_full,
							concat(
								if(isnull(`year`)!=1,`year`,\'\'),
								if(isnull(suffix)!=1,suffix,\'\'),
								if(isnull(year_2)!=1,
									concat(
										if(year_separator!=\'-\',
											concat(
												\' \',
												year_separator,
												\' \'
											),
											year_separator
										),
										year_2,
										if(isnull(suffix_2)!=1,
											suffix_2,
											\'\')
										)
										,\'\'
									)
							) as year_full'
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

    private function getCategories ($taxon = null, $languageId = null)
    {
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
		
		return array(
			'categories' => $tp, 
			'defaultCategory' => 1
		);
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
		
		$this->logChange($this->models->UserTaxon->getDataDelta());
        
        return $this->models->UserTaxon->getNewId();
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
			
			$this->logChange($this->models->ProjectRank->getDataDelta());
            
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
        
        return
        	preg_replace_callback(
        		'/\([a-z]{1}/',
        		create_function(
		            '$matches',
		            'return strtoupper($matches[0]);'
        		),
	        	ucfirst(strtolower($name))
    	    );

	}

    private function checkNameSpaces ($name, $projRankId, $parentId)
    {
        /*
        	please take note that only the rank table has an order in 
        	ranks that is guaranteed to be fixed and therefore allows
        	for smaller/larger-comparisons. the project ranks table
        	should NOT be used in a similar fashion. hence the
        	resolving of $projRankId to $rankId below.
        */

        
        // trim and replace accidental double spaces by single ones
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        $species_rank_id = SPECIES_RANK_ID;
        
        $i = $this->models->ProjectRank->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $projRankId
            )
        ));
        
        $rankId = $i[0]['rank_id'];
        
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
        
        $i = $this->models->ProjectRank->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $parent['rank_id']
            )
        ));
        
        $parentRankId = $i[0]['rank_id'];
        

        //parent_id <= species_rank_id:
        //twee spaties mogeljik
        //drie spaties alleen mogelijk als eerste karakter van twee woord een ( is
        if ($parentRankId <= $species_rank_id) {
            if (preg_match_all('/\s/', $name, $d) == 2)
                return true;
            if (preg_match_all('/\s/', $name, $d) == 3) {
                $d = explode(' ', $name);
                return substr($d[1], 0, 1) == '(';
            }
            return false;
        }

        //parent_id > species_rank_id
        //drie spaties mogeljik
        //vier spaties alleen mogelijk als eerste karakter van twee woord een ( is
        if ($parentRankId > $species_rank_id) {
            if (preg_match_all('/\s/', $name, $d) == 3)
                return true;
            if (preg_match_all('/\s/', $name, $d) == 4) {
                $d = explode(' ', $name);
                return substr($d[3], 0, 1) == '(';
            }
            return false;
        }
        echo '.';
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
		$this->logChange($this->models->TaxaRelations->getDataDelta());

        $this->models->VariationRelations->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
		$this->logChange($this->models->VariationRelations->getDataDelta());

        $this->models->VariationLabel->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
		$this->logChange($this->models->VariationLabel->getDataDelta());

        $this->models->TaxonVariation->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'id' => $id
        ));
		$this->logChange($this->models->TaxonVariation->getDataDelta());

        $this->models->MatrixTaxonState->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
		$this->logChange($this->models->MatrixTaxonState->getDataDelta());

        $this->models->MatrixVariation->delete(array(
            'project_id' => $this->getCurrentProjectId(), 
            'variation_id' => $id
        ));
		$this->logChange($this->models->MatrixVariation->getDataDelta());
		
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
                $rel[$key]['label'] = $this->formatTaxon($this->getTaxonById($val['relation_id']));
            }
            else {
                $d = $this->getVariation($val['relation_id']);
                $rel[$key]['label'] = $d['label'];
                $rel[$key]['taxon_id'] = $d['taxon_id'];
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
	
	private function verifyProjectRanksRelations()
	{
		
        $pr = $this->newGetProjectRanks(array(
            'forceLookup' => true
        ));
		
		$pr = array_reverse($pr);
		
		foreach((array)$pr as $key => $val) {
			
			if (!isset($pr[$key+1]['id'])) continue;

            $this->models->ProjectRank->update(array(
                'parent_id' => $pr[$key+1]['id']
            ), array(
                'project_id' => $this->getCurrentProjectId(), 
                'id' => $val['id']
            ));
			
		}
		
	}
        
    public function deleteTaxon($id,$pId=null)
    {
        if (!$id)
            return;
			
		$pId = is_null($pId) ? $this->getCurrentProjectId() : $pId;

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
        
		foreach ((array) $mt as $key => $val) {
			
			$this->deleteTaxonMedia($val['id'], false);
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
        $this->models->Names->delete(array(
            'project_id' => $pId,
            'id' => $id, 
        ));
		
        // delete taxon
        $this->models->Taxon->delete(array(
            'project_id' => $pId,
            'id' => $id, 
        ));
		
		$this->logChange($this->models->Taxon->getDataDelta());
        
    }

	private function getAdjacentTaxa($taxon)
    {
		$type=$taxon['lower_taxon']?'lower':'higher';

		if (!isset($_SESSION['admin']['species']['browse_order'][$type])) {

			$_SESSION['admin']['species']['browse_order'][$type]=
				$this->models->Taxon->freeQuery(
					array(
						'query' => '
							select _a.id,_a.taxon
							from %PRE%taxa _a 
							left join %PRE%projects_ranks _b on _a.rank_id=_b.id 
							where _a.project_id = '.$this->getCurrentProjectId().'
							and _b.lower_taxon = '.($type=='higher' ? 0 : 1).'
							order by _a.taxon_order, _a.taxon
							'
					));

		}

		$prev=$next=false;
		while (list ($key, $val) = each($_SESSION['admin']['species']['browse_order'][$type])) {

			if ($val['id']==$taxon['id']) {

				// current = next because the pointer has already shifted forward
				$next = current($_SESSION['admin']['species']['browse_order'][$type]);

				return array(
					'prev' => $prev!==false ? array(
						'id' => $prev['id'], 
						'label' => $prev['taxon']
					) : null, 
					'next' => $next!==false ? array(
						'id' => $next['id'], 
						'label' => $next['taxon']
					) : null
				);
			}
			
			$prev=$val;
            
		}

        return null;
    }

    private function getLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $getAll =isset($p['get_all']) ? $p['get_all']==1 : false;
        $concise=isset($p['concise']) ? $p['concise']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;

        if (empty($search) && !$getAll)
            return;
			
        $taxa = $this->models->Taxon->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "
			
				select
					_a.taxon_id as id,_a.name as label, _b.rank_id, _c.rank_id as base_rank_id, _b.taxon as taxon, 'names' as source
				from
					%PRE%names _a
	
				left join
					%PRE%taxa _b 
						on _a.project_id=_b.project_id
						and _a.taxon_id=_b.id
	
				left join
					%PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id=_c.id
				
				where
					_a.project_id =  ".$this->getCurrentProjectId()."
					and _a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					and _a.type_id != ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
	
	
				union

			")."
			
			select
				_b.id, _b.taxon as label, _b.rank_id, _d.rank_id as base_rank_id, _b.taxon as taxon, 'taxa' as source
			from
				%PRE%taxa _b

			left join
				%PRE%projects_ranks _d
					on _b.project_id=_d.project_id
					and _b.rank_id=_d.id

			where
				_b.project_id = ".$this->getCurrentProjectId()."
				and _b.taxon like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'

			) as unification
			".($rankAbove ? "where base_rank_id < ".$rankAbove : "")."
			order by label
			limit ".$maxResults
		);

        foreach ((array) $taxa as $key => $val)
		{
			if ($val['source']=='taxa')
			{
				if ($formatted)
					$taxa[$key]['label']=$this->formatTaxon($val);
			}
			else
			{
				if ($formatted)
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$this->formatTaxon($val).')';
				else
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$val['taxon'].')';
			}

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
		}

		return
			$this->makeLookupList(
				$taxa, 
				'species',
				'../species/taxon.php?id=%s',
				false,
				true,
				count($taxa)<$maxResults
			);

    }
	
	private function getCommonCommonName($id)
	{
		$d=$this->models->Commonname->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'taxon_id' => $id,
				'language_id'=>$this->getDefaultProjectLanguage()
			), 
			'columns'=>'commonname',
			'limit' => '1'
		));
		return isset($d) ? $d[0]['commonname'] : null;
	}

	public function branchesAction()
	{
		$this->checkAuthorisation();
				
		$this->setPageName('Browse taxon tree');
		
		if ($this->rHasVal('newOrder'))
		{
			foreach((array)$this->requestData['newOrder'] as $key=>$val)
			{
				$this->models->Taxon->update(
					array('taxon_order'=>$key),
					array('project_id'=>$this->getCurrentProjectId(),'id'=>$val)
				);
			}
			$this->addMessage('new order saved');
			$this->clearCache('species-adjacency-tree');
			
			
		}

		if ($this->rHasVal('p'))
		{
			$p=$this->requestData['p'];
		}
		else 
		{
			/*
				if no id requested, get the top taxon (no parent)
				"_r.id < 10" added as there might be orphans, which are ususally low-level
			*/
			$p=$this->models->Taxon->freeQuery("
				select
					_a.id,
					_a.taxon,
					_r.rank
				from
					%PRE%taxa _a
						
				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				where 
					_a.project_id = ".$this->getCurrentProjectId()." 
					and _a.parent_id is null
					and _r.id < 10

			");

			if ($p && count((array)$p)==1)
			{
				$p=$p[0]['id'];
			} 
			else
			{
				$p=null;
			}

			if (count((array)$p)>1)
			{
				$this->addError('Detected multiple high-order taxa without a parent. Unable to determine which is the top of the tree.');
			}
		}
		
		if ($p)
		{
			
			$taxon=$this->getTaxonById($p);

			$progeny=$this->models->Taxon->freeQuery("
				select
					_a.id,
					_a.taxon,
					_p.rank_id,
					_r.rank,
					_b.child_count,
					ifnull(_i.number_of_images,0) as number_of_images,
					ifnull(_s.number_of_synonyms,0) as number_of_synonyms,
					ifnull(_c.number_of_commonnames,0) as number_of_commonnames,
					ifnull(_n.number_of_pages,0) as number_of_pages

				from
					%PRE%taxa _a
								
				left join
					(select project_id,parent_id,count(*) as child_count from %PRE%taxa group by project_id,parent_id) as _b
						on _a.id=_b.parent_id
						and _b.project_id=_a.project_id

				left join
					(select project_id,taxon_id,count(*) as number_of_images from %PRE%media_taxon group by project_id,taxon_id) as _i
						on _a.id=_i.taxon_id
						and _i.project_id=_a.project_id
						
				left join
					(select project_id,taxon_id,count(*) as number_of_synonyms from %PRE%synonyms group by project_id,taxon_id) as _s
						on _a.id=_s.taxon_id
						and _s.project_id=_a.project_id

				left join
					(select project_id,taxon_id,count(*) as number_of_commonnames from %PRE%commonnames group by project_id,taxon_id) as _c
						on _a.id=_c.taxon_id
						and _c.project_id=_a.project_id

				left join
					(select project_id,taxon_id,count(*) as number_of_pages from %PRE%content_taxa group by project_id,taxon_id) as _n
						on _a.id=_n.taxon_id
						and _n.project_id=_a.project_id

				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				where 
					_a.project_id = ".$this->getCurrentProjectId()." 
					and _a.parent_id = ".$p."

				order by
					_a.taxon_order
			");

			foreach((array)$progeny as $key=>$val)
			{
				$progeny[$key]['commonname']=$this->getCommonCommonName($val['id']);
			}

			if (count((array)$progeny)==0)
			{
				$this->redirect('branches.php?p='.$taxon['parent_id'].'&h='.$taxon['id']);
			}

			$this->setPageName('Browse taxon tree - '.$taxon['taxon']);
			
			$parent=isset($taxon['parent_id']) ? $this->getTaxonById($taxon['parent_id']) : null;
			$parent['commonname']=$this->getCommonCommonName($parent['id']);

			$peers=$this->models->Taxon->freeQuery("
				select
					_a.id,
					_a.taxon,
					_a.rank_id,
					_r.rank,
					ifnull(_b.child_count,0) as child_count					

				from
					%PRE%taxa _a
						
				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join
					(select project_id,parent_id,count(*) as child_count from %PRE%taxa group by project_id,parent_id) as _b
						on _a.id=_b.parent_id
						and _b.project_id=_a.project_id
				where 
					_a.project_id = ".$this->getCurrentProjectId()." 
					and _a.rank_id = ".$taxon['rank_id']."
					and _a.parent_id = ".$taxon['parent_id']."

				order by
					_a.taxon_order
			");
			
			foreach((array)$peers as $key=>$val)
			{
				$peers[$key]['commonname']=$this->getCommonCommonName($val['id']);
			}


			$this->smarty->assign('highlight',$this->rGetVal('h'));
			$this->smarty->assign('parent',$parent);
			$this->smarty->assign('taxon',$taxon);
			$this->smarty->assign('progeny',$progeny);
			$this->smarty->assign('peers',$peers);
			
		}

		$toggle=$this->rHasVal('toggle')?$this->requestData['toggle']:'list-item';
		$this->smarty->assign('toggle',$toggle);
		
		$this->printPage();
		
	}


}
