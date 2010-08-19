<?php


	include_once(__DIR__."/configuration.php");

	class BaseClass {

		public $config = false;

		public function __construct() {

			$this->loadConfiguration();
			
			$this->startSession();

		}

		public function __destruct() {

		}
		
		private function startSession() {
		
			session_start();
		
		}

		private function loadConfiguration() {

			if (class_exists('configuration')) {

				$this->config = new configuration();

			} else {

				die(_('FATAL: cannot load configuration'));

			}

		}

	}


?>