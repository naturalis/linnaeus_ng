<?php

	include_once('model.php');

	class ProjectRoleUser extends Model {

		const tableBaseName = 'projects_roles_users';

		/**
		* Constructor, calls parent's constructor
		*
		* @access 	public
		*/
		public function __construct() {

			parent::__construct(self::tableBaseName);

		}

		/**
		* Destructor
		*
		* @access 	public
		*/
		public function __destruct() {

			parent::__destruct();

		}

	}

