<?php
/**
 *  Default configuration, which can be extended by the custom configuration.
 */

ini_set('memory_limit', '512M');
require_once 'constants.php';

if(file_exists( __DIR__ . '/custom-configuration.php')) include_once( __DIR__ . '/custom-configuration.php' );
class configuration
{
    private $_appFileRoot;

    public function __construct ()
    {
        // get All GeneralSettings from the config file
        $d = $this->getGeneralSettings();
        $d['app']['pathName'];
        $this->_appFileRoot = __DIR__;
        $this->_appFileRoot = str_replace('\\','/',
            substr_replace($this->_appFileRoot,'', -1 * (strlen($d['app']['pathName']) + strlen('configuration')+1)));
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

    /*
     * Assign (sub)domain names to project ids; required to generate sitemaps!
     * Format is [project id => domain name]
     */
    public function getProjectsDomains ()
    {
        return [
            // 1 => 'https://example.linnaeus.naturalis.nl',
        ];
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
            'app' => array(
				'applicationFileRoot' => $this->_appFileRoot,
                'name' => 'Linnaeus NG Administration',
                'version' => '@APP.VERSION@',
                'versionTimestamp' => '@TIMESTAMP@',
                'pathName' => 'admin',
            ),
			'serverTimeZone' => 'Europe/Amsterdam',
            'paths' => array(
                'login' => '/views/users/login.php',
                'logout' => '/views/users/logout.php',
                'chooseProject' => '/views/users/choose_project.php',
                'projectIndex' => '/views/utilities/admin_index.php',
                'notAuthorized' => '/views/utilities/not_authorized.php',
                'moduleNotPresent' => '/views/utilities/module_not_present.php',
                'mediaBasePath' => '../../../shared/media/project',
            ),
            'directories' => array(
                'mediaDirProject' => $this->_appFileRoot . 'www/shared/media/project',
				'log' => $this->_appFileRoot . 'log',
                'runtimeStyleRoot' => $this->_appFileRoot . 'www/app/style',
            ),
            'login-cookie' => array(
                'name' => 'linnaeus-login',
                'lifetime' => 30, // days
            ),
	        //'uiLanguages' => array(LANGUAGECODE_ENGLISH,LANGUAGECODE_DUTCH),
	        'uiLanguages' => array(LANGUAGECODE_ENGLISH),
			'soundPlayerPath' => '../../media/system/',
			'soundPlayerName' => 'player_mp3.swf',
			'useJavascriptLinks' => false,
			'projectCssTemplateFile' => 'project-template.css',
        	'appNameFrontEnd' => 'app',
            'pushUrl' => 'http://linnaeus.naturalis.nl/admin/server_csv.php'
        );
    }

}