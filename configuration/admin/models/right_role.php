<?php

	include_once('model.php');

	class RightRole extends Model {

		const tableBaseName = 'rights_roles';

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