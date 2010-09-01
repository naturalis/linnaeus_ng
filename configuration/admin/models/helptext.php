<?php

	include_once('model.php');

	class Helptext extends Model {

		const tableBaseName = 'helptexts';

		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		function __destruct() {

			parent::__destruct();

		}

	}

