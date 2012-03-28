<?php

/*


[2:35:48 PM] maarten schermer: uiteindelijk leiden alle 4 de types tot hetzelfde:
[2:35:54 PM] maarten schermer: - verzameling pagina's
[2:36:04 PM] maarten schermer: - met ieder een titel en inhoud
[2:36:09 PM] maarten schermer: - en mogelijk een afbeeling
[2:36:23 PM] maarten schermer: voorts
[2:36:44 PM] maarten schermer: die afbeeling is af te lezen uit ofwel een veld dat dezelfde structuur heeft als multimedia in species
[2:36:46 PM] maarten schermer: dus iets als:
[2:37:27 PM] maarten schermer: multimedia->multimediafile->filename / fullname / multimedia_type
[2:37:39 PM] Ruud Altenburg: Of overview
[2:37:42 PM] Ruud Altenburg: <overview>
[2:37:46 PM] maarten schermer: oh ja
[2:37:47 PM] maarten schermer: ok
[2:37:50 PM] Ruud Altenburg: Zoals in Introduction
[2:38:11 PM] Ruud Altenburg: Maar ik weet niet helemaal zeker of dat zo heet, moment
[2:38:13 PM] maarten schermer: anders gezegd: ofwel uit een veld waarvan we de naam niet weten en waar alleen de naam van een afbeelding stat
[2:38:31 PM] Ruud Altenburg: Wacht, je hebt een voorbeeld: habitat uit Vogels
[2:38:32 PM] maarten schermer: "overview" in dagvlinders::habitat
[2:38:40 PM] maarten schermer: die ook
[2:38:48 PM] Ruud Altenburg: Yes
[2:38:49 PM] maarten schermer: ok
[2:39:10 PM] Ruud Altenburg: Het moeilijke wordt nu natuurlijk: hoe gaan we die velden noemen
[2:39:25 PM] maarten schermer: dat komt zo, eerst even dit:
[2:39:27 PM] Ruud Altenburg: Tenminste, dat is niet moeilijk, je kunt de naam van de tags aanhouden
[2:39:31 PM] Ruud Altenburg: O sorry
[2:39:35 PM] maarten schermer: en dan moet er de mogelijkheid zijn van een alfabet, maar of dat er wel of niet moet zijn is eigenlijk niet af te lezen aan het xml.
[2:39:49 PM] Ruud Altenburg: Nee en het is ook niet bijster relevant meer
[2:39:50 PM] maarten schermer: ok
[2:40:13 PM] maarten schermer: wat ik kan doen, is een extra optie maken dat je het tonen van de alfabet in runtime kunt togglen.
[2:40:15 PM] Ruud Altenburg: Ik vraag me af met die sterk verbeterde inhoudsopgave of we dat uberhaupt nog nodig hebben
[2:40:21 PM] Ruud Altenburg: Bv
[2:40:21 PM] maarten schermer: geen idee, zeg het maar
[2:40:29 PM] Ruud Altenburg: Laat voorlopig maar zitten
[2:40:30 PM] maarten schermer: ok
[2:40:43 PM] maarten schermer: dus de vraag is vooral *hoe* de velden te herkennen
[2:40:57 PM] maarten schermer: tussen die twee habitats zat al het nodige verschil
[2:41:02 PM] Ruud Altenburg: Inderdaad
[2:41:21 PM] Ruud Altenburg: De export in LII had helemaal geen optie om custom modules te exporteren
[2:41:26 PM] maarten schermer: ok
[2:41:40 PM] Ruud Altenburg: Wat ik deed was de code voor een bestaande module klonen
[2:41:45 PM] Ruud Altenburg: en deze aanpassen
[2:41:46 PM] maarten schermer: ah so
[2:42:03 PM] Ruud Altenburg: Dus de tags zijn min of meer on-the-fly verzonnen
[2:42:06 PM] maarten schermer: ok
[2:42:08 PM] maarten schermer: lekkerrrr
[2:42:13 PM] Ruud Altenburg: Ja ja
[2:42:21 PM] maarten schermer: dan maar zo:
- image: ofwel alleen een image naam (moet te herkennen zijn), of er moet die mm-> structuut onder hangen
[2:42:23 PM] Ruud Altenburg: Dit is 80 jaar geleden he
[2:42:26 PM] maarten schermer: jaja
[2:42:48 PM] maarten schermer: andere twee velden: de lange is de content, en de korte is de titel?
[2:43:02 PM] maarten schermer: is dat wat?
[2:43:12 PM] Ruud Altenburg: Oei
[2:43:16 PM] maarten schermer: het is maar een idee
[2:43:22 PM] Ruud Altenburg: Er kunnen natuurlijk meer velden zijn
[2:43:29 PM] maarten schermer: doe eens een voorbeeld?
[2:43:35 PM] Ruud Altenburg: Maar er kon niet meer in dan 32(?) tekend
[2:43:37 PM] Ruud Altenburg: tekens
[2:43:47 PM] Ruud Altenburg: Zelfde voorbeeld habitat
[2:43:55 PM] Ruud Altenburg: Of genitalia!
[2:44:14 PM] Ruud Altenburg: Ik heb je Agromyzidae gestuurd met twee custom modules
[2:44:15 PM] maarten schermer: dat is de naam van de module, dat is al geluk die te isoleren
[2:44:24 PM] maarten schermer: ff kijken
[2:44:50 PM] Ruud Altenburg: Volgens mij heeft dat altijd een vaste naam want die kon je niet opgeven bij het aanmaken van een module
[2:44:51 PM] maarten schermer: heb je doe nog in de buurt? bomen, bos
[2:45:13 PM] maarten schermer: "Volgens mij heeft dat altijd.." wat is "dat" precies?
[2:45:19 PM] *** Ruud Altenburg verstuurde AGROMYZI.XML.zip ***
[2:45:38 PM] Ruud Altenburg: Titel bedoel ik
[2:45:45 PM] Ruud Altenburg: Paginatitel op precies te zijn
[2:46:43 PM] maarten schermer: ok, maar tussen "habitat" en "habitat_title" zit bij de een nog "topic" en bij de andere "habitat_topic".
[2:46:54 PM] Ruud Altenburg: Oeps
[2:47:06 PM] maarten schermer: dat is een beetje een issue
[2:47:23 PM] Ruud Altenburg: Naadje
[2:47:41 PM] Ruud Altenburg: Je loopt straks de hele modules door voordat je gaat importeren, of niet?
[2:47:48 PM] Ruud Altenburg: Op zoek naar de juiste velden
[2:47:53 PM] maarten schermer: mogelijk
[2:47:56 PM] maarten schermer: zal wel moeten denk ik
[2:47:57 PM] Ruud Altenburg: custom modules
[2:48:03 PM] Ruud Altenburg: Denk ik ook
[2:48:17 PM] Ruud Altenburg: Ik zou voor elk veld de max strlen bepalen
[2:48:17 PM] maarten schermer: getver
[2:48:21 PM] maarten schermer: AGROMYZI:
[2:48:31 PM] Ruud Altenburg: en degene met de kortste is de titel
[2:48:42 PM] Ruud Altenburg: Wazzup?
[2:48:43 PM] maarten schermer: host -> host_plant -> host_name / anyspecies
[2:49:36 PM] maarten schermer: dat wordt zoiets als:
[modulename] -> * -> [kortste veld] = titel
[modulename] -> * -> [langste veld] = content
[2:50:02 PM] Ruud Altenburg: Wat ik zei...
[2:50:11 PM] maarten schermer: ja
[2:51:08 PM] Ruud Altenburg: Mag ik nu?
[2:51:14 PM] maarten schermer: sure
[2:51:37 PM] Ruud Altenburg: Een mogelijk probleem wordt het creeren van interne links
[2:51:45 PM] maarten schermer: ok
[2:51:49 PM] maarten schermer: namelijk?
[2:51:57 PM] Ruud Altenburg: Naar die custom modules
[2:52:09 PM] maarten schermer: want?
[2:52:18 PM] Ruud Altenburg: Want je weet de naam van de module niet van tevoren
[2:52:27 PM] maarten schermer: ha, und?
[2:52:31 PM] Ruud Altenburg: En de naam van de velden
[2:52:31 PM] maarten schermer: na, und?
[2:52:45 PM] maarten schermer: wordt er ook naar VELDEN verwezen dan??
[2:52:50 PM] Ruud Altenburg: Nee
[2:52:53 PM] maarten schermer: ok
[2:53:06 PM] Ruud Altenburg: Dat kon volgens mij niet
[2:53:20 PM] maarten schermer: ok.
maar het gaat wel goed, denk ik. het scenario is als volgt:
[2:53:34 PM] maarten schermer: - ik vis er een custom uit, zeg "habitat"
[2:53:50 PM] maarten schermer: - ik sla die op, als, laten we zeggen, id = 33
[2:54:08 PM] maarten schermer: - met een handvol pagina's die id's hebben, 101-121
[2:54:10 PM] Ruud Altenburg: Dat is de id van een module?
[2:54:12 PM] maarten schermer: yep
[2:54:20 PM] maarten schermer: ik houd bij een array met de vorm:
[2:54:37 PM] maarten schermer: ['habitat']['id'] = 33
[2:54:42 PM] maarten schermer: en iets van
[2:54:42 PM] Ruud Altenburg: Ik snap m
[2:54:51 PM] Ruud Altenburg: Maar....
[2:54:56 PM] maarten schermer: ['habitat']['pages']['struweel']['id']= 111;
[2:54:59 PM] maarten schermer: etc
[2:55:04 PM] Ruud Altenburg: Je weet niet de naam van de module zoals die in LII stond
[2:55:15 PM] Ruud Altenburg: Want bv in Agro heet-ie host
[2:55:24 PM] Ruud Altenburg: terwijl de module volgens mij Host plants heette
[2:55:26 PM] Ruud Altenburg: maw
[2:55:32 PM] maarten schermer: ARGH UND WEH!
[2:55:44 PM] Ruud Altenburg: Er moet nog iets van een optie zijn om dat aan te geven of later te veranderen
[2:55:47 PM] Ruud Altenburg: Ja!
[2:55:54 PM] Ruud Altenburg: Ja ja ja
[2:56:01 PM] maarten schermer: kristus chod
[2:56:22 PM] maarten schermer: hoerenzonen!
[2:56:42 PM] Ruud Altenburg: Maar hebben de modules bij jou geen id?
[2:56:49 PM] Ruud Altenburg: in LNG
[2:56:51 PM] maarten schermer: uiteraard
[2:57:10 PM] Ruud Altenburg: Dacht ik al want je moet ze in een andere taal een andere naam kunnen geven
[2:57:20 PM] Ruud Altenburg: Dan is het leed toch al minder groot?
[2:57:27 PM] maarten schermer: want?
[2:58:06 PM] maarten schermer: als er wordt verwezen naar "Host plants" en die hele naam komt nergens voor, hoe moet ik dan weten welke id daar bij hoort?
[2:58:21 PM] Ruud Altenburg: Ik vrees een extra tabel in de db
[2:58:28 PM] maarten schermer: ik dacht het niet!
[2:58:36 PM] Ruud Altenburg: Hoe doe je dat dan bij custom modules?
[2:58:40 PM] Ruud Altenburg: Nu
[2:58:48 PM] maarten schermer: ik zal opnemen ene extra veld in het scherm
[2:58:58 PM] Ruud Altenburg: Die kun je toch ook een willekeurige naam geven?
[2:59:18 PM] maarten schermer: import custom modules:
[ ] "habitat"  enter module name: [............]
[2:59:43 PM] Ruud Altenburg: Lijkt me een goed plan
[3:00:02 PM] maarten schermer: dan kan ik daarna [<opgegeven naam>]['pages']['struweel']['id']= 111; doen
[3:00:08 PM] maarten schermer: etc


Species variant:
Zo te zien is het eigenlijk hetzelfde als de standaard module
[3:22:56 PM] Ruud Altenburg: Alleen er MOET een media veld in zitten
[3:23:08 PM] Ruud Altenburg: en er mag GEEN classfication veld in zitte


[4:16:13 PM] Ruud Altenburg: Volgens jouw vriend GG was er een module gebaseerd op Species in Lemurs
[4:16:18 PM] Ruud Altenburg: NOT!
[4:16:24 PM] maarten schermer: pom pom pom
[4:16:29 PM] maarten schermer: sleep hem er bij
[4:16:34 PM] maarten schermer: (haar zat)
[4:16:40 PM] Ruud Altenburg: Ben ik niet sterk genoeg voor
[4:16:44 PM] Ruud Altenburg: 100+ kg
[4:16:45 PM] maarten schermer: haha
[4:16:56 PM] maarten schermer: 200 cm vs 100 kg
[4:17:09 PM] Ruud Altenburg: Ik hoop maar dat je er nog niet al teveel tijd in gestoken had?
[4:17:17 PM] maarten schermer: gaat wel
[4:17:21 PM] maarten schermer: ik heb wel fake XML zitten maken
[4:17:27 PM] maarten schermer: en testen
[4:17:32 PM] Ruud Altenburg: Er zitten drie customs in, maar alle drie generiek
[4:17:33 PM] maarten schermer: lekker dan
[4:17:34 PM] maarten schermer: ah
[4:17:48 PM] maarten schermer: zal ik het officieel vergeten dan?
[4:18:10 PM] *** Ruud Altenburg verstuurde Screen shot 2012-02-14 at 16.17.57.png ***
[4:18:23 PM] Ruud Altenburg: Doe maar



q($_SESSION['admin']['system']['import']['loaded']['species']);


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


include_once ('Controller.php');

class ImportController extends Controller
{

    public $usedModels = array(
		'content_taxon',
		'page_taxon', 
		'page_taxon_title', 
		'commonname',
		'synonym',
		'media_taxon',
		'media_descriptions_taxon',
		'content',
		'literature',
		'literature_taxon',
		'keystep',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
        'module_project_user', 
		'matrix',
		'matrix_name',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
		'characteristic_label_state',
		'glossary',
		'glossary_synonym',
		'glossary_media',
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'content_free_module',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title',
		'content_introduction',
		'introduction_page',
		'introduction_media',
		'user_taxon',
		'l2_occurrence_taxon',
		'l2_map'
    );
   
    public $usedHelpers = array('file_upload_helper','xml_parser');

    public $controllerPublicName = 'Linnaeus 2 Import';

	public $cssToLoad = array('import.css');
	public $jsToLoad = array();
	
	private $_deleteOldMediaAfterImport = false; // might become a switch later, but let's not overdo it
	
	private $_knownModules = array('file','project','proj_literature','glossary','introduction','tree','records','text_key','pict_key','diversity');


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

		error_reporting(E_ERROR | E_PARSE);
		
		$this->setBreadcrumbRootName(_('Linnaeus 2 import'));
        
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
    public function indexAction()
    {
    
        $this->setPageName(_('Data import options'));

        $this->printPage();
    
    }

    public function l2StartAction()
    {

		if ($this->rHasVal('process','1')) $this->redirect('l2_project.php');

        $this->setPageName(_('Choose file'));
		
		$this->setSuppressProjectInBreadcrumbs();

		if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear','file')) {

			$tmp = tempnam(sys_get_temp_dir(),'lng');

			if (copy($this->requestDataFiles[0]['tmp_name'],$tmp)) {

				$_SESSION['admin']['system']['import']['file'] = array(
					'path' => $tmp,
					'name' => $this->requestDataFiles[0]['name'],
					'src' => 'upload'
				);

			} else {

				unset($_SESSION['admin']['system']['import']['file']);

			}

		} else
		if ($this->rHasVal('serverFile') && !$this->rHasVal('clear','file')) {
		
			if (file_exists($this->requestData['serverFile'])) {

				$_SESSION['admin']['system']['import']['file'] = array(
					'path' => $this->requestData['serverFile'],
					'name' => $this->requestData['serverFile'],
					'src' => 'existing'
				);

			} else {

				$this->addError('File "'.$this->requestData['serverFile'].'" does not exist.');
				unset($_SESSION['admin']['system']['import']['file']);

			}

		}

		if ($this->rHasVal('imagePath') || $this->rHasVal('noImages')) {

			if ($this->rHasVal('noImages')) {

				$_SESSION['admin']['system']['import']['imagePath'] = false;

			} else
			if (file_exists($this->requestData['imagePath'])) {

				$_SESSION['admin']['system']['import']['imagePath'] = rtrim($this->requestData['imagePath'],'/').'/';

			} else {

				$this->addError('Image path "'.$this->requestData['imagePath'].'" does not exist or unreachable.');
				unset($_SESSION['admin']['system']['import']['imagePath']);

			}

		}

		if ($this->rHasVal('thumbsPath') || $this->rHasVal('noThumbs')) {

			if ($this->rHasVal('noThumbs')) {

				$_SESSION['admin']['system']['import']['thumbsPath'] = false;

			} else
			if (file_exists($this->requestData['thumbsPath'])) {

				$_SESSION['admin']['system']['import']['thumbsPath'] = rtrim($this->requestData['thumbsPath'],'/').'/';

			} else {

				$this->addError('Thumbs path "'.$this->requestData['thumbsPath'].'" does not exist or unreachable.');
				unset($_SESSION['admin']['system']['import']['thumbsPath']);

			}

		}		

		if ($this->rHasVal('clear','file')) {

			unset($_SESSION['admin']['system']['import']['file']);
			unset($_SESSION['admin']['system']['import']['raw']);

		}

		if ($this->rHasVal('clear','imagePath')) unset($_SESSION['admin']['system']['import']['imagePath']);
		if ($this->rHasVal('clear','thumbsPath')) unset($_SESSION['admin']['system']['import']['thumbsPath']);


		if (isset($_SESSION['admin']['system']['import'])) $this->smarty->assign('s',$_SESSION['admin']['system']['import']);
    
        $this->printPage();

    }

	public function l2ProjectAction()
	{

		if (!isset($_SESSION['admin']['system']['import']['file']['path'])) $this->redirect('l2_start.php');

        $this->setPageName(_('Creating project'));
		
		$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

		$this->helpers->XmlParser->setDoReturnValues(true);
		$d = $this->helpers->XmlParser->getNode('project');

		if (isset($d->title)) {

			$newId = $this->createProject(
				array(
					 'title' => trim((string)$d->title),
					 'version' => trim((string)$d->version),
					 'sys_description' => 'Created by import from a Linnaeus 2-export.',
					 'css_url' => $this->controllerSettings['defaultProjectCss']
				)
			);
	
			if (!$newId) {

				$this->addError('Could not create new project "'.trim((string)$d->title).'". Does a project with the same name already exist?');

			} else { 

				$project = $this->getProjects($this->getNewProjectId());

				$this->addMessage('Created new project "'.trim((string)$d->title).'"');

				$this->setNewProjectId($newId);

				$this->addCurrentUserToProject();

				$this->makeMediaTargetPaths();

				$this->smarty->assign('newProjectId',$newId);

				// add language
				$l = $this->addProjectLanguage(trim((string)$d->language));

				if (!$l) {

					$this->addError('Unable to use project language "'.trim((string)$d->language).'"; defaulted to English.');
					$this->addProjectLanguage('English');

				} else {

					$this->setNewDefaultLanguageId($l);
					$this->addMessage('Set language "'.trim((string)$d->language).'"');

				}
				
				$_SESSION['admin']['system']['import']['paths'] = $this->makePathNames($this->getNewProjectId());

			}

		} else {

			$this->addError('Failed to retrieve project title from XML-file.');

		}

        $this->printPage();
	
	}

	public function l2SpeciesAction()
	{

		if (!isset($_SESSION['admin']['system']['import']['file']['path'])) $this->redirect('l2_start.php');
		
		set_time_limit(300);

		$project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(_('Species and ranks for "'.$project['title'].'"'));

		$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

		$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_ResolveRanks'));

		$this->helpers->XmlParser->getNodes('treetaxon');
		
		$_SESSION['admin']['system']['import']['substRanks'] = ($this->rHasVal('substRanks') ? $this->requestData['substRanks'] : null);

		$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_ResolveSpecies'));

		$this->helpers->XmlParser->getNodes('treetaxon');
		
		$treetops = $this->checkTreeTops($_SESSION['admin']['system']['import']['loaded']['species']);

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			$_SESSION['admin']['system']['import']['loaded']['ranks'] =
				$this->addProjectRanks(
					$_SESSION['admin']['system']['import']['loaded']['ranks'],
					($this->rHasVal('substRanks') ? $this->requestData['substRanks'] : null),
					($this->rHasVal('substParentRanks') ? $this->requestData['substParentRanks'] : null)
				);

			$species = $_SESSION['admin']['system']['import']['loaded']['species'] =
				$this->addSpecies($_SESSION['admin']['system']['import']['loaded']['species'],$_SESSION['admin']['system']['import']['loaded']['ranks']);

			if (isset($this->requestData['treetops']))
				$_SESSION['admin']['system']['import']['loaded']['species'] = $this->fixTreetops($species,$this->requestData['treetops']);
			
			$this->assignTopSpeciesToUser($_SESSION['admin']['system']['import']['loaded']['species']);

			$this->addModuleToProject(4);
			$this->addModuleToProject(5);
			$this->grantModuleAccessRights(4);
			$this->grantModuleAccessRights(5);
			
			$this->addMessage('Saved '.count((array)$_SESSION['admin']['system']['import']['loaded']['ranks']).' ranks');
			$this->addMessage('Saved '.count((array)$_SESSION['admin']['system']['import']['loaded']['species']).' species');

			$this->smarty->assign('processed',true);

		}

		$this->smarty->assign('project',$project);
		$this->smarty->assign('ranks',$_SESSION['admin']['system']['import']['loaded']['ranks']);
		$this->smarty->assign('projectRanks',$this->getPossibleRanks());
		if ($this->rHasVal('substRanks')) $this->smarty->assign('substRanks',$this->requestData['substRanks']);
		if ($this->rHasVal('substParentRanks')) $this->smarty->assign('substParentRanks',$this->requestData['substParentRanks']);
		$this->smarty->assign('species',$_SESSION['admin']['system']['import']['loaded']['species']);
		$this->smarty->assign('treetops',$treetops);

        $this->printPage();
	
	}
	
	public function l2SpeciesDataAction()
	{

		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');

		$project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(_('Additional species data for "'.$project['title'].'"'));

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			set_time_limit(900);

			if ($this->rHasVal('taxon_overview','on') || 
				$this->rHasVal('taxon_media','on') ||
				$this->rHasVal('taxon_common','on') ||
				$this->rHasVal('taxon_synonym','on')
				) {

				if ($this->rHasVal('taxon_overview','on')) {

					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview'] = true;
					$_SESSION['admin']['system']['import']['speciesOverviewCatId'] = $this->createStandardCat();
					$_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'] = array();

				} else {
				
					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview'] = false;
				
				}

				if ($this->rHasVal('taxon_media','on')) {

					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media'] = true;

					$this->loadControllerConfig('Species');

					foreach((array)$this->controllerSettings['media']['allowedFormats'] as $val)
						$_SESSION['admin']['system']['import']['mimes'][$val['mime']] = $val;
						
					$this->loadControllerConfig();

					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'] = array();

				} else {
				
					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media'] = false;
				
				}


				if ($this->rHasVal('taxon_common','on')) {

					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common'] = true;
					$_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'] = array();

				} else {
				
					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common'] = false;
				
				}

				if ($this->rHasVal('taxon_synonym','on')) {

					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym'] = true;
					$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'] = array();

				} else {
				
					$_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym'] = false;
				
				}
				

				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

				$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Species'));

				$this->helpers->XmlParser->getNodes('taxondata');


				if ($this->rHasVal('taxon_overview','on')) {

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved'].' general species description(s).');
		
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

					unset($_SESSION['admin']['system']['import']['speciesOverviewCatId']);
					unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']);
					unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed']);

				} else {
				
					$this->addMessage('Skipped species description.');
								
				}

				if ($this->rHasVal('taxon_media','on')) {

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved'].' media files.');
		
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

					unset($_SESSION['admin']['system']['import']['speciesOverviewCatId']);
					unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']);
					unset($_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed']);
					unset($_SESSION['admin']['system']['import']['mimes']);

				} else {
				
					$this->addMessage('Skipped media.');
								
				}

				if ($this->rHasVal('taxon_common','on')) {

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved'].' common name(s).');
	
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

				} else {
				
					$this->addMessage('Skipped common names.');

				}


				if ($this->rHasVal('taxon_synonym','on')) {

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved'].' synonym(s).');
	
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

				} else {
				
					$this->addMessage('Skipped synonyms.');
								
				}


				unset($_SESSION['admin']['system']['import']['elementsToLoad']);

	
			} else {

				$this->addMessage('Skipped species description.');
				$this->addMessage('Skipped media.');
				$this->addMessage('Skipped common names.');
				$this->addMessage('Skipped synonyms.');

			}
	
			$this->smarty->assign('processed',true);
	
		}	
			
       $this->printPage();

	}

	public function l2LiteratureGlossaryAction()
	{

		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');

		$project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(_('Literature and glossary for "'.$project['title'].'"'));

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			set_time_limit(900);

			if ($this->rHasVal('literature','on') || $this->rHasVal('glossary','on')) {

				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

				if ($this->rHasVal('literature','on')) {

					$_SESSION['admin']['system']['import']['loaded']['literature']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['literature']['failed'] = array();

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Literature'));
	
					$this->helpers->XmlParser->getNodes('proj_reference');

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['literature']['saved'].' literary reference(s).');
	
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['literature']['failed'])!==0) {

						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['literature']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

					$this->addModuleToProject(3);
					$this->grantModuleAccessRights(3);

				} else {
				
					$this->addMessage('Skipped literature.');
				
				}

				if ($this->rHasVal('glossary','on')) {

					$_SESSION['admin']['system']['import']['loaded']['glossary']['saved'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['glossary']['failed'] = array();

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Glossary'));

					$this->loadControllerConfig('Glossary');

					foreach((array)$this->controllerSettings['media']['allowedFormats'] as $val)
						$_SESSION['admin']['system']['import']['mimes'][$val['mime']] = $val;
						
					$this->loadControllerConfig();

					$this->helpers->XmlParser->getNodes('term');

					$this->loadControllerConfig();

					$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['glossary']['saved'].' glossary item(s).');
	
					if (count((array)$_SESSION['admin']['system']['import']['loaded']['glossary']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['glossary']['failed'] as $val)
							$this->addError($val['cause']);
		
					}

					unset($_SESSION['admin']['system']['import']['mimes']);

					$this->addModuleToProject(2);
					$this->grantModuleAccessRights(2);

				} else {
				
					$this->addMessage('Skipped glossary.');
				
				}

			} else {
			
				$this->addMessage('Skipped literature.');				
				$this->addMessage('Skipped glossary.');

			}
	
			$this->smarty->assign('processed',true);
	
		}	
			
       $this->printPage();

	}
	
	public function l2ContentAction()
	{

		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');

		$project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(_('Additional content for "'.$project['title'].'"'));

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			set_time_limit(900);

			if ($this->rHasVal('welcome','on') || 
				$this->rHasVal('introduction','on')
			) {

				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

				if ($this->rHasVal('welcome','on')) {

					$_SESSION['admin']['system']['import']['loaded']['welcome']['saved'] = array();
					$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'] = array();

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Welcome'));
					
					$this->helpers->XmlParser->getNodes('project');

					$this->addModuleToProject(10);
					$this->grantModuleAccessRights(10);

					if (count((array)$_SESSION['admin']['system']['import']['loaded']['welcome']['saved'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['welcome']['saved'] as $val)
							$this->addMessage($val);
		
					}

					if (count((array)$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'] as $val)
							$this->addError($val);
		
					}

				} else {
				
					$this->addMessage('Skipped welcome text(s).');
				
				}

				if ($this->rHasVal('introduction','on')) {

					$_SESSION['admin']['system']['import']['loaded']['introduction']['show_order'] = 0;
					$_SESSION['admin']['system']['import']['loaded']['introduction']['saved'] = array();
					$_SESSION['admin']['system']['import']['loaded']['introduction']['failed'] = array();

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Introduction'));

					$this->helpers->XmlParser->getNodes('topic');

					$this->addModuleToProject(1);
					$this->grantModuleAccessRights(1);

					if (count((array)$_SESSION['admin']['system']['import']['loaded']['introduction']['saved'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['introduction']['saved'] as $val)
							$this->addMessage($val);
		
					}

					if (count((array)$_SESSION['admin']['system']['import']['loaded']['introduction']['failed'])!==0) {
		
						foreach ((array)$_SESSION['admin']['system']['import']['loaded']['introduction']['failed'] as $val)
							$this->addError($val);
		
					}
					
					unset($_SESSION['admin']['system']['import']['loaded']['introduction']['show_order']);

				} else {
				
					$this->addMessage('Skipped introduction.');
				
				}

			} else {
			
				$this->addMessage('Skipped welcome text(s).');
				$this->addMessage('Skipped introduction.');

			}
			
			$this->smarty->assign('processed',true);

		}

        $this->printPage();

	}

	public function l2KeysAction()
	{

		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');

		$project = $this->getProjects($this->getNewProjectId());

        $this->setPageName(_('Keys for "'.$project['title'].'"'));

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			set_time_limit(900);

			if ($this->rHasVal('key_dich','on') || 
				$this->rHasVal('key_matrix','on')
			) {

				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);

				if ($this->rHasVal('key_dich','on')) {

					$_SESSION['admin']['system']['import']['loaded']['key_dich']['keys'] = array('text_key' => false, 'pict_key' => false);
					$_SESSION['admin']['system']['import']['loaded']['key_dich']['keyStepIds'] = null;
					$_SESSION['admin']['system']['import']['loaded']['key_dich']['stepAdd'] = null;

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_KeyDichotomous'));

					$this->helpers->XmlParser->getNodes('text_key');
					$this->helpers->XmlParser->getNodes('pict_key');

					$this->addModuleToProject(6);
					$this->grantModuleAccessRights(6);
	
					$this->addMessage('Created dichotomous key.');
					
					unset($_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']);

				} else {

					$this->addMessage('Skipped dichotomous key.');

				}

				if ($this->rHasVal('key_matrix','on')) {

					$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'] = null;
					$_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'] = array();

					$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_KeyMatrixResolve'));

					$this->helpers->XmlParser->getNodes('taxondata');

					if (isset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'])) {

						$m = $this->saveMatrices($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices']);
	
						if (isset($m['failed'])) foreach ((array)$m['failed'] as $val) $this->addError($val['cause']);
	
						$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'] = $m['matrices'];
	
						$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_KeyMatrixConnect'));
	
						$this->helpers->XmlParser->getNodes('taxondata');
	
						if (count((array)$_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'])!==0) {
			
							foreach ((array)$_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'] as $val)
								$this->addError($val['cause']);
			
						}
	
						$this->addModuleToProject(7);
						$this->grantModuleAccessRights(7);
		
						$this->addMessage('Created matrix key(s).');
	
						unset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices']);

					} else {

						$this->addMessage('No matrix key found.');

					}

				} else {
				
					$this->addMessage('Skipped matrix key(s).');
				
				}

			} else {

				$this->addMessage('Skipped dichotomous key.');				
				$this->addMessage('Skipped matrix key(s).');

			}
			
			$this->smarty->assign('processed',true);

		}

        $this->printPage();

	}

	public function l2MapAction()
	{
							
		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');
	
		$project = $this->getProjects($this->getNewProjectId());
	
		$this->setPageName(_('Map data for "'.$project['title'].'"'));
	
		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {
	
			set_time_limit(900);
	
			if ($this->rHasVal('map_items','on')) {
	
				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);
	
				$_SESSION['admin']['system']['import']['loaded']['map']['maps'] = null;
				$_SESSION['admin']['system']['import']['loaded']['map']['types'] = null;
				$_SESSION['admin']['system']['import']['loaded']['map']['saved'] = 0;
				$_SESSION['admin']['system']['import']['loaded']['map']['failed'] = 0;
				$_SESSION['admin']['system']['import']['loaded']['map']['skipped'] = 0;

				$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Map'));

				$this->loadControllerConfig('MapKey');
	
				$this->helpers->XmlParser->getNodes('taxondata');

				$this->loadControllerConfig();

				$this->updateMapTypeColours();

				$this->addModuleToProject(8);
				$this->grantModuleAccessRights(8);

				$this->addMessage('Imported '.$_SESSION['admin']['system']['import']['loaded']['map']['saved'].' map items.');

				$this->addMessage('Skipped '.$_SESSION['admin']['system']['import']['loaded']['map']['skipped'].' because of invalid coordinates.');

				$this->addMessage('Failed '.$_SESSION['admin']['system']['import']['loaded']['map']['failed'].', most likely duplicates.');
				
				unset($_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']);

			} else {

				$this->addMessage('Skipped map.');

			}
			
			$this->importPostProcessing();

			$this->smarty->assign('processed',true);
		}
			

        $this->printPage();

	}

	public function xmlParserCallback_Custom($obj,$node)
	{

		$this->addCustomModule($node,$obj);
	
	}

	private function addCustomModule($module,$data)
	{

		// create the module
		$m = $this->models->FreeModuleProject->save(
			array(
				'id' => null, 
				'module' => $module, 
				'project_id' => $this->getNewProjectId(),
				'active' => 'y'
			)
		);
		
		$_SESSION['admin']['system']['import']['loaded']['custom']['saved'][] = 'Created module "'.$module.'".';

		$newModuleId = $this->models->FreeModuleProject->getNewId();

		$this->grantFreeModuleAccessRights($newModuleId);	
		
			// element names for the module pages can vary, find out the element name for the entire page
		$arrayData = (array)$data;
		$d = array_keys($arrayData);
		$pageName = $d[0];

		$titleField = null;
		$contentField = null;
		$imageField = null;
		
		$i=0;
		foreach((array)$arrayData[$pageName] as $page) {
		
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
			foreach((array)$page as $key => $val) {
			
				if ($i==0) $titleField = $key;
				if ($i==1) $contentField = $key;
				if ($i==2) {
					if (is_object($val)) {
					// is object copied from species (which, we assume, has fixed element-names)
						$imageField = false;
					} else {
					// is simple field
						$imageField = $key;
					}
				}
				$i++;
				
			}
		
		}

		// get the multimedia-paths
		$paths = isset($_SESSION['admin']['system']['import']['paths']) ? 
			$_SESSION['admin']['system']['import']['paths'] : 
			$this->makePathNames($this->getNewProjectId());


		// saving the actual pages
		foreach((array)$arrayData[$pageName] as $page) {
		
			$page = ((array)$page);
			
			if ($imageField===false) {
		
				$d = (array)$page['multimediafile'];
				$image = trim($d['filename']);
				//$thisCaption = trim($d['caption']);
				
		
			} else 
			if (!is_null($imageField)) {
		
				$image = trim($page[$imageField]);
		
			} else {

				$image = null;

			}
		
			$topic = trim($page[$titleField]);
			$content = trim($page[$contentField]);

			// create a new page
			$this->models->FreeModulePage->save(
				array(
					'project_id' => $this->getNewProjectId(),
					'module_id' => $newModuleId,
					'got_content' => 1
				)
			);
			
			$newPageId = $this->models->FreeModulePage->getNewId();
		
			// save the title and content
			$this->models->ContentFreeModule->save(
				array(
					'id' => null, 
					'project_id' => $this->getNewProjectId(), 
					'module_id' => $newModuleId,
					'language_id' => $this->getNewDefaultLanguageId(), 
					'page_id' => $newPageId,
					'topic' => $topic,
					'content' => $this->replaceOldMarkUp($content)
				)
			);

			$_SESSION['admin']['system']['import']['loaded']['custom']['saved'][] = '  Saved '.$module.' topic "'.$topic.'".';

			$moduleLinkName =
				!is_null($_SESSION['admin']['system']['import']['freeModules']['names'][$module]) ?
					$_SESSION['admin']['system']['import']['freeModules']['names'][$module] :
					$module;

			$_SESSION['admin']['system']['import']['freeModules']['ids'][$moduleLinkName][$topic] = array('moduleId' => $newModuleId, 'pageId' => $newPageId);

			if (!empty($image)) {
	
				if ($this->cRename($_SESSION['admin']['system']['import']['imagePath'].$image,$paths['project_media'].$image)) {
				
					$this->models->FreeModulePage->update(
						array(
							'image' => $image
						),
						array(
							'project_id' => $this->getNewProjectId(),
							'module_id' => $newModuleId
						)
					);			
				
				} else {
				
					$_SESSION['admin']['system']['import']['loaded']['custom']['failed'][] = '  Could not save image '.$image.' for topic "'.$topic.'".';	
				
				}
	
			}
			
		}
	
	}


 	public function l2AdditionalAction()
	{
	
		if (
			!isset($_SESSION['admin']['system']['import']['file']['path']) ||
			!isset($_SESSION['admin']['system']['import']['loaded']['species'])
		) $this->redirect('l2_start.php');

		$project = $this->getProjects($this->getNewProjectId());

		$this->setPageName(_('Additional data for "'.$project['title'].'"'));

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {
	
			set_time_limit(900);

			if ($this->rHasVal('modules')) {


				if ($this->rHasVal('modules-name')) {
					
					$_SESSION['admin']['system']['import']['freeModules']['names'] = $this->requestData['modules-name'];

				} else {

					$_SESSION['admin']['system']['import']['freeModules']['names'] = null;

				}


				$this->helpers->XmlParser->setFileName($_SESSION['admin']['system']['import']['file']['path']);
				$this->helpers->XmlParser->setCallbackFunction(array($this,'xmlParserCallback_Custom'));
	
				$_SESSION['admin']['system']['import']['loaded']['custom']['saved'] = null;
				
				foreach((array)$this->requestData['modules'] as $module => $val) {
				
					if ($val=='on') $this->helpers->XmlParser->getNodes($module);		

				}

				if (count((array)$_SESSION['admin']['system']['import']['loaded']['custom']['saved'])!==0) {
	
					foreach ((array)$_SESSION['admin']['system']['import']['loaded']['custom']['saved'] as $val)
						$this->addMessage($val);
	
				}
				
				unset($_SESSION['admin']['system']['import']['loaded']['custom']['saved']);
	
			} else {
							
				$this->addMessage('Skipped additional modules.');

			}

			$this->smarty->assign('processed',true);
			
		}
		
		$addMod = $this->getCustomModules();

		if (count((array)$addMod)==0)  $this->addMessage('No additional modules found.');

		$this->smarty->assign('projectId',$this->getNewProjectId());

		$this->smarty->assign('modules',$addMod);

        $this->printPage();
	
	}
 
 
 
	public function goNewProject()
	{

		$this->setCurrentProjectId($this->getNewProjectId());
		$this->setCurrentProjectData();
		$this->reInitUserRolesAndRights();
		$this->setCurrentUserRoleId();

		unset($_SESSION['admin']['system']['import']);
		unset($_SESSION['admin']['project']['ranks']);

		$this->redirect($this->getLoggedInMainIndex());

	}

	/* xml parser callback functions */
	public function xmlParserCallback_Species($obj)
	{

		if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_overview']===true) $this->addSpeciesContent($obj);

		if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_media']===true) $this->addSpeciesMedia($obj);

		if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_common']===true) $this->addSpeciesCommonNames($obj);

		if ($_SESSION['admin']['system']['import']['elementsToLoad']['taxon_synonym']===true) $this->addSpeciesSynonyms($obj);
	
	}

	public function xmlParserCallback_Literature($obj)
	{

		$this->addLiterature($obj);

	}

	public function xmlParserCallback_Glossary($obj)
	{

		$this->addGlossary($obj);

	}

	public function xmlParserCallback_Welcome($obj)
	{

		$this->addWelcomeTexts($obj);

	}

	public function xmlParserCallback_Introduction($obj)
	{

		$this->addIntroduction($obj);

	}

	public function xmlParserCallback_KeyDichotomous($obj,$node)
	{

		$this->addKeyDichotomous($obj,$node);

	}

	public function xmlParserCallback_KeyMatrixResolve($obj)
	{

		$this->resolveMatrices($obj);

	}

	public function xmlParserCallback_KeyMatrixConnect($obj)
	{

		$this->connectMatrices($obj);

	}

	public function xmlParserCallback_Map($obj)
	{

		$this->saveMapItem($obj);

	}


	// projects, modules, users
	private function getProjects($id=null)
	{

		$d = isset($id) ? array('id' => $id) : '*';

		$d = $this->models->Project->_get(array('id' => $d));

		return isset($id) ? $d[0] : $d;
	
	}

	private function setNewProjectId($id)
	{

		if ($id==null)
			unset($_SESSION['admin']['system']['import']['newProjectId']);
		else
			$_SESSION['admin']['system']['import']['newProjectId'] = $id;
	
	}

	private function getNewProjectId()
	{
	
		return (isset($_SESSION['admin']['system']['import']['newProjectId'])) ?
			$_SESSION['admin']['system']['import']['newProjectId']:
			null;
	
	}

	private function addCurrentUserToProject()
	{

		$this->models->ProjectRoleUser->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),	
				'role_id' => ID_ROLE_LEAD_EXPERT,
				'user_id' => $this->getCurrentUserId(),
				'active' => 1
			)
		);

	}

	private function addModuleToProject($id)
	{

		/*

			 1 | Introduction
			 2 | Glossary
			 3 | Literature
			 4 | Species module
			 5 | Higher taxa
			 6 | Dichotomous key
			 7 | Matrix key
			 8 | Map key
			10 | Additional texts
			11 | Index
			12 | Search
		
		free modules
		
		*/

		$this->models->ModuleProject->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),	
				'module_id' => $id,
				'active' => 'y'
			)
		);
		
	}

	private function grantModuleAccessRights($id)
	{

		$this->models->ModuleProjectUser->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(), 
				'module_id' => $id, 
				'user_id' => $this->getCurrentUserId(),
			)
		);

	}

	private function grantFreeModuleAccessRights($id)
	{

		$this->models->FreeModuleProjectUser->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(), 
				'free_module_id' => $id, 
				'user_id' => $this->getCurrentUserId(),
			)
		);

	}

	// languages
	private function addProjectLanguage($language)
	{

		$l = $this->resolveLanguage($language);

		if (!$l) return false;

		$p = $this->models->LanguageProject->save(
			array(
				'id' => null,
				'language_id' => $l,				
				'project_id' => $this->getNewProjectId(),				
				'def_language' => 1,		
				'active' => 'y',				
				'tranlation_status' => 1
			)
		);
	
		return $p ? $l : false;
	
	}

	private function setNewDefaultLanguageId($id)
	{

		if ($id==null)
			unset($_SESSION['admin']['system']['import']['newLanguageId']);
		else
			$_SESSION['admin']['system']['import']['newLanguageId'] = $id;
	
	}

	private function getNewDefaultLanguageId()
	{

		return (isset($_SESSION['admin']['system']['import']['newLanguageId'])) ?
			$_SESSION['admin']['system']['import']['newLanguageId']:
			null;
	
	}

	private function resolveLanguage($l)
	{
		// too much encoding headaches
		switch (htmlentities($l)) {

			case 'Nederlands' :
				$l = 'Dutch';
				break;
			case 'Fran&Atilde;&sect;ais' :
			case 'Frans' :
				$l = 'French';
				break;
			case 'Deutsch' :
			case 'Duits' :
				$l = 'German';
				break;
			case 'Engels' :
				$l = 'English';
				break;
			case 'Spaans' :
				$l = 'Spanish';
				break;
			case 'Italiaans' :
				$l = 'Italian';
				break;
		}

		$l = $this->models->Language->_get(
			array(
				'id' => array(
					'language' => $l
				),
				'columns' => 'id'
			)
		);

		return ($l) ? $l[0]['id'] : false;

	}

	// ranks
	public function xmlParserCallback_ResolveRanks($obj)
	{

		$importRank = trim((string)$obj->taxon);
		$parentRankParent = trim((string)$obj->parenttaxon);
		
		if (empty($importRank)) return;

		if (!isset($_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank])) {

				$r = $this->models->Rank->_get(
					array(
						'id' => array('default_label' => $importRank),
						'columns' => 'id'
					)
				);
				
				$rankId = $r[0]['id'];

			if (isset($rankId)) {

				$_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['rank_id'] = $rankId;
				
				if ($parentRankParent!='none') {

					$r = $this->models->Rank->_get(
						array(
							'id' => array('default_label' => $parentRankParent),
							'columns' => 'id'
						)
					);
					
					$_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_id'] = isset($r[0]['id']) ? $r[0]['id'] : false;
					$_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_name'] = $parentRankParent;

				} else {

					$_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['parent_id'] = null;

				}


			} else {

				$_SESSION['admin']['system']['import']['loaded']['ranks'][$importRank]['rank_id'] = false;

			}
			
		}

	}

	private function getPossibleRanks()
	{
	
		return $this->models->Rank->_get(array('id' => '*','fieldAsIndex' => 'id'));

	}

	private function addProjectRank($label,$rank,$isLower,$parentId)
	{

		$this->models->ProjectRank->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),	
				'rank_id' => $rank['rank_id'],	
				'parent_id' => isset($parentId) ? $parentId : null,
				'lower_taxon' => $isLower ? '1' : '0'
			)
		);
		
		$rank['id'] = $this->models->ProjectRank->getNewId();

		$this->models->LabelProjectRank->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'project_rank_id' => $rank['id'],
				'language_id' => $this->getNewDefaultLanguageId(),
				'label' => $label
			)
		);

		return $rank;

	}

	private function addProjectRanks($ranks,$substituteRanks=null,$substituteParentRanks=null)
	{

		if (isset($substituteRanks) || isset($substituteParentRanks)) $possibleRanks = $this->getPossibleRanks();

		$d = $prevId = null;

		$isLower = false;

		foreach((array)$ranks as $key => $val) {

			// no valid rank
			if (!isset($val['rank_id']) || $val['rank_id']===false) {

				if (isset($substituteRanks) && isset($substituteRanks[$key])) {
				
					$x = $possibleRanks[$substituteRanks[$key]]; // hand picked substitute rank
					$val = array(
					  'rank_id'	=> $x['id'],
					  'parent_id'	=> $x['parent_id'],
					  'parent_name'	=> $x['rank']
					 );
				
				} else {

					continue; // unresolvable rank

				}

			}
			/*
			// no valid parent (except $key==0, top of the hierarchy)
			if ((!isset($val['parent_id']) || $val['parent_id']===false) && count((array)$d) > 0) {

				if (isset($substituteParentRanks) && isset($substituteParentRanks[$key])) {

					$val['parent_id'] = $substituteParentRanks[$key]; // hand picked substitute parent rank
				
				} else {

					continue; // unresolvable parent rank

				}

			}
			*/

			//if (!isset($val['parent_id']) && $key > 0) continue; // parentless ranks (other then topmost)
			
			if (!$isLower && (strtolower($key)=='species')) $isLower = true;

			$d[$key] = $this->addProjectRank($key,$val,$isLower,$prevId);
			$prevId = $d[$key]['id'];

		}

		// if $isLower is still false (i.e., all ranks are higher taxa), set the last (=lowest) rank to being lower taxa	
		if ($isLower==false) {

			$this->models->ProjectRank->update(
				array(
					'lower_taxon' => '1'
				),
				array(
					'id' => $d[$key]['id'],
					'project_id' => $this->getNewProjectId()
				)
			);

		}

		return $d;

	}

	// species (& treetops)
	public function xmlParserCallback_ResolveSpecies($obj)
	{

		$rankName = trim((string)$obj->taxon) ? trim((string)$obj->taxon) : null;
		$rankId =
			isset($_SESSION['admin']['system']['import']['loaded']['ranks'][trim((string)$obj->taxon)]) &&
			$_SESSION['admin']['system']['import']['loaded']['ranks'][trim((string)$obj->taxon)]['rank_id']!==false ?
				$_SESSION['admin']['system']['import']['loaded']['ranks'][trim((string)$obj->taxon)]['rank_id'] :
				(isset($_SESSION['admin']['system']['import']['substRanks'][$rankName]) ?
					$_SESSION['admin']['system']['import']['substRanks'][$rankName] :
					null
				);

		$_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$obj->name)] = array(
			'taxon' => $this->cleanL2Name(trim((string)$obj->name)),
			'rank_id' => $rankId,
			'rank_name' => $rankName,
			'parent' => trim((string)$obj->parentname),
			'source' => 'records->taxondata'
		);

	}
	
	private function cleanL2Name($taxon)
	{

		 $l2Markers = array('subsp.', 'var.', 'subvar.', 'f.', 'subf.');

		 if (count(explode(' ', $taxon)) > 2) {

			 foreach ($l2Markers as $marker) {
			   if (stristr($taxon, $marker) !== false) {
				$taxon = str_replace($marker, '', $taxon);
				break;
			   }
			 }
		 }
		 return str_replace('  ', ' ', $taxon);

	}

	private function removeRankFromTaxonName($taxon)
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

 
 	private function extractLinkedSpeciesRatherThanDisplayed($whatever)
	{

		return trim(preg_replace('/(\[m\](.*)\[\/m\])|(\[t\](.*)\[\/t\])|(\[r\]|\[l\]|\[p\]|\[\/r\]|\[\/l\]|\[\/p\])/','',trim($whatever)));
		
	}

	private function addSpecies($species,$ranks)
	{

		foreach((array)$species as $key => $val) {
		
			if (!isset($ranks[$val['rank_name']]['id'])) continue; // not loading rankless taxa

			$res = $this->models->Taxon->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'taxon' => $val['taxon'],
					'parent_id' => 'null',
					'rank_id' => $ranks[$val['rank_name']]['id'],
					'taxon_order' => ($key+1),
					'is_hybrid' => 0,
					'list_level' => 0
				)
			);
			
			$val['id'] = $this->models->Taxon->getNewId();
			$d[$key] = $val;

		}
		
		if (!isset($d)) return null;

		foreach((array)$d as $key => $val) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getNewProjectId(),	
						'taxon' => $val['parent']
					),
					'columns' => 'id'
				)
			);
			
			if ($t) {
			
				$this->models->Taxon->save(
					array(
						'id' => $val['id'],
						'project_id' => $this->getNewProjectId(),	
						'parent_id' =>  $t[0]['id']
					)
				);
	
				$d[$key]['parent_id'] = $t[0]['id'];

			}

		}

		return $d;

	}

	private function assignTopSpeciesToUser($species)
	{

		foreach((array)$species as $key => $val) {
		
			if (isset($val['parent_id'])) continue;

			$this->models->UserTaxon->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'user_id' => $this->getCurrentUserId(),
					'taxon_id' => $val['id'],
				)
			);
				
		}

	}

	private function checkTreeTops($d)
	{

		$treetops = false;

		foreach((array)$d as $key => $val) {

			if ($val['parent']=='') {

				$treetops[] = $val;

			}

		}
			
		return $treetops;

	}

	private function fixTreetops($species,$treetops)
	{

		if (count((array)$treetops)<2) return;

		$d = $this->addProjectRank('(master rank)',array('rank_id' => -1,'parent_id' => null));

		$this->models->ProjectRank->update(
			array(
				'parent_id' => $d['id']
			),
			array(
				'id !=' => $d['id'],
				'project_id' => $this->getNewProjectId(),	
				'parent_id' => 'null'
			)
		);

		$this->models->Taxon->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),	
				'taxon' => '(master taxon)',
				'parent_id' => 'null',
				'rank_id' => $d['id'],
				'taxon_order' => 0,
				'is_hybrid' => 0,
				'list_level' => 0
			)
		);

		$masterId = $this->models->Taxon->getNewId();

		foreach((array)$species as $key => $val) {
		
			if (in_array($val['taxon'],$treetops)) {

				$this->models->Taxon->save(
					array(
						'id' => $val['id'],
						'project_id' => $this->getNewProjectId(),	
						'parent_id' =>  $masterId
					)
				);
	
				$species[$key]['parent_id'] = $masterId;

			}

		}

		return $species;

	}

	//  species content, species media, common names, synoynms
	private function createStandardCat()
	{

		$pt = $this->models->PageTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getNewProjectId(),
					'page' => 'Description'
				),
				'columns' => 'id'
			)
		);

		if (isset($pt[0]['id'])) return $pt[0]['id'];

		$pt = $this->models->PageTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'page' => 'Description',
				'show_order' => 0,
				'def_page' => 1
			)
		);
	
		$id = $this->models->PageTaxon->getNewId();

		$this->models->PageTaxonTitle->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'page_id' => $id,
				'language_id' => $this->getNewDefaultLanguageId(),
				'title' => 'Description'
			)
		);

		return $id;
	
	}
	
	private function addSpeciesContent($taxon)
	{

		if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'])) {
		
			$this->models->ContentTaxon->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'taxon_id' => $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'],
					'language_id' => $this->getNewDefaultLanguageId(),
					'page_id' => $_SESSION['admin']['system']['import']['speciesOverviewCatId'],
					'content' => $this->replaceOldMarkUp(trim((string)$taxon->description)),
					'publish' => 1
				)
			);
			
			$_SESSION['admin']['system']['import']['loaded']['speciesContent']['saved']++;

		} else {
		
			$_SESSION['admin']['system']['import']['loaded']['speciesContent']['failed'][] = array(
				'data' => $taxon,
				'cause' => 'Unable to resolve name "'.trim((string)$taxon->name).'" to taxon id.'
			);
		
		}

	}

	private function addSpeciesMedia($taxon)
	{

		if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'])) {
			
			$taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'];
				
			$fileName = trim((string)$taxon->multimedia->overview);

			if (!empty($fileName)) {

				$r = $this->doAddSpeciesMedia(
					$taxonId,
					$fileName,
					$fileName,
					true
				);
	
				if ($r['saved']==true) {

					if (isset($r['full_path'])) $this->cRename($r['full_path'],$_SESSION['admin']['system']['import']['paths']['project_media'].$r['filename']);
					if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$_SESSION['admin']['system']['import']['paths']['project_thumbs'].$r['filename']);

					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved']++;
				
				} else {

					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'][] = $r;

				}
				
			}

			foreach($taxon->multimedia->multimediafile as $vKey => $vVal) {
			
				$fileName = trim((string)$vVal->filename);

				if (empty($fileName)) continue;

				$r = $this->doAddSpeciesMedia(
					$taxonId,
					$fileName,
					(isset($val->fullname) ? ((string)$val->fullname) : $fileName)
				);

				if ($r['saved']==true) {

					if (isset($r['full_path'])) $this->cRename($r['full_path'],$_SESSION['admin']['system']['import']['paths']['project_media'].$r['filename']);
					if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$_SESSION['admin']['system']['import']['paths']['project_thumbs'].$r['filename']);
					
					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['saved']++;
					
				} else {

					$_SESSION['admin']['system']['import']['loaded']['speciesMedia']['failed'][] = $r;

				}

			}

		}
					
	}

	private function doAddSpeciesMedia($taxonId,$fileName,$fullName,$isOverviewPicture=false)
	{

		if ($_SESSION['admin']['system']['import']['imagePath']==false)
			return array(
				'saved' => false,
				'data' => $fileName,
				'cause' => 'User specified no media import for project'
			);

		if (empty($fileName)) return array(
				'saved' => false,
				'data' => '',
				'cause' => 'Missing file name'
			);

		if (file_exists($_SESSION['admin']['system']['import']['imagePath'].$fileName)) {
		
			//$thisMIME = $this->helpers->FileUploadHelper->getMimeType($_SESSION['admin']['system']['import']['imagePath'].$fileName);
			$thisMIME = $this->mimeContentType($_SESSION['admin']['system']['import']['imagePath'].$fileName);
			
			if (isset($_SESSION['admin']['system']['import']['mimes'][$thisMIME])) {
			
				if ($_SESSION['admin']['system']['import']['thumbsPath']==false)
					$thumbName = null;
				else
					$thumbName = file_exists($_SESSION['admin']['system']['import']['thumbsPath'].$fileName) ? $fileName : null;

				$this->models->MediaTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'file_name' => $fileName,
						'thumb_name' => $thumbName,
						'original_name' => $fullName,
						'mime_type' => $thisMIME,
						'file_size' => filesize($_SESSION['admin']['system']['import']['imagePath'].$fileName),
						'overview_image' => ($isOverviewPicture ? 1 : 0)
					)
				);

                $this->models->MediaDescriptionsTaxon->save(
                    array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'language_id' => $this->getNewDefaultLanguageId(),	
                        'media_id' => $this->models->MediaTaxon->getNewId(), 
                        'description' => $fullName
                    )
                );

				return array(
					'saved' => true,
					'filename' => $fileName,
					'full_path' => $_SESSION['admin']['system']['import']['imagePath'].$fileName,
					'thumb' => isset($thumbName) ? $thumbName : null,
					'thumb_path' => isset($thumbName) ? $_SESSION['admin']['system']['import']['thumbsPath'].$thumbName : null
				);

			} else {

				return array(
					'saved' => false,
					'data' => $fileName,
					'cause' => isset($thisMIME) ? 'MIME-type "'.$thisMIME.'" not allowed' : 'Could not determine MIME-type'
				);

			}
		
		} else {
		
			return array(
				'saved' => false,
				'data' => $fileName,
				'cause' => 'File "'.$fileName.'" does not exist'
			);
		
		}	

	}

	private function addSpeciesCommonNames($taxon)
	{

		if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'])) {
		
			$taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'];

			if(isset($taxon->vernaculars->vernacular)) {

				foreach($taxon->vernaculars->vernacular as $vKey => $vVal) {

					$languagId = $this->resolveLanguage(trim((string)$vVal->language));
	
					if ($languagId) {
	
						$this->models->Commonname->save(
							array(
								'id' => null,
								'project_id' => $this->getNewProjectId(),
								'taxon_id' => $taxonId,
								'language_id' => $languagId,
								'commonname' => trim((string)$vVal->name)
							)
						);
	
						$_SESSION['admin']['system']['import']['loaded']['taxon_common']['saved']++;
	
					} else {
	
						$_SESSION['admin']['system']['import']['loaded']['taxon_common']['failed'][] = array(
							'data' => trim((string)$taxon->name),
							'cause' => 'Unable to resolve language "'.trim((string)$vVal->language).'"'
						);
		
					}
	
				}
				
			}

		}

	}

	private function addSpeciesSynonyms($taxon)
	{

		if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'])) {
		
			$taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$taxon->name)]['id'];
			
			$i = 0;
			
			foreach($taxon->synonyms as $vKey => $vVal) {
			
				$res = $this->models->Synonym->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'synonym' => trim((string)$vVal->synonym->name),
						'show_order' => $i++
					)
				);

				if ($res===true)
					$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['saved']++;
				else
					$_SESSION['admin']['system']['import']['loaded']['taxon_synonym']['failed'][] = array(
						'data' => trim((string)$taxon->name),
						'cause' => 'Unable to save synonym "'.trim((string)$vVal->synonym->name).'" ('.$res.').'
					);
	
			}
	
		}

	}

	// literature & glossary
	private function fixAuthors($s)
	{

		// Antezana et al., 1976b

		$d = strrpos($s,',');		// comma position
		$y = trim(substr($s,$d+1));	// year
		$a = substr($s,0,$d);		// all but year = auhor(s)
		$a2 = null;					// default no 2nd author
		$m = false;					// defualt no multiple authors (>2 = et al.)
		$d = strpos($a,'et al.');	// "et. al" position
		if ($d!==false) {
			$a = trim(substr($a,0,$d));
			$m = true;
		} else {
			$d = strpos($a,' and ');
			if ($d!==false) {
				$a2 = trim(substr($a,$d+strlen(' and ')));
				$a = trim(substr($a,0,$d));
			}
		}
		
		$f = null;

		if (!is_numeric($y)) {

			$f = substr($y,-1);
			$y2 = substr($y,0,strlen($y)-2);
			
			if (!is_numeric($y2)) {

				$f = substr($y,-2);
				$y2 = substr($y,0,strlen($y)-3);
				
				if (!is_numeric($y2))
					$f = null;
				else
					$y = $y2;

			} else {

				$y = $y2;
			
			}

		}
		
		return array(
			'year' => $y,
			'valid_year' => is_numeric($y),
			'suffix' => $f,
			'author_1' => $a,
			'author_2' => $a2,
			'multiple_authors' => $m,
			'original' => $s
		);

	}

	private function resolveLiterature($obj)
	{

		$lit = $this->fixAuthors(trim((string)$obj->literature_title));

		$lit['text'] = trim((string)$obj->fullreference);

		$okSp = $unSp = null;

		if ($obj->keywords->keyword) {

			foreach($obj->keywords->keyword as $kKey => $kVal) {

				// apparently we're skipping literature that is not related to species or higher taxa
				if (preg_match('/\[m\](Species|Higher taxa)\[\/m\]/i',trim((string)$kVal->name))==0) continue;

				//[p][l][m]Species[/m][r]Emys orbicularis subsp. hellenica[/r][t][i]Emys orbicularis hellenica[/i][/t][/l][/p]
				//$speciesName = $this->replaceOldMarkUp($this->removeInternalLinks(trim((string)$kVal->name)),true);
				$speciesName = $this->extractLinkedSpeciesRatherThanDisplayed((string)$kVal->name);

				if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$speciesName])) {

					$okSp[] = $_SESSION['admin']['system']['import']['loaded']['species'][$speciesName]['id'];

				} else
				if (strpos($speciesName,' ')!==false) {
				
					$speciesNameSplit = trim(substr($speciesName,strpos($speciesName,' ')));

					if (isset($_SESSION['admin']['system']['import']['loaded']['species'][$speciesNameSplit])) {
					
						$okSp[] = $_SESSION['admin']['system']['import']['loaded']['species'][$speciesNameSplit]['id'];

					} else {

						$unSp[] = $speciesName;

					}

				} else {

					$unSp[] = $speciesName;

				}

			}
			
		}

		$lit['references'] = array('species' => $okSp,'unknown_species' => $unSp);

		return $lit;

	}

	private function addLiterature($obj)
	{

		$lit = $this->resolveLiterature($obj);

		$res = $this->models->Literature->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),				
				'author_first' => isset($lit['author_1']) ? $lit['author_1'] : null,
				'author_second' => (isset($lit['author_2']) && $lit['multiple_authors']==false) ? $lit['author_2'] : null,
				'multiple_authors' => $lit['multiple_authors']==true ? 1 : 0,
				'year' => (isset($lit['year'])  && $lit['valid_year'] == true) ? $lit['year'].'-00-00' : '0000-00-00',
				'suffix' => isset($lit['suffix']) ? $lit['suffix'] : null,
				'text' => isset($lit['text']) ? $lit['text'] : null,
			)
		);

		if ($res===true) {

			$_SESSION['admin']['system']['import']['loaded']['literature']['saved']++;

		} else {

			$_SESSION['admin']['system']['import']['loaded']['literature']['failed'][] =
				array('data' => $lit,'cause' => 'Failed to save lit. ref. "'.$lit['original'].'" ('.$res.').');

			return;

		}

		$id = $this->models->Literature->getNewId();
		$_SESSION['admin']['system']['import']['literature'][] = array('id' => $id,'original' => $lit['original']);

		foreach((array)$lit['references']['species'] as $kV) {
		
			if (empty($kV)) continue;

			$this->models->LiteratureTaxon->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),				
					'taxon_id' => $kV,
					'literature_id' => $id,
				)
			);

		}

		foreach((array)$lit['references']['unknown_species'] as $kV) {
		
			if (empty($kV)) continue;

			$_SESSION['admin']['system']['import']['loaded']['literature']['failed'][] =
				array('data' => $lit,'cause' => 'Saved lit. ref. "'.$lit['original'].'" but could not resolve reference to "'.$kV.'".');

		}

	}

	private function resolveGlossary($obj)
	{

		$t = trim((string)$obj->glossary_title);
		$d = trim((string)$obj->definition);
		
		if ($obj->glossary_synonyms->glossary_synonym) {

			foreach($obj->glossary_synonyms->glossary_synonym as $sKey => $sVal) {

				$s[] = $this->replaceOldMarkUp(trim((string)$sVal->name),true);
				
			}

		}

		if ($obj->gloss_multimedia->gloss_multimediafile) {

			foreach($obj->gloss_multimedia->gloss_multimediafile as $mKey => $mVal) {

				$m[] = array(
					'filename' => trim((string)$mVal->filename),
					'fullname' => trim((string)$mVal->fullname),
					'caption' => trim((string)$mVal->caption),
					'type' => trim((string)$mVal->multimedia_type),
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

	private function addGlossaryMedia($id,$data)
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

		if ($_SESSION['admin']['system']['import']['imagePath']==false)
			return array(
				'saved' => 'skipped',
				'data' => $data['filename'],
				'cause' => 'User specified no media import for project'
			);

		$fileToImport = $_SESSION['admin']['system']['import']['imagePath'].$data['filename'];

		if (file_exists($fileToImport)) {

			$thisMIME = $this->mimeContentType($fileToImport);
			
			if (isset($_SESSION['admin']['system']['import']['mimes'][$thisMIME])) {
			
				if ($_SESSION['admin']['system']['import']['thumbsPath']==false)
					$thumbName = null;
				else
					$thumbName = file_exists($_SESSION['admin']['system']['import']['thumbsPath'].$data['filename']) ? $data['filename'] : null;

					$this->models->GlossaryMedia->save(
						array(
							'id' => null,
							'project_id' => $this->getNewProjectId(),
							'glossary_id' => $id,
							'file_name' => $data['filename'],
							'original_name' => $data['fullname'],
							'mime_type' => $thisMIME,
							'file_size' => filesize($fileToImport),
							'thumb_name' => $thumbName ? $thumbName : null,
						)
					);

				return array(
					'saved' => true,
					'filename' => $data['filename'],
					'full_path' => $fileToImport,
					'thumb' => isset($thumbName) ? $thumbName : null,
					'thumb_path' => isset($thumbName) ? $_SESSION['admin']['system']['import']['thumbsPath'].$thumbName : null
				);

			} else {

				return array(
					'saved' => false,
					'data' => $data['filename'],
					'cause' => isset($thisMIME) ? 'MIME-type "'.$thisMIME.'" not allowed' : 'Could not determine MIME-type'
				);

			}
		
		} else {
		
			return array(
				'saved' => false,
				'data' => $data['filename'],
				'cause' => 'File "'.$data['filename'].'" does not exist'
			);
		
		}

	}

	private function addGlossary($obj)
	{

		$gls = $this->resolveGlossary($obj);

		$res = $this->models->Glossary->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'language_id' => $this->getNewDefaultLanguageId(),
				'term' => isset($gls['term']) ? $gls['term'] : null,
				'definition' => isset($gls['definition']) ? $gls['definition'] : null
			)
		);
		
		if ($res===true) {
		
			$_SESSION['admin']['system']['import']['loaded']['glossary']['saved']++;

		} else {

			$_SESSION['admin']['system']['import']['loaded']['glossary']['failed'][] =
				array(
					'data' => $gls,
					'cause' => 'Failed to save glossary item "'.$gls['term'].'" ('.$res.').'
				);
			return;

		} 
		
		$id = $this->models->Glossary->getNewId();

		$_SESSION['admin']['system']['import']['glossary'][] = array('id' => $id, 'term' => $gls['term']);

		if (isset($gls['synonyms'])) {

			foreach((array)$gls['synonyms'] as $sVal) {
			
				$lt = $this->models->GlossarySynonym->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'language_id' => $this->getNewDefaultLanguageId(),
						'glossary_id' => $id,
						'synonym' => $sVal
					)
				);
	
			}

		}

		if (isset($gls['multimedia'])) {
		
			$paths = $this->makePathNames($this->getNewProjectId());

			foreach((array)$gls['multimedia'] as $mVal) {

				$r = $this->addGlossaryMedia($id,$mVal);

				if ($r['saved']==true) {

					if (isset($r['full_path'])) $this->cRename($r['full_path'],$paths['project_media'].$r['filename']);
					if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$paths['project_thumbs'].$r['filename']);

				} else
				if ($r['saved']!=='skipped') {

					$_SESSION['admin']['system']['import']['loaded']['glossary']['failed'][] =
							array('data' => $gls,'cause' => 'Could not save "'.$mVal['filename'].'" ('.$r['cause'].').');

				}

			}

		}

	}

	// content & introduction
	private function addWelcomeTexts($obj)
	{

		if (!empty($obj->projectintroduction)) {

			$res = $this->models->Content->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Welcome',	
					'content' => $this->replaceOldMarkUp(trim((string)$obj->projectintroduction))
				)
			);

			if ($res===true) {

				$_SESSION['admin']['system']['import']['loaded']['welcome']['saved'][] = 'Saved welcome text.';

			} else {

				$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'Failed to save welcome text ('.$res.').';
			}

		} else {

			$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'No welcome text found.';

		}

		if (!empty($obj->contributors)) {

			$res = $this->models->Content->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Contributors',	
					'content' => $this->replaceOldMarkUp(trim((string)$obj->contributors))
				)
			);

			if ($res===true) {

				$_SESSION['admin']['system']['import']['loaded']['welcome']['saved'][] = 'Saved contributors text.';

			} else {

				$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'Failed to save contributors text ('.$res.').';
			}

		} else {

			$_SESSION['admin']['system']['import']['loaded']['welcome']['failed'][] = 'No contributors text found.';

		}

	}

	private function addIntroduction($obj)
	{

		$this->models->IntroductionPage->save(
			array(
				'project_id' => $this->getNewProjectId(),
				'show_order' => $_SESSION['admin']['system']['import']['loaded']['introduction']['show_order']++,
				'got_content' => '1'
			)
		);
		
		$id = $this->models->IntroductionPage->getNewId();
		
		$res = $this->models->ContentIntroduction->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(),
				'language_id' => $this->getNewDefaultLanguageId(),
				'page_id' => $id,
				'topic' => $this->replaceOldMarkUp(trim((string)$obj->introduction_title),true),
				'content' => $this->replaceOldMarkUp($this->replaceInternalLinks(trim((string)$obj->text)))
			)
		);

		if ($res===true) {

			$_SESSION['admin']['system']['import']['loaded']['introduction']['saved'][] = 'Saved topic "'.trim((string)$obj->introduction_title).'".';

		} else {
				
			$_SESSION['admin']['system']['import']['loaded']['introduction']['failed'][] = 'Failed to save topic "'.trim((string)$obj->introduction_title).'" ('.$res.').';
			return;

		}
 
 		$img = trim((string)$obj->overview);
 
		if ($_SESSION['admin']['system']['import']['imagePath'] && $img) {
		
			$paths = $_SESSION['admin']['system']['import']['paths'];

			if ($this->cRename(
				$_SESSION['admin']['system']['import']['imagePath'].$img,
				$paths['project_media'].$img
				)
			) {
		
				$this->models->IntroductionMedia->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'page_id' => $id,
						'file_name' => $img,
						'original_name' => $img,
						'mime_type' => @$this->mimeContentType($img),
						'file_size' => @filesize($paths['project_media'].$img),
						'thumb_name' => null,
					)
				);

			}
        
        }

	}

	// dichotomous key
	private function createKeyStep($step,$stepIds,$stepAdd=0)
	{
	
		$paths = $_SESSION['admin']['system']['import']['paths'];
	
		$fileName = isset($step->keyoverviewpicture) ? (string)$step->keyoverviewpicture : null;

		if ($fileName) $this->cRename($_SESSION['admin']['system']['import']['imagePath'].$fileName,$paths['project_media'].$fileName);


		$k = $this->models->Keystep->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'number' => ($step=='god' ? -1 : (intval(trim((string)$step->pagenumber)) + $stepAdd)),
				'is_start' => 0,
				'image' => $fileName
			)
		);

		$stepId = $stepIds[($step=='god' ? -1 : trim((string)$step->pagenumber))] = $this->models->Keystep->getNewId();

		$this->models->ContentKeystep->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'keystep_id' => $stepId,
				'language_id' => $this->getNewDefaultLanguageId(),
				'title' => 
					($step=='god' ? 
						'Choose key type' : 
						(isset($step->pagetitle) ?
							trim((string)$step->pagetitle) :
							trim((string)$step->pagenumber)
						)
					),
				'content' =>
					($step=='god' ?
						'Choose between picture key and text key' : 
						(isset($step->pagetitle) ?
							trim((string)$step->pagetitle) :
							trim((string)$step->pagenumber)
						)
					)
			)
		);

		return $stepIds;

	}

	private function createKeyStepChoices($step,$stepIds)
	{

		$paths = $_SESSION['admin']['system']['import']['paths'];

		if ($step->text_choice) {
			$choices = $step->text_choice;
		} else {
			$choices = $step->pict_choice;
		}

		$i=0;

		foreach($step as $key => $val) {

			if ($key=='text_choice' || $key=='pict_choice') {

				$resStep = (trim((string)$val->destinationtype)=='turn' ? 
								(isset($stepIds[trim((string)$val->destinationpagenumber)]) ?
									$stepIds[trim((string)$val->destinationpagenumber)] :
									null
								) :
								null
							);

				// text_key->keypage->text_choice->destinationtaxonname = "Family Pontoporiidae". *SIGH*
				$destinationtaxonname =
					$this->removeRankFromTaxonName(trim((string)$val->destinationtaxonname));

				$resTaxon = (trim((string)$val->destinationtype)=='taxon' ?  
								(isset($_SESSION['admin']['system']['import']['loaded']['species'][$destinationtaxonname]['id']) ?
									$_SESSION['admin']['system']['import']['loaded']['species'][$destinationtaxonname]['id']:
									null
								) : 
								null
							);
	
				$fileName = isset($val->picturefilename) ? trim((string)$val->picturefilename) : null;
	
				if ($fileName && !file_exists($_SESSION['admin']['system']['import']['imagePath'].$fileName)) {
	
					$error[] = array(
						'cause' => 'Picture key image "'.$fileName.'" does not exist (choice created anyway)'
					);
					
					$fileName = null;
	
				} else
				if ($fileName) {
	
					$this->cRename($_SESSION['admin']['system']['import']['imagePath'].$fileName,$paths['project_media'].$fileName);
	
				}
	
				if (
						isset($val->leftpos) ||
						isset($val->toppos) ||
						isset($val->width) ||
						isset($val->height)
					) {
	
						$params =
							json_encode(
								array(
									'leftpos' => isset($val->leftpos) ? trim($val->leftpos) : null,
									'toppos' => isset($val->toppos) ? trim($val->toppos) : null,
									'width' => isset($val->width) ? trim($val->width) : null,
									'height' => isset($val->height) ? trim($val->height) : null,
								)
							);
	
				}
	
				if (isset($val->captiontext)) {

					$txt = $this->replaceOldMarkUp(trim((string)$val->captiontext));
					$p = trim((string)$step->pagenumber).trim((string)$val->choiceletter).'.';
					if (substr($txt,0,strlen($p))==$p) $txt = trim(substr($txt,strlen($p)));
					if (strlen($txt)==0) $txt = $this->replaceOldMarkUp(trim((string)$val->captiontext));
	
				} else
				if (isset($val->picturefilename)) {
	
					$txt = trim((string)$val->picturefilename);
				}
				
				$this->models->ChoiceKeystep->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'keystep_id' => ($step=='god' ? $stepIds['godId'] : $stepIds[trim((string)$step->pagenumber)]),
						'show_order' => (1 + $i++),
						'choice_img' => isset($fileName) ? $fileName : null,
						'choice_image_params' => isset($params) ? $params : null,
						'res_keystep_id' => $resStep,
						'res_taxon_id' => $resTaxon,
					)
				);
	
				$this->models->ChoiceContentKeystep->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'choice_id' => $this->models->ChoiceKeystep->getNewId(),
						'language_id' => $this->getNewDefaultLanguageId(),
						'choice_txt' => isset($txt) ? $txt : null
					)
				);
					
			}
		
		}

	}

	private function addKeyDichotomous($obj,$node)
	{

		$stepAdd = 0;

		if ($node == 'text_key' && count($obj->keypage) > 1) {

			$keyStepIds = null;

			// text key
			// create steps first (no choices yet)
			foreach($obj->keypage as $key => $val) {

				$keyStepIds = $this->createKeyStep($val,$keyStepIds);
				if ($key==0) $firstTxtStepId = current($keyStepIds);	

			}

			// create choices
			foreach($obj->keypage as $key => $val) {
	
				$this->createKeyStepChoices($val,$keyStepIds);
	
			}

			$k = $this->models->Keystep->_get(
				array(
					'id' => array('project_id' => $this->getNewProjectId()),
					'columns' => 'max(number) as `last`'
				)
			);
			
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
			foreach($obj->keypage as $key => $val) {

				$pictStepIds = $this->createKeyStep($val,$pictStepIds,$stepAdd);
				if ($key==0) $firstPictStepId = current($pictStepIds);
		
			}

			// create choices
			foreach($obj->keypage as $key => $val) {
	
				$this->createKeyStepChoices($val,$pictStepIds);
	
			}
			
			$_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key'] = $firstPictStepId;

		}

		if ($_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['text_key']!==false &&
			$_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key']!==false) {

			$keyStepIds = $this->createKeyStep('god',$keyStepIds);
			
			$firstTxtStepId = $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['text_key'];
			$firstPictStepId = $_SESSION['admin']['system']['import']['loaded']['key_dich']['keys']['pict_key'];

			end($keyStepIds);

			$this->models->ChoiceKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'keystep_id' =>  current($keyStepIds),
					'show_order' => 1,
					'res_keystep_id' => $firstTxtStepId
				)
			);

			$this->models->ChoiceContentKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'choice_id' => $this->models->ChoiceKeystep->getNewId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'choice_txt' => 'Text key'
				)
			);

			$this->models->ChoiceKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'keystep_id' =>  current($keyStepIds),
					'show_order' => 2,
					'res_keystep_id' => $firstPictStepId
				)
			);

			$this->models->ChoiceContentKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'choice_id' => $this->models->ChoiceKeystep->getNewId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'choice_txt' => 'Picture key'
				)
			);

			$this->models->Keystep->update(
				array('number' => 'number+1'),
				array(
					'project_id' => $this->getNewProjectId(),
					'number >=' => '1'
				)
			);

			$this->models->Keystep->update(
				array('number' => '1'),
				array(
					'project_id' => $this->getNewProjectId(),
					'number =' => '-1'
				)
			);

		}

		$this->models->Keystep->update(
			array('is_start' => '1'),
			array(
				'project_id' => $this->getNewProjectId(),
				'number =' => '1'
			)
		);

	}

	// matrix key
	private function resolveMatrices($obj)
	{

		$matrixname = !empty($obj->identify->id_file->filename) ? trim((string)$obj->identify->id_file->filename) : null;

		if ($matrixname) {

			//?? (string)$obj->identify->id_file->obj_link

			$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['name'] = str_replace('.adm','',$matrixname);
			
			if (isset($obj->identify->id_file->characters->character_))
				$chars = $obj->identify->id_file->characters->character_;
			else
			if (isset($obj->identify->id_file->characters->character))
				$chars = $obj->identify->id_file->characters->character;
			else
				$chars = null;

			foreach($chars as $char) {

				//character_type ?? welke mogelijkheden + resolvement: Text 

				$charname = trim((string)$char->character_name);
				$chartype = trim((string)$char->character_type);
				
				foreach($char->states->state as $stat) {

					$statename = trim((string)$stat->state_name);
					$statemin = trim((string)$stat->state_min);
					$statemax = trim((string)$stat->state_max);
					$statemean = trim((string)$stat->state_mean);
					$statesd = trim((string)$stat->state_sd);
					$statefile = trim((string)$stat->state_file);

					//stat->state_file; immer leeg

					$adHocIndex = 
						md5(
							$matrixname.
							$charname.
							$statename.
							$statemin.
							$statemax.
							$statemean.
							$statesd.
							$statefile
						);

					$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['charname'] = $charname;
					$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['chartype'] = $chartype;
					$_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$matrixname]['characteristics'][$charname]['states'][$adHocIndex] = array(
						'statename'=>$statename,
						'statemin'=>$statemin,
						'statemax'=>$statemax,
						'statemean'=>$statemean,
						'statesd'=>$statesd,
						'statefile'=>$statefile,
					);

				}

			}

		}

	}

	private function resolveCharType($t)
	{
	
		// ??? HAVE ONLY SEEN 'Text' & 'Picture'
		switch($t) {
			case 'Text':
				return 'text';
				break;
			case 'Distribution':
				return 'distribution';
				break;
			case 'Picture':
				return 'media';
				break;
			case 'Range':
				return 'range';
				break;
		}
	
	}

	private function saveMatrices($m)
	{
		
		$paths = 
			isset($_SESSION['admin']['system']['import']['paths']) ? 
				$_SESSION['admin']['system']['import']['paths'] : 
				$this->makePathNames($this->getNewProjectId());
		
		$d = $error = null;

		foreach((array) $m as $key => $val) {

			$this->models->Matrix->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'got_names' => 1
				)
			);

			
			$m[$key]['id'] = $this->models->Matrix->getNewId();

			$this->models->MatrixName->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'matrix_id' => $m[$key]['id'],
					'language_id' => $this->getNewDefaultLanguageId(),
					'name' => $val['name']
				)
			);

			foreach((array)$val['characteristics'] as $cKey => $cVal) {

				$c = $this->models->Characteristic->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'type' => $this->resolveCharType($cVal['chartype']),
						'got_labels' => 1
					)
				);

				$m[$key]['characteristics'][$cKey]['id'] = $this->models->Characteristic->getNewId();

				$this->models->CharacteristicLabel->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
						'language_id' => $this->getNewDefaultLanguageId(),
						'label' => $cVal['charname']
					)
				);

				$cm = $this->models->CharacteristicMatrix->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'matrix_id' => $m[$key]['id'],
						'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
					)
				);

				foreach((array)$cVal['states'] as $sKey => $sVal) {

					$fileName = isset($sVal['statefile']) ? $sVal['statefile'] : null;
		
					if ($fileName && !file_exists($_SESSION['admin']['system']['import']['imagePath'].$fileName)) {
		
						$error[] = array(
							'cause' => 'Matrix state image "'.$fileName.'" does not exist (state created anyway)'
						);

						$fileName = null;
		
					} else
					if ($fileName) {
		
						$this->cRename($_SESSION['admin']['system']['import']['imagePath'].$fileName,$paths['project_media'].$fileName);
		
					}

					$c = $this->models->CharacteristicState->save(
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
						)
					);

					$m[$key]['characteristics'][$cKey]['states'][$sKey]['id'] = $this->models->CharacteristicState->getNewId();

					$d[$sKey] = array(
						'matrix_id' => $m[$key]['id'],
						'characteristic_id' => $m[$key]['characteristics'][$cKey]['id'],
						'state_id' => $m[$key]['characteristics'][$cKey]['states'][$sKey]['id']
					);

					$this->models->CharacteristicLabelState->save(
						array(
							'id' => null,
							'project_id' => $this->getNewProjectId(),
							'state_id' => $m[$key]['characteristics'][$cKey]['states'][$sKey]['id'],
							'language_id' => $this->getNewDefaultLanguageId(),
							'label' => $sVal['statename']
						)
					);

				}

			}

		}

		return array(
			'matrices' => $d,
			'failed' => $error
		);

	}

	private function connectMatrices($obj)
	{

		$matrixname = !empty($obj->identify->id_file->filename) ? trim((string)$obj->identify->id_file->filename) : null;

		if ($matrixname) {

			if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$obj->name)]['id'])) {

				$taxonid = $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$obj->name)]['id'];

				foreach($obj->identify->id_file->characters->character_ as $char) {

					$charname = trim((string)$char->character_name);
					
					foreach($char->states->state as $stat) {

						$adHocIndex = 
							md5(
								$matrixname.
								$charname.
								trim((string)$stat->state_name).
								trim((string)$stat->state_min).
								trim((string)$stat->state_max).
								trim((string)$stat->state_mean).
								trim((string)$stat->state_sd).
								trim((string)$stat->state_file)
							);

						if (isset($_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex])) {

							$this->models->MatrixTaxon->setNoKeyViolationLogging(true);

							$this->models->MatrixTaxon->save(
								array(
									'id' => null,
									'project_id' => $this->getNewProjectId(),
									'matrix_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['matrix_id'],
									'taxon_id' => $taxonid,
								)
							);

							$this->models->MatrixTaxonState->setNoKeyViolationLogging(true);

							$this->models->MatrixTaxonState->save(
								array(
									'id' => null,
									'project_id' => $this->getNewProjectId(),
									'matrix_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['matrix_id'],
									'characteristic_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['characteristic_id'],
									'state_id' => $_SESSION['admin']['system']['import']['loaded']['key_matrix']['matrices'][$adHocIndex]['state_id'],
									'taxon_id' => $taxonid,
								)
							);
				
						}

					}

				}

			} else {

				$_SESSION['admin']['system']['import']['loaded']['key_matrix']['failed'][] = array(
					'cause' => 'Species "'.trim((string)$obj->name).'" in matrix key does not exist and has been discarded',
					'data' => trim((string)$obj->name)
				);

			}

		} // not part of any matrix

	}

	// map
	private function saveMapItemType($type)
	{
	
		$this->models->GeodataType->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'colour' => 'FFFFFF'
			)
		);

		$id = $this->models->GeodataType->getNewId();

		$this->models->GeodataTypeTitle->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'language_id' => $this->getNewDefaultLanguageId(),
				'type_id' => $id,
				'title' => $type
			)
		);
	
		return $id;

	}

	private function updateMapTypeColours()
	{

		$d = $this->models->GeodataType->_get(
			array(
				'id' => array(
					'project_id' => $this->getNewProjectId(),
				)
			)
		);	
	
		$c = array('ee0000','0033ff','00ee00','ffff00','ff66ff','990099','669999','666699','cc9966','ffcc00','008700');

		foreach((array)$d as $key => $val) {
			$this->models->GeodataType->update(
				array(
					'colour' => $c[$key % count((array)$c)]
				),
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId()
				)
			);
			
		}
		
	}


	private function doSaveMapItem($occurrence)
	{

		if (
			(!$occurrence['taxonId']) ||
			(count((array)$occurrence['nodes'])<=2) ||
			(!$occurrence['typeId'])
		) {

			$_SESSION['admin']['system']['import']['loaded']['map']['skipped']++;
			return;

		}
				
		// remove the last node if it is identical to the first, just in case
		if ($occurrence['nodes'][0]==$occurrence['nodes'][count((array)$occurrence['nodes'])-1]) array_pop($occurrence['nodes']);
	
		// create a string for mysql (which does require the first and last to be the same)
		$geoStr = array();
		foreach((array)$occurrence['nodes'] as $sVal) $geoStr[] = $sVal[0].' '.$sVal[1];
		$geoStr = implode(',',$geoStr).','.$geoStr[0];

		$this->models->OccurrenceTaxon->setNoKeyViolationLogging(true);
	
		$res = $this->models->OccurrenceTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'taxon_id' => $occurrence['taxonId'],
				'type_id' => $occurrence['typeId'],
				'type' => 'polygon',
				'boundary' => "#GeomFromText('POLYGON((".$geoStr."))',".$this->controllerSettings['SRID'].")",
				'boundary_nodes' => json_encode($occurrence['nodes']),
				'nodes_hash' => md5(json_encode($occurrence['nodes']))
			)
		);
		
		if ($res===true)
			$_SESSION['admin']['system']['import']['loaded']['map']['saved']++;
		else
			$_SESSION['admin']['system']['import']['loaded']['map']['failed']++;

	}
	
	
	private function saveL2Map($p)
	{

		$this->models->L2Map->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(),
				'name' => $p['name'],
				'coordinates' => $p['coordinates'],
				'rows' => $p['rows'],
				'cols' => $p['cols']
			)
		);

		$_SESSION['admin']['system']['import']['loaded']['map']['maps'][$p['name']]['id'] = $this->models->L2Map->getNewId();
		
		$mapImageName = $p['name'].'.GIF';
		
		$paths = $this->makePathNames($this->getNewProjectId());

		if (
			$this->cRename(
				$_SESSION['admin']['system']['import']['imagePath'].$mapImageName,
				$paths['project_media_l2_maps'].$mapImageName
			)
		) {

			$this->addMessage('Copied L2-map "'.$p['name'].'".');

			$this->models->L2Map->update(
				array(
					'image' => $mapImageName
				),
				array(
					'id' => $_SESSION['admin']['system']['import']['loaded']['map']['maps'][$p['name']]['id']
				)
			);
		
		} else {

			$this->addError('Missing map file: "'.$mapImageName.'"<br />(will still function properly if map exists in default folder).');
	
		}

	}
	

	private function saveLN2Cell($p)
	{	

		$this->models->L2OccurrenceTaxon->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(),
				'taxon_id' => $p['taxon_id'],
				'type_id' => $p['type_id'],
				'map_id' => $p['map_id'],
				'square_number' => $p['square_number'],
				'legend' => $p['legend'],
				'coordinates' => $p['coordinates']
			)
		);

	}


	private function saveMapItem($obj)
	{
	
		if (isset($_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$obj->name)]['id'])) {
		
			if (!isset($obj->distribution)) return;
			
			$taxonId = $_SESSION['admin']['system']['import']['loaded']['species'][trim((string)$obj->name)]['id'];

			foreach($obj->distribution->map as $vKey => $vVal) {

				if (!isset($_SESSION['admin']['system']['import']['loaded']['map']['maps'][trim((string)$vVal->mapname)])) {

					/*
			
						<mapname>North Atlantic</mapname>
						<specs>90,100,0,-80,5,5</specs>
						
						90,100 = linksboven = 90�N 100�W = 90 -100
						0, -80 = rechtonder = 0�S 80�E = -0 80
						5,5 - cell size (ASSUMING WxH)
			
					*/

					$d = explode(',',trim((string)$vVal->specs));

					$lat1 = floatval($d[0]);
					$lat2 = floatval($d[2]);
					//$lon1 = (-1 * floatval($d[1]));
					//$lon2 = (-1 * floatval($d[3]));
					$lon1 = floatval($d[1]);
					$lon2 = floatval($d[3]);
					$sqW = floatval($d[4]);
					$sqH = floatval($d[5]);


					//$widthInSquares = (($lon2 > $lon1 ? $lon2 - $lon1 : (180-$lon1) - $lon2)) / $sqW;	//((int)($d[1] - $d[3]) / $d[4]),
					$widthInSquares = (($lon1 >= $lon2) ? $lon1 - $lon2 : 360 + $lon1 - $lon2) / $sqW;
					$heightInSquares = (($lat1 - $lat2) / $sqH);
					$coordinates = 
						array(
							'topLeft' => array('lat' => $lat1,'long' => $lon1),			//array('lat' => (int)$d[0],'long' => (-1 * (int)$d[1])),
							'bottomRight' => array('lat' => $lat2,'long' => $lon2)		//array('lat' => (int)$d[2],'long' => (-1 * (int)$d[3]))
						);

					$_SESSION['admin']['system']['import']['loaded']['map']['maps'][trim((string)$vVal->mapname)] =
						array(
							'label' => trim((string)$vVal->mapname),
							'specs' => trim((string)$vVal->specs),
							'coordinates' => $coordinates,
							'square' => array('width' => $sqW,'height' => $sqH),
							'widthInSquares' => $widthInSquares,
							'heightInSquares' => $heightInSquares
						);
						
					$this->saveL2Map(
						array(
							'name' => trim((string)$vVal->mapname),
							'coordinates' => json_encode($coordinates),
							'rows' => $heightInSquares,
							'cols' => $widthInSquares
						)
					);

				}
				
				$maps = $_SESSION['admin']['system']['import']['loaded']['map']['maps'];
				
				foreach($vVal->squares->square as $sKey => $sVal) {

					if (!isset($_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string)$sVal->legend)]))
						$_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string)$sVal->legend)] = $this->saveMapItemType(trim((string)$sVal->legend));

					// determining the position of the square in the map grid
					$row = floor(trim((string)$sVal->number) / $maps[trim((string)$vVal->mapname)]['widthInSquares']);
					$col = trim((string)$sVal->number) % $maps[trim((string)$vVal->mapname)]['widthInSquares'];
					if ($col==0) $col = $maps[trim((string)$vVal->mapname)]['widthInSquares'];

					$mapname = trim((string)$vVal->mapname);
					
					$n1Lat = $n2Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - ($row * $maps[$mapname]['square']['height']);
					$n1Lon = $maps[$mapname]['coordinates']['topLeft']['long'] + (($col-1) * $maps[$mapname]['square']['width']);
					$n1Lon = $n4Lon = ($n1Lon >= 180 ? -360 + $n1Lon  : $n1Lon);
					$n2Lon = $maps[$mapname]['coordinates']['topLeft']['long'] + ($col * $maps[$mapname]['square']['width']);
					$n2Lon = $n3Lon = ($n2Lon > 180 ? -360 + $n2Lon : $n2Lon);
					$n3Lat = $n4Lat = $maps[$mapname]['coordinates']['topLeft']['lat'] - (($row+1) * $maps[$mapname]['square']['height']);

					$occurrence = array(
						'taxon' => trim((string)$obj->name),
						'taxonId' => $taxonId,
						'map' => $mapname,
						'square' => trim((string)$sVal->number),
						'row' => $row,
						'col' => $col,
						'legend' => trim((string)$sVal->legend),
						'typeId' => $_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string)$sVal->legend)],
						'nodes' => array(array($n1Lat,$n1Lon),array($n2Lat,$n2Lon),array($n3Lat,$n3Lon),array($n4Lat,$n4Lon))
					);
					
					$this->doSaveMapItem($occurrence);

					$this->saveLN2Cell(
						array(
							'taxon_id' => $taxonId,
							'type_id' => $_SESSION['admin']['system']['import']['loaded']['map']['types'][trim((string)$sVal->legend)],
							'square_number' => trim((string)$sVal->number),
							'legend' => trim((string)$sVal->legend),
							'map_id' => $maps[trim((string)$vVal->mapname)]['id'],
							'coordinates' => json_encode(array(array($n1Lat,$n1Lon),array($n2Lat,$n2Lon),array($n3Lat,$n3Lon),array($n4Lat,$n4Lon)))
						)
					);

				}

			}

		} else {
		
			// unknown species
		
		}

	}


	/* auxiliry functions */
	private function cRename($from,$to)
	{
	
		//return rename($from,$to); // generates odd errors on some linux filesystems
	
		if(copy($from,$to)) {

			if ($this->_deleteOldMediaAfterImport===true) @unlink($from);

			return true;

		} else {

			return false;

		}

	}

	private function makeMediaTargetPaths()
	{
	
		$paths = $this->makePathNames($this->getNewProjectId());

		if (!file_exists($paths['project_media'])) mkdir($paths['project_media']);
		if (!file_exists($paths['project_thumbs'])) mkdir($paths['project_thumbs']);
		if (!file_exists($paths['project_media_l2_maps'])) mkdir($paths['project_media_l2_maps']);

	}

	private function replaceOldMarkUp($s,$removeNotReplace=false)
	{
	
		$r = array('<b>','</b>','<i>','</i>','<br />', '<p>', '</p>');
	
		return str_replace(array('[b]','[/b]','[i]','[/i]','[br]','[p]','[/p]'),($removeNotReplace ? null : $r),$s);
	
	}

	private function resolveInternalLinks($s)
	{

		$controllers = 
			array(
				'Content pages' => array(
					'controller' => 'linnaeus',
					'param' => 'id',
				),
				'Glossary' => // [m]Glossary[/m]
					array(
						'controller' => 'glossary',
						'url' => 'term.php',
						'param' => 'id',
					),
				'Literature' => // [m]Literature[/m]
					array(
						'controller' => 'literature',
						'url' => 'reference.php',
						'param' => 'id',
					),
				'Species' => // [m]Species[/m]
					array(
						'controller' => 'species',
						'url' => 'taxon.php',
						'param' => 'id',
					),
				'Higher taxa' => // [m]Higher taxa[/m]
					array(
						'controller' => 'highertaxa',
						'url' => 'taxon.php',
						'param' => 'id',
					),
				'Dichotomous key' => array(
					'controller' => 'key',
					'param' => 'id',
				),
				'Map key index' => array(
					'controller' => 'mapkey',
					'param' => 'id',
				),
				'Matrix key index' => array(
					'controller' => 'matrixkey',
					'url' => 'matrices.php',
					'param' => 'id',
				)
			);


//		$d = rtrim($s[count((array)$s)-1],'[/t]');
		$d = preg_replace('/\[\/t\]$/','',$s[count((array)$s)-1]);
		$d = preg_split('/(\[\/m\]\[[r]\])|(\[\/r\]\[[t]\])/iU',$d);

		if (isset($d[0]) && isset($controllers[$d[0]])) {

			if ($controllers[$d[0]]['controller']=='glossary' && isset($_SESSION['admin']['system']['import']['lookupArrays']['glossary'][$d[1]])) {
				$id = $_SESSION['admin']['system']['import']['lookupArrays']['glossary'][$d[1]];

			}

			if ($controllers[$d[0]]['controller']=='literature' && isset($_SESSION['admin']['system']['import']['lookupArrays']['literature'][$d[1]])) {
				$id = $_SESSION['admin']['system']['import']['lookupArrays']['literature'][$d[1]];

			}

			if ($controllers[$d[0]]['controller']=='species' && isset($_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]])) {
				$id = $_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]];
			}

			if ($controllers[$d[0]]['controller']=='highertaxa' && isset($_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]])) {
				$id = $_SESSION['admin']['system']['import']['lookupArrays']['species'][$d[1]];
			}
	
			if (isset($id) && isset($d[2])) {

				if ($this->generalSettings['useJavascriptLinks']) {	

					$href =
						"goIntLink('".$controllers[$d[0]]['controller']."',".
						"'".(isset($controllers[$d[0]]['url']) ? $controllers[$d[0]]['url'] : 'index.php')."'".
						(isset($controllers[$d[0]]['param']) ? ",['".$controllers[$d[0]]['param'].":".$id."']" : null).
						");";

					return '<span class="internal-link" onclick="'.$href.'">'.trim($d[2]).'</span>';

				} else {

					$href = 
						'../'.$controllers[$d[0]]['controller'].'/'.
						(isset($controllers[$d[0]]['url']) ? $controllers[$d[0]]['url'] : 'index.php').
						(isset($controllers[$d[0]]['param']) ? '?'.$controllers[$d[0]]['param'].'='.$id : null);
						
					return '<a class="internal-link" href="'.$href.'">'.trim($d[2]).'</a>';

				}


			}
			
		} else
		if (isset($d[0]) && isset($_SESSION['admin']['system']['import']['lookupArrays']['modules'][$d[0]][$d[1]])) {
		
			$ids = $_SESSION['admin']['system']['import']['lookupArrays']['modules'][$d[0]][$d[1]];
		
			if (isset($id) && isset($d[2])) {
	
				if ($this->generalSettings['useJavascriptLinks']) {	

					$href = "goIntLink('module','topic.php',['modId:".$ids['moduleId']."','id:".$ids['pageId']."'])";
	
					return '<span class="internal-link" onclick="'.$href.'">'.trim($d[2]).'</span>';

				} else {

					$href = '../module/topic.php?modId='.$ids['moduleId'].'&id='.$ids['pageId'];
	
					return '<a class="internal-link" href="'.$href.'">'.trim($d[2]).'</a>';
					
				}



			}
				
		}


		return isset($d[2]) ? trim($d[2]) : $s[0];
	
	}

	private function resolveEmbeddedLinks($s)
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

		$d = preg_split('/\[\/f\]\[t\]/iU',$s[count((array)$s)-1]);
		
		if (isset($d[1]) && strpos($d[1],'[/im]')!==false) $type = 'image';
		else
		if (isset($d[1]) && strpos($d[1],'[/mo]')!==false) $type = 'movie';
		else
		if (isset($d[1]) && strpos($d[1],'[/s]')!==false) $type = 'sound';
		else return $s[0];

		$d = preg_replace('/(\[\/im\]|\[\/mo\]|\[\/s\]|\[f\]|\[\/t\])/','',$d);

		if (isset($d[0])) $filename = $d[0]; else return $s[0];
		$label = isset($d[1]) ? $d[1] : $filename;

		//if (file_exists($_SESSION['admin']['system']['import']['paths']['project_media'].$filename))
		
		if ($type=='image') {

			return '<span
				class="internal-link" 
				onclick="showMedia(\''.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'\',\''.addslashes($label).'\');">'.
				$label.' [IMG]</span>';

//			NO INLINE
//			return '<img
//				onclick="showMedia(\''.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'\',\''.addslashes($label).'\');"
//				src="'.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'"
//				class="media-image">';

		} else
		if ($type=='movie') {

			return '<span
				class="internal-link" 
				onclick="showMedia(\''.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'\',\''.addslashes($label).'\');">'.
				$label.' [VID]</span>';

//			return '<img
//				onclick="showMedia(\''.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'\',\''.addslashes($label).'\');" 
//				src="../../media/system/video.jpg" 
//				class="media-image">';

		} else
		if ($type=='sound') {

			return '<object type="application/x-shockwave-flash" data="'.
						$this->generalSettings['soundPlayerPath'].$this->generalSettings['soundPlayerName'].'" height="20" width="130">
						<param name="movie" value="'.$this->generalSettings['soundPlayerName'].'">
						<param name="FlashVars" value="mp3='.$_SESSION['admin']['system']['import']['paths']['media_url'].$filename.'">
					</object>';

		} else return $s[0];
	
	}

	private function replaceInternalLinks($s)
	{

		// regular links
		$d = preg_replace_callback('/(\[l\]\[m\](.*)\[\/l\])/sU',array($this,'resolveInternalLinks'),$s);

		// embedded media
		$d = preg_replace_callback('/((\[l\]\[im\]|\[l\]\[mo\]|\[l\]\[s\])(.*)\[\/l\])/sU',array($this,'resolveEmbeddedLinks'),$d);

		return $this->replaceOldMarkUp($d);

	}

	private function removeInternalLinks($s)
	{
	
		// removes internal links without replacing them, just leavinf the linked text

		if (strpos($s,'[l]')!==false && strpos($s,'[/l]')!==false) {
		
			$d = preg_split('/(\[t\]|\[\/t\])/',$s);
							
			$t = isset($d[1]) ? $d[1] : null;			

			return preg_replace('/\[l\](.*)\[\/l\]/',$t,$s);

		} else {

			return $s;

		}
		
	}

	private function importPostProcessing()
	{
	
		$res = $this->fixOldInternalLinks();

		// additional texts
		$this->addModuleToProject(10);
		$this->grantModuleAccessRights(10);

		// index
		$this->addModuleToProject(11);
		$this->grantModuleAccessRights(11);

		// search
		$this->addModuleToProject(12);
		$this->grantModuleAccessRights(12);
	
	}

	private function createLookupArrays()
	{


		$s = $g = $l = $m = null;

		if (isset($_SESSION['admin']['system']['import']['loaded']['species'])) {

			foreach((array)$_SESSION['admin']['system']['import']['loaded']['species'] as $val) {
				if (isset($val['taxon']) && isset($val['id'])) $s[$val['taxon']] = $val['id'];
			}
			
		}

		if (isset($_SESSION['admin']['system']['import']['glossary'])) {

			foreach((array)$_SESSION['admin']['system']['import']['glossary'] as $val) {
				if (isset($val['term']) && isset($val['id'])) $g[$val['term']] = $val['id'];
			}

		}

		if (isset($_SESSION['admin']['system']['import']['literature'])) {

			foreach((array)$_SESSION['admin']['system']['import']['literature'] as $val) {
				if (isset($val['original']) && isset($val['id'])) $l[$val['original']] = $val['id'];
			}

		}

		if (isset($_SESSION['admin']['system']['import']['freeModules']['ids'])) {
		
			$m = $_SESSION['admin']['system']['import']['freeModules']['ids'];

		}

		return array(
			'species' => $s,
			'glossary' => $g,
			'literature' => $l,
			'modules' => $m,
		);

	}

	private function fixOldInternalLinks()
	{

		$_SESSION['admin']['system']['import']['lookupArrays'] = $this->createLookupArrays();

		$d = $this->models->ContentTaxon->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->ContentTaxon->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'content' => $this->replaceInternalLinks($val['content'])
				)
			);

		}

		$d = $this->models->Literature->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->Literature->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'text' => $this->replaceInternalLinks($val['text'])
				)
			);
			
		}

		$d = $this->models->Glossary->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->Glossary->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'definition' => $this->replaceInternalLinks($val['definition'])
				)
			);

		}

		$d = $this->models->Content->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->Content->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'content' => $this->replaceInternalLinks($val['content'])
				)
			);
			
		}

		$d = $this->models->ContentKeystep->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->ContentKeystep->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'title' => $this->replaceInternalLinks($val['title']),
					'content' => $this->replaceInternalLinks($val['content'])
				)
			);
			
		}

		$d = $this->models->ChoiceContentKeystep->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->ChoiceContentKeystep->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'choice_txt' => $this->replaceInternalLinks($val['choice_txt'])
				)
			);
			
		}

		$d = $this->models->ContentFreeModule->_get(array('id' => array('project_id' => $this->getNewProjectId())));

		foreach((array)$d as $val) {

			$this->models->ContentFreeModule->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),
					'content' => $this->replaceInternalLinks($val['content'])
				)
			);
			
		}

	}
	
	private function mimeContentType($filename)
	{
	
		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}

	}

	private function getCustomModules()
	{

		if (!isset($_SESSION['admin']['system']['import']['additionalModules'])) {

			$d = new XMLReader;
			$r = array();
			if ($d->open($_SESSION['admin']['system']['import']['file']['path'])) {
				while($d->read()) {
					if($d->nodeType == XMLReader::ELEMENT && $d->depth == 1 && !in_array((string)$d->name,$this->_knownModules)) {
						$r[] = $d->name;
					}
				}
			}
			
			$_SESSION['admin']['system']['import']['additionalModules'] = $r;
			
		}

		return $_SESSION['admin']['system']['import']['additionalModules'];
	
	}


}