<?php

/*

	POST-PROCESSING
	- compact keys
	- make Diversity index
	- matrix char state image dimensions
	- save Group + Group order in matrix
	- check syn-vern-description-style synonyms + common names

	change the title in the xml file to %test% and a random (yet readable) project name will be assigned.

	should add '_sawModule' to all modules

	DO NOT ADD $this->replaceInternalLinks() to anything outside the procedure fixEmbeddedLinksAndMedia(); it needs ALL data to be processed
	first, before it can resolve and fix internal links

	species and higher taxa are strtolowered before parsed for the lookup arrays. OTHER MODULES ARE NOT, so:
		[l][m]Glossary[/m][r]annihilation[/r][t]annihilated[/t][/l]
	will only link correctly if the glosssary-entry is 'annihilation', and will not link when it is 'Annihilation'. that's
	what you get when you don't use unique ID's.

	keys als enige buiten de boom: nergens een los veld met de rank
	ik wil alleen zeker weten dat er in alle projecten in de Tk en Pk ook ALTIJD de rank er bij staat voor de HT.
	maarten schermer: want als dat niet zo is, heb ik ECHT een probleem, omdat ik er geen losse rank bij heb


I looked a little deeper, and have concluded the following: much like L2 itself can be, the exported XML-file is also ambiguous. There are two elements that both contain the taxon tree: <tree> and <records>.
<tree> is just that - all taxa with their rank and parent-child relationship -, whereas <records> contains more information - taxa, rank, parent-child relationship, description, reference to media, vernaculars, map data, etc. - but not necessarily for every taxon. In the Flora XML, <tree> has 3054 elements, while <records> only has 2205. Apparently, there are approx. 850 taxa that appear in the L2-tree, but have no other data than their name, rank and parentage attached to it.

The import follows the same logic: create the central list of taxa from <tree> and import all other data from <records>. However, I have found, and confirmed this from the XML-file, that there are taxa that DO appear in <records> but DO NOT appear in the tree. Obviously, this is a violation of the referential integrity of the file, and as a result, these taxa cannot be saved. In the flora, they are:

Unable to resolve name "Orchis militaris" to taxon id.
Unable to resolve name "Hordeum marinum" to taxon id.
Unable to resolve name "Saxifraga granulata-'Plena'" to taxon id.
Unable to resolve name "Callitriche cophocarpa" to taxon id.
Unable to resolve name "Oenanthe peucedanifolia" to taxon id.

Please be aware that these are six other taxa than the three mentioned earlier - Atropa belladonna, Genianella campestris & Nemesia melissaefolium - that appear in neither <tree>, nor <records>.

	DOCUMENT resolveLanguage($l) !!!!

	what is:
		$d->projectclassification --> "Five kingdoms"
		$d->projectnomenclaturecode --> "ICZM"

	need to set manually!
		project.includes_hybrids
		project.logo

	border higher/lower taxa (give choice)
	=> projects_ranks.lower_taxon
	=> projects_ranks.keypath_endpoint (gaat dat dan ook?)

	taxa table
		'taxon_order' => 0,
		'is_hybrid' => 0,
		'list_level' => 0

	doesn't seem to work, though empty:
		unlink($paths['project_thumbs']);
		unlink($paths['project_media']);

	yell about getControllerSettingsKey->maxChoicesPerKeystep

	proj_literature->proj_reference->keywords->keyword
	ignored: literary references to glossary terms

	MATRIX KEY: never saw all types


*/


include_once ('ImportController.php');

class ImportL2Controller extends ImportController
{
    public $usedHelpers = array(
        'file_upload_helper',
        'xml_parser'
    );
    public $controllerPublicName = 'Linnaeus 2 Import';
    public $cssToLoad = array(
        'import.css'
    );
    public $jsToLoad = array();
    private $_deleteOldMediaAfterImport = false; // might become a switch later, but let's not overdo it
    private $_knownModules = array(
        'file',
        'project',
        'proj_literature',
        'glossary',
        'introduction',
        'tree',
        'records',
        'text_key',
        'pict_key',
        'diversity'
    );
    private $_sawModule = false;
	private $_retainInternalLinks = false; // keep false, as internal links are re-created by means of hotwords

	private $_tempTeller = 0;

	private $defaultProjectCss = '../../style/import-default-stylesheet.css';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();

        error_reporting(E_ERROR | E_PARSE);

        $this->setBreadcrumbRootName($this->translate('Data import'));

        $this->setSuppressProjectInBreadcrumbs();

		set_time_limit(2400); // RIGHT!

		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
        $this->checkAuthorisation();
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


    public function l2StartAction ()
    {
        if ($this->rHasVal('process', '1'))
            $this->redirect('l2_project.php');

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
        else if ($this->rHasVal('serverFile') && !$this->rHasVal('clear', 'file')) {

            if (file_exists($this->rGetVal('serverFile'))) {

                $_SESSION['admin']['system']['import']['file'] = array(
                    'path' => $this->rGetVal('serverFile'),
                    'name' => $this->rGetVal('serverFile'),
                    'src' => 'existing'
                );
            }
            else {

                $this->addError('File "' . $this->rGetVal('serverFile') . '" does not exist.');
                unset($_SESSION['admin']['system']['import']['file']);
            }
        }

        if ($this->rHasVal('imagePath') || $this->rHasVal('noImages')) {

            if ($this->rHasVal('noImages')) {

                $_SESSION['admin']['system']['import']['imagePath'] = false;
            }
            else if (file_exists($this->rGetVal('imagePath'))) {

                $_SESSION['admin']['system']['import']['imagePath'] = rtrim($this->rGetVal('imagePath'), '/') . '/';

                if (!is_writable($_SESSION['admin']['system']['import']['imagePath'])) {

                    $this->addError($_SESSION['admin']['system']['import']['imagePath'] . ' is not writable.<br/>
                        This is required to change the file names to lowercase.');

                }

            }
            else {

                $this->addError('Image path "' . $this->rGetVal('imagePath') . '" does not exist or unreachable.');
                unset($_SESSION['admin']['system']['import']['imagePath']);
            }
        }

		$_SESSION['admin']['system']['import']['thumbsPath'] = false;

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

        clearstatcache(true, $this->generalSettings['directories']['mediaDirProject']);

        $this->smarty->assign('mediaDir', $this->generalSettings['directories']['mediaDirProject']);

        $this->smarty->assign('isSharedMediaDirWritable', is_writable($this->generalSettings['directories']['mediaDirProject']));

		$this->setWarning('PLEASE NOTE: two calls to "$this->saveSetting()" have been disabled, please check and fix manually (feb 2016).');

        $this->printPage();
    }


    public function l2ProjectAction ()
    {
        if (!empty($_SESSION['admin']['system']['import']['imagePath'])) {

            $errors = $this->lowercaseMediaFiles();

            if (!empty($errors)) {

                 foreach ($errors as $file) {

                    $this->addError("Cannot rename $file.");
                }
            }
        }

        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('l2_start.php');

        $this->setPageName($this->translate('Creating project'));

        $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

        $this->helpers->XmlParser->setDoReturnValues(true);
        $d = $this->helpers->XmlParser->getNode('project');

        if (isset($d->title)) {

            $projectTitle = trim((string) $d->title);

            if ($projectTitle == '%test%') {

                $projectTitle = $this->getRandomTestProjectName();
                $testProject = true;
            }

            $newId = $this->createProject(
            array(
                'title' => $projectTitle,
                'version' => trim((string) $d->version),
                'sys_description' => 'Created by import from a Linnaeus 2-export.',
                'css_url' => $this->defaultProjectCss
            ));

            $_SESSION['admin']['system']['import']['moduleCount'] = 0;

            if (!$newId) {

                $this->addError('Could not create new project "' . $projectTitle . '". Does a project with the same name already exist?');
            }
            else {

                $project = $this->getProjects($this->getNewProjectId());

                $this->addMessage('Created new project "' . $projectTitle . '"');

                $this->setNewProjectId($newId);

                $this->addUserToProjectAsLeadExpert($this->getNewProjectId());

                $this->makeMediaTargetPaths();

                $this->createProjectCssFile($newId, $projectTitle);

                $this->smarty->assign('newProjectId', $newId);

                // add language
                $l = $this->addProjectLanguage(trim((string) $d->language));

                if (!$l) {

                    $this->addError($this->storeError('Unable to use project language "' . trim((string) $d->language) . '"; defaulted to English.', 'Project'));
                    $this->addProjectLanguage('English');
                }
                else {

                    $this->setNewDefaultLanguageId($l);
                    $this->addMessage('Set language "' . trim((string) $d->language) . '"');
                }

                $_SESSION['admin']['system']['import']['paths'] = $this->makePathNames($this->getNewProjectId());

                $_SESSION['admin']['system']['import']['errorlog']['header'] = array(
                    'project' => $projectTitle,
                    'version' => trim((string) $d->version),
                    'test_project' => (isset($testProject) && $testProject === true),
                    'createdate' => date('c'),
                    'imported_from' => $_SESSION['admin']['system']['import']['file']['path'],
                    'id' => $this->getNewProjectId()
                );
            }
        }
        else {

            $this->addError('Failed to retrieve project title from XML-file.');
        }

        $this->printPage();
    }


    public function l2SpeciesAction ()
    {
        if (!isset($_SESSION['admin']['system']['import']['file']['path']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Species and ranks for "%s"'),$project['title']));

        $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

        $this->helpers->XmlParser->setCallbackFunction(array(
            $this,
            'xmlParserCallback_ResolveRanks'
        ));

        $this->helpers->XmlParser->getNodes('treetaxon');

        $_SESSION['admin']['system']['import']['substRanks'] = ($this->rHasVal('substRanks') ? $this->rGetVal('substRanks') : null);

        $this->helpers->XmlParser->setCallbackFunction(array(
            $this,
            'xmlParserCallback_ResolveSpecies'
        ));

        $this->helpers->XmlParser->getNodes('treetaxon');

        $treetops = $this->checkTreeTops($_SESSION['admin']['system']['import']['loaded']['species']);

		$d = $this->models->Ranks->_get(
			array(
				'id' =>'select lower(rank) as rank from %table% group by rank having count(*) > 1',
				'fieldAsIndex' => 'rank'
			)
		);

		// sadly, several ranks occur in multiple positions with the same name
		$multiples = array();
		foreach((array)$_SESSION['admin']['system']['import']['loaded']['ranks'] as $key => $val) {

			if (isset($d[$key])) {

				$whatever = $this->models->Ranks->_get(array('id' => array('rank'=>$key),'columns' => 'id,rank,additional,default_label,abbreviation,parent_id'));
				foreach((array)$whatever as $tra => $lala) {
					$dontcare = $this->models->Ranks->_get(array('id' => array('id'=>$lala['parent_id']),'columns' => 'rank'));
					$whatever[$tra]['parent_rank'] = $dontcare[0]['rank'];
				}

				$multiples[$key] = $whatever;

			}

		}

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            $_SESSION['admin']['system']['import']['loaded']['ranks'] =
				$this->addProjectRanks(
					$_SESSION['admin']['system']['import']['loaded']['ranks'],
					($this->rHasVal('substRanks') ? $this->rGetVal('substRanks') : null),
		            ($this->rHasVal('substParentRanks') ? $this->rGetVal('substParentRanks') : null),
		            ($this->rHasVal('multiRankChoice') ? $this->rGetVal('multiRankChoice') : null)
				);

            $species = $_SESSION['admin']['system']['import']['loaded']['species'] =
				$this->addSpecies(
					$_SESSION['admin']['system']['import']['loaded']['species'],
					$_SESSION['admin']['system']['import']['loaded']['ranks']
				);

            if ($this->rHasVar('treetops'))
                $_SESSION['admin']['system']['import']['loaded']['species'] = $this->fixTreetops($species, $this->rGetVal('treetops'));

			foreach((array)$_SESSION['admin']['system']['import']['loaded']['species'] as $key => $val) {

				if (empty($val['parent_id']))
					$_SESSION['admin']['system']['import']['species-errors'][] = array(
						'taxon' => $val['taxon'],
						'cause' => 'saved as orphan, could not resolve parent "' . $val['parent'] . '" with rank "' . $val['parent_rank_name'] . '".'
					);

            }

            $this->assignTopSpeciesToUser($_SESSION['admin']['system']['import']['loaded']['species']);
            $this->addModuleToProject(MODCODE_SPECIES, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);
            $this->addModuleToProject(MODCODE_HIGHERTAXA, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

            $this->addMessage('Saved ' . count((array) $_SESSION['admin']['system']['import']['loaded']['ranks']) . ' ranks');
            $this->addMessage('Saved ' . count((array) $_SESSION['admin']['system']['import']['loaded']['species']) . ' taxa');

            if (count((array) $_SESSION['admin']['system']['import']['species-errors']) !== 0) {

                foreach ((array) $_SESSION['admin']['system']['import']['species-errors'] as $val)
                    $this->addError($this->storeError($val['taxon'] . ': ' . $val['cause'], 'Species'));
            }

            $this->smarty->assign('processed', true);
        }

        $this->smarty->assign('project', $project);

        $this->smarty->assign('ranks', $_SESSION['admin']['system']['import']['loaded']['ranks']);

        $this->smarty->assign('projectRanks', $this->getPossibleRanks());

        $this->smarty->assign('multiples', $multiples);

        if ($this->rHasVal('substRanks'))
            $this->smarty->assign('substRanks', $this->rGetVal('substRanks'));

        if ($this->rHasVal('substParentRanks'))
            $this->smarty->assign('substParentRanks', $this->rGetVal('substParentRanks'));

        $this->smarty->assign('species', $_SESSION['admin']['system']['import']['loaded']['species']);

        $this->smarty->assign('treetops', $treetops);

        $this->printPage();
    }



    public function l2SpeciesDataAction ()
    {
        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Additional species data for "%s"'),$project['title']));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if (
				$this->rHasVal('taxon_overview', 'on') ||
				$this->rHasVal('taxon_media', 'on') ||
				$this->rHasVal('taxon_common', 'on') ||
				$this->rHasVal('taxon_synonym', 'on') ||
				!$this->rHasVal('syn_vern_description', 'off')) {

                if ($this->rHasVal('taxon_overview', 'on'))
				{
                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview'] = true;
                    $_SESSION['admin']['system']['import']['speciesOverviewCatId'] = $this->createCategory(null,0);
                    $_SESSION['admin']['system']['import']['speciesNomenclatureCatId'] = $this->createCategory('Nomenclature',1);
                    $_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'] = array();
                }
                else
				{
                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview'] = false;
                }

                if ($this->rHasVal('taxon_media', 'on')) {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media'] = true;

                    $this->loadControllerConfig('Species');

                    foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $val)
                        $_SESSION['admin']['system']['import']['mimes'][$val['mime']] = $val;

                    $this->loadControllerConfig();

                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'] = array();
                }
                else {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media'] = false;
                }


                if ($this->rHasVal('taxon_common', 'on')) {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common'] = true;
                    $_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'] = array();
                }
                else {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common'] = false;
                }

                if ($this->rHasVal('taxon_synonym', 'on')) {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym'] = true;
                    $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'] = array();
                }
                else {

                    $_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym'] = false;
                }


                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

                $this->helpers->XmlParser->setCallbackFunction(array(
                    $this,
                    'xmlParserCallback_Species'
                ));

                $this->helpers->XmlParser->getNodes('taxondata');


                if ($this->rHasVal('taxon_overview', 'on')) {

                    $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved'] . ' general species description(s).');

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'] as $val)
                            $this->addError($this->storeError($val['cause'], 'Species content'));

                        $this->addError($this->storeError('(probable cause: the taxa above are present in &lt;records&gt; but not in &lt;tree&gt;)', 'Species content'));
                    }

                    unset($_SESSION['admin']['system']['import']['speciesOverviewCatId']);
                    unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']);
                    unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed']);
                }
                else {

                    $this->addMessage($this->storeError('Skipped species description.'));
                }

                if ($this->rHasVal('taxon_media', 'on')) {

                    $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved'] . ' media files.');

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'] as $val)
                            $this->addError($this->storeError($val['cause'], 'Species media'));
                    }

                    unset($_SESSION['admin']['system']['import']['speciesOverviewCatId']);
                    unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']);
                    unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed']);
                    unset($_SESSION['admin']['system']['import']['mimes']);
                }
                else {

                    $this->addMessage($this->storeError('Skipped media.'));
                }

                if ($this->rHasVal('taxon_common', 'on')) {

                    $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved'] . ' common name(s).');

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'] as $val)
                            $this->addError($this->storeError($val['cause'], 'Species common name'));
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped common names.'));
                }


                if ($this->rHasVal('taxon_synonym', 'on')) {

                    $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved'] . ' synonym(s).');

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'] as $val)
                            $this->addError($this->storeError($val['cause'], 'Species synonyms'));
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped synonyms.'));
                }



                if (!$this->rHasVal('syn_vern_description', 'off')) {

					$_SESSION['admin']['system']['import']['synVernDescription']['unknownlanguages'] = array();
					$_SESSION['admin']['system']['import']['synVernDescription']['synonymCount'] =
					$_SESSION['admin']['system']['import']['synVernDescription']['commonCount'] = 0;
                    $_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description'] = $this->rGetVal('syn_vern_description');

					$this->helpers->XmlParser->setCallbackFunction(array(
						$this,
						'xmlParserCallback_SynVernDescription'
					));

					$this->helpers->XmlParser->getNodes('taxondata');


					if ($_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='synonyms') {

	                    $this->addMessage('Skipped common names from syn_vern_description.');

					} else {

						$this->addMessage(sprintf('Syn_vern_description: parsed %s common names.',$_SESSION['admin']['system']['import']['synVernDescription']['commonCount']));
						foreach((array)$_SESSION['admin']['system']['import']['synVernDescription']['unknownlanguages'] as $language => $count)
							$this->addError(sprintf('Syn_vern_description: skipped common names for unknown language "%s" (%sx)',$language,$count));

					}


					if ($_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='common') {

	                    $this->addMessage('Skipped synonyms from syn_vern_description.');

					} else {

	                    $this->addMessage(sprintf('Syn_vern_description: parsed %s synonyms.',$_SESSION['admin']['system']['import']['synVernDescription']['synonymCount']));
					}


                }
                else {

                    $this->addMessage($this->storeError('Skipped syn_vern_description.'));
                }

                unset($_SESSION['admin']['system']['import']['elementsToLoad']);
                unset($_SESSION['admin']['system']['import']['synVernDescription']);

            }
            else {

                $this->addMessage($this->storeError('Skipped species description.'));
                $this->addMessage($this->storeError('Skipped media.'));
                $this->addMessage($this->storeError('Skipped common names.'));
                $this->addMessage($this->storeError('Skipped synonyms.'));
            }

