<?php

include_once ('Controller.php');

class SearchController extends Controller
{

	private $_minSearchLength = 3;
	private $_maxSearchLength = 50;

	const S_TOKENIZED_TERMS=0;
	const S_FULLTEXT_STRING=1;
	const S_CONTAINS_LITERALS=2;
	const S_IS_CASE_SENSITIVE=3;
	const S_RESULT_LIMIT_PER_CAT=4;
	const S_LIKETEXT_STRING=5;
	const S_UNSET_ORIGINAL_CONTENT=6;
	const S_EXTENDED_SEARCH=7;
	const S_LIKETEXT_REPLACEMENT='###';
	const __CONCAT_RESULT__='__CONCAT_RESULT__';
	const V_RESULT_LIMIT_PER_CAT=200;

	const C_TAXA_SCI_NAMES=100;
	const C_TAXA_DESCRIPTIONS=101;
	const C_TAXA_SYNONYMS=102;
	const C_TAXA_VERNACULARS=103;
	const C_TAXA_ALL_NAMES=104;
	const C_SPECIES_MEDIA=105;

    public $usedModels = array(
		'content',
        'content_taxon', 
        'page_taxon', 
        'page_taxon_title', 
        'media_taxon',
        'media_descriptions_taxon',
		'synonym',
		'commonname',
		'literature',
		'content_free_module',
		'choice_content_keystep',
		'content_keystep',
		'choice_keystep',
		'keystep',
		'literature',
		'glossary',
		'glossary_media',
		'glossary_synonym',
		'matrix',
		'matrix_name',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'geodata_type_title',
		'occurrence_taxon',
		'content_introduction',
		'name_types',
		'presence',
		'page_taxon_title'
    );

    public function __construct ()
    {

        parent::__construct();

		$this->initialize();

    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		$this->_minSearchLength = isset($this->controllerSettings['minSearchLength']) ? $this->controllerSettings['minSearchLength'] : $this->_minSearchLength;
		$this->_maxSearchLength = isset($this->controllerSettings['maxSearchLength']) ? $this->controllerSettings['maxSearchLength'] : $this->_maxSearchLength;
	}


	public function validateSearchString($s)
	{
		return
			(strlen($s)>=$this->_minSearchLength) &&  // is it long enough?
			(strlen($s)<=$this->_maxSearchLength);    // is it short enough?
	}



}