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
    
        $this->checkAuthorisation();

        $this->printPage();
    
    }


    /**
     * Introduction
     *
     * @access    public
     */

    public function linnaeus2Action()
    {


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
	


WHAT MODULES!?!?!	

default but yell about:
	- border higher/lower taxa (give choice)
	
	xml-parsed
		file
			data of parsed file

		project -> new project
			title
			version
			(default other required values)
		
		==> we have new id!
			set new id so 'getCurrentProjectId()' works
	
		project
			.language -> march and select (if not exist, ask what it is from list)
			.projectintroduction --> introduction text
			.contributors --> contributors text
???			.classification --> "Five kingdoms"
???			.nomenclaturecode --> "ICZM"

		==> set default language
		

		tree
			.treetaxon
				.taxon -> rankname -> resolve to standard ranks, and FAIL if not exist (?)

		IF TWO TOP TAXA WITHOUT PARENT (animalis / planate) CREATE A "GOD" TAXON AS THEIR PARENT!

		tree
			.treetaxon
				.parenttaxon -> parent taxon rank (ignore) or 'none' (treetop)
				.parentname -> resolve to taxon_id (empty when parenttaxon == none) -
				.taxon -> rankname -> resolve to rank_id
				.name -> name

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


	
	
}