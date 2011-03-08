<?php
error_reporting(E_ALL);//    error_reporting(E_ALL | E_STRICT); 
class configuration
{

    private $_appFileRoot;

    public function __construct ()
    {

        $d = $this->getGeneralSettings();
        $this->_appFileRoot = dirname(__FILE__);
        $this->_appFileRoot = str_replace('\\','/',
            substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));

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
            'dir_template' => $this->_appFileRoot . 'www/app/templates/templates', 
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
			),
			'defaultController' => 'linnaeus',
			'urlNoProjectId' => '/app/views/linnaeus/set_project.php',
			'imageRootUrlOverride' => '../../../admin/media/project/',
            'maxSessionHistorySteps' => 10, 
            'controllerIndexNameExtension' => '-index.php', 
            'paths' => array(
            ), 
            'directories' => array(
				'locale' => $this->_appFileRoot . 'configuration/locale',
				'log' => $this->_appFileRoot . 'log',
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
			'mime-types' => array(
				'image/png' => 'image', 
				'image/jpg' => 'image', 
				'image/jpeg' => 'image', 
				'image/gif' => 'image', 
				'video/h264' => 'video', 
				'video/quicktime' => 'video', 
				'audio/mpeg' => 'sound', 
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
			)
		);

    }

}


// YES this will go (eventually)
function q ($v, $d = false)
{
	echo '<pre>';
    var_dump($v);
    if ($d)
        die();
}

