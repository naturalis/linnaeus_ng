<?php

	include_once('model.php');

	class ProjectRightUser extends Model {

		const tableBaseName = 'projects_rights_users';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

		public function insert($data = false) {

			foreach((array)$data as $key => $val) {

				$data[$key] = mysql_real_escape_string($val);

			}


			$fields = 
				(isset($data['project_id']) ? "project_id," : "" ).
				(isset($data['user_id']) ? "user_id," : "" ).
				(isset($data['right_id']) ? "right_id," : "" );

			$values = 
				(isset($data['project_id']) ? $data['project_id']."," : "" ).
				(isset($data['user_id']) ? $data['user_id']."," : "" ).
				(isset($data['right_id']) ? $data['right_id']."," : "" );

			$query =
				"insert into ".$this->tableName." (".trim($fields,',').") values (".trim($values,',').")";

			if (!mysql_query($query)) {

				return mysql_error($this->databaseConnection);

			} else {

				$this->newId = mysql_insert_id($this->databaseConnection);

				return true;

			}
			
		}

		public function update($data = false) {
		}

	}

?>




