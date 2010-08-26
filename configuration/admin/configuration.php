<?php

	class configuration {

		const smartyRoot = 'C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/admin/templates/';

		public function getGeneralSettings() {

			return array(
				'debugMode' => false,
				'applicationName' => 'Linnaeus NG Administration',
				'applicationVersion' => '0.1',
				'maxSessionHistorySteps' => 10,
				'webroot' => ''
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
				'dir_template' => self::smartyRoot.'templates',
				'dir_compile' => self::smartyRoot.'templates_c',
				'dir_cache' => self::smartyRoot.'cache',
				'dir_config' => self::smartyRoot.'configs',
				'caching' => 1, // 1,
				'compile_check' => true
			);

		}

	}

?>