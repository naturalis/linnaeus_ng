<?php
/*

FREE MODULES MEDIA
FREE MODULES INTERNAL LINKS
FREE MODULES RIGHTS requires login



page time out? (image copy)


wtf is?
	$d->projectclassification
	$d->projectnomenclaturecode


need to set manually!
	project.includes_hybrids
	project.css_url
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
	
	???			.classification --> "Five kingdoms"
	???			.nomenclaturecode --> "ICZM"

	proj_literature->proj_reference->keywords->keyword
	ignored: literary references to glossary terms



HAVE TO MERGE GEO DATA
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
		'geodata_type_title'
    );
   
    public $usedHelpers = array(
    );

    public $controllerPublicName = 'Linnaeus 2 Import';

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

    public function linnaeus2Action()
    {

		if ($this->rHasVal('process','1')) $this->redirect('l2_project.php');

        $this->setPageName(_('Choose file'));
		
		$this->setSuppressProjectInBreadcrumbs();

		if (isset($this->requestDataFiles[0]) && !$this->rHasVal('clear','file')) {

			$d = @file_get_contents($this->requestDataFiles[0]['tmp_name']) or $this->addError('Failed to load file.');
			
			if ($d) {

				$_SESSION['system']['import']['file'] = $this->requestDataFiles[0]['name'];
				$_SESSION['system']['import']['raw'] = $d;

			} else {

				unset($_SESSION['system']['import']['file']);
				unset($_SESSION['system']['import']['raw']);

			}

		}

		if ($this->rHasVal('imagePath') || $this->rHasVal('noImages')) {

			if ($this->rHasVal('noImages')) {

				$_SESSION['system']['import']['imagePath'] = false;

			} else
			if (file_exists($this->requestData['imagePath'])) {

				$_SESSION['system']['import']['imagePath'] = $this->requestData['imagePath'];

			} else {

				$this->addError('Image path "'.$this->requestData['imagePath'].'" does not exist or unreachable.');
				unset($_SESSION['system']['import']['imagePath']);

			}

		}

		if ($this->rHasVal('thumbsPath') || $this->rHasVal('noThumbs')) {

			if ($this->rHasVal('noThumbs')) {

				$_SESSION['system']['import']['thumbsPath'] = false;

			} else
			if (file_exists($this->requestData['thumbsPath'])) {

				$_SESSION['system']['import']['thumbsPath'] = $this->requestData['thumbsPath'];

			} else {

				$this->addError('Thumbs path "'.$this->requestData['thumbsPath'].'" does not exist or unreachable.');
				unset($_SESSION['system']['import']['thumbsPath']);

			}

		}		

		if ($this->rHasVal('clear','file')) {

			unset($_SESSION['system']['import']['file']);
			unset($_SESSION['system']['import']['raw']);

		}

		if ($this->rHasVal('clear','imagePath')) unset($_SESSION['system']['import']['imagePath']);
		if ($this->rHasVal('clear','thumbsPath')) unset($_SESSION['system']['import']['thumbsPath']);


		if (isset($_SESSION['system']['import'])) $this->smarty->assign('s',$_SESSION['system']['import']);
    
        $this->printPage();

    }

	public function l2ProjectAction()
	{

		if (!isset($_SESSION['system']['import']['raw'])) $this->redirect('linnaeus2.php');

        $this->setPageName(_('Creating project'));
		
		libxml_use_internal_errors(true);
		$d = simplexml_load_string($_SESSION['system']['import']['raw']);
		
		if ($d===false) {

			$this->addError('Failed to parse XML-file. The followinf error(s) occurred:');
			foreach (libxml_get_errors() as $error) $this->addError((string)$error->message);
			$this->addError('Import aborted.');

		} else {
		
			/*
			
			// user is no longer given a choice a new project is always created

			if ($this->rHasVal('clear','project')) {
	
				$this->setNewProjectId(null);
				$this->setNewDefaultLanguageId(null);

			}

			if ($this->rHasVal('project','-1')) {
			
			*/

				$newId = $this->createProject(
					array(
						 'title' => (string)$d->project->title,
						 'version' => (string)$d->project->version,
						 'sys_description' => 'Created by import from a Linnaeus 2-export.'
					)
				);

				if (!$newId) {
	
					$this->addError('Could not create new project "'.(string)$d->project->title.'". Does a project with the same name already exist?');
	
				} else { 

					$project = $this->getProjects($this->getNewProjectId());

					$this->addMessage('Created new project "'.(string)$d->project->title.'"');

					$this->setNewProjectId($newId);

					$this->addCurrentUserToProject();

					$this->makeMediaTargetPaths();

					$this->smarty->assign('newProjectId',$newId);
	
					// add language
					$l = $this->addProjectLanguage($d);

					if (!$l) {

						$this->addError('Unable to use project language "'.(string)$d->project->language.'"');

					} else {

						$this->setNewDefaultLanguageId($l);
						$this->addMessage('Set language "'.(string)$d->project->language.'"');

					}
	
				}

			/*
			
			} else 
			if ($this->rHasVal('project')) {

				$this->setNewProjectId($this->requestData['project']);
				$l = $this->models->LanguageProject->_get(
					array(
						'id' => array(
							'project_id'=>$this->requestData['project'],
							'def_language' => 1
						)
					)
				);
				$this->setNewDefaultLanguageId($l[0]['language_id']);
				$this->addMessage('Using existing project');

			}
			
			$p = $this->getNewProjectId();

			$project = $this->getProjects($p);
			
			$this->reInitUserRolesAndRights();

			if (!isset($p)) {

				$this->smarty->assign('projects',$this->getProjects());

			} else {

				$this->smarty->assign('project',$project);

			}
			*/

		}

        $this->printPage();
	
	}

	public function l2AnalyzeAction()
	{

		if (!isset($_SESSION['system']['import']['raw'])) $this->redirect('linnaeus2.php');

		$p = $this->getNewProjectId();
		$project = $this->getProjects($p);

        $this->setPageName(_('Data overview for "'.$project['title'].'"'));

		$d = simplexml_load_string($_SESSION['system']['import']['raw']);

		$ranks = $this->resolveProjectRanks($d);
		$species = $this->resolveSpecies($d,$ranks);
		$treetops = $this->checkTreeTops($species);

		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {

			$ranks = $_SESSION['system']['import']['loaded']['ranks'] = $this->addProjectRanks($ranks);
			$species = $_SESSION['system']['import']['loaded']['species'] = $this->addSpecies($species,$ranks);
			if (isset($this->requestData['treetops']))
				$species = $_SESSION['system']['import']['loaded']['species'] = $this->fixTreetops($species,$this->requestData['treetops']);
			
			$this->addMessage('Saved '.count((array)$ranks).' ranks');
			$this->addMessage('Saved '.count((array)$species).' species');

			$this->addModuleToProject(4);
			$this->addModuleToProject(5);
			
			$this->smarty->assign('processed',true);

		}

		$this->smarty->assign('project',$project);
		$this->smarty->assign('ranks',$ranks);
		$this->smarty->assign('species',$species);
		$this->smarty->assign('treetops',$treetops);

        $this->printPage();
	
	}

	public function l2SecondaryAction()
	{

		if (!isset($_SESSION['system']['import']['raw'])) $this->redirect('linnaeus2.php');
		if (!isset($_SESSION['system']['import']['loaded']['species'])) $this->redirect('linnaeus2.php');

		$p = $this->getNewProjectId();
		$project = $this->getProjects($p);

        $this->setPageName(_('Additional data for "'.$project['title'].'"'));

		$d = simplexml_load_string($_SESSION['system']['import']['raw']);
		$species = $_SESSION['system']['import']['loaded']['species'];

		if (!isset($_SESSION['system']['import']['content']))
			$_SESSION['system']['import']['content'] = $content = $this->getProjectContent($d);
		else
			$content = $_SESSION['system']['import']['content'];

		if (!isset($_SESSION['system']['import']['literature']))
			$_SESSION['system']['import']['literature'] = $literature = $this->resolveLiterature($d,$species);
		else
			$literature = $_SESSION['system']['import']['literature'];

		if (!isset($_SESSION['system']['import']['glossary']))
			$_SESSION['system']['import']['glossary'] = $glossary = $this->resolveGlossary($d);
		else
			$glossary = $_SESSION['system']['import']['glossary'];

		if (!isset($_SESSION['system']['import']['additionalContent']))
			$_SESSION['system']['import']['additionalContent'] = $additionalContent = $this->checkAdditionalContent($d);
		else
			$additionalContent = $_SESSION['system']['import']['additionalContent'];

		if (!isset($_SESSION['system']['import']['mapItems']))
			$_SESSION['system']['import']['mapItems'] = $mapItems = $this->getMapItems($d,$species);
		else
			$mapItems = $_SESSION['system']['import']['mapItems'];
	
		if ($this->rHasVal('process','1') && !$this->isFormResubmit()) {
		
			$_SESSION['system']['import']['paths'] = $this->makePaths($this->getNewProjectId());
		
			ini_set('max_execution_time',600);

			// DO NOT CHANGE ORDER; GLOSSARY, LITERATURE & MEDIA (SPECIES, KEY & MTRIX) MUST COME BEFORE CONTENT, FOR THEY MAY BE LINKED TO

			if ($this->rHasVal('literature','on')) {

				$res = $this->addLiterature($literature);

				$this->addMessage('Added '.$res['lit'].' literary reference(s) (failed '.$res['litFail'].').');
		
				$this->addMessage('Added '.$res['ref'].' literary-taxon link(s) (failed '.$res['refFail'].').');

				$this->addModuleToProject(3);

			}

			if ($this->rHasVal('glossary','on')) {

				$res = $this->addGlossary($glossary);

				$this->addMessage('Added '.$res['gloss'].' glossary item(s) (failed '.$res['fail'].').');

				$this->addMessage('Added '.count((array)$res['saved']).' glossary image(s) (failed '.count((array)$res['failed']).').');

				$this->addModuleToProject(2);

			}

			if ($this->rHasVal('taxon_media','on')) {

				$res = $this->addSpeciesMedia($d,$species);

				$this->addMessage('Added '.count((array)$res['saved']).' taxon media.');

				if (isset($res['failed'])) {

					foreach ((array)$res['failed'] as $val) $this->addError('Failed media:<br />'.$val['cause']);

				}

			}

			if ($this->rHasVal('key_dich','on')) {

				$this->makeKey($d,$species);

				$this->addMessage('Created dichotomous key.');

				$this->addModuleToProject(6);

			}

			if ($this->rHasVal('key_matrix','on')) {

				$m = $this->resolveMatrices($d);

				$m = $this->saveMatrices($m);

				if (isset($m['failed'])) {

					foreach ((array)$m['failed'] as $val) $this->addError('Error in matrix:<br />'.$val['cause']);

				}

				$failed = $this->connectMatrices($d,$m['matrices'],$species);

				if (isset($failed)) {

					foreach ((array)$failed as $val) $this->addError('Error in matrix:<br />'.$val['cause']);

				}

				$this->addMessage('Created matrix key(s).');

				$this->addModuleToProject(7);

			}

			
			$_SESSION['system']['import']['lookupArrays'] = $this->createLookupArrays();


			if ($this->rHasVal('taxon_overview','on')) {

				$overviewCatId = $this->createStandardCat();

				$res = $this->addSpeciesContent($d,$species,$overviewCatId);

				$this->addMessage('Added '.$res['loaded'].' general species description(s).');

				if (isset($res['failed'])) {

					foreach ((array)$res['failed'] as $val) $this->addError('Failed species description:<br />'.$val['cause']);

				}

			}

			if ($this->rHasVal('taxon_common','on')) {

				$res = $this->addSpeciesCommonNames($d,$species);

				$this->addMessage('Added '.$res['loaded'].' common name(s).');

				if (isset($res['failed'])) {

					foreach ((array)$res['failed'] as $val) $this->addError('Failed common name:<br />'.$val['cause']);

				}

			}

			if ($this->rHasVal('taxon_synonym','on')) {

				$count = $this->addSpeciesSynonyms($d,$species);

				$this->addMessage('Added '.$count.' synonym(s).');

			}



			if ($this->rHasVal('content_introduction','on')) {

				if ($this->addProjectContent($d,'introduction')) {

					$this->addMessage('Added introduction.');

				} else {

					$this->addError('Failed loading introduction');

				}

			}

			if ($this->rHasVal('content_contributors','on')) {

				if ($this->addProjectContent($d,'contributors')) {

					$this->addMessage('Added contributors text.');

				} else {

					$this->addError('Failed loading contributors text');

				}

			}

			if ($this->rHasVal('content_introduction','on') || $this->rHasVal('content_contributors','on')) {

				$this->addModuleToProject(1);

			}

			if ($this->rHasVal('additional_content','on')) {

				$res = $this->addAdditionalContent($additionalContent);

			}

			if ($this->rHasVal('map_items','on')) {

				//$nodes = $this->translateMapItems($mapItems);

				$types = $this->saveMapItemTypes($mapItems['types']);

				$m = $this->saveMapItems($mapItems,$types);

				if (isset($m['failed'])) {

					foreach ((array)$m['failed'] as $val) $this->addError('Failed geo data: '.$val);

				}

				$this->addMessage('Loaded geo data (saved '.$m['saved'].', failed '.count((array)$m['failed']).').');

				$this->addModuleToProject(8);

			}

			$this->addUserToProject($this->getCurrentUserId(),$this->getNewProjectId(),ID_ROLE_SYS_ADMIN);
		
			$this->addMessage('Added current user to project as system administrator.');

			$this->smarty->assign('processed',true);
//			q($_SESSION['system']['import']['glossary']);

			unset($_SESSION['system']['import']);

		}

		$this->smarty->assign('content',$content);
		$this->smarty->assign('literature',$literature);
		$this->smarty->assign('glossary',$glossary);
		$this->smarty->assign('additionalContent',$additionalContent);
		$this->smarty->assign('mapItems',$mapItems);

        $this->printPage();

	}

	public function goNewProject()
	{

		$this->reInitUserRolesAndRights();
		$this->setCurrentProjectId($this->getNewProjectId());
		$this->setCurrentProjectData();
		$this->getCurrentUserCurrentRole(true);

		unset($_SESSION['system']['import']);
		unset($_SESSION['project']['ranks']);

		$this->redirect($this->getLoggedInMainIndex());

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

	private function getProjects($id=null)
	{

		$d = isset($id) ? array('id' => $id) : '*';

		$d = $this->models->Project->_get(array('id' => $d));

		return isset($id) ? $d[0] : $d;
	
	}

	private function setNewProjectId($id)
	{

		if ($id==null)
			unset($_SESSION['system']['import']['newProjectId']);
		else
			$_SESSION['system']['import']['newProjectId'] = $id;
	
	}

	private function getNewProjectId()
	{
	
		return (isset($_SESSION['system']['import']['newProjectId'])) ?
			$_SESSION['system']['import']['newProjectId']:
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

	private function addProjectLanguage($d)
	{
	
		$l = $this->models->Language->_get(
			array(
				'id' => array(
					'language' => $d->project->language
				),
				'columns' => 'id'
			)
		);

		if (!$l) return false;

		$p = $this->models->LanguageProject->save(
			array(
				'id' => null,
				'language_id' => $l[0]['id'],				
				'project_id' => $this->getNewProjectId(),				
				'def_language' => 1,		
				'active' => 'y',				
				'tranlation_status' => 1
			)
		);
	
		return ($p) ? $l[0]['id'] : false;
	
	}

	private function setNewDefaultLanguageId($id)
	{

		if ($id==null)
			unset($_SESSION['system']['import']['newLanguageId']);
		else
			$_SESSION['system']['import']['newLanguageId'] = $id;
	
	}

	private function getNewDefaultLanguageId()
	{

		return (isset($_SESSION['system']['import']['newLanguageId'])) ?
			$_SESSION['system']['import']['newLanguageId']:
			null;
	
	}

	// project ranks
	private function resolveProjectRanks($d)
	{

		foreach($d->tree->treetaxon as $key => $val) {
		
			$rank = (string)$val->taxon;
			$parentRank = (string)$val->parenttaxon;

			if (!isset($res[$rank])) {
	
				$r = $this->models->Rank->_get(
					array(
						'id' => array('default_label' => $rank),
						'columns' => 'id'
					)
				);
	
				if (isset($r[0]['id'])) {

					$res[$rank]['rank_id'] = $r[0]['id'];
					
					if ($parentRank!='none') {

						$r = $this->models->Rank->_get(
							array(
								'id' => array('default_label' => $parentRank),
								'columns' => 'id'
							)
						);
						
						$res[$rank]['parent_id'] = isset($r[0]['id']) ? $r[0]['id'] : false;
						$res[$rank]['parent_name'] = $parentRank;

					} else {

						$res[$rank]['parent_id'] = null;

					}


				} else {

					$res[$rank]['rank_id'] = false;

				}
				
			}

		}

		return isset($res) ? $res : null;

	}

	private function addProjectRank($label,$rank)
	{

		$this->models->ProjectRank->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),	
				'rank_id' => $rank['rank_id'],	
				'parent_id' => isset($rank['parent_id']) ? $rank['parent_id'] : null,
				'lower_taxon' => '1'
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

	private function addProjectRanks($ranks)
	{

		$d = null;

		foreach((array)$ranks as $key => $val) {

			if (empty($val['rank_id'])) continue; // unresolvable rank
			if ($val['parent_id']===false) continue; // unresolvable parent rank
			if (!isset($val['parent_id']) && $key > 0) continue; // parentless ranks (other then topmost)

			$d[$key] = $this->addProjectRank($key,$val);

		}

		return $d;

	}

	// species (& treetops)
	private function resolveSpecies($d,$ranks)
	{

		$failed = null;

		foreach($d->tree->treetaxon as $key => $val) {

			$res[(string)$val->name] = array(
				'taxon' => (string)$val->name,
				'rank_id' => isset($ranks[(string)$val->taxon]) ? $ranks[(string)$val->taxon]['rank_id'] : null,
				'rank_name' => (string)$val->taxon ? (string)$val->taxon : null,
				'parent' => (string)$val->parentname,
				'source' => 'tree->treetaxon'
			);
			
		}

		foreach($d->records->taxondata as $val) {

			$res[(string)$val->name] = array(
				'taxon' => (string)$val->name,
				'rank_id' => isset($ranks[(string)$val->taxon]) ? $ranks[(string)$val->taxon]['rank_id'] : null,
				'rank_name' => (string)$val->taxon ? (string)$val->taxon : null,
				'parent' => (string)$val->parentname,
				'source' => 'records->taxondata'
			);

		}

		return isset($res) ? $res : null;

	}

	private function addSpecies($species,$ranks)
	{

		foreach((array)$species as $key => $val) {
		
			if (!isset($ranks[$val['rank_name']]['id'])) continue; // not loading rankless taxa

			$this->models->Taxon->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'taxon' => $val['taxon'],
					'parent_id' => 'null',
					'rank_id' => $ranks[$val['rank_name']]['id'],
					'taxon_order' => 0,
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

	private function getProjectContent($d)
	{
	
		return array(
			'Introduction' => isset($d->project->projectintroduction) ? $d->project->projectintroduction : null,
			'Contributors' => isset($d->project->contributors) ? $d->project->contributors : mull
		);
	
	}


	private function addProjectContent($d,$type)
	{
	
		if ($d->project->projectintroduction && $type=='introduction')
			$c = $this->models->Content->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Introduction',	
					'content' => $this->replaceOldTags((string)$d->project->projectintroduction)
				)
			);

		if ($d->project->contributors && $type=='contributors')
			$c = $this->models->Content->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Contributors',	
					'content' => $this->replaceOldTags((string)$d->project->contributors)
				)
			);
	
		return $c;
	
	}


	private function createStandardCat()
	{

		$pt = $this->models->PageTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getNewProjectId(),
					'page' => 'Overview'
				),
				'columns' => 'id'
			)
		);
		
		if (isset($pt['id'])) return $pt['id'];

		$pt = $this->models->PageTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'page' => 'Overview',
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
				'title' => 'Overview'
			)
		);
		
		return $id;
	
	}


	private function resolveLanguage($l)
	{

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

	private function addSpeciesContent($d,$species,$overviewCatId)
	{

		$failed = null;
		$loaded = 0;

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];

				$this->models->ContentTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'language_id' => $this->getNewDefaultLanguageId(),
						'page_id' => $overviewCatId,
//						'content' => $this->replaceOldTags((string)$val->description),
						'content' => $this->replaceInternalLinks($this->replaceOldTags((string)$val->description)),
						'publish' => 1
					)
				);
				
				$loaded++;

			} else {
			
				$failed[] = array(
					'data' => $val,
					'cause' => 'unable to resolve name "'.(string)$val->name.'" to taxon id'
				);
			
			}

		}
		
		return array('loaded' => $loaded, 'failed' => $failed);

	}

	private function addSpeciesCommonNames($d,$species)
	{

		$failed = null;
		$loaded = 0;

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];

				foreach($val->vernaculars as $vKey => $vVal) {

					$languagId = $this->resolveLanguage((string)$vVal->vernacular->language);

					if ($languagId) {

						$this->models->Commonname->save(
							array(
								'id' => null,
								'project_id' => $this->getNewProjectId(),
								'taxon_id' => $taxonId,
								'language_id' => $languagId,
								'commonname' => (string)$vVal->vernacular->name
							)
						);
						
						$loaded++;

					} else {

						$failed[] = array(
							'data' => $val,
							'cause' => 'unable to resolve language "'.(string)$vVal->vernacular->language.'" to id'
						);
		
					}

				}

			}

		}

		return array('loaded' => $loaded, 'failed' => $failed);

	}

	private function addSpeciesSynonyms($d,$species)
	{

		$loaded = 0;

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];
				
				$i = 0;

				foreach($val->synonyms as $vKey => $vVal) {

					$this->models->Synonym->save(
						array(
							'id' => null,
							'project_id' => $this->getNewProjectId(),
							'taxon_id' => $taxonId,
							'synonym' => (string)$vVal->synonym->name,
							'show_order' => $i++
						)
					);
					
					$loaded++;

				}

			}

		}
		
		return $loaded;

	}

	private function doAddSpeciesMedia($taxonId,$fileName,$fullName,$mimes)
	{

		if ($_SESSION['system']['import']['imagePath']==false)
			return array(
				'saved' => false,
				'data' => $fileName,
				'cause' => 'user specified no media import for project'
			);


		if (file_exists($_SESSION['system']['import']['imagePath'].$fileName)) {
		
			$thisMIME = mime_content_type($_SESSION['system']['import']['imagePath'].$fileName);
			
			if (isset($mimes[$thisMIME])) {
			
				if ($_SESSION['system']['import']['thumbsPath']==false)
					$thumbName = null;
				else
					$thumbName = file_exists($_SESSION['system']['import']['thumbsPath'].$fileName) ? $fileName : null;

				$this->models->MediaTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'file_name' => $fileName,
						'thumb_name' => $thumbName,
						'original_name' => $fullName,
						'mime_type' => $thisMIME,
						'file_size' => filesize($_SESSION['system']['import']['imagePath'].$fileName)
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
					'full_path' => $_SESSION['system']['import']['imagePath'].$fileName,
					'thumb' => isset($thumbName) ? $thumbName : null,
					'thumb_path' => isset($thumbName) ? $_SESSION['system']['import']['thumbsPath'].$thumbName : null
				);

			} else {

				return array(
					'saved' => false,
					'data' => $fileName,
					'cause' => 'mime-type "'.$thisMIME.'" not allowed'
				);

			}
		
		} else {
		
			return array(
				'saved' => false,
				'data' => $fileName,
				'cause' => 'file "'.$fileName.'" does not exist'
			);
		
		}	

	}

	private function cRename($from,$to)
	{
	
		//return rename($from,$to); // generates odd errors on some linux filesystems
	
		if(copy($from,$to)) {

			unlink($from);

			return true;

		} else {

			return false;

		}

	}

	private function makeMediaTargetPaths()
	{
	
		$paths = $this->makePaths($this->getNewProjectId());

		if (!file_exists($paths['project_media'])) mkdir($paths['project_media']);
		if (!file_exists($paths['project_thumbs'])) mkdir($paths['project_thumbs']);

	}

	private function addSpeciesMedia($d,$species)
	{

		$this->loadControllerConfig('Species');
		
		$paths = isset($_SESSION['system']['import']['paths']) ? $_SESSION['system']['import']['paths'] : $this->makePaths($this->getNewProjectId());

		foreach((array)$this->controllerSettings['media']['allowedFormats'] as $val) {

			$mimes[$val['mime']] = $val;

		}

		$failed = null;
		$saved = null;
		$prev = null;

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];
				
				$fileName = (string)$val->multimedia->overview;

				$r = $this->doAddSpeciesMedia(
					$taxonId,
					$fileName,
					$fileName,
					$mimes
				);

				if ($r['saved']==true) {

					if (isset($r['full_path'])) $this->cRename($r['full_path'],$paths['project_media'].$r['filename']);
					if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$paths['project_thumbs'].$r['filename']);

					$saved[] = $r;
					$prev[$fileName] = true;
				
				} else {

					$failed[] = $r;

				}

				foreach($val->multimedia->multimediafile as $vKey => $vVal) {
				
					$fileName = (string)$vVal->filename;
					
					if (isset($prev[$fileName])) continue;


					$r = $this->doAddSpeciesMedia(
						$taxonId,
						$fileName,
						(isset($val->fullname) ? ((string)$val->fullname) : $fileName),
						$mimes
					);

					if ($r['saved']==true) {
	
						if (isset($r['full_path'])) $this->cRename($r['full_path'],$paths['project_media'].$r['filename']);
						if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$paths['project_thumbs'].$r['filename']);
						
						$saved[] = $r;
						$prev[$fileName] = true;
						
					} else {

						$failed[] = $r;
	
					}

				}

			}

			unset($prev);

		}

		$this->loadControllerConfig();

		return array(
			'saved' => $saved,
			'failed' => $failed
		);

	}

	private function replaceOldTags($s,$removeAll=false)
	{
	
		$r = array('<b>','</b>','<i>','</i>','<br />', null,null);
	
		return str_replace(array('[b]','[/b]','[i]','[/i]','[br]','[p]','[/p]'),($removeAll ? null : $r),$s);
	
	}

	private function resolveAuthors($s)
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

	private function resolveLiterature($d,$species)
	{

		foreach($d->proj_literature->proj_reference as $key => $val) {

			$l = (string)$val->literature_title;
			$a = $this->resolveAuthors($l);
			$a['text'] = $this->replaceInternalLinks($this->replaceOldTags((string)$val->fullreference));
			$okSp = $unSp = null;

			if ($val->keywords->keyword) {

				foreach($val->keywords->keyword as $kKey => $kVal) {
	
					$t = $this->replaceOldTags($this->removeInternalLinks((string)$kVal->name),true);
	
					if (isset($species[$t])) {

						$okSp[] = $species[$t]['id'];
	
					} else {

						$unSp[] = $t;
	
					}
	
				}
				
			}

			$a['references'] = array('species' => $okSp,'unknown_species' => $unSp);

			$res[] = $a;

		}

		return isset($res) ? $res : null;

	}

	private function addLiterature($d)
	{

		$lit = $ref = 0;
		$litFail = $refFail = 0;

		foreach($d as $key => $val) {

			$l = $this->models->Literature->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),				
					'author_first' => isset($val['author_1']) ? $val['author_1'] : null,
					'author_second' => (isset($val['author_2']) && $val['multiple_authors']==false) ? $val['author_2'] : null,
					'multiple_authors' => $val['multiple_authors']==true ? 1 : 0,
					'year' => (isset($val['year'])  && $val['valid_year'] == true) ? $val['year'].'-00-00' : '0000-00-00',
					'suffix' => isset($val['suffix']) ? $val['suffix'] : null,
					'text' => isset($val['text']) ? $val['text'] : null,
				)
			);

			if ($l===false) {

				$litFail++;
				continue;

			} else {
		
				$lit++;

			}

			$_SESSION['system']['import']['literature'][$key]['id'] = $id = $this->models->Literature->getNewId();

			foreach((array)$val['references']['species'] as $kV) {
			
				if (empty($kV)) {

					$refFail++;

					continue;

				}

				$lt = $this->models->LiteratureTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),				
						'taxon_id' => $kV,
						'literature_id' => $id,
					)
				);

				if ($lt===false)
					$refFail++;
				else
					$ref++;

			}

		}

		return array(
			'lit' => $lit,
			'ref' => $ref,
			'litFail' => $litFail,
			'refFail' => $refFail,
		);

	}

	private function resolveGlossary($d)
	{
	
		if (!$d->glossary->term) return null;
	
		foreach($d->glossary->term as $key => $val) {
		
			$t = (string)$val->glossary_title;
			$d = $this->replaceInternalLinks($this->replaceOldTags((string)$val->definition,true));
			
			unset($s);

			if ($val->glossary_synonyms->glossary_synonym) {

				foreach($val->glossary_synonyms->glossary_synonym as $sKey => $sVal) {

					$s[] = $this->replaceOldTags((string)$sVal->name,true);
					
				}

			}

			if ($val->gloss_multimedia->gloss_multimediafile) {

				foreach($val->gloss_multimedia->gloss_multimediafile as $mKey => $mVal) {

					$m[] = array(
						'filename' => (string)$mVal->filename,
						'fullname' => (string)$mVal->fullname,
						'caption' => (string)$mVal->caption,
						'type' => (string)$mVal->multimedia_type,
					);
					
				}

			}
			
			$res[] = array(
				'term' => $t,
				'definition' => $d,
				'synonyms' => isset($s) ? $s : null,
				'multimedia' => isset($m) ? $m : null
				);

		}

		return isset($res) ? $res : null;

	}

	private function doAddGlossaryMedia($id,$data,$mimes)
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

		if ($_SESSION['system']['import']['imagePath']==false)
			return array(
				'saved' => false,
				'data' => $data['fileName'],
				'cause' => 'user specified no media import for project'
			);

		$fileToImport = $_SESSION['system']['import']['imagePath'].$data['fileName'];

		if (file_exists($fileToImport)) {

			$thisMIME = mime_content_type($fileToImport);
			
			if (isset($mimes[$thisMIME])) {
			
				if ($_SESSION['system']['import']['thumbsPath']==false)
					$thumbName = null;
				else
					$thumbName = file_exists($_SESSION['system']['import']['thumbsPath'].$data['fileName']) ? $data['fileName'] : null;

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
					'filename' => $data['fileName'],
					'full_path' => $fileToImport,
					'thumb' => isset($thumbName) ? $thumbName : null,
					'thumb_path' => isset($thumbName) ? $_SESSION['system']['import']['thumbsPath'].$thumbName : null
				);

			} else {

				return array(
					'saved' => false,
					'data' => $data['fileName'],
					'cause' => 'mime-type "'.$thisMIME.'" not allowed'
				);

			}
		
		} else {
		
			return array(
				'saved' => false,
				'data' => $data['fileName'],
				'cause' => 'file "'.$data['fileName'].'" does not exist'
			);
		
		}

	}

	private function addGlossary($d)
	{

		$gloss = $fail = 0;

		foreach($d as $key => $val) {

			$g = $this->models->Glossary->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'term' => isset($val['term']) ? $val['term'] : null,
					'definition' => isset($val['definition']) ? $val['definition'] : null
				)
			);

			if ($g!==true) {

				$fail++;
				continue;

			} else {

				$gloss++;

			}
			
			$id = $this->models->Glossary->getNewId();

			$_SESSION['system']['import']['glossary'][$key]['id'] = $id;

			foreach((array)$val['synonyms'] as $sVal) {
			
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

			$this->loadControllerConfig('Glossary');
			
			foreach((array)$this->controllerSettings['media']['allowedFormats'] as $val) {
	
				$mimes[$val['mime']] = $val;
	
			}


			if (isset($val['multimedia'])) {

				foreach((array)$val['multimedia'] as $mVal) {
	
					$r = $this->doAddGlossaryMedia($id,$mVal,$mimes);
	
					if ($r['saved']==true) {
	
						if (isset($r['full_path'])) $this->cRename($r['full_path'],$paths['project_media'].$r['filename']);
						if (isset($r['thumb_path'])) $this->cRename($r['thumb_path'],$paths['project_thumbs'].$r['filename']);
	
						$saved[] = $r;
					
					} else {
	
						$failed[] = $r;
	
					}
	
				}

			}

		}

		return array(
			'gloss' => $gloss,
			'fail' => $fail,
			'saved' => isset($saved) ? $saved : null,
			'failed' => isset($failed) ? $failed : null
		);

	}

	private function createKeyStep($step,$stepIds,$stepAdd=0)
	{

		$k = $this->models->Keystep->save(
			array(
				'id' => null,
				'project_id' => $this->getNewProjectId(),
				'number' => ($step=='god' ? -1 : (intval((string)$step->pagenumber) + $stepAdd)),
				'is_start' => 0
			)
		);

		$stepId = $stepIds[($step=='god' ? -1 : (string)$step->pagenumber)] = $this->models->Keystep->getNewId();

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
							(string)$step->pagetitle:
							(string)$step->pagenumber
						)
					),
				'content' =>
					($step=='god' ?
						'Choose between picture key and text key' : 
						(isset($step->pagetitle) ?
							(string)$step->pagetitle:
							(string)$step->pagenumber
						)
					)
			)
		);

		return $stepIds;

	}

	private function createKeyStepChoices($step,$stepIds,$species)
	{

		$paths = isset($_SESSION['system']['import']['paths']) ? $_SESSION['system']['import']['paths'] : $this->makePaths($this->getNewProjectId());

		if ($step->text_choice) {
			$choices = $step->text_choice;
		} else {
			$choices = $step->pict_choice;
		}

		$i=0;

		foreach($choices as $key => $val) {

			$resStep = ((string)$val->destinationtype=='turn' ? 
							(isset($stepIds[(string)$val->destinationpagenumber]) ?
								$stepIds[(string)$val->destinationpagenumber] :
								null
							) :
							null
						);

			$resTaxon = ((string)$val->destinationtype=='taxon' ?  
							(isset($species[(string)$val->destinationtaxonname]['id']) ?
								$species[(string)$val->destinationtaxonname]['id']:
								null
							) : 
							null
						);

			$fileName = isset($val->picturefilename) ? (string)$val->picturefilename : null;

			if ($fileName && !file_exists($_SESSION['system']['import']['imagePath'].$fileName)) {

				$error[] = array(
					'cause' => 'Picture key image "'.$fileName.'" does not exist (choice created anyway)'
				);
				
				$fileName = null;

			} else
			if ($fileName) {

				$this->cRename($_SESSION['system']['import']['imagePath'].$fileName,$paths['project_media'].$fileName);

			}

			$this->models->ChoiceKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'keystep_id' => ($step=='god' ? $stepIds['godId'] : $stepIds[(string)$step->pagenumber]),
					'show_order' => (1 + $i++),
					'choice_img' => isset($fileName) ? $fileName : null,
					'res_keystep_id' => $resStep,
					'res_taxon_id' => $resTaxon,
				)
			);

			if (isset($val->captiontext)) {

				$txt = $this->replaceOldTags((string)$val->captiontext);
				$p = (string)$step->pagenumber.(string)$val->choiceletter.'.';
				if (substr($txt,0,strlen($p))==$p) $txt = trim(substr($txt,strlen($p)));
				if (strlen($txt)==0) $txt = $this->replaceOldTags((string)$val->captiontext);

			} else
			if (isset($val->picturefilename)) {

				$txt = (string)$val->picturefilename;
			}
	
			$this->models->ChoiceContentKeystep->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'choice_id' => $this->models->ChoiceKeystep->getNewId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'choice_txt' => $txt
				)
			);

		}

	}

	private function makeKey($d,$species)
	{

		$stepAdd = 0;

		if (count($d->text_key)==1) {
	
			$keyStepIds = null;

			// text key
			// create steps first (no choices yet)
			foreach($d->text_key->keypage as $key => $val) {

				$keyStepIds = $this->createKeyStep($val,$keyStepIds);
				if ($key==0) $firstTxtStepId = current($keyStepIds);	

			}

			// create choices
			foreach($d->text_key->keypage as $key => $val) {
	
				$this->createKeyStepChoices($val,$keyStepIds,$species);
	
			}

			$k = $this->models->Keystep->_get(
				array(
					'id' => array('project_id' => $this->getNewProjectId()),
					'columns' => 'max(number) as `last`'
				)
			);

			$stepAdd = $k[0]['last'];

		}

		if (count($d->pict_key)==1) {

			$pictStepIds = null;

			// pict key
			// create steps first (no choices yet)
			foreach($d->pict_key->keypage as $key => $val) {

				$pictStepIds = $this->createKeyStep($val,$pictStepIds,$stepAdd);
				if ($key==0) $firstPictStepId = current($pictStepIds);
		
			}

			// create choices
			foreach($d->pict_key->keypage as $key => $val) {
	
				$this->createKeyStepChoices($val,$pictStepIds,$species);
	
			}

		}

		if (count($d->text_key)==1 && count($d->pict_key)==1) {

			$keyStepIds = $this->createKeyStep('god',$keyStepIds);

			end ($keyStepIds);

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

	private function resolveMatrices($d)
	{

		$matrices = null;

		foreach($d->records->taxondata as $val) {

			$matrixname = (string)$val->identify->id_file->filename;

			if ($matrixname) {

				//?? (string)$val->identify->id_file->obj_link

				$matrices[$matrixname]['name'] = str_replace('.adm','',$matrixname);

				foreach($val->identify->id_file->characters->character_ as $char) {

					//character_type ?? welke mogelijkheden + resolvement: Text 

					$charname = (string)$char->character_name;
					$chartype = (string)$char->character_type;
					
					foreach($char->states->state as $stat) {

						$statename = (string)$stat->state_name;
						$statemin = (string)$stat->state_min;
						$statemax = (string)$stat->state_max;
						$statemean = (string)$stat->state_mean;
						$statesd = (string)$stat->state_sd;
						$statefile = (string)$stat->state_file;

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

						$matrices[$matrixname]['characteristics'][$charname]['charname'] = $charname;
						$matrices[$matrixname]['characteristics'][$charname]['chartype'] = $chartype;
						$matrices[$matrixname]['characteristics'][$charname]['states'][$adHocIndex] = array(
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

		return $matrices;

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

		$paths = isset($_SESSION['system']['import']['paths']) ? $_SESSION['system']['import']['paths'] : $this->makePaths($this->getNewProjectId());
		
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
		
					if ($fileName && !file_exists($_SESSION['system']['import']['imagePath'].$fileName)) {
		
						$error[] = array(
							'cause' => 'Matrix state image "'.$fileName.'" does not exist (state created anyway)'
						);

						$fileName = null;
		
					} else
					if ($fileName) {
		
						$this->cRename($_SESSION['system']['import']['imagePath'].$fileName,$paths['project_media'].$fileName);
		
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

	private function connectMatrices($d,$statelist,$species)
	{

		foreach($d->records->taxondata as $val) {

			$matrixname = (string)$val->identify->id_file->filename;

			if ($matrixname) {

				if (isset($species[(string)$val->name]['id'])) {

					$taxonid = $species[(string)$val->name]['id'];

					foreach($val->identify->id_file->characters->character_ as $char) {
	
						$charname = (string)$char->character_name;
						
						foreach($char->states->state as $stat) {
	
							$statename = (string)$stat->state_name;
							$statemin = (string)$stat->state_min;
							$statemax = (string)$stat->state_max;
							$statemean = (string)$stat->state_mean;
							$statesd = (string)$stat->state_sd;
							$statefile = (string)$stat->state_file;
	
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
	
							if (isset($statelist[$adHocIndex])) {

								$this->models->MatrixTaxon->setNoKeyViolationLogging(true);

								$this->models->MatrixTaxon->save(
									array(
										'id' => null,
										'project_id' => $this->getNewProjectId(),
										'matrix_id' => $statelist[$adHocIndex]['matrix_id'],
										'taxon_id' => $taxonid,
									)
								);

								$this->models->MatrixTaxonState->setNoKeyViolationLogging(true);

								$this->models->MatrixTaxonState->save(
									array(
										'id' => null,
										'project_id' => $this->getNewProjectId(),
										'matrix_id' => $statelist[$adHocIndex]['matrix_id'],
										'characteristic_id' => $statelist[$adHocIndex]['characteristic_id'],
										'state_id' => $statelist[$adHocIndex]['state_id'],
										'taxon_id' => $taxonid,
									)
								);
					
							}
	
						}
	
					}
	
				} else {
	
					$failed[] = array(
						'cause' => 'species "'.(string)$val->name.'" in identifyit does not exist and has been discarded',
						'data' => (string)$val->name
					);
	
				}

			} // not part of any matrix

		}

		return isset($failed) ? $failed : null;

	}

	private function checkAdditionalContent($d)
	{

		if (!$d->introduction) return null;
	
		foreach($d->introduction->topic as $key => $val) {
		
			$res[] = array(
				'title' => (string)$val->introduction_title,
				'content' => (string)$val->text,
				'image' => (string)$val->overview
			);

		}

		return isset($res) ? $res : null;

	}

	private function addAdditionalContent($content)
	{

		// assuming only one 'introduction' branch in the import (= a single free module)

		$freeModName = 'Introduction';

		$exists = true;
		$i = null;

		while ($exists == true) {
		
			$freeModName = $freeModName.(isset($i) ? ' ('.$i.')' : '');

			$fmp = $this->models->FreeModuleProject->_get(
				array(
					'id' => array(
						'module' => $freeModName,
						'project_id' => $this->getNewProjectId(),
					),
					'columns' => 'count(*) as total'
				)
			);
			
			if ($fmp[0]['total']!=0) {
			
				$i++;

			} else {
			
				$exists = false;

			}

		}
		
		$this->models->FreeModuleProject->save(
			array(
				'id' => null, 
				'module' => $freeModName, 
				'project_id' => $this->getNewProjectId(),
				'active' => 'y'
			)
		);
		
		$moduleId = $this->models->FreeModuleProject->getNewId();

		$this->models->FreeModuleProjectUser->save(
			array(
				'id' => null, 
				'project_id' => $this->getNewProjectId(), 
				'free_module_id' => $moduleId, 
				'user_id' => $this->getCurrentUserId()
			)
		);

		foreach($content as $key => $val) {

			$this->models->FreeModulePage->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'module_id' => $moduleId
				)
			);
			
			$pageId = $this->models->FreeModulePage->getNewId();
			
			$this->models->ContentFreeModule->save(
				array(
					'id' => null, 
					'project_id' => $this->getNewProjectId(), 
					'module_id' => $moduleId,
					'language_id' => $this->getNewDefaultLanguageId(), 
					'page_id' => $pageId,
					'topic' => $val['title'],
					'content' => $val['content']
				)
			);

/*
					$fmm = $this->models->FreeModuleMedia->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'page_id' => $this->requestData['id'],
							'file_name' => $file['name'],
							'original_name' => $file['original_name'],
							'mime_type' => $file['mime_type'],
							'file_size' => $file['size'],
							'thumb_name' => $thumb ? $thumb : null,
						)
					);



//			$res[] = array(
//				'title' => (string)$val->introduction_title,
//				'content' => (string)$val->text,
//				'image' => (string)$val->overview
//			);

*/
		}

	}

	private function ORIG_getMapItems($d,$species)
	{
	
		$maps = $occurrences = $types = null;
		$total = 0;
	
		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];
				
				if (!isset($val->distribution)) continue;
				
				foreach($val->distribution->map as $vKey => $vVal) {
				
					$maps[(string)$vVal->mapname] = array(
						'label' => (string)$vVal->mapname,
						'specs' => (string)$vVal->specs
					);
					
					foreach($vVal->squares->square as $sKey => $sVal) {

						$occurrences[$taxonId][] = array(
							'map' => (string)$vVal->mapname,
							'square' => (string)$sVal->number,
							'legend' => (string)$sVal->legend
						);
						
						$types[(string)$sVal->legend] = (string)$sVal->legend;
						
						$total++;

					}

				}

			}

		}

		return array(
			'maps' => $maps,
			'occurrences' => $occurrences,
			'types' => $types,
			'total' => $total
		);
			
	}

	private function getMapItems($d,$species)
	{
	
		$maps = $occurrences = $types = null;
		$total = 0;
	
		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				if (!isset($val->distribution)) continue;
				
				$taxonId = $species[(string)$val->name]['id'];

				foreach($val->distribution->map as $vKey => $vVal) {
				
					if (!isset($maps[(string)$vVal->mapname])) {

						$maps[(string)$vVal->mapname] = array(
							'label' => (string)$vVal->mapname,
							'specs' => (string)$vVal->specs
						);

						$d = explode(',',(string)$vVal->specs);
						$maps[(string)$vVal->mapname]['coordinates'] = array(
							'topLeft' => array('lat' => (int)$d[0],'long' => (-1 * $d[1])),
							'bottomRight' => array('lat' => (int)$d[2],'long' => (-1 * $d[3]))
						);		
						$maps[(string)$vVal->mapname]['square'] = array('width' => (int)$d[4],'height' => (int)$d[5]);		
						$maps[(string)$vVal->mapname]['widthInSquares'] = (int)($d[1] - $d[3]) / $d[4];
						$maps[(string)$vVal->mapname]['heightInSquares'] = (int)($d[0] - $d[2]) / $d[5];
					}
					
					foreach($vVal->squares->square as $sKey => $sVal) {

						$types[(string)$sVal->legend] = (string)$sVal->legend;

						$row = floor((string)$sVal->number / $maps[(string)$vVal->mapname]['widthInSquares']);
						$col = (string)$sVal->number % $maps[(string)$vVal->mapname]['widthInSquares'];

						/*
				
							<mapname>North Atlantic</mapname>
							<specs>90,100,0,-80,5,5</specs>
							
							90,100 = linksboven = 90�N 100�W = 90 -100
							0, -80 = rechtonder = 0�S 80�E = -0 80
							5,5 - cell size (ASSUMING WxH)
				
						*/

						$occurrences[$taxonId][] = array(
							'taxonId' => $taxonId,
							'map' => (string)$vVal->mapname,
							'square' => (string)$sVal->number,
							'legend' => (string)$sVal->legend,
							'nodes' =>
								array(
									array(
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['lat'] - ($row * $maps[(string)$vVal->mapname]['square']['height']),
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['long'] + (($col-1) * $maps[(string)$vVal->mapname]['square']['width'])
									),
									array(
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['lat'] - ($row * $maps[(string)$vVal->mapname]['square']['height']),
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['long'] + ($col * $maps[(string)$vVal->mapname]['square']['width'])
									),
									array(
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['lat'] - (($row+1) * $maps[(string)$vVal->mapname]['square']['height']),
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['long'] + ($col * $maps[(string)$vVal->mapname]['square']['width'])
									),				
									array(
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['lat'] - (($row+1) * $maps[(string)$vVal->mapname]['square']['height']),
										$maps[(string)$vVal->mapname]['coordinates']['topLeft']['long'] + (($col-1) * $maps[(string)$vVal->mapname]['square']['width'])
									)
								)
						);
						
						$total++;

					}

				}

			}

		}

		return array(
			'maps' => $maps,
			'occurrences' => $occurrences,
			'types' => $types,
			'total' => $total
		);
			
	}

	private function saveMapItemTypes($types)
	{
	
		$colours = array('00FFFF','000000','0000FF','8A2BE2','A52A2A','DEB887','5F9EA0','7FFF00','FF00FF','00FA9A');
		
		$i = 0;

		foreach((array)$types as $key => $val) {

			$this->models->GeodataType->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'colour' => $colours[$i++ % 10]
				)
			);

			$d[$key] = array('id' => $this->models->GeodataType->getNewId(),'title' => $key);

			$this->models->GeodataTypeTitle->save(
				array(
					'id' => null,
					'project_id' => $this->getNewProjectId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'type_id' => $d[$key]['id'],
					'title' => $val
				)
			);
	
		}

		return $d;

	}

	private function saveMapItems($p,$types)
	{

		if (!isset($p['occurrences'])) return;

		$this->loadControllerConfig('MapKey');
		
		$saved = 0;
		$failed = array();

		foreach((array)$p['occurrences'] as $key => $val) {

			foreach((array)$val as $occurrence) {

				$taxonId = $occurrence['taxonId'];
				
				if (!$taxonId) continue;
	
				$nodes = $occurrence['nodes'];
				
				if (count((array)$nodes)<=2) continue;
	
				$type_id = isset($types[$occurrence['legend']]['id']) ? $types[$occurrence['legend']]['id'] : null;
				
				if (!$type_id) continue;
				
				// remove the last node if it is identical to the first, just in case
				if ($nodes[0]==$nodes[count((array)$nodes)-1]) array_pop($nodes);
	
				// create a string for mysql (which does require the first and last to be the same)
				$geoStr = array();
				foreach((array)$nodes as $sVal) $geoStr[] = $sVal[0].' '.$sVal[1];
				$geoStr = implode(',',$geoStr).','.$geoStr[0];

				$this->models->OccurrenceTaxon->setNoKeyViolationLogging(true);
	
				$d = $this->models->OccurrenceTaxon->save(
					array(
						'id' => null,
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'type_id' => $type_id,
						'type' => 'polygon',
						'boundary' => "#GeomFromText('POLYGON((".$geoStr."))',".$this->controllerSettings['SRID'].")",
						'boundary_nodes' => json_encode($nodes),
						'nodes_hash' => md5(json_encode($nodes))
					)
				);
	
				if ($d===true) $saved++; else $failed[]=$d;
				
			}

		}

		return array('failed' => $failed, 'saved' => $saved);

	}


	private function resolveInternalLinks($s)
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
	
			/*
			array_push($i,
				array(
					'label' => _($val['label'].' topic'),
					'controller' => 'module',
					'url' => 'topic.php?modId='.$val['id'],
					'params' => json_encode(
						array(
							array(
								'label' => _('Topic:'),
								'param' => 'id',
								'values' => $this->intLinkGetFreeModuleTopics($val['id'])
							)
						)
					)
				)
			);
			*/
	
