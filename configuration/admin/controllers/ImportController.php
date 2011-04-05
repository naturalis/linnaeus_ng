<?php

include_once ('Controller.php');

class ImportController extends Controller
{

    public $pathMedia = 'C:\Users\maarten\Desktop\Tanbif_linnaeus\images\tanbif_linnaeus\pictures\\';
    public $pathThumbs = 'C:\Users\maarten\Desktop\Tanbif_linnaeus\images\tanbif_linnaeus\thumbs\\';

    public $usedModels = array(
    );
   
    public $usedHelpers = array(
    );

    public $controllerPublicName = 'Content';

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
    
//        $this->checkAuthorisation();

        $this->printPage();
    
    }


	private function getNewProjectId()
	{
	
	}

	private function getNewDefaultLanguageId()
	{
	
	}

	private function createProject($d)
	{
	
		$p = $this->models->Project->save(
			array(
				'id' => 'null',
				'sys_name' => $d->project->title.' v'.$d->project->version,
				'sys_description' => 'Created from Linnaeus 2 export.',
				'title' => $d->project->title,				
			)
		);
	
		return ($p) ? $this->models->Project->getNewId() : false;
	
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
				'id' => 'null',
				'language_id' => $l[0]['id'],				
				'project_id' => $this->getNewProjectId(),				
				'def_language' => 1,		
				'active' => 'y',				
				'tranlation_status' => 1
			)
		);
	
		return ($p) ? $this->models->Project->getNewId() : false;
	
	}

	private function addProjectContent($d)
	{
	
		if ($d->project->projectintroduction)
			$ci = $this->models->LanguageProject->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Introduction',	
					'content' => $d->project->projectintroduction	
				)
			);

		if ($d->project->contributors)
			$cc = $this->models->LanguageProject->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),	
					'language_id' => $this->getNewDefaultLanguageId(),	
					'subject' => 'Contributors',	
					'content' => $d->project->contributors	
				)
			);
	
		return array('Introduction' => $ci,'Contributors' => $cc);
	
	}

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
						
						$res[$rank]['parent_id'] = $r[0]['id'];

					}


				} else {

					$res[$rank]['rank_id'] = false;

				}
				
			}

		}

		return isset($res) ? $res : null;

	}

	private function addProjectRanks($ranks)
	{

		foreach((array)$ranks as $key => $val) {

			$this->models->ProjectRank->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),	
					'rank_id' => $val['rank_id'],	
					'parent_id' => isset($val['parent_id']) ? $val['parent_id'] : null,
					'lower_taxon' => '1'
				)
			);
			
			$ranks[$key]['id'] = $this->models->ProjectRank->getNewId();

		}

		return $ranks;

	}

	private function resolveSpecies($d,$ranks)
	{

		foreach($d->tree->treetaxon as $key => $val) {

			$res[(string)$val->name] = array(
				'taxon' => (string)$val->name,
				'rank_id' => isset($ranks[(string)$val->taxon]) ? $ranks[(string)$val->taxon]['rank_id'] : null,
				'parent' => (string)$val->parentname
			);
			
		}
		
		return isset($res) ? $res : null;

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

	private function addSpecies($species)
	{

		foreach((array)$species as $key => $val) {
$species[$key]['id'] = rand(12,999);continue;
			$this->models->Taxon->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),	
					'taxon' => $val['taxon'],
					'parent_id' => 'null',
					'rank_id' => $val['rank_id'],
					'taxon_order' => 0,
					'is_hybrid' => 0,
					'list_level' => 0
				)
			);

			$species[$key]['id'] = $this->models->Taxon->getNewId();

		}
