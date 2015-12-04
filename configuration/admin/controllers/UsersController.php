<?php

/*
	ISSUE
	user.active is system wide (i.e., trans-project), but can be set by specific project admins

	send e-mail when added to a project

*/

include_once ('Controller.php');

class UsersController extends Controller
{

    public $usedModels = array();

	public $cssToLoad = array(
        'users.css',
    	'lookup.css',
    	'dialog/jquery.modaldialog.css'
	);

	public $jsToLoad = array(
	   'all' => array(
	       'user.js',
    	   'lookup.js',
    	   'dialog/jquery.modaldialog.js'
	));

    public $controllerPublicName = 'User administration';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
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
     * Overview of all collaborators in the current project
     *
     * @access    public
     */
    public function indexAction ()
    {

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Project collaborator overview'));

        // get all collaborators for the current project
        $pru = $this->models->ProjectsRolesUsers->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'role_id !=' => '1'
				),
				'columns' => 'distinct user_id, role_id,
					if(active=1,"active","blocked") as status,
					concat(datediff(curdate(),created)," '.$this->translate('days').'") as days_active,
					ifnull(date_format(last_project_select,\'%d-%m-%Y\'),"-") as last_login'
			)
		);

        // get full details, as well as roles for each collaborator
        foreach ((array) $pru as $key => $val)
		{
            $u = $this->models->Users->_get(array('id'=>$val['user_id']));
            $r = $this->models->Roles->_get(array('id'=>$val['role_id']));

            $u['role'] = $r['role'];
            $u['role_id'] = $r['id'];
            $u['status'] = $val['status'];
            $u['days_active'] = $val['days_active'];
            $u['last_login'] = $val['last_login'];

            $users[] = $u;
        }

        if ($this->rHasVal('key'))
		{
	        // user requested a sort of the table
            $sortBy = array(
                'key' => $this->rGetVal('key'),
                'dir' => ($this->rGetVal('dir') == 'asc' ? 'desc' : 'asc'),
                'case' => 'i'
            );

        }
        else
		{
	        // default sort order
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
        $this->smarty->assign('isSysAdmin', $this->isCurrentUserSysAdmin());
        $this->smarty->assign('columnsToShow',
            array(
                array('name'=> 'first_name', 'label' => $this->translate('first name'),'align' => 'left'),
                array('name'=> 'last_name', 'label' => $this->translate('last name'),'align' => 'left'),
                array('name'=> 'username', 'label' => $this->translate('username'),'align' => 'left'),
                array('name'=> 'email_address', 'label' => $this->translate('e-mail'),'align' => 'left'),
                array('name'=> 'role', 'label'  => $this->translate('role'),'align' => 'left'),
                array('name'=> 'last_login', 'label'  => $this->translate('last access'),'align' => 'right'),
                array('name'=> 'status', 'label' => $this->translate('status'),'align' => 'left'),
            )
        );

        $this->printPage();

    }

	private function authenticate( $username, $password )
	{
		$d=$this->getUserByCredentials( $username );

		if ( is_null($d) || !isset($d['password']) )
		{
			return false;
		}

		if (password_verify( $password, $d['password'] ) )
		{
			return true;
		}

		if ( $this->userPasswordEncode($password,true)==$d['password'] )
		{
			// update using new password algorithm
			$this->models->Users->update(
				array(
					'password'=>$this->userPasswordEncode($password)
				),
				array(
					'id'=>$d['id'],
					'username'=>$username,
				)
			);

			return true;
		}

		return false;
	}

