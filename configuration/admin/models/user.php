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
				(!empty($data['username']) ? "username," : "" ).
				(!empty($data['password']) ? "password," : "" ).
				(!empty($data['first_name']) ? "first_name," : "" ).
				(!empty($data['last_name']) ? "last_name," : "" ).
				(!empty($data['gender']) ? "gender," : "" ).
				(!empty($data['email_address']) ? "email_address," : "" ).
				(!empty($data['active']) ? "active," : "" ).
				(!empty($data['last_login']) ? "last_login," : "" ).
				(!empty($data['logins']) ? "logins," : "" ).
				(!empty($data['password_changed']) ? "password_changed," : "" ).
				"created";

			$values = 
				(!empty($data['username']) ? "'".$data['username']."'," : "" ).
				(!empty($data['password']) ? "'".$data['password']."'," : "" ).
				(!empty($data['first_name']) ? "'".$data['first_name']."', " : "" ).
				(!empty($data['last_name']) ? "'".$data['last_name']."'," : "" ).
				(!empty($data['gender']) ? "'".$data['gender']."'," : "" ).
				(!empty($data['email_address']) ? "'".$data['email_address']."'," : "" ).
				(!empty($data['active']) ? $data['active']."," : "" ).
				(!empty($data['last_login']) ? $data['last_login']."," : "" ).
				(!empty($data['logins']) ? $data['logins']."," : "" ).
				(!empty($data['password_changed']) ? $data['password_changed']."," : "" ).
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
					(!empty($data['username']) ? "username = '".$data['username']."', " : "" ).
					(!empty($data['password']) ? "password = '".$data['password']."', " : "" ).
					(!empty($data['first_name']) ? "first_name = '".$data['first_name']."', " : "" ).
					(!empty($data['last_name']) ? "last_name = '".$data['last_name']."', " : "" ).
					(!empty($data['gender']) ? "gender = '".$data['gender']."', " : "" ).
					(!empty($data['email_address']) ? "email_address = '".$data['email_address']."', " : "" ).
					(!empty($data['active']) ? "active = ".$data['active'].", " : "" ).
					(!empty($data['last_login']) ? "last_login = ".$data['last_login'].", " : "" ).
					(!empty($data['logins']) ? "logins = ".$data['logins'].", " : "" ).
					(!empty($data['password_changed']) ? "password_changed = ".$data['password_changed'].", " : "" ).
					"id = id
				where id = ".$data['id'];

			//echo($query);

			if (!mysql_query($query)) {

				return mysql_error($this->databaseConnection);

			} else {

				return true;

			}
		}

	}

?>