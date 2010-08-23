<?php

	include_once(__DIR__."/../BaseClass.php");

	abstract class Model extends BaseClass {

		private $databaseSettings;
		private $databaseConnection;
		private $data;
		public $tableName;
		private $id;

		abstract function update($data=false);
		abstract function insert($data=false);

		public function __construct($tableBaseName = false) {

			parent::__construct();

			$this->connectToDatabase() or die(_('FATAL: cannot connect to database').' ('.mysql_error().')');

			if (!$tableBaseName) {

				die(_('FATAL: no table basename defined'));

			} else {

				$this->tableName = $this->databaseSettings['tablePrefix'] . $tableBaseName;

			}

		}

		public function __destruct() {

			if ($this->databaseConnection) {

				$this->disconnectFromDatabase();

			}

			parent::__destruct();
		}
		
		private function connectToDatabase() {

			$this->databaseSettings = $this->config->getDatabaseSettings();

			$this->databaseConnection =
				mysql_connect(
					$this->databaseSettings['host'],
					$this->databaseSettings['user'],
					$this->databaseSettings['password']
				);

			if (!$this->databaseConnection) return false;

			mysql_select_db($this->databaseSettings['database'],$this->databaseConnection);// or return false;
			
			if ($this->databaseSettings['characterSet']) {
			
				mysql_query('SET NAMES '.$this->databaseSettings['characterSet'],$this->databaseConnection);
				mysql_query('SET CHARACTER SET '.$this->databaseSettings['characterSet'],$this->databaseConnection);

			}
			
			return true;

		}
		
		private function disconnectFromDatabase() {

			@mysql_close($this->databaseConnection);

		}
		
		private function hasId($data) {

			foreach((array)$data as $col => $val) {

				if ($col=='id') {

					$this->id = $val;

					return true;

				}

			}
			
			return false;

		}

		public function save($data) {
			
			if (!$this->hasId($data)) return false;

			$this->get();
			
			if (!$this->data) {

				$this->insert($data);

			} else {

				$this->update($data);

			}

		}

		public function del() {
		}

		private function set($id = false) {

			if (!$id) return;

			if (is_array($id)) {
			
				$query = 'select * from '.$this->tableName.' where 1=1 ';

				foreach((array)$id as $col => $val) {

					$query .= ' and '.$col." = '". mysql_real_escape_string($val)."'";

				}

				$set = mysql_query($query);

				while ($row = mysql_fetch_assoc($set)) {

					$this->data[] = $row;

				}

			} elseif ($id+0 == $id) {
			
				// is_int won't work here, as mysql returns everything as a string

				$query = 'select * from '.$this->tableName.' where id ='.mysql_real_escape_string($id).' limit 1';

				$this->data = mysql_fetch_assoc(mysql_query($query));

			} else {

				return;

			}

		}

		public function get($id = false) {
		
			unset($this->data);

			$this->set($id ? $id : $this->id);

			return $this->data;

		}

		
	}


?>