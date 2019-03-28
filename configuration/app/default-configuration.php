<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Amsterdam');
ini_set('session.gc_maxlifetime',86400);
ini_set('memory_limit', '512M');
if(file_exists( __DIR__ . '/custom-configuration.php')) include_once( __DIR__ . '/custom-configuration.php' );
class configuration
{

    private $_appFileRoot;
    private $_skinName;

    public function __construct ()
    {
        $d = $this->getGeneralSettings();
        $this->_appFileRoot = __DIR__;
        $this->_appFileRoot = str_replace('\\','/',
            substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));

		$this->_skinName = $d['app']['skinName'];

    }

    public function getDatabaseSettings ()
    {

        return array(
            'host' => 'localhost',
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
            'dir_template' => $this->_appFileRoot . 'www/app/templates/templates/',
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
            'app' => array(
				'name' => 'Linnaeus NG',
				'version' => '0.1-dev.r001',
				'versionTimestamp' => date('r'),
				'pathName' => 'app',
				'fileRoot' => $this->_appFileRoot.'www/app/',
				//'skinName' => 'original_skin'
				'skinName' => 'linnaeus_ng',
				'skinNameWebservices' => 'webservices'
			),
			'defaultController' => 'linnaeus',
			'startUpUrl' => '/index.php',
			'showEntryProgramLink' => true,
			//'urlNoProjectId' => '../../../app/views/linnaeus/set_project.php',
			'urlNoProjectId' => '../../../app/views/linnaeus/no_project.php',
			'urlUploadedProjectMedia' => '../../../shared/media/project/',
			//'urlSplashScreen' => '../../../app/views/linnaeus/splash.php',
			'lngFileRoot' => $this->_appFileRoot,
            'paths' => array(
            ),
            'directories' => array(
				'log' => $this->_appFileRoot . 'log',
				'mediaDirProject' => $this->_appFileRoot . 'www/shared/media/project',
				'customStyle' => $this->_appFileRoot . 'www/app/style/custom'
            ),
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
				'mapkey:l2_examine_species' => '../../../admin/views/mapkey/choose_species.php',
				'matrixkey:identify' => '../../../admin/views/matrixkey/index.php?id=%s',
				'key:index' => '../../../admin/views/key/step_show.php?id=%s',
			),
			'useGlossaryPostIts' => false,
			'addedProjectIDParam' => 'epi',
        );

    }

}
