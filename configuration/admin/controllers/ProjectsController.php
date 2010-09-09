<?php

/*

hard coded number of free modules

*/


	include_once('Controller.php');

	class ProjectsController extends Controller {

		public $usedModels = array(
			'project','module','module_project','free_module_project',
			'project_role_user','user','role',
			'module_project_user','free_module_project_user',
			'language','language_project','content_taxon'
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
			
			if (!empty($this->requestData['module_new'])) {

				$fmp = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));

				if (count((array)$fmp) < 5 && !$this->isFormResubmit()) {

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

			$this->setPageName( _('Assign collaborator to modules'));

			$modules = $this->models->ModuleProject->get(
							array('project_id'=>$this->getCurrentProjectId()),
							false,'
							module_id asc'
						);

			foreach((array)$modules as $key => $val) {

				$mp = $this->models->Module->get($val['module_id']);

				$modules[$key]['module'] = $mp['module'];

				$modules[$key]['description'] = $mp['description'];

				$mpu = $this->models->ModuleProjectUser->get(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'module_id' => $val['module_id']
					)
				);

				foreach((array)$mpu as $key2 => $val2) {

					$modules[$key]['collaborators'][$val2['user_id']] = $val2;

				}

			}

			$free_modules = $this->models->FreeModuleProject->get(array('project_id'=>$this->getCurrentProjectId()));
			
			foreach((array)$free_modules as $key => $val) {

				$fpu = $this->models->FreeModuleProjectUser->get(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'free_module_id' => $val['id']
					)
				);

				foreach((array)$fpu as $key2 => $val2) {

					$free_modules[$key]['collaborators'][$val2['user_id']] = $val2;

				}

			}

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

			$this->customSortArray($users,array('key'=>'last_name','dir'=>'asc','case'=>'i'));

			$this->smarty->assign('users', $users);

			$this->smarty->assign('free_modules',$free_modules);

			$this->smarty->assign('modules',$modules);

			$this->printPage();

		}

		public function dataAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Project data'));

			if ($this->requestData) {

				$this->requestData['id'] = $this->getCurrentProjectId();

				if (isset($this->requestData['deleteLogo']) && $this->requestData['deleteLogo']=='1') {

					$data = $this->models->Project->get($this->getCurrentProjectId());
					
					if (unlink($data['logo_path'])) {

						$this->requestData['logo_url'] = null;

						$this->requestData['logo_path'] = null;

					}						

				}

				$this->models->Project->save($this->requestData);

			}

			if ($this->requestDataFiles) {

				$fuh = $this->helpers->FileUploadHelper->saveFiles(
						$this->requestDataFiles,
						$this->getDefaultImageUploadDir(),
						$this->getDefaultUploadFilemask(),
						$this->getDefaultUploadMaxSize()
					);

				if (!$fuh['error']) {

					$img = $this->getDefaultImageProjectDir().'project_logo.'.$fuh['result'][0]['extension'];

					if (rename($fuh['result'][0]['path'],$img)) {

						$this->models->Project->save(
							array(
								'id' => $this->getCurrentProjectId(),
								'logo_url' => 
									$this->generalSettings['rootWebUrl'].
									$this->appName.
									'/images/project/'.
									sprintf('%04s',$this->getCurrentProjectId()).'/'.
									'project_logo.'.
									$fuh['result'][0]['extension'],
								'logo_path' => $img
							)
						);

					} else {

						$this->addError(_('Upload failed; could not rename.'));

					}

				} else {

					$this->addError(_('Upload failed'));

					$this->addError($fuh['error']);

				}

			}

			$data = $this->models->Project->get($this->getCurrentProjectId());

			$languages = array_merge(
				$this->models->Language->get('select * from %table% where show_order is not null order by show_order asc'),
				$this->models->Language->get('select * from %table% where show_order is null order by language asc')
			);
			
			foreach((array)$languages as $key => $val) {

				$lp = $this->models->LanguageProject->get(array(
						'language_id' => $val['id'],
						'project_id' => $this->getCurrentProjectId()
					)
				);
				
				$languages[$key]['language_project_id'] = $lp[0]['id'];

				$languages[$key]['is_project_default'] = ($lp[0]['def_language'] == 1);

				$languages[$key]['is_active'] = ($lp[0]['active'] == 'y');

			}

			$this->smarty->assign('data',$data);

			$this->smarty->assign('languages',$languages);

			$this->printPage();

		}

		private function ajaxActionModules($moduleType,$action,$id) {

			if ($moduleType == 'free') {

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
	
					$this->models->FreeModuleProjectUser->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'free_module_id' => $id
						)
					);

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

					$this->models->ModuleProjectUser->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'module_id' => $id
						)
					);
	
					$this->models->ModuleProject->delete(
						array(
							'module_id'=>$id,
							'project_id'=>$this->getCurrentProjectId()
						)
					);
	
				}

			}

		}	

		private function ajaxActionCollaborators($moduleType,$action,$id,$user) {
//NEED CHECKS!
			if ($moduleType == 'free') {
			
				if ($action=='add') {
	
					$this->models->FreeModuleProjectUser->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'free_module_id' => $id,
							'user_id' => $user
						)
					);
	
				} else
				if ($action=='remove') {
	
					$this->models->FreeModuleProjectUser->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'free_module_id' => $id,
							'user_id' => $user
						)
					);
	
				}
	
			} else {
	
				if ($action=='add') {
	
					$this->models->ModuleProjectUser->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'module_id' => $id,
							'user_id' => $user
						)
					);
	
				} else
				if ($action=='remove') {
	
					$this->models->ModuleProjectUser->delete(
						array(
							'project_id' => $this->getCurrentProjectId(),
							'module_id' => $id,
							'user_id' => $user
						)
					);
	
				}
	
			}
	
		}

		private function ajaxActionLanguages($action,$id,$user) {
		
			if ($action == 'add') {
	
				$lp = $this->models->LanguageProject->get(array('project_id' => $this->getCurrentProjectId()));
				
				$make_default = (count((array)$lp)==0);
	
				$this->models->LanguageProject->save(array(
						'id' => null,
						'language_id' => $id,
						'project_id' => $this->getCurrentProjectId(),
						'def_language' => $make_default ? 1 : 0,
						'active' => 1
					)
				);
				
				if ($this->models->LanguageProject->getNewId()=='') $this->addError(_('Language already assigned.'));
	
			}
			elseif ($action == 'default') {
	
				$this->models->LanguageProject->update(
					array('def_language' => 0),
					array('project_id' => $this->getCurrentProjectId())
				);
	
				$this->models->LanguageProject->update(
					array('def_language' => 1),
					array('language_id' => $id,'project_id' => $this->getCurrentProjectId())
				);
	
			}
			elseif ($action == 'deactivate') {
	
				$this->models->LanguageProject->update(
					array('active' => 'n'),
					array('language_id' => $id,'project_id' => $this->getCurrentProjectId())
				);
	
			}
			elseif ($action == 'reactivate') {
	
				$this->models->LanguageProject->update(
					array('active' => 'y'),
					array('language_id' => $id,'project_id' => $this->getCurrentProjectId())
				);
	
			}
			elseif ($action == 'delete') {
	
				$this->models->ContentTaxon->delete(
					array('language_id' => $id,'project_id' => $this->getCurrentProjectId())
				);

				$this->models->LanguageProject->delete(
					array('language_id' => $id,'project_id' => $this->getCurrentProjectId())
				);
	
			}
	
		}
	
		public function ajaxInterfaceAction() {

			$view  = !empty($this->requestData['v']) ? $this->requestData['v'] : null;

			$id = !empty($this->requestData['i']) ? $this->requestData['i'] : null;

			$user = !empty($this->requestData['u']) ? $this->requestData['u'] : null;

			$action  = !empty($this->requestData['a']) ? $this->requestData['a'] : null;

			if (!$view) return;
		
			// determine type of module: free or standard
			if (substr($id,0,1)=='f') {

				$moduleType = 'free';
				
				$id = substr($id,1);

			} else {

				$moduleType = false;

			}

			if ($view=='modules') {
	
				$this->ajaxActionModules($moduleType,$action,$id);	
				
			} else
			if ($view=='collaborators') {

				$this->ajaxActionCollaborators($moduleType,$action,$id,$user);

			} else
			if ($view=='languages') {

				$this->ajaxActionLanguages($action,$id,$user);

			}

			$this->printPage();

		}

	}


