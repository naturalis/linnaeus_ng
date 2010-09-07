<?php

	/*
	
		- replace hard coded role_id's

	*/

	include_once('Controller.php');

	class SpeciesController extends Controller {

		public $usedModels = array('taxon','content_taxon','language_project','language');

		public $controllerPublicName = 'Species module';

		/**
		* Constructor, calls parent's constructor
		*
		* @access 	public
		*/
		public function __construct() {

			parent::__construct();

		}

		/**
		* Destroys!
		*
		* @access 	public
		*/
		public function __destruct() {

			parent::__destruct();

		}

		/**
		* Index of the species module
		*
		* @access	public
		*/
		public function indexAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Species module overview'));

			$this->printPage();

		}
		
		/**
		* Add taxon action
		*
		* @access	public
		*/
		public function addAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Add a new taxon'));

			$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));

			foreach((array)$lp as $key => $val) {

				$l = $this->models->Language->get($val['language_id']);

				$lp[$key]['language'] = $l['language'];
			
			}

			$this->smarty->assign('languages',$lp);

			$this->printPage();

		}		

		
		/**
		* AJAX interface for this class
		*
		* @access	public
		*/
		public function ajaxInterfaceAction() {

		}

	}

