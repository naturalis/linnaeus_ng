<?php

	include_once('model.php');

	class FreeModuleProject extends Model {

		const tableBaseName = 'free_modules_projects';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>