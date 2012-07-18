<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Amsterdam');
class configuration
{

    private $_appFileRoot;
    private $_skinName;

    public function __construct ()
    {

		$this->_setConstants();	
        $d = $this->getGeneralSettings();
        $this->_appFileRoot = dirname(__FILE__);
        $this->_appFileRoot = str_replace('\\','/',
            substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));

		$this->_skinName = $d['app']['skinName'];

    }

    public function getDatabaseSettings ()
    {
        
        return array(
            'host' => '127.0.0.1', 
            'user' => 'linnaeus_user', 
            'password' => 'car0lu5', 
            'database' => 'linnaeus_ng', 
            'tablePrefix' => 'dev_', 
            'characterSet' => 'utf8'
        );
    
    }

    public function getSmartySettings ()
    {

        return array(
            'dir_template' => $this->_appFileRoot . 'www/app/templates/templates/'.$this->_skinName, 
            'dir_compile' => $this->_appFileRoot . 'www/app/templates/templates_c', 
            'dir_cache' => $this->_appFileRoot . 'www/app/templates/cache', 
            'dir_config' => $this->_appFileRoot . 'www/app/templates/configs', 
            'caching' => 1,  // 1,
            'compile_check' => true
        );
    
    }

    public function getGeneralSettings ()
    {

        return array(
            'debugMode' => false, 
            'app' => array(
				'name' => 'Linnaeus NG', 
				'version' => '0.1-dev.r001', 
				'versionTimestamp' => date('r'), 
				'pathName' => 'app',
				'fileRoot' => $this->_appFileRoot.'www/app/',
				//'skinName' => 'original_skin'
				'skinName' => 'linnaeus_2'
			),
			'defaultController' => 'linnaeus',
			'startUpUrl' => '/index.php',
			'showEntryProgramLink' => true,
			'urlNoProjectId' => '../../../app/views/linnaeus/set_project.php',
			'urlUploadedProjectMedia' => '../../../admin/media/project/',
			'lngFileRoot' => $this->_appFileRoot,
            'maxSessionHistorySteps' => 10, 
            'controllerIndexNameExtension' => '-index.php', 
            'paths' => array(
            ), 
            'directories' => array(
				'locale' => $this->_appFileRoot . 'configuration/locale',
				'log' => $this->_appFileRoot . 'log',
            ),
			'hybridMarker' => 'X',
			'maxBackSteps' => 100,
			'urlsToAdminEdit' => array(
				'introduction:topic' => '../../../admin/views/introduction/edit.php?id=%s',
				'glossary:term' => '../../../admin/views/glossary/edit.php?id=%s',
				'literature:reference' => '../../../admin/views/literature/edit.php?id=%s',
				'species:taxon' => '../../../admin/views/species/taxon.php?id=%s',
				'species:taxon:literature' => '../../../admin/views/species/literature.php?id=%s',
				'species:taxon:names' => '../../../admin/views/species/synonyms.php?id=%s',
				'module:topic' => '../../../admin/views/module/index.php?page=%s&freeId=%s',
				'linnaeus:content' => '../../../admin/views/content/content.php?page=%s&freeId=%s',
				'mapkey:examine_species' => '../../../admin/views/mapkey/species_edit.php?id=%s',
				'matrixkey:identify' => '../../../admin/views/matrixkey/index.php?id=%s',
				'key:index' => '../../../admin/views/key/step_show.php?id=%s',
			),
			'useJavascriptLinks' => false,
			'useGlossaryPostIts' => false
        );
    
    }

    public function getControllerSettingsLinnaeus()
    {

        return array(
            'minimumSearchStringLength' => 3,
            'visibleSearchResultsPerCategory' => 10,
			'contentAboutETI' => 
				array(
					'sub' => 'About ETI',
					'projectID' => -10
				)
		);

	}

    public function getControllerSettingsKey ()
    {

        return array(
            'keyPathMaxItems' => 3
		);

	}

    public function getControllerSettingsSpecies ()
    {

        return array(
            'speciesPerPage' => 25,
			'mime_types' => array(
				'image/png' => array(
					'label' => 'Images',
					'type' => 'image',
				),
				'image/jpg' => array(
					'label' => 'Images', 
					'type' => 'image',
				),
				'image/jpeg' => array(
					'label' => 'Images', 
					'type' => 'image',
				),
				'image/gif' => array(
					'label' => 'Images', 
					'type' => 'image',
				),
				'video/h264' => array(
					'label' => 'Videos', 
					'type' => 'video',
				),
				'video/quicktime' => array(
					'label' => 'Videos', 
					'type' => 'video',
				),
				'audio/mpeg' => array(
					'label' => 'Sounds', 
					'type' => 'audio',
				),
			),
			'mime_show_order' => array(
				'image' => 1,
				'video' => 2,
				'audio' => 3,
			)
		);

	}

    public function getControllerSettingsHighertaxa ()
    {

        return array(
            'speciesPerPage' => 25
		);

	}

    public function getControllerSettingsMatrixKey()
    {

		return array(
			'characteristicTypes' => array(
				array(
					'name' => 'text',
					'info' => 'a textual description.'
				),
				array(
					'name' => 'media',
					'info' => 'an image, video or soundfile.'
				),
				array(
					'name' => 'range',
					'info' => 'a value range, defined by a lowest and a highest value.'
				),
				array(
					'name' => 'distribution',
					'info' => 'a value distribution, defined by a mean and values for one and two standard deviations.'
				)
			),
			'useCorrectedHValue' => true
		);

    }

    public function getControllerSettingsMapKey()
    {

		return array(
			'speciesPerPage' => 20,
            'maxTypes' => 10,
			'urlToCheckConnectivity' =>'http://maps.google.com/maps/api/js?sensor=false',
			'SRID' => 4326,
			'l2DiversityIndexNumOfClasses' => 8, // be aware that increasing this does *not* automatically create extra css classes
			'l2MaxMapWidth' => 0 // maps exceeding this size are automatically resized, set to 0 to ignore
		);

    }

	private function _setConstants()
	{

		if (!defined('MODCODE_INTRODUCTION')) define('MODCODE_INTRODUCTION',1);
		if (!defined('MODCODE_GLOSSARY')) define('MODCODE_GLOSSARY',2);
		if (!defined('MODCODE_LITERATURE')) define('MODCODE_LITERATURE',3);
		if (!defined('MODCODE_SPECIES')) define('MODCODE_SPECIES',4);
		if (!defined('MODCODE_HIGHERTAXA')) define('MODCODE_HIGHERTAXA',5);
		if (!defined('MODCODE_KEY')) define('MODCODE_KEY',6);
		if (!defined('MODCODE_MATRIXKEY')) define('MODCODE_MATRIXKEY',7);
		if (!defined('MODCODE_DISTRIBUTION')) define('MODCODE_DISTRIBUTION',8);
		if (!defined('MODCODE_CONTENT')) define('MODCODE_CONTENT',10);
		if (!defined('MODCODE_INDEX')) define('MODCODE_INDEX',11);
		if (!defined('MODCODE_UTILITIES')) define('MODCODE_UTILITIES',12);
		
	}


}