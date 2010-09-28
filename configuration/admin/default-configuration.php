<?php

class configuration
{
    
	private $_appFileRoot;

    public function __construct ()
    {

		$d = $this->getGeneralSettings();
		$d['app']['pathName'];
		$this->_appFileRoot = dirname(__FILE__);
		$this->_appFileRoot = substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1));

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
            'uploading' => array(
                'defaultUploadFilemask' => array(
                    'image/jpg', 
                    'image/jpeg', 
                    'image/png'
                ), 
                'defaultUploadMaxSize' => 1000000
            ), 
            'directories' => array(
                'imageDirProject' => $this->_appFileRoot . 'www/admin/images/project', 
                'imageDirUpload' => $this->_appFileRoot . 'www/admin/images/upload'
            ), 
            'maxSubPages' => 10,
			'login-cookie' => array(
				'name' => 'linnaeus-login',
				'lifetime' => 30, // days
			)
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

