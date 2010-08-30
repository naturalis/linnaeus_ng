<?php

/*

hard coded number of free modules

*/


	include_once('Controller.php');

	class ProjectsController extends Controller {

		public $usedModels = array('project','module','module_project','free_module_project');

		public $controllerPublicName = 'Project administration';

		public function __construct() {

			parent::__construct();

		}

		public function __destruct() {

			parent::__destruct();

		}

		public function indexAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Index'));

			$this->printPage();

		}

		public function modulesAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Project modules'));
			
			if ($this->requestData) {

				// adding new modules
				foreach((array)$this->requestData['module'] as $key => $val) {

					if (!in_array($val,(array)$this->requestData['module_exist'])) {

						$this->models->ModuleProject->save(
							array(
								'id' => null,
								'module_id'=>$val,
								'project_id'=>$this->getCurrentProjectId()
							)
						);
						
					}

				}

				// removing de-selected modules
				foreach((array)$this->requestData['module_exist'] as $key => $val) {

					if (!in_array($val,(array)$this->requestData['module'])) {

						$this->models->ModuleProject->delete(
							array(
								'module_id' => $val,
								'project_id'=>$this->getCurrentProjectId()
							)
						);

					}

				}
				
				if ($this->requestData['module_new']) {

					$fmp = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));
					
					if (count((array)$fmp) < 5) {

						$this->models->FreeModuleProject->save(
							array(
								'id' => null,
								'module' => $this->requestData['module_new'],
								'project_id'=>$this->getCurrentProjectId()
							)
						);
									
					}

				}

			}

			$modules = $this->models->Module->get(array('1'=>'1'),false,'show_order');

			foreach((array)$modules as $key => $val) {

				$mp = $this->models->ModuleProject->get(
					array(
						'module_id'=>$val['id'],
						'project_id'=>$this->getCurrentProjectId()
					)
				);

				$modules[$key]['_in_project'] = $mp[0]['id'] ? $mp[0]['id'] : false;

			}

			$free_modules = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));

			$this->smarty->assign('free_modules',$free_modules);

			$this->smarty->assign('modules',$modules);

			$this->printPage();

		}

		public function dataAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Index'));

			$this->printPage();

		}



	}

?>