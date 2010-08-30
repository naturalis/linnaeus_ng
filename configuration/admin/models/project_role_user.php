<?php

	include_once('model.php');

	class ProjectRoleUser extends Model {

		const tableBaseName = 'projects_roles_users';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>