            $this->smarty->assign('processed', true);
        } else {

			$_SESSION['admin']['system']['import']['hasSynVernDescription'] =
			$_SESSION['admin']['system']['import']['hasSynonyms'] =
			$_SESSION['admin']['system']['import']['hasCommonNames'] = false;

			$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

			$this->helpers->XmlParser->setCallbackFunction(array(
				$this,
				'xmlParserCallback_SynVernDescriptionCheck'
			));
			$this->helpers->XmlParser->getNodes('taxondata');

			$this->smarty->assign('hasCommonNames',$_SESSION['admin']['system']['import']['hasCommonNames']);
			$this->smarty->assign('hasSynVernDescription',$_SESSION['admin']['system']['import']['hasSynVernDescription']);
			$this->smarty->assign('hasSynonyms',$_SESSION['admin']['system']['import']['hasSynonyms']);

		}

        $this->printPage();
    }




    public function l2LiteratureGlossaryAction ()
    {
        $this->checkAuthorisation(true);

        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Literature and glossary for "%s"'),$project['title']));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if ($this->rHasVal('literature', 'on') || $this->rHasVal('glossary', 'on')) {

                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

                if ($this->rHasVal('literature', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['literature']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['literature']['failed'] = array();

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_Literature'
                    ));

                    $this->helpers->XmlParser->getNodes('proj_reference');

                    if ($this->_sawModule) {

                        $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['literature']['saved'] . ' literary reference(s).');

                        if (count((array) $_SESSION['admin']['system']['import']['loaded']['literature']['failed']) !== 0) {

                            foreach ((array) $_SESSION['admin']['system']['import']['loaded']['literature']['failed'] as $val)
                                $this->addError($this->storeError($val['cause'], 'Literature'));
                        }

                        $this->addModuleToProject(MODCODE_LITERATURE, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

                        $this->_sawModule = false;
                    }
                    else {

                        $this->addMessage($this->storeError('Didn\'t find any literature.', 'Literature'));
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped literature.'));
                }

                if ($this->rHasVal('glossary', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['glossary']['saved'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['glossary']['failed'] = array();

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_Glossary'
                    ));

                    $this->loadControllerConfig('Glossary');

                    foreach ((array) $this->controllerSettings['media']['allowedFormats'] as $val)
                        $_SESSION['admin']['system']['import']['mimes'][$val['mime']] = $val;

                    $this->loadControllerConfig();

                    $this->helpers->XmlParser->getNodes('term');

                    $this->loadControllerConfig();

                    if ($this->_sawModule) {

                        $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['glossary']['saved'] . ' glossary item(s).');

                        if (count((array) $_SESSION['admin']['system']['import']['loaded']['glossary']['failed']) !== 0) {

                            foreach ((array) $_SESSION['admin']['system']['import']['loaded']['glossary']['failed'] as $val)
                                $this->addError($this->storeError($val['cause'], 'Glossary'));
                        }

                        unset($_SESSION['admin']['system']['import']['mimes']);

                        $this->addModuleToProject(MODCODE_GLOSSARY, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

                        $this->_sawModule = false;
                    }
                    else {

                        $this->addMessage($this->storeError('Didn\'t find a glossary.', 'Glossary'));
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped glossary.'));
                }
            }
            else {

                $this->addMessage($this->storeError('Skipped literature.'));
                $this->addMessage($this->storeError('Skipped glossary.'));
            }

            $this->smarty->assign('processed', true);
        }

        $this->printPage();
    }



    public function l2ContentAction ()
    {

        $this->checkAuthorisation(true);

        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Additional content for "%s"'),$project['title']));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if ($this->rHasVal('welcome', 'on') || $this->rHasVal('introduction', 'on')) {

                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

                if ($this->rHasVal('welcome', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['welcome']['saved'] = array();
                    $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'] = array();

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_Welcome'
                    ));

                    $this->helpers->XmlParser->getNodes('project');

                    $this->addModuleToProject(MODCODE_CONTENT, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['welcome']['saved']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['welcome']['saved'] as $val)
                            $this->addMessage($val);
                    }

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['welcome']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'] as $val)
                            $this->addError($this->storeError($val, 'Welcom texts'));
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped welcome text(s).'));
                }

                if ($this->rHasVal('introduction', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['introduction']['show_order'] = 0;
                    $_SESSION['admin']['system']['import']['loaded']['introduction']['saved'] = array();
                    $_SESSION['admin']['system']['import']['loaded']['introduction']['failed'] = array();

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_Introduction'
                    ));

                    $this->helpers->XmlParser->getNodes('topic');

                    $this->addModuleToProject(MODCODE_INTRODUCTION, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['introduction']['saved']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['introduction']['saved'] as $val)
                            $this->addMessage($val);
                    }

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['introduction']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['introduction']['failed'] as $val)
                            $this->addError($this->storeError($val, 'Introduction'));
                    }

                    unset($_SESSION['admin']['system']['import']['loaded']['introduction']['show_order']);
                }
                else {

                    $this->addMessage($this->storeError('Skipped introduction.'));
                }
            }
            else {

                $this->addMessage($this->storeError('Skipped welcome text(s).'));
                $this->addMessage($this->storeError('Skipped introduction.'));
            }

            $this->smarty->assign('processed', true);
        }

        $this->printPage();
    }



    public function l2KeysAction ()
    {

        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName($this->translate('Keys for "' . $project['title'] . '"'));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if ($this->rHasVal('key_dich', 'on') || $this->rHasVal('key_matrix', 'on')) {

                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

                if ($this->rHasVal('key_dich', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys'] = array(
                        'text_key' => false,
                        'pict_key' => false
                    );
                    $_SESSION['admin']['system']['import']['loaded']['key_dich']['keyStepIds'] = null;
                    $_SESSION['admin']['system']['import']['loaded']['key_dich']['stepAdd'] = null;
                    $_SESSION['admin']['system']['import']['loaded']['key_dich']['failed'] = array();

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_KeyDichotomous'
                    ));

                    $this->helpers->XmlParser->getNodes('text_key');
                    $this->helpers->XmlParser->getNodes('pict_key');



                    if ($this->_sawModule) {

                        $this->addModuleToProject(MODCODE_KEY, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);
						/*
                        $this->saveSetting(array(
                            'name' => 'keytype',
                            'value' => 'l2',
                            'pId' => $this->getNewProjectId()
                        ));
						*/

                        $this->_sawModule = false;

                        $this->addMessage('Created dichotomous key.');
                    }
                    else {

                        $this->addMessage($this->storeError('Didn\'t find a dichotomous key.', 'Dichotomous key'));
                    }

                    if (count((array) $_SESSION['admin']['system']['import']['loaded']['key_dich']['failed']) !== 0) {

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['key_dich']['failed'] as $val)
                            $this->addError($this->storeError($val, 'Dichotomous key'));
                    }

                    unset($_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']);
                }
                else {

                    $this->addMessage($this->storeError('Skipped dichotomous key.'));
                }

                if ($this->rHasVal('key_matrix', 'on')) {

                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'] = null;
                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'] = array();
                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['taxaNotPresent'] = 0;

                    $this->helpers->XmlParser->setCallbackFunction(array(
                        $this,
                        'xmlParserCallback_KeyMatrixResolve'
                    ));

                    $this->helpers->XmlParser->getNodes('taxondata');

                    if (isset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'])) {

                        $m = $this->saveMatrices($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices']);

                        if (isset($m['failed']))
                            foreach ((array) $m['failed'] as $val)
                                $this->addError($this->storeError($val['cause'], 'Matrix'));

                        $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'] = $m['matrices'];

                        $this->helpers->XmlParser->setCallbackFunction(array(
                            $this,
                            'xmlParserCallback_KeyMatrixConnect'
                        ));

                        $this->helpers->XmlParser->getNodes('taxondata');

                        if (count((array) $_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed']) !== 0) {

                            foreach ((array) $_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'] as $val)
                                $this->addError($this->storeError($val, 'Matrix'));
                        }

						if ($_SESSION['admin']['system']['import']['loaded']['key_matrix']['taxaNotPresent']!=0)
							$this->addError($this->storeError('Note: '.$_SESSION['admin']['system']['import']['loaded']['key_matrix']['taxaNotPresent'].' taxa do not appear in any matrix (due to empty \'identify\' tag or missing matrix name).', 'Matrix'));

                        if ($this->_sawModule) {

                            $this->addModuleToProject(MODCODE_MATRIXKEY, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

                            $this->_sawModule = false;

                            $this->addMessage('Created matrix key(s).');
                        }
                        else {

                            $this->addMessage($this->storeError('Didn\'t find any matrix key(s).', 'Matrix'));
                        }

                        unset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices']);
                    }
                    else {

                        $this->addMessage('Didn\'t find a matrix key.');
                    }
                }
                else {

                    $this->addMessage($this->storeError('Skipped matrix key(s).'));
                }
            }
            else {

                $this->addMessage($this->storeError('Skipped dichotomous key.'));
                $this->addMessage($this->storeError('Skipped matrix key(s).'));
            }

            $this->smarty->assign('processed', true);
        }

        $this->printPage();
    }



    public function l2MapAction ()
    {
        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Map data for "%s"'),$project['title']));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if ($this->rHasVal('map_items', 'on')) {

                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

                $_SESSION['admin']['system']['import']['loaded']['map']['maps'] = null;
                $_SESSION['admin']['system']['import']['loaded']['map']['types'] = null;
                $_SESSION['admin']['system']['import']['loaded']['map']['saved'] = $_SESSION['admin']['system']['import']['loaded']['map']['failed'] = $_SESSION['admin']['system']['import']['loaded']['map']['skipped'] = 0;
                $_SESSION['admin']['system']['import']['loaded']['map']['typeless'] = null;

                $_SESSION['admin']['system']['import']['map']['amersfoort'] = $this->rHasVal('afoort', 'on');

                $this->helpers->XmlParser->setCallbackFunction(array(
                    $this,
                    'xmlParserCallback_Map'
                ));

                $this->loadControllerConfig('MapKey');

				set_time_limit(4800); // RIGHT!

                $this->helpers->XmlParser->getNodes('taxondata');

                $this->loadControllerConfig();

                $this->updateMapTypeColours();

                if ($this->_sawModule) {

                    $this->addModuleToProject(MODCODE_DISTRIBUTION, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);
					/*
                    $this->saveSetting(array(
                        'name' => 'maptype',
                        'value' => 'l2',
                        'pId' => $this->getNewProjectId()
                    ));
					*/

                    $this->addMessage('Imported ' . $_SESSION['admin']['system']['import']['loaded']['map']['saved'] . ' map items.');

                    if (isset($_SESSION['admin']['system']['import']['loaded']['map']['typeless'])) {

                        $this->addMessage('Imported ' . count((array) $_SESSION['admin']['system']['import']['loaded']['map']['typeless']) . ' without a datatype.');

                        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['map']['typeless'] as $val) {

                            $this->storeError('Saved map item without datatype (taxon ID / occurrence ID: ' . $val . '; ref OccurrenceTaxon table)', 'Map');
                        }
                    }

                    $this->addMessage('Skipped ' . $_SESSION['admin']['system']['import']['loaded']['map']['skipped'] . ' because of invalid coordinates.');

                    $this->addMessage('Failed ' . $_SESSION['admin']['system']['import']['loaded']['map']['failed'] . ', most likely duplicates.');

                    $this->_sawModule = false;
                }
                else {

                    $this->addMessage($this->storeError('Didn\'t find any maps.', 'Map'));
                }
            }
            else {

                $this->addMessage($this->storeError('Skipped map.'));
            }

            $this->smarty->assign('processed', true);
        }


        $this->printPage();
    }



    public function l2AdditionalAction ()
    {

        $this->checkAuthorisation(true);

        if ($this->rHasVal('action', 'errorlog'))
            $this->downloadErrorLog();

        if (!isset($_SESSION['admin']['system']['import']['file']['path']) || !isset($_SESSION['admin']['system']['import']['loaded']['species']))
            $this->redirect('l2_start.php');

        $project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(sprintf($this->translate('Additional data for "%s"'),$project['title']));

        if ($this->rHasVal('process', '1') && !$this->isFormResubmit()) {

            if ($this->rHasVal('modules')) {


                if ($this->rHasVal('modules-name')) {

                    $_SESSION['admin']['system']['import']['freeModules']['names'] = $this->rGetVal('modules-name');
                }
                else {

                    $_SESSION['admin']['system']['import']['freeModules']['names'] = null;
                }


                $this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);
                $this->helpers->XmlParser->setCallbackFunction(array(
                    $this,
                    'xmlParserCallback_Custom'
                ));

                $_SESSION['admin']['system']['import']['loaded']['custom']['saved'] = null;

                foreach ((array)$this->rGetVal('modules') as $module => $val) {

                    if ($val == 'on')
                        $this->helpers->XmlParser->getNodes($module);
                }

                if (count((array) $_SESSION['admin']['system']['import']['loaded']['custom']['saved']) !== 0) {

                    foreach ((array) $_SESSION['admin']['system']['import']['loaded']['custom']['saved'] as $val)
                        $this->addMessage($val);
                }

                unset($_SESSION['admin']['system']['import']['loaded']['custom']['saved']);
            }
            else {

                $this->addMessage($this->storeError('Skipped additional modules.'));
            }

            $this->importPostProcessing();

            $this->smarty->assign('processed', true);
        }

        $addMod = $this->detectCustomModulesInXML();

        if (count((array) $addMod) == 0) {

            $this->addMessage('No additional modules found.');
            $this->importPostProcessing();
        }

        if (isset($_SESSION['admin']['system']['import']['loaded']['embeddedMedia'])) {

            if (isset($_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['saved']))
                $this->addMessage($this->storeError('Processed ' . $_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['saved'] . ' embedded media files.'));

            if (isset($_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['failed'])) {

                foreach ((array) $_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['failed'] as $val) {

                    $this->addError($this->storeError('Embedded media: could not copy file "' . $val . '"'));
                }
            }
        }

        $this->smarty->assign('projectId', $this->getNewProjectId());

        $this->smarty->assign('modules', $addMod);

        $this->printPage();
    }



    public function goNewProject ()
    {
		die( 'should redirect to choose_project.php' );

        $this->unsetProjectSessionData();
        $this->setCurrentProjectId($this->getNewProjectId());
        $this->setCurrentProjectData();
        $this->reInitUserRolesAndRights();
        $this->setCurrentUserRoleId();

        unset($_SESSION['admin']['system']['import']);
        unset($_SESSION['admin']['project']['ranks']);

        $this->redirect($this->getLoggedInMainIndex());
    }



    /* xml parser callback functions */
    public function xmlParserCallback_Species ($obj)
    {
        if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview'] === true)
            $this->addSpeciesContent($obj);

        if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media'] === true)
            $this->addSpeciesMedia($obj);

        if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common'] === true)
            $this->addSpeciesCommonNames($obj);

        if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym'] === true)
            $this->addSpeciesSynonyms($obj);
    }



    public function xmlParserCallback_Literature ($obj)
    {
        $this->_sawModule = true;

        $this->addLiterature($obj);
    }



    public function xmlParserCallback_Glossary ($obj)
    {
        $this->_sawModule = true;

        $this->addGlossary($obj);
    }



    public function xmlParserCallback_Welcome ($obj)
    {
        $this->addWelcomeTexts($obj);
    }



    public function xmlParserCallback_Introduction ($obj)
    {
        $this->addIntroduction($obj);
    }



    public function xmlParserCallback_KeyDichotomous ($obj, $node)
    {
        $this->_sawModule = true;

        $this->addKeyDichotomous($obj, $node);
    }



    public function xmlParserCallback_KeyMatrixResolve ($obj)
    {
        $this->resolveMatrices($obj);
    }



    public function xmlParserCallback_KeyMatrixConnect ($obj)
    {
        $this->connectMatrices($obj);
    }



    public function xmlParserCallback_Map ($obj)
    {
        $this->saveMapItem($obj);
    }



    public function xmlParserCallback_Custom ($obj, $node)
    {
        $this->addCustomModule($node, $obj);
    }


    private function addCustomModule ($module, $data)
    {

        // create the module
        $m = $this->models->FreeModulesProjects->save(array(
            'id' => null,
            'module' => $module,
            'project_id' => $this->getNewProjectId(),
            'active' => 'y'
        ));

        $_SESSION['admin']['system']['import']['loaded']['custom']['saved'][] = 'Created module "' . $module . '".';

        $newModuleId = $this->models->FreeModulesProjects->getNewId();

        $this->grantFreeModuleAccessRights($newModuleId);

        // element names for the module pages can vary, find out the element name for the entire page
        $arrayData = (array) $data;
        $d = array_keys($arrayData);
        $pageName = $d[0];

        $titleField = null;
        $contentField = null;
        $imageField = null;

        $i = 0;
        foreach ((array) $arrayData[$pageName] as $page) {

            /*
				names of fields with a page are unpredictable; we assume 1st is title, 2nd is content and 3rd, if any, image.
				image can be one of two types:
				1)	straightforward: <image>imagename.jpg</image>
				2)	copied from species module:
						<multimediafile>
							<filename>name.jpg</filename>
							<caption>bla</caption>
							<multimedia_type>image</multimedia_type>
						</multimediafile>
			*/
            foreach ((array) $page as $key => $val) {

                if ($i == 0)
                    $titleField = $key;
                if ($i == 1)
                    $contentField = $key;
                if ($i == 2) {
                    if (is_object($val)) {
                        // is object copied from species (which, we assume, has fixed element-names)
                        $imageField = false;
                    }
                    else {
                        // is simple field
                        $imageField = $key;
                    }
                }
                $i++;
            }
        }

        // get the multimedia-paths
        $paths = isset($_SESSION['admin']['system']['import']['paths']) ? $_SESSION['admin']['system']['import']['paths'] : $this->makePathNames($this->getNewProjectId());

        $showOrder = 0;

        // saving the actual pages
        foreach ((array) $arrayData[$pageName] as $page) {

            $page = ((array) $page);

            if ($imageField === false) {

                $d = (array) $page['multimediafile'];
                $image = strtolower(trim($d['filename']));
                //$thisCaption = trim($d['caption']);
            }
            else if (!is_null($imageField)) {

                $image = strtolower(trim($page[$imageField]));
            }
            else {

                $image = null;
            }

            $topic = trim($page[$titleField]);
            $content = trim($page[$contentField]);


            if (!empty($topic)) {

                // create a new page
                $this->models->FreeModulesPages->save(
                array(
                    'project_id' => $this->getNewProjectId(),
                    'module_id' => $newModuleId,
                    'got_content' => 1,
                    'show_order' => $showOrder++
                ));

                $newPageId = $this->models->FreeModulesPages->getNewId();

                // save the title and content
                $cfm = $this->models->ContentFreeModules->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'module_id' => $newModuleId,
                    'language_id' => $this->getNewDefaultLanguageId(),
                    'page_id' => $newPageId,
                    'topic' => $topic,
                    'content' => $content
                ));

                if ($cfm === true) {

                    $_SESSION['admin']['system']['import']['loaded']['custom']['saved'][] = '  Saved ' . $module . ' topic "' . $topic . '".';

                    $moduleLinkName = !is_null($_SESSION['admin']['system']['import']['freeModules']['names'][$module]) ? $_SESSION['admin']['system']['import']['freeModules']['names'][$module] : $module;

                    $_SESSION['admin']['system']['import']['freeModules']['ids'][$moduleLinkName][$topic] = array(
                        'moduleId' => $newModuleId,
                        'pageId' => $newPageId
                    );

                    if (!empty($image)) {

                        if ($this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $image, $paths['project_media'] . $image)) {

                            $this->models->FreeModulesPages->update(array(
                                'image' => $image
                            ), array(
                                'project_id' => $this->getNewProjectId(),
                                'id' => $newPageId
                            ));
                        }
                        else {

                            $_SESSION['admin']['system']['import']['loaded']['custom']['failed'][] = '  Could not save image ' . $image . ' for topic "' . $topic . '".';
                        }
                    }
                }
                else {

                    $_SESSION['admin']['system']['import']['loaded']['custom']['failed'][] = '  Could not save content for topic "' . $topic . '" (' . $cfm . ').';
                }
            }
            else {

                // encountered empty topic
                if (!empty($content))
                    $_SESSION['admin']['system']['import']['loaded']['custom']['failed'][] = '  Skipped titleless topic ("' . substr($content, 0, 25) . '...").';
            }
        }
    }


    // projects, modules, users
    private function getProjects ($id = null)
    {
        $d = isset($id) ? array(
            'id' => $id
        ) : '*';

        $d = $this->models->Projects->_get(array(
            'id' => $d
        ));

        return isset($id) ? $d[0] : $d;
    }


    private function setNewProjectId ($id)
    {
        if ($id == null)
            unset($_SESSION['admin']['system']['import']['newProjectId']);
        else
            $_SESSION['admin']['system']['import']['newProjectId'] = $id;
    }


    private function getNewProjectId ()
    {
        return (isset($_SESSION['admin']['system']['import']['newProjectId'])) ? $_SESSION['admin']['system']['import']['newProjectId'] : null;
    }


    private function grantFreeModuleAccessRights ($id)
    {
        $this->models->FreeModulesProjectsUsers->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'free_module_id' => $id,
            'user_id' => $this->getCurrentUserId()
        ));
    }

    // languages
    private function addLanguage ($language)
	{

		$this->models->Languages->save(
			array(
				'id' => null,
				'language' => $language
			));

		return $this->models->Languages->getNewId();

	}

    private function addProjectLanguage ($language)
    {
        $l = $this->resolveLanguage($language);

        if (!$l) $l = $this->addLanguage($language);

        $p = $this->models->LanguagesProjects->save(
        array(
            'id' => null,
            'language_id' => $l,
            'project_id' => $this->getNewProjectId(),
            'def_language' => 1,
            'active' => 'y',
            'tranlation_status' => 1
        ));

        return $p ? $l : false;
    }


    private function setNewDefaultLanguageId ($id)
    {
        if ($id == null)
            unset($_SESSION['admin']['system']['import']['newLanguageId']);
        else
            $_SESSION['admin']['system']['import']['newLanguageId'] = $id;
    }


    private function getNewDefaultLanguageId ()
    {
        return (isset($_SESSION['admin']['system']['import']['newLanguageId'])) ? $_SESSION['admin']['system']['import']['newLanguageId'] : null;
    }


    private function resolveLanguage ($l)
    {
        // too much encoding headaches
        switch (htmlentities($l)) {

            case 'Nederlands':
                $l = 'Dutch';
                break;
            case 'Fran&Atilde;&sect;ais':
            case 'Frans':
                $l = 'French';
                break;
            case 'Deutsch':
            case 'Duits':
                $l = 'German';
                break;
            case 'Engels':
                $l = 'English';
                break;
            case 'Spaans':
                $l = 'Spanish';
                break;
            case 'Italiaans':
                $l = 'Italian';
                break;
        }

        $l = $this->models->Languages->_get(array(
            'id' => array(
                'language' => $l
            ),
            'columns' => 'id'
        ));

        return ($l) ? $l[0]['id'] : false;
    }


    // ranks
    public function xmlParserCallback_ResolveRanks ($obj)
    {
        $importRank = trim((string) $obj->taxon);
        $parentRankParent = trim((string) $obj->parenttaxon);

		//echo ':'.$obj->name.':'.$importRank.':'.$parentRankParent.'<br />';

        if (empty($importRank))
            return;

        // @TODO: 1==1 is always true? Is this useful?
        if (1==1 || !isset($_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank])) {

            $r = $this->models->Ranks->_get(array(
                'id' => array(
                    'default_label' => $importRank
                ),
                'columns' => 'id'
            ));

            $rankId = $r[0]['id'];

            if (isset($rankId)) {

                $_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['rank_id'] = $rankId;


                if ($parentRankParent != 'none' && $parentRankParent != 'unknown') {

                    $r = $this->models->Ranks->_get(
                    array(
                        'id' => array(
                            'default_label' => $parentRankParent
                        ),
                        'columns' => 'id'
                    ));

                    $_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_id'] = isset($r[0]['id']) ? $r[0]['id'] : false;


                    $_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_name'] = $parentRankParent;
                }
                else {
                    $_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_id'] = null;
                }
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['rank_id'] = false;
            }
        }
    }


    private function getPossibleRanks ()
    {
        return $this->models->Ranks->_get(array(
            'id' => '*',
            'fieldAsIndex' => 'id'
        ));
    }


    private function addProjectRank ($label, $rank, $isLower, $parentId)
    {
        $this->models->ProjectsRanks->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'rank_id' => $rank['rank_id'],
            'parent_id' => isset($parentId) ? $parentId : null,
            'lower_taxon' => $isLower ? '1' : '0'
        ));

        $rank['id'] = $this->models->ProjectsRanks->getNewId();

        $this->models->LabelsProjectsRanks->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'project_rank_id' => $rank['id'],
            'language_id' => $this->getNewDefaultLanguageId(),
            'label' => $label
        ));

        return $rank;
    }


    private function addProjectRanks ($ranks, $substituteRanks = null, $substituteParentRanks = null, $multiRankChoice = null)
    {
        if (isset($substituteRanks) || isset($substituteParentRanks))
            $possibleRanks = $this->getPossibleRanks();

        $d = $prevId = null;

        $isLower = false;

        foreach ((array) $ranks as $key => $val) {

            // no valid rank
            if (!isset($val['rank_id']) || $val['rank_id'] === false) {

                if (isset($substituteRanks) && isset($substituteRanks[$key])) {

                    $x = $possibleRanks[$substituteRanks[$key]]; // hand picked substitute rank
                    $val = array(
                        'rank_id' => $x['id'],
                        'parent_id' => $x['parent_id'],
                        'parent_name' => $x['rank']
                    );
                }
                else {

                    continue; // unresolvable rank
                }
            }

            if (!$isLower && (strtolower($key) == 'species'))
                $isLower = true;

			if (isset($multiRankChoice[$key]))
				$val['rank_id'] = $multiRankChoice[$key];

            $d[$key] = $this->addProjectRank($key, $val, $isLower, $prevId);
            $prevId = $d[$key]['id'];
        }

        // if $isLower is still false (i.e., all ranks are higher taxa), set the last (=lowest (is it? (possibly not, but do we really care?))) rank to being lower taxa
        if ($isLower == false) {

            $this->models->ProjectsRanks->update(array(
                'lower_taxon' => '1'
            ), array(
                'id' => $d[$key]['id'],
                'project_id' => $this->getNewProjectId()
            ));
        }

        return $d;
    }


    private function makeIndexName ($name, $rank)
    {
        $name = trim($name);

        // no spaces = HT, which always has the rank name prefixed
        if (strpos($name, ' ') === false)
            return strtolower(trim($rank) . ' ' . $this->cleanL2Name($name));
        else
            return strtolower($this->cleanL2Name($name));
    }


    // species (& treetops)
    public function xmlParserCallback_ResolveSpecies ($obj)
    {
        $rankName = trim((string) $obj->taxon) ? trim((string) $obj->taxon) : null;

        $rankId = isset($_SESSION['admin']['system']['import']['loaded']['ranks'][trim((string) $obj->taxon)]) && $_SESSION['admin']['system']['import']['loaded']['ranks'][trim((string) $obj->taxon)]['rank_id'] !== false ? $_SESSION['admin']['system']['import']['loaded']['ranks'][trim(
        (string) $obj->taxon)]['rank_id'] : (isset($_SESSION['admin']['system']['import']['substRanks'][$rankName]) ? $_SESSION['admin']['system']['import']['substRanks'][$rankName] : null);

        $indexName = $this->makeIndexName(trim((string) $obj->name), $rankName);

        $_SESSION['admin']['system']['import']['loaded']['species'][$indexName] = array(
            'taxon' => $this->cleanL2Name(trim((string) $obj->name)),
            'original_taxon' => trim((string) $obj->name),
            'rank_id' => $rankId,
            'rank_name' => $rankName,
            'parent' => $this->cleanL2Name(trim((string) $obj->parentname)),


            'original_parent' => trim((string) $obj->parentname),
            'parent_rank_name' => trim((string) $obj->parenttaxon),
            'source' => 'records->taxondata'
        );
    }


	// that lovely field called "syn_vern_description"
    public function xmlParserCallback_SynVernDescriptionCheck ($taxon)
    {

		if (isset($taxon->vernaculars->vernacular))
			$_SESSION['admin']['system']['import']['hasCommonNames'] = true;

		if (isset($taxon->syn_vern_description))
			$_SESSION['admin']['system']['import']['hasSynVernDescription'] = true;

		if (isset($taxon->synonyms->synonym))
			$_SESSION['admin']['system']['import']['hasSynonyms'] = true;


    }


    public function xmlParserCallback_SynVernDescription ($taxon)
    {

		//$_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']
		// off / common / synonyms / both

        $indexName = $this->makeIndexName((string) $taxon->name, (string) $taxon->taxon);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id']))
            $taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];
		else
			return;

		if (!isset($taxon->syn_vern_description) || $_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='off')
			return;

		// data is split in single lines based on [br]
        $lines = explode('[br]', $taxon->syn_vern_description);

		$synonyms = $commons = $unknownLanguages = array();

		foreach((array)$lines as $line) {

			// links ([l][/l]) are replaced with just their text part (between [t][/t])
			$line = preg_replace_callback('/(\[l\])(.*)((\[t\]){1})(.*)(\[\/t\]){1}(\[\/l\])/',function($n){return $n[5];},$line);

			// remaining links (apparently without any text), and paragraph starts and ends ([p][/p]) are removed, lines are trimmed
			$line = trim(preg_replace(array('/(\[p\]|\[\/p\])/','/(\[l\])(.*)(\[\/l\])/'),'',$line));

			// if what remains is shorter than 10 characters, or has no spaces (single word), the line is ignored
			if (strlen($line)<10 || strpos($line,' ')===false)
				continue;

			// if what remains starts with [b],[u],[i], the line is ignored (header) (not the ending as well: "[b]Synonymy[/b] (of subgenus Isocladius)")
			if (preg_match('/^(\[b\]|\[u\]|\[i\])(.*)(\[\/b\]|\[\/u\]|\[\/i\])$/',$line)==1)
				continue;

			// ignoring Type Species
			if (stripos($line,'Type species')!==false)
				continue;

			// ignoring inline Common name headers
			if (stripos($line,'Common names')!==false)
				continue;

			// Common names: if they end with a valid Dutch or English language name in brackets (straight or curved) ("Dansemyg (Danish)")
			if (preg_match('/(.*)((\(|\[)([a-zA-Z-\/]*)(\)|\]))$/',$line,$m)==1) {

				$pNames = trim($m[1]);
				$language = trim($m[4]);

				$l = $this->models->Languages->_get(array(
					'id' => array(
						'language' => $language
					),
					'columns' => 'id'
				));

				// valid language
				if ($l[0]['id']) {

					if (strpos($pNames,';')!==false) {
						$names = explode(';',$names);
						foreach((array)$names as $val) {
							$val = trim($val);
							if (!empty($val)) $commons[] = array($l[0]['id'],$val,$language);
						}
					} else {
						$commons[] = array($l[0]['id'],$pNames,$language);
					}

				}
				// invalid language
				else {

					//$this->addError(sprintf($this->translate('Could not resolve language "%s" of common name(s) "%s"'),$language,$pNames));
					$_SESSION['admin']['system']['import']['synVernDescription']['unknownlanguages'][$language] =
						isset($_SESSION['admin']['system']['import']['synVernDescription']['unknownlanguages'][$language]) ?
						($_SESSION['admin']['system']['import']['synVernDescription']['unknownlanguages'][$language]+1) : 1;

				}

			} else {

				$synonyms[] = trim($line);

			}

		}


		if (
			$_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='common' ||
			$_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='both') {


			foreach((array)$commons as $key => $val) {

				$dummy = $this->replaceOldMarkUp(str_replace(array('  ',' :','::',' :'),array(' ',':',':',':'),$val[1]));

				$this->models->Commonnames->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $taxonId,
					'language_id' => $val[0],
					'commonname' => $dummy
				));

				//$this->addMessage(sprintf('Added common name "%s" (%s)',$val[1],$val[2]));
				$_SESSION['admin']['system']['import']['synVernDescription']['commonCount']++;

			}

		}

		if (
			$_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='synonyms' ||
			$_SESSION['admin']['system']['import']['elementsToLoad']['syn_vern_description']=='both') {

			foreach((array)$synonyms as $key => $val) {

				$dummy = $this->replaceOldMarkUp(str_replace(array('  ',' :','::',' :'),array(' ',':',':',':'),$val));

				$res = $this->models->Synonyms->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $taxonId,
					'synonym' => $dummy,
					'author' => null,
					'show_order' => $key
				));

				//$this->addMessage(sprintf('Added synonym "%s"',$val));
				$_SESSION['admin']['system']['import']['synVernDescription']['synonymCount']++;

			}

		}


    }


	// auxiliry functions
    private function cleanL2Name ($taxon)
    {
        $l2Markers = array(
            'subsp.',
            'var.',
            'subvar.',
            'f.',
            'subf.'
        );

        if (count(explode(' ', $taxon)) > 2) {

            foreach ($l2Markers as $marker) {
                if (false !== stripos($taxon, $marker)) {
                    $taxon = str_replace($marker, '', $taxon);
                    break;
                }
            }
        }
        return trim(str_replace('  ', ' ', $taxon));
    }


    private function removeRankFromTaxonName ($taxon)
    {
        $ranks = array_keys($_SESSION['admin']['system']['import']['loaded']['ranks']);

        if (count(explode(' ', $taxon)) > 1) {

            foreach ($ranks as $rank) {
                if (stripos($taxon, $rank) === 0) {
                    $taxon = str_ireplace($rank, '', $taxon);
                    break;
                }
            }
        }
        return trim($taxon);
    }


    private function extractLinkedSpeciesRatherThanDisplayed ($whatever)
    {
        return trim(preg_replace('/(\[m\](.*)\[\/m\])|(\[t\](.*)\[\/t\])|(\[r\]|\[l\]|\[p\]|\[\/r\]|\[\/l\]|\[\/p\])/', '', trim($whatever)));
    }


    private function addSpecies ($species, $ranks)
    {
        $d = array();

        foreach ((array) $species as $key => $val) {

            if (!isset($ranks[$val['rank_name']]['id'])) {

                $_SESSION['admin']['system']['import']['species-errors'][] = array(
                    'taxon' => $val['taxon'],
                    'cause' => 'No or illegal rank (' . $val['rank_name'] . ')'
                );
            }
            else {

                $res = $this->models->Taxa->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'taxon' => $val['taxon'],
                    'parent_id' => 'null',
                    'rank_id' => $ranks[$val['rank_name']]['id'],
                    'taxon_order' => ($this->_tempTeller++),
                    'is_hybrid' => 0,
                    'list_level' => 0
                ));

				//echo $this->_tempTeller.' '.$val['taxon'].'<br />';

                $val['id'] = $this->models->Taxa->getNewId();
                $d[$key] = $val;

            }
        }

        if (!isset($d))
            return null;

        foreach ((array) $d as $key => $val) {

            $t = $this->models->Taxa->_get(
            array(
                'id' => array(
                    'project_id' => $this->getNewProjectId(),
                    'taxon' => $val['parent'],
                    'rank_id' => $_SESSION['admin']['system']['import']['loaded']['ranks'][$val['parent_rank_name']]['id']
                ),
                'columns' => 'id'
            ));

            if ($t) {

                if ($t[0]['id'] == $val['id']) {

                    $_SESSION['admin']['system']['import']['species-errors'][] = array(
                        'taxon' => $d[$key]['taxon'],
                        'cause' => 'saved as orphan, defined as being its own parent.'
                    );
                }
                else {

                    $this->models->Taxa->save(
                    array(
                        'id' => $val['id'],
                        'project_id' => $this->getNewProjectId(),
                        'parent_id' => $t[0]['id']
                    ));

                    $d[$key]['parent_id'] = $t[0]['id'];
                }
            }
        }

        return $d;
    }


    private function assignTopSpeciesToUser ($species)
    {
        foreach ((array) $species as $key => $val) {

            if (isset($val['parent_id']))
                continue;

            $this->models->UsersTaxa->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'user_id' => $this->getCurrentUserId(),
                'taxon_id' => $val['id']
            ));
        }
    }


    private function checkTreeTops ($d)
    {
        $treetops = false;

        foreach ((array) $d as $key => $val) {

            if ($val['parent'] == '') {

                $treetops[] = $val;
            }
        }

        return $treetops;
    }


    private function fixTreetops ($species, $treetops)
    {
        if (count((array) $treetops) < 2)
            return;

		// rank_id 1 = domain or empire
        $d = $this->addProjectRank('(master rank)', array(
            'rank_id' => 1,
            'parent_id' => null
        ));

        $this->models->ProjectsRanks->update(array(
            'parent_id' => $d['id']
        ), array(
            'id !=' => $d['id'],
            'project_id' => $this->getNewProjectId(),
            'parent_id' => 'null'
        ));

        $this->models->Taxa->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'taxon' => '(master taxon)',
            'parent_id' => 'null',
            'rank_id' => $d['id'],
            'taxon_order' => 0,
            'is_hybrid' => 0,
            'list_level' => 0
        ));

        $masterId = $this->models->Taxa->getNewId();

        foreach ((array) $species as $key => $val) {

            if (in_array($val['taxon'], $treetops)) {

                $this->models->Taxa->save(array(
                    'id' => $val['id'],
                    'project_id' => $this->getNewProjectId(),
                    'parent_id' => $masterId
                ));

                $species[$key]['parent_id'] = $masterId;
            }
        }

        return $species;
    }


    private function createCategory ($name='Description',$showOrder=0)
    {
        $pt = $this->models->PagesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId(),
                'page' => $name
            ),
            'columns' => 'id'
        ));

        if (isset($pt[0]['id']))
            return $pt[0]['id'];

        $pt = $this->models->PagesTaxa->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'page' => $name,
            'show_order' => $showOrder,
            'def_page' => ($name=='Description')
        ));

        $id = $this->models->PagesTaxa->getNewId();

        $this->models->PagesTaxaTitles->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'page_id' => $id,
            'language_id' => $this->getNewDefaultLanguageId(),
            'title' => $name
        ));

        return $id;
    }


    private function addSpeciesContent ($taxon)
    {
        $indexName = $this->makeIndexName((string) $taxon->name, (string) $taxon->taxon);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

            $content = trim((string) $taxon->description);

            $this->models->ContentTaxa->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'taxon_id' => $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'],
                'language_id' => $this->getNewDefaultLanguageId(),
                'page_id' => $_SESSION['admin']['system']['import']['speciesOverviewCatId'],
                'content' => $content,
                'publish' => 1
            ));


            if (isset($taxon->syn_vern_description)) {

				$this->models->ContentTaxa->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'],
					'language_id' => $this->getNewDefaultLanguageId(),
					'page_id' => $_SESSION['admin']['system']['import']['speciesNomenclatureCatId'],
					'content' => $taxon->syn_vern_description,
					'publish' => 1
				));

			}


            if (!empty($content)) {
                $this->models->Taxa->update(array(
                    'is_empty' => 0
                ), array(
                    'id' => $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'],
                    'project_id' => $this->getNewProjectId()
                ));
            }

            $_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']++;
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'][] = array(
                'data' => $taxon,
                'cause' => 'Unable to resolve name "' . trim((string) $taxon->name) . '" to taxon id.'
            );
        }
    }


    private function addSpeciesMedia ($taxon)
    {
        $indexName = $this->makeIndexName((string) $taxon->name, (string) $taxon->taxon);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

            $taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];

            $overviewFileName = strtolower(trim((string) $taxon->multimedia->overview));

            $imageCount = 0;

            if (!empty($overviewFileName)) {

                $r = $this->doAddSpeciesMedia($taxonId, $overviewFileName, null, null, true, $imageCount++);

                if ($r['saved'] == true) {

                    if (isset($r['full_path']))
                        $this->cRename($r['full_path'], $_SESSION['admin']['system']['import']['paths']['project_media'] . strtolower($r['filename']));
                    if (isset($r['thumb_path']))
                        $this->cRename($r['thumb_path'], $_SESSION['admin']['system']['import']['paths']['project_thumbs'] . strtolower($r['filename']));

                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved']++;
                }
                else {

                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'][] = $r;
                }
            }

            foreach ($taxon->multimedia->multimediafile as $vKey => $vVal) {

                $fileName = strtolower(trim((string) $vVal->filename));

                if (empty($fileName))
                    continue;
                if ($fileName == $overviewFileName)
                    continue;

                $r = $this->doAddSpeciesMedia($taxonId, $fileName, isset($vVal->fullname) ? ((string) $vVal->fullname) : null, isset($vVal->caption) ? trim((string) $vVal->caption) : null, false, $imageCount++);

                if ($r['saved'] == true) {

                    if (isset($r['full_path']))
                        $this->cRename($r['full_path'], $_SESSION['admin']['system']['import']['paths']['project_media'] . strtolower($r['filename']));
                    if (isset($r['thumb_path']))
                        $this->cRename($r['thumb_path'], $_SESSION['admin']['system']['import']['paths']['project_thumbs'] . strtolower($r['filename']));

                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved']++;
                }
                else {

                    $_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'][] = $r;
                }
            }
        }
    }


    private function doAddSpeciesMedia ($taxonId, $fileName, $fullName, $caption, $isOverviewPicture, $sortOrder)
    {
        if ($_SESSION['admin']['system']['import']['imagePath'] == false)
            return array(
                'saved' => false,
                'data' => $fileName,
                'cause' => 'User specified no media import for project'
            );

        if (empty($fileName))
            return array(
                'saved' => false,
                'data' => '',
                'cause' => 'Missing file name'
            );

        if (file_exists($_SESSION['admin']['system']['import']['imagePath'] . $fileName)) {

            //$thisMIME = $this->helpers->FileUploadHelper->getMimeType($_SESSION['admin']['system']['import']['imagePath'].$fileName);
            $thisMIME = $this->mimeContentType($_SESSION['admin']['system']['import']['imagePath'] . $fileName);

            if (isset($_SESSION['admin']['system']['import']['mimes'][$thisMIME])) {

                if ($_SESSION['admin']['system']['import']['thumbsPath'] == false)
                    $thumbName = null;
                else
                    $thumbName = file_exists($_SESSION['admin']['system']['import']['thumbsPath'] . $fileName) ? $fileName : null;

                $this->models->MediaTaxon->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'taxon_id' => $taxonId,
                    'file_name' => $fileName,
                    'thumb_name' => $thumbName,
                    'original_name' => $fullName,
                    'mime_type' => $thisMIME,
                    'file_size' => filesize($_SESSION['admin']['system']['import']['imagePath'] . $fileName),
                    'overview_image' => ($isOverviewPicture ? 1 : 0),
                    'sort_order' => $sortOrder
                ));

                if (!empty($caption)) {

                    $this->models->MediaDescriptionsTaxon->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'language_id' => $this->getNewDefaultLanguageId(),
                        'media_id' => $this->models->MediaTaxon->getNewId(),
                        'description' => $caption
                    ));
                }

                return array(
                    'saved' => true,
                    'filename' => $fileName,
                    'full_path' => $_SESSION['admin']['system']['import']['imagePath'] . $fileName,
                    'thumb' => isset($thumbName) ? $thumbName : null,
                    'thumb_path' => isset($thumbName) ? $_SESSION['admin']['system']['import']['thumbsPath'] . $thumbName : null
                );
            }
            else {

                return array(
                    'saved' => false,
                    'data' => $fileName,
                    'cause' => isset($thisMIME) ? 'MIME-type "' . $thisMIME . '" not allowed' : 'Could not determine MIME-type'
                );
            }
        }
        else {

            return array(
                'saved' => false,
                'data' => $fileName,
                'cause' => 'File "' . $fileName . '" does not exist'
            );
        }
    }


    private function addSpeciesCommonNames ($taxon)
    {
        $indexName = $this->makeIndexName((string) $taxon->name, (string) $taxon->taxon);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

            $taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];

            if (isset($taxon->vernaculars->vernacular)) {

                foreach ($taxon->vernaculars->vernacular as $vKey => $vVal) {

					$lang=trim((string) $vVal->language);

                    $langId = $this->resolveLanguage($lang);

                    if (!$langId) {
						$langId = $this->addLanguage($lang);
						$this->addMessage('Added language "'.$lang.'"');
					}

					$this->models->Commonnames->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'language_id' => $langId,
						'commonname' => trim((string) $vVal->name)
					));

					$_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved']++;
                }
            }
        }
    }


    private function getSynonymAuthor ($synonym, $synVernDescription)
    {
        foreach ($synVernDescription as $line) {

            // Synonym should be at start of line; if not continue
            if (strpos(trim($line), $synonym) !== 0)
                continue;

                // No links in resulting string
            if (false === strpos($line, '[l]')) {
                $author = trim(str_replace($synonym, '', $line));

                // Links present, remove these
            }
            else {
                $author = trim(str_replace($synonym, '', $this->removeLinks($line)));
            }

            // Clean up any starting or closing characters
            $cleanUp = array(
                ':',
                '|',
                '.',
                ','
            );
            if (in_array($author[0], $cleanUp))
                $author = trim(substr($author, 1));
            if (in_array($author[strlen($author) - 1], $cleanUp))
                $author = trim(substr($author, 0, - 1));

            return $author;
        }
    }


    private function addSpeciesSynonyms($taxon)
    {
        $indexName = $this->makeIndexName((string) $taxon->name, (string) $taxon->taxon);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

            $taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];

            $i = 0;

            if (isset($taxon->syn_vern_description)) {

                $synVernDescription = $this->prepareSynVernDescription($taxon->syn_vern_description);
            }

            if (isset($taxon->synonyms->synonym)) {

                foreach ($taxon->synonyms->synonym as $vKey => $vVal) {

                    $synonym = trim((string) $vVal->name);

                    $res = $this->models->Synonyms->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'taxon_id' => $taxonId,
                        'synonym' => $synonym,
                        'author' => isset($taxon->syn_vern_description) ? $this->getSynonymAuthor($synonym, $synVernDescription) : null,
                        'show_order' => $i++
                    ));

                    if ($res === true)
                        $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved']++;
                    else
                        $_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'][] = array(
                            'data' => trim((string) $taxon->name),
                            'cause' => 'Unable to save synonym "' . trim((string) $vVal->name) . '" (' . $res . ').'
                        );
                }
            }
        }

    }


    private function prepareSynVernDescription ($text)
    {
        $delete = array(
            '[p]',
            '[/p]',
            '[b]',
            '[/b]',
            '[i]',
            '[/i]',
            '[u]',
            '[/u]'
        );
        $text = str_replace($delete, '', $text);

        return explode('[br]', $text);
    }


    private function removeLinks ($line)
    {

        // Add counter for safety check to prevent endless loops; max 10 hits
        $i = 0;

        while (false !== strpos($line, '[l]') || $i < 10) {

            $i++;

            preg_match('/(\[l\]).*?(\[\/l\])/', $line, $matches);
            $link = $matches[0];

            preg_match('/(\[t\]).*(\[\/t\])/', $link, $matches);
            $author = substr($matches[0], 3, -4);

            $line = str_replace($link, $author, $line);
        }

        return $line;
    }


	private function extractSuffix($y)
	{

		// there is no suffix
		if (is_numeric($y)) {

			return array($y,null);

		} else {

			//might be 1991c
			$f = substr($y, -1);
			$y2 = substr($y, 0, -1);

			if (!is_numeric($y2)) {

				//should be 1991ab
				$f = substr($y, -2);
				$y2 = substr($y, 0, -2);

				//not even that!? you're on your own
				if (!is_numeric($y2))
					$f = null;
				else
					$y = $y2;
			}
			else {

				$y = $y2;
			}

			return array($y,$f);

		}

	}


    // literature & glossary
    private function fixAuthors ($s)
    {

        // Antezana et al., 1976b-1978c
        $d = strrpos($s, ','); // comma position
        $y = str_replace(' ','',substr($s, $d + 1)); // year(s), all spaces removed
        $a = substr($s, 0, $d); // everything but the year(s) = author(s)
        $a2 = null; // default: no 2nd author
        $m = false; // defualt: no multiple authors (>2 = 'et al.')
        $d = strpos($a, 'et al.'); // 'et. al' position

        if ($d !== false) {
            $a = trim(substr($a, 0, $d));
            $m = true;
        }
        else {
            $d = strpos($a, ' and ');
            if ($d !== false) {
                $a2 = trim(substr($a, $d + strlen(' and ')));
                $a = trim(substr($a, 0, $d));
            }
        }

        $f = $f2 = $y2 = $separator = null;

        if (!is_numeric($y)) {
			$separator=false;
			if (strpos($y,'&amp;')!==false) {
				$separator='&amp;';
			} else
			if (strpos($y,'&')!==false) {
				$separator='&';
			} else
			if (strpos($y,'-')!==false) {
				$separator='-';
			}
			if ($separator) {
				$boom=explode($separator,$y);
				$d = $this->extractSuffix($boom[0]);
				$y = $d[0];
				$f = $d[1];
				$d = $this->extractSuffix($boom[1]);
				$y2 = $d[0];
				$f2 = $d[1];
				if ($separator=='&amp;') $separator='&';
			} else {
				$d = $this->extractSuffix($y);
				$y = $d[0];
				$f = $d[1];
				$separator=null;
			}

        }

        return array(
            'year' => $y,
            'valid_year' => is_numeric($y),
            'year_2' => $y2,
			'separator' => $separator,
            'suffix' => $f,
            'suffix_2' => $f2,
            'author_1' => $a,
            'author_2' => $a2,
            'multiple_authors' => $m,
            'original' => $s
        );
    }


    private function resolveLiterature ($obj)
    {
        $lit = $this->fixAuthors(trim((string) $obj->literature_title));

        $lit['text'] = trim((string) $obj->fullreference);

        $okSp = $unSp = null;

        if ($obj->keywords->keyword) {

            foreach ($obj->keywords->keyword as $kKey => $kVal) {

                // apparently we're skipping literature that is not related to species or higher taxa
                if (preg_match('/\[m\](Species|Higher taxa)\[\/m\]/i', trim((string) $kVal->name)) == 0)
                    continue;

                    //[p][l][m]Species[/m][r]Emys orbicularis subsp. hellenica[/r][t][i]Emys orbicularis hellenica[/i][/t][/l][/p]
                $speciesName = $this->makeIndexName($this->extractLinkedSpeciesRatherThanDisplayed((string) $kVal->name));

                if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$speciesName])) {

                    $okSp[] = $_SESSION['admin']['system']['import']['loaded']['species'][$speciesName]['id'];
                }
                else {

                    $unSp[] = $speciesName;
                }
            }
        }

        $lit['references'] = array(
            'species' => $okSp,
            'unknown_species' => $unSp
        );

        return $lit;
    }


    private function addLiterature ($obj)
    {
        $lit = $this->resolveLiterature($obj);

        $res = $this->models->Literature->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'author_first' => isset($lit['author_1']) ? $lit['author_1'] : null,
            'author_second' => (isset($lit['author_2']) && $lit['multiple_authors'] == false) ? $lit['author_2'] : null,
            'multiple_authors' => $lit['multiple_authors'] == true ? 1 : 0,
            'year' => (isset($lit['year']) && $lit['valid_year'] == true) ? $lit['year'] : '0000',
            'suffix' => isset($lit['suffix']) ? $lit['suffix'] : null,
            'text' => isset($lit['text']) ? trim($lit['text']) : null,
            'year_2' => isset($lit['year_2']) ? $lit['year_2'] : null,
            'suffix_2' => isset($lit['suffix_2']) ? $lit['suffix_2'] : null,
            'year_separator' => isset($lit['separator']) ? $lit['separator'] : null,
        ));

        if ($res === true) {

            $_SESSION['admin']['system']['import']['loaded']['literature']['saved']++;

        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['literature']['failed'][] = array(
                'data' => $lit,
                'cause' => 'Failed to save lit. ref. "' . $lit['original'] . '" (' . $res . ').'
            );

            return;
        }

        $id = $this->models->Literature->getNewId();

        $_SESSION['admin']['system']['import']['literature'][] = array(
            'id' => $id,
            'original' => $lit['original']
        );

        foreach ((array) $lit['references']['species'] as $kV) {

            if (empty($kV))
                continue;

            $this->models->LiteratureTaxa->save(array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'taxon_id' => $kV,
                'literature_id' => $id
            ));

        }

        foreach ((array) $lit['references']['unknown_species'] as $kV) {

            if (empty($kV))
                continue;

            $_SESSION['admin']['system']['import']['loaded']['literature']['failed'][] = array(
                'data' => $lit,
                'cause' => 'Saved lit. ref. "' . $lit['original'] . '" but could not resolve reference to "' . $kV . '".'
            );
        }
    }


    private function resolveGlossary ($obj)
    {
        $t = trim((string) $obj->glossary_title);
        $d = trim((string) $obj->definition);

        if ($obj->glossary_synonyms->glossary_synonym) {

            foreach ($obj->glossary_synonyms->glossary_synonym as $sKey => $sVal) {

                $s[] = trim((string) $sVal->name);
            }
        }

        if ($obj->gloss_multimedia->gloss_multimediafile) {

            foreach ($obj->gloss_multimedia->gloss_multimediafile as $mKey => $mVal) {

                $m[] = array(
                    'filename' => strtolower(trim((string) $mVal->filename)),
                    'fullname' => trim((string) $mVal->fullname),
                    'caption' => trim((string) $mVal->caption),
                    'type' => trim((string) $mVal->multimedia_type)
                );
            }
        }

        return array(
            'term' => $t,
            'definition' => $d,
            'synonyms' => isset($s) ? $s : null,
            'multimedia' => isset($m) ? $m : null
        );
    }


    private function addGlossaryMedia ($id, $data)
    {

        /*

			$data =
				array(
					'filename' => (string)$mVal->filename,
					'fullname' => (string)$mVal->fullname,
					'caption' => (string)$mVal->caption,
					'type' => (string)$mVal->multimedia_type,
				);

		*/
        if ($_SESSION['admin']['system']['import']['imagePath'] == false)
            return array(
                'saved' => 'skipped',
                'data' => $data['filename'],
                'cause' => 'User specified no media import for project'
            );

        $fileToImport = $_SESSION['admin']['system']['import']['imagePath'] . $data['filename'];

        if (file_exists($fileToImport)) {

            $thisMIME = $this->mimeContentType($fileToImport);

            if (isset($_SESSION['admin']['system']['import']['mimes'][$thisMIME])) {

                if ($_SESSION['admin']['system']['import']['thumbsPath'] == false)
                    $thumbName = null;
                else
                    $thumbName = file_exists($_SESSION['admin']['system']['import']['thumbsPath'] . $data['filename']) ? $data['filename'] : null;

                if ($this->models->GlossaryMedia->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'glossary_id' => $id,
                    'file_name' => $data['filename'],
                    'original_name' => $data['fullname'],
                    'mime_type' => $thisMIME,
                    'file_size' => filesize($fileToImport),
                    'thumb_name' => ($thumbName ? $thumbName : null),
                    'fullname' => isset($data['fullname']) ? $data['fullname'] : null
                ))) {

                    $newId = $this->models->GlossaryMedia->getNewId();

                    if (!empty($data['caption'])) {

                        $this->models->GlossaryMediaCaptions->save(
                        array(
                            'id' => null,
                            'project_id' => $this->getNewProjectId(),
                            'language_id' => $this->getNewDefaultLanguageId(),
                            'media_id' => $newId,
                            'caption' => isset($data['caption']) ? $data['caption'] : null
                        ));
                    }

                    return array(
                        'saved' => true,
                        'id' => $newId,
                        'filename' => $data['filename'],
                        'full_path' => $fileToImport,
                        'thumb' => isset($thumbName) ? $thumbName : null,
                        'thumb_path' => isset($thumbName) ? $_SESSION['admin']['system']['import']['thumbsPath'] . $thumbName : null
                    );
                }
                else {

                    return array(
                        'saved' => false,
                        'data' => $data['filename'],
                        'cause' => 'query failed (' . $this->models->GlossaryMedia->getLastQuery() . ')'
                    );
                }
            }
            else {

                return array(
                    'saved' => false,
                    'data' => $data['filename'],
                    'cause' => isset($thisMIME) ? 'MIME-type "' . $thisMIME . '" not allowed' : 'Could not determine MIME-type'
                );
            }
        }
        else {

            return array(
                'saved' => false,
                'data' => $data['filename'],
                'cause' => 'File "' . $data['filename'] . '" does not exist'
            );
        }
    }


    private function addGlossary ($obj)
    {
        $gls = $this->resolveGlossary($obj);

        $res = $this->models->Glossary->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'language_id' => $this->getNewDefaultLanguageId(),
            'term' => isset($gls['term']) ? $gls['term'] : null,
            'definition' => isset($gls['definition']) ? trim($gls['definition']) : null
        ));

        if ($res === true) {

            $_SESSION['admin']['system']['import']['loaded']['glossary']['saved']++;
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['glossary']['failed'][] = array(
                'data' => $gls,
                'cause' => 'Failed to save glossary item "' . $gls['term'] . '" (' . $res . ').'
            );
            return;
        }

        $id = $this->models->Glossary->getNewId();

        $_SESSION['admin']['system']['import']['glossary'][] = array(
            'id' => $id,
            'term' => $gls['term']
        );

        if (isset($gls['synonyms'])) {

            foreach ((array) $gls['synonyms'] as $sVal) {

                $lt = $this->models->GlossarySynonyms->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'language_id' => $this->getNewDefaultLanguageId(),
                    'glossary_id' => $id,
                    'synonym' => $sVal
                ));
            }
        }

        if (isset($gls['multimedia'])) {

            $paths = $this->makePathNames($this->getNewProjectId());

            foreach ((array) $gls['multimedia'] as $mVal) {

                $r = $this->addGlossaryMedia($id, $mVal);

                if ($r['saved'] == true) {

                    if (isset($r['full_path']))
                        $this->cRename($r['full_path'], $paths['project_media'] . strtolower($r['filename']));
                    if (isset($r['thumb_path']))
                        $this->cRename($r['thumb_path'], $paths['project_thumbs'] . strtolower($r['filename']));
                }
                else if ($r['saved'] !== 'skipped') {

                    $_SESSION['admin']['system']['import']['loaded']['glossary']['failed'][] = array(
                        'data' => $gls,
                        'cause' => $gls['term'].': could not save "' . $mVal['filename'] . '" (' . $r['cause'] . ').'
                    );
                }
            }
        }
    }

    // content & introduction
    private function addWelcomeTexts ($obj)
    {
        if (!empty($obj->projectintroduction)) {

            $res = $this->models->Content->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'language_id' => $this->getNewDefaultLanguageId(),
                'subject' => 'Welcome',
                'content' => trim((string) $obj->projectintroduction)
            ));

            if ($res === true) {

                $_SESSION['admin']['system']['import']['loaded']['welcome']['saved'][] = 'Saved welcome text.';
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'Failed to save welcome text (' . $res . ').';
            }
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'No welcome text found.';
        }

        if (!empty($obj->contributors)) {

            $res = $this->models->Content->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'language_id' => $this->getNewDefaultLanguageId(),
                'subject' => 'Contributors',
                'content' => trim((string) $obj->contributors)
            ));

            if ($res === true) {

                $_SESSION['admin']['system']['import']['loaded']['welcome']['saved'][] = 'Saved contributors text.';
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'Failed to save contributors text (' . $res . ').';
            }
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'No contributors text found.';
        }
    }


    private function addIntroduction ($obj)
    {
        $this->models->IntroductionPages->save(
        array(
            'project_id' => $this->getNewProjectId(),
            'show_order' => $_SESSION['admin']['system']['import']['loaded']['introduction']['show_order']++,
            'got_content' => '1'
        ));

        $id = $this->models->IntroductionPages->getNewId();

        $res = $this->models->ContentIntroduction->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'language_id' => $this->getNewDefaultLanguageId(),
            'page_id' => $id,
            'topic' => trim((string) $obj->introduction_title),
            'content' => trim((string) $obj->text)
        ));

        if ($res === true) {

            $_SESSION['admin']['system']['import']['loaded']['introduction']['saved'][] = 'Saved topic "' . trim((string) $obj->introduction_title) . '".';
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['introduction']['failed'][] = 'Failed to save topic "' . trim((string) $obj->introduction_title) . '" (' . $res . ').';
            return;
        }

        $img = strtolower(trim((string) $obj->overview));

        if ($_SESSION['admin']['system']['import']['imagePath'] && $img) {

            $paths = $_SESSION['admin']['system']['import']['paths'];

            if ($this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $img, $paths['project_media'] . $img)) {

                $this->models->IntroductionMedia->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'page_id' => $id,
                    'file_name' => $img,
                    'original_name' => $img,
                    'mime_type' => @$this->mimeContentType($img),
                    'file_size' => @filesize($paths['project_media'] . $img),
                    'thumb_name' => null
                ));
            }
        }
    }


    // dichotomous key
    private function createKeyStep ($step, $stepIds, $stepAdd = 0)
    {
        $paths = $_SESSION['admin']['system']['import']['paths'];

        $fileName = isset($step->keyoverviewpicture) ? strtolower(trim((string) $step->keyoverviewpicture)) : null;

        if ($fileName)
            $this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $fileName, $paths['project_media'] . strtolower($fileName));


        $k = $this->models->Keysteps->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'number' => ($step == 'god' ? -1 : ((int)trim((string) $step->pagenumber) + $stepAdd)),
            'is_start' => 0,
            'image' => $fileName
        ));

        $stepId = $stepIds[($step == 'god' ? -1 : trim((string) $step->pagenumber))] =
            $this->models->Keysteps->getNewId();

        $this->models->ContentKeysteps->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'keystep_id' => $stepId,
            'language_id' => $this->getNewDefaultLanguageId(),
            'title' => ($step == 'god' ? 'Choose key type' : (isset($step->pagetitle) ? trim((string) $step->pagetitle) : trim((string) $step->pagenumber))),
            'content' => ($step == 'god' ? 'Choose between picture key and text key' : (isset($step->pagetitle) ? trim((string) $step->pagetitle) : trim((string) $step->pagenumber)))
        ));

        //[l][m]Text Key[/m][r]Pagina 2218: Salviniaceae - Vlotvarenfamilie[/r][t]Pagina 2218: [b]Salviniaceae - Vlotvarenfamilie[/b][/t][/l]
        //[l][m]Text Key[/m][r]Pagina 1234[/r][t]Pagina 1234[/t][/l]
        //[l][m]Text Key[/m][r]Page 23: Ursidae[/r][t]Page 23: [b]Ursidae[/b][/t][/l]
        //[l][m]Text Key[/m][r]Page 66[/r][t]Page 66[/t][/l]



        $d = (isset($step->pagetitle) ? trim((string) $step->pagenumber) . ': ' . trim((string) $step->pagetitle) : trim((string) $step->pagenumber));

        $_SESSION['admin']['system']['import']['key'][] = array(
            'id' => $stepId,
            'page' => strtolower('Pagina ' . $d)
        );
        $_SESSION['admin']['system']['import']['key'][] = array(
            'id' => $stepId,
            'page' => strtolower('Page ' . $d)
        );

        return $stepIds;
    }


    private function createKeyStepChoices ($step, $stepIds)
    {
        $paths = $_SESSION['admin']['system']['import']['paths'];

        if ($step->text_choice) {
            $choices = $step->text_choice;
        }
        else {
            $choices = $step->pict_choice;
        }

        $i = 0;

        foreach ($step as $key => $val) {

            if ($key == 'text_choice' || $key == 'pict_choice') {

                $resStep = ((trim((string) $val->destinationtype) == 'turn' || trim((string) $val->destinationtype) == 'jump') ? (isset($stepIds[trim((string) $val->destinationpagenumber)]) ? $stepIds[trim(
                (string) $val->destinationpagenumber)] : null) : null);

                /*

					in l2-xml files, nearly all relevant taxon-info is stored IN the taxon tree, which allows
					for access to all data of a taxon under consideration: name, rank, parent etc.

					the exception are the dichotomous keys, pict_key and text_key. at their endpoints, they
					have a field called 'destinationtaxonname', which contains the taxon name (in case of species)
					or the rank name plus the taxon name (in case of higher taxa). there is *no* separate field
					that holds just the rank. we therefore cannot use $this->makeIndexName() to create an
					accurate index name with which to lookup the correct species in the loaded species array.

					for species, this is not a problem, but in the case of higher taxa, it is implicitly assumed
					that they are ALWAYS listed in this field as rank plus name ('Family Cynipidae' rather than
					'Cynipidae'). if ever an export file is used in which the destinationtaxonname-field lacks
					the prefixed rank in the case of higher taxa, this function will fail to resolve those taxa.

				*/
                $destinationtaxonname = strtolower($this->cleanL2Name(trim((string) $val->destinationtaxonname)));

                $resTaxon = (trim((string) $val->destinationtype) == 'taxon' ? (isset($_SESSION['admin']['system']['import']['loaded']['species'][$destinationtaxonname]['id']) ? $_SESSION['admin']['system']['import']['loaded']['species'][$destinationtaxonname]['id'] : null) : null);

                $fileName = isset($val->picturefilename) ? strtolower(trim((string) $val->picturefilename)) : null;

                if ($fileName && !file_exists($_SESSION['admin']['system']['import']['imagePath'] . $fileName)) {

                    $error[] = array(
                        'cause' => 'Picture key image "' . $fileName . '" does not exist (choice created anyway)'
                    );

                    $fileName = null;
                }
                else if ($fileName) {

                    $this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $fileName, $paths['project_media'] . $fileName);
                }

                if (isset($val->leftpos) || isset($val->toppos) || isset($val->width) || isset($val->height)) {

                    $params = json_encode(
                    array(
                        'leftpos' => isset($val->leftpos) ? trim($val->leftpos) : null,
                        'toppos' => isset($val->toppos) ? trim($val->toppos) : null,
                        'width' => isset($val->width) ? trim($val->width) : null,
                        'height' => isset($val->height) ? trim($val->height) : null
                    ));
                }

                if (isset($val->captiontext)) {

                    $txt = trim((string) $val->captiontext);
                    $p = trim((string) $step->pagenumber) . trim((string) $val->choiceletter) . '.';
                    if (substr($txt, 0, strlen($p)) == $p)
                        $txt = trim(substr($txt, strlen($p)));
                    if (strlen($txt) == 0)
                        $txt = trim((string) $val->captiontext);
                }
                else if (isset($val->picturefilename)) {

                    $txt = trim((string) $val->picturefilename);
                }

                $stepId = ($step == 'god' ? $stepIds['godId'] : $stepIds[trim((string) $step->pagenumber)]);

                $this->models->ChoicesKeysteps->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'keystep_id' => $stepId,
                    'show_order' => (1 + $i++),
                    'choice_img' => isset($fileName) ? $fileName : null,
                    'choice_image_params' => isset($params) ? $params : null,
                    'res_keystep_id' => $resStep,
                    'res_taxon_id' => $resTaxon
                ));

                $choiceId = $this->models->ChoicesKeysteps->getNewId();

                $this->models->ChoicesContentKeysteps->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'choice_id' => $choiceId,
                    'language_id' => $this->getNewDefaultLanguageId(),
                    'choice_txt' => isset($txt) ? $txt : null
                ));

                if (is_null($resTaxon) && is_null($resStep)) {

                    $d = trim((string) $val->destinationtype);
                    $t = empty($d) ? '-' : $d;
                    $d = trim((string) $val->destinationpagenumber);
                    $p = empty($d) ? '-' : $d;
                    $d = trim((string) $val->destinationtaxonname);
                    $x = empty($d) ? '-' : $d;

                    $_SESSION['admin']['system']['import']['loaded']['key_dich']['failed'][] = 'Could not resolve target of "' . substr($this->replaceOldMarkUp(trim((string) $val->captiontext), true), 0, 25) . '..." ' . '(' .
                     ($t == 'turn' ? 'turn: ' . $p : ($t == 'taxon' ? 'taxon: ' . $x : 'unknown targettype: ' . $t)) . ')';
                }
            }
        }
    }


    private function addKeyDichotomous ($obj, $node)
    {
        $stepAdd = 0;

        if ($node == 'text_key' && count($obj->keypage) > 1) {

            $keyStepIds = null;

            // text key
            // create steps first (no choices yet)
            foreach ($obj->keypage as $key => $val) {

                $keyStepIds = $this->createKeyStep($val, $keyStepIds);
                if ($key == 0)
                    $firstTxtStepId = current($keyStepIds);
            }

            // create choices
            foreach ($obj->keypage as $key => $val) {

                $this->createKeyStepChoices($val, $keyStepIds);
            }

            $k = $this->models->Keysteps->_get(array(
                'id' => array(
                    'project_id' => $this->getNewProjectId()
                ),
                'columns' => 'max(number) as `last`'
            ));

            $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['text_key'] = $firstTxtStepId;
            $_SESSION['admin']['system']['import']['loaded']['key_dich']['keyStepIds'] = $keyStepIds;
            $_SESSION['admin']['system']['import']['loaded']['key_dich']['stepAdd'] = $k[0]['last'];
        }

        if ($node == 'pict_key' && count($obj->keypage) > 1) {

            $keyStepIds = $_SESSION['admin']['system']['import']['loaded']['key_dich']['keyStepIds'];
            $stepAdd = $_SESSION['admin']['system']['import']['loaded']['key_dich']['stepAdd'];

            $pictStepIds = null;

            // pict key
            // create steps first (no choices yet)
            foreach ($obj->keypage as $key => $val) {

                $pictStepIds = $this->createKeyStep($val, $pictStepIds, $stepAdd);
                if ($key == 0)
                    $firstPictStepId = current($pictStepIds);
            }

            // create choices
            foreach ($obj->keypage as $key => $val) {

                $this->createKeyStepChoices($val, $pictStepIds);
            }

            $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key'] = $firstPictStepId;
        }

        if ($_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['text_key'] !== false && $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key'] !== false) {

            $keyStepIds = $this->createKeyStep('god', $keyStepIds);

            $firstTxtStepId = $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['text_key'];
            $firstPictStepId = $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key'];

            end($keyStepIds);

            $this->models->ChoicesKeysteps->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'keystep_id' => current($keyStepIds),
                'show_order' => 1,
                'res_keystep_id' => $firstTxtStepId
            ));

            $this->models->ChoicesContentKeysteps->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'choice_id' => $this->models->ChoicesKeysteps->getNewId(),
                'language_id' => $this->getNewDefaultLanguageId(),
                'choice_txt' => 'Text key'
            ));

            $this->models->ChoicesKeysteps->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'keystep_id' => current($keyStepIds),
                'show_order' => 2,
                'res_keystep_id' => $firstPictStepId
            ));

            $this->models->ChoicesContentKeysteps->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'choice_id' => $this->models->ChoiceKeystep->getNewId(),
                'language_id' => $this->getNewDefaultLanguageId(),
                'choice_txt' => 'Picture key'
            ));

            $this->models->Keysteps->update(array(
                'number' => 'number+1'
            ), array(
                'project_id' => $this->getNewProjectId(),
                'number >=' => '1'
            ));

            $this->models->Keysteps->update(array(
                'number' => '1'
            ), array(
                'project_id' => $this->getNewProjectId(),
                'number =' => '-1'
            ));
        }

        $this->models->Keysteps->update(array(
            'is_start' => '1'
        ), array(
            'project_id' => $this->getNewProjectId(),
            'number =' => '1'
        ));
    }


    // matrix key
    private function resolveMatrices ($obj)
    {
        $matrixname = !empty($obj->identify->id_file->filename) ? trim((string) $obj->identify->id_file->filename) : null;

        if ($matrixname) {

            //?? (string)$obj->identify->id_file->obj_link

            $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['name'] = str_ireplace('.adm', '', $matrixname);

			$chars = $this->resolveCharacterElement($obj->identify->id_file);

            foreach ($chars as $char) {

                //character_type ?? welke mogelijkheden + resolvement: Text

				$this->_sawModule = true;

                $charname = trim((string) $char->character_name);
                $chartype = trim((string) $char->character_type);

                foreach ($char->states->state as $stat) {

                    $adHocIndex = $this->createAdHocIndex($obj->identify->id_file->filename,$char,$stat);

                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['charname'] = $charname;
                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['chartype'] = $chartype;
                    $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['states'][$adHocIndex] = array(
                        'statename' => trim((string) $stat->state_name),
                        'statemin' => trim((string) $stat->state_min),
                        'statemax' => trim((string) $stat->state_max),
                        'statemean' => trim((string) $stat->state_mean),
                        'statesd' => trim((string) $stat->state_sd),
                        'statefile' => strtolower(trim((string) $stat->state_file))
                    );
                }
            }
        }

    }


    private function resolveCharType ($t)
    {

		//echo ':'.$t.':';

        // ??? HAVE ONLY SEEN Long Text, Text & Picture & sound
        switch ($t) {
            case 'Long Text':
            case 'Text':
                return 'text';
                break;
            case 'Distribution':
                return 'distribution';
                break;
            case 'Picture':
            case 'Sound':
                return 'media';
                break;
            case 'Range':
                return 'range';
                break;
        }
    }


    private function saveMatrices ($m)
    {
        $paths =
			isset($_SESSION['admin']['system']['import']['paths']) ?
				$_SESSION['admin']['system']['import']['paths'] :
				$this->makePathNames($this->getNewProjectId());

        $d = $error = null;

        foreach ((array) $m as $key => $val) {

            $this->models->Matrices->save(array(
                'id' => null,
                'project_id' => $this->getNewProjectId()
            ));


            $m[$key]['id'] = $this->models->Matrices->getNewId();

            $this->models->MatricesNames->save(
            array(
                'id' => null,
                'project_id' => $this->getNewProjectId(),
                'matrix_id' => $m[$key]['id'],
                'language_id' => $this->getNewDefaultLanguageId(),
                'name' => $val['name']
            ));

            $showOrder = 0;

            foreach ((array) $val['characteristics'] as $cKey => $cVal) {

                $this->models->Characteristics->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'type' => $this->resolveCharType($cVal['chartype']),
                    'got_labels' => 1
                ));
                $m[$key]['characteristics'][$cKey]['id'] = $this->models->Characteristics->getNewId();

                $this->models->CharacteristicsLabels->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
                    'language_id' => $this->getNewDefaultLanguageId(),
                    'label' => $cVal['charname']
                ));

                $this->models->CharacteristicsMatrices->save(
                array(
                    'id' => null,
                    'project_id' => $this->getNewProjectId(),
                    'matrix_id' => $m[$key]['id'],
                    'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
                    'show_order' => $showOrder++
                ));

                foreach ((array) $cVal['states'] as $sKey => $sVal) {

                    $fileName = isset($sVal['statefile']) ? strtolower($sVal['statefile']) : null;

                    if ($fileName && !file_exists($_SESSION['admin']['system']['import']['imagePath'] . $fileName)) {

                        $error[] = array(
                            'cause' => 'Matrix state image "' . $fileName . '" does not exist (state created anyway)'
                        );

                        $fileName = null;
                    }
                    else if ($fileName) {

                        $this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $fileName, $paths['project_media'] . $fileName);
                    }

                    $this->models->CharacteristicsStates->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
                        'file_name' => $fileName,
                        'lower' => isset($sVal['statemin']) ? $sVal['statemin'] : null,
                        'upper' => isset($sVal['statemax']) ? $sVal['statemax'] : null,
                        'mean' => isset($sVal['statemean']) ? $sVal['statemean'] : null,
                        'sd' => isset($sVal['statesd']) ? $sVal['statesd'] : null,
                        'got_labels' => 1
                    ));

                    $m[$key]['characteristics'][$cKey]['states'][$sKey]['id'] =
                        $this->models->CharacteristicsStates->getNewId();

                    $d[$sKey] = array(
                        'matrix_id' => $m[$key]['id'],
                        'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
                        'state_id' => $m[$key]['characteristics'][$cKey]['states'][$sKey]['id']
                    );

                    $this->models->CharacteristicsLabelsStates->save(
                    array(
                        'id' => null,
                        'project_id' => $this->getNewProjectId(),
                        'state_id' => $m[$key]['characteristics'][$cKey]['states'][$sKey]['id'],
                        'language_id' => $this->getNewDefaultLanguageId(),
                        'label' => $sVal['statename']
                    ));
                }
            }
        }

		/*

			the matrices array is hereby reduced to the bare necessities for resolving the states to the correct id's:

			[27b2992b3873ba401ca9a84c887eb9c5] => Array
				(
					[matrix_id] => 208
					[characteristic_id] => 2920
					[state_id] => 27082
				)

			the md5-hash identifies a specific state (see createAdHocIndex())

		*/

        return array(
            'matrices' => $d,
            'failed' => $error
        );
    }


    private function connectMatrices ($obj)
    {
        $matrixname = !empty($obj->identify->id_file->filename) ? trim((string) $obj->identify->id_file->filename) : null;

        if ($matrixname) {

            $indexName = $this->makeIndexName((string) $obj->name, (string) $obj->taxon);

            if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

                $taxonid = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];

				$chars = $this->resolveCharacterElement($obj->identify->id_file);

	            foreach ($chars as $char) {

                    $charname = trim((string) $char->character_name);

                    foreach ($char->states->state as $stat) {

						$adHocIndex = $this->createAdHocIndex($obj->identify->id_file->filename,$char,$stat);

                        if (isset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex])) {

                            $this->models->MatricesTaxa->setNoKeyViolationLogging(true);

                            $this->models->MatricesTaxa->save(
                            array(
                                'id' => null,
                                'project_id' => $this->getNewProjectId(),
                                'matrix_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['matrix_id'],
                                'taxon_id' => $taxonid
                            ));

                            $this->models->MatricesTaxaStates->setNoKeyViolationLogging(true);

                            $this->models->MatricesTaxaStates->save(
                            array(
                                'id' => null,
                                'project_id' => $this->getNewProjectId(),
                                'matrix_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['matrix_id'],
                                'characteristic_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['characteristic_id'],
                                'state_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['state_id'],
                                'taxon_id' => $taxonid
                            ));
                        }
                    }
                }
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'][] = 'Species "' . trim((string) $obj->name) . '" in matrix key does not exist and has been discarded';
            }
        } // not part of any matrix
		else {

			//$_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'][] = 'Encountered empty matrix name';
			$_SESSION['admin']['system']['import']['loaded']['key_matrix']['taxaNotPresent']++;

		}

    }

    // map
    private function saveMapItemType ($type)
    {
        $this->models->GeodataTypes->save(array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'colour' => 'FFFFFF'
        ));

        $id = $this->models->GeodataTypes->getNewId();

        $this->models->GeodataTypesTitles->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'language_id' => $this->getNewDefaultLanguageId(),
            'type_id' => $id,
            'title' => $type
        ));

        return $id;
    }


    private function updateMapTypeColours ()
    {
        $d = $this->models->GeodataTypes->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        $c = array('ee0000','0033ff','00ee00','ffff00','ff66ff','990099','669999','666699','cc9966','ffcc00','008700');

        foreach ((array) $d as $key => $val) {
            $this->models->GeodataTypes->update(array(
                'colour' => $c[$key % count((array) $c)]
            ), array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId()
            ));
        }
    }


    private function doSaveMapItem ($occurrence)
    {
        if ((!$occurrence['taxonId']) || (count((array) $occurrence['nodes']) <= 2) || (!$occurrence['typeId'])) {

            $_SESSION['admin']['system']['import']['loaded']['map']['skipped']++;
            return;
        }

        // remove the last node if it is identical to the first, just in case
        if ($occurrence['nodes'][0] == $occurrence['nodes'][count((array) $occurrence['nodes']) - 1])
            array_pop($occurrence['nodes']);

            // create a string for mysql (which does require the first and last to be the same)
        $geoStr = array();
        foreach ((array) $occurrence['nodes'] as $sVal)
            $geoStr[] = $sVal[0] . ' ' . $sVal[1];
        $geoStr = implode(',', $geoStr) . ',' . $geoStr[0];

        $this->models->OccurrencesTaxa->setNoKeyViolationLogging(true);

        $res = $this->models->OccurrencesTaxa->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'taxon_id' => $occurrence['taxonId'],
            'type_id' => $occurrence['typeId'],
            'type' => 'polygon',
            'boundary' => "#GeomFromText('POLYGON((" . $geoStr . "))'," . $this->controllerSettings['SRID'] . ")",
            'boundary_nodes' => json_encode($occurrence['nodes']),
            'nodes_hash' => md5(json_encode($occurrence['nodes']))
        ));

        if ($res === true) {

            if ($occurrence['typeless'] == true) {

                $_SESSION['admin']['system']['import']['loaded']['map']['typeless'][] =
                    $occurrence['taxonId'] . ' / ' . $this->models->OccurrencesTaxa->getNewId();
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['map']['saved']++;
            }
        }
        else {

            $_SESSION['admin']['system']['import']['loaded']['map']['failed']++;
        }
    }


    private function saveL2Map ($p)
    {
        $d = $this->models->L2Maps->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'name' => $p['name'],
            'coordinates' => $p['coordinates'],
            'rows' => $p['rows'],
            'cols' => $p['cols']
        ));

        if ($d !== true)
            $this->addError('While saving L2-map: ' . $d);

        $_SESSION['admin']['system']['import']['loaded']['map']['maps'][$p['name']]['id'] =
            $this->models->L2Maps->getNewId();

        $mapImageName = $p['name'] . '.gif';

        $paths = $this->makePathNames($this->getNewProjectId());

        if ($this->cRename($_SESSION['admin']['system']['import']['imagePath'] . $mapImageName, $paths['project_media_l2_maps'] . $mapImageName)) {

            $this->addMessage('Copied L2-map "' . $p['name'] . '".');

            $this->models->L2Maps->update(array(
                'image' => $mapImageName
            ), array(
                'id' => $_SESSION['admin']['system']['import']['loaded']['map']['maps'][$p['name']]['id']
            ));
        }
        else {

            $this->addError($this->storeError('Missing map file: "' . $mapImageName . '" <br />(will still function properly if map exists in default folder).', 'Map'));
        }
    }


    private function saveLN2Cell ($p)
    {
        $d = $this->models->L2OccurrencesTaxa->save(
        array(
            'id' => null,
            'project_id' => $this->getNewProjectId(),
            'taxon_id' => $p['taxon_id'],
            'type_id' => $p['type_id'],
            'map_id' => $p['map_id'],
            'square_number' => $p['square_number'],
            //'legend' => $p['legend'],
            //'coordinates' => $p['coordinates']
        ));

        if ($d !== true)
            $this->addError('While saving L2-cell: ' . $d);
    }


    private function saveMapItem ($obj)
    {
        $indexName = $this->makeIndexName(trim((string) $obj->name), $rankName);

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'])) {

            if (!isset($obj->distribution))
                return;

			 $this->_sawModule = true;

            $taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][$indexName]['id'];

            foreach ($obj->distribution->map as $vKey => $vVal) {

                if (!isset($_SESSION['admin']['system']['import']['loaded']['map']['maps'][trim((string) $vVal->mapname)])) {

                    /*

						<mapname>North Atlantic</mapname>
						<specs>90,100,0,-80,5,5</specs>

						90,100 = linksboven = 90�N 100�W = 90 -100
						0, -80 = rechtonder = 0�S 80�E = -0 80
						5,5 - cell size (ASSUMING WxH)

					*/

                    $d = explode(',', trim((string) $vVal->specs));

                    $lat1 = (float)$d[0];
                    $lat2 = (float)$d[2];
                    $lon1 = (float)$d[1];
                    $lon2 = (float)$d[3];

                    $sqW = (float)$d[4];
                    $sqH = (float)$d[5];

                    $widthInSquares = (($lon1 >= $lon2) ? $lon1 - $lon2 : 360 + $lon1 - $lon2) / $sqW;
                    $heightInSquares = (($lat1 - $lat2) / $sqH);

                    if ($_SESSION['admin']['system']['import']['map']['amersfoort']) {

                        //620,10,300,280,10,10	= y,x,y,x,w,h
                        $af = $this->bombAmersfoort($lat1, $lon1);
                        $lat1 = (float)$af[0];
                        $lon1 = (float)$af[1];
                        $af = $this->bombAmersfoort($lat2, $lon2);
                        $lat2 = (float)$af[0];
                        $lon2 = (float)$af[1];

                        $heightInSquares = ($d[0] - $d[2]) / $d[4];
                        $widthInSquares = ($d[3] - $d[1]) / $d[5];

                        $sqH = (float)(($lat1 - $lat2) / $heightInSquares);
                        $sqW = (float)(($lon2 - $lon1) / $widthInSquares);
                    }

                    $coordinates = array(
                        'topLeft' => array(
                            'lat' => $lat1,
                            'long' => $lon1
                        ),
                        'bottomRight' => array(
                            'lat' => $lat2,
                            'long' => $lon2
                        )
                    );

                    $_SESSION['admin']['system']['import']['loaded']['map']['maps'][trim((string) $vVal->mapname)] = array(
                        'label' => trim((string) $vVal->mapname),
                        'specs' => trim((string) $vVal->specs),
                        'coordinates' => $coordinates,
                        'square' => array(
                            'width' => $sqW,
                            'height' => $sqH
                        ),
                        'widthInSquares' => $widthInSquares,
                        'heightInSquares' => $heightInSquares
                    );

                    if ($this->isTest()) {

                        $this->addMessage('-l2 map name: <b>' . trim((string) $vVal->mapname) . '</b>');
                        $this->addMessage('&nbsp;&nbsp;&nbsp;coordinates: ' . var_export($coordinates, true));
                        $this->addMessage('&nbsp;&nbsp;&nbsp;square: ' . var_export(array(
                            'width' => $sqW,
                            'height' => $sqH
                        ), true));
                        $this->addMessage('&nbsp;&nbsp;&nbsp;rows: ' . $heightInSquares);
                        $this->addMessage('&nbsp;&nbsp;&nbsp;cols: ' . $widthInSquares);
                    }

                    $this->saveL2Map(
                    array(
                        'name' => trim((string) $vVal->mapname),
                        'coordinates' => json_encode($coordinates),
                        'rows' => $heightInSquares,
                        'cols' => $widthInSquares
                    ));
                }

                $maps = $_SESSION['admin']['system']['import']['loaded']['map']['maps'];

                foreach ($vVal->squares->square as $sKey => $sVal) {

                    $legend = trim((string) $sVal->legend);

                    if (!isset($_SESSION['admin']['system']['import']['loaded']['map']['types'][$legend]))
                        $_SESSION['admin']['system']['import']['loaded']['map']['types'][$legend] = $this->saveMapItemType($legend);

                        // determining the position of the square in the map grid
                    $row = floor(trim((string) $sVal->number) / $maps[trim((string) $vVal->mapname)]['widthInSquares']);
                    $col = trim((string) $sVal->number) % $maps[trim((string) $vVal->mapname)]['widthInSquares'];
                    if ($col == 0)
                        $col = $maps[trim((string) $vVal->mapname)]['widthInSquares'];

                    $mapname = trim((string) $vVal->mapname);

                    $n1Lat = $n2Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - ($row * $maps[$mapname]['square']['height']);
                    $n1Lon = $maps[$mapname]['coordinates']['topLeft']['long'] + (($col - 1) * $maps[$mapname]['square']['width']);
                    $n1Lon = $n4Lon = ($n1Lon >= 180 ? -360 + $n1Lon : $n1Lon);
                    $n2Lon = $maps[$mapname]['coordinates']['topLeft']['long'] + ($col * $maps[$mapname]['square']['width']);
                    $n2Lon = $n3Lon = ($n2Lon > 180 ? -360 + $n2Lon : $n2Lon);
                    $n3Lat = $n4Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - (($row + 1) * $maps[$mapname]['square']['height']);

                    $typeless =

                    $occurrence = array(
                        'taxon' => trim((string) $obj->name),
                        'taxonId' => $taxonId,
                        'map' => $mapname,
                        'square' => trim((string) $sVal->number),
                        'row' => $row,
                        'col' => $col,
                        'legend' => trim((string) $sVal->legend),
                        'typeId' => $_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string) $sVal->legend)],
                        'nodes' => array(
                            array(
                                $n1Lat,
                                $n1Lon
                            ),
                            array(
                                $n2Lat,
                                $n2Lon
                            ),
                            array(
                                $n3Lat,
                                $n3Lon
                            ),
                            array(
                                $n4Lat,
                                $n4Lon
                            )
                        ),
                        'typeless' => empty($legend)
                    );

                    $this->doSaveMapItem($occurrence);

                    $this->saveLN2Cell(
                    array(
                        'taxon_id' => $taxonId,
                        'type_id' => $_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string) $sVal->legend)],
                        'square_number' => trim((string) $sVal->number),
                        'legend' => trim((string) $sVal->legend),
                        'map_id' => $maps[trim((string) $vVal->mapname)]['id'],
                        'coordinates' => json_encode(
                        array(
                            array(
                                $n1Lat,
                                $n1Lon
                            ),
                            array(
                                $n2Lat,
                                $n2Lon
                            ),
                            array(
                                $n3Lat,
                                $n3Lon
                            ),
                            array(
                                $n4Lat,
                                $n4Lon
                            )
                        ))
                    ));
                }
            }
        }
        else {

            // unknown species (the first album by koi division)
        }
    }


    /* auxiliry functions */
    private function cRename ($from, $to)
    {

        //return rename($from,$to); // generates odd errors on some linux filesystems
        if (copy($from, $to)) {

            if ($this->_deleteOldMediaAfterImport === true)
                @unlink($from);

            return true;
        }
        else {

            return false;
        }
    }


    private function makeMediaTargetPaths ()
    {
        $paths = $this->makePathNames($this->getNewProjectId());

        if (!file_exists($paths['project_media']))
            mkdir($paths['project_media']);
        if (!file_exists($paths['project_thumbs']))
            mkdir($paths['project_thumbs']);
        if (!file_exists($paths['project_media_l2_maps']))
            mkdir($paths['project_media_l2_maps']);
    }


    private function importPostProcessing ()
    {

		$res = $this->fixEmbeddedLinksAndMedia();

        if ($this->renumberGeoDataTypeOrder())
            $this->addMessage($this->storeError('Re-ordened map datatype legend (alphabetically).'));

        $this->copyEmbeddedMediaFiles();

        $this->addMessage($this->storeError('Processed embedded images.'));

        // additional texts
        $this->addModuleToProject(MODCODE_CONTENT, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

        $this->addMessage($this->storeError('Added module "content".'));

        // index
        $this->addModuleToProject(MODCODE_INDEX, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

        $this->addMessage($this->storeError('Added module "index".'));

        // search
        $this->addModuleToProject(MODCODE_UTILITIES, $this->getNewProjectId(), $_SESSION['admin']['system']['import']['moduleCount']++);

        $this->addMessage($this->storeError('Added module "search".'));

        $this->models->Projects->save(array(
            'id' => $this->getNewProjectId(),
            'published' => '1'
        ));

        $this->addMessage($this->storeError('Published project.'));
    }


    private function resolveInternalLinks ($s)
    {
        $controllers = array(
            'content pages' => array(
                'controller' => 'linnaeus',
                'param' => 'id'
            ),
            'glossary' =>             // [m]Glossary[/m]
			array(
                'controller' => 'glossary',
                'url' => 'term.php',
                'param' => 'id'
            ),
            'literature' =>             // [m]Literature[/m]
			array(
                'controller' => 'literature',
                'url' => 'reference.php',
                'param' => 'id'
            ),
            'species' =>             // [m]Species[/m]
			array(
                'controller' => 'species',
                'url' => 'taxon.php',
                'param' => 'id'
            ),
            'higher taxa' =>             // [m]Higher taxa[/m]
			array(
                'controller' => 'highertaxa',
                'url' => 'taxon.php',
                'param' => 'id'
            ),
            'text key' =>             // [m]Text Key[/m]
			array(
                'controller' => 'key',
                'url' => 'index.php?forcetree=1',
                'param' => 'step'
            ),
            'map key index' => array(
                'controller' => 'mapkey',
                'param' => 'id'
            ),
            'matrix key index' => array(
                'controller' => 'matrixkey',
                'url' => 'matrices.php',
                'param' => 'id'
            )
        );

        //$d = rtrim($s[count((array)$s)-1],'[/t]');
        $d = preg_replace('/\[\/t\]$/', '', $s[count((array) $s) - 1]);
        $d = preg_split('/(\[\/m\]\[[r]\])|(\[\/r\]\[[t]\])/iU', $d);

        // $d[0] holds the L2 module name, which is also the key in the $controllers-array above
        $d[0] = strtolower($d[0]);

        // during the import of some modules, the referring "id's" (i.e., names we hope to be unique) have been lowercased
        if ($d[0] == 'species' || $d[0] == 'higher taxa' || $d[0] == 'text key') {

            $d[1] = strtolower($d[1]);
        }

        if (isset($d[0]) && isset($controllers[$d[0]])) {

            if ($controllers[$d[0]]['controller'] == 'glossary' && isset($_SESSION['admin']['system']['import']['lookupArrays']['glossary'][$d[1]])) {
                $id = $_SESSION['admin']['system']['import']['lookupArrays']['glossary'][$d[1]];
            }

            if ($controllers[$d[0]]['controller'] == 'literature' && isset($_SESSION['admin']['system']['import']['lookupArrays']['literature'][$d[1]])) {
                $id = $_SESSION['admin']['system']['import']['lookupArrays']['literature'][$d[1]];
            }

            if ($controllers[$d[0]]['controller'] == 'species' && isset($_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]])) {
                $id = $_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]];
            }

            if ($controllers[$d[0]]['controller'] == 'highertaxa' && isset($_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]])) {
                $id = $_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]];
            }

            if ($controllers[$d[0]]['controller'] == 'key' && isset($_SESSION['admin']['system']['import']['lookupArrays']['key'][$d[1]])) {
                $id = $_SESSION['admin']['system']['import']['lookupArrays']['key'][$d[1]];
            }


            if (isset($id) && isset($d[2])) {

                $cnt = $controllers[$d[0]];

				$href = '../' . $cnt['controller'] . '/' . (isset($cnt['url']) ? $controllers[$d[0]]['url'] : 'index.php') . (isset($cnt['param']) ? (strpos($cnt['url'], '?') === false ? '?' : '&') . $cnt['param'] . '=' . $id : null);

				return '<a class="internal-link" href="' . $href . '">' . trim($d[2]) . '</a>';
            }
        }
        else if (isset($d[0]) && isset($_SESSION['admin']['system']['import']['lookupArrays']['modules'][$d[0]][$d[1]])) {

            $ids = $_SESSION['admin']['system']['import']['lookupArrays']['modules'][$d[0]][$d[1]];

            if (isset($id) && isset($d[2])) {

				$href = '../module/topic.php?modId=' . $ids['moduleId'] . '&id=' . $ids['pageId'];

				return '<a class="internal-link" href="' . $href . '">' . trim($d[2]) . '</a>';
            }
        }

        return isset($d[2]) ? trim($d[2]) : $s[0];
    }


    private function replaceInternalLinks ($s)
    {

        // regular links
        return preg_replace_callback('/(\[l\]\[m\](.*)\[\/l\])/isU', array(
            $this,
            'resolveInternalLinks'
        ), $s);

    }


    private function replaceEmbeddedMedia ($s)
    {

        /*
			if image / movie
				extract media name
				does media exist
					create link for local pop up
				does media not exist
					create link to fail message ???
				add to arrays [orig links] => [new links] <- text + front-end image pop-up logic

			? [l][m]Introduction[/m][r]Larval Euphausiids[/r][t]Larval euphausiids[/t][/l]

			x [l][im][f]carapace.jpg[/f][t]carapace[/t][/im][/l]
			x [l][mo][f]pleopod_motion_epacifica.mov[/f][t]Pleopod motion (E.pacifica)[/t][/mo][/l]

			[link] = [l]

			[module] = [m]
			[image] = [im]
			[movie] = [mo]
			[sound] = [s]

			[filename] = [f]
			[record] = [r]
			[text] = [t]

		*/

        //		$d = preg_replace('/(\[\/im\]|\[\/mo\]|\[\/s\]|\[f\]|\[\/t\])/','',preg_split('/\[\/f\]\[t\]/iU',$s[count((array)$s)-1]));
        $d = preg_split('/\[\/f\]\[t\]/iU', $s[count((array) $s) - 1]);

        if (isset($d[1]) && strpos($d[1], '[/im]') !== false)
            $type = 'image';
        else if (isset($d[1]) && strpos($d[1], '[/mo]') !== false)
            $type = 'movie';
        else if (isset($d[1]) && strpos($d[1], '[/s]') !== false)
            $type = 'sound';
        else
            return $s[0];

        $d = preg_replace('/(\[\/im\]|\[\/mo\]|\[\/s\]|\[f\]|\[\/t\])/', '', $d);

        if (isset($d[0]))
            $filename = strtolower($d[0]);
        else
            return $s[0];
        $label = isset($d[1]) ? $d[1] : $filename;

        //if (file_exists($_SESSION['admin']['system']['import']['paths']['project_media'].$filename))


        if ($type == 'image') {

            $_SESSION['admin']['system']['import']['embeddedMedia'][md5($filename)] = $filename;
            return '<span class="inline-image" onclick="showMedia(\'' . $_SESSION['admin']['system']['import']['paths']['media_url'] . $filename . '\',\'' . addslashes($label) . '\');">'.$label.'</span>';

        }
        else if ($type == 'movie') {

            $_SESSION['admin']['system']['import']['embeddedMedia'][md5($filename)] = $filename;
            return '<span class="inline-video" onclick="showMedia(\'' . $_SESSION['admin']['system']['import']['paths']['media_url'] . $filename . '\',\'' . addslashes($label) . '\');">'.$label.'</span>';

        }
        else if ($type == 'sound') {

            $_SESSION['admin']['system']['import']['embeddedMedia'][md5($filename)] = $filename;
            return '<span class="inline-audio" onclick="showMedia(\'' . $_SESSION['admin']['system']['import']['paths']['media_url'] . $filename . '\',\'' . addslashes($label) . '\');">'.$label.'</span>';

        }
        else
            return $s[0];
    }


    private function removeInternalLinks ($s)
    {
		return preg_replace_callback('/(\[l\])(.*)(\[t\])(.*)(\[\/t\])(.*)(\[\/l\])/iU',function($m){return trim($m[4]);},$s);
    }


    private function replaceOldExternalURLs ($s)
    {

		return preg_replace_callback('/(\[u\])(http|https){1}(.*)(\[\/u\])/U',function ($m){$url = trim($m[2]).trim($m[3]); return '<a href="'.$url.'" target="_blank">'.$url.'</a>';},$s);

    }


    private function replaceOldMarkUp ($s, $removeNotReplace=false)
    {

        $s = str_replace(
			array('[b]','[/b]','[i]','[/i]','[br]','[p]','[/p]','[u]','[/u]'),
			($removeNotReplace ? null : array('<b>','</b>','<i>','</i>','<br />','<p>','</p>','<u>','</u>')), $s);

        return preg_replace('/(\[)(\/)?(im|mo|s|f|t)(\])/U', '', $s);

    }


    private function copyEmbeddedMediaFiles ()
    {
        unset($_SESSION['admin']['system']['import']['loaded']['embeddedMedia']);

        foreach ($_SESSION['admin']['system']['import']['embeddedMedia'] as $basename) {

            if (empty($basename))
                continue;

            $fullname = $_SESSION['admin']['system']['import']['imagePath'] . $basename;

            if (!file_exists($fullname))
                $fullname = $_SESSION['admin']['system']['import']['imagePath'] . strtolower($basename);

            if (file_exists($fullname)) {

                $this->cRename($fullname, $_SESSION['admin']['system']['import']['paths']['project_media'] . $basename);

                $_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['saved']++;
            }
            else {

                $_SESSION['admin']['system']['import']['loaded']['embeddedMedia']['failed'][] = $basename;
            }
        }
    }


    private function createLookupArrays ()
    {
        $s = $g = $l = $m = $k = null;

        if (isset($_SESSION['admin']['system']['import']['loaded']['species'])) {

            foreach ((array) $_SESSION['admin']['system']['import']['loaded']['species'] as $key => $val) {

                if (isset($val['id'])) {

                    if (isset($val['taxon']))
                        $s[strtolower($val['taxon'])] = $val['id'];
                    if (isset($val['taxon']))
                        $s[strtolower($val['original_taxon'])] = $val['id']; // too tired to implicitly resolve all the original ranks
                    $s[strtolower($key)] = $val['id']; // too tired to implicitly resolve all the prefixed ranks
                }
            }
        }

        if (isset($_SESSION['admin']['system']['import']['glossary'])) {

            foreach ((array) $_SESSION['admin']['system']['import']['glossary'] as $val) {
                if (isset($val['term']) && isset($val['id']))
                    $g[$val['term']] = $val['id'];
            }
        }

        if (isset($_SESSION['admin']['system']['import']['literature'])) {

            foreach ((array) $_SESSION['admin']['system']['import']['literature'] as $val) {
                if (isset($val['original']) && isset($val['id']))
                    $l[$val['original']] = $val['id'];
            }
        }

        if (isset($_SESSION['admin']['system']['import']['freeModules']['ids'])) {

            $m = $_SESSION['admin']['system']['import']['freeModules']['ids'];
        }

        if (isset($_SESSION['admin']['system']['import']['key'])) {

            foreach ((array) $_SESSION['admin']['system']['import']['key'] as $val) {
                if (isset($val['page']) && isset($val['id']))
                    $k[$val['page']] = $val['id'];
            }
        }

        return array(
            'species' => $s,
            'glossary' => $g,
            'literature' => $l,
            'modules' => $m,
            'key' => $k
        );
    }


	private function doFixEmbeddedLinksAndMedia($src,$removeMarkup=false,$taxonId=null)
	{

		$src = preg_replace_callback('/((\[l\]\[im\]|\[l\]\[mo\]|\[l\]\[s\])(.*)\[\/l\])/isU', array(
            $this,
            'replaceEmbeddedMedia'
        ),$src);

		$src = $this->replaceOldExternalURLs($src);

		if ($this->_retainInternalLinks)
			$src = $this->replaceInternalLinks($src);
		else
			$src = $this->removeInternalLinks($src);

		$src = $this->replaceOldMarkUp($src,$removeMarkup);

		return $src;

	}


    private function fixEmbeddedLinksAndMedia ()
    {

		if ($this->_retainInternalLinks)
        	$_SESSION['admin']['system']['import']['lookupArrays'] = $this->createLookupArrays();


        $d = $this->models->ContentTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->ContentTaxa->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'content' => $this->doFixEmbeddedLinksAndMedia($val['content'])
            ));
        }

        $d = $this->models->Literature->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->Literature->save(array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'text' => $this->doFixEmbeddedLinksAndMedia($val['text'])
            ));
        }

        $d = $this->models->Glossary->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->Glossary->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'definition' => $this->doFixEmbeddedLinksAndMedia($val['definition'])
            ));
        }

        $d = $this->models->GlossarySynonyms->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->GlossarySynonyms->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'synonym' => $this->doFixEmbeddedLinksAndMedia($val['synonym'],true)
            ));
        }


        $d = $this->models->Content->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->Content->save(array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'content' => $this->doFixEmbeddedLinksAndMedia($val['content'])
            ));
        }


        $d = $this->models->ContentIntroduction->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->ContentIntroduction->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'content' => $this->doFixEmbeddedLinksAndMedia($val['content'])
            ));
        }


        $d = $this->models->ContentKeysteps->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->ContentKeysteps->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'title' => $this->doFixEmbeddedLinksAndMedia($val['title'],true),
                'content' => $this->doFixEmbeddedLinksAndMedia($val['content'])
            ));
        }

        $d = $this->models->ChoicesContentKeysteps->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->ChoicesContentKeysteps->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'choice_txt' => $this->doFixEmbeddedLinksAndMedia($val['choice_txt'])
            ));
        }

        $d = $this->models->ContentFreeModules->_get(array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            )
        ));

        foreach ((array) $d as $val) {

            $this->models->ContentFreeModules->save(
            array(
                'id' => $val['id'],
                'project_id' => $this->getNewProjectId(),
                'content' => $this->doFixEmbeddedLinksAndMedia($val['content'])
            ));
        }

		if ($this->_retainInternalLinks)
			$this->addMessage($this->storeError('Replaced internal links.'));
		else
			$this->addMessage($this->storeError('Removed internal links.'));

    }

    private function renumberGeoDataTypeOrder ()
    {
        $s = $this->models->GeodataTypesTitles->_get(
        array(
            'id' => array(
                'project_id' => $this->getNewProjectId()
            ),
            'columns' => 'type_id,title',
            'order' => 'title'
        ));

        if (!$s)
            return false;


        foreach ((array) $s as $key => $val) {

            $this->models->GeodataTypes->update(array(
                'show_order' => $key
            ), array(
                'id' => $val['type_id'],
                'project_id' => $this->getNewProjectId()
            ));
        }

        return true;
    }


    private function lowercaseMediaFiles ()
    {
        if (!isset($_SESSION['admin']['system']['import']['imagePath']))
            return false;

        $path = $_SESSION['admin']['system']['import']['imagePath'];

        $dh = opendir($path);

        while (($file = readdir($dh)) !== false) {

            if ($file != "." && $file != "..") {

                if (!rename($path . '/' . $file, $path . '/' . strtolower($file))) {

                    $errors[] = $file;
                }
            }
        }

        closedir($dh);

        return isset($errors) ? $errors : array();
    }


    private function detectCustomModulesInXML ()
    {
        if (!isset($_SESSION['admin']['system']['import']['additionalModules'])) {

            $d = new XMLReader();
            $r = array();
            if ($d->open($_SESSION['admin']['system']['import']['file']['path'])) {
                while ($d->read()) {
                    if ($d->nodeType == XMLReader::ELEMENT && $d->depth == 1 && !in_array((string) $d->name, $this->_knownModules)) {
                        $r[] = $d->name;
                    }
                }
            }

            $_SESSION['admin']['system']['import']['additionalModules'] = $r;
        }

        return $_SESSION['admin']['system']['import']['additionalModules'];
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
					$_SESSION['admin']['system']['import']['errorlog']['header']['project']
				).
				'--'.
				str_replace('.','-',$_SERVER['SERVER_NAME'])
			).
			'.log'
		);

        header('Content-type:text/txt');

        echo 'project: ' . $_SESSION['admin']['system']['import']['errorlog']['header']['project'] . chr(10);
        echo 'version: ' . $_SESSION['admin']['system']['import']['errorlog']['header']['version'] . chr(10);
        echo 'project id: ' . $_SESSION['admin']['system']['import']['errorlog']['header']['id'] . chr(10);
        echo 'source file: ' . $_SESSION['admin']['system']['import']['errorlog']['header']['imported_from'] . chr(10);
        echo 'image path: ' . (!empty($_SESSION['admin']['system']['import']['imagePath']) ? $_SESSION['admin']['system']['import']['imagePath'] : '(no image import)') . chr(10);
        echo 'thumbs path: ' . (!empty($_SESSION['admin']['system']['import']['thumbsPath']) ? $_SESSION['admin']['system']['import']['thumbsPath'] : '(no thumbs import)') . chr(10);
        echo 'created: ' . $_SESSION['admin']['system']['import']['errorlog']['header']['createdate'] . chr(10);
		echo 'server: '. $_SERVER['SERVER_NAME'] . chr(10);
        echo '--------------------------------------------------------------------------------' . chr(10);

        $prevMod = null;

        foreach ((array) $_SESSION['admin']['system']['import']['errorlog']['errors'] as $val) {

            $mod = @strtolower($val[0]);

            if ($mod !== $prevMod) {

                if (!is_null($prevMod))
                    echo chr(10);

                if (empty($mod))
                    echo 'while post-processing:' . chr(10);
                else
                    echo 'while loading ' . $mod . ':' . chr(10);
            }

            echo strip_tags($val[1]) . chr(10);

            $prevMod = $mod;
        }

        echo chr(10);


        echo '--------------------------------------------------------------------------------' . chr(10);

        echo 'loaded ' . (count((array) $_SESSION['admin']['system']['import']['loaded']['species'])) . ' taxa:' . chr(10);

        foreach ((array) $_SESSION['admin']['system']['import']['loaded']['species'] as $key => $val) {

            echo $val['original_taxon'] . ' > ' . $val['taxon'] . ' (' . $val['rank_name'] . '; id: ' . $val['id'] . ')' . chr(10);
        }

        echo chr(10);

        die();
    }


    /**
     * Vertaalt Rijksdriehoekscoördinaten naar noorderbreedte/oosterlengte
     *
     * Gebaseerd op Javascript van Ed Stevenhagen en Frank Kissels ({@link http://www.xs4all.nl/~estevenh/})
     * @access private
     * @param float x-coördinaat (RD)
     * @param float y-coördinaat (RD)
     * @return array array met noorderbreedte en oosterlengte
     */
    private function bombAmersfoort ($rd_x, $rd_y)
    {

        // http://et10.org/gc/rd.php
        $afX = $rd_x < 1000 ? $rd_x * 1000 : $rd_x;
        $afY = $rd_y < 1000 ? $rd_y * 1000 : $rd_y;

        //De waarde van x dient te liggen tussen 0 en 290(000)
            //De waarde van y dient te liggen tussen 290(000) en 630(000)
        if ($afX > $afY) {

            $afX = $afX + $afY;
            $afY = $afX - $afY;
            $afX = $afX - $afY;
        }

        // constanten
        $X0 = 155000.000;
        $Y0 = 463000.000;
        $F0 = 52.156160556;
        $L0 = 5.387638889;

        $A01 = 3236.0331637;
        $B10 = 5261.3028966;
        $A20 = -32.5915821;
        $B11 = 105.9780241;
        $A02 = -0.2472814;
        $B12 = 2.4576469;
        $A21 = -0.8501341;
        $B30 = -0.8192156;
        $A03 = -0.0655238;
        $B31 = -0.0560092;
        $A22 = -0.0171137;
        $B13 = 0.0560089;
        $A40 = 0.0052771;
        $B32 = -0.0025614;
        $A23 = -0.0003859;
        $B14 = 0.0012770;
        $A41 = 0.0003314;
        $B50 = 0.0002574;
        $A04 = 0.0000371;
        $B33 = -0.0000973;
        $A42 = 0.0000143;
        $B51 = 0.0000293;
        $A24 = -0.0000090;
        $B15 = 0.0000291;

        $dx = ($afX - $X0) * pow(10, -5);
        $dy = ($afY - $Y0) * pow(10, -5);

        $df = ($A01 * $dy) + ($A20 * pow($dx, 2)) + ($A02 * pow($dy, 2)) + ($A21 * pow($dx, 2) * $dy) + ($A03 * pow($dy, 3));
        $df += ($A40 * pow($dx, 4)) + ($A22 * pow($dx, 2) * pow($dy, 2)) + ($A04 * pow($dy, 4)) + ($A41 * pow($dx, 4) * $dy);
        $df += ($A23 * pow($dx, 2) * pow($dy, 3)) + ($A42 * pow($dx, 4) * pow($dy, 2)) + ($A24 * pow($dx, 2) * pow($dy, 4));

        $noorderbreedte = $F0 + ($df / 3600);

        $dl = ($B10 * $dx) + ($B11 * $dx * $dy) + ($B30 * pow($dx, 3)) + ($B12 * $dx * pow($dy, 2)) + ($B31 * pow($dx, 3) * $dy);
        $dl += ($B13 * $dx * pow($dy, 3)) + ($B50 * pow($dx, 5)) + ($B32 * pow($dx, 3) * pow($dy, 2)) + ($B14 * $dx * pow($dy, 4));
        $dl += ($B51 * pow($dx, 5) * $dy) + ($B33 * pow($dx, 3) * pow($dy, 3)) + ($B15 * $dx * pow($dy, 5));

        $oosterlengte = $L0 + ($dl / 3600);

        return array(
            $noorderbreedte,
            $oosterlengte
        );
    }

    private function getRandomTestProjectName ()
    {
        $animales = array(
			'aardvark','albatross','alpaca','anteater','antelope','armadillo','baboon','badger','barracuda','bat','bear','beaver',
			'bee', 'butterfly', 'camel', 'caribou', 'chinchilla', 'clam', 'cobra', 'cormorant', 'coyote', 'crab', 'crane', 'crow',
			'dragonfly', 'donkey', 'dugong', 'eagle', 'echidna', 'eland', 'emu', 'falcon', 'ferret', 'finch', 'gazelle', 'gnat',
			'guanaco', 'hawk', 'hedgehog', 'heron', 'hippopotamus', 'hummingbird', 'hyena', 'iguana', 'jackal', 'koala', 'komodo dragon',
			'kouprey', 'kudu', 'lemur', 'leopard', 'llama', 'lobster', 'locust', 'lyrebird', 'manatee', 'meerkat', 'mole', 'moose',
			'mouse', 'mule', 'narwhal', 'octopus', 'okapi', 'opossum', 'oryx', 'otter', 'owl', 'oyster', 'pelican', 'penguin',
			'platypus', 'polar_bear', 'porcupine', 'quelea', 'rhinoceros', 'rook', 'serval', 'shark', 'sheep', 'squid', 'swallow',
			'swan', 'tapir', 'vicuña','wasp','weasel','wolf','wombat', 'yak','zebra'
        );

        return '-test project «' . ucwords($animales[rand(0, count($animales) - 1)]) . '» (' . date('c') . ')';
    }

    private function isTest ()
    {
        return $_SESSION['admin']['system']['import']['errorlog']['header']['test_project'];
    }

	private function createAdHocIndex($matrixname,$char,$stat)
	{

		return md5(
			$matrixname.
			trim((string) $char->character_name).
			trim((string) $stat->state_name).
			trim((string) $stat->state_min).
			trim((string) $stat->state_max).
			trim((string) $stat->state_mean).
			trim((string) $stat->state_sd).
			trim((string) $stat->state_file)
		);

	}

	private function resolveCharacterElement($file)
	{

		if (isset($file->characters->character_))
			return $file->characters->character_;
		else if (isset($file->characters->character))
			return $file->characters->character;
		else
			return null;

	}



}
