<?php

include_once ('Controller.php');

class LoaderController extends Controller
{

	public $cssToLoad = array('basics.css');

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

		if ($this->rHasVal('go','load')) {

			$this->doPreload();
			
			die();
		
		}

		$_SESSION['app']['project']['showedSplash'] = true;

		$this->smarty->assign('startUrl','../'.$this->generalSettings['defaultController'].'/');

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
//		$this->preloadMap();
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

	}

	private function preloadIndex()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/IndexController.php');
		$c = new IndexController(array('checkForSplash'=>false));

	}

	private function preloadIntroduction()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/IntroductionController.php');
		$c = new IntroductionController(array('checkForSplash'=>false));

	}

	private function preloadKey()
	{

		require_once ('../../../../configuration/app/controllers/KeyController.php');
		$c = new KeyController(array('checkForSplash'=>false));

		$c->loadControllerConfig('Key');
		
		$c->setKeyTree();
		$c->getAllTaxaInKey();

		$c->loadControllerConfig();

	}

	private function preloadLinnaeus()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/LinnaeusController.php');
		$c = new LinnaeusController(array('checkForSplash'=>false));

	}

	private function preloadLiterature()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/LiteratureController.php');
		$c = new LiteratureController(array('checkForSplash'=>false));

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

		$this->loadControllerConfig();

		unset($c);

	}

	private function preloadMatrix()
	{

		require_once ('../../../../configuration/app/controllers/MatrixKeyController.php');
		$c = new MatrixKeyController(array('checkForSplash'=>false));

		$d = $c->getMatrices();
		
		foreach((array)$d as $val)  $c->getTaxaInMatrix($val['id']);
		
		unset($c);

	}

	private function preloadModule()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/ModuleController.php');
		$c = new ModuleController(array('checkForSplash'=>false));

	}

	private function preloadSearch()
	{

		return; // nothing to preload
		
		require_once ('../../../../configuration/app/controllers/SearchController.php');
		$c = new SearchController(array('checkForSplash'=>false));

	}

	private function preloadSpecies()
	{

		//require_once ('../../../../configuration/app/controllers/SpeciesController.php');
		//$c = new SpeciesController();
		
		$d = $this->getCurrentLanguageId();
		
		$l = $this->getProjectLanguages();
		
		foreach((array)$l as $val) {

			$this->setCurrentLanguageId($val['id']);
			$this->buildTaxonTree();
		
		}
		
		$this->setCurrentLanguageId($d);

	}

}
