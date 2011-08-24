<?php

class configuration
{
    
    private $_appFileRoot;

    public function __construct ()
    {

		$this->setConstants();

        $d = $this->getGeneralSettings();
        $d['app']['pathName'];
        $this->_appFileRoot = dirname(__FILE__);
        $this->_appFileRoot = str_replace('\\','/',
            substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));

    }

	private function setConstants()
	{

		if (!defined('ID_ROLE_SYS_ADMIN')) define('ID_ROLE_SYS_ADMIN',1);
		if (!defined('ID_ROLE_LEAD_EXPERT')) define('ID_ROLE_LEAD_EXPERT',2);

		if (!defined('TIMEOUT_COL_RETRIEVAL')) define('TIMEOUT_COL_RETRIEVAL',600); // secs.

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
			'serverTimeZone' => 'Europe/Amsterdam',
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
			'uiDefaultLanguage' => 1, // points to the element in the array above
			'soundPlayerPath' => '../../media/system/',
			'soundPlayerName' => 'player_mp3.swf'
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
				),
				'randomPassword' =>
					array(
						'chars' => 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789',
						'length'=> 8
					),
				'email' =>
					array(
						'smtp_server' => 'smtp.eti.uva.nl',
						'mailfrom_address' => 'linnaeus@eti.uva.nl',
						'mailfrom_name' => 'Linnaeus NG',
						'mails' =>
							array(
								'newuser' =>
									array(
										'subject' => 'Your username and password for Linnaeus NG', 
										'plain' =>
											'Below are your username and password for access to the Linnaeus NG administration:'.chr(10).
											'Username: %s'.chr(10).
											'Password: %s'.chr(10).chr(10).
											'You can access Linnaeus NG at:'.chr(10).
											'[[url]]',
										'html' =>
											'<html>Below are your username and password for access to the Linnaeus NG administration:<br />'.chr(10).
											'Username: %s<br />'.chr(10).
											'Password: %s<br />'.chr(10).
											'<br />'.chr(10).
											'You can access Linnaeus NG at:<br />'.chr(10).
											'<a href="[[url]]">[[url]]</a>',
									),
								'resetpassword' =>
									array(
										'subject' => 'Your new password for Linnaeus NG', 
										'plain' =>
											'Your password has been reset. Below is your new password for access to the Linnaeus NG administration:'.chr(10).
											'Password: %s'.chr(10).chr(10).
											'You can access Linnaeus NG at:'.chr(10).
											'[[url]]',
										'html' =>
											'<html>Your password has been reset. Below is your new password for access to the Linnaeus NG administration:<br />'.chr(10).
											'Password: %s<br />'.chr(10).
											'<br />'.chr(10).
											'You can access Linnaeus NG at:<br />'.chr(10).
											'<a href="[[url]]">[[url]]</a>',
									),
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
                        'allowedTags' => '<a><b><strong><u><i><em><p><span><h1><h2><h3><h4><h5><h6><ul><ol><li><table><th><tr><td>'
                    )
                ),
            'media' =>
                array(
                    'allowedFormats' => 
                        array(
                            array(
                                'mime' => 'image/png', 
                                'media_name' => 'PNG image', 
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
					'media_name' => 'PNG image', 
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
			'maxChoicesPerKeystep' => 6      
      	);

    }

    public function getControllerSettingsLiterature()
    {

        return array('allowedTags' => '<i><b>');

    }

    public function getControllerSettingsGlossary()
    {

		return array(
			'termsPerPage' => 20,
			'media' =>
                array(
                    'allowedFormats' => 
                        array(
                            array(
                                'mime' => 'image/png', 
                                'media_name' => 'PNG image', 
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
                                'mime' => 'application/zip', 
                                'media_name' => 'ZIP-file', 
                                'media_type' => 'archive', 
                                'maxSize' => 50000000
                            ),
                        ),
                    'defaultUploadMaxSize' => 5000000 // 5mb
                )
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
			'media' =>
                array(
				  'allowedFormats' => 
					array(
					  array(
						'mime' => 'image/png', 
						'media_name' => 'PNG image', 
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
					),
                    'defaultUploadMaxSize' => 5000000 // 5mb
                )			
		);

    }

    public function getControllerSettingsModule()
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
					  )
					),
				'defaultUploadMaxSize' => 5000000 // 5mb
			)
		);

    }

    public function getControllerSettingsMapKey()
    {

		return array(
			'speciesPerPage' => 20,
            'maxTypes' => 10,
			'urlToCheckConnectivity' =>'http://maps.google.com/maps/api/js?sensor=false',
			'SRID' => 4326
		);

    }

}

