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

			$this->smarty->assign('includeHtmlEditor',true);

			$this->smarty->assign('activeLanguage',11);

			$this->printPage();

		}
		
		/**
		*  List of existing taxa
		*
		* @access	public
		*/
		public function listAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Taxon list'));

			$taxa = $this->models->Taxon->get(array('project_id' => $this->getCurrentProjectId()),'*','taxon');

			$this->smarty->assign('taxa',$taxa);

			$this->printPage();

		}		

		
		/**
		* AJAX interface for this class
		*
		* @access	public
		*/
		public function ajaxInterfaceAction() {
// check if language exists in project

			if ($this->requestData['action']=='save_taxon') {

				if (empty($this->requestData['name']) && empty($this->requestData['content'])) {

					return;

				} else {

					$d = $this->models->Taxon->save(
						array(
							'id' => !empty($this->requestData['id']) ? $this->requestData['id'] : null,
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => !empty($this->requestData['name']) ? $this->requestData['name'] : '?'
						)
					);

				if ($d) {
				
					if (empty($this->requestData['id'])) {
	
						$taxonId = $this->models->Taxon->getNewId();

					} else {

						$taxonId = $this->requestData['id'];

					}

					if (!empty($this->requestData['content'])) {
	
						if (!empty($this->requestData['language'])) {

							if (!empty($this->requestData['page'])) {

								$ct = $this->models->ContentTaxon->get(
									array(
										'project_id' => $this->getCurrentProjectId(),
										'taxon_id' => $taxonId,
										'language_id' => $this->requestData['language']
									)
								);
								
								$id = count((array)$ct)!=0 ? $ct[0]['id'] : null;

								$d = $this->models->ContentTaxon->save(
										array(
											'id' => $id,
											'project_id' => $this->getCurrentProjectId(),
											'taxon_id' => $taxonId,
											'language_id' => $this->requestData['language'],
											'content' => $this->requestData['content'],
											'page_name' => $this->requestData['page']
										)
									);
								
								if ($d) {
								
									$this->smarty->assign('returnText','id='.$taxonId);
									
								} else {
								
									$this->addError(_('Could not save taxon content'));
	
								}

							} else {
	
								$this->addError(_('No page name specified'));
	
							}				

						} else {

							$this->addError(_('No language specified'));

						}				
					
										
					} else {
					
						$this->smarty->assign('returnText','id='.$taxonId);
					
					}
				
				} else {
				
					$this->addError(_('Could not save taxon'));
			
				}

				}
			}

			$this->printPage();

		}

	}






















