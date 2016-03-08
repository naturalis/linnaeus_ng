<?php

/*

	(view) ergens heen verplaatsen

	must hide sysadmin! (set "hidden" in set users)
	webservice
	user in multiple projects
	free modules!
	isCurrentUserSysAdmin!!!
	log for REDESIGN RIGHTS
	
	removeUserFromCurrentProject
		needs deletiog of referenced rights etc

*/

include_once ('Controller.php');

class UsersController extends Controller
{
	private $_expertroleid;
	private $_allprojectusers;
	private $_userid;
	private $_user;
	private $_newuserdata;
	private $_newuserdatasave=false;
	private $_newuserpasswordsave=false;
	private $_newuserrolesave=false;

    public $usedModels = array( 
		'user_module_access',
		'user_item_access'
	);

    public $usedHelpers = array(
        'password_encoder',
    );

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

    public $controllerPublicName='User administration';
	
	private $checksUsername=array('min' => 4,'max' => 32); // regExp => 
	private $checksPassword=array('min' => 8,'max' => 32); // regExp => 
	private $checksName=array('min' => 1,'max' => 64);

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }



	private function initialize()
	{
		$this->setAllProjectUsers();
		$this->setExpertRoleId();

		if ( empty($this->getExpertRoleId()) )
		{
			$this->addWarning( $this->translate('No ID for the editor-role defined.') );
		}
	}

	private function setExpertRoleId()
	{
		$d=$this->models->Roles->_get(array('id'=>array('role'=>'Editor')));
		$this->_expertroleid=$d[0] ? $d[0]['id'] : null;
	}

	private function getExpertRoleId()
	{
		return $this->_expertroleid;
	}

	private function setAllProjectUsers()
	{
		$this->_allprojectusers=$this->models->UsersModel->getProjectUsers(array(
			'project_id'=>$this->getCurrentProjectId()
		));
		
		foreach((array)$this->_allprojectusers as $key=>$user)
		{
			$this->_allprojectusers[$key]['module_access']=$this->getUserModuleAccess( $user['id'] );
			$this->_allprojectusers[$key]['item_access']=$this->getUserItemAccess( $user['id'] );
			$this->_allprojectusers[$key]['project_role']=$this->getUserProjectRole( $user['id'] );
		}
	}

	private function getAllProjectUsers()
	{
		return $this->_allprojectusers;
	}

	private function setUserId( $userid )
	{
		$this->_userid = $userid;
	}

	private function getUserId()
	{
		return $this->_userid;
	}

	private function setUser()
	{
		$this->_user=$this->models->Users->_get(array(
			'id' => array( 'id' => $this->getUserId() ),
			'columns' => '*, datediff(curdate(),created) as days_active'
		));
		
		if ( $this->_user ) 
		{
			$this->_user=$this->_user[0];
			$this->_user['module_access']=$this->getUserModuleAccess( $this->getUserId() );
			$this->_user['item_access']=$this->getUserItemAccess( $this->getUserId() );
			$this->_user['project_role']=$this->getUserProjectRole( $this->getUserId() );
		}
		else
		{
			$this->_user=null;
		}
	}

	private function getUser()
	{
		return $this->_user;
	}

	private function getUserProjectRole( $userid )
	{
		return $this->models->UsersModel->getUserProjectRole(array(
			'project_id'=>$this->getCurrentProjectId(),
			'user_id'=>$userid
		));
	}

	private function getUserModuleAccess( $userid )
	{
		return $this->models->UserModuleAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$userid
			)
		));
	}

	private function getUserItemAccess( $userid )
	{
		return $this->models->UserItemAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$userid
			)
		));
	}

	private function setNewUserData( $data )
	{
		$this->_newuserdata=$data;
	}

	private function getNewUserData()
	{
		return $this->_newuserdata;
	}

	private function setNewUserDataSave( $state )
	{
		$this->_newuserdatasave=$state;
	}

	private function getNewUserDataSave()
	{
		return $this->_newuserdatasave;
	}

	private function setNewUserPasswordSave( $state )
	{
		$this->_newuserpasswordsave=$state;
	}

	private function getNewUserPasswordSave()
	{
		return $this->_newuserpasswordsave;
	}

	private function setNewUserRoleSave( $state )
	{
		$this->_newuserrolesave=$state;
	}

	private function getNewUserRoleSave()
	{
		return $this->_newuserrolesave;
	}

	private function sanitizeNewUserData()
    {
		$data=$this->getNewUserData();

        if (isset($data['email_address']))
		{
            $data['email_address'] = strtolower($data['email_address']);
        }

        foreach ((array) $data as $key => $val)
		{
			if ($key=='password' || $key=='password_repeat') continue;

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

		$this->setNewUserData( $data );
    }

    private function addUserToCurrentProject()
    {
		if ( empty($this->getExpertRoleId()))
		{
			$this->addError( $this->translate('Cannot add user without role ID.') );
			return;
		}
		
        $this->models->ProjectsRolesUsers->save(array(
            'id' => null,
            'project_id' => $this->getCurrentProjectId(),
            'role_id' => $this->getExpertRoleId(),
            'user_id' => $this->getUserId(),
            'active' => 1
        ));
    }

	private function removeUserFromCurrentProject()
	{
		$d = array(
			'user_id' => $this->getUserId(),
			'project_id' => $this->getCurrentProjectId(),
		);
			
        $this->models->ProjectsRolesUsers->delete( $d );
		//$this->models->ModulesProjectsUsers->delete($d);
		//$this->models->FreeModulesProjectsUsers->delete($d);
		//$this->models->UsersTaxa->delete($d);
	}




    public function indexAction ()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Project collaborators'));
        $this->smarty->assign('users',$this->getAllProjectUsers());
        $this->smarty->assign('non_users',$this->models->UsersModel->getNonProjectUsers(array('project_id'=>$this->getCurrentProjectId())));
        $this->printPage();
    }

    public function viewAction ()
    {
        $this->checkAuthorisation();
		if (!$this->rHasId()) $this->redirect( 'index.php' );
        $this->setPageName($this->translate('Project collaborator data'));
		$this->setUserId( $this->rGetId() );
		$this->setUser();
		$this->smarty->assign('user',$this->getUser());
		$this->printPage();
    }

    public function addUserAction ()
    {
        $this->checkAuthorisation();
		if (!$this->rHasId()) $this->redirect( 'index.php' );
		$this->setUserId( $this->rGetId() );
		$this->addUserToCurrentProject();
		$this->redirect('index.php');
    }

    public function removeUserAction ()
    {
        $this->checkAuthorisation();
		if (!$this->rHasId()) $this->redirect( 'index.php' );
		$this->setUserId( $this->rGetId() );
		$this->removeUserFromCurrentProject();
		$this->redirect('index.php');
    }

    private function isNameCorrect()
    {
		$first_name=$this->getNewUserData()['first_name'];
		$last_name=$this->getNewUserData()['last_name'];
		
		$str1=sprintf($this->translate('First name should be between %s and %s characters.'),$this->checksName['min'],$this->checksName['max']);
		$str2=sprintf($this->translate('Last name should be between %s and %s characters.'),$this->checksName['min'],$this->checksName['max']);

        if ( strlen($first_name) < $this->checksName['min'] )
		{
            $this->addError( $str1 );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($first_name) > $this->checksName['max'] )
		{
            $this->addError( $str1 );
			$this->setNewUserDataSave( false );
        }

        if ( strlen($last_name) < $this->checksName['min'] )
		{
            $this->addError( $str2 );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($last_name) > $this->checksName['max'] )
		{
            $this->addError( $str2 );
			$this->setNewUserDataSave( false );
        }
    }

    private function isUsernameCorrect()
    {
		$username=$this->getNewUserData()['username'];
		
		$str=sprintf($this->translate('Username should be between %s and %s characters.'),$this->checksUsername['min'],$this->checksUsername['max']);

        if ( strlen($username) < $this->checksUsername['min'] )
		{
            $this->addError( $str );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($username) > $this->checksUsername['max'] )
		{
            $this->addError( $str );
			$this->setNewUserDataSave( false );
        } else
        if (isset($this->checksUsername['regExp']) && !preg_match($this->checksUsername['regExp'],$username) )
		{
            $this->addError( $this->translate('Username has incorrect format.') );
			$this->setNewUserDataSave( false );
        }
    }

    private function isUsernameUnique()
    {
		$username=$this->getNewUserData()['username'];

		$d=$this->models->Users->_get(array(
			'id'=>array( 'username'=>$username, 'id !='=>$this->getUserId() ),
			'columns'=>'count(*) as total'
		));

		if ($d[0]['total']>0)
		{
			$this->addError( $this->translate('Username already exists.') );
			$this->setNewUserDataSave( false );
		}
    }

    private function isEmailAddressCorrect()
    {
		$email_address=$this->getNewUserData()['email_address'];

        if ( !filter_var($email_address, FILTER_VALIDATE_EMAIL) )
		{
            $this->addError( $this->translate('Invalid e-mail address.') );
			$this->setNewUserDataSave( false );
        }
    }

    private function isEmailAddressUnique()
    {
		$email_address=$this->getNewUserData()['email_address'];

		$d=$this->models->Users->_get(array(
			'id'=>array( 'email_address'=>$email_address, 'id !='=>$this->getUserId() ),
			'columns'=>'count(*) as total'
		));

		if ($d[0]['total']>0)
		{
			$this->addError($this->translate('E-mail address already exists.'));
			$this->setNewUserDataSave( false );
		}
    }

    private function isPasswordCorrect()
    {
		$p1=$this->getNewUserData()['password'];
		$p2=$this->getNewUserData()['password_repeat'];
		$id=$this->getNewUserData()['id'];
		
		// existing user not entering new passwords: no change
        if ( !empty($id) && ( empty($p1) && empty($p2) ) ) return;
		
		$str=sprintf($this->translate('Password should be between %s and %s characters.'),$this->checksPassword['min'],$this->checksPassword['max']);

        if ( $p1!=$p2 )
		{
            $this->addError( $this->translate('Passwords not the same.') );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( empty($id) && empty($p1) && empty($p2) )
		{
            $this->addError( $this->translate('Passwords is required.') );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( strlen($p1) < $this->checksPassword['min'] )
		{
            $this->addError( $str );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( strlen($p1) > $this->checksPassword['max'] )
		{
            $this->addError( $str );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( isset($this->checksPassword['regExp']) && !preg_match($this->checksPassword['regExp'],$p1) )
		{
            $this->addError( $this->translate('Password has incorrect format.') );
			$this->setNewUserPasswordSave( false );
        }
    }

	private function userDataCheck()
	{
		$this->setNewUserDataSave( true );
		$this->isNameCorrect();
		$this->isUsernameCorrect();
		$this->isUsernameUnique();
		$this->isEmailAddressCorrect();
		$this->isEmailAddressUnique();
	}

	private function userDataSave()
	{
		if ( $this->getNewUserDataSave()==false )
		{
            $this->addMessage( $this->translate('Data not saved.') );
			return;
		}

		$data=$this->getNewUserData();

		unset($data['password']);

		$this->models->Users->save( $data );

		if ( empty($data['id']) )
		{
			$this->setUserId( $this->models->Users->getNewId() );
			$this->addMessage( $this->translate('New user created.') );
		}
		else
		{
			$this->addMessage( $this->translate('Data saved.') );
		}
	}

	private function userPasswordCheck()
	{
		$this->setNewUserPasswordSave( true );
		$this->isPasswordCorrect();
	}

	private function userPasswordSave()
	{
		$password=$this->getNewUserData()['password'];

		if ( !empty($password) && $this->getNewUserPasswordSave()==false )
		{
            $this->addMessage( $this->translate('Password not saved.') );
		}
		else
		if ( !empty($password) && empty($this->getUserId()) )
		{
            $this->addMessage( $this->translate('Cannot save password (empty user ID).') );
		}
		else
		if ( !empty($password) && !empty($this->getUserId()) )
		{
			$password=$this->userPasswordEncode( $password );
			$this->models->Users->save( array('id'=>$this->getUserId(),'password'=>$password) );
            $this->addMessage( $this->translate('Password saved.') );
			$this->models->Users->save( array('id'=>$this->getUserId(),'password_changed'=>'now()') );
		}
	}

	private function userRoleCheck()
	{
		$this->setNewUserRoleSave( true );
	}

	private function userRoleSave()
	{
		if ( $this->getNewUserRoleSave()==false )
		{
            $this->addMessage( $this->translate('Data not saved.') );
			return;
		}

		$role_id=$this->getNewUserData()['role_id'];

		$user=$this->getUser();
		
		if ( $role_id==$user['project_role']['role_id'] )
		{
			return;
		}

		if ( empty($role_id) )
		{
            $this->addMessage( $this->translate('Cannot save empty role ID).') );
		}
		else
		if ( empty($user['project_role']['id']) )
		{
			$this->models->ProjectsRolesUsers->save(array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'role_id' => $role_id,
				'user_id' => $this->getUserId(),
				'active' => 1
			));

            $this->addMessage( $this->translate('Saved role.') );
		}
		else
		{
			$this->models->ProjectsRolesUsers->save(array(
				'id' => $user['project_role']['id'],
				'project_id' => $this->getCurrentProjectId(),
				'role_id' => $role_id,
				'user_id' => $this->getUserId(),
				'active' => 1
			));

            $this->addMessage( $this->translate('Updated role.') );
		}
	}


    public function createAction ()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Create new collaborator'));
		$this->setUserId( null );

		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->setNewUserData( $this->rGetAll() );
			$this->sanitizeNewUserData();
			$this->userDataCheck();
			$this->userPasswordCheck();
			$this->userDataSave();
			$this->userPasswordSave();
			$this->setUser();
		}
		
		$this->smarty->assign('user', $this->rGetAll() );
		
		$this->printPage( 'edit' );
    }

    public function editAction ()
    {
        $this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect( 'index.php' );

		$this->setPageName($this->translate('Edit project collaborator'));
		$this->setUserId( $this->rGetId() );
		$this->setUser();

		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->setNewUserData( $this->rGetAll() );
			$this->sanitizeNewUserData();
			$this->userDataCheck();
			$this->userDataSave();
			$this->userPasswordCheck();
			$this->userPasswordSave();
			$this->userRoleCheck();
			$this->userRoleSave();
			$this->setUser();
		}

		$this->smarty->assign( 'user', $this->getUser() );
		$this->smarty->assign( 'roles', $this->getRoles() );
		$this->smarty->assign( 'modules', $this->getProjectModulesUser() );

		$this->printPage();
    }
	
	private function getRoles()
	{
		return $this->models->Roles->_get( array( 'id' => array('hidden'=>'0') ) );
	}

	private function getProjectModulesUser()
	{
		$d=$this->getProjectModules();

		$u=$this->models->UserModuleAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$this->getUserId()
			),
			'fieldAsIndex'=>'module_id'
		));
		
		foreach((array)$d['modules'] as $key=>$val)
		{
			$d['modules'][$key]['access']=isset($u[$val['module_id']]) ? $u[$val['module_id']] : null;
		}
		
		return $d;
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
				$this->smarty->assign('requestData',$this->rGetAll());
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
				$this->smarty->assign('requestData',$this->rGetAll());
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
		$min = $this->dataChecks['password']['minLength'];
		$max = $this->dataChecks['password']['maxLength'];

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
     * Encodes a user's password for storing or checking against the database when logging in
     *
     * password_hash is used as encoding function
     *
     * @param      string    $p    the password
     * @return     string    password_hash
     * @access     private
     */
    private function userPasswordEncode( $password )
    {
		$this->helpers->PasswordEncoder->setForceMd5( false );
		$this->helpers->PasswordEncoder->setPassword( $password );
		$this->helpers->PasswordEncoder->encodePassword();
		return $this->helpers->PasswordEncoder->getHash();
    }


	private function generateRandomPassword()
	{
		$chars='abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		srand((double)microtime()*1000000);

		$i = 0;
		$pass = '' ;

		while ($i <= $this->dataChecks['password']['default_length'])
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