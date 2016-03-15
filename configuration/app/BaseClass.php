<?php

if (file_exists(dirname(__FILE__) . "/configuration.php"))
	include_once (dirname(__FILE__) . "/configuration.php");

if (file_exists(dirname(__FILE__) . "/constants.php"))
	include_once (dirname(__FILE__) . "/constants.php");


class BaseClass
{

    public $config;
    public $customConfig;
    public $generalSettings;

    public function __construct ()
    {

        $this->loadConfiguration();

        $this->setGeneralSettings();

    }


    public function __destruct ()
    {

    }


    private function loadConfiguration ()
    {

        if (class_exists('configuration')) {

            $this->config = new configuration();

        } else {
<<<<<<< HEAD
            
            die(_('Cannot load admin configuration file. Make sure the file config.php is present in both 
            	configuration/admin and configuration/app. In both directories, the template file 
=======

            die(_('Cannot load app configuration file. Make sure the file config.php is present in both
            	configuration/admin and configuration/app. In both directories, the template file
>>>>>>> development-WEG
            	default-config.php can be adapted.'));

        }

<<<<<<< HEAD
        if (class_exists('customConfiguration'))
		{
            $this->customConfig=new customConfiguration();
        }    
    
=======
>>>>>>> development-WEG
    }

    private function setGeneralSettings ()
    {

        $this->generalSettings = $this->config->getGeneralSettings();

    }

}


