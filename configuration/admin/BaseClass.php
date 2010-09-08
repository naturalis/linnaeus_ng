<?php

	include_once(dirname(__FILE__)."/configuration.php");

	class BaseClass {

		public $config;
		public $generalSettings;

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


