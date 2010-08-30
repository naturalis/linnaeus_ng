<?php

	include_once('model.php');

	class Module extends Model {

		const tableBaseName = 'modules';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>