return $species;
		foreach((array)$species as $key => $val) {

			$t = $this->models->Taxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getNewProjectId(),	
						'taxon' => $val['parent']
					),
					'columns' => 'id'
				)
			);
			
			$this->models->Taxon->save(
				array(
					'id' => $val['id'],
					'project_id' => $this->getNewProjectId(),	
					'parent_id' =>  $t[0]['id']
				)
			);

			$species[$key]['parent_id'] = $t[0]['id'];

		}

		return $species;

	}

	private function createStandardCat()
	{
return 23;
		$pt = $this->model->PageTaxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getNewProjectId(),
					'page' => 'Overview'
				),
				'columns' => 'id'
			)
		);
		
		if (isset($pt['id'])) return $pt['id'];

		$pt = $this->model->PageTaxa->save(
			array(
				'id' => 'null',
				'project_id' => $this->getNewProjectId(),
				'page' => 'Overview',
				'show_order' => 0,
				'def_page' => 1
			)
		);
	
		$id = $this->model->PageTaxaTitle->getNewId();

		$this->model->PageTaxaTitle->save(
			array(
				'id' => 'null',
				'project_id' => $this->getNewProjectId(),
				'page_id' => $id,
				'language_id' => $this->getNewDefaultLanguageId(),
				'title' => 'Overview'
			)
		);
		
		return $this->model->PageTaxa->getNewId();
	
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

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];
/*				
				$this->models->ContentTaxon->save(
					array(
						'id' => 'null',
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'language_id' => $this->getNewDefaultLanguageId()
						'page_id' => $overviewCatId,
						'content' => $this->replaceOldTags((string)$val->description),
						'publish' => 1
					)
				);
*/

			} else {
			
				$failed[] = array(
					'data' => $val,
					'cause' => 'unable to resolve name "'.(string)$val->name.'" to taxon id'
				);
			
			}

		}
		
		return $failed;

	}

	private function addSpeciesCommonNames($d,$species)
	{

		$failed = null;

		foreach($d->records->taxondata as $key => $val) {

			if (isset($species[(string)$val->name]['id'])) {
			
				$taxonId = $species[(string)$val->name]['id'];

				foreach($val->vernaculars as $vKey => $vVal) {

					$languagId = $this->resolveLanguage((string)$vVal->vernacular->language);

					if ($languagId) {
/*
						$this->models->Commonnames->save(
							array(
								'id' => 'null',
								'project_id' => $this->getNewProjectId(),
								'taxon_id' => $taxonId,
								'language_id' => $languagId,
								'commonname' => (string)$vVal->vernacular->name
							)
						);
*/
					} else {

						$failed[] = array(
							'data' => $val,
							'cause' => 'unable to resolve language "'.(string)$vVal->vernacular->language.'" to id'
						);
		
					}

				}

			}

		}

		return $failed;

	}

	private function doAddSpeciesMedia($taxonId,$fileName,$fullName,$mimes)
	{

		if (file_exists($this->pathMedia.$fileName)) {
		
			$thisMIME = mime_content_type($this->pathMedia.$fileName);
			
			if (isset($mimes[$thisMIME])) {
			
				$thumbName = file_exists($this->pathThumbs.$fileName) ? $fileName : null;

/*				
				$this->models->MediaTaxon->save(
					array(
						'id' => 'null',
						'project_id' => $this->getNewProjectId(),
						'taxon_id' => $taxonId,
						'file_name' => $fileName,
						'thumb_name' => $thumbName,
						'original_name' => $fullName,
						'mime_type' => $thisMIME,
						'file_size' => filesize($this->pathMedia.$fileName)
					)
				);
*/					
				return array(
					'saved' => true,
					'filename' => $fileName,
					'full_path' => $this->pathMedia.$fileName,
					'thumb' => isset($thumbName) ? $thumbName : null,
					'thumb_path' => isset($thumbName) ? $this->pathThumbs.$thumbName : null
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

	private function addSpeciesMedia($d,$species)
	{

		$this->loadControllerConfig('Species');
		
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
	
		return str_replace(array('[b]','[/b]','[i]','[/i]','[br]','[p]','[/p]'),($removeAll?null:$r),$s);
	
	}

	private function resolveAuthors($s)
	{

		$d = strrpos($s,',');
		$y = trim(substr($s,$d+1));
		$a = substr($s,0,$d);
		$a2 = null;
		$m = false;
		$d = strpos($a,'et al.');
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
		
		return array(
			'year' => $y,
			'valid_year' => is_numeric($y),
			'author_1' => $a,
			'author_2' => $a2,
			'multiple_authors' => $m
		);

	}

	private function resolveLiterature($d,$species)
	{

		foreach($d->proj_literature->proj_reference as $key => $val) {

			$l = (string)$val->literature_title;
			$a = $this->resolveAuthors($l);
			$a['text'] = $this->replaceOldTags((string)$val->fullreference);
			$okSp = $unSp = null;

			foreach($val->keywords->keyword as $kKey => $kVal) {

				$t = $this->replaceOldTags((string)$kVal->name,true);
				if (isset($species[$t])) {
					$okSp[] = $species[$t]['id'];
				} else {
					$unSp[] = $t;
				}

			}


			$a['references'] = array('species' => $okSp,'unknown species' => $unSp);

			$res[] = $a;

		}

		return isset($res) ? $res : null;

	}

	private function addLiterature($d)
	{

		foreach($d as $v) {

			$this->models->Literature->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),				
					'author_first' => isset($val['author_1']) ? $val['author_1'] : null,
					'author_second' => (isset($val['author_2']) && $val['multiple_authors']==false) ? $val['author_2'] : null,
					'multiple_authors' => $val['multiple_authors']==true ? 1 : 0,
					'year' => (isset($val['year'])  && $val['valid_year'] == true) ? $val['year'] : null,
					'suffix' => isset($val['suffix']) ? $val['suffix'] : null,
					'text' => isset($val['text']) ? $val['text'] : null,
				)
			);
			
			$id = $this->models->Literature->getNewId();

			foreach((array)$v['references']['species'] as $kV) {

				$this->models->LiteratureTaxon->save(
					array(
						'id' => 'null',
						'project_id' => $this->getNewProjectId(),				
						'taxon_id' => $kV,
						'literature_id' => $id,
					)
				);

			}

		}

	}

	private function createKeyStep($step,$stepIds,$stepAdd=0)
	{

/*
		$this->model->Keystep->save(
			array(
				'id' => 'null',
				'project_id' => $this->getNewProjectId(),
				'number' => ($step=='god' ? -1 : ((string)$step->pagenumber +$stepAdd)),
				'is_start' => 0
			)
		);

		$stepId = $stepIds[($step=='god' ? -1 : (string)$step->pagenumber)] = $this->model->Keystep->getNewId();

		$this->model->ContentKeystep->save(
			array(
				'id' => 'null',
				'project_id' => $this->getNewProjectId(),
				'keystep_id' => $stepId,
				'language_id' => $this->getNewDefaultLanguageId(),
				'title' => 
					($step=='god' ? 
						'Choose key type' : 
						(isset((string)$step->pagetitle) ?
							(string)$step->pagetitle:
							(string)$step->pagenumber
						)
					),
				'content' =>
					($step=='god' ?
						'Choose between picture key and text key' : 
						(isset((string)$step->pagetitle) ?
							(string)$step->pagetitle:
							(string)$step->pagenumber
						)
					)
			)
		);


*/
$stepId = $stepIds[($step=='god' ? -1 : (string)$step->pagenumber)] = rand(0,120);
		return $stepIds;

	}


	private function createKeyStepChoices($step,$stepIds,$species)
	{

		if ($step=='god') {
			$choices[0]['destinationtype'] = $choices[1]['destinationtype'] = 'turn';
			$choices = (object)$choices;
		} elseif ($step->text_choice) {
			$choices = $step->text_choice;
		} else {
			$choices = $step->pict_choice;
		}

		foreach($choices as $key => $val) {

			$resStep = ((string)$val->destinationtype=='turn' ? 
							(isset($stepIds[(string)$val->destinationpagenumber]) ?
								$stepIds[(string)$val->destinationpagenumber] :
								($step=='god' ? 
									($key==0 ? 
										$stepIds['firstTxtId'] : 
										$stepIds['firstPictId']
									) : 
									null
								)
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

			if ($fileName && !file_exists($this->pathMedia.$fileName))
				$error[] = array(
					'cause' => 'Picture key image "'.$fileName.'" does not exist (choice created anyway)'
				);

/*
			$this->model->ChoiceKeystep->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),
					'keystep_id' => ($step=='god' ? $stepIds['godId'] : $stepIds[(string)$step->pagenumber]),
					'show_order' => $key,
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
	
			$this->model->ChoiceContentKeystep->save(
				array(
					'id' => 'null',
					'project_id' => $this->getNewProjectId(),
					'choice_id' => $this->model->ChoiceKeystep->getNewId(),
					'language_id' => $this->getNewDefaultLanguageId(),
					'choice_txt' => $txt
				)
			);

*/
		}


	
	}


	private function makeKey($d,$species)
	{
	
//		q($d->pict_key);
//		q($d->text_key);

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
/*			
			$k = $this->model->Keystep->_get(
				array(
					'id' => array('project_id' => $this->getNewProjectId()),
					'columns' => 'max(number) as `last`'
				)
			);
*/			
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

			$this->createKeyStepChoices(
				'god',
				array(
					'godId' => current($keyStepIds),
					'firstTxtId' => $firstTxtStepId,
					'firstPictId' => $firstPictStepId
				)
			);

			$this->model->Keystep->update(
				array('number' => 'number+1'),
				array(
					'project_id' => $this->getNewProjectId(),
					'number >=' => '1'
				)
			);

			$this->model->Keystep->update(
				array('number' => '1'),
				array(
					'project_id' => $this->getNewProjectId(),
					'number =' => '-1'
				)
			);

		}

		$this->model->Keystep->update(
			array('is_start' => '1'),
			array(
				'project_id' => $this->getNewProjectId(),
				'number =' => '1'
			)
		);
	
	}


/*
what the fuck is?
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
	

	proj_literature->proj_reference->keywords->keyword
		taxa only, what do glossary terms look like??

	WHAT MODULES!?!?!	



*/


    public function linnaeus2Action()
    {

		$file = 'C:\Users\maarten\Desktop\Tanbif_linnaeus\ExportFile\march20.xml';

		$d = simplexml_load_file ($file);

// switch what to import?

		
		// create new project
//		$newId = $this->createProject($d);
//		if (!$newId) die('eeek'); else set getNewProjectId

		// add language
//		if (!addProjectLanguage($d)) display ($d->project->language); and raise hell
//		else set getNewDefaultLanguageId

		// add content
//	`	$res = $this->addProjectContent($d);


		// get ranks
		$ranks = $this->resolveProjectRanks($d);
//		if res == null all hell (no ranks matched)
//		if all resolved (no falses) proceed
//		if falses, let user decide
//		more than one parent==null: show ans resolve

		// save ranks
//		$ranks = $this->addProjectRanks($res);

		$species = $this->resolveSpecies($d,$ranks);
//		user check --> might have spelling mistakes!!
		$treetops = $this->checkTreeTops($species);
//		if many: animalia / plantae => god species, BUT also spelling mistakes (sigh)

		// BEWARE THERE ARE FAKE ID'S IN THIS FUNCION!!!!!
		$overviewCatId = $this->createStandardCat();
		// BEWARE THERE ARE FAKE ID'S IN THIS FUNCION!!!!!
		$species = $this->addSpecies($species);
//		$this->addSpeciesContent($d,$species,$overviewCatId);
		$this->addSpeciesCommonNames($d,$species);
		$r = $this->addSpeciesMedia($d,$species);

		$lit = $this->resolveLiterature($d,$species);
// 		check for invalid years, might be suffix! (so set suffix in the $lit array)
		//$this->addLiterature($lit);

//		$this->makeKey($d,$species);

//q($d);
//yell about getControllerSettingsKey->maxChoicesPerKeystep

/*

	parse XML
		fail
			error
			exit
		success
			display cancel /save
		cancel
			exit
		save
			create project <- xml-data
			create standard user <- defaults
			load
			log
	

	xml-parsed
		file
			data of parsed file

v		project -> new project
v			title
v			version
			(default other required values)
		
v		==> we have new id!
v			set new id so 'getCurrentProjectId()' works
	
		project
v			.language -> march and select (if not exist, ask what it is from list)

v		==> set default language

v			.projectintroduction --> introduction text
v			.contributors --> contributors text
???			.classification --> "Five kingdoms"
???			.nomenclaturecode --> "ICZM"

		

v		tree
v			.treetaxon
v				.taxon -> rankname -> resolve to standard ranks, and FAIL if not exist (?)


v		tree
v			.treetaxon
v				.parenttaxon -> parent taxon rank (ignore) or 'none' (treetop)
v				.parentname -> resolve to taxon_id (empty when parenttaxon == none) -
v				.taxon -> rankname -> resolve to rank_id
v				.name -> name



MATRIX

	loop records
		$matrices[filename][characters][] = name / type
			states
				state_name
				state_min
				state_max
				state_mean
				state_sd
				state_file ??





	first check all filenames and create matrices from them

		records
				.identify
					.id_file
						.filename --> resolve matrix id
???						.obj_link --> ??? (seems empty)
						.character_
							.character_name --> "Habitat"
							.character_type --> "Text"
							.states --> 
								etc
							
					
















*/
	
	
	}



	private function deleteLiterature($id)
	{

		$this->models->LiteratureTaxon->delete(array('project_id' => $id));
		$this->models->Literature->delete(array('project_id' => $id));
			
	}

	private function deleteSpeciesMedia($id)
	{
// GET PATHS!!!!
		$mt = $this->models->MediaTaxon->_get(
			array(
				'id' => array(
					'project_id' => $id
				)
			)
		);

		foreach((array)$mt as $val) {

			if (isset($val['file_name'])) @unlink(MEDIA_PATH.$val['file_name']);
			if (isset($val['thumb_name'])) @unlink(THUMB_PATH.$val['thumb_name']);

		}

		$this->models->MediaTaxon->delete(array('project_id' => $id));

	}

	private function deleteStandardCat()
	{
	
		$this->model->PageTaxaTitle->delete(array('project_id' => $id));
		$this->model->PageTaxa->delete(array('project_id' => $id));

	}
	
	private function deleteSpecies($species)
	{

		$this->models->Taxon->delete(array('project_id' => $this->getNewProjectId()));

	}

	private function deleteProjectRanks($id)
	{

		$this->models->ProjectRank->delete(array('project_id' => $id));

	}

	private function deleteProjectContent($id)
	{
	
		$this->models->LanguageProject->delete(
			array(
				'project_id' => $id
			)
		);
	
	}

	private function deleteProjectLanguage($id)
	{
	
		$this->models->LanguageProject->delete(
			array(
				'project_id' => $id
			)
		);
	
	}

	private function deleteProject($id)
	{
	
		$p = $this->models->Project->delete(
			array(
				'id' => $id,
			)
		);
	
		return ($p) ? $this->models->Project->getNewId() : false;
	
	}
	
	
}