<?php

/*

	beware!

	calling the same procedure in an external class multiple times from a loop in 
	this class based on the output from another function in the external class can cause 
	strange results. in one example, a get()-call to the MatrixTaxon-model in the matrix-
	class caused the model to lose the where-clause-variables, which were still intact in
	the line before the get(). also, connection to the database appeared to fall away 
	occasionally.

	while not fully understood, this is probably to do with some kind of overlap between 
	this class and the external one, as they both extend from the same controller.

	if necessary, write a new procedure in the external class that performs all of the 
	required work in one call, as in preloadMatrix(). not quite sure why the loop in 
	preloadMap() doesn't cause trouble.


	$data = $this->getCache('controller-cache_name');
	if (!$data) {
		$this->saveCache('controller-cache_name',$data);
	}

*/


include_once ('Controller.php');

class LoaderController extends Controller
{

	public $cssToLoad = array('basics.css');
	public $jsToLoad = array('all' => array('main.js'));

    public function __construct($p=null)
    {

        parent::__construct($p);

    }

    public function __destruct ()
    {
        
        parent::__destruct();
    
    }

    public function splashAction ()
    {
	
		//$this->doPreload();die(); // straight debug version (no ajax)
	
		if ($this->rHasVal('go','load')) {

			$this->doPreload();
			
			die();
		
		}

		$url =
			isset($_SESSION['app']['project']['splashEntryUrl']) ? 
				$_SESSION['app']['project']['splashEntryUrl'] : 
				'../'.$this->generalSettings['defaultController'].'/';

		$_SESSION['app']['project']['showedSplash'] = true;
		unset($_SESSION['app']['project']['splashEntryUrl']);

		$this->smarty->assign('startUrl',$url);

        $this->printPage('../linnaeus/splash');


    }

	private function doPreload()
	{

		$execStart = microtime(true);

		$this->preloadGlossary();
		$this->preloadIndex();
		$this->preloadIntroduction();
		$this->preloadKey();
		$this->preloadLinnaeus();
		$this->preloadLiterature();
		$this->preloadMap();
		$this->preloadMatrix();
		$this->preloadModule();
		$this->preloadSearch();
		$this->preloadSpecies();

		sleep(max(0,($this->generalSettings['splashDelay']-((microtime(true) - $execStart) / 1000))));

	}

	private function preloadGlossary()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/GlossaryController.php');
		$c = new GlossaryController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadIndex()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/IndexController.php');
		$c = new IndexController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadIntroduction()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/IntroductionController.php');
		$c = new IntroductionController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadKey()
	{

		require_once ('../../../../configuration/app/controllers/KeyController.php');
		$c = new KeyController(array('checkForSplash'=>false));

		$c->loadControllerConfig('Key');

		$c->getKeyTree();
		$c->getAllTaxaInKey();
		$c->getTaxonDivision($c->getStartKeystepId());

		$c->loadControllerConfig();

		unset($c);

	}

	private function preloadLinnaeus()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/LinnaeusController.php');
		$c = new LinnaeusController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadLiterature()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/LiteratureController.php');
		$c = new LiteratureController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadMap()
	{

		require_once ('../../../../configuration/app/controllers/MapKeyController.php');
		$c = new MapKeyController(array('checkForSplash'=>false));

		$c->loadControllerConfig('MapKey');

		$m = $c->l2GetMaps();
		// too lazy to create all possible combinations of typeId's and cache them all
		//$t = $c->getGeodataTypes();
		
		foreach((array)$m  as $mVal) $c->l2GetDiversityIndex($mVal['id']);

		$c->l2GetTaxaOccurrenceCount();
		$c->l2GetTaxaWithOccurrences();
		$c->getLookupList(
			array(
				'search' => '*',
				'match_start' => '1',
				'get_all' => '1',
			)
		);

		unset($c);

	}

	private function preloadMatrix()
	{

		require_once ('../../../../configuration/app/controllers/MatrixKeyController.php');
		$c = new MatrixKeyController(array('checkForSplash'=>false));

		$d = $c->cacheAllTaxaInMatrix();

		unset($c);

	}

	private function preloadModule()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/ModuleController.php');
		$c = new ModuleController(array('checkForSplash'=>false));
		// code goes here
		unset($c);

	}

	private function preloadSearch()
	{

		require_once ('../../../../configuration/app/controllers/SearchController.php');
		$c = new SearchController(array('checkForSplash'=>false));

		$c->getGlossaryLookupList();
		$c->getLiteratureLookupList();
		//$c->getSpeciesLookupList(); // might be too complex (see remark at top of file)
		$c->getModuleLookupList();  //idem

		unset($c);

	}

	private function preloadSpecies()
	{

		//require_once ('../../../../configuration/app/controllers/SpeciesController.php');
		//$c = new SpeciesController();

		$this->loadControllerConfig('Species');

		$this->getProjectRanks();

		$this->buildTaxonTree();
		
		$this->loadControllerConfig();

	}

}
