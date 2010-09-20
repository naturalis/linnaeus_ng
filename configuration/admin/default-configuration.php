<?php

class configuration
{
    
    const applicationRootDir = '@PATH.APP_ROOT@';



    public function getGeneralSettings ()
    {
        
        return array(
            'debugMode' => false, 
            'applicationName' => 'Linnaeus NG Administration', 
            'applicationVersion' => '0.1', 
            'maxSessionHistorySteps' => 10, 
            'heartbeatFrequency' => 60000,  // milliseconds
            'autosaveFrequency' => 300000,  // milliseconds
            'rootWebUrl' => '@URL.WEBROOT@', 
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
                'defaultUploadDir' => self::applicationRootDir . 'www/admin/uploads/', 
                'imageDirProject' => self::applicationRootDir . 'www/admin/images/project', 
                'imageDirUpload' => self::applicationRootDir . 'www/admin/images/upload'
            ), 
            'maxSubPages' => 10
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
            'dir_template' => self::applicationRootDir . 'www/admin/templates/templates', 
            'dir_compile' => self::applicationRootDir . 'www/admin/templates/templates_c', 
            'dir_cache' => self::applicationRootDir . 'www/admin/templates/cache', 
            'dir_config' => self::applicationRootDir . 'www/admin/templates/configs', 
            'caching' => 1,  // 1,
            'compile_check' => true
        );
    
    }

}

