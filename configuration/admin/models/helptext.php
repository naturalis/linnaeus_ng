<?php

	include_once('model.php');

	class Helptext extends Model {

		const tableBaseName = 'helptexts';

		/**
		* Constructor, calls parent's constructor
		*
		* @access 	public
		*/
		function __construct() {

			parent::__construct(self::tableBaseName);

		}

		/**
		* Destructor
		*
		* @access 	public
		*/
		function __destruct() {

			parent::__destruct();

		}

	}

