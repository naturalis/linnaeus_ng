<?php

	include_once(__DIR__."/../BaseClass.php");

	abstract class Model extends BaseClass {

		private $databaseSettings;
		public $databaseConnection;
		private $data;
		public $tableName;
		private $id;
		public $newId;

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

			$this->getTableColumnInfo();

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

		private function getTableColumnInfo() {

			$r = mysql_query('select * from '.$this->tableName.' limit 1');

			$i = 0;

			while ($i < mysql_num_fields($r)) {

				$info = mysql_fetch_field($r, $i);

				if ($info) {
				
					$this->columns[$info->name] =
					array(
						'blob' => $info->blob,
						'max_length' => $info->max_length,
						'multiple_key' => $info->multiple_key,
						'name' => $info->name,
						'not_null' => $info->not_null,
						'numeric' => $info->numeric,
						'primary_key' => $info->primary_key,
						'table' => $info->table,
						'type' => $info->type,
						'unique_key' => $info->unique_key,
						'unsigned' => $info->unsigned,
						'zerofill' => $info->zerofill
					);

					$i++;
				}

			}

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

				return $this->insert($data);

			} else {

				return $this->update($data);

			}

		}

		public function xupdate($data) {


			foreach((array)$data as $key => $val) {

				$data[$key] = mysql_real_escape_string($val);

			}

			$query = "update ".$this->tableName." set ";
			
			foreach((array)$data as $key => $val) {

				$d = $this->columns[$key];

				if ($d && !empty($val)) {

					if ($d['numeric']==1) {

						$query .= " ".$key." = ".$val.", ";

					} elseif ($d['type']=='datetime') {
					
						$query .= " ".$key." = ".$val.", ";

					} else {

						$query .= " ".$key." = '".$val."', ";

					}

				}
			
			}
			
			$query .= " id = id where id = ".$data['id'];

			//echo '<pre>'.$query;

			if (!mysql_query($query)) {

				return mysql_error($this->databaseConnection);

			} else {

				return true;

			}

		}



		public function delete($id = false) {

			if (!$id) return;

			if (is_array($id)) {

				$query = 'delete from '.$this->tableName.' where 1=1 ';

				foreach((array)$id as $col => $val) {

					if (strpos($col,' ')===false) {

						$operator = '=';

					} else {

						$operator = trim(substr($col,strpos($col,' ')));

						$col = trim(substr($col,0,strpos($col,' ')));

					}

					$query .= ' and '.$col." ".$operator." '". mysql_real_escape_string($val)."'";

					//echo $query.'<br />';

				}

				$result = mysql_query($query);


			} elseif ($id+0 == $id) {

				$query = "delete from ".$this->tableName." where id = ".($id ? $id : $this->id)." limit 1";
	
				//echo($query);
	
				$result = mysql_query($query);

			} else {

				return;
		
			}

			if (!$result) {

				return mysql_error($this->databaseConnection);

			} else {

				return true;

			}

		}

		private function set($id = false, $cols = false, $order = false ) {

			/*

				function can take a single $id to find the corresponding row
				or an array of column/value-pairs (array('last_name' => 'turing' ))
				standard operator is '=' but it is possible to tag another operator 
				after the column-value (array('last_name !=' => 'gates' ))
				
				$cols can hold a string that replaces the defualt * in 'select * from...'

			*/

			if (!$id) return;

			if (is_array($id)) {
			
				$query = 'select '.( !$cols ? '*' : $cols).' from '.$this->tableName.' where 1=1 ';

				foreach((array)$id as $col => $val) {

					if (strpos($col,' ')===false) {

						$operator = '=';

					} else {

						$operator = trim(substr($col,strpos($col,' ')));

						$col = trim(substr($col,0,strpos($col,' ')));

					}

					$query .= ' and '.$col." ".$operator." '". mysql_real_escape_string($val)."'";

				}

				$query .= $order ? " ".$order : '';

				//echo $query.'<br />';

				$set = mysql_query($query);

				while ($row = mysql_fetch_assoc($set)) {

					$this->data[] = $row;

				}

			} elseif ($id+0 == $id) {
			
				// is_int won't work here, as mysql returns everything as a string

				$query =
					'select '.( !$cols ? '*' : $cols).
					' from '.$this->tableName.
					' where id ='.mysql_real_escape_string($id).' limit 1'.
					($query .= $order ? ' '.$order : '');

				$this->data = mysql_fetch_assoc(mysql_query($query));

			} else {

				return;

			}

		}

		public function get($id = false, $cols = false, $order = false ) {
		
			unset($this->data);

			$this->set($id ? $id : $this->id, $cols, $order);

			return $this->data;

		}

		public function getNewId() {

			return $this->newId;

		}

		
	}


?>