	private function getUserByCredentials( $username, $password=null )
	{
		if ( empty($username) ) return;

		if ( isset($password) )
		{
			$d=$this->models->Users->_get(array('id'=>array('username' => $username, 'password' => $this->userPasswordEncode($password))));
		}
		else
		{
			$d=$this->models->Users->_get(array('id'=>array('username' => $username)));
		}

		return isset($d[0]) ? $d[0] : null;
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
        $u=$this->getRememberedUser();

         if ($u)
		 {
            $this->doLogin($u[0],true);
            // determine and redirect to the default start page after logging in
            $this->redirect($this->getLoginStartPage());
        }

        $this->setPageName($this->translate('Login'));

        $this->smarty->assign('excludeLogout', true);

		$this->includeLocalMenu = false;

        // check wheter the user has entered a username and password
        if ( $this->rHasVal('username') && $this->rHasVal('password') )
		{
			$auth=$this->authenticate( $this->rGetVal('username'), $this->rGetVal('password') );

            if ($auth)
			{
				$user=$this->getUserByCredentials( $this->rGetVal('username') );

				if ( isset($user) )
				{
					// user found
					$this->doLogin($user,(null!==$this->rGetVal('remember_me') && $this->rGetVal('remember_me')=='1'));

					// determine and redirect to the default start page after logging in
					$this->redirect($this->getLoginStartPage());
				}
				else
				{
					$this->addError($this->translate('Login failed.'));
				}
			}
			else
			{
                $this->addError($this->translate('Login failed.'));
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
        $this->setPageName($this->translate('Logout'));

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

        $this->setPageName($this->translate('Select a project to work on'));

		$this->includeLocalMenu = false;

        if ( $this->rHasVal('project_id') )
		{
			if ( $this->rGetVal('project_id') != $this->getCurrentProjectId() )
			{
				if ( $this->isCurrentUserAuthorizedForProject($this->rGetVal('project_id')) )
				{
					$this->unsetProjectSessionData();
					$this->setCurrentProjectId( $this->rGetVal('project_id') );
					$this->setCurrentProjectData();
					$this->setCurrentUserRoleId();
					$this->redirect( $this->getLoggedInMainIndex() );
				}
				else
				{
					$this->redirect('choose_project.php');
				}

			}
			else
			{
				$this->redirect($this->getLoggedInMainIndex());
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

        $this->setPageName($this->translate('Create new collaborator'));

        if ($this->rHasVal('action','create') && !$this->isFormResubmit())
		{
			$data=$this->sanitizeUserData($this->GetAll());

			if (
				$this->isUserDataComplete($data) &&
				$this->isUsernameCorrect($this->rGetVal('username')) &&
				$this->isUsernameUnique($this->rGetVal('username')) &&
				$this->arePasswordsIdentical($this->rGetVal('password'),$this->rGetVal('password_2')) &&
				$this->isPasswordCorrect($this->rGetVal('password')) &&
				$this->isEmailAddressCorrect($this->rGetVal('email_address')) &&
				$this->isEmailAddressUnique($this->rGetVal('email_address')) &&
				$this->isRoleAssignable($this->rGetVal('role_id'))
			)
			{
				$d = $this->saveNewUser($data);

				if ($d===true)
				{
					$this->sendNewUserEmail($data);
					$this->redirect('index.php');
				}
				else
				{
					$this->addError($this->translate('Failed to save user ('.$d.').'));
				}
			}
        }

		$maxLengths = array(
			'username' => $this->controllerSettings['dataChecks']['username']['maxLength'],
			'password' => $this->controllerSettings['dataChecks']['password']['maxLength'],
			'first_name' => $this->controllerSettings['dataChecks']['first_name']['maxLength'],
			'last_name' => $this->controllerSettings['dataChecks']['last_name']['maxLength'],
			'email_address' => $this->controllerSettings['dataChecks']['email_address']['maxLength'],
		);

		$modules = $this->getProjectModules();

		$zones = $this->models->Timezones->_get(array('id'=>'*'));

		if ($this->isCurrentUserSysAdmin())
		{
			$d=array('id !='=>ID_ROLE_SYS_ADMIN);
		}
		else
		{
			$d=array('assignable'=>'y');
		}

        $roles = $this->models->Roles->_get(array('id'=>$d));

		$this->smarty->assign('maxLengths', $maxLengths);
		$this->smarty->assign('zones', $zones);
        $this->smarty->assign('roles', $roles);
        $this->smarty->assign('modules', $modules['modules']);
        $this->smarty->assign('freeModules', $modules['freeModules']);

        if (null!==$this->GetAll() && isset($data) && $this->GetAll()!=$data)
		{
			$this->smarty->assign('data', $data);
		}
		else
        if (null!==$this->GetAll())
		{
			$this->smarty->assign('data', $this->GetAll());
		}

        $this->printPage();

    }


    /**
     * Overview of all users in the system
     *
     * @access    public
     */
    public function allAction ()
    {

		$this->checkAuthorisation(true);

		if ($this->getCurrentProjectId()==null) $this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setPageName($this->translate('All users'));

		$users = $this->models->Users->_get(array('id'=>'*','order' => 'last_name,first_name'));

        $userProjectCount = $this->models->ProjectsRolesUsers->_get(
			array(
				'id' => '*',
				'fieldAsIndex' => 'user_id',
				'columns' => 'count(distinct project_id) as total, user_id',
				'group' => 'user_id'
			)
		);

        $currentProjectUsers = $this->models->ProjectsRolesUsers->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'fieldAsIndex' => 'user_id',
			)
		);

		$pagination = $this->getPagination($users,20);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
		$this->smarty->assign('nextStart', $pagination['nextStart']);
		$this->smarty->assign('users',$pagination['items']);
        $this->smarty->assign('userProjectCount', $userProjectCount);
        $this->smarty->assign('currentProjectUsers', $currentProjectUsers);

        $this->printPage();

    }


    /**
     * Viewing data of a collaborator
     *
     * @access    public
     */
    public function viewAction ()
    {

        $this->checkAuthorisation($this->isCurrentUserSysAdmin());

        $this->setPageName($this->translate('Project collaborator data'));

		if ($this->rHasId())
		{
			$user = $this->getUserById($this->rGetId());

			if ($user==null)
			{
				$this->addError('Unknown user ID: '.$this->rGetId());
			}
			else
			{

	            $zone = $this->models->Timezones->_get(array('id'=>$user['timezone_id']));
				$currentRole = $this->getUserProjectRole($this->rGetId(),$this->getCurrentProjectId());
				$modules = $this->getProjectModules();

				$mpu = $this->models->ModulesProjectsUsers->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'user_id' => $this->rGetId(),
						),
						'columns' => 'module_id',
						'fieldAsIndex' => 'module_id'
					)
				);

				$fpu = $this->models->FreeModulesProjectsUsers->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'user_id' => $this->rGetId(),
						),
						'columns' => 'free_module_id',
						'fieldAsIndex' => 'free_module_id'
					)
				);

				$hasModules = false;

				foreach ((array) $modules['modules'] as $key => $val)
				{
					$modules['modules'][$key]['isAssigned'] = isset($mpu[$val['module_id']]);
					$hasModules = $hasModules || isset($fpu[$val['module_id']]);
				}

				foreach ((array) $modules['freeModules'] as $key => $val)
				{
					$modules['freeModules'][$key]['isAssigned'] = isset($fpu[$val['id']]);
					$hasModules = $hasModules || isset($fpu[$val['id']]);
				}

				$pru = $this->models->ProjectsRolesUsers->_get(
					array(
						'id' => array('user_id' => $this->rGetId()),
						'columns' => 'project_id,role_id'
					)
				);

				foreach((array)$pru as $key => $val)
				{
					$p = $this->models->Projects->_get(
						array(
							'id' => array('id' => $val['project_id']),
							'columns' => 'title'
						)
					);

					$pru[$key]['projectTitle'] = $p[0]['title'];
					$d = $this->getUserProjectRole($this->rGetId(),$val['project_id']);
					$pru[$key]['role'] = $d['role']['role'];
				}

				$this->smarty->assign('user',$user);
				$this->smarty->assign('currentRole',$currentRole);
				$this->smarty->assign('zone', $zone);
				$this->smarty->assign('modules', $modules);
				$this->smarty->assign('hasModules', $hasModules);
				$this->smarty->assign('projects', $pru);
			}
		}
		else
		{
			$this->addError('No ID specified');
		}

		$this->printPage();

    }

    public function deleteAction ()
    {

        $this->checkAuthorisation();

		if ($this->rHasId() && !$this->isFormResubmit())
		{
			$user = $this->getUserById($this->rGetId());

			//$canDelete = ($user['created_by']==$this->getCurrentUserId() || $this->isCurrentUserSysAdmin());
			$canDelete = $this->isCurrentUserSysAdmin();

			if ($canDelete)
			{
				$this->removeUserFromProject($this->rGetId(),$this->getCurrentProjectId());

				// conditional delete! can only delete when user is no longer part on *any* project
				if ($this->deleteUser($this->rGetId()))
				{
					$this->redirect('index.php');
				}
				else
				{
					$this->addMessage($this->translate('User removed from current project.'));
					$this->addError($this->translate('User could not be deleted, as he is active in other project(s).'));
				}
			}
			else
			{
				//$this->addError($this->translate('User can only be deleted by system admin or user record\'s creator.'));
				$this->addError($this->translate('User can only be deleted by system admin.'));
			}

		}
		else
		{
			$this->redirect('index.php');
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

		$this->setPageName($this->translate('Edit project collaborator'));

		if ($this->getCurrentUserRoleId() != ID_ROLE_SYS_ADMIN &&
			$this->getCurrentUserRoleId() != ID_ROLE_LEAD_EXPERT &&
			$this->getCurrentUserId() != $this->rGetId())
		{

			$this->addError($this->translate('You are not authorized to edit that user.'));
		}
		else
		{

			$user = $this->getUserById($this->rGetId());

			if ($this->rHasVal('action','update') && !$this->isFormResubmit())
			{
				$data=$this->sanitizeUserData($this->GetAll());

				$passwordsUnchanged = empty($data['password']) && empty($data['password_2']);

				if (
					$this->isUserDataComplete($data,($passwordsUnchanged ? array('password','password_2'): null)) &&
					$this->isUsernameCorrect($data['username']) &&
					$this->isUsernameUnique($data['username'],$user['id']) &&
					($passwordsUnchanged || $this->arePasswordsIdentical($data['password'],$data['password_2'])) &&
					($passwordsUnchanged || $this->isPasswordCorrect($data['password'])) &&
					$this->isEmailAddressCorrect($data['email_address']) &&
					$this->isEmailAddressUnique($data['email_address'],$user['id']) &&
					$this->isRoleAssignable($data['role_id'])
					) {

					// get the current role of the collaborator in the current project
					$upr = $this->getUserProjectRole($this->rGetId(), $this->getCurrentProjectId());

					// if collaborator has a regular role (or the current user is sysadmin), update to the new role...
					if (
						($upr['role_id'] != ID_ROLE_SYS_ADMIN && $upr['role_id'] != ID_ROLE_LEAD_EXPERT) ||
						$this->getCurrentUserRoleId() == ID_ROLE_SYS_ADMIN
						)
					{
						$this->models->ProjectsRolesUsers->save(
							array(
								'id' => $data['userProjectRole'],
								'user_id' => $this->rGetId(),
								'project_id' => $this->getCurrentProjectId(),
								'role_id' => $data['role_id'],
								'active' => $data['active']
							)
						);
					}
					else
					{
						// ... but the role of lead expert or system admin cannot be changed, nether can he be made inactive
						$data['active']=1;
					}

					/*
						'active' was moved to the ProjectRoleUser table but the column in User does still exist
					*/
					//$d = $this->rGetVal('active');
					//$this->rGetVal('active') = 1;

					if (!$passwordsUnchanged)
					{
						$data['password']=$this->userPasswordEncode($data['password']);
					}
					else
					{
						unset($data['password']);
					}

					$this->models->Users->save($data);
					$this->saveUsersModuleData($data,$this->rGetId(),true);
					$this->addMessage($this->translate('User data saved'));

					$user=$this->getUserById( $this->rGetId() );
				}
				else
				{
					$user=$this->GetAll();
				}
			}

			$modules = $this->getProjectModules();

			$mpu = $this->models->ModulesProjectsUsers->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->rGetId(),
					),
					'columns' => 'count(*) as total,module_id',
					'group' =>  'module_id',
					'fieldAsIndex' => 'module_id'
				)
			);

			foreach ((array) $modules['modules'] as $key => $val)
			{
				$modules['modules'][$key]['isAssigned'] = isset($mpu[$val['module_id']]) ? $mpu[$val['module_id']]['total']=='1' : false;
			}

			$fpu = $this->models->FreeModulesProjectsUsers->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->rGetId(),
					),
					'columns' => 'count(*) as total,free_module_id',
					'group' =>  'free_module_id',
					'fieldAsIndex' => 'free_module_id'
				)
			);

			foreach ((array) $modules['freeModules'] as $key => $val)
			{
				$modules['freeModules'][$key]['isAssigned'] = isset($fpu[$val['id']]) ? $fpu[$val['id']]['total']=='1' : false;
			}

			$canDelete = (isset($user['created_by']) && $user['created_by']==$this->getCurrentUserId() || $this->isCurrentUserSysAdmin());

			$upr = $this->getUserProjectRole($this->rGetId(), $this->getCurrentProjectId());

			if ($this->isCurrentUserSysAdmin())
			{
				$d = array('id !=' => ID_ROLE_SYS_ADMIN);
			}
			else
			{
				$d = array('assignable' => 'y');
			}

			$roles = $this->models->Roles->_get(array('id'=>$d));
			$zones = $this->models->Timezones->_get(array('id' => '*'));

			$this->smarty->assign('isLeadExpert', $upr['role_id'] == ID_ROLE_LEAD_EXPERT);
			$this->smarty->assign('zones',$zones);
			$this->smarty->assign('modules',$modules['modules']);
			$this->smarty->assign('freeModules',$modules['freeModules']);
			$this->smarty->assign('roles',$roles);
			$this->smarty->assign('data',$user);
			$this->smarty->assign('userRole',$upr);
			$this->smarty->assign('canDelete',$canDelete);

		}

		$this->printPage();

    }


    /**
     * Find, reset and send new password
     *
     * See function code for detailed comments on the function's flow
     *
     * @access    public
     */
    public function passwordAction ()
    {

        $this->setPageName($this->translate('Reset password'));

		if ($this->rHasVal('email') && !$this->isFormResubmit())
		{
			$u = $this->models->Users->_get(
				array(
					'id' => array(
						'email_address' => trim($this->rGetVal('email')
						)
					)
				)
			);

			if (count((array)$u)==1)
			{

				$newPass = $this->generateRandomPassword();

				$this->sendPasswordEmail($u[0],$newPass);

				$r = $this->models->Users->save(
					array(
						'id' => $u[0]['id'],
						'password' => $this->userPasswordEncode($newPass)
					)
				);

				$this->addMessage($this->translate('Your password has been reset. An e-mail with a new password has been sent to you.'));

			}
			else
			{
				$this->addError($this->translate('Invalid or unknown e-mail address.'));
			}
		}

		$this->printPage();

    }

    public function addUserModuleAction ()
    {
        $this->checkAuthorisation();

		if ($this->rHasVal('action','add'))
		{
			if ($this->rHasVal('modId') && $this->rHasVal('modId'))
			{
				if ($this->rGetVal('type')=='free')
				{
					$this->addUserToFreeModule($this->rGetVal('uid'),$this->getCurrentProjectId(),$this->rGetVal('modId'));
				}
				else
				{
					$this->addUserToModule($this->rGetVal('uid'),$this->getCurrentProjectId(),$this->rGetVal('modId'));
				}

			}

			$this->redirect($this->rHasVal('returnUrl') ? $this->rGetVal('returnUrl') : 'all.php');

		}
		else
		if ($this->rHasVal('uid') && $this->rHasVal('modId'))
		{
			$modules = $this->getProjectModules();

			if ($this->rHasVal('type','free'))
			{
				foreach((array)$modules['freeModules'] as $val)
				{
					if ($val['id']==$this->rGetVal('modId'))
						$module = $val['module'];
				}
			}
			else
			{
				foreach((array)$modules['modules'] as $val)
				{
					if ($val['module_id']==$this->rGetVal('modId'))
						$module = $val['module'];
				}
			}

			if (isset($module))
			{
				$this->smarty->assign('user',$this->getUserById($this->rGetVal('uid')));
				$this->smarty->assign('module',$module);
				$this->smarty->assign('requestData',$this->GetAll());
			}
			else
			{
				$this->addError($this->translate('Non-existant module ID specified.'));
			}

		}
		else
		{
			$this->addError($this->translate('No user ID or module ID specified.'));
		}

        $this->printPage();

    }

    public function removeUserModuleAction ()
    {
        $this->checkAuthorisation();

		if ($this->rHasVal('action','remove'))
		{
			if ($this->rHasVal('modId') && $this->rHasVal('modId'))
			{
				if ($this->rGetVal('type')=='free')
				{
					$this->removeUserFromFreeModule($this->rGetVal('uid'),$this->getCurrentProjectId(),$this->rGetVal('modId'));
				}
				else
				{
					$this->removeUserFromModule($this->rGetVal('uid'),$this->getCurrentProjectId(),$this->rGetVal('modId'));
				}
			}

			$this->redirect($this->rHasVal('returnUrl') ? $this->rGetVal('returnUrl') : 'all.php');
		}
		else
		if ($this->rHasVal('uid') && $this->rHasVal('modId'))
		{
			$modules = $this->getProjectModules();

			if ($this->rHasVal('type','free'))
			{
				foreach((array)$modules['freeModules'] as $val)
				{
					if ($val['id']==$this->rGetVal('modId'))
						$module = $val['module'];
				}
			}
			else
			{
				foreach((array)$modules['modules'] as $val)
				{
					if ($val['module_id']==$this->rGetVal('modId'))  $module = $val['module'];
				}
			}

			if (isset($module))
			{
				$this->smarty->assign('user',$this->getUserById($this->rGetVal('uid')));
				$this->smarty->assign('module',$module);
				$this->smarty->assign('requestData',$this->GetAll());
			}
			else
			{
				$this->addError($this->translate('Non-existant module ID specified.'));
			}
		}
		else
		{
			$this->addError($this->translate('No user ID or module ID specified.'));
		}

        $this->printPage();

    }

	public function rightsMatrixAction()
	{

        $this->checkAuthorisation(true);

		$this->setBreadcrumbRootName($this->translate('System administration'));

        $this->setPageName($this->translate('Rights matrix'));

		if ($this->rHasVal('right') && $this->rHasVal('role') && !$this->isFormResubmit())
		{
			$d = $this->models->RightsRoles->_get(
				array(
					'id' => array('right_id' => $this->rGetVal('right'),'role_id' => $this->rGetVal('role'))
				)
			);

			if (isset($d))
			{
				$this->models->RightsRoles->delete(array('right_id' => $this->rGetVal('right'),'role_id' => $this->rGetVal('role')));
			}
			else
			{
				$this->models->RightsRoles->save(
					array(
						'id' => null,
						'right_id' => $this->rGetVal('right'),
						'role_id' => $this->rGetVal('role')
					)
				);
			}
		}

		$roles = $this->models->Roles->_get(array('id' => '*'));
		$rights = $this->models->Rights->_get(array('id' => '*','order' => 'controller'));

		foreach((array)$rights as $iKey => $iVal)
		{
			foreach((array)$roles as $oKey => $oVal)
			{
				$d = $this->models->RightsRoles->_get(
					array(
						'id' => array('right_id' => $iVal['id'],'role_id' => $oVal['id'])
					)
				);

				$rights[$iKey]['roles'][] = array('id' => $oVal['id'], 'state' => $d[0]['id']);
			}
		}

        $this->smarty->assign('roles', $roles);
        $this->smarty->assign('rights', $rights);

        $this->printPage();

	}

    public function addUserAction ()
    {
        $this->checkAuthorisation();

		if ($this->rHasVal('action','save'))
		{
			if ($this->rHasId() && $this->rHasVal('role_id'))
			{
				$this->addUserToProject($this->rGetId(),$this->getCurrentProjectId(),$this->rGetVal('role_id'));
			}

			$this->redirect($this->rHasVal('returnUrl') ? $this->rGetVal('returnUrl') : 'all.php');

		}
		else
		if ($this->rHasVal('uid'))
		{
			if ($this->isCurrentUserSysAdmin())
			{
				$d = array('id !=' => ID_ROLE_SYS_ADMIN);
			}
			else
			{
				$d = array('assignable' => 'y');
			}

			$this->smarty->assign('user',$this->getUserById($this->rGetVal('uid')));
			$this->smarty->assign('roles',$this->models->Roles->_get(array('id'=>$d)));

			if ($this->rHasVal('returnUrl'))
				$this->smarty->assign('returnUrl',$this->rGetVal('returnUrl'));
		}
		else
		{
			$this->addError($this->translate('No user ID specified.'));
		}

        $this->printPage();

    }

    public function removeUserAction ()
    {
        $this->checkAuthorisation();

		if ($this->rHasVal('action','remove'))
		{
			if ($this->rHasVal('uid'))
				$this->removeUserFromProject($this->rGetVal('uid'),$this->getCurrentProjectId());

			$this->redirect($this->rHasVal('returnUrl') ? $this->rGetVal('returnUrl') : 'all.php');
		}
		else
		if ($this->rHasVal('uid'))
		{
			$user = $this->getUserById($this->rGetVal('uid'));

			$pru = $this->models->ProjectsRolesUsers->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->rGetVal('uid'),
					)
				)
			);

			if (!$pru)
			{
				$this->addError($this->translate('User is not part of current project.'));
			}
			else
			{
				$this->smarty->assign('uid',$this->rGetVal('uid'));
				$this->smarty->assign('user',$user);
				$this->smarty->assign('role',$this->models->Roles->_get(array('id'=>$pru[0]['role_id'])));
				if ($this->rHasVal('returnUrl'))
					$this->smarty->assign('returnUrl',$this->rGetVal('returnUrl'));
			}
		}
		else
		{
			$this->addError($this->translate('No user ID specified.'));
		}

        $this->printPage();
    }

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

		$idToIgnore = $this->rHasVal('id_to_ignore') ? $this->rGetVal('id_to_ignore') : null;

		$values=$this->rHasVal('values') ? $this->rGetVal('values') : null;

        if ($this->rHasVal('action','check_username'))
		{
			if (in_array('f',$this->rGetVal('tests')))
			{
				$this->isUsernameCorrect($values[0]);
			}
			if (in_array('e',$this->rGetVal('tests')))
			{
				$this->isUsernameUnique($values[0], $idToIgnore);
			}
		}
		else
        if ($this->rHasVal('action','check_password'))
		{
			if (in_array('f',$this->rGetVal('tests')))
			{
				$this->getPasswordStrength($values[0]);
			}
		}
		else
        if ($this->rHasVal('action','check_passwords'))
		{
			if (in_array('f',$this->rGetVal('tests')))
			{
				$this->isPasswordCorrect($values[0]);
			}
			if (in_array('q',$this->rGetVal('tests')))
			{
				$this->arePasswordsIdentical($values[0],$values[1]);
			}
		}
		else
        if ($this->rHasVal('action','check_email_address'))
		{
			if (in_array('e',$this->rGetVal('tests')))
			{
				$this->isEmailAddressUnique($values[0], $idToIgnore);
			}
			if (in_array('f',$this->rGetVal('tests')))
			{
				$this->isEmailAddressCorrect($values[0]);
			}
		}
		else
        if ($this->rHasVal('action','check_first_name') || $this->rHasVal('action','check_last_name'))
		{
			if (in_array('f',$this->rGetVal('tests')))
			{
				if (strlen($values[0]) == 0) $this->addError($this->translate('Missing value.'));
			}
		}
		else
        if ($this->rHasVal('action','connect_existing'))
		{
            $this->ajaxActionConnectExistingUser();
		}
		else
        if ($this->rHasVal('action','create_from_session'))
		{
            $this->ajaxActionCreateUserFromSession();
		}
		else
		if ($this->rHasVal('action','get_lookup_list') && !empty($this->rGetVal('search')))
		{
            $this->getLookupList($this->rGetVal('search'));
        }

        $this->printPage();

    }

    /**
     * Returns the page to redirect to after logging in
     *
     * @return     string    path of page to redirect to
     * @access     private
     */
    private function getLoginStartPage ($includeDomain=false)
    {
        if (!empty($_SESSION['admin']['login_start_page']))
		{
            $script=$_SESSION['admin']['login_start_page'];
			unset($_SESSION['admin']['login_start_page']);
        }
		else
		{
			$script=$this->baseUrl.$this->getAppName();

			if (isset($_SESSION['admin']['user']) && $_SESSION['admin']['user']['_number_of_projects'] == 1)
			{
				$script.=$this->generalSettings['paths']['projectIndex'];
			}
			else
			{
				$script.=$this->generalSettings['paths']['chooseProject'];
			}
        }

		return ($includeDomain ? 'http://' . $_SERVER['HTTP_HOST'] . '/' : '').$script;
    }

	private function ajaxActionConnectExistingUser()
	{
		if (
			!isset($_SESSION['admin']['data']['new_user']['role_id']) ||
			!isset($_SESSION['admin']['data']['new_user']['existing_user_id'])
		) return;


		$this->models->ProjectsRolesUsers->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'user_id' => $_SESSION['admin']['data']['new_user']['existing_user_id']
			)
		);

		// save new role only for existing collaborator and new project
		$pru = $this->models->ProjectsRolesUsers->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'role_id' => $_SESSION['admin']['data']['new_user']['role_id'],
				'user_id' => $_SESSION['admin']['data']['new_user']['existing_user_id']
			)
		);
		if (!$pru)
		{
			$this->addError($this->translate('Failed to connect user from session.'));
		}
		else
		{
			unset($_SESSION['admin']['data']['new_user']);
		}
	}

	private function ajaxActionCreateUserFromSession ()
	{
		if (!isset($_SESSION['admin']['data']['new_user'])) return;

		$su = $this->saveNewUser($_SESSION['admin']['data']['new_user']);

		if (!$su)
		{
			$this->addError($this->translate('Failed to create user from session.'));
		}
		else
		{
			$this->sendNewUserEmail($_SESSION['admin']['data']['new_user']);
			unset($_SESSION['admin']['data']['new_user']);
		}
	}

	private function saveNewUser($data)
	{
		$data['password'] = $this->userPasswordEncode($data['password']);
		$data['active'] = '1';
		$data['id'] = null;
		$data['created_by'] = $this->getCurrentUserId();

		$r = $this->models->Users->save($data);

		if ($r !== true)
		{
			$this->addError($this->translate('Failed to save user.'),2);
			$this->log(serialize($data));
			return false;
		}
		else
		{
			// if saving was succesful, save new role
			$newUserId = $this->models->Users->getNewId();

			$this->models->ProjectsRolesUsers->save(
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
		if (isset($data['modules']))
		{
			if ($deleteOld)
			{
				$this->models->ModulesProjectsUsers->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $userId
					)
				);
			}

			foreach((array)$data['modules'] as $key => $val)
			{
				$this->models->ModulesProjectsUsers->save(
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
		if (isset($data['freeModules']))
		{
			if ($deleteOld)
			{
				$this->models->FreeModulesProjectsUsers->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $userId
					)
				);
			}

			foreach((array)$data['freeModules'] as $key => $val)
			{
				$this->models->FreeModulesProjectsUsers->save(
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
		// avoiding orphans: see if collaborator is present in any other projects...
		$data = $this->models->ProjectsRolesUsers->_get(
			array(
				'id' => array(
					'user_id' => $id
				),
				'columns' => 'count(*) as tot'
			)
		);

		// ...if not, delete entire collaborator record
		if (isset($data) && $data[0]['tot'] == '0')
		{
			$this->models->ModulesProjectsUsers->delete($id);
			$this->models->FreeModulesProjectsUsers->delete($id);
			$this->models->Users->delete($id);

			return true;
		}
		else
		{
			return false;
		}
	}

	private function getPasswordStrength($password)
	{
		$min = $this->controllerSettings['dataChecks']['password']['minLength'];
		$max = $this->controllerSettings['dataChecks']['password']['maxLength'];

		if (strlen($password) > $max)
		{
            $this->smarty->assign('returnText',sprintf($this->translate('Password too long; should be between %s and %s characters.'),$min,$max));
		}
		else
		if (strlen($password) < $min)
		{
            $this->smarty->assign('returnText',sprintf($this->translate('Password too short; should be between %s and %s characters.'),$min,$max));
		}
		else
		if (strlen($password) < ($min + 3))
		{
			$this->smarty->assign('returnText','<weak>');
		}
		else
		{
			if (
				preg_match_all('/[0-9]/',$password,$d)>=1 &&
				preg_match_all('/[a-zA-Z]/',$password,$d)>=1 &&
				preg_match_all('/[^a-zA-Z0-9]/',$password,$d)>=1
			) {
				$this->smarty->assign('returnText','<strong>');
			}
			else
			{
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
        $this->models->Users->save(
            array(
                'id' => $user['id'],
                'last_login' => 'now()',
                'logins' => 'logins+1'
            )
        );

        // get user's roles and rights
        $cur = $this->getUserRights($user['id']);

        // save all relevant data to the session
        $this->initUserSession($user, $cur['roles'], $cur['rights'], $cur['number_of_projects']);

        // set 'remember me' cookie
        if ($remember)
		{
            $this->setRememberMeCookie();
        }
		else
		{
            $this->unsetRememberMeCookie();
        }

        // determine and set the default active project
        $this->setDefaultProject();
		$this->setCurrentUserRoleId();
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
    private function initUserSession($userData, $roles, $rights, $numberOfProjects)
    {
        if (!$userData) return;

        $_SESSION['admin']['user'] = $userData;
        $_SESSION['admin']['user']['_login']['time'] = time();
        $_SESSION['admin']['user']['_said_welcome'] = false;
        $_SESSION['admin']['user']['_logged_in'] = true;

        //$_SESSION['admin']['user']['_roles'] = $roles;
        //$_SESSION['admin']['user']['_rights'] = $rights;
        //$_SESSION['admin']['user']['_number_of_projects'] = $numberOfProjects;
		$this->setUserSessionRights($rights);
		$this->setUserSessionRoles($roles);
		$this->setUserSessionNumberOfProjects($numberOfProjects);
    }

    /**
     * Destroys a user's session (when logging out)
     *
     * @access     public
     */
    private function destroyUserSession ()
    {
		unset($_SESSION['admin']);
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

        if ($c)
		{
            return $this->models->Users->_get(
				array(
					'id' => array(
						'id' => $c,
						'active' => '1'
					)
				)
            );
        }
		else
		{
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
        $pru = $this->models->ProjectsRolesUsers->_get(
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
    private function getUserProjectRole($userId, $projectId)
    {
        $pru = $this->models->ProjectsRolesUsers->_get(
			array(
				'id'=> array(
					'user_id' => $userId,
					'project_id' => $projectId
				),
				'columns' => '*, ifnull(last_project_select,"'.$this->translate('(has never worked on project)').'") as last_login'
        	)
		);

        if ($pru)
		{
            $r = $this->models->Roles->_get(array('id'=>$pru[0]['role_id']));
            $pru[0]['role'] = $r;
        }

        return $pru[0];
    }

    /**
     * Encodes a user's password for storing or checking against the database when logging in
     *
     * password_hash is used as encoding function
     *
     * @param      string    $p    the password
     * @return     string    password_hash
     * @access     private
     */
    private function userPasswordEncode($p,$force_md5=false)
    {
		if ($force_md5)
		{
        	return md5($p);
		}
		else
		{
			return password_hash($p, PASSWORD_DEFAULT);
		}
    }

    /**
     * Verifies if the user data that has been entered is complete
     *
     * @param      array    $fieldsToIgnore    fields that might be in the data, but need not be checked
     * @return     boolean    data is complete or not
     * @access     private
     */
    private function isUserDataComplete($data,$fieldsToIgnore=array())
    {
        $result = true;

        if (!in_array('username',(array)$fieldsToIgnore) && $data['username'] == '')
		{
            $this->addError($this->translate('Missing username.'));
            $result = false;
        }

        if (!in_array('password',(array)$fieldsToIgnore) && $data['password'] == '')
		{
            $this->addError($this->translate('Missing password.'));
            $result = false;
        }

        if (!in_array('password_2',(array)$fieldsToIgnore) && $data['password_2'] == '')
		{
            $this->addError($this->translate('Missing password repeat.'));
            $result = false;
        }

        if (!in_array('first_name',(array)$fieldsToIgnore) && $data['first_name'] == '')
		{
            $this->addError($this->translate('Missing first name.'));
            $result = false;
        }

        if (!in_array('last_name',(array)$fieldsToIgnore) && $data['last_name'] == '')
		{
            $this->addError($this->translate('Missing last name.'));
            $result = false;
        }

        if (!in_array('email_address',(array)$fieldsToIgnore) && $data['email_address'] == '')
		{
            $this->addError($this->translate('Missing email address.'));
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
    private function isUsernameCorrect($username)
    {
        if (!$username) return false;

		$min = $this->controllerSettings['dataChecks']['username']['minLength'];
		$max = $this->controllerSettings['dataChecks']['username']['maxLength'];

        $result = true;

        if (strlen($username) < $min)
		{
            $this->addError(sprintf($this->translate('Username too short; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (strlen($username) > $max)
		{
            $this->addError(sprintf($this->translate('Username too long; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (
			isset($this->controllerSettings['dataChecks']['username']['regexp']) &&
			!preg_match($this->controllerSettings['dataChecks']['username']['regexp'],$username)
		)
		{
            $this->addError($this->translate('Username has incorrect format.'));
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
    private function isPasswordCorrect($password)
    {
        if (empty($password)) return false;

		$min = $this->controllerSettings['dataChecks']['password']['minLength'];
		$max = $this->controllerSettings['dataChecks']['password']['maxLength'];

        $result = true;

        if (strlen($password) < $min)
		{
            $this->addError(sprintf($this->translate('Password too short; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (strlen($password) > $max)
		{
            $this->addError(sprintf($this->translate('Password too long; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (
			isset($this->controllerSettings['dataChecks']['password']['regexp']) &&
			!preg_match($this->controllerSettings['dataChecks']['password']['regexp'],$password)
			)
		{
            $this->addError($this->translate('Password has incorrect format.'));
            $result = false;
        }

        return $result;
    }

    private function arePasswordsIdentical($password,$password_2)
    {
        if ($password != $password_2)
		{
            $this->addError($this->translate('Passwords not the same.'));
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
    private function isEmailAddressCorrect($email_address)
    {
		if (empty($email_address)) return false;

		$min = $this->controllerSettings['dataChecks']['email_address']['minLength'];
		$max = $this->controllerSettings['dataChecks']['email_address']['maxLength'];

        $result = true;

        if (strlen($email_address) < $min)
		{
            $this->addError(sprintf($this->translate('E-mail adress too short; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (strlen($email_address) > $max)
		{
            $this->addError(sprintf($this->translate('E-mail adress too long; should be between %s and %s characters.'),$min,$max));
            $result = false;
        }
		else
        if (!$this->verifyEmailAddress($email_address))
		{
            $this->addError($this->translate('Invalid e-mail address.'));
            $result = false;
        }

        return $result;
    }

    private function verifyEmailAddress ($email)
    {
		return (filter_var($email, FILTER_VALIDATE_EMAIL));
    }

    /**
     * Tests whether username is unique in the database
     *
     * @param      string    $username    username to check; if false, it is the 'username' var from the request data that is tested
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @return     boolean    unique or not
     * @access     private
     */
    private function isUsernameUnique($username,$idToIgnore=null)
    {
		$d = array('username' => $username);

		if (!empty($idToIgnore)) $d['id !='] = $idToIgnore;

		$users = $this->models->Users->_get(array('id'=>$d));

		if (count((array) $users) != 0)
		{
			$this->addError($this->translate('Username already exists.'));
			return false;
		}
		else
		{
			return true;
		}
    }

    /**
     * Tests whether emailaddress is unique in the database
     *
     * @param      string    $email_address    address to check; if false, it is the 'email_address' var from the request data that is tested
     * @param      integer    $idToIgnore    user id to ignore, as not to match someone with himself
     * @return     boolean    unique or not
     * @access     private
     */
    private function isEmailAddressUnique($email_address,$idToIgnore=null)
    {
		$d = array('email_address' => $email_address);

		if ($idToIgnore)  $d['id !='] = $idToIgnore;

		$users = $this->models->Users->_get(array('id'=>$d));

		if (count((array) $users) != 0)
		{
			$this->addError($this->translate('E-mail address already exists.'));
			return false;
		}
		else
		{
			return true;
		}
    }

	private function prepareEmail($user,$plain,$html,$mailName=null)
	{
		$plain = str_replace('[[url]]',$this->getLoginStartPage(true),$plain);
		$html = str_replace('[[url]]',$this->getLoginStartPage(true),$html);

		return
			array(
				'mailto_address' => $user['email_address'],
				'mailto_name' => ($user['first_name'] ? $user['first_name'].' ' : '').$user['last_name'],
				'mailfrom_address' => $this->controllerSettings['email']['mailfrom_address'],
				'mailfrom_name' => $this->controllerSettings['email']['mailfrom_name'],
				'subject' => $this->controllerSettings['email']['mails']['newuser']['subject'],
				'plain' => $plain,
				'html' => $html,
				'smtp_server' => $this->controllerSettings['email']['smtp_server'],
				'mail_name' => $mailName,
				'debug' => false
			);

	}

	private function sendNewUserEmail($user)
	{
		return $this->sendEmail(
			$this->prepareEmail(
				$user,
				sprintf(
					$this->translate($this->controllerSettings['email']['mails']['newuser']['plain']),
					$user['username'],
					$user['password']
				),
				sprintf(
					$this->translate($this->controllerSettings['email']['mails']['newuser']['html']),
					$user['username'],
					$user['password']
				),
				'created new user'
			)
		);

	}

	private function sendPasswordEmail($user,$password)
	{
		return $this->sendEmail(
			$this->prepareEmail(
				$user,
				sprintf(
					$this->translate($this->controllerSettings['email']['mails']['resetpassword']['plain']),
					$password
				),
				sprintf(
					$this->translate($this->controllerSettings['email']['mails']['resetpassword']['html']),
					$password
				),
				'reset password'
			)
		);

	}

	private function generateRandomPassword()
	{
		$chars = $this->controllerSettings['randomPassword']['chars'];

		srand((double)microtime()*1000000);

		$i = 0;
		$pass = '' ;

		while ($i <= $this->controllerSettings['randomPassword']['length'])
		{
			$num = rand()%33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass.$tmp;
			$i++;
		}

		return $pass;
	}

	private function getUserById($id)
	{
		if (empty($id)) return;
		return $this->models->Users->_get(array('id' => $id));
	}

	private function getLookupList($search)
	{
		if (empty($search)) return;

		$users = $this->models->Users->_get(
			array(
				'where' =>
					'username like \'%'.$search.'%\'
					or first_name like \'%'.$search.'%\'
					or last_name like \'%'.$search.'%\'
					or email_address like \'%'.$search.'%\''
				,
				'columns' => 'id,concat(first_name,\' \',last_name,\' (\',username,\'; \',email_address,\')\') as label,last_name,first_name',
				'order' => 'last_name,first_name'
			)
		);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(array(
				'data'=>$users,
				'module'=>$this->controllerBaseName,
				'url'=>'edit.php?id=%s'
			))
		);

	}

	private function addUserToModule($uid,$pId,$modId)
	{
		$this->models->ModulesProjectsUsers->save(
			array(
				'id' => null,
				'user_id' => $uid,
				'project_id' => $pId,
				'module_id' => $modId
			)
		);
	}

	private function addUserToFreeModule($uid,$pId,$modId)
	{
		$this->models->FreeModulesProjectsUsers->save(
			array(
				'id' => null,
				'user_id' => $uid,
				'project_id' => $pId,
				'free_module_id' => $modId
			)
		);
	}

	private function removeUserFromModule($uid,$pId,$modId)
	{
		$this->models->ModulesProjectsUsers->delete(
			array(
				'user_id' => $uid,
				'project_id' => $pId,
				'module_id' => $modId
			)
		);
	}

	private function removeUserFromFreeModule($uid,$pId,$modId)
	{
		$this->models->FreeModulesProjectsUsers->delete(
			array(
				'user_id' => $uid,
				'project_id' => $pId,
				'free_module_id' => $modId
			)
		);

	}

	private function removeUserFromProject($uid,$pId)
	{
		$d = array(
				'user_id' => $uid,
				'project_id' => $pId
			);

		$this->models->ProjectsRolesUsers->delete($d);
		$this->models->ModulesProjectsUsers->delete($d);
		$this->models->FreeModulesProjectsUsers->delete($d);
		$this->models->UsersTaxa->delete($d);
	}

	private function sanitizeUserData($data)
    {
        if (isset($data['email_address']))
		{
            $data['email_address'] = strtolower($data['email_address']);
        }

        foreach ((array) $data as $key => $val)
		{
			if (is_array($val))
			{
		        foreach ((array) $val as $key2 => $val2)
				{
		            $val[$key2] = trim($val2);
				}
			}
			else
			{
	            $data[$key] = trim($val);
			}
        }

        return $data;
    }

	private function isRoleAssignable($roleId)
	{
		if ($this->isCurrentUserSysAdmin())
			return true;

		// make sure an unassignable role (like system admin) wasn't injected
		$r = $this->models->Roles->_get(array('id'=>$roleId));

		if ($r['assignable']=='n')
		{
			$this->addError($this->translate('Unassignable role selected.'));
			return false;
		}
		else
		{
			return true;
		}

	}


}