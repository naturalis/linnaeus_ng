<?php

	include_once('model.php');

	class User extends Model {

		const tableBaseName = 'users';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}
		
		public function sanatizeData($data) {

			if (isset($data['email_address'])) {

				$data['email_address'] = strtolower($data['email_address']);

			}

			foreach((array)$data as $key => $val) {

				$data[$key] = trim($val);

			}

			return $data;

		}

		public function insert($data = false) {

			$data = $this->sanatizeData($data);

			foreach((array)$data as $key => $val) {

				$data[$key] = mysql_real_escape_string($val);

			}

			$fields = 
				(isset($data['username']) ? "username," : "" ).
				(isset($data['password']) ? "password," : "" ).
				(isset($data['first_name']) ? "first_name," : "" ).
				(isset($data['last_name']) ? "last_name," : "" ).
				(isset($data['gender']) ? "gender," : "" ).
				(isset($data['email_address']) ? "email_address," : "" ).
				(isset($data['active']) ? "active," : "" ).
				(isset($data['last_login']) ? "last_login," : "" ).
				(isset($data['logins']) ? "logins," : "" ).
				(isset($data['password_changed']) ? "password_changed," : "" ).
				"created";

			$values = 
				(isset($data['username']) ? "'".$data['username']."'," : "" ).
				(isset($data['password']) ? "'".$data['password']."'," : "" ).
				(isset($data['first_name']) ? "'".$data['first_name']."', " : "" ).
				(isset($data['last_name']) ? "'".$data['last_name']."'," : "" ).
				(isset($data['gender']) ? "'".$data['gender']."'," : "" ).
				(isset($data['email_address']) ? "'".$data['email_address']."'," : "" ).
				(isset($data['active']) ? $data['active']."," : "" ).
				(isset($data['last_login']) ? $data['last_login']."," : "" ).
				(isset($data['logins']) ? $data['logins']."," : "" ).
				(isset($data['password_changed']) ? $data['password_changed']."," : "" ).
				"now()";

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

			$data = $this->sanatizeData($data);

			foreach((array)$data as $key => $val) {

				$data[$key] = mysql_real_escape_string($val);

			}

			$query =
				"update ".$this->tableName." set ".
					(isset($data['username']) ? "username = '".$data['username']."', " : "" ).
					(isset($data['password']) ? "password = '".$data['password']."', " : "" ).
					(isset($data['first_name']) ? "first_name = '".$data['first_name']."', " : "" ).
					(isset($data['last_name']) ? "last_name = '".$data['last_name']."', " : "" ).
					(isset($data['gender']) ? "gender = '".$data['gender']."', " : "" ).
					(isset($data['email_address']) ? "email_address = '".$data['email_address']."', " : "" ).
					(isset($data['active']) ? "active = ".$data['active'].", " : "" ).
					(isset($data['last_login']) ? "last_login = ".$data['last_login'].", " : "" ).
					(isset($data['logins']) ? "logins = ".$data['logins'].", " : "" ).
					(isset($data['password_changed']) ? "password_changed = ".$data['password_changed'].", " : "" ).
					"id = id
				where id = ".$data['id'];

			if (!mysql_query($query)) {

				return mysql_error($this->databaseConnection);

			} else {

				return true;

			}
		}

	}

?>




