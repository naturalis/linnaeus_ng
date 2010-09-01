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

	}

