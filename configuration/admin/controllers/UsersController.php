<?php

	include_once('Controller.php');

	class UsersController extends Controller {

		public $usedModels = array('user','right','project_right_user','project');

		public function __construct() {

			parent::__construct();

		}

		public function __destruct() {

			parent::__destruct();

		}
		
		private function userPasswordEncode($p) {

			return md5($p);

		}

		private function getCurrentUserRights() {

			$pru = $this->models->ProjectRightUser->get(array('user_id' => $this->getCurrentUserId()));
			
			foreach((array)$pru as $key => $val) {

				$p = $this->models->Project->get($val['project_id']);
				$pru[$key]['project'] = $p['name'];

				$r = $this->models->Right->get($val['right_id']);
				$pru[$key]['action'] = $r['action'];
				$pru[$key]['full_path'] = $r['full_path'];

			}
			
			return $pru;

		}

		public function loginAction() {

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

				$this->errors[] = _('Login failed');

			} else {
			
				$this->setCurrentUserId($users[0]);

				$this->models->User->save(
					array(
						'id' => $this->getCurrentUserId(),
						'last_login' => 'now()',
						'logins' => 'logins+1'
						)
					);

				$userRights = $this->getCurrentUserRights();

				$this->setUserSession($users[0],$userRights);

				$this->redirect($this->getLoginStartPage());

			}

		}

		public function logoutAction() {

			$this->destroyUserSession();

			$this->destroyHistory();

			$this->redirect('login.php');

		}

		public function indexAction() {

			$this->checkAuthorisation();

		}
		
		public function chooseProjectAction() {
		
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

		private function isUserDataComplete() {
		
			$result = true;

			if ($this->requestData['username'] == '') {
			
				$this->errors[] = _('Missing username');

				$result = false;

			}

			if ($this->requestData['password'] == '') {
			
				$this->errors[] = _('Missing password');

				$result = false;

			}

			if ($this->requestData['password_2'] == '') {
			
				$this->errors[] = _('Missing password repeat');

				$result = false;

			}

			if ($this->requestData['first_name'] == '') {
			
				$this->errors[] = _('Missing first name');

				$result = false;

			}

			if ($this->requestData['last_name'] == '') {
			
				$this->errors[] = _('Missing last name');

				$result = false;

			}

			if ($this->requestData['email_address'] =='') {
			
				$this->errors[] = _('Missing email address');

				$result = false;

			}
			
			return $result;

		}
		
		private function isUsernameCorrect() {
		
			$result  = true;
		
			if (strlen($this->requestData['username']) < 5) {
		
				$this->errors[] = _('Username too short');
			
				$result = false;

			}

			if (strlen($this->requestData['username']) > 16) {
		
				$this->errors[] = _('Username too long');
			
				$result = false;

			}

			return $result;

		}

		private function isPasswordCorrect() {

			$result  = true;
		
			if (strlen($this->requestData['password']) < 5) {
		
				$this->errors[] = _('Password too short');
			
				$result = false;

			}

			if (strlen($this->requestData['password']) > 16) {
		
				$this->errors[] = _('Password too long');
			
				$result = false;

			}
			
			if ($this->requestData['password'] != $this->requestData['password_2']) {

				$this->errors[] = _('Passwords not the same');
			
				$result = false;

			}


			return $result;

		}

		private function isEmailAddressCorrect() {

			$result  = true;

			$regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";

			if (!preg_match($regexp, $this->requestData['email_address'])) {

				$this->errors[] = _('Invalid e-mail address');
			
				$result = false;

			}

			return $result;

		}

		private function isUserDataCorrect() {

			$result = true;

			if(!$this->isUsernameCorrect()) $result = false;

			if(!$this->isPasswordCorrect()) $result = false;

			if(!$this->isEmailAddressCorrect()) $result = false;

			return $result;

		}

		private function isUsernameUnique() {

			$result  = true;

			if ($this->requestData['username']=='') {

				$result = false;

			} else {

				$users =  $this->models->User->get(array('username' => $this->requestData['username']));
	
				if (count((array)$users)!=0) {
	
					$this->errors[] = _('Username not unique');
				
					$result = false;
	
				}

			}

			return $result;

		}

		private function isEmailAddressUnique() {

			$result  = true;
			
			if ($this->requestData['email_address']=='') {

				$result = false;

			} else {

				$users =  $this->models->User->get(array('email_address' => $this->requestData['email_address']));

				if (count((array)$users)!=0) {
	
					$this->errors[] = _('E-mail address not unique');
				
					$result = false;
	
				}

			}

			return $result;

		}

		private function isUserDataUnique() {

			$result = true;

			if(!$this->isUsernameUnique()) $result = false;

			if(!$this->isEmailAddressUnique()) $result = false;

			return $result;

		}

		public function createAction() {

			$this->checkAuthorisation();

			if ($this->requestData) {

				if ($this->requestData['checked']=='1') {

					$saveUser = true;
				
					$this->requestData = $this->models->User->sanatizeData($this->requestData);
	
					if (!$this->isUserDataComplete()) $saveUser = false;
	
					if (!$this->isUserDataCorrect()) $saveUser = false;
	
					if (!$this->isUserDataUnique()) $saveUser = false;
	
					if ($saveUser) {
		
						$this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);
	
						$this->models->User->save($this->requestData);

//INSERT INTO dev_rights VALUES (NULL , 'Login', 'n', '/admin/views/users/login.php',CURRENT_TIMESTAMP);
					}

				} else {

					$this->smarty->assign('check', true);

					$this->smarty->assign('data', $this->requestData);				

				}
				

			} else {

				$this->smarty->assign('check', false);

				$this->smarty->assign('data', $this->requestData);

			}

		}

	}


?>