<?php

/*
 *
 *

alter table pages_taxa add column redirect_to varchar(512) null after def_page;
alter table pages_taxa add column check_query varchar(512) null after redirect_to;
update pages_taxa set check_query='select if(sum(tot)>0,1,0) as `result` from (select count(*) as tot from  traits_taxon_values where project_id = %pid% and taxon_id = %tid% union select count(*) as tot from  traits_taxon_freevalues where project_id = %pid% and taxon_id = %tid%) as u' where page='Exotenpaspoort';

update pages_taxa set redirect_to='?cat=external&id=%tid%&source=aHR0cDovL3Nvb3J0ZW5yZWdpc3Rlci1kZXZlbG9wbWVudC0wMDEuY2xvdWQubmF0dXJhbGlzLm5sL2xpbm5hZXVzX25nL2FwcC92aWV3cy90cmFpdHMvZ2V0LnBocD90YXhvbj0ldGF4b24lJmdyb3VwPSVncm91cCU%3D&taxon=%tid%&group=1&name=kenmerkengroep' where page='Exoteninformatie';
	source = urlencode(base64encode(full url))
		http://localhost/linnaeus_ng/app/views/traits/get.php?taxon=%taxon%&group=%group%

	variationsAction
		saves all variations in project default language only!!!!

	pre-check
		make sure $iniSettings.upload_max_filesize and $iniSettings.post_max_size are sufficient (see config for allowed filesizes)

	new project order of business (* do immediately, others can be done later):

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



        return array(
            'defaultCategories' =>
                array(
                    0 => array(
                        'name' => 'Description',
                        'default' => true,
                        'mandatory' => true,
                        'sections' => array ('General description','Biology')
                    ),
                    1 => array(
                        'name' => 'Detailed Description',
                        'sections' => array ('Behaviour','Cytology','Diagnostic Description',
                            'Genetics','Look Alikes','Molecular Biology','Morphology','Physiology',
                            'Size','Taxon Biology')
                    ),
                    2 => array(
                        'name' => 'Ecology',
                        'sections' => array ('Associations','Cyclicity','Dispersal','Distribution',
                            'Ecology','Habitat','Life Cycle','Life Expectancy','Migration','Trophic Strategy')
                    ),
                    3 => array(
                        'name' => 'Conservation',
                        'sections' => array ('Conservation Status','Legislation','Management','Procedures',
                            'Threats','Trends')
                    ),
                    4 => array(
                        'name' => 'Relevance',
                        'sections' => array ('Diseases','Risk Statement','Uses')
                    ),
                    5 => array(
                        'name' => 'Reproductive',
                        'sections' => array ('Population Biology','Reproduction')
                    )
                )
            );




*/
include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('MediaController.php');


