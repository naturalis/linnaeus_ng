<?php

	/*
	
	check out all tinyMCE options (+ instruct)

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
		* Edit taxon action
		*
		* @access	public
		*/
		public function editAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Edit taxon'));

$defaultPageName = 'main';

			$t = $this->models->Taxon->get(
				array(
					'id' => $this->requestData['id'],
					'project_id' => $this->getCurrentProjectId()
				)
			);
			
			$taxon = $t[0];

			$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));

			foreach((array)$lp as $key => $val) {

				$l = $this->models->Language->get($val['language_id']);

				$lp[$key]['language'] = $l['language'];

				$ct = $this->models->ContentTaxon->get(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $this->requestData['id'],
						'language_id' => $val['language_id'],
						'page' => $defaultPageName
					)
				);

				$content[$val['language_id']] = $ct[0];
				
				if ($val['active']=='y') $defaultLanguage = $val['language_id'];

			}

			$this->smarty->assign('taxon',$taxon);

			$this->smarty->assign('content',$content);

			$this->smarty->assign('languages',$lp);

			$this->smarty->assign('includeHtmlEditor',true);

			$this->smarty->assign('activeLanguage',!empty($this->requestData['lan']) ? $this->requestData['lan'] : $defaultLanguage);

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

				if ($val['def_language']=='1') $defaultLanguage = $val['language_id'];

			}

			$this->smarty->assign('languages',$lp);

			$this->smarty->assign('includeHtmlEditor',true);

			$this->smarty->assign('activeLanguage',!empty($this->requestData['lan']) ? $this->requestData['lan'] : $defaultLanguage);

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

			$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));

			foreach((array)$lp as $key => $val) {

				$l = $this->models->Language->get($val['language_id']);

				$lp[$key]['language'] = $l['language'];
			
			}

			foreach((array)$taxa as $key => $taxon) {

				foreach((array)$lp as $key2 => $val) {

					$ct = $this->models->ContentTaxon->get(
						array(
							'taxon_id' => $taxon['id'],
							'language_id' => $val['language_id'],
							'project_id' => $this->getCurrentProjectId()
						), 'length(content) as bytes'
					);
					
					$lp[$key2]['size'][$taxon['id']] = $ct[0]['bytes'] ? $ct[0]['bytes'] : 0;
	
				}
							
			}

			$this->smarty->assign('taxa',$taxa);

			$this->smarty->assign('languages',$lp);

			$this->printPage();

		}		

		private function ajaxActionSaveTaxon() {

			// new taxon
			if (empty($this->requestData['id'])) {

				$d = $this->models->Taxon->save(
					array(
						'id' => !empty($this->requestData['id']) ? $this->requestData['id'] : null,
						'project_id' => $this->getCurrentProjectId(),
						'taxon' => !empty($this->requestData['name']) ? $this->requestData['name'] : '?'
					)
				);

				$taxonId = $this->models->Taxon->getNewId();

			}
			// existing taxon 
			else {

				$d = true;

				$taxonId = $this->requestData['id'];

			}

			// save of new taxon succeded, or existing taxon
			if ($d) {

				// must have a language
				if (!empty($this->requestData['language'])) {

					// must have a page name
					if (!empty($this->requestData['page'])) {

						// see if such content already exists
						$ct = $this->models->ContentTaxon->get(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'taxon_id' => $taxonId,
								'language_id' => $this->requestData['language'],
								'page' => 'main'
							)
						);
						
						$id = count((array)$ct)!=0 ? $ct[0]['id'] : null;

						// save content
						$d = $this->models->ContentTaxon->save(
								array(
									'id' => $id,
									'project_id' => $this->getCurrentProjectId(),
									'taxon_id' => $taxonId,
									'language_id' => $this->requestData['language'],
									'content' => !empty($this->requestData['content']) ? $this->requestData['content'] : '',
									'content_name' => !empty($this->requestData['name']) ? $this->requestData['name'] : '?',
									'page' => 'main'
								)
							);
						
						if ($d) {

							$lp = $this->models->LanguageProject->get(
								array(
									'project_id' => $this->getCurrentProjectId(),
									'def_language' => '1'									
								)
							);
							
							$defaultLanguage = isset($lp[0]['language_id']) ? $lp[0]['language_id'] : $this->requestData['language'];

							$ct = $this->models->ContentTaxon->get(
								array(
									'project_id' => $this->getCurrentProjectId(),
									'taxon_id' => $taxonId,
									'language_id' => $defaultLanguage
								)
							);

							$this->models->Taxon->save(
								array(
									'id' => $taxonId ,
									'project_id' => $this->getCurrentProjectId(),
									'taxon' => $ct[0]['content_name']
								)
							);

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
			
				$this->addError(_('Could not save taxon'));
		
			}
	
		}

		private function ajaxActionGetTaxon() {

			if (empty($this->requestData['id']) ||
				empty($this->requestData['language'])) {

				return;

			} else {

				$ct = $this->models->ContentTaxon->get(
					array(
						'taxon_id' => $this->requestData['id'],
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $this->requestData['language'],
						'page' => 'main'
					)
				);

				if (empty($ct[0])) {

					$c = array(
							'project_id' => $this->requestData['id'],
							'taxon_id' => $this->getCurrentProjectId(),
							'language_id' => $this->requestData['language'],
							'page' => 'main',
							'content' => null,
							'content_name' => null
						);

				} else { 

					$c = $ct[0];

				}

				$this->smarty->assign('returnText',json_encode($c));

			}

		}

		private function ajaxActionDeleteTaxon() {

			if (empty($this->requestData['id']) ||
				empty($this->requestData['language'])) {

				return;

			} else {

				$ct = $this->models->ContentTaxon->delete(
					array(
						'taxon_id' => $this->requestData['id'],
						'project_id' => $this->getCurrentProjectId(),
						'page' => 'main'
					)
				);

				$ct = $this->models->Taxon->delete(
					array(
						'id' => $this->requestData['id'],
						'project_id' => $this->getCurrentProjectId()
					)
				);

			}

		}

		/**
		* AJAX interface for this class
		*
		* @access	public
		*/
		public function ajaxInterfaceAction() {

			if ($this->requestData['action']=='save_taxon') {

				$this->ajaxActionSaveTaxon();

			} else
			if ($this->requestData['action']=='get_taxon') {

				$this->ajaxActionGetTaxon();

			} else
			if ($this->requestData['action']=='delete_taxon') {

				$this->ajaxActionDeleteTaxon();

			}

			$this->printPage();

		}

	}






















