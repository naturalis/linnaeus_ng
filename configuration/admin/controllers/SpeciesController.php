<?php

	/*
	
	check out all tinyMCE options (+ instruct)
	max pages hardcoded into page.tpl
	page 'main' is hardcoded
	delete page must also delete taxon_content!

	*/

	include_once('Controller.php');

	class SpeciesController extends Controller {

		public $usedModels = array('taxon','content_taxon','language_project','language','taxon_page','taxon_page_title');

		public $controllerPublicName = 'Species module';

		/**
		* Constructor, calls parent's constructor
		*
		* @access 	public
		*/
		public function __construct() {

			parent::__construct();
			
			$this->createTaxonPage('Main');

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
		
		private function createTaxonPage($name) {

			$this->models->TaxonPage->save(
				array(
					'id' => null,
					'page' => $name,
					'project_id' => $this->getCurrentProjectId()
				)
			);
	
		}
	
		public function pageAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Subpages'));

			// adding a new page
			if (!empty($this->requestData['new_page']) && !$this->isFormResubmit()) {

				$tp = $this->models->TaxonPage->save(
					array(
						'id' => null,
						'project_id' => $this->getCurrentProjectId(),
						'page' => $this->requestData['new_page']
					)
				);
				
				if ($tp!==true) {

					$this->addError(_('Could not save page name.'));
					$this->addError('('.$tp.')');

				}

			}
			
			$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));
	
			foreach((array)$lp as $key => $val) {

				$l = $this->models->Language->get($val['language_id']);

				$lp[$key]['language'] = $l['language'];

				if ($val['def_language']==1) $defaultLanguage = $val['language_id'];

			}

			$pages = $this->models->TaxonPage->get(array('project_id' => $this->getCurrentProjectId()));

			foreach((array)$pages as $key => $page) {

				foreach((array)$lp as $key2 => $language) {

					$tpt = $this->models->TaxonPageTitle->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'page_id' => $page['id'],
							'language_id' => $language['language_id']
						)
					);
					
					$pages[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];

				}

			}

			$this->smarty->assign('languages',$lp);

			$this->smarty->assign('pages',$pages);

			$this->smarty->assign('defaultLanguage',$defaultLanguage);

			$this->printPage();

		}
		
		

		/**
		* Edit taxon action
		*
		* @access	public
		*/
		public function editAction() {

			$this->checkAuthorisation();

			$defaultPageName = 'main';
			
			if (!empty($this->requestData['id'])) {

				$t = $this->models->Taxon->get(
					array(
						'id' => $this->requestData['id'],
						'project_id' => $this->getCurrentProjectId()
					)
				);
				
				$taxon = $t[0];

				$this->setPageName(_('Editing').' "'.$taxon['taxon'].'"');

			} else {

				$this->setPageName(_('Adding new taxon'));

			}

			$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));
	
			foreach((array)$lp as $key => $val) {

				$l = $this->models->Language->get($val['language_id']);

				$lp[$key]['language'] = $l['language'];

				if (!empty($this->requestData['id'])) {

					$ct = $this->models->ContentTaxon->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'taxon_id' => $this->requestData['id'],
							'language_id' => $val['language_id'],
							'page' => $defaultPageName
						)
					);
	
					$content[$val['language_id']] = $ct[0];

				}

				if ($val['def_language']==1) $defaultLanguage = $val['language_id'];

			}

			if (isset($taxon)) $this->smarty->assign('taxon',$taxon);

			if (isset($content)) $this->smarty->assign('content',$content);
	
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

			// user requested a sort of the table
			if (isset($this->requestData['key'])) {

				$sortBy = array('key'=>$this->requestData['key'],'dir'=>($this->requestData['dir']=='asc' ? 'desc' : 'asc' ),'case'=>'i');

			} 
			// default sort order
			else {

				$sortBy = array('key'=>'taxon','dir'=>'asc','case'=>'i');

			}

			// sort array of collaborators
			$this->customSortArray($taxa,$sortBy);

			$this->smarty->assign('sortBy', $sortBy);

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
							
							// if succesful, get the projects default language
							$lp = $this->models->LanguageProject->get(
								array(
									'project_id' => $this->getCurrentProjectId(),
									'def_language' => '1'									
								)
							);
							
							$defaultLanguage = isset($lp[0]['language_id']) ? $lp[0]['language_id'] : $this->requestData['language'];

							// get the main page content for the default language
							$ct = $this->models->ContentTaxon->get(
								array(
									'project_id' => $this->getCurrentProjectId(),
									'taxon_id' => $taxonId,
									'language_id' => $defaultLanguage
								)
							);

							// save the title of that page as taxon name in the taxon table
							$this->models->Taxon->save(
								array(
									'id' => $taxonId ,
									'project_id' => $this->getCurrentProjectId(),
									'taxon' => !empty($ct[0]['content_name']) ? $ct[0]['content_name'] : '?'
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
		
		private function ajaxActionDeletePage() {

			if (empty($this->requestData['id'])) {

				return;

			} else {

				$this->models->TaxonPageTitle->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $this->requestData['id']
					)
				);
	
				$tpt = $this->models->TaxonPage->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $this->requestData['id']
					)
				);

			}

		}
		
		private function ajaxActionSavePageTitle() {

			if (empty($this->requestData['id']) ||
				empty($this->requestData['language'])
			) {

				return;

			} else {

				if (empty($this->requestData['title'])) {

					$tpt = $this->models->TaxonPageTitle->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->requestData['language'],					
							'page_id' => $this->requestData['id'],
						)
					);

				} else {

					$tpt = $this->models->TaxonPageTitle->get(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->requestData['language'],					
							'page_id' => $this->requestData['id'],
						)
					);
	
					$this->models->TaxonPageTitle->save(
						array(
							'id' => isset($tpt[0]['id']) ? $tpt[0]['id'] : null,
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $this->requestData['language'],					
							'page_id' => $this->requestData['id'],
							'title' => $this->requestData['title'],
						)
					);

				}	

				$this->smarty->assign('returnText','saved');

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

			} else
			if ($this->requestData['action']=='delete_page') {

				$this->ajaxActionDeletePage();

			} else
			if ($this->requestData['action']=='save_page_title') {

				$this->ajaxActionSavePageTitle();

			}

			$this->printPage();

		}

	}






















