<?php

if (file_exists(__DIR__ . "/constants.php"))
    include_once (__DIR__ . "/constants.php");
    
if (file_exists(__DIR__ . "/configuration.php"))
	include_once (__DIR__ . "/configuration.php");

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

            die(_('Cannot load admin configuration file. Make sure the file config.php is present in both
            	configuration/admin and configuration/app. In both directories, the template file
            	default-config.php can be adapted.'));

        }

        if (class_exists('customConfiguration'))
		{
            $this->customConfig=new customConfiguration();
        }    

    }

    private function setGeneralSettings ()
    {

        $this->generalSettings = $this->config->getGeneralSettings();

    }

}


