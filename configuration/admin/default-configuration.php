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
                'mediaDirProject' => $this->_appFileRoot . 'www/admin/media/project', 
                'mediaDirUpload' => $this->_appFileRoot . 'www/admin/media/upload',
				'locale' => $this->_appFileRoot . 'configuration/locale',
				'log' => $this->_appFileRoot . 'log',
            ), 
            'maxCategories' => 10,
            'login-cookie' => array(
                'name' => 'linnaeus-login',
                'lifetime' => 30, // days
            ),
			'uiLanguages' => array('Dutch','English'),
			'uiDefaultLanguage' => 1 // points to the element in the array above
        );
    
    }

    public function getControllerSettingsProjects()
    {
        
        return array(
		  'media' =>
			array(
			  'allowedFormats' => 
				array(
				  array(
					'mime' => 'image/png', 
					'media_name' => 'PNG image', 
					'media_type' => 'image', 
					'maxSize' => 500000
				  ),
				  array(
					'mime' => 'image/jpg', 
					'media_name' => 'JPG image', 
					'media_type'  => 'image', 
					'maxSize' => 500000
				  ),
				  array(
					'mime' => 'image/jpeg', 
					'media_name' => 'JPG image', 
					'media_type'  => 'image', 
					'maxSize' => 500000
				  ),

				),
				'defaultUploadMaxSize' => 2000000 // 2mb
			),
			'freeModulesMax' => 5
      	);

    }

    public function getControllerSettingsUsers()
    {
        return array(
			'dataChecks' =>
				array(
					'username' =>
						array(
							'minLength' => 2,
							'maxLength' => 32
						),
					'password' =>
						array(
							'minLength' => 6,
							'maxLength' => 24
						),
					'first_name' =>
						array(
							'minLength' => 1,
							'maxLength' => 32
						),
					'last_name' =>
						array(
							'minLength' => 1,
							'maxLength' => 32
						),
					'email_address' =>
						array(
							'minLength' => 1,
							'maxLength' => 64,
							'regexp' => '/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/'
						)
				)
		);
	}

    public function getControllerSettingsSpecies()
    {
        
        return array(
            'defaultCategories' =>
                array(
                    0 => array(
                        'name' => 'Overview',
                        'default' => true,
                        'mandatory' => true,
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
                        'allowedTags' => '<a><b><u><i><p><span><h1><h2><h3><h4><h5><h6><ul><ol><li><table><th><tr><td>'
                    )
                ),
            'media' =>
                array(
                    'allowedFormats' => 
                        array(
                            array(
                                'mime' => 'image/png', 
                                'media_name' => 'PNG movie', 
                                'media_type' => 'image', 
                                'maxSize' => 1000000
                            ),
                            array(
                                'mime' => 'image/jpg', 
                                'media_name' => 'JPG image', 
                                'media_type'  => 'image', 
                                'maxSize' => 1000000
                            ),
                            array(
                                'mime' => 'image/jpeg', 
                                'media_name' => 'JPG image', 
                                'media_type'  => 'image', 
                                'maxSize' => 1000000
                            ),
                            array(
                                'mime' => 'image/gif', 
                                'media_name' => 'GIF image', 
                                'media_type'  => 'image', 
                                'maxSize' => 1000000
                            ),
                            array(
                                'mime' => 'video/h264', 
                                'media_name' => 'h.264 movie', 
                                'media_type'  => 'video', 
                                'maxSize' => 50000000
                            ),
                            array(
                                'mime' => 'video/quicktime', 
                                'media_name' => 'Quicktime', 
                                'media_type'  => 'video', 
                                'maxSize' => 50000000
                            ),
                            array(
                                'mime' => 'audio/mpeg', 
                                'media_name' => 'mp3', 
                                'media_type'  => 'sound', 
                                'maxSize' => 10000000
                            ),
                            array(
                                'mime' => 'application/zip', 
                                'media_name' => 'ZIP-file', 
                                'media_type' => 'archive', 
                                'maxSize' => 50000000
                            ),
                        ),
                    'defaultUploadMaxSize' => 50000000 //50 mb (h264!?)
                ),                
            );

    }

    public function getControllerSettingsKey()
    {
        
        return array(
		  'media' =>
			array(
			  'allowedFormats' => 
				array(
				  array(
					'mime' => 'image/png', 
					'media_name' => 'PNG movie', 
					'media_type' => 'image', 
					'maxSize' => 2000000
				  ),
				  array(
					'mime' => 'image/jpg', 
					'media_name' => 'JPG image', 
					'media_type'  => 'image', 
					'maxSize' => 2000000
				  ),
				  array(
					'mime' => 'image/jpeg', 
					'media_name' => 'JPG image', 
					'media_type'  => 'image', 
					'maxSize' => 2000000
				  ),
				  array(
					'mime' => 'image/gif', 
					'media_name' => 'GIF image', 
					'media_type'  => 'image', 
					'maxSize' => 2000000
				  ),
				),
				'defaultUploadMaxSize' => 2000000 // 2mb
			),
			'maxChoicesPerKeystep' => 4      
      	);

    }

    public function getControllerSettingsLiterature()
    {

        return array('allowedTags' => '<i>');

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

