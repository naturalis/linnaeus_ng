<?php
include_once ('Controller.php');
include_once ('ModuleSettingsController.php');

class SearchController extends Controller
{
	protected $_minSearchLength = 3;
	protected $_maxSearchLength = 50;

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

    );

    public function __construct ()
    {

        parent::__construct();
		$this->initialise();
   }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialise()
	{
		$this->moduleSettings=new ModuleSettingsController(['controllerBaseName' => 'utilities']);
		$this->moduleSettings->setUseDefaultWhenNoValue( true );
		$this->_minSearchLength = $this->moduleSettings->getModuleSetting( 'min_search_length',3);
		$this->_maxSearchLength = $this->moduleSettings->getModuleSetting( 'max_search_length',50);
	}

	protected function validateSearchString($s)
	{
		return
			(strlen($s)>=$this->_minSearchLength) &&  // is it long enough?
			(strlen($s)<=$this->_maxSearchLength);    // is it short enough?
	}

	// removes interfering noise from search term
	protected function removeSearchNoise( $search )
	{
		$noise = [
			$this->_hybridMarker,
			$this->_hybridMarkerHtml,
			$this->_formaMarker,
			$this->_hybridMarker_graftChimaera,
			$this->_varietyMarker,
			$this->_subspeciesMarker,
			$this->_nothoInfixPrefix . $this->_varietyMarker,
			$this->_nothoInfixPrefix . $this->_subspeciesMarker,
		];
		 
		return preg_replace('/(\s+)/',' ',str_replace($noise,' ', $search));
	}
}