//		$d = rtrim($s[count((array)$s)-1],'[/t]');
		$d = preg_replace('/\[\/t\]$/','',$s[count((array)$s)-1]);
	
		$d = preg_split('/(\[\/m\]\[[r]\])|(\[\/r\]\[[t]\])/iU',$d);
	
		if (isset($d[0]) && isset($controllers[$d[0]])) {

			if ($controllers[$d[0]]=='glossary' && $_SESSION['system']['import']['lookupArrays']['g'][$d[1]])
				$id = $_SESSION['system']['import']['lookupArrays']['g'][$d[1]];

			if ($controllers[$d[0]]=='literature' && $_SESSION['system']['import']['lookupArrays']['l'][$d[1]])
				$id = $_SESSION['system']['import']['lookupArrays']['l'][$d[1]];

			if ($controllers[$d[0]]=='species' && $_SESSION['system']['import']['lookupArrays']['s'][$d[1]])
				$id = $_SESSION['system']['import']['lookupArrays']['s'][$d[1]];

			if ($controllers[$d[0]]=='highertaxa' && $_SESSION['system']['import']['lookupArrays']['s'][$d[1]])
				$id = $_SESSION['system']['import']['lookupArrays']['s'][$d[1]];
	
			if ($id) {
	
				$href =
					"goIntLink('".$controllers[$d[0]]['controller']."',".
					(isset($controllers[$d[0]]['url']) ? $controllers[$d[0]]['url'] : "'index.php'").
					(isset($controllers[$d[0]]['param']) ? ",['".$controllers[$d[0]]['param'].":".$id."']" : null).
					");";

				return '<span class="internal-link" style="color:#900" onclick="'.$href.'">'.$d[2].'</span>';

			}
			
		}
		
		return $d[2];
	
	}

	private function replaceInternalLinks($s)
	{

// TEMP WORKAROUND TO FIX "Undefined offset: 2" op 2919.
return $this->removeInternalLinks($s);

		$d = preg_replace_callback('/(\[l\](.*)\[\/l\])/sU',array($this,'resolveInternalLinks'),$s);

		return $d;

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

	private function createLookupArrays()
	{

		$s = $g = $l = null;

		foreach((array)$_SESSION['system']['import']['loaded']['species'] as $val) {
			if (isset($val['taxon']) && isset($val['id'])) $s[$val['taxon']] = $val['id'];
		}

		foreach((array)$_SESSION['system']['import']['glossary'] as $val) {
			if (isset($val['term']) && isset($val['id'])) $g[$val['term']] = $val['id'];
		}

		foreach((array)$_SESSION['system']['import']['literature'] as $val) {
			if (isset($val['original']) && isset($val['id'])) $l[$val['original']] = $val['id'];
		}
		
		return array(
			'species' => $s,
			'glossary' => $g,
			'literature' => $l,
		);

	}

	
}