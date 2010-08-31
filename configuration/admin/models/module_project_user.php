<?php

	include_once('model.php');

	class ModulesProjectsUsers extends Model {

		const tableBaseName = 'modules_projects_users';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>