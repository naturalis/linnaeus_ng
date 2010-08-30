<?php

	include_once('model.php');

	class Project extends Model {

		const tableBaseName = 'projects';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>