class SpeciesController extends Controller
{
    private $_useNBCExtras = false;
	private $_lookupListMaxResults=99999;
    public $usedModels = array(
        'choices_keysteps',
        'commonnames',
        'content_taxa',
        'hybrids',
        'l2_occurrences_taxa',
        'l2_occurrences_taxa_combi',
        'labels_languages',
        'labels_sections',
        'literature',
        'literature_taxa',
        'matrices_taxa',
        'matrices_taxa_states',
        'matrices_variations',
        'media_descriptions_taxon',
        'media_taxon',
		'name_types',
		'names',
        'nbc_extras',
        'occurrences_taxa',
        'pages_taxa',
        'pages_taxa_titles',
        'projects_roles_users',
        'roles',
        'sections',
        'synonyms',
        'taxa_relations',
		'taxon_quick_parentage',
        'users',
        'users_taxa',
        'variation_relations'
    );
    public $usedHelpers = array(
        'col_loader_helper',
        'csv_parser_helper',
        'file_upload_helper',
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
        '../vendor/prettyPhoto/css/prettyPhoto.css',
        'taxon.css',
        'rank-list.css',
        'dialog/jquery.modaldialog.css',
        'lookup.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'taxon.js',
			'taxon_extra.js',
            'int-link.js',
            'lookup.js'
        )
    );
    public $controllerPublicName = 'Species module';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	private $maxCategories = 50;


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

        $this->smarty->assign('useNBCExtras', $this->_useNBCExtras);
        $this->smarty->assign('useRelated', $this->useRelated);
        $this->smarty->assign('useVariations', $this->useVariations);
        $this->smarty->assign('isHigherTaxa', $this->getIsHigherTaxa());

        $this->includeLocalMenu = true;


		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->useVariations=$this->moduleSettings->getModuleSetting( array( 'setting'=>'use_taxon_variations','subst'=>false) );
		$matrixtype=$this->moduleSettings->getModuleSetting( array( 'setting'=>'matrixtype','module'=>'matrixkey','subst'=>'l2') );

        // variations & related are only shown for NBC matrix projects
        $this->_useNBCExtras = $this->useRelated =  ($matrixtype=='nbc');

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

		$this->redirect('taxon.php?id='.$id);
	}


    public function taxonAction()
    {

		$this->checkAuthorisation();

		$taxon=$this->getTaxonById($this->rGetId());

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
				$p['id'] = $this->rGetId();
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
            $this->filterInternalTags($this->rGetId());
//            $taxon=$this->getTaxonById($this->rGetId());

			$this->setActiveTaxonId($taxon['id']);
			$this->setPageName(sprintf($this->translate('Editing "%s"'),$this->formatTaxon($taxon)));

			// determine the language the page will open in
			$projectLanguages=$this->getProjectLanguages();
			$startLanguage = $this->rHasVal('lan') ? $this->rGetVal('lan') : $this->getDefaultProjectLanguage();

			// get the defined categories (just the page definitions, no content yet)
			$taxonPages = $this->models->PagesTaxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'order' => 'show_order'
			));

			$defaultPage = null;
			foreach ((array)$taxonPages as $key => $val)
			{

				if (!isset($defaultPage)) {
				    $defaultPage = $val['id'];
				}

				foreach ((array) $projectLanguages as $k => $language)
				{

					// for each category in each language, get the category title
					$tpt = $this->models->PagesTaxaTitles->_get(
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
					$this->rGetVal('page') :
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

    public function manageAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Species module overview'));

        if (count((array) $this->getProjectLanguages()) == 0)
            $this->addError(sprintf($this->translate('No languages have been defined. You need to define at least one language. Go %shere%s to define project languages.'), '<a href="../projects/data.php">', '</a>'));

        $this->printPage();
    }

    public function parentageAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Generate parentage table'));

        if ($this->rHasVal('action','generate'))
		{
			$i=$this->saveParentage();
	        $this->smarty->assign('cleared', true);
			$this->addMessage('Generated parentage for '.$i.' taxa');
		}

		$this->printPage();
    }

    public function allSynonymsAction()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('All synonyms'));

		$s = $this->models->Synonyms->freeQuery("
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

    public function allSynonyms2Action()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('All synonyms'));

		$s = $this->models->Synonyms->freeQuery("
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
			if (preg_match_all('/(\s)/',$str,$matches,PREG_OFFSET_CAPTURE)!==false) {
				foreach((array)$matches[0] as $offset) {
					$end=$offset[1];
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

    public function allCommonAction()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('All common names'));

		$c = $this->models->Commonnames->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
			),
			'order' => 'commonname'
		));

		$c = $this->models->Commonnames->freeQuery("
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


    public function pageAction()
    {
		die( 'disabled (will be replaced with NSR-style editor)' );

        $this->checkAuthorisation();
        $this->setPageName($this->translate('Define categories'));

        if ($this->rHasVal('new_page') && !$this->isFormResubmit())
		{
            $tp=$this->createTaxonCategory($this->rGetVal('new_page'), $this->rGetVal('show_order'));
            if (!$tp)
			{
                $this->addError($this->translate('Could not save category.'), 1);
                $this->addError('(' . $tp . ')', 1);
            }
        }

        $lp=$this->getProjectLanguages();

        $pages = $this->models->PagesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'show_order'
        ));

        foreach((array)$pages as $key=>$page)
		{
            foreach((array)$lp as $k=>$language)
			{
                $tpt = $this->models->PagesTaxaTitles->_get(
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
        $this->smarty->assign('maxCategories', $this->maxCategories);
        $this->smarty->assign('languages', $lp);
        $this->smarty->assign('pages', $pages);
        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());

        $this->printPage();
    }

    public function editAction()
    {

		die( 'disabled (will be replaced with NSR-style editor)' );

        $this->checkAuthorisation();

        if (!$this->rHasId())
            $this->redirect('new.php');

        $data = $this->getTaxonById($this->rGetId());

        $pr = $this->newGetProjectRanks();

		//REFAC2015 move this to init!
        if (count((array)$pr)==0)
		{
            $this->addMessage($this->translate('No ranks have been defined.'));
        }

        else
        if (!$this->doLockOutUser($this->rGetId(), true))
		{
	        if (isset($data))
	            $this->smarty->assign('data', $data);

	        $this->smarty->assign('projectRanks', $pr);

			$this->newGetTaxonTree();

            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;

            // save
            if ($this->rHasId() && $this->rHasVal('taxon') && $this->rHasVal('rank_id') && $this->rHasVal('action', 'save') && !$this->isFormResubmit())
			{

                $isHybrid = $this->rGetVal('is_hybrid');

                if ($this->rGetId() == $this->rGetVal('parent_id'))
                    $parentId = $this->rGetVal('org_parent_id');
                else if ($isEmptyTaxaList || $this->rGetVal('parent_id') == '-1')
                    $parentId = null;
                else
                    $parentId = $this->rGetVal('parent_id');

                $parent = $this->getTaxonById($parentId);

                $newName = $this->rGetVal('taxon');

                $newName = trim(preg_replace('/\s+/', ' ', $newName));

                // remove ()'s from subgenus (changed silently)
                $newName = $this->fixSubgenusParentheses($newName, $this->rGetVal('rank_id'));
                // first letter is capitalized & subgenus parantheses are removed (changed silently)
                $newName = $this->fixNameCasting($newName);

                $hasErrorButCanSave = null;

                // Test if children have to be renamed;
                // only applies to genus and below and when name != newName
                $dummy = $this->models->ProjectsRanks->_get(array(
                    'id' => array(
                        'id' => $this->rGetVal('rank_id'),
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                if ($dummy[0]['rank_id'] >= GENUS_RANK_ID && $data['taxon'] != $newName)
				{

                    $this->getTaxonTree(array('pId' => $this->rGetId()));

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
                if (!$this->checkNameSpaces($newName, $this->rGetVal('rank_id'), $this->rGetVal('parent_id'))) {
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
				if (isset($pr[$this->rGetVal('rank_id')]['ideal_parent_id']) && $parent['rank_id'] != $pr[$this->rGetVal('rank_id')]['ideal_parent_id']) {
                    $this->addError(
                    sprintf($this->translate('A %s should be linked to %s. This relationship is not enforced, so you can link to %s, but this may result in problems with the classification.'),
                    strtolower($pr[$this->rGetVal('rank_id')]['rank']), strtolower($pr[$pr[$this->rGetVal('rank_id')]['ideal_parent_id']]['rank']), strtolower($pr[$parent['rank_id']]['rank'])));
                    $hasErrorButCanSave = true;
                }


                /* LETHAL / NON-LETHAL */
                $dummy = $this->newIsTaxonNameUnique(array(
                    'name' => $newName,
                    'rankId' => $this->rGetVal('rank_id'),
                    'parentId' => $parentId,
	                'ignoreId' => $this->rGetId()
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
                if (!is_null($parent) && !$this->canParentHaveChildTaxa($this->rGetVal('parent_id')) || $isEmptyTaxaList) {
                    $this->addError($this->translate('The selected parent taxon can not have children.'));
                    $hasErrorButCanSave = false;
                }
                else
				if(!is_null($parent)) {

                    if (!$this->doNameAndParentMatch($newName, $parent['taxon'])) {
                        $this->addError(sprintf($this->translate('"%s" cannot be selected as a parent for "%s".'), $parent['taxon'], $newName));
                        $hasErrorButCanSave = true;
                    }
                }

                if ($isHybrid != '0' && !$this->canRankBeHybrid($this->rGetVal('rank_id'))) {
                    $this->addError($this->translate('Rank cannot be hybrid.'));
                    $hasErrorButCanSave = false;
                }

                // save as requested
                if (is_null($hasErrorButCanSave) || $this->rHasVal('override', '1')) {

                    $this->clearErrors();



                    $this->models->Taxa->save(
                    array(
                        'id' => $this->rGetId(),
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon' => $newName,
                        'author' => ($this->rGetVal('author') ? $this->rGetVal('author') : null),
                        'parent_id' => (empty($parentId) ? 'null' : $parentId),
                        'rank_id' => $this->rGetVal('rank_id'),
                        'is_hybrid' => $isHybrid
                    ));

					$this->logChange($this->models->Taxa->getDataDelta());

					$this->saveParentage($this->rGetId());

                    if (!empty($children)) {

                        foreach ($children as $child) {

                            $this->models->Taxa->save(
                            array(
                                'id' => $child['id'],
                                'project_id' => $this->getCurrentProjectId(),
                                'taxon' => preg_replace('/^('.$data['taxon'].')\b/', $newName, $child['taxon'])
                            ));

							$this->logChange($this->models->Taxa->getDataDelta());

                        }

                    }

                    if ($this->rHasVal('next', 'main'))
                        $this->redirect('taxon.php?id=' . $this->rGetId());

                    $d = $this->getTaxonById($this->rGetId());

                    $this->addMessage(sprintf($this->translate('"%s" saved.'), $this->formatTaxon($d)));

					$data = $this->getTaxonById($this->rGetId());

                    $this->smarty->assign('data', $d);

                }
                else {

                    $this->requestData['taxon'] = $newName;

                    if ($hasErrorButCanSave) {
                        $this->addMessage(
                        'Please be aware of the warnings above before saving.<br />
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
			$this->smarty->assign('taxa',$this->newGetTaxonTree());

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

		die( 'disabled (will be replaced with NSR-style editor)' );

        $this->checkAuthorisation();

		$this->setPageName($this->translate('New taxon'));

        $pr = $this->newGetProjectRanks();

		// REFAC2015 move to init
        if (count((array) $pr)==0)
		{
            $this->addMessage($this->translate('No ranks have been defined.'));
        }
        else
		{

			// REFAC2015 why are these here??
			$this->newGetTaxonTree();
            $isEmptyTaxaList = !isset($this->treeList) || count((array) $this->treeList) == 0;



            // save
            if ($this->rHasVal('taxon') && $this->rHasVal('rank_id') && $this->rHasVal('action', 'save')

			)
//			 && !$this->isFormResubmit())
			{

                $isHybrid = $this->rGetVal('is_hybrid');

                $parentId = (($this->rHasId() && $this->rGetId() == $this->rGetVal('parent_id')) || $isEmptyTaxaList || $this->rGetVal('parent_id') == '-1' ? null : $this->rGetVal('parent_id'));

                $parent = $this->getTaxonById($parentId);


		// REFAC2015 move to a cleanuo function
		$newName = $this->rGetVal('taxon');
		$newName = trim(preg_replace('/\s+/', ' ', $newName));
		// remove ()'s from subgenus (changed silently)
		$newName = $this->fixSubgenusParentheses($newName, $this->rGetVal('rank_id'));
		// first letter is capitalized & subgenus parantheses are removed (changed silently)
		$newName = $this->fixNameCasting($newName);


                $hasErrorButCanSave = null;

		// REFAC2015 make separate functions
		//checks
		/* NON LETHAL */
		if (!$this->checkNameSpaces($newName, $this->rGetVal('rank_id'), $this->rGetVal('parent_id'))) {
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
		if (isset($pr[$this->rGetVal('rank_id')]['ideal_parent_id']) && $parent['rank_id'] != $pr[$this->rGetVal('rank_id')]['ideal_parent_id']) {
			$this->addError(
			sprintf($this->translate('A %s should be linked to %s. This relationship is not enforced, so you can link to %s, but this may result in problems with the classification.'),
			strtolower($pr[$this->rGetVal('rank_id')]['rank']), strtolower($pr[$pr[$this->rGetVal('rank_id')]['ideal_parent_id']]['rank']), strtolower($pr[$parent['rank_id']]['rank'])));
			$hasErrorButCanSave = true;
                }




		// REFAC2015 make separate functions
		/* LETHAL / NON-LETHAL */
		$dummy = $this->newIsTaxonNameUnique(array(
			'name' => $newName,
			'rankId' => $this->rGetVal('rank_id'),
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
		if (!$this->canParentHaveChildTaxa($this->rGetVal('parent_id')) || $isEmptyTaxaList) {
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

		if ($isHybrid != '0' && !$this->canRankBeHybrid($this->rGetVal('rank_id'))) {
			$this->addError($this->translate('Rank cannot be hybrid.'));
			$hasErrorButCanSave = false;
		}




                // save as requested
                if (is_null($hasErrorButCanSave) || $this->rHasVal('override', '1'))
				{
                    $this->clearErrors();



                    $this->models->Taxa->save(
                    array(
                        'id' => ($this->rHasId() ? $this->rGetId() : null),
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon' => $newName,
                        'author' => ($this->rHasVal('author') ? $this->rGetVal('author') : null),
                        'parent_id' => $parentId,
                        'rank_id' => $this->rGetVal('rank_id'),
                        'is_hybrid' => $isHybrid
                    ));

					$this->logChange($this->models->Taxa->getDataDelta());

                    $newId = $this->models->Taxa->getNewId();

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
                else
				{
                    $this->requestData['taxon'] = $newName;

                    if ($hasErrorButCanSave)
					{
                        $this->addMessage(
                        '
                        	Please be aware of the warnings above before saving.<br />
                        	<input type="button" onclick="taxonOverrideSaveNew()" value="' . $this->translate('save anyway') . '" />');
                    }
                    else
					{
                        $this->addError('Taxon not saved.');
                    }

                    $this->smarty->assign('hasErrorButCanSave', $hasErrorButCanSave);

                    $this->smarty->assign('data', $this->requestData);
                }
            } // save
        } // no ranks defined

        $this->smarty->assign('projectRanks', $pr);

		$this->smarty->assign('taxa',$this->newGetTaxonTree());

        $s = $this->getProjectIdRankByname('Subgenus');

        if ($s)
            $this->smarty->assign('rankIdSubgenus', $s);

        $this->printPage();
    }



    public function deleteAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasVal('action', 'process') && $this->rHasId()) {



			set_time_limit(600);

            $taxon = $this->getTaxonById($this->rGetId());

            foreach ((array) $this->rGetVal('child') as $key => $val) {

                if ($val == 'delete')
				{
                    $this->deleteTaxonBranch($key);
                }
                else
				if ($val == 'orphan')
				{
                    // kill off the parent_id and turn it into a orphan
                    $this->models->Taxa->update(array(
                        'parent_id' => 'null'
                    ), array(
                        'project_id' => $this->getCurrentProjectId(),
                        'id' => $key
                    ));
                }
                else
				if ($val == 'attach') {

                    // reacttach to the parent_id of the to-be-deleted taxon
                    $this->models->Taxa->update(array(
                        'parent_id' => $taxon['parent_id']
                    ), array(
                        'project_id' => $this->getCurrentProjectId(),
                        'id' => $key
                    ));
                }
            }

            // delete the taxon
            $this->deleteTaxon($this->rGetId());

            $this->redirect('branches.php');
        }
        elseif ($this->rHasId()) {

            $taxon = $this->getTaxonById( $this->rGetId() );

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

            $this->redirect('branches.php');
        }

        $this->printPage();
    }

    public function orphansAction ()
    {

		die( 'disabled (should be replaced with NSR-style editor)' );

	    $this->checkAuthorisation();

        $this->setPageName($this->translate('Orphaned taxa'));

        if ($this->rHasVal('child')) {



            foreach ((array) $this->rGetVal('child') as $key => $val) {

                if ($val == 'delete') {

                    $this->deleteTaxonBranch($key);

                    $this->deleteTaxon($key);
                }
                elseif ($val == 'attach') {

                    $this->models->Taxa->update(array(
                        'parent_id' => $this->rGetVal('parent')[$key]
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

        $isOwnParent = $this->models->Taxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'parent_id' => 'id'
            )
        ));

        $hasNoParent = $this->models->Taxa->_get(
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
        $this->checkAuthorisation();

        $this->setBreadcrumbIncludeReferer(array(
            'name' => $this->translate('Taxon list'),
            'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/branches.php'
        ));

        if ($this->rHasId()) {
            // get existing taxon name

            $taxon = $this->getTaxonById( $this->rGetId() );

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

            $this->smarty->assign('id', $this->rGetId());

            $refs = $this->getTaxonLiterature($taxon['id']);

            // user requested a sort of the table
            if ($this->rHasVal('key')) {

                $sortBy = array(
                    'key' => $this->rGetVal('key'),
                    'dir' => ($this->rGetVal('dir') == 'asc' ? 'desc' : 'asc'),
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
			switch ($this->rGetVal("enclosure")) {
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

            switch ($this->rGetVal("delimiter")) {
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
                    $t = $this->models->Taxa->_get(
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

                        $t = $this->models->Taxa->_get(
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
        else if (null!==$this->rGetAll() && !$this->isFormResubmit()) {
            // list of taxa and ranks to be saved detected: save taxa

            if ($this->rHasVal('rows') && isset($_SESSION['admin']['system']['csv_data'])) {

                if ($this->rHasVal('connectToTaxonId')) {

                    $t = $this->models->Taxa->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'id' => $this->rGetVal('connectToTaxonId')
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
                foreach ((array) $this->rGetVal('rows') as $key => $val) {

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
                            foreach ((array) $predecessors as $idx => $pre) {

                                if ($rank == $pre[0] && $idx != count((array) $predecessors) - 1) {
                                    // found a previous occurrence

                                    if (isset($predecessors[$idx - 1])) {

                                        // get the name of the previous occurrence's parent
                                        $parentName = $predecessors[$idx - 1][1];

                                        // apparantly we are at the start of a new branch, so chop off the previous one
                                        $predecessors = array_slice($predecessors, 0, $idx);

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

						$d = $this->models->Commonnames->_get(array('id' =>
						array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $newId,
							'language_id' => $this->getDefaultProjectLanguage(),
							'commonname' => $common
						)));

						if (!$d) {

							$this->models->Commonnames->save(
							array(
								'id' => null,
								'project_id' => $this->getCurrentProjectId(),
								'taxon_id' => $newId,
								'language_id' => $this->getDefaultProjectLanguage(),
								'commonname' => $common
							));

							$this->logChange($this->models->Commonnames->getDataDelta());

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

        if ($this->requestDataFiles) // && !$this->isFormResubmit())
		{

			$raw = array();

			$saved = $failed = $odd = $skipped = 0;

			if (($handle = fopen($this->requestDataFiles[0]["tmp_name"], "r")) !== FALSE)
			{
				$i = 0;
				while (($dummy = fgetcsv($handle,0,$this->rGetVal('fieldsep'))) !== FALSE)
				{
					foreach ((array) $dummy as $val)
					{
						$raw[$i][] = $val;
					}
					$i++;
				}
				fclose($handle);
			}

			$cats = array();
			$clearedTaxa = array();

			foreach ((array) $raw as $key => $line)
			{
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

									$t = $this->models->Taxa->_get(
										array(
											'id' => array(
												'project_id' => $this->getCurrentProjectId(),
												'id' => (int)$tIdOrName
											)
										));

									if ($t[0]['id']!=$tIdOrName)
										$tId = null;

								} else {

									$t = $this->models->Taxa->_get(
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

								$this->models->ContentTaxa->delete(array(
									'project_id' => $this->getCurrentProjectId(),
									'taxon_id' => $tId,
									'language_id' => $lId,
								));

								$clearedTaxa[$tId][$lId] = true;

							} else
							if(!$this->rHasVal('del_all','1')) {

								$this->models->ContentTaxa->delete(array(
									'project_id' => $this->getCurrentProjectId(),
									'taxon_id' => $tId,
									'language_id' => $lId,
									'page_id' => $catId
								));

								$this->logChange($this->models->ContentTaxa->getDataDelta());

							}

							$d = $this->models->ContentTaxa->save(
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

							$this->logChange($this->models->ContentTaxa->getDataDelta());

							if ($d) {

								$argh = $this->models->ContentTaxa->_get(
								array(
									'id' => array(
										'id' => $this->models->ContentTaxa->getNewId(),
										'project_id' => $this->getCurrentProjectId(),
									),
									'columns' => 'length(content) as l'
								));

								if ((int)$argh[0]['l'] != strlen($fVal)) {
									$odd++;
									$this->addMessage(sprintf('mismatched content size for %s (%s)',$tIdOrName,$this->models->ContentTaxa->getNewId()));
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

							$tp = $this->models->PagesTaxa->_get(
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

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

        if ($this->rGetVal('action') == 'save_taxon') {

            $c = $this->saveTaxon($this->rGetAll());

            if (!$c)
                $this->smarty->assign('returnText', '<msg>Empty taxa are not shown');
        }
        else if ($this->rGetVal('action') == 'get_taxon') {

            $this->ajaxActionGetTaxon();

            $_SESSION['admin']['system']['lastActivePage'] = $this->rGetVal('page');
        }
        else if ($this->rGetVal('action') == 'delete_taxon') {



            $this->ajaxActionDeleteTaxon();
        }
        else if ($this->rGetVal('action') == 'delete_page') {

            $this->ajaxActionDeletePage();
        }
        else if ($this->rGetVal('action') == 'get_page_labels') {

            $this->ajaxActionGetPageTitles();
        }
        else if ($this->rGetVal('action') == 'save_page_title') {

            $this->ajaxActionSavePageTitle();
        }
        else if ($this->rGetVal('action') == 'get_page_states') {

            $this->ajaxActionGetPageStates();
        }
        else if ($this->rGetVal('action') == 'publish_content') {

            $this->ajaxActionPublishContent();
        }
        else if ($this->rGetVal('action') == 'get_col') {

            $this->getCatalogueOfLifeData();
        }
        else if ($this->rGetVal('action') == 'save_col') {



            $this->ajaxActionImportTaxa();
        }
        else if ($this->rGetVal('action') == 'save_taxon_name') {

            $this->ajaxActionSaveTaxonName();
        }
        else if ($this->rGetVal('action') == 'save_rank_label') {

            $this->ajaxActionSaveRankLabel();
        }
        else if ($this->rGetVal('action') == 'get_rank_labels') {

            $this->ajaxActionGetRankLabels();
        }
        else if ($this->rGetVal('action') == 'get_rank_by_parent') {

            // get intel on the taxon that will be the parent
            $d = $this->getTaxonById($this->rGetId());

            //// get the project RANK that is the child of the parent taxon's RANK
            //$rank = $this->getProjectRankByParentProjectRank($d['rank_id']);


            // in some cases, certain children have to be skipped in favour of more likely progeny lower down the tree
            $rank = $this->getCorrectedProjectRankByParentProjectRank($d['rank_id']);

            $this->smarty->assign('returnText', $rank);
        }
        else if ($this->rGetVal('action') == 'save_section_title') {

            $this->ajaxActionSaveSectionTitle();
        }
        else if ($this->rGetVal('action') == 'delete_section_title') {

            $this->ajaxActionDeleteSectionTitle();
        }
        else if ($this->rGetVal('action') == 'get_section_titles') {

            $this->ajaxActionGetSectionLabels();
        }
        else if ($this->rGetVal('action') == 'get_language_labels') {

            $this->ajaxActionGetLanguageLabels();
        }
        else if ($this->rGetVal('action') == 'save_language_label') {

            $this->ajaxActionSaveLanguageLabel();
        }
        else if ($this->rGetVal('action') == 'get_subgenus_child_name_prefix') {

            $this->smarty->assign('returnText', $this->getSubgenusChildNamePrefix($this->rGetId())); // phew!
        }
        else if ($this->rGetVal('action') == 'get_formatted_name') {

            $this->smarty->assign('returnText',
            $this->formatTaxon(
            array(
                'taxon' => $this->rGetVal('name'),
                'rank_id' => $this->rGetVal('rank_id'),

                'parent_id' => $this->rGetVal('parent_id'),
                'is_hybrid' => $this->rGetVal('is_hybrid')
            )));
        }
        else if ($this->rGetVal('action') == 'delete_variation') {

            $this->deleteVariation($this->rGetId());
        }
        else if ($this->rHasVal('action', 'get_lookup_list') && $this->rHasVal('search')) {

            $list=$this->getLookupList($this->rGetAll());
			$this->smarty->assign('returnText',$list);

        }
        else if ($this->rHasVal('action', 'delete_synonym') && rHasVal('id')) {

			$d = $this->models->Synonyms->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $this->rGetId()
			));
			$this->smarty->assign('returnText', $d ? '<ok>' : 'error' );

        }
        else if ($this->rHasVal('action', 'delete_common') && rHasVal('id')) {

			$d = $this->models->Commonnames->delete(array(
				'project_id' => $this->getCurrentProjectId(),
				'id' => $this->rGetId()
			));
			$this->smarty->assign('returnText', $d ? '<ok>' : 'error' );

        }
		 else if ($this->rHasVal('action', 'save_synonym_data') && !empty($this->rGetId()) && rHasVal('val') && rHasVal('col')) {

			if ($this->rGetVal('col')=='s')
				$what = array('synonym' => trim($this->rGetVal('val')));
			elseif ($this->rGetVal('col')=='a')
				$what = array('author' => trim($this->rGetVal('val')));
			else
				$what = null;

			if (isset($what))
			{
				$d = $this->models->Synonyms->update(
					$what,
					array(
					'project_id' => $this->getCurrentProjectId(),
					'id' => $this->rGetId()
				));
				$this->logChange($this->models->Synonyms->getDataDelta());
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

		die( 'disabled (will be replaced with NSR-style editor)' );

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxonomic ranks'));

        if ($this->rHasVal('ranks') && !$this->isFormResubmit()) {

			$pr = $this->newGetProjectRanks();

            $parent = 'null';

            $isLowerTaxon = false;

            foreach ((array) $this->rGetVal('ranks') as $key => $rank) {

                if ($this->rGetVal('higherTaxaBorder') == $rank) {

                    $isLowerTaxon = true;

                }

                $d = $this->models->ProjectsRanks->_get(array(
                    'id' => array(
                        'rank_id' => $rank,
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                if ($d) {

                    $this->models->ProjectsRanks->save(
						array(
							'id' => $d[0]['id'],
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));

                    $parent = $d[0]['id'];

                }
                else {

					$this->models->ProjectsRanks->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank,
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));

					$this->logChange($this->models->ProjectsRanks->getDataDelta());

                    $parent = $this->models->ProjectsRanks->getNewId();
                }
            }

            $this->models->ProjectsRanks->update(array(
                'keypath_endpoint' => 0
            ), array(
                'project_id' => $this->getCurrentProjectId(),
                'lower_taxon' => 0
            ));


            foreach ((array) $pr as $key => $rank) {

                if (!in_array($rank['rank_id'], $this->rGetVal('ranks'))) {

                    $pr = $this->models->ProjectsRanks->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'rank_id' => $rank['rank_id']
                        )
                    ));

                    foreach ((array) $pr as $key => $val) {

                        $this->models->LabelsProjectsRanks->delete(array(
                            'project_id' => $this->getCurrentProjectId(),
                            'project_rank_id' => $val['id']
                        ));
                    }

                    $this->models->ProjectsRanks->delete(array(
                        'project_id' => $this->getCurrentProjectId(),
                        'rank_id' => $rank['rank_id']
                    ));
                }
            }

            if (isset($_SESSION['admin']['project']['ranklist']))
                unset($_SESSION['admin']['project']['ranklist']);
            if (isset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']))
                unset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']);

            $this->addMessage($this->translate('Ranks saved.'));
        }

        $r = array_merge($this->models->Ranks->_get(array(
            'id' => array(
                'parent_id is' => 'null'
            ),
            'order' => 'parent_id',
            'fieldAsIndex' => 'id'
        )), $this->models->Ranks->_get(array(
            'id' => array(
                'parent_id !=' => -1
            ),
            'order' => 'parent_id',
            'fieldAsIndex' => 'id'
        )), $this->models->Ranks->_get(array(
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
		die( 'disabled (will be replaced with NSR-style editor)' );

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
		die( 'disabled (will be replaced with NSR-style editor)' );

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Define sections'));

        if ($this->rHasVal('new') && !$this->isFormResubmit()) {

            foreach ((array) $this->rGetVal('new') as $key => $val) {

				if (empty($val)) continue;

				$d = $this->models->Sections->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $key
					),
					'columns' => 'max(show_order) as max_show_order',
				));

				$d = $d ? $d[0]['max_show_order']+1 : 0;

                $this->models->Sections->save(
                array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'page_id' => $key,
                    'section' => $val,
                    'show_order' => $d
                ));

				$this->logChange($this->models->Sections->getDataDelta());
            }
        }

        $lp = $this->getProjectLanguages();

        $pages = $this->models->PagesTaxa->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => '*',
            'order' => 'show_order'
        ));

        foreach ((array) $pages as $key => $val) {

            $s = $this->models->Sections->_get(
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

		die( 'disabled (might be replaced with new rights-function)' );

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Assign taxa to collaborators'));

        if (null!==$this->rGetAll() && !$this->isFormResubmit()) {

            if ($this->rHasVal('delete')) {

                $this->models->UsersTaxa->delete(array(
                    'id' => $this->rGetVal('delete'),
                    'project_id' => $this->getCurrentProjectId()
                ));
            }
            else {

                $this->doAssignUserTaxon($this->rGetVal('user_id'), $this->rGetVal('taxon_id'));
            }

            unset($_SESSION['admin']['species']['usertaxa']);
        }

        $users = $this->getProjectUsers();

        $this->newGetTaxonTree();

        if (isset($this->treeList)) {

            $ut = $this->models->UsersTaxa->_get(array(
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
        $this->checkAuthorisation();

        if ($this->rHasId()) {

            $taxon = $this->getTaxonById( $this->rGetId() );

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

                $this->models->Synonyms->delete(array(
                    'id' => $this->rGetVal('synonym_id'),
                    'project_id' => $this->getCurrentProjectId()
                ));

                $synonyms = $this->models->Synonyms->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon_id' => $this->rGetId()
                    ),
                    'order' => 'show_order'
                ));

                foreach ((array) $synonyms as $key => $val) {

                    $this->models->Synonyms->save(
                    array(
                        'id' => $val['id'],
                        'project_id' => $this->getCurrentProjectId(),
                        'show_order' => $key
                    ));

					$this->logChange($this->models->Synonyms->getDataDelta());

                    $synonyms[$key]['show_order'] = $key;
                }
            }

            if ($this->rHasVal('action', 'up') || $this->rHasVal('action', 'down')) {

                $s = $this->models->Synonyms->_get(
                array(
                    'id' => array(
                        'id' => $this->rGetVal('synonym_id'),
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                $this->models->Synonyms->update(array(
                    'show_order' => $s[0]['show_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(),
                    'show_order' => ($this->rGetVal('action') == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ));

                $this->models->Synonyms->update(array(
                    'show_order' => ($this->rGetVal('action') == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ), array(
                    'id' => $this->rGetVal('synonym_id'),
                    'project_id' => $this->getCurrentProjectId()
                ));
            }

            if ($this->rHasVal('synonym')) {

                $s = $this->models->Synonyms->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon_id' => $this->rGetId()
                    ),
                    'columns' => 'max(show_order) as next'
                ));

                $show_order = $s[0]['next'] == null ? 0 : ($s[0]['next'] + 1);

                $this->models->Synonyms->save(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->rGetId(),
                    'lit_ref_id' => $this->rHasVal('lit_ref_id') ? $this->rGetVal('lit_ref_id') : null,
                    'synonym' => $this->rGetVal('synonym'),
                    'author' => $this->rHasVal('author') ? $this->rGetVal('author') : null,
                    'show_order' => $show_order
                ));

				$this->logChange($this->models->Synonyms->getDataDelta());

                //				echo $this->models->Synonyms->getLastQuery();die();
            }
        }

        //		$literature = $this->getAllLiterature();




        if (!isset($synonyms)) {

            $synonyms = $this->models->Synonyms->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->rGetId()
                ),
                'order' => 'show_order'
            ));
        }

        $this->smarty->assign('adjacentTaxa',$this->getAdjacentTaxa($taxon));

        $this->smarty->assign('id', $this->rGetId());

        $this->smarty->assign('taxon', $taxon);

        $this->smarty->assign('synonyms', $synonyms);

        $this->printPage();
    }

    public function commonAction ()
    {
        $this->checkAuthorisation();

        if ($this->rHasId()) {

            $taxon = $this->getTaxonById( $this->rGetId() );

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

                $this->models->Commonnames->delete(array(
                    'id' => $this->rGetVal('commonname_id'),
                    'project_id' => $this->getCurrentProjectId()
                ));

                $commonnames = $this->models->Commonnames->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon_id' => $this->rGetId()
                    ),
                    'order' => 'show_order'
                ));

                foreach ((array) $commonnames as $key => $val) {

                    $this->models->Commonnames->save(
                    array(
                        'id' => $val['id'],
                        'project_id' => $this->getCurrentProjectId(),
                        'show_order' => $key
                    ));

					$this->logChange($this->models->Commonnames->getDataDelta());

                    $commonnames[$key]['show_order'] = $key;
                }
            }

            if ($this->rHasVal('action', 'up') || $this->rHasVal('action', 'down')) {

                $s = $this->models->Commonnames->_get(
                array(
                    'id' => array(
                        'id' => $this->rGetVal('commonname_id'),
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                $this->models->Commonnames->update(array(
                    'show_order' => $s[0]['show_order']
                ), array(
                    'project_id' => $this->getCurrentProjectId(),
                    'show_order' => ($this->rGetVal('action') == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ));

                $this->models->Commonnames->update(array(
                    'show_order' => ($this->rGetVal('action') == 'up' ? $s[0]['show_order'] - 1 : $s[0]['show_order'] + 1)
                ), array(
                    'id' => $this->rGetVal('commonname_id'),
                    'project_id' => $this->getCurrentProjectId()
                ));
            }

            if ($this->rHasVal('commonname') || $this->rHasVal('transliteration')) {

                $s = $this->models->Commonnames->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon_id' => $this->rGetId()
                    ),
                    'columns' => 'max(show_order) as next'
                ));

                $show_order = $s[0]['next'] == null ? 0 : ($s[0]['next'] + 1);

                $this->models->Commonnames->save(
                array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->rGetId(),
                    'language_id' => $this->rGetVal('language_id'),
                    'commonname' => $this->rGetVal('commonname'),
                    'transliteration' => $this->rGetVal('transliteration'),
                    'show_order' => $show_order
                ));

				$this->logChange($this->models->Commonnames->getDataDelta());

                $this->smarty->assign('lastLanguage', $this->rGetVal('language_id'));
            }
        }

        // get all languages
        $allLanguages = $this->getAllLanguages();

        // get project languages
        $lp = $this->getProjectLanguages();

        if (!isset($commonnames)) {

            $commonnames = $this->models->Commonnames->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->rGetId()
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

        $this->smarty->assign('id', $this->rGetId());

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
            $d = $this->getVariation($this->rGetVal('var'));
            $taxon = $this->getTaxonById($d['taxon_id']);
        }
        else {
            $taxon = $this->getTaxonById( $this->rGetId() );
        }

        $this->setPageName(sprintf($this->translate('Variations for "%s"'), $taxon['taxon']));

        if (!$this->isFormResubmit() && $this->rHasVal('variation')) {

            $v = $this->models->TaxaVariations->save(
            array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $this->rGetId(),
                'label' => trim($this->rGetVal('variation'))
            ));

			$this->logChange($this->models->TaxaVariations->getDataDelta());

            if ($v) {

                $nId = $this->models->TaxaVariations->getNewId();

                $this->models->VariationsLabels->save(
                array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'variation_id' => $nId,
                    'language_id' => $this->getDefaultProjectLanguage(),
                    'label' => trim($this->rGetVal('variation')),
                    'label_type' => 'alternative'
                ));

				$this->logChange($this->models->VariationsLabels->getDataDelta());
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

        $taxon = $this->getTaxonById($this->rGetId());

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

        $taxon = $this->getTaxonById( $this->rGetId() );

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

        $varData = $this->models->TaxaVariations->_get(array(
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
        $this->redirect('../../../app/views/species/taxon.php?p=' . $this->getCurrentProjectId() . '&id=' . $this->rGetVal('taxon_id') . '&cat=' . $this->rGetVal('activePage') . '&lan=' . $this->getDefaultProjectLanguage());
    }


	private function getAdjacentTaxa($taxon)
    {
		$type=$taxon['lower_taxon']?'lower':'higher';

		if (!isset($_SESSION['admin']['species']['browse_order'][$type])) {

			$_SESSION['admin']['species']['browse_order'][$type]=
				$this->models->Taxa->freeQuery(
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
		$taxa = $_SESSION['admin']['species']['browse_order'][$type];
		$keys = array_keys($taxa);

        foreach ($keys as $index => $key)
        {
            $val = $taxa[$key];

			if ($val['id']==$taxon['id']) {

                $next = array_key_exists($index+1, $keys) ? $taxa[$keys[$index+1]] : null;

				return array(
				    'prev' => $prev!==false ? array( 'id' => $prev['id'], 'label' => $prev['taxon'] ) : null,
					'next' => $next!==false ? array( 'id' => $next['id'], 'label' => $next['taxon'] ) : null
				);
			}

			$prev=$val;

		}

        return null;
    }



	private function getProgeny($parent,$level,$family)
	{
		$result = $this->models->Taxa->_get(
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

		if (!$this->models->TaxonQuickParentage->getTableExists())
			return;

		$t = $this->models->Taxa->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'parent_id is' => null
			),
			'columns' => '*'
		));

		if (empty($t[0]['id']))
		{
			$this->addError('no top!?');
			return;
		}

		foreach((array)$t as $parentlesstaxon)
		{

			$this->tmp=array();

			$this->getProgeny($parentlesstaxon['id'],0,array());

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

		$d0=$this->models->Taxa->freeQuery(sprintf($q,'%PRE%','%PRE%','is null',1));
		$d0[0]['list_level']=0;
		$list[]=$d0[0];
		foreach((array)$d0 as $val0) {
			$d1=$this->models->Taxa->freeQuery(sprintf($q,'%PRE%','%PRE%','='.$val0['id'],1000));
			foreach((array)$d1 as $val1) {
				$val1['list_level']=1;
				$list[]=$val1;
				$d2=$this->models->Taxa->freeQuery(sprintf($q,'%PRE%','%PRE%','='.$val1['id'],1000));
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
        $t = $this->models->Taxa->freeQuery(
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

                $this->helpers->ColLoaderHelper->setTaxonName($this->rGetVal('taxon_name'));
            }

            if ($this->rHasVal('taxon_id')) {

                $this->helpers->ColLoaderHelper->setTaxonId($this->rGetVal('taxon_id'));
            }

            if ($this->rHasVal('levels')) {

                $this->helpers->ColLoaderHelper->setNumberOfChildLevels($this->rGetVal('levels'));
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
        $tp = $this->models->PagesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        ));


        foreach ((array) $this->controllerSettings['defaultCategories'] as $key => $page) {

            if ($tp[0]['total'] == 0) {

                if ($this->createTaxonCategory($this->translate($page['name']), $key, isset($page['default']) && $page['default'])) {

                    $this->createTaxonCategorySections($page['sections'], $this->models->PagesTaxa->getNewId());
                }
            }
            else {

                if (isset($page['mandatory']) && $page['mandatory'] === true) {

                    $d = $this->models->PagesTaxa->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'page' => $page['name']
                        ),
                        'columns' => 'count(*) as total'
                    ));

                    if ($d[0]['total'] == 0) {

                        if ($this->createTaxonCategory($this->translate($page['name']), $key, isset($page['default']) && $page['default'])) {

                            $this->createTaxonCategorySections($page['sections'], $this->models->PagesTaxa->getNewId());
                        }
                    }
                }
            }
        }
    }






    private function createTaxonCategorySections ($sections, $pageId)
    {
        foreach ((array) $sections as $key => $val) {

            $this->models->Sections->save(
            array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'page_id' => $pageId,
                'section' => $val,
                'show_order' => $key
            ));

			$this->logChange($this->models->Sections->getDataDelta());
        }
    }



    private function doLockOutUser ($taxonId, $lockOutOfAllScreens = false)
    {
        return false;
/*
        if (empty($taxonId))
            return false;

        $this->models->Heartbeats->cleanUp($this->getCurrentProjectId(), ($this->generalSettings['heartbeatFrequency']));

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

        $h = $this->models->Heartbeats->_get(array(
            'id' => $d
        ));


        return isset($h) ? true : false;
*/
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
            $d = $this->models->Taxa->save(
            array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'taxon' => !empty($name) ? $name : '?'
            ));

			$this->logChange($this->models->Taxa->getDataDelta());
            $taxonId = $this->models->Taxa->getNewId();
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
                        $ct = $this->models->ContentTaxa->delete(
                        array(
                            'project_id' => $this->getCurrentProjectId(),
                            'taxon_id' => $taxonId,
                            'language_id' => $language,
                            'page_id' => $page
                        ));

                        // Mark taxon as 'empty'
                        $this->models->Taxa->update(array(
                            'is_empty' => 1
                        ), array(
                            'id' => $taxonId
                        ));
                    }
                    else
					{

                        // see if such content already exists
                        $ct = $this->models->ContentTaxa->_get(
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
                        $d = $this->models->ContentTaxa->save($newdata);

						$this->logChange($this->models->ContentTaxa->getDataDelta());

                        // Mark taxon as 'empty/not empty' depending on presence of contents
                        $this->models->Taxa->update(array(
                            'is_empty' => empty($content) ? 1 : 0
                        ), array(
                            'id' => $taxonId
                        ));

						$this->logChange($this->models->Taxa->getDataDelta());

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
        $c = $this->models->ContentTaxa->_get(array(
            'where' => 'taxon_id = ' . $taxonId
        ));

        return empty($c) && !$new ? false : true;

    }

    private function filterContent($content)
    {
		return $content;

		/*
        if (!$this->controllerSettings['filterContent'])
            return $content;

        $modified = $content;

        if ($this->controllerSettings['filterContent']['html']['doFilter'])
		{
			$allowedtags=$this->controllerSettings['filterContent']['html']['allowedTags'];




			if ($this->getSetting('admin_species_allow_embedded_images',false))
			{
				$allowedtags.='<img>';
			}
            $modified=strip_tags($modified,$allowedtags);
        }

        return array(
            'content' => $modified,
            'modified' => $content != $modified
        );
		*/
    }

    private function deleteTaxonBranch ($id)
    {

		die( 'disabled (will be replaced with NSR-style editor)' );

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

            $r = $this->models->Ranks->_get(array(
                'id' => array(
                    'rank' => $taxon['taxon_rank']
                )
            ));

            if ($r == false)
                return;

            $pr = $this->models->ProjectsRanks->_get(array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'rank_id' => $r[0]['id']
                )
            ));

            $rankId = $pr[0]['id'];
        }

        if (is_null($rankId))
            return;

        $t = $this->models->Taxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon' => $taxon['taxon_name']
            )
        ));

        if (count((array) $t[0]) == 0) {
            // taxon does not exist in database




            if (!empty($taxon['parent_taxon_name'])) {

                // see if the parent taxon already exists
                $p = $this->models->Taxa->_get(
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
            $this->models->Taxa->save(
            array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'taxon' => $taxon['taxon_name'],
                'parent_id' => $pId,
                'rank_id' => $rankId,
                'is_hybrid' => isset($taxon['hybrid']) && $taxon['hybrid']===true && $this->canRankBeHybrid($rankId) ? 1 : 0
            ));

			$this->logChange($this->models->Taxa->getDataDelta());

            return $this->models->Taxa->getNewId();
        }
        else {
            // taxon does exist in database

            if (empty($t[0]['rank']) || empty($t[0]['parent_id'])) {

                $pId = null;

                if (empty($t[0]['parent_id']) && !empty($taxon['parent_taxon_name'])) {

                    // see if the parent taxon already exists
                    $p = $this->models->Taxa->_get(
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

                $this->models->Taxa->save(
                array(
                    'id' => $t[0]['id'],
                    'project_id' => $this->getCurrentProjectId(),
                    'parent_id' => (empty($t[0]['parent_id']) ? $pId : $t[0]['parent_id']),
                    'rank_id' => $rankId,
                    'is_hybrid' => isset($taxon['hybrid']) && $taxon['hybrid']===true && $this->canRankBeHybrid($rankId) ? 1 : 0
                ));

				$this->logChange($this->models->Taxa->getDataDelta());



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

        $t = $this->models->Taxa->_get(array(
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
        $taxonName = $taxonName ? $taxonName : $this->rGetVal('taxon_name');

        if (empty($taxonName))
            return;

        if (!empty($idToIgnore)) {

            $t = $this->models->Taxa->_get(
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

            $t = $this->models->Taxa->_get(
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
        $s = $this->models->Sections->_get(
        array(
            'id' => array(
                'page_id' => $pageId,
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'show_order asc'
        ));

        $b = '';

        foreach ((array) $s as $key => $val) {

            $ls = $this->models->LabelsSections->_get(
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

            $this->models->ContentTaxa->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'page_id' => $this->rGetId()
            ));

            $this->models->PagesTaxaTitles->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'page_id' => $this->rGetId()
            ));

            $this->models->Sections->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'page_id' => $this->rGetId()
            ));

            $this->models->PagesTaxa->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $this->rGetId()
            ));
        }
    }

    private function ajaxActionGetPageTitles ()
    {
        if (!$this->rHasVal('language')) {

            return;
        }
        else {

            $l = $this->models->Languages->_get(array(
                'id' => $this->rGetVal('language'),
                'columns' => 'direction'
            ));

            $ptt = $this->models->PagesTaxaTitles->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language')
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

                $this->models->PagesTaxaTitles->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetId()
                ));
            }
            else {

                $tpt = $this->models->PagesTaxaTitles->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->rGetVal('language'),
                        'page_id' => $this->rGetId()
                    )
                ));

                $this->models->PagesTaxaTitles->save(
                array(
                    'id' => isset($tpt[0]['id']) ? $tpt[0]['id'] : null,
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetId(),
                    'title' => trim($this->rGetVal('label'))
                ));

				$this->logChange($this->models->PagesTaxaTitles->getDataDelta());
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

            $ct = $this->models->ContentTaxa->_get(
            array(
                'id' => array(
                    'taxon_id' => $this->rGetId(),
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetVal('page')
                )
            ));

            if (empty($ct[0])) {

                $c = array(
                    'project_id' => $this->rGetId(),
                    'taxon_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetVal('page'),
                    'content' => $this->getDefaultPageSections($this->rGetVal('page'), $this->rGetVal('language')),
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

            $t = $this->models->Taxa->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'parent_id' => $this->rGetId()
                ),
                'columns' => 'count(*) as total'
            ));

            if ($t[0]['total'] != 0) {

                $this->smarty->assign('returnText', '<redirect>');
            }
            else {

                $this->deleteTaxon($this->rGetId());

                $this->smarty->assign('returnText', '<ok>');
            }
        }
    }

    private function ajaxActionGetPageStates ()
    {

        // see if such content already exists
        $ct = $this->models->ContentTaxa->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'taxon_id' => $this->rGetId(),
                'language_id' => $this->rGetVal('language')
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

            $ct = $this->models->ContentTaxa->_get(
            array(
                'id' => array(
                    'taxon_id' => $this->rGetId(),
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetVal('page')
                )
            ));

            if (!empty($ct[0])) {

                $d = $this->models->ContentTaxa->update(array(
                    'publish' => $this->rGetVal('state')
                ),
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'taxon_id' => $this->rGetId(),
                    'language_id' => $this->rGetVal('language'),
                    'page_id' => $this->rGetVal('page')
                ));

				$this->logChange($this->models->ContentTaxa->getDataDelta());

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

        $t = $this->models->Taxa->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $this->rGetVal('taxon_id')
            ),
            'columns' => 'count(*) as total'
        ));

        if ($t[0]['total'] > 0) {

            $d = $this->models->Taxa->save(array(
                'id' => $this->rGetVal('taxon_id'),
                'taxon' => trim($this->rGetVal('taxon_name'))
            ));

			$this->logChange($this->models->Taxa->getDataDelta());

            if ($d)
                $this->smarty->assign('returnText', '<ok>');
        }
    }

    private function ajaxActionImportTaxa ()
    {
        if (!$this->rHasVal('data'))
            return;

        foreach ((array) $this->rGetVal('data') as $key => $val) {

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

                $this->models->LabelsProjectsRanks->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'project_rank_id' => $this->rGetId()
                ));
            }
            else {

                $lpr = $this->models->LabelsProjectsRanks->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->rGetVal('language'),
                        'project_rank_id' => $this->rGetId()
                    )
                ));

                $this->models->LabelsProjectsRanks->save(
                array(
                    'id' => isset($lpr[0]['id']) ? $lpr[0]['id'] : null,
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'project_rank_id' => $this->rGetId(),
                    'label' => trim($this->rGetVal('label'))
                ));

				$this->logChange($this->models->LabelsProjectsRanks->getDataDelta());
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
            $l = $this->models->Languages->_get(array(
                'id' => $this->rGetVal('language'),
                'columns' => 'direction'
            ));

            $lpr = $this->models->LabelsProjectsRanks->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language')
                ),
                'columns' => '*, \'' . $l['direction'] . '\' as direction'
            ));

            $this->smarty->assign('returnText', json_encode($lpr));
        }
    }

    private function getProjectRankByParentProjectRank ($id = false)
    {
        if ($id === false)
            $id = $this->rGetId();

        if (empty($id))
            return;

        $d = $this->models->ProjectsRanks->_get(array(
            'id' => array(
                'parent_id' => $id
            )
        ));

        $result = $d[0]['id'] ? $d[0]['id'] : -1;

        return $result;
    }

    private function getProjectIdRankByname ($name)
    {
        $r = $this->models->Ranks->_get(array(
            'id' => array(
                'rank' => $name
            )
        ));
        $r = $this->models->ProjectsRanks->_get(array(
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
        $d = $this->models->ProjectsRanks->_get(array(
            'id' => $projectRankId
        ));

        $r = $this->models->Ranks->_get(array(
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

                $this->models->LabelsSections->delete(
                array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'section_id' => $this->rGetId()
                ));
            }
            else {

                $ls = $this->models->LabelsSections->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'language_id' => $this->rGetVal('language'),
                        'section_id' => $this->rGetId()
                    )
                ));

                $this->models->LabelsSections->save(
                array(
                    'id' => isset($ls[0]['id']) ? $ls[0]['id'] : null,
                    'project_id' => $this->getCurrentProjectId(),
                    'section_id' => $this->rGetId(),
                    'language_id' => $this->rGetVal('language'),
                    'label' => trim($this->rGetVal('label'))
                ));

				$this->logChange($this->models->LabelsSections->getDataDelta());
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

            $this->models->LabelsSections->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'section_id' => $this->rGetId()
            ));

            $this->models->Sections->delete(array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $this->rGetId()
            ));
        }
    }

    private function ajaxActionGetSectionLabels ()
    {
        if (!$this->rHasVal('language')) {

            return;
        }
        else {

            $l = $this->models->Languages->_get(array(
                'id' => $this->rGetVal('language'),
                'columns' => 'direction'
            ));

            $ls = $this->models->LabelsSections->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language')
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

            $ll = $this->models->LabelsLanguages->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language')
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

            $this->models->LabelsLanguages->delete(
            array(
                'project_id' => $this->getCurrentProjectId(),
                'language_id' => $this->rGetVal('language'),
                'label_language_id' => $this->rGetId()
            ));

            if ($this->rHasVal('label')) {

                $this->models->LabelsLanguages->save(
                array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'language_id' => $this->rGetVal('language'),
                    'label_language_id' => $this->rGetId(),
                    'label' => trim($this->rGetVal('label'))
                ));

				$this->logChange($this->models->LabelsLanguages->getDataDelta());
            }

            $this->smarty->assign('returnText', 'saved');
        }
    }

    private function getTaxonSynonymsById ($id = false)
    {
        $id = $id ? $id : ($this->rHasId() ? $this->rGetId() : false);

        if (!$id)
            return;

        $s = $this->models->Synonyms->_get(array(
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
        $lt = $this->models->LiteratureTaxa->_get(array(
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

            $this->models->ContentTaxa->execute(
            'update %table%
					set content = replace(content,"[new litref]","' .  $this->models->Taxa->escapeString($_SESSION['admin']['system']['literature']['newRef']) . '")
					where project_id = ' . $this->getCurrentProjectId() . '
					and taxon_id = ' . $id);
        }

        if (isset($_SESSION['admin']['system']['media']['newRef']) && $_SESSION['admin']['system']['media']['newRef'] != '<new>') {

            $this->models->ContentTaxa->execute(
            'update %table%
					set content = replace(content,"[new media]","' .  $this->models->Taxa->escapeString($_SESSION['admin']['system']['media']['newRef']) . '")
					where project_id = ' . $this->getCurrentProjectId() . '
					and taxon_id = ' . $id);
        }

        $this->models->ContentTaxa->freeQuery('update %table%
				set content = replace(replace(content,"[new litref]",""),"[new media]","")
				where project_id = ' . $this->getCurrentProjectId() . '
				and taxon_id = ' . $id);

        unset($_SESSION['admin']['system']['literature']['newRef']);
        unset($_SESSION['admin']['system']['media']['newRef']);
    }

    private function getCategories ($taxon = null, $languageId = null)
    {
		// get the defined categories (just the page definitions, no content yet)
		$tp = $this->models->PagesTaxa->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId()
			),
			'order' => 'show_order',
			'fieldAsIndex' => 'page_id'
		));

		foreach ((array) $tp as $key => $val) {

			// for each category, get the category title
			$tpt = $this->models->PagesTaxaTitles->_get(
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

        $this->models->UsersTaxa->save(array(
            'id' => null,
            'project_id' => $this->getCurrentProjectId(),
            'user_id' => $userId,
            'taxon_id' => $taxonId
        ));

		$this->logChange($this->models->UserTaxon->getDataDelta());

        return $this->models->UsersTaxa->getNewId();
    }

    private function createStandardCoLRanks ()
    {
        $pr = $this->models->ProjectsRanks->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => 'count(*) as total'
        ));

        if ($pr[0]['total'] > 0)
            return;

        $r = $this->models->Ranks->_get(array(
            'id' => array(
                'in_col' => 1
            ),
            'order' => 'parent_id'
        ));

        $parent = null;

        foreach ((array) $r as $key => $val) {

            $this->models->ProjectsRanks->save(
            array(
                'id' => null,
                'project_id' => $this->getCurrentProjectId(),
                'rank_id' => $val['id'],
                'parent_id' => $parent,
                'lower_taxon' => ($key >= (count((array) $r) - 1) ? 1 : 0)
            ));

			$this->logChange($this->models->ProjectsRanks->getDataDelta());

            $parent = $this->models->ProjectsRanks->getNewId();
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
        return preg_replace_callback(
            '/\([a-z]{1}/',
            function($matches) {
                return strtoupper($matches[0]);
            },
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

        $i = $this->models->ProjectsRanks->_get(array(
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

        $i = $this->models->ProjectsRanks->_get(array(
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

        $t = $this->models->Taxa->_get(
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

        $pr = $this->models->ProjectsRanks->_get(array(
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

            $pr = $this->models->ProjectsRanks->_get(array(
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

        $this->models->VariationsLabels->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'variation_id' => $id
        ));
		$this->logChange($this->models->VariationsLabels->getDataDelta());

        $this->models->TaxaVariations->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'id' => $id
        ));
		$this->logChange($this->models->TaxaVariations->getDataDelta());

        $this->models->MatricesTaxaStates->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'variation_id' => $id
        ));
		$this->logChange($this->models->MatricesTaxaStates->getDataDelta());

        $this->models->MatricesVariations->delete(array(
            'project_id' => $this->getCurrentProjectId(),
            'variation_id' => $id
        ));
		$this->logChange($this->models->MatricesVariations->getDataDelta());

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

		$pr = !empty($pr) ? array_reverse($pr) : array();

		foreach((array)$pr as $key => $val) {

			if (!isset($pr[$key+1]['id'])) continue;

            $this->models->ProjectsRanks->update(array(
                'parent_id' => $pr[$key+1]['id']
            ), array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $val['id']
            ));

		}

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
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;

        if (empty($search) && !$getAll)
            return;

        $taxa = $this->models->Taxa->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "

				select
					_a.taxon_id as id,
					_a.name as label,
					_b.rank_id,
					_c.rank_id as base_rank_id,
					_b.taxon as taxon,
					'names' as source,
					_d.rank
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

				left join
					%PRE%ranks _d
					on _c.rank_id=_d.id


				where
					_a.project_id =  ".$this->getCurrentProjectId()."
					and _a.name like '".($matchStartOnly ? '':'%'). $this->models->Taxa->escapeString($search)."%'
					and _a.type_id != ".
						(
							isset($this->_nameTypeIds[PREDICATE_VALID_NAME]['id']) ?
								$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : -1
						)."
				union

			")."

			select
				_b.id,
				_b.taxon as label,
				_b.rank_id,
				_d.rank_id as base_rank_id,
				_b.taxon as taxon,
				'taxa' as source,
				_e.rank
			from
				%PRE%taxa _b

			left join
				%PRE%projects_ranks _d
					on _b.project_id=_d.project_id
					and _b.rank_id=_d.id

				left join
					%PRE%ranks _e
					on _d.rank_id=_e.id

			where
				_b.project_id = ".$this->getCurrentProjectId()."
				and _b.taxon like '".($matchStartOnly ? '':'%'). $this->models->Taxa->escapeString($search)."%'

			) as unification
			where 1=1
			".($rankAbove ? "and base_rank_id < ".$rankAbove : "")."
			".($rankEqualAbove ? "and base_rank_id <= ".$rankEqualAbove : "")."

			order by base_rank_id, label
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

			$taxa[$key]['label']=$taxa[$key]['label'].' ['.$val['rank'].']';

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
		}

		return
			$this->makeLookupList(array(
				'data'=>$taxa,
				'module'=>'species',
				'url'=>'../species/taxon.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($taxa)<$maxResults
			));

    }

    public function deleteTaxon($id,$pId=null)
    {
        if (!$id)
            return;

		$pId = is_null($pId) ? $this->getCurrentProjectId() : $pId;

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
        $tv = $this->models->TaxaVariations->delete(
			array('id' =>
					array(
						'project_id' => $pId,
						'taxon_id' => $id
					)
				)
		);

		foreach((array)$tv as $key => $val) {

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
        $this->models->Commonnames->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));

        // delete synonyms
        $this->models->Synonyms->delete(array(
            'project_id' => $pId,
            'taxon_id' => $id
        ));

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

		foreach ((array) $mt as $key => $val) {

			$this->deleteTaxonMedia($val['id'], false);
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
        $this->models->Names->delete(array(
            'project_id' => $pId,
            'id' => $id,
        ));

        // delete taxon
        $this->models->Taxa->delete(array(
            'project_id' => $pId,
            'id' => $id,
        ));

		$this->logChange($this->models->Taxa->getDataDelta());

    }


}
