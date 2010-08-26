<?php

	include_once(dirname(__FILE__)."/../BaseClass.php");

	class dbConnectHelper extends BaseClass {

		private $databaseSettings;
		private $databaseConnection;

		public function __construct() {

		}

		public function __destruct() {

			if ($this->databaseConnection) {

				$this->disconnectFromDatabase();

			}

		}
		
		public function connectToDatabase() {

			$this->databaseSettings = $this->config->getDatabaseSettings();

			$this->databaseConnection =
				mysql_connect(
					$this->databaseSettings['host'],
					$this->databaseSettings['user'],
					$this->databaseSettings['password']
				) or die(_('FATAL: cannot connect to database').' ('.mysql_error().')');

			mysql_select_db($this->databaseSettings['database'],$this->databaseConnection) or 
				die(_('FATAL: cannot select database').' ('.mysql_error().')');
			
			if ($this->databaseSettings['characterSet']) {
			
				mysql_query('SET NAMES '.$this->databaseSettings['characterSet'],$this->databaseConnection);
				mysql_query('SET CHARACTER SET '.$this->databaseSettings['characterSet'],$this->databaseConnection);

			}

		}
		
		private function disconnectFromDatabase() {
		
			mysql_close($this->databaseConnection);

		}

	}


?>