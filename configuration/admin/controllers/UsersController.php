<?php

	/*
	
		- replace hard coded role_id's
		- must check if role_id != assignable

	*/


	include_once('Controller.php');

	class UsersController extends Controller {

		public $usedModels = array('user','right','role','project_role_user','project','right_role');

		public function __construct() {

			parent::__construct();

		}

		public function __destruct() {

			parent::__destruct();

		}

		private function isUserPartOfProject($user,$project) {

			$pru = $this->models->ProjectRoleUser->get(array('user_id'=>$user,'project_id'=>$project));

			return count((array)$pru)!=0;

		}

		private function getUserProjectRole($user,$project) {

			$pru = $this->models->ProjectRoleUser->get(array('user_id'=>$user,'project_id'=>$project));

			if ($pru) {

				$r = $this->models->Role->get($pru[0]['role_id']);

				$pru[0]['role'] = $r;
			}
			
			return $pru[0];

		}

		private function userPasswordEncode($p) {

			return md5($p);

		}

		private function isUserDataComplete($fieldsToIgnore = array()) {

			$result = true;

			if (!in_array('username',$fieldsToIgnore) && $this->requestData['username'] == '') {
			
				$this->addError(_('Missing username'));

				$result = false;

			}
			
			if (!in_array('password',$fieldsToIgnore) && $this->requestData['password'] == '') {
			
				$this->addError(_('Missing password'));

				$result = false;

			}

			if (!in_array('password_2',$fieldsToIgnore) && $this->requestData['password_2'] == '') {
			
				$this->addError(_('Missing password repeat'));

				$result = false;

			}
			

			if (!in_array('first_name',$fieldsToIgnore) && $this->requestData['first_name'] == '') {
			
				$this->addError(_('Missing first name'));

				$result = false;

			}

			if (!in_array('last_name',$fieldsToIgnore) && $this->requestData['last_name'] == '') {
			
				$this->addError(_('Missing last name'));

				$result = false;

			}

			if (!in_array('email_address',$fieldsToIgnore) && $this->requestData['email_address'] =='') {
			
				$this->addError(_('Missing email address'));

				$result = false;

			}
			
			return $result;

		}
		
		private function isUsernameCorrect() {
		
			$result  = true;
		
			if (strlen($this->requestData['username']) < 5) {
		
				$this->addError(_('Username too short'));
			
				$result = false;

			}

			if (strlen($this->requestData['username']) > 16) {
		
				$this->addError(_('Username too long'));
			
				$result = false;

			}

			return $result;

		}

		private function isPasswordCorrect() {

			$result  = true;
		
			if (strlen($this->requestData['password']) < 5) {
		
				$this->addError(_('Password too short'));
			
				$result = false;

			}

			if (strlen($this->requestData['password']) > 16) {
		
				$this->addError(_('Password too long'));
			
				$result = false;

			}
			
			if ($this->requestData['password'] != $this->requestData['password_2']) {

				$this->addError(_('Passwords not the same'));
			
				$result = false;

			}


			return $result;

		}

		private function isEmailAddressCorrect() {

			$result  = true;

			$regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";

			if (!preg_match($regexp, $this->requestData['email_address'])) {

				$this->addError(_('Invalid e-mail address'));
			
				$result = false;

			}

			return $result;

		}

		private function isUserDataCorrect($fieldsToIgnore = array()) {

			$result = true;

			if (!in_array('username',$fieldsToIgnore)) if(!$this->isUsernameCorrect()) $result = false;

			if (!in_array('password',$fieldsToIgnore)) if(!$this->isPasswordCorrect()) $result = false;

			if (!in_array('email_address',$fieldsToIgnore)) if(!$this->isEmailAddressCorrect()) $result = false;

			return $result;

		}

		private function isUsernameUnique($idToIgnore = false) {

			$result  = true;

			if ($this->requestData['username']=='') {

				$result = false;

			} else {
			
				if ($idToIgnore) {

					$w = array('username' => $this->requestData['username'],'id !=' => $idToIgnore);

				} else {

					$w = array('username' => $this->requestData['username']);

				}

				$users =  $this->models->User->get($w);
	
				if (count((array)$users)!=0) {
	
					$this->addError(_('Username not unique'));
				
					$result = false;
	
				}

			}

			return $result;

		}

		private function isEmailAddressUnique($idToIgnore = false) {

			$result  = true;
			
			if ($this->requestData['email_address']=='') {

				$result = false;

			} else {

				if ($idToIgnore) {

					$w = array('email_address' => $this->requestData['email_address'],'id !=' => $idToIgnore);

				} else {

					$w = array('email_address' => $this->requestData['email_address']);

				}

				$users =  $this->models->User->get($w);

				if (count((array)$users)!=0) {
	
					$this->addError(_('E-mail address not unique'));

					$result = false;
	
				}

			}

			return $result;

		}

		private function isUserDataUnique($idToIgnore = false) {

			$result = true;

			if(!$this->isUsernameUnique($idToIgnore)) $result = false;

			if(!$this->isEmailAddressUnique($idToIgnore)) $result = false;

			return $result;

		}

		public function loginAction() {

			$this->setPageName(_('Login'));

			$this->smarty->assign('excludecludeBottonMenu',true);

			if (
				(!isset($this->requestData['username']) || $this->requestData['username']=='') && 
				(!isset($this->requestData['password']) || $this->requestData['password']=='')
				) {

				return;

			}

			$users = 
				$this->models->User->get(
					array(
						'username' => $this->requestData['username'],
						'password' => $this->userPasswordEncode($this->requestData['password']),
						'active' => '1'
						)
					);

			if(count((array)$users)!=1) {

				$this->addError(_('Login failed'));

			} else {
			
				$this->setCurrentUserId($users[0]);

				$this->models->User->save(
					array(
						'id' => $this->getCurrentUserId(),
						'last_login' => 'now()',
						'logins' => 'logins+1'
						)
					);

				$userRolesAndRights = $this->getCurrentUserRights();

				$this->setUserSession($users[0],$userRolesAndRights);

				$this->redirect($this->getLoginStartPage());

			}

		}

		public function logoutAction() {

			$this->setPageName(_('Logout'));

			$this->destroyUserSession();

//			$this->destroyHistory();

			$this->redirect('login.php');

		}

		public function indexAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Overview'));

		}
		
		public function chooseProjectAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Choose a project'));

			if (isset($this->requestData['project_id'])) {
			
				if ($this->isCurrentUserAuthorizedForProject($this->requestData['project_id'])) {
				
					$this->setCurrentProjectId($this->requestData['project_id']);

					$this->setCurrentProjectName();

					$this->redirect('index.php');

				} else {
				
					$this->redirect('choose_project.php');

				}

			}

			$this->smarty->assign('projects', $this->getCurrentUserProjects());

		}

		public function createAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Create new project user'));

			if ($this->requestData) {

				if ($this->requestData['checked']=='1') {

					$this->requestData = $_SESSION['data']['new_user'];

					$this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);

					$this->requestData['active'] = '1';

					$r = $this->models->User->save($this->requestData);
					
					if ($r!==true) {

						$this->addError(_('Failed to save user'));

						$this->smarty->assign('check', false);
	
						$userData = $_SESSION['data']['new_user'];

					} else {

						$newUserId = $this->models->User->getNewId();

						// must check if role_id != assignable
						$this->models->ProjectRoleUser->save(
								array(
									'id' => 'null',
									'project_id' => $this->getCurrentProjectId(),
									'role_id' => $this->requestData['role_id'],
									'user_id' => $newUserId
								)
							);

						unset($_SESSION['data']['new_user']);

						$this->redirect('user_overview.php');

					}


				} elseif ($this->requestData['checked']=='-1') {
				
					// user verified data and clicked 'back'

					$this->smarty->assign('check', false);

					$userData = $_SESSION['data']['new_user'];

				} else {

					// user submitted data, is shown non-editable data to verify

					$saveUser = true;
				
					$this->requestData = $this->models->User->sanatizeData($this->requestData);

					$_SESSION['data']['new_user'] = $this->requestData;
	
					if (!$this->isUserDataComplete()) $saveUser = false;
	
					if (!$this->isUserDataCorrect()) $saveUser = false;
	
					if (!$this->isUserDataUnique()) $saveUser = false;
	
					if ($saveUser) {
		
						$this->smarty->assign('check', true);

					}
					
					$userData = $this->requestData;

				}
			

			} else {
			
				// input form, shows empty. or with data when user clicked 'save' but data contained errors

				$this->smarty->assign('check', false);
				
				$userData = $this->requestData;

			}

			$roles = $this->models->Role->get(array('assignable' => 'y'));

			$this->smarty->assign('roles', $roles);

			$this->smarty->assign('data', $userData);

		}

		public function userOverviewAction() {

			$this->checkAuthorisation();


			$this->setPageName(_('Project users overview'));


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

			if ($this->requestData) {

				$sortBy = array('key'=>$this->requestData['key'],'dir'=>($this->requestData['dir']=='asc' ? 'desc' : 'asc' ),'case'=>'i');

			} else {

				$sortBy = array('key'=>'last_name','dir'=>'asc','case'=>'i');

			}

			$this->sortUserArray($users,$sortBy);

			$this->smarty->assign('sortBy', $sortBy);
			$this->smarty->assign('users', $users);

		}

		public function viewAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Project user data'));

			if ($this->isUserPartOfProject($this->requestData['id'],$this->getCurrentProjectId())) {

				$user = $this->models->User->get($this->requestData['id']);

				$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

				$this->smarty->assign('data', $user);

				$this->smarty->assign('userRole', $upr);

			} else {

				$this->redirect();

			}

		}

		public function editAction() {

			$this->checkAuthorisation();
			
			$this->setPageName(_('Edit project user'));

			if ($this->isUserPartOfProject($this->requestData['id'],$this->getCurrentProjectId())) {

				if ($this->requestData['delete']=='1') {
				
					$this->models->ProjectRoleUser->delete(array('user_id' => $this->requestData['id']));

					$this->models->User->delete($this->requestData['id']);

					$this->redirect('user_overview.php');

				} else if ($this->requestData['checked']=='1') {
	
					$saveUser = true;
				
					$this->requestData = $this->models->User->sanatizeData($this->requestData);
	
					if ($this->requestData['password'] == '' && $this->requestData['password_2'] == '') {
	
						if (!$this->isUserDataComplete(array('password', 'password_2'))) $saveUser = false;
					
						if (!$this->isUserDataCorrect(array('password', 'password_2'))) $saveUser = false;

					} else {
	
						if (!$this->isUserDataComplete()) $saveUser = false;
					
						if (!$this->isUserDataCorrect()) $saveUser = false;

					}
	
					if (!$this->isUserDataUnique($this->requestData['id'])) $saveUser = false;

					if ($saveUser) {

						if ($this->requestData['password']) {

							$this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);

						}

						$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

						// cannot change role of lead expert, or make them inactive
						if ($upr['role_id'] != 2) {

							$this->models->ProjectRoleUser->save(array(
								'id' => $this->requestData['userProjectRole'],
								'user_id'=>$this->requestData['id'],
								'project_id'=>$this->getCurrentProjectId(),
								'role_id'=> $this->requestData['role_id']
								)
							);

						} else {

							$this->requestData['active'] = 1;

						}

						$this->models->User->save($this->requestData);

						$this->addMessage(_('User data saved'));

					} else {

						$user = $this->requestData;

					}

				}

				$user = $this->models->User->get($this->requestData['id']);

				$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

				$roles = $this->models->Role->get(array('assignable' => 'y'));

				$this->smarty->assign('isLeadExpert', $upr['role_id'] == 2 );

				$this->smarty->assign('roles', $roles);

				$this->smarty->assign('data', $user);

				$this->smarty->assign('userRole', $upr);

			} else {

				$this->redirect();

			}

		}
		
		public function notAuthorizedAction() {

			$this->addError(_('You are not authorized to do that.'));

		}

	}


?>