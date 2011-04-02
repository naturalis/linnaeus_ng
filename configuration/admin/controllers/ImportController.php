<?php


class myIterator implements Iterator {
    private $position = 0;
    private $array = array(
        "firstelement",
        "secondelement",
        "lastelement",
    );  

    public function __construct() {
        $this->position = 0;
    }

    function rewind() {
        var_dump(__METHOD__);
        $this->position = 0;
    }

    function current() {
        var_dump(__METHOD__);
        return $this->array[$this->position];
    }

    function key() {
        var_dump(__METHOD__);
        return $this->position;
    }

    function next() {
        var_dump(__METHOD__);
        ++$this->position;
    }

    function valid() {
        var_dump(__METHOD__);
        return isset($this->array[$this->position]);
    }
}


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

	private function addSpecies($species)
	{

		foreach((array)$species as $key => $val) {

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

	taxa tyable
		'taxon_order' => 0,
		'is_hybrid' => 0,
		'list_level' => 0
	

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
//		$species = $this->addSpecies($species);





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

//IF TWO TOP TAXA WITHOUT PARENT (animalis / planate) CREATE A "GOD" TAXON AS THEIR PARENT!
// create out of system rank_id 999 in  dev_projects_ranks;

		proj_literature
			.proj_reference
				.literature_title -> author_first / author_second / multiple_authors / year / suffix
				.fullreference -> text
				.keywords
					.keyword
???						.name -> taxon and/or glossary (seems taxon only, whar does glossary look like?)
		
for [p] etc: find all and propose defaults, find internal links

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