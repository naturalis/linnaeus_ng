<?php

/*

hard coded number of free modules

*/


	include_once('Controller.php');

	class ProjectsController extends Controller {

		public $usedModels = array(
			'project','module','module_project','free_module_project',
			'project_role_user','user','role',
			'modules_projects_users','free_modules_projects_users'
			);

		public $usedHelpers = array('file_upload_helper');

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
			
			if ($this->requestData['module_new']) {

				$fmp = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));

				if (count((array)$fmp) < 5 && $_SESSION['system']['last_rnd'] != $this->requestData['rnd']) {

					$this->models->FreeModuleProject->save(
						array(
							'id' => null,
							'module' => $this->requestData['module_new'],
							'project_id'=>$this->getCurrentProjectId()
						)
					);
					
					$_SESSION['system']['last_rnd'] = $this->requestData['rnd'];

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

				$modules[$key]['module_project_id'] = $mp[0]['id'] ? $mp[0]['id'] : false;
				$modules[$key]['active'] = $mp[0]['id'] ? $mp[0]['active'] : false;

			}

			$free_modules = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));

			$this->smarty->assign('free_modules',$free_modules);

			$this->smarty->assign('modules',$modules);

			$this->printPage();

		}

		public function collaboratorsAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Collaborator tasks'));

			$modules = $this->models->ModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));

			foreach((array)$modules as $key => $val) {

				$mp = $this->models->Module->get($val['module_id']);

				$modules[$key]['module'] = $mp['module'];

				$modules[$key]['description'] = $mp['description'];

			}

			$free_modules = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));
			

			$pru =  $this->models->ProjectRoleUser->get(
				array(
					'project_id' => $this->getCurrentProjectId()), 
					'distinct user_id, role_id'
				);

			foreach((array)$pru as $key => $val) {

				$u = $this->models->User->get($val['user_id']);

				$r = $this->models->Role->get($val['role_id']);

				$u['role'] = $r['role'];

				$users[] = $u;

			}

			$this->sortUserArray($users,array('key'=>'last_name','dir'=>'asc','case'=>'i'));

			$this->smarty->assign('users', $users);

			$this->smarty->assign('free_modules',$free_modules);

			$this->smarty->assign('modules',$modules);

			$this->printPage();

		}

		public function dataAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Project data'));
			
			if ($this->requestDataFiles) {
//DIR!
				$d =$this->helpers->FileUploadHelper->saveFiles(
						$this->requestDataFiles,'C:\tmp',
						$this->getDefaultUploadFilemask(),
						$this->getDefaultUploadMaxSize()
					);

				echo '<pre>';
				var_dump($d);

			}
			
			$data = $this->models->Project->get($this->getCurrentProjectId());

			$this->smarty->assign('data',$data);

			$this->printPage();

		}

		public function ajaxInterfaceAction() {

			$view  = $this->requestData['v'];
			
			$id = $this->requestData['i'];

			$action  = $this->requestData['a'];

			if (!$view) return;
		
			// determine type of module: free or standard
			if (substr($id,0,1)=='f') {

				$t = 'free';
				
				$id = substr($id,1);

			}

			if ($view=='modules') {
	
				if ($t == 'free') {
	
					if ($action=='activate' || $action=='reactivate') {
	
						$this->models->FreeModuleProject->update(
							array('active' => 'y'),
							array('id' => $id, 'project_id' => $this->getCurrentProjectId())
						);
	
					}
					if ($action=='deactivate') {
		
						$this->models->FreeModuleProject->update(
							array('active' => 'n'),
							array('id' => $id, 'project_id' => $this->getCurrentProjectId())
						);
		
					} else
					if ($action=='delete') {
		
						$this->models->FreeModuleProject->delete(
							array(
								'id'=>$id,
								'project_id'=>$this->getCurrentProjectId()
							)
						);
		
					}
	
				} else {
	
					if ($action=='activate') {
					
						$this->models->ModuleProject->save(
							array(
								'id' => null,
								'module_id'=>$id,
								'project_id'=>$this->getCurrentProjectId()
							)
						);
	
					} else
					if ($action=='reactivate') {
		
						$this->models->ModuleProject->update(
							array('active' => 'y'),
							array('module_id' => $id, 'project_id' => $this->getCurrentProjectId())
						);
		
		
					} else
					if ($action=='deactivate') {
		
						$this->models->ModuleProject->update(
							array('active' => 'n'),
							array('module_id' => $id, 'project_id' => $this->getCurrentProjectId())
						);
		
					} else
					if ($action=='delete') {
		
						$this->models->ModuleProject->delete(
							array(
								'module_id'=>$id,
								'project_id'=>$this->getCurrentProjectId()
							)
						);
		
					}
	
				}			
				
			} else
			if ($view=='collaborators') {
			
//$this-models->ModulesProjectsUsers
//$this-models->FreemodulesProjectsUsers

			}

			$this->printPage();

		}

	}

?>