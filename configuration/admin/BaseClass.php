<?php

class BaseClass
{
    
    public $config;
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

        if (file_exists(dirname(__FILE__) . "/configuration.php") && class_exists('configuration')) {
            
			   include_once (dirname(__FILE__) . "/configuration.php");

            $this->config = new configuration();
        
        } else {
            
            die(_('Cannot load configuration file. Make sure the file config.php is present in both 
            	configuration/admin and configuration/app. In both
            	directories, the template file default-config.php can be adapted.'));
        
        }
    
    }

    private function setGeneralSettings ()
    {
        
        $this->generalSettings = $this->config->getGeneralSettings();
    
    }

}


