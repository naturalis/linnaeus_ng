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
		}

		public function update($data = false) {
		}

	}

?>




