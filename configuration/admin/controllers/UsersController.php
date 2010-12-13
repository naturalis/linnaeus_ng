<?php

/*
    - replace hard coded role_id's
    - user.active is project wide, but can be set by specific project admins...

*/

include_once ('Controller.php');

class UsersController extends Controller
{
    
    public $usedModels = array(
        'user', 
        'right', 
        'role', 
        'project_role_user', 
        'right_role',
        'timezone',
        'module', 
        'module_project', 
        'free_module_project', 
        'module_project_user', 
        'free_module_project_user', 
    );
    
	public $jsToLoad = array('all' => array('user.js'));

    public $controllerPublicName = 'User administration';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();
		
		$this->isMultiLingual = false;
    
    }



    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }



    /**
     * Login page and function
     *
     * See function code for detailed comments on the function's flow
     *
     * @access    public
     */
    public function loginAction ()
    {

        // user previously set remember me: auto-login
        $u = $this->getRememberedUser();

         if ($u) {

            $this->doLogin($u[0],true);

            // determine and redirect to the default start page after logging in
            $this->redirect($this->getLoginStartPage());

        }

        $this->setPageName(_('Login'));
       
        $this->smarty->assign('excludeLogout', true);
       
        // check wheter the user has entered a username and/or password
        if ($this->rHasVal('username') || $this->rHasVal('password')) {
            
            // get data of any active user based on entered username and password
            $users = $this->models->User->_get(
				array(
					'id' => array(
						'username' => $this->requestData['username'], 
						'password' => $this->userPasswordEncode($this->requestData['password']), 
						'active' => '1'
					)
				)
			);
            
            if (count((array) $users) != 1) {
            // no user found
                
                $this->addError(_('Login failed.'));

            } else {
            // user found

                $this->doLogin($users[0],(isset($this->requestData['remember_me']) && $this->requestData['remember_me'] == '1'));

                // determine and redirect to the default start page after logging in
                $this->redirect($this->getLoginStartPage());
            
            }
        
        }
        
        $this->printPage();
    
    }


    /**
     * Logging out
     *
     * @access    public
     */
    public function logoutAction ()
    {
        
        $this->setPageName(_('Logout'));
        
        $this->destroyUserSession();
        
        $this->unsetRememberMeCookie();

        $this->redirect('login.php');
    
    }


    /**
     * Choosing the active project
     *
     * @access    public
     */
    public function chooseProjectAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName(_('Select a project to work on'));
        
        if ($this->rHasVal('project_id')) {

            if ($this->isCurrentUserAuthorizedForProject($this->requestData['project_id'])) {
                
                $this->setCurrentProjectId($this->requestData['project_id']);

                $this->setCurrentProjectData($this->models->Project->_get(array('id'=>$this->getCurrentProjectId())));

                $this->redirect($this->getLoggedInMainIndex());
            
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
     * @access    public
     */
    public function createAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Create new collaborator'));

        if ($this->rHasVal('username')) {
        // data was submitted
		
			$_SESSION['data']['new_user'] = $this->requestData;

			$saveUser = true;
			
			$this->requestData = $this->models->User->sanatizeData($this->requestData);
			
			// check data validity etc.
			if (!$this->isUserDataComplete()) $saveUser = false;
			
			if (!$this->isUserDataCorrect(null,true)) $saveUser = false;
			
			if (!$this->isUsernameUnique()) $saveUser = false;
			
			if ($saveUser) {

                // see if similar collaborators might exist, based on identical name, or identical email address
                $sim = $this->getSimilarUsers();
				
				$saveUser = count((array)$sim) == 0;

                // if there are similar users...
                if (!$saveUser) {

					$this->smarty->assign('existingUser', $sim[0]);

					$_SESSION['data']['new_user']['existing_user_id'] = $sim[0]['id'];

					// ...it might be because of his name...
					if ($this->isEmailAddressUnique(false, false, true)) {

						$this->smarty->assign('existingUserReason', 'same name');

					} else {
					// ...or because of his email address (or both)

						$pru = $this->models->ProjectRoleUser->_get(
							array(
								'id' => array(
									'project_id' => $this->getCurrentProjectId(),
									'user_id' => $sim[0]['id']
								),
								'columns' => 'count(*) as total'
							)
						);
						
						if ($pru[0]['total']>0) {
	
							$this->addError(_('A user with that e-mail address already exists in your project.'));
	
							$this->smarty->assign('existingUser', false);
	
						} else {

							$this->smarty->assign('existingUserReason', 'same email');

						}

					}
	
				} else {

                    $this->smarty->assign('existingUser', false);

					// make sure an unassignable role (like system admin) wasn't injected
					$r = $this->models->Role->_get(array('id'=>$this->requestData['role_id']));

					$saveUser = ($r['assignable'] != 'n');

					if (!$saveUser) {
					// if unassignable, raise error
						
						$this->addError(_('Unassignable role selected.'));
					
					} else {
					// save new user					

						if ($this->saveNewUser($this->requestData)) {
						
							$this->redirect('index.php');
						
						}
					
					}

				}
					
			}

        }

        $modules = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				), 
				'order' => 'module_id asc'
			)
		);

        foreach ((array) $modules as $key => $val) {
            
            $mp = $this->models->Module->_get(array('id'=>$val['module_id']));
            
            $modules[$key]['module'] = $mp['module'];
            
            $modules[$key]['description'] = $mp['description'];
            
        }
        
        $freeModules = $this->models->FreeModuleProject->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

		$maxLengths = array(
			'username' => $this->controllerSettings['dataChecks']['username']['maxLength'],
			'password' => $this->controllerSettings['dataChecks']['password']['maxLength'],
			'first_name' => $this->controllerSettings['dataChecks']['first_name']['maxLength'],
			'last_name' => $this->controllerSettings['dataChecks']['last_name']['maxLength'],
			'email_address' => $this->controllerSettings['dataChecks']['email_address']['maxLength'],
		);

		$zones = $this->models->Timezone->_get(array('id'=>'*'));

        $roles = $this->models->Role->_get(array('id'=>array('assignable' => 'y')));


		$this->smarty->assign('maxLengths', $maxLengths);

		$this->smarty->assign('zones', $zones);

        $this->smarty->assign('roles', $roles);
        
        $this->smarty->assign('modules', $modules);

        $this->smarty->assign('freeModules', $freeModules);

        if (isset($this->requestData)) $this->smarty->assign('data', $this->requestData);
        
        $this->printPage();
    
    }


    /**
     * Overview of all collaborators in the current project
     *
     * @access    public
     */
    public function indexAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Project collaborator overview'));
        
        // get all collaborators for the current project
        $pru = $this->models->ProjectRoleUser->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'role_id !=' => '1'
				),
				'columns' => 'distinct user_id, role_id,
					if(active=1,"active","blocked") as status,
					concat(datediff(curdate(),created)," '._('days').'") as days_active,
					ifnull(last_project_select,"'._('(has never logged in)').'") as last_login'
			)
		);

        // get full details, as well as roles for each collaborator
        foreach ((array) $pru as $key => $val) {

            $u = $this->models->User->_get(array('id'=>$val['user_id']));

            $r = $this->models->Role->_get(array('id'=>$val['role_id']));
            
            $u['role'] = $r['role'];
            $u['role_id'] = $r['id'];
            $u['status'] = $val['status'];
            $u['days_active'] = $val['days_active'];
            $u['last_login'] = $val['last_login'];

            $users[] = $u;
        
        }
        
        // user requested a sort of the table
        if ($this->rHasVal('key')) {

            $sortBy = array(
                'key' => $this->requestData['key'], 
                'dir' => ($this->requestData['dir'] == 'asc' ? 'desc' : 'asc'), 
                'case' => 'i'
            );
        
        }
        // default sort order
        else {
            
            $sortBy = array(
                'key' => 'username', 
                'dir' => 'asc', 
                'case' => 'i'
            );
        
        }
        
        // sort array of collaborators
        $this->customSortArray($users, $sortBy);
        
        $this->smarty->assign('sortBy', $sortBy);
        
        $this->smarty->assign('users', $users);

        $this->smarty->assign('columnsToShow',
            array(
                array('name'=> 'username', 'label' => _('username'),'align' => 'left'),
                array('name'=> 'status', 'label' => _('status'),'align' => 'left'),
                array('name'=> 'role', 'label'  => _('role'),'align' => 'left'),
                array('name'=> 'days_active', 'label' => _('collaborator for'),'align' => 'right'),
                array('name'=> 'last_login', 'label'  => _('last access'),'align' => 'right'),
            )
        );

        $this->printPage();
    
    }


    /**
     * Viewing data of a collaborator
     *
     * @access    public
     */
    public function viewAction ()
    {
        
        $this->checkAuthorisation();
        
        $this->setPageName(_('Project collaborator data'));
        
        if ($this->isUserPartOfProject($this->requestData['id'], $this->getCurrentProjectId())) {
            
            $user = $this->models->User->_get(array('id'=>$this->requestData['id']));
            
            $upr = $this->getUserProjectRole($this->requestData['id'], $this->getCurrentProjectId());

            $zone = $this->models->Timezone->_get(array('id'=>$user['timezone_id']));
	
			$modules = $this->models->ModuleProject->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					),
					'order' => 'module_id asc'
				)
			);
	
			foreach ((array) $modules as $key => $val) {
				
				$mp = $this->models->Module->_get(array('id'=>$val['module_id']));
				
				$modules[$key]['module'] = $mp['module'];
				
				$modules[$key]['description'] = $mp['description'];

				$mpu = $this->models->ModuleProjectUser->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'module_id' => $val['module_id'],
							'user_id' => $this->requestData['id'],			
						),
						'columns' => 'count(*) as total'
					)
				);

				$modules[$key]['isAssigned'] = $mpu[0]['total']=='1';

			}
			
			$freeModules = $this->models->FreeModuleProject->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

			foreach ((array) $freeModules as $key => $val) {
				
				$fpu = $this->models->FreeModuleProjectUser->_get(
					array(
						'id'=>array(
							'project_id' => $this->getCurrentProjectId(), 
							'free_module_id' => $val['id'],
							'user_id' => $this->requestData['id'],			
						),
						'columns' => 'count(*) as total'
					)
				);
				
				$freeModules[$key]['isAssigned'] = $fpu[0]['total']=='1';

			}
			            
            $this->smarty->assign('modules', $modules);

            $this->smarty->assign('freeModules', $freeModules);

            $this->smarty->assign('zone', $zone);

            $this->smarty->assign('data', $user);
            
            $this->smarty->assign('userRole', $upr);
            
            $this->printPage();
        
        }
        else {
            
            $this->redirect();
        
        }
    
    }


    /**
     * Editing collaborator data
     *
     * See function code for detailed comments on the function's flow
     *
     * @access    public
     */
    public function editAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Edit project collaborator'));

        // check whether the collaborator to be edited is part of the current project (avoid injected id)
        if ($this->isUserPartOfProject($this->requestData['id'], $this->getCurrentProjectId())) {
            
            // user requested delete
            if ($this->rHasVal('delete','1')) {
                
				$pru = $this->models->ProjectRoleUser->_get(
					array(
						'id' => array(
							'user_id' => $this->requestData['id'], 
							'project_id' => $this->getCurrentProjectId()
						)
					)
				);
				
				if ($pru[0]['role_id'] != 2) {
				
					$this->deleteUser($this->requestData['id']);

				} else {

					$this->addError(_('Cannot delete lead expert.'));
	
				}

				// redirect user to overview of remaining collaborators
				$this->redirect('index.php');                
                
            } elseif ($this->rHasVal('checked','1')) {
            // user requested data update
                
                // make sure an unassignable role (like system admin) wasn't injected
                if ($this->rHasVal('role_id')) $r = $this->models->Role->_get(array('id'=>$this->requestData['role_id']));
                
                if (isset($r) && $r['assignable'] == 'n') {
                    
                    $this->addError(_('Unassignable role selected'));
                    
                    $saveUser = false;
                
                } else {
                    
                    $saveUser = true;
                    
                    // clean up data
                    $this->requestData = $this->models->User->sanatizeData($this->requestData);
                    
                    // if no new passwords were entered, don't do a password check...
                    if ($this->requestData['password'] == '' &&
                     $this->requestData['password_2'] == '') {
                        
                        if (!$this->isUserDataComplete(array(
                            'password', 
                            'password_2'
                        )))
                            $saveUser = false;
                        
                        if (!$this->isUserDataCorrect(array(
                            'password', 
                            'password_2'
                        )))
                            $saveUser = false;
                    
                    }
                    // ...otherwise do a full check
                    else {
                        
                        if (!$this->isUserDataComplete())
                            $saveUser = false;
                        
                        if (!$this->isUserDataCorrect())
                            $saveUser = false;
                    
                    }
                    
                    // check whether data is unique; passing the collaborator's id avoids conflict with himself
                    if (!$this->isUserDataUnique($this->requestData['id'])) $saveUser = false;
                
                }
                
                // data ok, can be saved
                if ($saveUser) {
                    
                    // if new password, encrypt the human readable to an encrypted one
                    if ($this->requestData['password'] != '') {
                        
                        $this->requestData['password'] = $this->userPasswordEncode($this->requestData['password']);
                    
                    } else {
                        
                        unset($this->requestData['password']);
                    
                    }
                    
                    // get the current role of the collaborator in the current project
                    $upr = $this->getUserProjectRole(
                    $this->requestData['id'], $this->getCurrentProjectId());
                    
                    // if collaborator has a regular role, update to the new role...
                    if ($upr['role_id'] != 1 && $upr['role_id'] != 2) {
                        
                        $this->models->ProjectRoleUser->save(
							array(
								'id' => $this->requestData['userProjectRole'], 
								'user_id' => $this->requestData['id'], 
								'project_id' => $this->getCurrentProjectId(), 
								'role_id' => $this->requestData['role_id'],
								'active' => $this->requestData['active']
							)
						);

                    } else {
                    // ... but the role of lead expert or system admin cannot be changed, nether can he be made inactive
                        
                        $this->requestData['active'] = 1;
                    
                    }

					$this->saveUsersModuleData($this->requestData,$this->requestData['id'],true);
                    
                    // save the new data
					/*
						'active' was moved to the ProjectRoleUser table but the column in User does still exist
					*/
					$d = $this->requestData['active'];
					$this->requestData['active'] = 1;
                    $this->models->User->save($this->requestData);
                    $this->requestData['active'] = $d;
					
                    $this->addMessage(_('User data saved'));
                
                }
                // user cannot be saved
                else {
                    
                    $user = $this->requestData;
                
                }
            
            }
            
            // assign all data and print success or errors
            $user = $this->models->User->_get(array('id'=>$this->requestData['id']));
            
            $upr = $this->getUserProjectRole($this->requestData['id'], $this->getCurrentProjectId());
            
            $roles = $this->models->Role->_get(array('id'=>array('assignable' => 'y')));

            $zones = $this->models->Timezone->_get(array('id' => '*'));

			$modules = $this->models->ModuleProject->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId()
					), 
					'order' => 'module_id asc'
				)
			);
	
			foreach ((array) $modules as $key => $val) {
				
				$mp = $this->models->Module->_get(array('id'=>$val['module_id']));
				
				$modules[$key]['module'] = $mp['module'];
				
				$modules[$key]['description'] = $mp['description'];

				$mpu = $this->models->ModuleProjectUser->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'module_id' => $val['module_id'],
							'user_id' => $this->requestData['id'],			
						),
						'columns' => 'count(*) as total'
					)
				);

				$modules[$key]['isAssigned'] = $mpu[0]['total']=='1';

			}
			
			$freeModules = $this->models->FreeModuleProject->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

			foreach ((array) $freeModules as $key => $val) {
				
				$fpu = $this->models->FreeModuleProjectUser->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(), 
							'free_module_id' => $val['id'],
							'user_id' => $this->requestData['id'],			
						),
						'columns' => 'count(*) as total'
					)
				);
				
				$freeModules[$key]['isAssigned'] = $fpu[0]['total']=='1';

			}

            $this->smarty->assign('isLeadExpert', $upr['role_id'] == 2);

            $this->smarty->assign('zones', $zones);

			$this->smarty->assign('modules', $modules);
	
			$this->smarty->assign('freeModules', $freeModules);

            $this->smarty->assign('roles', $roles);
            
            $this->smarty->assign('data', $user);
            
            $this->smarty->assign('userRole', $upr);
            
			$this->printPage();        

        } else {
            
            $this->redirect();
        
        }

    }


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
		
		$idsToIgnore = isset($this->requestData['id_to_ignore']) ? $this->requestData['id_to_ignore'] : null;

        if ($this->rHasVal('action','check_username')) {

			if (in_array('f',$this->requestData['tests'])) {

				$this->isUsernameCorrect($this->requestData['values'][0]);

			}
			if (in_array('e',$this->requestData['tests'])) {

				$this->isUsernameUnique($this->requestData['values'][0], $idsToIgnore);

			}
        
		} else
        if ($this->rHasVal('action','check_password')) {

			if (in_array('f',$this->requestData['tests'])) {

//				$this->isPasswordCorrect($this->requestData['values'][0]);
				$this->getPasswordStrength($this->requestData['values'][0]);

			}
        
		} else
        if ($this->rHasVal('action','check_passwords')) {

			if (in_array('f',$this->requestData['tests'])) {

				$this->isPasswordCorrect($this->requestData['values'][0]);

			}
			if (in_array('q',$this->requestData['tests'])) {

				$this->arePasswordsIdentical($this->requestData['values'][0],$this->requestData['values'][1]);

			}
        
		} else
        if ($this->rHasVal('action','check_email_address')) {

			if (in_array('e',$this->requestData['tests'])) {

				$this->isEmailAddressUnique($this->requestData['values'][0], $idsToIgnore);

			}
			if (in_array('f',$this->requestData['tests'])) {

				$this->isEmailAddressCorrect($this->requestData['values'][0]);

			}
        
		} else
        if ($this->rHasVal('action','check_first_name') || $this->rHasVal('action','check_last_name')) {

			if (in_array('f',$this->requestData['tests'])) {

				if (strlen($this->requestData['values'][0]) == 0) $this->addError(_('Missing value.'));
			
			}
        
		} else
        if ($this->rHasVal('action','connect_existing')) {
            
            $this->ajaxActionConnectExistingUser();
        
		} else
        if ($this->rHasVal('action','create_from_session')) {
            
            $this->ajaxActionCreateUserFromSession();
        
		}

        $this->printPage();

    }


    /**
     * Creating a new collaborator
     *
     * See function code for detailed comments on the function's flow
     *
     * @access    public
     */
    public function adminIndexAction ()
    {

        $this->checkAuthorisation();
        
        $this->setPageName(_('Project overview'));

		$modules = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				), 
				'order' => 'module_id asc'
			)
		);

		foreach ((array) $modules as $key => $val) {
			
			$mp = $this->models->Module->_get(array('id'=>$val['module_id']));
			
			$modules[$key]['icon'] = $mp['icon'];
			$modules[$key]['module'] = $mp['module'];
			$modules[$key]['controller'] = $mp['controller'];

		}

		$freeModules = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

        $this->smarty->assign('modules',$modules);

        $this->smarty->assign('freeModules',$freeModules);

        $this->smarty->assign('currentRole',$this->getCurrentUserCurrentRole());

        $this->printPage();
    
    }


    /**
     * Retrieves all rights and roles of the current user
     *
     * Is called directly after log in. Results are stored in the user's session.
     *
     * @return     array    array of roles, rights and the number of projects the user is involved with
     * @access     private
     */
    private function getCurrentUserRights ($id = false)
    {
        
        $pru = $this->models->ProjectRoleUser->_get(array('id'=>array('user_id' => $id ? $id : $this->getCurrentUserId())));
        
        foreach ((array) $pru as $key => $val) {
            
            $p = $this->models->Project->_get(array('id'=>$val['project_id']));
            
            $pru[$key]['project_name'] = $p['sys_name'];
            
            $r = $this->models->Role->_get(array('id'=>$val['role_id']));
            
            $pru[$key]['role_name'] = $r['role'];
            
            $pru[$key]['role_description'] = $r['description'];
            
            $rr = $this->models->RightRole->_get(array('id'=>array('role_id' => $val['role_id'])));
            
            foreach ((array) $rr as $rr_key => $rr_val) {
                
                $r = $this->models->Right->_get(array('id'=>$rr_val['right_id']));
                
                $rs[$val['project_id']][$r['controller']][$r['id']] = $r['view'];
            
            }
            
            $d[$val['project_id']] = $val['project_id'];
        
        }
        
        return array(
            'roles' => $pru, 
            'rights' => $rs, 
            'number_of_projects' => count((array) $d)
        );
    
    }

	private function ajaxActionConnectExistingUser()
	{

		if (
			!isset($_SESSION['data']['new_user']['role_id']) ||
			!isset($_SESSION['data']['new_user']['existing_user_id'])
		) return;


		$this->models->ProjectRoleUser->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'user_id' => $_SESSION['data']['new_user']['existing_user_id']
			)
		);

		// save new role only for existing collaborator and new project
		$pru = $this->models->ProjectRoleUser->save(
			array(
				'id' => null, 
				'project_id' => $this->getCurrentProjectId(), 
				'role_id' => $_SESSION['data']['new_user']['role_id'], 
				'user_id' => $_SESSION['data']['new_user']['existing_user_id']
			)
		);
		
		if (!$pru) {

			$this->addError(_('Failed to connect user from session.'));

		} else {

			unset($_SESSION['data']['new_user']);

		}

	}

	private function ajaxActionCreateUserFromSession ()
	{
	
		if (!isset($_SESSION['data']['new_user'])) return;
	
		$su = $this->saveNewUser($_SESSION['data']['new_user']);

		if (!$su) {

			$this->addError(_('Failed to create user from session.'));

		} else {

			unset($_SESSION['data']['new_user']);

		}

	}

	private function saveNewUser($data)
	{

		// encode passwords
		$data['password'] = $this->userPasswordEncode($data['password']);
		
		$data['active'] = '1';
		
		$data['id'] = null;
		
		$r = $this->models->User->save($data);
		
		if ($r !== true) {
			
			$this->addError(_('Failed to save user.'),2);

			$this->log(serialize($data));
			
			return false;
			
		} else {
			
			// if saving was succesful, save new role
			$newUserId = $this->models->User->getNewId();
			
			$this->models->ProjectRoleUser->save(
				array(
					'id' => null, 
					'project_id' => $this->getCurrentProjectId(), 
					'role_id' => $data['role_id'], 
					'user_id' => $newUserId,
					'active' => '1'
				)
			);


			$this->saveUsersModuleData($data,$newUserId); 

			return true;
		
		}
						
	}


	private function saveUsersModuleData($data,$userId,$deleteOld=false)
	{

		// is assigned modules are present, save those
		if (isset($data['modules'])) {

			if ($deleteOld) {

				$this->models->ModuleProjectUser->delete(
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'user_id' => $userId
					)
				);

			}

			foreach((array)$data['modules'] as $key => $val) {

				$this->models->ModuleProjectUser->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'module_id' => $val,
						'user_id' => $userId
					)
				);

			}

		}

		// is assigned free modules are present, save those
		if (isset($data['freeModules'])) {

			if ($deleteOld) {

				$this->models->FreeModuleProjectUser->delete(
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'user_id' => $userId
					)
				);

			}

			foreach((array)$data['freeModules'] as $key => $val) {

				$this->models->FreeModuleProjectUser->save(
					array(
						'id' => null, 
						'project_id' => $this->getCurrentProjectId(), 
						'free_module_id' => $val,
						'user_id' => $userId
					)
				);

			}

		}

	}


	private function deleteUser($id)
	{

		// delete collaborator's role from this project
		$this->models->ProjectRoleUser->delete(
			array(
				'user_id' => $id, 
				'project_id' => $this->getCurrentProjectId()
			)
		);

		$this->models->ModuleProjectUser->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'user_id' => $id
			)
		);

		$this->models->FreeModuleProjectUser->delete(
			array(
				'project_id' => $this->getCurrentProjectId(), 
				'user_id' => $id
			)
		);


		// avoiding orphans: see if collaborator is present in any other projects...
		$data = $this->models->ProjectRoleUser->_get(
			array(
				'id' => array(
					'user_id' => $id
				), 
				'columns' => 'count(*) as tot'
			)
		);
		
		// ...if not, delete entire collaborator record
		if (isset($data) && $data[0]['tot'] == '0') {

			$this->models->User->delete($id);
			$this->models->ModuleProjectUser->delete($id);
			$this->models->FreeModuleProjectUser->delete($id);

		}

	}


	private function getPasswordStrength($password)
	{

		$min = $this->controllerSettings['dataChecks']['password']['minLength'];
		$max = $this->controllerSettings['dataChecks']['password']['maxLength'];

		if (strlen($password) > $max) {

            $this->smarty->assign('returnText',sprintf(_('Password too long; should be between %s and %s characters.'),$min,$max));

		} else
		if (strlen($password) < $min) {

            $this->smarty->assign('returnText',sprintf(_('Password too short; should be between %s and %s characters.'),$min,$max));

		} else
		if (strlen($password) < ($min + 3)) {

			$this->smarty->assign('returnText','<weak>');

		} else {
		
			if (
				preg_match_all('/[0-9]/',$password,$d)>=1 && 
				preg_match_all('/[a-zA-Z]/',$password,$d)>=1 && 
				preg_match_all('/[^a-zA-Z0-9]/',$password,$d)>=1
			) {

				$this->smarty->assign('returnText','<strong>');

			} else {

				$this->smarty->assign('returnText','<moderate>');

			}

		}

	}

    /**
     * Calls all the relevant methods to log the user in
     *
     * @param      array    $user    basic user data
     * @param      boolean    $remember    whether or not the user wants his being loged in to be remembered across sessions
     * @access     private
     */
    private function doLogin($user,$remember) 
    {

        // update last and number of logins
        $this->models->User->save(
            array(
                'id' => $user['id'], 
                'last_login' => 'now()', 
                'logins' => 'logins+1'
            )
        );

        // get user's roles and rights
        $cur = $this->getCurrentUserRights($user['id']);

        // save all relevant data to the session
        $this->setUserSession($user, $cur['roles'], $cur['rights'], $cur['number_of_projects']);
        
        // set 'remember me' cookie
        if ($remember) {
        
            $this->setRememberMeCookie();
        
        } else {
        
            $this->unsetRememberMeCookie();
        
        }
        
        // determine and set the default active project
        $this->setDefaultProject();

    }


    /**
     * Sets user's data in a session after logging in
     *
     * User data retrieved after logging in is stored in a session for faster access.
     * Data includes basic personal data, the user's various roles within projects,
     * the user's rights to see actual pages and the number of projects he is assigned to.
     *
     * @param      array    $userData    basic user data
     * @param      array    $roles    user's roles
     * @param      array    $rights    user's rights
     * @param      integer    $numberOfProjects    number of assigned projects
     * @access     public
     */
    private function setUserSession ($userData, $roles, $rights, $numberOfProjects )
    {
        
        if (!$userData) return;

        $userData['_login']['time'] = time();

        $userData['_roles'] = $roles;
        $userData['_rights'] = $rights;
        $userData['_number_of_projects'] = $numberOfProjects;
        $userData['_said_welcome'] = false;
        
        $_SESSION['user'] = $userData;
    
    }

    /**
     * Destroys a user's session (when logging out)
     *
     * @access     public
     */
    private function destroyUserSession ()
    {
        
        session_destroy();
    
    }


    private function setRememberMeCookie()
    {

        setcookie(
            $this->generalSettings['login-cookie']['name'], 
            $this->getCurrentUserId(), 
            time() + (86400 * $this->generalSettings['login-cookie']['lifetime'])
        );

    }


    private function getRememberMeCookie()
    {

        return isset($_COOKIE[$this->generalSettings['login-cookie']['name']]) ? $_COOKIE[$this->generalSettings['login-cookie']['name']] : false;

    }


    private function unsetRememberMeCookie()
    {

        setcookie(
            $this->generalSettings['login-cookie']['name'], 
            false, 
            time() - 86400
        );

    }
    
    private function getRememberedUser()
    {

        $c = $this->getRememberMeCookie();

        if ($c) {

            return $this->models->User->_get(
				array(
					'id' => array(
						'id' => $c,
						'active' => '1'
					)
				)
            );

        } else {

            return false;
    
        }

    }


    /**
     * Finds out if a collaborator has a role within the specified project
     *
     * @param      string    $userId    id of the user to find
     * @param      string    $projectId    id of the project to find
     * @return     boolean    collaborator is part of the project, or not
     * @access     private
     */
    private function isUserPartOfProject ($user, $project)
    {
        
        $pru = $this->models->ProjectRoleUser->_get(
			array(
				'id'=> array(
					'user_id' => $user, 
					'project_id' => $project
				)
			)
		);
        
        return count((array) $pru) != 0;
    
    }



    /**
     * Retrieves a collaborator's role within the specified project
     *
     * @param      string    $userId    id of the user to find
     * @param      string    $projectId    id of the project to find
     * @return     array    role of user
     * @access     private
     */
    private function getUserProjectRole ($userId, $projectId)
    {
        
        $pru = $this->models->ProjectRoleUser->_get(
			array(
				'id'=> array(
					'user_id' => $userId, 
					'project_id' => $projectId
				)
        	)
		);
        
        if ($pru) {
            
            $r = $this->models->Role->_get(array('id'=>$pru[0]['role_id']));
            
            $pru[0]['role'] = $r;
        }
        
        return $pru[0];
    
    }



    /**
     * Encodes a user's password for storing or checking against the database when logging in
     *
     * Currently md5 is used as encoding function
     *
     * @param      string    $p    the password
     * @return     string    as 32 byte md5 hash
     * @access     private
     */
    private function userPasswordEncode ($p)
    {
        
        return md5($p);
    
    }



    /**
     * Verifies if the user data that has been entered is complete 
     *
     * @param      array    $fieldsToIgnore    fields that might be in the data, but need not be checked
     * @return     boolean    data is complete or not
     * @access     private
     */
    private function isUserDataComplete ($fieldsToIgnore = array())
    {
        
        $result = true;
        
        if (!in_array('username', $fieldsToIgnore) && $this->requestData['username'] == '') {
            
            $this->addError(_('Missing username.'));
            
            $result = false;
        
        }
        
        if (!in_array('password', $fieldsToIgnore) && $this->requestData['password'] == '') {
            
            $this->addError(_('Missing password.'));
            
            $result = false;
        
        }
        
        if (!in_array('password_2', $fieldsToIgnore) && $this->requestData['password_2'] == '') {
            
            $this->addError(_('Missing password repeat.'));
            
            $result = false;
        
        }
        

        if (!in_array('first_name', $fieldsToIgnore) && $this->requestData['first_name'] == '') {
            
            $this->addError(_('Missing first name.'));
            
            $result = false;
        
        }
        
        if (!in_array('last_name', $fieldsToIgnore) && $this->requestData['last_name'] == '') {
            
            $this->addError(_('Missing last name.'));
            
            $result = false;
        
        }
        
        if (!in_array('email_address', $fieldsToIgnore) && $this->requestData['email_address'] == '') {
            
            $this->addError(_('Missing email address.'));
            
            $result = false;
        
        }
        
        return $result;
    
    }



    /**
     * Check whether a username qualifies as correct
     *
     * Looks currently only at length constraints (2 <= length <= 32)
     *
     * @param      string    $username    username to check; if absent, username is taken from the request variables
     * @return     boolean    username is correct or not
     * @access     private
     * @todo        a more complete check
     */
    private function isUsernameCorrect ($username = false,$ignoreEmptiness = false)
    {
        
		$min = $this->controllerSettings['dataChecks']['username']['minLength'];
		$max = $this->controllerSettings['dataChecks']['username']['maxLength'];
		
        if (!$username)
            $username = isset($this->requestData['username']) ? $this->requestData['username'] : null;

		if (empty($username) && $ignoreEmptiness) return;
        
        $result = true;
        
        if (strlen($username) < $min) {
            
            $this->addError(sprintf(_('Username too short; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (strlen($username) > $max) {
            
            $this->addError(sprintf(_('Username too long; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (
			isset($this->controllerSettings['dataChecks']['username']['regexp']) && 
			!preg_match($this->controllerSettings['dataChecks']['username']['regexp'],$username)
		) {
            
            $this->addError(_('Username has incorrect format.'));
            
            $result = false;
        
        }
        
        return $result;
    
    }



    /**
     * Check whether a password qualifies as correct
     *
     * Looks currently only at length constraints (5 <= length <= 16)
     *
     * @param      string    $password    password to check; if absent, password is taken from the request variables
     * @param      string    $password_2    second password from user data form; idem.
     * @return     boolean    password is correct (and identical if two were supplied) or not 
     * @access     private
     * @todo        a more complete check
     */
    private function isPasswordCorrect ($password = false)
    {

		$min = $this->controllerSettings['dataChecks']['password']['minLength'];
		$max = $this->controllerSettings['dataChecks']['password']['maxLength'];

        if (!$password)
            $password = isset($this->requestData['password']) ? $this->requestData['password'] : null;

        $result = true;
        
        if (strlen($password) < $min) {
            
            $this->addError(sprintf(_('Password too short; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (strlen($password) > $max) {
            
            $this->addError(sprintf(_('Password too long; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (
			isset($this->controllerSettings['dataChecks']['password']['regexp']) && 
			!preg_match($this->controllerSettings['dataChecks']['password']['regexp'],$password)
			)  {

            $this->addError(_('Password has incorrect format.'));
            
            $result = false;
        
        }
        

        return $result;
    
    }

    private function arePasswordsIdentical ($password = false, $password_2 = false)
    {

        if (!$password)
            $password = isset($this->requestData['password']) ? $this->requestData['password'] : null;

        if (!$password_2)
            $password_2 = isset($this->requestData['password_2']) ? $this->requestData['password_2'] : null;
        
        if ($password_2 != '' && ($password != $password_2)) {
            
            $this->addError(_('Passwords not the same.'));
            
			return false;        
        }
		
		return true;

	}


    /**
     * Check whether an e-mail address qualifies as correct
     *
     * Uses reg exp mask: /^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/
     *
     * @param      string    $email_address    address to check; if absent, username is taken from the request variables
     * @return     boolean    address is correct or not
     * @access     private
     */
    private function isEmailAddressCorrect ($email_address = false,$ignoreEmptiness = false)
    {
        
        if (!$email_address)
            $email_address = isset($this->requestData['email_address']) ? $this->requestData['email_address'] : null;
        
		if (empty($email_address) && $ignoreEmptiness) return;

		$min = $this->controllerSettings['dataChecks']['email_address']['minLength'];
		$max = $this->controllerSettings['dataChecks']['email_address']['maxLength'];
		
        $result = true;
        
        if (strlen($email_address) < $min) {
            
            $this->addError(sprintf(_('E-mail adress too short; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (strlen($email_address) > $max) {
            
            $this->addError(sprintf(_('E-mail adress too long; should be between %s and %s characters.'),$min,$max));
            
            $result = false;
        
        } else
        if (
			isset($this->controllerSettings['dataChecks']['email_address']['regexp']) && 
			!preg_match($this->controllerSettings['dataChecks']['email_address']['regexp'],$email_address)
			)  {
            
            $this->addError(_('Invalid e-mail address.'));
            
            $result = false;
        
        }
        
        return $result;
    
    }



    /**
     * Tests whether userdata (username and emailaddress) is correct
     *
     * @param      array    $fieldsToIgnore    fields that might be in the data, but need not be checked
     * @return     boolean    unique or not
     * @access     private
     */
    
    private function isUserDataCorrect ($fieldsToIgnore = null,$ignoreEmptiness = false)
    {

        if ($fieldsToIgnore == null) $fieldsToIgnore = array();

        $result = true;
        
        if (!in_array('username', $fieldsToIgnore)) {
            if (!$this->isUsernameCorrect(false,$ignoreEmptiness))
                $result = false;
        }
        
        if (!in_array('password', $fieldsToIgnore)) {
            if (!$this->isPasswordCorrect())
                $result = false;
        }
        
        if (!in_array('email_address', $fieldsToIgnore)) {
            if (!$this->isEmailAddressCorrect(false,$ignoreEmptiness))
                $result = false;
        }
        
        return $result;
    
    }



    /**
     * Tests whether username is unique in the database
     *
     * @param      string    $username    username to check; if false, it is the 'username' var from the request data that is tested
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @return     boolean    unique or not
     * @access     private
     */
    private function isUsernameUnique ($username = false, $idToIgnore = false)
    {
        
        if (!$username)
            $username = isset($this->requestData['username']) ? $this->requestData['username'] : null;
        
        $result = true;
        
        if ($username == '') {
            
            $result = false;
        
        } else {
            
            if ($idToIgnore) {
                
                $w = array(
                    'username' => $username, 
                    'id !=' => $idToIgnore
                );
            
            } else {
                
                $w = array(
                    'username' => $username
                );
            
            }
            
            $users = $this->models->User->_get(array('id'=>$w));
            
            if (count((array) $users) != 0) {
                
                $this->addError(_('Username already exists.'));
                
                $result = false;
            
            }
        
        }
        
        return $result;
    
    }



    /**
     * Tests whether emailaddress is unique in the database
     *
     * @param      string    $email_address    address to check; if false, it is the 'email_address' var from the request data that is tested
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @param      boolean    $suppress_error    if true, function just returns result and adds no error
     * @return     boolean    unique or not
     * @access     private
     */
    private function isEmailAddressUnique ($email_address = false, $idToIgnore = false, $suppress_error = false)
    {
        
        if (!$email_address)
            $email_address = isset($this->requestData['email_address']) ? $this->requestData['email_address'] : null;
        
        $result = true;
        
        if ($email_address == '') {
            
            $result = false;
        
        } else {
            
            if ($idToIgnore) {
                
                $w = array(
                    'email_address' => $email_address, 
                    'id !=' => $idToIgnore
                );
            
            } else {
                
                $w = array(
                    'email_address' => $email_address
                );
            
            }
            
            $users = $this->models->User->_get(array('id'=>$w));
            
            if (count((array) $users) != 0) {
                
                if (!$suppress_error)
                    $this->addError(_('E-mail address already exists.'));
                
                $result = false;
            
            }
        
        }
        
        return $result;
    
    }



    /**
     * Tests whether userdata (username and emailaddress) is unique in the database
     *
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @return     boolean    unique or not
     * @access     private
     */
    private function isUserDataUnique ($idToIgnore = false)
    {
        
        $result = true;
        
        if (!$this->isUsernameUnique(false, $idToIgnore))
            $result = false;
        
        if (!$this->isEmailAddressUnique(false, $idToIgnore))
            $result = false;
        
        return $result;
    
    }



    /**
     * Finds existing users in the database, based on mathcing name and/or emailaddress
     *
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @return     array    array of users
     * @access     private
     */
    private function getSimilarUsers ($idToIgnore = false)
    {
        
        $q = "select * from %table% where 
                    ((lower(first_name) = '" . $this->models->User->escapeString(strtolower($this->requestData['first_name'])) . "'
                    and lower(last_name) = '" . $this->models->User->escapeString(strtolower($this->requestData['last_name'])) . "')
                    or email_address = '" . $this->models->User->escapeString($this->requestData['email_address']) . "')" . ($idToIgnore ? " and id !=" . $idToIgnore : '');
        
        $users = $this->models->User->_get(array('id'=>$q));
        
        return $users;
    
    }

}

