<?php


	include_once(__DIR__."/configuration.php");

	class BaseClass {

		public $config;

		public function __construct() {

			$this->loadConfiguration();

			$this->setGeneralSettings();			

		}

		public function __destruct() {

		}
		
		private function loadConfiguration() {

			if (class_exists('configuration')) {

				$this->config = new configuration();

			} else {

				die(_('FATAL: cannot load configuration'));

			}

		}

		private function setGeneralSettings() {

			$this->generalSettings = $this->config->getGeneralSettings();

		}

	}


?>