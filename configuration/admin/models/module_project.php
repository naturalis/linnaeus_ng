<?php

	include_once('model.php');

	class ModuleProject extends Model {

		const tableBaseName = 'modules_projects';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>