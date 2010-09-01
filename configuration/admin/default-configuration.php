<?php

	class configuration {

		const smartyRoot = '@PATH.SMARTY@';

		public function getGeneralSettings() {

			return array(
				'debugMode' => false,
				'applicationName' => 'Linnaeus NG Administration',
				'applicationVersion' => '0.1',
				'maxSessionHistorySteps' => 10,
				'rootWebUrl' => '@URL.WEBROOT@'	,
				'paths' => array(
					'login' => '/views/users/login.php',
					'logout' => '/views/users/logout.php',
					'chooseProject' => '/views/users/choose_project.php',
					'notAuthorized' => '/views/users/not_authorized.php'
				)	
			);
			// hard paths are necessary as they are called from all over the place and used by functions in the base controller

			//dev.eti.uva.nl	"/linnaeus_ng/"
			//linnaeus			"/"
		}

		public function getDatabaseSettings() {

			return array(
				'host' => '@DB.HOST@',
				'user' => 'linnaeus_user',
				'password' => 'car0lu5',
				'database' => 'linnaeus_ng',
				'tablePrefix' => 'dev_',
				'characterSet' => 'utf8'
			);

		}

		public function getSmartySettings() {

			return array(
				'dir_template' => self::smartyRoot.'templates',
				'dir_compile' => self::smartyRoot.'templates_c',
				'dir_cache' => self::smartyRoot.'cache',
				'dir_config' => self::smartyRoot.'configs',
				'caching' => 1, // 1,
				'compile_check' => true
			);

		}

	}

