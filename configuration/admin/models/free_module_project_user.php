<?php

	include_once('model.php');

	class FreemodulesProjectsUsers extends Model {

		const tableBaseName = 'free_modules_projects_users';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>