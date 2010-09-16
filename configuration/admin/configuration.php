<?php

//	error_reporting(E_ALL | E_STRICT); 
	error_reporting(E_ALL);

	// YES this will go (eventually)
	function q($v,$d=false,$p=true) {
		if ($p) echo '<pre>';
		var_dump($v);
		if ($d) die();
	}

	class configuration {

		const applicationRootDir = 'C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/';

		public function getGeneralSettings() {

			return array(
				'debugMode' => false,
				'applicationName' => 'Linnaeus NG Administration',
				'applicationVersion' => '0.1',
				'maxSessionHistorySteps' => 10,
				'heartbeatFrequency' => 60000, // milliseconds
				'autosaveFrequency' => 300000, // milliseconds
				'rootWebUrl' => '/'	,
				'paths' => array(
					'login' => '/views/users/login.php',
					'logout' => '/views/users/logout.php',
					'chooseProject' => '/views/users/choose_project.php',
					'notAuthorized' => '/views/users/not_authorized.php'
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
					'imageDirProject' => self::applicationRootDir.'www/admin/images/project',
					'imageDirUpload' => self::applicationRootDir.'www/admin/images/upload',
				)
			);

		}

		public function getDatabaseSettings() {

			return array(
				'host' => '127.0.0.1',
				'user' => 'linnaeus_user',
				'password' => 'car0lu5',
				'database' => 'linnaeus_ng',
				'tablePrefix' => 'dev_',
				'characterSet' => 'utf8'
			);

		}

		public function getSmartySettings() {

			return array(
				'dir_template' => self::applicationRootDir.'www/admin/templates/templates',
				'dir_compile' => self::applicationRootDir.'www/admin/templates/templates_c',
				'dir_cache' => self::applicationRootDir.'www/admin/templates/cache',
				'dir_config' => self::applicationRootDir.'www/admin/templates/configs',
				'caching' => 1, // 1,
				'compile_check' => true
			);

		}

	}

