<?php

	include_once('model.php');

	class Role extends Model {

		const tableBaseName = 'roles';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

		public function insert($data = false) {
		}

		public function update($data = false) {
		}

	}

?>