<?php

class configuration
{
    
	private $_appFileRoot;

    public function __construct ()
    {

		$d = $this->getGeneralSettings();
		$d['app']['pathName'];
		$this->_appFileRoot = dirname(__FILE__);
		$this->_appFileRoot = str_replace('\\','/',
			substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));

	}

    public function getGeneralSettings ()
    {
        
        return array(
            'debugMode' => false, 
            'app' => array(
				'name' => 'Linnaeus NG Administration', 
            	'version' => '@APP.VERSION@', 
            	'versionTimestamp' => '@TIMESTAMP@', 
				'pathName' => 'admin',
			),
            'maxSessionHistorySteps' => 10, 
            'heartbeatFrequency' => 60000,  // milliseconds
            'autosaveFrequency' => 300000,  // milliseconds
            'controllerIndexNameExtension' => '-index.php', 
            'paths' => array(
                'login' => '/views/users/login.php', 
                'logout' => '/views/users/logout.php', 
                'chooseProject' => '/views/users/choose_project.php', 
                'notAuthorized' => '/views/utilities/not_authorized.php', 
                'moduleNotPresent' => '/views/utilities/module_not_present.php'
            ), 
            'directories' => array(
                'mediaDirProject' => $this->_appFileRoot . 'www/admin/images/project', 
                'mediaDirUpload' => $this->_appFileRoot . 'www/admin/images/upload'
            ), 
            'maxSubPages' => 10,
			'login-cookie' => array(
				'name' => 'linnaeus-login',
				'lifetime' => 30, // days
			)
        );
    
    }

    public function getControllerSettingsSpecies()
    {
        
        return array(
			'defaultSubPages' =>
				array(
					0 => array(
						'name' => 'Overview',
						'default' => true,
						'mandatory' => array(0),
						'sections' => array ('General description','Biology')
					),
					1 => array(					
						'name' => 'Detailed Description',
						'sections' => array ('Behaviour','Cytology','Diagnostic Description',
							'Genetics','Look Alikes','Molecular Biology','Morphology','Physiology',
							'Size','Taxon Biology')
					),
					2 => array(					
						'name' => 'Ecology',
						'sections' => array ('Associations','Cyclicity','Dispersal','Distribution',
							'Ecology','Habitat','Life Cycle','Life Expectancy','Migration','Trophic Strategy')
					),
					3 => array(					
						'name' => 'Conservation',
						'sections' => array ('Conservation Status','Legislation','Management','Procedures',
							'Threats','Trends')
					),
					4 => array(					
						'name' => 'Relevance',
						'sections' => array ('Diseases','Risk Statement','Uses')
					),
					5 => array(					
						'name' => 'Reproductive',
						'sections' => array ('Population Biology','Reproduction')
					)
				),
			'filterContent' =>
				array(
					'html' => array(
						'doFilter' => true,
						'allowedTags' => '<a><b><u><i><p><h1><h2><h3><h4><h5><h6><ul><ol><li><table><th><tr><td>'
					)
				),
			'media' =>
				array(
					'allowedFormats' => 
						array(
							'image/png' => 'image',
							'image/jpg' => 'image',
							'image/jpeg' => 'image',
							'image/gif' => 'image',
							'video/h264' => 'video',
							'video/quicktime' => 'video',
							'audio/mpeg' => 'sound',
							'application/zip' => 'archive',
						),
	                'defaultUploadMaxSize' => 50000000 //50 mb (h264!?)
				),				
			);

    }

    public function getDatabaseSettings ()
    {
        
        return array(
            'host' => '@DB.HOST@', 
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
            'dir_template' => $this->_appFileRoot . 'www/admin/templates/templates', 
            'dir_compile' => $this->_appFileRoot . 'www/admin/templates/templates_c', 
            'dir_cache' => $this->_appFileRoot . 'www/admin/templates/cache', 
            'dir_config' => $this->_appFileRoot . 'www/admin/templates/configs', 
            'caching' => 1,  // 1,
            'compile_check' => true
        );
    
    }

}

