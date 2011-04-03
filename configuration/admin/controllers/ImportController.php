<?php

include_once ('Controller.php');

class ImportController extends Controller
{

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

	private function addSpeciesContent($d,$species)
	{

die('hiero!');

		foreach((array)$species as $key => $val) {

/*
		records
				.parenttaxon -> parent taxon rank (ignore)
				.parentname ->  parent taxon name (ignore)
				.taxon -> rankname (ignore)
				.name -> resolve to taxon_id (and holler when that fails)
???				.description -> content, cat = ??? (filter [p] etc)
				.taxonomy -> ignore (auto)
				.vernacuars
					.vernacular
						.name -> common name
						.language -> common name language in default language
				.syn_vern_description -> ignore
				.multimedia
???					.overview	-> image name only 
					.multimediafile
						.filename --> filename
						.fullname --> same (ignore)
						.caption --> (opt) description (<-- otherwise filename)
???						.multimedia_type --> "image"
*/
		}

		return $species;

	}

	private function replaceOldTags($s,$removeAll=false)
	{
	
		$r = array('<b>','</b>','<i>','</i>',null,null);
	
		return str_replace(array('[b]','[/b]','[i]','[/i]','[p]','[/p]'),($removeAll?null:$r),$s);
	
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




/*
what te fuck is?
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

//		q($d->tree);


//		q($d);

		

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
		$species = $this->addSpecies($species);
		$this->addSpeciesContent($d,$species);




		$lit = $this->resolveLiterature($d,$species);
// 		check for invalid years, might be suffix! (so set suffix in the $lit array)
		//$this->addLiterature($lit);




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



						

KEY
	if both PIC and TEXT, create first GOD step (of end with that)



MATRIX
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