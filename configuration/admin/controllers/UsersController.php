<?php

	/*
	
		- replace hard coded role_id's

	*/


	include_once('Controller.php');

	class UsersController extends Controller {

		public $usedModels = array('user','right','role','project_role_user','project','right_role');

		public $controllerPublicName = 'User administration';

		public function __construct() {

			parent::__construct();

		}

		public function __destruct() {

			parent::__destruct();

		}

		public function getCurrentUserRights() {

			$pru = $this->models->ProjectRoleUser->get(array('user_id' => $this->getCurrentUserId()));

			foreach((array)$pru as $key => $val) {
			
				$p = $this->models->Project->get($val['project_id']);
				
				$pru[$key]['project_name'] = $p['sys_name'];

				$r = $this->models->Role->get($val['role_id']);

				$pru[$key]['role_name'] = $r['role'];

				$pru[$key]['role_description'] = $r['description'];

				$rr = $this->models->RightRole->get(array('role_id' => $val['role_id']));
				
				foreach((array)$rr as $rr_key => $rr_val) {

					$r = $this->models->Right->get($rr_val['right_id']);

					$rs[$val['project_id']][$r['controller']][$r['id']] = $r['view'];

				}				

				$d[$val['project_id']] = $val['project_id'];

			}

			return array('roles' => $pru,'rights' => $rs, 'number_of_projects' => count((array)$d));

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
		
		private function isUsernameCorrect($username = false) {
		
			if (!$username) $username = $this->requestData['username'];

			$result  = true;
		
			if (strlen($username) < 5) {
		
				$this->addError(_('Username too short'));
			
				$result = false;

			}

			if (strlen($username) > 16) {
		
				$this->addError(_('Username too long'));
			
				$result = false;

			}

			return $result;

		}

		private function isPasswordCorrect($password = false, $password_2 = false) {

			if (!$password) $password = isset($this->requestData['password']) ? $this->requestData['password'] : null;

			if (!$password_2) $password_2 = isset($this->requestData['password_2']) ? $this->requestData['password_2'] : null;

			$result  = true;
		
			if (strlen($password) < 5) {
		
				$this->addError(_('Password too short'));
			
				$result = false;

			}

			if (strlen($password) > 16) {
		
				$this->addError(_('Password too long'));
			
				$result = false;

			}
			
			if ($password_2 != '' && ($password != $password_2)) {

				$this->addError(_('Passwords not the same'));
			
				$result = false;

			}


			return $result;

		}

		private function isEmailAddressCorrect($email_address = false) {

			if (!$email_address) $email_address = isset($this->requestData['email_address']) ? $this->requestData['email_address'] : null;

			$result  = true;

			$regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";

			if (!preg_match($regexp, $email_address)) {

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

		private function isUsernameUnique($username = false, $idToIgnore = false) {

			if (!$username) $username = isset($this->requestData['username']) ? $this->requestData['username'] : null;

			$result  = true;

			if ($username=='') {

				$result = false;

			} else {
			
				if ($idToIgnore) {

					$w = array('username' => $username,'id !=' => $idToIgnore);

				} else {

					$w = array('username' => $username);

				}

				$users =  $this->models->User->get($w);
	
				if (count((array)$users)!=0) {
	
					$this->addError(_('Username already exists'));
				
					$result = false;
	
				}

			}

			return $result;

		}

		private function isEmailAddressUnique($email_address = false, $idToIgnore = false, $suppress_error = false) {

			if (!$email_address) $email_address = isset($this->requestData['email_address']) ? $this->requestData['email_address'] : null;

			$result  = true;
			
			if ($email_address=='') {

				$result = false;

			} else {

				if ($idToIgnore) {

					$w = array('email_address' => $email_address,'id !=' => $idToIgnore);

				} else {

					$w = array('email_address' => $email_address);

				}

				$users =  $this->models->User->get($w);

				if (count((array)$users)!=0) {
	
					if (!$suppress_error) $this->addError(_('E-mail address already exists'));

					$result = false;
	
				}

			}

			return $result;

		}

		private function isUserDataUnique($idToIgnore = false) {

			$result = true;

			if(!$this->isUsernameUnique(false, $idToIgnore)) $result = false;

			if(!$this->isEmailAddressUnique(false, $idToIgnore)) $result = false;

			return $result;

		}

		private function getSimilarUsers($idToIgnore = false) {

			$q = "select * from %table% where 
					((lower(first_name) = '". $this->models->User->escapeString(strtolower($this->requestData['first_name']))."'
					and lower(last_name) = '". $this->models->User->escapeString(strtolower($this->requestData['last_name']))."')
					or email_address = '". $this->models->User->escapeString($this->requestData['email_address'])."')".
					($idToIgnore ? " and id !=". $idToIgnore : '' );

			$users =  $this->models->User->get($q);

			return $users;

		}

		public function loginAction() {

			$this->setPageName(_('Login'));

			$this->smarty->assign('excludecludeBottonMenu',true);

			if (
				(isset($this->requestData['username']) && $this->requestData['username']!='') || 
				(isset($this->requestData['password']) && $this->requestData['password']!='')
				) 
			{

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
	
					$cur = $this->getCurrentUserRights();
	
					$this->setUserSession($users[0],$cur['roles'],$cur['rights'],$cur['number_of_projects']);
					
					$this->setDefaultProject();
	
					$this->redirect($this->getLoginStartPage());
	
				}

			}

			$this->printPage();

		}

		public function logoutAction() {

			$this->setPageName(_('Logout'));

			$this->destroyUserSession();

			$this->redirect('login.php');

		}

		public function indexAction() {

			$this->checkAuthorisation();

			$this->setPageName( _('Index'));

			$this->printPage();

		}
		
		public function chooseProjectAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Select a project to work on'));

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

			$this->printPage();

		}

		/**
		* Creating a new collaborator
		*
		* See function code for detailed comments on the function's flow
		*
		* @access	public
		*/
		public function createAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Create new collaborator'));

			// data was submitted
			if ($this->requestData) {

				// checked = 2: user entered data of a collaborator that already exists, but was not assigned to current project yet.
				// instead of creating a new collaborator, we assign him to the current project with the specified role.
				if ($this->requestData['checked']=='2') {

					// make sure an unassignable role (like system admin) wasn't injected
					$r = $this->models->Role->get($this->requestData['role_id']);

					// if unassignable, raise error
					if ($r['assignable'] == 'n') {

						$this->addError(_('Unassignable role selected'));

						$this->smarty->assign('check', false);
	
						$userData = $_SESSION['data']['new_user'];

					} else {

						// save new role only for existing collaborator and new project
						$this->models->ProjectRoleUser->save(
								array(
									'id' => null,
									'project_id' => $this->getCurrentProjectId(),
									'role_id' => $this->requestData['role_id'],
									'user_id' => $this->requestData['existing_user_id']
								)
							);

						unset($_SESSION['data']['new_user']);

						$this->redirect('user_overview.php');

					}

				}
				// cheked = 1: new collaborator, save data
				elseif ($this->requestData['checked']=='1') {

					// make sure an unassignable role (like system admin) wasn't injected
					$r = $this->models->Role->get($_SESSION['data']['new_user']['role_id']);

					// if unassignable, raise error
					if ($r['assignable'] == 'n') {

						$this->addError(_('Unassignable role selected'));

						$this->smarty->assign('check', false);
	
						$userData = $_SESSION['data']['new_user'];

					} else {

						// encode passwords and save data
						$this->requestData = $_SESSION['data']['new_user'];
	
						$this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);
	
						$this->requestData['active'] = '1';

						$this->requestData['id'] = null;
	
						$r = $this->models->User->save($this->requestData);

						if ($r!==true) {
	
							$this->addError(_('Failed to save user'));
	
							$this->smarty->assign('check', false);
		
							$userData = $_SESSION['data']['new_user'];
	
						} else {
	
							// if saving was succesfull, save new role
							$newUserId = $this->models->User->getNewId();
	
							$this->models->ProjectRoleUser->save(
									array(
										'id' => null,
										'project_id' => $this->getCurrentProjectId(),
										'role_id' => $this->requestData['role_id'],
										'user_id' => $newUserId
									)
								);
	
							unset($_SESSION['data']['new_user']);
	
							$this->redirect('user_overview.php');
	
						}

					}

				} 
				// user verified the data and clicked 'back'
				elseif ($this->requestData['checked']=='-1') {
				
					$this->smarty->assign('check', false);

					$userData = $_SESSION['data']['new_user'];

				} 
				// user submitted data, is now shown non-editable data to verify, or editable if containing errors
				else {

					$saveUser = true;
				
					$this->requestData = $this->models->User->sanatizeData($this->requestData);

					// save data in session for saving in the next step
					$_SESSION['data']['new_user'] = $this->requestData;

					// check data validity etc.
					if (!$this->isUserDataComplete()) $saveUser = false;
	
					if (!$this->isUserDataCorrect()) $saveUser = false;
	
					if (!$this->isUserDataUnique()) $saveUser = false;

					// see if similar collaborators might exist, based on identical name, or identical email address
					$sim = $this->getSimilarUsers();
					
					// if there are similar users...
					if (count((array)$sim) != 0) {

						// ...it might be because of his name...
						if ($this->isEmailAddressUnique(false,false,true)) {
		
							$this->addMessage(_('A similar user, albeit with a different e-mail address, already exists in another project:'));

							$this->addMessage('<span class="message-existing-user">'.$sim[0]['first_name'].' '.$sim[0]['last_name'].'</span> ('.$sim[0]['email_address'].')');

							$this->addMessage(_('Would you like to connect that user to the current project instead of creating a new one?'));

							$this->addMessage(
								'<input type="button" value="'._('yes, connect existing').'" onclick="$(\'#checked\').val(\'2\');$(\'#theForm\').submit();">&nbsp;
								<input type="button" value="'._('no, create new').'" onclick="$(\'#checked\').val(\'1\');$(\'#theForm\').submit();">&nbsp;'
							);

						} 
						// ...or because of his email address (or both)
						else {

							$this->addMessage(_('A user with the same e-mail address already exists in another project:'));

							$this->addMessage('<span class="message-existing-user">'.$sim[0]['first_name'].' '.$sim[0]['last_name'].'</span> ('.$sim[0]['email_address'].')');

							$this->addMessage(_('You cannot create a new user with the same e-mail address, but you can connect the existing user to the current project. Would you like to do that?'));

							$this->addMessage(
								'<input type="button" value="'._('yes').'" onclick="$(\'#checked\').val(\'2\');$(\'#theForm\').submit();">&nbsp;
								<input type="button" value="'._('no').'" onclick="window.open(\'user_overview.php\',\'_self\');">'
							);

						}

						$this->smarty->assign('existing_user', $sim[0]);

						$saveUser = false;

					}

					$this->smarty->assign('check', $saveUser ? '1' : false);

					$userData = $this->requestData;

				}
			

			} 

			// input form, shows empty. or with data when user clicked 'save' but data contained errors			
			else {

				$this->smarty->assign('check', false);
				
				$userData = $this->requestData;

			}

			$roles = $this->models->Role->get(array('assignable' => 'y'));

			$this->smarty->assign('roles', $roles);

			$this->smarty->assign('data', $userData);

			$this->printPage();

		}

		/**
		* Overview of all collaborators in the current project
		*
		* @access	public
		*/
		public function userOverviewAction() {

			$this->checkAuthorisation();


			$this->setPageName(_('Project collaborator overview'));

			// get all collaborators for the current project
			$pru =  $this->models->ProjectRoleUser->get(
				array(
					'project_id' => $this->getCurrentProjectId()), 
					'distinct user_id, role_id'
				);

			// get full details, as well as roles for each collaborator
			foreach((array)$pru as $key => $val) {

				$u = $this->models->User->get($val['user_id']);

				$r = $this->models->Role->get($val['role_id']);

				$u['role'] = $r['role'];

				$users[] = $u;

			}

			// user requested a sort of the table
			if ($this->requestData) {

				$sortBy = array('key'=>$this->requestData['key'],'dir'=>($this->requestData['dir']=='asc' ? 'desc' : 'asc' ),'case'=>'i');

			} 
			// default sort order
			else {

				$sortBy = array('key'=>'last_name','dir'=>'asc','case'=>'i');

			}

			// sort array of collaborators
			$this->customSortArray($users,$sortBy);

			$this->smarty->assign('sortBy', $sortBy);

			$this->smarty->assign('users', $users);

			$this->printPage();

		}

		/**
		* Viewing data of a collaborator
		*
		* @access	public
		*/
		public function viewAction() {

			$this->checkAuthorisation();

			$this->setPageName(_('Project collaborator data'));

			if ($this->isUserPartOfProject($this->requestData['id'],$this->getCurrentProjectId())) {

				$user = $this->models->User->get($this->requestData['id']);

				$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

				$this->smarty->assign('data', $user);

				$this->smarty->assign('userRole', $upr);

				$this->printPage();

			} else {

				$this->redirect();

			}

		}

		/**
		* Editing collaborator data
		*
		* See function code for detailed comments on the function's flow
		*
		* @access	public
		*/
		public function editAction() {

			$this->checkAuthorisation();
			
			$this->setPageName(_('Edit project collaborator'));

			// check whether the collaborator to be edited is part of the current project (avoid injected id)
			if ($this->isUserPartOfProject($this->requestData['id'],$this->getCurrentProjectId())) {

				// user requested delete
				if (isset($this->requestData['delete']) && $this->requestData['delete']=='1') {

					// delete collaborator's role from this project
					$this->models->ProjectRoleUser->delete(
						array(
							'user_id' => $this->requestData['id'],
							'project_id' => $this->getCurrentProjectId()
							)
						);

					// avoiding orphans: see if collaborator is present in any other projects...
					$data = $this->models->ProjectRoleUser->get(array('user_id' => $this->requestData['id']),'count(*) as tot');

					// ...if not, delete entire collaborator record
					if (isset($data) && $data[0]['tot'] == '0') {

						$this->models->User->delete($this->requestData['id']);

					}

					// redirect user to overview of remaining collaborators
					$this->redirect('user_overview.php');

				// user requested data update
				} else if (isset($this->requestData['checked']) && $this->requestData['checked']=='1') {

					// make sure an unassignable role (like system admin) wasn't injected
					$r = $this->models->Role->get($this->requestData['role_id']);

					if ($r['assignable'] == 'n') {

						$this->addError(_('Unassignable role selected'));

						$saveUser = false;

					} else {

						$saveUser = true;
					
						// clean up data
						$this->requestData = $this->models->User->sanatizeData($this->requestData);

						// if a no new passwords were entered, don't do a password check...
						if ($this->requestData['password'] == '' && $this->requestData['password_2'] == '') {
		
							if (!$this->isUserDataComplete(array('password', 'password_2'))) $saveUser = false;
						
							if (!$this->isUserDataCorrect(array('password', 'password_2'))) $saveUser = false;
	
						}
						// ...otherwise do a full check
						else {
		
							if (!$this->isUserDataComplete()) $saveUser = false;
						
							if (!$this->isUserDataCorrect()) $saveUser = false;
	
						}

						// check whether data is unique; passing the collaborator's id avoids conflict with himself
						if (!$this->isUserDataUnique($this->requestData['id'])) $saveUser = false;
					
					}

					// data ok, can be saved
					if ($saveUser) {

						// if new password, encrypt the human readable to an encrypted one
						if ($this->requestData['password']) {

							$this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);

						}

						// get the current role of the collaborator in the current project
						$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

						// if collaborator has a regular role, update to the new role...
						if ($upr['role_id'] != 1 && $upr['role_id'] != 2) {

							$this->models->ProjectRoleUser->save(array(
								'id' => $this->requestData['userProjectRole'],
								'user_id'=>$this->requestData['id'],
								'project_id'=>$this->getCurrentProjectId(),
								'role_id'=> $this->requestData['role_id']
								)
							);

						} 
						// ... but the role of lead expert or system admin cannot be changed, nether can he be made inactive
						else {

							$this->requestData['active'] = 1;

						}

						// save the new data
						$this->models->User->save($this->requestData);

						$this->addMessage(_('User data saved'));

					} 
					// user cannot be saved
					else {

						$user = $this->requestData;

					}

				}

				// assign all data and print success or errors
				$user = $this->models->User->get($this->requestData['id']);

				$upr = $this->getUserProjectRole($this->requestData['id'],$this->getCurrentProjectId());

				$roles = $this->models->Role->get(array('assignable' => 'y'));

				$this->smarty->assign('isLeadExpert', $upr['role_id'] == 2 );

				$this->smarty->assign('roles', $roles);

				$this->smarty->assign('data', $user);

				$this->smarty->assign('userRole', $upr);

				$this->printPage();

			} else {

				$this->redirect();

			}

		}
		
		/**
		* View displaying 'not authorized'
		*
		* Users can be redirected to notAuthorizedAction from every controller,
		* so the controller name is hidden in the output to avoid confusion.
		*
		* @access	public
		*/
		public function notAuthorizedAction() {

			$this->smarty->assign('hideControllerPublicName', true);

			$this->addError(_('You are not authorized to do that.'));

			$this->printPage();

		}
		
		/**
		* AJAX interface for this class
		*
		* Is used by the 'edit' and 'create' views to check values without reloading the page
		* The array 'v' contains the values of the variables to check.
		* The variable 'f' contains the name of the variable to check.
		* Possible test (request variable 't'):
		*  e	does value v already exit for field f?
		*  f	is formatting of value v correct?
		*  q	are values 1 & 2 equal?
		* The variable 'i' can contain the id of a user to ignore in the test (to avoid claiming conflict
		* with a user's own username or email address when editing).
		*
		* @access	public
		*/
		public function ajaxInterfaceAction() {

			$field = $this->requestData['f'];

			$values = explode(',',$this->requestData['v']);

			$tests  = explode(',',$this->requestData['t']);

			$idToIgnore  = isset($this->requestData['i']) ? $this->requestData['i'] : false;

			if ($field=='') return;

			foreach((array)$tests as $key => $test) {

				if ($test == 'e') {
	
					if ($field == 'username') $this->isUsernameUnique($values[0],$idToIgnore);
	
					if ($field == 'email_address') $this->isEmailAddressUnique($values[0],$idToIgnore);
	
				} else
				if ($test == 'f') {
	
					switch ($field) {

						case 'username':

							$this->isUsernameCorrect($values[0]);

							break;

						case 'email_address':
						
							$this->isEmailAddressCorrect($values[0]);
							
							break;
	
						case 'password':

							$this->isPasswordCorrect($values[0]);

							break;

						case 'password_2':

							$this->isPasswordCorrect($values[0],$values[1]);

							break;

						default:

							if (strlen($values[0])==0) $this->addError(_('Missing value'));

					}	

				} else
				if ($test == 'q') {

					if ($field == 'password') $this->isPasswordCorrect($value[0],$value[1]);
	
				}

			}
			
			if (count((array)$this->errors) == 0) $this->addMessage('Ok');

			$this->printPage();

		}

	}

