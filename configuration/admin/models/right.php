<?php

	include_once('model.php');

	class Right extends Model {

		const tableBaseName = 'rights';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

?>